<?php
	
	if(! defined('BD_WSM'))
	{
		if(! defined('NOYAU_BD_WSM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('LIGNE_MODELE_BD_WSM'))
		{
			include dirname(__FILE__)."/LigneModele.class.php" ;
		}
		if(! defined('LIGNE_PAGE_BD_WSM'))
		{
			include dirname(__FILE__)."/LignePage.class.php" ;
		}
		define('BD_WSM', 1) ;
	}
	
?>