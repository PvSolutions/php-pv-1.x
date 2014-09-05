<?php
	
	if(! defined('PV_IHM_ADMIN_DIRECTE'))
	{
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/Simple.class.php" ;
		}
		if(! defined('PV_ZONE_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/AdminDirecte/Zone.class.php" ;
		}
		define('PV_IHM_ADMIN_DIRECTE', 1) ;
	}
	
?>