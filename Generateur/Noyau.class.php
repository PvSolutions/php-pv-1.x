<?php
	
	if(! defined('PV_GENERATEUR_NOYAU'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/Noyau.class.php" ;
		}
		define('PV_GENERATEUR_NOYAU', 1) ;
		
		class PvGenerateurBase extends PvObjet
		{
			public $CheminDossierDestinationRelatif = "." ;
			public $CheminRelatifBibliotheques = "lib" ;
			protected $CheminDossierDestination = "." ;
			public $FichiersCrees = array() ;
			public $Active = 1 ;
			protected $ExecutionAnnulee = 0 ;
			public $NomBD = "" ;
			public $NomProjet = "" ;
			public $VersionProjet = "" ;
			public $BasesDonnees = array() ;
			public $Portions = array() ;
			public $FixerCheminDossierDestinationParEnv = 1 ;
			public function CheminDossierDestination()
			{
				if($this->FixerCheminDossierDestinationParEnv)
				{
					$this->CheminDossierDestination = dirname((php_sapi_name() != 'cli') ? $_SERVER["SCRIPT_FILENAME"] : $_SERVER["argv"][0]) ;
				}
				return realpath($this->CheminDossierDestination.DIRECTORY_SEPARATOR. $this->CheminDossierDestinationRelatif) ;
			}
			public function CheminDossierBibliotheques()
			{
				return $this->CheminDossierDestination(). DIRECTORY_SEPARATOR . $this->CheminRelatifBibliotheques ;
			}
			public function InscritPortion(& $portion)
			{
				$this->Portions[] = & $portion ;
			}
			public function InscritNouvPortion($portion)
			{
				$this->InscritPortion($portion) ;
			}
			public function & ObtientBD($nom)
			{
				$bd = null ;
				if(isset($this->BaseDonnees[$nom]))
				{
					$bd = & $this->BaseDonnees[$nom] ;
				}
				return $bd ;
			}
			public function DefinitInfosProjet($nom, $version)
			{
				$this->NomProjet = $nom ;
				$this->VersionProjet = $version ;
			}
			public function InscritBD($nom, & $bd)
			{
				$this->BasesDonnees[$nom] = & $bd ;
			}
			public function InscritNouvBD($nom, $bd)
			{
				$this->InscritBD($nom, $bd) ;
			}
			public function InscritBaseDonnees($nom, & $bd)
			{
				$this->InscritBD($nom, $bd) ;
			}
			public function InscritNouvBaseDonnees($nom, $bd)
			{
				$this->InscritBaseDonnees($nom, $bd) ;
			}
			public function SupprimeDossier($dir)
			{
				if (is_dir($dir))
				{
					$files = scandir($dir);
					foreach ($files as $file)
						if ($file != "." && $file != "..")
						$this->SupprimeDossier("$dir/$file");
					rmdir($dir);
				}
				else if (file_exists($dir)) unlink($dir) ;
			}
			public function CopieDossier($src, $dst)
			{
				if (file_exists($dst)) $this->SupprimeDossier($dst);
				if (is_dir($src))
				{
					$this->CreeDossier($dst);
					$files = scandir($src);
					foreach ($files as $file)
						if ($file != "." && $file != "..")
							$this->CopieDossier("$src/$file", "$dst/$file"); 
				}
				else if (file_exists($src)) copy($src, $dst);
			}
			public function CreeDossier($cheminDossier, $mode=0777)
			{
				if(is_dir($cheminDossier))
					return true ;
				return mkdir($cheminDossier, $mode, true) ;
			}
			public function SauveFichier($cheminFichier, $contenuFichier="")
			{
				if(is_array($contenuFichier))
				{
					$contenuFichier = join("\r\n", $contenuFichier) ;
				}
				// echo $cheminFichier ;
				$this->CreeDossier(dirname($cheminFichier)) ;
				$fr = @fopen($cheminFichier, "w") ;
				if($fr !== false)
				{
					fwrite($fr, $contenuFichier) ;
					fclose($fr) ;
				}
				$this->FichiersCrees[] = $cheminFichier ;
			}
			public function CopieDossierBibliotheques($cheminDossier)
			{
				return $this->CopieDossier(dirname(__FILE__)."/..", $cheminDossier) ;
			}
			public function EncodeExpressionVariable($valeur)
			{
				$resultat = $valeur ;
				$resultat = str_replace(array(' ', "\r", "\n", "\t"), '_', $resultat) ;
				$resultat = preg_replace_callback('/(_| |\-)([a-z0-9])/i', create_function('$matches', 'return strtoupper($matches[2]) ;'), $resultat) ;
				return $resultat ;
			}
			public function EncodeNomVariable($valeur)
			{
				$resultat = $valeur ;
				$resultat = lcfirst($this->EncodeExpressionVariable($resultat)) ;
				return $resultat ;
			}
			public function EncodeNomAttribut($valeur)
			{
				$resultat = $valeur ;
				$resultat = ucfirst($this->EncodeExpressionVariable($resultat)) ;
				return $resultat ;
			}
			public function EncodeNomPropriete($valeur)
			{
				return $this->EncodeNomAttribut($valeur) ;
			}
			public function EncodeNomMethode($valeur)
			{
				return $this->EncodeNomAttribut($valeur) ;
			}
			public function EncodeNomContante1($valeur)
			{
				$resultat = $valeur ;
				$resultat = str_replace(array(' ', "\r", "\n", "\t"), '_', $resultat) ;
				$resultat = preg_replace('/([A-Z])/', '_\\1', $resultat) ;
				$resultat = substr(strtoupper($resultat), 1) ;
				return $resultat ;
			}
			public function EncodeNomClasse($valeur)
			{
				return $this->EncodeNomAttribut($valeur) ;
			}
			protected function DetermineCheminDossierDestination()
			{
				$this->CheminDossierDestination = dirname(__FILE__).DIRECTORY_SEPARATOR ."..".DIRECTORY_SEPARATOR ."..".DIRECTORY_SEPARATOR .$this->CheminDossierDestinationRelatif ;
			}
			public function PrepareEnvironnement()
			{
				$this->DetermineCheminDossierDestination() ;
			}
			protected function CreeFichiers()
			{
			}
			public function Execute()
			{
				if($this->Active == 0)
				{
					return ;
				}
				$this->ExecutionAnnulee = 0 ;
				$this->PrepareEnvironnement() ;
				if($this->ExecutionAnnulee)
				{
					return ;
				}
				$this->CreeFichiers() ;
			}
			public function CreeFichiersPortionsSpec(& $portions)
			{
				$nomPortions = array_keys($portions) ;
				foreach($nomPortions as $i => $nomPortion)
				{
					$portion = & $portions[$nomPortion] ;
					$portion->CreeFichiers() ;
				}
			}
			protected function CreeFichiersPortions()
			{
				$this->CreeFichiersPortionsSpec($this->Portions) ;
			}
		}
		
		class PvPortionCodeGenere extends PvObjet
		{
			public $GenerateurParent ;
			public $NomElementGenerateur ;
			public function AdopteGenerateur($nom, & $generateur)
			{
				$this->NomElementGenerateur = $nom ;
				$this->GenerateurParent = & $generateur ;
			}
			public function CreeFichiers()
			{
			}
		}
		
		class PvGenerateurBD extends PvPortionCodeGenere
		{
			public $NomBD ;
			protected function ObtientBD()
			{
				$bd = $this->GenerateurParent->ObtientBD($this->NomBD) ;
				return $bd ;
			}

		}
	}
	
?>