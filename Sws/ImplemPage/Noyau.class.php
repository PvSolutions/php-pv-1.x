<?php
	
	if(! defined('NOYAU_IMPLEM_PAGE_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__)."/../ModulePage/Noyau.class.php" ;
		}
		define("NOYAU_IMPLEM_PAGE_SWS", 1) ;
		
		class CfgBaseApplImplemSws
		{
		}
		
		class ImplemPageBaseSws extends ElementRenduBaseSws
		{
			public $EstIndefini = 0 ;
			public $Active = 1 ;
			public $NomRef = "base" ;
			public $EntitesAppls = array() ;
			public $ModulesAppls = array() ;
			protected $CfgsEntitesAppls = array() ;
			protected $CfgsModulesAppls = array() ;
			public $PrivilegesConsult = array() ;
			public $RemplZoneMembrePossible = 1 ;
			public $RemplZonePublPossible = 1 ;
			public $RemplZoneAdminPossible = 1 ;
			public $BarreMenu ;
			public $BarreElemsRendu ;
			public $ClsCSSLienTblList = "ui-widget" ;
			public $LibAjoutTblList = "Ajouter" ;
			public $LibModifTblList = "Modifier" ;
			public $LibSupprTblList = "Supprimer" ;
			public $ChemIconAjoutTblList = "images/icones/ajout.png" ;
			public $ChemIconModifTblList = "images/icones/modif.png" ;
			public $ChemIconSupprTblList = "images/icones/suppr.png" ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
			public function ObtientReqSqlFluxRSS()
			{
				$this->DefFluxRSS->ValeurColNatureRendu = "implem" ;
				$this->DefFluxRSS->ValeurColGroupeRendu = "implems" ;
				$this->DefFluxRSS->ValeurColElemRendu = $this->NomElementSyst ;
				return parent::ObtientReqSqlFluxRSS() ;
			}
			public function & InsereIconeAction(& $tabl, & $colActs, $url, $cheminIcone, $libelle='')
			{
				$lien = $tabl->InsereIconeAction($colActs, $url, $cheminIcone, $libelle) ;
				$lien->ClasseCSS = $this->ClsCSSLienTblList ;
				return $lien ;
			}
			public function & InsereIconeActionModif(& $tabl, & $colActs, $url)
			{
				return $this->InsereIconeAction($tabl, $colActs, $url, $this->ChemIconModifTblList, $this->LibModifTblList) ;
			}
			public function & InsereIconeActionSuppr(& $tabl, & $colActs, $url)
			{
				return $this->InsereIconeAction($tabl, $colActs, $url, $this->ChemIconSupprTblList, $this->LibSupprTblList) ;
			}
			public function & InsereCmdRedirectUrlTabl(& $tabl, $nom, $url, $libelle, $cheminIcone="")
			{
				$cmd = $tabl->InsereCmdRedirectUrl($nom, $url, $libelle) ;
				$cmd->CheminIcone = $cheminIcone ;
				return $cmd ;
			}
			public function & InsereCmdAjoutTabl(& $tabl, $url)
			{
				return $this->InsereCmdRedirectUrlTabl($tabl, "cmdAjout", $url, $this->LibAjoutTblList, $this->ChemIconAjoutTblList) ;
			}
			public function CreeCfgAppl()
			{
				return new CfgBaseApplImplemSws() ;
			}
			public function SupporteEntite(& $entite)
			{
				return (in_array($entite->NomElementModule, $this->EntitesAppls) || in_array($entite->ModuleParent->NomElementSyst, $this->ModulesAppls)) ;
			}
			public function PrepareScript(& $script, & $entite)
			{
			}
			public function ObtientPrivilegesConsult()
			{
				$privs = $this->PrivilegesConsult ;
				if(count($this->SystemeParent->PrivilegesConsult))
				{
					array_splice($privs, 0, 0, $this->SystemeParent->PrivilegesConsult) ;
				}
				return $privs ;
			}
			public function ObtientPrivilegesEdit()
			{
				$privs = $this->PrivilegesConsult ;
				if(count($this->SystemeParent->PrivilegesEdit))
				{
					array_splice($privs, 0, 0, $this->SystemeParent->PrivilegesEdit) ;
				}
				return $privs ;
			}
			public function DefinitSysteme($nom, & $systeme)
			{
				$this->NomElementSyst = $nom ;
				$this->SystemeParent = & $systeme ;
			}
			public function & ObtientBDSupport()
			{
				$bd = & $this->SystemeParent->BDSupport ;
				return $bd ;
			}
			public function EstDefini()
			{
				return $this->EstIndefini == 0 ;
			}
			public function EstAccessible()
			{
				return ($this->Active && $this->EstIndefini == 0) ;
			}
			public function CreeFournDonnees()
			{
				return $this->SystemeParent->CreeFournDonnees() ;
			}
			public function ObtientNomFichier()
			{
				return $this->NomRef ;
			}
			public function AppliqueEntitePubl(& $entite, & $zone)
			{
				if(! $this->EstAccessible())
				{
					return ;
				}
				$this->AppliqueEntitePublSpec($entite, $zone) ;
			}
			protected function AppliqueEntitePublSpec(& $entite, & $zone)
			{
			}
			public function AppliqueEntiteMembre(& $entite, & $zone)
			{
				if(! $this->EstAccessible())
				{
					return ;
				}
				$this->AppliqueEntiteMembreSpec($entite, $zone) ;
			}
			protected function AppliqueEntiteMembreSpec(& $entite, & $zone)
			{
			}
			public function AppliqueEntiteAdmin(& $entite, & $zone)
			{
				if(! $this->EstAccessible())
				{
					return ;
				}
				$this->AppliqueEntiteAdminSpec($entite, $zone) ;
			}
			protected function AppliqueEntiteAdminSpec(& $entite, & $zone)
			{
			}
			public static function ObtientImplemPageComp(& $comp)
			{
				$systemeSws = ReferentielSws::$SystemeEnCours ;
				return $systemeSws->ObtientImplemPageParNom($comp->NomImplemPage) ;
			}
			public function RemplitZonePubl(& $zone)
			{
				if(! $this->RemplZonePublPossible || ! $this->EstAccessible())
				{
					return ;
				}
				$this->RemplitZonePublValide($zone) ;
			}
			protected function RemplitZonePublValide(& $zone)
			{
			}
			public function RemplitZonePublique(& $zone)
			{
				$this->RemplitZonePubl($zone) ;
			}
			public function RemplitZoneAdministration(& $zone)
			{
				$this->RemplitZoneAdmin($zone) ;
			}
			protected function RemplitZoneAdminValide(& $zone)
			{
			}
			public function RemplitZoneAdmin(& $zone)
			{
				if(! $this->RemplZoneAdminPossible || ! $this->EstAccessible())
				{
					return ;
				}
				$this->RemplitZoneAdminValide($zone) ;
			}
			protected function RemplitZoneMembreValide(& $zone)
			{
			}
			public function RemplitZoneMembre(& $zone)
			{
				if(! $this->RemplZoneMembrePossible || ! $this->EstAccessible())
				{
					return ;
				}
				$this->RemplitZoneMembreValide($zone) ;
			}
			public function InscritNouvScript($nom, $script, & $zone, $privs=array())
			{
				$this->InscritScript($nom, $script, $zone, $privs) ;
				return $script ;
			}
			public function InsereScript($nom, $script, & $zone, $privs=array())
			{
				$this->InscritScript($nom, $script, $zone, $privs) ;
				return $script ;
			}
			public function & InscritScript($nom, & $script, & $zone, $privs=array())
			{
				$script->NomImplemPage = $this->NomElementSyst;
				if(count($privs) > 0)
				{
					$script->NecessiteMembreConnecte = 1 ;
					$script->Privileges = $privs ;
				}
				$zone->InscritScript($nom, $script);
				return $script ;
			}
			public function & InsereTacheWeb($nom, $tache, & $zone)
			{
				$this->InscritTacheWeb($nom, $tache, $zone) ;
				return $tache;
			}
			public function InscritNouvTacheWeb($nom, $tache, & $zone)
			{
				$this->InscritTacheWeb($nom, $tache, $zone) ;
				return $tache ;
			}
			public function & InscritTacheWeb($nom, & $tache, & $zone)
			{
				$tache->NomModulePage = $this->NomElementSyst;
				$zone->InscritTacheWeb($nom, $tache);
				return $tache ;
			}
			public function ObtientFournEntitesAppl()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$entitesAppl = array() ;
				foreach($this->SystemeParent->ModulesPage as $i => & $module)
				{
					foreach($module->Entites as $j => & $entite)
					{
						if($this->SupporteEntite($entite))
						{
							$entitesAppl[] = array(
								"id" => $entite->IDInstanceCalc,
								"nom" => $entite->NomElementModule,
								"titre" => $entite->TitreMenu,
							) ;
						}
					}
				}
				$fourn->Valeurs["entitesAppl"] = $entitesAppl ;
				return $fourn ;
			}
			public function InscritEntiteAppl(& $entite, $cfg=null)
			{
				$this->InscritEntiteApplNom($entite->NomElementModule, $cfg) ;
			}
			public function InscritModuleAppl(& $module, $cfg=null)
			{
				$this->InscritModuleApplNom($module->NomElementSyst, $cfg) ;
			}
			public function InscritNomEntiteAppl(& $entite, $cfg=null)
			{
				$this->InscritEntiteApplNom($entite->NomElementModule, $cfg) ;
			}
			public function InscritNomModuleAppl(& $module, $cfg=null)
			{
				$this->InscritModuleApplNom($module->NomElementSyst, $cfg) ;
			}
			public function InscritEntiteApplNom($nomEntite, $cfg=null)
			{
				if($cfg == null)
				{
					$cfg = $this->CreeCfgAppl() ;
				}
				$this->EntitesAppls[] = $nomEntite ;
				$this->CfgEntitesAppls[$nomEntite] = $cfg ;
			}
			public function ObtientCfgEntiteAppl(&$entite)
			{
				return $this->ObtientCfgEntiteApplNom($entite->NomElementModule) ;
			}
			public function ObtientCfgEntiteApplNom($nomEntite)
			{
				if(in_array($nomEntite, $this->EntitesAppls))
				{
					return $this->CfgEntitesAppls[$nomEntite] ;
				}
				return $this->CreeCfgAppl() ;
			}
			public function InscritModuleApplNom($nomModule, $cfg=null)
			{
				if($cfg == null)
				{
					$cfg = $this->CreeCfgAppl() ;
				}
				$this->ModulesAppls[] = $nomModule ;
				$this->CfgModulesAppls[$nomModule] = $cfg ;
			}
			public function ObtientNomEntitesAppl()
			{
				$entitesAppl = array() ;
				foreach($this->SystemeParent->ModulesPage as $i => & $module)
				{
					foreach($module->Entites as $j => & $entite)
					{
						if($this->SupporteEntite($entite))
						{
							$entitesAppl[$entite->NomElementModule] = $entite->TitreMenu ;
						}
					}
				}
				return $entitesAppl ;
			}
			public function AppliqueAvantCmdEntite($nomAction, & $cmd, & $entite)
			{
			}
			public function AppliqueApresCmdEntite($nomAction, & $cmd, & $entite)
			{
			}
			public function RemplitMenu(& $menu)
			{
				$menuImplem = $menu->InscritSousMenuFige($this->NomElementSyst) ;
				$menuImplem->Titre = $this->TitreMenu ;
				$this->RemplitMenuSpec($menuImplem) ;
			}
			public function RemplitMenuSpec(& $menu)
			{
			}
			protected function CreeBarreMenuImplems()
			{
				return ReferentielSws::$SystemeEnCours->CreeBarreMenuImplemsPage() ;
			}
			protected function ChargeBarreMenu(& $barreMenu)
			{
				$barreMenu->InclureRenduIcone = 0 ;
				/*
				$this->MenuAccueil = $barreMenu->MenuRacine->InscritSousMenuScript($barreMenu->ZoneParent->NomScriptParDefaut) ;
				$this->MenuAccueil->Titre = "Accueil" ;
				*/
				foreach($this->SystemeParent->ImplemsPage as $nom => & $implem)
				{
					$implem->RemplitMenu($barreMenu->MenuRacine) ;
				}
			}
			protected function InscritBarreMenu(& $script)
			{
				$this->BarreMenu = $this->InsereBarreMenu($script) ;
			}
			protected function & InsereBarreMenu(& $script)
			{
				$barreMenu = $this->CreeBarreMenuImplems($script) ;
				$barreMenu->AdopteScript("barreMenu", $script) ;
				$barreMenu->ChargeConfig() ;
				$this->ChargeBarreMenu($barreMenu) ;
				return $barreMenu ;
			}
			protected function CreeBarreElemsRendu()
			{
				return ReferentielSws::$SystemeEnCours->CreeBarreElemsRendu() ;
			}
			protected function & InsereBarreElemsRendu(& $script)
			{
				$barreMenu = $this->CreeBarreElemsRendu($script) ;
				$barreMenu->AdopteScript("barreElemsRendu", $script) ;
				$barreMenu->ChargeConfig() ;
				$this->ChargeBarreElemsRendu($barreMenu) ;
				return $barreMenu ;
			}
			protected function InscritBarreElemsRendu(& $script)
			{
				$this->BarreElemsRendu = $this->InsereBarreElemsRendu($script) ;
			}
			public function PrepareScriptAdmin(& $script)
			{
				if(! $script->UtiliserCorpsDocZone)
				{
					return ;
				}
				$this->InscritBarreMenu($script) ;
				$this->InscritBarreElemsRendu($script) ;
			}
		}
		
		class ImplemPageIndefiniSws extends ImplemPageBaseSws
		{
			public $EstIndefini = 1 ;
		}
		class ImplemPageIndefSws extends ImplemPageIndefiniSws
		{
		}
		
		class ImplemTableSws extends ImplemPageBaseSws
		{
			public function PrepareScriptEdit(& $script, & $entite)
			{
			}
			public function PrepareScriptConsult(& $script, & $entite)
			{
			}
			public function PrepareScriptEnum(& $script, & $entite)
			{
			}
			public function PrepareScriptLst(& $script, & $entite)
			{
			}
		}
		
		class ScriptAdminImplemBaseSws extends ScriptAdminBaseSws
		{
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$implemPage = $this->ObtientImplemPage() ;
				$implemPage->PrepareScriptAdmin($this) ;
			}
			public function RenduEnteteAdmin()
			{
				$implemPage = $this->ObtientImplemPage() ;
				$ctn = '' ;
				$ctn .= $implemPage->BarreElemsRendu->RenduDispositif() ;
				$ctn .= $implemPage->BarreMenu->RenduDispositif() ;
				return $ctn ;
			}
			public function RenduPiedAdmin()
			{
				$ctn = "" ;
				return $ctn ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->TablListeCmt->RenduDispositif() ;
				return $ctn ;
			}
			public function RenduDispositifBrut()
			{
				$ctn = '' ;
				// $ctn .= '<p>mmmm</p>' ;
				$ctn .= $this->RenduEnteteAdmin() ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctn .= $this->RenduPiedAdmin() ;
				return $ctn ;
			}
		}
	}
	
?>