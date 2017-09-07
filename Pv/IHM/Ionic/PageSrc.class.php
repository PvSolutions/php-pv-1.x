<?php
	
	if(! defined('PV_PAGE_SRC_BASE_IONIC'))
	{
		if(! defined('PV_PAGE_SRC_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/PageSrc/Noyau.class.php" ;
		}
		if(! defined('PV_PAGE_SRC_MEMBERSHIP_IONIC'))
		{
			include dirname(__FILE__)."/PageSrc/Membership.class.php" ;
		}
		define('PV_PAGE_SRC_BASE_IONIC', 1) ;
	}
	
?>