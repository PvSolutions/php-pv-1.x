<?php
	
	if(! defined('PV_ZONE_CORDOVA'))
	{
		if(! defined('PV_ZONE_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Simple/Zone.class.php" ;
		}
		define('PV_ZONE_CORDOVA', 1) ;
		
		class PvPluginCordova
		{
			public function AppliqueZone(& $zone)
			{
			}
		}
		
		class PvScriptAccueilCordova extends PvScriptWebSimple
		{
			public $TitreDocument = "Bienvenue" ;
			public $Titre = "Bienvenue" ;
			public function RenduSpecifique()
			{
				return "Bienvenue sur notre site" ;
			}
		}
		
		class PvZoneCordova extends PvZoneWebSimple
		{
			public $Titre = "Ma Zone Cordova" ;
			public $InclureCtnJsEntete = 0 ;
			public $CheminCSSBootstrap = "css/bootstrap.min.css" ;
			public $InclureJQuery = 1 ;
			public $InclureJQueryMigrate = 1 ;
			public $InclureBootstrap = 1 ;
			public $InclureFontAwesome = 1 ;
			public $InclureNavbarFlottant = 1 ;
			public $TagTitre = "h3" ;
			public $UrlDistant ;
			public $ScriptAccueil ;
			public $CheminAvatarMenu = "" ;
			public $MessageMenuNonConnecte = '<b>Inconnu</b>,<br>Veuillez vous connecter' ;
			public $MessageMenuConnecte = 'Bienvenue, <b><span data-pv-var="loginMembreConnecte"></span></b><br><span data-pv-var="titreProfilConnecte"></span>' ;
			public $MessageDlgAttente = "Veuillez patienter..." ;
			public $TitreMenuAccueil = "Accueil" ;
			public $TitreMenuInscription = "S'inscrire" ;
			public $TitreMenuRecouvreMP = "Mot de passe oubli&eacute;" ;
			public $TitreMenuConnexion = "Connexion" ;
			public $TitreMenuDeconnexion = "Deconnexion" ;
			public $TitreMenuModifPrefs = "Informations perso." ;
			public $TitreMenuMotPasse = "Mot de passe" ;
			public $TitreMenuListeMembres = "Tous les membres" ;
			public $TitreMenuAjoutMembre = "Ajouter un membre" ;
			public $TitreMenuListeProfils = "Tous les profils" ;
			public $TitreMenuAjoutProfil = "Ajouter un profil" ;
			public $TitreMenuListeRoles = "Tous les r&ocirc;les" ;
			public $TitreMenuAjoutRole = "Ajouter un r&ocirc;le" ;
			public $TitreMenuEditAcces = "Gestion des acc&egrave;s" ;
			public $RenduExtraHead = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' ;
			public $ViewportMeta = 'width=device-width, initial-scale=1' ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPCordova" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionCordova" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionCordova" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionCordova" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseCordova" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseCordova" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreCordova" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreCordova" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreCordova" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsCordova" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreCordova" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresCordova" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilCordova" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilCordova" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilCordova" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsCordova" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleCordova" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleCordova" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleCordova" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesCordova" ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplConfigMembershipCordova" ;
			protected function RenduCSSDeviceXs()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduCSSDeviceSm()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduCSSDeviceMd()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduCSSDeviceLg()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduCSSDeviceXl()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduCSSDeviceResponsive()
			{
				$ctnXs = $this->RenduCSSDeviceXs() ;
				$ctnSm = $this->RenduCSSDeviceSm() ;
				$ctnMd = $this->RenduCSSDeviceMd() ;
				$ctnLg = $this->RenduCSSDeviceLg() ;
				$ctnXl = $this->RenduCSSDeviceXl() ;
				$ctn = '' ;
				if($ctnXs != '')
				{
					$ctn .= '@media (max-width:575px) {
'.$ctnXs.'
}'.PHP_EOL ;
				}
				if($ctnSm != '')
				{
					$ctn .= '@media (min-width:576px; max-width:767px) {
'.$ctnSm.'
}'.PHP_EOL ;
				}
				if($ctnMd != '')
				{
					$ctn .= '@media (min-width:768px; max-width:991px) {
'.$ctnMd.'
}'.PHP_EOL ;
				}
				if($ctnLg != '')
				{
					$ctn .= '@media (min-width:992px; max-width:1199px) {
'.$ctnLg.'
}'.PHP_EOL ;
				}
				if($ctnXl != '')
				{
					$ctn .= '@media (min-width:1200px;) {
'.$ctnXl.'
}'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduCSSSidebar()
			{
				$ctn = '' ;
				$ctn .= '#'.$this->IDInstanceCalc.'_sidebar {
    width: 300px;
    position: fixed;
    top: 0;
    left: -300px;
    height: 100vh;
    z-index: 999;
    color: #fff;
    transition: all 0.3s;
    overflow-y: scroll;
    box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.2);
}
#'.$this->IDInstanceCalc.'_sidebar.active {
    left: 0;
}
#'.$this->IDInstanceCalc.'_dismiss {
    width: 35px;
    height: 35px;
    line-height: 35px;
    text-align: center;
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    -webkit-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
}
#'.$this->IDInstanceCalc.'_dismiss:hover {
    background: #fff;
    color: #7386D5;
}
#'.$this->IDInstanceCalc.'_attente {
	display:none ;
	padding: 1em;
	position: absolute;
	top: 50%;
	left: 50%;
	margin-right: -50%;
	z-index:9999 ;
	transform: translate(-50%, -50%)
}
#'.$this->IDInstanceCalc.'_overlay, #'.$this->IDInstanceCalc.'_overlay2 {
    position: fixed;
    top: 0;
    right: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.7);
    z-index: 998;
    display: none;
}
#'.$this->IDInstanceCalc.'_overlay2 {
	z-index:9998;
}
#'.$this->IDInstanceCalc.'_sidebar .sidebar-header {
    padding: 20px;
}
#'.$this->IDInstanceCalc.'_sidebar ul.components {
    padding: 20px 0;
    border-bottom: 1px solid #47748b;
}
#'.$this->IDInstanceCalc.'_sidebar ul p {
    padding: 10px;
}
#'.$this->IDInstanceCalc.'_sidebar ul li a {
    padding: 10px;
    font-size: 1.1em;
    display: block;
	color:white;
}
#'.$this->IDInstanceCalc.'_sidebar ul li.active > a, a[aria-expanded="true"] {
    color: white;
}
#'.$this->IDInstanceCalc.'_sidebar a, #'.$this->IDInstanceCalc.'_sidebar a:hover, #'.$this->IDInstanceCalc.'_sidebar a:focus {
    color: inherit;
    text-decoration: none;
    transition: all 0.3s;
}
#'.$this->IDInstanceCalc.'_sidebar ul li a:hover {
    background: #fff;
	color:black;
}

