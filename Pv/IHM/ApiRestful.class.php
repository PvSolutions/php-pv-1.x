<?php
	
	if(! defined('PV_API_BASE_RESTFUL'))
	{
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../Base.class.php" ;
		}
		if(! defined('PV_API_RESTFUL'))
		{
			include dirname(__FILE__)."/Restful/Api.class.php" ;
		}
		define('PV_API_BASE_RESTFUL', 1) ;
	}
	
?>