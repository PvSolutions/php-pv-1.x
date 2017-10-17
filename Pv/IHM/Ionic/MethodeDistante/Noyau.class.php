<?php
	
	if(! defined('PV_METHODE_DISTANTE_NOYAU_IONIC'))
	{
		define('PV_METHODE_DISTANTE_NOYAU_IONIC', 1) ;
		
		class PvMethodeDistanteNoyauIonic extends PvMethodeDistanteBase
		{
		}
		
		class PvMtdDistNonTrouveeIonic extends PvMtdDistNonTrouvee
		{
		}
		
		class PvMtdDistFormDonneesIonic extends PvMtdDistFormDonnees
		{
			public function PrepareExecution()
			{
				$this->ComposantIUParent->LieTousLesFiltres() ;
			}
		}
		class PvMtdDistTablDonneesIonic extends PvMtdDistTablDonnees
		{
			public function PrepareExecution()
			{
				$this->ComposantIUParent->LieTousLesFiltres() ;
			}
		}
	}
	
?>