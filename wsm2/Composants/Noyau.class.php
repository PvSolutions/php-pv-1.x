<?php
	
	if(! defined('NOYAU_COMPOSANT_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../../Pv/IHM/Compose.class.php" ;
		}
		define('NOYAU_COMPOSANT_WSM', 1) ;
		
		class ComposantIUWsmBase extends PvComposantIUBase
		{
			public $RefererBD = 1 ;
			public $BD = null ;
			public function RenduDispositif()
			{
				if($this->RefererBD)
				{
					$this->BD = & $this->ApplicationParent->BDWsm ;
				}
				return parent::RenduDispositif() ;
			}
		}
	}
	
?>