<?php
	
	if(! defined('PV_FILTRE_RESTFUL'))
	{
		define('PV_FILTRE_RESTFUL', 1) ;
		
		class PvFiltreBaseRestful extends PvObjet
		{
			public $Obligatoire = 0 ;
			public $RouteParent ;
			public $ApiParent ;
			public $ApplicationParent ;
			public $NomElementRoute = "" ;
			public $NomElementApi = "" ;
			public $TypeLiaisonParametre = "" ;
			public $Role = "base" ;
			public $Liaison ;
			public $Composant ;
			public $Libelle = "" ;
			public $CheminIcone = "" ;
			public $NomClasseCSS = "" ;
			public $NomClasseCSSIcone = "" ;
			public $EspaceReserve = "" ;
			public $NomParametreLie = "" ;
			public $NomParametreDonnees = "" ;
			public $AliasParametreDonnees = "" ;
			public $NomClasseLiaison ;
			public $ExpressionDonnees = "" ;
			public $NomColonneLiee = "" ;
			public $ExpressionColonneLiee = "" ;
			public $NePasInclureSiVide = 1 ;
			public $ValeurParDefaut ;
			public $ValeurVide ;
			public $ValeurParametre ;
			public $ValeurBrute = "" ;
			public $DejaLie = 0 ;
			public $Invisible = 0 ;
			public $EstEtiquette = 0 ;
			public $LectureSeule = 0 ;
			public $NePasLierColonne = 0 ;
			public $NePasLireColonne = 0 ;
			public $NePasLierParametre = 0 ;
			public $NePasIntegrerParametre = 0 ;
			public $AppliquerCorrecteurValeur = 1 ;
			public $CorrecteurValeur ;
			public $FormatteurEtiquette ;
			public function ImpressionEnCours()
			{
				return $this->EstPasNul($this->ApiParent) && $this->ApiParent->ImpressionEnCours() ;
			}
			public function InserePrefxErr($contenu)
			{
				$this->InserePrefixeLib(new PvMarqErrFiltre($contenu)) ;
			}
			public function InserePrefxNotice($contenu)
			{
				$this->InserePrefixeLib(new PvMarqNoticeFiltre($contenu)) ;
			}
			public function InsereSuffxErr($contenu)
			{
				$this->InsereSuffixeLib(new PvMarqErrFiltre($contenu)) ;
			}
			public function InsereSuffxNotice($contenu)
			{
				$this->InsereSuffixeLib(new PvMarqNoticeFiltre($contenu)) ;
			}
			public function InsereSuffxLib($val)
			{
				$this->InsereSuffixeLib($val) ;
			}
			public function InserePrefxLib($val)
			{
				$this->InserePrefixeLib($val) ;
			}
			public function InserePrefixeLib($val)
			{
				$this->PrefixesLibelle[] = $val ;
			}
			public function InsereSuffixeLib($val)
			{
				$this->SuffixesLibelle[] = $val ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CorrecteurValeur = new PvCorrecteurValeurFiltreBase() ;
				$this->FormatteurEtiquette = new PvFormatteurEtiquetteFiltre() ;
			}
			public function AdopteApi($nom, & $script)
			{
				$this->ApiParent = & $script->ApiParent ;
				$this->ApplicationParent = & $script->ApplicationParent ;
				$this->NomElementApi = $nom ;
			}
			public function AdopteRoute($nom, & $script)
			{
				$this->RouteParent = & $script ;
				$this->ApiParent = & $script->ApiParent ;
				$this->ApplicationParent = & $script->ApplicationParent ;
				$this->NomElementRoute = $nom ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeComposant() ;
			}
			protected function CorrigeConfig()
			{
				if($this->NomParametreDonnees == '' && $this->NomElementRoute != '')
					$this->NomParametreDonnees = $this->NomElementRoute ;
				if($this->NomParametreLie == '' && $this->NomElementRoute != '')
					$this->NomParametreLie = $this->NomElementRoute ;
			}
			protected function ChargeComposant()
			{
			}
			public function NePasInclure()
			{
				if($this->NePasInclureSiVide && ! $this->Obligatoire)
				{
					return ($this->ValeurVide == $this->ValeurParametre) ;
				}
				return 0 ;
			}
			public function RenduPossible()
			{
				return (! $this->Invisible && ($this->TypeLiaisonParametre == 'get' or $this->TypeLiaisonParametre == 'post')) ? 1 : 0 ;
			}
			public function ObtientValeurParametre()
			{
				return "" ;
			}
			public function CorrigeNomParametreLie()
			{
				if($this->NomParametreLie == '')
				{
					if($this->NomElementRoute != '')
						$this->NomParametreLie = $this->NomElementRoute ;
					else
						$this->NomParametreLie = $this->IDInstanceCalc ;
				}
			}
			public function ObtientLibelle()
			{
				$libelle = $this->Libelle ;
				if($libelle == '')
				{
					$libelle = $this->NomElementRoute ;
				}
				return $libelle ;
			}
			public function Lie()
			{
				$this->CorrigeConfig() ;
				if($this->DejaLie == 1)
				{
					return $this->ValeurParametre ;
				}
				$this->ValeurParametre = $this->ValeurParDefaut ;
				// echo $this->NomParametreDonnees ;
				if($this->Invisible == 1 || $this->NePasLierParametre == 1)
				{
					return $this->ValeurParametre ;
				}
				$valeurParametre = $this->ObtientValeurParametre() ;
				if($this->AppliquerCorrecteurValeur)
				{
					$valeurParametre = $this->CorrecteurValeur->Applique($valeurParametre, $this) ;
				}
				if($valeurParametre !== $this->ValeurVide || $this->ValeurVide !== null)
				{
					$this->ValeurParametre = $valeurParametre ;
				}
				$this->DejaLie = 1 ;
				return $this->ValeurParametre ;
			}
			public function DefinitColLiee($nomCol)
			{
				$this->NomColonneLiee = $nomCol ;
				$this->NomParametreDonnees = $nomCol ;
			}
			public function ObtientNomComposant()
			{
				$this->CorrigeNomParametreLie() ;
				$nomComposant = $this->NomParametreLie ;
				return $nomComposant ;
			}
			public function ObtientIDElementHtmlComposant()
			{
				if($this->EstNul($this->Composant))
				{
					$this->DeclareComposant($this->NomClasseComposant) ;
				}
				if($this->EstNul($this->Composant))
					return "" ;
				$iDInstanceCalc = $this->Composant->IDInstanceCalc ;
				return $iDInstanceCalc ;
			}
			public function ObtientIDComposant()
			{
				return $this->ObtientIDElementHtmlComposant() ;
			}
			public function FormatTexte()
			{
				$valTemp = $this->Lie() ;
				return $valTemp ;
			}
			public function LiePourRendu()
			{
				$valeur = $this->Lie() ;
				if($valeur !== $this->ValeurVide && $this->EstPasNul($this->CorrecteurValeur))
				{
					$valeur = $this->CorrecteurValeur->AppliquePourRendu($valeur, $this) ;
				}
				return $valeur ;
			}
			public function LiePourTraitement()
			{
				$valeur = $this->Lie() ;
				if($valeur !== $this->ValeurVide && $this->EstPasNul($this->CorrecteurValeur))
				{
					$valeur = $this->CorrecteurValeur->AppliquePourTraitement($valeur, $this) ;
				}
				return $valeur ;
			}
		}
		class PvFiltreFixeRestful extends PvFiltreBaseRestful
		{
			public $TypeLiaisonParametre = "hidden" ;
			public $Role = "fixe" ;
			public function NePasInclure()
			{
				return 0 ;
			}
			public function ObtientValeurParametre()
			{
				return $this->ValeurParDefaut ;
			}
		}
		class PvFiltreCacheRestful extends PvFiltreFixeRestful
		{
		}
		class PvFiltreRefRestful extends PvFiltreBaseRestful
		{
			public $Role = "ref" ;
			public $TypeLiaisonParametre = "hidden" ;
			public $Source = null ;
			public function NePasInclure()
			{
				if($this->EstPasNul($this->Source))
				{
					return $this->Source->NePasInclure() ;
				}
				return 1 ;
			}
			public function ObtientValeurParametre()
			{
				if($this->EstPasNul($this->Source))
				{
					return $this->Source->Lie() ;
				}
				return $this->ValeurVide ;
			}
		
		}
		class PvFiltreCookieRestful extends PvFiltreBaseRestful
		{
			public $Role = "cookie" ;
			public $TypeLiaisonParametre = "cookie" ;
			public function ObtientValeurParametre()
			{
				return (isset($_COOKIE[$this->NomParametreLie])) ? $_COOKIE[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreSessionRestful extends PvFiltreBaseRestful
		{
			public $Role = "session" ;
			public $TypeLiaisonParametre = "session" ;
			public function ObtientValeurParametre()
			{
				return (isset($_SESSION[$this->NomParametreLie])) ? $_SESSION[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreMembreConnecteRestful extends PvFiltreBaseRestful
		{
			public $Role = "membre_connecte" ;
			public $TypeLiaisonParametre = "membre_connecte" ;
			public function ObtientValeurParametre()
			{
				// print_r($this->ApiParent->Membership->MemberLogged->RawData) ;
				if($this->EstNul($this->ApiParent) || $this->EstNul($this->ApiParent->Membership) || $this->EstNul($this->ApiParent->Membership->MemberLogged))
				{
					return $this->ValeurVide ;
				}
				if(isset($this->ApiParent->Membership->MemberLogged->RawData[$this->NomParametreLie]))
				{
					// print "ssds ".$this->ApiParent->Membership->MemberLogged->RawData[$this->NomParametreLie] ;
					return $this->ApiParent->Membership->MemberLogged->RawData[$this->NomParametreLie] ;
				}
			}
		}
		class PvFiltreHttpRequestRestful extends PvFiltreBaseRestful
		{
			public $Role = "request" ;
			public $TypeFormatRegexp ;
			public $FormatRegexp ;
			public $MessageErreurRegexp ;
			public $TypeLiaisonParametre = "request" ;
			public $AccepteTagsHtml = 1 ;
			public $AccepteTagsSuspicieux = 0 ;
			public $ValeurBruteNonCorrigee = false ;
			protected function EnleveTagsHtml($valeur)
			{
				/*
				$tag = new HtmlTag() ;
				$tag->LoadFromText($valeur) ;
				return $tag->Preview ;
				$result = str_get_html($valeur)->plaintext ;
				return $result ;
				*/
				return strip_tags($valeur) ;
			}
			protected function EnleveTagsSuspicieux($valeur)
			{
				/*
				$tag = new HtmlTag() ;
				$tag->SafeMode = 1 ;
				$tag->LoadFromText($valeur) ;
				return $tag->GetContent(true) ;
				*/
				$parser = new SafeHTML();
				$result = $parser->parse($valeur);
				return $result ;
			}
			protected function CorrigeValeurBrute($valeur)
			{
				if(! is_array($valeur) && ! is_scalar($valeur))
				{
					return $this->ValeurParDefaut ;
				}
				if(is_array($valeur))
				{
					$resultat = array() ;
					foreach($valeur as $cle => $sousVal)
					{
						$resultat[$cle] = $this->CorrigeValeurBrute($sousVal) ;
					}
				}
				else
				{
					if($this->AccepteTagsHtml == 0)
					{
						$resultat = $this->EnleveTagsHtml($valeur) ;
					}
					elseif($this->AccepteTagsSuspicieux == 0)
					{
						$resultat = $this->EnleveTagsSuspicieux($valeur) ;
					}
					else
					{
						$resultat = $valeur ;
					}
				}
				return $resultat ;
			}
			protected function ExtraitValeurFormattee($valeur)
			{
				$resultat = "" ;
				if(is_array($valeur))
				{
					$resultat = join(";", $valeur) ;
				}
				else
				{
					$resultat = $valeur ;
				}
				return $resultat ;
			}
			protected function CalculeValeurBruteNonCorrigee()
			{
				$this->ValeurBruteNonCorrigee = (array_key_exists($this->NomParametreLie, $_REQUEST)) ? $_REQUEST[$this->NomParametreLie] : $this->ValeurVide ;
			}
			public function ObtientValeurParametre()
			{
				$this->CalculeValeurBruteNonCorrigee() ;
				$this->ValeurBrute = $this->CorrigeValeurBrute($this->ValeurBruteNonCorrigee) ;
				return $this->ExtraitValeurFormattee($this->ValeurBrute) ;
			}
			public function FormatTexte()
			{
				$valTemp = $this->Lie() ;
				if($this->AccepteTagsHtml)
				{
					$valTemp = strip_tags($valTemp) ;
					$valTemp = slugify($valTemp, false) ;
				}
				return $valTemp ;
			}
		}
		class PvFiltreHttpGetRestful extends PvFiltreHttpRequestRestful
		{
			public $Role = "get" ;
			public $TypeLiaisonParametre = "get" ;
			protected function CalculeValeurBruteNonCorrigee()
			{
				$this->ValeurBruteNonCorrigee = (array_key_exists($this->NomParametreLie, $_GET)) ? $_GET[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreHttpPostRestful extends PvFiltreHttpRequestRestful
		{
			public $Role = "post" ;
			public $TypeLiaisonParametre = "post" ;
			protected function CalculeValeurBruteNonCorrigee()
			{
				$this->ValeurBruteNonCorrigee = (array_key_exists($this->NomParametreLie, $_POST)) ? $_POST[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreHttpUploadRestful extends PvFiltreBaseRestful
		{
			public $Role = "http_upload" ;
			public $TypeLiaisonParametre = "post" ;
			public $InfosTelechargement = array() ;
			public $CheminDossier = "." ;
			public $CheminFichierDest = "" ;
			public $CheminFichierSrc = "" ;
			public $DejaTelecharge = 0 ;
			public $NettoyerCaractsFichier = 1 ;
			public $ExtensionsAcceptees = array() ;
			public $ExtensionsRejetees = array('pl', 'cgi', 'html', 'xhtml', 'html5', 'html4', 'xml', 'xss', 'rss', 'xlt', 'php', 'phtml', 'inc', 'js', 'vbs', 'py', 'bat', 'sh', 'cmd', 'exe', 'msi', 'bin', 'apk', 'com', 'command', 'cpl', 'action', 'csh', 'gadget', 'inf1', 'ins', 'inx', 'ipa', 'isu', 'job', 'jse', 'ksh', 'lnk', 'msc', 'msp', 'mst', 'osx', 'out', 'paf', 'pif', 'prg', 'ps1', 'reg', 'rgs', 'run', 'scr', 'sct', 'shb', 'shs', 'u3p', 'vb', 'vbe', 'vbs', 'vbscript', 'workflow', 'ws', 'wsf', 'wsh') ;
			public $CheminFichierClient = "" ;
			public $CodeErreurTelechargement = "0" ;
			public $CheminFichierSoumis = "" ;
			public $NomFichierSelect = "" ;
			public $ExtFichierSelect = "" ;
			public $NomEltCoteSrv = "CoteSrv_" ;
			public $LibelleErreurTelecharg = '' ;
			public $FormatFichierTelech = '' ;
			public $SourceTelechargement = '' ;
			public $CodeErreurMauvaiseExt = '501' ;
			public $LibelleErreurAucunFich = 'Aucun fichier n\'a &eacute;t&eacute; soumis' ;
			public $LibelleErreurMauvaiseExt = 'Mauvais format pour le fichier soumis.' ;
			public $CodeErreurDeplFicTelecharg = '502' ;
			public $LibelleErreurDeplFicTelecharg = 'Le deplacement du fichier sur le serveur a &eacute;chou&eacute;. V&eacute;rifiez que vous avez les droits en ecriture.' ;
			public $CodeErreurFicSoumisInexist = '503' ;
			public $ToujoursRenseignerFichier = 0 ;
			public $NePasInclureSiVide = 0 ;
			public $LibelleErreurFicSoumisInexist = 'Le fichier soumis n\'existe pas.' ;
			public function AccepteVidsSeulem()
			{
				$this->ExtensionsAcceptees = array('mp4', 'avi', 'mpeg', 'flv', 'mkv', '3gp') ;
			}
			public function AccepteImgsSeulem()
			{
				$this->ExtensionsAcceptees = array('jpg', 'jpeg', 'png', 'gif', 'svg') ;
			}
			public function AccepteDocsSeulem()
			{
				$this->ExtensionsAcceptees = array('doc', 'docx', 'xls', 'xlsx', 'pdf', 'odt') ;
			}
			public function AccepteTxtsSeulem()
			{
				$this->ExtensionsAcceptees = array('txt', 'log') ;
			}
			public function TelechargementSoumis()
			{
				return $this->SourceTelechargement == 'files' ? 1 : 0 ;
			}
			protected function NettoieCaractsFichier($nomFich)
			{
				$result = preg_replace('/[^a-z0-9\_\.]/i', '_', $nomFich) ;
				return $result ;
			}
			public function ObtientValeurParametre()
			{
				if($this->DejaTelecharge == 1)
				{
					return $this->CheminFichierDest ;
				}
				if(! isset($_FILES[$this->NomParametreLie]) && ! isset($_POST[$this->NomEltCoteSrv.$this->NomParametreLie]))
				{
					return $this->ValeurVide ;
				}
				if(isset($_FILES[$this->NomParametreLie]) && $_FILES[$this->NomParametreLie]["error"] != 4)
				{
					$this->SourceTelechargement = 'files' ;
					$this->InfosTelechargement = $_FILES[$this->NomParametreLie] ;
					$this->CheminFichierSrc = $this->InfosTelechargement["tmp_name"] ;
					$this->CheminFichierClient = $this->InfosTelechargement["name"] ;
					$infosFichier = pathinfo($this->CheminFichierClient) ;
					$this->ExtFichierSelect = (isset($infosFichier["extension"])) ? $infosFichier["extension"] : "" ;
					$this->NomFichierSelect = $infosFichier["basename"] ;
					if($this->ExtFichierSelect != '')
					{
						$this->NomFichierSelect = substr($this->NomFichierSelect, 0, strlen($this->NomFichierSelect) - strlen(".".$infosFichier["extension"])) ;
					}
					if($this->NettoyerCaractsFichier == 1)
					{
						$ancFich = $this->NomFichierSelect ;
						$this->NomFichierSelect = $this->NettoieCaractsFichier($this->NomFichierSelect) ;
					}
					if($this->FormatFichierTelech != '')
					{
						$this->NomFichierSelect = _parse_pattern(
							$this->FormatFichierTelech,
							array(
								"Cle" => uniqid(),
								"NombreAleatoire" => rand(0, 10000),
								"NomFichier" => $this->NomFichierSelect,
								"Timestamp" => date("U"),
								"Date" => date("YmdHis")
							)
						) ;
						// print $this->NomFichierSelect ;
					}
					if($this->ExtFichierSelect != "")
					{
						$this->NomFichierSelect .= '.'.$this->ExtFichierSelect ;
					}
				}
				else
				{
					$this->SourceTelechargement = 'post' ;
					if(isset($_POST[$this->NomEltCoteSrv.$this->NomParametreLie]))
					{
						$this->CheminFichierSoumis = $_POST[$this->NomEltCoteSrv.$this->NomParametreLie] ;
					}
					if($this->CheminFichierSoumis != "")
					{
						$cheminFichierSoumis = realpath(dirname($_SERVER["SCRIPT_FILENAME"])."/".$this->CheminFichierSoumis) ;
						$cheminDossier = realpath(dirname($_SERVER["SCRIPT_FILENAME"])."/".$this->CheminDossier) ;
						if($this->CheminFichierSoumis != '' && file_exists($cheminFichierSoumis))
						{
							$infosFichier = pathinfo($cheminFichierSoumis) ;
							$this->NomFichierSelect = str_replace("\\", "/", substr($cheminFichierSoumis, strlen($cheminDossier) + 1)) ;
							$this->ExtFichierSelect = (isset($infosFichier["extension"])) ? $infosFichier["extension"] : "" ;
							// echo $this->NomFichierSelect.' kkk <br>' ;
						}
					}
					else
					{
						$this->NomFichierSelect = "" ;
						$this->ExtFichierSelect = "" ;
					}
					// print_r("Doss : ".$this->CheminDossier) ;
					// print_r($infosFichier) ;
				}
				if((count($this->ExtensionsAcceptees) > 0 && ! in_array(strtolower($this->ExtFichierSelect), array_map("strtolower", $this->ExtensionsAcceptees))) || (count($this->ExtensionsRejetees) > 0 && in_array(strtolower($this->ExtFichierSelect), array_map("strtolower", $this->ExtensionsRejetees))))
				{
					$this->CodeErreurTelechargement = $this->CodeErreurMauvaiseExt ;
					$this->LibelleErreurTelecharg = $this->LibelleErreurMauvaiseExt ;
					return $this->ValeurVide ;
				}
				$this->CheminFichierDest = $this->ValeurVide ;
				if($this->NomFichierSelect != "")
				{
					$this->CheminFichierDest = $this->CheminDossier. "/" .$this->NomFichierSelect ;
				}
				if($this->SourceTelechargement == 'files')
				{
					// echo $this->CheminFichierSrc.' '.$this->CheminFichierDest.'<br>' ;
					$ok = move_uploaded_file($this->CheminFichierSrc, $this->CheminFichierDest) ;
					if(! $ok)
					{
						$this->CodeErreurTelechargement = $this->CodeErreurDeplFicTelecharg ;
						$this->LibelleErreurTelecharg = $this->LibelleErreurDeplFicTelecharg ;
						return $this->ValeurVide ;
					}
				}
				else
				{
					if($this->ToujoursRenseignerFichier == 1 && $this->CheminFichierDest == "")
					{
						$this->CodeErreurTelechargement = $this->CodeErreurFicSoumisInexist ;
						$this->LibelleErreurTelecharg = $this->LibelleErreurFicSoumisInexist ;
						return $this->ValeurVide ;
					}
				}
				$this->DejaTelecharge = 1 ;
				// echo $this->NomParametreLie.' '.$this->CheminFichierDest.'<br>' ;
				return $this->CheminFichierDest ;
			}
		}
		
	}
	
?>