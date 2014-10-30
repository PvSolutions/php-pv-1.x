<?php
	
	if(! defined('PV_COMPOSANT_IU_BOOTSTRAP'))
	{
		if(! defined('PV_NOYAU_COMPOSANT_IU_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU/Noyau.class.php" ;
		}
		if(! defined('PV_MENU_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU/Menu.class.php" ;
		}
		if(! defined('PV_COMPOSANT_MEMBERSHIP_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU/Membership.class.php" ;
		}
		if(! defined('PV_COMMANDE_DONNEES_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU/Commande.class.php" ;
		}
		if(! defined('PV_ELEMENT_FORMULAIRE_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU/ElementFormulaire.class.php" ;
		}
		if(! defined('PV_TABLEAU_DONNEES_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU/Tableau.class.php" ;
		}
		if(! defined('PV_FORMULAIRE_DONNEES_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ComposantIU/Formulaire.class.php" ;
		}
		define('PV_COMPOSANT_IU_BOOTSTRAP', 1) ;
	}
	
?>