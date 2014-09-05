<?php
	
	if(! defined('PV_BASE'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_IHM'))
		{
			include dirname(__FILE__)."/IHM.class.php" ;
		}
		if(! defined('PV_PROCESSUS_PERSISTANT'))
		{
			include dirname(__FILE__)."/ProcessusPersistant.class.php" ;
		}
		if(! defined('PV_SERVICE_REQUETE'))
		{
			include dirname(__FILE__)."/ServiceRequete.class.php" ;
		}
	}
	
?>