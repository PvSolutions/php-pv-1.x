<?php
	
	if(! defined('BASE_WSM'))
	{
		if(! defined('NOYAU_WSM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('ZONE_WSM'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		if(! defined('APPLICATION_WSM'))
		{
			include dirname(__FILE__)."/Application.class.php" ;
		}
		define('BASE_WSM', 1) ;
	}
	
?>