<?php
	
	if(! defined('MODULE_COMPTEUR_HITS_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__).'/Noyau.class.php' ;
		}
		define('MODULE_COMPTEUR_HITS_SWS', 1) ;
		
		class ModuleCompteurHitsSws extends ModulePageBaseSws
		{
			public $NomRef = "compteur_hits" ;
			public $TitreMenu = "Compteur des clics" ;
			public $IdPageWeb = "" ;
			public function ObtientUrlPageWeb(& $zone)
			{
				$url = $zone->ObtientUrl() ;
				if(preg_match("/\/$/", $url))
				{
					$url .= "index.php" ;
				}
				$params = $_GET ;
				arsort($params) ;
				$url .= "?".http_build_query_string($params) ;
				return $url ;
				// return base64_encode($url) ;
			}			
			public function SauveVisiteActuelle(& $zone)
			{
				$url = $this->ObtientUrlPageWeb($zone) ;
				$userAgent = $_SERVEUR["USER_AGENT"] ;
				$sessionId = session_id() ;
			}
		}
	}
?>