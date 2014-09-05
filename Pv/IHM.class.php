<?php
	
	if(! defined('PV_IHM'))
	{
		if(! defined('PV_NOYAU_IHM'))
		{
			include dirname(__FILE__)."/IHM/Noyau.class.php" ;
		}
		if(! defined('PV_SCRIPT_IHM'))
		{
			include dirname(__FILE__)."/IHM/Script.class.php" ;
		}
		if(! defined('PV_ZONE_IHM'))
		{
			include dirname(__FILE__)."/IHM/Zone.class.php" ;
		}
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/IHM/Simple.class.php" ;
		}
		define('PV_IHM', 1) ;
	}
	
?>