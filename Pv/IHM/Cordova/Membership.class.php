<?php
	
	if(! defined('PV_MEMBERSHIP_BASE_CORDOVA'))
	{
		if(! defined('PV_NOYAU_MEMBERSHIP_CORDOVA'))
		{
			include dirname(__FILE__)."/Membership/Noyau.class.php" ;
		}
		if(! defined('PV_ACCES_MEMBERSHIP_CORDOVA'))
		{
			include dirname(__FILE__)."/Membership/Acces.class.php" ;
		}
		if(! defined('PV_MEMBRE_MEMBERSHIP_CORDOVA'))
		{
			include dirname(__FILE__)."/Membership/Membre.class.php" ;
		}
		if(! defined('PV_PROFIL_MEMBERSHIP_CORDOVA'))
		{
			include dirname(__FILE__)."/Membership/Profil.class.php" ;
		}
		define('PV_MEMBERSHIP_BASE_CORDOVA', 1) ;
	}
	
?>