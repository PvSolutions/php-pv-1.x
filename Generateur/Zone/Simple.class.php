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
			public function CreeFichiers()
			{
				$nomFic = $this->GenerateurParent->CheminFichierScript($this->NomAppel) ;
			}
		}
		
		class PvBDSimpleGenere extends PvPortionCodeGenere
		{
			public $NomBD = "" ;
			public $PortionsTable = array() ;
			public function ObtientBD()
			{
				return $this->GenerateurParent->ObtientBD($this->NomBD) ;
			}
			public function & CreePortionTable($nomTable)
			{
				$portion = new PvTableSimpleGenere() ;
				$portion->NomTable = $nomTable ;
				return $portion ;
			}
			public function InserePortionTable($nomTable)
			{
				$portion = $this->CreePortionTable($nomTable) ;
				$this->PortionsTable[] = & $portion ;
				$portion->AdopteGenerateur($this->NomElementGenerateur."_".count($this->Portions), $this) ;
			}
			public function CreeFichiers()
			{
				$bd = $this->ObtientBD() ;
				if(empty($this->NomBD) && $this->EstPasNul($bd))
				{
					die("La portion doit avoir un nom de base de donnees valide") ;
					return ;
				}
				$this->CreeFichierBD() ;
				$this->CreeFichiersTables() ;
			}
			protected function CreeFichierBD()
			{
				$cheminFichier = $this->CheminDossierBibliotheques(). DIRECTORY_SEPARATOR . $this->NomClasseBD.$this->SuffixeClasseAbstr.".class.php" ;
			}
			protected function CreeFichiersTables()
			{
				foreach($this->Portions as $nom => $portion)
				{
					$portion->Execute() ;
				}
			}
		}
		class PvTableSimpleGenere extends PvPortionCodeGenere
		{
			public $NomTable ;
			public $Cols = array() ;
			protected $Scripts = array() ;
		}
		class PvColTableSimpleGenere
		{
			public $Nom ;
			public $NomClasseCompEdit ;
			public $LibCompEdit ;
			public $LibColList ;
			public $StructNative ;
		}
		
		class PvGenerateurZoneSimple extends PvGenerateurBase
		{
			public $SuffixeClasseAbstr = "Abstrait" ;
			public $NomZone = "" ;
			public $NomClasseZone = "" ;
			public $NomDossierScripts = "Script" ;
			public $NomDossierActions = "Action" ;
			public $NomDossierTachesWeb = "TacheWeb" ;
			public $NomClasseZoneParent = "PvZoneWebSimple" ;
			public function CheminFichierScript($nomAppel)
			{
				$chemin = $this->CheminDossierScripts(). DIRECTORY_SEPARATOR . $this->EncodeNomAttribut($nomAppel).".class.php" ;
				return $chemin ;
			}
			public function CheminDossierTachesWeb()
			{
				return $this->CheminDossierBibliotheques(). DIRECTORY_SEPARATOR . $this->NomDossierTachesWeb ;
			}
			public function CheminDossierActions()
			{
				return $this->CheminDossierBibliotheques(). DIRECTORY_SEPARATOR . $this->NomDossierActions ;
			}
			public function CheminDossierScripts()
			{
				return $this->CheminDossierBibliotheques(). DIRECTORY_SEPARATOR . $this->NomDossierScripts ;
			}
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
				$contenu .= "\t\tclass ".$this->NomClasseZone.$this->SuffixeClasseAbstr." extends ".$this->NomClasseZoneParent .PHP_EOL ;
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
					$this->InscritPortion($portion) ;
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
				$this->CreeDossier($this->CheminDossierBibliotheques()) ;
				$this->CreeDossier($this->CheminDossierScripts()) ;
				$this->CreeDossier($this->CheminDossierActions()) ;
				$this->CreeDossier($this->CheminDossierTachesWeb()) ;
				$this->CreeFichiersZone() ;
				$this->CreeFichiersPortions() ;
			}
		}
		class PvGenerZoneSimple extends PvGenerateurZoneSimple
		{
		}
	}

?>