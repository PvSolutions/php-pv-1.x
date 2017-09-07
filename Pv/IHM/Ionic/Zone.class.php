<?php
	
	if(! defined('PV_ZONE_BASE_IONIC'))
	{
		if(! defined('PV_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_METHODE_DISTANTE_BASE_IONIC'))
		{
			include dirname(__FILE__)."/MethodeDistante.class.php" ;
		}
		if(! defined('PV_APP_SRC_BASE_IONIC'))
		{
			include dirname(__FILE__)."/AppSrc.class.php" ;
		}
		if(! defined('PV_PAGE_SRC_BASE_IONIC'))
		{
			include dirname(__FILE__)."/PageSrc.class.php" ;
		}
		if(! defined('PV_SERVICE_SRC_BASE_IONIC'))
		{
			include dirname(__FILE__)."/ServiceSrc.class.php" ;
		}
		if(! defined('PV_SCRIPT_WEB_IONIC'))
		{
			include dirname(__FILE__)."/ScriptWeb.class.php" ;
		}
		define('PV_ZONE_BASE_IONIC', 1) ;
		
		class PvAppelJsonIonic
		{
			public $method ;
			public $args ;
		}
		
		class PvAppelRecuIonic
		{
			public $IdDonnees ;
			public $Id ;
			public $Origine ;
			public $Adresse ;
			public $Contenu ;
			public $Resultat ;
		}
		
		class PvIHMBaseIonic extends PvIHM
		{
			public $PageSrcAccueil ;
			public $PagesSrc = array() ;
			public $ServicesSrc = array() ;
			public $MethodesDistantes = array() ;
			public $AppSrc ;
			public $MtdDistNonTrouvee ;
			public $NomPageAccueil = "Accueil" ;
			public $CheminProjetIonic ;
			public $NomParamMtdDist ;
			public $ValeurParamMtdDist ;
			public $ValeurParamMtdDistBrute ;
			public $MtdDistSelect ;
			public $AppelDistant ;
			public $MessageSuccesGener = "Fichiers generes avec succes..." ;
			public $MessageMtdDistNonTrouvee = "La méthode que vous souhaitez exécuter n'existe pas." ;
			public $ServiceSrcUtils ;
			public $NomServiceSrcUtils = "PvUtilitesIonic" ;
			public $UrlDistant = "?" ;
			protected $DhRepAppel ;
			protected $TablAppelRecu ;
			public $EnregistrerAppelsRecus = 0 ;
			public $DelaiExpirAppelsRecus = 2419200 ;
			public $NomTableAppelsRecus = "appel_recu" ;
			public $NomDocWebTrcAppelRecu = "" ;
			public $NomScriptListeAppelRecu = "liste_trc_appel_recu" ;
			public $ScriptListeAppelRecu ;
			public $TitreListeAppelRecu = "Liste des appels re&ccedil;us" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->AppelRecu = $this->CreeAppelRecuVide() ;
			}
			protected function CreeAppelRecuVide($origine="")
			{
				$appel = new PvAppelRecuIonic() ;
				$appel->Origine = $origine ;
				$appel->Id = uniqid() ;
				return $appel ;
			}
			public function CreeBdAppelsRecus()
			{
				return new AbstractSqlDB() ;
			}
			public function CreeFournBdAppelRecus()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->CreeBdAppelsRecus() ;
				return $fourn ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreFixeIonic() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteZone($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreHttpRequestIonic() ;
				$filtre->AdopteZone($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreFixe($nom, $valeur) ;
			}
			public function & CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
			public function ImpressionEnCours()
			{
				return 0 ;
			}
			protected function CreeAppSrc()
			{
				return new PvAppSrcBaseIonic() ;
			}
			protected function CreeServiceSrcUtils()
			{
				return new PvServiceSrcUtilsIonic() ;
			}
			protected function ChargeConfigAuto()
			{
				$this->AppSrc = $this->CreeAppSrc() ;
				$this->AppSrc->AdopteZone("app", $this) ;
				$this->PageSrcAccueil = $this->CreePageSrcAccueil() ;
				$this->PageSrcAccueil->AdopteZone($this->NomPageAccueil, $this) ;
				$this->MtdDistNonTrouvee = $this->CreeMtdDistNonTrouvee() ;
				$this->MtdDistNonTrouvee->AdopteZone("nonTrouvee", $this) ;
				$this->ServiceSrcUtils = $this->CreeServiceSrcUtils() ;
				$this->ServiceSrcUtils->AdopteZone($this->NomServiceSrcUtils, $this) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigAuto() ;
				$this->ChargePagesSrc() ;
				$this->ChargeServicesSrc() ;
			}
			protected function CreeMtdDistNonTrouvee()
			{
				return new PvMtdDistNonTrouveeIonic() ;
			}
			protected function ChargePagesSrc()
			{
			}
			protected function ChargeServicesSrc()
			{
			}
			protected function ChargeMethodesDistantes()
			{
			}
			public function & ToutesPagesSrc()
			{
				$res = array($this->PageSrcAccueil, $this->PageSrcNonAutorise) ;
				array_splice($res, count($res), 0, $this->PagesSrc) ;
				return $res ;
			}
			public function & TousServicesSrc()
			{
				$res = array($this->ServiceSrcUtils) ;
				array_splice($res, count($res), 0, $this->ServicesSrc) ;
				return $res ;
			}
			protected function CreePageSrcAccueil()
			{
				return new PvPageSrcAccueilIonic() ;
			}
			public function ExistePageSrc($nom)
			{
				return (isset($this->PagesSrc[$nom])) ? 1 : 0 ;
			}
			public function & InsereServiceSrc($nom, $servSrc)
			{
				$this->ServicesSrc[$nom] = & $servSrc ;
				$servSrc->AdopteZone($nom, $this) ;
				return $servSrc ;
			}
			public function & InserePageSrc($nom, $pageSrc)
			{
				$this->PagesSrc[$nom] = & $pageSrc ;
				$pageSrc->AdopteZone($nom, $this) ;
				return $pageSrc ;
			}
			public function & InsereMethodeDistante($nom, $methode)
			{
				$this->MethodesDistantes[$nom] = & $methode ;
				$methode->AdopteZone($nom, $this) ;
				return $methode ;
			}
			public function & InsereMethodeDist($nom, $methode)
			{
				return $this->InsereMethodeDistante($nom, $methode) ;
			}
			public function & InsereMtdDist($nom, $methode)
			{
				return $this->InsereMethodeDistante($nom, $methode) ;
			}
			protected function ChargeConfigElems()
			{
				$pagesSrc = $this->ToutesPagesSrc() ;
				$servsSrc = $this->TousServicesSrc() ;
				foreach($pagesSrc as $nom => $pageSrc)
				{
					$pageSrc->ChargeConfig() ;
					$pageSrc->ChargeComposantsIU() ;
				}
				foreach($servsSrc as $nom => $servSrc)
				{
					$servSrc->ChargeConfig() ;
				}
				$this->AppSrc->ChargeConfig() ;
			}
			protected function VideDossier($chemRep)
			{
				if(! is_dir($chemRep))
				{
					return ;
				}
				$chemReps = array() ;
				$dh = opendir($chemRep) ;
				while(($nomFich = readdir($dh)) !== false)
				{
					if($nomFich == "." || $nomFich == "..")
					{
						continue ;
					}
					$chemFich = $chemRep."/".$nomFich ;
					if(is_dir($chemFich))
					{
						$chemReps[] = $chemFich ;
					}
					else
					{
						// @unlink($chemFich) ;
					}
				}
				closedir($dh) ;
				foreach($chemReps as $i => $chemRep)
				{
					$this->VideDossier($chemRep) ;
					// @unlink($chemRep) ;
				}
			}
			protected function GenereFichiersElems()
			{
				$pagesSrc = $this->ToutesPagesSrc() ;
				$servicesSrc = $this->TousServicesSrc() ;
				$this->VideDossier($this->CheminProjetIonic."/src/pages") ;
				foreach($pagesSrc as $nom => $pageSrc)
				{
					$pageSrc->GenereFichiers() ;
				}
				$this->VideDossier($this->CheminProjetIonic."/src/providers") ;
				foreach($servicesSrc as $nom => $servSrc)
				{
					$servSrc->GenereFichiers() ;
				}
				$this->AppSrc->GenereFichiers() ;
			}
			public function GenereFichiers()
			{
				$this->ChargeConfigElems() ;
				$this->GenereFichiersElems() ;
			}
			protected function ChargeMtdsDistsElems()
			{
				$servicesSrc = $this->TousServicesSrc() ;
				$pagesSrc = $this->ToutesPagesSrc() ;
				foreach($pagesSrc as $i => $pageSrc)
				{
					$pageSrc->ChargeComposantsIU() ;
					foreach($pageSrc->ComposantsIU as $nomComp => $comp)
					{
						$comp->FournitMethodesDistantes() ;
					}
				}
				foreach($servicesSrc as $i => $serviceSrc)
				{
					$serviceSrc->FournitMethodesDistantes() ;
				}
				// file_put_contents("appel.txt", print_r(array_keys($this->MethodesDistantes), true)) ;
			}
			protected function CorpsHttpBrut()
			{
				$ctn = '' ;
				$fh = fopen("php://input", "r") ;
				if($fh !== false)
				{
					while(! feof($fh))
					{
						$ctn .= fgets($fh) ;
					}
					fclose($fh) ;
				}
				return $ctn ;
			}
			protected function CtnEntetesRequeteHttp()
			{
				$entetes = apache_request_headers() ;
				$ctn = '' ;
				foreach($entetes as $nom => $val)
				{
					$ctn .= $nom.':'.$val."\r\n" ;
				}
				return $ctn ;
			}
			protected function AppelHttpDistant()
			{
				$ctn = $this->CorpsHttpBrut() ;
				$this->AppelRecu->Adresse = get_current_url() ;
				$this->AppelRecu->Contenu = $ctn ;
				// file_put_contents("TTTT.txt", $this->CtnEntetesRequeteHttp()."\r\n".$ctn."\r\n\r\n", FILE_APPEND) ;
				$appel = ($ctn != '') ? @svc_json_decode($ctn) : new PvAppelJsonIonic() ;
				if($appel == null)
				{
					$appel = new PvAppelJsonIonic() ;
				}
				return $appel ;
			}
			protected function DetecteMtdDistSelect($appelDistant)
			{
				$this->AppelDistant = $appelDistant ;
				$this->ValeurParamMtdDistBrute = $this->AppelDistant->method ;
				$this->ValeurParamMtdDist = "" ;
				// file_put_contents("appel.txt", print_r($this->AppelDistant, true)) ;
				if(isset($this->MethodesDistantes[$this->ValeurParamMtdDistBrute]))
				{
					$this->ValeurParamMtdDist = $this->ValeurParamMtdDistBrute ;
				}
				// print "ooo : ".print_r(array_keys($this->MethodesDistantes), true) ;
			}
			protected function ExecuteMtdDistSelect()
			{
				$this->MtdDistSelect = & $this->MtdDistNonTrouvee ;
				if($this->ValeurParamMtdDist != '')
				{
					$this->MtdDistSelect = & $this->MethodesDistantes[$this->ValeurParamMtdDist] ;
				}
				$this->MtdDistSelect->Execute($this->AppelDistant->args) ;
			}
			protected function FixeAccesCrossOrigin()
			{
				// Détecter les requetes HTTP
				if (isset($_SERVER['HTTP_ORIGIN'])) {
					header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
					header('Access-Control-Allow-Credentials: true');
					header('Access-Control-Max-Age: 86400');    // cache for 1 day
				}

				// Access-Control headers are received during OPTIONS requests
				if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

					if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
						header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

					if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
						header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
					exit(0);
				}
			}
			protected function InsereDonneesAppelRecu()
			{
				if(! $this->EnregistrerAppelsRecus)
				{
					return ;
				}
				$bd = $this->CreeBdAppelsRecus() ;
				$ok = $bd->RunSql("delete from ".$bd->EscapeTableName($this->NomTableAppelsRecus)." where ".$bd->SqlDateDiff($bd->SqlNow(), "date_creation")." > ".$bd->ParamPrefix."delaiExpir", array("delaiExpir" => $this->DelaiExpirAppelsRecus)) ;
				if(! $ok)
				{
					die("Impossible d'enregistrer les traces, le service est interrompu") ;
					return ;
				}
				$ok = $bd->RunSql(
					"insert into ".$bd->EscapeTableName($this->NomTableAppelsRecus)." (id_ctrl, origine_appel, adresse_appel, contenu_appel) values (".$bd->ParamPrefix."idCtrl, ".$bd->ParamPrefix."origineAppel, ".$bd->ParamPrefix."adresseAppel, ".$bd->ParamPrefix."contenuAppel)",
					array(
						"idCtrl" => $this->AppelRecu->Id,
						"origineAppel" => $this->AppelRecu->Origine,
						"adresseAppel" => $this->AppelRecu->Adresse,
						"contenuAppel" => $this->AppelRecu->Contenu
					)
				) ;
				if($ok)
				{
					$this->AppelRecu->IdDonnees = $bd->FetchSqlValue("select id from ".$bd->EscapeTableName($this->NomTableAppelsRecus)." where id_ctrl=:idCtrl", array("idCtrl" => $this->AppelRecu->Id), "id") ;
				}
			}
			protected function MajDonneesAppelRecu()
			{
				if(! $this->EnregistrerAppelsRecus)
				{
					return ;
				}
				$bd = $this->CreeBdAppelsRecus() ;
				$bd->RunSql(
					"update ".$bd->EscapeTableName($this->NomTableAppelsRecus)." set contenu_resultat=".$bd->ParamPrefix."resultat where id=".$bd->ParamPrefix."id",
					array(
						"id" => $this->AppelRecu->IdDonnees,
						"resultat" => $this->AppelRecu->Resultat,
					)
				) ;
			}
			protected function RecoitAppelDistant()
			{
				$this->AppelRecu = $this->CreeAppelRecuVide("http") ;
				$this->FixeAccesCrossOrigin() ;
				$this->ChargeMethodesDistantes() ;
				$this->ChargeMtdsDistsElems() ;
				$appelDistant = $this->AppelHttpDistant() ;
				$this->InsereDonneesAppelRecu() ;
				$this->DetecteMtdDistSelect($appelDistant) ;
				$this->ExecuteMtdDistSelect() ;
				$this->AfficheResultAppelDistant() ;
				$this->MajDonneesAppelRecu() ;
			}
			public function TraiteAppel($appelDistant)
			{
				$this->ChargeMethodesDistantes() ;
				$this->ChargeMtdsDistsElems() ;
				$this->AppelRecu = $this->CreeAppelRecuVide("direct") ;
				$this->AppelRecu->Adresse = (isset($_SERVER["argv"]) && isset($_SERVER["argv"][0])) ? $_SERVER["argv"][0] : '<vide>' ;
				$this->AppelRecu->Contenu = svc_json_encode($appelDistant) ;
				$this->InsereDonneesAppelRecu() ;
				$this->DetecteMtdDistSelect($appelDistant) ;
				$this->ExecuteMtdDistSelect() ;
				$this->AppelRecu->Resultat = svc_json_encode($this->MtdDistSelect->Result()) ;
				return $this->AppelRecu->Resultat ;
			}
			public function ParcourtRepAppelsFtp($cheminRepAppel, $cheminRepResult)
			{
				if(! is_dir($cheminRepAppel))
				{
					return ;
				}
				$this->ChargeMethodesDistantes() ;
				$this->ChargeMtdsDistsElems() ;
				$this->DhRepAppel = opendir($cheminRepAppel) ;
				if(is_resource($this->DhRepAppel))
				{
					while(($nomFich = readdir($this->DhRepAppel)) !== false)
					{
						if($nomFich == "." || $nomFich == "..")
						{
							continue ;
						}
						$infosFich = pathinfo($cheminRepAppel) ;
						if(! isset($infosFich["extension"]) || strtolower($infosFich["extension"]) != "json")
						{
							continue ;
						}
						$cheminFichAppel = $cheminRepAppel."/".$nomFich ;
						$fh = @fopen($cheminFichAppel, "r") ;
						$ctnAppel = '' ;
						if(is_resource($fh))
						{
							while(! feof($fh))
							{
								$ctnAppel .= fread($fh) ;
							}
							fclose($fh) ;
						}
						$this->AppelRecu = $this->CreeAppelRecuVide("repertoire") ;
						$appelDistant = new PvAppelJsonIonic() ;
						if($ctnAppel != '')
						{
							$appelDistant = @svc_json_encode($ctnAppel) ;
						}
						$this->AppelRecu->Adresse = $cheminFichAppel ;
						$this->AppelRecu->Contenu = $ctnAppel ;
						$this->InsereDonneesAppelRecu() ;
						$this->DetecteMtdDistSelect($appelDistant) ;
						$this->ExecuteMtdDistSelect() ;
						$this->AppelRecu->Resultat = svc_json_encode($this->MtdDistSelect->Result()) ;
						$cheminFichRes = $cheminRepResult."/".$nomFich ;
						$fhRes = @fopen($cheminFichRes, "w") ;
						if($fhRes !== false)
						{
							fputs($fhRes, $this->AppelRecu->Resultat) ;
							fclose($fhRes) ;
						}
						unlink($cheminFichAppel) ;
						$this->MajDonneesAppelRecu() ;
					}
				}
			}
			public function PossedeMtdDistSelect()
			{
				return (php_sapi_name() != "cli" && $this->ValeurParamMtdDist != "") ;
			}
			protected function AfficheResultAppelDistant()
			{
				$this->AppelRecu->Resultat = svc_json_encode($this->MtdDistSelect->Result()) ;
				echo $this->AppelRecu->Resultat ;
			}
			public function AppelJs($args)
			{
				$ctn = '' ;
				return $ctn ;
			}
			public function Execute()
			{
				if(php_sapi_name() == "cli")
				{
					if($this->CheminProjetIonic != "")
					{
						$this->GenereFichiers() ;
					}
					echo $this->MessageSuccesGener. PHP_EOL ;
				}
				else
				{
					$this->RecoitAppelDistant() ;
				}
			}
			protected function CreeScriptListeAppelRecu()
			{
				return new PvScriptTrcListeAppelRecuIonic() ;
			}
			protected function & InsereScriptWeb($nom, $script, & $zone, $privs=array(), $membreConnecte=1)
			{
				$script = $zone->InsereScript($nom, $script) ;
				$script->NomZoneIonic = $this->NomElementApplication ;
				$script->NecessiteMembreConnecte = $membreConnecte ;
				$script->Privileges = $privs ;
				return $script ;
			}
			public function RemplitScriptsTrcWeb(& $zone, $privs=array(), $membreConnecte=1)
			{
				$this->ScriptListeAppelRecu = $this->InsereScriptWeb($this->NomScriptListeAppelRecu, $this->CreeScriptListeAppelRecu(), $zone, $privs, $membreConnecte) ;
				$this->ScriptListeAppelRecu->NomDocumentWeb = $this->NomDocWebTrcAppelRecu ;
				$this->ScriptListeAppelRecu->Titre = $this->TitreListeAppelRecu ;
				$this->ScriptListeAppelRecu->TitreDocument = $this->TitreListeAppelRecu ;
			}
			protected function CreeTablAppelRecu(& $script)
			{
				return new PvTableauDonneesHtml() ;
			}
			public function & RemplitTablAppelRecu($nom, & $script)
			{
				$this->TablAppelRecu = $this->CreeTablAppelRecu($script) ;
				$this->InitTablAppelRecu() ;
				$this->TablAppelRecu->AdopteScript($nom, $script) ;
				$this->ChargeTablAppelRecu() ;
				return $this->TablAppelRecu ;
			}
			protected function InitTablAppelRecu()
			{
			}
			protected function ChargeTablAppelRecu()
			{
				$this->TablAppelRecu->AutoriserTriColonneInvisible = 1 ;
				$this->TablAppelRecu->SensColonneTri = "desc" ;
				$this->TablAppelRecu->InsereDefColCachee("date_creation") ;
				$this->TablAppelRecu->InsereDefColCachee("id") ;
				$this->TablAppelRecu->InsereDefColDateTimeFr("date_creation", "Date cr&eacute;ation") ;
				$this->TablAppelRecu->InsereDefCol("origine_appel", "Origine") ;
				$this->TablAppelRecu->InsereDefColDetail("adresse_appel", "Adresse") ;
				$this->TablAppelRecu->InsereDefColDetail("contenu_appel", "Contenu") ;
				$this->TablAppelRecu->InsereDefColDetail("contenu_resultat", "Resultat") ;
				$this->TablAppelRecu->FournisseurDonnees = $this->CreeFournBdAppelRecus() ;
				$this->TablAppelRecu->FournisseurDonnees->RequeteSelection = $this->NomTableAppelsRecus ;
			}
		}
		
		class PvZoneBaseIonic extends PvIHMBaseIonic
		{
			public $InclurePagesSrcMembership = 1 ;
			public $NomClasseMembership = "AkSqlMembership" ;
			public $NomPageSrcConnexion = "connexion" ;
			public $NomClassePageSrcConnexion = "PvPageSrcConnexionIonic" ;
			public $NomPageSrcInscription = "inscription" ;
			public $NomClassePageSrcInscription = "PvPageSrcInscriptionIonic" ;
			public $NomPageSrcModifPrefs = "modifPrefs" ;
			public $NomClassePageSrcModifPrefs = "PvPageSrcModifPrefsIonic" ;
			public $NomPageSrcRecouvreMP = "recouvreMP" ;
			public $NomClassePageSrcRecouvreMP = "" ;
			public $NomServiceSrcMembership = "membership" ;
			public $NomClasseServiceSrcMembership = "PvServiceSrcMembershipIonic" ;
			public $NomClasseTsMembership = "MembershipLocal" ;
			public $NomPageNonAutorise = "nonAutorise" ;
			public $PageSrcNonAutorise ;
			public $ServiceSrcMembership ;
			public $RedirigerVersConnexion = 1 ;
			public $InscrireMtdsAccesPageSrc = 1 ;
			public $AutoriserInscription = 1 ;
			public $AutoriserModifPrefs = 1 ;
			protected function CreeAppSrc()
			{
				return new PvAppSrcRestreintIonic() ;
			}
			protected function CreeMembership()
			{
				$nomClasse = "AkSqlMembership" ;
				if(class_exists($this->NomClasseMembership))
				{
					$nomClasse = $this->NomClasseMembership ;
				}
				return new $nomClasse($this) ; 
			}
			protected function CreePageSrcNonAutorise()
			{
				return new PvPageSrcNonAutoriseIonic() ;
			}
			protected function ChargeConfigAuto()
			{
				$this->Membership = $this->CreeMembership() ;
				parent::ChargeConfigAuto() ;
				$this->PageSrcNonAutorise = $this->CreePageSrcNonAutorise() ;
				$this->PageSrcNonAutorise->AdopteZone($this->NomPageNonAutorise, $this) ;
			}
			protected function & CreePageSrcParClasse($nomClasse)
			{
				$pageSrc = new PvPageSrcIndefIonic() ;
				if(class_exists($nomClasse))
				{
					$pageSrc = new $nomClasse() ;
				}
				return $pageSrc ;
			}
			protected function & InserePageSrcParClasse($nom, $nomClasse)
			{
				return $this->InserePageSrc($nom, $this->CreePageSrcParClasse($nomClasse)) ;
			}
			protected function & CreeServiceSrcParClasse($nomClasse)
			{
				$servSrc = new PvServiceSrcIndefIonic() ;
				if(class_exists($nomClasse))
				{
					$servSrc = new $nomClasse() ;
				}
				return $servSrc ;
			}
			protected function & InsereServiceSrcParClasse($nom, $nomClasse)
			{
				return $this->InsereServiceSrc($nom, $this->CreeServiceSrcParClasse($nomClasse)) ;
			}
			protected function ChargePagesSrcMembership()
			{
				if($this->AutoriserInscription == 1)
				{
					$this->PageSrcInscription = $this->InserePageSrcParClasse($this->NomPageSrcInscription, $this->NomClassePageSrcInscription) ;
				}
				if($this->AutoriserModifPrefs == 1)
				{
					$this->PageSrcModifPrefs = $this->InserePageSrcParClasse($this->NomPageSrcModifPrefs, $this->NomClassePageSrcModifPrefs) ;
				}
				$this->PageSrcConnexion = $this->InserePageSrcParClasse($this->NomPageSrcConnexion, $this->NomClassePageSrcConnexion) ;
				
			}
			protected function ChargePagesSrc()
			{
				$this->ChargePagesSrcMembership() ;
			}
			protected function ChargeServicesSrc()
			{
				$this->ServiceSrcMembership = $this->InsereServiceSrcParClasse($this->NomServiceSrcMembership, $this->NomClasseServiceSrcMembership) ;
			}
			public function & ToutesPagesSrc()
			{
				$res = parent::ToutesPagesSrc() ;
				$res[] = & $this->PageSrcNonAutorise ;
				return $res ;
			}
			public function RemplitMtdsAccesPageSrc(& $pageSrc)
			{
				$pageSrc->FichTs->InsereImportGlobal(array("Storage"), "@ionic/storage") ;
				$pageSrc->FichTs->InsereImportGlobal(array("Events"), "ionic-angular") ;
				$pageSrc->ClasseTs->InsereMembre("membreConnecteBrut", "''", "string") ;
				$pageSrc->ClasseTs->InsereMembre("membreConnecte", 'null', "any") ;
				$pageSrc->ClasseTs->MtdConstruct->Arguments[] = "public events: Events" ;
				$pageSrc->ClasseTs->MtdConstruct->Arguments[] = "public storage:Storage" ;
				$mtdDetermineMembreConnecte = $pageSrc->ClasseTs->InsereMethode("determineMembreConnecte", array('fonctSuiv:any')) ;
				$mtdDetermineMembreConnecte->CorpsBrut .= 'var _self:any = this ;
console.log("HAAAA") ;
_self.menuCtrl.close() ;
_self.menuCtrl.enable(true, "non_connecte") ;
_self.menuCtrl.enable(false, "connecte") ;
_self.membreConnecteBrut = "" ;
_self.membreConnecte = null ;
_self.storage.get("membreConnecte").then((val) => {
if(val !== null && val !== \'\') {
_self.membreConnecteBrut = val ;
_self.membreConnecte = JSON.parse(_self.membreConnecteBrut) ;
_self.menuCtrl.enable(false, "non_connecte") ;
_self.menuCtrl.enable(true, "connecte") ;
_self.events.publish("login:succes", _self.membreConnecte) ;
}
if(fonctSuiv !== undefined && fonctSuiv !== null)
{
fonctSuiv() ;
}
}, (error) => {
if(fonctSuiv !== undefined && fonctSuiv !== null)
{
fonctSuiv() ;
}	
}) ;' ;
				$mtdIdMembreConnecte = $pageSrc->ClasseTs->InsereMethode("idMembreConnecte", array()) ;
				$mtdIdMembreConnecte->CorpsBrut .= 'return (this.membreConnecte !== null) ? this.membreConnecte.Id : 0 ;' ;
				$mtdPossedeMembreConnecte = $pageSrc->ClasseTs->InsereMethode("possedeMembreConnecte", array()) ;
				$mtdPossedeMembreConnecte->CorpsBrut .= 'return this.membreConnecte !== null ;' ;
				$mtdPossedePrivileges = $pageSrc->ClasseTs->InsereMethode("possedePrivileges", array('privs:string[]')) ;
				$mtdPossedePrivileges->CorpsBrut .= 'let ok = this.possedeMembreConnecte() ;
if(! ok) {
return ok ;
}
if(privs === undefined || privs === null || privs.length == 0) {
return true ;
}
for(let i:any=0; i<privs.length; i++)
{
if(this.membreConnecte.Profile[privs[i]] !== undefined && this.membreConnecte.Profile[privs[i]].Enabled === true)
{
ok = true ;
break ;
}
}
return ok ;' ;
			}
			protected function GenereFichiersElems()
			{
				$this->PageSrcNonAutorise->GenereFichiers() ;
				parent::GenereFichiersElems() ;
			}
			protected function ChargeConfigElems()
			{
				$this->PageSrcNonAutorise->ChargeConfig() ;
				$this->PageSrcNonAutorise->ChargeComposantsIU() ;
				parent::ChargeConfigElems() ;
			}
		}
	}
	
?>