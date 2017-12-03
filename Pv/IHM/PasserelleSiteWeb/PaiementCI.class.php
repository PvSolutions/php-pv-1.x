<?php
	
	if(! defined('PV_INTERFACE_PAIEMENT_CIV'))
	{
		if(! defined('PV_BASE_INTERFACE_PAIEMENT'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined("PV_PASSERELLE_PAIEMENT_ASSISTANCE"))
		{
			include dirname(__FILE__)."/Paiement/Assistance.class.php" ;
		}
		if(! defined('PV_PASSERELLE_PAIEMENT_CINETPAY'))
		{
			include dirname(__FILE__)."/Paiement/Cinetpay.class.php" ;
		}
		if(! defined('PV_PASSERELLE_PAIEMENT_MOOVWEBTECH'))
		{
			include dirname(__FILE__)."/Paiement/MoovWebTech.class.php" ;
		}
	}
	
?>