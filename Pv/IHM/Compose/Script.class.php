<?php
	
	if(! defined('PV_SCRIPT_COMPOSE_IHM'))
	{
		if(! defined('PV_SCRIPT_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Simple/Script.class.php" ;
		}
		define('PV_SCRIPT_COMPOSE_IHM', 1) ;
		
		class PvScriptWebCompose extends PvScriptWebSimple
		{
			public $ComposantsRendu = array() ;
			public $CompsCorpsDocument = array() ;
			public $NomClasseCompPrinc = "" ;
			public $ComposantPrincipal = null ;
			public $EcraserCadreDocumentZone = 1 ;
			public $EcraserCorpsDocumentZone = 1 ;
			public $NomClasseCompEnteteDocument = "" ;
			public $NomClasseCompPiedDocument = "" ;
			public $CompEnteteDocument ;
			public $CompPiedDocument ;
			public function InscritComposantRendu($nom, & $comp)
			{
				if(empty($nom))
				{
					$nom = uniqid("comp_") ;
				}
				$this->ComposantsRendu[$nom] = & $comp ;
				$comp->AdopteScript($nom, $this) ;
			}
			public function InscritCompCorpsDocument($nom, & $comp)
			{
				$this->CompsCorpsDocument[$nom] = & $comp ;
				$comp->AdopteScript($nom, $this) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeComposants() ;
			}
			protected function ChargeComposants()
			{
				$this->ChargeComposantPrincipal() ;
				$this->ChargeAutresComposants() ;
			}
			protected function ChargeComposantPrincipal()
			{
				$nomClasse = $this->NomClasseCompPrinc ;
				if(class_exists($nomClasse))
				{
					$this->ComposantPrincipal = new $nomClasse() ;
				}
				if($this->EstPasNul($this->ComposantPrincipal))
				{
					$this->InscritComposantRendu("principal", $this->ComposantPrincipal) ;
				}
			}
			protected function ChargeAutresComposants()
			{
			}
			protected function RenduDispositifBrut()
			{
				$nomComps = array_keys($this->ComposantsRendu) ;
				$ctn = '' ;
				foreach($nomComps as $i => $nomComp)
				{
					$comp = & $this->ComposantsRendu[$nomComp] ;
					if($comp->Visible == 0)
					{
						continue ;
					}
					$comp->ChargeConfig() ;
					$ctn .= $comp->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineDocument() ;
			}
			protected function DetermineDocument()
			{
				$zone = & $this->ZoneParent ;
				if($this->EcraserCadreDocumentZone == 1)
				{
					if($this->NomClasseCompEnteteDocument != "")
					{
						$nomClasse = $this->NomClasseCompEnteteDocument ;
						$this->CompEnteteDocument = new $nomClasse() ;
					}
					if($this->EstPasNul($this->CompEnteteDocument))
					{
						$zone->InscritCompEnteteDocument() ;
					}
					if($this->NomClasseCompPiedDocument != "")
					{
						$nomClasse = $this->NomClasseCompPiedDocument ;
						$zone->CompPiedDocument = new $nomClasse() ;
					}
					if($this->EstPasNul($this->CompPiedDocument))
					{
						$zone->InscritCompPiedDocument($this->CompPiedDocument) ;
					}
				}
				if($this->EcraserCorpsDocumentZone == 1)
				{
					if(count($this->CompsCorpsDocument) > 0)
					{
						$nomComps = array_keys($this->CompsCorpsDocument) ;
						$zone->CompsCorpsDocument = array() ;
						foreach($nomComps as $i => $nomComp)
						{
							$comp = $this->CompsCorpsDocument[$nomComp] ;
							$zone->InscritCompCorpsDocument(
								$this->NomElementZone.'_'.$comp->IDInstanceCalc,
								$this->CompsCorpsDocument[$nomComp]
							) ;
						}
						// print count($this->CompsCorpsDocument) ;
					}
				}
			}
		}
		
	}
	
?>