<?php
	
	if(! defined('PERS_RMT_HTTP_SVC'))
	{
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		define('PERS_RMT_HTTP_SVC', 1) ;
		
		class PersRmtHttpBaseSvc
		{
			protected $DBs = array() ;
			protected $httpReqHeaders = array() ;
			protected $methods = array() ;
			protected $reqProviders = array() ;
			protected $activeRespProvider = array() ;
			protected $req ;
			protected $resp ;
			protected $activeReqProviderName ;
			protected $activeMethodName ;
			protected $notFoundMethod ;
			protected $badRequestMethod ;
			protected $testMethod ;
			public function __construct()
			{
				$this->initConfig() ;
			}
			protected function initConfig()
			{
				$this->initMethods() ;
				$this->initDBs() ;
				$this->initReqProviders() ;
				$this->initActiveRespProvider() ;
				$this->initLocalMethods() ;
			}
			protected function initLocalMethods()
			{
				$this->testMethod = new PersRmtTestMethod() ;
				$this->testMethod->setParentSvc("test", $this) ;
				$this->badRequestMethod = new PersRmtBadRequestMethod() ;
				$this->badRequestMethod->setParentSvc("bad_request", $this) ;
				$this->notFoundMethod = new PersRmtNotFoundMethod() ;
				$this->notFoundMethod->setParentSvc("not_found", $this) ;
			}
			protected function initMethods()
			{
			}
			protected function initDBs()
			{
			}
			protected function initReqProviders()
			{
				$this->insertReqProvider("http_get", new PersRmtHttpReqProvider()) ;
			}
			protected function initActiveRespProvider()
			{
				$this->activeRespProvider = new PersRmtJsonRespProvider() ;
			}
			protected function getAllMethods()
			{
				$methods = $this->methods ;
				$methods["test"] = & $this->testMethod ;
				return $methods ;
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
			protected function & insertMethod($name, $method)
			{
				$this->methods[$name] = & $method ;
				$method->setParentSvc($name, $this) ;
				return $method ;
			}
			protected function prepare()
			{
				$this->httpReqHeaders = apache_request_headers();
				$this->req = null ;
				$this->resp = new PersRmtResp() ;
			}
			public function httpReqHeader($name, $defaultValue='')
			{
				return (isset($this->httpReqHeaders[$name])) ? $this->httpReqHeaders[$name] : $defaultValue ;
			}
			public function & getResp()
			{
				return $this->resp ;
			}
			public function getReqParam($name, $defaultValue='')
			{
				return $this->req->getParam($name, $defaultValue) ;
			}
			public function getReqParams()
			{
				return $this->req->params ;
			}
			public function getDB($name)
			{
				$db = null ;
				if($name == '')
				{
					return $db ;
				}
				if(isset($this->DBs[$name]))
				{
					$db = $this->DBs[$name] ;
				}
				return $db ;
			}
			public function run()
			{
				$this->prepare() ;
				if($this->detectActiveReqProvider())
				{
					$this->req = $this->getActiveReqProvider()->extractReq() ;
					if($this->detectActiveMethod())
					{
						$this->getActiveMethod()->execute($this->req) ;
					}
					else
					{
						$this->notFoundMethod->execute(new PersRmtReq()) ;
					}
				}
				else
				{
					$this->badRequestMethod->execute(new PersRmtReq()) ;
				}
				$this->activeRespProvider->render($this->resp, $this) ;
			}
			protected function detectActiveMethod()
			{
				$this->activeMethodName = null ;
				$methods = $this->getAllMethods() ;
				foreach($methods as $methodName => & $method)
				{
					if($method->methodName() == $this->req->methodName)
					{
						$this->activeMethodName = $method->methodName() ;
						break ;
					}
				}
				return $this->activeMethodName != '' ;
			}
			public function getActiveMethodName()
			{
				return $this->activeMethodName ;
			}
			protected function getActiveMethod()
			{
				$methods = $this->getAllMethods() ;
				return $methods[$this->activeMethodName] ;
			}
			protected function getActiveReqProvider()
			{
				return $this->reqProviders[$this->activeReqProviderName] ;
			}
			protected function detectActiveReqProvider()
			{
				$this->activeReqProviderName = null ;
				foreach($this->reqProviders as $name => & $req)
				{
					if($req->isActive($name, $this))
					{
						$this->activeReqProviderName = $name ;
						break ;
					}
				}
				return $this->activeReqProviderName != null ;
			}
		}
		class PersRmtAppSvc extends PersRmtHttpBaseSvc
		{
			protected $dbRunSqlMethod ;
			protected $dbFetchSqlRowMethod ;
			protected $dbFetchSqlRowsMethod ;
			protected $dbRunStoredProcMethod ;
			protected $dbFetchStoredProcRowMethod ;
			protected $dbFetchStoredProcRowsMethod ;
			protected $registerDbMethods = 1 ;
			protected $registerDbSqlMethods = 1 ;
			protected $registerDbStoredProcMethods = 1 ;
			protected function initMethods()
			{
				parent::initMethods() ;
				if($this->registerDbMethods == 1)
				{
					if($this->registerDbStoredProcMethods == 1)
					{
						$this->dbFetchStoredProcRowMethod = $this->insertMethod("db.fetchStoredProcRow", new PersRmtDbFetchStoredProcRowMethod()) ;
						$this->dbFetchStoredProcRowsMethod = $this->insertMethod("db.fetchStoredProcRows", new PersRmtDbFetchStoredProcRowsMethod()) ;
						$this->dbRunStoredProcMethod = $this->insertMethod("db.runStoredProc", new PersRmtDbRunStoredProcMethod()) ;
					}
					if($this->registerDbSqlMethods == 1)
					{
						$this->dbRunSqlMethod = $this->insertMethod("db.runSql", new PersRmtDbRunSqlMethod()) ;
						$this->dbFetchSqlRowMethod = $this->insertMethod("db.fetchSqlRow", new PersRmtDbFetchSqlRowMethod()) ;
						$this->dbFetchSqlRowsMethod = $this->insertMethod("db.fetchSqlRows", new PersRmtDbFetchSqlRowsMethod()) ;
					}
				}
			}
		}
		
		class PersRmtReq
		{
			public $methodName ;
			public $params = array() ;
			public function getParam($name, $defaultValue='')
			{
				$res = $defaultValue ;
				if(isset($this->params[$name]))
					$res = $this->params[$name] ;
				return $res ;
			}
		}
		class PersRmtResp
		{
			public $errorCode = -1 ;
			public $errorMsg = "Response not initialised" ;
			public $result ;
			public function confirmSuccess($result)
			{
				$this->errorCode = 0 ;
				$this->errorMsg = "success" ;
				$this->result = $result ;
			}
			public function confirmError($errorCode, $errorMsg)
			{
				$this->errorCode = $errorCode ;
				$this->errorMsg = $errorMsg ;
				$this->result = null ;
			}
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
				$data = $_REQUEST ;
				$req = new PersRmtReq() ;
				$req->methodName = (isset($data["methodName"])) ? $data["methodName"] : '' ;
				unset($data["methodName"]) ;
				$req->params = $data ;
				// print_r($req) ;
				return $req ;
			}
		}
		
		class PersRmtRespProviderBase
		{
			public function render($resp, & $svc)
			{
			}
		}
		class PersRmtJsonRespProvider extends PersRmtRespProviderBase
		{
			public function render($resp, & $svc)
			{
				// Header("Content-type:application/json\n") ;
				echo json_encode($resp) ;
			}
		}
		
		class PersRmtMethodBase
		{
			protected $_parentSvc ;
			protected $_methodName ;
			protected function & getResp()
			{
				// print_r($this->_parentSvc) ;
				return $this->_parentSvc->getResp() ;
			}
			public function & parentSvc()
			{
				return $this->_parentSvc ;
			}
			public function methodName()
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
		class PersRmtTestMethod extends PersRmtMethodBase
		{
			public function execute()
			{
				$this->getResp()->confirmSuccess("Test succeeded") ;
			}
		}
		class PersRmtBadRequestMethod extends PersRmtMethodBase
		{
			public function execute()
			{
				$this->getResp()->confirmError(-4, "The request sent cant be treated") ;
			}
		}
		class PersRmtNotFoundMethod extends PersRmtMethodBase
		{
			public function execute()
			{
				$this->getResp()->confirmError(-2, "The method '".$this->_parentSvc->getActiveMethodName()."' doesnt not exists in this service") ;
			}
		}
		
		class PersRmtDbBaseMethod extends PersRmtMethodBase
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return false ;
			}
			protected function extractSqlParams(& $params, & $db)
			{
				$sqlparams = array() ;
				$sqlExprs = array() ;
				foreach($params as $name => $value)
				{
					if(strpos($name, "sql_param_") === 0)
					{
						$sqlparams[str_replace($name, "sql_param_", "")] = $value ;
					}
					elseif(strpos($name, "sql_expr_") === 0)
					{
						$sqlExprs[str_replace($name, "sql_expr_", "")] = $value ;
					}
				}
				if(count($sqlExprs) > 0)
				{
					$sqlParams[$db->ExprKeyName] = & $sqlExprs ;
				}
				return $sqlparams ;
			}
			public function execute()
			{
				$dbName = $this->_parentSvc->getReqParam("dbName") ;
				$text = $this->_parentSvc->getReqParam("text") ;
				$db = $this->_parentSvc->getDB($dbName) ;
				if($db == null)
				{
					$this->_parentSvc->getResp()->confirmError(1, "Database not found") ;
					return ;
				}
				$reqParams = $this->_parentSvc->getReqParams() ;
				$params = $this->extractSqlParams($reqParams, $db) ;
				$result = $this->extractResults($db, $text, $params) ;
				if($result == false)
				{
					if($db->ConnectionException != '')
					{
						$this->_parentSvc->getResp()->confirmError(2, $db->ConnectionException) ;
					}
					else
					{
						$this->_parentSvc->getResp()->confirmError(3, "Database query failed") ;
					}
					return ;
				}
				$this->_parentSvc->getResp()->confirmSuccess($result) ;
			}
		}
		class PersRmtDbRunSqlMethod extends PersRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->RunSql($text, $params) ;
			}
		}
		class PersRmtDbFetchSqlRowsMethod extends PersRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchSqlRows($text, $params) ;
			}
		}
		class PersRmtDbFetchSqlRowMethod extends PersRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchSqlRow($text, $params) ;
			}
		}
		class PersRmtDbRunStoredProcMethod extends PersRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->RunStoredProc($text, $params) ;
			}
		}
		class PersRmtDbFetchStoredProcRowsMethod extends PersRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchStoredProcRows($text, $params) ;
			}
		}
		class PersRmtDbFetchStoredProcRowMethod extends PersRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchStoredProcRow($text, $params) ;
			}
		}
	}
	

?>