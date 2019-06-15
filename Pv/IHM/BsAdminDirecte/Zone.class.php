<?php
	
	if(! defined('PV_ZONE_BS_ADMIN_DIRECTE'))
	{
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/../Simple.class.php" ;
		}
		if(! defined('PV_COMPOSANT_BS_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('PV_MENU_BS_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/Menu.class.php" ;
		}
		define('PV_ZONE_BS_ADMIN_DIRECTE', 1) ;
		
		class PvRemplisseurConfigMembershipBsAdminDirecte extends PvRemplisseurConfigMembershipSimple
		{
			public $NomClasseLienModifTableauMembre = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienSupprTableauMembre = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienModifTableauRole = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienSupprTableauRole = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienModifTableauProfil = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienSupprTableauProfil = "PvConfigFormatteurColonneOuvreOnglet" ;
		}
		
		class PvZoneWebBsAdminDirecteTypeRendu
		{
			const NonDefini = 0 ;
			const NonConnecte = 1 ;
			const ParDefaut = 2 ;
			const Script = 3 ;
		}
		
		class PvZoneWebBsAdminDirecte extends PvZoneWebSimple
		{
			public $BarreMenuSuperfish = null ;
			protected $TypeRendu = 0 ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplisseurConfigMembershipBsAdminDirecte" ;
			public $NomScriptBienvenue = "bienvenue" ;
			protected $LargeurFenInscription = 525 ;
			protected $HauteurFenInscription = 450 ;
			public $InclureScriptsMembership = 1 ;
			public $ImageArrierePlanDocument = "images/bg-document.png" ;
			public $EtirerImageArrierePlanDocument = 1 ;
			public $CheminJsDialogExtend = "" ;
			public $CheminJsSuperfish = "js/superfish.min.js" ;
			public $CheminCSSSuperfish = "css/superfish.css" ;
			public $CouleurArrierePlanDocument = "black" ;
			public $TaillePoliceDocument = "12px" ;
			public $FormatPoliceDocument = "arial" ;
			public $CouleurPoliceEnteteDocument = "white" ;
			public $LargeurEnteteDocument = "90px" ;
			public $EspacementEnteteDocument = "8px" ;
			public $CouleurPolicePiedDocument = "white" ;
			public $EspacementPiedDocument = "8px" ;
			public $LargeurPiedDocument = "" ;
			public $CouleurArrierePlanConteneurMenuPrincipal = "#7faae5" ;
			public $LargeurImageArrierePlanDocument = "98%" ;
			public $HauteurImageArrierePlanDocument = "100%" ;
			public $LargeurEspaceTravail = "92%" ;
			public $CouleurArrierePlanEspaceTravail = "white" ;
			public $HauteurGroupeOnglets = "585px" ;
			public $HauteurOnglet = "520px" ;
			public $RetraitOnglet = "45" ;
			public $HauteurNouvFenetre = "600" ;
			public $LargeurNouvFenetre = "525" ;
			public $TaillePoliceMenuPrincipal = "12px" ;
			public $CouleurPoliceMenuPrincipal = "black" ;
			public $PoidsPoliceMenuPrincipal = "none" ;
			public $CouleurArrierePlanEnCoursMenuPrincipal = "#7faae5" ;
			public $CouleurArrierePlanMenuPrincipal = "#7faae5" ;
			public $CouleurArrierePlanSousMenuPrincipal = "#7faae5" ;
			public $CouleurArrierePlanSelectionMenuPrincipal = "#9fc5f8" ;
			public $ContenuPiedDocument = "Zone d'administration directe, pour la biblioth&egrave;que PView &copy; tous droits r&eacute;serv&eacute;s." ;
			public $MessageIntroduction = "Bienvenue dans l'espace d'administration directe, pour la biblioth&egrave;que PView." ;
			public $InclureRenduTitre = 0 ;
			public $InclureRenduChemin = 0 ;
			public $InclureRenduEntete = 1 ;
			public $ContenuRenduEntete = '<p>&nbsp;</p>' ;
			public $InclureRenduDescription = 0 ;
			public $CacherIconeOnglet = 0 ;
			public $InscrireMenuMembership = 1 ;
			public $PrivilegesMenuMembership = array() ;
			public $LibelleMenuMembership = "Authentification" ;
			public $InscrireMenuDeconnexion = 1 ;
			public $LibelleMenuDeconnexion = "D&eacute;connexion" ;
			public $InscrireMenuChangeMotPasse = 1 ;
			public $LibelleMenuChangeMotPasse = "Changer mot de passe" ;
			public $InscrireMenuListeMembres = 1 ;
			public $LibelleMenuListeMembres = "Tous les Membres" ;
			public $InscrireMenuAjoutMembre = 1 ;
			public $LibelleMenuAjoutMembre = "Ajout membre" ;
			public $InscrireMenuListeRoles = 1 ;
			public $LibelleMenuListeRoles = "Tous les roles" ;
			public $InscrireMenuAjoutRole = 1 ;
			public $LibelleMenuAjoutRole = "Ajout role" ;
			public $InscrireMenuListeProfils = 1 ;
			public $LibelleMenuListeProfils = "Tous les profils" ;
			public $InscrireMenuAjoutProfil = 1 ;
			public $LibelleMenuAjoutProfil = "Ajout profil" ;
			public $MenuChangeMotPasse ;
			public $MenuListeMembres ;
			public $MenuAjoutMembre ;
			public $MenuListeRoles ;
			public $MenuAjoutRole ;
			public $MenuListeProfils ;
			public $MenuAjoutProfil ;
			public $MenuDeconnexion ;
			public $MenuAuthentification = null ;
			public $CompAvantEspaceTravail = null ;
			public $CompApresEspaceTravail = null ;
			public $FenetresRedimensionnable = "true" ;
			public $FenetresDraggable = "true" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->BarreMenuSuperfish = new PvBarreMenuBsAdminDirecte() ;
			}
			protected function AfficheRenduInacessible()
			{
				$url = $this->ScriptConnexion->ObtientUrl() ;
				echo '<script type=text/javascript>
	var urlConnexion = '.svc_json_encode($url).' ;
	// alert(window.top.document.body.id + " " + window.document.body.id) ;
	// alert("mmm") ;
	window.location = urlConnexion ;
</script>' ;
				exit ;
			}
			public function ContenuJsOuvreOngletScript($nomScript)
			{
				if(! isset($this->Scripts[$nomScript]))
					return "" ;
				$cheminIcone = $this->Scripts[$nomScript]->CheminIcone ;
				if($cheminIcone == '')
					$cheminIcone = $this->BarreMenuSuperfish->CheminIconeParDefaut ;
				return 'ouvreOngletCadre("'.$nomScript.'", '.svc_json_encode($cheminIcone).', '.svc_json_encode($this->Scripts[$nomScript]->ObtientTitreDocument()).', "'.$this->ObtientUrlScript($nomScript).'")' ;
			}
			public function InclutLibrairiesExternes()
			{
				$this->InclureJQuery = 1 ;
				$this->InclureBootstrap = 1 ;
				$this->InclureFontAwesome = 1 ;
				parent::InclutLibrairiesExternes() ;
				if($this->TypeRendu == PvZoneWebBsAdminDirecteTypeRendu::ParDefaut)
				{
					$this->InscritContenuJs($this->ObtientContenuJsBsAdminDirecte()) ;
					$this->InscritContenuCSS($this->ObtientContenuCSSBsAdminDirecte()) ;
					$this->InscritContenuCSS($this->ObtientContenuCSSBsMenu()) ;
				}
				elseif($this->TypeRendu == PvZoneWebBsAdminDirecteTypeRendu::NonConnecte)
				{
					$this->InscritContenuJs($this->ObtientContenuJsNonConnecte()) ;
					$this->InscritContenuCSS($this->ObtientContenuCSSNonConnecte()) ;
				}
				elseif($this->TypeRendu == PvZoneWebBsAdminDirecteTypeRendu::Script)
				{
					$this->InscritContenuJs($this->ObtientContenuJsScript()) ;
					$this->InscritContenuCSS($this->ObtientContenuCSSScript()) ;
				}
			}
			protected function ObtientContenuCSSNonConnecte()
			{
				$ctn = '' ;
				$ctn .= 'body
{
'.(($this->ImageArrierePlanDocument != '' && ! $this->EtirerImageArrierePlanDocument) ? 'background-image:url('.$this->ImageArrierePlanDocument.') ;
	background-repeat:no-repeat ;
	background-position:top center ;
' : '').'background-color:'.$this->CouleurArrierePlanDocument.' ;
	text-align:center;
	padding:0px ;
	margin:10px ;
}'.PHP_EOL ;
				if($this->ImageArrierePlanDocument != '' && $this->EtirerImageArrierePlanDocument)
				{
					$ctn .= '#imageFond { width: '.$this->LargeurImageArrierePlanDocument.'; height:'.$this->HauteurImageArrierePlanDocument.'; z-index:-100000; position:absolute; left:10px }'.PHP_EOL ;
				}
				$ctn .= '/*
p, table, td, th, p, div, input, textarea
{
	font-family:'.$this->FormatPoliceDocument.' ;
	font-size:'.$this->TaillePoliceDocument.' ;
}
#entete
{
	color:'.$this->CouleurPoliceEnteteDocument.' ;
	height:'.$this->LargeurEnteteDocument.' ;
}
#pied
{
	padding:'.$this->EspacementPiedDocument.' ;
	color:'.$this->CouleurPolicePiedDocument.' ;
}
*/
.conteneurMenuPrincipal
{
	background-color:'.$this->CouleurArrierePlanConteneurMenuPrincipal.' ;
}
#espaceTravail
{
	width:'.$this->LargeurEspaceTravail.' ;
	background-color : '.$this->CouleurArrierePlanEspaceTravail.' ;
}
#fenetreConnexion {
	display:none;
}' ;
				return $ctn ;
			}
			protected function ObtientContenuCSSScript()
			{
				$ctn = '' ;
				// Faire confiance à Bootstrap :)
				/*
				$ctn .= 'body, p, table, td, th, p, div, input, textarea
{
	font-family:'.$this->FormatPoliceDocument.' ;
	font-size:'.$this->TaillePoliceDocument.' ;
}' ;
				*/
				return $ctn ;
			}
			protected function ObtientContenuCSSBsAdminDirecte()
			{
				$ctn = '' ;
				$ctn .= 'body
{
	'.(($this->ImageArrierePlanDocument != '' && ! $this->EtirerImageArrierePlanDocument) ? 'background-image:url('.$this->ImageArrierePlanDocument.') ;
	background-repeat:no-repeat ;
	background-position:top center ;
' : '').'	background-color:'.$this->CouleurArrierePlanDocument.' ;
	text-align:center;
	padding:0px ;
	margin:10px ;
}'.PHP_EOL ;
				if($this->ImageArrierePlanDocument != '' && $this->EtirerImageArrierePlanDocument)
				{
					$ctn .= '#imageFond { width: '.$this->LargeurImageArrierePlanDocument.'; height:'.$this->HauteurImageArrierePlanDocument.'; z-index:-100000; position:absolute; left:10px }'.PHP_EOL ;
				}
				$ctn .= '.nav-tabs > li .close {
margin: -2px 0 0 10px;
font-size: 18px;
}
#entete
{
	color:'.$this->CouleurPoliceEnteteDocument.' ;
	height:'.$this->LargeurEnteteDocument.' ;
}
#pied
{
	padding:'.$this->EspacementPiedDocument.' ;
	color:'.$this->CouleurPolicePiedDocument.' ;
}
.conteneurMenuPrincipal
{
	background-color:'.$this->CouleurArrierePlanConteneurMenuPrincipal.' ;
}
#espaceTravail
{
	width:'.$this->LargeurEspaceTravail.' ;
	background-color : '.$this->CouleurArrierePlanEspaceTravail.' ;
}
#groupeOnglets
{
	margin-top: 0px;
	height:'.$this->HauteurGroupeOnglets.' ;
}
#groupeOnglets .iconeOnglet
{
	float:left;
	display:'.(($this->CacherIconeOnglet) ? 'none' : 'block').';
	padding-top:4px ;
	padding-left:4px ;
}
#groupeOnglets .ui-tabs-nav .ui-icon
{ 
	display:inline-block;
}
#groupeOnglets li .ui-icon-close
{
	float: left;
	margin: 0.4em 0.2em 0 0;
	cursor: pointer;
}
.modal-header .btnGrp {
  position: absolute;
  top: 8px;
  right: 10px;
}
.min {
  width: 250px;
  height: 35px;
  overflow: hidden !important;
  padding: 0px !important;
  margin: 0px;
  float: left;
  position: static !important;
}
.min .modal-dialog, .min .modal-content {
  height: 100%;
  width: 100%;
  margin: 0px !important;
  padding: 0px !important;
}
.min .modal-header {
  height: 100%;
  width: 100%;
  margin: 0px !important;
  padding: 3px 5px !important;
}
.display-none { display: none; }
.min .fa { font-size: 14px; }
.min .menuTab { display: none; }
.minmaxCon {
  height: 35px;
  bottom: 1px;
  left: 1px;
  position: fixed;
  right: 1px;
  z-index: 9999;
}' ;
				return $ctn ;
			}
			protected function ObtientContenuCSSBsMenu()
			{
				return '.dropdown-submenu {
    position:relative;
}
.dropdown-submenu>.dropdown-menu {
    top:0;
    left:100%;
    margin-top:-6px;
    margin-left:-1px;
    -webkit-border-radius:0 6px 6px 6px;
    -moz-border-radius:0 6px 6px 6px;
    border-radius:0 6px 6px 6px;
}
.dropdown-submenu:hover>.dropdown-menu {
    display:block;
}
.dropdown-submenu>a:after {
    display:block;
    content:" ";
    float:right;
    width:0;
    height:0;
    border-color:transparent;
    border-style:solid;
    border-width:5px 0 5px 5px;
    border-left-color:#cccccc;
    margin-top:5px;
    margin-right:-10px;
}
.dropdown-submenu:hover>a:after {
    border-left-color:#ffffff;
}
.dropdown-submenu.pull-left {
    float:none;
}
.dropdown-submenu.pull-left>.dropdown-menu {
    left:-100%;
    margin-left:10px;
    -webkit-border-radius:6px 0 6px 6px;
    -moz-border-radius:6px 0 6px 6px;
    border-radius:6px 0 6px 6px;
}' ;
			}
			protected function ObtientContenuJsNonConnecte()
			{
				$ctn = '' ;
				$ctn .= 'jQuery(function()
{
	ouvreFenetreConnexion() ;
}) ;
function ouvreFenetreConnexion()
{
	jQuery(\'#fenetreConnexion\').modal();
	jQuery(\'#fenetreConnexion\').find(":input").keypress(function(evt) {
		if(evt.which == 13)
		{
			jQuery(this).closest("form").submit() ;
			jQuery(\'#fenetreConnexion\').dialog("close") ;
		}
	}) ;
}' ;
				if($this->AutoriserInscription == 1)
				{
					$ctn .= '
function ouvreFenetreInscription()
{
	jQuery(\'#fenetreInscription\').modal("show");
}
function fermeFenetreInscription()
{
	jQuery(\'#fenetreInscription\').modal("hide");
}
jQuery(function() {
	// jQuery(\'#fenetreInscription\').modal() ;
}) ;' ;
				}
				return $ctn ;
			}
			protected function ObtientContenuJsScript()
			{
				$ctn = '' ;
				$ctn .= 'jQuery(window).load(function() {
if(window.top && window.top.fenetreOuverte !== undefined && window.top.fenetreOuverte()) {
setTimeout(function() {
var hauteur = 0 ;
jQuery("body").children().each(function() {
	var jqNoeud = jQuery(this) ;
	if(jqNoeud.is(":hidden")) {
		return ;
	}
	hauteur += jQuery(this).innerHeight() ;
}) ;
window.top.definitHauteurFenetre(hauteur) ;
},
500) ;
}
}) ;' ;
				return $ctn ;
			}
			protected function ObtientContenuJsBsAdminDirecte()
			{
				$ctn = '' ;
				$ctn .= '
var autoRafraich = '.($this->ActiverRafraichScript && ($this->ScriptPourRendu->DoitAutoRafraich()) ? 'true' : 'false').' ;
function htmlEncode(value){
  return jQuery(\'<div/>\').text(value).html();
}
function htmlDecode(value){
  return jQuery(\'<div/>\').html(value).text();
}
var optionsOuvreFenetreDefaut = {
	Titre : "Nouvelle Fenetre",
	CacheFenetre : false,
	Icone : "images/icones/fichier.png",
	LargeurMin : "",
	HauteurMin : "",
	Largeur : '.svc_json_encode($this->LargeurNouvFenetre).',
	Hauteur : '.svc_json_encode($this->HauteurNouvFenetre).',
	LargeurMax : "",
	HauteurMax : "",
	Modal : false,
	Redimensionnable : '.$this->FenetresRedimensionnable.',
	Draggable : '.$this->FenetresDraggable.',
	OuvrirEnMemeTemps : true,
	FermerSurEchap : false,
	NomClasseFenetre : "",
	BoutonReduire : false,
	BoutonFermer : true,
	LibelleFermer : "Fermer",
	BoutonConfimer : null,
	LibelleConfirmer : "OK",
	ExecuteBoutonConfirmer : undefined,
	RafraichOnglActifSurFerm : 0,
	UrlOnglActifSurFerm : "",
} ;
function ouvreFenetreCadre(idFenetre, icone, titre, urlCadre, options)
{
	return ouvreFenetre(
		idFenetre,
		icone,
		titre,
		\'<iframe src="\' + urlCadre + \'" style="width:100%;" height="100%" frameborder="0" scrolling="true">Veuillez mettre votre navigateur a jour</iframe>\',
		options
	) ;
}
function ouvreFenetre(idFenetre, icone, titre, contenu, options)
{
	var fenetreExistante = jQuery("#" + idFenetre) ;
	if(optionsOuvreFenetreDefaut.CacheFenetre == false && fenetreExistante.length > 0)
	{
		// alert(fenetreExistante.length) ;
		fenetreExistante.modal("hide") ;
		fenetreExistante.remove() ;
		fenetreExistante = jQuery("#" + idFenetre) ;
	}
	if(autoRafraich) {
		annulAutoRafraich() ;
	}
	if(fenetreExistante.length == 0)
	{
		largeurFenetre = (options.Largeur !== undefined) ? options.Largeur : null ;
		titreFenetre = titre ;
		var contenuDiv = \'\' ;
		contenuDiv += \'<div class="modal fade" id="\' + htmlEncode(idFenetre) + \'" title="\' + htmlEncode(titreFenetre) + \'" tabindex="-1" role="dialog" aria-labelledby="\' + htmlEncode(idFenetre) + \'_lbl">\' ;
		contenuDiv += \'<div class="modal-dialog" role="document"><div class="modal-content"\' + ((largeurFenetre !== null) ? \' style="width:\' + largeurFenetre + \'px"\': \'\') + \'><div class="modal-header">\' ;
		if(options.BoutonReduire !== undefined && options.BoutonReduire === true)
		{
			contenuDiv += \'<button class="close modalMinimize"> <i class="fa fa-minus"></i> </button>\' ;
		}
		contenuDiv += \'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\' ;
		contenuDiv += \'<h4 class="modal-title" id="\' + htmlEncode(idFenetre) + \'_lbl">\' + titreFenetre + \'</h4></div><div class="modal-body">\' ;
		// titreFenetre doit etre encodee avant de passer la variable
		contenuDiv += contenu ;
		contenuDiv += \'</div></div></div></div>\' ;
		var blocFenetre = jQuery(contenuDiv) ;
		// alert(contenuDiv) ;
		objBarreFenetre.append(blocFenetre) ;
		blocFenetre.modal("show").on("hidden.bs.modal", function () {
			jQuery(this).data("bs.modal", null);
			jQuery(this).remove();
			if(options.RafraichOnglActifSurFerm === undefined || options.RafraichOnglActifSurFerm === 1) {
				rafraichitOngletActif() ;
			}
			else {
				if(options.UrlOnglActifSurFerm !== undefined) {
					rafraichitUrlOngletActif(options.UrlOnglActifSurFerm) ;
				}
				else {
					if(autoRafraich) {
						demarreAutoRafraich() ;
					}
				}
			}
		}) ;
		if(options.BoutonReduire == true)
		{
			blocFenetre.find(".modalMinimize").on("click", function(){
				jQuerymodalCon = jQuery(this).closest(".modal").attr("id");
				jQueryapnData = jQuery(this).closest(".modal");
				jQuerymodal = "#" + jQuerymodalCon;
				jQuery(".modal-backdrop").addClass("display-none");
				jQuery(jQuerymodal).toggleClass("min");
				if(jQuery(jQuerymodal).hasClass("min")) {
					jQuery(".minmaxCon").append(jQueryapnData);
					jQuery(this).find("i").toggleClass("fa-minus").toggleClass("fa-clone");
				} else {
					jQuery(".container").append(jQueryapnData);
					jQuery(this).find("i").toggleClass("fa-clone").toggleClass("fa-minus");
				};
			}) ;
			blocFenetre.find("button[data-dismiss=\'modal\']").click(function(){
				jQuery(this).closest(".modal").removeClass("min");
				jQuery(".container").removeClass(jQueryapnData);
				jQuery(this).next(".modalMinimize").find("i").removeClass("fa fa-clone").addClass("fa fa-minus");
			});
		}
	}
	else
	{
		fenetreExistante.modal("show") ;
	}
}
function extraitOptionsJQueryUiDialog(optionsSource)
{
	var optionsCompletes = {} ;
	for(var n in optionsOuvreFenetreDefaut)
		optionsCompletes[n] = optionsOuvreFenetreDefaut[n] ;
	if(optionsSource != undefined)
	{
		for(var n in optionsSource)
		{
			optionsCompletes[n] = optionsSource[n] ;
		}
	}
	var options = {
		autoOpen: optionsCompletes.OuvrirEnMemeTemps,
		width: optionsCompletes.Largeur,
		closeOnEscape: optionsCompletes.FermerSurEchap,
		modal: optionsCompletes.Modal,
		draggable: optionsCompletes.Draggable,
		resizable: optionsCompletes.Redimensionnable,
		beforeClose : function(event, ui) {
			jQuery(this).empty() ;
		}
	} ;
	if(optionsCompletes.LargeurMin != "")
		options.minWidth = optionsCompletes.LargeurMin ;
	if(optionsCompletes.HauteurMin != "")
		options.minHeight = optionsCompletes.HauteurMin ;
	if(optionsCompletes.LargeurMax != "")
		options.maxWidth = optionsCompletes.LargeurMax ;
	if(optionsCompletes.HauteurMax != "")
		options.maxHeight = optionsCompletes.HauteurMax ;
	if(optionsCompletes.Largeur != "")
		options.width = optionsCompletes.Largeur ;
	if(optionsCompletes.Hauteur != "")
		options.height = optionsCompletes.Hauteur ;
	if(optionsCompletes.NomClasseFenetre != "")
		options.dialogClass = optionsCompletes.NomClasseFenetre ;
	options.buttons = [] ;
	if(optionsCompletes.BoutonFermer == true)
	{
		options.buttons.push({
			text: optionsCompletes.LibelleFermer,
			click: function() {
				jQuery( this ).dialog( "close" );
			}
		}) ;
	}
	if(optionsCompletes.BoutonConfirmer == true)
	{
		funcExecuter = function() {} ;
		if(optionsCompletes.ExecuteBoutonConfirmer != undefined)
		{
			funcExecuter = optionsCompletes.ExecuteBoutonConfirmer ;
		}
		options.buttons.push({
			text: optionsCompletes.LibelleConfirmer,
			click: funcExecuter
		}) ;
	}
	options.close = function(event, ui) {
		if(optionsCompletes.UrlOnglActifSurFerm != "") {
			rafraichitUrlOngletActif(optionsCompletes.UrlOnglActifSurFerm) ;
		}
		else {
			if(autoRafraich) {
				demarreAutoRafraich() ;
			}
		}
	} ;
	return options ;
}
function fermeFenetreActive()
{
	jQuery(document).find(".modal").modal("hide");
}
function fenetreOuverte()
{
	return ((jQuery(".modal").data("bs.modal")) !== undefined) ? (jQuery(".modal").data("bs.modal")).isShown : false ;
}
function definitHauteurFenetre(height) {
var node = jQuery(document).find(".modal") ;
var winHeight = jQuery(window).height() ;
var iframeHeight = node.find("iframe").height() ;
if(height < winHeight) {
	if(height > iframeHeight) {
		var realHeight = height + 20 ;
		node.find("iframe").css({ height : realHeight + "px", overflow : "hidden" }) ;
		node.find("iframe").attr("scrolling", "no") ;
	}
}
else {
	node.find("iframe").css({ height : (height) + "px", overflow : "scroll" }) ;
	node.find("iframe").attr("scrolling", "yes") ;
}
}
var objGroupeOnglet = null ;
var objBarreFenetre = null ;
jQuery(function()
{'.PHP_EOL ;
					$ctn .= '	// Conteneur des fenetres
	objBarreFenetre = jQuery( "#barreFenetres" ) ;
	
	// création des onglets
	objGroupeOnglet = jQuery( "#groupeOnglets" ) ;
	objGroupeOnglet.find(".nav-tabs").on("click", "a", function (e) {
e.preventDefault() ;
jQuery(this).tab("show") ;
}) ;

	ouvreOngletBienvenue() ;'.PHP_EOL ;
			$ctn .= '}) ;

// Bouton fermer
function fermeOngletBtn(btn) {
	fermeOnglet(jQuery(btn).parent().parent()) ;
};
function fermeTousLesOnglets()
{
	fermeOngletsEtSelection(true) ;
}
function fermeOngletsNonSelection()
{
	fermeOngletsEtSelection(false) ;
}
function fermeOngletsEtSelection(avecSelection)
{
	objGroupeOnglet.find( "li" ).each(function(){
		var li = jQuery(this)
		if(! avecSelection && li.hasClass("active"))
		{
			return ;
		}
		fermeOnglet(li) ;
	});
}
function fermeOnglet(li)
{
	tabContentId = li.children("a").attr("aria-controls") ;
	li.remove() ; //remove li of tab
	objGroupeOnglet.find(".nav-tabs").find("a:first").tab("show") ; // Select first tab
	jQuery("#" + tabContentId).remove() ; //remove respective tab content
	if(objGroupeOnglet.find(".nav-tabs").find("a").length == 0)
	{
		ouvreOngletBienvenue() ;
	}
	else
	{
		objGroupeOnglet.find(".nav-tabs").find("a:first").tab( "show" );
	}
}
function fermeOngletActif()
{
	fermeOnglet(objGroupeOnglet.find(".nav-tabs").find( ".active" ));
}
function ouvreOngletBienvenue()
{
	return '.$this->ContenuJsOuvreOngletScript($this->NomScriptBienvenue).';
}
function ouvreOngletCadre(idOnglet, iconeOnglet, libelleOnglet, urlCadre)
{
	return ouvreOnglet(idOnglet, iconeOnglet, libelleOnglet, \'<iframe src="\' + urlCadre + \'" width="100%" height="'.$this->HauteurOnglet.'" style="padding:0px; margin:0px ;" frameborder="0" scrolling="yes">Vous devez mettre a jour votre navigateur.</iframe>\')
}
function obtientUrlScript(url)
{
	var pattern = /(&)?'.preg_quote(urlencode($this->NomParamScriptAppele)).'\=([^\&]+)/g ;
	var partiesUrl = url.split("?", 2) ;
	var result = partiesUrl[0] ;
	if(partiesUrl.length == 2)
	{
		var match = pattern.exec(partiesUrl[1]) ;
		// alert(match) ;
		if(match != null)
		{
			result += "?'.urlencode($this->NomParamScriptAppele).'=" + encodeURIComponent(RegExp.$2) ;
		}
	}
	return result ;
}
function rafraichitUrlOngletActif(nouvUrl)
{
	var ongletActif = objGroupeOnglet.find(".nav-tabs").find( ".active" ) ;
	var idOnglet = ongletActif.attr("id") ;
	var cadresActif = ongletActif.find("iframe") ;
	if(cadresActif.length > 0)
	{
		for(var i=0; i<cadresActif.length; i++)
		{
			noeudCadre = cadresActif.get(i) ;
			// alert(obtientUrlScript(noeudCadre.contentWindow.location.href)) ;
			noeudCadre.src = nouvUrl ;
		}
	}
	else
	{
		rafraichitOnglet(idOnglet) ;
	}
}
function rafraichitOngletActif()
{
	var id = objGroupeOnglet.find(".nav-tabs").find(".active").find("a").attr("aria-controls") ;
	rafraichitOnglet(id) ;
}
function rafraichitOnglet(idOnglet)
{
	var ongletActif = jQuery("#" + idOnglet) ;
	// alert(ongletActif.length) ;
	if(ongletActif.length == 0)
	{
		return ;
	}
	var ctn = ongletActif.html() ;
	ongletActif.html(ctn) ;
}
function ouvreOnglet(idOnglet, iconeOnglet, libelleOnglet, contenuOnglet)
{
	var ongletCree = document.getElementById(idOnglet) ;
	listeOnglets = objGroupeOnglet.find( ".nav-tabs" ) ;
	corpsOnglets = objGroupeOnglet.find( ".tab-content" ) ;
	if(ongletCree != null)
	{
		var positionOnglet = -1 ;
		objGroupeOnglet.find(".tab-content").find("div").each(function(index) {
			if(jQuery(this).attr("id") == idOnglet)
			{
				positionOnglet = index ;
			}
		}) ;
		if(positionOnglet > -1)
		{
			objGroupeOnglet.find(".nav-tabs").find("li")[positionOnglet].tab("show");
		}
		else
		{
			alert("L\'onglet a ete mal cree !!! Reactualisez la page web") ;
		}
		return ;
	}
	var contenuLi = "<li role=\'presentation\'>" ;
	if(iconeOnglet != "")
		contenuLi += "<span class=\'iconeOnglet\'><img src=\'" + iconeOnglet + "\' border=\'0\' /></span>" ;
	contenuLi += "<a href=\'#" + idOnglet + "\' aria-controls=\'" + idOnglet + "\' role=\'tab\' data-toggle=\'tab\'><button class=\'close closeTab\' type=\'button\' onclick=\'fermeOngletBtn(this)\' >x</button>" + libelleOnglet + "</a></li>" ;
	var li = jQuery(contenuLi) ;
	listeOnglets.append( li );
	corpsOnglets.append( "<div id=\'" + idOnglet + "\' role=\'tabpanel\' class=\'tab-pane\'>" + contenuOnglet + "</div>" );
	li.children("a").tab("show") ;
}' ;
				return $ctn ;
			}
			protected function DetermineTypeRendu()
			{
				$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::NonDefini ;
				if(! $this->EstNul($this->Membership))
				{
					if($this->EstNul($this->Membership->MemberLogged))
					{
						if($this->AutoriserInscription == 1 && $this->ValeurParamScriptAppele == $this->NomScriptInscription)
						{
							$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::Script ;
						}
						elseif($this->ScriptPourRendu->EstAccessible())
						{
							$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::NonConnecte ;
						}
						else
						{
							$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::NonConnecte ;
						}
					}
					elseif($this->ScriptPourRendu->NomElementZone == $this->NomScriptParDefaut)
					{
						$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::ParDefaut ;
					}
					elseif($this->ScriptPourRendu->NomElementZone == $this->NomScriptDeconnexion)
					{
						$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::NonConnecte ;
					}
					else
					{
						$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::Script ;
					}
				}
				else
				{
					if($this->ScriptPourRendu->NomElementZone == $this->NomScriptParDefaut)
					{
						$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::ParDefaut ;
					}
					else
					{
						$this->TypeRendu = PvZoneWebBsAdminDirecteTypeRendu::Script ;
					}
				}
			}
			protected function RenduEnteteCorpsDocumentComm()
			{
				$ctn = '' ;
				if(! $this->InclureRenduEntete)
				{
					return $ctn ;
				}
				$ctn .= '<div id="entete">'.$this->ContenuRenduEntete.'</div>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduCorpsDocumentParDefaut()
			{
				$ctn = '' ;
				$ctn .= '<body>'.PHP_EOL ;
				if($this->ImageArrierePlanDocument != '' && $this->EtirerImageArrierePlanDocument)
				{
					$ctn .= '<img src="'.$this->ImageArrierePlanDocument.'" id="imageFond" />'.PHP_EOL ;
				}
				$ctn .= $this->RenduEnteteCorpsDocumentComm().PHP_EOL ;
				$ctn .= '<table id="espaceTravail" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="left">'.PHP_EOL ;
				if($this->EstPasNul($this->CompAvantEspaceTravail))
				{
					$this->CompAvantEspaceTravail->PrepareRendu() ;
					$ctn .= '<div>'.PHP_EOL ;
					$ctn .= $this->CompAvantEspaceTravail->RenduDispositif() ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="conteneurMenuPrincipal">'.PHP_EOL ;
				$ctn .= $this->BarreMenuSuperfish->RenduDispositif() ;
				$ctn .= '</div>
<div class="conteneurGroupeOnglets">
<div id="groupeOnglets">
<ul class="nav nav-tabs" role="tablist"></ul>
<div class="tab-content"></div>
</div>
</div>'.PHP_EOL ;
				if($this->EstPasNul($this->CompApresEspaceTravail))
				{
					$this->CompApresEspaceTravail->PrepareRendu() ;
					$ctn .= $this->CompApresEspaceTravail->RenduDispositif() ;
				}
				$ctn .= '<div>
<div id="barreFenetres"></div>
<div id="pied">'.$this->ContenuPiedDocument.'</div>
</div>
</td>
</tr>
</table>
</body>' ;
				return $ctn ;
			}
			protected function RenduCorpsDocumentNonConnecte()
			{
				$ctn = '' ;
				$ctn .= '<script type="text/javascript">
	jQuery(function() {
		if(window.top != window)
		{
			window.top.location.href = window.location ;
		}
	}) ;
</script>' ;
				$ctn .= '<body id="corps_document">' ;
				$ctn .= $this->RenduEnteteCorpsDocumentComm() ;
				if($this->ScriptPourRendu->NomElementZone == $this->NomScriptDeconnexion)
				{
					$ctn .= '<div class="container-fluid">
<div class="row">
<p align="center">' ;
					$ctn .= $this->RenduContenuCorpsDocument() ;
					$ctn .= '</p>
</div>
</div>' ;
				}
				else
				{
					$ctn .= '<div id="espaceTravail" class="container-fluid">
<div class="row">'.PHP_EOL ;
					$ctn .= '<p>'.$this->MessageIntroduction.'</p>
<p><a href="javascript:ouvreFenetreConnexion() ;">Se Connecter</a></p>
</div>
</div>'.PHP_EOL ;
					$ctn .= '<div class="modal fade" id="connexion" tabindex="-1" role="dialog" aria-labelledby="lblConnexion">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" id="lblConnexion">Connexion</h4>
</div>
<div class="modal-body">'.PHP_EOL ;
					$ctn .= '<form id="formulaireConnexion" action="'.$this->ScriptConnexion->ObtientUrl().'" method="post">' ;
					if($this->ScriptConnexion->TentativeConnexionEnCours && $this->ScriptConnexion->TentativeConnexionValidee == 0)
					{
						$ctn .= '<div class="erreur ui-state-error">'.$this->ScriptConnexion->MessageConnexionEchouee.'</div>'.PHP_EOL ;
					}
					$ctn .= $this->ScriptConnexion->RenduTableauParametres().'
</form>'.PHP_EOL ;
					$ctn .= '</div>
</div>
</div>
</div>' ;
				}
				$ctn .= $this->RenduFenetreInscription() ;
				$ctn .= '<div id="pied">'.$this->ContenuPiedDocument.'</div>'.PHP_EOL ;
				$ctn .= '</body>' ;
				return $ctn ;
			}
			protected function RenduFenetreInscription()
			{
				$ctn = '' ;
				if($this->AutoriserInscription == 1)
				{
					$ctn .= '<div class="modal fade" id="fenetreInscription" tabindex="-1" role="dialog" aria-labelledby="lbl-fenetreInscription">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" id="lbl-fenetreInscription">Inscription / e-AFTrading</h4>
</div>
<div class="modal-body">
<iframe src="?'.urlencode($this->NomParamScriptAppele)."=".urlencode($this->NomScriptInscription).'" frameborder="0" style="width:100%; height:'.($this->HauteurFenInscription - 10).'px"></iframe>
</div>
</div>
</div>
</div>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduEnteteCorpsDocumentScript()
			{
				$ctn = '' ;
				$ctn .= '<body>' ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocumentScript()
			{
				$ctn = '' ;
				$ctn .= '</body>' ;
				return $ctn ;
			}
			protected function RenduContenuCorpsDocument()
			{
				$this->ScriptPourRendu->PrepareRendu() ;
				return $this->ScriptPourRendu->RenduDispositif() ;
			}
			protected function ChargeBarreMenuSuperfish()
			{
			}
			protected function ChargeAutresMenus()
			{
				$ok = $this->PossedePrivileges($this->PrivilegesMenuMembership) ;
				// print_r($this->Membership->MemberLogged->Profile) ;
				if($this->InscrireMenuMembership && $ok)
				{
					$this->BarreMenuSuperfish->MenuRacine->InscritSousMenuFige("authentification") ;
					$this->MenuAuthentification = & $this->BarreMenuSuperfish->MenuRacine->SousMenus["authentification"] ;
					$this->MenuAuthentification->Titre = $this->LibelleMenuMembership ;
					if($this->InscrireMenuDeconnexion)
					{
						$this->MenuDeconnexion = $this->MenuAuthentification->InscritSousMenuRedirScript($this->NomScriptDeconnexion) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptDeconnexion]->Titre = $this->LibelleMenuDeconnexion ;
					}
					if($this->InscrireMenuChangeMotPasse)
					{
						$this->MenuChangeMotPasse = $this->MenuAuthentification->InscritSousMenuFenetreScript($this->NomScriptChangeMotPasse) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptChangeMotPasse]->Titre = $this->LibelleMenuChangeMotPasse ;
					}
					$this->ChargeAvantMenusMembership() ;
					if($this->InscrireMenuAjoutMembre)
					{
						$this->MenuAjoutMembre = $this->MenuAuthentification->InscritSousMenuFenetreScript($this->NomScriptAjoutMembre) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptAjoutMembre]->Titre = $this->LibelleMenuAjoutMembre ;
					}
					if($this->InscrireMenuListeMembres)
					{
						$this->MenuListeMembres = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptListeMembres) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptListeMembres]->Titre = $this->LibelleMenuListeMembres ;
					}
					$this->ChargeAutresMenusMembres() ;
					if($this->InscrireMenuAjoutProfil)
					{
						$this->MenuAjoutProfil = $this->MenuAuthentification->InscritSousMenuFenetreScript($this->NomScriptAjoutProfil) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptAjoutProfil]->Titre = $this->LibelleMenuAjoutProfil ;
					}
					if($this->InscrireMenuListeProfils)
					{
						$this->MenuListeProfils = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptListeProfils) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptListeProfils]->Titre = $this->LibelleMenuListeProfils ;
					}
					$this->ChargeAutresMenusProfils() ;
					if($this->InscrireMenuAjoutRole)
					{
						$this->MenuAjoutRole = $this->MenuAuthentification->InscritSousMenuFenetreScript($this->NomScriptAjoutRole) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptAjoutRole]->Titre = $this->LibelleMenuAjoutRole ;
					}
					if($this->InscrireMenuListeRoles)
					{
						$this->MenuListeRoles = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptListeRoles) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptListeRoles]->Titre = $this->LibelleMenuListeRoles ;
					}
					$this->ChargeAutresMenusRoles() ;
				}
			}
			protected function ChargeAvantMenusMembership()
			{
			}
			protected function ChargeAutresMenusMembres()
			{
			}
			protected function ChargeAutresMenusProfils()
			{
			}
			protected function ChargeAutresMenusRoles()
			{
			}
			public function RenduDocument()
			{
				$this->DetermineTypeRendu() ;
				$this->BarreMenuSuperfish->AdopteScript('BarreMenuSuperfish', $this->ScriptPourRendu) ;
				$this->ChargeBarreMenuSuperfish() ;
				$this->ChargeAutresMenus() ;
				return parent::RenduDocument() ;
			}
			protected function RenduCorpsDocument()
			{
				$ctn = '' ;
				switch($this->TypeRendu)
				{
					case PvZoneWebBsAdminDirecteTypeRendu::ParDefaut :
					{
						$ctn .= $this->RenduCorpsDocumentParDefaut() ;
					}
					break ;
					case PvZoneWebBsAdminDirecteTypeRendu::Script :
					{
						$ctn .= $this->RenduEnteteCorpsDocumentScript() ;
					}
					break ;
					case PvZoneWebBsAdminDirecteTypeRendu::NonConnecte :
					{
						$ctn .= $this->RenduCorpsDocumentNonConnecte() ;
					}
					break ;
				}
				if($this->TypeRendu == PvZoneWebBsAdminDirecteTypeRendu::Script)
				{
					$ctn .= $this->RenduContenuCorpsDocument().PHP_EOL ;
				}
				switch($this->TypeRendu)
				{
					case PvZoneWebBsAdminDirecteTypeRendu::Script :
					{
						$ctn .= $this->RenduPiedCorpsDocumentScript() ;
					}
					break ;
				}
				return $ctn ;
			}
		}
	}
	
?>