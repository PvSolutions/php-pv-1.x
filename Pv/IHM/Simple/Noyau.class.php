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
		if(! defined('HDOM_TYPE_TEXT'))
		{
			include dirname(__FILE__)."/../../../misc/simple_html_dom.php" ;
		}
		if(! defined('HTML_TAG_INC'))
		{
			include dirname(__FILE__)."/../../../misc/HTMLTag.class.php" ;
		}
		if(! class_exists('SafeHTML'))
		{
			include dirname(__FILE__)."/../../../misc/SafeHTML.php" ;
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
			public $Cible ;
			public $InclureIcone = 1 ;
			public $InclureLibelle = 1 ;
			public $HauteurIcone = "18" ;
			public $NomDonneesValid = "" ;
			public $Privileges = array() ;
			public $ValeurVraiValid = 1 ;
			public $Visible = 1 ;
			public function Accepte($donnees)
			{
				$ok = ($this->RenduPossible() && ($this->NomDonneesValid == "" || (isset($donnees[$this->NomDonneesValid]) && $donnees[$this->NomDonneesValid] == $this->ValeurVraiValid))) ? 1 : 0 ;
				return $ok ;
			}
			protected function RenduIcone($donnees, $donneesUrl)
			{
				$ctn = '' ;
				if(! $this->InclureIcone || $this->FormatCheminIcone == "")
				{
					return $ctn ;
				}
				$cheminIcone = _parse_pattern($this->FormatCheminIcone, $donneesUrl) ;
				$ctn .= '<img src="'.$cheminIcone.'" height="'.$this->HauteurIcone.'" border="0" />' ;
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
			protected function ObtientHrefFmt(& $donneesUrl)
			{
				return _parse_pattern($this->FormatURL, $donneesUrl) ;
			}
			protected function ObtientLibelleFmt(& $donnees)
			{
				return _parse_pattern($this->FormatLibelle, $donnees) ;
			}
			protected function RenduBrut($donnees)
			{
				$ctn = '' ;
				$donneesUrl = array_map("urlencode", $donnees) ;
				$href = $this->ObtientHrefFmt(array_merge($donneesUrl, array_apply_suffix($donnees, "_brut"))) ;
				$libelle = $this->ObtientLibelleFmt($donnees) ;
				$ctn .= '<a href="'.htmlentities($href).'"' ;
				if($this->Cible != '')
				{
					$ctn .= ' target="'.$this->Cible.'"' ;
				}
				if($this->ChaineAttributs != '')
				{
					$ctn .= ' '.$this->ChaineAttributs ;
				}
				if($this->ClasseCSS != '')
				{
					$ctn .= ' class="'.$this->ClasseCSS.'"' ;
				}
				if($this->InclureLibelle == 0)
				{
					$ctn .= ' title="'.htmlspecialchars(_parse_pattern($this->FormatLibelle, $donnees)).'"' ;
				}
				$ctn .= '>' ;
				$ctnIcone = $this->RenduIcone($donnees, $donneesUrl) ;
				$ctn .= $ctnIcone ;
				if($this->InclureLibelle)
				{
					if($ctnIcone != '')
					{
						$ctn .= ' ' ;
					}
					if($this->EncodeHtmlLibelle)
					{
						$libelle = htmlentities($libelle) ;
					}
					$ctn .= $libelle ;
				}
				$ctn .= '</a>' ;
				return $ctn ;
			}
			public function DefinitValidite($nomDonnees, $valeurVrai=1)
			{
				$this->NomDonneesValid = $nomDonnees ;
				$this->ValeurVraiValid = $valeurVrai ;
			}
		}
		
		class PvFormatteurColonneDonnees extends PvObjet
		{
			public $ExtracteurValeur ;
			public function EstEditable()
			{
				return 0 ;
			}
			public function EstAccessible(& $zone, $colonne)
			{
				return true ;
			}
			public function Encode(& $script, $colonne, $ligne)
			{
				if(isset($ligne[$colonne->NomDonnees]))
					return $ligne[$colonne->NomDonnees] ;
				return '' ;
			}
			public function ObtientDonnees($colonne, $ligne)
			{
				$valeurCourante = (isset($ligne[$colonne->NomDonnees])) ? $ligne[$colonne->NomDonnees] : '' ;
				$donnees = array_merge($ligne, array('self' => $valeurCourante, 'this' => $valeurCourante)) ;
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
		class PvFormatteurColonneChoix extends PvFormatteurColonneDonnees
		{
			public $ValeursChoix = array() ;
			public $ValeurNonTrouvee = "&nbsp;" ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$valeurEntree = $ligne[$colonne->NomDonnees] ;
				if(isset($this->ValeursChoix[$valeurEntree]))
				{
					$valeur = $this->ValeursChoix[$valeurEntree] ;
				}
				else
				{
					$valeur = $this->ValeurNonTrouvee ;
				}
				return $valeur ;
			}
		}
		class PvFormatteurColonneFixe extends PvFormatteurColonneDonnees
		{
			public $ValeurParDefaut = "" ;
			public function Encode(& $script, $colonne, $ligne)
			{
				return htmlentities($this->ValeurParDefaut) ;
			}
		}
		class PvFormatteurColonneMonnaie extends PvFormatteurColonneDonnees
		{
			public $MaxDecimals = 3 ;
			public $MinChiffres = 1 ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$valeurEntree = $ligne[$colonne->NomDonnees] ;
				return format_money($valeurEntree, $this->MaxDecimals, $this->MinChiffres) ;
			}
		}
		class PvFormatteurColonneDateFr extends PvFormatteurColonneDonnees
		{
			public $InclureHeure = 0 ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$valeurEntree = $ligne[$colonne->NomDonnees] ;
				if($this->InclureHeure == 1)
				{
					return date_time_fr($valeurEntree) ;
				}
				else
				{
					return date_fr($valeurEntree) ;
				}
			}
		}
		class PvFormatteurColonneTimestamp extends PvFormatteurColonneDonnees
		{
			public $FormatDate = "Y-m-d H:i:s" ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$valeurEntree = $ligne[$colonne->NomDonnees] ;
				if($valeurEntree == "")
				{
					return $valeurEntree ;
				}
				return date($this->FormatDate, $valeurEntree) ;
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
			public function EstAccessible(& $zone, $colonne)
			{
				return ! $zone->ImpressionEnCours() ;
			}
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
		class PvFormatteurColonnePlusDetail extends PvFormatteurColonneDonnees
		{
			public $MaxCaracteresIntro = 40 ;
			public $HauteurBlocDetail = '225px' ;
			public $ArrPlBlocDetail = 'white' ;
			public $CouleurBlocDetail = 'black' ;
			public $CouleurBordureBlocDetail = '#e8e8e8' ;
			public $TailleBordureBlocDetail = '4px' ;
			protected $RenduSourceInclus = 0 ;
			protected $IndexLigne = 0 ;
			public function Encode(& $script, $colonne, $ligne)
			{
				$valeur = '' ;
				if(isset($ligne[$colonne->NomDonnees]))
					$valeur = $ligne[$colonne->NomDonnees] ;
				$valeurIntro = substr($valeur, 0, $this->MaxCaracteresIntro) ;
				if(strlen($valeurIntro) < strlen($valeur))
				{
					$valeurIntro .= "..." ;
				}
				$this->IndexLigne++ ;
				if(strlen($valeur) == strlen($valeurIntro))
				{
					return $valeur ;
				}
				$rendu = '' ;
				if($this->RenduSourceInclus == 0)
				{
					$rendu .= '<style type="text/css">
.detail-'.$this->IDInstanceCalc.' {
position: relative;
display: inline-block;
}
.detail-'.$this->IDInstanceCalc.':hover .tooltiptext {
    visibility: visible;
}
.detail-'.$this->IDInstanceCalc.' .tooltiptext {
visibility: hidden;
width: 100%;
overflow: scroll ;
top: 50%;
left: 20% ;
margin-left: -40px;
background-color: '.$this->ArrPlBlocDetail.';
padding:8px ;
border:'.$this->TailleBordureBlocDetail.' solid '.$this->CouleurBordureBlocDetail.' ;
color: '.$this->CouleurBlocDetail.' ;
height:'.$this->HauteurBlocDetail.' ;
text-align: center;
border-radius: 2px;
position: absolute;
z-index: 1;
}
</style>' ;
					$this->RenduSourceInclus = 1 ;
				}
				$rendu .= '<div class="detail-'.$this->IDInstanceCalc.'">'.htmlentities($valeurIntro).'<span class="tooltiptext">'.htmlentities($valeur).'</span></div>' ;
				return $rendu ;
			}
		}
		
		class PvFormatteurColonneEditable extends PvFormatteurColonneDonnees
		{
			public $NomParametrePost ;
			protected $NomClasseComposant  ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Composant = new PvZoneTexteHtml() ;
				$this->NomParametrePost = "Param_".$this->IDInstanceCalc ;
			}
			public function DeclareComposant($nomClasseComposant)
			{
				if($nomClasseComposant == "")
				{
					return ;
				}
				if(! class_exists($nomClasseComposant))
				{
					die("Echec creation du composant ".htmlentities($nomClasseComposant)) ;
				}
				$this->NomClasseComposant = $nomClasseComposant ;
			}
			protected function ChargeComposant(& $composant, & $composantParent)
			{
				$composant->AdopteScript("Composant_".$this->IDInstanceCalc, $composantParent->ScriptParent) ;
				$composant->ChargeConfig() ;
			}
			public function EstEditable()
			{
				return 1 ;
			}
			public function Encode(& $composantParent, $colonne, $ligne)
			{
				$filtreSupport = $composantParent->ScriptParent->CreeFiltreHttpPost($this->NomParametrePost."[]") ;
				$valeur = $ligne[$colonne->NomDonnees] ;
				$nomClasseComposant = $this->NomClasseComposant ;
				$composant = new $nomClasseComposant() ;
				$this->ChargeComposant($composant, $composantParent) ;
				$composant->Valeur = $valeur ;
				$composant->NomElementHtml = $this->NomParametrePost."[]" ;
				$composant->FiltreParent = $filtreSupport ;
				$ctn = $composant->RenduDispositif() ;
				$composant->FiltreParent = null ;
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
			public $CorrecteurValeur = null ;
			public $TriPossible = 1 ;
			public $EncodeHtmlValeur = 1 ;
			public $ExtracteurValeur = null ;
			public $PrefixeValeursExtraites = "" ;
			public $Visible = 1 ;
			public $ExporterDonnees = 1 ;
			public $ExporterDonneesObligatoire = 0 ;
			public $FormatValeur ;
			public $StyleCSS ;
			public $NomClasseCSS ;
			public $RenvoyerValeurVide = 1 ;
			public $ValeurVide = "&nbsp;" ;
			public function EstVisible(& $zone)
			{
				$ok = $this->Visible == 1 ;
				if($ok && $this->EstPasNul($this->Formatteur))
				{
					$ok = $this->Formatteur->EstAccessible($zone, $this) ;
				}
				return $ok ;
			}
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
			public function FormatteValeur(& $composant, $ligne)
			{
				$val = null ;
				if($this->EstNul($this->Formatteur))
				{
					$val = $this->FormatteValeurInt($composant, $ligne) ;
				}
				else
				{
					$val = $this->Formatteur->Encode($composant, $this, $ligne) ;
				}
				if($this->EstPasNul($this->CorrecteurValeur))
				{
					$val = $this->CorrecteurValeur->AppliquePourColonne($val, $this) ;
				}
				return $val ;
			}
			protected function FormatteValeurInt(& $composant, $ligne)
			{
				$val = "" ;
				if($this->NomDonnees != '')
					$val = $this->EncodeValeur($ligne[$this->NomDonnees]) ;
				if($val == "" && $this->RenvoyerValeurVide)
					return $this->ValeurVide ;
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
			public function EstEditable()
			{
				$ok = 1 ;
				if($this->EstNul($this->Formatteur))
				{
					return 0 ;
				}
				return $this->Formatteur->EstEditable() ;
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
		
		class PvCorrecteurValeurFiltreBase
		{
			public function Applique($valeur, & $filtre)
			{
				return $valeur ;
			}
			public function AppliquePourRendu($valeur, & $filtre)
			{
				return $valeur ;
			}
			public function AppliquePourTraitement($valeur, & $filtre)
			{
				return $valeur ;
			}
			public function AppliquePourColonne($valeur, & $defCol)
			{
				return $valeur ;
			}
		}
		class PvCorrecteurValeurEncodeeUtf8 extends PvCorrecteurValeurFiltreBase
		{
			public function Applique($valeur, & $filtre)
			{
				return utf8_encode($valeur) ;
			}
		}
		class PvCorrecteurValeurSansAccent extends PvCorrecteurValeurFiltreBase
		{
			public function Applique($valeur, & $filtre)
			{
				// return slugify($valeur) ;
				$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'N', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', 'z', 'ƒ');
				$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'N', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 'z', 'f');
				return str_replace($a, $b, $valeur);
			}
		}
		class PvCorrecteurValeurEncodeeHtml extends PvCorrecteurValeurFiltreBase
		{
			public function Applique($valeur, & $filtre)
			{
				return htmlspecialchars($valeur) ;
			}
		}
		class PvCorrecteurValeurDecodeeHtml extends PvCorrecteurValeurFiltreBase
		{
			public function Applique($valeur, & $filtre)
			{
				return htmlspecialchars_decode($valeur) ;
			}
		}
		
		class PvFormatteurEtiquetteFiltre
		{
			public function Applique($valeur, & $filtre)
			{
				return $valeur ;
			}
		}
		class PvFmtEtiquetteFiltre extends PvFormatteurEtiquetteFiltre
		{
		}
		class PvFmtMonnaieEtiquetteFiltre extends PvFmtEtiquetteFiltre
		{
			public $MaxDecimals = 3 ;
			public $MinChiffres = 1 ;
			public function Applique($valeur, & $filtre)
			{
				return format_money($valeur, $this->MaxDecimals, $this->MinChiffres) ;
			}
		}
		class PvFmtDateFrEtiquetteFiltre extends PvFmtEtiquetteFiltre
		{
			public function Applique($valeur, & $filtre)
			{
				return date_fr($valeur) ;
			}
		}
		
		class PvFiltreDonneesBase extends PvObjet
		{
			public $PrefixesLibelle = array() ;
			public $SuffixesLibelle = array() ;
			public $Obligatoire = 0 ;
			public $ScriptParent ;
			public $ZoneParent ;
			public $ApplicationParent ;
			public $NomElementScript = "" ;
			public $NomElementZone = "" ;
			public $TypeLiaisonParametre = "" ;
			public $Role = "base" ;
			public $Liaison ;
			public $Composant ;
			public $Libelle = "" ;
			public $CheminIcone = "" ;
			public $NomClasseCSS = "" ;
			public $NomClasseCSSIcone = "" ;
			public $EspaceReserve = "" ;
			public $NomClasseComposant = "PvZoneTexteHtml" ;
			public $NomComposant = "" ;
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
				return $this->EstPasNul($this->ZoneParent) && $this->ZoneParent->ImpressionEnCours() ;
			}
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
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CorrecteurValeur = new PvCorrecteurValeurFiltreBase() ;
				$this->FormatteurEtiquette = new PvFormatteurEtiquetteFiltre() ;
			}
			public function AdopteZone($nom, & $script)
			{
				$this->ZoneParent = & $script->ZoneParent ;
				$this->ApplicationParent = & $script->ApplicationParent ;
				$this->NomElementZone = $nom ;
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
			public function Rendu()
			{
				if($this->EstEtiquette || $this->ImpressionEnCours())
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
				$this->Composant->Valeur = $this->LiePourRendu() ;
				$this->Composant->EspaceReserve = $this->EspaceReserve ;
				if($this->Composant->EspaceReserve == "" && $this->ZoneParent->LibelleEspaceReserveFiltres == 1)
				{
					$this->Composant->EspaceReserve = $this->Libelle ;
				}
				$this->Composant->FiltreParent = $this ;
				$ctn = $this->Composant->RenduDispositif() ;
				$this->Composant->FiltreParent = null ;
				return $ctn ;
			}
			public function DefinitFmtLbl($fmt)
			{
				$this->ObtientComposant()->FmtLbl = $fmt ;
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
				$this->Composant->Valeur = $this->FormatteurEtiquette->Applique($this->LiePourRendu(), $this) ;
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
					return $this->DeclareComposant($this->NomClasseComposant) ;
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
		class PvFiltreDonneesFixe extends PvFiltreDonneesBase
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
		class PvFiltreDonneesCache extends PvFiltreDonneesFixe
		{
		}
		class PvFiltreDonneesRef extends PvFiltreDonneesBase
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
		class PvFiltreDonneesCookie extends PvFiltreDonneesBase
		{
			public $Role = "cookie" ;
			public $TypeLiaisonParametre = "cookie" ;
			public function ObtientValeurParametre()
			{
				return (isset($_COOKIE[$this->NomParametreLie])) ? $_COOKIE[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesSession extends PvFiltreDonneesBase
		{
			public $Role = "session" ;
			public $TypeLiaisonParametre = "session" ;
			public function ObtientValeurParametre()
			{
				return (isset($_SESSION[$this->NomParametreLie])) ? $_SESSION[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesMembreConnecte extends PvFiltreDonneesBase
		{
			public $Role = "membre_connecte" ;
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
					// print "ssds ".$this->ZoneParent->Membership->MemberLogged->RawData[$this->NomParametreLie] ;
					return $this->ZoneParent->Membership->MemberLogged->RawData[$this->NomParametreLie] ;
				}
			}
		}
		class PvFiltreDonneesHttpRequest extends PvFiltreDonneesBase
		{
			public $Role = "request" ;
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
		class PvFiltreDonneesHttpGet extends PvFiltreDonneesHttpRequest
		{
			public $Role = "get" ;
			public $TypeLiaisonParametre = "get" ;
			protected function CalculeValeurBruteNonCorrigee()
			{
				$this->ValeurBruteNonCorrigee = (array_key_exists($this->NomParametreLie, $_GET)) ? $_GET[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesHttpPost extends PvFiltreDonneesHttpRequest
		{
			public $Role = "post" ;
			public $TypeLiaisonParametre = "post" ;
			protected function CalculeValeurBruteNonCorrigee()
			{
				$this->ValeurBruteNonCorrigee = (array_key_exists($this->NomParametreLie, $_POST)) ? $_POST[$this->NomParametreLie] : $this->ValeurVide ;
			}
		}
		class PvFiltreDonneesHttpUpload extends PvFiltreDonneesBase
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
			public $ExtensionsRejetees = array('pl', 'cgi', 'html', 'xhtml', 'html5', 'html4', 'xml', 'xss', 'rss', 'xlt', 'php', 'phtml', 'inc', 'js', 'vbs', 'py', 'bat', 'sh', 'cmd') ;
			public $CheminFichierClient = "" ;
			public $CodeErreurTelechargement = "0" ;
			public $CheminFichierSoumis = "" ;
			public $NomFichierSelect = "" ;
			public $ExtFichierSelect = "" ;
			public $NomEltCoteSrv = "CoteSrv_" ;
			public $NomClasseComposant = "PvZoneUploadHtml" ;
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
			public function & DeclareComposant($nomClasseComposant)
			{
				return parent::DeclareComposant($nomClasseComposant) ;
				/*
				if($this->EstPasNul($this->Composant))
				{
					$this->Composant->NomEltCoteSrv = $this->NomEltCoteSrv ;
				}
				*/
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