<?php
	
	if(! defined('PV_JOURNAL_TRACES'))
	{
		if(! defined('JOURNAL_TRACES'))
		{
			include dirname(__FILE__)."/../JournalTraces/JournalTraces.class.php" ;
		}
		define('PV_JOURNAL_TRACES', 1) ;
		
		class PvJournalTracesBase extends JournalTraces
		{
			public $ApplicationParent ;
			public $ZoneParent ;
			public function AdopteApplication(& $application)
			{
				$this->ApplicationParent = & $zone->ApplicationParent ;
			}
			public function AdopteZone(& $zone)
			{
				$this->ZoneParent = & $zone ;
				$this->AdopteApplication($zone->ApplicationParent) ;
			}
		}
		class PvJournalRequetesEnvoyeesBase extends PvJournalTracesBase
		{
		}
		class PvJournalRequetesEnvoyeesHttp extends PvJournalRequetesEnvoyeesBase
		{
			public $FichierTraces = null ;
			public $NomDossierRacineTracesExcel = "" ;
			protected function ObtientNomDossierRacineTracesExcel()
			{
				$nomDossier = $this->NomDossierRacineTracesExcel ;
				if($nomDossier == "")
				{
					$nomDossier = "journal/requetes" ;
				}
				return $nomDossier ;
			}
			protected function ChargeAttributs()
			{
				$this->Attributs= array() ;
				$this->Attributs[] = new AttributIdSessionEntreeTrace() ;
				$this->Attributs[] = new AttributDateCreationEntreeTrace() ;
				$this->Attributs[] = new AttributNomNavigateurEntreeTrace() ;
				$this->Attributs[] = new AttributAddrIPRemoteEntreeTrace() ;
				$this->Attributs[] = new AttributHttpGetEntreeTrace() ;
				$this->Attributs[] = new AttributHttpPostEntreeTrace() ;
				$this->Attributs[] = new AttributOSClientEntreeTrace() ;
			}
			protected function ChargeFichiersTraces()
			{
				$this->FichiersTraces = array() ;
				/*
				$this->FichierTracesTexte = new FichierTexteTraces() ;
				$this->FichierTracesTexte->NomDossierRacine = $this->NomDossierRacineTracesTexte ;
				$this->FichiersTraces[] = & $this->FichierTracesTexte ;
				*/
				$this->FichierTraces = new FichierExcelTraces() ;
				$this->FichierTraces->NomDossierRacine = $this->ObtientNomDossierRacineTracesExcel() ;
				$this->FichiersTraces[] = & $this->FichierTraces ;
			}
		}
		
		class PvNomElementZoneEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "nom_zone" ;
			public $Libelle = "Nom Zone" ;
			public $UtiliserCache = 1 ;
			public function ObtientValeur()
			{
				if($this->ZoneParent == null)
				{
					return "(zone non definie)" ;
				}
				return $this->EntreeTraceParent->JournalParent->ZoneParent->NomElementApplication ;
			}
		}
		class PvNomElementScriptEntreeTrace extends AttributEntreeTraceBase
		{
			public $Nom = "nom_script" ;
			public $Libelle = "Nom Script" ;
			public $UtiliserCache = 1 ;
			public function ObtientValeur()
			{
				if($this->ZoneParent == null)
				{
					return "(zone non definie)" ;
				}
				return $this->EntreeTraceParent->JournalParent->ZoneParent->ScriptEnCours->NomElementZone ;
			}
		}
	}
	
?>