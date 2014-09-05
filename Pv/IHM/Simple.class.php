<?php
	
	if(! defined('PV_IHM_SIMPLE'))
	{
		if(! defined('PV_IHM_NOYAU_SIMPLE'))
		{
			include dirname(__FILE__)."/Simple/Noyau.class.php" ;
		}
		if(! defined('PV_MENU_IHM'))
		{
			include dirname(__FILE__)."/Menu.class.php" ;
		}
		if(! defined('PV_COMPOSANT_IU_SIMPLE'))
		{
			include dirname(__FILE__)."/Simple/ComposantIU.class.php" ;
		}
		if(! defined('PV_IHM_ZONE_SIMPLE'))
		{
			include dirname(__FILE__)."/Simple/Zone.class.php" ;
		}
		if(! defined('PV_IHM_SCRIPT_SIMPLE'))
		{
			include dirname(__FILE__)."/Simple/Script.class.php" ;
		}
		define('PV_IHM_SIMPLE', 1) ;
	}
	
?>