<?php
	
	if(! defined('PV_NOYAU_PASSERELLE_SITE_WEB'))
	{
		define('PV_NOYAU_PASSERELLE_SITE_WEB', 1) ;
		
		class PvPasserelleSiteWebBase extends PvIHM
		{
			protected function UrlRacine()
			{
				$url = remove_url_params(get_current_url()) ;
				if($this->ApplicationParent->NomElementActif == $this->NomElementApplication)
				{
					return $url ;
				}
				$url = ((isset($_SERVER["HTTPS"])) ? 'https' : 'http').'://'.$_SERVER["SERVER_NAME"].(($_SERVER["SERVER_PORT"] != '') ? ':'.$_SERVER["SERVER_PORT"] : '').'/'.$this->CheminFichierRelatif ;
				return $url ;
			}
		}
	}

?>