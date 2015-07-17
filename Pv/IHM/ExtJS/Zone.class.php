<?php
	
	if(! defined('PV_ZONE_EXT_JS'))
	{
		if(! defined('PV_ZONE_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Simple/Zone.class.php" ;
		}
		if(! defined('PV_COMPOSANT_BASE_EXT_JS'))
		{
			include dirname(__FILE__)."/Composant/Base.class.php" ;
		}
		define('PV_ZONE_EXT_JS', 1) ;
		
		class PvZoneBaseExtJs extends PvZoneWebSimple
		{
			public $CheminFichierIncExtJs = "js/extjs-shared/include-ext.js" ;
			public $CheminFichierOptToolbarExtJs = "js/extjs-shared/options-toolbar.js" ;
			public $InclureOptToolbarExtJs = 1 ;
			public $RenduExtraHead = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' ;
			public $NomAppExtJS = "MonApp" ;
			public $ApplicationExtJS ;
			public function InclutLibrairiesExternes()
			{
				parent::InclutLibrairiesExternes() ;
				// Opts toolbar d'abord
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $this->RenduApplicationExtJS() ;
				array_splice($this->ContenusJs, 0, 0, array($ctnJs)) ;
				if($this->InclureOptToolbarExtJs == 1)
				{
					// Opts toolbar d'abord
					$ctnJs = new PvLienFichierJs() ;
					$ctnJs->Src = $this->CheminFichierOptToolbarExtJs ;
					array_splice($this->ContenusJs, 0, 0, array($ctnJs)) ;
				}
				// Inclusion lib all ensuite :/
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $this->CheminFichierIncExtJs ;
				array_splice($this->ContenusJs, 0, 0, array($ctnJs)) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeApplicationExtJS() ;
			}
			protected function ChargeScripts()
			{
				$this->InsereScriptParDefaut(new PvScriptWebSimple()) ;
			}
			protected function ChargeApplicationExtJS()
			{
				$this->ApplicationExtJS = $this->CreeApplicationExtJS() ;
				$this->ApplicationExtJS->AdopteZone("appExtJS", $this) ;
				$this->ApplicationExtJS->ChargeConfig() ;
			}
			protected function RenduApplicationExtJS()
			{
				if($this->EstNul($this->ApplicationExtJS))
				{
					return '// Application ExtJS non definie' ;
				}
				return $this->ApplicationExtJS->RenduComposantExtJS() ;
			}
			protected function & ControllerExtJS()
			{
				return $this->ApplicationExtJS->ControllerParDefaut ;
			}
			protected function & ViewportExtJS()
			{
				return $this->ApplicationExtJS->Viewport ;
			}
			protected function CreeApplicationExtJS()
			{
				return new PvApplicationExtJS() ;
			}
		}
	}
	
?>