<?php
	
	if(! defined('PV_TACHE_PROG'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_TACHE_PROG', 1) ;
		
		class PvTacheCtrlBase extends PvTacheProg
		{
			protected $NaturePlateforme = "console" ;
			public $Message = "La tache est terminée" ;
			protected function CreeDeclenchParDefaut()
			{
				return new PvDeclenchTjrTache() ;
			}
		}
		
		class PvCtrlTachesProgsApp extends PvTacheCtrlBase
		{
			protected function ExecuteSession()
			{
				$nomTaches = array_keys($this->ApplicationParent->TachesProgs) ;
				foreach($nomTaches as $i => $nomTache)
				{
					$tacheProg = & $this->ApplicationParent->TachesProgs[$nomTache] ;
					if($tacheProg->NomElementApplication == $this->NomElementApplication)
					{
						continue ;
					}
					$tacheProg->LanceProcessus() ;
				}
				echo $this->Message ;
			}
		}
		class PvCtrlServsPersistsApp extends PvTacheCtrlBase
		{
			protected function ExecuteSession()
			{
				$nomServsPersists = array_keys($this->ApplicationParent->ServicesPersists) ;
				foreach($nomServsPersists as $i => $nomServPersist)
				{
					$servPersist = & $this->ApplicationParent->ServicesPersists[$nomServPersist] ;
					if($servPersist->NomElementApplication == $this->NomElementApplication)
					{
						continue ;
					}
					if(! $servPersist->Verifie())
						$servPersist->LanceProcessus() ;
				}
				echo $this->Message ;
			}
		}
		
		class PvTacheDossierSE extends PvTacheProg
		{
			public $CheminAbsoluDossier ;
			protected $Flux ;
			public $AutoSupprFichier = 1 ;
			public $AnnuleSupprFichier = 0 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				register_shutdown_function(array(& $this, 'FermeFlux'), array()) ;
			}
			protected function ExecuteSession()
			{
				if($this->CheminAbsoluDossier == '' || ! is_dir($this->CheminAbsoluDossier))
				{
					return ;
				}
				if($this->OuvreFlux() !== false)
				{
					while(($nomFichier = readdir($this->Flux)) !== false)
					{
						if($nomFichier == '.' || $nomFichier == '..')
						{
							continue ;
						}
						if($this->AccepteFichier($nomFichier))
						{
							$this->AnnuleSupprFichier = 0 ;
							$this->TraiteFichier($nomFichier) ;
							if($this->AutoSupprFichier && ! $this->AnnuleSupprFichier)
							{
								$cheminFichier = $this->CheminAbsoluFichier.'/'.$nomFichier ;
								if(! is_dir($cheminFichier))
								{
									unlink($cheminFichier) ;
								}
							}
						}
					}
					$this->FermeFlux() ;
				}
			}
			protected function TraiteFichier($nomFichier)
			{
			}
			protected function OuvreFlux()
			{
				$this->Flux = opendir($this->CheminAbsoluDossier) ;
				return ($this->Flux != false) ;
			}
			public function FermeFlux()
			{
				if(is_resource($this->Flux))
				{
					closedir($this->Flux) ;
					$this->Flux = false ;
				}
			}
			protected function AccepteFichier($nomFichier)
			{
				return 1 ;
			}
		}
		
	}
	
?>