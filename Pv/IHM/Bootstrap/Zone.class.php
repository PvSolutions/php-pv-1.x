<?php
	
	if(! defined('PV_ZONE_BOOTSTRAP'))
	{
		define('PV_ZONE_BOOTSTRAP', 1) ;
		
		class PvZoneBootstrap extends PvZoneWebSimple
		{
			public $ViewportMeta = "width=device-width, initial-scale=1, maximum-scale=1" ;
			public $CheminJsBootstrap = "js/bootstrap.min.js" ;
			public $CheminCSSBootstrap = "css/bootstrap.min.css" ;
			public $CheminCSSThemeBootstrap = "css/bootstrap-theme.min.css" ;
			public $InclureJQuery = 1 ;
			public function InclutLibrairiesExternes()
			{
				if(! $this->InclureJQuery)
				{
					$this->InclureJQuery = 1 ;
				}
				parent::InclutLibrairiesExternes() ;
				// Placer Bootstrap après JQuery
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $this->CheminJsBootstrap ;
				array_splice($this->ContenusJs, 1, 0, array($ctnJs)) ;
				// Inscrire CSS
				$this->InscritLienCSS($this->CheminCSSBootstrap) ;
			}
		}
	}
	
?>