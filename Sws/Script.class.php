<?php
	
	if(! defined('SCRIPT_SWS'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/IHM/Simple.class.php" ;
		}
		define('SCRIPT_SWS', 1) ;
		
		class ScriptBaseSws extends PvScriptWebSimple
		{
			public $NomModulePage ;
			public $ModulePage ;
			public $NomEntitePage ;
			public $EntitePage ;
			public $NomImplemPage ;
			public $ImplemPage ;
			public function CreeFournDonnees()
			{
				return ReferentielSws::$SystemeEnCours->CreeFournDonnees() ;
			}
			public function ObtientBDSupport()
			{
				return ReferentielSws::$SystemeEnCours->BDSupport ;
			}
			public function & ObtientSystemeSws()
			{
				return ReferentielSws::$SystemeEnCours ;
			}
			public function & ObtientModulePage()
			{
				$modulePage = new ModulePageIndefiniSws();
				if($this->NomModulePage != '')
				{
					$modulePage = ReferentielSws::$SystemeEnCours->ObtientModulePageParNom($this->NomModulePage) ;
				}
				return $modulePage ;
			}
			public function & ObtientEntitePage()
			{
				$entitePage = new EntitePageIndefSws() ;
				$modulePage = $this->ObtientModulePage() ;
				if($this->EstPasNul($modulePage) && $this->NomEntitePage != '' && isset($modulePage->Entites[$this->NomEntitePage]))
				{
					$entitePage = & $modulePage->Entites[$this->NomEntitePage] ;
				}
				return $entitePage ;
			}
			public function & ObtientImplemPage()
			{
				$implPage = new ImplemPageIndefSws() ;
				if($this->NomImplemPage != '')
				{
					$implPage = ReferentielSws::$SystemeEnCours->ObtientImplemPageParNom($this->NomImplemPage) ;
				}
				return $implPage ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				if($this->NomModulePage != '')
				{
					$this->ModulePage = $this->ObtientModulePage() ;
					if(! $this->ModulePage->EstDefini())
					{
						$this->LanceExceptionCritique('Le modele "'.htmlentities($this->NomModulePage).'" de la page n\'est pas support? par ce syst?me !!!') ;
					}
					$this->EntitePage = $this->ObtientEntitePage() ;
				}
			}
			protected function & InsereDateModifForm(& $form, $nomColLiee='')
			{
				$flt = $form->InsereFltEditFixe("dateModif", '0', $nomColLiee) ;
				return $flt ;
			}
			protected function LanceExceptionCritique($msg)
			{
				$msg = htmlentities($msg) ;
				echo '<p>'.$msg.'</p>' ;
				exit ;
			}
		}
		class ScriptAdminBaseSws extends ScriptBaseSws
		{
			public $NecessiteMembreConnecte = 1 ;
		}
		class ScriptPublBaseSws extends ScriptBaseSws
		{
		}
	}
	
?>