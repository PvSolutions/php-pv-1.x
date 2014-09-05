<?php
	
	if(! defined('COMPOSANT_IU_CHARISMA'))
	{
		if(! defined('NOYAU_COMPOSANT_IU_CHARISMA'))
		{
			include dirname(__FILE__)."/ComposantIU/Noyau.class.php" ;
		}
		if(! defined('COMPOSANT_IU_ZONE_CHARISMA'))
		{
			include dirname(__FILE__)."/ComposantIU/Zone.class.php" ;
		}
		if(! defined('COMPOSANT_IU_FEATURES_CHARISMA'))
		{
			include dirname(__FILE__)."/ComposantIU/UiFeatures.class.php" ;
		}
		if(! defined('COMPOSANT_IU_CONTENEURS_CHARISMA'))
		{
			include dirname(__FILE__)."/ComposantIU/Conteneurs.class.php" ;
		}
		define('COMPOSANT_IU_CHARISMA', 1) ;
	}
	
?>