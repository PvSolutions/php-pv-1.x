<?php
	
	if(! defined('SCRIPT_NOYAU_WSM'))
	{
		if(! defined('PV_SCRIPT_COMPOSE_IHM'))
		{
			include dirname(__FILE__)."/../../Pv/IHM/Compose/Script.class.php" ;
		}
		if(! defined('NOYAU_BD_WSM'))
		{
			include dirname(__FILE__)."/BaseDonnees/Base.class.php" ;
		}
		define('SCRIPT_NOYAU_WSM', 1) ;
		
		class ScriptWebBaseWsm extends PvScriptWebCompose
		{
			public $BDWsm = null ;
			public $SystemeWsm = null ;
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				$this->BDWsm = & $this->ApplicationParent->BDWsm ;
				$this->SystemeWsm = & $this->ApplicationParent->Systeme ;
			}
			protected function DetermineVarsLocales()
			{
				$this->SystemeWsm->CalculeVarsGlobales() ;
			}
		}
		
		class ScriptPageAffichBaseWsm extends ScriptWebBaseWsm
		{
			public $PageAffich = null ;
			public $NomParamIdPageAffich = "id_page" ;
			public $ValeurParamIdPageAffich = "" ;
			public $DetecterParamIdPageAffich = 1 ;
			public $ValeurBruteParamIdPageAffich = "" ;
			public $IdPageAffichParDefaut = "0" ;
			public $SourceParams ;
			public $ModelePage ;
			public $AffichageSelect ;
			public $AutoDetecteSourceParams = 1 ;
			public $MsgPageNonTrouve = "La page que vous avez demand&eacute;e n'existe pas sur ce site web." ;
			public $ChargerListeValsExtraPageAffich = 1 ;
			public function ObtientUrlPageActive()
			{
				return $this->ObtientUrlPage($this->ValeurParamIdPageAffich) ;
			}
			public function ObtientUrlPage($idPage)
			{
				return $this->ObtientUrl().'&'.urlencode($this->NomParamIdPageAffich).'='.urlencode($idPage) ;
			}
			protected function DetectePageAffich()
			{
				$this->ValeurParamIdPageAffich = $this->IdPageAffichParDefaut ;
				if($this->DetecterParamIdPageAffich)
				{
					if($this->AutoDetecteSourceParams)
					{
						$this->SourceParams = & $_GET ;
					}
					if(isset($this->SourceParams[$this->NomParamIdPageAffich]))
					{
						$this->ValeurBruteParamIdPageAffich = $this->SourceParams[$this->NomParamIdPageAffich] ;
						$this->ValeurParamIdPageAffich = $this->SourceParams[$this->NomParamIdPageAffich] ;
					}
				}
				$this->PageAffich = $this->BDWsm->ObtientPage($this->ValeurParamIdPageAffich) ;
				if($this->PageAffich == null || ! $this->PageAffich->EstTrouve())
				{
					$this->ValeurParamIdPageAffich = 0 ;
					$this->PageAffich = $this->BDWsm->ObtientPageNonTrouve() ;
				}
				$this->PageAffich->DetecteModele() ;
			}
			public function RenduPageNonTrouve()
			{
				return $this->MsgPageNonTrouve ;
			}
			protected function DeterminePageAffich()
			{
				$this->DetectePageAffich() ;
				// print 'Ml : '.$this->PageAffich->NomModele ;
				if(! $this->PageAffich->EstTrouve() || $this->PageAffich->Modele->AffichImpossible == 1)
				{
					return ;
				}
				$this->DetermineVarsLocales() ;
				$this->PageAffich->DetermineVarsLocales() ;
				/*
				if($this->ChargerListeValsExtraPageAffich)
				{
					$this->PageAffich->DetecteListeValeursExtra() ;
				}
				*/
				$this->PageAffich->Modele->ChargeConfig() ;
				$this->PageAffich->Modele->DetecteAffichageSelect() ;
				$this->ModelePage = & $this->PageAffich->Modele ;
				$this->AffichageSelect = null ;
				if($this->PageAffich->Modele->EstPasNul($this->PageAffich->Modele->AffichageSelect))
				{
					$this->AffichageSelect = & $this->PageAffich->Modele->AffichageSelect ;
					if($this->AffichageSelect->UtiliserRenduComposants)
					{
						$this->AffichageSelect->RemplitScript($this) ;
					}
					else
					{
						$this->AffichageSelect->AdopteScript('affichageSelect', $this) ;
						$this->AffichageSelect->ChargeConfig() ;
						$this->AffichageSelect->ChargeConfigComps() ;
					}
				}
			}
			public function DetermineDocument()
			{
				$this->DeterminePageAffich() ;
				parent::DetermineDocument() ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineComposants() ;
			}
			protected function DetermineComposants()
			{
			}
			public function RenduDispositif()
			{
				$ctn = '' ;
				if(! $this->PageAffich->EstTrouve() || $this->PageAffich->Modele->AffichImpossible == 1)
				{
					$ctn .= $this->RenduPageNonTrouve() ;
				}
				else
				{
					if(! $this->AffichageSelect->UtiliserRenduComposants)
					{
						$ctn .= $this->AffichageSelect->RenduDispositif() ;
					}
					else
					{
						$ctn .= parent::RenduDispositif() ;
					}
				}
				return $ctn ;
			}
		}
		
	}
	
?>