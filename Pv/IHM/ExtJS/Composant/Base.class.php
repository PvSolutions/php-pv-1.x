<?php
	
	if(! defined('PV_COMPOSANT_BASE_EXT_JS'))
	{
		if(! defined('PV_COMPOSANT_NOYAU_EXT_JS'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_WIDGET_EXT_JS'))
		{
			include dirname(__FILE__)."/Widget.class.php" ;
		}
		if(! defined('PV_COMPOSANT_DONNEES_EXT_JS'))
		{
			include dirname(__FILE__)."/Donnees.class.php" ;
		}
		define('PV_COMPOSANT_BASE_EXT_JS', 1) ;
	}
	
?>