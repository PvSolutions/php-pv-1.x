<?php
	
	if(! defined('SCRIPT_BASE_WSM'))
	{
		if(! defined('PV_SCRIPT_COMPOSE_IHM'))
		{
			include dirname(__FILE__)."/../../../../../_PVIEW/Pv/IHM/Compose/Script.class.php" ;
		}
		if(! defined('SCRIPT_NOYAU_WSM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('SCRIPT_PUBLIQUE_WSM'))
		{
			include dirname(__FILE__)."/Publique.class.php" ;
		}
		if(! defined('SCRIPT_ADMIN_WSM'))
		{
			include dirname(__FILE__)."/Admin.class.php" ;
		}
		define('SCRIPT_BASE_WSM', 1) ;
	}
	
?>