<?php
	
	if(! defined('PV_ZONE_EDITEUR_DOCUMENT'))
	{
		if(! defined('PV_ZONE_ADMIN_DIRECTE'))
		{
			include dirname(__FILE__)."/../AdminDirecte/Zone.class.php" ;
		}
		if(! defined('PV_CATEG_MDL_DOCUMENT'))
		{
			include dirname(__FILE__)."/CategMdlDocument.class.php" ;
		}
		if(! defined('PV_MDL_DOCUMENT_BASE'))
		{
			include dirname(__FILE__)."/MdlDocument/Noyau.class.php" ;
		}
		define('PV_ZONE_EDITEUR_DOCUMENT', 1) ;
		
		class PvZoneWebEditDocs extends PvZoneWebAdminDirecte
		{
			public $InscrireMenuFichier = 1 ;
			public $TitreMenuFichier = "Fichier" ;
			public $TitreMenuNouveau = "Nouveau" ;
			public $TitreMenuOuvrir = "Ouvrir" ;
			public $TitreMenuFermer = "Fermer" ;
			public $TitreMenuFermerAutres = "Fermer les autres" ;
			public $TitreMenuFermerTout = "Fermer tout" ;
			public $CheminIconeMenuNouveau = "images/menu/nouveau.png" ;
			public $CheminIconeMenuOuvrir = "images/menu/ouvrir.png" ;
			public $CheminIconeMenuFermer = "images/menu/fermer.png" ;
			public $CheminIconeMenuFermerAutres = "images/menu/fermerAutres.png" ;
			public $CheminIconeMenuFermerTout = "images/menu/fermerTout.png" ;
			public $InscrireMenuAide = 1 ;
			public $CategsMdlDocument = array() ;
			public $NomScriptNouveauDoc = "nouveauDocument" ;
			public $NomScriptOuvrirDoc = "ouvrirDocument" ;
			protected $MenuFichier ;
			protected $SousMenuNouveau ;
			protected $SousMenuOuvrir ;
			protected $SousMenuFermer ;
			protected $SousMenuFermerAutres ;
			protected $SousMenuFermerTout ;
			public function InscritCategMdlDocument($nom, & $categorie)
			{
				$this->CategsMdlDocument[$nom] = $categorie ;
				$categorie->AdopteZone($nom, $zone) ;
			}
			public function & InsereCategMdlDocument($nom, $categorie)
			{
				$this->InscritCategMdlDocument($nom, $categorie) ;
				return $categorie ;
			}
			public function ChargeScripts()
			{
				parent::ChargeScripts() ;
				$this->ScriptAccueil = $this->InsereScriptParDefaut($this->CreeScriptAccueil()) ;
				$this->ScriptBienvenue = $this->InsereScript($this->NomScriptBienvenue, $this->CreeScriptBienvenue()) ;
				$this->ScriptNouveauDoc = $this->InsereScript($this->NomScriptNouveauDoc, $this->CreeScriptNouveauDoc()) ;
				$this->ScriptOuvrDoc = $this->InsereScript($this->NomScriptOuvrDoc, $this->CreeScriptOuvrDoc()) ;
				foreach($this->CategsMdlDocument as $i => & $categ)
				{
					foreach($categ->MdlsDocument as $nomMdl => $mdlDoc)
					{
						$mdlDoc->RemplitZone($this) ;
					}
				}
			}
			protected function CreeScriptAccueil()
			{
				return new PvScriptAccueilEditDocs() ;
			}
			protected function CreeScriptBienvenue()
			{
				return new PvScriptBienvenueEditDocs() ;
			}
			protected function CreeScriptNouveauDoc()
			{
				return new PvScriptNouvDocEditDocs() ;
			}
			protected function CreeScriptOuvrDoc()
			{
				return new PvScriptOuvrDocEditDocs() ;
			}
			protected function ChargeMenuFichier()
			{
				$this->MenuFichier = $this->BarreMenuSuperfish->MenuRacine->InscritSousMenuFige("fichier") ;
				$this->MenuFichier->Titre = $this->TitreMenuFichier ;
				$this->SousMenuNouveau = $this->MenuFichier->InscritSousMenuFenetreScript($this->NomScriptNouveauDoc) ;
				$this->SousMenuNouveau->Titre = $this->TitreMenuNouveau ;
				$this->SousMenuOuvr = $this->MenuFichier->InscritSousMenuFenetreScript($this->NomScriptOuvrDoc) ;
				$this->SousMenuOuvr->Titre = $this->TitreMenuOuvrir ;
				$this->SousMenuFermer = $this->MenuFichier->InscritSousMenuFermeOnglActif() ;
				$this->SousMenuFermer->Titre = $this->TitreMenuFermer ;
				$this->SousMenuFermerAutres = $this->MenuFichier->InscritSousMenuFermeAutresOngls() ;
				$this->SousMenuFermerAutres->Titre = $this->TitreMenuFermerAutres ;
				$this->SousMenuFermerTout = $this->MenuFichier->InscritSousMenuFermeTousOngls() ;
				$this->SousMenuFermerTout->Titre = $this->TitreMenuFermerTout ;
			}
			protected function ChargeBarreMenuSuperfish()
			{
				parent::ChargeBarreMenuSuperfish() ;
				$this->ChargeMenuFichier() ;
			}
		}
		
		class PvScriptAccueilEditDocs extends PvScriptWebSimple
		{
		}
		
		class PvScriptBienvenueEditDocs extends PvScriptWebSimple
		{
			public $Titre = "Bienvenue" ;
		}
		
		class PvScriptNouvDocEditDocs extends PvScriptWebSimple
		{
			protected function RenduDispositifBrut()
			{
				$ctn = "Lllll" ;
				return $ctn ;
			}
		}
		class PvScriptOuvrDocEditDocs extends PvScriptWebSimple
		{
		}
	}
	
?>