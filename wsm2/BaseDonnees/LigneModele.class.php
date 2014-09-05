<?php
	
	if(! defined('LIGNE_MODELE_BD_WSM'))
	{
		if(! defined('MDL_PAGE_NOYAU_WSM'))
		{
			include dirname(__FILE__)."/ModelePage/Noyau.class.php" ;
		}
		if(! defined('MDL_PAGE_DEFAUT_WSM'))
		{
			include dirname(__FILE__)."/ModelePage/Defaut.class.php" ;
		}
		if(! defined('MDL_PAGE_RACINE_WSM'))
		{
			include dirname(__FILE__)."/ModelePage/Racine.class.php" ;
		}
		if(! defined('MDL_PAGE_COMMENTAIRE_WSM'))
		{
			include dirname(__FILE__)."/ModelePage/Commentaire.class.php" ;
		}
		define('LIGNE_MODELE_BD_WSM', 1) ;
		
	}
	
?>