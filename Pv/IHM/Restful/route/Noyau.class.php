<?php
	
	if(! defined('PV_ROUTE_NOYAU_RESTFUL'))
	{
		if(! defined("PV_NOYAU_SIMPLE_IHM"))
		{
			include dirname(__FILE__)."/../../Simple/Noyau.class.php" ;
		}
		if(! defined("PV_FOURNISSEUR_DONNEES_SIMPLE"))
		{
			include dirname(__FILE__)."/../../Simple/FournisseurDonnees.class.php" ;
		}
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
		
		class PvRouteDonneesRestful extends PvRouteNoyauRestful
		{
			public $FournisseurDonnees ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->FournisseurDonnees = new PvFournisseurDonneesSql() ;
			}
			public function & CreeFiltreRef($nom, & $filtreRef)
			{
				$filtre = new PvFiltreRefRestful() ;
				$filtre->Source = & $filtreRef ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreFixeRestful() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteScript($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreCookie($nom)
			{
				$filtre = new PvFiltreCookieRestful() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreSession($nom)
			{
				$filtre = new PvFiltreSessionRestful() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreMembreConnecte($nom, $nomParamLie='')
			{
				$filtre = new PvFiltreMembreConnecteRestful() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->NomParametreLie = $nomParamLie ;
				return $filtre ;
			}
			public function & CreeFiltreHttpUpload($nom, $cheminDossierDest="")
			{
				$filtre = new PvFiltreHttpUploadRestful() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CheminDossier = $cheminDossierDest ;
				return $filtre ;
			}
			public function & CreeFiltreHttpGet($nom)
			{
				$filtre = new PvFiltreHttpGetRestful() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpPost($nom)
			{
				$filtre = new PvFiltreHttpPostRestful() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreHttpRequestRestful() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function CreeFltRef($nom, & $filtreRef)
			{
				return $this->CreeFiltreRef($nom, $filtreRef) ;
			}
			public function CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreRef($nom, $valeur) ;
			}
			public function CreeFltCookie($nom)
			{
				return $this->CreeFiltreCookie($nom) ;
			}
			public function CreeFltSession($nom)
			{
				return $this->CreeFiltreSession($nom) ;
			}
			public function CreeFltMembreConnecte($nom, $nomParamLie='')
			{
				return $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
			}
			public function CreeFltHttpUpload($nom, $cheminDossierDest="")
			{
				return $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
			}
			public function CreeFltHttpGet($nom)
			{
				return $this->CreeFiltreHttpGet($nom) ;
			}
			public function CreeFltHttpPost($nom)
			{
				return $this->CreeFiltreHttpPost($nom) ;
			}
			public function CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
		}
	
		class PvRouteFiltrableRestful extends PvRouteDonneesRestful
		{
			public $FiltresSelection = array() ;
			public function & InsereFltSelectRef($nom, & $filtreRef, $exprDonnees='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectCookie($nom, $exprDonnees='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectSession($nom, $exprDonnees='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectMembreConnecte($nom, $nomParamLie='', $exprDonnees='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpGet($nom, $exprDonnees='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpPost($nom, $exprDonnees='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
		}
	}
	
?>