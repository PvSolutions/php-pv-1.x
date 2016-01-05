<?php

	if(! defined("COMMON_HTTP_BROWSER"))
	{
		define('COMMON_HTTP_BROWSER', 1) ;
		
		class CommonHttpBrowser
		{
		}
		
		class CommonURI
		{
			protected $Scheme ;
			protected $Host ;
			protected $Port ;
			protected $User ;
			protected $Pass ;
			protected $Path ;
			protected $Query ;
			protected $QueryData = array() ;
			protected $Fragment ;
			public function Parse($url)
			{
				$urlParts = parse_url($url) ;
				$this->Scheme = (isset($urlParts["scheme"])) ? $urlParts["scheme"] : '' ;
				$this->Host = (isset($urlParts["host"])) ? $urlParts["host"] : '' ;
				$this->Port = (isset($urlParts["port"])) ? $urlParts["port"] : '' ;
				$this->User = (isset($urlParts["user"])) ? $urlParts["user"] : '' ;
				$this->Pass = (isset($urlParts["pass"])) ? $urlParts["pass"] : '' ;
				$this->Path = (isset($urlParts["path"])) ? $urlParts["path"] : '' ;
				$this->Query = (isset($urlParts["query"])) ? $urlParts["query"] : '' ;
				$this->Fragment = (isset($urlParts["fragment"])) ? $urlParts["fragment"] : '' ;
				$this->QueryData = array() ;
			}
			public function GetScheme()
			{
				return $this->Scheme ;
			}
			public function GetHost()
			{
				return $this->Host ;
			}
			public function GetPort()
			{
				return $this->Port ;
			}
			public function GetUser()
			{
				return $this->User ;
			}
			public function GetPass()
			{
				return $this->Pass ;
			}
			public function GetPath()
			{
				return $this->Path ;
			}
			public function GetQuery()
			{
				return CommonURI::ToQueryString($this->QueryData) ;
			}
			public static function ToQueryString($data)
			{
				$result = '' ;
				if(function_exists('http_build_query'))
				{
					$result = http_build_query($data) ;
				}
				else
				{
					foreach($data as $name => $val)
					{
						if($result != '')
							$result .= '&' ;
						$result .= urlencode($name).'='.urlencode($val) ;
					}
				}
				return $result ;
			}
			public function GetFragment()
			{
				return $this->Fragment ;
			}
		}
		class CommonHttpMessage
		{
			protected $Headers = array() ;
			protected $Body ;
			public function SetContentType($newContentType)
			{
			}
			public function ClearHeaders()
			{
				$this->Headers = array() ;
			}
			public function & AddHeaderByValue($key, $value=null)
			{
				$header = new CommonHttpHeader() ;
				$header->SetKey($key) ;
				$header->SetValue($value) ;
				return $this->InsertHeader($header) ;
			}
			public function GetHeaderValue($key)
			{
				
			}
			public function InsertHeader($header)
			{
				$this->AddHeader($header) ;
				return $header ;
			}
			public function AddHeader(& $header)
			{
				$index = $this->GetHeaderIndex($header->GetKey()) ;
				if($index > -1)
					$this->Headers[$index] = $header ;
				else
					$this->Headers[] = $header ;
				return $header ;
			}
			public function GetHeaderIndex($key)
			{
				$index = -1 ;
				if($key == null)
					return $index ;
				foreach($this->Headers as $i => $header)
				{
					if(strtolower($header->GetKey()) == strtolower($key))
					{
						$index = $i ;
						break ;
					}
				}
				return $index ;
			}
			public function & GetHeader($key)
			{
				$header = new CommonHttpEmptyHeader() ;
				$index = $this->Headers->GetHeaderIndex($key) ;
				if($index == -1)
					return $header ;
				return $this->Headers[$index] ;
			}
			protected function GetBody()
			{
			}
			public function WriteToStream(& $stream)
			{
				
			}
			public function ReadFromStream(& $stream)
			{
				
			}
		}
		class CommonHttpRequestBase extends CommonHttpMessage
		{
			protected $Method ;
			public function GetMethod()
			{
			}
		}
		class CommonUrlRequest extends CommonHttpRequestBase
		{
		}
		class CommonHttpGetRequest extends CommonHttpRequestBase
		{
		}
		class CommonHttpPostRequest extends CommonHttpRequestBase
		{
		}
		
		class CommonHttpResponse extends CommonHttpMessage
		{
		}
		
		class CommonHttpHeader
		{
			public $IncludeEmptyValue = 1 ;
			protected $Key ;
			protected $Value ;
			public function GetKey()
			{
				return $this->Key ;
			}
			public function GetValue()
			{
				return $this->Value ;
			}
			public function SetKey($key)
			{
				$this->Key = $key ;
			}
			public function SetValue($value)
			{
				$this->Value = $value ;
			}
			public function IsNull()
			{
				return $this->Key != null ;
			}
			public function ToHttpString()
			{
				$ctn = $this->Key ;
				if($this->IncludeEmptyValue && empty($this->Value))
					return $ctn ;
				$ctn .= ':'.$this->Value ;
				return $ctn ;
			}
		}
		class CommonHttpEmptyHeader extends CommonHttpHeader
		{
		}
	}

?>