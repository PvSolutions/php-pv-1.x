<?php
	
	if(! defined("PV_ZONE_MATERIALIZE"))
	{
		if(! defined('PV_MEMBERSHIP_MATERIALIZE'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		define("PV_ZONE_MATERIALIZE", 1) ;
		
		class PvZoneBaseMaterialize extends PvZoneWebSimple
		{
			public $InclureCtnJsEntete = 0 ;
			public $InclureJQuery = 1 ;
			public $InclureBootstrap = 0 ;
			public $CheminFontMaterialize = "https://fonts.googleapis.com/icon?family=Material+Icons" ;
			public $CheminJsMaterialize = "js/materialize.min.js" ;
			public $CheminCSSMaterialize = "css/materialize.min.css" ;
			public $InclureNavbarFlottant = 0 ;
			public $RenduExtraHead = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' ;
			public $ViewportMeta = 'width=device-width, initial-scale=1.0' ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPMaterialize" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionMaterialize" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionMaterialize" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionMaterialize" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseMaterialize" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseMaterialize" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreMaterialize" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreMaterialize" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreMaterialize" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsMaterialize" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreMaterialize" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresMaterialize" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilMaterialize" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilMaterialize" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilMaterialize" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsMaterialize" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleMaterialize" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleMaterialize" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleMaterialize" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesMaterialize" ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplisseurConfigMembershipMaterialize" ;
			public $ClasseCSSMsgExecSucces = "card-panel teal lighten-4" ;
			public $ClasseCSSMsgExecErreur = "card-panel red lighten-4" ;
			public $TagTitre = "h3" ;
			public function InclutLibrairiesExternes()
			{
				parent::InclutLibrairiesExternes() ;
				$this->InscritLienCSS($this->CheminFontMaterialize) ;
				$this->InscritLienCSS($this->CheminCSSMaterialize) ;
				$this->InscritLienJs($this->CheminJsMaterialize) ;
			}
		}
	}
	
?>