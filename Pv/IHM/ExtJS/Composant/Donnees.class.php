<?php
	
	if(! defined('PV_COMPOSANT_DONNEES_EXT_JS'))
	{
		if(! defined('PV_COMPOSANT_NOYAU_EXT_JS'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_COMPOSANT_DONNEES_EXT_JS', 1) ;
		
		class PvConfigGridPanelExtJS extends PvConfigComposantBaseExtJS
		{
			public $title ;
			public $store ;
			public $columns = array() ;
			public $iconCls = '' ;
			public $dockedItems = array() ;
		}
		class PvConfigColumnExtJS
		{
			public $header ;
			public $dataIndex ;
			public $flex = 1 ;
			
		}
		class PvConfigFormExtJS extends PvConfigWidgetExtJS
		{
		}
		
		class PvColGridPanelExtJS extends PvComposantBaseExtJS
		{
			protected $InclutXTypeCrea = 0 ;
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigColumnExtJS() ;
			}
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigColumnExtJS() ;
			}
		}
		class PvGridPanelExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.grid.Panel" ;
			public $ColsExtJS = array() ;
			public $StoreExtJS ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->StoreExtJS = new PvStoreExtJS() ;
			}
			public function AdopteComposantParentExtJS($nom, & $compParent)
			{
				parent::AdopteComposantParentExtJS($nom, $compParent) ;
				$this->StoreExtJS->AdopteComposantParentExtJS($nom."_store", $this) ;
				$this->StoreExtJS->ChargeConfig() ;
			}
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigWidgetExtJS() ;
			}
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigGridPanelExtJS() ;
			}
			public function & InsereColExtJS($header, $dataIndex)
			{
				$col = new PvColGridPanelExtJS() ;
				$col->ChargeConfig() ;
				$col->CfgCreaExtJS->header = $header ;
				$col->CfgCreaExtJS->dataIndex = $dataIndex ;
				$this->InscritColExtJS($col) ;
				return $col ;
			}
			public function InscritColExtJS(& $col)
			{
				$this->ColsExtJS[] = & $col ;
			}
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				$this->CfgDefExtJS->store = $this->StoreExtJS->ObtientAliasClasseExtJS() ;
				$this->CfgDefExtJS->columns = array() ;
				foreach($this->ColsExtJS as $nom => & $elem)
				{
					$this->CfgDefExtJS->columns[] = & $elem->CfgCreaExtJS ;
				}
			}
			public function RenduComposantExtJS()
			{
				$ctn = parent::RenduComposantExtJS() ;
				$ctn .= $this->StoreExtJS->RenduComposantExtJS() ;
				return $ctn ;
			}
		}
		class PvGridExtJS extends PvGridPanelExtJS
		{
		}
		
		class PvFormPanelExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.form.Panel" ;
			public $DefItemsDansInitComponent = 0 ;
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigFormExtJS() ;
			}
		}
		class PvFormExtJS extends PvFormPanelExtJS
		{
		}
		
		class PvAdaptTableauDonneesExtJS extends PvTableauDonneesHtml
		{
			public function & CreeFiltreExtJS($nom, $valeur)
			{
				$filtre = new PvFiltreDonneesExtJS() ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteScript($nom, $this->ZoneParent->ScriptParDefaut) ;
				return $filtre ;
			}
			public function & InsereFltSelectExtJS($nom, $exprDonnees='', $valeur='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreExtJS($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & ProduitElementExtJS($nom, & $widget)
			{
				$panel = $widget->InsereElementExtJS($nom, new PvPanelTableauDonneesExtJS()) ;
				$panel->ChargeConfig() ;
				// UI
				$panel->CfgDefExtJS->title = $this->Titre ;
				// Action read
				$panel->ActRead = $widget->ZoneParent->InsereActionAvantRendu($nom.'_read', new PvActReadTableauDonneesExtJS()) ;
				$panel->ActRead->ChargeConfig() ;
				$panel->ActRead->AdaptTableauDonnees = & $this ;
				// production form filtres
				$panel->FormFiltres = $panel->InsereElementExtJS('formFiltres', new PvFormExtJS()) ;
				$panel->FormFiltres->ChargeConfig() ;
				$paramsRech = '' ;
				foreach($this->FiltresSelection as $i => & $filtre)
				{
					if($filtre->TypeLiaisonParametre != "extjs")
						continue ;
					$compFlt = $filtre->ObtientComposant() ;
					$compFlt->ChargeConfig() ;
					$compFlt->CfgCreaExtJS->name = $filtre->NomParametreLie ;
					$compFlt->CfgCreaExtJS->fieldLabel = $filtre->ObtientLibelle() ;
					if($filtre->NomParametreLie == '')
						continue ;
					if($paramsRech != '')
						$paramsRech .= ', ' ;
					$paramsRech .= $filtre->NomParametreLie.' : Ext.getCmp("'.$compFlt->IDInstanceCalc.'").getValue()' ;
					$panel->FormFiltres->InsereElementExtJS('filtre_'.$i, $compFlt) ;
				}
				$panel->ToolbarFiltres = $panel->InsereElementExtJS("toolbarFiltres", new PvPanelExtJS()) ;
				$panel->ToolbarFiltres->ChargeConfig() ;
				$panel->BtnSoumetFiltres = $panel->ToolbarFiltres->InsereElementExtJS('btnOK', new PvButtonExtJS()) ;
				$panel->BtnSoumetFiltres->ChargeConfig() ;
				$panel->BtnSoumetFiltres->CfgCreaExtJS->text = $this->TitreBoutonSoumettreFormulaireFiltres ;
				// $panel->BtnSoumetFiltres->CfgCreaExtJS->InsereListenerClick() ;
				// Production panel donnees
				$panel->GridPanelDonnees = $panel->InsereElementExtJS('gridPanelDonnees', new PvGridPanelExtJS()) ;
				$panel->GridPanelDonnees->ChargeConfig() ;
				foreach($this->DefinitionsColonnes as $i => $defCol)
				{
					if($defCol->Visible == 0)
						continue ;
					$idx = count($panel->ColsExtJS) ;
					$panel->ColsExtJS[$idx] = $panel->GridPanelDonnees->InsereColExtJS($defCol->Libelle, $defCol->NomDonnees) ;
					$panel->ColsExtJS[$idx]->CfgDefExtJS->width = $defCol->Largeur ;
					$panel->GridPanelDonnees->StoreExtJS->ModelExtJS->CfgDefExtJS->fields[] = $defCol->IDInstanceCalc ;
				}
				$panel->GridPanelDonnees->StoreExtJS->ProxyExtJS->CfgCreaExtJS->api->read = $panel->ActRead->ObtientUrl() ;
				$panel->GridPanelDonnees->StoreExtJS->ProxyExtJS->CfgCreaExtJS->reader->root = "lgns" ;
				$panel->GridPanelDonnees->StoreExtJS->ProxyExtJS->CfgCreaExtJS->reader->successProperty = "succes" ;
				$panel->BtnSoumetFiltres->InsereListenerClick("var gridPanel = Ext.getCmp('".$panel->GridPanelDonnees->IDInstanceCalc."') ;
var gridStore = gridPanel.getStore() ;
gridStore.load( { params : { ".$paramsRech." } } ) ;") ;
				return $panel ;
			}
		}
		
		class PvActReadTableauDonneesExtJS extends PvActionResultatJSONZoneWeb
		{
			public $AdaptTableauDonnees ;
			public $ValeurParamStart ;
			public $ValeurParamLimit ;
			protected function DetermineParams()
			{
				$this->ValeurParamStart = intval(_GET_def('start')) ;
				$this->ValeurParamLimit = intval(_GET_def('limit')) ;
				if($this->ValeurParamLimit <= 1)
				{
					$this->ValeurParamLimit = 25 ;
				}
			}
			protected function ConstruitResultat()
			{
				$this->Resultat = new PvResultReadTablDonneesExtJS() ;
				$this->DetermineParams() ;
				if($this->EstNul($this->AdaptTableauDonnees))
				{
					return ;
				}
				$defCols = $this->AdaptTableauDonnees->ObtientDefColsRendu() ;
				$fourn = & $this->AdaptTableauDonnees->FournisseurDonnees ;
				$elemsBruts = $this->AdaptTableauDonnees->FournisseurDonnees->RangeeElements($defCols, $this->AdaptTableauDonnees->FiltresSelection, $this->ValeurParamStart, $this->ValeurParamLimit, 0, 'asc') ;
				$this->Resultat->lgns = ($this->AdaptTableauDonnees->ExtraireValeursElements) ? $this->AdaptTableauDonnees->ObtientValeursExtraites($elemsBruts) : $elemsBruts ;
			}
		}
		class PvResultReadTablDonneesExtJS
		{
			public $succes = true ;
			public $lgns = array() ;
		}
		
		class PvPanelTableauDonneesExtJS extends PvPanelExtJS
		{
			public $ColsExtJS ;
			public $ActRead ;
			public $FormFiltres ;
			public $GridPanelDonnees ;
			public $Store ;
			public $Model ;
			public $Navigateur ;
			public $ToolbarFiltres ;
			public $ToolbarCmds ;
			public $BtnSoumetFiltres ;
		}
	}
	
?>