<?php
	
	if(! defined('PV_SB_ADMIN_2'))
	{
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../Base.class.php" ;
		}
		if(! defined('PV_ZONE_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/SbAdmin2/Zone.class.php" ;
		}
		define('PV_SB_ADMIN_2', 1) ;
	}
	
?>