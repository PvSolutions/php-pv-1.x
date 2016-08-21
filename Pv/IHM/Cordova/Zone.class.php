<?php
	
	if(! defined('PV_ZONE_CORDOVA'))
	{
		if(! defined('PV_ZONE_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Simple/Zone.class.php" ;
		}
		define('PV_ZONE_CORDOVA', 1) ;
		
		class PvZoneCordovaBase extends PvZoneWebSimple
		{
			protected function RenduJsSpec()
			{
				$ctn = 'var PvCordovaDocument = {
	function loadUrl(url) {
		oldlocation = window.location.href ;
		prevParser = document.createElement("a") ;
		currParser = document.createElement("a") ;
		prevParser.href = url ;
		currParser.href = oldlocation ;
		window.location.href = url ;
		if(prevParser.host == currParser.host)
			window.location.reload() ;
	}
}' ;
				return $ctn ;
			}
			public function InclutLibrairiesExternes()
			{
				parent::InclutLibrairiesExternes() ;
				$this->InscritContenuJs($this->RenduJsSpec()) ;
			}
		}
	}
	
?>