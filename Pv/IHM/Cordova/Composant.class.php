<?php
	
	if(! defined('PV_COMPOSANT_CORDOVA'))
	{
		if(! defined('PV_COMPOSANT_NOYAU_CORDOVA'))
		{
			include dirname(__FILE__)."/Composant/Noyau.class.php" ;
		}
		if(! defined('PV_TABLEAU_DONNEES_CORDOVA'))
		{
			include dirname(__FILE__)."/Composant/TableauDonnees.class.php" ;
		}
		if(! defined('PV_FORMULAIRE_DONNEES_CORDOVA'))
		{
			include dirname(__FILE__)."/Composant/FormulaireDonnees.class.php" ;
		}
		define('PV_COMPOSANT_CORDOVA', 1) ;
	}
	
?>