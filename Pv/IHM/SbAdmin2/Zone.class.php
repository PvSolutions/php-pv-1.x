<?php
	
	if(! defined('PV_ZONE_SB_ADMIN2'))
	{
		if(! defined('PV_MENU_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/Menu.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('PV_TABLEAU_DONNEES_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/TableauDonnees.class.php" ;
		}
		if(! defined('PV_FORMULAIRE_DONNEES_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/FormulaireDonnees.class.php" ;
		}
		if(! defined('PV_SCRIPT_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		if(! defined('PV_DOCWEB_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/DocumentWeb.class.php" ;
		}
		if(! defined('PV_MEMBERSHIP_SB_ADMIN'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		
		define('PV_ZONE_SB_ADMIN2', 1) ;
		
		class PvZoneSbAdmin2 extends PvZoneWebSimple
		{
			public $Titre = "Pv - Sb Admin v2.0" ;
			public $InclureCtnJsEntete = 0 ;
			public $InclureJQuery = 1 ;
			public $InclureRenduTitre = 0 ;
			public $InclureRenduChemin = 0 ;
			public $InclureRenduIcone = 0 ;
			public $InclureRenduDescription = 0 ;
			public $LargeurDefautBoiteDlgUrl = 600 ;
			public $HauteurDefautBoiteDlgUrl = 450 ;
			public $RafraichPageSurFermDefautBoiteDlgUrl = true ;
			public $NomDocumentWebEditMembership = "connecte" ;
			public $NomClasseMembership = "AkSqlMembership" ;
			public $InclureScriptsMembership = 1 ;
			public $UtiliserDocumentWeb = 1 ;
			public $EncodageDocument = "utf-8" ;
			public $RenduExtraHead = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' ;
			public $ViewportMeta = 'width=device-width, initial-scale=1' ;
			public $CheminJQuery = "vendor/jquery/jquery.min.js" ;
			public $CheminJQueryMigrate = "vendor/jquery/jquery-migrate.min.js" ;
			public $CheminJsBootstrap = "vendor/bootstrap/js/bootstrap.min.js" ;
			public $CheminJsMetisMenu = "vendor/metisMenu/metisMenu.min.js" ;
			public $CheminJsRafaelCharts = "vendor/raphael/raphael.min.js" ;
			public $CheminJsMorrisCharts = "vendor/morrisjs/morris.min.js" ;
			public $CheminJsSbAdmin = "dist/js/sb-admin-2.js" ;
			public $CheminCSSBootstrap = "vendor/bootstrap/css/bootstrap.min.css" ;
			public $CheminCSSMetisMenu = "vendor/metisMenu/metisMenu.min.css" ;
			public $CheminCSSSbAdmin = "dist/css/sb-admin-2.css" ;
			public $CheminCSSFontAwesome = "vendor/font-awesome/css/font-awesome.min.css" ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPSbAdmin2" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionSbAdmin2" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionSbAdmin2" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionSbAdmin2" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseSbAdmin2" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseSbAdmin2" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreSbAdmin2" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreSbAdmin2" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreSbAdmin2" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsSbAdmin2" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreSbAdmin2" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresSbAdmin2" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilSbAdmin2" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilSbAdmin2" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilSbAdmin2" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsSbAdmin2" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleSbAdmin2" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleSbAdmin2" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleSbAdmin2" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesSbAdmin2" ;
			protected function CreeDocWebNonConnecte()
			{
				return new PvDocWebNonConnecteSbAdmin2() ;
			}
			protected function CreeDocWebConnecte()
			{
				return new PvDocWebConnecteSbAdmin2() ;
			}
			protected function CreeDocWebCadre()
			{
				return new PvDocWebCadreSbAdmin2() ;
			}
			protected function ChargeDocumentsWeb()
			{
				$this->DocumentsWeb["non_connecte"] = $this->CreeDocWebNonConnecte() ;
				$this->DocumentsWeb["connecte"] = $this->CreeDocWebConnecte() ;
				$this->DocumentsWeb["cadre"] = $this->CreeDocWebCadre() ;
			}
			protected function CreeScriptParDefaut()
			{
				return new PvScriptAccueilSbAdmin2() ;
			}
			protected function ChargeScripts()
			{
				$this->InsereScriptParDefaut($this->CreeScriptParDefaut()) ;
			}
		}
		
	}
	
?>