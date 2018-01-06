<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_ELEM_JS'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
		{
			include dirname(__FILE__)."/../FournisseurDonnees.class.php" ;
		}
		if(! defined('COMMON_GD_CONTROLS_INCLUDED'))
		{
			include dirname(__FILE__)."/../../../../Common/GD.class.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_ELEM_JS', 1) ;
		
		class PvEditeurHtmlBase extends PvElementFormulaireHtml
		{
			protected static $SourceIncluse = 0 ;
			protected function RenduSourceIncluse()
			{
				$nomClasse = get_class($this) ;
				if($this->ObtientValStatique("SourceIncluse") == 1)
					return "" ;
				$ctn = $this->RenduSourceBrut() ;
				$this->AffecteValStatique("SourceIncluse", 1) ;
				return $ctn ;
			}
			protected function RenduSourceBrut()
			{
				return "" ;
			}
			protected function RenduEditeurBrut()
			{
				return "" ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$this->CorrigeIDsElementHtml() ;
				$ctn .= $this->RenduSourceIncluse() ;
				$ctn .= $this->RenduEditeurBrut() ;
				return $ctn ;
			}
		}
		
		class PvEditeurChoixBase extends PvZoneBoiteChoixBaseHtml
		{
			protected static $SourceIncluse = 0 ;
			protected function RenduSourceIncluse()
			{
				if($this->ObtientValStatique("SourceIncluse") == 1)
					return "" ;
				$ctn = $this->RenduSourceBrut() ;
				$this->AffecteValStatique("SourceIncluse", 1) ;
				return $ctn ;
			}
			protected function RenduSourceBrut()
			{
				return "" ;
			}
			protected function RenduEditeurBrut()
			{
				return "" ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$this->CorrigeIDsElementHtml() ;
				$this->InitFournisseurDonnees() ;
				if(! $this->EstNul($this->FournisseurDonnees))
				{
					$this->ChargeConfigFournisseurDonnees() ;
					$this->CalculeElementsRendu() ;
                    $ctn .= $this->RenduSourceIncluse() ;
                    $ctn .= $this->RenduEditeurBrut() ;
				}
				else
				{
					die("Le composant ".$this->IDInstanceCalc." necessite un fournisseur de donnees.") ;
				}
				return $ctn ;
			}
		}
        
		class PvActionImgCommonCaptcha extends PvActionEnvoiFichierBaseZoneWeb
		{
			protected $Support ;
			protected function InitSupport()
			{
				$this->Support = CommonGDCaptcha::Create($this->ComposantIUParent->LargeurImg, $this->ComposantIUParent->HauteurImg) ;
				$this->Support->Name = $this->ComposantIUParent->NomImg ;
				$this->Support->CaseInsensitive = $this->ComposantIUParent->CasseInsensibleImg ;
			}
			protected function AfficheContenu()
			{
				$this->InitSupport() ;
				$this->Support->Open() ;
				$this->Support->Draw() ;
				$this->Support->Show() ;
				$this->Support->Close() ;
			}
			public function VerifieValeurSoumise($texte)
			{
				$this->InitSupport() ;
				return $this->Support->ConfirmSubmittedText($texte) ;
			}
		}
		class PvZoneCommonCaptcha extends PvZoneTexteHtml
		{
			public $NomImg = "verify" ;
			public $LargeurImg = 115 ;
			public $HauteurImg = 32 ;
			public $CasseInsensibleImg = 1 ;
			public $NomActionAffichImg ;
			public $ActionAffichImg ;
			public $NomParamsAction = array() ;
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				$this->NomActionAffichImg = $this->IDInstanceCalc.'_AffichImg' ;
				$this->ActionAffichImg = new PvActionImgCommonCaptcha() ;
				$this->InscritActionAvantRendu($this->NomActionAffichImg, $this->ActionAffichImg) ;
			}
			protected function RenduDispositifBrut()
			{
				if(count($this->NomParamsAction) > 0)
				{
					$this->ActionAffichImg->Params = array_extract_value_for_keys($_GET, $this->NomParamsAction) ;
				}
				$ctn = '' ;
				$ctn .= '<table cellspacing="0" cellpadding="0"><tr><td>' ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctn .= '</td><td>&nbsp;</td><td>' ;
				$ctn .= '<img src="'.$this->ActionAffichImg->ObtientUrl().'" />' ;
				$ctn .= '</td></tr></table>' ;
				return $ctn ;
			}
			public function VerifieValeurSoumise($texte)
			{
				return $this->ActionAffichImg->VerifieValeurSoumise($texte) ;
			}
		}
		class PvZoneCaptcha extends PvZoneCommonCaptcha
		{
		}
		
		class PvNoteBloc extends PvEditeurHtmlBase
		{
			public $CheminFichierJs = "js/noteBloc.js" ;
			public $ValeurMin = 1 ;
			public $ValeurMax = 5 ;
			protected static $SourceIncluse = 0;
			public function DefinitRangee($min, $max)
			{
				$this->ValeurMin = $min ;
				$this->ValeurMax = $max ;
			}
			protected function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminFichierJs) ;
				return $ctn ;
			}
			protected function RenduEditeurBrut()
			{
				$ctn = '' ;
				$ctn .= '<script>drawNoteBloc('.svc_json_encode($this->NomElementHtml).', '.svc_json_encode($this->ValeurMin).', '.svc_json_encode($this->ValeurMax).', '.(($this->Modifiable) ? 'true' : 'false').') ;</script>' ;
				return $ctn ;
			}
		}
		
		class PvTimeInput extends PvEditeurHtmlBase
		{
			public $CheminFichierJs = "js/timeInput.js" ;
			protected static $SourceIncluse = 0;
			public $UseValeurActuelleParDefaut = 1;
			protected function CorrigeValeur()
			{
				if($this->Valeur == "" && $this->UseValeurActuelleParDefaut)
				{
					$this->Valeur = date("H:i:s") ;
				}
			}
			protected function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminFichierJs) ;
				return $ctn ;
			}
			protected function RenduEditeurBrut()
			{
				$ctn = '' ;
				$this->CorrigeValeur() ;
				$ctn .= '<script>drawTimeInput('.svc_json_encode($this->NomElementHtml).', '.svc_json_encode($this->Valeur).') ;</script>' ;
				return $ctn ;
			}
		}
		
		class PvCalendarDateInput extends PvElementFormulaireHtml
		{
			public $Format = "YYYY-MM-DD" ;
			public $Necessaire = 1 ;
			public static $SourceInclus = 0 ;
			public static $CheminSource = "js/calendarDateInput.js" ;
			public function CorrigeValeur()
			{
				if($this->Valeur == "")
				{
					$this->Valeur = date("Y-m-d") ;
				}
			}
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$this->CorrigeValeur() ;
				$ctn = '' ;
				$ctn .= $this->InclutSource() ;
				$ctn .= '<script type="text/javascript">
	DateInput('.svc_json_encode($this->NomElementHtml).', '.(($this->Necessaire == 1) ? 'true' : 'false').', '.svc_json_encode($this->Format).', '.svc_json_encode(htmlentities($this->Valeur)).', '.svc_json_encode($this->IDInstanceCalc).') ;
</script>' ;
				return $ctn ;
			}
		}
		class PvJasonCalendarDateInput extends PvCalendarDateInput
		{
			public static $SourceInclus = 0 ;
		}
		
		class PvBlocAjax extends PvBaliseHtmlBase
		{
			public $TexteMsgSurExpirationAtteint = "" ;
			public $TexteMsgSurChargement = "Chargement en cours..." ;
			public $AlignMsgSurChargement = "center" ;
			public $ActionRecupContenuHtml = null ;
			public $DelaiExpiration = 10 ;
			public $AutoRafraich = false ;
			public $DelaiRafraich = 0 ;
			public $ContenuHtml = "<p>Bloc Ajax</p>" ;
			public $Support = null ;
			public $NomActionRecupContenuHtml = null ;
			public static $SourceInclus = 0 ;
			public static $CheminSource = "js/AppelAjax.js" ;
			public function AdopteScript($nom, & $script)
			{
				parent::AdopteScript($nom, $script) ;
				$this->NomActionRecupContenuHtml = $this->IDInstanceCalc.'_ContenuHtml' ;
				$this->ActionRecupContenuHtml = new PvBlocAjaxActRecupCtnHtml() ;
				$this->InscritActionAvantRendu($this->NomActionRecupContenuHtml, $this->ActionRecupContenuHtml) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->InclutSource() ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'"></div>';
				$ctn .= '<script type="text/javascript">
	var bloc'.$this->IDInstanceCalc.' = new BlocAjax('.svc_json_encode($this->IDInstanceCalc).', "POST") ;
	bloc'.$this->IDInstanceCalc.'.TexteMsgSurChargement = '.svc_json_encode($this->TexteMsgSurChargement).' ;
	bloc'.$this->IDInstanceCalc.'.AutoRafraich = '.svc_json_encode($this->AutoRafraich).' ;
	bloc'.$this->IDInstanceCalc.'.DelaiRafraich = '.svc_json_encode($this->DelaiRafraich).' ;
	bloc'.$this->IDInstanceCalc.'.AlignMsgSurChargement = '.svc_json_encode($this->AlignMsgSurChargement).' ;
	bloc'.$this->IDInstanceCalc.'.RequeteAjax.DelaiExpiration = '.intval($this->DelaiExpiration).' ;
	bloc'.$this->IDInstanceCalc.'.UtiliserContenuBrutContenuCorps = true ;'.PHP_EOL ;
	if(isset($HTTP_RAW_POST_DATA))
	{
		$ctn .= 'bloc'.$this->IDInstanceCalc.'.ContenuBrutContenuCorps = '.svc_json_encode($HTTP_RAW_POST_DATA).' ;'.PHP_EOL ;
	}
	$ctn .= 'bloc'.$this->IDInstanceCalc.'.DefinitUrl('.svc_json_encode($this->ActionRecupContenuHtml->ObtientUrl()).') ;
	bloc'.$this->IDInstanceCalc.'.Remplit() ;
</script>'.PHP_EOL ;
				return $ctn ;
			}
			public function InscritSupport(& $composantIU)
			{
				$this->ComposantSupport = & $composantIU ;
				$composantIU->AdopteComposantUI($this->IDInstanceCalc."_support", $this) ;
			}
			public function RecupContenu()
			{
				if($this->EstPasNul($this->Support))
				{
					return $this->Support->RenduDispositif() ;
				}
				return $this->ContenuHtml ;
			}
		}
		class PvBlocAjaxActRecupCtnHtml extends PvActionEnvoiFichierBaseZoneWeb
		{
			protected function AfficheContenu()
			{
				echo $this->ComposantIUParent->RecupContenu() ;
			}
		}
        
        class PvDatePick extends PvEditeurHtmlBase
        {
			protected static $SourceIncluse = 0 ;
            public static $CheminFichierJs = "js/ts_picker.js" ;
            public $CheminRepImgs = "images" ;
            public $DescriptifPopup = 'Afficher le calendrier' ;
            public $LibellesMois = array() ;
            public $LibellesJour = array() ;
            protected function RenduSourceIncluse() {
                $ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus(PvDatePick::$CheminFichierJs) ;
                $ctn .= $this->ZoneParent->RenduContenuJsInclus('ts_picker_arr_months = '.  svc_json_encode($this->LibellesMois).' ;
ts_picker_week_days = '.  svc_json_encode($this->LibellesMois)) ;
                return $ctn ;
            }
            protected function RenduEditeurBrut() {
                $ctn = '' ;
                $ctn .= '<input type="text" id="'.$this->IDInstanceCalc.'" name="'.htmlentities($this->NomElementHtml).'" value="'.htmlentities($this->Valeur).'" />';
                $ctn .= '
<a href="javascript:show_calendar(\''.$this->IDInstanceCalc.'\', document.getElementById(&quot;'.$this->IDInstanceCalc.'&quot;).value, '.  svc_json_encode_attr($this->CheminRepImgs).') ;"><img src="'.$this->CheminRepImgs.'/cal.gif" width="16" height="16" border="0" alt="'.htmlentities($this->DescriptifPopup).'"></a>' ;
                return $ctn ;
            }
        }
		
		class PvCmdAppelFonctJS extends PvCommandeRedirectionHttp
		{
			public $FenetreCible = "window" ;
			public $NomFonct = "" ;
			public $Params = array() ;
			protected function EnumParamsJS()
			{
				$ctn = '' ;
				foreach($this->Params as $i => $param)
				{
					if($ctn != '')
						$ctn .= ', ' ;
					$ctn .= svc_json_encode($param) ;
				}
				return $ctn ;
			}
			protected function ExecuteInstructions()
			{
				$ctn = '<script type="text/javascript">
	jQuery(function() {
		'.$this->FenetreCible.'.'.$this->NomFonct.'('.$this->EnumParamsJS().') ;
	}) ;
</script>'.PHP_EOL ;
				if($this->EstPasNul($this->TableauDonneesParent))
				{
					$this->TableauDonneesParent->ContenuAvantRendu .= $ctn ;
				}
				elseif($this->EstPasNul($this->FormulaireDonneesParent))
				{
					$this->FormulaireDonneesParent->ContenuAvantRendu .= $ctn ;
				}
			}
		}
		
		class PvDataSetupVideoJs
		{
		}
		class PvAttrsTagVideoJs
		{
			public $Skin = "vjs-default-skin" ;
			public $Preload = "auto" ;
			public $Largeur = "640" ;
			public $Hauteur = "264" ;
			public $InclureControles = 1 ;
			public $DataSetup ;
			public function __construct()
			{
				$this->DataSetup = new PvDataSetupVideoJs() ;
			}
		}
		class PvVideoJs extends PvComposantJSFiltrable
		{
			public $AttrsTag ;
			public $CheminFichierCSS = "css/video-js.min.css" ;
			public $CheminFichierJs = "js/video.min.js" ;
			public $InclureVtt = 0 ;
			public $CheminFichierVttJs = "js/videojs-vtt.js" ;
			public $CheminFichierSwf = "video-js.swf" ;
			public $NomColonneCheminVideo = "chemin_video" ;
			public $NomColonneTitre ;
			public $Largeur ;
			public $MessageAucunElement = 'Aucune vid&eacute;o trouv&eacute;e' ;
			public $MessageMauvaiseConfig = 'Le composant n\'a pas &eacute;t&eacute; configur&eacute; correctement.' ;
			public $ElementsEnCours = null ;
			public static $TypesMimeDefaut = array(
				'mp4' => 'video/mp4',
				'mpeg' => 'video/mpeg',
				'avi' => 'video/avi',
				'msvideo' => 'video/msvideo',
				'qt' => 'video/quicktime',
				'3gp' => 'video/3gpp',
				'mp3' => 'audio/mp3',
				'ogg' => 'audio/ogg',
				'wav' => 'audio/wav',
			) ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->AttrsTag = new PvAttrsTagVideoJs() ;
			}
			protected function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduContenuCSS($this->CheminFichierCSS) ;
				$ctn .= $this->RenduLienJs($this->CheminFichierJs) ;
				if($this->InclureVtt == 1)
				{
					$ctn .= $this->RenduLienJs($this->CheminFichierVttJs) ;
				}
				$ctn .= $this->RenduContenuJs('videojs.options.flash.swf = '.svc_json_encode($this->CheminFichierJs)) ;
				return $ctn ;
			}
			public function CalculeElementsRendu()
			{
				$this->ElementsEnCours = $this->FournisseurDonnees->SelectElements(array(), $this->ObtientFiltresSelection()) ;
			}
			protected function RenduDispositifBrutSpec()
			{
				$ctn = '' ;
				if(! is_array($this->ElementsEnCours))
				{
					$ctn .= '<p class="Erreur">'.htmlentities($this->FournisseurDonnees->MessageException()).'</p>' ;
					return $ctn ;
				}
				if(count($this->ElementsEnCours) > 0)
				{
					if(! isset($this->ElementsEnCours[0][$this->NomColonneCheminVideo]))
					{
						$ctn .= '<p class="Erreur">'.$this->MessageMauvaiseConfig.'</p>' ;
						return $ctn ;
					}
					$ctn .= '<video id="'.$this->IDInstanceCalc.'" class="video-js '.$this->AttrsTag->Skin.'"' ;
					$ctn .= (($this->AttrsTag->InclureControles == 1) ? ' controls' : '').' preload="'.$this->AttrsTag->Preload.'" width="'.$this->Largeur.'" height="'.$this->Hauteur.'" data-setup="'.htmlspecialchars(svc_json_encode($this->AttrsTag->DataSetup)).'"' ;
					$ctn .= '>'.PHP_EOL ;
					$extsTypesMime = array_keys(PvVideoJs::$TypesMimeDefaut) ;
					foreach($this->ElementsEnCours as $i => $lgn)
					{
						$cheminVideo = $lgn[$this->NomColonneCheminVideo] ;
						if($cheminVideo == '')
						{
							continue ;
						}
						$info = pathinfo($cheminVideo) ;
						$extension = strtolower($info["extension"]) ;
						if(! in_array($extension, $extsTypesMime))
						{
							continue ;
						}
						$titre = (isset($lgn[$this->NomColonneTitre])) ? $lgn[$this->NomColonneTitre] : substr($info["basename"], strlen($info["basename"]) - strlen($info["basename"]), strlen($info["basename"])) ;
						$ctn .= '<source src="'.htmlspecialchars($cheminVideo).'" title="'.htmlspecialchars($titre).'" type="'.PvVideoJs::$TypesMimeDefaut[$extension].'" >'.PHP_EOL ;
					}
					$ctn .= '<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>'.PHP_EOL ;
					$ctn .= '</video>' ;
				}
				else
				{
					$ctn .= '<p>'.$this->MessageAucunElement.'</p>' ;
				}
				return $ctn ;
			}
		}
		
		class PvDatetimePickerRainForest extends PvEditeurHtmlBase
		{
			protected static $SourceIncluse = 0 ;
            public static $CheminFichierJs = "js/datetimepicker_css.js" ;
            public $CheminRepImgs = "images" ;
            public $DescriptifPopup = 'Afficher le calendrier' ;
			public $FormatDatePHP = "d-m-Y H:i:s" ;
			public $FormatDateJs = "ddMMyyyy" ;
            protected function RenduSourceIncluse()
			{
                $ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus(PvDatetimePickerRainForest::$CheminFichierJs) ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('function fixeValeur'.$this->IDInstanceCalc.'() {
document.getElementById("'.$this->IDInstanceCalc.'").value = Cal.Year + "-" + Cal.Month + "-" + Cal.Date + " " + Cal.Hours + "-" + Cal.Minutes + "-" + Cal.Seconds ;
}') ;
                return $ctn ;
            }
            protected function RenduEditeurBrut()
			{
                $ctn = '' ;
				if($this->Valeur == "")
				{
					$this->Valeur = date($this->FormatDatePHP) ;
				}
				else
				{
					$this->Valeur = date($this->FormatDatePHP, strtotime($this->Valeur)) ;
				}
                $ctn .= '<input type="text" id="'.$this->IDInstanceCalc.'_Support" value="'.htmlspecialchars($this->Valeur).'" onchange="fixeValeur'.$this->IDInstanceCalc.'()" />' ;
                $ctn .= '<input type="hidden" id="'.$this->IDInstanceCalc.'" name="'.htmlspecialchars($this->NomElementHtml).'" value="'.htmlspecialchars($this->Valeur).'" />' ;
                $ctn .= '
<a href="javascript:NewCssCal(\''.$this->IDInstanceCalc.'_Support\',\''.$this->FormatDateJs.'\', \'dropdown\', true, \'24\', true)"><img src="'.$this->CheminRepImgs.'/cal.gif" border="0" alt="'.htmlspecialchars($this->DescriptifPopup).'"></a>' ;
                return $ctn ;
            }
		}
		
		class PvDatePickerRainForest extends PvDatetimePickerRainForest
		{
		}
	
	}
	
?>