<?php
	
	if(! defined('PV_SCRIPT_BOOTSTRAP'))
	{
		if(! defined('PV_NOYAU_SCRIPT_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Script/Noyau.class.php" ;
		}
		define('PV_SCRIPT_BOOTSTRAP', 1) ;
	}
	
?>