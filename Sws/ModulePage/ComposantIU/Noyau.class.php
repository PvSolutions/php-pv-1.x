<?php
	
	if(! defined('COMPOSANT_UI_MODULE_BASE_SWS'))
	{
		define('COMPOSANT_UI_MODULE_BASE_SWS', 1) ;
		
		class GrilleModulesSws extends PvGrilleDonneesHtml
		{
			public $ContenuLigneModele = '<a href="${url}"><div align="center"><img src="${chemin_icone}" /></div><div align="center">${titre}</div></a>' ;
			public $AlignVCellule = "bottom" ;
			public $DefColTitre ;
			public $DefColUrl ;
			public $DefColCheminIcone ;
			public $MaxColonnes = 4 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeFournModules() ;
				$this->ChargeDefCols() ;
			}
			protected function ChargeFournModules()
			{
				$this->FournisseurDonnees = ReferentielSws::$SystemeEnCours->CreeFournModules() ;
				$this->FournisseurDonnees->RequeteSelection = "modules" ;
			}
			protected function ChargeDefCols()
			{
				$this->DefColTitre = $this->InsereDefCol("titre") ;
				$this->DefColUrl = $this->InsereDefCol("url") ;
				$this->DefColCheminIcone = $this->InsereDefCol("chemin_icone") ;
			}
		}
		class GrilleImplemsSws extends GrilleModulesSws
		{
			protected function ChargeFournModules()
			{
				$this->FournisseurDonnees = ReferentielSws::$SystemeEnCours->CreeFournImplems() ;
				$this->FournisseurDonnees->RequeteSelection = "implems" ;
			}
			
		}
		
		class TableauBordSws extends ComposantIUBaseSws
		{
			public $Blocs = array() ;
			public $MaxColonnes = 2 ;
			public function PossedeBlocsVisibles()
			{
				return count($this->Blocs) > 0 ;
			}
			public function & InsereBloc($bloc)
			{
				$bloc->AdopteScript($this->IDInstanceCalc.'_'.count($this->Blocs), $this) ;
				$this->Blocs[] = & $bloc ;
				return $bloc ;
			}
			public function & InsereBlocVide()
			{
				$bloc = $this->InsereBloc(new BlocTablBordSws()) ;
				return $bloc ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if(! $this->PossedeBlocsVisibles())
					return '' ;
				$ctn .= '<table width="100%" cellpadding="2" cellspacing="0">'.PHP_EOL ;
				foreach($this->Blocs as $i => & $bloc)
				{
					if(! $bloc->EstAccessible())
						continue ;
					if($i % $this->MaxColonnes == 0)
					{	
						$ctn .= '<tr>'.PHP_EOL ;
					}
					$ctn .= '<td align="center" valign="top">'.PHP_EOL ;
					$ctn .= '<div class="ui-widget ui-widget-content ui-state-active sws-ui-padding-4">'.PHP_EOL ;
					$ctn .= $bloc->ObtientTitre().PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
					$ctn .= '<div class="ui-widget ui-widget-content ui-state-default">'.PHP_EOL ;
					$ctn .= $bloc->RenduDispositif().PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
					$ctn .= '</td>'.PHP_EOL ;
					if($i % $this->MaxColonnes == $this->MaxColonnes - 1)
					{	
						$ctn .= '</tr>'.PHP_EOL ;
					}
				}
				$ctn .= '</table>'.PHP_EOL ;
				return $ctn ;
			}
		}
		
		class BlocTablBordSws extends ComposantIUBaseSws
		{
			public $LignesOccupees = 1 ;
			public $ColonnesOccupees = 1 ;
			public $Titre = "" ;
			public $MsgSiVide = "-- Aucun composant defini --" ;
			public $CheminIcone = "" ;
			protected $CompPrinc ;
			public function & DefinitCompPrinc($comp)
			{
				$this->CompPrinc = & $comp ;
				$comp->AdopteScript($this->NomInstanceCalc."_CompPrinc", $this->ScriptParent) ;
				$comp->ChargeConfig() ;
				return $comp ;
			}
			public function ObtientTitre()
			{
				return $this->Titre ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->EstPasNul($this->CompPrinc))
				{
					$ctn .= $this->CompPrinc->RenduDispositifBrut() ;
				}
				else
				{
					$ctn .= '<div class="ui-widget ui-widget-content">'.$this->MsgSiVide.'</div>'.PHP_EOL ;
				}
				return $ctn ;
			}
		}
		class BarreOngletsTablBordSws extends ComposantIUBaseSws
		{
			protected $Onglets = array() ;
			public $MaxOngletsAffiches = 4 ;
			protected $Onglet1 ;
			protected $Onglet2 ;
			protected $Onglet3 ;
			protected $Onglet4 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->Onglet1 = $this->InsereOnglet(new OngletTablBordSws()) ;
				$this->Onglet2 = $this->InsereOnglet(new OngletTablBordSws()) ;
				$this->Onglet3 = $this->InsereOnglet(new OngletTablBordSws()) ;
				$this->Onglet4 = $this->InsereOnglet(new OngletTablBordSws()) ;
			}
			public function & InsereOnglet($onglet)
			{
				$this->Onglets[] = & $onglet ;
				$onglet->OngletParent = & $this ;
				return $onglet ;
			}
		}
		class BarreLiensTablBordSws extends ComposantIUBaseSws
		{
			public $Titre ;
			public $BlocParent ;
			public $StyleRendu ;
			public $Liens = array() ;
			public $NomClsCSS = "ui-widget ui-widget-content";
			public function EstVide()
			{
				return count($this->Liens) == 0 ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div class="ui-widget ui-widget-content">'.PHP_EOL ;
				foreach($this->Liens as $i => $lien)
				{
					$ctn .= $lien->Rendu($this, $i) ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class OngletTablBordSws extends BarreLiensTablBordSws
		{
			public $OngletParent ;
		}
		
		class LienTablBordSws extends ComposantIUBaseSws
		{
			public $Url ;
			public $AttrsCSS ;
			public $CheminImage ;
			public $CheminIcone ;
			public $CheminMiniature ;
			public $Titre ;
			public $Description ;
		}
		
		class StyleRenduLienTablBordSws extends PvObjet
		{
			public function Rendu(& $comp, & $liens, $index)
			{
			}
		}
	}
	
?>