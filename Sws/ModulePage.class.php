<?php
	
	if(! defined('MODULE_PAGE_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__).'/ModulePage/Noyau.class.php' ;
		}
		if(! defined('MODULE_PAGE_RACINE_SWS'))
		{
			include dirname(__FILE__).'/ModulePage/Racine.class.php' ;
		}
		if(! defined('MODULE_MENU_SWS'))
		{
			include dirname(__FILE__).'/ModulePage/Menu.class.php' ;
		}
		if(! defined('MODULE_ARTICLE_SWS'))
		{
			include dirname(__FILE__).'/ModulePage/Article.class.php' ;
		}
		if(! defined('MODULE_SLIDER_SWS'))
		{
			include dirname(__FILE__).'/ModulePage/Slider.class.php' ;
		}
		if(! defined('MODULE_CONTACT_SWS'))
		{
			include dirname(__FILE__).'/ModulePage/LivreDOr.class.php' ;
		}
		if(! defined('MODULE_CONTACT_SWS'))
		{
			include dirname(__FILE__).'/ModulePage/Contact.class.php' ;
		}
		define('MODULE_PAGE_SWS', 1) ;
	}
	
?>