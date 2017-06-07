<?php
	
	if(! defined('PV_BASE_INTERFACE_PAIEMENT'))
	{
		if(! defined("PV_NOYAU_PASSERELLE_PAIEMENT"))
		{
			include dirname(__FILE__)."/Paiement/Noyau.class.php" ;
		}
		if(! defined("PV_PASSERELLE_PAIEMENT_ASSISTANCE"))
		{
			include dirname(__FILE__)."/Paiement/Assistance.class.php" ;
		}
		if(! defined("PV_PASSERELLE_PAIEMENT_CINETPAY"))
		{
			include dirname(__FILE__)."/Paiement/Cinetpay.class.php" ;
		}
		define('PV_BASE_INTERFACE_PAIEMENT', 1) ;
	}

?>