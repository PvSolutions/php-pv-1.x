<?php
	
	if(! defined('PV_ROUTE_BASE_RESTFUL'))
	{
		if(! defined('PV_ROUTE_NOYAU_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Noyau.class.php" ;
		}
		if(! defined('PV_ROUTE_COLLECTION_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Collection.class.php" ;
		}
		if(! defined('PV_ROUTE_INDIVIDUEL_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Individuel.class.php" ;
		}
		if(! defined('PV_ROUTE_ACCES_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Acces.class.php" ;
		}
		if(! defined('PV_ROUTE_MON_ESPACE_RESTFUL'))
		{
			include dirname(__FILE__)."/route/MonEspace.class.php" ;
		}
		define('PV_ROUTE_BASE_RESTFUL', 1) ;
	}
	
?>