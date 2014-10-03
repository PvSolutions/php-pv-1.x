<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_JQUERY'))
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
		define('PV_COMPOSANT_SIMPLE_JQUERY', 1) ;
		
		class PvConfigJQueryTreeview
		{
			public $persist = "location" ;
			public $collapsed = false ;
			public $unique = false ;
			public $cookieId = "" ;
		}
		class PvJQueryTreeview extends PvBarreMenuWebBase
		{
			protected static $SourceIncluse = 0 ;
			public $Config ;
			public $CheminCSS = "css/jquery.treeview.css" ;
			public $CheminJsJQueryCookie = "js/jquery.cookie.js" ;
			public $UtiliserJQueryCookie = 1 ;
			public $CheminJs = "js/jquery.treeview.js" ;
			public $AppliquerJQueryUi = 1 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Config = new PvConfigJQueryTreeview() ;
			}
			protected function RenduSourceIncluse()
			{
				$sourceInc = $this->ObtientValeurStatique("SourceIncluse") ;
				if($sourceInc)
				{
					return "" ;
				}
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienCSS($this->CheminCSS) ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminJs) ;
				if($this->UtiliserJQueryCookie)
				{
					$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminJsJQueryCookie) ;
				}
				$this->AffecteValeurStatique("SourceIncluse", 1) ;
				return $ctn ;
			}
			protected function RenduDefinitionJs()
			{
				$ctn = '' ;
				$ctn .= 'jQuery(function() {'.PHP_EOL ;
				$ctn .= 'var selection = jQuery("#'.$this->IDInstanceCalc.'") ;'.PHP_EOL ;
				if($this->AppliquerJQueryUi && $this->ZoneParent->InclureJQueryUi)
				{
					$ctn .= 'selection.addClass("ui-widget ui-state-default") ;'.PHP_EOL ;
					// $ctn .= 'selection.find("ul").css("background", "none") ;'.PHP_EOL ;
				}
				$ctn .= 'selection.treeview('.svc_json_encode($this->Config).') ;'.PHP_EOL ;
				$ctn .= '}) ;' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduSourceIncluse() ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus($this->RenduDefinitionJs()) ;
				return $ctn ;
			}
		
		}
		
		class PvLeftSlideBarJQuery extends PvComposantIUBase
		{
			protected $CheminJs = "js/slidebars.js" ;
			protected $CheminCSS = "css/slidebars.css" ;
			protected $NomClsCSSSlideBar = "sb-left" ;
			protected static $SourceIncluse = 0 ;
			// Doit être initialisé avant la méthode "ChargeConfig()"
			public $ComposantSupport ;
			public $LibelleLien = "Ouvrir le menu" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ComposantSupport = new PvPortionRenduHtml() ;
			}
			public function & DeclareComposantSupport($comp)
			{
				$this->RemplaceCompSupport($comp) ;
				return $comp ;
			}
			public function & DeclareCompSupport($comp)
			{
				$this->RemplaceCompSupport($comp) ;
				return $comp ;
			}
			public function RemplaceComposantSupport(& $comp)
			{
				$this->ComposantSupport = & $comp ;
				if($this->EstPasNul($this->ScriptParent))
				{
					$this->ComposantSupport->AdopteScript('support_'.$this->NomElementScript, $this->ScriptParent) ;
				}
				if($this->EstPasNul($this->ZoneParent))
				{
					$this->ComposantSupport->AdopteZone('support_'.$this->NomElementZone, $this->ZoneParent) ;
				}
			}
			public function RemplaceCompSupport(& $comp)
			{
				$this->RemplaceComposantSupport($comp) ;
			}
			public function InsereCompSupport($comp)
			{
				$this->RemplaceComposantSupport($comp) ;
			}
			public function AdopteScript($nom, & $script)
			{
				parent::AdopteScript($nom, $script) ;
				if($this->EstPasNul($this->ComposantSupport))
				{
					$this->ComposantSupport->AdopteScript($nom.'_support', $script) ;
				}
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				if($this->EstPasNul($this->ComposantSupport) && $this->EstNul($this->ScriptParent))
				{
					$this->ComposantSupport->AdopteZone($nom.'_support', $zone) ;
				}
			}
			protected function RenduSourceIncluse()
			{
				if($this->ObtientValeurStatique("SourceIncluse"))
				{
					return "" ;
				}
				$ctn = "" ;
				$ctn .= $this->ZoneParent->RenduLienCSS($this->CheminCSS) ;
				$ctn .= $this->ZoneParent->RenduContenuCSS('.sb-slidebar {
	padding: 14px;
	color: #fff;
}
html.sb-active #sb-site, .sb-toggle-left, .sb-toggle-right, .sb-open-left, .sb-open-right, .sb-close {
	cursor: pointer;
}
/* Fixed position examples */
#fixed-top {
	position: fixed;
	top: 0;
	width: 100%;
	height: 50px;
	background-color: red;
	z-index: 4;
}
#fixed-top span.sb-toggle-left {
	float: left;
	color: white;
	padding: 10px;
}
#fixed-top span.sb-toggle-right {
	float: right;
	color: white;
	padding: 10px;
}') ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminJs) ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus($this->RenduDefinitionJs()) ;
				$this->AffecteValeurStatique("SourceIncluse", 1) ;
				return $ctn ;
			}
			protected function RenduDefinitionJs()
			{
				$ctn = '' ;
				$ctn .= '(function(jQuery) {
	jQuery(document).ready(function() {
		jQuery.slidebars();
	});
}) (jQuery);' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduSourceIncluse() ;
				$ctn .= '<div id="Lien'.$this->IDInstanceCalc.'"><a href="javascript:;" class="sb-open-left">'.$this->LibelleLien.'</a></div>'.PHP_EOL ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="sb-slidebar '.$this->NomClsCSSSlideBar.'">'.PHP_EOL ;
				if($this->EstPasNul($this->ComposantSupport))
				{
					$ctn .= $this->ComposantSupport->RenduDispositif() ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class PvConfigMaskMoney
		{
			public $prefix = "" ;
            public $suffix = "" ;
			public $affixesStay = true ;
			public $thousands = " " ;
			public $decimal = "" ;
			public $precision = 0 ;
			public $allowZero = false ;
			public $allowNegative = false ;
		}
		class PvMaskMoneyJQuery extends PvZoneInvisibleHtml
		{
			public static $SourceIncluse = 0 ;
			public $Config ;
			protected $ValeurEditeur ;
			public $CheminJs = "js/jquery.maskMoney.js" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Config = new PvConfigMaskMoney() ;
			}
			public function InclutLibSource()
			{
				$ctn = '' ;
				if($this->ObtientValeurStatique('SourceIncluse') == 1)
				{
					return $ctn ;
				}
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminJs) ;
				$this->AffecteValeurStatique("SourceIncluse", 1) ;
				return $ctn ;
			}
			protected function PrepareEditeur()
			{
				$this->ValeurEditeur = $this->Valeur ;
				if($this->Config->precision > 0 && intval($this->Valeur) != $this->Valeur)
				{
					$this->ValeurEditeur .= ".".str_repeat("0", $this->Config->precision) ;
				}
			}
			protected function RenduEditeur()
			{
				$ctn = '' ;
				$this->PrepareEditeur() ;
				$ctn .= '<input id="Editeur_'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' value="'.htmlentities($this->ValeurEditeur).'"' ;
				$ctn .= ' type="text"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= ' />' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->InclutLibSource() ;
				$ctn .= $this->RenduEditeur() ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('jQuery(function () {
	jQuery("#Editeur_'.$this->IDInstanceCalc.'").maskMoney('.svc_json_encode($this->Config).')
		.change(function () {
			if(jQuery(this).val() == "")
			{
				jQuery("#'.$this->IDInstanceCalc.'").val(jQuery(this).val()) ;
				return ;
			}
			var val = jQuery(this).maskMoney("unmasked") ;
			if(val[0] != undefined)
				val = val[0] ;'.(($this->Config->precision == 0) ? '
			alert(Math.pow(10, ((String(val).length > 2) ? 3 : String(val).length - 1))) ;
			val = val * Math.pow(10, ((String(val).length > 2) ? 3 : String(val).length - 1)) ;' : '').'
			jQuery("#'.$this->IDInstanceCalc.'").val(val) ;
		})
		.maskMoney("mask") ;
}) ;') ;
				return $ctn ;
			}
		}
	}
	
?>