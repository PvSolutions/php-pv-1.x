<?php
	
	if(! defined('PV_NOYAU_PASSERELLE_SITE_WEB'))
	{
		define('PV_NOYAU_PASSERELLE_SITE_WEB', 1) ;
		
		class PvPasserelleSiteWebBase extends PvIHM
		{
			public function ObtientUrl()
			{
				return $this->UrlRacine() ;
			}
			protected function UrlRacine()
			{
				if($this->ApplicationParent->EnModeConsole())
				{
					if($this->ApplicationParent->UrlRacine != '')
					{
						return $this->ApplicationParent->UrlRacine."/".$this->CheminFichierRelatif ;
					}
					elseif($this->ApplicationParent->NomElementActif == $this->NomElementApplication)
					{
						return $_SERVER["argv"][0] ;
					}
					else
					{
						return "" ;
					}
				}
				$url = remove_url_params(get_current_url()) ;
				if($this->ApplicationParent->NomElementActif == $this->NomElementApplication)
				{
					return $url ;
				}
				$url = ((isset($_SERVER["HTTPS"])) ? 'https' : 'http').'://'.$_SERVER["SERVER_NAME"].(($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? ':'.$_SERVER["SERVER_PORT"] : '').'/'.$this->CheminFichierRelatif ;
				return $url ;
			}
		}
	}

?>