a[data-toggle="collapse"] {
    position: relative;
}
a[aria-expanded="false"]::before, a[aria-expanded="true"]::before {
    content: \'\e259\';
    display: block;
    position: absolute;
    right: 20px;
    font-family: \'Glyphicons Halflings\';
    font-size: 0.6em;
}
a[aria-expanded="true"]::before {
    content: \'\e260\';
}
ul ul a {
    font-size: 0.9em !important;
    padding-left: 30px !important;
}

#'.$this->IDInstanceCalc.'_connecte, #'.$this->IDInstanceCalc.'_non_connecte {
display:none ;
}' ;
				return $ctn ;
			}
			protected function RenduCSSPvCordova()
			{
				$ctn = '' ;
				$ctn .= '#'.$this->IDInstanceCalc.'_corps>div { display:none; }' ;
				return $ctn ;
			}
			protected function RenduJsSidebar()
			{
				$ctn = '' ;
				$ctn .= 'function hideSidebar'.$this->IDInstanceCalc.'() {
jQuery(\'#'.$this->IDInstanceCalc.'_sidebar\').removeClass(\'active\');
jQuery(\'#'.$this->IDInstanceCalc.'_overlay\').fadeOut();
}
function showSidebar'.$this->IDInstanceCalc.'() {
jQuery(\'#'.$this->IDInstanceCalc.'_sidebar\').addClass(\'active\');
jQuery(\'#'.$this->IDInstanceCalc.'_overlay\').fadeIn();
jQuery(\'.collapse.in\').toggleClass(\'in\');
jQuery(\'a[aria-expanded=true]\').attr(\'aria-expanded\', \'false\');
}
jQuery(document).ready(function () {
jQuery(\'#'.$this->IDInstanceCalc.'_dismiss, #'.$this->IDInstanceCalc.'_overlay\').on(\'click\', function () {
hideSidebar'.$this->IDInstanceCalc.'() ;
});
jQuery(\'#'.$this->IDInstanceCalc.'_sidebarCollapse\').on(\'click\', function () {
showSidebar'.$this->IDInstanceCalc.'() ;
});
});' ;
				return $ctn ;
			}
			protected function RenduJsCorrectifs()
			{
				$ctn = '' ;
				$ctn .= 'var timeoutIds = [] ;
var intervalIds = [] ;

window.setTimeoutOld = window.setTimeout ;
window.clearTimeoutOld = window.clearTimeout ;
window.setIntervalOld = window.setInterval ;
window.clearIntervalOld = window.clearInterval ;

window.setTimeout = function(evt, index) {
	var id = window.setTimeoutOld(evt, index) ;
	timeoutIds.push(id) ;
} ;
window.setInterval = function(evt, index) {
	var id = window.setIntervalOld(evt, index) ;
	intervalIds.push(id) ;
} ;
window.clearTimeout = function(id) {
	var ix = timeoutIds.indexOf(id) ;
	if(ix > -1)	{
		timeoutIds.splice(ix, 1) ;
	}
	window.clearTimeoutOld(id) ;
} ;
window.clearInterval = function(id) {
	var ix = intervalIds.indexOf(id) ;
	if(ix > -1)	{
		intervalIds.splice(ix, 1) ;
	}
	window.clearIntervalOld(id) ;
} ;
window.hasTrueValue = function(val) {
	return val === true || val === "1" || val === 1 ;
}
' ;
				return $ctn ;
			}
			protected function RenduJsPvCordova()
			{
				$ctn = '' ;
				$ctn .= 'var pv = {} ;
pv.cordova = {} ;
pv.cordova.ui = {} ;

pv.cordova.membership = {} ;
pv.cordova.membership.Member = function() {
	var _self = this ;
	_self.id = 0 ;
	_self.login = "" ;
	_self.firstName = "" ;
	_self.lastName = "" ;
	_self.email = "" ;
	_self.estRoot = false ;
	_self.estInvite = false ;
	_self.locked = false ;
	_self.enable = 0 ;
	_self.ADActivated = "" ;
	_self.ADServer = "" ;
	_self.mustChangePassword = false ;
	_self.totalRetry = 0 ;
	_self.contact = "" ;
	_self.address = "" ;
	_self.rowData = {} ;
	_self.profile = new pv.cordova.membership.Profile() ;
	_self.parseMemberRow = function(lgnMembre) {
		_self.rowData = lgnMembre ;
		_self.id = (lgnMembre["MEMBER_ID"]) ? lgnMembre["MEMBER_ID"] : 0 ;
		_self.login = (lgnMembre["MEMBER_LOGIN"]) ? lgnMembre["MEMBER_LOGIN"] : "" ;
		_self.firstName = (lgnMembre["MEMBER_FIRST_NAME"]) ? lgnMembre["MEMBER_FIRST_NAME"] : "" ;
		_self.lastName = (lgnMembre["MEMBER_LAST_NAME"]) ? lgnMembre["MEMBER_LAST_NAME"] : "" ;
		_self.email = (lgnMembre["MEMBER_EMAIL"]) ? lgnMembre["MEMBER_EMAIL"] : "" ;
		_self.address = (lgnMembre["MEMBER_ADDRESS"]) ? lgnMembre["MEMBER_ADDRESS"] : "" ;
		_self.contact = (lgnMembre["MEMBER_CONTACT"]) ? lgnMembre["MEMBER_CONTACT"] : "" ;
		_self.enable = hasTrueValue(lgnMembre["MEMBER_ENABLE"]) ;
		_self.mustChangePassword = hasTrueValue(lgnMembre["MEMBER_MUST_CHANGE_PASSWORD"]) ;
		_self.ADServer = (lgnMembre["MEMBER_AD_SERVER"]) ? lgnMembre["MEMBER_AD_SERVER"] : "" ;
		_self.locked = hasTrueValue(lgnMembre["MEMBER_LOCKED"]) ;
		_self.totalRetry = parseInt(lgnMembre["MEMBER_TOTAL_RETRY"]) ;
		_self.profile = new pv.cordova.membership.Profile() ;
		_self.profile.id = parseInt(lgnMembre["PROFILE_ID"]) ;
		_self.profile.title = (lgnMembre["PROFILE_TITLE"]) ? lgnMembre["PROFILE_TITLE"] : "" ;
		_self.profile.description = (lgnMembre["PROFILE_DESCRIPTION"]) ? lgnMembre["PROFILE_DESCRIPTION"] : "" ;
		if(lgnMembre["PROFILE_PRIVILEGES"] !== null) {
			for(i = 0; i < lgnMembre["PROFILE_PRIVILEGES"].length; i++) {
				var privilegeRow = lgnMembre["PROFILE_PRIVILEGES"][i] ;
				var priv = _self.profile.parsePrivilegeRow(privilegeRow) ;
			}
		}
	}
} ;
pv.cordova.membership.Profile = function() {
	var _self = this ;
	_self.id = 0 ;
	_self.title = "" ;
	_self.description = "" ;
	_self.privileges = [] ;
	_self.parsePrivilegeRow = function(privilegeRow) {
		var privilege = new pv.cordova.membership.Privilege() ;
		privilege.id = (privilegeRow["PRIVILEGE_ROLE"]) ? privilegeRow["PRIVILEGE_ROLE"] : 0 ;
		privilege.roleId = (privilegeRow["ROLE_ID"]) ? privilegeRow["ROLE_ID"] : 0 ;
		privilege.name = (privilegeRow["ROLE_NAME"]) ? privilegeRow["ROLE_NAME"] : "" ;
		privilege.title = (privilegeRow["ROLE_TITLE"]) ? privilegeRow["ROLE_TITLE"] : "" ;
		privilege.description = (privilegeRow["ROLE_DESCRIPTION"]) ? privilegeRow["ROLE_DESCRIPTION"] : "" ;
		_self.privileges.push(privilege) ;
		return privilege ;
	} ;
} ;
pv.cordova.membership.Privilege = function() {
	var _self = this ;
	_self.id = 0 ;
	_self.roleId = 0 ;
	_self.name = "" ;
	_self.title = "" ;
	_self.description = "" ;
	_self.enabled = false ;
} ;

pv.cordova.ui.Zone = function() {
	var _self = this ;
	_self.titreDocument = "" ;
	_self.ecrans = {} ;
	_self.argsEcran = null ;
	_self.ecranActif = null ;
	_self.noeudHtmlEntity = jQuery("<div></div>") ;
	_self.contenuDlgAttente = "'.$this->MessageDlgAttente.'" ;
	_self.urlServiceDistant = "" ;
	_self.membreConnecte = null ;
	_self.premierJqNoeudBody = null ;
	_self.dernierJqNoeudBody = null ;
	_self.variablesContexte = function() {
		var variables = {
			idMembreConnecte : _self.idMembreConnecte(),
			loginMembreConnecte : _self.nomMembreConnecte(),
			titreProfilConnecte : _self.titreProfilConnecte(),
			nomMembreConnecte : _self.nomMembreConnecte(),
			prenomMembreConnecte : _self.prenomMembreConnecte()
		} ;
		return variables ;
	} ;
	_self.afficheExceptionAppelDistant = function() {
		alert("Contenu vide impossible a dechiffrer ! Veuillez contacter le webmestre") ;
	} ;
	_self.cleIdMembreSession = function() {
		return "idMembre'.$this->IDInstanceCalc.'" ;
	} ;
	_self.autoConnecteMembre = function() {
		if(sessionStorage !== undefined) {
			var idMembreStocke = sessionStorage.getItem(_self.cleIdMembreSession()) ;
			if(idMembreStocke !== undefined && idMembreStocke !== null) {
				_self.appelleUrl("'.$this->ScriptConnexion->ActPrincChargeMembre->ObtientUrl().'&idMembre=" + encodeURIComponent(idMembreStocke), {}, function(resultat, xhr) {
					if(resultat.messageErreur === "") {
						_self.definitMembreConnecte(resultat.lgnMembre) ;
					}
					_self.init() ;
				}) ;
			}
			else {
				_self.init() ;
			}
		}
		else {
			_self.init() ;
		}
	} ;
	_self.definitMembreConnecte = function(lgnMembre) {
		if(lgnMembre !== null) {
			_self.membreConnecte = new pv.cordova.membership.Member() ;
			_self.membreConnecte.parseMemberRow(lgnMembre) ;
			if(sessionStorage !== undefined) {
				sessionStorage.setItem(_self.cleIdMembreSession(), _self.membreConnecte.id) ;
			}
		}
	} ;
	_self.possedeMembreConnecte = function() {
		return (_self.membreConnecte !== null && _self.membreConnecte.id !== 0 && _self.membreConnecte.estInvite === false) ;
	} ;
	_self.possedeMembreSuperAdmin = function() {
		return (_self.possedeMembreConnecte() && _self.estRoot === true) ;
	} ;
	_self.membreSuperAdmin = function() {
		return _self.possedeMembreSuperAdmin() ;
	} ;
	_self.possedeMembreRoot = function() {
		return _self.possedeMembreSuperAdmin() ;
	} ;
	_self.estSuperAdmin = function() {
		return _self.possedeMembreSuperAdmin() ;
	} ;
	_self.possedePrivileges = function(privileges) {
		var ok = _self.possedeMembreConnecte() ;
		if(! ok) {
			return ok ;
		}
		ok = false ;
		if(_self.membreConnecte.estRoot == 1) {
			return true ;
		}
		for(var i=0; i<privileges.length; i++) {
			if(_self.membreConnecte.profile.privileges[privileges[i]] === undefined || _self.membreConnecte.profile.privileges[privileges[i]].enabled === true){
				ok = true ;
				break ;
			}
		}
		return ok ;
	} ;
	_self.possedePrivilege = function(privilege) {
		return _self.possedePrivileges[privilege] ;
	} ;
	_self.idMembreConnecte = function() {
		if(! _self.possedeMembreConnecte()) {
			return 0 ;
		}
		return _self.membreConnecte.id ;
	} ;
	_self.loginMembreConnecte = function() {
		if(! _self.possedeMembreConnecte()) {
			return "" ;
		}
		return _self.membreConnecte.login ;
	} ;
	_self.nomMembreConnecte = function() {
		if(! _self.possedeMembreConnecte()) {
			return "" ;
		}
		return _self.membreConnecte.lastName ;
	} ;
	_self.prenomMembreConnecte = function() {
		if(! _self.possedeMembreConnecte()) {
			return "" ;
		}
		return _self.membreConnecte.firstName ;
	} ;
	_self.idProfilConnecte = function() {
		if(! _self.possedeMembreConnecte()) {
			return 0 ;
		}
		return _self.membreConnecte.profile.id ;
	} ;
	_self.titreProfilConnecte = function() {
		if(! _self.possedeMembreConnecte()) {
			return "" ;
		}
		return _self.membreConnecte.profile.title ;
	} ;
	_self.afficheDlgAttente = function(ctn) {
		if(ctn === undefined) {
			ctn = _self.contenuDlgAttente ;
		}
		jQuery("#'.$this->IDInstanceCalc.'_attente").html(ctn) ;
		jQuery("#'.$this->IDInstanceCalc.'_overlay2").show() ;
		jQuery("#'.$this->IDInstanceCalc.'_attente").show() ;
	} ;
	_self.cacheDlgAttente = function() {
		jQuery("#'.$this->IDInstanceCalc.'_attente").hide() ;
		jQuery("#'.$this->IDInstanceCalc.'_overlay2").hide() ;
	} ;
	_self.init = function() {
		_self.premierJqNoeudBody = jQuery("body").children().first() ;
		_self.dernierJqNoeudBody = jQuery("body").children().last() ;
		_self.contenuDlgAttente = jQuery("#'.$this->IDInstanceCalc.'_attente").html() ;
		var jqNoeuds = jQuery("#'.$this->IDInstanceCalc.'_corps").children() ;
		jqNoeuds.each(function(ixEcran) {
			var jqNoeud = jQuery(this) ;
			var idEcran = jqNoeud.attr("id") ;
			if(idEcran == "") {
				return ;
			}
			if(_self.ecrans[idEcran] === undefined) {
				_self.ecrans[idEcran] = new pv.cordova.ui.Ecran() ;
			}
			_self.ecrans[idEcran].htmlInitial = jqNoeud.html() ;
		}) ;
		_self.affichePremEcran() ;
	} ;
	_self.prepareEspaceTravail = function() {
		var elemsCreesDebutBody = _self.premierJqNoeudBody.prevAll() ;
		elemsCreesDebutBody.remove() ;
		var elemsCreesFinBody = _self.dernierJqNoeudBody.nextAll() ;
		elemsCreesFinBody.remove() ;
		while(timeoutIds.length > 0) {
			window.clearTimeout(timeoutIds[0]) ;
		}
		while(intervalIds.length > 0) {
			window.clearInterval(intervalIds[0]) ;
		}
	} ;
	_self.affichePremEcran = function() {
		if(_self.possedeMembreConnecte() == false) {
			_self.afficheEcran("'.$this->ScriptConnexion->IDInstanceCalc.'") ;
		}
		else {
			_self.afficheEcran("'.$this->ScriptAccueil->IDInstanceCalc.'") ;
		}
	} ;
	_self.ecranAccessible = function (idEcran) {
		var ecran = _self.ecrans[idEcran] ;
		if(ecran === undefined) {
			return false ;
		}
		if(ecran.necessiteMembreConnecte === false) {
			if(ecran.accesStrict === false || ! _self.possedeMembreConnecte()) {
				return true ;
			}
			return false ;
		}
		else {
			if(! _self.possedeMembreConnecte()) {
				return false ;
			}
			if(ecran.privileges.length === 0 ||_self.possedePrivileges(ecran.privileges)) {
				return true ;
			}
			return false ;
		}
	} ;
	_self.afficheMenuAuth = function() {
		jQuery("#'.$this->IDInstanceCalc.'_sidebar").find(".auth_menu").hide() ;
		if(_self.possedeMembreConnecte()) {
			jQuery("#'.$this->IDInstanceCalc.'_connecte").show() ;
		}
		else {
			jQuery("#'.$this->IDInstanceCalc.'_non_connecte").show() ;
		}
	} ;
	_self.afficheEcran = function(index, args) {
		hideSidebar'.$this->IDInstanceCalc.'() ;
		args = args || {} ;
		var jqNoeuds = jQuery("#'.$this->IDInstanceCalc.'_corps").children() ;
		var ecranAccessible = false ;
		jqNoeuds.each(function(ixEcran) {
			var jqNoeud = jQuery(this) ;
			var idEcran = jqNoeud.attr("id") ;
			if(ixEcran === index || idEcran === index) {
				ecranAccessible = _self.ecranAccessible(idEcran) ;
			}
		}) ;
		if(! ecranAccessible)
		{
			return ;
		}
		_self.argsEcran = args ;
		_self.afficheDlgAttente() ;
		jqNoeuds.each(function(ixEcran) {
			var jqNoeud = jQuery(this) ;
			var idEcran = jqNoeud.attr("id") ;
			var jqNoeudVisible = jqNoeud.filter(":visible") ;
			if(jqNoeudVisible.length) {
				if(_self.ecrans[idEcran] !== undefined && typeof _self.ecrans[idEcran].evtFermeture == "function") {
					_self.ecrans[idEcran].evtFermeture(jqNoeud, ixEcran) ;
				}
			}
			jqNoeud.html("") ;
			jqNoeud.hide() ;
		}) ;
		_self.prepareEspaceTravail() ;
		jqNoeuds.each(function(ixEcran) {
			var jqNoeud = jQuery(this) ;
			var idEcran = jqNoeud.attr("id") ;
			if(ixEcran === index || idEcran === index) {
				var ecran = _self.ecrans[idEcran] ;
				_self.ecranActif = ecran ;
				if(ecran !== undefined && typeof ecran.evtOuverture == "function") {
					ecran.evtOuverture(jqNoeud, args, ixEcran) ;
				}
				jQuery("title").text((ecran.titre !== null) ? ecran.titre : '.svc_json_encode($this->Titre).') ;
				jqNoeud.html(ecran.htmlInitial) ;
				jqNoeud.show() ;
			}
		}) ;
		_self.afficheMenuAuth() ;
		_self.fixeAccesInterface() ;
		_self.appliqueVariablesContexte() ;
		_self.cacheDlgAttente() ;
	} ;
	_self.appliqueVariablesContexte = function() {
		var variables = _self.variablesContexte() ;
		var jqElems = jQuery("[data-pv-var]") ;
		jqElems.each(function() {
			var jqElem = jQuery(this) ;
			var varName = jqElem.attr("data-pv-var") ;
			jqElem.text((variables[varName] !== undefined) ? variables[varName] : "") ;
		}) ;
	} ;
	_self.fixeAccesInterface = function() {
		var jqElems = jQuery("[data-pv-privileges]").each(function() {
			var jqElem = jQuery(this) ;
			var privilegeValue = jqElem.data("pv-privileges") ;
			var estAccessible = false ;
			if(privilegeValue === "" || privilegeValue === null) {
				estAccessible = _self.possedeMembreConnecte() ;
			}
			else {
				var privileges = privilegeValue.split(" ") ;
				estAccessible = _self.possedePrivileges(privileges) ;
			}
			if(! estAccessible) {
				if(jqElem.is("a")) {
					var jqParent = jqElem.parent() ;
					if(jqParent.is("li")) {
						jqParent.show() ;
					}
					else {
						jqElem.show() ;
					}
				}
				else {
					jqElem.show() ;
				}
			}
			else {
				if(jqElem.is("a")) {
					var jqParent = jqElem.parent() ;
					if(jqParent.is("li")) {
						jqParent.show() ;
					}
					else {
						jqElem.show() ;
					}
				}
				else {
					jqElem.show() ;
				}
			}
		}) ;
	} ;
	_self.definitEcran = function (idEcran, titreDocument, evtOuverture, evtFermeture) {
		var ecran = new pv.cordova.ui.Ecran() ;
		ecran.titreDocument = titreDocument ;
		ecran.evtOuverture = evtOuverture ;
		ecran.evtFermeture = evtFermeture ;
		_self.ecrans[idEcran] = ecran ;
		return ecran ;
	} ;
	_self.encodeQueryString = function (obj) {
		if(obj === undefined ||obj === null) {
			return "" ;
		}
		var result = "" ;
		for(var n in obj) {
			if(result !== "") {
				result += "&" ;
			}
			result += encodeURIComponent(n) + "=" + encodeURIComponent(obj[n]) ;
		}
		return result ;
	} ;
	_self.htmlEntityEncode = function(value) {
		return _self.noeudHtmlEntity.text(value).html() ;
	} ;
	_self.htmlEntityDecode = function(value) {
		return _self.noeudHtmlEntity.html(value).text() ;
	} ;
	_self.appelleUrl = function(urlService, donneesPost, fonctSucces, fonctException) {
		_self.afficheDlgAttente() ;
		jQuery.ajax({
			url : urlService,
			type : (donneesPost !== undefined && donneesPost !== null) ? "POST" : "GET",
			data : _self.encodeQueryString(donneesPost),
			error : function(xhr, status, error) {
				if(fonctException !== undefined && fonctException !== null) {
					fonctException(error, status, xhr) ;
				}
				_self.cacheDlgAttente() ;
			},
			success : function(result, status, xhr) {
				if(fonctSucces !== undefined && fonctSucces !== null) {
					fonctSucces(result, xhr) ;
				}
				_self.cacheDlgAttente() ;
			}
		}) ;
	} ;
	_self.soumetForm = function(urlService, form, fonctSucces, fonctException) {
		_self.afficheDlgAttente() ;
		var formData = new FormData(form.get(0)) ;
		jQuery.ajax({
			url : urlService,
			type: "POST",
			data: formData,
			error : function(xhr, status, error) {
				if(fonctException !== undefined && fonctException !== null) {
					fonctException(error, status, xhr) ;
				}
				_self.cacheDlgAttente() ;
			},
			success : function(result, status, xhr) {
				if(fonctSucces !== undefined && fonctSucces !== null) {
					fonctSucces(result, xhr) ;
				}
				_self.cacheDlgAttente() ;
			},
			cache: false,
			contentType: false,
			processData: false
		}) ;
	} ;
} ;

pv.cordova.ui.Ecran = function() {
	var _self = this ;
	_self.args = {} ;
	_self.htmlInitial = "" ;
	_self.titreDocument = "" ;
	_self.titre = "" ;
	_self.accesStrict = false ;
	_self.necessiteMembreConnecte = false ;
	_self.privileges = [] ;
	_self.evtOuverture = function(jqEcran, args, ixEcran) {
	} ;
	_self.evtFermeture = function(jqEcran, ixEcran) {
	} ;
} ;

var pvZoneCordova = new pv.cordova.ui.Zone() ;'.PHP_EOL ;
				foreach($this->Scripts as $nomScript => $script)
				{
					$ctn .= 'var pvEcran'.$script->IDInstanceCalc.' = pvZoneCordova.definitEcran("'.$script->IDInstanceCalc.'") ;
pvEcran'.$script->IDInstanceCalc.'.necessiteMembreConnecte = '.svc_json_encode(($script->NecessiteMembreConnecte) ? true : false).' ;
pvEcran'.$script->IDInstanceCalc.'.privileges = '.svc_json_encode($script->Privileges).' ;
pvEcran'.$script->IDInstanceCalc.'.titreDocument = '.svc_json_encode(html_entity_decode($script->TitreDocument)).' ;
pvEcran'.$script->IDInstanceCalc.'.evtOuverture = function(jqEcran, args, ixEcran) {
'.$this->InstrsJsOuvrEcran($script).'} ;
pvEcran'.$script->IDInstanceCalc.'.titre = '.svc_json_encode(html_entity_decode($script->Titre)).' ;'.PHP_EOL ;
				}
				$ctn .= 'jQuery(function() {
	pvZoneCordova.autoConnecteMembre() ;'.PHP_EOL ;
				$ctn .= '}) ;' ;
				return $ctn ;
			}
			public function InclutLibrairiesExternes()
			{
				$this->InscritContenuJs($this->RenduJsCorrectifs()) ;
				parent::InclutLibrairiesExternes() ;
				$this->InscritContenuCSS($this->RenduCSSSidebar()) ;
				$this->InscritContenuJs($this->RenduJsSidebar()) ;
				$ctnCSSResponsive = $this->RenduCSSDeviceResponsive() ;
				if($ctnCSSResponsive != '')
				{
					$this->InscritContenuCSS($ctnCSSResponsive) ;
				}
				$this->InscritContenuCSS($this->RenduCSSPvCordova()) ;
				$this->InscritContenuJs($this->RenduJsPvCordova()) ;
			}
			protected function CreeScriptAccueil()
			{
				return new PvScriptAccueilCordova() ;
			}
			protected function DetecteScriptAppele()
			{
				$this->DetecteParamScriptAppele() ;
				$this->ValeurParamScriptAppele = $this->NomScriptParDefaut ;
				$this->ScriptAppele = & $this->Scripts[$this->NomScriptParDefaut] ;
			}
			public function InstrsJsOuvrEcran(& $script)
			{
				return $script->ValAttrSuppl("ouverture_ecran_cordova") ;
			}
			public function InscritInstrsJsOuvrEcran(& $script, $instrsJs)
			{
				if(! isset($script->AttrsSuppl["ouverture_ecran_cordova"]))
				{
					$script->FixeAttrSuppl("ouverture_ecran_cordova", "") ;
				}
				$script->AttrsSuppl["ouverture_ecran_cordova"] .= $instrsJs.PHP_EOL ;
			}
			public function Execute()
			{
				$this->DemarreExecution() ;
				$this->ScriptAccueil = $this->InsereScriptParDefaut($this->CreeScriptAccueil()) ;
				$this->DetecteScriptsMembership() ;
				$this->DetecteActionAppelee() ;
				$this->PrepareScripts() ;
				if(isset($_GET[$this->NomParamActionAppelee]))
				{
					$this->ExecuteActionPrinc() ;
				}
				else
				{
					$this->DetecteScriptAppele() ;
					$this->ExecuteScriptAppele() ;
				}
				$this->TermineExecution() ;
			}
			public function RenduDocument()
			{
				$ctn = '' ;
				$ctn .= $this->RenduDefinitionTypeDocument().PHP_EOL ;
				$ctn .= '<html lang="'.$this->LangueDocument.'">'.PHP_EOL ;
				$ctn .= $this->RenduEnteteDocument().PHP_EOL ;
				$ctn .= $this->RenduEnteteCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduEspaceTravail().PHP_EOL ;
				$ctn .= $this->RenduPiedCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduPiedDocument().PHP_EOL ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
			protected function ChargeScriptsMembership()
			{
				if(! $this->InclureScriptsMembership || $this->EstNul($this->Membership))
					return ;
				$this->ChargeScriptsMSNonConnecte() ;
				$this->ChargeScriptsMSConnecte() ;
			}
			protected function PrepareScripts()
			{
				$nomsScript = array_keys($this->Scripts) ;
				foreach($nomsScript as $i => $nomScript)
				{
					$script = & $this->Scripts[$nomScript] ;
					$this->DetermineEnvironnement($script) ;
					$this->ExecuteRequeteSoumise($script) ;
				}
			}
			public function LienJsEcran(& $script)
			{
				return 'pvZoneCordova.afficheEcran("'.$script->IDInstanceCalc.'")' ;
			}
			protected function RenduHtmlSidebarNonConnecte()
			{
				$ctn = '' ;
				if($this->MessageMenuNonConnecte != '')
				{
					$ctn .= '<p align="center">'.$this->MessageMenuNonConnecte.'</p>'.PHP_EOL ;
				}
				$ctn .= '<ul class="list-unstyled components">'.PHP_EOL ;
				$ctn .= '<li class="active">
<a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptConnexion)).'"><span class="fa fa-lock"></span> '.$this->TitreMenuConnexion.'</a>
</li>'.PHP_EOL ;
				if($this->AutoriserInscriptions == 1)
				{
					$ctn .= '<li>
<a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptInscription)).'"><span class="fa fa-user"></span> '.$this->TitreMenuInscription.'</a>
</li>'.PHP_EOL ;
				}
				$ctn .= '<li>
<a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptRecouvreMP)).'"><span class="fa fa-envelope"></span> '.$this->TitreMenuRecouvreMP.'</a>
</li>'.PHP_EOL ;
				$ctn .= $this->RenduHtmlMenuNonConnecte() ;
				$ctn .= '</ul>' ;
				return $ctn ;
			}
			protected function RenduHtmlMenuNonConnecte()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduHtmlSidebarConnecte()
			{
				$ctn = '' ;
				if($this->MessageMenuConnecte != '')
				{
					$ctn .= '<p align="center">'.$this->MessageMenuConnecte.'</p>'.PHP_EOL ;
				}
				$ctn .= '<ul class="list-unstyled components">'.PHP_EOL ;
				$ctn .= $this->RenduHtmlMenuAcces().PHP_EOL ;
				$ctn .= $this->RenduHtmlMenuConnecte().PHP_EOL ;
				$ctn .= $this->RenduHtmlMenuMembership().PHP_EOL ;
				$ctn .= '</ul>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduHtmlMenuMembership()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduHtmlMenuConnecte()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduHtmlMenuAcces()
			{
				$privsAcces = $this->PrivilegesEditMembres ;
				array_splice($privsAcces, 0, count($privsAcces), $this->PrivilegesEditMembership) ;
				$ctn = '' ;
				$ctn .= '<li class="active"><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptAccueil)).'"><span class="fa fa-home"></span> '.$this->TitreMenuAccueil.'</a></li>'.PHP_EOL ;
				if($this->AutoriserModifPrefs == 1)
				{
					$ctn .= '<li><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptModifPrefs)).'"><span class="fa fa-info"></span> '.$this->TitreMenuModifPrefs.'</a></li>'.PHP_EOL ;
				}
				$ctn .= '<li><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptChangeMotPasse)).'"><span class="fa fa-eye"></span> '.$this->TitreMenuMotPasse.'</a></li>'.PHP_EOL ;
				$ctn .= '<li><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptDeconnexion)).'"><span class="fa fa-lock"></span> '.$this->TitreMenuDeconnexion.'</a></li>'.PHP_EOL ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $privsAcces).'"><hr></li>' ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $privsAcces).'">' ;
				$ctn .= '<a href="#menu_acces" data-toggle="collapse" aria-expanded="false"><span class="fa fa-unlock"></span> '.$this->TitreMenuEditAcces.'</a>'.PHP_EOL ;
				$ctn .= '<ul class="list-unstyled" id="menu_acces">'.PHP_EOL ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $this->PrivilegesEditMembres).'"><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptListeMembres)).'">'.$this->TitreMenuListeMembres.'</a></li>' ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $this->PrivilegesEditMembres).'"><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptAjoutMembre)).'">'.$this->TitreMenuAjoutMembre.'</a></li>' ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $this->PrivilegesEditMembership).'"><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptListeProfils)).'">'.$this->TitreMenuListeProfils.'</a></li>' ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $this->PrivilegesEditMembership).'"><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptAjoutProfil)).'">'.$this->TitreMenuAjoutProfil.'</a></li>' ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $this->PrivilegesEditMembership).'"><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptListeRoles)).'">'.$this->TitreMenuListeRoles.'</a></li>' ;
				$ctn .= '<li data-pv-privileges="'.join(" ", $this->PrivilegesEditMembership).'"><a href="javascript:'.htmlspecialchars($this->LienJsEcran($this->ScriptAjoutRole)).'">'.$this->TitreMenuAjoutRole.'</a></li>' ;
				$ctn .= '</ul>'.PHP_EOL ;
				$ctn .= '</li>' ;
				return $ctn ;
			}
			protected function RenduHtmlSidebar()
			{
				$ctn = '' ;
				$ctn .= '<nav id="'.$this->IDInstanceCalc.'_sidebar" class="bg-primary">
<div id="'.$this->IDInstanceCalc.'_dismiss" title="Fermer">
<i class="glyphicon glyphicon-arrow-left"></i>
</div>
<br />
<div class="auth_menu" id="'.$this->IDInstanceCalc.'_non_connecte">
'.$this->RenduHtmlSidebarNonConnecte().'
</div>
<div class="auth_menu" id="'.$this->IDInstanceCalc.'_connecte">
'.$this->RenduHtmlSidebarConnecte().'
</div>
</nav>' ;
				return $ctn ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '<body>'.PHP_EOL
.$this->RenduHtmlSidebar().PHP_EOL ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				if($this->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduCtnJs() ;
				}
				$ctn .= '</body>' ;
				return $ctn ;
			}
			protected function RenduEspaceTravail()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'_espaceTravail">'.PHP_EOL ;
				$ctn .= $this->RenduEnteteEspaceTravail().PHP_EOL ;
				$ctn .= $this->RenduCorpsEspaceTravail().PHP_EOL ;
				$ctn .= $this->RenduPiedEspaceTravail().PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function RenduEnteteEspaceTravail()
			{
				$ctn = '' ;
				$ctn .= '<nav class="navbar navbar-light bg-light" id="'.$this->IDInstanceCalc.'_entete">
<button type="button" title="Menu" id="'.$this->IDInstanceCalc.'_sidebarCollapse" class="btn btn-info navbar-btn">
<i class="fa fa-2x fa-bars"></i>
</button>
<ul class="nav navbar-nav navbar-logo mx-auto">
<li class="nav-item">
<h3>'.$this->Titre.'</h3>
</li>
</ul>
</nav>' ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-12">
<div id="'.$this->IDInstanceCalc.'_corps">' ;
				return $ctn ;
			}
			protected function RenduCorpsEspaceTravail()
			{
				$ctn = '' ;
				$nomsScript = array_keys($this->Scripts) ;
				foreach($nomsScript as $i => $nomScript)
				{
					$script = & $this->Scripts[$nomScript] ;
					$ctn .= '<div id="'.$script->IDInstanceCalc.'">'.PHP_EOL ;
					$ctn .= $script->PrepareRendu().PHP_EOL ;
					$ctn .= $script->RenduDispositif().PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduPiedEspaceTravail()
			{
				$ctn = '' ;
				$ctn .= '</div>
</div>
</div>
</div>

<div id="'.$this->IDInstanceCalc.'_pied">
</div>

<div id="'.$this->IDInstanceCalc.'_dialogs">
</div>

<div id="'.$this->IDInstanceCalc.'_attente" class="bg-primary">
Veuillez patienter...
</div>

<div id="'.$this->IDInstanceCalc.'_overlay"></div>
<div id="'.$this->IDInstanceCalc.'_overlay2"></div>' ;
				return $ctn ;
			}
		}
	}
	
?>