<?php
	
	if(! defined('PV_BASE_BOOTSTRAP'))
	{
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/../Simple.class.php" ;
		}
		if(! defined('PV_COMPOSANT_IU_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU.class.php" ;
		}
		if(! defined('PV_SCRIPT_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		if(! defined('PV_ZONE_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		define('PV_BASE_BOOTSTRAP', 1) ;
	}
	
?>