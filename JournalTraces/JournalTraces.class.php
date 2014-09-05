<?php
	
	if(! defined('JOURNAL_TRACES'))
	{
		if(! class_exists('UserAgentParser'))
		{
			include dirname(__FILE__)."/useragentparser.php" ;
		}
		define('JOURNAL_TRACES', 1) ;
		
		define('ID_GLOBAL_JOURNAL_TRACES', uniqid('JOURNAL_TRACES_')) ;
		
		class JournalTraces
		{
			public $FichiersTraces = array() ;
			public $Attributs = array() ;
			public $Contenu = "" ;
			public $Id = "" ;
			public function __construct()
			{
				$this->Id = uniqid("JournalTraces_") ;
			}
			public function ChargeConfig()
			{
				$this->ChargeFichiersTraces() ;
				$this->ChargeAttributs() ;
			}
			protected function ChargeFichiersTraces()
			{
			}
			protected function ChargeAttributs()
			{
			}
			protected function CreeEntree($type='INFO', $contenu="", $cheminFichier="", $ligne="0")
			{
				$entree = new EntreeTraceBase($type, $contenu, $cheminFichier, $ligne) ;
				$entree->JournalParent = & $this ;
				return $entree ;
			}
			public function InscritNotice($contenu, $cheminFichier="", $ligne="0")
			{
				$this->InscritEntree("NOTICE", $contenu, $cheminFichier, $ligne) ;
			}
			public function InscritAlerte($contenu, $cheminFichier="", $ligne="0")
			{
				$this->InscritEntree("ALERTE", $contenu, $cheminFichier, $ligne) ;
			}
			public function InscritInfo($contenu, $cheminFichier="", $ligne="0")
			{
				$this->InscritEntree("INFO", $contenu, $cheminFichier, $ligne) ;
			}
			public function InscritErreur($contenu, $cheminFichier="", $ligne="0")
			{
				$this->InscritEntree("ERREUR", $contenu, $cheminFichier, $ligne) ;
			}
			public function InscritException($contenu, $cheminFichier="", $ligne="0")
			{
				$this->InscritEntree("EXCEPTION", $contenu, $cheminFichier, $ligne) ;
			}
			public function Inscrit()
			{
				$this->InscritInfo("", "", 0) ;
			}
			public function InscritEntree($type="INFO", $contenu="", $cheminFichier="", $ligne="0")
			{
				$entree = $this->CreeEntree($type, $contenu, $cheminFichier, $ligne) ;
				$entree->Lie($this->Attributs) ;
				$this->SauveEntree($entree) ;
			}
			protected function SauveEntree($entree)
			{
				foreach($this->FichiersTraces as $i => $base)
				{
					$base->SauveEntree($entree) ;
				}
			}
		}
		
		class AttributEntreeTraceBase
		{
			public $UtiliserCache = 0 ;
			public $ValeurCache = false ;
			public $Nom = "" ;
			public $Libelle = "Base" ;
			public $EntreeTraceParent = null ;
			public $Valeur = null ;
			public $Erreur = "" ;
			protected function Prepare()
			{
				$this->Valeur = "" ;
				$this->Erreur = "" ;
			}
			public function Lie()
			{
				if($this->UtiliserCache && $this->ValeurCache !== false)
				{
					return ;
				}
				$this->Prepare() ;
				$this->Valeur = $this->ObtientValeur() ;
				if($this->UtiliserCache)
				{
					$this->ValeurCache = $this->Valeur ;
				}
			}
			public function ObtientValeur()
			{
				return "" ;
			}
		}
		class AttributIdEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "id" ;
			public $Libelle = "ID" ;
		}
		class AttributIdGlobalEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "id_global" ;
			public $Libelle = "ID Global" ;
			public function ObtientValeur()
			{
				return ID_GLOBAL_JOURNAL_TRACES ;
			}
		}
		class AttributIdJournalEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "id_journal" ;
			public $Libelle = "ID Journal" ;
			public function ObtientValeur()
			{
				return $this->EntreeTraceParent->JournalParent->Id ;
			}
		}
		class AttributIdSessionEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "id_session" ;
			public $Libelle = "ID Session" ;
			public function ObtientValeur()
			{
				return session_id() ;
			}
		}
		class AttributTypeEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "type" ;
			public $Libelle = "Type" ;
			public function ObtientValeur()
			{
				return $this->EntreeTraceParent->Type ;
			}
		}
		class AttributLigneEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "ligne" ;
			public $Libelle = "Numero Ligne" ;
			public function ObtientValeur()
			{
				return $this->EntreeTraceParent->Ligne ;
			}
		}
		class AttributCheminFichierEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "chemin_fichier" ;
			public $Libelle = "Chemin fichier" ;
			public function ObtientValeur()
			{
				return $this->EntreeTraceParent->CheminFichier ;
			}
		}
		class AttributContenuEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "contenu" ;
			public $Libelle = "Contenu" ;
			public function ObtientValeur()
			{
				$valeur = "" ;
				if($this->EntreeTraceParent != null)
				{
					$valeur = $this->EntreeTraceParent->Contenu ;
				}
				return $valeur ;
			}
		}
		class AttributDateCreationEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "date_creation" ;
			public $Libelle = "Date creation" ;
			public function ObtientValeur()
			{
				return date('Y-m-d H:i:s') ;
			}
		}
		class AttributCheminScriptEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "chemin_script" ;
			public $Libelle = "Chemin Script" ;
			public function ObtientValeur()
			{
				$valeur = "" ;
				if(php_sapi_name() == "cli")
				{
					$valeur = $_SERVER["argv"][0] ;
				}
				elseif(isset($_SERVER["SCRIPT_FILENAME"]))
				{
					$valeur = $_SERVER["SCRIPT_FILENAME"] ;
				}
				else
				{
					$this->Erreur = "Le chemin du script n'a pas ete renseigne dans les variables \$_SERVER." ;
				}
				return $valeur ;
			}
		}
		class AttributNomHoteServeurEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "nom_hote_serveur" ;
			public $Libelle = "Nom d'Hote Serveur" ;
			public $UtiliserCache = 1 ;
			public function ObtientValeur()
			{
				return php_uname("n") ;
			}
		}
		class AttributAdrIPServeurEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "adresse_ip_serveur" ;
			public $Libelle = "Adresse IP Serveur" ;
			public $UtiliserCache = 1 ;
			public function ObtientValeur()
			{
				return gethostbyname(php_uname("n")) ;
			}
		}
		class AttributOSClientEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "os_client" ;
			public $Libelle = "OS Client" ;
			public $UtiliserCache = 1 ;
			protected $ResultatUserAgent = null ;
			protected function InitResultatUserAgent()
			{
				$parseur = new UserAgentParser($_SERVER["HTTP_USER_AGENT"]) ;
				$this->ResultatUserAgent = $parseur->parse() ;
			}
			public function ObtientValeur()
			{
				$this->InitResultatUserAgent() ;
				return $this->ResultatUserAgent->getPlatform() ;
			}
		}
		class AttributNomNavigateurEntreeTrace extends AttributOSClientEntreeTrace
		{
			public $Nom = "nom_navigateur" ;
			public $Libelle = "Nom du navigateur" ;
			public function ObtientValeur()
			{
				$this->InitResultatUserAgent() ;
				return $this->ResultatUserAgent->getBrowser() ;
			}
		}
		class AttributUserAgentEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "user_agent" ;
			public $Libelle = "UserAgent" ;
			public $UtiliserCache = 1 ;
			public function ObtientValeur()
			{
				$valeur = "" ;
				if(isset($_SERVER['HTTP_USER_AGENT']))
				{
					$valeur = $_SERVER['HTTP_USER_AGENT'] ;
				}
				else
				{
					$this->Erreur = "La variable \$_SERVER[HTTP_USER_AGENT] n'est pas disponible" ;
				}
				return $valeur ;
			}
		}
		class AttributAddrIPRemoteEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "adresse_ip_serveur" ;
			public $Libelle = "Adresse IP Serveur" ;
			public function ObtientValeur()
			{
				if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
				{
					$ip=$_SERVER['HTTP_CLIENT_IP'];
				}
				elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
				{
					$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				else
				{
					$ip=$_SERVER['REMOTE_ADDR'];
				}
				if(! empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				{
					return 'http://proxyfound' ;
				}
				return $ip;
			}
		}
		class AttributPortClientEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "port_client" ;
			public $Libelle = "Port client" ;
			public function ObtientValeur()
			{
				$valeur = "" ;
				if(isset($_SERVER["REMOTE_PORT"]))
					$valeur = $_SERVER["REMOTE_PORT"] ;
				else
					$this->Erreur = "La variable \$_SERVER[REMOTE_PORT] n'est pas renseignee" ;
				return $valeur ;
			}
		}
		class AttributOSServeurEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "os_server" ;
			public $Libelle = "OS Serveur" ;
			public $UtiliserCache = 1 ;
			protected $ResultatUserAgent = null ;
			public function ObtientValeur()
			{
				return php_uname("s")." ".php_uname("v") ;
			}
		}
		class AttributHttpFilesEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "http_files" ;
			public $Libelle = "Donnees Http FILES" ;
			public function ObtientValeur()
			{
				return var_export($_FILES, true) ;
			}
		}
		class AttributHttpGetEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "http_get" ;
			public $Libelle = "Donnees Http GET" ;
			public $FormatQueryString = 1 ;
			public function ObtientValeur()
			{
				$valeur = ($this->FormatQueryString) ? http_build_query_string($_GET) : var_export($_GET, true) ;
				return $valeur ;
			}
		}
		class AttributHttpPostEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "http_post" ;
			public $Libelle = "Donnees Http POST" ;
			public $FormatQueryString = 1 ;
			public function ObtientValeur()
			{
				$valeur = ($this->FormatQueryString) ? http_build_query_string($_POST) : var_export($_POST, true) ;
				return $valeur ;
			}
		}
		class AttibutConsoleArgvEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "console_argv" ;
			public $Libelle = "Donnees console argv" ;
			public function ObtientValeur()
			{
				if(! isset($_SERVER["argv"]))
				{
					$this->Erreur = "La variable \$_SERVER[argv] n'est pas renseignee." ;
					return "" ;
				}
				$argv = $_SERVER["argv"] ;
				array_splice($argv, 0, 1) ;
				return var_export($argv) ;
			}
		}
		
		class EntreeTraceBase
		{
			public $Type = "INFO" ;
			public $Valeurs = array() ;
			public $Contenu = "" ;
			public $JournalParent = null ;
			public $Contexte = null ;
			public $CheminFichier = "" ;
			public $Ligne = 0 ;
			public $Attributs = array() ;
			public function __construct($type="", $contenu="", $cheminFichier="", $ligne=0)
			{
				if($type != "")
					$this->Type = $type ;
				if($contenu != "")
					$this->Contenu = $contenu ;
				if($cheminFichier != "")
					$this->CheminFichier = $cheminFichier ;
				if($ligne != "")
					$this->Ligne = $ligne ;
			}
			public function Lie($attributs)
			{
				$this->Valeurs = array() ;
				$this->Attributs = $attributs ;
				foreach($attributs as $i => $attribut)
				{
					$attribut->EntreeTraceParent = $this ;
					$attribut->Lie() ;
					$this->Valeurs[$i] = $attribut->Valeur ;
				}
			}
			public function ChargeAttributs()
			{
			}
		}
		
		class FichierTracesBase
		{
			public $TypesAcceptes = array() ;
			public function AccepteEntree($entree)
			{
				$ok = 0 ;
				if(! count($this->TypesAcceptes))
				{
					$ok = 1 ;
				}
				else
				{
					if(in_array($entree->Type, $this->TypesAcceptes) || in_array('*', $this->TypesAcceptes))
					{
						$ok = 1 ;
					}
				}
				return $ok ;
			}
			public function SauveEntree($entree)
			{
				if(! $this->AccepteEntree($entree))
				{
					return ;
				}
				$this->ExecSauveEntree($entree) ;
			}
			protected function ExecSauveEntree($entree)
			{
			}
		}
		class FichierTexteTraces extends FichierTracesBase
		{
			public $ExtensionFichier = "log" ;
			public $NomDossierRacine = "traces" ;
			public $CheminDossierRacine = "" ;
			public $TailleMaxFichier = 5242880 ;
			public $TailleFichierSupport = 0 ;
			protected $CheminDossierSupport = "" ;
			protected $CheminFichierSupport = "" ;
			public $SeparateurAttributs = "\t" ;
			protected function AlertePbEcritureFichierSupport()
			{
				die("Impossible de creer un fichier dans le repertoire racine ".$this->CheminDossierRacine) ;
			}
			protected function ExecSauveEntree($entree)
			{
				$this->DetermineCheminFichierSupport() ;
				if(! is_writable($this->CheminDossierRacine))
				{
					$this->AlertePbEcritureFichierSupport() ;
					return ;
				}
				if(! is_dir($this->CheminDossierRacine."/".$this->NomDossierRacine))
				{
					mkdir($this->CheminDossierRacine."/".$this->NomDossierRacine, 0777, true) ;
				}
				if(! is_dir($this->CheminDossierSupport))
				{
					@mkdir($this->CheminDossierSupport, 0777, true) ;
				}
				if(! is_writable($this->CheminDossierSupport) && ! is_writable($this->CheminFichierSupport))
				{
					$this->AlertePbEcritureFichierSupport() ;
					return ;
				}
				$fr = fopen($this->CheminFichierSupport, "a") ;
				if($this->TailleFichierSupport == 0)
				{
					$this->InscritEnteteFichierSupport($fr, $entree) ;
				}
				$contenu = $this->EncodeEntree($entree)."\r\n" ;
				fputs($fr, $contenu) ;
				if(strlen($contenu) + $this->TailleFichierSupport > $this->TailleMaxFichier)
				{
					$this->InscritPiedFichierSupport($fr) ;
				}
				fclose($fr) ;
			}
			protected function InscritEnteteFichierSupport(& $fr, $entree)
			{
			}
			protected function InscritPiedFichierSupport(& $fr)
			{
			}
			protected function EncodeEntree($entree)
			{
				$ctn = join($this->SeparateurAttributs, $entree->Valeurs) ;
				return $ctn ;
			}
			protected function DetermineCheminFichierSupport()
			{
				$this->CheminFichierSupport = "" ;
				$this->CheminDossierSupport = "" ;
				$this->TailleFichierSupport = 0 ;
				if($this->CheminDossierRacine == "")
				{
					$this->CheminDossierRacine = dirname(__FILE__)."/../.." ;
				}
				$this->CheminDossierSupport = $this->CheminDossierRacine."/".$this->NomDossierRacine."/".date("Y-m-d") ;
				if(is_dir($this->CheminDossierSupport))
				{
					$dr = opendir($this->CheminDossierSupport) ;
					while(($nomFichier = readdir($dr)) !== false)
					{
						if($nomFichier == "." || $nomFichier == "..")
						{
							continue ;
						}
						$cheminFichier = $this->CheminDossierSupport."/".$nomFichier ;
						$attrFichier = pathinfo($cheminFichier) ;
						if(! isset($attrFichier["extension"]) || $attrFichier["extension"] != $this->ExtensionFichier)
						{
							continue ;
						}
						if($this->TailleMaxFichier > 0)
						{
							$taille = filesize($cheminFichier) ;
							if($this->TailleMaxFichier > $taille)
							{
								$this->CheminFichierSupport = $cheminFichier ;
								$this->TailleFichierSupport = $taille ;
								break ;
							}
						}
						else
						{
							$this->CheminFichierSupport = $cheminFichier ;
							break ;
						}
					}
					closedir($dr) ;
				}
				if($this->CheminFichierSupport == "")
				{
					$this->CheminFichierSupport = $this->CheminDossierSupport."/".date('H-i-s').".".$this->ExtensionFichier ;
				}
				//echo "chemin : ".$this->CheminFichierSupport ;
			}
		}
		class FichierExcelTraces extends FichierTexteTraces
		{
			public $ExtensionFichier = "xls" ;
			protected function InscritEnteteFichierSupport(& $fr, $entree)
			{
				$ctn = '<!doctype html>
<html>
<head>
<title>Traces</title>
</head>
<body>
<table width="100%" cellspacing="0" cellpadding="4">
<tr>'.PHP_EOL ;
				foreach($entree->Attributs as $i => $attribut)
				{
					$ctn .= '<th>'.htmlentities($attribut->Libelle).'</th>'.PHP_EOL ;
				}
				$ctn .= '</tr>'.PHP_EOL ;
				fputs($fr, $ctn) ;
			}
			protected function InscritPiedFichierSupport(& $fr)
			{
				$ctn = '</table>
</body>
</html>' ;
				fputs($fr, $ctn) ;
			}
			protected function EncodeEntree($entree)
			{
				$ctn = '<tr>'.PHP_EOL ;
				foreach($entree->Valeurs as $i => $valeur)
				{
					$ctn .= '<td>'.htmlentities($valeur).'</td>'.PHP_EOL ;
				}
				$ctn .= '</tr>'.PHP_EOL ;
				return $ctn ;
			}
		}
		
		class FichierAfficheTraces extends FichierTracesBase
		{
			protected function ExecSauveEntree($entree)
			{
				$dump = print_r($entree->Valeurs, true) ;
				if(php_sapi_name() == 'cli')
				{
					print "\n" ;
				}
				else
				{
					print '<pre>'.$dump.'</pre>'."\n" ;
				}
			}
		}
		class FichierBasesDonneesTraces extends FichierTracesBase
		{
			public $BaseDonnees = null ;
			protected function ExecSauveEntree($entree)
			{
				if($this->BaseDonnees == null)
				{
					return ;
				}
			}
		}
		class FichierMessageEmailTraces extends FichierTracesBase
		{
			public $EmailsDestinataires = array() ;
			public $EmailDe = "" ;
			protected function ObtientMessage($entree)
			{
				$msg = '' ;
				foreach($entree->Valeurs as $n => $valeur)
				{
					$msg .= $n.' : '.$valeur ;
				}
				return $msg ;
			}
		}
	}

?>