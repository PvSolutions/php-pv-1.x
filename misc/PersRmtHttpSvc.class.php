<?php
	
	if(! defined('PERS_RMT_HTTP_SVC'))
	{
		define('PERS_RMT_HTTP_SVC', 1) ;
		
		class PersRmtHttpSvc
		{
			protected $DBs = array() ;
			protected $httpReqHeaders = array() ;
			protected $methods = array() ;
			protected $reqProviders = array() ;
			protected $respProviders = array() ;
			public function __construct()
			{
				$this->initConfig() ;
			}
			protected function initConfig()
			{
				$this->initMethods() ;
				$this->initDBs() ;
				$this->initReqProviders() ;
				$this->initRespProviders() ;
			}
			protected function initMethods()
			{
			}
			protected function initDBs()
			{
			}
			protected function initReqProviders()
			{
			}
			protected function initRespProviders()
			{
			}
			protected function & insertDB($name, $db)
			{
				$this->DBs[$name] = & $db ;
				return $db ;
			}
			protected function & insertReqProvider($name, $provider)
			{
				$this->reqProviders[$name] = & $provider ;
				return $provider ;
			}
			protected function & insertRespProvider($name, $provider)
			{
				$this->respProviders[$name] = & $provider ;
				return $provider ;
			}
			protected function & insertMethod($name, $method)
			{
				$this->methods[$name] = & $method ;
				$method->setParentSvc($name, $this) ;
				return $method ;
			}
			protected function prepare()
			{
				$this->httpReqHeaders = apache_request_headers();
			}
			public function httpReqHeader($name, $defaultValue='')
			{
				return (isset($this->httpReqHeaders[$name])) ? $this->httpReqHeaders[$name] : $defaultValue ;
			}
			public function run()
			{
				$this->prepare() ;
			}
		}
		
		class PersRmtReq
		{
			public $methodName ;
			public $params = array() ;
		}
		
		class PersRmtReqProviderBase
		{
			public function isActive($name, & $svc)
			{
				return 0 ;
			}
			public function extractReq()
			{
				return null ;
			}
		}
		class PersRmtHttpReqProvider extends PersRmtReqProviderBase
		{
			public function isActive($name, & $svc)
			{
				return (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET") ;
			}
			public function extractReq()
			{
				$data = & $_GET ;
				$req = new PersRmtReq() ;
				$req->methodName = (isset($data["methodName"])) ? $data["methodName"] : '' ;
				unset($data["methodName"]) ;
				$req->params = $data ;
				return $req ;
			}
		}
		
		class PersRmtRespProviderBase
		{
			public function render($na²me, & $svc)
			{
			}
		}
		
		class PersRmtMethodBase
		{
			protected $_parentSvc ;
			protected $_methodName ;
			public function & parentSvc()
			{
				return $this->_parentSvc ;
			}
			public function & methodName()
			{
				return $this->_methodName ;
			}
			public function setParentSvc($name, & $parentSvc)
			{
				$this->_methodName = $name ;
				$this->_parentSvc = & $parentSvc ;
			}
			public function execute()
			{
			}
		}
	}
	

?>