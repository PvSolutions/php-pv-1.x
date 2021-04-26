<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_CRITERE'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_IU_BASE'))
		{
			include dirname(__FILE__)."/Base.class.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_CRITERE', 1) ;
		
		class PvCritereBase extends PvElementCommandeBase
		{
			public $TypeElementCommande = "critere" ;
			public $MessageErreur = "" ;
			public $FormatMessageErreur = 'Les champs ${ListeFiltres} ne n\'ont pas le bon format' ;
			public function PrepareRendu(& $form)
			{
				$nomFiltres = array_keys($this->FiltresCibles) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FiltresCibles[$nomFiltre] ;
					$this->PrepareRenduFiltre($filtre) ;
				}
			}
			protected function PrepareRenduFiltre(& $filtre)
			{
			}
			public function EstRespecte()
			{
				if(count($this->FiltresCibles) == 0)
				{
					return 1 ;
				}
				$this->MessageErreur = "" ;
				$nomFiltres = array_keys($this->FiltresCibles) ;
				$filtreErreurs = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FiltresCibles[$nomFiltre] ;
					$filtre->Lie() ;
					$ok = $this->RespecteRegle($filtre) ;
					if(! $ok)
					{
						$filtreErreurs[] = $filtre->ObtientLibelle() ;
					}
				}
				if(count($filtreErreurs) > 0)
				{
					$this->MessageErreur = _parse_pattern(
						$this->FormatMessageErreur,
						array(
							"ListeFiltres" => join(", ", $filtreErreurs)
						)
					) ;
					return 0 ;
				}
				return ($this->MessageErreur == '') ? 1 : 0 ;
			}
			public function RenseigneErreur($format, $params=array())
			{
				$this->MessageErreur = _parse_pattern(
					$format,
					$params
				) ;
				return 0 ;
			}
			protected function RespecteRegle(& $filtre)
			{
				return 1 ;
			}
		}
		
		class PvCritereNonVide extends PvCritereBase
		{
			public $FormatMessageErreur = 'Les champs ${ListeFiltres} ne doivent pas &ecirc;tre vides' ;
			public $FormatMessageErreurUn = 'Le champ ${ListeFiltres} ne doit pas &ecirc;tre vide' ;
			protected function PrepareRenduFiltre(& $filtre)
			{
				$filtre->InsereSuffxErr("*") ;
			}
			public function EstRespecte()
			{
				if(count($this->FiltresCibles) == 0)
				{
					return 1 ;
				}
				$this->MessageErreur = "" ;
				$nomFiltres = array_keys($this->FiltresCibles) ;
				$filtreErreurs = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FiltresCibles[$nomFiltre] ;
					$filtre->Lie() ;
					$ok = $this->RespecteRegle($filtre) ;
					if(! $ok)
					{
						$filtreErreurs[] = $filtre->ObtientLibelle() ;
					}
				}
				if(count($filtreErreurs) > 0)
				{
					$this->MessageErreur = _parse_pattern(
						(count($filtreErreurs) == 1) ? $this->FormatMessageErreurUn : $this->FormatMessageErreur,
						array(
							"ListeFiltres" => join(", ", $filtreErreurs)
						)
					) ;
					return 0 ;
				}
				return ($this->MessageErreur == '') ? 1 : 0 ;
			}
			protected function RespecteRegle(& $filtre)
			{
				$valeur = trim($filtre->ValeurParametre) ;
				return ($valeur !== "" && $valeur !== null) ? 1 : 0 ;
			}
		}
		class PvCritereFormatLogin extends PvCritereBase
		{
			public $FormatMessageErreur = 'Les champs ${ListeFiltres} doivent avoir un pseudo valide' ;
			protected function RespecteRegle(& $filtre)
			{
				return validate_name_user_format($filtre->ValeurParametre) ;
			}
		}
		class PvCritereFormatMotPasse extends PvCritereBase
		{
			public $FormatMessageErreur = 'Les champs ${ListeFiltres} doivent avoir un mot de passe valide' ;
			protected function RespecteRegle(& $filtre)
			{
				return validate_password_format($filtre->ValeurParametre) ;
			}
		}
		class PvCritereFormatEmail extends PvCritereBase
		{
			public $FormatMessageErreur = 'Les champs ${ListeFiltres} doivent avoir un email valide' ;
			protected function RespecteRegle(& $filtre)
			{
				return validate_email_format($filtre->ValeurParametre) ;
			}
		}
		class PvCritereFormatUrl extends PvCritereBase
		{
			public $FormatMessageErreur = 'Les champs ${ListeFiltres} doivent avoir une URL valide' ;
			protected function RespecteRegle(& $filtre)
			{
				return validate_url_format($filtre->ValeurParametre) ;
			}
		}
		
		class PvCritereValideRegexpForm extends PvCritereBase
		{
			public $FormatMessageErreur = 'Le champ ${libelleFiltre} a un format invalide' ;
			public $TypesFormatRegexp = array(
				'titre_court' => '.{0,75}',
				'titre' => '.{0,150}',
				'titre_alpha' => '[^0-9]{0,150}',
				'adresse' => '.{0,65}',
				'ville' => '[^0-9]{0,75}',
				'pays' => '[^0-9]{0,75}',
				'paragraphe' => '.*',
				'paragraphe_alpha' => '[^0-9]*',
				'telephone' => '(\+?[0-9 \-]{0,16}[ ,;]{0,1})*',
				'nom_personne' => '[a-zA-Z]{0,20}([^a-zA-Z0-9]{1,2}[a-zA-Z]{1,20}){0,5}',
				'prenom_personne' => '[a-zA-Z]{0,20}([^a-zA-Z0-9]{1,2}[a-zA-Z]{1,20}){0,9}',
				'code_postal' => '[a-zA-Z0-9]{0,20}([^a-zA-Z0-9]{1,2}[a-zA-Z0-9]{1,20}){0,9}',
			) ;
			protected function & ObtientFiltresCibles()
			{
				return $this->FormulaireDonneesParent->FiltresEdition ;
			}
			public function EstRespecte()
			{
				$this->MessageErreur = "" ;
				$filtresCibles = $this->ObtientFiltresCibles() ;
				if(! is_array($filtresCibles) || count($filtresCibles) == 0)
				{
					return 1 ;
				}
				$nomsFiltres = array_keys($filtresCibles) ;
				foreach($nomsFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtresCibles[$nomFiltre] ;
					if($filtre->Role != "get" && $filtre->Role != "post")
					{
						continue ;
					}
					$formatRegexp = '' ;
					if($filtre->TypeFormatRegexp != '')
					{
						if(isset($this->TypesFormatRegexp[$filtre->TypeFormatRegexp]))
						{
							$formatRegexp = $this->TypesFormatRegexp[$filtre->TypeFormatRegexp] ;
						}
						else
						{
							die("Type de format inconnu pour ".$filtre->NomParametreDonnees." : ".$filtre->TypeFormatRegexp) ;
						}
					}
					else
					{
						$formatRegexp = $filtre->FormatRegexp ;
					}
					if($formatRegexp == "")
					{
						continue ;
					}
					// print $filtre->NomParametreDonnees." : ".$formatRegexp."<br>" ;
					$valeur = $filtre->Lie() ;
					if(! preg_match('/^'.$formatRegexp.'$/', $valeur))
					{
						$this->MessageErreur = ($filtre->MessageErreurRegexp == "") ? _parse_pattern($this->FormatMessageErreur, array("libelleFiltre" => $filtre->ObtientLibelle())) : $filtre->MessageErreurRegexp ;
						break ;
					}
				}
				// exit ;
				return ($this->MessageErreur == '') ? 1 : 0 ;
			}

		}
		class PvCritereValideRegexpTabl extends PvCritereValideRegexpForm
		{
			protected function & ObtientFiltresCibles()
			{
				return $this->TableauDonneesParent->FiltresSelection ;
			}
		}
		
		class PvCritereValideCaptcha extends PvCritereBase
		{
			public $FltCaptchaParent ;
			public $MessageErreur = "Le code de s&eacute;curit&eacute; saisi est incorrect" ;
			public function EstRespecte()
			{
				$ok  = $this->FltCaptchaParent->Composant->VerifieValeurSoumise($this->FltCaptchaParent->Lie()) ;
				return $ok ;
			}
		}
		class PvCritereValideScript extends PvCritereBase
		{
			public function EstRespecte()
			{
				$ok = $this->ScriptParent->ValideCritere($this) ;
				return $ok ;
			}
		}
		class PvCritereValideZone extends PvCritereBase
		{
			public function EstRespecte()
			{
				$ok = $this->ZoneParent->ValideCritere($this, $this->ScriptParent) ;
				return $ok ;
			}
		}
		
		/*
		class PvCritereElementUnique extends PvCritereBase
		{
			public $FormatMessageErreur = 'Il existe d&eacute;j&agrave; des &eacute;l&eacute;ments identiques dans la base de donn&eacute;es' ;
			public function EstRespecte()
			{
				if(count($this->FiltresCibles) == 0)
				{
					return 1 ;
				}
				$this->MessageErreur = "" ;
				$nomFiltres = array_keys($this->FiltresCibles) ;
				if($this->FormulaireDonneesParent->EstNul($this->FormulaireDonneesParent->FournisseurDonnees))
					return 1 ;
				$ok = 0 ;
				$filtres = array() ;
				$this->FormulaireDonneesParent->LieFiltres($this->FiltresCibles) ;
				foreach($this->FiltresCibles as $i => $filtreCible)
				{
					$filtre = new PvFiltreDonneesFixe() ;
					$filtre->NomParametreDonnees = $filtreCible->NomParametreDonnees ;
					$filtre->ValeurParDefaut = $filtreCible->ValeurParametre ;
					$filtre->ExpressionDonnees = $filtreCible->NomColonneLiee.' = <SELF>' ;
					$filtres[] = $filtre ;
				}
				$lignes = $this->FormulaireDonneesParent->FournisseurDonnees->SelectElements(array(), $filtres) ;
				$this->MessageErreur = _parse_pattern(
					$this->FormatMessageErreur,
					array(
						"ListeFiltres" => join(", ", $nomFiltres)
					)
				) ;
				return 0 ;
			}
		}
		*/
	}
	
?>