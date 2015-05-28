<?php
	
	if(! defined('NOYAU_IMPLEM_PAGE_SWS'))
	{
		define("NOYAU_IMPLEM_PAGE_SWS", 1) ;
		
		class ImplemPageBaseSws extends PvObjet
		{
			public $NomElementSyst ;
			public $SystemeParent ;
			public $EstIndefini = 0 ;
			public $Active = 1 ;
			public $NomRef = "base" ;
			public $EntitesAppls = array() ;
			public $ModulesAppls = array() ;
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