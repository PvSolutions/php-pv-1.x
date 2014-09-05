<?php
	
	if(! defined('COMPOSANT_UI_MODULE_BASE_SWS'))
	{
		define('COMPOSANT_UI_MODULE_BASE_SWS', 1) ;
		
		class GrilleModulesSws extends PvGrilleDonneesHtml
		{
			public $ContenuLigneModele = '<a href="${url}"><div><img src="${chemin_icone}" /></div><div>${titre}</div></a>' ;
			public $DefColTitre ;
			public $DefColUrl ;
			public $DefColCheminIcone ;
			public $MaxColonnes = 8 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeFournModules() ;
				$this->ChargeDefCols() ;
			}
			protected function ChargeFournModules()
			{
				$this->FournisseurDonnees = ReferentielSws::$SystemeEnCours->CreeFournModules() ;
				$this->FournisseurDonnees->RequeteSelection = "modules" ;
			}
			protected function ChargeDefCols()
			{
				$this->DefColTitre = $this->InsereDefCol("titre") ;
				$this->DefColUrl = $this->InsereDefCol("url") ;
				$this->DefColCheminIcone = $this->InsereDefCol("chemin_icone") ;
			}
		}
	}
	
?>