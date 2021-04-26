<?php
	
	if(! defined("PV_ZONE_BOOTSTRAP5"))
	{
		if(! defined('PV_MEMBERSHIP_BOOTSTRAP5'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		define("PV_ZONE_BOOTSTRAP5", 1) ;
		
		class PvZoneBaseBootstrap5 extends PvZoneWebSimple
		{
			public $InclureCtnJsEntete = 1 ;
			public $InclureJQuery = 1 ;
			public $InclureBootstrap = 1 ;
			public $InclureNavbarFlottant = 0 ;
			public $RenduExtraHead = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' ;
			public $ViewportMeta = 'width=device-width, initial-scale=1' ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPBootstrap5" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionBootstrap5" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionBootstrap5" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionBootstrap5" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseBootstrap5" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseBootstrap5" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreBootstrap5" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreBootstrap5" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreBootstrap5" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsBootstrap5" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreBootstrap5" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresBootstrap5" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilBootstrap5" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilBootstrap5" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilBootstrap5" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsBootstrap5" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleBootstrap5" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleBootstrap5" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleBootstrap5" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesBootstrap5" ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplisseurConfigMembershipBootstrap5" ;
			public $HauteurTableauFixe = '600px' ;
			public $BackgroundEnteteTableauFixe = 'white' ;
			public $ClasseCSSMsgExecSucces = "alert alert-success" ;
			public $ClasseCSSMsgExecErreur = "alert alert-danger" ;
			public $BackgroundNavbarFlottant = "white" ;
			public $CouleurBordureNavbarFlottant = "" ;
			public $CouleurTexteNavbarFlottant = "black" ;
			public $CheminCSSBootstrap = 'css/bootstrap.min.css' ;
			public $CheminFontAwesome = 'vendor/fontawesome/css/all.min.css' ;
		}
	}
	
?>