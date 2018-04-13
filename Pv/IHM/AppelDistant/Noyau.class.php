<?php
	
	if(! defined('PV_NOYAU_APPEL_DISTANT'))
	{
		define('PV_NOYAU_APPEL_DISTANT', 1) ;
		
		class PvElemZoneAppelDistant extends PvObjet
		{
			public $NomElementZone ;
			public $ZoneParent ;
			public function AdopteZone($nom, & $zone)
			{
				$this->NomElementZone = $nom ;
				$this->ZoneParent = & $zone ;
				// print get_class($this)." : ".get_class($this->ZoneParent)." kkk<br>" ;
			}
			public function & ApplicationParent()
			{
				return $this->ZoneParent->ApplicationParent ;
			}
		}
	}
	
?>