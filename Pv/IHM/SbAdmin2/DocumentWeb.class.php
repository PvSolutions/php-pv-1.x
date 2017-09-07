<?php
	
	if(! defined('PV_DOCWEB_SB_ADMIN2'))
	{
		define('PV_DOCWEB_SB_ADMIN2', 1) ;
		
		class PvDocWebBaseSbAdmin2 extends PvDocumentHtmlSimple
		{
			protected function CtnJsHabillageCommun()
			{
				$ctn = 'jQuery(function() {
	jQuery(".Erreur").addClass("alert alert-danger") ;
	jQuery(".erreur").addClass("alert alert-danger") ;
	jQuery(".Succes").addClass("alert alert-info") ;
	jQuery(".succes").addClass("alert alert-info") ;
	jQuery(".FiltresNonRenseignes").addClass("alert alert-warning") ;
	jQuery(".AucunElement").addClass("alert alert-warning") ;
}) ;' ;
				return $ctn ;
			}
			protected function CtnJsBoiteDlgUrl(& $zone)
			{
				$ctn = '' ;
				$ctn .= 'var BoiteDlgUrl = {
	rafraichPageSurFerm : '.svc_json_encode($zone->RafraichPageSurFermDefautBoiteDlgUrl).',
	prepare : function() {
		var jqDlg = jQuery("#fenBoiteDlgUrl") ;
		jqDlg.on("hidden.bs.modal", function() {
			jQuery("#iframeBoiteDlgUrl").attr("src", "about:blank") ;
			if(BoiteDlgUrl.rafraichPageSurFerm) {
				window.location.href = window.location.href ;
			}
		}) ;
	},
	ouvre : function(titre, url, largeur, hauteur, rafraichPageSurFerm) {
		if(largeur === undefined || largeur === null) {
			largeur = '.$zone->LargeurDefautBoiteDlgUrl.' ;
		}
		if(hauteur === undefined || hauteur === null) {
			hauteur = '.$zone->HauteurDefautBoiteDlgUrl.' ;
		}
		if(rafraichPageSurFerm !== undefined && rafraichPageSurFerm === null) {
			BoiteDlgUrl.rafraichPageSurFerm = '.svc_json_encode($zone->RafraichPageSurFermDefautBoiteDlgUrl).' ;
		}
		var jqDlg = jQuery("#fenBoiteDlgUrl") ;
		jqDlg.find(".modal-dialog").css("width", largeur + "px") ;
		var jqFram = jQuery("#iframeBoiteDlgUrl") ;
		jqFram.css({ width : "98%", height : hauteur + "px"}) ;
		jqFram.attr("src", url) ;
		jqDlg.find(".modal-title").text(titre) ;
		jqDlg.modal("show") ;
	},
	ferme : function() {
		var jqDlg = jQuery("#fenBoiteDlgUrl") ;
		jqDlg.modal("hide") ;
	}
} ;
jQuery(function() {
	BoiteDlgUrl.prepare() ;
}) ;' ;
				return $ctn ;
			}
			protected function RenduBoiteDlgUrl(& $zone)
			{
				$ctn = '' ;
				$ctn .= '<div class="modal fade" id="fenBoiteDlgUrl" tabindex="-1" role="dialog" aria-labelledby="LibelleBoiteDlgUrl" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h4 class="modal-title" id="LibelleBoiteDlgUrl">...</h4>
</div>
<div class="modal-body">
<iframe id="iframeBoiteDlgUrl" src="about:blank" style="height:'.$zone->HauteurBoiteDlgDefaut.'" frameborder="0"></iframe>
</div>
</div>
</div>
</div>' ;
				return $ctn ;
			}
			public function PrepareRendu(& $zone)
			{
				$zone->InscritLienJs($zone->CheminJsBootstrap) ;
				$zone->InscritLienCSS($zone->CheminCSSBootstrap) ;
				if($zone->InclureThemeBootstrap == 1)
				{
					$zone->InscritLienCSS($zone->CheminThemeBootstrap) ;
				}
				$zone->InscritLienCSS($zone->CheminCSSFontAwesome) ;
				$this->RemplitLibrairiesSpec($zone) ;
				$zone->InscritLienCSS($zone->CheminCSSSbAdmin) ;
				$zone->InscritContenuJs(
					$this->CtnJsHabillageCommun()
					. PHP_EOL .
					$this->CtnJsBoiteDlgUrl($zone)
				) ;
			}
			protected function RemplitLibrairiesSpec(& $zone)
			{
			}
			public function RenduEnteteHtmlSimple(& $zone)
			{
				return parent::RenduEntete($zone) ;
			}
			public function RenduPiedHtmlSimple(& $zone)
			{
				$ctn = '' ;
				$ctn .= $this->RenduBoiteDlgUrl($zone) ;
				$ctn .= parent::RenduPied($zone) ;
				return $ctn ;
			}
		}
		class PvDocWebNonConnecteSbAdmin2 extends PvDocWebBaseSbAdmin2
		{
			protected function RemplitLibrairiesSpec(& $zone)
			{
			}
			public function RenduTitre(& $zone)
			{
				$ctn = '' ;
				$titre = $zone->ObtientTitreNonConnecte() ;
				if($titre == '')
				{
					return '' ;
				}
				return '<h1 align="center">'.$titre.'</h1><br />'.PHP_EOL ;
			}
			public function RenduArrPlan(& $zone)
			{
				if($zone->CheminImageArrPlanNonConnecte == "")
				{
					return "" ;
				}
				$ctn = '' ;
				$ctn .= '<img src="'.$zone->CheminImageArrPlanNonConnecte.'" style="position:absolute; width:100%; height:100%; z-index:-100000; left:0px" border="0" />'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduEntete(& $zone)
			{
				$scriptRendu = & $zone->ScriptPourRendu ;
				$ctn = $this->RenduEnteteHtmlSimple($zone).PHP_EOL ;
				$ctn .= '<div class="container">
<div class="row">
<div class="col-md-4 col-md-offset-4">
<div class="login-panel panel panel-default">'.PHP_EOL ;
				$ctn .= $this->RenduArrPlan($zone) ;
				$ctn .= $this->RenduTitre($zone) ;
				$ctn .= '<div class="panel-heading">
<h3 class="panel-title">'.$scriptRendu->Titre.'</h3>
</div>'.PHP_EOL ;
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduPied(& $zone)
			{
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>
</div>
</div>
</div>'.PHP_EOL ;
				$ctn = $this->RenduPiedHtmlSimple($zone).PHP_EOL ;
				return $ctn ;
			}
		}
		class PvDocWebConnecteSbAdmin2 extends PvDocWebBaseSbAdmin2
		{
			protected $Navbar1 ;
			protected $Navbar2 ;
			protected $Sidebar1 ;
			public $ClasseCSSNavbar = "navbar-default" ;
			protected function DetermineComposants(& $zone)
			{
				$this->Navbar1 = new PvNavbarStaticTopSbAdmin2() ;
				$this->Navbar1->AdopteScript("navbar1", $zone->ScriptPourRendu) ;
				$this->Navbar1->ChargeConfig() ;
				$this->Navbar2 = new PvNavbarTopLinksSbAdmin2() ;
				$this->Navbar2->AdopteScript("navbar2", $zone->ScriptPourRendu) ;
				$this->Navbar2->ChargeConfig() ;
				$this->Sidebar1 = new PvSidebarSbAdmin2() ;
				$this->Sidebar1->AdopteScript("sidebar1", $zone->ScriptPourRendu) ;
				$this->Sidebar1->ChargeConfig() ;
			}
			protected function CtnJsInitSbAdmin(& $zone)
			{
				$nomScriptAccueil = svc_json_encode($zone->NomScriptParDefaut) ;
				return '/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
var PvScriptLocation = function(url)
{
	var cheminDefaut = window.location.href.replace(/\#.*/g, "").split("?", 2)[0] ;
	
	url = url.replace(/\#.*/g, "") ;
	var attrs = url.split("?") ;
	this.chemin = (attrs[0] === "") ? cheminDefaut : attrs[0] ;
	this.donneesGet = {} ;
	if(attrs.length == 2 && attrs[1] != "")
	{
		var queryParams = attrs[1].split("&") ;
		for(var i=0; i<queryParams.length; i++)
		{
			var queryAttrs = queryParams[i].split("=", 2) ;
			this.donneesGet[queryAttrs[0]] = (queryAttrs.length == 2) ? queryAttrs[1] : null ;
			// alert(url + " " + attrs[1] + " " + queryParams[i] + " " + queryAttrs[0] + " " + queryAttrs[1]) ;
		}
	}
	this.valeurScriptAppele = (this.donneesGet["'.$zone->NomParamScriptAppele.'"] !== undefined) ? this.donneesGet["'.$zone->NomParamScriptAppele.'"] : '.$nomScriptAccueil.' ;
	this.valeurActionAppelee = (this.donneesGet["'.$zone->NomParamActionAppelee.'"] !== undefined) ? this.donneesGet["'.$zone->NomParamActionAppelee.'"] : "" ;
	var _self = this ;
	_self.estEgal = function(scriptLocation) {
		if(scriptLocation.chemin.indexOf("javascript:") > -1) {
			// alert(scriptLocation.chemin) ;
			return false ;
		}
		// alert(_self.chemin + " " + _self.valeurScriptAppele + " " + scriptLocation.valeurScriptAppele) ;
		return (_self.chemin === scriptLocation.chemin && _self.valeurScriptAppele === scriptLocation.valeurScriptAppele && _self.valeurActionAppelee === scriptLocation.valeurActionAppelee) ;
	}
}
//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
jQuery(function() {
    jQuery("#side-menu").metisMenu();
    jQuery(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            jQuery("div.navbar-collapse").addClass("collapse");
            topOffset = 100; // 2-row-menu
        } else {
            jQuery("div.navbar-collapse").removeClass("collapse");
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            jQuery("#page-wrapper").css("min-height", (height) + "px");
        }
    });

	var scriptLocationActuel = new PvScriptLocation(window.location.href) ;
    // var element = jQuery("ul.nav a").filter(function() {
    //     return this.href == url;
    // }).addClass("active").parent().parent().addClass("in").parent();
    var element = jQuery("ul.nav a").filter(function() {
		// alert(this.href) ;
		var scriptLocationDem = new PvScriptLocation(this.href) ;
		return scriptLocationActuel.estEgal(scriptLocationDem) ;
    }).addClass("active").parent();

    while (true) {
        if (element.is("li")) {
            element = element.parent().addClass("in").parent();
        } else {
            break;
        }
    }
});' ;
			}
			protected function RemplitLibrairiesSpec(& $zone)
			{
				$zone->InscritLienCSS($zone->CheminCSSMetisMenu) ;
				$zone->InscritContenuJs($this->CtnJsInitSbAdmin($zone)) ;
				$zone->InscritLienJs($zone->CheminJsMetisMenu) ;
				$this->DetermineComposants($zone) ;
				if($zone->CouleurTexteSidebars != '')
				{
					$zone->InscritContenuCSS('.sidebar ul li a
{
color:'.$zone->CouleurTexteSidebars.' ;
}
.navbar-default .active, .navbar-default a:hover, .sidebar .in a {
color:'.$zone->CouleurTexteSidebars.' ;
}') ;
				}
			}
			public function RenduEntete(& $zone)
			{
				$scriptRendu = & $zone->ScriptPourRendu ;
				$ctn = $this->RenduEnteteHtmlSimple($zone).PHP_EOL ;
				$ctn .= '<div id="wrapper">'.PHP_EOL ;
				$ctn .= '<nav class="navbar '.$this->ClasseCSSNavbar.' navbar-static-top" role="navigation" style="margin-bottom: 0">'.PHP_EOL ;
				$ctn .= $this->Navbar1->RenduDispositif() ;
				$ctn .= $this->Navbar2->RenduDispositif() ;
				$ctn .= $this->Sidebar1->RenduDispositif() ;
				$ctn .= '</nav>'.PHP_EOL ;
				$ctn .= '<div id="page-wrapper">
<div class="row">
<div class="col-lg-12">
<h1 class="page-header">'.$scriptRendu->Titre.'</h1>
</div>
</div>' ;
				return $ctn ;
			}
			public function RenduPied(& $zone)
			{
				$ctn .= '</div>'.PHP_EOL ;
				$ctn = $this->RenduPiedHtmlSimple($zone).PHP_EOL ;
				return $ctn ;
			}
		}
		class PvDocWebCadreSbAdmin2 extends PvDocWebBaseSbAdmin2
		{
		}
	}
	
?>