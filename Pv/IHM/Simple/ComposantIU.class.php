<?php
	
	if(! defined('PV_COMPOSANT_IU_SIMPLE'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
		{
			include dirname(__FILE__)."/FournisseurDonnees.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_BASE'))
		{
			include dirname(__FILE__)."/ComposantIU/Noyau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_ELEM_FORM'))
		{
			include dirname(__FILE__)."/ComposantIU/ElementFormulaire.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_CRITERE'))
		{
			include dirname(__FILE__)."/ComposantIU/Critere.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_ACT_CMD'))
		{
			include dirname(__FILE__)."/ComposantIU/ActCmd.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_FORMULAIRE'))
		{
			include dirname(__FILE__)."/ComposantIU/Formulaire.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_CHART'))
		{
			include dirname(__FILE__)."/ComposantIU/Chart.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_RAPPORT'))
		{
			include dirname(__FILE__)."/ComposantIU/Rapport.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_TABLEAU'))
		{
			include dirname(__FILE__)."/ComposantIU/Tableau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_ARBORESCENCE'))
		{
			include dirname(__FILE__)."/ComposantIU/Arborescence.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_MEMBERSHIP'))
		{
			include dirname(__FILE__)."/ComposantIU/Membership.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_ELEM_CSS'))
		{
			include dirname(__FILE__)."/ComposantIU/ElementCSS.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_ELEM_JS'))
		{
			include dirname(__FILE__)."/ComposantIU/ElementJS.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_JQUERY'))
		{
			include dirname(__FILE__)."/ComposantIU/JQuery.class.php" ;
		}
		define('PV_COMPOSANT_IU_SIMPLE', 1) ;
		
	}
	
?>