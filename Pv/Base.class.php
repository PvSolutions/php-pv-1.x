<?php
	
	if(! defined('PV_IGNORE_ERR_PHP'))
	{
		if(defined('E_DEPRECATED'))
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED) ;
		else
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT) ;
	}
	
	if(! defined('PV_BASE'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_IHM'))
		{
			include dirname(__FILE__)."/IHM.class.php" ;
		}
		if(! defined('PV_SERVICE_PERSISTANT'))
		{
			include dirname(__FILE__)."/ServicePersist.class.php" ;
		}
		if(! defined('PV_TACHE_PROG'))
		{
			include dirname(__FILE__)."/TacheProg.class.php" ;
		}
	}
	
?>