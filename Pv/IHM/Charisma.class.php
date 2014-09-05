<?php
	
	if(! defined('PV_IHM_CHARISMA'))
	{
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../Base.class.php" ;
		}
		if(! defined('PV_IHM_CHARISMA'))
		{
			include dirname(__FILE__)."/Charisma/Base.class.php" ;
		}
	}
	
?>