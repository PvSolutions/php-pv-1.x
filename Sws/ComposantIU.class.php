<?php
	
	if(! defined('COMPOSANT_IU_SWS'))
	{
		if(! defined('COMPOSANT_IU_BASE_SWS'))
		{
			include dirname(__FILE__)."/ComposantIU/Noyau.class.php" ;
		}
		if(! defined('COMPOSANT_IU_ENTITE_SWS'))
		{
			include dirname(__FILE__)."/ComposantIU/Entite.class.php" ;
		}
		if(! defined('EDITEUR_SITE_WEB_SWS'))
		{
			include dirname(__FILE__)."/ComposantIU/EditeurSiteWeb.class.php" ;
		}
		define('COMPOSANT_IU_SWS', 1) ;
	}
	
?>