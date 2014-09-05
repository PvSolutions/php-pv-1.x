<?php
	
	if(! defined('ENTETE_PAGE_WEB_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../../../../_PVIEW/Pv/IHM/Compose.class.php" ;
		}
		define('ENTETE_PAGE_WEB_WSM', 1) ;
		
		class BarreSommetPageBaseWebWsm extends PvPortionRenduHtml
		{
		}
	}
	
?>