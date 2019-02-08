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
		if(! defined('FORCE_ENCODING'))
		{
			include dirname(__FILE__)."/../misc/ForceEncoding.class.php" ;
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
			public $AttrsSuppl = array() ;
			public $EstNul = 0 ;
			public function ValAttrSuppl($nom, $valeurDefaut=null)
			{
				if(isset($this->AttrsSuppl[$nom]))
				{
					return $this->AttrsSuppl[$nom] ;
				}
				return $valeurDefaut ;
			}
			public function AffecteAttrSuppl($nom, $valeur)
			{
				$this->AttrsSuppl[$nom] = $valeur ;
			}
			public function FixeAttrSuppl($nom, $valeur)
			{
				$this->AffecteAttrSuppl($nom, $valeur) ;
			}
			public function SupprAttrSuppl($nom, $valeur)
			{
				unset($this->AttrsSuppl[$nom]) ;
			}
			public function ObtientAttrSuppl($nom, $valeurDefaut=null)
			{
				return $this->ValAttrSuppl($nom, $valeurDefaut) ;
			}
			public function CreeInstanceGener()
			{
				$nomClasse = get_class($this) ;
				return new $nomClasse() ;
			}
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
				$nomClasse = get_class($this) ;
				try
				{
					eval('if(isset('.$nomClasse.'::$'.$nomPropriete.'))
					{
						$valeur = '.$nomClasse.'::$'.$nomPropriete.' ;
					}') ;
				}
				catch(Exception $ex)
				{
				}
				return $valeur ;
			}
			public function AffecteValeurStatique($nomPropriete, $valeur)
			{
				$nomClasse = get_class($this) ;
				try
				{
					eval('if(isset('.$nomClasse.'::$'.$nomPropriete.'))
					{
						'.$nomClasse.'::$'.$nomPropriete.' = $valeur ;
					}') ;
				}
				catch(Exception $ex)
				{
				}
				return $valeur ;
			}
			public function __construct()
			{
				$this->InitConfigStatique() ;
				$this->InitConfig() ;
			}
			protected function InitConfigStatique()
			{
				$classe = get_class($this) ;
				eval('if(! isset('.$classe.'::$TotalInstances))
				{
					'.$classe.'::$TotalInstances = 0 ;
				}') ;
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
			public function ObtientValSuppl($nom, $valeurDefaut=null)
			{
				return (isset($this->AttrsSuppl[$nom])) ? $this->AttrsSuppl[$nom] : $valeurDefaut ;
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
			public $AutoDetectChemRelFichierActif = 1 ;
			public $ModelesOperation = array() ;
			public $IHMs = array() ;
			public $SystTrad ;
			public $BasesDonnees = array() ;
			public $ServsPersists = array() ;
			public $TachesProgs = array() ;
			public $Integrations = array() ;
			public $Elements = array() ;
			public $CheminFichierElementActifFixe = "" ;
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
			public $NomsInterfsPaiement = array() ;
			public $NomZoneRenduInterfPaiement ;
			public $UrlRacine ;
			public $CheminDossierRacine ;
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
				$this->ChargeIntegrations() ;
				$this->AppliqueIntegrations() ;
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
			public function & InscritStopServsPersists($nomElem='stopTachesProgs')
			{
				$stopServsPersists = new PvStopServsPersistsApp() ;
				$this->InscritTacheProg($nomElem, $stopServsPersists) ;
				return $stopServsPersists ;
			}
			public function & InscritArretServsPersists($cheminRelatif="", $nomElem='stopTachesProgs')
			{
				$stopServsPersists = $this->InscritStopServsPersists($nomElem) ;
				$stopServsPersists->CheminFichierRelatif = $cheminRelatif ;
				return $stopServsPersists ;
			}
			protected function ChargeBasesDonnees()
			{
			}
			public function & InsereInterfPaiement($nom, $interf)
			{
				$interf = $this->InsereIHM($nom, $interf) ;
				$this->NomsInterfsPaiement[] = $nom ;
				return $interf ;
			}
			public function & InterfsPaiement()
			{
				$results = array() ;
				foreach($this->NomsInterfsPaiement as $i => $nom)
				{
					$results[$nom] = & $this->IHMs[$nom] ;
				}
				return $results ;
			}
			public function & InterfPaiement($nom)
			{
				$result = null ;
				if(! in_array($nom, array_keys($this->NomsInterfsPaiement)))
				{
					return $result ;
				}
				return $this->IHMs[$nom] ;
			}
			public function & ExisteInterfPaiement($nom)
			{
				return in_array($nom, $this->NomsInterfsPaiement) ;
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
			protected function ChargeIntegrations()
			{
			}
			protected function AppliqueIntegrations()
			{
				$nomsIntegrs = array_keys($this->Integrations) ;
				foreach($nomsIntegrs as $i => $nomIntegr)
				{
					$integr = & $this->Integrations[$nomIntegr] ;
					$integr->ChargeConfig() ;
					$integr->RemplitApplication($nomIntegr, $this) ;
				}
			}
			public function & InscritIntegration($nomIntegr, & $integr)
			{
				return $this->InsereIntegration($nomIntegr, $integr) ;
			}
			public function InsereIntegration($nomIntegr, $integr)
			{
				$this->Integrations[$nomIntegr] = & $integr ;
				return $integr ;
			}
			public function & ObtientIntegration($nomIntegr)
			{
				$integr = new PvIntegrationIndef() ;
				if(isset($this->Integrations[$nomIntegr]))
				{
					$integr = & $this->Integrations[$nomIntegr] ;
				}
				return $integr ;
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
			public function & InsereBaseDonnees($nom, $bd)
			{
				$this->InscritBaseDonnees($nom, $bd) ;
				return $bd ;
			}
			public function & InsereTacheProg($nom, $tacheProg)
			{
				$this->InscritTacheProg($nom, $tacheProg) ;
				return $tacheProg ;
			}
			public function & InsereServPersist($nom, $srvPersist)
			{
				$this->InscritServPersist($nom, $srvPersist) ;
				return $srvPersist ;
			}
			public function & InsereServsProcessus($nom, $srvProc, $totalInstances=2)
			{
				$servs = array() ;
				for($i=0; $i<$totalInstances; $i++)
				{
					$servs[$i] = $this->InsereServPersist($nom."_".$i, $srvProc) ;
					$servs[$i]->ArgsParDefaut["no_processus"] = $i ;
				}
				return $servs ;
			}
			public function & InsereIHM($nom, $ihm)
			{
				$this->InscritIHM($nom, $ihm) ;
				return $ihm ;
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
			public function EnModeConsole()
			{
				return (php_sapi_name() == "cli" || (isset($_SERVER["argv"]) && isset($_SERVER["argv"][0]) && ! isset($_SERVER["SCRIPT_FILENAME"]))) ;
			}
			public function ContenuRequeteHttp()
			{
				$ctn = '' ;
				$ctn .= $_SERVER["REQUEST_METHOD"]." ".$_SERVER["SERVER_PROTOCOL"]." ".$_SERVER["REQUEST_URI"].$_SERVER["QUERY_STRING"]."\r\n" ;
				$entetes = apache_request_headers() ;
				foreach($entetes as $nom => $valeur)
				{
					$ctn .= $nom." : ".$valeur."\r\n" ;
				}
				$ctn .= "\r\n".file_get_contents("php://input") ;
				return $ctn ;
			}
			protected function DetecteCheminFichierElementActif()
			{
				$this->CheminFichierAbsolu = dirname(__FILE__) ;
				if($this->CheminFichierRelatif != "")
				{
					$this->CheminFichierAbsolu .= "/".$this->CheminFichierRelatif ;
				}
				$this->CheminFichierAbsolu = realpath($this->CheminFichierAbsolu) ;
				if($this->AutoDetectChemRelFichierActif == 0)
				{
					$this->CheminFichierElementActif = $this->CheminFichierElementActifFixe ;
					return ;
				}
				if($this->EnModeConsole())
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
			public static function TelechargeUrl($url, $valeurPost=array(), $async=1)
			{
				$parts = parse_url($url) ;
				$port = $parts["port"] != '' ? $parts["port"] : (($parts["scheme"] == "https") ? 443 : 80) ;
				$chainePostee = http_build_query_string($valeurPost) ;
				$res = false ;
				$fh = fsockopen($parts["host"], $port, $errno, $errstr, 30) ;
				if ($fh)
				{
					if($chainePostee == '')
					{
						$ctn = "GET ".$parts["path"]."?".$parts["query"]." HTTP/1.0\r\n";
						$ctn .= "Host: ".$parts["host"].":".$port."\r\n" ;
						$ctn .= "Content-Type: text/html\r\n" ;
						$ctn .= "Connection: Close\r\n\r\n" ;
					}
					else
					{
						$ctn = "POST ".$parts["path"]."?".$parts["query"]." HTTP/1.1\r\n";
						$ctn .= "Host: ".$parts["host"].":".$port."\r\n" ;
						$ctn .= "Content-Type: application/x-www-form-urlencoded\r\n" ;
						$ctn .= "Content-Length: ".strlen($chainePostee)."\r\n" ;
						$ctn .= "Connection: Close\r\n\r\n" ;
						$ctn .= $chainePostee ;
						// print $ctn ;
					}
					$ok = fputs($fh, $ctn) ;
					if($async == 0)
					{
						$res = '' ;
						while(! feof($fh))
						{
							$res .= fgets($fh) ;
						}
					}
					else
					{
						$res = $ok ;
					}
					fclose($fh) ;
				}
				return $res ;
			}
			public static function TelechargeShell($commande, $async=1)
			{
				$fh = popen($commande, "r") ;
				if($async == 1)
				{
					if($fh !== false)
					{
						pclose($fh) ;
						return 1 ;
					}
					else
					{
						return 0 ;
					}
				}
				$res = '' ;
				while(! feof($fh))
				{
					$res .= fgets($fh) ;
				}
				pclose($fh) ;
				return $res ;
			}
			public static function ObtientCheminPHP()
			{
				$phpbin = preg_replace("@/lib(64)?/.*$@", "/bin/php", ini_get("extension_dir"));
				$execPath = dirname($phpbin)."/php" ;
				if($os == 'Windows')
					$execPath .= ".exe" ;
				return $execPath ;
			}
			public static function EncodeArgsShell($args)
			{
				$cmd = '' ;
				foreach($args as $nom => $val)
				{
					$cmd .= ' -'.$nom.'='.escapeshellarg($val) ;
				}
				return $cmd ;
			}
			public static function TelechargeCmd($adresse, $args=array(), $valeurPost='', $async=1)
			{
				$proc = new OsProcessPipe() ;
				$cmd = $adresse ;
				if(is_array($args) && count($args) > 0)
				{
					$cmd .= PvApplication::EncodeArgsShell($args) ;
				}
				$result = false ;
				if($proc->Open($cmd))
				{
					if($valeurPost != '')
					{
						$proc->Write($valeurPost) ;
					}
					$result = false ;
					if($async)
					{
						$proc->Close() ;
						return true ;
					}
					$error = $proc->GetError() ;
					if($error == '')
					{
						$ctn = $proc->ReadUntilEOF() ;
					}
					$proc->Close() ;
				}
				return $result ;
			}
			public static function ObtientOS()
			{
				$os = (PHP_OS == "WINNT" || PHP_OS == "WIN32") ? 'Windows' : 'Linux' ;
				return $os ;
			}
			public function & ZoneRenduInterfPaiement()
			{
				$zoneWeb = new PvZoneWebSimple() ;
				if($this->NomZoneRenduInterfPaiement != '' && isset($this->IHMs[$this->NomZoneRenduInterfPaiement]))
				{
					$zoneWeb = & $this->IHMs[$this->NomZoneRenduInterfPaiement] ;
				}
				return $zoneWeb ;
			}
		}
		
		class PvIntegration extends PvObjet
		{
			protected $NomIntegration ;
			public $NomDocumentWeb = "" ;
			protected $PrivilegesGlobauxScript = array() ;
			public function EstIndefinie()
			{
				return false ;
			}
			public function RemplitApplication($nomIntegration, & $app)
			{
				$this->NomIntegration = $nomIntegration ;
				$this->RemplitBasesDonnees($app) ;
				$this->RemplitTachesProgs($app) ;
				$this->RemplitServsPersists($app) ;
				$this->RemplitIHMs($app) ;
				$this->RemplitApplicationSpec($app) ;
				$this->NomIntegration = "" ;
			}
			protected function & InsereTacheProg($nom, $tacheProg, & $app)
			{
				$res = $app->InsereTacheProg($this->NomIntegration."_".$nom, $tacheProg) ;
				$res->NomIntegrationParent = $this->NomIntegration ;
				return $res ;
			}
			protected function & InsereServPersist($nom, $serv, & $app)
			{
				$res = $app->InsereServPersist($this->NomIntegration."_".$nom, $serv) ;
				$res->NomIntegrationParent = $this->NomIntegration ;
				return $res ;
			}
			protected function & InsereIHM($nom, $ihm, & $app)
			{
				$res = $app->InsereIHM($this->NomIntegration."_".$nom, $ihm) ;
				$res->NomIntegrationParent = $this->NomIntegration ;
				return $res ;
			}
			protected function & InsereZone($nom, $ihm, & $app)
			{
				return $this->InsereIHM($nom, $ihm, $app) ;
			}
			protected function & InsereScript($nom, $script, & $zone, $privs=array())
			{
				$res = $zone->InsereScript($this->NomIntegration."_".$nom, $script) ;
				if(count($this->PrivilegesGlobauxScript) > 0)
				{
					array_splice($res->Privileges, count($res->Privileges), 0, $this->PrivilegesGlobauxScript) ;
				}
				if(count($privs) > 0)
				{
					array_splice($res->Privileges, count($res->Privileges), 0, $privs) ;
				}
				$res->NomIntegrationParent = $this->NomIntegration ;
				$res->NomDocumentWeb = $this->NomDocumentWeb ;
				return $res ;
			}
			protected function RemplitApplicationSpec(& $app)
			{
			}
			protected function RemplitBasesDonnees(& $app)
			{
			}
			protected function RemplitTachesProgs(& $app)
			{
				foreach($app->TachesProgs as $nom => & $tacheProg)
				{
					$this->RemplitTacheProg($tacheProg) ;
				}
			}
			protected function RemplitServsPersists(& $app)
			{
				foreach($app->ServsPersists as $nom => & $serv)
				{
					$this->RemplitServPersist($serv) ;
				}
			}
			protected function RemplitIHMs(& $app)
			{
				foreach($app->IHMs as $nom => & $ihm)
				{
					$this->RemplitIHM($ihm) ;
				}
			}
			protected function RemplitIHM(& $ihm)
			{
			}
			protected function RemplitServPersist(& $serv)
			{
			}
			protected function RemplitTacheProg(& $tacheProg)
			{
			}
		}
		class PvIntegrationIndef extends PvIntegration
		{
			public function EstIndefinie()
			{
				return true ;
			}
		}
		
		class PvElementApplication extends PvObjet
		{
			public $ApplicationParent = null ;
			public $NomElementApplication = "" ;
			public $NomIntegrationParent = "" ;
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
			public $Integrations = array() ;
			public function NatureElementApplication()
			{
				return "base" ;
			}
			public function IntegrationParent()
			{
				return $this->ApplicationParent->ObtientIntegration($this->NomIntegrationParent) ;
			}
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
				// echo $cheminFichier.' : '.$cheminFichierElementActif."<br>\n" ;
				
				// echo get_class($this).' : '.$cheminFichierAbsolu.DIRECTORY_SEPARATOR.$this->CheminFichierRelatif.' hhh<br>' ;
				$ok = ($this->CorrigeChemin($cheminFichier) == $this->CorrigeChemin($cheminFichierElementActif)) ? 1 : 0 ;
				// echo $cheminFichier.' : '.$cheminFichierElementActif." = ".$ok."<br>\n" ;
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
			public function NatureElementApplication()
			{
				return "ihm" ;
			}
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
				return PvApplication::ObtientOS() ;
			}
			public function ObtientCmdExecProg(& $prog)
			{
				$os = $this->ObtientOS() ;
				$execPath = PvApplication::ObtientCheminPHP() ;
				$cmd = realpath(dirname(__FILE__).'/../../'.$prog->CheminFichierRelatif) ;
				$chemJournal = '' ;
				if($this->SortieDansFichier == 1)
				{
					$chemJournal = dirname($cmd).'/'.$prog->IDInstanceCalc.'.log' ;
				}
				if($cmd === false)
				{
					return "" ;
				}
				$cmd .= PvApplication::EncodeArgsShell($prog->ArgsParDefaut) ;
				$cmd = $execPath.' '.$cmd ;
				if($os == 'Linux')
				{
					$cmd = $cmd.' >'.(($prog->SortieDansFichier == 1) ? $chemJournal : '/dev/null').' 2>&1 &' ;
				}
				else
				{
					$cmd = 'start /b ('.$cmd.(($prog->SortieDansFichier == 1) ? ' >'.$chemJournal.' 2>&1' : '').')' ;
				}
				return $cmd ;
			}
			public function LanceProcessusProg(& $prog)
			{
				$os = $this->ObtientOS() ;
				$cmd = $this->ObtientCmdExecProg($prog) ;
				if($cmd == '')
				{
					return false ;
				}
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
			public $SortieDansFichier = 0 ;
			protected function CreePlateforme()
			{
				$platf = new PvPlateformProcConsole() ;
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
					case "INDEF" :
					case "UNDEF" :
					case "INDEFINI" :
						{ $platf = new PvPlateformProcIndef() ; }
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
			public $ToujoursExecuter = 1 ;
			public $TypeDeclenchParDefaut = "" ;
			public function NatureElementApplication()
			{
				return "tache_programmee" ;
			}
			protected function CreeDeclenchParDefaut()
			{
				$declench = null ;
				if($this->TypeDeclenchParDefaut != "")
				{
					switch(strtolower($this->TypeDeclenchParDefaut))
					{
						case "jour" :
						case "day" :
						case "daily" :
						case "journalier" :
						{
							$declench = new PvDeclenchJourTache() ;
						}
						break ;
						case "semaine" :
						case "hebdo" :
						case "weekly" :
						case "week" :
						{
							$declench = new PvDeclenchSemaineTache() ;
						}
						break ;
						case "mois" :
						case "month" :
						case "monthly" :
						{
							$declench = new PvDeclenchMoisTache() ;
						}
						break ;
					}
				}
				if($declench != null)
				{
					return $declench ;
				}
				return new PvDeclenchTacheIndef() ;
			}
			public function DelaiAtteint()
			{
				$ok = 0 ;
				$declenchs = $this->Declenchs ;
				$declenchDefaut = ($this->ToujoursExecuter == 1) ? new PvDeclenchTjrTache() : $this->CreeDeclenchParDefaut() ;
				array_splice($declenchs, 0, 0, array($declenchDefaut)) ;
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
			public function NatureElementApplication()
			{
				return "service_persistant" ;
			}
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
				$this->ArreteService() ;
				$this->LanceProcessus() ;
			}
			public function ArreteService()
			{
				$this->DetecteEtat() ;
				if($this->Etat->PID != 0)
				{
					$processMgr = OsProcessManager::Current() ;
					$processMgr->KillProcessIDs($processMgr->KillProcessList(array($this->Etat->PID))) ;
				}
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
				$this->Etat->TimestmpCapt = date("U") ;
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
				return 1 ;
			}
			public function EstServiceDemarre()
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
					$this->PrepareSession() ;
					$this->ExecuteSession() ;
					$this->TermineSession() ;
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
			protected function PrepareSession()
			{
			}
			protected function TermineSession()
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