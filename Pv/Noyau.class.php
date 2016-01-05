<?php
	
	/*
	 *
	 Librairies PV = PERSONAL VIEW
	 * */
	if(! defined('PV_NOYAU'))
	{
		if(! defined('UTILS_INCLUDED'))
		{
			include dirname(__FILE__)."/../misc/utils.php" ;
		}
		if(! defined('PERS_ZIP_SLICE'))
		{
			include dirname(__FILE__)."/../misc/PersZip.class.php" ;
		}
		if(! defined('SERVICES_JSON_SLICE'))
		{
			include dirname(__FILE__)."/../misc/Services_JSON.class.php" ;
		}
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		if(! defined('PROCESS_MANAGER_INCLUDED'))
		{
			include dirname(__FILE__)."/../Common/ProcessManager.class.php" ;
		}
		if(! defined('PV_JOURNAL_TRACES'))
		{
			include dirname(__FILE__)."/JournalTraces.class.php" ;
		}
		define('PV_NOYAU', 1) ;
		
		class PvObjet
		{
			public $ID = "" ;
			public $IDInstance = "" ;
			public $IDInstanceCalc = "" ;
			public $NomClasseInstance = "" ;
			public $IndiceInstance = 0 ;
			static $TotalInstances = 0 ;
			public function ObtientValStatique($nomPropriete, $valParDefaut=false)
			{
				return $this->ObtientValeurStatique($nomPropriete, $valParDefaut) ;
			}
			public function AffecteValStatique($nomPropriete, $valParDefaut=false)
			{
				return $this->AffecteValeurStatique($nomPropriete, $valParDefaut) ;
			}
			public function ObtientValeurStatique($nomPropriete, $valeurDefaut=false)
			{
				$valeur = $valeurDefaut ;
				try { eval('$valeur = '.get_class($this).'::$'.$nomPropriete.' ;') ; } catch(Exception $ex) {} ;
				return $valeur ;
			}
			public function AffecteValeurStatique($nomPropriete, $valeur)
			{
				try { eval(get_class($this).'::$'.$nomPropriete.' = $valeur ;') ; } catch(Exception $ex) {}
				return $valeur ;
			}
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
				$this->InitConfigInstance() ;
			}
			protected function InitConfigInstance()
			{
				$totalInstances = $this->ObtientValeurStatique("TotalInstances") ;
				$totalInstances++ ;
				$this->NomClasseInstance = get_class($this) ;
				$this->IndiceInstance = $totalInstances ;
				$this->AffecteValeurStatique("TotalInstances", $totalInstances) ;
				$this->IDInstance = $this->NomClasseInstance."_".$this->IndiceInstance ;
				$this->IDInstanceCalc = $this->IDInstance ;
			}
			public function ChargeConfig()
			{
			}
			public function EstNul($objet)
			{
				if($objet == null)
					return 1 ;
				$nomClasse = get_class($objet) ;
				$nomClasseObj = get_class($this) ;
				return (in_array($nomClasse, array("PvNul", "stdClass"))) ? 1 : 0 ;
				// return (get_class($objet) == "PvNul" or get_class($objet) == get_class($this)) ? 1 : 0 ;
			}
			public function EstNonNul($objet)
			{
				return ($this->EstNul($objet)) ? 0 : 1 ;
			}
			public function EstPasNul($objet)
			{
				return $this->EstNonNul($objet) ;
			}
			public function ValeurNulle()
			{
				return new PvNul() ;
			}
			public function & ObjetNul()
			{
				$objet = $this->ValeurNulle() ;
				return $objet ;
			}
			public function CorrigeChemin($cheminFichier)
			{
				$resultat = $cheminFichier ;
				if(DIRECTORY_SEPARATOR != "/")
					$resultat = str_replace("/", DIRECTORY_SEPARATOR, $resultat) ;
				if(DIRECTORY_SEPARATOR != "\\")
					$resultat = str_replace("\\", DIRECTORY_SEPARATOR, $resultat) ;
				return $resultat ;
			}
			public function ObtientDelaiMaxExecution()
			{
				return ini_get('max_execution_time');
			}
			public function ObtientDelaiMaxExec()
			{
				return $this->ObtientDelaiMaxExecution() ;
			}
			public function DelaiMaxExec()
			{
				return $this->ObtientDelaiMaxExecution() ;
			}
		}
		class PvNul extends PvObjet
		{
			public $EstNul = 1 ;
		}
		
		class PvApplication extends PvObjet
		{
			public $CheminIconeElem = "images/icone-elem-app.png" ;
			public $CheminMiniatureElem = "images/miniature-elem-app.png" ;
			public $ValeurUniteTache = 5 ;
			public $ModelesOperation = array() ;
			public $IHMs = array() ;
			public $SystTrad ;
			public $BasesDonnees = array() ;
			public $ServsPersists = array() ;
			public $TachesProgs = array() ;
			public $Elements = array() ;
			public $CheminFichierElementActif = "" ;
			public $CheminFichierAbsolu = "" ;
			public $CheminFichierRelatif = "../.." ;
			public $NomElementActif = "" ;
			public $ElementActif = null ;
			public $ElementHorsLigne = null ;
			public $DebogageActive = 0 ;
			public $CtrlTachesProgs ;
			public $CtrlServsPersists ;
			public $ChemRelRegServsPersists ;
			public function ObtientChemRelRegServsPersists()
			{
				return dirname(__FILE__)."/".$this->CheminFichierRelatif."/".$this->ChemRelRegServsPersists ;
			}
			public function Debogue($niveau, $message)
			{
				if(! $this->DebogageActive)
				{
					return ;
				}
			}
			public function ChargeConfig()
			{
				$this->ChargeBasesDonnees() ;
				$this->ChargeSystTrad() ;
				$this->ChargeTachesProgs() ;
				$this->ChargeServsPersists() ;
				$this->ChargeIHMs() ;
				$this->ChargeElementHorsLigne() ;
			}
			public function InscritCtrlTachesProgs($nomElem='ctrlTachesProgs')
			{
				$this->CtrlTachesProgs = new PvCtrlTachesProgsApp() ;
				$this->InscritTacheProg($nomElem, $this->CtrlTachesProgs) ;
			}
			public function InscritCtrlServsPersists($nomElem='ctrlTachesProgs')
			{
				$this->CtrlServsPersists = new PvCtrlServsPersistsApp() ;
				$this->InscritTacheProg($nomElem, $this->CtrlServsPersists) ;
			}
			protected function ChargeBasesDonnees()
			{
			}
			public function Traduit($nomExpr, $params=array(), $valParDefaut='', $nomTrad='')
			{
				return $this->SystTrad->Execute($nomExpr, $params, $valParDefaut, $nomTrad) ;
			}
			public function ActiveTraducteur($nomTrad)
			{
				return $this->SystTrad->ActiveTraducteur($nomTrad) ;
			}
			public function CreeSystTrad()
			{
				return new PvSystemeTradsBase() ;
			}
			protected function ChargeSystTrad()
			{
				$this->SystTrad = $this->CreeSystTrad() ;
				$this->SystTrad->ChargeConfig() ;
			}
			protected function ChargeIHMs()
			{
			}
			protected function ChargeTachesProgs()
			{
			}
			protected function ChargeServsPersists()
			{
			}
			protected function ChargeElementHorsLigne()
			{
				$this->ElementHorsLigne = null ;
			}
			public function InscritElement($nom, & $element)
			{
				if(isset($this->Elements[$nom]))
				{
					die("Impossible d'inscrire l'element ".$nom.". Il existe deja.") ;
				}
				$this->Elements[$nom] = & $element ;
				$element->AdopteApplication($nom, $this) ;
			}
			public function InscritIHM($nom, & $ihm)
			{
				$this->IHMs[$nom] = & $ihm ;
				$this->InscritElement($nom, $ihm) ;
			}
			public function InscritTacheProg($nom, & $tacheProg)
			{
				$this->TachesProgs[$nom] = & $tacheProg ;
				$this->InscritElement($nom, $tacheProg) ;
			}
			public function InscritServPersist($nom, & $srvPersist)
			{
				$this->ServsPersists[$nom] = & $srvPersist ;
				$this->InscritElement($nom, $srvPersist) ;
			}
			public function InscritBaseDonnees($nom, & $bd)
			{
				$this->BasesDonnees[$nom] = & $bd ;
				// $this->InscritElement($nom, $bd) ;
			}
			public function EnregIHM(& $ihm)
			{
				$this->InscritIHM($ihm->IDInstance, $ihm) ;
			}
			public function DeclareIHM($nom='', $classeIHM='', $cheminFichier='')
			{
				if(! class_exists($classeIHM))
				{
					die("La classe $classIHM n'existe pas. Elle ne peut pas etre inscrte comme zone IHM") ;
				}
				$ihm = new $classeIHM() ;
				if($nom == '')
				{
					$nom = "IHM_".(count($this->IHMs) + 1) ;
				}
				$ihm->CheminFichierRelatif = $cheminFichier ;
				$nomPropriete = 'IHM'.ucfirst($nom) ;
				$this->$nomPropriete = & $ihm ;
				$this->InscritIHM($nom, $ihm) ;
			}
			public function DetecteElementActif()
			{
				$ok = 0 ;
				$this->DetecteCheminFichierElementActif() ;
				$this->ElementActif = $this->ValeurNulle() ;
				$this->NomElementActif = "" ;
				$cles = array_keys($this->Elements) ;
				foreach($cles as $i => $cle)
				{
					$element = & $this->Elements[$cle] ;
					$ok = $element->EstActif($this->CheminFichierAbsolu, $this->CheminFichierElementActif) ;
					if($ok)
					{
						$this->NomElementActif = $element->NomElementApplication ;
						$this->ElementActif = & $element ;
						break ;
					}
				}
				return $ok ;
			}
			public function ExecuteElementActif()
			{
				if($this->EstPasNul($this->ElementActif))
				{
					$this->ElementActif->ChargeConfig() ;
					$this->ElementActif->Execute() ;
				}
				else
				{
					if($this->EstPasNul($this->ElementHorsLigne))
					{
						$this->ElementHorsLigne->ChargeConfig() ;
						$this->ElementHorsLigne->Execute() ;
					}
				}
			}
			protected function DetecteCheminFichierElementActif()
			{
				$this->CheminFichierAbsolu = dirname(__FILE__) ;
				if($this->CheminFichierRelatif != "")
				{
					$this->CheminFichierAbsolu .= "/".$this->CheminFichierRelatif ;
				}
				$this->CheminFichierAbsolu = realpath($this->CheminFichierAbsolu) ;
				if(php_sapi_name() == "cli")
				{
					$this->CheminFichierElementActif = $_SERVER["argv"][0] ;
				}
				else
				{
					$this->CheminFichierElementActif = $_SERVER["SCRIPT_FILENAME"] ;
				}
				$this->CheminFichierElementActif = realpath($this->CheminFichierElementActif) ;
				// echo "Chemin actif : ".$this->CheminFichierElementActif."\n" ;
			}
			public function Execute()
			{
				// print_r("MMMM ".count($this->IHMs)) ;
				$this->ChargeConfig() ;
				$this->DetecteElementActif() ;
				$this->ExecuteElementActif() ;
			}
		}
		
		class PvElementApplication extends PvObjet
		{
			public $ApplicationParent = null ;
			public $NomElementApplication = "" ;
			public $CheminFichierRelatif = "" ;
			public $AccepterTousChemins = 0 ;
			public $Titre ;
			public $CheminIcone ;
			public $CheminMiniature ;
			public $Description ;
			public $DelaiExecution = 0 ;
			public $DebutExecution = 0 ;
			public $FinExecution = 0 ;
			public $TempsExecution = 0 ;
			public $DelaiExecutionPrec = 0 ;
			public function ObtientCheminIcone()
			{
				if($this->CheminIcone != '')
					return $this->CheminIcone ;
				return $this->ApplicationParent->CheminIconeElem;
			}
			public function ObtientCheminMiniature()
			{
				if($this->CheminMiniature != '')
					return $this->CheminMiniature ;
				return $this->ApplicationParent->CheminMiniatureElem ;
			}
			public function ObtientTitre()
			{
				if($this->Titre != '')
					return $this->Titre ;
				return ucfirst($this->NomElementApplication) ;
			}
			public function ObtientDescription()
			{
				return $this->Description ;
			}
			public function Traduit($nomExpr, $params=array(), $valParDefaut='', $nomTrad='')
			{
				return $this->ApplicationParent->Traduit($nomExpr, $params, $valParDefaut, $nomTrad) ;
			}
			public function ActiveTraducteur($nomTrad)
			{
				return $this->ApplicationParent->ActiveTraducteur($nomTrad) ;
			}
			public function AdopteApplication($nom, & $application)
			{
				$this->ApplicationParent = & $application ;
				$this->NomElementApplication = $nom ;
				// print get_class($this)."<br>" ;
			}
			public function EstActif($cheminFichierAbsolu, $cheminFichierElementActif)
			{
				if($this->AccepterTousChemins)
				{
					return 1 ;
				}
				$cheminFichier = realpath($cheminFichierAbsolu.DIRECTORY_SEPARATOR.$this->CheminFichierRelatif) ;
				// echo $cheminFichier."\n" ;
				// echo get_class($this).' : '.$cheminFichierAbsolu.DIRECTORY_SEPARATOR.$this->CheminFichierRelatif.' hhh<br>' ;
				$ok = ($this->CorrigeChemin($cheminFichier) == $this->CorrigeChemin($cheminFichierElementActif)) ? 1 : 0 ;
				return $ok ;
			}
			public function ObtientCheminFichierRelatif()
			{
				return realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.$this->CheminFichierRelatif) ;
			}
			protected function DemarreExecution()
			{
				if($this->DelaiExecution > 0)
				{
					$this->DelaiExecutionPrec = @set_time_limit($this->DelaiExecution) ;
					$this->DebutExecution = date("U") ;
				}
			}
			public function ObtientTempsExecution()
			{
				return $this->FinExecution - $this->DebutExecution ;
			}
			protected function TermineExecution()
			{
				@set_time_limit($this->DelaiExecutionPrec) ;
				$this->FinExecution = date("U") ;
			}
			public function Execute()
			{
				$this->DemarreExecution() ;
				$this->TermineExecution() ;
			}
		}
		
		class PvIHM extends PvElementApplication
		{
			public $TypeIHM = "indefini" ;
		}
		
		class PvPlateformProc
		{
			public function Http()
			{
				return new PvPlateformProcHttp() ;
			}
			public function Console()
			{
				return new PvPlateformProcHttp() ;
			}
		}
		class PvPlateformProcBase
		{
			public function EstDisponible()
			{
				return 0 ;
			}
			public function RecupArgs()
			{
				return array() ;
			}
			public function LanceProcessusProg(& $prog)
			{
			}
			public function TermineProcessusProg(& $prog)
			{
			}
		}
		class PvPlateformProcIndef extends PvPlateformProcBase
		{
			public function EstDisponible()
			{
				return 0 ;
			}
			public function RecupArgs()
			{
				return array() ;
			}
			public function LanceProcessusProg(& $prog)
			{
			}
		}
		class PvPlateformProcConsole extends PvPlateformProcBase
		{
			public function ObtientNomOS()
			{
				return (PHP_OS == "WINNT" || PHP_OS == "WIN32") ? 'Windows' : 'Linux' ;
			}
			public function EstDisponible()
			{
				return php_sapi_name() == 'cli' ? 1 : 0 ;
			}
			public function RecupArgs()
			{
				$args = array() ;
				for($i=1; $i<count($_SERVER["argv"]); $i++)
				{
					$partsArg = explode("=", $_SERVER["argv"][$i], 2) ;
					$partsArg[0] = preg_replace('/^\-+/', '', $partsArg[0]) ;
					if(! isset($partsArg[1]))
					{
						$partsArg[1] = null ;
					}
					$args[$partsArg[0]] = $partsArg[1] ;
				}
				return $args ;
			}
			protected function ObtientOS()
			{
				$os = (PHP_OS == "WINNT" || PHP_OS == "WIN32") ? 'Windows' : 'Linux' ;
				return $os ;
			}
			public function ObtientCmdExecProg(& $prog)
			{
				$os = $this->ObtientOS() ;
				$phpbin = preg_replace("@/lib(64)?/.*$@", "/bin/php", ini_get("extension_dir"));
				$execPath = dirname($phpbin)."/php" ;
				if($os == 'Windows')
					$execPath .= ".exe" ;
				$cmd = realpath(dirname(__FILE__).'/../../'.$prog->CheminFichierRelatif) ;
				if($cmd === false)
				{
					return "" ;
				}
				foreach($prog->ArgsParDefaut as $nom => $val)
				{
					$cmd .= ' -'.$nom.'='.escapeshellarg($val) ;
				}
				$cmd = $execPath.' '.$cmd ;
				if($os == 'Linux')
				{
					$cmd = $cmd.' >/dev/null 2>&1 &' ;
				}
				else
				{
					$cmd = 'start /b '.$cmd ;
				}
				return $cmd ;
			}
			public function LanceProcessusProg(& $prog)
			{
				$os = $this->ObtientOS() ;
				$cmd = $this->ObtientCmdExecProg($prog) ;
				// echo $cmd."\n" ;
				if($os == 'Linux')
				{
					return pclose(popen($cmd, 'r')) ;
				}
				else
				{
					$fluxProc = popen($cmd, 'r') ;
					register_shutdown_function(array(& $this, 'AnnuleFluxProc'), array(& $fluxProc)) ;
					return 1 ;
				}
			}
			public function TermineProcessusProg(& $prog)
			{
				$os = $this->ObtientNomOS() ;
			}
			public function AnnuleFluxProc($fluxProc)
			{
				if(is_resource($fluxProc))
				{
					pclose($fluxProc) ;
				}
			}
		}
		class PvPlateformProcHttp extends PvPlateformProcBase
		{
			public function EstDisponible()
			{
				return php_sapi_name() != 'cli' ? 1 : 0 ;
			}
			public function RecupArgs()
			{
				$args = $_GET ;
				return $args ;
			}
			protected function ExtraitPort()
			{
				$port = (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != 80) ? $_SERVER["SERVER_PORT"] : 80 ;
				return $port ;
			}
			protected function ExtraitUrlProg(& $prog)
			{
				$port = $this->ExtraitPort() ;
				$url = (isset($_SERVER["HTTPS"])) ? 'https' : 'http'.'://'.$_SERVER["SERVER_NAME"].(($port != 80) ? ':'.$port : '').'/'.$prog->CheminFichierRelatif ;
				return $url ;
			}
			public function LanceProcessusProg(& $prog)
			{
				$port = $this->ExtraitPort() ;
				$entetesHttp = '' ;
				$entetesHttp .= "GET /".$prog->CheminFichierRelatif."?".http_build_query_string($prog->ArgsParDefaut)." HTTP/1.0\r\n" ;
				$entetesHttp .= "Host: ".$_SERVER["SERVER_NAME"]."\r\n";
				$entetesHttp .= "Connection: close\r\n\r\n";
				$flux = @fsockopen($_SERVER["SERVER_NAME"], $port) ;
				if($flux == false)
				{
					return 0 ;
				}
				fputs($flux, $entetesHttp) ;
				fclose($flux) ;
				$flux = false ;
				return 1 ;
			}
		}
		
		class PvProgramAppBase extends PvElementApplication
		{
			public $Plateforme = null ;
			protected $NaturePlateforme = "" ;
			public $ArgsParDefaut = array() ;
			public $Args = array() ;
			protected function CreePlateforme()
			{
				$platf = new PvPlateformProcIndef() ;
				switch(strtoupper($this->NaturePlateforme))
				{
					case "WEB" :
					case "NAVIGATEUR" :
					case "BROWSER" :
					case "HTTP" :
						{ $platf = new PvPlateformProcHttp() ; }
					break ;
					case "CONSOLE" :
					case "SHELL" :
					case "DOS" :
						{ $platf = new PvPlateformProcConsole() ; }
					break ;
				}
				return $platf ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Plateforme = $this->CreePlateforme() ;
			}
			protected function DetecteArgs()
			{
				$this->Args = $this->ArgsParDefaut ;
				$args = $this->Plateforme->RecupArgs() ;
				foreach($this->Args as $nom => $val)
				{
					if(isset($args[$nom]))
						$this->Args[$nom] = $args[$nom] ;
				}
			}
			public function EstActif($cheminFichierAbsolu, $cheminFichierElementActif)
			{
				$this->DetecteArgs() ;
				return parent::EstActif($cheminFichierAbsolu, $cheminFichierElementActif) ;
			}
			public function LanceProcessus()
			{
				return $this->Plateforme->LanceProcessusProg($this) ;
			}
			public function TermineProcessus()
			{
				return $this->Plateforme->TermineProcessusProg($this) ;
			}
		}
		
		class PvDeclenchTacheBase
		{
			public function DelaiTacheAtteint(& $tacheProg)
			{
			}
		}
		class PvDeclenchTacheIndef extends PvDeclenchTacheBase
		{
			public function DelaiTacheAtteint(& $tacheProg)
			{
				return 0 ;
			}
		}
		class PvDeclenchTjrTache extends PvDeclenchTacheBase
		{
			public function DelaiTacheAtteint(& $tacheProg)
			{
				return 1 ;
			}
		}
		class PvDeclenchJourTache extends PvDeclenchTacheBase
		{
			public $Heure = 0 ;
			public $Minute = 0 ;
			public $Seconde = 0 ;
			public function DelaiTacheAtteint(& $tacheProg)
			{
				$secondes = intval(date("s")) ;
				return date("G") == $this->Heure && intval(date("m")) == $this->Minute && ($secondes >= $this->Seconde && $secondes <= $this->Seconde + $tacheProg->ApplicationParent->ValeurUniteTache) ;
			}
		}
		class PvDeclenchSemaineTache extends PvDeclenchJourTache
		{
			public $Jour = 1 ;
			public function DelaiTacheAtteint(& $tacheProg)
			{
				$secondes = intval(date("s")) ;
				return date("w") == $this->Jour && parent::DelaiTacheAtteint($tacheProg) ;
			}
		}
		class PvDeclenchMoisTache extends PvDeclenchJourTache
		{
			public $Jour = 1 ;
			public function DelaiTacheAtteint(& $tacheProg)
			{
				if($this->Jour > date("t"))
					$this->Jour = date("t") ;
				$jourMois = intval(date("j")) ;
				return $jourMois == $this->Jour && parent::DelaiTacheAtteint($tacheProg) ;
			}
		}
		
		class PvTacheProg extends PvProgramAppBase
		{
			public $Declenchs = array() ;
			public $DeclenchParDefaut ;
			protected function CreeDeclenchParDefaut()
			{
				return new PvDeclenchTacheIndef() ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DeclenchParDefaut = $this->CreeDeclenchParDefaut() ;
			}
			public function DelaiAtteint()
			{
				$ok = 0 ;
				$declenchs = $this->Declenchs ;
				array_splice($declenchs, 0, 0, array($this->DeclenchParDefaut)) ;
				foreach($declenchs as $i => $declench)
				{
					if($declench->DelaiTacheAtteint($this))
					{
						$ok = 1 ;
						break ;
					}
				}
				return $ok ;
			}
			public function Execute()
			{
				if(! $this->Plateforme->EstDisponible() || ! $this->DelaiAtteint())
				{
					return ;
				}
				$this->DemarreExecution() ;
				$this->ExecuteSession() ;
				$this->TermineExecution() ;
			}
			protected function ExecuteSession()
			{
			}
		}
		
		class PvEtatServPersist
		{
			const ETAT_NON_DEFINI = 0 ;
			const ETAT_DEMARRE = 1 ;
			const ETAT_STOPPE = 2 ;
			public $PID = 0 ;
			public $Statut = 0 ;
			public $TimestmpCapt = 0 ;
			public $TimestmpDebutSession = 0 ;
			public $TimestmpFinSession = 0 ;
			public function EstDefini()
			{
				return $this->Statut != PvEtatServPersist::ETAT_NON_DEFINI ;
			}
		}
		class PvServPersist extends PvProgramAppBase
		{
			public $Arreter = 0 ;
			public $MaxSessions = 0 ;
			public $TotalSessions = 0 ;
			public $DelaiAttente = 5 ;
			public $DelaiBoucle = 30 ;
			public $DelaiEtatInactif = 120 ;
			public $LimiterDelaiBoucle = 0 ;
			public $Etat ;
			public $EnregEtat = 1 ;
			public $VerifSurPresenceProc = 0 ;
			protected $NaturePlateforme = "console" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Etat = new PvEtatServPersist() ;
				register_shutdown_function(array(& $this, "ConfirmEtatArrete")) ;
			}
			protected function ObtientChemFicEtat()
			{
				// print $this->NomElementApplication.' : '.get_class($this)."\n" ;
				return $this->ApplicationParent->ObtientChemRelRegServsPersists()."/".$this->NomElementApplication.".dat" ;
			}
			public function DemarreService()
			{
				$this->DetecteEtat() ;
				// print_r($this->Etat) ;
				if($this->Etat->PID != 0)
				{
					$processMgr = OsProcessManager::Current() ;
					$processMgr->KillProcessIDs($processMgr->KillProcessList(array($this->Etat->PID))) ;
				}
				$this->LanceProcessus() ;
			}
			public function EstDemarre()
			{
				// echo get_class($this).' : '.(date("U") - $this->Etat->TimestmpCapt)."\n" ;
				return $this->Etat->Statut == PvEtatServPersist::ETAT_DEMARRE && date("U") - $this->Etat->TimestmpCapt <= $this->DelaiEtatInactif + $this->DelaiAttente ;
			}
			public function DetecteEtat()
			{
				$this->ChargeEtat() ;
			}
			public function ChargeEtat()
			{
				if($this->EstNul($this->ApplicationParent))
				{
					return ;
				}
				$chemFicEtat = $this->ObtientChemFicEtat() ;
				if(! file_exists($chemFicEtat))
				{
					return ;
				}
				$fh = fopen($chemFicEtat, "r") ;
				$ctn = "" ;
				if($fh != false)
				{
					while(! feof($fh))
					{
						$ctn .= fgets($fh) ;
					}
					fclose($fh) ;
				}
				if($ctn != "")
				{
					$this->Etat = unserialize($ctn) ;
					if($this->Etat == false)
					{
						$this->Etat = new PvEtatServPersist() ;
					}
				}
			}
			protected function SauveEtat()
			{
				$chemFicEtat = $this->ObtientChemFicEtat() ;
				$fh = fopen($chemFicEtat, "w") ;
				fputs($fh, serialize($this->Etat)) ;
				fclose($fh) ;
			}
			protected function FixeTempsExec($nouvDelai)
			{
				$ancDelai = $this->DelaiMaxExec() ;
				set_time_limit($nouvDelai) ;
				return $ancDelai ;
			}
			protected function ProcPresent()
			{
				$cmd = $this->ObtientCmdExecProg() ;
				$processMgr = OsProcessManager::Current() ;
				$entries = $processMgr->LocateByName($cmd) ;
				return (count($entries) == 1) ;
			}
			public function Verifie()
			{
				$this->DetecteEtat() ;
				if(! $this->EstDemarre() || ($this->VerifSurPresenceProc == 1 && ! $this->ProcPresent()))
				{
					return 0 ;
				}
				return 1 ;
			}
			public function EstDisponible()
			{
				return 1 ;
			}
			protected function ConfirmEtatDebutSession()
			{
				$this->Etat->TimestmpDebutSession = date("U") ;
				$this->Etat->TimestmpFinSession = 0 ;
				$this->SauveEtat() ;
			}
			protected function ConfirmEtatFinSession()
			{
				$this->Etat->TimestmpFinSession = date("U") ;
				$this->SauveEtat() ;
			}
			protected function RepeteBoucle()
			{
				$this->TotalSessions = 0 ;
				while(! $this->Arreter)
				{
					if($this->LimiterDelaiBoucle)
						$oldTimeLimit = $this->FixeTempsExec($this->DelaiBoucle) ;
					$this->ExecuteSession() ;
					if($this->LimiterDelaiBoucle)
						$this->FixeTempsExec($oldTimeLimit) ;
					$this->TotalSessions++ ;
					if($this->MaxSessions > 0 && $this->TotalSessions >= $this->MaxSessions)
					{
						break ;
					}
					if($this->DelaiAttente > 0)
					{
						sleep($this->DelaiAttente) ;
					}
					$this->SauveEtat() ;
				}
			}
			protected function ExecuteSession()
			{
			}
			protected function PrepareEnvironnement()
			{
			}
			protected function ConfirmEtatDemarre()
			{
				$this->Etat->PID = getmypid() ;
				$this->Etat->TimestmpCapt = date("U") ;
				$this->Etat->Statut = PvEtatServPersist::ETAT_DEMARRE ;
				$this->SauveEtat() ;
			}
			public function ConfirmEtatArrete()
			{
				$this->DetecteEtat() ;
				if($this->Etat->PID != getmypid())
					return ;
				$this->Etat->PID = 0 ;
				$this->Etat->TimestmpCapt = date("U") ;
				$this->Etat->Statut = PvEtatServPersist::ETAT_STOPPE ;
				$this->SauveEtat() ;
			}
			protected function DemarreExecution()
			{
				parent::DemarreExecution() ;
				$this->ConfirmEtatDemarre() ;
			}
			public function Execute()
			{
				if(! $this->Plateforme->EstDisponible() || ! $this->EstDisponible())
				{
					return ;
				}
				$this->DemarreExecution() ;
				$this->PrepareEnvironnement() ;
				$this->RepeteBoucle() ;
				$this->TermineExecution() ;
			}
		}
		class PvServicePersist extends PvServPersist
		{
		}
		
		class PvSystemeTradsBase extends PvObjet
		{
			public $Traducteurs = array() ;
			public $NomTraducteurActif ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeTraducteurs() ;
				$this->DetecteTraducteurActif() ;
			}
			protected function DetecteTraducteurActif()
			{
				if(count($this->Traducteurs) > 0)
				{
					$nomTrads = array_keys($this->Traducteurs) ;
					$this->NomTraducteurActif = $nomTrads[0] ;
				}
			}
			protected function ChargeTraducteurs()
			{
			}
			public function TraducteurActif()
			{
				$nomTrad = $this->NomTraducteurActif ;
				$traducteur = new PvTraducteur() ;
				$traducteur->EstNul = 1 ;
				if(isset($this->Traducteurs[$nomTrad]))
				{
					$traducteur = & $this->Traducteurs[$nomTrad] ;
				}
				return $traducteur ;
			}
			public function Execute($nomExpr, $params=array(), $valParDefaut='', $nomTrad='')
			{
				$traducteur = null;
				if($nomTrad == '' || ! isset($this->Traducteurs[$nomTrad]))
				{
					$traducteur = & $this->Traducteurs[$nomTrad] ;
				}
				else
				{
					$traducteur = $this->TraducteurActif() ;
				}
				return $traducteur->Execute($nomExpr, $params, $valParDefaut) ;
			}
			public function ActiveTraducteur($nomTrad='')
			{
				if(! isset($this->Traducteurs[$nomTrad]))
				{
					return ;
				}
				$this->NomTraducteurActif = $nomTrad ;
			}
		}
		class PvTraducteur extends PvObjet
		{
			public $Exprs = array() ;
			public $IdLangue = 0 ;
			public $NomLangue = "" ;
			public $LibelleLangue = "" ;
			public $EstNul = 0 ;
			public function Execute($nomExpr, $params=array(), $valParDefaut='')
			{
				$val = $valParDefaut ;
				if(isset($this->Exprs[$nomExpr]))
				{
					$val = _parse_pattern($this->Exprs[$nomExpr], $params) ;
				}
				return $val ;
			}
		}
		
		if(! function_exists('RemoveMagicQuotes'))
		{
			function RemoveMagicQuotes ($postArray, $trim = false)
			{
				if (get_magic_quotes_gpc() == 1)
				{
						if ( is_array($postArray) )
						{
								$newArray = array();   
							 
								foreach ($postArray as $key => $val)
								{
										if (is_array($val))
										{
												$newArray[$key] = removeMagicQuotes ($val, $trim);
										}
										else
										{
												if ($trim == true)
												{
														$val = trim($val);
												}
												$newArray[$key] = stripslashes($val);
										}
								}
								return $newArray;
						}
						else
						{
								return stripcslashes($postArray);
						}
				}
				else
				{
						return $postArray;   
				}
			}
		}
		
		if(php_sapi_name() != 'cli' && get_magic_quotes_gpc() == 1)
		{
			$_PHP_POST = $_POST ;
			$_PHP_GET = $_GET ;
			$_PHP_COOKIE = $_COOKIE ;
			
			$_GET = RemoveMagicQuotes($_GET) ;
			$_POST = RemoveMagicQuotes($_POST) ;
			$_COOKIE = RemoveMagicQuotes($_COOKIE) ;
		}
	}
	
?>