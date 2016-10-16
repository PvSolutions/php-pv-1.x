<?php
	
	if(! defined('PV_BASE_PLATF_SVC_WEB'))
	{
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/../Simple.class.php" ;
		}
		if(! defined('PV_MEMBERSHIP_PLATF_SVC_WEB'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		if(! defined('PV_ACTION_PLATF_SVC_WEB'))
		{
			include dirname(__FILE__)."/Action.class.php" ;
		}
		if(! defined('PV_COMPOSANT_UI_PLATF_SVC_WEB'))
		{
			include dirname(__FILE__)."/ComposantIU.class.php" ;
		}
		if(! defined('PV_SCRIPT_PLATF_SVC_WEB'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		if(! defined('PV_ZONE_PLATF_SVC_WEB'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		define('PV_BASE_PLATF_SVC_WEB', 1) ;
	}
	
?>