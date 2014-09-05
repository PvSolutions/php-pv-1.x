<?php
	
	if(! defined('ZONE_BASE_WSM'))
	{
		if(! defined('PV_IHM_COMPOSE'))
		{
			include dirname(__FILE__)."/../Pv/IHM/Compose.class.php" ;
		}
		if(! defined('NOYAU_BD_WSM'))
		{
			include dirname(__FILE__)."/BaseDonnees/Base.class.php" ;
		}
		if(! defined('SCRIPT_ADMIN_WSM'))
		{
			include dirname(__FILE__)."/Scripts/Admin.class.php" ;
		}
		if(! defined('SCRIPT_PUBLIQUE_WSM'))
		{
			include dirname(__FILE__)."/Scripts/Publique.class.php" ;
		}
		define('ZONE_BASE_WSM', 1) ;
		
		class ZoneWebBaseWsm extends PvZoneWebCompose
		{
			public $TypeAcces = "base" ;
			protected function ObtientCompsCorpsDoc()
			{
				$comps = $this->CompsCorpsDocument ;
				// Integrer les comps avant app
				$compsAvant = $this->ApplicationParent->CompsAvantCorpsDoc ;
				foreach($compsAvant as $nom => $comp)
				{
					$compsAvant[$nom]->AdopteZone($nom, $this) ;
				}
				array_splice($comps, 0, 0, $compsAvant) ;
				// Integrer les comps apres app
				$compsApres = $this->ApplicationParent->CompsApresCorpsDoc ;
				foreach($compsApres as $nom => $comp)
				{
					$compsApres[$nom]->AdopteZone($nom, $this) ;
				}
				array_splice($comps, count($comps), 0, $compsApres) ;
				return $comps ;
			}
			protected function RenduCorpsDocument()
			{
				// Vrai contenu
				$ctn = '' ;
				if(! $this->UtiliserComposantsRendu)
				{
					$ctn .= parent::RenduCorpsDocument() ;
				}
				else
				{
					$comps = $this->ObtientCompsCorpsDoc() ;
					$ctn .= $this->RenduComposants($comps) ;
				}
				return $ctn ;
			}
		}
		
		class ZoneWebAdminWsm extends ZoneWebBaseWsm
		{
			public $TypeAcces = "admin" ;
			public $ScriptAccueilNonConnecte = null ;
			public $ScriptRecupMotPasse = null ;
			public $ScriptAccueilConnecte = null ;
			public $ScriptEditionPage = null ;
			public $ScriptInfosCache = null ;
			public $ScriptConfigCache = null ;
			public $ScriptViderCache = null ;
			public $ScriptGenererPdf = null ;
			public $ScriptReconstrArbr = null ;
			public $ScriptReconstrRech = null ;
			public $ScriptRepareCtn = null ;
			public $ScriptExecuterPhp = null ;
			public $ScriptTachesProgramees = null ;
			public $ScriptLangueDefaut = null ;
			public $ScriptEditionLangues = null ;
			public $ScriptEditionFichiersLang = null ;
			public $ScriptExportBD = null ;
			public $ScriptRestaureBD = null ;
			public $ScriptCopieFichiersModif = null ;
			public $ScriptEditionFichiers = null ;
			public $ScriptAccesFichiers = null ;
			protected function ChargeScripts()
			{
				parent::ChargeScripts() ;
				$this->ScriptAccueilNonConnecte = new ScriptAccueilNonConnecteAdminWsm() ;
				$this->InscritScript($this->NomScriptParDefaut, $this->ScriptAccueilNonConnecte) ;
			}
		}
		
		class ZoneWebPubliqueWsm extends ZoneWebBaseWsm
		{
			public $TypeAcces = "publique" ;
			public $ScriptAccueil = null ;
			public $ScriptAffichPage = null ;
			public $ScriptRecherche = null ;
			public $ScriptRSS = null ;
			public $NomParamScriptAppele = "action" ;
			public $NomScriptAccueil = "home" ;
			public $NomScriptAffichPage = "show_page" ;
			public $NomScriptRecherche = "search_publish_page" ;
			public $NomScriptRSS = "rss" ;
			public $NomScriptCarteSite = "show_map" ;
			protected function CreeScriptAccueil()
			{
				return new ScriptAccueilPublWsm() ;
			}
			protected function CreeScriptAffichPage()
			{
				return new ScriptAffichPagePublWsm() ;
			}
			protected function CreeScriptRecherche()
			{
				return new ScriptRecherchePublWsm() ;
			}
			protected function CreeScriptRSS()
			{
				return new ScriptRSSPublWsm() ;
			}
			protected function CreeScriptCarteSite()
			{
				return new ScriptCarteSitePublWsm() ;
			}
			public function ChargeScripts()
			{
				parent::ChargeScripts() ;
				$this->ScriptAccueil = $this->CreeScriptAccueil() ;
				$this->InscritScript($this->NomScriptParDefaut, $this->ScriptAccueil) ;
				$this->ScriptAffichPage = $this->CreeScriptAffichPage() ;
				$this->InscritScript($this->NomScriptAffichPage, $this->ScriptAffichPage) ;
				$this->ScriptCarteSite = $this->CreeScriptCarteSite() ;
				$this->InscritScript($this->NomScriptCarteSite, $this->ScriptCarteSite) ;
				$this->ScriptRecherche = $this->CreeScriptRecherche() ;
				$this->InscritScript($this->NomScriptRecherche, $this->ScriptRecherche) ;
				$this->ScriptRSS = $this->CreeScriptRSS() ;
				$this->InscritScript($this->NomScriptRSS, $this->ScriptRSS) ;
			}
		}
	}
	
?>