<?php

	if(! defined('PV_GENER_ZONE_SIMPLE'))
	{
		if(! defined('PV_GENERATEUR_NOYAU'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		define('PV_GENER_ZONE_SIMPLE', 1) ;
		
		class PvScriptSimpleGenere extends PvPortionCodeGenere
		{
			public $NomAppel = "" ;
		}
		class PvScriptsBDSimpleGenere extends PvPortionCodeGenere
		{
			public $NomBD = "" ;
		}
		
		class PvGenerateurZoneSimple extends PvGenerateurBase
		{
			public $SuffixeClasseAbstr = "Abstrait" ;
			public $NomZone = "" ;
			public $NomClasseZone = "" ;
			public $NomClasseZoneParent = "PvZoneWebSimple" ;
			public function DossierZone()
			{
				return ($this->NomZone != '') ? DIRECTORY_SEPARATOR . $this->NomZone : '' ;
			}
			public function CreeFichierZoneAbstrait()
			{
				$constanteZone = $this->EncodeNomContante1($this->NomClasseZone.$this->SuffixeClasseAbstr) ;
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
				$contenu .= "\t\tclass ".$this->NomClasseZone.$this->SuffixeClasseAbstr." extends ".$this->NomClasseZoneParent.PHP_EOL ;
				$contenu .= "\t\t{".PHP_EOL ;
				$contenu .= "\t\t\tprotected function ChargeScripts()".PHP_EOL ;
				$contenu .= "\t\t\t{".PHP_EOL ;
				$contenu .= "\t\t\t\tparent::ChargeScripts() ;".PHP_EOL ;
				$contenu .= "\t\t\t}".PHP_EOL ;
				$contenu .= "\t\t}".PHP_EOL ;
				$contenu .= "\t}".PHP_EOL ;
				$contenu .= PHP_EOL ;
				$contenu .= "?>" ;
				$cheminFichierZone = $this->CheminDossierBibliotheques(). DIRECTORY_SEPARATOR . $this->NomClasseZone.$this->SuffixeClasseAbstr.".class.php" ;
				$this->SauveFichier($cheminFichierZone, $contenu) ;
			}
			public function CreeFichierZone()
			{
				$constanteZone = $this->EncodeNomContante1($this->NomClasseZone) ;
				$constanteZoneAbstr = $this->EncodeNomContante1($this->NomClasseZone.$this->SuffixeClasseAbstr) ;
				$contenu = '' ;
				$contenu .= '<?php'.PHP_EOL ;
				$contenu .= PHP_EOL ;
				$contenu .= "\tif(! defined('".$constanteZone."'))".PHP_EOL ;
				$contenu .= "\t{".PHP_EOL ;
				$contenu .= "\t\tif(! defined('".$constanteZoneAbstr."'))".PHP_EOL ;
				$contenu .= "\t\t{".PHP_EOL ;
				$contenu .= "\t\t\tinclude dirname(__FILE__).'/".$this->NomClasseZone.$this->SuffixeClasseAbstr.".class.php' ;".PHP_EOL ;
				$contenu .= "\t\t}".PHP_EOL ;
				$contenu .= "\t\tdefine('".$constanteZone."', 1) ;".PHP_EOL ;
				$contenu .= PHP_EOL ;
				$contenu .= "\t\tclass ".$this->NomClasseZone." extends ".$this->NomClasseZone.$this->SuffixeClasseAbstr. PHP_EOL ;
				$contenu .= "\t\t{".PHP_EOL ;
				$contenu .= "\t\t\tprotected function ChargeScripts()".PHP_EOL ;
				$contenu .= "\t\t\t{".PHP_EOL ;
				$contenu .= "\t\t\t\tparent::ChargeScripts() ;".PHP_EOL ;
				$contenu .= "\t\t\t}".PHP_EOL ;
				$contenu .= "\t\t}".PHP_EOL ;
				$contenu .= "\t}".PHP_EOL ;
				$contenu .= PHP_EOL ;
				$contenu .= "?>" ;
				$cheminFichierZone = $this->CheminDossierBibliotheques(). DIRECTORY_SEPARATOR . $this->NomClasseZone.".class.php" ;
				$this->SauveFichier($cheminFichierZone, $contenu) ;
			}
			public function CreePortionScript()
			{
				return new PvScriptSimpleGenere() ;
			}
			public function InscritScriptsParNom()
			{
				$nomScripts = func_get_args() ;
				foreach($nomScripts as $i => $nomScript)
				{
					$portion = $this->CreePortionScript() ;
					$portion->NomAppel = $nomScript ;
					$this->InscritNouvPortion($portion) ;
				}
			}
			protected function CreeFichiersZone()
			{
				$this->CreeFichierZoneAbstrait() ;
				$this->CreeFichierZone() ;
			}
			public function CreeFichiers()
			{
				if(empty($this->NomClasseZone))
					die("Le nom de classe de la zone ne doit pas etre vide lors de la generation !!!") ;
				// $this->CopieDossierBibliotheques($this->CheminDossierBibliotheques()) ;
				$this->CreeFichiersZone() ;
				$this->CreeFichiersPortions() ;
			}
		}
		
	}

?>