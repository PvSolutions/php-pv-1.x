<?php
	
	if(! defined('PV_COMPOSANT_DONNEES_EXT_JS'))
	{
		if(! defined('PV_COMPOSANT_NOYAU_EXT_JS'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_COMPOSANT_DONNEES_EXT_JS', 1) ;
		
		class PvConfigGridPanelExtJS extends PvConfigWidgetExtJS
		{
			public $store ;
			public $columns = array() ;
		}
		class PvConfigFormExtJS extends PvConfigWidgetExtJS
		{
		}
		class PvGridPanelExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.grid.Panel" ;
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigGridPanelExtJS() ;
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
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteScript($nom, $this->ZoneParent->ScriptParDefaut) ;
				return $filtre ;
			}
			public function & InsereFltSelectExtJS($nom, $valeur, $exprDonnees='', $nomClsComp='')
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
				// Model
				$panel->Model = $widget->ZoneParent->ApplicationExtJS->InsereModel('model_'.$this->IDInstanceCalc, new PvModelExtJS()) ;
				$panel->Model->ChargeConfig() ;
				foreach($this->DefinitionsColonnes as $i => $defCol)
				{
					$panel->Model->CfgDefExtJS->fields[] = $defCol->IDInstanceCalc ;
				}
				// Store
				$panel->Store = $widget->ZoneParent->ApplicationExtJS->InsereStore('store_'.$this->IDInstanceCalc, new PvStoreExtJS()) ;
				$panel->Store->ChargeConfig() ;
				$panel->Store->CfgDefExtJS->model = $panel->Model->ObtientNomClasseExtJS() ;
				$panel->Store->CfgDefExtJS->proxy->api->read = $panel->ActRead->ObtientUrl() ;
				// production form filtres
				$panel->FormFiltres = $panel->InsereElementExtJS('formFiltres', new PvFormExtJS()) ;
				$panel->FormFiltres->ChargeConfig() ;
				foreach($this->FiltresSelection as $i => & $filtre)
				{
					if($filtre->TypeLiaisonParametre != "extjs")
						continue ;
					$compFlt = $filtre->ObtientComposant() ;
					$compFlt->ChargeConfig() ;
					$compFlt->CfgCreaExtJS->name = $filtre->NomParametreLie ;
					$compFlt->CfgCreaExtJS->fieldLabel = $filtre->ObtientLibelle() ;
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
				return $panel ;
			}
		}
		
		class PvActReadTableauDonneesExtJS extends PvActionResultatJSONZoneWeb
		{
			public $AdaptTableauDonnees ;
			protected function ConstruitResultat()
			{
				$this->Resultat = new PvResultReadTablDonneesExtJS() ;
			}
		}
		class PvResultReadTablDonneesExtJS
		{
			public $success = true ;
			public $rows = array() ;
		}
		
		class PvPanelTableauDonneesExtJS extends PvPanelExtJS
		{
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