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
			public function CreeFournDonnees()
			{
				return ReferentielSws::$SystemeEnCours->CreeFournDonnees() ;
			}
			public function ObtientBDSupport()
			{
				return ReferentielSws::$SystemeEnCours->BDSupport ;
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
				if($this->EstPasNul($this->ModulePage) && $this->NomEntitePage != '' && isset($this->ModulePage->Entites[$this->NomEntitePage]))
				{
					$entitePage = & $this->ModulePage->Entites[$this->NomEntitePage] ;
				}
				return $entitePage ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->ModulePage = $this->ObtientModulePage() ;
				if(! $this->ModulePage->EstDefini())
				{
					$this->LanceExceptionCritique('Le modele "'.htmlentities($this->NomModulePage).'" de la page n\'est pas supporté par ce système !!!') ;
				}
				$this->EntitePage = $this->ObtientEntitePage() ;
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
	}
	
?>