<?php
	
	if(! defined('PV_ZONE_APPEL_DISTANT_BASE'))
	{
		if(! defined('PV_METHODE_DISTANTE_BASE_IONIC'))
		{
			include dirname(__FILE__)."/MethodeDistante.class.php" ;
		}
		define('PV_ZONE_APPEL_DISTANT_BASE', 1) ;
		
		class PvAppelJsonDistant
		{
			public $method ;
			public $args ;
		}
		
		class PvAppelRecuDistant
		{
			public $IdDonnees ;
			public $Id ;
			public $Origine ;
			public $Adresse ;
			public $Contenu ;
			public $Resultat ;
		}
		
		class PvZoneAppelDistant extends PvIHM
		{
			public $MethodesDistantes = array() ;
			public $MtdDistNonTrouvee ;
			public $NomParamMtdDist ;
			public $ValeurParamMtdDist ;
			public $ValeurParamMtdDistBrute ;
			public $MtdDistSelect ;
			public $AppelDistant ;
			public $UrlDistant = "?" ;
			protected $DhRepAppel ;
			protected $TablAppelRecu ;
			public $EnregistrerAppelsRecus = 0 ;
			public $DelaiExpirAppelsRecus = 2419200 ; // 30 jour(s)
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
				$appel = new PvAppelRecuDistant() ;
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
			public function ImpressionEnCours()
			{
				return 0 ;
			}
			protected function ChargeConfigAuto()
			{
				$this->MtdDistNonTrouvee = $this->CreeMtdDistNonTrouvee() ;
				$this->MtdDistNonTrouvee->AdopteZone("nonTrouvee", $this) ;
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
				return new PvMtdDistNonTrouveeDistant() ;
			}
			protected function ChargeMethodesDistantes()
			{
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
				$appel = ($ctn != '') ? @svc_json_decode($ctn) : new PvAppelJsonDistant() ;
				if($appel == null)
				{
					$appel = new PvAppelJsonDistant() ;
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
						$appelDistant = new PvAppelJsonDistant() ;
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
				if(php_sapi_name() != "cli")
				{
					$this->RecoitAppelDistant() ;
				}
			}
			protected function CreeScriptListeAppelRecu()
			{
				return new PvScriptTrcListeAppelRecuDistant() ;
			}
			protected function & InsereScriptWeb($nom, $script, & $zone, $privs=array(), $membreConnecte=1)
			{
				$script = $zone->InsereScript($nom, $script) ;
				$script->NomZoneDistant = $this->NomElementApplication ;
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
	}
	
?>