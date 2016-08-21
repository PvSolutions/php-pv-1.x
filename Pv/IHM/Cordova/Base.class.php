<?php
	
	if(! defined('PV_IHM_CORDOVA_BASE'))
	{
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../../../Pv/Base.class.php" ;
		}
		if(! defined('PV_MENU_CORDOVA'))
		{
			include dirname(__FILE__)."/Menu.class.php" ;
		}
		if(! defined('PV_COMPOSANT_CORDOVA'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('PV_ZONE_CORDOVA'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		define('PV_IHM_CORDOVA_BASE', 1) ;
	}
	
?>