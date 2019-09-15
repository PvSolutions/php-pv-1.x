<?php
	
	if(! defined('PV_ROUTE_NOYAU_RESTFUL'))
	{
		define('PV_ROUTE_NOYAU_RESTFUL', 1) ;
		
		class PvRouteNoyauRestful extends PvObjet
		{
			protected $MethodeHttp ;
			protected $NomElementApi ;
			protected $ApiParent ;
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $PrivilegesStricts = 0 ;
			public function EstAppelee()
			{
				return 1 ;
			}
			public function PossedeMembreConnecte()
			{
				return $this->ApiParent->PossedeMembreConnecte() ;
			}
			public function PossedePrivilege($privilege)
			{
				return $this->ApiParent->PossedePrivilege($privilege) ;
			}
			public function PossedePrivileges($privileges)
			{
				return $this->ApiParent->PossedePrivileges($privileges) ;
			}
			public function IdMembreConnecte()
			{
				return $this->ApiParent->IdMembreConnecte() ;
			}
			public function LoginMembreConnecte()
			{
				return $this->ApiParent->LoginMembreConnecte() ;
			}
			public function EstAccessible()
			{
				return ($this->NecessiteMembreConnecte == 0 || count($this->Privileges) == 0 || $this->ApiParent->PossedePrivileges($this->Privileges, $this->PrivilegesStricts)) ;
			}
			public function AdopteApi($nom, & $api)
			{
				$this->NomElementApi = $nom ;
				$this->ApiParent = & $api ;
			}
			public function Execute()
			{
			}
		}
	}
	
?>