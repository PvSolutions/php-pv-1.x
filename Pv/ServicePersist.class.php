<?php
	
	if(! defined('PV_SERVICE_PERSISTANT'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_SERVICE_PERSISTANT', 1) ;
		
		class PvServeurSocketBase extends PvServicePersist
		{
			protected $FluxSupport = false ;
			protected function ExecuteSession()
			{
			}
			protected function OuvreFluxSupport()
			{
			}
			protected function RecoitDemandes()
			{
			}
			protected function FermeFluxSupport()
			{
			}
		}
	
	}
	
?>