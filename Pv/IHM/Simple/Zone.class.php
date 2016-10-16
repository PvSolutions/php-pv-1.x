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
		
		class PvDocumentWebBase
		{
			protected function RenduDefsJS(& $zone)
			{
				$ctn = '' ;
				$ctn = '' ;
				for($i=0; $i<count($zone->ContenusJs); $i++)
				{
					$ctnJs = $zone->ContenusJs[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
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
			}
			public function RenduPied(& $zone)
			{
			}
		}
		class PvDocumentHtmlSimple extends PvDocumentWebBase
		{
			public function RenduEntete(& $zone)
			{
				$ctn = '' ;
				$ctn .= '<!doctype html>'.PHP_EOL ;
				$ctn .= '<html lang="'.$zone->LangueDocument.'">'.PHP_EOL ;
				$ctn .= '<head>'.PHP_EOL ;
				$ctn .= '<title>'.$zone->ObtientTitreDocument().'</title>'.PHP_EOL ;
				$ctn .= '<meta name="keywords" value="'.htmlentities($zone->ObtientMotsCleMetaDocument()).'" />'.PHP_EOL ;
				$ctn .= '<meta name="description" value="'.htmlentities($zone->ObtientDescMetaDocument()).'" />'.PHP_EOL ;
				if($zone->EncodageDocument != '')
					$ctn .= '<meta charset="'.$zone->EncodageDocument.'" />'.PHP_EOL ;
				$viewport = $zone->ObtientViewportMetaDocument() ;
				if($viewport != '')
				{
					$ctn .= '<meta name="viewport" value="'.htmlentities($viewport).'" />'.PHP_EOL ;
				}
				$auteur = $zone->ObtientAuteurMetaDocument() ;
				if($auteur != '')
				{
					$ctn .= '<meta name="author" value="'.htmlentities($auteur).'" />'.PHP_EOL ;
				}
				$ctn .= '<meta name="description" value="'.htmlentities($zone->ObtientDescMetaDocument()).'" />'.PHP_EOL ;
				if($zone->InclureCtnJsEntete)
				{
					$ctn .= $this->RenduDefsJS($zone) ;
				}
				$ctn .= $this->RenduDefsCSS($zone) ;
				$ctn .= $zone->RenduExtraHead ;
				$ctn .= '</head>'.PHP_EOL ;
				$ctn .= '<body>' ;
				return $ctn ;
			}
			public function RenduPied(& $zone)
			{
				$ctn = '' ;
				if(! $zone->InclureCtnJsEntete)
				{
					$ctn .= $this->RenduDefsJS($zone) ;
				}
				$ctn .= '</body>'.PHP_EOL ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
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
				$port = $parts["port"] != '' ? $parts["port"] : 80 ;
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
			public $DelaiExecution = 3600 ;
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
				if($this->Etat->Statut != PvEtatServPersist::ETAT_DEMARRE)
				{
					$this->Etat->TimestmpDebutSession = date("U") ;
					$this->Etat->Statut = PvEtatServPersist::ETAT_DEMARRE ;
				}
				$this->Etat->TimestmpCapt = date("U") ;
				$ok = $this->SauveEtat() ;
				if(! $ok)
				{
					return ;
				}
				$this->TerminerExecution = 1 ;
				$this->ExecuteInstructions() ;
				if($this->TerminerExecution)
				{
					$this->Etat->Statut = PvEtatServPersist::ETAT_STOPPE ;
					$this->Etat->TimestmpFinSession = date("U") ;
					$this->SauveEtat() ;
				}
				exit ;
			}
			public function Appelle($params=array())
			{
				return $this->GestParent->LanceTache($this->NomElementGest, $params) ;
			}
			protected function ExecuteInstructions()
			{
			}
		}
		
		class PvAdrScriptSessionWeb
		{
			public $ChaineGet ;
			public $DonneesPost ;
			protected function Sauvegarde(& $zone)
			{
				$_SESSION[$zone->NomElementApplication."_AddrScriptSession"] = serialize($this) ;
			}
			protected function Restaure(& $zone)
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
			public $TypeDocument ;
			public $AdrScriptSession ;
			public $DocumentsWeb = array() ;
			public $GestTachesWeb ;
			public $UtiliserDocumentWeb = 0 ;
			public $DocumentWebSelect = null ;
			public $DefinitionTypeDocument ;
			public $LangueDocument = "fr" ;
			public $EncodageDocument = "iso-8859-1" ;
			public $TitreDocument ;
			public $MotsCleMeta ;
			public $DescriptionMeta ;
			public $ViewportMeta ;
			public $AuteurMeta ;
			public $RenduExtraMeta ;
			public $ModeCache ;
			public $ScriptPourRendu ;
			public $InclureCtnJsEntete = 1 ;
			public $RenduExtraHead = '' ;
			public $InclureJQuery = 0 ;
			public $CheminJQuery = "js/jquery.min.js" ;
			public $InclureJQueryMigrate = 1 ;
			public $CheminJQueryMigrate = "js/jquery-migrate.min.js" ;
			public $InclureJQueryUi = 0 ;
			public $CheminJsJQueryUi = "js/jquery-ui.min.js" ;
			public $CheminCSSJQueryUi = "css/jquery-ui.css" ;
			public $ContenusCSS = array() ;
			public $ContenusJs = array() ;
			public $CheminIconeScript = "" ;
			public $InclureRenduTitre = 1 ;
			public $InclureRenduIcone = 1 ;
			public $DetectIconeCorresp = 0 ;
			public $CheminDossierIconeCorresp = "images/icones" ;
			public $ExtIconeCorresp = "png" ;
			public $InclureRenduChemin = 1 ;
			public $InclureRenduDescription = 1 ;
			public $ActionsAvantRendu = array() ;
			public $ActionsApresRendu = array() ;
			public $NomParamActionAppelee = "appelleAction" ;
			public $NomParamTacheAppelee = "appelleTache" ;
			public $ValeurParamActionAppelee = false ;
			public $ValeurParamTacheAppelee = false ;
			public $ActionsAppelees = array() ;
			public $AnnulerRendu = 0 ;
			public $RenduEnCours = 0 ;
			public $Habillage = null ;
			public $ActiverRafraichScript = 1 ;
			public $InclureScriptsMembership = 1 ;
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
			protected $TacheAppelee ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->GestTachesWeb = new PvGestTachesWebSimple() ;
				$this->GestTachesWeb->AdopteZone("gestTaches", $this) ;
				$this->AdrScriptSession = new PvAdrScriptSessionWeb() ;
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
				if(class_exists($this->NomClasseHabillage))
				{
					$nomClasse = $this->NomClasseHabillage ;
					$this->Habillage = new $nomClasse() ;
				}
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
			public function InsereActionAvantRendu($nomAction, $action)
			{
				$this->InscritActionAvantRendu($nomAction, $action) ;
				return $action ;
			}
			public function InsereActionApresRendu($nomAction, $action)
			{
				$this->InscritActionApresRendu($nomAction, $action) ;
				return $action ;
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
						$ctn .= $this->RenduDebutCorpsDocument().PHP_EOL ;
					}
					$ctn .= $this->RenduPiedDocument().PHP_EOL ;
					$ctn .= '</html>' ;
				}
				$ctn .= $this->RenduAutoRafraich() ;
				return $ctn ;
			}
			public function RenduAutoRafraich()
			{
				$ctn = '' ;
				if($this->ActiverRafraichScript && ($this->ScriptPourRendu->DoitAutoRafraich()))
				{
					$ctn .= '<script type="text/javascript">
	function execAutoRafraich() {
		window.location = '.json_encode($this->ScriptPourRendu->ObtientUrlParam($this->ScriptPourRendu->ParamsAutoRafraich)).' ;
	}
	window.setTimeout("execAutoRafraich()", '.intval($this->ScriptPourRendu->DelaiAutoRafraich).' * 1000) ;
</script>'.PHP_EOL ;
				}
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
			public function RenduEnteteDocument()
			{
				$this->InclutLibrairiesExternes() ;
				$ctn = '' ;
				$ctn .= '<head>'.PHP_EOL ;
				if($this->EncodageDocument != '')
					$ctn .= '<meta charset="'.$this->EncodageDocument.'" />'.PHP_EOL ;
				$ctn .= '<title>'.$this->ObtientTitreDocument().'</title>'.PHP_EOL ;
				$ctn .= '<meta name="keywords" value="'.htmlentities($this->ObtientMotsCleMetaDocument()).'" />'.PHP_EOL ;
				$viewport = $this->ObtientViewportMetaDocument() ;
				if($viewport != '')
				{
					$ctn .= '<meta name="viewport" value="'.htmlentities($viewport).'" />'.PHP_EOL ;
				}
				$auteur = $this->ObtientAuteurMetaDocument() ;
				if($auteur != '')
				{
					$ctn .= '<meta name="author" value="'.htmlentities($auteur).'" />'.PHP_EOL ;
				}
				$ctn .= '<meta name="description" value="'.htmlentities($this->ObtientDescMetaDocument()).'" />'.PHP_EOL ;
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
			protected function RenduCtnJs()
			{
				$ctn = '' ;
				for($i=0; $i<count($this->ContenusJs); $i++)
				{
					$ctnJs = $this->ContenusJs[$i] ;
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
				// print_r(array_keys($this->ActionsAvantRendu)) ;
				$this->ChargeScriptSession() ;
				$this->DetermineEnvironnement($script) ;
				$this->ExecuteRequeteSoumise($script) ;
				// $script->PrepareRendu() ;
				$this->ScriptPourRendu = $script ;
				$this->RenduEnCours = 1 ;
				$this->DetecteActionAppelee() ;
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