<?php
	
	if(! defined('COMP_PAGE_WEB_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../../Pv/IHM/Compose.class.php" ;
		}
		if(! defined('NOYAU_COMPOSANT_WSM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('VISIONNEUSE_SITE_WEB'))
		{
			include dirname(__FILE__)."/Visionneuse.class.php" ;
		}
		if(! defined('ELEMENT_FORM_SITE_WEB'))
		{
			include dirname(__FILE__)."/ElementFormulaire.class.php" ;
		}
		if(! defined('COMP_BASE_DONNEES_WSM'))
		{
			include dirname(__FILE__)."/BaseDonnees.class.php" ;
		}
		if(! defined('ENTETE_PAGE_WEB_WSM'))
		{
			include dirname(__FILE__)."/EntetePageWeb.class.php" ;
		}
		if(! defined('COMP_PAGE_AFFICH_WSM'))
		{
			include dirname(__FILE__)."/PageAffich.class.php" ;
		}
		define('COMP_PAGE_WEB_WSM', 1) ;
	}
	
?>