<?php
	
	if(! defined('PV_ZONE_COMPOSE_IHM'))
	{
		if(! defined('PV_ZONE_IHM'))
		{
			include dirname(__FILE__)."/../Simple/Zone.class.php" ;
		}
		if(! defined('PV_SCRIPT_COMPOSE_IHM'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		define('PV_ZONE_COMPOSE_IHM', 1) ;
		
		class PvZoneWebCompose extends PvZoneWebSimple
		{
			public $CompEnteteDocument = null ;
			public $CompsCorpsDocument = array() ;
			public $CompPiedDocument = null ;
			public $NomClasseCompEnteteDocument = "PvEnteteDocumentHtml5" ;
			public $NomClasseCompPiedDocument = "PvPiedDocumentHtml5" ;
			public $NomClasseCompPrincCorpsDocument = "PvComposantsScriptPourRendu" ;
			public $UtiliserComposantsRendu = 1 ;
			public $EncadrerDocument = 1 ;
			public function InscritCompEnteteDocument(& $comp)
			{
				$this->CompEnteteDocument = & $comp ;
				$this->CompEnteteDocument->AdopteZone("enteteDocument", $this) ;
			}
			public function InscritCompPiedDocument(& $comp)
			{
				$this->CompPiedDocument = & $comp ;
				$this->CompPiedDocument->AdopteZone("piedDocument", $this) ;
			}
			public function InscritCompCorpsDocument($nom, & $comp)
			{
				// echo get_class($comp).' kkk<br/>' ;
				$this->CompsCorpsDocument[$nom] = & $comp ;
				$comp->AdopteZone($nom, $this) ;
			}
			public function RenduDocument()
			{
				$ctn = '' ;
				// print_r(array_keys($this->CompsCorpsDocument)) ;
				$ctn .= $this->RenduEnteteDocument().PHP_EOL ;
				$ctn .= $this->RenduCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduPiedDocument().PHP_EOL ;
				return $ctn ;
			}
			protected function RenduEnteteDocument()
			{
				$ctn = '' ;
				if(! $this->EncadrerDocument)
					return $ctn ;
				if(! $this->UtiliserComposantsRendu)
				{
					$ctn .= parent::RenduEnteteDocument() ;
				}
				elseif($this->EstPasNul($this->CompEnteteDocument))
				{
					$ctn .= $this->RenduComposant($this->CompEnteteDocument) ;
				}
				return $ctn ;
			}
			protected function RenduPiedDocument()
			{
				$ctn = '' ;
				if(! $this->EncadrerDocument)
					return $ctn ;
				if(! $this->UtiliserComposantsRendu)
				{
					$ctn .= parent::RenduPiedDocument() ;
				}
				elseif($this->EstPasNul($this->CompPiedDocument))
				{
					$ctn .= $this->RenduComposant($this->CompPiedDocument) ;
				}
				return $ctn ;
			}
			protected function RenduCorpsDocument()
			{
				$ctn = '' ;
				if(! $this->UtiliserComposantsRendu)
				{
					$ctn .= parent::RenduCorpsDocument() ;
				}
				else
				{
					$ctn .= $this->RenduComposants($this->CompsCorpsDocument) ;
				}
				return $ctn ;
			}
			protected function RenduComposant($comp)
			{
				return $this->RenduComposants(array($comp)) ;
			}
			protected function RenduComposants($comps=array())
			{
				$ctn = '' ;
				$nomComps = array_keys($comps) ;
				foreach($nomComps as $i => $nomComp)
				{
					$comps[$nomComp]->ChargeConfig() ;
					$ctn .= $comps[$nomComp]->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeComposantsDocument() ;
			}
			protected function ChargeComposantsDocument()
			{
				$this->ChargeCompEnteteDocument() ;
				$this->ChargeCompsCorpsDocument() ;
				$this->ChargeCompPiedDocument() ;
			}
			protected function ChargeCompEnteteDocument()
			{
				$nomClasse = $this->NomClasseCompEnteteDocument ;
				if(class_exists($nomClasse))
				{
					$this->InscritCompEnteteDocument(new $nomClasse()) ;
				}
			}
			protected function ChargeCompsCorpsDocument()
			{
				$nomClasse = $this->NomClasseCompPrincCorpsDocument ;
				if(class_exists($nomClasse))
				{
					$this->InscritCompCorpsDocument("corpsPrincipal", new $nomClasse()) ;
				}
				$this->ChargeAutresCompsCorpsDocument() ;
			}
			protected function ChargeAutresCompsCorpsDocument()
			{
			}
			protected function ChargeCompPiedDocument()
			{
				$nomClasse = $this->NomClasseCompPiedDocument ;
				if(class_exists($nomClasse))
				{
					$this->InscritCompPiedDocument(new $nomClasse()) ;
				}
			}
		}
		
	}
	
?>