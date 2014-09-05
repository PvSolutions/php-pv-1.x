<?php
	
	if(! defined('PV_ZONE_EDITEUR_DOCUMENT'))
	{
		if(! defined('PV_ZONE_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/../AdminDirecte/Zone.class.php" ;
		}
		define('PV_ZONE_EDITEUR_DOCUMENT', 1) ;
		
		class PvZoneWebEditeurDocument extends PvZoneWebAdminDirecte
		{
			public $InscrireMenuFichier = 1 ;
			public $TitreMenuNouveau = "Nouveau" ;
			public $CheminIconeMenuNouveau = "images/menu/nouveau.png" ;
			public $TitreMenuOuvrir = "Ouvrir" ;
			public $CheminIconeMenuOuvrir = "images/menu/ouvrir.png" ;
			public $TitreMenuFermer = "Fermer" ;
			public $CheminIconeMenuFermer = "images/menu/fermer.png" ;
			public $TitreMenuFermerAutres = "Fermer les autres" ;
			public $CheminIconeMenuFermerAutres = "images/menu/fermerAutres.png" ;
			public $TitreMenuFermerTout = "Fermer tout" ;
			public $CheminIconeMenuFermerTout = "images/menu/fermerTout.png" ;
			public $InscrireMenuAide = 1 ;
			public $CategoriesModele = array() ;
			public function InscritCategorieModele($nom, & $categorie)
			{
				$this->CategoriesModele[$nom] = $categorie ;
				$categorie->AdopteZone($nom, $zone) ;
			}
		}
	}
	
?>