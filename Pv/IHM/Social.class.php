<?php
	
	if(! defined('PV_IHM_SOCIAL'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_SOCIAL'))
		{
			include dirname(__FILE__)."/Simple/ComposantIU/Social.class.php" ;
		}
		define('PV_IHM_SOCIAL', 1) ;
	}
	
?>