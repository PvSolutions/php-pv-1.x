<?php
	
	if(! defined('BASE_MORECUST'))
	{
		if(! defined('CORE_MORECUST'))
		{
			include dirname(__FILE__)."/Core.class.php" ;
		}
		if(! defined('PERIOD_MORECUST'))
		{
			include dirname(__FILE__)."/Period.class.php" ;
		}
		if(! defined('ENVIRONMENT_MORECUST'))
		{
			include dirname(__FILE__)."/Environment.class.php" ;
		}
		if(! defined('ACTION_MORECUST'))
		{
			include dirname(__FILE__)."/Action.class.php" ;
		}
		if(! defined('SYSTEM_MORECUST'))
		{
			include dirname(__FILE__)."/System.class.php" ;
		}
		define('BASE_MORECUST', 1) ;
	}
	
?>