<?php
	
	if(! defined('BASE_SWS'))
	{
		if(! defined('SYSTEME_SWS'))
		{
			include dirname(__FILE__)."/Systeme.class.php" ;
		}
		define('BASE_SWS', 1) ;
	}

?>