<?php
	
	if(! defined('PV_ACTION_PLATF_SVC_WEB'))
	{
		define('PV_ACTION_PLATF_SVC_WEB', 1) ;
		
		class PvResultActPlatfSvcWeb
		{
			public $contenu ;
			public $erreur ;
			public function __construct()
			{
				$this->erreur = new PvErreurActPlatfSvcWeb() ;
			}
			public function ConfirmeSucces($contenu)
			{
				$this->erreur->niveau = "" ;
				$this->erreur->code = 0 ;
				$this->erreur->message = "" ;
				$this->erreur->alias = "" ;
				$this->erreur->params = array() ;
				$this->contenu = $contenu ;
			}
			public function ConfirmeErreur($code, $message="", $alias="", $params=array())
			{
				$this->erreur->niveau = "erreur" ;
				$this->erreur->code = $code ;
				$this->erreur->message = ($message != "") ? $message : "Une erreur est survenue"  ;
				$this->erreur->alias = $alias ;
				$this->erreur->params = $params ;
				$this->contenu = null ;
			}
			public function ConfirmeException($code, $message="", $alias="", $params=array())
			{
				$this->ConfirmeErreur($code, $message, $alias, $params) ;
				$this->erreur->niveau = "exception" ;
			}
		}
		class PvErreurActPlatfSvcWeb
		{
			public $niveau = "erreur" ;
			public $code = -1 ;
			public $message = "non initialise" ;
			public $alias ;
			public $params = array() ;
		}
		
		class PvActBasePlatfSvcWeb extends PvActionResultatJSONZoneWeb
		{
			protected function CreeResultat()
			{
				return new PvResultActPlatfSvcWeb() ;
			}
			protected function ConstruitResultSpec()
			{
			}
			public function ConstruitResultat()
			{
				$this->Resultat = $this->CreeResultat() ;
				$this->ConstruitResultSpec() ;
			}
		}
	}
	
?>