<?php
	
	if(! defined('SYSTEME_SWS'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/Base.class.php" ;
		}
		if(! defined('MEMBERSHIP_SWS'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		if(! defined('ZONE_SWS'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		if(! defined('MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__)."/ModulePage.class.php" ;
		}
		define('SYSTEME_SWS', 1) ;
		
		class ReferentielSws
		{
			static $SystemeEnCours ;
		}
		
		class SystemeBaseSws extends PvObjet
		{
			public $BDSupport ;
			public $InclureAdminPubl = 0 ;
			public $CheminAdminVersPubl = ".." ;
			public $CheminMembreVersPubl = ".." ;
			public $NomScriptListeModulesPage = "liste_module_page" ;
			public $ScriptListeModulesPage = "liste_module_page" ;
			public $MaxColonnesBarreMenuModsPage = 10 ;
			public function ObtientCheminPubl($chemin)
			{
				return substr($chemin, strlen($this->CheminAdminVersPubl."/"), strlen($chemin)) ;
			}
			public function CreeBarreMenuModulesPage()
			{
				$barreMenu = new PvTablMenuHoriz() ;
				$barreMenu->MaxColonnes = $this->MaxColonnesBarreMenuModsPage ;
				return $barreMenu ;
			}
			public function CreeBarreMenuEntitesPage()
			{
				return new PvCadreMenuWeb() ;
			}
			public function CreeFournModules()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$fourn->ChargeConfig() ;
				$modules = array() ;
				$i = 0 ;
				foreach($this->ModulesPage as $nomModule => & $module)
				{
					$modules[] = array(
						"index" => $i,
						"titre" => $module->ObtientTitreMenu(),
						"url" => $module->ObtientUrlAdmin(),
						"chemin_icone" => $module->ObtientCheminIcone(),
						"version" => $module->ObtientVersion(),
						"id" => $module->IDInstanceCalc,
						"nom_element" => $module->NomElementSyst,
						"ref_module" => $module->NomRef,
					) ;
					$i++ ;
				}
				$fourn->Valeurs = array("modules" => $modules) ;
				return $fourn ;
			}
			public function CreeFournEntites($inclureVide = 1)
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$fourn->ChargeConfig() ;
				$entites = array() ;
				$i = 0 ;
				if($inclureVide)
				{
					$entites[] = array("index" => -1, "libelle" => "", "nom" => "", "id" => "", "nom_element" => "", "nom_element_module" => "", "ref_module" => "") ;
				}
				foreach($this->ModulesPage as $nomModule => & $module)
				{
					foreach($module->Entites as $nomEntite => & $entite)
					{
						$entites[] = array(
							"index" => $i,
							"libelle" => $entite->LibEntite,
							"nom" => $entite->NomEntite,
							"id" => $entite->IDInstanceCalc,
							"nom_element" => $entite->NomElementModule,
							"nom_element_module" => $module->NomElementSyst,
							"ref_module" => $module->NomRef,
						) ;
						$i++ ;
					}
				}
				$fourn->Valeurs = array("entites" => $entites) ;
				return $fourn ;
			}
			protected function CreeScriptListeModules()
			{
				
			}
			public function Execute()
			{
				ReferentielSws::$SystemeEnCours = & $this ;
				$this->ChargeConfig() ;
			}
			public function & ObtientModulePageNomme($nomModele)
			{
				$module = $this->ObtientModulePageParNom($nomModele) ;
				return $module ;
			}
			public function & ObtientModulePageParNom($nomModele)
			{
				$modulePage = new ModulePageIndefiniSws() ;
				if(isset($this->ModulesPage[$nomModele]))
				{
					$modulePage = & $this->ModulesPage[$nomModele] ;
				}
				// print get_class($modulePage).'<br>' ;
				return $modulePage;
			}
			public function & ObtientModulePageRef($nomRef)
			{
				$modules = $this->ObtientModulesPageRef($nomRef) ;
				$modulePage = new ModulePageIndefiniSws() ;
				if(count($modules) == 0)
				{
					return $modulePage ;
				}
				return $modules[0] ;
			}
			public function & ObtientModulesPageRef($nomRef)
			{
				$modulesPage = array() ;
				foreach($modulesPage as $nom => & $modulePage)
				{
					if($modulePage->NomRef == $nomRef)
					{
						$modulesPage[] = & $modulePage ;
					}
				}
				return $modulesPage;
			}
			public function & CreeFournDonnees()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = & $this->BDSupport ;
				return $fourn ;
			}
			public function CreeFilAriane()
			{
				return new FilArianeSws() ;
			}
			protected function RemplitZonePublSpec(& $zone)
			{
			}
			public function RemplitZonePubl(& $zone)
			{
				$this->RemplitZonePublSpec($zone) ;
				$nomModeles = array_keys($this->ModulesPage) ;
				foreach($nomModeles as $i => $nomModele)
				{
					$module = & $this->ModulesPage[$nomModele] ;
					$module->RemplitZonePubl($zone) ;
				}
			}
			protected function RemplitZoneAdminSpec(& $zone)
			{
			}
			public function RemplitZoneAdmin(& $zone)
			{
				$this->RemplitZoneAdminSpec($zone) ;
				$nomModeles = array_keys($this->ModulesPage) ;
				foreach($nomModeles as $i => $nomModele)
				{
					$module = & $this->ModulesPage[$nomModele] ;
					$module->RemplitZoneAdmin($zone) ;
				}
			}
			protected function RemplitZoneMembreSpec(& $zone)
			{
			}
			public function RemplitZoneMembre(& $zone)
			{
				$this->RemplitZoneMembreSpec($zone) ;
				$nomModeles = array_keys($this->ModulesPage) ;
				foreach($nomModeles as $i => $nomModele)
				{
					$module = & $this->ModulesPage[$nomModele] ;
					$module->RemplitZoneMembre($zone) ;
				}
			}
			public function & InscritNouvMdlPage($nom, $module)
			{
				$this->InscritModulePage($nom, $module) ;
				return $module ;
			}
			public function & InsereMdlPage($nom, $module)
			{
				$this->InscritModulePage($nom, $module) ;
				return $module ;
			}
			public function InscritMdlPage($nom, & $module)
			{
				$this->InscritModulePage($nom, $module) ;
			}
			public function & InscritNouvModulePage($nom, $module)
			{
				$this->InscritModulePage($nom, $module) ;
				return $module ;
			}
			public function & InsereModulePage($nom, $module)
			{
				$this->InscritModulePage($nom, $module) ;
				return $module ;
			}
			public function InscritModulePage($nom, & $module)
			{
				if($nom == '')
				{
					$nom = $module->NomRef ;
				}
				$this->ModulesPage[$nom] = & $module ;
				$module->DefinitSysteme($nom, $this) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigBase() ;
				$this->ChargeModulesPage() ;
				$this->ChargeConfigModulesPage() ;
				$this->ChargeConfigSuppl() ;
			}
			protected function ChargeConfigModulesPage()
			{
				foreach($this->ModulesPage as $nom => & $module)
				{
					$module->ChargeConfig() ;
				}
			}
			protected function ChargeModulesPage()
			{
			}
			protected function ChargeConfigBase()
			{
			}
			protected function ChargeConfigSuppl()
			{
			}
			public function ObtientUrlAdminPremModule()
			{
				$url = 'javascript:;' ;
				$nomModules = array_keys($this->ModulesPage) ;
				if(count($nomModules) > 0)
				{
					$url = $this->ModulesPage[$nomModules[0]]->ObtientUrlAdmin() ;
				}
				return $url ;
			}
			public function ObtientUrlPublPremModule()
			{
				$url = 'javascript:;' ;
				$nomModules = array_keys($this->ModulesPage) ;
				if(count($nomModules) > 0)
				{
					$url = $this->ModulesPage[$nomModule]->ObtientUrlPubl() ;
				}
				return $url ;
			}
		}
		
		class SystemeDefautSws extends SystemeBaseSws
		{
			public $ModulePageRacine ;
			public $ModuleArticle ;
			public $ModuleMenu ;
			public $ModuleSlider ;
			public $ModuleContact ;
			public $ModuleLivreDOr ;
			public $PrivilegesConsult = array() ;
			public $PrivilegesEdit = array() ;
			protected function CreeModulePageRacine()
			{
				return new ModulePageRacineSws() ;
			}
			protected function CreeModuleArticle()
			{
				return new ModuleArticleSws() ;
			}
			protected function CreeModuleMenu()
			{
				return new ModuleMenuSws() ;
			}
			protected function CreeModuleSlider()
			{
				return new ModuleSliderSws() ;
			}
			protected function CreeModuleContact()
			{
				return new ModuleContactSws() ;
			}
			protected function CreeModuleLivreDOr()
			{
				return new ModuleLivreDOrSws() ;
			}
			protected function ChargeModulesPage()
			{
				$this->ModulePageRacine = $this->CreeModulePageRacine() ;
				$this->InscritModulePage('', $this->ModulePageRacine) ;
				$this->ModuleArticle = $this->CreeModuleArticle() ;
				$this->InscritModulePage('', $this->ModuleArticle) ;
				$this->ModuleMenu = $this->CreeModuleMenu() ;
				$this->InscritModulePage('', $this->ModuleMenu) ;
				$this->ModuleContact = $this->CreeModuleContact() ;
				$this->InscritModulePage('', $this->ModuleContact) ;
				$this->ModuleSlider = $this->CreeModuleSlider() ;
				$this->InscritModulePage('', $this->ModuleSlider) ;
				$this->ModuleLivreDOr = $this->CreeModuleLivreDOr() ;
				$this->InscritModulePage('', $this->ModuleLivreDOr) ;
			}
		}
		
	}
	
?>