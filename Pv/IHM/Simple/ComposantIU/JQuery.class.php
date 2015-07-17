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
			// Doit �tre initialis� avant la m�thode "ChargeConfig()"
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
        
        class ResultTypeahead
        {
            public $total_pages = 0 ;
            public $total_results = 0 ;
            public $page ;
            public $results = array() ;
        }
        class PvActEnvoiResultsTypeahead extends PvActionResultatJSONZoneWeb
        {
            /**
            * Composant Typeahead qui contient l'action
            *
            * @var PvTypeahead
            */
            public $ComposantRendu ;
            protected function ObtientFiltresSelection()
            {
                $filtres = $this->ComposantRendu->FiltresSelection ;
                $filtres["terme"] = $this->ScriptParent->CreeFiltreHttpGet($this->ComposantRendu->IDInstanceCalc."_terme") ;
                if(isset($this->ComposantRendu->FournisseurDonnees->BaseDonnees))
                {
                    $bd = & $this->ComposantRendu->FournisseurDonnees->BaseDonnees ;
                    $filtres["terme"]->ExpressionDonnees = $bd->SqlIndexOf('lower('.$bd->EscapeVariableName($this->ComposantRendu->NomColonneLibelle).')', 'upper(<self>)').' > 0' ;
                }
                // $filtres[] = $this->Sc
                return $filtres ;
            }
            protected function ObtientColonnesRendu()
            {
                $cols = array() ;
                $col1 = new PvDefinitionColonneDonnees() ;
                $col1->NomDonnees = "value" ;
                $col1->AliasDonnees = $this->ComposantRendu->NomColonneValeur ;
                $cols[] = $col1 ;
                $col2 = new PvDefinitionColonneDonnees() ;
                $col2->NomDonnees = "label" ;
                $col2->AliasDonnees = $this->ComposantRendu->NomColonneLibelle ;
                $cols[] = $col2 ;
                return $cols ;
            }
            protected function ConstruitResultat() {
                $result = new ResultTypeahead() ;
                if(is_array($result->results))
                {
                    $result->results = $this->ComposantRendu->FournisseurDonnees->SelectElements($this->ObtientColonnesRendu(), $this->ObtientFiltresSelection()) ;
                    $result->page = 1 ;
                    $result->total_pages = 1 ;
                    $result->total_results = count($result->results) ;
                }
                $this->Resultat = $result ;
            }
            
        }
        class PvTypeahead extends PvEditeurChoixBase
        {
            public $CheminFichierJs = "js/typeahead.bundle.min.js" ;
			public $LibelleEtiqVide = "" ;
            /**
            * Action generant les resultats JSON
            *
            * @var PvActEnvoiResultsTypeahead
            */
            public $ActEnvoiResults ;
            public $ParamsActEnvoiResults = array() ;
            public function AdopteScript($nom, & $script)
            {
                $this->ActEnvoiResults = new PvActEnvoiResultsTypeahead() ;
                $script->InscritActionAvantRendu($this->IDInstanceCalc.'_results', $this->ActEnvoiResults) ;
                $this->ActEnvoiResults->ComposantRendu = & $this ;
                parent::AdopteScript($nom, $script);
            }
            protected function RenduSourceBrut() {
                $ctn = '' ;
                $ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminFichierJs) ;
                $ctn .= $this->ZoneParent->RenduContenuCSS('.tt-query, .tt-hint {
	width: 396px;
	height: 30px;
	padding: 8px 12px;
	font-size: 24px;
	line-height: 30px;
	border: 2px solid #ccc;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	display:none ;
}
.tt-query {
	-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}
.tt-hint {
	color: #999
}
.tt-menu {
	width: 422px;
	margin: 2px 0;
	padding: 8px 0;
	background-color: #fff;
	border: 1px solid #ccc;
	border: 1px solid rgba(0, 0, 0, 0.2);
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	-webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	-moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	box-shadow: 0 5px 10px rgba(0,0,0,.2);
}
.tt-suggestion {
	padding: 3px 20px;
	font-size: 18px;
	line-height: 24px;
}
.tt-suggestion:hover {
	cursor: pointer;
	color: #fff;
	background-color: #0097cf;
}
.tt-suggestion.tt-cursor {
	color: #fff;
	background-color: #0097cf;
}
.tt-suggestion p {
	margin: 0;
}') ;
                return $ctn ;
            }
            protected function RenduEditeurBrut() {
                $this->ActEnvoiResults->Params = $this->ParamsActEnvoiResults ;
                $ctn = '' ;
                $ctn .= '<input class="typeahead" id="'.$this->IDInstanceCalc.'_libelle" type="text" placeholder="" value="'.htmlentities($this->RenduEtiquette()).'" />'.PHP_EOL ;
                $ctn .= '<input type="hidden" name="'.htmlentities($this->NomElementHtml).'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" />'.PHP_EOL ;
                $ctn .= $this->ZoneParent->RenduContenuJsInclus('var '.$this->IDInstanceCalc.'_dataset = new Bloodhound({
	datumTokenizer: function (datum) {
		return Bloodhound.tokenizers.whitespace(datum.title);
	},
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
		url: '.svc_json_encode($this->ActEnvoiResults->ObtientUrl()).',
		filter: function (dataset) {
            // Map the remote source JSON array to a JavaScript object array
            return jQuery.map(dataset.results, function (record) {
                return record ;
            });
        },
        replace: function(url, uriEncodedQuery) {
            return url + "&'.$this->IDInstanceCalc.'_terme=" + uriEncodedQuery;
        }
	}
});
// Initialize the Bloodhound suggestion engine
'.$this->IDInstanceCalc.'_dataset.initialize();
// Instantiate the Typeahead UI
jQuery("#'.$this->IDInstanceCalc.'_libelle").typeahead(null, {
	displayKey: "label",
	valueKey: "value",
	limit: 12,
	source: '.$this->IDInstanceCalc.'_dataset.ttAdapter()
});
jQuery("#'.$this->IDInstanceCalc.'_libelle").on("typeahead:selected typeahead:autocompleted", function(e,datum) {
	jQuery("#'.$this->IDInstanceCalc.'").val(datum.value);
}) ;') ;
                return $ctn ;
            }
        }
	}
	
?>