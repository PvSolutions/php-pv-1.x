<?php
	
	if(! defined('PV_SCRIPT_IHM'))
	{
		if(! defined('PV_NOYAU_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_IU'))
		{
			include dirname(__FILE__)."/ComposantIU.class.php" ;
		}
		define('PV_SCRIPT_IHM', 1) ;
		
		class PvScriptIHMDeBase extends PvObjet
		{
            /*
             * Zone qui contient le script. Doit etre toujours affectee avec la methode
             * AdopteZone()
             * 
             * @var PvZoneIHMDeBase
             */
			public $ZoneParent ;
			public $ApplicationParent ;
			public $NomElementZone = "" ;
			public $CheminRoute = "" ;
			public $NomIntegrationParent = "" ;
			public $NomZoneAppelDistant = "" ;
			public $ValeurAppel = "" ;
			public $CheminIcone = "" ;
			public $CheminMiniature = "" ;
			public $Titre = "" ;
			public $TitreDocument = "" ;
			public $Privileges = array() ;
			public $PrivilegesStricts = 0 ;
			public $MessageIndisponible = "" ;
			public $NecessiteMembreConnecte = 0 ;
			public $AnnulDetectMemberCnx = 0 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeReferantsSurs() ;
			}
			protected function ChargeReferantsSurs()
			{
			}
			public function EstBienRefere()
			{
				return 1 ;
			}
			public function RapporteRequeteEnvoyee()
			{
				$this->ZoneParent->RapporteRequeteEnvoyee() ;
			}
			public function RapporteException($exception)
			{
				$this->ZoneParent->RapporteException($exception) ;
			}
			public function & IntegrationParent()
			{
				return $this->ApplicationParent->ObtientIntegration($this->NomIntegrationParent) ;
			}
			public function & ZoneAppelDistant()
			{
				$ihm = new PvZoneIHMDeBase() ;
				if($this->NomZoneAppelDistant != '' && isset($this->ApplicationParent->IHMs[$this->NomZoneAppelDistant]))
				{
					$ihm = & $this->ApplicationParent->IHMs[$this->NomZoneAppelDistant] ;
				}
				return $ihm ;
			}
			public function DetermineEnvironnement()
			{
			}
			public function PrepareRendu()
			{
			}
			public function EstDisponible()
			{
				$this->VerifieDisponibilite() ;
				return $this->MessageIndisponible == '' ;
			}
			protected function VerifieDisponibilite()
			{
				
			}
			public function EstAccessible()
			{
				if(! $this->NecessiteMembreConnecte)
				{
					return 1 ;
				}
				return $this->ZoneParent->PossedePrivileges($this->Privileges, $this->PrivilegesStricts) ;
			}
			public function Execute()
			{
			}
			public function AccepteAppel($valeurAppel)
			{
				// $valeurInterneAppel = ($this->ValeurAppel != "") ? $this->ValeurAppel : $this->NomElementZone ;
				$valeurInterneAppel = $this->NomElementZone ;
				// echo $valeurInterneAppel." == ".$valeurAppel."<br>" ;
				return ($valeurInterneAppel == $valeurAppel) ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ValeurAppel = $this->IDInstance ;
			}
			public function AdopteZone($nom, & $zone)
			{
				$this->ZoneParent = & $zone ;
				$this->NomElementZone = $nom ;
				// print get_class($zone->ApplicationParent).' iii <br>' ;
				$this->ApplicationParent = & $this->ZoneParent->ApplicationParent ;
			}
			public function PossedeMembreConnecte()
			{
				return $this->ZoneParent->PossedeMembreConnecte() ;
			}
			public function SurScriptParDefaut()
			{
				return $this->ZoneParent->SurScriptParDefaut() ;
			}
			public function SurScriptConnecte()
			{
				return $this->ZoneParent->SurScriptConnecte() ;
			}
			public function ObtientMembreConnecte()
			{
				return $this->ZoneParent->ObtientMembreConnecte() ;
			}
			public function EstSuperAdminConnecte()
			{
				return $this->ZoneParent->EstSuperAdminConnecte() ;
			}
			public function MembreSuperAdminConnecte()
			{
				return $this->ZoneParent->MembreSuperAdminConnecte() ;
			}
			public function EditMembresPossible()
			{
				return $this->ZoneParent->EditMembresPossible() ;
			}
			public function EditMembershipPossible()
			{
				return $this->ZoneParent->EditMembershipPossible() ;
			}
			public function IdMembreConnecte()
			{
				return $this->ZoneParent->IdMembreConnecte() ;
			}
			public function LoginMembreConnecte()
			{
				return $this->ZoneParent->LoginMembreConnecte() ;
			}
			public function NomCompletMembreConnecte($inverse=0)
			{
				return $this->ZoneParent->NomCompletMembreConnecte($inverse) ;
			}
			public function AttrMembreConnecte($nomAttr)
			{
				return $this->ZoneParent->AttrMembreConnecte($nomAttr) ;
			}
			public function TitreProfilConnecte()
			{
				return $this->ZoneParent->TitreProfilConnecte() ;
			}
			public function PossedeTousPrivileges()
			{
				return $this->ZoneParent->PossedeTousPrivileges() ;
			}
			public function PossedePrivilege($nomRole, $strict=0)
			{
				return $this->ZoneParent->PossedePrivilege($nomRole, $strict) ;
			}
			public function PossedePrivileges($privileges=array(), $strict=0)
			{
				return $this->ZoneParent->PossedePrivileges($privileges, $strict) ;
			}
			public function DoitChangerMotPasse()
			{
				return $this->ZoneParent->DoitChangerMotPasse($this) ;
			}
			public function PeutChangerMotPasse()
			{
				return $this->ZoneParent->PeutChangerMotPasse() ;
			}
			public function InserePrivilege($nomPriv)
			{
				$this->InserePrivilege(array($nomPriv)) ;
			}
			public function InserePrivileges($nomPrivs)
			{
				$this->NecessiteMembreConnecte = 1 ;
				array_splice($this->Privileges, count($this->Privileges), 0, $nomPrivs) ;
			}
			public function DeclarePrivilege($nomPriv)
			{
				$this->DeclarePrivileges(array($nomPriv)) ;
			}
			public function DeclarePrivileges($nomPrivs=array())
			{
				$this->NecessiteMembreConnecte = 1 ;
				$this->Privileges = $nomPrivs ;
			}
		}
		
		class PvScriptDInclusion extends PvScriptIHMDeBase
		{
			public $CheminFichier = "" ;
			public $Variables = array() ;
			protected $ContenuVariablesModeleZone = "" ;
			protected $ContenuVariablesFormatteZone = "" ;
			protected $ContenuRenduModeleZone = "" ;
			protected $ContenuRenduFormatteZone = "" ;
			protected $ContenuRendu = "" ;
			protected $ContenuVariables = "" ;
			public function Execute()
			{
				$this->DetermineVariables() ;
				$this->AfficheRendu() ;
			}
			public function ObtientCheminFichierVariables()
			{
				return $this->ZoneParent->ObtientCheminDossierVariables(). DIRECTORY_SEPARATOR .$this->CheminFichier . ".".$this->ZoneParent->ExtensionFichierVariables ;
			}
			public function ObtientCheminFichierRendu()
			{
				return $this->ZoneParent->ObtientCheminDossierRendus(). DIRECTORY_SEPARATOR .$this->CheminFichier . ".".$this->ZoneParent->ExtensionFichierVariables ;
			}
			protected function DetermineVariables()
			{
				$this->Variables = array() ;
				$cheminFichierZone = $this->ZoneParent->ObtientCheminFichierVariables() ;
				$cheminFichierScript = $this->ObtientCheminFichierVariables() ;
				// echo 'f : '.$cheminFichierZone ;
				$this->ContenuVariablesModeleZone = content_of_file($cheminFichierZone) ;
				$this->ContenuVariables = content_of_file($cheminFichierScript) ;
				$this->ContenuVariablesFormatteZone = str_replace($this->ZoneParent->BaliseContenuScript, $this->ContenuVariables, $this->ContenuVariablesModeleZone) ;
				$scriptActuel = & $this ;
				$zoneActuelle = & $this->ZoneParent ;
				$applicationActuelle = & $this->ApplicationParent ;
				eval('?>'."\n".$this->ContenuVariablesFormatteZone.'<?php'."\n") ;
			}
			protected function AfficheRendu()
			{
				$this->Rendus = array() ;
				$cheminFichierZone = $this->ZoneParent->ObtientCheminFichierRendus() ;
				$cheminFichierScript = $this->ObtientCheminFichierRendu() ;
				$this->ContenuRendusModeleZone = content_of_file($cheminFichierZone) ;
				$this->ContenuRendu = content_of_file($cheminFichierScript) ;
				$this->ContenuRendusFormatteZone = str_replace($this->ZoneParent->BaliseContenuScript, $this->ContenuRendu, $this->ContenuRendusModeleZone) ;
				$scriptActuel = & $this ;
				$zoneActuelle = & $this->ZoneParent ;
				$applicationActuelle = & $this->ApplicationParent ;
				foreach($this->Variables as $n => $variable)
				{
					${"__".$n} = & $variable ;
				}
				eval('?>'."\n".$this->ContenuRendusFormatteZone.'<?php'."\n") ;
			}
		}
	}
	
?>