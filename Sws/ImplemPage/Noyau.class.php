<?php
	
	if(! defined('NOYAU_IMPLEM_PAGE_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__)."/../ModulePage/Noyau.class.php" ;
		}
		define("NOYAU_IMPLEM_PAGE_SWS", 1) ;
		
		class ImplemPageBaseSws extends ElementRenduBaseSws
		{
			public $EstIndefini = 0 ;
			public $Active = 1 ;
			public $NomRef = "base" ;
			public $EntitesAppls = array() ;
			public $ModulesAppls = array() ;
			public $PrivilegesConsult = array() ;
			public $RemplZoneMembrePossible = 1 ;
			public $RemplZonePublPossible = 1 ;
			public $RemplZoneAdminPossible = 1 ;
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
				$systemeSws = $comp->ScriptParent->ObtientSystemeSws() ;
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
	}
	
?>