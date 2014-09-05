<?php
	
	if(! defined('PV_GENER_ZONE_INCLUS'))
	{
		if(! defined('PV_GENERATEUR_NOYAU'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		define('PV_GENER_ZONE_INCLUS', 1) ;	
		
		class PvScriptDInclusionGenere extends PvPortionCodeGenere
		{
			public $NomAppel = "" ;
			public function CreeFichiers()
			{
				$this->CreeFichierVariables() ;
				$this->CreeFichierRendus() ;
			}
			protected function CreeFichierVariables()
			{
				$cheminFichier = $this->CheminFichierVariables() ;
				if(file_exists($cheminFichier) && $this->GenerateurParent->EcraserSiExistant == 0)
					return 1 ;
				$contenu = '' ;
				$contenu .= '<?php'.PHP_EOL ;
				$contenu .= '?>' ;
				$this->GenerateurParent->SauveFichier(
					$cheminFichier,
					$contenu
				) ;
			}
			protected function CreeFichierRendus()
			{
				$cheminFichier = $this->CheminFichierRendus() ;
				if(file_exists($cheminFichier) && $this->GenerateurParent->EcraserSiExistant == 0)
					return 1 ;
				$contenu = '' ;
				$contenu .= '<?php'.PHP_EOL ;
				$contenu .= '?>' ;
				$this->GenerateurParent->SauveFichier(
					$cheminFichier,
					$contenu
				) ;
			}
			public function CheminFichierVariables()
			{
				return $this->GenerateurParent->CheminDossierDestination(). DIRECTORY_SEPARATOR . $this->GenerateurParent->CheminRelatifVariables. $this->GenerateurParent->DossierZone(). DIRECTORY_SEPARATOR . $this->GenerateurParent->EncodeNomVariable($this->NomAppel).".".$this->GenerateurParent->ExtensionFichierVariables ;
			}
			public function CheminFichierRendus()
			{
				return $this->GenerateurParent->CheminDossierDestination(). DIRECTORY_SEPARATOR . $this->GenerateurParent->CheminRelatifRendus. $this->GenerateurParent->DossierZone(). DIRECTORY_SEPARATOR . $this->GenerateurParent->EncodeNomVariable($this->NomAppel).".".$this->GenerateurParent->ExtensionFichierRendus ;
			}
		}
		
		class PvGenerateurDeScriptsDInclusion extends PvGenerateurBase
		{
			public $Scripts = array() ;
			public $NomZone = "" ;
			public $NomClasseZone = "MaZone" ;
			public $NomClasseZoneParent = "PvZoneDInclusions" ;
			public $CheminRelatifVariables = "variables" ;
			public $CheminRelatifRendus = "rendu" ;
			public $ExtensionFichierVariables = "php" ;
			public $ExtensionFichierRendus = "php" ;
			public $EcraserSiExistant = 0 ;
			public function InscritScripts()
			{
				$nomScripts = func_get_args() ;
				foreach($nomScripts as $i => $nomScript)
				{
					$script = new PvScriptDInclusionGenere() ;
					$script->NomAppel = $nomScript ;
					$script->AdopteGenerateur($nomScript, $this) ;
					$this->Scripts[$nomScript] = $script ;
				}
			}
			public function PrepareEnvironnement()
			{
				parent::PrepareEnvironnement() ;
				$this->CreeDossier($this->CheminDossierDestination(). DIRECTORY_SEPARATOR . $this->CheminRelatifRendus. $this->DossierZone()) ;
				$this->CreeDossier($this->CheminDossierDestination(). DIRECTORY_SEPARATOR . $this->CheminRelatifVariables. $this->DossierZone()) ;
			}
			public function CreeFichiers()
			{
				if(empty($this->NomZone))
					die("Le nom de la zone ne doit pas etre vide lors de la generation !!!") ;
				$this->CopieDossierBibliotheques($this->CheminDossierBibliotheques()) ;
				$this->CreeFichierZone() ;
				$nomScripts = array_keys($this->Scripts) ;
				foreach($nomScripts as $i => $nomScript)
				{
					$script = & $this->Scripts[$nomScript] ;
					$script->CreeFichiers() ;
				}
			}
			public function CreeFichierZone()
			{
				$constanteZone = $this->EncodeNomContante1($this->NomClasseZone) ;
				$contenu = '' ;
				$contenu .= '<?php'.PHP_EOL ;
				$contenu .= PHP_EOL ;
				$contenu .= "\tif(! defined('".$constanteZone."'))".PHP_EOL ;
				$contenu .= "\t{".PHP_EOL ;
				$contenu .= "\t\tif(! defined('PV_BASE'))".PHP_EOL ;
				$contenu .= "\t\t{".PHP_EOL ;
				$contenu .= "\t\t\tinclude dirname(__FILE__).'/Pv/Base.php' ;".PHP_EOL ;
				$contenu .= "\t\t}".PHP_EOL ;
				$contenu .= "\t\tdefine('".$constanteZone."', 1) ;".PHP_EOL ;
				$contenu .= PHP_EOL ;
				$contenu .= "\t\tclass ".$this->NomClasseZone." extends ".$this->NomClasseZoneParent.PHP_EOL ;
				$contenu .= "\t\t{".PHP_EOL ;
				$contenu .= "\t\t\tprotected function ChargeScripts()".PHP_EOL ;
				$contenu .= "\t\t\t{".PHP_EOL ;
				$nomScripts = array_keys($this->Scripts) ;
				foreach($nomScripts as $i => $nomScript)
				{
					$contenu .= "\t\t\t\t\$this->DeclareInclusionScript('".$this->EncodeNomVariable($nomScript)."') ;".PHP_EOL ;
				}
				$contenu .= "\t\t\t}".PHP_EOL ;
				$contenu .= "\t\t}".PHP_EOL ;
				$contenu .= "\t}".PHP_EOL ;
				$contenu .= PHP_EOL ;
				$contenu .= "?>" ;
				$cheminFichierZone = $this->CheminDossierBibliotheques(). DIRECTORY_SEPARATOR . $this->NomClasseZone.".class.php" ;
				$this->SauveFichier($cheminFichierZone, $contenu) ;
			}
			public function DossierZone()
			{
				return ($this->NomZone != '') ? DIRECTORY_SEPARATOR . $this->NomZone : '' ;
			}
		}
	}
	
?>