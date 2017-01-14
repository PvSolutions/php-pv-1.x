<?php
	
	if(! defined('APPLICATION_TEST_SBADM2'))
	{
		if(! defined('CONSTS_TEST_SBADM2'))
		{
			include dirname(__FILE__)."/Consts.php" ;
		}
		if(! defined('PV_BASE'))
		{
			include CHEMIN_PVIEW_TEST_SBADM2."/Pv/Base.class.php" ;
		}
		if(! defined('ZONE_PRINC_TEST_SBADM2'))
		{
			include dirname(__FILE__)."/ZonePrinc.class.php" ;
		}
		define('APPLICATION_TEST_SBADM2', 1) ;
		
		class ApplicationTestSbAdm2 extends PvApplication
		{
			public $ZonePrinc ;
			public $BDPrinc ;
			protected function ChargeBaseDonnees()
			{
				$this->BDPrinc = $this->InsereBaseBonnees("bdPrinc", new BDPrincTestSbAdm2()) ;
			}
			protected function ChargeIHMs()
			{
				$this->ZonePrinc = $this->InsereIHM("zonePrinc", new ZonePrincTestSbAdm2()) ;
				$this->ZonePrinc->CheminFichierRelatif = CHEM_REL_ZONE_PRINC_TEST_SBADM2 ;
			}
		}
		
		class BDPrincTestSbAdm2 extends MysqliDB
		{
			public function InitConnectionParams()
			{
				parent::InitConnectionParams() ;
				$this->ConnectionParams["server"] = "localhost" ;
				$this->ConnectionParams["user"] = "root" ;
				$this->ConnectionParams["password"] = "" ;
				$this->ConnectionParams["schema"] = "certif_cv" ;
			}
		}
	}
	
?>