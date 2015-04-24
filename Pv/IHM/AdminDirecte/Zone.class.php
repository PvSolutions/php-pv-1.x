<?php
	
	if(! defined('PV_ZONE_ADMIN_DIRECTE'))
	{
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/../Simple.class.php" ;
		}
		if(! defined('PV_COMPOSANT_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('PV_MENU_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/Menu.class.php" ;
		}
		define('PV_ZONE_ADMIN_DIRECTE', 1) ;
		
		class PvRemplisseurConfigMembershipAdminDirecte extends PvRemplisseurConfigMembershipSimple
		{
			public $NomClasseLienModifTableauMembre = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienSupprTableauMembre = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienModifTableauRole = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienSupprTableauRole = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienModifTableauProfil = "PvConfigFormatteurColonneOuvreOnglet" ;
			public $NomClasseLienSupprTableauProfil = "PvConfigFormatteurColonneOuvreOnglet" ;
		}
		
		class PvZoneWebAdminDirecteTypeRendu
		{
			const NonDefini = 0 ;
			const NonConnecte = 1 ;
			const ParDefaut = 2 ;
			const Script = 3 ;
		}
		
		class PvZoneWebAdminDirecte extends PvZoneWebSimple
		{
			public $BarreMenuSuperfish = null ;
			protected $TypeRendu = 0 ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplisseurConfigMembershipAdminDirecte" ;
			public $NomScriptBienvenue = "bienvenue" ;
			public $InclureScriptsMembership = 1 ;
			public $ImageArrierePlanDocument = "images/bg-document.png" ;
			public $EtirerImageArrierePlanDocument = 1 ;
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
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->BarreMenuSuperfish = new PvBarreMenuAdminDirecte() ;
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
			protected function ContenuJsOuvreOngletScript($nomScript)
			{
				if(! isset($this->Scripts[$nomScript]))
					return "" ;
				$cheminIcone = $this->Scripts[$nomScript]->CheminIcone ;
				if($cheminIcone == '')
					$cheminIcone = $this->BarreMenuSuperfish->CheminIconeParDefaut ;
				return 'ouvreOngletCadre("'.$nomScript.'", '.svc_json_encode($cheminIcone).', '.svc_json_encode($this->Scripts[$nomScript]->Titre).', "'.$this->ObtientUrlScript($nomScript).'")' ;
			}
			public function InclutLibrairiesExternes()
			{
				$this->InclureJQuery = 1 ;
				$this->InclureJQueryUi = 1 ;
				parent::InclutLibrairiesExternes() ;
				if($this->TypeRendu == PvZoneWebAdminDirecteTypeRendu::ParDefaut)
				{
					$this->InscritLienJs($this->CheminJsSuperfish) ;
					$this->InscritLienCSS($this->CheminCSSSuperfish) ;
					$this->InscritContenuJs($this->ObtientContenuJsAdminDirecte()) ;
					$this->InscritContenuCSS($this->ObtientContenuCSSAdminDirecte()) ;
				}
				elseif($this->TypeRendu == PvZoneWebAdminDirecteTypeRendu::NonConnecte)
				{
					$this->InscritContenuJs($this->ObtientContenuJsNonConnecte()) ;
					$this->InscritContenuCSS($this->ObtientContenuCSSNonConnecte()) ;
				}
				elseif($this->TypeRendu == PvZoneWebAdminDirecteTypeRendu::Script)
				{
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
				$ctn .= 'p, table, td, th, p, div, input, textarea
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
				$ctn .= 'body, p, table, td, th, p, div, input, textarea
{
	font-family:'.$this->FormatPoliceDocument.' ;
	font-size:'.$this->TaillePoliceDocument.' ;
}' ;
				return $ctn ;
			}
			protected function ObtientContenuCSSAdminDirecte()
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
				$ctn .= 'p, table, td, th, p, div, input, textarea
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
	display:block;
	padding-top:4px ;
	padding-left:4px ;
}
#groupeOnglets .ui-tabs-nav .ui-icon
{ 
	display: inline-block; 
}
#groupeOnglets li .ui-icon-close
{
	float: left;
	margin: 0.4em 0.2em 0 0;
	cursor: pointer;
}
/** THEME SUPERFISH **/
#'.$this->BarreMenuSuperfish->IDInstanceCalc.' a {
	color: '.$this->CouleurPoliceMenuPrincipal.';
	font-weight:'.$this->PoidsPoliceMenuPrincipal.' ;
	font-size:'.$this->TaillePoliceMenuPrincipal.';
}
#'.$this->BarreMenuSuperfish->IDInstanceCalc.' li {
	background: '.$this->CouleurArrierePlanMenuPrincipal.';
}
#'.$this->BarreMenuSuperfish->IDInstanceCalc.' ul li {
	background: '.$this->CouleurArrierePlanSousMenuPrincipal.';
}
#'.$this->BarreMenuSuperfish->IDInstanceCalc.' ul ul li {
	background: '.$this->CouleurArrierePlanMenuPrincipal.';
}
#'.$this->BarreMenuSuperfish->IDInstanceCalc.' li:hover,
#'.$this->BarreMenuSuperfish->IDInstanceCalc.' li.sfHover {
	background: '.$this->CouleurArrierePlanSelectionMenuPrincipal.';
}' ;
				return $ctn ;
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
	jQuery(\'#fenetreConnexion\').dialog({
		modal : true,
		width : 300,
		height : 177,
		resizable : false,
		buttons : [{
			text : "Annuler",
			click : function() { jQuery(this).dialog("close") ; }
		},
		{
			text : "Se Connecter",
			click : function() {
				jQuery(this).find("form").submit() ;
				jQuery(this).dialog("close") ;
			}
		}]
	});
	jQuery(\'#fenetreConnexion\').find(":input").keypress(function(evt) {
		if(evt.which == 13)
		{
			jQuery(this).closest("form").submit() ;
			jQuery(\'#fenetreConnexion\').dialog("close") ;
		}
	}) ;
}' ;
				return $ctn ;
			}
			protected function ObtientContenuJsAdminDirecte()
			{
				$ctn = '' ;
				$ctn .= '
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
	Redimensionnable : false,
	Draggable : true,
	OuvrirEnMemeTemps : true,
	FermerSurEchap : false,
	NomClasseFenetre : "",
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
	var optionsJQueryUiDialog = extraitOptionsJQueryUiDialog(options) ;
	if(optionsOuvreFenetreDefaut.CacheFenetre == false && fenetreExistante.length > 0)
	{
		// alert(fenetreExistante.length) ;
		fenetreExistante.dialog("destroy") ;
		fenetreExistante.remove() ;
		fenetreExistante = jQuery("#" + idFenetre) ;
	}
	if(fenetreExistante.length == 0)
	{
		titreFenetre = titre ;
		// var contenuDiv = \'<div id="\' + htmlEncode(idFenetre) + \'" title="\' + htmlEncode(titreFenetre) + \'">\' ;
		// titreFenetre doit etre encodee avant de passer la variable
		var contenuDiv = \'<div id="\' + htmlEncode(idFenetre) + \'" title="\' + titreFenetre + \'">\' ;
		contenuDiv += contenu ;
		contenuDiv += \'</div>\' ;
		var blocFenetre = jQuery(contenuDiv) ;
		objBarreFenetre.append(blocFenetre) ;
		blocFenetre.dialog(optionsJQueryUiDialog) ;
	}
	else
	{
		fenetreExistante.dialog("open") ;
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
		draggable: optionsCompletes.Draggable
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
	if(optionsCompletes.UrlOnglActifSurFerm != "")
	{
		options.close = function(event, ui) {
			rafraichitUrlOngletActif(optionsCompletes.UrlOnglActifSurFerm) ;
		}
	}
	return options ;
}
function fermeFenetreActive()
{
	// alert(objBarreFenetre.find(".ui-dialog-content").length) ;
	jQuery(document).find(".ui-dialog-content").dialog("close");
}
var objGroupeOnglet = null ;
var objBarreFenetre = null ;
jQuery(function()
{'.PHP_EOL ;
					$ctn .= '	// Création du menu principal
	var menuPrincipal = jQuery(\'#'.$this->BarreMenuSuperfish->IDInstanceCalc.'\').superfish({
		//add options here if required
	});
	
	// Conteneur des fenetres
	objBarreFenetre = jQuery( "#barreFenetres" ) ;
	
	// création des onglets
	objGroupeOnglet = jQuery( "#groupeOnglets" ) ;
	objGroupeOnglet.tabs();
	objGroupeOnglet.delegate(
		"span.ui-icon-close",
		"click",
		function() {
			fermeOnglet(jQuery( this ).closest( "li" ));
		}
	);
	objGroupeOnglet.bind(
		"keyup",
		function( event ) {
			if ( event.altKey && event.keyCode === jQuery.ui.keyCode.BACKSPACE )
			{
				fermeOngletActif();
			}
		}
	);
	ouvreOngletBienvenue() ;'.PHP_EOL ;
			$ctn .= '}) ;
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
		if(! avecSelection && li.hasClass("ui-tabs-active"))
		{
			return ;
		}
		fermeOnglet(li) ;
	});
}
function fermeOnglet(li)
{
	var panelId = li.remove().attr("aria-controls") ;
	jQuery( "#" + panelId ).remove();
	if(objGroupeOnglet.find("div").length == 0)
	{
		ouvreOngletBienvenue() ;
	}
	else
	{
		objGroupeOnglet.tabs( "refresh" );
	}
}
function fermeOngletActif()
{
	fermeOnglet(objGroupeOnglet.find( ".ui-tabs-active" ));
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
	var idOnglet = objGroupeOnglet.find( ".ui-tabs-active" ).attr("aria-controls") ;
	var ongletActif = jQuery("#" + idOnglet) ;
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
	var id = objGroupeOnglet.find( ".ui-tabs-active" ).attr("aria-controls") ;
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
	listeOnglets = objGroupeOnglet.find( ".ui-tabs-nav" ) ;
	if(ongletCree != null)
	{
		var positionOnglet = -1 ;
		objGroupeOnglet.find("div").each(function(index) {
			if(jQuery(this).attr("id") == idOnglet)
			{
				positionOnglet = index ;
			}
		}) ;
		if(positionOnglet > -1)
		{
			objGroupeOnglet.tabs( "option", "active", positionOnglet);
		}
		else
		{
			alert("L\'onglet a ete mal cree !!! Reactualisez la page web") ;
		}
		return ;
	}
	var contenuLi = "<li>" ;
	if(iconeOnglet != "")
		contenuLi += "<span class=\'iconeOnglet\'><img src=\'" + iconeOnglet + "\' border=\'0\' /></span>" ;
	contenuLi += "<a href=\'#" + idOnglet + "\'>" + libelleOnglet + "</a> <span class=\'ui-icon ui-icon-close\'>Fermer l\'onglet</span></li>" ;
	var li = jQuery(contenuLi) ;
	listeOnglets.append( li );
	objGroupeOnglet.append( "<div id=\'" + idOnglet + "\'>" + contenuOnglet + "</div>" );
	objGroupeOnglet.tabs( "refresh" );
	objGroupeOnglet.tabs( "option", "active", listeOnglets.find("li").length - 1);
}' ;
				return $ctn ;
			}
			protected function DetermineTypeRendu()
			{
				$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::NonDefini ;
				if(! $this->EstNul($this->Membership))
				{
					if($this->EstNul($this->Membership->MemberLogged))
					{
						if($this->ScriptPourRendu->EstAccessible())
						{
							$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::NonConnecte ;
						}
						else
						{
							$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::NonConnecte ;
						}
					}
					elseif($this->ScriptPourRendu->NomElementZone == $this->NomScriptParDefaut)
					{
						$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::ParDefaut ;
					}
					elseif($this->ScriptPourRendu->NomElementZone == $this->NomScriptDeconnexion)
					{
						$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::NonConnecte ;
					}
					else
					{
						$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::Script ;
					}
				}
				else
				{
					if($this->ScriptPourRendu->NomElementZone == $this->NomScriptParDefaut)
					{
						$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::ParDefaut ;
					}
					else
					{
						$this->TypeRendu = PvZoneWebAdminDirecteTypeRendu::Script ;
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
<td align="left">' ;
				if($this->EstPasNul($this->CompAvantEspaceTravail))
				{
					$this->CompAvantEspaceTravail->PrepareRendu() ;
					$ctn .= $this->CompAvantEspaceTravail->RenduDispositif() ;
				}
				$ctn .= '<table width="100%" id="contenuEspaceTravail" cellspacing="2" cellpadding="0">
<tr>
<td class="conteneurMenuPrincipal">' ;
				$ctn .= $this->BarreMenuSuperfish->RenduDispositif() ;
				$ctn .= '</td>
</tr>
<tr>
<td class="conteneurGroupeOnglets">
<div id="groupeOnglets">
<ul>
</ul>
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>'.PHP_EOL ;
				if($this->EstPasNul($this->CompApresEspaceTravail))
				{
					$this->CompApresEspaceTravail->PrepareRendu() ;
					$ctn .= $this->CompApresEspaceTravail->RenduDispositif() ;
				}
				$ctn .= '<div id="barreFenetres"></div>
<div id="pied">'.$this->ContenuPiedDocument.'</div>
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
					$ctn .= '<table id="espaceTravail" cellspacing="0" cellpadding="0" align="center">
	<tr>
	<td align="center">' ;
					$ctn .= $this->RenduContenuCorpsDocument() ;
					$ctn .= '</td>
	</tr>
</table>' ;
				}
				else
				{
					$ctn .= '<table id="espaceTravail" cellspacing="0" cellpadding="0" align="center">
	<tr>
	<td align="center">' ;
					$ctn .= '<p>'.$this->MessageIntroduction.'</p>
		<p><a href="javascript:ouvreFenetreConnexion() ;">Se Connecter</a></p>
	</td>
	</tr>
	</table>'.PHP_EOL ;
					$ctn .= '<div id="fenetreConnexion" title="Authentification">
		<form id="formulaireConnexion" action="'.$this->ScriptConnexion->ObtientUrl().'" method="post">' ;
					if($this->ScriptConnexion->TentativeConnexionEnCours && $this->ScriptConnexion->TentativeConnexionValidee == 0)
					{
						$ctn .= '<div class="erreur ui-state-error">'.$this->ScriptConnexion->MessageConnexionEchouee.'</div>'.PHP_EOL ;
					}
					$ctn .= $this->ScriptConnexion->RenduTableauParametres().'
		</form>
	</div>'.PHP_EOL ;
				}
				$ctn .= '<div id="pied">'.$this->ContenuPiedDocument.'</div>'.PHP_EOL ;
				$ctn .= '</body>' ;
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
					case PvZoneWebAdminDirecteTypeRendu::ParDefaut :
					{
						$ctn .= $this->RenduCorpsDocumentParDefaut() ;
					}
					break ;
					case PvZoneWebAdminDirecteTypeRendu::Script :
					{
						$ctn .= $this->RenduEnteteCorpsDocumentScript() ;
					}
					break ;
					case PvZoneWebAdminDirecteTypeRendu::NonConnecte :
					{
						$ctn .= $this->RenduCorpsDocumentNonConnecte() ;
					}
					break ;
				}
				if($this->TypeRendu == PvZoneWebAdminDirecteTypeRendu::Script)
				{
					$ctn .= $this->RenduContenuCorpsDocument().PHP_EOL ;
				}
				switch($this->TypeRendu)
				{
					case PvZoneWebAdminDirecteTypeRendu::Script :
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