<?php
	
	if(! defined('MODULE_PAGE_RACINE_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__).'/Noyau.class.php' ;
		}
		if(! defined('COMPOSANT_UI_MODULE_BASE_SWS'))
		{
			include dirname(__FILE__).'/ComposantIU/Noyau.class.php' ;
		}
		define('MODULE_PAGE_RACINE_SWS', 1) ;
		
		class ModulePageRacineSws extends ModulePageBaseSws
		{
			public $NomRef = "racine" ;
			public $TitreMenu = "Accueil" ;
			public $ScriptAccueil ;
			public $FournitFluxRSS = 1 ;
			protected function CreeActionFluxRSS()
			{
				return new ActionFluxRSSRacineSws() ;
			}
			protected function CreeScriptAccueil()
			{
				return new ScriptAccueilBaseSws() ;
			}
			protected function CreeScriptAccueilAdmin()
			{
				return new ScriptAccueilAdminBaseSws() ;
			}
			public function RemplitZonePublValide(& $zone)
			{
				$this->ScriptAccueil = $this->InsereScript($zone->NomScriptParDefaut, $this->CreeScriptAccueil(), $zone) ;
			}
			public function RemplitZoneAdminValide(& $zone)
			{
				$this->ScriptAccueil = $this->InsereScript($zone->NomScriptParDefaut, $this->CreeScriptAccueilAdmin(), $zone) ;
			}
		}
		
		class ScriptAccueilBaseSws extends ScriptBaseSws
		{
			protected function RenduDispositifBrut()
			{
				return "Bienvenue sur le site web !!!" ;
			}
		}
		class ScriptAccueilAdminBaseSws extends ScriptBaseSws
		{
			public $AliasMsgBienvenue = "" ;
			public $GrilleModules ;
			public $CtnMsgBienvenue = "Bienvenue sur l'espace d'administration" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->GrilleModules = new GrilleModulesSws() ;
				$this->GrilleModules->AdopteScript("grilleModules", $this) ;
				$this->GrilleModules->ChargeConfig() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div align="center">' ;
				$ctn .= '<p align="center">'.$this->CtnMsgBienvenue.'</p>' ;
				if($this->ZoneParent->PossedeMembreConnecte())
				{
					$ctn .= $this->GrilleModules->RenduDispositif() ;
				}
				else
				{
					$ctn .= '<p><a href="'.$this->ZoneParent->ScriptConnexion->ObtientUrl().'">CONNEXION</a></p>' ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class ActionFluxRSSRacineSws extends ActionFluxRSSModuleSws
		{
		}
	}
	
?>