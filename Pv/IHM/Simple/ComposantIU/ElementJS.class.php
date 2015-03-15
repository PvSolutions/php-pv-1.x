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
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				$this->NomActionAffichImg = $this->IDInstanceCalc.'_AffichImg' ;
				$this->ActionAffichImg = new PvActionImgCommonCaptcha() ;
				$this->InscritActionAvantRendu($this->NomActionAffichImg, $this->ActionAffichImg) ;
			}
			protected function RenduDispositifBrut()
			{
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
		
	}
	
?>