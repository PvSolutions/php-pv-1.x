<?php
	
	if(! defined('PV_SCRIPT_WEB_IONIC'))
	{
		define('PV_SCRIPT_WEB_IONIC', 1) ;
		
		class PvScriptWebBaseIonic extends PvScriptWebSimple
		{
			public $NomZoneIonic ;
			public function & ZoneIonic()
			{
				$ihm = new PvIHMBaseIonic() ;
				if($this->NomZoneIonic != '' && isset($this->ApplicationParent->IHMs[$this->NomZoneIonic]))
				{
					$ihm = & $this->ApplicationParent->IHMs[$this->NomZoneIonic] ;
				}
				return $ihm ;
			}
		}
		
		class PvScriptTrcListeAppelRecuIonic extends PvScriptWebBaseIonic
		{
			protected $TablPrinc ;
			public function DetermineEnvironnement()
			{
				$zone = $this->ZoneIonic() ;
				$this->TablPrinc = $zone->RemplitTablAppelRecu("tablPrinc", $this) ;
			}
			public function RenduSpecifique()
			{
				return $this->TablPrinc->RenduDispositif() ;
			}
		}
	}
	
?>