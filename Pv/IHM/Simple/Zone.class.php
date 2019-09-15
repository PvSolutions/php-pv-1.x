<?php
	
	if(! defined('PV_ZONE_SIMPLE_IHM'))
	{
		if(! defined('PV_ZONE_IHM'))
		{
			include dirname(__FILE__)."/../Zone.class.php" ;
		}
		if(! defined('PV_SCRIPT_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		if(! defined('PV_HABILLAGE_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/Habillage.class.php" ;
		}
		define('PV_ZONE_SIMPLE_IHM', 1) ;
		
		class PvMessageExecutionZoneWeb
		{
			public $NomScriptSource ;
			public $Statut ;
			public $Contenu ;
			public function EstVide()
			{
				return $this->Contenu == "" ;
			}
			public function NonRenseigne()
			{
				return $this->Statut === null ;
			}
			public function Succes()
			{
				return $this->Statut == 1 ;
			}
		}
		
		class PvDocumentWebBase
		{
			protected function RenduDefsJS(& $zone)
			{
				$ctn = '' ;
				for($i=0; $i<count($zone->ContenusJs); $i++)
				{
					$ctnJs = $zone->ContenusJs[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduDefsCSS(& $zone)
			{
				$ctn = '' ;
				for($i=0; $i<count($zone->ContenusCSS); $i++)
				{
					$ctnCSS = $zone->ContenusCSS[$i] ;
					$ctn .= $ctnCSS->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			public function PrepareRendu(& $zone)
			{
			}
			public function RenduEntete(& $zone)
			{
				$ctn = '' ;
				$ctn .= $zone->RenduCtnJsEntete() ;
				return ;
			}
			public function RenduPied(& $zone)
			{
			}
		}
		class PvDocumentHtmlSimple extends PvDocumentWebBase
		{
			public $AttrsBody = array() ;
			public function RenduEntete(& $zone)
			{
				$ctn = '' ;
				$ctn .= '<!doctype html>'.PHP_EOL ;
				$ctn .= '<html lang="'.$zone->LangueDocument.'">'.PHP_EOL ;
				$ctn .= '<head>'.PHP_EOL ;
				$ctn .= $zone->RenduLienFavicon() ;
				$ctn .= $zone->RenduMetasDocument() ;
				$ctn .= '<title>'.$zone->ObtientTitreDocument().'</title>'.PHP_EOL ;
				if($zone->InclureCtnJsEntete)
				{
					$ctn .= $this->RenduDefsJS($zone) ;
				}
				$ctn .= $this->RenduDefsCSS($zone) ;
				$ctn .= $zone->RenduExtraHead ;
				$ctn .= '</head>'.PHP_EOL ;
				$ctnAttrsBody = '' ;
				foreach($this->AttrsBody as $n => $v)
				{
					$ctnAttrsBody .= ' '.$n.'="'.htmlspecialchars(html_entity_decode($v)).'"' ;
				}
				$ctn .= '<body'.$ctnAttrsBody.'>' ;
				return $ctn ;
			}
			public function RenduPied(& $zone)
			{
				$ctn = '' ;
				if($zone->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduDefsJS($zone).PHP_EOL ;
				}
				$ctn .= $zone->RenduCtnJsPied($zone) ;
				$ctn .= '</body>'.PHP_EOL ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
		}
		class PvDocumentWebHtml extends PvDocumentHtmlSimple
		{
		}
		class PvDocBoiteDialogueWeb extends PvDocumentWebBase
		{
			public function RenduEntete(& $zone)
			{
			}
			public function RenduPied(& $zone)
			{
			}
		}
		
		class PvGestTachesWebSimple extends PvObjet
		{
			public $NomDossierTaches = "taches" ;
			protected $Taches = array() ;
			public $ZoneParent = null ;
			public $NomElementZone = "" ;
			public function AdopteZone($nom, & $zone)
			{
				$this->ZoneParent = & $zone ;
				$this->NomElementZone = $nom ;
			}
			public function ObtientCheminDossierTaches()
			{
				return dirname($this->ZoneParent->ObtientCheminFichierRelatif()).DIRECTORY_SEPARATOR.$this->NomDossierTaches ;
			}
			public function & ObtientTaches()
			{
				return $this->Taches ;
			}
			public function InsereTache($nom, $tache)
			{
				$this->InscritTache($nom, $tache) ;
				return $tache ;
			}
			public function InscritTache($nom, & $tache)
			{
				$this->Taches[$nom] = & $tache ;
				$tache->AdopteGest($nom, $this) ;
			}
			public function Execute()
			{
				$taches = $this->ObtientTaches() ;
				foreach($taches as $i => & $tache)
				{
					if($tache->EstPret())
					{
						$this->LanceTache($tache->NomElementGest) ;
					}
				}
			}
			public function LanceTache(& $nomTache, $params=array())
			{
				if(! isset($this->Taches[$nomTache]))
				{
					return false ;
				}
				$tache = & $this->Taches[$nomTache] ;
				$urlZone = $this->ZoneParent->ObtientUrl() ;
				$parts = parse_url($urlZone) ;
				$port = ($parts["port"] != '') ? $parts["port"] : 80 ;
				$chaineParams = http_build_query($params) ;
				if($chaineParams != "")
				{
					$chaineParams = "&".$chaineParams ;
				}
				$fh = fsockopen($parts["host"], $port, $errno, $errstr, 30);
				if ($fh)
				{
					$ctn = "GET ".$parts["path"]."?".urlencode($this->ZoneParent->NomParamTacheAppelee)."=".urlencode($tache->NomElementGest).$chaineParams." HTTP/1.0\r\n";
					$ctn .= "Host: ".$parts["host"].":".$port."\r\n" ;
					$ctn .= "Content-Type: text/html\r\n" ;
					$ctn .= "Connection: Close\r\n\r\n" ;
					fputs($fh, $ctn) ;
					fclose($fh) ;
				}
			}
		}
		class PvTacheWebBaseSimple extends PvObjet
		{
			public $NomElementGest ;
			public $GestParent ;
			protected $Etat ;
			public $DelaiExecution = 1 ; // En heures
			protected $TerminerExecution = 0 ;
			public function InitConfig()
			{
				parent::InitConfig() ;
				$this->Etat = new PvEtatServPersist() ;
			}
			public function ObtientEtat()
			{
				return $this->Etat ;
			}
			public function ObtientCheminFichier()
			{
				return $this->GestParent->ObtientCheminDossierTaches()."/".$this->NomElementGest.".dat" ;
			}
			protected function ObtientCtnBrutEtat()
			{
				$cheminFichier = $this->ObtientCheminFichier() ;
				if(! file_exists($cheminFichier))
					return "" ;
				$fh = fopen($cheminFichier, "r") ;
				$ctn = '' ;
				if($fh !== false)
				{
					while(! feof($fh))
					{
						$ctn .= fgets($fh, 256) ;
					}
					fclose($fh) ;
				}
				else
				{
					return false ;
				}
				return $ctn ;
			}
			protected function SauveEtat()
			{
				$fh = fopen($this->ObtientCheminFichier(), "w") ;
				if($fh != false)
				{
					fputs($fh, serialize($this->Etat)) ;
					fclose($fh) ;
				}
				else
				{
					return 0 ;
				}
				return 1 ;
			}
			public function InitEtat()
			{
				$this->Etat->PID = getmypid() ;
				$this->Etat->TimestmpDebutSession = date("U") ;
				$this->Etat->Statut = PvEtatServPersist::ETAT_DEMARRE ;
				$this->Etat->TimestmpCapt = date("U") ;
				return $this->SauveEtat() ;
			}
			public function ActualiseEtat()
			{
				$this->Etat->TimestmpCapt = date("U") ;
				return $this->SauveEtat() ;
			}
			protected function ChargeEtat()
			{
				$ctn = $this->ObtientCtnBrutEtat() ;
				if($ctn === false)
				{
					return 0 ;
				}
				if($ctn != '')
				{
					$this->Etat = unserialize($ctn) ;
				}
				return 1 ;
			}
			public function AdopteGest($nom, & $gest)
			{
				$this->NomElementGest = $nom ;
				$this->GestParent = & $gest ;
			}
			public function & ZoneParent()
			{
				return $this->GestParent->ZoneParent ;
			}
			public function EstPret()
			{
				if(! $this->Etat->EstDefini())
				{
					$this->ChargeEtat() ;
				}
				if($this->Etat->Statut == PvEtatServPersist::ETAT_DEMARRE)
				{
					return 1 ;
				}
				$timestampAtteint = $this->Etat->TimestmpFinSession + ($this->DelaiExecution * 3600) ;
				$ok = 0 ;
				if(($this->Etat->Statut == PvEtatServPersist::ETAT_STOPPE || $this->Etat->Statut == PvEtatServPersist::ETAT_NON_DEFINI) && date("U") >= $timestampAtteint)
				{
					$ok = 1 ;
				}
				return $ok ;
			}
			public function Demarre()
			{
				if(! $this->EstPret())
				{
					return ;
				}
				$ok = $this->InitEtat() ;
				if(! $ok)
				{
					return ;
				}
				$this->TerminerExecution = 1 ;
				$this->ExecuteInstructions() ;
				if($this->TerminerExecution)
				{
					$this->TermineExecution() ;
				}
				exit ;
			}
			public function TermineExecution()
			{
				$this->Etat->PID = 0 ;
				$this->Etat->Statut = PvEtatServPersist::ETAT_STOPPE ;
				$this->Etat->TimestmpCapt = date("U") ;
				$this->Etat->TimestmpFinSession = date("U") ;
				$this->SauveEtat() ;
			}
			public function Arrete()
			{
				$processMgr = OsProcessManager::Current() ;
				if($this->Etat->PID == 0)
				{
					return ;
				}
				$processMgr->KillProcessList(array($this->Etat->PID)) ;
			}
			public function Appelle($params=array())
			{
				return $this->GestParent->LanceTache($this->NomElementGest, $params) ;
			}
			protected function ExecuteInstructions()
			{
			}
		}
		class PvTacheWebCtrlTachesProgs extends PvTacheWebBaseSimple
		{
			public $DelaiTransition = 0 ;
			public $Message = "Verification des taches programmees terminee" ;
			protected function ExecuteInstructions()
			{
				$nomTaches = array_keys($this->ApplicationParent->TachesProgs) ;
				foreach($nomTaches as $i => $nomTache)
				{
					$tacheProg = & $this->ApplicationParent->TachesProgs[$nomTache] ;
					$tacheProg->LanceProcessus() ;
					if($this->DelaiTransition > 0)
					{
						sleep($this->DelaiTransition) ;
					}
				}
				echo $this->Message."\n" ;
			}
		}
		class PvTacheWebCtrlServsPersists extends PvTacheWebBaseSimple
		{
			public $DelaiTransition = 0 ;
			public $Message = "Verification des services persistants terminee" ;
			protected function ExecuteInstructions()
			{
				$zone = $this->ZoneParent() ;
				$nomServsPersists = array_keys($zone->ApplicationParent->ServsPersists) ;
				foreach($nomServsPersists as $i => $nomServPersist)
				{
					$servPersist = & $this->ApplicationParent->ServsPersists[$nomServPersist] ;
					if(! $servPersist->EstServiceDemarre() || ! $servPersist->Verifie())
					{
						$servPersist->DemarreService() ;
						if($this->DelaiTransition > 0)
						{
							sleep($this->DelaiTransition) ;
						}
					}
				}
				echo $this->Message."\n" ;
			}
		}
		class PvTacheWebCtrlTransacts extends PvTacheWebBaseSimple
		{
			public $DelaiExecution = 0.25 ;
			public $Message = "Verification des transactions en attente terminee" ;
			protected function ExecuteInstructions()
			{
				$zone = $this->ZoneParent() ;
				$interfsPaiement = $zone->ApplicationParent->InterfsPaiement() ;
				foreach($interfsPaiement as $i => $interfPaiement)
				{
					$interfPaiement->ControleTransactionsEnAttente() ;
				}
				echo $this->Message."\n" ;
			}
		}
		
		class PvAdrScriptSessionWeb
		{
			public $ChaineGet ;
			public $DonneesPost = array() ;
			public function Sauvegarde(& $zone)
			{
				$_SESSION[$zone->NomElementApplication."_AddrScriptSession"] = serialize($this) ;
			}
			public static function Restaure(& $zone)
			{
				if(isset($_SESSION[$zone->NomElementApplication."_AddrScriptSession"]))
				{
					return unserialize($_SESSION[$zone->NomElementApplication."_AddrScriptSession"]) ;
				}
				return new PvAdrScriptSessionWeb() ;
			}
			public function ImporteRequeteHttp(& $zone)
			{
				$this->ChaineGet = $_SERVER["REQUEST_URI"].$_REQUEST["QUERY_STRING"] ;
				$this->DonneesPost = $_POST ;
				$this->Sauvegarde($zone) ;
				// print_r($_SESSION) ;
			}
			public function ExporteZone(& $zone)
			{
				return PvAdrScriptSessionWeb::Restaure($zone) ;
			}
		}
		
		class PvZoneWebSimple extends PvZoneWeb
		{
			public $TagTitre = "div" ;
			public $TypeDocument ;
			public $HoteRecaptcha ;
			public $CleSiteRecaptcha ;
			public $CleSecreteRecaptcha ;
			public $AdrScriptSession ;
			public $DocumentsWeb = array() ;
			public $GestTachesWeb ;
			public $UtiliserDocumentWeb = 0 ;
			public $DocumentWebSelect = null ;
			public $DefinitionTypeDocument ;
			public $CheminFavicon ;
			public $LangueDocument = "fr" ;
			public $EncodageDocument = "iso-8859-1" ;
			public $TitreDocument ;
			public $MotsCleMeta ;
			public $DescriptionMeta ;
			public $ViewportMeta ;
			public $AuteurMeta ;
			public $UrlBase = "" ;
			public $ModeCache ;
			public $ScriptPourRendu ;
			public $InclureCtnJsEntete = 1 ;
			public $RenduExtraHead = '' ;
			public $InclureJQuery = 0 ;
			public $CheminJQuery = "js/jquery.min.js" ;
			public $InclureBootstrap = 0 ;
			public $CheminJsBootstrap = "js/bootstrap.min.js" ;
			public $CheminCSSBootstrap = "css/bootstrap.css" ;
			public $InclureBootstrapTheme = 0 ;
			public $CheminCSSBootstrapTheme = "css/bootstrap-theme.min.css" ;
			public $InclureFontAwesome = 0 ;
			public $CheminFontAwesome = "css/font-awesome.min.css" ;
			public $InclureJQueryMigrate = 1 ;
			public $CheminJQueryMigrate = "js/jquery-migrate.min.js" ;
			public $InclureJQueryMigrate3 = 0 ;
			public $CheminJQueryMigrate3 = "js/jquery-migrate3.min.js" ;
			public $InclureJQueryUi = 0 ;
			public $CheminJsJQueryUi = "js/jquery-ui.min.js" ;
			public $CheminCSSJQueryUi = "css/jquery-ui.css" ;
			public $InclureNormalize = 0 ;
			public $CheminNormalize = "css/normalize.css" ;
			public $ContenusCSS = array() ;
			public $ContenusJs = array() ;
			public $ContenusJsPied = array() ;
			public $CheminIconeScript = "" ;
			public $InclureRenduTitre = 1 ;
			public $InclureRenduIcone = 1 ;
			public $InclureRenduMessageExecution = 1 ;
			public $DetectIconeCorresp = 0 ;
			public $CheminDossierIconeCorresp = "images/icones" ;
			public $ExtIconeCorresp = "png" ;
			public $InclureRenduChemin = 1 ;
			public $InclureRenduDescription = 1 ;
			public $ActionsPrinc = array() ;
			public $ActionsAvantRendu = array() ;
			public $ActionsApresRendu = array() ;
			public $NomParamActionAppelee = "appelleAction" ;
			public $NomParamTacheAppelee = "appelleTache" ;
			public $ValeurParamActionAppelee = false ;
			public $ValeurParamTacheAppelee = false ;
			public $ActionsAppelees = array() ;
			public $AnnulerRendu = 0 ;
			public $RenduEnCours = 0 ;
			public $RedirigerVersConnexion = 0 ;
			public $Habillage = null ;
			public $ActiverRafraichScript = 1 ;
			public $LibelleEspaceReserveFiltres = 0 ;
			public $InclureScriptsMembership = 1 ;
			public $NomDocumentWebEditMembership = "" ;
			public $ModeRecouvrMP = "directe" ;
			public $NomClasseHabillage = "PvHabillageSimpleBase" ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPWeb" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionWeb" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionWeb" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionWeb" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseWeb" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseWeb" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreWeb" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreMSWeb" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreMSWeb" ;
			public $NomClasseScriptImportMembre = "PvScriptImportMembreMSWeb" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsWeb" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreMSWeb" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresMSWeb" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilMSWeb" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilMSWeb" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilMSWeb" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsMSWeb" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleMSWeb" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleMSWeb" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleMSWeb" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesMSWeb" ;
			public $NomClasseScriptAjoutServeurAD = "PvScriptAjoutServeurADWeb" ;
			public $NomClasseScriptModifServeurAD = "PvScriptModifServeurADWeb" ;
			public $NomClasseScriptSupprServeurAD = "PvScriptSupprServeurADWeb" ;
			public $NomClasseScriptListeServeursAD = "PvScriptListeServeursADWeb" ;
			protected $TacheAppelee ;
			protected $ScriptExecuteAccessible = false ;
			public $CleMessageExecutionSession = "PvMessageExecution" ;
			public $ClasseCSSMsgExecSucces = "Succes" ;
			public $ClasseCSSMsgExecErreur = "Erreur" ;
			public $InscrireActRedirectScriptSession = 1 ;
			public $InscrireTacheWebCtrlTransacts = 1 ;
			public $Metas = array() ;
			public $ActionRedirScriptSession ;
			public $NomDossierModelesEval ;
			public $UtiliserModelesEval = 0 ;
			public $UtiliserModelesEvalAuto = 1 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->GestTachesWeb = new PvGestTachesWebSimple() ;
				$this->GestTachesWeb->AdopteZone("gestTaches", $this) ;
				$this->AdrScriptSession = new PvAdrScriptSessionWeb() ;
			}
			public function Execute()
			{
				$this->DetecteActionAppelee() ;
				$this->ExecuteActionPrinc() ;
				$this->DemarreExecution() ;
				$this->DetecteScriptsMembership() ;
				$this->DetecteScriptAppele() ;
				$this->ExecuteScriptAppele() ;
				$this->TermineExecution() ;
			}
			protected function CreeTacheWebCtrlTransacts()
			{
				return new PvTacheWebCtrlTransacts() ;
			}
			public function RestaureMessageExecutionSession()
			{
				$msg = new PvMessageExecutionZoneWeb() ;
				if(isset($_SESSION[$this->CleMessageExecutionSession]))
				{
					$msg = unserialize($_SESSION[$this->CleMessageExecutionSession]) ;
					unset($_SESSION[$this->CleMessageExecutionSession]) ;
				}
				return $msg ;
			}
			public function SauveMessageExecutionSession($statut, $contenu, $nomScript='')
			{
				$msg = new PvMessageExecutionZoneWeb() ;
				$msg->Statut = $statut ;
				$msg->Contenu = $contenu ;
				$msg->NomScriptSource = $nomScript ;
				$_SESSION[$this->CleMessageExecutionSession] = serialize($msg) ;
			}
			public function DefinitMessageExecution($statut, $contenu, $nomScript='')
			{
				$this->SauveMessageExecutionSession($statut, $contenu, $nomScript) ;
			}
			protected function ExecuteActionPrinc()
			{
				if($this->ValeurParamActionAppelee !== false)
				{
					$this->ExecuteActionAppelee($this->ActionsPrinc) ;
				}
			}
			protected function ExecuteGestTachesWeb()
			{
				$this->DetecteTacheAppelee() ;
				if($this->EstNul($this->TacheAppelee))
				{
					$this->GestTachesWeb->Execute() ;
				}
				else
				{
					$this->TacheAppelee->Demarre() ;
				}
			}
			protected function DetecteTacheAppelee()
			{
				$this->TacheAppelee = null ;
				if(isset($_GET[$this->NomParamTacheAppelee]))
				{
					$this->ValeurParamTacheAppelee = $_GET[$this->NomParamTacheAppelee] ;
				}
				$taches = $this->GestTachesWeb->ObtientTaches() ;
				if(isset($taches[$this->ValeurParamTacheAppelee]))
				{
					$this->TacheAppelee = & $taches[$this->ValeurParamTacheAppelee] ;
				}
				else
				{
					$this->ValeurParamTacheAppelee = "" ;
				}
			}
			public function PossedeTacheAppelee()
			{
				return ($this->ValeurParamTacheAppelee != "") ? 1 : 0 ;
			}
			public function & InsereTacheWeb($nom, $tache)
			{
				$this->GestTachesWeb->InsereTache($nom, $tache) ;
				return $tache ;
			}
			public function InscritTacheWeb($nom, & $tache)
			{
				$this->GestTachesWeb->InscritTache($nom, $tache) ;
			}
			public function ObtientUrlTache($nomTache)
			{
				$taches = $this->GestTachesWeb->ObtientTaches() ;
				if(! isset($taches[$nomTache]))
				{
					return ;
				}
				return $this->ObtientUrl()."?".urlencode($this->NomParamTacheAppelee)."=".urlencode($nomTache) ;
			}
			public function ObtientUrlAction($nomAction)
			{
				return $this->ObtientUrlActionAvantRendu($nomAction) ;
			}
			public function ObtientUrlActionAvantRendu($nomAction)
			{
				return $this->ObtientUrlActionDansListe($nomAction, $this->ActionsAvantRendu) ;
			}
			public function ObtientUrlActionApresRendu($nomAction)
			{
				return $this->ObtientUrlActionDansListe($nomAction, $this->ActionsApresRendu) ;
			}
			protected function ObtientUrlActionDansListe($nomAction, & $actions)
			{
				$url = false ;
				if(isset($actions[$nomAction]))
				{
					$url = $actions[$nomAction]->ObtientUrl() ;
				}
				return $url ;
			}
			protected function ChargeScripts()
			{
				$this->ChargeActionsAvantRendu() ;
				$this->ChargeActionsApresRendu() ;
				parent::ChargeScripts() ;
			}
			protected function ChargeDocumentsWeb()
			{
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeGestTachesWeb() ;
				$this->ChargeDocumentsWeb() ;
				$this->ChargeActionsPrinc() ;
				if($this->InscrireActRedirectScriptSession == 1)
				{
					$this->ActionRedirScriptSession = $this->InsereActionAvantRendu("redirectScriptSession", new PvActionRedirScriptSession()) ;
				}
				if($this->InscrireTacheWebCtrlTransacts == 1)
				{
					$interfsPaiement = $this->ApplicationParent->InterfsPaiement() ;
					if(count($interfsPaiement) > 0)
					{
						$this->InsereTacheWeb('ctrlTransacts', $this->CreeTacheWebCtrlTransacts()) ;
					}
				}
				if(class_exists($this->NomClasseHabillage))
				{
					$nomClasse = $this->NomClasseHabillage ;
					$this->Habillage = new $nomClasse() ;
				}
			}
			protected function ChargeActionsPrinc()
			{
			}
			protected function ChargeGestTachesWeb()
			{
			}
			public function & ObtientActionsAvantRendu()
			{
				$actions = $this->ActionsAvantRendu ;
				return $actions ;
			}
			public function & ObtientActionsApresRendu()
			{
				$actions = $this->ActionsApresRendu ;
				return $actions ;
			}
			protected function ChargeActionsAvantRendu()
			{
			}
			protected function ChargeActionsApresRendu()
			{
			}
			protected function DetecteActionAppelee()
			{
				$this->ValeurParamActionAppelee = false ;
				if(isset($_GET[$this->NomParamActionAppelee]))
					$this->ValeurParamActionAppelee = $_GET[$this->NomParamActionAppelee] ;
			}
			protected function ExecuteActionAppelee(& $actions)
			{
				return $this->ExecuteAction($actions, $this->ValeurParamActionAppelee) ;
			}
			protected function ExecuteAction(& $actions, $valeurAction)
			{
				$nomActions = array_keys($actions) ;
				foreach($nomActions as $i => $nomAction)
				{
					$action = & $actions[$nomAction] ;
					if($action->Accepte($valeurAction))
					{
						if(! $action->EstAccessible())
						{
							$this->AfficheRenduInacessible() ;
						}
						$this->ActionsAppelees[] = & $action ;
						$action->Execute() ;
					}
				}
			}
			public function ActionAccessible($nomAction)
			{
				$actions = $this->ObtientActionsAvantRendu() ;
				if(! isset($actions[$nomAction]))
				{
					return 0 ;
				}
				return $actions[$nomAction]->EstAccessible() ;
			}
			public function InvoqueAction($valeurAction, $params=array(), $valeurPost=array(), $async=1)
			{
				$nomActions = array_keys($this->ActionsAvantRendu) ;
				foreach($nomActions as $i => $nomAction)
				{
					$action = & $this->ActionsAvantRendu[$nomAction] ;
					if($action->Accepte($valeurAction))
					{
						$action->Invoque($params, $valeurPost, $async) ;
					}
				}
			}
			protected function & CreeAction($nomClasseAction)
			{
				if(! class_exists($nomClasseAction))
				{
					die("La classe $nomClasseAction n'existe pas !!!") ;
				}
				$action = new $nomClasseAction() ;
				return $action ;
			}
			public function PossedeActionAppelee()
			{
				return ($this->ValeurParamActionAppelee != "") ? 1 : 0 ;
			}
			public function & InsereActionPrinc($nomAction, $action)
			{
				$this->InscritActionPrinc($nomAction, $action) ;
				return $action ;
			}
			public function & InsereActionAvantRendu($nomAction, $action)
			{
				$this->InscritActionAvantRendu($nomAction, $action) ;
				return $action ;
			}
			public function & InsereActionApresRendu($nomAction, $action)
			{
				$this->InscritActionApresRendu($nomAction, $action) ;
				return $action ;
			}
			public function InscritActionPrinc($nomAction, & $action)
			{
				$this->ActionsPrinc[$nomAction] = & $action ;
				$action->AdopteZone($nomAction, $this) ;
			}
			public function InscritActionAvantRendu($nomAction, & $action)
			{
				$this->ActionsAvantRendu[$nomAction] = & $action ;
				$action->AdopteZone($nomAction, $this) ;
			}
			public function InscritActionApresRendu($nomAction, & $action)
			{
				$this->ActionsApresRendu[$nomAction] = & $action ;
				$action->AdopteZone($nomAction, $this) ;
			}
			public function CreeScript($nomClasse, $titre='')
			{
				if(! class_exists($nomClasse))
					return new PvNul() ;
				$script = new $nomClasse() ;
				if($titre == '')
				{
					$titre = ucfirst($nomClasse) ;
				}
				$script->Titre = $titre ;
				$script->TitreDocument = $titre ;
				return $script ;
			}
			protected function ExecuteScriptInaccessible(& $script)
			{
				if($this->RedirigerVersConnexion == 1)
				{
					if($this->InclureScriptsMembership == 1 && ! $this->PossedeMembreConnecte() && $this->ValeurParamScriptAppele != $this->NomScriptConnexion)
					{
						$params = array() ;
						if($this->ScriptConnexion->AutoriserUrlsRetour == 1)
						{
							$params[$this->ScriptConnexion->NomParamUrlRetour] = get_current_url() ;
						}
						redirect_to($this->ScriptConnexion->ObtientUrlParam($params)) ;
					}
				}
				parent::ExecuteScriptInaccessible($script) ;
			}
			protected function ChargeScriptsMSConnecte()
			{
				parent::ChargeScriptsMSConnecte() ;
				foreach($this->NomScriptsEditMembership as $i => $nomScript)
				{
					$this->Scripts[$nomScript]->NomDocumentWeb = $this->NomDocumentWebEditMembership ;
				}
			}
			protected function DetecteDocumentWebSelect()
			{
				$nomDocWeb = $this->ScriptPourRendu->NomDocumentWeb ;
				if($nomDocWeb == '' || ! isset($this->DocumentsWeb[$nomDocWeb]))
				{
					$nomDocsWeb = array_keys($this->DocumentsWeb) ;
					$nomDocWeb = $nomDocsWeb[0] ;
				}
				$this->DocumentWebSelect = $this->DocumentsWeb[$nomDocWeb] ;
			}
			public function RenduDocumentWebActive()
			{
				return ($this->UtiliserDocumentWeb && count($this->DocumentsWeb) > 0) ;
			}
			public function RenduMetasDocument()
			{
				$ctn = '' ;
				if($this->EncodageDocument != '')
					$ctn .= '<meta charset="'.$this->EncodageDocument.'" />'.PHP_EOL ;
				$viewport = $this->ObtientViewportMetaDocument() ;
				if($viewport != '')
				{
					$ctn .= '<meta name="viewport" content="'.htmlspecialchars(html_entity_decode($viewport)).'">'.PHP_EOL ;
				}
				$auteur = $this->ObtientAuteurMetaDocument() ;
				if($auteur != '')
				{
					$ctn .= '<meta name="author" content="'.htmlspecialchars(html_entity_decode($auteur)).'">'.PHP_EOL ;
				}
				$ctn .= '<meta name="description" content="'.htmlspecialchars(html_entity_decode($this->ObtientDescMetaDocument())).'">'.PHP_EOL ;
				$ctn .= '<meta name="keywords" content="'.htmlspecialchars(html_entity_decode($this->ObtientMotsCleMetaDocument())).'">'.PHP_EOL ;
				foreach($this->Metas as $nom => $contenu)
				{
					$ctn .= '<meta name="'.$nom.'" content="'.htmlspecialchars(html_entity_decode($contenu)).'">'.PHP_EOL ;
				}
				return $ctn ;
			}
			public function RenduDocument()
			{
				$ctn = '' ;
				if($this->RenduDocumentWebActive())
				{
					$this->DetecteDocumentWebSelect() ;
					$this->InclutLibrairiesExternes() ;
					$this->DocumentWebSelect->PrepareRendu($this) ;
					$ctn .= $this->DocumentWebSelect->RenduEntete($this) ;
					$ctn .= $this->RenduContenuCorpsDocument() ;
					$ctn .= $this->DocumentWebSelect->RenduPied($this) ;
				}
				else
				{
					$ctn .= $this->RenduDefinitionTypeDocument().PHP_EOL ;
					$ctn .= '<html lang="'.$this->LangueDocument.'">'.PHP_EOL ;
					$ctn .= $this->RenduEnteteDocument().PHP_EOL ;
					if($this->ScriptPourRendu->UtiliserCorpsDocZone)
					{
						$ctn .= $this->RenduCorpsDocument().PHP_EOL ;
					}
					else
					{
						$ctn .= $this->RenduDebutCorpsDocument().PHP_EOL ;
						$ctn .= $this->RenduContenuCorpsDocument().PHP_EOL ;
						$ctn .= $this->RenduFinCorpsDocument().PHP_EOL ;
					}
					$ctn .= $this->RenduPiedDocument().PHP_EOL ;
					$ctn .= '</html>' ;
				}
				$ctn .= $this->RenduAutoRafraich() ;
				$ctn .= $this->RenduBoiteImpression() ;
				return $ctn ;
			}
			public function RenduAutoRafraich()
			{
				$ctn = '' ;
				if($this->PourImpression == 1)
				{
					return '' ;
				}
				if($this->ActiverRafraichScript && ($this->ScriptPourRendu->DoitAutoRafraich()))
				{
					$ctn .= '<script type="text/javascript">
	var idAutoRafraich = 0 ;
	function execAutoRafraich() {
		window.location = '.json_encode($this->ScriptPourRendu->ObtientUrlParam($this->ScriptPourRendu->ParamsAutoRafraich)).' ;
	}
	function annulAutoRafraich() {
		clearTimeout(idAutoRafraich) ;
		idAutoRafraich = 0 ;
	}
	function demarreAutoRafraich() {
		idAutoRafraich = window.setTimeout("execAutoRafraich()", '.intval($this->ScriptPourRendu->DelaiAutoRafraich).' * 1000) ;
	}
	demarreAutoRafraich() ;
	if(window.onblur) {
		oldWindowBlur = window.onblur ;
		window.onblur = function() {
			if(oldWindowBlur)
			{
				oldWindowBlur() ;
			}
			annulAutoRafraich() ;
		}
	}
	if(window.onfocus) {
		oldWindowFocus = window.onfocus ;
		window.onfocus = function() {
			if(oldWindowFocus)
			{
				oldWindowFocus() ;
			}
			execAutoRafraich() ;
		}
	}
</script>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduBoiteImpression()
			{
				$ctn = '' ;
				if($this->PourImpression == 0)
				{
					return '' ;
				}
					$ctn .= '<script type="text/javascript">
	window.print() ;
</script>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduDebutCorpsDocument()
			{
				return '<body>' ;
			}
			protected function RenduFinCorpsDocument()
			{
				return '</body>' ;
			}
			protected function RenduDefinitionTypeDocument()
			{
				return '<!DOCTYPE html>' ;
			}
			public function InclutLibrairiesExternes()
			{
				if($this->InclureNormalize)
				{
					$this->InscritLienCSS($this->CheminNormalize) ;
				}
				if($this->InclureBootstrap)
				{
					$ctnJs = new PvLienFichierJs() ;
					$ctnJs->Src = $this->CheminJsBootstrap ;
					array_splice($this->ContenusJs, 0, 0, array($ctnJs)) ;
					$this->InscritLienCSS($this->CheminCSSBootstrap) ;
					if($this->InclureBootstrapTheme)
					{
						$this->InscritLienCSS($this->CheminCSSBootstrapTheme) ;
					}
				}
				if($this->InclureFontAwesome)
				{
					$this->InscritLienCSS($this->CheminFontAwesome) ;
				}
				if($this->InclureJQueryUi)
				{
					$ctnJs = new PvLienFichierJs() ;
					$ctnJs->Src = $this->CheminJsJQueryUi ;
					array_splice($this->ContenusJs, 0, 0, array($ctnJs)) ;
					$this->InscritLienCSS($this->CheminCSSJQueryUi) ;
				}
				if($this->InclureJQueryUi || $this->InclureJQuery)
				{
					$ctnJs = new PvLienFichierJs() ;
					$ctnJs->Src = $this->CheminJQuery ;
					$lstCtnJs = array($ctnJs) ;
					if($this->InclureJQueryMigrate)
					{
						$ctnJs = new PvLienFichierJs() ;
						$ctnJs->Src = $this->CheminJQueryMigrate ;
						$lstCtnJs[] = $ctnJs ;
						if($this->InclureJQueryMigrate3 == 1 && $this->CheminJQueryMigrate3 != '')
						{
							$this->InscritLienJs($this->CheminJQueryMigrate3) ;
						}
					}
					array_splice($this->ContenusJs, 0, 0, $lstCtnJs) ;
				}
			}
			public function ObtientTitreDocument()
			{
				$titreDocument = $this->ScriptPourRendu->ObtientTitreDocument() ;
				return (($titreDocument != "") ? $titreDocument : $this->TitreDocument) ;
			}
			public function ObtientMotsCleMetaDocument()
			{
				return ($this->ScriptPourRendu->MotsCleMeta != "") ? $this->ScriptPourRendu->MotsCleMeta : $this->MotsCleMeta ;
			}
			public function ObtientViewportMetaDocument()
			{
				return ($this->ScriptPourRendu->ViewportMeta != "") ? $this->ScriptPourRendu->ViewportMeta : $this->ViewportMeta ;
			}
			public function ObtientAuteurMetaDocument()
			{
				return ($this->ScriptPourRendu->AuteurMeta != "") ? $this->ScriptPourRendu->AuteurMeta : $this->AuteurMeta ;
			}
			public function ObtientDescMetaDocument()
			{
				return ($this->ScriptPourRendu->DescriptionMeta != "") ? $this->ScriptPourRendu->DescriptionMeta : $this->DescriptionMeta ;
			}
			public function RenduLienFavicon()
			{
				$ctn = '' ;
				if($this->CheminFavicon == '')
				{
					return '' ;
				}
				$infosFich = pathinfo($this->CheminFavicon) ;
				if($infosFich["extension"] != "ico")
				{
					$extMime = ($infosFich["extension"] == 'jpg') ? 'jpeg' : $infosFich["extension"] ;
					$ctn .= '<link rel="icon" type="image/'.$extMime.'" href="'.$this->CheminFavicon.'">' ;
				}
				else
				{
					$ctn .= '<link rel="icon" href="'.$this->CheminFavicon.'">' ;
				}
				$ctn .= PHP_EOL ;
				return $ctn ;
			}
			public function RenduEnteteDocument()
			{
				$this->InclutLibrairiesExternes() ;
				$ctn = '' ;
				$ctn .= '<head>'.PHP_EOL ;
				$ctn .= $this->RenduLienFavicon() ;
				$ctn .= $this->RenduMetasDocument() ;
				$ctn .= '<title>'.$this->ObtientTitreDocument().'</title>'.PHP_EOL ;
				for($i=0; $i<count($this->ContenusCSS); $i++)
				{
					$ctnCSS = $this->ContenusCSS[$i] ;
					$ctn .= $ctnCSS->RenduDispositif().PHP_EOL ;
				}
				if($this->InclureCtnJsEntete)
				{
					$ctn .= $this->RenduCtnJs() ;
				}
				$ctn .= $this->RenduExtraHead ;
				$ctn .= '</head>' ;
				return $ctn ;
			}
			public function UrlRedirScriptSession($urlDefaut='')
			{
				if($this->InscrireActRedirectScriptSession == 0)
				{
					return '?' ;
				}
				if($this->AdrScriptSession->ChaineGet == '' && $urlDefaut != '')
				{
					$partsUrl = explode('?', $urlDefaut, 2) ;
					if(count($partsUrl) == 2)
					{
						$this->AdrScriptSession->ChaineGet = '?'.$partsUrl[1] ;
						$this->AdrScriptSession->Sauvegarde($this) ;
					}
				}
				return $this->ActionRedirScriptSession->ObtientUrl() ;
			}
			protected function RenduCtnJs()
			{
				$ctn = '' ;
				// print_r($this->ContenusJs) ;
				for($i=0; $i<count($this->ContenusJs); $i++)
				{
					$ctnJs = $this->ContenusJs[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			public function RenduCtnJsPied()
			{
				$ctn = '' ;
				for($i=0; $i<count($this->ContenusJsPied); $i++)
				{
					$ctnJs = $this->ContenusJsPied[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			public function RenduPiedDocument()
			{
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '<body>' ;
				return $ctn ;
			}
			protected function RenduContenuCorpsDocument()
			{
				$this->ScriptPourRendu->PrepareRendu() ;
				return $this->ScriptPourRendu->RenduDispositif() ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				if($this->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduCtnJs() ;
				}
				$ctn .= $this->RenduCtnJsPied() ;
				$ctn .= '</body>' ;
				return $ctn ;
			}
			protected function RenduCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= $this->RenduEnteteCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduContenuCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduPiedCorpsDocument() ;
				return $ctn ;
			}
			public function DemarreTachesWeb()
			{
				$this->GestTachesWeb->Execute() ;
			}
			public function ExecuteScript(& $script)
			{
				$this->RapporteRequeteEnvoyee() ;
				$this->ExecuteGestTachesWeb() ;
				if($script->EstBienRefere() == 0)
				{
					$this->ExecuteScriptMalRefere($script) ;
					return ;
				}
				$this->VerifieValiditeMotPasse($script) ;
				if($script->EstAccessible() == 0)
				{
					// print_r(get_class($this->Membership->MemberLogged)) ;
					// print_r(get_class($script)) ;
					$this->ExecuteScriptInaccessible($script) ;
					return ;
				}
				$this->ChargeScriptSession() ;
				$this->DetermineEnvironnement($script) ;
				$this->ExecuteRequeteSoumise($script) ;
				// $script->PrepareRendu() ;
				$this->ScriptPourRendu = $script ;
				$this->RenduEnCours = 1 ;
				if($this->ValeurParamActionAppelee !== false)
				{
					$this->ExecuteActionAppelee($this->ActionsAvantRendu) ;
				}
				if($this->AnnulerRendu)
				{
					$this->RenduEnCours = 0 ;
					$this->ScriptPourRendu = null ;
					return ;
				}
				$ctn = $this->RenduDocument() ;
				/*
				if($this->ValeurParamActionAppelee !== false)
				{
					$this->ExecuteActionAppelee($this->ActionsApresRendu) ;
				}
				*/
				$this->RenduEnCours = 0 ;
				$this->ScriptPourRendu = null ;
				if(! $this->PossedeActionAppelee())
				{
					$this->FixeAdrScriptSession($script) ;
				}
				echo $ctn ;
			}
			protected function ChargeScriptSession()
			{
				$adr = $this->AdrScriptSession->ExporteZone($this) ;
				if($adr != null)
				{
					$this->AdrScriptSession = $adr ;
				}
			}
			protected function FixeAdrScriptSession(& $script)
			{
				if($script->EstScriptSession)
				{
					$this->AdrScriptSession->ImporteRequeteHttp($this) ;
				}
			}
			protected function ExecuteRequeteSoumise(& $script)
			{
			}
			// Incrire un fichier CSS
			public function InscritContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritContenuJs($contenu)
			{
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritContenuJsCmpIE($contenu, $versionMin=9)
			{
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJs($src)
			{
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJsCmpIE($src, $versionMin=9)
			{
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritContenuJsPied($contenu)
			{
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				$this->ContenusJsPied[] = $ctnJs ;
			}
			public function InscritContenuJsPiedCmpIE($contenu, $versionMin=9)
			{
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJsPied[] = $ctnJs ;
			}
			public function InscritLienJsPied($src)
			{
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				$this->ContenusJsPied[] = $ctnJs ;
			}
			public function InscritLienJsPiedCmpIE($src, $versionMin=9)
			{
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJsPied[] = $ctnJs ;
			}
			public function RenduLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuJsInclus($contenu)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduContenuJsCmpIEInclus($contenu, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsInclus($src)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsCmpIEInclus($src, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function TailleImageAjustee($cheminImage, $largeurMax, $hauteurMax)
			{
				return $GLOBALS["CommonGDManipulator"]->getAdjustedDimsFromFile($cheminImage, $largeurMax, $hauteurMax) ;
			}
			public function RenduImageAjustee($cheminImage, $largeurMax, $hauteurMax, $autresAttrsHtml='')
			{
				$dims = $GLOBALS["CommonGDManipulator"]->getAdjustedDimsFromFile($cheminImage, $largeurMax, $hauteurMax) ;
				return '<img src="'.htmlspecialchars($cheminImage).'" width="'.$largeurMax.'"'.(($autresAttrsHtml != '') ? ' '.$autresAttrsHtml : '').' />' ;
			}
			public function RenduRedirectScriptSession($urlDefaut = '')
			{
				$adr = & $this->AdrScriptSession ;
				$ctn = '' ;
				if($adr->ChaineGet != '')
				{
					$ctn .= '<!doctype html>
<html>
<head><title>Redirection en cours...</title></head>
<body>
<form style="display:none" id="FormRetour" action="'.htmlspecialchars($adr->ChaineGet).'" method="post">' ;
					foreach($adr->DonneesPost as $nom => $valeur)
					{
						if(is_array($valeur))
						{
							$valeur = join(",", $valeur) ;
						}
						$ctn .= '<input type="hidden" name="'.htmlspecialchars($nom).'" value="'.htmlspecialchars($valeur).'" />' ;
						
					}
					$ctn .= '<input type="submit" value="envoyer" /></form>
<script language="javascript">
	document.getElementById("FormRetour").submit() ;
</script>
</body>
</html>' ;
					echo $ctn ;
					exit ;
				}
				elseif($urlDefaut != '')
				{
					redirect_to($urlDefaut) ;
				}
			}
			public function ObtientCheminDossierTaches()
			{
				if($this->NomDossierModelesEval === null)
				{
					return null ;
				}
				return dirname($this->ObtientCheminFichierRelatif()).DIRECTORY_SEPARATOR.$this->NomDossierModelesEval ;
			}
			public function ModelesEvalActive()
			{
				return ($this->UtiliserModelesEval == 1 && $this->NomDossierModelesEval !== null) ;
			}
			public function RenduModeleEval($cheminModele)
			{
				ob_start() ;
				$zone = & $this ;
				if($this->EstPasNul($this->ScriptPourRendu))
				{
					$script = & $this->ScriptPourRendu ;
				}
				include $cheminModele ;
				$ctn = ob_get_clean() ;
				return $ctn ;
			}
		}
		
		class PvZoneConsoleSimple extends PvZoneConsole
		{
			public $ScriptPourRendu ;
			public $InsererSautLigneFinal = 1 ;
			public function RenduProgramme()
			{
				$ctn = '' ;
				$ctn .= $this->ScriptPourRendu->RenduDispositif() ;
				return $ctn ;
			}
			public function ExecuteScript(& $script)
			{
				$this->RapporteRequeteEnvoyee() ;
				if($script->EstBienRefere() == 0)
				{
					$this->ExecuteScriptMalRefere($script) ;
					return ;
				}
				$this->VerifieValiditeMotPasse($script) ;
				if($script->EstAccessible() == 0)
				{
					$this->ExecuteScriptInaccessible($script) ;
					return ;
				}
				$this->DetermineEnvironnement($script) ;
				$this->ScriptPourRendu = $script ;
				$this->RenduEnCours = 1 ;
				if($this->AnnulerRendu)
				{
					$this->RenduEnCours = 0 ;
					$this->ScriptPourRendu = null ;
					return ;
				}
				$ctn = $this->RenduProgramme() ;
				$this->RenduEnCours = 0 ;
				$this->ScriptPourRendu = null ;
				echo $ctn ;
				if($this->InsererSautLigneFinal)
				{
					echo PHP_EOL ;
				}
			}
		}
		
	}
	
?>