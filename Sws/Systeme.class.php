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
		if(! defined('IMPLEM_PAGE_SWS'))
		{
			include dirname(__FILE__)."/ImplemPage.class.php" ;
		}
		define('SYSTEME_SWS', 1) ;
		
		class ReferentielSws
		{
			static $SystemeEnCours ;
		}
		
		class SystemeBaseSws extends PvObjet
		{
			public $BDSupport ;
			public $ModulesPage = array() ;
			public $ImplemsPage = array() ;
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
			public function CreeBarreElemsRendu()
			{
				$barreMenu = new PvBarreBtnsHoriz() ;
				return $barreMenu ;
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
					if($module->Active == 0)
					{
						continue ;
					}
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
			public function CreeFournImplems()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$fourn->ChargeConfig() ;
				$implems = array() ;
				$i = 0 ;
				// print count($this->ImplemsPage)." kkk" ;
				foreach($this->ImplemsPage as $nomImplem => & $implem)
				{
					if($implem->Active == 0)
					{
						continue ;
					}
					$implems[] = array(
						"index" => $i,
						"titre" => $implem->ObtientTitreMenu(),
						"url" => $implem->ObtientUrlAdmin(),
						"chemin_icone" => $implem->ObtientCheminIcone(),
						"version" => $implem->ObtientVersion(),
						"id" => $implem->IDInstanceCalc,
						"nom_element" => $implem->NomElementSyst,
						"ref_module" => $implem->NomRef,
					) ;
					$i++ ;
				}
				$fourn->Valeurs = array("implems" => $implems) ;
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
			public function & ObtientModulePageNomme($nomModule)
			{
				$module = $this->ObtientModulePageParNom($nomModule) ;
				return $module ;
			}
			public function & ObtientModulePageParNom($nomModule)
			{
				$modulePage = new ModulePageIndefiniSws() ;
				if(isset($this->ModulesPage[$nomModule]))
				{
					$modulePage = & $this->ModulesPage[$nomModule] ;
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
			public function & ObtientImplemPageNomme($nomImplem)
			{
				$implem = $this->ObtientImplemPageParNom($nomImplem) ;
				return $implem ;
			}
			public function & ObtientImplemPageParNom($nomImplem)
			{
				$implemPage = new ImplemPageIndefiniSws() ;
				if(isset($this->ImplemsPage[$nomImplem]))
				{
					$implemPage = & $this->ImplemsPage[$nomImplem] ;
				}
				// print get_class($implemPage).'<br>' ;
				return $implemPage;
			}
			public function & ObtientImplemPageRef($nomRef)
			{
				$implems = $this->ObtientImplemsPageRef($nomRef) ;
				$implemPage = new ImplemPageIndefiniSws() ;
				if(count($implems) == 0)
				{
					return $implemPage ;
				}
				return $implems[0] ;
			}
			public function & ObtientImplemsPageRef($nomRef)
			{
				$implemsPage = array() ;
				foreach($implemsPage as $nom => & $implemPage)
				{
					if($implemPage->NomRef == $nomRef)
					{
						$implemsPage[] = & $implemPage ;
					}
				}
				return $implemsPage;
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
				$nomModules = array_keys($this->ModulesPage) ;
				foreach($nomModules as $i => $nomModule)
				{
					$module = & $this->ModulesPage[$nomModule] ;
					$module->RemplitZonePubl($zone) ;
				}
				$nomImplems = array_keys($this->ImplemsPage) ;
				foreach($nomImplems as $i => $nomImplem)
				{
					$implem = & $this->ImplemsPage[$nomImplem] ;
					$implem->RemplitZonePubl($zone) ;
				}
				$this->AppliqueImplemsPubl($zone) ;
			}
			protected function RemplitZoneAdminSpec(& $zone)
			{
			}
			public function RemplitZoneAdmin(& $zone)
			{
				$zone->GestTachesWeb->NomDossierTaches = $this->CheminAdminVersPubl. DIRECTORY_SEPARATOR .$zone->GestTachesWeb->NomDossierTaches ;
				$this->RemplitZoneAdminSpec($zone) ;
				$nomModules = array_keys($this->ModulesPage) ;
				foreach($nomModules as $i => $nomModule)
				{
					$module = & $this->ModulesPage[$nomModule] ;
					$module->RemplitZoneAdmin($zone) ;
				}
				$nomImplems = array_keys($this->ImplemsPage) ;
				foreach($nomImplems as $i => $nomImplem)
				{
					$implem = & $this->ImplemsPage[$nomImplem] ;
					$implem->RemplitZoneAdmin($zone) ;
				}
				$this->AppliqueImplemsAdmin($zone) ;
			}
			protected function RemplitZoneMembreSpec(& $zone)
			{
			}
			public function RemplitZoneMembre(& $zone)
			{
				$this->RemplitZoneMembreSpec($zone) ;
				$nomModules = array_keys($this->ModulesPage) ;
				foreach($nomModules as $i => $nomModule)
				{
					$module = & $this->ModulesPage[$nomModule] ;
					$module->RemplitZoneMembre($zone) ;
				}
				$nomImplems = array_keys($this->ImplemsPage) ;
				foreach($nomImplems as $i => $nomImplem)
				{
					$implem = & $this->ImplemsPage[$nomImplem] ;
					$implem->RemplitZoneMembre($zone) ;
				}
				$this->AppliqueImplemsMembre($zone) ;
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
				$this->ChargeImplemsPage() ;
				$this->ChargeConfigImplemsPage() ;
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
			protected function ChargeConfigImplemsPage()
			{
				foreach($this->ImplemsPage as $nom => & $implem)
				{
					$implem->ChargeConfig() ;
				}
			}
			protected function ChargeImplemsPage()
			{
			}
			protected function ChargeConfigBase()
			{
			}
			protected function ChargeConfigSuppl()
			{
			}
			public function ObtientUrlZoneAdmin(& $zone)
			{
				return "" ;
			}
			public function ObtientUrlZonePubl(& $zone)
			{
				return "" ;
			}
			public function ObtientUrlZoneMembre(& $zone)
			{
				return "" ;
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
			public function ObtientUrlAdminPremImplem()
			{
				$url = 'javascript:;' ;
				$nomImplems = array_keys($this->ImplemsPage) ;
				if(count($nomImplems) > 0)
				{
					$url = $this->ImplemsPage[$nomImplems[0]]->ObtientUrlAdmin() ;
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
			public function & InscritNouvImplemPage($nom, $implem)
			{
				$this->InscritImplemPage($nom, $implem) ;
				return $implem ;
			}
			public function & InsereImplemPage($nom, $implem)
			{
				$this->InscritImplemPage($nom, $implem) ;
				return $implem ;
			}
			public function InscritImplemPage($nom, & $implem)
			{
				if($nom == '')
				{
					$nom = $implem->NomRef ;
				}
				$this->ImplemsPage[$nom] = & $implem ;
				$implem->DefinitSysteme($nom, $this) ;
			}
			protected function AppliqueImplemsZone(& $zone, $mode=0)
			{
				$nomImplems = array_keys($this->ImplemsPage) ;
				foreach($nomImplems as $i => $nomImplem)
				{
					$implem = & $this->ImplemsPage[$nomImplem] ;
					if(! $implem->EstAccessible() || (count($implem->EntitesAppls) == 0 && count($implem->ModulesAppls) == 0))
					{
						continue ;
					}
					$nomModules = array_keys($this->ModulesPage) ;
					foreach($nomModules as $j => $nomModule)
					{
						$module = & $this->ModulesPage[$nomModule] ;
						if(! $module->EstAccessible())
						{
							continue ;
						}
						$nomEntites = array_keys($module->Entites) ;
						foreach($nomEntites as $k => $nomEntite)
						{
							$entite = & $module->Entites[$nomEntite] ;
							if(! $entite->EstAccessible())
							{
								continue ;
							}
							if(in_array($entite->NomElementModule, $implem->EntitesAppls) || in_array($module->NomElementSyst, $implem->ModulesAppls))
							{
								switch($mode)
								{
									case 0 :
									{
										$implem->AppliqueEntitePubl($entite, $zone) ;
									}
									break ;
									case 1 :
									{
										$implem->AppliqueEntiteMembre($entite, $zone) ;
									}
									break ;
									case 3 :
									{
										$implem->AppliqueEntiteMembre($entite, $zone) ;
									}
									break ;
								}
							}
						}
					}
				}
			}
			protected function AppliqueImplemsPubl(& $zone)
			{
				$this->AppliqueImplemsPublSpec($zone) ;
				$this->AppliqueImplemsZone($zone, 0) ;
			}
			protected function AppliqueImplemsPublSpec(& $zone)
			{
			}
			protected function AppliqueImplemsMembre(& $zone)
			{
				$this->AppliqueImplemsMembreSpec($zone) ;
				$this->AppliqueImplemsZone($zone, 1) ;
			}
			protected function AppliqueImplemsMembreSpec(& $zone)
			{
			}
			protected function AppliqueImplemsAdmin(& $zone)
			{
				$this->AppliqueImplemsAdminSpec($zone) ;
				$this->AppliqueImplemsZone($zone, 2) ;
			}
			protected function AppliqueImplemsAdminSpec(& $zone)
			{
			}
		}
		
		class SystemeDefautSws extends SystemeBaseSws
		{
			public $ModuleCompteurHits ;
			public $ModulePageRacine ;
			public $ModuleArticle ;
			public $ModuleMenu ;
			public $ModuleSlider ;
			public $ModuleContact ;
			public $ModuleLivreDOr ;
			public $ModuleNewsletter ;
			public $ImplemCommentaire ;
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
			protected function CreeModuleCompteurHits()
			{
				return new ModuleCompteurHitsSws() ;
			}
			protected function CreeModuleNewsletter()
			{
				return new ModuleNewsletterSws() ;
			}
			protected function CreeImplemCommentaire()
			{
				return new ImplemCommentaireSws() ;
			}
			protected function ChargeModulesPage()
			{
				$this->ModulePageRacine = $this->CreeModulePageRacine() ;
				$this->InscritModulePage('', $this->ModulePageRacine) ;
				$this->ModuleMenu = $this->CreeModuleMenu() ;
				$this->InscritModulePage('', $this->ModuleMenu) ;
				$this->ModuleContact = $this->CreeModuleContact() ;
				$this->InscritModulePage('', $this->ModuleContact) ;
				$this->ModuleSlider = $this->CreeModuleSlider() ;
				$this->InscritModulePage('', $this->ModuleSlider) ;
				$this->ModuleLivreDOr = $this->CreeModuleLivreDOr() ;
				$this->InscritModulePage('', $this->ModuleLivreDOr) ;
				$this->ModuleArticle = $this->CreeModuleArticle() ;
				$this->InscritModulePage('', $this->ModuleArticle) ;
				$this->ModuleNewsletter = $this->CreeModuleNewsletter() ;
				$this->InscritModulePage('', $this->ModuleNewsletter) ;
				/*
				$this->ModuleCompteurHits = $this->CreeModuleCompteurHits() ;
				$this->InscritModulePage('', $this->ModuleCompteurHits) ;
				*/
			}
			protected function ChargeImplemsPage()
			{
				$this->ImplemCommentaire = $this->CreeImplemCommentaire() ;
				$this->InscritImplemPage('', $this->ImplemCommentaire) ;
				$this->ImplemCommentaire->EntitesAppls[] = $this->ModuleArticle->EntiteArticle->NomEntite ;
			}
		}
		
	}
	
?>