<?php
	
	if(! defined('ZONE_SMTADM'))
	{
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../Base.class.php" ;
		}
		define('ZONE_SMTADM', 1) ;
		
		class PvZoneBaseSmtAdm extends PvZoneWebSimple
		{
			public $InclureBootstrap = 1 ;
		}
		
		class PvScriptBaseSmtAdm extends PvScriptWebSimple
		{
		}
		
		class PvGrilleRenduSmtAdm extends PvComposantIUBase
		{
			public $TotalColonnes = 4 ;
			public $TotalLignes = 8 ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$fourn = & $this->FournisseurDonnees ;
				$fourn->
				return $ctn ;
			}
		}
	}
	
?>