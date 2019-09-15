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
		if(! defined('PV_ROUTE_CREATION_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Creation.class.php" ;
		}
		if(! defined('PV_ROUTE_MAJ_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Maj.class.php" ;
		}
		if(! defined('PV_ROUTE_SUPPRESSION_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Suppression.class.php" ;
		}
		if(! defined('PV_ROUTE_ACCES_RESTFUL'))
		{
			include dirname(__FILE__)."/route/Acces.class.php" ;
		}
		define('PV_ROUTE_BASE_RESTFUL', 1) ;
	}
	
?>