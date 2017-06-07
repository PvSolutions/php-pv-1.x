<?php
	
	if(! defined('PV_BASE_BOOTSTRAP'))
	{
		if(! defined('PV_ELEMENT_FORMULAIRE_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/ElementFormulaire.class.php" ;
		}
		if(! defined('PV_SLIDER_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Slider.class.php" ;
		}
		if(! defined('PV_MEMBERSHIP_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		if(! defined('PV_ZONE_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		define('PV_BASE_BOOTSTRAP', 1) ;
	}
	
?>