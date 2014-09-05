<?php
	
	if(! defined('PV_PROC_PERS_TRAITEMENT'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../PvNoyau.class.php" ;
		}
		define('PV_PROC_PERS_TRAITEMENT', 1) ;
		
		class PvProcPersistTraitement extends PvProcessusPersistant
		{
			public $Service = null ;
			public $Controleur = null ;
			public $Registre = null ;
			public $BibliothequeActions = array() ;
			public $SequenceActions = array() ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitService() ;
				$this->ChargeService() ;
				$this->InitControleur() ;
				$this->ChargeControleur() ;
				$this->InitRegistre() ;
				$this->ChargeRegistre() ;
				$this->ChargeBibliothequeActions() ;
			}
			public function CreeService()
			{
				return new PvProcPersistTraitServiceBase() ;
			}
			public function CreeControleur()
			{
				return new PvProcPersistTraitControleurBase() ;
			}
			public function CreeRegistre()
			{
				return new PvProcPersistTraitRegistreBase() ;
			}
			protected function InitService()
			{
				$this->Service = $this->CreeService() ;
				$this->AdopteTraitementParent("service", $this->CreeService()) ;
			}
			protected function InitControleur()
			{
				$this->Controleur = $this->CreeControleur() ;
				$this->AdopteTraitementParent("controleur", $this->CreeControleur()) ;
			}
			protected function InitRegistre()
			{
				$this->Registre = $this->CreeRegistre() ;
				$this->AdopteTraitementParent("registre", $this->CreeRegistre()) ;
			}
			protected function ChargeService()
			{
			}
			protected function ChargeControleur()
			{
			}
			protected function ChargeRegistre()
			{
			}
			protected function ChargeBibliothequeActions($seqActions)
			{
			}
		}
		
		class PvProcPersistTraitElement extends PvObjet
		{
			public $TraitementParent = null ;
			public $ApplicationParent = null ;
			public $NomElementTraitement = null ;
			public function AdopteTraitementParent($nom, & $traitParent)
			{
				$this->NomElementTraitement = $nom ;
				$this->TraitementParent = & $traitParent ;
				$this->ApplicationParent = & $traitParent->ApplicationParent ;
			}
			public function & ObtientBaseDonnees($nom)
			{
				$valeurNulle = null ;
				if(! isset($this->ApplicationParent->BasesDonnees) && ! isset($this->ApplicationParent->BasesDonnees[$nom]))
					return $valeurNulle ;
				return $this->ApplicationParent->BasesDonnees[$nom] ;
			}
		}
		class PvProcPersistTraitServiceBase extends PvProcPersistTraitElement
		{
			public $SequenceActions = array() ;
			public function CreeSession()
			{
				return new PvProcPersistTraitSessionBase() ;
			}
		}
		class PvProcPersistTraitSessionBase extends PvObjet
		{
			protected $EstDemarre = 0 ;
			protected $ExecutionReussie = 0 ;
			public $TotalEntites = 0 ;
			protected $EntiteEnCours = null ;
			protected $ActionsEntiteEnCours = array() ;
			public $SequenceActions = array() ;
			public function AdopteServiceParent($nom, & $service)
			{
				$this->NomElementService = $nom ;
				$this->ServiceParent = & $service ;
				$this->TraitementParent = & $service->TraitementParent ;
				$this->ApplicationParent = & $service->ApplicationParent ;
			}
			public function Execute()
			{
				$this->ExecutionReussie = 0 ;
				$this->TotalEntites = 0 ;
				$this->Demarre() ;
				if(! $this->DemarrageReussi())
				{
					return ;
				}
				$this->TraiteEntites() ;
			}
			protected function Demarre()
			{
				$this->EstDemarre = 1 ;
				$this->ExecInstrsDemarrage() ;
			}
			protected function ExecInstrsDemarrage()
			{
			}
			protected function DemarrageReussi()
			{
				return 1 ;
			}
			protected function TraiteEntites()
			{
				$this->Entites = $this->RecupEntites() ;
				$this->TotalEntites = count($this->Entites) ;
				foreach($this->Entites as $i => $entite)
				{
					$this->TraiteActionsEntite($entite, $i) ;
				}
			}
			protected function TraiteActionsEntite($entite, $position=-1)
			{
				$this->EntiteEnCours = $entite ;
				$this->ActionsEntiteEnCours = $this->ObtientActions() ;
			}
			public function ObtientActions()
			{
				$seqActions = array() ;
				$seqActions = $this->SequenceActions ;
				if(count($seqActions) == 0)
				{
					$seqActions = $this->ServiceParent->SequenceActions ;
				}
				if(count($seqActions) == 0)
				{
					$seqActions = $this->TraitementParent->SequenceActions ;
				}
				if(count($seqActions) == 0)
				{
					return ;
				}
				$actions = $this->TraitementParent->ObtientActions($seqActions) ;
			}
			protected function RecupEntites()
			{
				return array() ;
			}
			protected function Arrete()
			{
				$this->EstDemarre = 0 ;
			}
		}
		class PvProcPersistTraitActionBase extends PvObjet
		{
		}
		class PvProcPersistTraitEntiteBase extends PvObjet
		{
		}
		class PvProcPersistTraitControleurBase extends PvProcPersistTraitElement
		{
		}
		class PvProcPersistTraitRegistreBase extends PvProcPersistTraitElement
		{
		}
		
		class PvProcPersistTraitEntreeRegBase
		{
		}
		class PvProcPersistTraitEntreeSessionReg extends PvProcPersistTraitEntreeRegBase
		{
			public $DateDebut = "" ;
			public $DateFin = "" ;
			public $TotalElements = 0 ;
		}
		class PvProcPersistTraitEntreeActionReg extends PvProcPersistTraitEntreeRegBase
		{
			public $Nom = "" ;
		}
	}
	
?>