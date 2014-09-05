<?php		if(! defined('NATIVE_THREAD_INCLUDED'))	{		define('NATIVE_THREAD_INCLUDED', 1) ;				class NativeThreadProcessBase		{			public $MethodSocketHandle = false ;			protected $ParentHost = false ;			public $ReadTimeout = 60 ;			public function __construct(& $parentHost)			{				$this->ParentHost = & $parentHost ;			}			public function & GetArgs()			{			}			public function Start()			{			}			public function Open()			{			}			public function Close()			{			}			public function ReadBuffer()			{			}			public function WriteBuffer($data)			{			}			public function EncodeArgs($args)			{				$result = '' ;				foreach($args as $name => $value)				{					if($result != "")						$result .= "&" ;					$result .= urlencode($name)."=".urlencode($value) ;				}				return $result ;			}			public function DecodeArgs($argString)			{				$result = array() ;				$argDatas = explode("&", $argString) ;				foreach($argDatas as $i => $argData)				{					$argMembers = explode("=", $argData, 2) ;					if(! isset($argMembers[1]))						$argMembers[1] = "" ;					$result[urldecode($argMembers[0])] = urldecode($argMembers[1]) ;				}				return $result ;			}		}		class HttpThreadProcess extends NativeThreadProcessBase		{			public $MethodScriptHost = "" ;			public $MethodScriptPort = "" ;			public $MethodScriptPath = "" ;			public $MethodScriptRaw = "" ;			protected function GetLocalUrl()			{				$scheme = (! isset($_SERVER["HTTPS"])) ? "http" : "https" ;				$url = $scheme."://".$_SERVER["SERVER_NAME"] ;				if($_SERVER['SERVER_PORT'] != '80')				{					$url .= ':'.$_SERVER['SERVER_PORT'] ;				}				$url .= $_SERVER['REQUEST_URI'] ;				return $url ;			}			public function DetectMethodScriptParts()			{				if(empty($this->ParentHost->RelativeMethodScript))				{					$this->MethodScriptRaw = $this->ParentHost->MethodScript ;					return ;				}				$localUrl = $this->GetLocalUrl() ;				$urlAttrs = parse_url($localUrl) ;				$localUrlDir = "" ;				if(isset($urlAttrs["path"]))				{					$result = pathinfo($urlAttrs["path"]) ;					if(! isset($result["extension"]))					{						$localUrlDir = $localUrl ;						if(preg_match('/\/$/', $localUrlDir))						{							$localUrlDir = substr($localUrlDir, 0, strlen($localUrlDir) - 1) ;						}					}					else					{						$localUrlDir = dirname($localUrl) ;					}				}				$this->MethodScriptRaw = $localUrlDir."/".$this->ParentHost->RelativeMethodScript ;			}			public function DetectMethodUrl()			{				$this->DetectMethodScriptParts() ;				$urlAttrs = @parse_url($this->MethodScriptRaw) ;				$this->MethodScriptHost = false ;				$this->MethodScriptPath = false ;				$this->MethodScriptPort = false ;				if(is_array($urlAttrs))				{					$this->MethodScriptHost = (isset($urlAttrs["host"])) ? $urlAttrs["host"] : "localhost" ;					$this->MethodScriptPort = (isset($urlAttrs["port"])) ? $urlAttrs["port"] : 80 ;					$this->MethodScriptPath = "" ;					if(isset($urlAttrs["path"]))					{						$this->MethodScriptPath = $urlAttrs["path"] ;					}				}			}			public function Start()			{				$this->DetectMethodUrl() ;				switch(strtoupper($this->ParentHost->MethodArgSource))				{					case "GET" :					case "ARG" :					{						$this->ExecuteMethodByGet() ;					}					break ;					default :					{						$this->ExecuteMethodByPost() ;					}					break ;				}			}			protected function ExecuteMethodByGet()			{				// echo $this->MethodScriptPath.'<br>' ;				$header = "GET ".$this->MethodScriptPath."?".$this->ParentHost->ActivateThreadArgName."=1&".$this->ParentHost->MethodArgName."=".urlencode($this->ParentHost->MethodName);				$methodArgString = $this->GetMethodArgsString() ;				if($methodArgString != "")					$header .= "&".$this->GetMethodArgsString() ;				$header .= " HTTP/1.0\r\n" ;				$header .= "Host: ".$this->MethodScriptHost."\r\n";				$header .= "Connection: close\r\n\r\n";				// echo $header ;				$this->MethodSocketHandle = fsockopen($this->MethodScriptHost, $this->MethodScriptPort);				fputs($this->MethodSocketHandle, $header) ;				fclose($this->MethodSocketHandle) ;			}			protected function ExecuteMethodByPost()			{				$body = urlencode($this->ParentHost->ActivateThreadArgName)."=1&".urlencode($this->ParentHost->MethodArgName)."=".urlencode($this->ParentHost->MethodName) ;				$methodArgString = $this->GetMethodArgsString() ;				if($methodArgString != "")					$body .= "&".$this->GetMethodArgsString() ;				$header = "POST ".$this->MethodScriptPath." HTTP/1.1\r\n" ;				$header .= "Host: ".$this->MethodScriptHost."\r\n";				$header .= "Content-Type: application/x-www-form-urlencoded\r\n";				$header .= "Content-Length: ".strlen($body)."\r\n";				$header .= "Connection: close\r\n\r\n";				// echo $header ;				$data = $header.$body ;				$this->MethodSocketHandle = fsockopen($this->MethodScriptHost, $this->MethodScriptPort);				// echo $data ;				fputs($this->MethodSocketHandle, $data) ;				fclose($this->MethodSocketHandle) ;			}			public function GetMethodArgsString()			{				return $this->EncodeArgs($this->ParentHost->MethodArgs) ;			}			public function & GetArgs()			{				$data = array() ;				switch(strtoupper($this->ParentHost->ArgSource))				{					case "GET" :					case "ARG" :					{						$data = & $_GET ;					}					break ;					case "POST" :					{						$data = & $_POST ;					}					break ;					default :					{						$data = & $_REQUEST ;					}					break ;				}				return $data ;			}		}		class ShellOsType		{			const Window = 1 ;			const Mac = 2 ;			const Unix = 3 ;		}		class ShellThreadProcess extends NativeThreadProcessBase		{			public $InterpreterPath = false ;			public $MethodScriptPath = "" ;			public $OsType = 0 ;			public function DetectLocalPath()			{				$this->ParentHost->MethodScriptRaw = "" ;				if(isset($_SERVER["argv"][0]))				{					$this->ParentHost->MethodScriptRaw = $_SERVER["argv"][0] ;				}			}			public function DetectOsType()			{				if(PHP_OS == "WINNT" || PHP_OS == "WIN32")				{					$this->OsType = ShellOsType::Window ;				}				else				{					$this->OsType = ShellOsType::Unix ;				}			}			public function GetInterpreterPath()			{				$execPath = $this->InterpreterPath ;				if($execPath != false)				{					return $execPath ;				}				$phpbin = preg_replace("@/lib(64)?/.*$@", "/bin/php", ini_get("extension_dir"));				$execPath = dirname($phpbin)."/php" ;				if($this->OsType == ShellOsType::Window)					$execPath .= ".exe" ;				return $execPath ;			}			public function Start()			{				$cmd = $this->ExtractCommandString() ;				$fh = @popen($cmd, "r") ;				$ctn = '' ;				if($fh != false)				{					stream_set_timeout($fh, 60) ;					while(! feof($fh))					{						$ctn .= fgets($fh) ;						stream_set_timeout($fh, 60) ;					}					pclose($fh) ;				}				return $ctn ;			}			// public function GetReturn			protected function ExtractCommandString()			{				$this->DetectOsType() ;				$execPath = $this->GetInterpreterPath() ;				if($execPath == false)				{					throw new Exception("Interpreteur PHP absent !!! Veuillez le renseigner en assignant le chemin au membre InterpreterPath de la classe ".get_class($this));					exit ;				}				$this->DetectMethodPath() ;				$cmd = $execPath." ".$this->MethodScriptPath." ".escapeshellarg($this->GetMethodArgsString()) ;				if($this->OsType == ShellOsType::Window)				{					$cmd = 'start /b '.$cmd ;				}				else				{					$cmd = $cmd.' >/dev/null 2>&1 &' ;				}				return $cmd ;			}			public function DetectMethodPath()			{				$this->MethodScriptPath = $this->ParentHost->MethodScriptRaw ;				if($this->ParentHost->RelativeMethodScript != "")				{					$this->DetectLocalPath() ;					$this->MethodScriptPath = dirname($this->ParentHost->MethodScriptRaw)."/".$this->ParentHost->RelativeMethodScript ;				}			}			public function GetMethodArgsString()			{				return $this->EncodeArgs(					array_merge(						array(							$this->ParentHost->ActivateThreadArgName => 1,							$this->ParentHost->MethodArgName => $this->ParentHost->MethodName						),						$this->ParentHost->MethodArgs					)				) ;			}			public function FetchPostData()			{				return $this->FetchArgData() ;				if(! defined('STDIN'))					return array() ;				$line = trim(fgets(STDIN));				return $this->DecodeArgs($line) ;			}			public function FetchArgData()			{				$data = array() ;				if(isset($_SERVER["argv"][1]))					$data = $this->DecodeArgs($_SERVER["argv"][1]) ;				return $data ;			}			public function & GetArgs()			{				$args = false ;				switch(strtoupper($this->ParentHost->ArgSource))				{					/*					case "POST" :					{						$args = $this->FetchPostData() ;					}					break ;					case "REQUEST" :					{						$args = array_merge(							$this->FetchArgData(),							$this->FetchPostData()						) ;					}					break ;					*/					default :					{						$args = $this->FetchArgData() ;					}					break ;				}				return $args ;			}		}				class NativeThreadScript		{			public static $Current ;		}				class NativeThreadHostBase		{			public $ProcessClassName = "" ;			public $Process = false ;			public function & CreateProcess()			{				$processClassName = $this->ProcessClassName ;				$process = new $processClassName($this) ;				return $process ;			}		}		class NativeThreadCallBase extends NativeThreadHostBase		{			public $MethodScript = "" ;			public $RelativeMethodScript = "" ;			public $MethodScriptRaw = "" ;			public $MethodName = "" ;			public $MethodArgName = "MethodName" ;			public $ActivateThreadArgName = "" ;			public $MethodArgs = array() ;			public $MethodArgSource = "" ;			public function __construct($name, $args=array())			{				$this->Init($name, $args) ;			}			protected function Init($name, $args=array())			{				$this->InitMembers() ;				$this->InitMethodScript() ;				$this->UpdateMethodMembers($name, $args) ;			}			protected function InitMembers()			{				$this->Process = $this->CreateProcess() ;			}			protected function InitMethodScript()			{			}			public function SetMethodScript($methodScript)			{			}			public function SetRelativeMethodScript($methodScript)			{				$this->RelativeMethodScript = $methodScript ;			}			protected function UpdateMethodMembers($name, $args=array())			{				$this->MethodName = $name ;				$this->MethodArgs = $args ;			}			public function Start()			{				$process = $this->CreateProcess() ;				$process->Start() ;			}		}		class NativeThreadScriptBase extends NativeThreadHostBase		{			public $Methods = array() ;			public $MethodName = "" ;			public $MethodSourceType = "" ;			public $ArgSource = "" ;			public $Args = array() ;			public $MethodArgName = "MethodName" ;			public $MethodArgValue = "" ;			public $MethodIndex = -1 ;			public $EnableDefaultMethod = 1 ;			public $ExitAfterMethodExec = 1 ;			public $DefaultMethod = null ;			public $DefaultMethodClassName = "" ;			public function __construct()			{				$this->Init() ;			}			protected function Init()			{				$this->InitMembers() ;				$this->InitDefaultMethod() ;				$this->InitMethods() ;			}			protected function InitMembers()			{				$this->Process = $this->CreateProcess() ;			}			protected function InitDefaultMethod()			{				$this->DefaultMethod = $this->CreateDefaultMethod() ;			}			protected function & CreateDefaultMethod()			{				$methodClassName = $this->DefaultMethodClassName ;				$method = null ;				if(! class_exists($methodClassName))				{					return $method ;				}				$method = new $methodClassName(uniqid("HomeThread_")) ;				return $method ;			}			protected function InitMethods()			{				$this->ClearMethods() ;				$this->LoadMethods() ;			}			protected function LoadMethods()			{			}			public function AddMethod($methodName, $className)			{				$this->Methods[] = $this->CreateMethod($methodName, $className) ;			}			public function AddFunctionMethod($methodName, $functionName="")			{				$this->Methods[] = $this->CreateFunctionMethod($methodName, $functionName) ;			}			public function AddObjectMethod($methodName, & $target, $targetMethod='')			{				$this->Methods[] = $this->CreateObjectMethod($methodName, $target, $targetMethod) ;			}			public function AddLocalMethod($methodName, $targetMethod='')			{				$this->AddObjectMethod($methodName, $this, $targetMethod) ;			}			public function & CreateMethod($methodName, $className)			{				$method = null ;				if(! class_exists($className))				{					return $method ;				}				$method = new $className($methodName) ;				return $method ;			}			public function & CreateFunctionMethod($methodName, $functionName='')			{				$method = new NativeThreadGlobalFunction($methodName) ;				$method->FunctionName = (empty($functionName)) ? $methodName : $functionName ;				return $method ;			}			public function & CreateObjectMethod($methodName, & $target, $targetMethod='')			{				$method = new NativeThreadObjectMethod($methodName) ;				$method->TargetObject = & $target ;				$method->TargetMethod = (empty($targetMethod)) ? $methodName : $targetMethod ;				return $method ;			}			public function ClearMethods()			{				$this->Methods = array() ;			}			protected function IsEnabled()			{				return 1 ;			}			public function Run()			{				NativeThreadScript::$Current = $this ;				if(! $this->IsEnabled())				{					NativeThreadScript::$Current = null ;					return ;				}				$this->Args = $this->GetArgs() ;				$this->MethodArgValue = (isset($this->Args[$this->MethodArgName])) ? $this->Args[$this->MethodArgName] : "" ;				$methodIndex = -1 ;				for($i=0; $i<count($this->Methods); $i++)				{					if($this->Methods[$i]->Accept($this->MethodArgValue))					{						$methodIndex = $i ;						break ;					}				}				$this->MethodIndex = $methodIndex ;				if($methodIndex == -1)				{					if($this->EnableDefaultMethod)					{						if($this->DefaultMethod != null)						{							$this->ExecuteMethod($this->DefaultMethod) ;						}						else						{							$this->ExecuteDefaultMethod() ;						}					}				}				else				{					$this->ExecuteMethod($this->Methods[$methodIndex]) ;				}				NativeThreadScript::$Current = null ;			}			protected function ExecuteMethod(& $method)			{				$method->Args = & $this->Args ;				$method->Execute() ;				if($this->ExitAfterMethodExec)				{					exit ;				}			}			public function ExecuteDefaultMethod()			{			}			public function & GetArgs()			{				$data = array() ;				$process = $this->CreateProcess() ;				if($process == null)				{					return $data ;				}				return $process->GetArgs() ;			}		}				class HttpThreadCall extends NativeThreadCallBase		{			public $ProcessClassName = "HttpThreadProcess" ;			public $MethodArgName = "MethodName" ;			public $ActivateThreadArgName = "RunThread" ;		}		class HttpThreadScript extends NativeThreadScriptBase		{			public $MethodArgName = "MethodName" ;			public $DefaultMethodClassName = "NativeThreadMethodBase" ;			public $ProcessClassName = "HttpThreadProcess" ;		}				class ShellThreadCall extends NativeThreadCallBase		{			public $ProcessClassName = "ShellThreadProcess" ;			public $ActivateThreadArgName = "RunThread" ;			public $MethodArgName = "MethodName" ;		}		class ShellThreadScript extends NativeThreadScriptBase		{			public $MethodArgName = "MethodName" ;			public $DefaultMethodClassName = "NativeThreadMethodBase" ;			public $ProcessClassName = "ShellThreadProcess" ;		}				class NativeThreadMethodBase		{			public $Name = "" ;			public $ThreadId = "" ;			public $Args = array() ;			public $Results = array() ;			public function __construct($name)			{				$this->Name = $name ;				$this->ThreadId = uniqid() ;			}			public function Accept($methodName)			{				return ($methodName == $this-> Name) ? 1 : 0 ;			}			public function Execute()			{			}		}		class NativeThreadGlobalFunction extends NativeThreadMethodBase		{			public $FunctionName = "" ;			public function Execute()			{				if(! function_exists($this->FunctionName))				{					return ;				}				call_user_func_array($this->FunctionName, array($this->Args)) ;			}		}		class NativeThreadObjectMethod extends NativeThreadMethodBase		{			public $TargetMethod = "" ;			public $TargetObject = null ;			public function Execute()			{				if($this->TargetObject == null || $this->TargetMethod == "")				{					return ;				}				if(! method_exists($this->TargetObject, $this->TargetMethod))				{					return ;				}				call_user_func_array(					array(&$this->TargetObject, $this->TargetMethod),					array($this->Args)				) ;			}		}			}?>