<?php
	
	if(! defined('PV_NOYAU_ENTITE_DONNEES'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		define('PV_NOYAU_ENTITE_DONNEE', 1) ;
		
		
		class PvReferentielEntiteDonnees
		{
			public $Entites = array() ;
			public function InsereEntite($nom, $entite)
			{
				$this->InscritEntite($nom, $entite) ;
				return $entite ;
			}
			public function InscritEntite($nom, & $entite)
			{
				$this->Entites[$nom] = & $entite ;
				$entite->NomEntite = $nom ;
			}
		}
		
		class PvTableBaseEntiteDonnees extends PvElementApplication
		{
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $Nom ;
			public $NomBaseDonnees ;
			public $NomDonnees ;
			public $RequeteSelectionDonnees ;
			public $TableEditionDonnees ;
			public $NomParam ;
			public $Titre ;
			public $Cols = array() ;
			public $AccepteActAjout = 1 ;
			public $AccepteActModif = 1 ;
			public $AccepteActSuppr = 1 ;
			public $AccepteActListage = 1 ;
			public $Acts = array() ;
		}
		class PvColBaseEntiteDonnees
		{
			public $Nom ;
			public $NomDonnees ;
			public $EstCleDonnees = 0 ;
			public $NomParam ;
			public $Titre ;
			public $CompBase ;
			public $CompSelect ;
			public $CompEdit ;
			public $AccepteAjout = 1 ;
			public $AccepteModif = 1 ;
			public $AccepteEdition = 1 ;
			public $AccepteDetail = 1 ;
			public $AccepteListage = 1 ;
			public $AccepteFiltrageList = 1 ;
		}
		class PvActTableBaseEntiteDonnees extends PvObjet
		{
			public $Active = 1 ;
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $Script ;
		}
		class PvScriptTableEntiteDonnees extends PvScriptWebSimple
		{
			public $NomTableEntite ;
			public function AdopteTableEntite($nom, & $entite, & $zone)
			{
				$this->NomTableEntite = $entite->NomTableEntite ;
				$this->AdopteZone($nom, $zone) ;
			}
		}
		
		class PvEntiteDonneesBase extends PvObjet
		{
			public $NomEntite = "base" ;
			public $Tables = array() ;
		}
	}
	
?>