<?php
	
	if(! defined('MDL_PAGE_RACINE_WSM'))
	{
		if(! defined('MDL_PAGE_BASE'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('MDL_PAGE_RACINE_WSM', 1) ;
		
		class ModelePageRacineWsm extends ModelePageDefautWsm
		{
		}
	}
	
?>