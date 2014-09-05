<?php
	
	if(! defined('PV_GENERATEUR_BASE'))
	{
		if(! defined('PV_GENERATEUR_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_GENER_ZONE_INCLUS'))
		{
			include dirname(__FILE__)."/Zone/Inclusion.class.php" ;
		}
		if(! defined('PV_GENER_ZONE_SIMPLE'))
		{
			include dirname(__FILE__)."/Zone/Simple.class.php" ;
		}
		define('PV_GENERATEUR_BASE', 1) ;
	}
	
?>