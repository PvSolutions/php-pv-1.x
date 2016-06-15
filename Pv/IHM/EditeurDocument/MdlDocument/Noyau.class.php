<?php
	
	if(! defined('PV_NOYAU_MDL_DOCUMENT'))
	{
		define('PV_NOYAU_MDL_DOCUMENT', 1) ;
		
		class PvMdlDocumentBaseEditDocs extends PvObjet
		{
			public $CategParent = null ;
			public $NomElementCateg = "" ;
			public $CheminMiniatureCreation = "" ;
			public $TitreCreation = "" ;
			public $CheminIconeAjout = "" ;
			public $CheminIconeModif = "" ;
			public $CheminIconeSuppr = "" ;
			public $NomClasseComposantOuverture = "" ;
			public $NomClasseScriptAjout = "" ;
			public $NomClasseScriptModif = "" ;
			public $NomClasseScriptSuppr = "" ;
			public function AdopteCateg($nom, & $categorie)
			{
				$this->NomElementCateg = $nom ;
				$this->CategParent = & $categorie ;
			}
			public function RemplitZone(& $zone)
			{
			}
			public function UrlCreation()
			{
			}
			public function ObtientCompOuvrDocs()
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
		
		class PvInstDocumentBaseEditDocs extends PvObjet
		{
			public $Donnees ;
		}
		class PvDonneesDocumentEditDocs
		{
		}
	}
	
?>