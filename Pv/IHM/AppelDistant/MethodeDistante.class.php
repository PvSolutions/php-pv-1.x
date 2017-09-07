<?php
	
	if(! defined('PV_METHODE_DISTANTE_BASE'))
	{
		if(! defined('PV_NOYAU_METHODE_DISTANTE'))
		{
			include dirname(__FILE__)."/MethodeDistante/Noyau.class.php" ;
		}
		define('PV_METHODE_DISTANTE_BASE', 1) ;
	}
	
?>