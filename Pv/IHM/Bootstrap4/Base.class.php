<?php
	
	if(! defined('PV_BASE_BOOTSTRAP4'))
	{
		if(! defined('PV_COMPOSANT_IU_BOOTSTRAP4'))
		{
			include dirname(__FILE__)."/ComposantIU.class.php" ;
		}
		if(! defined('PV_ELEMENT_FORMULAIRE_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/../Bootstrap/ElementFormulaire.class.php" ;
		}
		if(! defined('PV_SLIDER_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/../Bootstrap/Slider.class.php" ;
		}
		if(! defined('PV_ELEMENT_FORMULAIRE_BOOTSTRAP4'))
		{
			include dirname(__FILE__)."/ElementFormulaire.class.php" ;
		}
		if(! defined('PV_MEMBERSHIP_BOOTSTRAP4'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		if(! defined('PV_ZONE_BOOTSTRAP4'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		define('PV_BASE_BOOTSTRAP4', 1) ;
	}
	
?>