<?php
	
	if(! defined('PV_MEMBRE_MEMBERSHIP_CORDOVA'))
	{
		define('PV_MEMBRE_MEMBERSHIP_CORDOVA', 1) ;
		
		
		
		class PvScriptAjoutMembreCordova extends PvScriptAjoutMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutMembreCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptInscriptionCordova extends PvScriptInscriptionWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireInscriptionMembreCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifMembreCordova extends PvScriptModifMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifMembreCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprMembreCordova extends PvScriptSupprMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprMembreCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifPrefsCordova extends PvScriptModifPrefsWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifInfosCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptChangeMotPasseCordova extends PvScriptChangeMotPasseWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMotPasseCordova" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptDoitChangerMotPasseCordova extends PvScriptDoitChangerMotPasseWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireDoitChangerMotPasseCordova" ;
		}
		class PvScriptChangeMPMembreCordova extends PvScriptChangeMPMembreWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMPMembreCordova" ;
		}
		class PvScriptListeMembresCordova extends PvScriptListeMembresMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauMembresCordova" ;
			public $TagTitre = "h3" ;
		}

	}
	
?>