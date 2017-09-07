<?php
	
	if(! defined('PV_COMPOSANT_IU_BASE_IONIC'))
	{
		if(! defined('PV_COMPOSANT_IU_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/ComposantIU/Noyau.class.php" ;
		}
		if(! defined('PV_FILTRE_IU_IONIC'))
		{
			include dirname(__FILE__)."/ComposantIU/Filtre.class.php" ;
		}
		if(! defined('PV_COMMANDE_IU_IONIC'))
		{
			include dirname(__FILE__)."/ComposantIU/Commande.class.php" ;
		}
		if(! defined('PV_ELEMENT_FORMULAIRE_IU_IONIC'))
		{
			include dirname(__FILE__)."/ComposantIU/ElementFormulaire.class.php" ;
		}
		if(! defined('PV_FORMULAIRE_DONNEES_IONIC'))
		{
			include dirname(__FILE__)."/ComposantIU/FormulaireDonnees.class.php" ;
		}
		if(! defined('PV_TABLEAU_DONNEES_IONIC'))
		{
			include dirname(__FILE__)."/ComposantIU/TableauDonnees.class.php" ;
		}
		define('PV_COMPOSANT_IU_BASE_IONIC', 1) ;
	}
	
?>