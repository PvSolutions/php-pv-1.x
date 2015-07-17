<?php
	
	if(! defined('PV_EXT_JS'))
	{
		if(! defined('PV_ZONE_EXT_JS'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		define('PV_EXT_JS', 1) ;
	}
	
?>