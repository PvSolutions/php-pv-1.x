<?php
	
	if(! defined('PV_MODELE_EDITEUR_DOCUMENT'))
	{
		if(! defined('PV_ZONE_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/../AdminDirecte/Zone.class.php" ;
		}
		define('PV_MODELE_EDITEUR_DOCUMENT', 1) ;
		
		class PvCategorieModeleDocument extends PvObjet
		{
			public $ModelesDocument = array() ;
			public $Titre = "" ;
			public $Description = "" ;
			public $CheminIcone = "" ;
			public $CheminMiniature = "" ;
			public function AdopteZone($nom, & $zone)
			{
				$this->NomElementZone = $nom ;
				$this->ZoneParent = & $zone ;
			}
			public function InscritModeleDocument($nom, & $modeleDocument)
			{
				$this->ModelesDocument[$nom] = & $modeleDocument ;
				$modeleDocument->AdopteCategorie($nom, $modeleDocument) ;
			}
		}
		class PvModeleDocument extends PvObjet
		{
			public $CategorieParent = null ;
			public $NomElementCategorie = "" ;
			public $CheminMiniatureCreation = "" ;
			public $TitreCreation = "" ;
			public $CheminIconeAjout = "" ;
			public $CheminIconeModif = "" ;
			public $CheminIconeSuppr = "" ;
			public $NomClasseComposantOuverture = "" ;
			public $NomClasseScriptAjout = "" ;
			public $NomClasseScriptModif = "" ;
			public $NomClasseScriptSuppr = "" ;
			public function AdopteCategorie($nom, & $categorie)
			{
				$this->NomElementCategorie = $nom ;
				$this->CategorieParent = & $categorie ;
			}
			public function UrlCreation()
			{
			}
			public function ObtientComposantOuverture()
			{
			}
			public function ObtientScriptAjout()
			{
			}
			public function ObtientScriptModif()
			{
			}
			public function ObtientScriptSuppr()
			{
			}
		}
	}
	
?>