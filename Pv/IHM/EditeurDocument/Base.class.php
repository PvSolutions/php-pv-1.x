<?php
	
	if(! defined('PV_EDITEUR_DOCUMENT_BASE'))
	{
		if(! defined('PV_ZONE_EDITEUR_DOCUMENT'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		
		define('PV_EDITEUR_DOCUMENT_BASE', 1) ;
	}
	
?>