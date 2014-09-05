<?php
	
	if(! defined('APPLICATION_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/IHM/Compose.class.php" ;
		}
		if(! defined('NOYAU_BD_WSM'))
		{
			include dirname(__FILE__)."/BaseDonnees/Base.class.php" ;
		}
		if(! defined('ZONE_BASE_WSM'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		if(! defined('SCRIPT_BASE_WSM'))
		{
			include dirname(__FILE__)."/Scripts/Base.class.php" ;
		}
		if(! defined('COMP_PAGE_WEB_WSM'))
		{
			include dirname(__FILE__)."/Composants/Base.class.php" ;
		}
		define('APPLICATION_WSM', 1) ;
		
		class ApplicationWsm extends PvApplication
		{
			public $TexteLogo = '' ;
			public $CheminLogo = '' ;
			public $TexteSlogan = '' ;
			public $EncadrerDocParDefaut = 1 ;
			public $NomZoneAdministration = "zoneAdministrationWSM" ;
			public $NomZonePublique = "zonePubliqueWSM" ;
			public $ZoneAdministration = null ;
			public $ZonePublique = null ;
			public $CheminFicRelZonePubl = "wsm2/index.php" ;
			public $CheminFicRelZoneAdmin = "wsm2/__admin/index.php" ;
			public $Systeme = null ;
			public $CompatibleV1 = 0 ;
			public $BDWsm = null ;
			public $BDSystemeWsm = null ;
			public $CompsAvantCorpsDoc = array() ;
			public $CompsApresCorpsDoc = array() ;
			public function InscritCompAvantCorpsDoc($nomComp, & $comp)
			{
				$this->CompsAvantCorpsDoc[$nomComp] = & $comp ;
			}
			public function InscritCompApresCorpsDoc($nomComp, & $comp)
			{
				$this->CompsApresCorpsDoc[$nomComp] = & $comp ;
			}
			public function InscritNouvCompAvantCorpsDoc($nomComp, $comp)
			{
				$this->InscritCompAvantCorpsDoc($nomComp, $comp) ;
			}
			public function InscritNouvCompApresCorpsDoc($nomComp, $comp)
			{
				$this->InscritCompApresCorpsDoc($nomComp, $comp) ;
			}
			public function CreeSystemeWsm()
			{
				return new SystemeWsm() ;
			}
			protected function CreeZonePublique()
			{
				return new ZoneWebPubliqueWsm() ;
			}
			protected function CreeZoneAdmin()
			{
				return new ZoneWebAdminWsm() ;
			}
			protected function ChargeSysteme()
			{
				$this->Systeme = $this->CreeSystemeWsm() ;
				$this->Systeme->AdopteApplication($this) ;
				if($this->BDSystemeWsm != null)
				{
					$this->Systeme->DeclareBaseDonnees($this->BDSystemeWsm) ;
				}
				$this->Systeme->ChargeConfig() ;
			}
			protected function ChargeBDSysteme()
			{
			}
			protected function ChargeBasesDonnees()
			{
				$this->ChargeBDSysteme() ;
				$this->ChargeSysteme() ;
				$this->BDWsm = & $this->Systeme->BaseDonnees ;
				$this->BaseDonnees["WSM"] = & $this->Systeme->BaseDonnees ;
				parent::ChargeBasesDonnees() ;
			}
			protected function ChargeComposants()
			{
				$this->ChargeCompsAvantCorpsDoc() ;
				$this->ChargeCompsApresCorpsDoc() ;
			}
			protected function ChargeCompsAvantCorpsDoc()
			{
			}
			protected function ChargeCompsApresCorpsDoc()
			{
			}
			public function ChargeIHMs()
			{
				parent::ChargeIHMs() ;
				
				$this->ChargeComposants() ;
				
				$this->ZonePublique = $this->CreeZonePublique() ;
				$this->ZonePublique->EncadrerDocument = $this->EncadrerDocParDefaut ;
				$this->ZonePublique->CheminFichierRelatif = $this->CheminFicRelZonePubl ;
				$this->InscritIHM($this->NomZonePublique, $this->ZonePublique) ;
				
				$this->ZoneAdministration = $this->CreeZoneAdmin() ;
				$this->ZoneAdministration->EncadrerDocument = $this->EncadrerDocParDefaut ;
				$this->ZoneAdministration->CheminFichierRelatif = $this->CheminFicRelZoneAdmin ;
				$this->InscritIHM($this->NomZoneAdministration, $this->ZoneAdministration) ;
			}
		}
	}
	
?>