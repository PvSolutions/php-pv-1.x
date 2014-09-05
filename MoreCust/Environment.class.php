<?php
	
	if(! defined('ENVIRONMENT_MORECUST'))
	{
		if(! defined('CORE_MORECUST'))
		{
			include dirname(__FILE__)."/Core.class.php" ;
		}
		define('ENVIRONMENT_MORECUST', 1) ;
		
		class EnvVisitorBaseMoreCust extends EnvBaseMoreCust
		{
		}
		class EnvPagesViewedByVisitor extends EnvVisitorBaseMoreCust
		{
			public $PagesKeyName = "pages" ;
			public $PagesData = array() ;
			public $Min = 15 ;
			protected function _IsAvailable()
			{
				if(php_sapi_name() == 'cli')
					return 0 ;
				$this->DetectPagesData() ;
				$this->RegisterNewPage() ;
				$this->StorePagesData() ;
				$ok = 0 ;
				if($this->Min >= count($this->PagesData))
				{
					$ok = 1 ;
				}
				return $ok ;
			}
			protected function StorePagesData()
			{
				$pageDataStr = "" ;
				foreach($this->PagesData as $name => $val)
				{
					if($pageDataStr != "")
					{
						$pageDataStr .= "&" ;
					}
					$pageDataStr .= urlencode($name).'='.urlencode($val) ;
				}
				$_SESSION[$this->PagesKeyName] = $pageDataStr ;
			}
			protected function DetectPagesData()
			{
				if(! isset($_SESSION[$this->PagesKeyName]))
					return array() ;
				$pagesDataStr = $_SESSION[$this->PagesKeyName] ;
				@parse_str($pagesDataStr, $this->PagesData) ;
				if(! is_array($this->PagesData))
				{
					$this->PagesData = array() ;
				}
			}
			protected function RegisterNewPage()
			{
				$url = $_SERVER["REQUEST_URI"] ;
				$key = base64_encode($url) ;
				if(isset($this->PagesData[$key]))
				{
					return ;
				}
				$this->PagesData[$key] = 1 ;
			}
		}
		
	}
	
?>