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
				return 1 ;
			}
			protected function RespecteRegle(& $filtre)
			{
				return 1 ;
			}
		}
		
		class PvCritereNonVide extends PvCritereBase
		{
			public $FormatMessageErreur = 'Les champs ${ListeFiltres} ne doivent pas &ecirc;tre vides' ;
			protected function PrepareRenduFiltre(& $filtre)
			{
				$filtre->InsereSuffxErr("*") ;
			}
			protected function RespecteRegle(& $filtre)
			{
				return ($filtre->ValeurParametre !== "" && $filtre->ValeurParametre !== null) ? 1 : 0 ;
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