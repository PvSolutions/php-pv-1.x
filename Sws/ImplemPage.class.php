<?php
	
	if(! defined('IMPLEM_PAGE_SWS'))
	{
		if(! defined('NOYAU_IMPLEM_PAGE_SWS'))
		{
			include dirname(__FILE__).'/ImplemPage/Noyau.class.php' ;
		}
		if(! defined('IMPLEM_COMMENTAIRE_SWS'))
		{
			include dirname(__FILE__).'/ImplemPage/Commentaire.class.php' ;
		}
		if(! defined('IMPLEM_SHOPPING_SWS'))
		{
			include dirname(__FILE__).'/ImplemPage/Shopping.class.php' ;
		}
	}
	
?>