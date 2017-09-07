<?php
	
	if(! defined('PV_METHODE_DISTANTE_BASE_IONIC'))
	{
		if(! defined('PV_METHODE_DISTANTE_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/MethodeDistante/Noyau.class.php" ;
		}
		if(! defined('PV_METHODE_DISTANTE_MEMBERSHIP_IONIC'))
		{
			include dirname(__FILE__)."/MethodeDistante/Membership.class.php" ;
		}
		if(! defined('PV_METHODE_DISTANTE_GOOGLEMAPS_IONIC'))
		{
			include dirname(__FILE__)."/MethodeDistante/GoogleMaps.class.php" ;
		}
		define('PV_METHODE_DISTANTE_BASE_IONIC', 1) ;
	}
	
?>