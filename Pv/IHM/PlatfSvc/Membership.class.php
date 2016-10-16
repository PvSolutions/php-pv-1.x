<?php
	
	if(! defined('PV_MEMBERSHIP_PLATF_SVC_WEB'))
	{
		define('PV_MEMBERSHIP_PLATF_SVC_WEB', 1) ;
		
		class PvMembershipPlatfSvcWeb extends AkSqlMembership
		{
			public $SessionMemberKey = "idMembreConnecte" ;
			public $UpdateTimeKey = "dateMajMembreConnecte" ;
			public $SessionSource = "GET" ;
			protected function InitConfig(& $parentArea)
			{
				parent::InitConfig($parentArea) ;
				$this->Database = $this->CreeBaseDonnees() ;
			}
			protected function CreeBaseDonnees()
			{
				return null ;
			}
			public function GetSessionValue($key, $defaultValue=false)
			{
				$value = parent::GetSessionValue($key, $defaultValue) ;
				if($this->SessionSource == "GET")
				{
					$value = (isset($_GET[$key])) ? $_GET[$key] : $defaultValue ;
				}
				return $value ;
			}
			public function SetSessionValue($key, $value="")
			{
				parent::SetSessionValue($key, $value) ;
			}
		}
		
	}

?>