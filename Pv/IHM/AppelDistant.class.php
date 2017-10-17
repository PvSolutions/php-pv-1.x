<?php
	
	if(! defined('PV_IHM_APPEL_DISTANT'))
	{
		if(! defined('PV_ZONE_APPEL_DISTANT'))
		{
			include dirname(__FILE__)."/AppelDistant/Zone.class.php" ;
		}
		define('PV_IHM_APPEL_DISTANT', 1) ;
	}
	
?>