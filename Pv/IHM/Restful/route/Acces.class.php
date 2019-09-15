<?php
	
	if(! defined('PV_ROUTE_ACCES_RESTFUL'))
	{
		define('PV_ROUTE_ACCES_RESTFUL', 1) ;
		
		class PvRouteConnexionRestful extends PvObjet
		{
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public function EstAccessible()
			{
				$ok = ($this->PossedeMembreConnecte() == 0) ;
				if(! $ok)
				{
					return $ok ;
				}
			}
			public function AdopteApi($nom, & $api)
			{
				$this->NomElementApi = $nom ;
				$this->ApiParent = & $api ;
			}
			public function Execute()
			{
				$this->ParamIdDevice = _POST_def("id_device") ;
				$this->ParamDescDevice = _POST_def("description_device") ;
			}
		}
	}
	
?>