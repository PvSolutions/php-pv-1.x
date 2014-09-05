<?php
	
	if(! defined('COMMON_HTTP_SESSION_INCLUDED'))
	{
		define('COMMON_HTTP_SESSION_INCLUDED', 1) ;
		
		if(! function_exists('url_to_absolute'))
		{
			include dirname(__FILE__)."/url_to_absolute/url_to_absolute.php" ;
		}
		
		class HttpSession
		{
			var $UseBuffer = true;
			var $AutoSetContentType = true;
			var $FollowRedirection = true;
			var $MaxRedirection = 10;
			
			var $UserAgent = 'PHP Http Session v1.0' ;
			
			var $RequestDefaultHeaders = array(
				"Host" => "localhost",
				"Content-Type" => "",
				"Content-Length" => "",
				"Accept" => "text/*",
			) ;
			
			var $LocalUrl = false ;
			
			var $RequestData = "" ;
			var $RequestUrl = "" ;
			var $RequestOutput = false ;
			var $RequestGetData = array() ;
			var $RequestPostData = array() ;
			var $RequestPostDataType = "array" ;
			var $RequestHost = "" ;
			var $RequestPort = "" ;
			var $RequestFilesData = array() ;
			var $RequestQueryRawEncoding = "1" ;
			var $RequestHeaders = array() ;
			var $RequestHeaderData = array() ;
			var $RequestContentType = "" ;
			var $RequestCharset = "utf-8" ;
			var $RequestBoundary = "" ;
			var $RequestMethod = "GET" ;
			var $RequestContentLength = 0 ;
			var $RequestCookies = array() ;
			var $RequestUrlParts = array() ;
			var $RequestFileUploadActivated = false;
			
			var $ConnectTimeout = 30 ;
			var $DownloadTimeout = 30 ;
			
			var $RequestException = "" ;
			
			var $DownloadResponse = 1 ;
			var $ResponseHeadersData = "";
			var $ResponseData = "" ;
			var $ResponseFileName = "" ;
			var $ResponseOutputSet = false ;
			var $ResponseOutput = false ;
			var $ResponseParseBodyEnabled = true ;
			var $ResponseRedirectionUrl = "" ;
			var $ResponseHeaders = array() ;
			var $ResponseCookies = array() ;
			var $ResponseHeadersSet = false;
			var $ResponseHttpVersion = false;
			var $ResponseHttpStatusCode = false;
			var $ResponseHttpStatusDesc = false;
			
			function ResetRequest()
			{
				$this->RequestData = "" ;
				$this->RequestUrl = "" ;
				$this->RequestException = "" ;
				$this->RequestOutput = false ;
				$this->RequestGetData = array() ;
				$this->RequestPostData = array() ;
				$this->RequestPostDataType = "array" ;
				$this->RequestHost = "" ;
				$this->RequestFilesData = array() ;
				$this->RequestHeaders = $this->RequestDefaultHeaders ;
				$this->RequestHeaders["User-Agent"] = $this->UserAgent ;
				if($this->RequestContentType == "")
				{
					$this->RequestContentType = "text/html" ;
				}
				$this->RequestContentLength = 0 ;
				$this->ResponseOutput = false ;
				$this->ResponseOutputSet = false ;
				$this->ResponseData = "" ;
				$this->ResponseHeadersData = "" ;
				$this->ResponseParseBodyEnabled = true ;
				$this->ResponseRedirectionUrl = "" ;
			}
			function & RequestUrlEncodeSubName()
			{
				$subName = "urlencode" ;
				if($this->RequestQueryRawEncoding)
				{
					$subName = "rawurlencode" ;
				}
				return $subName;
			}
			function CallRequestUrlEncode($value)
			{
				return call_user_func_array($this->RequestUrlEncodeSubName(), array($value));
			}
			function ExtractMimeContentType($filePath, $defaultMimeType="Application/Octet-stream")
			{
				$mimeType = "" ;
				if(function_exists('finfo_open'))
				{
					try
					{
						$fInfo = finfo_open(FILEINFO_MIME_TYPE);
						if($fInfo)
						{
							$mimeType = finfo_file($finfo, $filename) ;
							finfo_close($fInfo) ;
						}
					}
					catch(Exception $ex)
					{
					}
				}
				else
				{
					if(function_exists("mime_content_type"))
					{
						$mimeType = @mime_content_type($filename) ;
					}
				}
				if(empty($mymeType))
				{
					$mimeType = $defaultMimeType;
				}
				return $mimeType;
			}
			function SetRequestException($ex)
			{
				$msg = "" ;
				if(is_object($ex))
				{
					$msg = $ex->getMessage() ;
				}
				else
				{
					$msg = $ex ;
				}
				$this->RequestException = $msg ;
			}
			function DetectLocalUrl()
			{
				if($this->LocalUrl !== false)
				{
					return ;
				}
				$this->LocalUrl = $this->GetLocalUrl() ;
			}
			function GetLocalUrl()
			{
				$url = "" ;
				if(isset($_SERVER["SERVER_NAME"]))
				{
					$https = (isset($_SERVER["HTTPS"])) ? $_SERVER["HTTPS"] : false ;
					$url = (($https) ? "https" : "http")."://".$_SERVER['SERVER_NAME'] ;
					if($_SERVER['SERVER_PORT'] != '80')
					{
						$url .= ':'.$_SERVER['SERVER_PORT'] ;
					}
					$url .= $_SERVER['REQUEST_URI'] ;
				}
				return $url ;
			}
			function DetectUrlParts($url)
			{
				$this->DetectLocalUrl() ;
				$ok = 0 ;
				$realUrl = url_to_absolute($this->LocalUrl, $url) ;
				if($realUrl === false)
				{
					$realUrl = $url ;
				}
				$this->RequestUrl = $realUrl ;
				try
				{
					$this->RequestUrlParts = parse_url($realUrl) ;
					$ok = 1 ;
				}
				catch(Exception $ex)
				{
					$this->SetRequestException($ex) ;
					$ok = 0 ;
				}
				if($ok)
				{
					if(! isset($this->RequestUrlParts["scheme"]))
					{
						$this->RequestUrlParts["scheme"] = "http" ;
					}
					if(! isset($this->RequestUrlParts["port"]))
					{
						$this->RequestUrlParts["port"] = ($this->RequestUrlParts["scheme"] == "https") ? 443 : 80 ;
					}
					if(! isset($this->RequestUrlParts["path"]))
					{
						$this->RequestUrlParts["path"] = "/" ;
					}
					if(! isset($this->RequestUrlParts["query"]))
					{
						$this->RequestUrlParts["query"] = "" ;
					}
				}
				if($realUrl !== false)
				{
					// $this->RequestUrlParts["query"] = urldecode($this->RequestUrlParts["query"]) ;
				}
				return $ok ;
			}
			function GetUrl($url, $headers=array(), $getData="", $postData="")
			{
				$this->ResetRequest() ;
				// Url and PostData has been set only
				$argCount = func_num_args() ;
				switch($argCount)
				{
					case 1 :
					{
						$headers = array() ;
						$postData = array() ;
						$getData = array() ;
					}
					break;
					case 2 :
					{
						$postData = $headers ;
						$headers = array() ;
						$getData = array() ;
					}
					break;
					case 3 :
					{
						$postData = $getData ;
						$getData = array() ;
					}
					break ;
				}
				// Detect the url parts
				if(! $this->DetectUrlParts($url))
				{
					return ;
				}
				// Parse string getData
				if(is_string($getData))
				{
					if($getData != "")
					{
						$realGetData = "" ;
						try
						{
							parse_str($getData, $realGetData);
							$getData = $realGetData;
						}
						catch(Exception $ex)
						{
						}
					}
				}
				if(is_array($getData))
				{
					foreach($getData as $name => $value)
					{
						if($this->RequestUrlParts["query"] != "")
						{
							$this->RequestUrlParts["query"] .= "&" ;
						}
						$this->RequestUrlParts["query"] .= urlencode($name)."=".urlencode($value) ;
					}
				}
				
				// Set the current post data
				if(is_array($postData))
				{
					foreach($postData as $name => $value)
					{
						if(is_array($value))
						{
							if(isset($value["filename"]))
							{
								$value["name"] = $name; 
								$this->RequestFilesData[] = $value ;
							}
							else
							{
								$this->RequestPostData[] = array($name, join(",", array_map("urlencode", $value))) ;
							}
						}
						else
						{
							$this->RequestPostData[] = array($name, urlencode($value)) ;
						}
					}
				}
				else
				{
					$this->RequestPostData = array($postData) ;
					$this->RequestPostDataType = "string";
				}
				
				$this->DetectHeadersRequest() ;
				
				// Creating the request
				if(! $this->OpenRequest())
				{
					return null ;
				}
				$this->WriteHeadersRequest($headers) ;
				// Adding the post data
				if($this->RequestMethod == "POST")
				{
					// Case of the string
					if($this->RequestPostDataType == "string")
					{
						fputs($this->RequestOutput, $this->RequestPostData[0]) ;
					}
					// Case of the table of parameters
					else
					{
						$requestEntryCount = count($this->RequestPostData) ;
						$requestPostData = "" ;
						for($i=0; $i <$requestEntryCount; $i++)
						{
							$n = $this->RequestPostData[$i][0] ;
							$v = $this->RequestPostData[$i][1] ;
							if($this->RequestFileUploadActivated)
							{
								$requestEntry = "--".$this->RequestBoundary."\r\nContent-Disposition: form-data; name=\"".$this->CallRequestUrlEncode($n)."\"\r\n\r\n".$this->CallRequestUrlEncode($v)."\r\n--".$this->RequestBoundary."\r\n" ;
								fputs($this->RequestOutput, $requestEntry);
							}
							else
							{
								$requestPart = (($i > 0) ? "&" : "").$this->CallRequestUrlEncode($n)."=".$this->CallRequestUrlEncode($v) ;
								// echo $requestPart."\n" ;
								fputs($this->RequestOutput, $requestPart) ;
							}
						}
						// Adding the file contents
						if($this->RequestFileUploadActivated)
						{
							$requestEntryCount = count($this->RequestFilesData) ;
							for($i=0; $i<$requestEntryCount; $i++)
							{
								$fileData = &$this->RequestFilesData[$i] ;
								if(! isset($fileData["filename"]))
								{
									continue ;
								}
								fputs($this->RequestOutput, "--".$this->RequestBoundary."\r\n");
								$disposition = 'Content-Disposition: form-data; name="'.$fileData["name"].'"';
								if (isset($fileData['filename'])) {
									$disposition .= '; filename="'.$fileData['filename'].'"';
								}
								fputs($this->RequestOutput, $disposition."\r\n");
								fputs($this->RequestOutput, 'Content-Type: '.$fileData['type']."\r\n");
								if (isset($fileData['binary']) && $fileData['binary']) {
									fputs($this->RequestOutput, 'Content-Transfer-Encoding: binary'."\r\n");
								}
								fputs($this->RequestOutput, "\r\n");
								if(file_exists($fileData["filename"]))
								{
									$fh = fopen($fileData["filename"], "r") ;
									while(($line = fgets($fh)) !== false)
									{
										fputs($this->RequestOutput, $line) ;
									}
									fclose($fh) ;
								}
								fputs($this->RequestOutput, "\r\n"."--".$this->RequestBoundary."\r\n");
							}
						}
					}
				}
				// Reading the response now !!!
				if(! $this->DownloadResponse)
				{
					$this->CloseRequest() ;
					return true ;
				}
				$this->ResponseHeadersSet = false;
				stream_set_timeout($this->RequestOutput, $this->DownloadTimeout) ;
				$this->ResponseHeadersData = "" ;
				$this->ResponseData = "" ;
				$ClrfLength = strlen("\r\n\r\n") ;
				while(($line = fgets($this->RequestOutput)) !== false)
				{
					if(! $this->ResponseHeadersSet)
					{
						$this->ProcessResponseHeadersData($line) ;
					}
					else
					{
						if(! $this->ResponseParseBodyEnabled)
						{
							break ;
						}
						$this->ProcessResponseBodyData($line) ;
					}
					stream_set_timeout($this->RequestOutput, $this->DownloadTimeout) ;
				}
				$this->CloseResponseOutput() ;
				$this->CloseRequest() ;
				
				return $this->ResponseData ;
			}
			function OpenRequest()
			{
				$this->RequestOutput = @fsockopen($this->RequestHost, $this->RequestUrlParts['port'], $errno, $errstr, $this->ConnectTimeout);
				if($this->RequestOutput == false)
				{
					$this->SetRequestException($errstr) ;
					return false ;
				}
				return true ;
			}
			function CloseRequest()
			{
				fclose($this->RequestOutput) ;
				$this->RequestOutput = false;
			}
			function DetectHeadersRequest()
			{
				// Set the real content-type and the request method
				if($this->AutoSetContentType)
				{
					if(count($this->RequestFilesData))
					{
						$this->RequestContentType = "multipart/form-data" ;
						$this->RequestBoundary = "---------------------".substr(md5(rand(0,32000)),0,10) ;
						$this->RequestMethod = "POST" ;
					}
					elseif(count($this->RequestPostData))
					{
						$this->RequestContentType = "application/x-www-form-urlencoded" ;
						$this->RequestMethod = "POST" ;
					}
				}
				elseif(count($this->RequestFilesData) || count($this->RequestPostData))
				{
					$this->RequestMethod = "POST" ;
				}
				// Detect the file upload active
				$this->RequestFileUploadActivated = ($this->RequestContentType == "multipart/form-data") ;// Calculate the content length
				$this->CalculateRequestContentLength() ;
				// Setting the host
				$this->RequestHost = ($this->RequestUrlParts['scheme'] == 'https' ? 'ssl://' : '').$this->RequestUrlParts['host'];
				// Setting the cookies
				if(! isset($this->RequestCookies[$this->CookieHost()]))
				{
					$this->RequestCookies[$this->CookieHost()] = array() ;
				}
				// Update the others Headers
				$this->RequestHeaders["Content-Length"] = $this->RequestContentLength;
				if(! $this->RequestHeaders["Content-Length"])
				{
					unset($this->RequestHeaders["Content-Length"]) ;
				}
				$this->RequestHeaders["Connection"] = "close";
				$this->RequestHeaders["Host"] = $this->RequestHost;
				// Adding the clause
				if($this->RequestContentType != "")
				{
					$this->RequestHeaders["Content-Type"] = $this->RequestContentType ;
					if($this->RequestHeaders["Content-Type"] == "multipart/form-data")
					{
						$this->RequestHeaders["Content-Type"] .= ", boundary=".$this->RequestBoundary ;
					}
					if($this->RequestCharset != "")
					{
						$this->RequestHeaders["Content-Type"] .= "; charset=".$this->RequestCharset ;
					}
				}
			}
			function WriteHeadersRequest($headers)
			{
				// Send headers
				$this->RequestHeaderData = "" ;
				// Building the page
				$page = "" ;
				$page = $this->RequestUrlParts['path'] ;
				if($this->RequestUrlParts['query'] != "")
					$page .= '?' . $this->RequestUrlParts['query'] ;
				if($this->RequestMethod == "POST") {
					$this->RequestHeaderData .= "POST $page HTTP/1.1\r\n";
				}
				else {
					$this->RequestHeaderData .= "GET $page HTTP/1.0\r\n"; //HTTP/1.0 is much easier to handle than HTTP/1.1 than GET
				}
				$headers = array_merge($this->RequestHeaders, $headers) ;
				foreach($headers as $name => $value)
				{
					$this->RequestHeaderData .= $name.": ".$value."\r\n" ;
				}
				if(count($this->RequestCookies[$this->CookieHost()]))
				{
					$cookieList = $this->BuildCookieHeader($this->RequestCookies[$this->CookieHost()], $this->RequestUrlParts["path"]);
					if($cookieList !== false)
					{
						$this->RequestHeaderData .= "Cookie: ".$cookieList."\r\n" ;
					}
				}
				fputs($this->RequestOutput, $this->RequestHeaderData."\r\n") ;
			}
			function CalculateRequestContentLength()
			{
				$this->RequestContentLength = 0 ;
				// print_r($this->RequestPostData) ;
				if($this->RequestPostDataType == "string")
				{
					$this->RequestContentLength = strlen($this->RequestPostData[0]) ;
				}
				else
				{
					$ClrfLength = strlen("\r\n") ;
					$requestEntryCount = count($this->RequestPostData) ;
					for($i=0; $i <$requestEntryCount; $i++)
					{
						$n = $this->RequestPostData[$i][0] ;
						$v = $this->RequestPostData[$i][1] ;
						if($this->RequestFileUploadActivated)
						{
							$this->RequestContentLength += strlen("--".$this->RequestBoundary."\r\nContent-Disposition: form-data; name=\"".$this->CallRequestUrlEncode($n)."\"\r\n\r\n".$this->CallRequestUrlEncode($v)."\r\n--".$this->RequestBoundary."\r\n");
						}
						else
						{
							if($i != 0)
							{
								$this->RequestContentLength++ ; // Adding "&"
							}
							$this->RequestContentLength += strlen($this->CallRequestUrlEncode($n)."=".$this->CallRequestUrlEncode($v)) ;
						}
					}
					// Adding the file lengths
					if($this->RequestFileUploadActivated)
					{
						$requestEntryCount = count($this->RequestFilesData) ;
						for($i=0; $i<$requestEntryCount; $i++)
						{
							$fileData = &$this->RequestFilesData[$i] ;
							if(! isset($fileData["filename"]))
							{
								continue ;
							}
							$this->RequestContentLength += strlen("--".$this->RequestBoundary."\r\n");
							$disposition = 'Content-Disposition: form-data; name="'.$fileData["name"].'"';
							if (isset($fileData['filename'])) {
								$disposition .= '; filename="'.$fileData['filename'].'"';
							}
							$this->RequestContentLength += strlen($disposition."\r\n");
							if(! isset($fileData['type']))
							{
								$fileData["type"] = $this->ExtractMimeContentType($fileData["filename"]) ;
							}
							if (isset($fileData['type'])) {
								$this->RequestContentLength += strlen('Content-Type: '.$fileData['type']."\r\n");
							}
							if (isset($fileData['binary']) && $fileData['binary']) {
								$this->RequestContentLength += strlen('Content-Transfer-Encoding: binary'."\r\n");
							}
							$this->RequestContentLength += $ClrfLength;
							if(file_exists($fileData["filename"]))
							{
								$this->RequestContentLength += filesize($fileData["filename"]) ;
							}
							$this->RequestContentLength += $ClrfLength;
							$this->RequestContentLength += strlen("--".$this->RequestBoundary."\r\n");
						}
					}
				}
			}
			function ParseResponseHeaders()
			{
				$headerEntry = explode("\r\n", $this->ResponseHeadersData) ;
				// print_r($this->RequestCookies) ;
				for($i = 1; $i<count($headerEntry); $i++)
				{
					if(strpos($headerEntry[$i], "Set-Cookie:") === 0)
					{
						$this->ResponseCookies[$this->CookieHost()] = $this->ExtractCookieList($headerEntry[$i]);
						if(count($this->ResponseCookies[$this->CookieHost()]))
						{
							for($j=0; $j<count($this->ResponseCookies[$this->CookieHost()]); $j++)
							{
								$pos = -1 ;
								for($k=0; $k<count($this->RequestCookies[$this->CookieHost()]) ;$k++)
								{
									if($this->RequestCookies[$this->CookieHost()][$j]["value"]["key"] == $this->ResponseCookies[$this->CookieHost()][$j]["value"]["value"])
									{
										$pos = $k ;
										break ;
									}
								}
								if($pos == -1)
								{
									$this->RequestCookies[$this->CookieHost()][] = $this->ResponseCookies[$this->CookieHost()][$j] ;
								}
								else
								{
									$this->RequestCookies[$this->CookieHost()][$pos] = $this->ResponseCookies[$this->CookieHost()][$j] ;
								}
							}
							// print_r($this->ResponseCookies) ;
							// array_splice($this->RequestCookies[$this->CookieHost()], count($this->RequestCookies[$this->CookieHost()]), 0, $this->ResponseCookies) ;
						}
					}
					else
					{
						$headerData = explode(":", $headerEntry[$i]) ;
						$this->ResponseHeaders[trim($headerData[0])] = trim($headerData[1]) ;
					}
				}
				// $responseHeaderNames = array_keys($this->ResponseHeaders) ;
				$responseHeaders = array_change_key_case($this->ResponseHeaders) ;
				if(array_key_exists("location", $responseHeaders))
				{
					$this->ResponseRedirectionUrl = $responseHeaders["location"] ;
					$this->ResponseParseBodyEnabled = true;
				}
			}
			protected function CookieHost()
			{
				return $this->RequestHost."-".$this->RequestPort ;
			}
			function ProcessResponseHeadersData($line)
			{
				$this->ResponseHeadersData .= $line ;
				if(strrpos($this->ResponseHeadersData, "\r\n\r\n") !== false)
				{
					$this->ResponseHeadersData = substr($this->ResponseHeadersData, 0, strlen($this->ResponseHeadersData) - 4) ;
					$this->ResponseHeadersSet = true ;
					$this->ParseResponseHeaders() ;
				}
			}
			function ProcessResponseBodyData($line)
			{
				if($this->ResponseFileName != "")
				{
					if(! $this->ResponseOutputSet)
					{
						try
						{
							$this->ResponseOutputSet = true;
							$this->ResponseOutput = fopen($this->ResponseFileName, "w") ;
						}
						catch(Exception $ex)
						{
						}
					}
					if($this->ResponseOutput)
					{
						fputs($this->ResponseOutput, $line) ;
					}
				}
				else
				{
					$this->ResponseData .= $line ;
				}
			}
			function CloseResponseOutput()
			{
				if($this->ResponseHeadersSet)
				{
					if($this->ResponseOutputSet)
					{
						fclose($this->ResponseOutput) ;
					}
				}
			}
			function ExtractCookieList($line)
			{
				$cookies = array() ;
				$line = preg_replace( '/^Set-Cookie: /i', '', trim($line) );
				$csplit = explode( ';', $line );
				$cdata = array();
				$cvalues = array() ;
				foreach( $csplit as $data ) {
					$cinfo = explode( '=', $data );
					$cinfo[0] = trim( $cinfo[0] );
					if( strtolower($cinfo[0]) == 'expires' ) $cinfo[1] = strtotime( $cinfo[1] );
					if( strtolower($cinfo[0]) == 'secure' ) $cinfo[1] = "true";
					if( in_array( strtolower($cinfo[0]), array( 'domain', 'expires', 'path', 'secure', 'comment', 'httponly' ) ) ) {
						$cdata[trim( strtolower($cinfo[0]) )] = (isset($cinfo[1])) ? $cinfo[1] : "";
					}
					else {
						$cvalues[$cinfo[0]] = $cinfo[1];
					}
					if(! isset($cdata["path"]))
					{
						$cdata["path"] = "/" ;
					}
				}
				$cookies = array() ;
				foreach($cvalues as $key => $value)
				{
					$cookies[] = array_merge(
						array(
							'value' => array(
								'key' => $key,
								'value' => $value,
							)
						),
						$cdata
					) ;
				}
				return $cookies;
			}
			function BuildCookieHeader($data, $path="/")
			{
				$cookie = array();
				foreach($data as $i => $d) {
					if($path == "" or strpos($path, $d["path"]) === 0)
					{
						$cookie[] = $d['value']['key'].'='.$d['value']['value'];
					}
				}
				if( count( $cookie ) > 0 ) {
					return trim( implode( '; ', $cookie ) );
				}
				return false;
			}
			function GetPage($url, $getData=array(), $headers=array())
			{
				$oldContentType = $this->AutoSetContentType ;
				$this->AutoSetContentType = false ;
				$this->RequestMethod = "GET" ;
				$this->RequestContentType = "text/html" ;
				$result = $this->GetUrl($url, $headers, $getData, array()) ;
				$this->AutoSetContentType = $oldContentType ;
				return $result ;
			}
			function PostData($url, $postData=array(), $headers=array())
			{
				$oldContentType = $this->AutoSetContentType ;
				$this->AutoSetContentType = false ;
				$this->RequestMethod = "POST" ;
				$this->RequestContentType = "application/x-www-form-urlencoded" ;
				$result = $this->GetUrl($url, $headers, "", $postData) ;
				$this->AutoSetContentType = $oldContentType ;
				return $result ;
			}
		}
	}
	
?>