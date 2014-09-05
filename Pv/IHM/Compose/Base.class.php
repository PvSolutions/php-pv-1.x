<?php
	
	if(! defined('PV_IHM_COMPOSE'))
	{
		if(! defined('PV_COMPOSANT_COMPOSE_IHM'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('PV_SCRIPT_COMPOSE_IHM'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		if(! defined('PV_ZONE_COMPOSE_IHM'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
	}
	
?>