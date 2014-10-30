<?php
	
	if(! defined('PV_ZONE_BOOTSTRAP'))
	{
		if(! defined('PV_NOYAU_ZONE_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Zone/Noyau.class.php" ;
		}
		if(! defined('PV_ZONE_ADMIN1BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Zone/Admin1.class.php" ;
		}
		if(! defined('PV_ZONE_SB_ADMIN'))
		{
			include dirname(__FILE__)."/Zone/SbAdmin.class.php" ;
		}
		define('PV_ZONE_BOOTSTRAP', 1) ;
	}
	
?>