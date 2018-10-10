<?php
	
	if(! defined('PV_PROFIL_MEMBERSHIP_CORDOVA'))
	{
		define('PV_PROFIL_MEMBERSHIP_CORDOVA', 1) ;

		class PvScriptAjoutProfilCordova extends PvScriptAjoutProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutProfilCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifProfilCordova extends PvScriptModifProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifProfilCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprProfilCordova extends PvScriptSupprProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprProfilCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeProfilsCordova extends PvScriptListeProfilsMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauProfilsCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptAjoutRoleCordova extends PvScriptAjoutRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutRoleCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifRoleCordova extends PvScriptModifRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifRoleCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprRoleCordova extends PvScriptSupprRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprRoleCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeRolesCordova extends PvScriptListeRolesMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauRolesCordova" ;
			public $TagTitre = "h3" ;
		}
	}
	
?>