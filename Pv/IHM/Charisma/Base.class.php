<?php
	
	if(! defined('PV_IHM_CHARISMA'))
	{
		if(! defined('PV_COMPOSANT_IU_CHARISMA'))
		{
			include dirname(__FILE__)."/ComposantIU.class.php" ;
		}
		if(! defined('PV_SCRIPT_CHARISMA'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		if(! defined('PV_ZONE_CHARISMA'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
	}
	
?>