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
			public function ApprouveAppel()
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
				$this->Reponse->ConfirmeSucces() ;
				$this->PrepareExecution() ;
				$this->ExecuteInstructions() ;
				$this->TermineExecution() ;
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
			public function ConfirmeData($data)
			{
				$this->ApiParent->Reponse->Contenu->data = $data ;
			}
			public function RenseigneErreur($message='')
			{
				return $this->ApiParent->Reponse->ConfirmeInvalide($message) ;
			}
			public function RenseigneException($message='')
			{
				return $this->ApiParent->Reponse->ConfirmeErreurInterne($message) ;
			}
			public function ConfirmeSucces($message='')
			{
				return $this->ApiParent->Reponse->ConfirmeSucces($message) ;
			}
			public function EstSucces()
			{
				return $this->ApiParent->Reponse->EstSucces() ;
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
				$filtre->AdopteRoute($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreFixeRestful() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteRoute($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreCookie($nom)
			{
				$filtre = new PvFiltreCookieRestful() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteRoute($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreSession($nom)
			{
				$filtre = new PvFiltreSessionRestful() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteRoute($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreMembreConnecte($nom, $nomParamLie='')
			{
				$filtre = new PvFiltreMembreConnecteRestful() ;
				$filtre->AdopteRoute($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->NomParametreLie = $nomParamLie ;
				return $filtre ;
			}
			public function & CreeFiltreHttpUpload($nom, $cheminDossierDest="")
			{
				$filtre = new PvFiltreHttpUploadRestful() ;
				$filtre->AdopteRoute($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CheminDossier = $cheminDossierDest ;
				return $filtre ;
			}
			public function & CreeFiltreHttpGet($nom)
			{
				$filtre = new PvFiltreHttpGetRestful() ;
				$filtre->AdopteRoute($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpPost($nom)
			{
				$filtre = new PvFiltreHttpPostRestful() ;
				$filtre->AdopteRoute($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpCorps($nom)
			{
				$filtre = new PvFiltreHttpCorpsRestful() ;
				$filtre->AdopteRoute($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreHttpRequestRestful() ;
				$filtre->AdopteRoute($nom, $this) ;
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
			public function AlerteExceptionFournisseur()
			{
				$this->RenseigneException($this->FournisseurDonnees->MessageException()) ;
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
			public function & InsereFltSelectHttpCorps($nom, $exprDonnees='')
			{
				$flt = $this->CreeFiltreHttpCorps($nom) ;
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
		
		class PvRouteEditableRestful extends PvRouteFiltrableRestful
		{
			public $FiltresEdition = array() ;
			public function & InsereFltEditRef($nom, & $filtreRef, $colLiee='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditFixe($nom, $valeur, $colLiee='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditCookie($nom, $colLiee='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditSession($nom, $colLiee='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditMembreConnecte($nom, $nomParamLie='', $colLiee='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpUpload($nom, $cheminDossierDest="", $colLiee='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpGet($nom, $colLiee='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpPost($nom, $colLiee='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpCorps($nom, $colLiee='')
			{
				$flt = $this->CreeFiltreHttpCorps($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpRequest($nom, $colLiee='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditRef($nom, & $filtreRef, $colLiee='', $nomComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditFixe($nom, $valeur, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditCookie($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditSession($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditMembreConnecte($nom, $nomParamLie='', $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditHttpUpload($nom, $cheminDossierDest="", $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditHttpGet($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditHttpPost($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFiltreEditHttpRequest($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
		}
	}
	
?>