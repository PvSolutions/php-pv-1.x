<?php
	
	if(! defined('PV_IONIC'))
	{
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../Base.class.php" ;
		}
		if(! defined('PV_ZONE_BASE_IONIC'))
		{
			include dirname(__FILE__)."/Ionic/Zone.class.php" ;
		}
		define('PV_IONIC', 1) ;
	}
	
?>