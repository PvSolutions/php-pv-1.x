<?php
	
	if(! defined('PV_ROUTE_NOYAU_RESTFUL'))
	{
		define('PV_ROUTE_NOYAU_RESTFUL', 1) ;
		
		class PvRouteNoyauRestful extends PvObjet
		{
			public $MethodeHttp ;
			public $NomElementApi ;
			public $CheminRouteApi ;
			public $ApiParent ;
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $PrivilegesStricts = 0 ;
			public $ComposantRacine ;
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
			public function AdopteApi($nom, $cheminRoute, & $api)
			{
				$this->NomElementApi = $nom ;
				if($this->CheminRouteApi == '')
				{
					$this->CheminRouteApi = $nom ;
				}
				$this->CheminRouteApi = $cheminRoute ;
				$this->ApiParent = & $api ;
			}
			public function CreeComposantRacine()
			{
				return new PvComposantRacineRestful() ;
			}
			public function InsereComposant($nom, $composant)
			{
				return $this->ComposantRacine->InsereComposant($nom, $composant) ;
			}
			public function InscritComposant($nom, & $composant)
			{
				return $this->ComposantRacine->InscritComposant($nom, $composant) ;
			}
			public function SuccesReponse()
			{
				return $this->ApiParent->Reponse->EstSucces() ;
			}
			public function EchecReponse()
			{
				return $this->ApiParent->Reponse->EstEchec() ;
			}
			public function Execute()
			{
				$this->Requete = & $this->ApiParent->Requete ;
				$this->Reponse = & $this->ApiParent->Reponse ;
				$this->ContenuReponse = & $this->ApiParent->Reponse->Contenu ;
				$this->PrepareExecution() ;
				$this->ExecuteInstructions() ;
				$this->TermineExecution() ;
				if($this->SuccesReponse() && $this->ContenuReponse == '')
				{
					$this->ContenuReponse = $this->ComposantRacine->RenduDispositif() ;
				}
			}
			protected function PrepareExecution()
			{
			}
			protected function ExecuteInstructions()
			{
			}
			protected function TermineExecution()
			{
			}
		}
	}
	
?>