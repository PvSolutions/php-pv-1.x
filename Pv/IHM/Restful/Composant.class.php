<?php
	
	if(! defined('PV_COMPOSANT_RESTFUL'))
	{
		if(! defined('PV_COMPOSANT_BASE_RESTFUL'))
		{
			include dirname(__FILE__)."/composant/Noyau.class.php" ;
		}
		if(! defined('PV_TABLEAU_DONNEES_RESTFUL'))
		{
			include dirname(__FILE__)."/composant/TableauDonnees.class.php" ;
		}
		if(! defined('PV_FORMULAIRE_DONNEES_RESTFUL'))
		{
			include dirname(__FILE__)."/composant/FormulaireDonnees.class.php" ;
		}
	}
	
?>