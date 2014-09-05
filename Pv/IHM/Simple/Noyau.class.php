<?php
	
	if(! defined('PV_NOYAU_SIMPLE_IHM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../../Noyau.class.php" ;
		}
		if(! defined('PV_SCRIPT_IHM'))
		{
			include dirname(__FILE__)."/../Script.class.php" ;
		}
		if(! defined('PV_ZONE_IHM'))
		{
			include dirname(__FILE__)."/../Zone.class.php" ;
		}
		if(! defined('HTML_TAG_INC'))
		{
			include dirname(__FILE__)."/../../../misc/HTMLTag.class.php" ;
		}
		define('PV_NOYAU_SIMPLE_IHM', 1) ;
		
		class PvExceptionSimpleBase
		{
			public $Code = "" ;
			public $Message = "" ;
			public $Parametres = array() ;
			public $NumeroLigne = "" ;
			public $CheminFichier = "" ;
			public $ExceptionInterne = null ;
		}
		
		class PvConfigFormatteurColonneLien
		{
			public $FormatLibelle ;
			public $EncodeHtmlLibelle ;
			public $FormatURL ;
			public $FormatCheminIcone ;
			public $ClasseCSS ;
			public $ChaineAttributs ;
			public $InclureIcone = 1 ;
			public $HauteurIcone = "15" ;
			public $NomDonneesValid = "" ;
			public $Privileges = array() ;
			public $ValeurVraiValid = 1 ;
			public $Visible = 1 ;
			public function Accepte($donnees)
			{
				return ($this->RenduPossible() && ($this->NomDonneesValid == "" || (isset($donnees[$this->NomDonneesValid]) && $donnees[$this->NomDonneesValid] == $this->ValeurVraiValid))) ? 1 : 0 ;
			}
			protected function RenduIcone($donnees, $donneesUrl)
			{
				$ctn = '' ;
				if(! $this->InclureIcone || $this->FormatCheminIcone == "")
				{
					return $ctn ;
				}
				$cheminIcone = _parse_pattern($this->FormatCheminIcone, $donneesUrl) ;
				$ctn .= '<img src="'.$cheminIcone.'" height="'.$this->HauteurIcone.'" border="0" /> ' ;
				return $ctn ;
			}
			public function RenduPossible()
			{
				return ($this->Visible == 1) ;
			}
			public function Rendu($donnees)
			{
				if(! $this->RenduPossible())
				{
					return '' ;
				}
				return $this->RenduBrut($donnees) ;
			}
			protected function RenduBrut($donnees)
			{
				$ctn = '' ;
				$donneesUrl = array_map("urlencode", $donnees) ;
				$href = _parse_pattern($this->FormatURL, $donneesUrl) ;
				$libelle = _parse_pattern($this->FormatLibelle, $donnees) ;
				$ctn .= '<a href="'.$href.'"' ;
				if($this->ChaineAttributs != '')
				{
					$ctn .= ' '.$this->ChaineAttributs ;
				}
				if($this->ClasseCSS != '')
				{
					$ctn .= ' class="'.$this->ClasseCSS.'"' ;
				}
				$ctn .= '>' ;
				$ctn .= $this->RenduIcone($donnees, $donneesUrl) ;
				if($this->EncodeHtmlLibelle)
				{
					$libelle = htmlentities($libelle) ;
				}
				$ctn .= $libelle ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
		}
		
		class PvFormatteurColonneDonnees extends PvObjet
		{
			public function Encode(& $script, $colonne, $ligne)
			{
				if(isset($ligne[$colonne->NomDonnees]))
					return $ligne[$colonne->NomDonnees] ;
				return '' ;
			}
			public function ObtientDonnees($colonne, $ligne)
			{
				$donnees = array_merge($ligne, array('self' => (isset($ligne[$colonne->NomDonnees])) ? $ligne[$colonne->NomDonnees] : '')) ;
				return $donnees ;
			}
		}
		class PvFormatteurColonneBooleen extends PvFormatteurColonneDonnees
		{
			public $ValeursPositivesAcceptees = array("1", "true", "vrai") ;
			public $CasseInsensitive = 1 ;
			public $ValeurPositive = "Oui" ;
			public $ValeurNegative = "Non" ;
			public $StyleValPositive = "color:green" ;
			public $StyleValNegative = "color:red" ;
			public $NomClasseCSSValPositive = "" ;
			public $NomClasseCSSValNegative = "" ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$valeurEntree = $ligne[$colonne->NomDonnees] ;
				if($this->CasseInsensitive)
				{
					$valeurEntree = strtolower($ligne[$colonne->NomDonnees]) ;
				}
				return (in_array($valeurEntree, $this->ValeursPositivesAcceptees)) ? $this->RenduValPositive() : $this->RenduValNegative() ;
			}
			protected function RenduValPositive()
			{
				$ctn = '' ;
				$ctn .= '<span' ;
				if($this->StyleValPositive != '')
					$ctn .= ' style="'.$this->StyleValPositive.'"' ;
				if($this->NomClasseCSSValPositive != '')
					$ctn .= ' class="'.$this->NomClasseCSSValPositive.'"' ;
				$ctn .= '>' ;
				$ctn .= $this->ValeurPositive ;
				$ctn .= '</span>' ;
				return $ctn ;
			}
			protected function RenduValNegative()
			{
				$ctn = '' ;
				$ctn .= '<span' ;
				if($this->StyleValNegative != '')
					$ctn .= ' style="'.$this->StyleValNegative.'"' ;
				if($this->NomClasseCSSValNegative != '')
					$ctn .= ' class="'.$this->NomClasseCSSValNegative.'"' ;
				$ctn .= '>' ;
				$ctn .= $this->ValeurNegative ;
				$ctn .= '</span>' ;
				return $ctn ;
			}
		}
		class PvFormatteurColonneModeleHtml extends PvFormatteurColonneDonnees
		{
			public $ModeleHtml = "" ;
			public $EncodeValeursHtml = array() ;
			public $EncodeValeursUrl = array() ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$donnees = $this->ObtientDonnees($colonne, $ligne) ;
				if(count($this->EncodeValeursHtml))
				{
					$donnees = array_merge(array_map("htmlentities", array_extract_value_for_keys($donnees, $this->EncodeValeursHtml))) ;
				}
				if(count($this->EncodeValeursUrl))
				{
					$donnees = array_merge(array_map("urlencode", array_extract_value_for_keys($donnees, $this->EncodeValeursUrl))) ;
				}
				return _parse_pattern($this->ModeleHtml, $donnees) ;
			}
		}
		class PvFormatteurColonneLiens extends PvFormatteurColonneDonnees
		{
			public $Liens = array() ;
			public $InclureIcone = 0 ;
			public $SeparateurLiens = "&nbsp;&nbsp;" ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$ctn = '' ;
				foreach($this->Liens as $i => $lien)
				{
					$donnees = $this->ObtientDonnees($colonne, $ligne) ;
					if(! $lien->Accepte($donnees))
					{
						continue ;
					}
					if($ctn != '')
					{
						$ctn .= $this->SeparateurLiens ;
					}
					if($this->InclureIcone)
						$lien->InclureIcone = $this->InclureIcone ;
					$ctn .= $lien->Rendu($donnees) ;
				}
				return $ctn ;
			}
		}

		class PvExtracteurValeursDonneesBase
		{
			public $AccepteValeursVide = 0 ;
			public $ChainesCaractSeulement = 1 ;
			public function Execute($texte, & $composant)
			{
				if($this->AccepteValeursVide == 0 && $texte == '')
					return array() ;
				$valeursBrutes = $this->DecodeValeurs($texte, $composant) ;
				if(! is_array($valeursBrutes))
					return array() ;
				$valeurs = $this->NettoieValeurs($valeursBrutes) ;
				return $valeurs ;
			}
			protected function DecodeValeurs($texte, & $composant)
			{
				return array() ;
			}
			protected function NettoieValeurs($valeursBrutes)
			{
				$valeurs = array() ;
				foreach($valeursBrutes as $nom => $valeur)
				{
					if(! is_scalar($valeur) && $this->ChainesCaractSeulement)
					{
						continue ;
					}
					$valeurs[$nom] = $valeur ;
				}
				return $valeurs ;
			}
		}
		class PvExtracteurValeursJson extends PvExtracteurValeursDonneesBase
		{
			protected function DecodeValeurs($texte, & $composant)
			{
				$valeurs = svc_json_decode($texte) ;
			}
		}
		class PvExtracteurValeursChaineHttp extends PvExtracteurValeursDonneesBase
		{
			protected function DecodeValeurs($texte, & $composant)
			{
				parse_str($texte, $valeurs) ;
				return $valeurs ;
			}
		}
		class PvExtracteurIntroDonnees extends PvExtracteurValeursDonneesBase
		{
			public $MaxMots = 255 ;
			public $ExprPlus = "..." ;
			public $AccepteValeursVide = 1 ;
			protected function DecodeValeurs($texte, & $composant)
			{
				$valeurs = array("intro" => intro($texte, $this->MaxMots, $this->ExprPlus)) ;
				return $valeurs ;
			}
		}		
		class PvDefinitionColonneDonnees extends PvObjet
		{
			public $TriPrealable = 0 ;
			public $OrientationTri = "asc" ;
			public $NomDonneesTri = "" ;
			public $AliasDonneesTri = "" ;
			public $AlignElement ;
			public $AlignVElement = "top" ;
			public $HauteurElement ;
			public $Largeur ;
			public $HauteurEntete ;
			public $AlignEntete ;
			public $AlignVEntete = "top" ;
			public $NomDonnees ;
			public $AliasDonnees ;
			public $Libelle ;
			public $Formatteur = null ;
			public $TriPossible = 1 ;
			public $EncodeHtmlValeur = 1 ;
			public $ExtracteurValeur = null ;
			public $PrefixeValeursExtraites = "" ;
			public $Visible = 1 ;
			public $ExporterDonnees = 1 ;
			public $ExporterDonneesObligatoire = 0 ;
			public $FormatValeur ;
			public $RenvoyerValeurVide = 1 ;
			public function DeclareFormatteurLiens()
			{
				$this->Formatteur = new PvFormatteurColonneLiens() ;
			}
			public function DeclareFormatteurBool()
			{
				$this->Formatteur = new PvFormatteurColonneBooleen() ;
			}
			public function ObtientPrefixeValsExtraites()
			{
				$prefixe = $this->PrefixeValeursExtraites ;
				if($prefixe == '')
				{
					$prefixe = $this->NomDonnees ;
				}
				return $prefixe ;
			}
			public function PeutExporterDonnees()
			{
				return (($this->Visible == 1 && $this->ExporterDonnees == 1) || $this->ExporterDonneesObligatoire) ? 1 : 0 ;
			}
			public function ObtientLibelle()
			{
				$libelle = $this->NomDonnees ;
				if($this->Libelle != "")
				{
					$libelle = $this->Libelle ;
				}
				return $libelle ;
			}
			public function FormatteValeur(& $script, $ligne)
			{
				if($this->EstNul($this->Formatteur))
				{
					return $this->FormatteValeurInt($script, $ligne) ;
				}
				return $this->Formatteur->Encode($script, $this, $ligne) ;
			}
			protected function FormatteValeurInt(& $script, $ligne)
			{
				$val = "" ;
				if($this->NomDonnees != '')
					$val = $this->EncodeValeur($ligne[$this->NomDonnees]) ;
				if($val == "" && $this->RenvoyerValeurVide)
					return $val ;
				if($this->FormatValeur != '')
				{
					$val = str_ireplace(array('${self}', '${luimeme}', '${soi}'), $val, $this->FormatValeur) ;
				}
				return $val ;
			}
			public function EncodeValeur($valeur)
			{
				$resultat = ($this->EncodeHtmlValeur) ? htmlentities($valeur) : $valeur ;
				return $resultat ;
			}
		}
		
		class PvMarqueFiltreDonneesBase
		{
			public $Contenu ;
			public $CouleurPolice ;
			public $CouleurArPl ;
			public function __construct($contenu)
			{
				$this->Contenu = $contenu ;
			}
		}
		class PvMarqErrFiltreDonnees extends PvMarqueFiltreDonneesBase
		{
			public $CouleurPolice = "red" ;
		}
		class PvMarqNoticeFiltreDonnees extends PvMarqueFiltreDonneesBase
		{
			public $CouleurPolice = "blue" ;
		}
		
		class PvFiltreDonneesBase extends PvObjet
		{
			public $PrefixesLibelle = array() ;
			public $SuffixesLibelle = array() ;
			public $Obligatoire = 0 ;
			public $ScriptParent = null ;
			public $ZoneParent = null ;
			public $ApplicationParent = null ;
			public $NomElementScript = "" ;
			public $TypeLiaisonParametre = "" ;
			public $Liaison = null ;
			public $Composant = null ;
			public $Libelle = "" ;
			public $NomClasseComposant = "PvZoneTexteHtml" ;
			public $NomComposant = "" ;
			public $NomParametreLie = "" ;
			public $NomParametreDonnees = "" ;
			public $NomClasseLiaison = null ;
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
			public $NePasLierParametre = 0 ;
			public $NePasIntegrerParametre = 0 ;
			public function InserePrefxErr($contenu)
			{
				$this->InserePrefixeLib(new PvMarqErrFiltreDonnees($contenu)) ;
			}
			public function InserePrefxNotice($contenu)
			{
				$this->InserePrefixeLib(new PvMarqNoticeFiltreDonnees($contenu)) ;
			}
			public function InsereSuffxErr($contenu)
			{
				$this->InsereSuffixeLib(new PvMarqErrFiltreDonnees($contenu)) ;
			}
			public function InsereSuffxNotice($contenu)
			{
				$this->InsereSuffixeLib(new PvMarqNoticeFiltreDonnees($contenu)) ;
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
			public function AdopteScript($nom, & $script)
			{
				$this->ScriptParent = & $script ;
				$this->ZoneParent = & $script->ZoneParent ;
				$this->ApplicationParent = & $script->ApplicationParent ;
				$this->NomElementScript = $nom ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeComposant() ;
			}
			protected function CorrigeConfig()
			{
				if($this->NomParametreDonnees == '' && $this->NomElementScript != '')
					$this->NomParametreDonnees = $this->NomElementScript ;
				if($this->NomParametreLie == '' && $this->NomElementScript != '')
					$this->NomParametreLie = $this->NomElementScript ;
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
					if($this->NomElementScript != '')
						$this->NomParametreLie = $this->NomElementScript ;
					else
						$this->NomParametreLie = $this->IDInstanceCalc ;
				}
			}
			public function ObtientLibelle()
			{
				$libelle = $this->Libelle ;
				if($libelle == '')
				{
					$libelle = $this->NomElementScript ;
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
					return "" ;
				$iDInstanceCalc = $this->Composant->IDInstanceCalc ;
				return $iDInstanceCalc ;
			}
			public function Rendu()
			{
				if($this->EstEtiquette)
				{
					return $this->Etiquette() ;
				}
				if($this->EstNul($this->Composant))
				{
					$this->DeclareComposant($this->NomClasseComposant) ;
				}
				if($this->EstNul($this->Composant))
				{
					return "(Composant nul)" ;
				}
				$this->Composant->Valeur = $this->Lie() ;
				$this->Composant->FiltreParent = $this ;
				$ctn = $this->Composant->RenduDispositif() ;
				$this->Composant->FiltreParent = null ;
				return $ctn ;
			}
			public function Etiquette()
			{
				if($this->EstNul($this->Composant))
				{
					$this->DeclareComposant($this->NomClasseComposant) ;
				}
				if($this->EstNul($this->Composant))
				{
					return "(Composant nul)" ;
				}
				$this->Composant->Valeur = $this->Lie() ;
				$this->Composant->FiltreParent = $this ;
				$ctn = $this->Composant->RenduEtiquette() ;
				$this->Composant->FiltreParent = null ;
				return $ctn ;
			}
			public function InitComposant()
			{
			}
			public function & ObtientComposant()
			{
				if($this->EstNul($this->Composant))
					$this->DeclareComposant($this->NomClasseComposant) ;
				return $this->Composant ;
			}
			public function & DeclareComposant($nomClasseComposant)
			{
				$this->Composant = $this->ValeurNulle() ;
				$this->NomClasseComposant = $nomClasseComposant ;
				if(class_exists($nomClasseComposant))
				{
					$this->Composant = new $nomClasseComposant() ;
					$this->Composant->AdopteScript($this->ObtientNomComposant(), $this->ScriptParent) ;
					$this->InitComposant() ;
					$this->Composant->ChargeConfig() ;
				}
				return $this->Composant ;
			}
			public function RemplaceComposant($nouvComposant)
			{
				$this->Composant = $nouvComposant ;
				$this->Composant->AdopteScript($this->ObtientNomComposant(), $this->ScriptParent) ;
				$this->InitComposant() ;
				$this->Composant->ChargeConfig() ;
			}
		}
		class PvFiltreDonneesFixe extends PvFiltreDonneesBase
		{
			public $TypeLiaisonParametre = "hidden" ;
			public function NePasInclure()
			{
				return 0 ;
			}
			public function ObtientValeurParametre()
			{
				return $this->ValeurParDefaut ;
			}
		}
		class PvFiltreDonneesCache extends PvFiltreDonneesFixe
		{
		}
		class PvFiltreDonneesRef extends PvFiltreDonneesBase
		{
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
		class PvFiltreDonneesCookie extends PvFiltreDonneesBase
		{
			public $TypeLiaisonParametre = "cookie" ;
			public function ObtientValeurParametre()
			{
				return (isset($_COOKIE[$this->NomParametreLie])) ? $_COOKIE[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesSession extends PvFiltreDonneesBase
		{
			public $TypeLiaisonParametre = "session" ;
			public function ObtientValeurParametre()
			{
				return (isset($_SESSION[$this->NomParametreLie])) ? $_SESSION[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesMembreConnecte extends PvFiltreDonneesBase
		{
			public $TypeLiaisonParametre = "membre_connecte" ;
			public function ObtientValeurParametre()
			{
				// print_r($this->ZoneParent->Membership->MemberLogged->RawData) ;
				if($this->EstNul($this->ZoneParent) || $this->EstNul($this->ZoneParent->Membership) || $this->EstNul($this->ZoneParent->Membership->MemberLogged))
				{
					return $this->ValeurVide ;
				}
				if(isset($this->ZoneParent->Membership->MemberLogged->RawData[$this->NomParametreLie]))
				{
					return $this->ZoneParent->Membership->MemberLogged->RawData[$this->NomParametreLie] ;
				}
			}
		}
		class PvFiltreDonneesHttpRequest extends PvFiltreDonneesBase
		{
			public $TypeLiaisonParametre = "request" ;
			public $AccepteTagsHtml = 1 ;
			public $AccepteTagsSuspicieux = 0 ;
			public $ValeurBruteNonCorrigee = false ;
			protected function EnleveTagsHtml($valeur)
			{
				$tag = new HtmlTag() ;
				$tag->LoadFromText($valeur) ;
				return $tag->Preview ;
			}
			protected function EnleveTagsSuspicieux($valeur)
			{
				$tag = new HtmlTag() ;
				$tag->SafeMode = 1 ;
				$tag->LoadFromText($valeur) ;
				return $tag->GetContent(true) ;
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
				$this->ValeurBruteNonCorrigee = (isset($_REQUEST[$this->NomParametreLie])) ? $_REQUEST[$this->NomParametreLie] : $this->ValeurVide ;
			}
			public function ObtientValeurParametre()
			{
				$this->CalculeValeurBruteNonCorrigee() ;
				$this->ValeurBrute = $this->CorrigeValeurBrute($this->ValeurBruteNonCorrigee) ;
				return $this->ExtraitValeurFormattee($this->ValeurBrute) ;
			}
		}
		class PvFiltreDonneesHttpGet extends PvFiltreDonneesHttpRequest
		{
			public $TypeLiaisonParametre = "get" ;
			protected function CalculeValeurBruteNonCorrigee()
			{
				$this->ValeurBruteNonCorrigee = (isset($_GET[$this->NomParametreLie])) ? $_GET[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesHttpPost extends PvFiltreDonneesHttpRequest
		{
			public $TypeLiaisonParametre = "post" ;
			protected function CalculeValeurBruteNonCorrigee()
			{
				$this->ValeurBruteNonCorrigee = (isset($_POST[$this->NomParametreLie])) ? $_POST[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesHttpUpload extends PvFiltreDonneesBase
		{
			public $TypeLiaisonParametre = "post" ;
			public $InfosTelechargement = array() ;
			public $CheminDossier = "." ;
			public $CheminFichierDest = "" ;
			public $CheminFichierSrc = "" ;
			public $ExtensionsAcceptees = array() ;
			public $CheminFichierClient = "" ;
			public $CodeErreurTelechargement = "0" ;
			public $CheminFichierSoumis = "" ;
			public $NomFichierSelect = "" ;
			public $ExtFichierSelect = "" ;
			public $NomEltCoteSrv = "CoteSrv_" ;
			public $NomClasseComposant = "PvZoneUploadHtml" ;
			public $LibelleErreurTelecharg = '' ;
			public $SourceTelechargement = '' ;
			public $CodeErreurMauvaiseExt = '501' ;
			public $LibelleErreurMauvaiseExt = 'Mauvais format pour le fichier soumis.' ;
			public $CodeErreurDeplFicTelecharg = '502' ;
			public $LibelleErreurDeplFicTelecharg = 'Le deplacement du fichier sur le serveur a &eacute;chou&eacute;. V&eacute;rifiez que vous avez les droits en ecriture.' ;
			public $CodeErreurFicSoumisInexist = '503' ;
			public $LibelleErreurFicSoumisInexist = 'Le fichier soumis n\'existe pas.' ;
			public function DeclareComposant($nomClasseComposant)
			{
				parent::DeclareComposant($nomClasseComposant) ;
				/*
				if($this->EstPasNul($this->Composant))
				{
					$this->Composant->NomEltCoteSrv = $this->NomEltCoteSrv ;
				}
				*/
			}
			public function ObtientValeurParametre()
			{
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
					$this->NomFichierSelect = $infosFichier["basename"] ;
					$this->ExtFichierSelect = (isset($infosFichier["extension"])) ? $infosFichier["extension"] : "" ;
				}
				else
				{
					$this->SourceTelechargement = 'post' ;
					$this->CheminFichierSoumis = $_POST[$this->NomEltCoteSrv.$this->NomParametreLie] ;
					$infosFichier = pathinfo($this->CheminFichierSoumis) ;
					$this->NomFichierSelect = $infosFichier["basename"] ;
					$this->ExtFichierSelect = (isset($infosFichier["extension"])) ? $infosFichier["extension"] : "" ;
					// print_r("Doss : ".$this->CheminDossier) ;
					// print_r($infosFichier) ;
				}
				if(count($this->ExtensionsAcceptees) > 0 && ! in_array(strtolower($this->ExtFichierSelect), array_map("strtolower", $this->ExtensionsAcceptees)))
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
					$ok = @move_uploaded_file($this->CheminFichierSrc, $this->CheminFichierDest) ;
					if(! $ok)
					{
						$this->CodeErreurTelechargement = $this->CodeErreurDeplFicTelecharg ;
						$this->LibelleErreurTelecharg = $this->LibelleErreurDeplFicTelecharg ;
						return $this->ValeurVide ;
					}
				}
				else
				{
					if($this->CheminFichierDest != "" && ! file_exists($this->CheminFichierDest))
					{
						$this->CodeErreurTelechargement = $this->CodeErreurFicSoumisInexist ;
						$this->LibelleErreurTelecharg = $this->LibelleErreurFicSoumisInexist ;
						return $this->ValeurVide ;
					}
				}
				return $this->CheminFichierDest ;
			}
		}
		
	}
	
?>