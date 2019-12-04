<?php
	
	if(! defined('PV_COMPOSANT_BASE_RESTFUL'))
	{
		define('PV_COMPOSANT_BASE_RESTFUL', 1) ;
		
		class PvComposantBaseRestful extends PvObjet
		{
			public $NomElementComposant ;
			public $NomElementRoute ;
			public $RouteParent ;
			public $ApiParent ;
			public $ApplicationParent ;
			public function EstAccessible()
			{
				return 1 ;
			}
			public function AdopteComposant($nom, & $composant)
			{
				$this->NomElementComposant = $nom ;
				$this->ComposantParent = & $composant ;
				$this->AdopteRoute($composant->NomElementRoute."_".$nom, $composant->RouteParent) ;
			}
			public function AdopteRoute($nom, & $route)
			{
				$this->NomElementRoute = $nom ;
				$this->RouteParent = & $route ;
				$this->ApiParent = & $route->ApiParent ;
				$this->ApplicationParent = & $route->ApplicationParent ;
			}
			public function RenduDispositif()
			{
				return $this->RenduDispositifBrut() ;
			}
			protected function RenduDispositifBrut()
			{
				return "null" ;
			}
		}
		
		class PvComposantConteneurRestful extends PvComposantBaseRestful
		{
			public $Contenu ;
			public $Composants = array() ;
			public function & InscritComposant($nom, & $composant)
			{
				$this->Composants[$nom] = & $composant ;
				$composant->AdopteComposant($this->NomElementRoute.'_'.$nom, $this) ;
				return $composant ;
			}
			public function & InsereComposant($nom, $composant)
			{
				return $this->InscritComposant($nom, $composant) ;
			}
			public function RenduDispositifBrut()
			{
				if(count($this->Composants) > 0)
				{
					$result = array() ;
					foreach($this->Composants as $nom => $comp)
					{
						$result[$nom] = $comp->RenduDispositif() ;
					}
				}
				else
				{
					$result = $this->Contenu ;
				}
				return json_encode($result) ;
			}
		}
		
		class PvComposantRacineRestful extends PvComposantConteneurRestful
		{
			public function & InscritComposant($nom, & $composant)
			{
				$this->Composants[$nom] = & $composant ;
				$composant->AdopteComposant($nom, $this) ;
				return $composant ;
			}
		}
		
		class PvComposantDonneesRestful extends PvComposantBaseRestful
		{
			public $FournisseurDonnees ;
			public function & CreeFiltreRef($nom, & $filtreRef)
			{
				$filtre = new PvFiltreDonneesRef() ;
				$filtre->Source = & $filtreRef ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreDonneesFixe() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
				return $filtre ;
			}
			public function & CreeFiltreSession($nom)
			{
				$filtre = new PvFiltreDonneesSession() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
				return $filtre ;
			}
			public function & CreeFiltreMembreConnecte($nom, $nomParamLie='')
			{
				$filtre = new PvFiltreDonneesMembreConnecte() ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->NomParametreLie = $nomParamLie ;
				return $filtre ;
			}
			public function & CreeFiltreHttpUpload($nom, $cheminDossierDest="")
			{
				$filtre = new PvFiltreDonneesHttpUpload() ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CheminDossier = $cheminDossierDest ;
				return $filtre ;
			}
			public function & CreeFiltreHttpGet($nom)
			{
				$filtre = new PvFiltreDonneesHttpGet() ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpPost($nom)
			{
				$filtre = new PvFiltreDonneesHttpPost() ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreDonneesHttpRequest() ;
				$filtre->AdopteApi($nom, $this->ApiParent) ;
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
			public function ExtraitValeursParametre(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$valeurs = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtres[$nomFiltre] ;
					$filtre->Lie() ;
					$valeurs[$filtre->NomParametreDonnees] = $filtre->ValeurParametre ;
				}
				return $valeurs ;
			}
			public function ExtraitValeursParametreLie(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$valeurs = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtres[$nomFiltre] ;
					$valeurs[$filtre->NomParametreLie] = $filtre->Lie() ;
				}
				return $valeurs ;
			}
			public function ExtraitValeursColonneLiee(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$valeurs = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtres[$nomFiltre] ;
					$filtre->Lie() ;
					$valeurs[$filtre->NomColonneLiee] = $filtre->ValeurParametre ;
				}
				return $valeurs ;
			}
			public function ExtraitObjetParametre(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$obj = new StdClass() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtres[$nomFiltre] ;
					$nomProp = $filtre->NomParametreDonnees ;
					if($nomProp == '')
					{
						continue ;
					}
					$filtre->Lie() ;
					$obj->$nomProp = $filtre->ValeurParametre ;
				}
				return $obj ;
			}
			public function ExtraitObjetColonneLiee(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$obj = new StdClass() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtres[$nomFiltre] ;
					$nomProp = $filtre->NomColonneLiee ;
					if($nomProp == '')
					{
						continue ;
					}
					$filtre->Lie() ;
					$obj->$nomProp = $filtre->ValeurParametre ;
				}
				return $obj ;
			}
			public function ObtientFiltre(& $filtres, $nomParamLie)
			{
			}
			public function CreeCmdRedirectUrl()
			{
				return new PvCommandeRedirectionHttp() ;
			}
			public function CreeCmdRedirectApi()
			{
				return new PvCommandeRedirectionHttp() ;
			}
			protected function AppliqueHabillage()
			{
				if($this->ZoneParent->EstNul($this->ZoneParent->Habillage))
				{
					return ;
				}
				$this->ZoneParent->Habillage->AppliqueSur($this) ;
				return $this->ZoneParent->Habillage->Rendu ;
			}
			public function ExtraitFiltresDeRendu(& $filtres, $filtresCaches=array())
			{
				$resultats = array() ;
				foreach($filtres as $i => $filtre)
				{
					if($filtre->RenduPossible() && ! in_array($filtre->NomParametreLie, $filtresCaches))
					{
						$resultats[$i] = & $filtres[$i] ;
					}
				}
				return $resultats ;
			}
			public function ExtraitFiltresAffichables(& $filtres)
			{
				$resultats = array() ;
				foreach($filtres as $i => $filtre)
				{
					if($filtre->RenduPossible() && ! $filtre->LectureSeule)
					{
						$resultats[$i] = & $filtres[$i] ;
					}
				}
				return $resultats ;
			}
		}
		
		class PvComposantFiltrableRestful extends PvComposantDonneesRestful
		{
			public $FournisseurDonnees ;
			public $FiltresSelection = array() ;
		}
		
		class PvComposantEditableRestful extends PvComposantFiltrableRestful
		{
			public $FiltresEdition = array() ;
		}
		
	}
	
?>