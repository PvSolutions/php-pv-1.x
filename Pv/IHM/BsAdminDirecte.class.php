<?php
	
	if(! defined('PV_IHM_BS_ADMIN_DIRECTE'))
	{
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/Simple.class.php" ;
		}
		if(! defined('PV_ZONE_BS_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/BsAdminDirecte/Zone.class.php" ;
		}
		define('PV_IHM_BS_ADMIN_DIRECTE', 1) ;
	}
	
?>