<?php
	
	if(! defined('PV_API_RESTFUL'))
	{
		if(! defined('OPENSSL_CRYPTER'))
		{
			include dirname(__FILE__)."/../../../misc/OpensslCrypter.class.php" ;
		}
		if(! defined('PV_AUTH_RESTFUL'))
		{
			include dirname(__FILE__)."/Auth.class.php" ;
		}
		if(! defined('PV_FILTRE_RESTFUL'))
		{
			include dirname(__FILE__)."/Filtre.class.php" ;
		}
		if(! defined('PV_DEF_COL_RESTFUL'))
		{
			include dirname(__FILE__)."/DefCol.class.php" ;
		}
		if(! defined('PV_COMMANDE_RESTFUL'))
		{
			include dirname(__FILE__)."/Commande.class.php" ;
		}
		if(! defined('PV_ACTION_RESTFUL'))
		{
			include dirname(__FILE__)."/Action.class.php" ;
		}
		if(! defined('PV_COMPOSANT_BASE_RESTFUL'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('PV_ROUTE_BASE_RESTFUL'))
		{
			include dirname(__FILE__)."/Route.class.php" ;
		}
		define('PV_API_RESTFUL', 1) ;
		
		class PvMessageHttpRestful
		{
			public $Entetes = array() ;
		}
		
		class PvRequeteRestful extends PvMessageHttpRestful
		{
			public $Methode ;
			public $EncodageParDefaut = "utf-8" ;
			public $EnteteContentType ;
			public $EnteteAuthorization ;
			public $EnteteAuthType ;
			public $EnteteAuthCredentials ;
			public $AttrsContentType = array() ;
			public $CheminRelatifRoute ;
			public $CorpsBrut ;
			public $Corps ;
			public function __construct()
			{
				$this->Entetes = array() ;
				$entetesTemp = apache_request_headers() ;
				foreach($entetesTemp as $nom => $val)
				{
					$this->Entetes[strtolower($nom)] = $val ;
				}
				$this->DetecteEntetesSpec() ;
				$attrs = explode("?", $_SERVER["REQUEST_URI"], 2) ;
				$this->CheminRelatifRoute = $attrs[0] ;
				$this->CorpsBrut = file_get_contents("php://input") ;
				$this->Corps = new StdClass ;
				if($this->EnteteContentType == "application/x-www-form-urlencoded")
				{
					parse_str($this->CorpsBrut, $vals) ;
					foreach($vals as $nom => $val)
					{
						$this->Corps->$nom = $val ;
					}
				}
				elseif($this->EnteteContentType == "application/json")
				{
					$this->Corps = json_decode($this->CorpsBrut) ;
				}
			}
			protected function DetecteEntetesSpec()
			{
				$this->Methode = $_SERVER["REQUEST_METHOD"] ;
				if(isset($this->Entetes["x-http-method-override"]))
				{
					$this->Methode = $this->Entetes["x-http-method-override"] ;
				}
				if(isset($this->Entetes["content-type"]))
				{
					$attrsContentType = explode(";", strtolower($this->Entetes["content-type"])) ;
					$this->Entetes["content-type"] = $attrsContentType[0] ;
					array_splice($attrsContentType, 0, 1) ;
					$this->AttrsContentType = array() ;
					foreach($attrsContentType as $i => $attrSpec)
					{
						$attrs = explode("=", $attrSpec, 2) ;
						$this->AttrsContentType[strtolower($attrs[0])] = $attrs[1] ;
					}
					$this->EnteteContentType = $this->Entetes["content-type"] ;
				}
				$this->EnteteEncodage = (isset($this->AttrsContentType["encoding"])) ? $this->AttrsContentType["encoding"] : $this->EncodageParDefaut ;
				$this->EnteteAuthorization = (isset($this->Entetes["authorization"])) ? $this->Entetes["authorization"] : null ;
				if($this->EnteteAuthorization != null)
				{
					$attrsAuth = explode(" ", $this->EnteteAuthorization, 2) ;
					$this->EnteteAuthType = strtolower($attrsAuth[0]) ;
					$this->EnteteAuthCredentials = (count($attrsAuth) == 2) ? $attrsAuth[1] : null ;
				}
			}
			public function AttrEntete($nom, $valeurDefaut=null)
			{
				return (isset($this->Entetes[strtolower($nom)])) ? $this->Entetes[strtolower($nom)] : $valeurDefaut ;
			}
		}
		
		class PvContenuJsonRestful
		{
			public $errors = array() ;
			public $data ;
		}
		class PvErrContenuJsonRestful
		{
			public $code ;
			public $userMessage ;
			public $internalMessage ;
			public $moreInfo ;
		}
		class PvReponseRestful extends PvMessageHttpRestful
		{
			public $NomFichierAttache ;
			public $EnteteContentType = "application/json" ;
			public $Contenu ;
			public $Metadatas = array() ;
			public $EnteteStatusCode ;
			public $MessageStatusCode ;
			public function __construct()
			{
				$this->Contenu = new PvContenuJsonRestful() ;
			}
			protected function EnvoieCode($code)
			{
				if(function_exists('http_response_code'))
				{
					http_response_code($code) ;
				}
				elseif($code !== NULL)
				{
					$text = '' ;
					switch ($code) {
						case 100: $text = 'Continue'; break;
						case 101: $text = 'Switching Protocols'; break;
						case 200: $text = 'OK'; break;
						case 201: $text = 'Created'; break;
						case 202: $text = 'Accepted'; break;
						case 203: $text = 'Non-Authoritative Information'; break;
						case 204: $text = 'No Content'; break;
						case 205: $text = 'Reset Content'; break;
						case 206: $text = 'Partial Content'; break;
						case 300: $text = 'Multiple Choices'; break;
						case 301: $text = 'Moved Permanently'; break;
						case 302: $text = 'Moved Temporarily'; break;
						case 303: $text = 'See Other'; break;
						case 304: $text = 'Not Modified'; break;
						case 305: $text = 'Use Proxy'; break;
						case 400: $text = 'Bad Request'; break;
						case 401: $text = 'Unauthorized'; break;
						case 402: $text = 'Payment Required'; break;
						case 403: $text = 'Forbidden'; break;
						case 404: $text = 'Not Found'; break;
						case 405: $text = 'Method Not Allowed'; break;
						case 406: $text = 'Not Acceptable'; break;
						case 407: $text = 'Proxy Authentication Required'; break;
						case 408: $text = 'Request Time-out'; break;
						case 409: $text = 'Conflict'; break;
						case 410: $text = 'Gone'; break;
						case 411: $text = 'Length Required'; break;
						case 412: $text = 'Precondition Failed'; break;
						case 413: $text = 'Request Entity Too Large'; break;
						case 414: $text = 'Request-URI Too Large'; break;
						case 415: $text = 'Unsupported Media Type'; break;
						case 500: $text = 'Internal Server Error'; break;
						case 501: $text = 'Not Implemented'; break;
						case 502: $text = 'Bad Gateway'; break;
						case 503: $text = 'Service Unavailable'; break;
						case 504: $text = 'Gateway Time-out'; break;
						case 505: $text = 'HTTP Version not supported'; break;
						default:
							exit('Unknown http status code "' . htmlentities($code) . '"');
						break;
					}
					$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
					header($protocol . ' ' . $code . ' ' . $text);
				}
			}
			public function InsereErreur($code, $msgInterne, $msgUser='')
			{
				$errData = new PvErrContenuJsonRestful() ;
				$errData->code = $code ;
				$errData->internalMessage = $msgInterne ;
				$errData->userMessage = ($msgUser != '') ? $msgUser : $msgInterne ;
				$this->Contenu->errors[] = $errData ;
				$this->Contenu->data = null ;
			}
			protected function DefinitEnteteStatusCode($code, $message='')
			{
				$this->EnteteStatusCode = $code ;
				if($code != 200)
				{
					$this->InsereErreur($code, (($message != '') ? $message : 'Le service a renvoye le code HTTP '.$code)) ;
				}
			}
			public function EstSucces()
			{
				return $this->EnteteStatusCode == 200 ;
			}
			public function EstEchec()
			{
				return ! $this->EstSucces() ;
			}
			public function ConfirmeSucces()
			{
				$this->DefinitEnteteStatusCode(200) ;
			}
			public function ConfirmeInvalide($message='')
			{
				$this->DefinitEnteteStatusCode(400, $message) ;
			}
			public function ConfirmeEchecAuth()
			{
				$this->DefinitEnteteStatusCode(403) ;
			}
			public function ConfirmeErreurInterne($message='')
			{
				$this->DefinitEnteteStatusCode(500, $message) ;
			}
			public function ConfirmeNonAutoris()
			{
				$this->DefinitEnteteStatusCode(401) ;
			}
			public function ConfirmeNonAutorise()
			{
				$this->DefinitEnteteStatusCode(401) ;
			}
			public function ConfirmeNonTrouve()
			{
				$this->DefinitEnteteStatusCode(404) ;
			}
			protected function CalculeEntetesSpec(& $api)
			{
				$this->Entetes["Access-Control-Allow-Origin"] = $api->OriginesAutorisees ;
				$this->Entetes["Content-Type"] = $this->EnteteContentType ;
				$this->Entetes["Cache-Control"] = "no-cache, must-revalidate" ;
				$this->Entetes["Expires"] = "Sat, 01 Jul 1970 00:00:00 GMT" ;
			}
			protected function EnvoieEntetes(& $api)
			{
				$this->CalculeEntetesSpec($api) ;
				foreach($this->Entetes as $nom => $val)
				{
					header($nom.": ".$val) ;
				}
			}
			public function EnvoieRendu(& $api)
			{
				$this->EnvoieCode($this->EnteteStatusCode) ;
				$this->EnvoieEntetes($api) ;
				$contenu = $this->Contenu ;
				$contenu->_metadatas = $api->Metadatas ;
				echo svc_json_encode($contenu) ;
			}
		}
		
		class PvApiRestful extends PvIHM
		{
			public $TypeIHM = "API" ;
			public $CrypteurToken ;
			public $CheminRacineApi = "/" ;
			public $NomClasseAuth = "PvAuthDistRestful" ;
			public $Auth ;
			public $Routes = array() ;
			public $VersionMin = 1 ;
			public $VersionMax = 1 ;
			public $NomTableSession = "membership_session" ;
			public $DelaiExpirSession = 900 ;
			public $TotalJoursExpirDevice = 90 ;
			public $MaxSessionsMembre = 0 ;
			public $EncodageDocument = "utf-8" ;
			public $OriginesAutorisees = "*" ;
			public $RouteParDefaut ;
			public $Reponse ;
			public $Requete ;
			public $NomClasseMembership ;
			public $Membership ;
			public $InclureRoutesMembership = 1 ;
			public $NomRouteAppelee ;
			public $PrivilegesEditMembership = array() ;
			public $PrivilegesEditMembres = array() ;
			protected $NomRoutesEditMembership = array() ;
			public $AutoriserInscription = 0 ;
			public $AutoriserModifPrefs = 0 ;
			public $NomClasseRouteRecouvreMP = "PvRouteRecouvreMPRestful" ;
			public $NomClasseRouteConnexion = "PvRouteConnexionRestful" ;
			public $NomClasseRouteInscription = "PvRouteInscriptionRestful" ;
			public $NomClasseRouteDeconnexion = "PvRouteDeconnexionRestful" ;
			public $NomClasseRouteModifPrefs = "PvRouteModifPrefsRestful" ;
			public $NomClasseRouteChangeMotPasse = "PvRouteChangeMotPasseRestful" ;
			public $NomClasseRouteAjoutMembre = "PvRouteAjoutMembreRestful" ;
			public $NomClasseRouteModifMembre = "PvRouteModifMembreRestful" ;
			public $NomClasseRouteChangeMPMembre = "PvRouteChangeMPMembreRestful" ;
			public $NomClasseRouteSupprMembre = "PvRouteSupprMembreRestful" ;
			public $NomClasseRouteListeMembres = "PvRouteListeMembresRestful" ;
			public $NomClasseRouteAjoutProfil = "PvRouteAjoutProfilRestful" ;
			public $NomClasseRouteModifProfil = "PvRouteModifProfilRestful" ;
			public $NomClasseRouteSupprProfil = "PvRouteSupprProfilRestful" ;
			public $NomClasseRouteListeProfils = "PvRouteListeProfilsRestful" ;
			public $NomClasseRouteAjoutRole = "PvRouteAjoutRoleRestful" ;
			public $NomClasseRouteModifRole = "PvRouteModifRoleRestful" ;
			public $NomClasseRouteSupprRole = "PvRouteSupprRoleRestful" ;
			public $NomClasseRouteListeRoles = "PvRouteListeRolesRestful" ;
			public $NomRouteRecouvreMP = "reinitialise_password" ;
			public $NomRouteConnexion = "connexion" ;
			public $NomRouteInscription = "inscription" ;
			public $NomRouteDeconnexion = "deconnexion" ;
			public $NomRouteImporteMembre = "importe" ;
			public $NomRouteChangeMPMembre = "change_password" ;
			public $NomRouteChangeMotPasse = "change_password" ;
			public $NomRoutesAcces = "acces" ;
			public $NomRoutesMonEspace = "mon_espace" ;
			public $NomRoutesProfils = "profils" ;
			public $NomRoutesRoles = "roles" ;
			public $NomRoutesServeursAD = "serveurs_ad" ;
			public $NomParamMaxElementsCollection = "size" ;
			public $NomParamIndiceDebutCollection = "start" ;
			public $NomParamSensTriCollection = "sort" ;
			public $NomParamColonnesCollection = "fields" ;
			public $InclureMetadatasEntete = true ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CrypteurToken = new OpensslCrypter() ;
			}
			public function & InscritRoute($nom, $cheminRoute, & $route)
			{
				$this->Routes[$nom] = & $route ;
				$route->AdopteApi($nom, $cheminRoute, $this) ;
				return $route ;
			}
			public function & InsereRoute($nom, $cheminRoute, $route)
			{
				return $this->InscritRoute($nom, $cheminRoute, $route) ;
			}
			public function & InsereRouteClasse($nom, $cheminRoute, $nomClasse)
			{
				if(! class_exists($nomClasse))
				{
					die("[InsereRoute] : la classe $nomClasse n'existe pas. Veuillez corriger") ;
				}
				$route = new $nomClasse() ;
				return $this->InscritRoute($nom, $cheminRoute, $route) ;
			}
			public function & InsereRouteParDefaut($route)
			{
				$this->RouteParDefaut = $route ;
				$route->AdopteApi("accueil", "", $this) ;
				return $this->RouteParDefaut ;
			}
			protected function DetecteRouteAppelee()
			{
				$this->MethodeHttp = $this->Requete->Methode ;
				$this->NomRouteAppelee = '' ;
				foreach($this->Routes as $nom => $route)
				{
					preg_match_all("/\{([a-zA-Z0-9\_]+)\}/", $route->CheminRouteApi, $nomsArgsRoute) ;
					$cheminRegexRoute = preg_quote($this->CheminRacineApi, '/')
						.preg_replace("/\\\\{[a-zA-Z0-9\_]+\\\\}/", '([^\/]+)', preg_quote($route->CheminRouteApi, '/')) ;
					// echo $nom." : ".$cheminRegexRoute." !== ".$this->ValeurParamRoute."<br>" ;
					// exit ;
					if(preg_match('/^'.$cheminRegexRoute.'$/', $this->ValeurParamRoute, $valeursArgsRoute) && ($route->MethodeHttp == '' || $route->MethodeHttp == $this->Requete->Methode) && $route->ApprouveAppel($this))
					{
						$this->NomRouteAppelee = $nom ;
						if(count($nomsArgsRoute[1]) > 0)
						{
							for($i=1; $i<count($valeursArgsRoute); $i++)
							{
								$this->ArgsRouteAppelee[$nomsArgsRoute[1][$i - 1]] = $valeursArgsRoute[$i] ;
							}
						}
					}
				}
				if($this->NomRouteAppelee != '')
				{
					$this->RouteAppelee = & $this->Routes[$this->NomRouteAppelee] ;
				}
			}
			public function PossedeRouteAppelee()
			{
				return $this->NomRouteAppelee != '' ;
			}
			public function & BDMembership()
			{
				return $this->Membership->Database ;
			}
			protected function ChargeMembership()
			{
				$nomClasseMembership = $this->NomClasseMembership ;
				if($nomClasseMembership != '')
				{
					if(class_exists($nomClasseMembership))
					{
						$this->Membership = new $nomClasseMembership($this) ;
					}
					else
					{
						die('La classe Membership '.$nomClasseMembership.' n\'est pas declaree') ;
					}
				}
				else
				{
					return ;
				}
				$this->DetecteMembreConnecte() ;
				if($this->InclureRoutesMembership == true)
				{
					$this->DetermineRoutesMembership() ;
				}
			}
			protected function DetecteMembreConnecte()
			{
				$this->Auth->ChargeSession($this) ;
			}
			protected function ChargeRoutesMSConnecte()
			{
				$this->InsereRouteClasse($this->NomRoutesMonEspace."_".$this->NomRouteModifPrefs, $this->NomRoutesMonEspace."/".$this->NomRouteModifPrefs, $this->NomClasseRouteModifPrefs) ;
				$this->InsereRouteClasse($this->NomRoutesMonEspace."_".$this->NomRouteDeconnexion, $this->NomRoutesMonEspace."/".$this->NomRouteDeconnexion, $this->NomClasseRouteDeconnexion) ;
			}
			protected function ChargeRoutesMSNonConnecte()
			{
				$this->RouteRecouvreMP = $this->InsereRouteClasse($this->NomRoutesAcces."_".$this->NomRouteRecouvreMP, $this->NomRoutesAcces."/".$this->NomRouteRecouvreMP, $this->NomClasseRouteRecouvreMP) ;
				$this->RouteInscription = $this->InsereRouteClasse($this->NomRoutesAcces."_".$this->NomRouteInscription, $this->NomRoutesAcces."/".$this->NomRouteInscription, $this->NomClasseRouteInscription) ;
				$this->RouteConnexion = $this->InsereRouteClasse($this->NomRoutesAcces."_".$this->NomRouteConnexion, $this->NomRoutesAcces."/".$this->NomRouteConnexion, $this->NomClasseRouteConnexion) ;
			}
			protected function DetermineRoutesMembership()
			{
				if(! $this->PossedeMembreConnecte())
				{
					$this->ChargeRoutesMSNonConnecte() ;
				}
				else
				{
					$this->ChargeRoutesMSConnecte() ;
				}
			}
			public function PossedeMembreConnecte()
			{
				$ok = 0 ;
				if($this->Membership != null)
				{
					if($this->EstPasNul($this->Membership->MemberLogged))
					{
						if(! $this->Membership->UseGuestMember || $this->Membership->MemberLogged->Id != $this->Membership->GuestMemberId)
						{
							$ok = 1 ;
						}
					}
				}
				return $ok ;
			}
			public function ObtientMembreConnecte()
			{
				$membre = null ;
				if($this->EstPasNul($this->Membership))
				{
					if($this->Membership->MemberLogged != null)
					{
						$membre = $this->Membership->MemberLogged ;
					}
				}
				return $membre ;
			}
			public function EstSuperAdmin($membre)
			{
				if($this->Membership->RootMemberId != "" && $membre->Id == $this->Membership->RootMemberId)
				{
					return 1 ;
				}
				return 0 ;
			}
			public function EstSuperAdminConnecte()
			{
				return $this->MembreSuperAdminConnecte() ;
			}
			public function MembreSuperAdminConnecte()
			{
				if(! $this->PossedeMembreConnecte())
				{
					return 0 ;
				}
				return $this->EstSuperAdmin($this->Membership->MemberLogged) ;
			}
			public function EditMembershipPossible()
			{
				if($this->PossedeMembreConnecte() && count($this->PrivilegesEditMembership) == 0)
					return 1 ;
				return $this->PossedePrivileges($this->PrivilegesEditMembership) ;
			}
			public function EditMembresPossible()
			{
				if($this->PossedeMembreConnecte() && count($this->PrivilegesEditMembres) == 0)
					return 1 ;
				return $this->PossedePrivileges($this->PrivilegesEditMembres) ;
			}
			public function IdMembreConnecte()
			{
				if(! $this->PossedeMembreConnecte())
				{
					return 0 ;
				}
				return $this->Membership->MemberLogged->Id ;
			}
			public function LoginMembreConnecte()
			{
				if(! $this->PossedeMembreConnecte())
				{
					return 0 ;
				}
				return $this->Membership->MemberLogged->Login ;
			}
			public function AttrMembreConnecte($nomAttr)
			{
				if(! $this->PossedeMembreConnecte()|| ! isset($this->Membership->MemberLogged->RawData[$nomAttr]))
				{
					return null ;
				}
				return $this->Membership->MemberLogged->RawData[$nomAttr] ;
			}
			public function TitreProfilConnecte()
			{
				if(! $this->PossedeMembreConnecte()|| ! isset($this->Membership->MemberLogged->Profile))
				{
					return null ;
				}
				return $this->Membership->MemberLogged->Profile->Title ;
			}
			public function PossedeTousPrivileges()
			{
				$ok = 1 ;
				foreach($this->Membership->MemberLogged->Profile->Privileges as $nomRole => $priv)
				{
					if($priv->Enabled == 0)
					{
						$ok = 0 ;
						break ;
					}
				}
				return $ok ;
			}
			public function PossedePrivilege($nomRole, $strict=0)
			{
				return $this->PossedePrivileges(array($nomRole), $strict) ;
			}
			public function PossedePrivileges($privileges=array(), $strict=0)
			{
				$ok = 0 ;
				$privilegesSpec = $privileges ;
				if($strict == 0 && count($this->PrivilegesPassePartout) > 0)
					array_splice($privileges, 0, 0, $this->PrivilegesPassePartout) ;
				if($this->PossedeMembreConnecte() == 0)
				{
					return 0 ;
				}
				if(count($privilegesSpec) == 0)
				{
					return 1 ;
				}
				$membre = $this->Membership->MemberLogged ;
				if(count($privileges) > 0)
				{
					foreach($privileges as $i => $nomRole)
					{
						if(isset($membre->Profile->Privileges[$nomRole]))
						{
							if($membre->Profile->Privileges[$nomRole]->Enabled)
							{
								$ok = 1 ;
								break ;
							}
						}
					}
				}
				return $ok ;
			}
			protected function PrepareExecution()
			{
			}
			protected function TermineExecution()
			{
			}
			public function ChargeConfig()
			{
			}
			protected function ChargeRoutes()
			{
			}
			public function SuccesReponse()
			{
				return $this->Reponse->EstSucces() ;
			}
			public function EchecReponse()
			{
				return $this->Reponse->EstEchec() ;
			}
			protected function DetermineEnvironnement()
			{
				if($this->NomClasseAuth != '')
				{
					$nomClasse = $this->NomClasseAuth ;
					$this->Auth = new $nomClasse() ;
				}
				$this->Requete = new PvRequeteRestful() ;
				$this->Reponse = new PvReponseRestful() ;
				$this->ValeurParamRoute = $this->Requete->CheminRelatifRoute ;
				if($this->ValeurParamRoute != '' && $this->ValeurParamRoute[strlen($this->ValeurParamRoute) - 1] == "/")
				{
					$this->ValeurParamRoute = substr($this->ValeurParamRoute, 0, strlen($this->ValeurParamRoute) - 1) ;
				}
			}
			public function Execute()
			{
				$this->DetermineEnvironnement() ;
				$this->ChargeMembership() ;
				$this->ChargeRoutes() ;
				$this->PrepareExecution() ;
				$this->DetecteRouteAppelee() ;
				if($this->PossedeRouteAppelee())
				{
					if($this->RouteAppelee->EstAccessible())
					{
						$this->RouteAppelee->Execute() ;
					}
					else
					{
						$this->Reponse->ConfirmeNonAutoris() ;
					}
				}
				else
				{
					if($this->EstPasNul($this->RouteParDefaut))
					{
						$this->RouteParDefaut->Execute() ;
					}
					else
					{
						$this->Reponse->ConfirmeNonTrouve() ;
					}
				}
				$this->Reponse->EnvoieRendu($this) ;
				$this->TermineExecution() ;
				$this->Requete = null ;
				$this->Reponse = null ;
				exit ;
			}
			public function ArgRouteAppelee($nom, $valeurDefaut=null)
			{
				return (isset($this->ArgsRouteAppelee[$nom])) ? $this->ArgsRouteAppelee[$nom] : $valeurDefaut ;
			}
			public function ArgRoute($nom, $valeurDefaut=null)
			{
				return (isset($this->ArgsRouteAppelee[$nom])) ? $this->ArgsRouteAppelee[$nom] : $valeurDefaut ;
			}
		}
	}
	
?>