<?php
	
	if(! defined('PV_SERVICE_SRC_BASE_IONIC'))
	{
		if(! defined('PV_SERVICE_SRC_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/ServiceSrc/Noyau.class.php" ;
		}
		if(! defined('PV_SERVICE_SRC_MEMBERSHIP_IONIC'))
		{
			include dirname(__FILE__)."/ServiceSrc/Membership.class.php" ;
		}
		define('PV_SERVICE_SRC_BASE_IONIC', 1) ;
	}
	
?>