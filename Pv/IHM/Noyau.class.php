<?php
	
	if(! defined('PV_NOYAU_IHM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		define('PV_NOYAU_IHM', 1) ;
		
		class PvRemplisseurConfigBase extends PvObjet
		{
		}
	}
	
?>