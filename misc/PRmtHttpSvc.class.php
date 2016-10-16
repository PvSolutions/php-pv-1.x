<?php
	
	if(! defined('PERS_RMT_HTTP_SVC'))
	{
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		if(! defined('AK_MEMBERSHIP'))
		{
			include dirname(__FILE__)."/../Ak/Membership.class.php" ;
		}
        if(! defined("SERVICES_JSON_SLICE"))
        {
            include dirname(__FILE__)."/Services_JSON.class.php" ;
        }
		if(! defined('UTILS_INCLUDED'))
		{
			include dirname(__FILE__)."/utils.php" ;
		}
		define('PERS_RMT_HTTP_SVC', 1) ;
		
		class PRmtHttpBaseSvc
		{
            public $respCharset = "utf-8" ;
            public $respContentTypePlain = 1 ;
			protected $httpReqHeaders = array() ;
			protected $methods = array() ;
			protected $reqProviders = array() ;
			protected $activeRespProvider = array() ;
			protected $methodProviders = array() ;
			protected $req ;
			protected $resp ;
			protected $outputData ;
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
				$this->initMethodProviders() ;
				$this->prepareMethodProviders() ;
				$this->installMethodProviders() ;
				$this->initReqProviders() ;
				$this->initActiveRespProvider() ;
				$this->initLocalMethods() ;
			}
			protected function initMethodProviders()
			{
			}
			protected function prepareMethodProviders()
			{
			}
			private function installMethodProviders()
			{
				foreach($this->methodProviders as $providerName => & $provider)
				{
					$provider->installMethods() ;
				}
			}
			protected function initLocalMethods()
			{
				$this->testMethod = new PRmtTestMethod() ;
				$this->testMethod->setParentSvc("test", $this) ;
				$this->badRequestMethod = new PRmtBadRequestMethod() ;
				$this->badRequestMethod->setParentSvc("bad_request", $this) ;
				$this->notFoundMethod = new PRmtNotFoundMethod() ;
				$this->notFoundMethod->setParentSvc("not_found", $this) ;
			}
			protected function initMethods()
			{
			}
			protected function initReqProviders()
			{
				$this->insertReqProvider("http_get", new PRmtHttpReqProvider()) ;
			}
			protected function initActiveRespProvider()
			{
				$this->activeRespProvider = new PRmtJsonRespProvider() ;
			}
			public function getAllMethods()
			{
				$methods = $this->methods ;
				$methods["test"] = & $this->testMethod ;
				return $methods ;
			}
			protected function & insertReqProvider($name, $provider)
			{
				$this->reqProviders[$name] = & $provider ;
				return $provider ;
			}
			public function & insertMethod($name, $method)
			{
				$this->methods[$name] = & $method ;
				$method->setParentSvc($name, $this) ;
				return $method ;
			}
			public function & insertMethodProvider($name, $methodProvider)
			{
				if($name == '')
				{
					$name = $methodProvider->getDefaultProviderName() ;
					if($name == '')
					{
						$name = uniqid().count($this->methodProviders) ;
					}
				}
				$this->methodProviders[$name] = & $methodProvider ;
				$methodProvider->setParentSvc($name, $this) ;
				return $methodProvider ;
			}
			public function & addMethodProvider($methodProvider)
			{
				return $this->insertMethodProvider('', $methodProvider) ;
			}
			protected function prepare()
			{
				header("Access-Control-Allow-Origin: *");
				$this->req = null ;
				$this->resp = new PRmtResp() ;
			}
			public function httpReqHeader($name, $defaultValue='')
			{
				return (isset($this->httpReqHeaders[$name])) ? $this->httpReqHeaders[$name] : $defaultValue ;
			}
			public function & getResp()
			{
				return $this->resp ;
			}
			public function & getOutputData()
			{
				return $this->outputData ;
			}
			public function getReqParam($name, $defaultValue='')
			{
				return $this->req->getParam($name, $defaultValue) ;
			}
			public function getReqParams()
			{
				return $this->req->params ;
			}
			protected function executeMethod(& $method)
			{
				$method->prepare() ;
				$method->execute() ;
				$this->outputData = $method->getOutputData() ;
			}
			public function run()
			{
				$this->prepare() ;
				if($this->detectActiveReqProvider())
				{
					$this->req = $this->getActiveReqProvider()->extractReq() ;
					if($this->detectActiveMethod())
					{
						$method = $this->getActiveMethod() ;
						$this->executeMethod($method) ;
					}
					else
					{
						$this->executeMethod($this->notFoundMethod) ;
					}
				}
				else
				{
					$this->executeMethod($this->badRequestMethod) ;
				}
				$this->activeRespProvider->render($this->outputData, $this) ;
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
			protected function & getActiveMethod()
			{
				$methods = $this->getAllMethods() ;
				return $methods[$this->activeMethodName] ;
			}
			protected function & getActiveReqProvider()
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
		class PRmtAppSvc extends PRmtHttpBaseSvc
		{
			protected $commonDBMethodProvider ;
			protected $akMSMethodProvider ;
			protected $registerSpecMethodProviders = 0 ;
			protected function initMethodProviders() 
			{
				if($this->registerSpecMethodProviders == 0)
				{
					return ;
				}
				$this->commonDBMethodProvider = $this->addMethodProvider(new PRmtCommonDBMethodProvider()) ;
				$this->akMSMethodProvider = $this->addMethodProvider(new PRmtAkMSMethodProvider()) ;
			}
			protected function & installMSMethodProvider()
			{
				return $this->addMethodProvider(new PRmtAkMSMethodProvider()) ;
			}
			public function & insertSqlProvider($db) 
			{
				$methodProvider = $this->addMethodProvider(new PRmtSqlMethodProvider()) ;
				$methodProvider->setDb($db) ;
				return $methodProvider ;
			}
		}
		
		class PRmtReq
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
		class PRmtResp
		{
			public $errorCode = -1 ;
			public $errorMsg = "Response not initialised" ;
			public $errorAlias = "not_initialised" ;
			public $errorLevel = "" ;
			public $result ;
			public function confirmSuccess($result)
			{
				$this->errorCode = 0 ;
				$this->errorMsg = "success" ;
				$this->errorAlias = "success" ;
				$this->errorLevel = "" ;
				$this->result = $result ;
			}
			public function confirmError($errorCode, $errorMsg, $errorAlias='undefined_alias', $errorLevel='error')
			{
				$this->errorCode = $errorCode ;
				$this->errorMsg = $errorMsg ;
				$this->errorAlias = $errorAlias ;
				$this->errorLevel = $errorLevel ;
				$this->result = null ;
			}
		}
		
		class PRmtReqProviderBase
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
		class PRmtHttpReqProvider extends PRmtReqProviderBase
		{
			public function isActive($name, & $svc)
			{
				return (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET") ;
			}
			public function extractReq()
			{
				$data = $_REQUEST ;
				$req = new PRmtReq() ;
				$req->methodName = (isset($data["method"])) ? $data["method"] : '' ;
				unset($data["methodName"]) ;
				$req->params = $data ;
				// print_r($req) ;
				return $req ;
			}
		}
		
		class PRmtRespProviderBase
		{
			public function render($resp, & $svc)
			{
			}
		}
		class PRmtJsonRespProvider extends PRmtRespProviderBase
		{
			public function render($resp, & $svc)
			{
				$contentType = ($svc->respContentTypePlain == 1) ? "text/plain" : "application/json" ;
				Header("Content-type:".$contentType."\n") ;
				header("Content-Type: text/html; charset=".$svc->respCharset."\n");
                echo svc_json_encode($resp) ;
                // print_r($resp) ;
			}
		}
		
		class PRmtMethodProviderBase
		{
			protected $_parentSvc ;
			protected $_providerName ;
			protected $defaultProviderName ;
			public function __construct()
			{
				$this->initConfig() ;
			}
			protected function initConfig()
			{
			}
			public function setParentSvc($providerName, & $svc)
			{
				$this->_providerName = $providerName ;
				$this->_parentSvc = & $svc ;
			}
			public function getDefaultProviderName()
			{
				return $this->defaultProviderName ;
			}
			public function installMethods()
			{
				$this->installInnerMethods() ;
				$this->installSpecMethods() ;
			}
			protected function installInnerMethods()
			{
			}
			protected function installSpecMethods()
			{
			}
			public function & installMethod($methodName, $method)
			{
				$method->setMethodProvider($this) ;
				$this->_parentSvc->insertMethod($this->_providerName.".".$methodName, $method) ;
				return $method ;
			}
		}
		class PRmtDBMethodProviderBase extends PRmtMethodProviderBase
		{
			protected $DBs ;
			protected function initConfig()
			{
				$this->initDBs() ;
			}
			protected function initDBs()
			{
			}
			public function & insertDB($name, $db)
			{
				$this->DBs[$name] = & $db ;
				return $db ;
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
		}
		class PRmtCommonDBMethodProvider extends PRmtDBMethodProviderBase
		{
			protected $defaultProviderName = "commonDB" ;
			protected $dbRunSqlMethod ;
			protected $dbFetchSqlRowMethod ;
			protected $dbFetchSqlRowsMethod ;
			protected $dbRunStoredProcMethod ;
			protected $dbFetchStoredProcRowMethod ;
			protected $dbFetchStoredProcRowsMethod ;
			protected $registerDbMethods = 1 ;
			protected $registerDbSqlMethods = 1 ;
			protected $registerDbStoredProcMethods = 1 ;
			public function installMethods()
			{
				parent::installMethods() ;
				if($this->registerDbMethods == 1)
				{
					if($this->registerDbStoredProcMethods == 1)
					{
						$this->fetchStoredProcRowMethod = $this->installMethod("fetchStoredProcRow", new PRmtDbFetchStoredProcRowMethod()) ;
						$this->fetchStoredProcRowsMethod = $this->installMethod("fetchStoredProcRows", new PRmtDbFetchStoredProcRowsMethod()) ;
						$this->runStoredProcMethod = $this->installMethod("runStoredProc", new PRmtDbRunStoredProcMethod()) ;
					}
					if($this->registerDbSqlMethods == 1)
					{
						$this->runSqlMethod = $this->installMethod("runSql", new PRmtDbRunSqlMethod()) ;
						$this->fetchSqlRowMethod = $this->installMethod("fetchSqlRow", new PRmtDbFetchSqlRowMethod()) ;
						$this->fetchSqlRowsMethod = $this->installMethod("fetchSqlRows", new PRmtDbFetchSqlRowsMethod()) ;
					}
				}
			}
		}
		class PRmtAkMSMethodProvider extends PRmtMethodProviderBase
		{
			protected $defaultProviderName = "akMembership" ;
			protected $membership ;
			protected $loginMemberMethod ;
			protected $getMemberMethod ;
			protected $changePasswordMemberMethod ;
			protected function initConfig()
			{
				parent::initConfig() ;
				$this->initMembership() ;
			}
			protected function initMembership()
			{
			}
			public function setMembership($membership)
			{
				$this->membership = & $membership ;
				return $membership ;
			}
			public function & getMembership()
			{
				return $this->membership ;
			}
			public function installMethods()
			{
				parent::installMethods() ;
				$this->loginMemberMethod = $this->installMethod("loginMember", new PRmtAkLoginMemberMethod()) ;
				$this->getMemberMethod = $this->installMethod("getMember", new PRmtAkGetMemberMethod()) ;
				$this->changePasswordMemberMethod = $this->installMethod("changePasswordMember", new PRmtAkChangePasswordMemberMethod()) ;
			}
		}
		class PRmtSqlMethodProvider extends PRmtMethodProviderBase
		{
			protected $db ;
			protected $editCommands = array() ;
			protected $defaultProviderName = "sql" ;
			protected $defaultEditCommandName = "detail" ;
			protected $tableDefs = array() ;
			protected $membership ;
			protected $msLoginMethod ;
			protected $msChangePwdMethod ;
			protected $msListMembersMethod ;
			protected $msEditMemberMethod ;
			protected $msListProfilesMethod ;
			protected $msEditProfileMethod ;
			protected $msListRolesMethod ;
			protected $msEditRoleMethod ;
			protected $msMemberTableDef ;
			protected $msIdMemberColDef ;
			protected $msLoginMemberColDef ;
			protected $msPwdMemberColDef ;
			protected $msFNameMemberColDef ;
			protected $msLNameMemberColDef ;
			protected $msAddrMemberColDef ;
			protected $msContactMemberColDef ;
			protected $msADActivatedMemberColDef ;
			protected $msEnableMemberColDef ;
			protected $msMustChangePwdMemberColDef ;
			protected function initConfig()
			{
				parent::initConfig() ;
				$this->initMembership() ;
				$this->editCommands["detail"] = new PRmtSqlDetailCommand() ; 
				$this->editCommands["insert"] = new PRmtSqlInsertCommand() ; 
				$this->editCommands["update"] = new PRmtSqlUpdateCommand() ; 
				$this->editCommands["delete"] = new PRmtSqlDeleteCommand() ; 
			}
			protected function initMembership()
			{
			}
			public function setMembership($membership)
			{
				$this->membership = & $membership ;
				return $membership ;
			}
			public function & getMembership()
			{
				return $this->membership ;
			}
			public function & getEditCommand($cmdName)
			{
				$cmd = & $this->editCommands[$this->defaultEditCommandName] ;
				if(isset($this->editCommands[$cmdName]))
				{
					$cmd = & $this->editCommands[$cmdName] ;
				}
				return $cmd ;
			}
			public function getEditCommandNames()
			{
				return array_keys($this->editCommands) ;
			}
			public function setDb($db)
			{
				$this->db = & $db ;
			}
			public function & getDb()
			{
				return $this->db ;
			}
			public function installSelectMethod($methodName)
			{
				return $this->installMethod($methodName, new PRmtSqlSelectMethod()) ;
			}
			public function installEditMethod($methodName)
			{
				return $this->installMethod($methodName, new PRmtSqlEditMethod()) ;
			}
			public function & getTableDefs()
			{
				return $this->tableDefs ;
			}
			public function & insertTableDef($editTableName, $selectQueryText='', $selectQueryParams=array())
			{
				$tableDef = new PRmtSqlTableDef() ;
				$tableDef->editTableName = $editTableName ;
				if($selectQueryText == '')
				{
					$selectQueryText = $editTableName ;
				}
				$tableDef->selectQueryText = $selectQueryText ;
				$tableDef->selectQueryParams = $selectQueryParams ;
				$this->tableDefs[$tableDef->editTableName] = & $tableDef ;
				return $tableDef ;
			}
			public function removeTableDef($editTableName)
			{
				unset($this->tableDefs[$tableDef->editTableName]) ;
			}
			protected function installTableDefsMethods()
			{
				foreach($this->tableDefs as $name => & $tableDef)
				{
					$tableDef->installMethods($this) ;
				}
			}
			protected function installInnerMethods()
			{
				parent::installInnerMethods() ;
				$this->installMembershipMethods() ;
				$this->installTableDefsMethods() ;
			}
			protected function installMembershipMethods()
			{
				if(is_null($this->membership))
				{
					return ;
				}
				$this->msLoginMethod = $this->installMethod("ms_login", new PRmtSqlLoginMemberMethod()) ;
				$this->msChangePwdMethod = $this->installMethod("ms_change_password", new PRmtSqlChangePasswordMemberMethod()) ;
				// print_r(array_keys($this->_parentSvc->getAllMethods())) ;
				$membership = & $this->membership ;
				$db = & $membership->Database ;
				// Member table def
				$this->msMemberTableDef = $this->insertTableDef($membership->MemberTable) ;
				$this->msMemberTableDef->httpParamName = "ms_members" ;
				$this->msMemberTableDef->selectQueryText = "(select t1.*, t2.".$db->EscapeVariableName($membership->TitleProfileColumn)." profile_title, t2.".$db->EscapeVariableName($membership->DescriptionProfileColumn)." profile_desc from ".$db->EscapeTableName($membership->MemberTable)." t1 left join ".$db->EscapeTableName($membership->ProfileTable)." t2 on t1.".$db->EscapeVariableName($membership->ProfileMemberColumn)." = t2.".$db->EscapeVariableName($membership->ProfileMemberForeignKey).")" ;
				$this->msIdMemberColDef = $this->msMemberTableDef->insertColumn($membership->IdMemberColumn) ;
				$this->msIdMemberColDef->isKey = true ;
				$this->msIdMemberColDef->httpParamName = "id" ;
				$this->msLoginMemberColDef = $this->msMemberTableDef->insertColumn($membership->LoginMemberColumn) ;
				$this->msLoginMemberColDef->isSearchable = 1 ;
				$this->msLoginMemberColDef->httpParamName = "login" ;
				$this->msFNameMemberColDef = $this->msMemberTableDef->insertColumn($membership->FirstNameMemberColumn) ;
				$this->msPwdMemberColDef = $this->msMemberTableDef->insertColumn($membership->LoginMemberColumn) ;
				$this->msPwdMemberColDef->httpParamName = "password" ;
				if($membership->PasswordMemberExpr != "")
				{
					$this->msPwdMemberColDef->editAlias = $membership->PasswordMemberExpr.'(<self>)' ;
				}
				$this->msFNameMemberColDef = $this->msMemberTableDef->insertColumn($membership->FirstNameMemberColumn) ;
				$this->msFNameMemberColDef->httpParamName = "first_name" ;
				$this->msFNameMemberColDef->insertSearchParam('fname_contains', $db->SqlIndexOf('upper(${column})', 'upper(<self>)').' > 0') ;
				$this->msLNameMemberColDef = $this->msMemberTableDef->insertColumn($membership->LastNameMemberColumn) ;
				$this->msLNameMemberColDef->httpParamName = "last_name" ;
				$this->msLNameMemberColDef->insertSearchParam('lname_contains', $db->SqlIndexOf('upper(${column})', 'upper(<self>)').' > 0') ;
				$this->msEmailMemberColDef = $this->msMemberTableDef->insertColumn($membership->EmailMemberColumn) ;
				$this->msEmailMemberColDef->httpParamName = "email" ;
				$this->msEmailMemberColDef->isSearchable = 1 ;
				$this->msAddrMemberColDef = $this->msMemberTableDef->insertColumn($membership->AddressMemberColumn) ;
				$this->msAddrMemberColDef->httpParamName = "address" ;
				$this->msContactMemberColDef = $this->msMemberTableDef->insertColumn($membership->ContactMemberColumn) ;
				$this->msContactMemberColDef->httpParamName = "contact" ;
				$this->msEnableMemberColDef = $this->msMemberTableDef->insertColumn($membership->EnableMemberColumn) ;
				$this->msEnableMemberColDef->httpParamName = "enable" ;
				if($membership->ADActivatedMemberColumn != '')
				{
					$this->msADActivatedMemberColDef = $this->msMemberTableDef->insertColumn($membership->ADActivatedMemberColumn) ;
				}
				$this->msProfileMemberColDef = $this->msMemberTableDef->insertColumn($membership->ProfileMemberColumn) ;
				$this->msProfileTitleColDef = $this->msMemberTableDef->insertColumn("profile_title") ;
				$this->msProfileTitleColDef->isEditable = false ;
				$this->msProfileDescColDef = $this->msMemberTableDef->insertColumn("profile_desc") ;
				$this->msProfileDescColDef->isEditable = false ;
				$this->msMemberTableDef->insertLoginRule($membership->LoginMemberColumn) ;
				$this->msMemberTableDef->insertEmailRule($membership->EmailMemberColumn) ;
				$this->msMemberTableDef->setEditCommand(new PRmtSqlMSMemberEditCmd()) ;
				// Profile table def
				$this->msProfileTableDef = $this->insertTableDef($membership->ProfileTable) ;
				$this->msProfileTableDef->setEditCommand(new PRmtSqlMSProfileEditCmd()) ;
				$this->msProfileTableDef->httpParamName = "ms_profiles" ;
				$this->msIdProfileColDef = $this->msProfileTableDef->insertColumn($membership->IdProfileColumn) ;
				$this->msIdProfileColDef->isKey = true ;
				$this->msIdProfileColDef->httpParamName = "id" ;
				$this->msTitleProfileColDef = $this->msProfileTableDef->insertColumn($membership->TitleProfileColumn) ;
				$this->msTitleProfileColDef->httpParamName = "title" ;
				$this->msTitleProfileColDef->insertSearchParam('title_contains', $db->SqlIndexOf('upper(${column})', 'upper(<self>)').' > 0') ;
				$this->msDescProfileColDef = $this->msProfileTableDef->insertColumn($membership->DescriptionProfileColumn) ;
				$this->msDescProfileColDef->httpParamName = "description" ;
				$this->msEnableProfileColDef = $this->msProfileTableDef->insertColumn($membership->EnableProfileColumn) ;
				$this->msEnableProfileColDef->httpParamName = "enable" ;
				// Role table def
				$this->msRoleTableDef = $this->insertTableDef($membership->RoleTable) ;
				$this->msRoleTableDef->setEditCommand(new PRmtSqlMSRoleEditCmd()) ;
				$this->msRoleTableDef->httpParamName = "ms_roles" ;
				$this->msIdRoleColDef = $this->msRoleTableDef->insertColumn($membership->IdRoleColumn) ;
				$this->msIdRoleColDef->isKey = true ;
				$this->msIdRoleColDef->httpParamName = "id" ;
				$this->msNameRoleColDef = $this->msRoleTableDef->insertColumn($membership->NameRoleColumn) ;
				$this->msNameRoleColDef->httpParamName = "name" ;
				$this->msNameRoleColDef->isSearchable = true ;
				$this->msTitleRoleColDef = $this->msRoleTableDef->insertColumn($membership->TitleRoleColumn) ;
				$this->msTitleRoleColDef->insertSearchParam('title_contains', $db->SqlIndexOf('upper(${column})', 'upper(<self>)').' > 0') ;
				$this->msTitleRoleColDef->httpParamName = "title" ;
				$this->msDescRoleColDef = $this->msRoleTableDef->insertColumn($membership->DescriptionRoleColumn) ;
				$this->msDescRoleColDef->httpParamName = "description" ;
				$this->msEnableRoleColDef = $this->msRoleTableDef->insertColumn($membership->EnableRoleColumn) ;
				$this->msEnableRoleColDef->httpParamName = "enable" ;
				// Privilege table def
				$this->msPrivilegeTableDef = $this->insertTableDef($membership->PrivilegeTable) ;
				$this->msPrivilegeTableDef->editCommandNames = array("update") ;
				$this->msPrivilegeTableDef->httpParamName = "ms_privileges" ;
				$this->msPrivilegeTableDef->selectQueryText = "(select t2.*, t1.".$db->EscapeVariableName($membership->TitleProfileColumn)." profile_title, t3.".$db->EscapeVariableName($membership->TitleRoleColumn)." role_title 
from ".$db->EscapeTableName($membership->PrivilegeTable)." t2 
left join ".$db->EscapeTableName($membership->ProfileTable)." t1 on t1.".$db->EscapeVariableName($membership->ProfilePrivilegeForeignKey)." = t2.".$db->EscapeVariableName($membership->ProfilePrivilegeColumn)."
left join ".$db->EscapeTableName($membership->RoleTable)." t3 on t2.".$db->EscapeVariableName($membership->RolePrivilegeColumn)." = t3.".$db->EscapeVariableName($membership->RolePrivilegeForeignKey).")" ;
				$this->msIdPrivilegeColDef = $this->msPrivilegeTableDef->insertColumn($membership->IdPrivilegeColumn) ;
				$this->msIdPrivilegeColDef->isKey = true ;
				$this->msIdPrivilegeColDef->httpParamName = "id" ;
				$this->msProfilePrivilegeColDef = $this->msPrivilegeTableDef->insertColumn($membership->ProfilePrivilegeColumn) ;
				$this->msProfilePrivilegeColDef->httpParamName = "profile" ;
				$this->msProfilePrivilegeColDef->isSearchable = true ;
				$this->msProfileRoleColDef = $this->msPrivilegeTableDef->insertColumn($membership->RolePrivilegeColumn) ;
				$this->msProfileRoleColDef->httpParamName = "role" ;
				$this->msEnablePrivilegeColDef = $this->msPrivilegeTableDef->insertColumn($membership->EnablePrivilegeColumn) ;
				$this->msEnablePrivilegeColDef->httpParamName = "enable" ;
				$this->msProfileTitlePrivilegeColDef = $this->msPrivilegeTableDef->insertColumn("profile_title") ;
				$this->msProfileTitlePrivilegeColDef->isEditable = false ;
				$this->msProfileTitleRoleColDef = $this->msPrivilegeTableDef->insertColumn("role_title") ;
				$this->msProfileTitleRoleColDef->isEditable = false ;
			}
		}
		
		class PRmtMsgFormatBase
		{
			public function & getInputData(& $method) {
				return new StdClass() ;
			}
			public function & getOutputData(& $method) {
				return $method->getResp() ;
			}
		}
		class PRmtDefaultMsgFormat extends PRmtMsgFormatBase
		{
			public function & getInputData(& $method) {
				$data = new StdClass() ;
				return $data ;
			}
			public function & getOutputData(& $method) {
				return $method->getResp() ;
			}
		}
		
		class PRmtMethodBase
		{
			protected $_methodProvider ;
			protected $_parentSvc ;
			protected $_methodName ;
			protected $_selectedMsgFormatName ;
			protected $_defaultMsgFormat ;
			protected $_selectedMsgFormat ;
			protected $_msgsFormat = array() ;
			public function __construct()
			{
				$this->initConfig() ;
			}
			protected function createDefaultMsgFormat()
			{
				return new PRmtDefaultMsgFormat() ;
			}
			protected function initConfig()
			{
				$this->_defaultMsgFormat = $this->createDefaultMsgFormat() ;
			}
			protected function confirmRespSuccess($result)
			{
				$this->_parentSvc->getResp()->confirmSuccess($result) ;
			}
			protected function confirmRespError($errorCode, $errorMsg, $errorAlias='undefined_alias', $errorLevel='error')
			{
				$this->_parentSvc->getResp()->confirmError($errorCode, $errorMsg, $errorAlias, $errorLevel) ;
			}
			public function respError()
			{
				return $this->_parentSvc->getResp()->errorCode != 0 ;
			}
			public function respSuccess()
			{
				return $this->_parentSvc->getResp()->errorCode == 0 ;
			}
			public function getReqParam($paramName, $defaultValue='')
			{
				return $this->_parentSvc->getReqParam($paramName, $defaultValue) ;
			}
			public function & getResp()
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
			public function setMethodProvider(& $methodProvider)
			{
				$this->_methodProvider = & $methodProvider ;
			}
			public function prepare()
			{
				$this->detectMsgFormat() ;
			}
			protected function insertMsgFormat($name, $msgFormat)
			{
				$this->_msgsFormat[$name] = $msgFormat ;
			}
			protected function getMsgsFormat()
			{
				return $this->_msgsFormat ;
			}
			protected function detectMsgFormat()
			{
				$this->_selectedMsgFormat = & $this->_defaultMsgFormat ;
				$this->_selectedMsgFormatName = $this->getReqParam("msgFormat") ;
				if(isset($this->_msgsFormat[$this->_selectedMsgFormatName]))
				{
					$this->_selectedMsgFormat = & $this->_msgsFormat[$this->_selectedMsgFormatName] ;
				}
				else
				{
					$this->_selectedMsgFormatName = null ;
				}
			}
			public function & getInputData()
			{
				return $this->_selectedMsgFormat->getInputData($this) ;
			}
			public function & getOutputData()
			{
				return $this->_selectedMsgFormat->getOutputData($this) ;
			}
			public function execute()
			{
			}
		}
		class PRmtTestMethod extends PRmtMethodBase
		{
			public function execute()
			{
				$this->getResp()->confirmSuccess("Test succeeded") ;
			}
		}
		class PRmtBadRequestMethod extends PRmtMethodBase
		{
			public function execute()
			{
				$this->getResp()->confirmError(-4, "The request sent cant be treated") ;
			}
		}
		class PRmtNotFoundMethod extends PRmtMethodBase
		{
			public function execute()
			{
				$this->getResp()->confirmError(-2, "The method '".$this->_parentSvc->getActiveMethodName()."' doesnt not exists in this service") ;
			}
		}
		
		class PRmtDbBaseMethod extends PRmtMethodBase
		{
			protected function getDB($name)
			{
				return $this->_methodProvider->getDB($name) ;
			}
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
				$db = $this->getDB($dbName) ;
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
		class PRmtDbRunSqlMethod extends PRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->RunSql($text, $params) ;
			}
		}
		class PRmtDbFetchSqlRowsMethod extends PRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchSqlRows($text, $params) ;
			}
		}
		class PRmtDbFetchSqlRowMethod extends PRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchSqlRow($text, $params) ;
			}
		}
		class PRmtDbRunStoredProcMethod extends PRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->RunStoredProc($text, $params) ;
			}
		}
		class PRmtDbFetchStoredProcRowsMethod extends PRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchStoredProcRows($text, $params) ;
			}
		}
		class PRmtDbFetchStoredProcRowMethod extends PRmtDbBaseMethod
		{
			protected function extractResults(& $db, $text, $params=array())
			{
				return $db->FetchStoredProcRow($text, $params) ;
			}
		}
		
		class PRmtAkMSMethodBase extends PRmtMethodBase
		{
			protected function getMembership()
			{
				return $this->_methodProvider->getMembership() ;
			}
		}
		class PRmtAkLoginMemberMethod extends PRmtAkMSMethodBase
		{
			public function execute()
			{
				$membership = $this->getMembership() ;
				$login = $this->getReqParam("login") ;
				$password = $this->getReqParam("password") ;
				$idMember = $membership->ValidateConnection($login, $password) ;
				if($idMember != $membership->IdMemberNotFoundValue) 
				{
					$this->confirmRespSuccess($idMember) ;
				}
				else
				{
					$this->confirmRespError(1, $membership->LastValidateError, $membership->LastValidateError, 'error') ;
				}
			}
		}
		class PRmtAkGetMemberMethod extends PRmtAkMSMethodBase
        {
            public function execute()
            {
                $membership = $this->getMembership() ;
                $memberId = $this->getReqParam("id") ;
                $member = $membership->FetchMember($memberId) ;
                if(is_object($member))
                {
                    $member->ParentMembership = null ;
                    // print_r($member) ;
                    $this->confirmRespSuccess($member) ;
                }
                else
                {
                    if($membership->Database->ConnectionException == "")
                    {
                        $this->confirmRespError(1, "The member with id ".htmlentities($memberId)." cannot be found", "member_not_exists", "error") ;
                    }
                    else
                    {
                        $this->confirmRespError(-2, $membership->Database->ConnectionException, "database_exception", "exception") ;
                    }
                }
            }
        }
		class PRmtAkChangePasswordMemberMethod extends PRmtAkMSMethodBase
        {
            public function execute()
            {
                $membership = $this->getMembership() ;
                $login = $this->getReqParam("login") ;
                $oldPassword = $this->getReqParam("old_password") ;
                $newPassword = $this->getReqParam("new_password") ;
				$db = $membership->Database ;
				$idMember = $membership->ValidateConnection($login, $oldPassword) ;
				if($idMember != $membership->IdMemberNotFoundValue) 
				{
					$memberRow = array($membership->PasswordMemberColumn => $newPassword) ;
					if($membership->PasswordMemberExpr != '')
					{
						$memberRow[$db->ExprKeyName] = array($membership->PasswordMemberColumn => $membership->PasswordMemberExpr.'(<SELF>)') ;
					}
					$ok = $db->UpdateRow(
						$membership->MemberTable, $memberRow,
						$db->EscapeVariableName($membership->IdMemberColumn).' = :idMember', array("idMember" => $idMember)
					) ;
					// print $db->LastSqlText ;
					if($ok)
					{
						$this->confirmRespSuccess(array("newPassword" => $newPassword)) ;
					}
					else
					{
						$this->confirmRespError(2, $db->ConnectionException, "query_database_failed", "exception") ;
					}
				}
				else
				{
					$this->confirmRespError(1, $membership->LastValidateError, $membership->LastValidateError, 'error') ;
				}            
			}
        }
		
		class PRmtSqlQuery
		{
			public $text ;
			public $params = array() ;
		}
		class PRmtSqlCond
		{
			public $text ;
			public $params = array() ;
		}
		
		class PRmtSqlSelectResult
		{
			public $range = array() ;
			public $total = 0 ;
			public $start = 0 ;
			public $rangeCount = 0 ;
		}
		
		class PRmtSqlFilterBase
		{
			public $httpParamName ;
			public $httpDefaultValue = null ;
			public $nullValue = null ;
			protected $isBound = 0 ;
			protected $value ;
			public $enabled = 1 ;
			public $sqlColumnName ;
			public $sqlColumnAlias ;
			public $sqlExpr ;
			public $mandatory = 0 ;
			public function isEnabled()
			{
				return $this->enabled ;
			}
			public function hasNullValue()
			{
				return ($this->mandatory == 0 && $this->value === $this->nullValue) ? 1 : 0 ;
			}
			public function forSelection()
			{
				return ($this->sqlExpr != '') ? 1 : 0 ;
			}
			public function forEdition()
			{
				return ($this->sqlColumnName != '') ? 1 : 0 ;
			}
			public function getQueryString(& $db)
			{
				return $this->sqlColumnName ;
			}
			public function bind(& $method)
			{
				if($this->isBound == 1)
				{
					return $this->value ;
				}
				$this->value = $method->getReqParam($this->httpParamName, $this->httpDefaultValue) ;
				$this->isBound = 1 ;
				return $this->value ;
			}
			public function unbind()
			{
				$this->value = $this->defaultValue ;
				$this->isBound = 0 ;
			}
			public function getValue()
			{
				return $this->value ;
			}
		}
		class PRmtSqlHttpFilter extends PRmtSqlFilterBase
		{
		}
		class PRmtSqlFixedFilter extends PRmtSqlFilterBase
		{
			public function bind(& $method)
			{
				$this->value = $this->httpDefaultValue ;
			}
		}
		
		class PRmtSqlFilterRuleResult
		{
			protected $errorCode = -1 ;
			protected $errorMsg = "Result rule not initialised" ;
			protected $errorAlias = "rule_not_initialized" ;
			public function isSuccess()
			{
				return $this->errorCode == 0 ;
			}
			public function success()
			{
				return $this->isSuccess() ;
			}
			public function isError()
			{
				return ! $this->isSuccess() ;
			}
			public function error()
			{
				return $this->isError() ;
			}
			public function confirmSuccess()
			{
				$this->errorCode = 0 ;
				$this->errorMsg = "" ;
				$this->errorAlias = "" ;
			}
			public function confirmError($errorMsg, $errorAlias)
			{
				$this->errorCode = 1 ;
				$this->errorMsg = $errorMsg ;
				$this->errorAlias = $errorAlias ;
			}
			public function getErrorMsg()
			{
				return $this->errorMsg ;
			}
			public function getErrorAlias()
			{
				return $this->errorAlias ;
			}
		}
		
		class PRmtSqlFilterRuleBase
		{
			protected $filterNames = array() ;
			protected $filterNameErrors = array() ;
			protected $checkResult = null ;
			protected $errorMsg ;
			protected $errorAlias ;
			public function __construct($filterNames)
			{
				$this->filterNames = $filterNames ;
			}
			public function check(& $method)
			{
				$this->checkResult = new PRmtSqlFilterRuleResult() ;
				$this->filterNameErrors = array() ;
				$filters = $method->getEditFilterGroup($this->filterNames) ;
				$this->checkResult->confirmSuccess() ;
				if(count($filters) > 0)
				{
					foreach($filters as $name => & $flt)
					{
						if(! $this->validateFilter($method, $flt))
						{
							$this->filterNameErrors[] = $name ;
						}
					}
				}
				if(count($this->filterNameErrors) > 0)
				{
					$this->checkResult->confirmError($this->errorMsg, $this->errorAlias) ;
				}
				return $this->checkResult ;
			}
			protected function validateFilter(& $method, & $flt)
			{
				return 0 ;
			}
		}
		class PRmtSqlNotEmptyFilterRule extends PRmtSqlFilterRuleBase
		{
			protected $errorMsg = "fields must not be empty" ;
			protected $errorAlias = "not_empty" ;
			protected function validateFilter(& $method, & $flt)
			{
				return trim($flt->bind()) != '' ;
			}
		}
		class PRmtSqlUniqueFilterRule extends PRmtSqlFilterRuleBase
		{
		}
		class PRmtSqlLoginFilterRule extends PRmtSqlFilterRuleBase
		{
			protected $errorMsg = "wrong login format" ;
			protected $errorAlias = "wrong_login_format" ;
			protected function validateFilter(& $method, & $flt)
			{
				return validate_name_user_format($flt->bind()) ;
			}
		}
		class PRmtSqlPwdFilterRule extends PRmtSqlFilterRuleBase
		{
			protected $errorMsg = "wrong password format" ;
			protected $errorAlias = "wrong_password_format" ;
			protected function validateFilter(& $method, & $flt)
			{
				return validate_password_format($flt->bind()) ;
			}
		}
		class PRmtSqlEmailFilterRule extends PRmtSqlFilterRuleBase
		{
			protected $errorMsg = "wrong email format" ;
			protected $errorAlias = "wrong_email_format" ;
			protected function validateFilter(& $method, & $flt)
			{
				return validate_email_format($flt->bind()) ;
			}
		}
		class PRmtSqlRegexpFilterRule extends PRmtSqlFilterRuleBase
		{
			protected $errorMsg = "wrong format" ;
			protected $errorAlias = "wrong_format" ;
			public $pattern = '/./i' ;
			protected function validateFilter(& $method, & $flt)
			{
				return (preg_match($this->pattern, $flt->bind())) ;
			}
		}
		class PRmtSqlFilepathFilterRule extends PRmtSqlFilterRuleBase
		{
			protected $errorMsg = "wrong file path format" ;
			protected $errorAlias = "wrong_filepath_format" ;
			protected function validateFilter(& $method, & $flt)
			{
				return (validate_file_path_format($flt->bind())) ;
			}
		}
		class PRmtSqlUrlFilterRule extends PRmtSqlFilterRuleBase
		{
			protected $errorMsg = "wrong url format" ;
			protected $errorAlias = "wrong_url_format" ;
			protected function validateFilter(& $method, & $flt)
			{
				return (validate_url_format($flt->bind())) ;
			}
		}
		
		class PRmtSqlSelectColumn
		{
			public $sqlColumnName ;
			public $sqlColumnAlias ;
			public function getQueryAliasString(& $db)
			{
				return ($this->sqlColumnAlias != '') ? $this->sqlColumnAlias : $this->sqlColumnName ;
			}
			public function getQueryString(& $db)
			{
				return $this->getQueryAliasString($db)." ".$db->EscapeVariableName($this->sqlColumnName) ;
			}
			public function isValid()
			{
				return ! empty($this->sqlColumnName) ;
			}
		}
		
		class PRmtSqlCommandBase
		{
			public function apply(& $method)
			{
			}
		}
		class PRmtSqlDetailCommand extends PRmtSqlCommandBase
		{
			public function apply(& $method)
			{
				if($method->hasTableDef())
				{
					$method->getTableDef()->getEditCommand()->applyDetail($method) ;
				}
				else
				{
					$method->applyDetail() ;
				}
			}
		}
		class PRmtSqlInsertCommand extends PRmtSqlCommandBase
		{
			public function apply(& $method)
			{
				if($method->hasTableDef())
				{
					$method->getTableDef()->getEditCommand()->applyInsert($method) ;
				}
				else
				{
					$method->applyInsert() ;
				}
			}
		}
		class PRmtSqlUpdateCommand extends PRmtSqlCommandBase
		{
			public function apply(& $method)
			{
				if($method->hasTableDef())
				{
					$method->getTableDef()->getEditCommand()->applyUpdate($method) ;
				}
				else
				{
					$method->applyUpdate() ;
				}
			}
		}
		class PRmtSqlDeleteCommand extends PRmtSqlCommandBase
		{
			public function apply(& $method)
			{
				if($method->hasTableDef())
				{
					$method->getTableDef()->getEditCommand()->applyDelete($method) ;
				}
				else
				{
					$method->applyDelete() ;
				}
			}
		}
		
		class PRmtSqlMethodBase extends PRmtMethodBase
		{
			protected $tableDef ;
			public function setTableDef(& $tableDef)
			{
				$this->tableDef = & $tableDef ;
			}
			public function & getTableDef()
			{
				return $this->tableDef ;
			}
			public function hasTableDef()
			{
				return $this->tableDef != null ;
			}
			public function & getProviderDb()
			{
				$db = $this->_methodProvider->getDb() ;
				return $db ;
			}
			public function & getDb()
			{
				return $this->getProviderDb() ;
			}
			public function & getMembership()
			{
				$membership = $this->_methodProvider->getMembership() ;
				return $membership ;
			}
			protected function & createSelectFilter($filterName, $sqlExpr='')
			{
				$filter = new PRmtSqlHttpFilter() ;
				$filter->httpParamName = $filterName ;
				$filter->sqlExpr = $sqlExpr ;
				return $filter ;
			}
			protected function & createSelectFixedFilter($filterName, $value, $sqlExpr='')
			{
				$filter = new PRmtSqlFixedFilter() ;
				$filter->httpParamName = $filterName ;
				$filter->defaultValue = $value ;
				$filter->sqlExpr = $sqlExpr ;
				return $filter ;
			}
			protected function & createEditFilter($filterName, $sqlColumnName, $sqlColumnAlias='')
			{
				$filter = new PRmtSqlHttpFilter() ;
				$filter->httpParamName = $filterName ;
				$filter->sqlColumnName = $sqlColumnName ;
				$filter->sqlColumnAlias = $sqlColumnAlias ;
				return $filter ;
			}
			protected function & createEditFixedFilter($filterName, $value, $sqlColumnName, $sqlColumnAlias='')
			{
				$filter = new PRmtSqlFixedFilter() ;
				$filter->httpParamName = $filterName ;
				$filter->defaultValue = $value ;
				$filter->sqlColumnName = $sqlColumnName ;
				$filter->sqlColumnAlias = $sqlColumnAlias ;
				return $filter ;
			}
			protected function getColQueryStringByCols(& $db, $cols)
			{
				$text = '' ;
				foreach($cols as $name => $col)
				{
					if(! $col->isValid())
					{
						continue ;
					}
					if($text != '')
					{
						$text .= ', ' ;
					}
					$text .= $col->getQueryString($db) ;
				}
				return $text ;
			}
			protected function extractQueryCond(& $db, $filters)
			{
				$cond = new PRmtSqlCond() ;
				foreach($filters as $name => & $flt)
				{
					if(! $flt->isEnabled() || ! $flt->forSelection())
					{
						continue ;
					}
					$flt->bind($this) ;
					if($flt->hasNullValue())
					{
						continue ;
					}
					if($cond->text != '')
					{
						$cond->text .= ' and ' ;
					}
					$cond->text .= str_ireplace(array("<self>", "<this>"), $db->ParamPrefix.$name, $flt->sqlExpr) ;
					$cond->params[$name] = $flt->bind($this) ;
				}
				return $cond ;
			}
			protected function extractQueryParamsByFlts(& $db, $filters)
			{
				$params = array() ;
				foreach($filters as $name => & $flt)
				{
					if(! $flt->isEnabled() || ! $flt->forEdition())
					{
						continue ;
					}
					$flt->bind($this) ;
					if($flt->hasNullValue())
					{
						continue ;
					}
					$params[$name] = $flt->bind($this) ;
				}
				return $params ;
			}
			protected function getColQueryStringByFlts(& $db,  $filters)
			{
				$text = '' ;
				foreach($filters as $name => $flt)
				{
					if(! $flt->isEnabled() || ! $flt->forEdition())
					{
						continue ;
					}
					if($text != '')
					{
						$text .= ', ' ;
					}
					$text .= $flt->getQueryString($db) ;
				}
				return $text ;
			}
		}
		
		class PRmtSqlSelectInputData
		{
			public $start ;
			public $max ;
			public $orderCols = array() ;
		}
		class PRmtSqlSelectOrderCol
		{
			public $columnName ;
			public $direction = "asc" ;
		}
		
		class PRmtDatatableOutputData
		{
		}
		
		class PRmtSqlSelectMsgFormat extends PRmtMsgFormatBase
		{
			public function & getInputData(& $method)
			{
				$data = new PRmtSqlSelectInputData() ;
				$data->start = $method->getReqParam("start") ;
				$data->max = $method->getReqParam("max") ;
				return $data ;
			}
			public function & getOutputData(& $method)
			{
				return $method->getResp() ;
			}
		}
		class PRmtDatatableMsgFormat extends PRmtMsgFormatBase
		{
			public function & getInputData(& $method)
			{
				$data = new PRmtSqlSelectInputData() ;
				$data->start = $method->getReqParam("start") ;
				$data->max = $method->getReqParam("length") ;
				return $data ;
			}
			public function & getOutputData(& $method)
			{
				return $method->getResp() ;
				// $outputData $method->getResp() ;
			}
		}
		
		class PRmtSqlSelectMethod extends PRmtSqlMethodBase
		{
			protected $selectColumns = array() ;
			protected $maxItemCount = 40 ;
			public $selectQueryText = "" ;
			public $selectQueryParams = array() ;
			public $selectFilters = array() ;
			protected function createDefaultMsgFormat()
			{
				return new PRmtSqlSelectMsgFormat() ;
			}
			public function getStartItemValue($start, $max, $total)
			{
				$val = intval($start) ;
				if($val >= $total)
				{
					$val = $total ;
				}
				if($val % $max > 0)
				{
					$val = (intval($val / $max)) * $max ;
				}
				return $val ;
			}
			public function getMaxItemValue($start, $max)
			{
				$val = intval($start) ;
				if($val <= 0 || $val >= $this->maxItemCount)
				{
					$val = $this->maxItemCount ;
				}
				return $val ;
			}
			public function & insertSelectColumn($colName, $colAlias='')
			{
				$col = new PRmtSqlSelectColumn() ;
				$col->sqlColumnName = $colName ;
				$col->sqlColumnAlias = $colAlias ;
				$this->selectColumns[] = & $col ;
				return $col ;
			}
			public function & insertSelectCol($colName, $colAlias='')
			{
				return $this->insertSelectColumn($colName, $colAlias) ;
			}
			public function & insertSelectFilter($filterName, $expr='')
			{
				$this->selectFilters[$filterName] = $this->createSelectFilter($filterName, $expr) ;
				return $this->selectFilters[$filterName] ;
			}
			public function & insertFilter($filterName, $expr='')
			{
				return $this->insertSelectFilter($filterName, $expr) ;
			}
			public function execute()
			{
				$db = $this->getProviderDb() ;
				$inputData = $this->getInputData() ;
				if(is_null($db))
				{
					$this->confirmRespError(-1, "Database not exists in the service", "database_config_error", "exception") ;
					return ;
				}
				$colQueryString = $this->getColQueryStringByCols($db, $this->selectColumns) ;
				if($colQueryString == '')
				{
					$this->confirmRespError(-2, "No column specified in the query", "column_config_error", "exception") ;
					return ;
				}
				$query = new PRmtSqlQuery() ;
				$query->text = 'select '.$colQueryString.' from '.$this->selectQueryText.' t1' ;
				$query->params = $this->selectQueryParams ;
				$cond = $this->extractQueryCond($db, $this->selectFilters) ;
				if($cond->text != '')
				{
					$query->text .= ' where '.$cond->text ;
					$query->params = array_merge($query->params, $cond->params) ;
				}
				$result = new PRmtSqlSelectResult() ;
				$result->total = $db->CountSqlRows($query->text, $query->params) ;
				$count = $this->getMaxItemValue($inputData->max, $result->total) ;
				$start = $this->getStartItemValue($inputData->start, $count, $result->total) ;
				$result->start = $start ;
				if($result->total !== null)
				{ 
					if($result->total > 0)
					{
						$result->range = $db->LimitSqlRows($query->text, $query->params, $start, $count) ;
						$result->rangeCount = intval($result->total / $count) + (($result->total % $count > 0) ? 1 : 0) ;
					}
					else
					{
						$result->range = array() ;
						$result->rangeCount = 0 ;
					}
					$this->confirmRespSuccess($result) ;
				}
				else
				{
					$this->confirmRespError("4", $db->ConnectionException, "exception", "database_exception") ;
				}
			}
		}
		class PRmtSqlEditMethod extends PRmtSqlMethodBase
		{
			protected $editFilters = array() ;
			protected $selectedCommandName ;
			public $editCommandNames = array() ;
			public $editTableName = "" ;
			protected $selectFilters = array() ;
			public $selectQueryText = "" ;
			public $selectQueryParams = array() ;
			protected $editRules = array() ;
			public function & insertSelectFilter($filterName, $expr='')
			{
				$this->selectFilters[$filterName] = $this->createSelectFilter($filterName, $expr) ;
				$this->selectFilters[$filterName]->mandatory = true ;
				return $this->selectFilters[$filterName] ;
			}
			public function & insertFilter($filterName, $expr='')
			{
				return $this->insertSelectFilter($filterName, $expr) ;
			}
			protected function getEditCommandNames()
			{
				$cmdNames = $this->editCommandNames ;
				if($this->hasTableDef())
				{
					array_splice($cmdNames, count($cmdNames), 0, $this->tableDef->editCommandNames) ;
				}
				if(count($cmdNames) == 0)
				{
					$cmdNames = $this->_methodProvider->getEditCommandNames() ;
				}
				return $cmdNames ;
			}
			public function getEditTableName()
			{
				return ($this->editTableName != '') ? $this->editTableName : $this->selectQueryText ;
			}
			public function & insertEditFilter($httpParamName, $colName, $colAlias='')
			{
				$flt = new PRmtSqlHttpFilter() ;
				$flt->httpParamName = $httpParamName ;
				$flt->sqlColumnName = $colName ;
				$flt->sqlColumnAlias = $colAlias ;
				$this->editFilters[$colName] = & $flt ;
				return $flt ;
			}
			public function & insertEditFixedFilter($httpParamName, $colName, $value, $colAlias='')
			{
				$flt = new PRmtSqlFixedFilter() ;
				$flt->httpParamName = $httpParamName ;
				$flt->sqlColumnName = $colName ;
				$flt->sqlColumnAlias = $colAlias ;
				$flt->defaultValue = $value ;
				$this->editFilters[$colName] = & $flt ;
				return $flt ;
			}
			public function getEditFilters()
			{
				return $this->editFilters ;
			}
			public function getEditFilter($name)
			{
				return (isset($this->editFilters[$name])) ? $this->editFilters[$name] : null ;
			}
			public function & getEditFilterGroup($names)
			{
				$filters = array() ;
				foreach($names as $i => $name)
				{
					if(isset($this->editFilters[$name]))
					{
						$filters[$name] = & $this->editFilters[$name] ;
					}
				}
				return $filters ;
			}
			public function execute()
			{
				$cmdName = $this->getReqParam("command") ;
				$allowedCmdNames = $this->getEditCommandNames() ;
				if(count($allowedCmdNames) == 0)
				{
					$this->confirmRespError(1, "no command specified for the method", "no_command_specified", "exception") ;
				}
				if(! in_array($cmdName, $allowedCmdNames))
				{
					$cmdName = $allowedCmdNames[0] ;
				}
				$cmd = $this->_methodProvider->getEditCommand($cmdName) ;
				$cmd->apply($this) ;
			}
			public function applyDetail()
			{
				$db = $this->getProviderDb() ;
				if(is_null($db))
				{
					$this->confirmRespError(-1, "Database not exists in the service", "database_config_error", "exception") ;
					return ;
				}
				$colQueryString = $this->getColQueryStringByFlts($db, $this->editFilters) ;
				if($colQueryString == '')
				{
					$this->confirmRespError(-1, "No column specified in the query", "column_config_error", "exception") ;
					return ;
				}
				$query = new PRmtSqlQuery() ;
				$query->text = 'select '.$colQueryString.' from '.$this->selectQueryText.' t1' ;
				$query->params = $this->selectQueryParams ;
				$cond = $this->extractQueryCond($db, $this->selectFilters) ;
				if($cond->text != '')
				{
					$query->text .= ' where '.$cond->text ;
					$query->params = array_merge($query->params, $cond->params) ;
				}
				$result = $db->FetchSqlRow($query->text, $query->params) ;
				// print_r($query) ;
				if(is_array($result) && count($result) == 0)
				{
					$result = new StdClass() ;
				}
				if(is_object($result) || is_array($result))
				{
					$this->confirmRespSuccess($result) ;
				}
				else
				{
					$this->confirmRespError(2, $db->ConnectionException, "database_exception", "exception") ;
				}
			}
			public function applyInsert()
			{
				$db = $this->getProviderDb() ;
				if(is_null($db))
				{
					$this->confirmRespError(-1, "Database not exists in the service", "database_config_error", "exception") ;
					return ;
				}
				if(! $this->checkRules())
				{
					return ;
				}
				$queryParams = $this->extractQueryParamsByFlts($db, $this->editFilters) ;
				$ok = $db->InsertRow($this->getEditTableName(), $queryParams) ;
				if($ok)
				{
					$this->confirmRespSuccess(1) ;
				}
				else
				{
					$this->confirmRespError(2, $db->ConnectionException, "database_exception", "exception") ;
				}
			}
			public function applyUpdate()
			{
				$db = $this->getProviderDb() ;
				if(is_null($db))
				{
					$this->confirmRespError(-1, "Database not exists in the service", "database_config_error", "exception") ;
					return ;
				}
				if(! $this->checkRules())
				{
					return ;
				}
				$queryParams = $this->extractQueryParamsByFlts($db, $this->editFilters) ;
				$cond = $this->extractQueryCond($db, $this->selectFilters) ;
				$ok = $db->UpdateRow($this->getEditTableName(), $queryParams, $cond->text, $cond->params) ;
				if($ok)
				{
					$this->confirmRespSuccess(1) ;
				}
				else
				{
					$this->confirmRespError(2, $db->ConnectionException, "database_exception", "exception") ;
				}
			}
			public function applyDelete()
			{
				$db = $this->getProviderDb() ;
				if(is_null($db))
				{
					$this->confirmRespError(-1, "Database not exists in the service", "database_config_error", "exception") ;
					return ;
				}
				$cond = $this->extractQueryCond($db, $this->selectFilters) ;
				$ok = $db->DeleteRow($this->getEditTableName(), $cond->text, $cond->params) ;
				if($ok)
				{
					$this->confirmRespSuccess(1) ;
				}
				else
				{
					$this->confirmRespError(2, $db->ConnectionException, "database_exception", "exception") ;
				}
			}
			public function & insertEditRule($rule)
			{
				$this->editRules[] = & $rule ;
				return $rule ;
			}
			public function & insertLoginRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlLoginFilterRule($filterNames)) ;
			}
			public function & insertPasswordRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlPwdFilterRule($filterNames)) ;
			}
			public function & insertPwdRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlPwdFilterRule($filterNames)) ;
			}
			public function & insertUrlRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlUrlFilterRule($filterNames)) ;
			}
			public function & insertFilepathRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlFilepathFilterRule($filterNames)) ;
			}
			public function & insertEmailRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlEmailFilterRule($filterNames)) ;
			}
			public function & insertRegexpRule($filterNames, $pattern)
			{
				$rule = PRmtSqlRegexpFilterRule($filterNames) ;
				$rule->pattern = $pattern ;
				return $this->insertEditRule($rule) ;
			}
			public function & getEditRules()
			{
				$rules = $this->editRules ;
				if(! is_null($this->tableDef))
				{
					array_splice($rules, count($rules), 0, $this->tableDef->getEditRules()) ;
				}
				return $rules ;
			}
			protected function checkRules()
			{
				$this->confirmRespSuccess(1) ;
				$rules = $this->getEditRules() ;
				foreach($rules as $i => & $rule)
				{
					$result = $rule->check($this) ;
					if($result->isError())
					{
						$this->confirmRespError(1, $result->getErrorMsg(), $result->getErrorAlias(), "error") ;
					}
				}
				return $this->respSuccess() ;
			}
		}
		class PRmtSqlLoginMemberMethod extends PRmtSqlMethodBase
		{
			public function execute()
			{
				$membership = $this->getMembership() ;
				$login = $this->getReqParam("login") ;
				$password = $this->getReqParam("password") ;
				$idMember = $membership->ValidateConnection($login, $password) ;
				if($idMember != $membership->IdMemberNotFoundValue) 
				{
					$this->confirmRespSuccess($idMember) ;
				}
				else
				{
					$this->confirmRespError(1, $membership->LastValidateError, $membership->LastValidateError, 'error') ;
				}
			}
		}
		class PRmtSqlChangePasswordMemberMethod extends PRmtSqlMethodBase
        {
            public function execute()
            {
                $membership = $this->getMembership() ;
                $login = $this->getReqParam("login") ;
                $oldPassword = $this->getReqParam("old_password") ;
                $newPassword = $this->getReqParam("new_password") ;
				$db = $membership->Database ;
				$idMember = $membership->ValidateConnection($login, $oldPassword) ;
				if($idMember != $membership->IdMemberNotFoundValue) 
				{
					$memberRow = array($membership->PasswordMemberColumn => $newPassword) ;
					if($membership->PasswordMemberExpr != '')
					{
						$memberRow[$db->ExprKeyName] = array($membership->PasswordMemberColumn => $membership->PasswordMemberExpr.'(<SELF>)') ;
					}
					$ok = $db->UpdateRow(
						$membership->MemberTable, $memberRow,
						$db->EscapeVariableName($membership->IdMemberColumn).' = :idMember', array("idMember" => $idMember)
					) ;
					// print $db->LastSqlText ;
					if($ok)
					{
						$this->confirmRespSuccess(array("newPassword" => $newPassword)) ;
					}
					else
					{
						$this->confirmRespError(2, $db->ConnectionException, "query_database_failed", "exception") ;
					}
				}
				else
				{
					$this->confirmRespError(1, $membership->LastValidateError, $membership->LastValidateError, 'error') ;
				}            
			}
        }
	
		class PRmtSqlTableDef
		{
			protected $columns = array() ;
			public $httpParamName = "" ;
			public $editTableName = "" ;
			public $selectQueryText = "" ;
			public $selectQueryParams = array() ;
			protected $editMethod ;
			protected $selectMethod ;
			protected $editCommand ;
			public $editCommandNames = array() ;
			protected $editRules = array() ;
			public function __construct()
			{
				$this->editCommand = new PRmtSqlTableEditCmdBase() ;
			}
			public function & getEditCommand()
			{
				return $this->editCommand ;
			}
			public function setEditCommand($cmd)
			{
				$this->editCommand = & $cmd ;
				$cmd->setTableDef($this) ;
			}
			public function getHttpParamName()
			{
				return ($this->httpParamName == "") ? $this->editTableName : $this->httpParamName ;
			}
			protected function parseColumnExpr(& $db, & $col, $expr)
			{
				$res = $expr ;
				$res = str_ireplace('${column}', $db->EscapeVariableName($col->columnName), $res) ;
				return $res ;
			}
			public function & insertColumn($colname, $isSearchable=false, $isSelectable=true, $isKey=false, $isEditable=true)
			{
				$col = new PRmtSqlColumnDef() ;
				$col->columnName = $colname ;
				$col->httpParamName = $colname ;
				// $col->searchExpr = '<self> = :<self>' ;
				$col->isSearchable = $isSearchable ;
				$col->isSelectable = $isSelectable ;
				$col->isEditable = $isEditable ;
				$this->columns[$colname] = & $col ;
				return $col ;
			}
			public function & insertCol($colname, $isSearchable=true, $isSelectable=true, $isEditable=true)
			{
				return $this->insertColumn($colname, $isSearchable, $isSelectable, $isEditable) ;
			}
			public function removeColumn($colName)
			{
				unset($this->columns[$colName]) ;
			}
			public function removeCol($colName)
			{
				$this->removeColumn($colName) ;
			}
			public function getColumns()
			{
				return $this->columns ;
			}
			protected function installSelectMethod(& $methodProvider)
			{
				$db = $methodProvider->getDb() ;
				$this->selectMethod = $methodProvider->installSelectMethod($this->getHttpParamName().".select") ;
				$this->selectMethod->setTableDef($this) ;
				$this->selectMethod->selectQueryText = $this->selectQueryText ;
				$this->selectMethod->selectQueryParams = $this->selectQueryParams ;
				foreach($this->columns as $name => $col)
				{
					if($col->enabled && $col->isSelectable)
					{
						$selectCol = $this->selectMethod->insertSelectColumn($col->columnName) ;
						$selectCol->sqlColumnAlias = $this->parseColumnExpr($db, $col, $col->selectAlias) ;
					}
					if($col->enabled && $col->isSearchable)
					{
						$selectColParams = $col->getSearchParams() ;
						foreach($selectColParams as $j => $colParam)
						{
							$expr = $this->parseColumnExpr($db, $col, $colParam->expr) ;
							$selectFlt = $this->selectMethod->insertSelectFilter($colParam->paramName, $expr) ;
						}
					}
				}
			}
			protected function installEditMethod(& $methodProvider)
			{
				$db = $methodProvider->getDb() ;
				$this->editMethod = $methodProvider->installEditMethod($this->getHttpParamName().".edit") ;
				$this->editMethod->setTableDef($this) ;
				/*
				$this->editMethod->selectQueryText = $this->selectQueryText ;
				$this->editMethod->selectQueryParams = $this->selectQueryParams ;
				*/
				$this->editMethod->selectQueryText = $this->editTableName ;
				$this->editMethod->editTableName = $this->editTableName ;
				foreach($this->columns as $name => $col)
				{
					if($col->enabled && $col->isKey)
					{
						$expr = $this->parseColumnExpr($db, $col, $col->keyExpr) ;
						$selectFlt = $this->editMethod->insertSelectFilter("key_".$col->getHttpParamName(), $expr) ;
						$selectFlt->mandatory = 1 ;
					}
					if($col->enabled && $col->isEditable)
					{
						$expr = $this->parseColumnExpr($db, $col, $col->editAlias) ;
						$editFlt = $this->editMethod->insertEditFilter($col->getHttpParamName(), $col->columnName) ;
						$editFlt->sqlColumnAlias = $col->editAlias ;
					}
				}
			}
			public function installMethods(& $methodProvider)
			{
				$this->installSelectMethod($methodProvider) ;
				$this->installEditMethod($methodProvider) ;
			}
			public function & insertEditRule($rule)
			{
				$this->editRules[] = & $rule ;
				return $rule ;
			}
			public function & insertLoginRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlLoginFilterRule($filterNames)) ;
			}
			public function & insertPasswordRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlPwdFilterRule($filterNames)) ;
			}
			public function & insertPwdRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlPwdFilterRule($filterNames)) ;
			}
			public function & insertUrlRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlUrlFilterRule($filterNames)) ;
			}
			public function & insertFilepathRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlFilepathFilterRule($filterNames)) ;
			}
			public function & insertEmailRule($filterNames)
			{
				return $this->insertEditRule(new PRmtSqlEmailFilterRule($filterNames)) ;
			}
			public function & insertRegexpRule($filterNames, $pattern)
			{
				$rule = PRmtSqlRegexpFilterRule($filterNames) ;
				$rule->pattern = $pattern ;
				return $this->insertEditRule($rule) ;
			}
			public function & getEditRules()
			{
				return $this->editRules ;
			}
		}
		
		class PRmtSqlTableEditCmdBase
		{
			protected $tableDef ;
			public function setTableDef(& $tableDef)
			{
				$this->tableDef = & $tableDef ;
			}
			public function applyDetail(& $method)
			{
				$method->applyDetail() ;
			}
			public function applyInsert(& $method)
			{
				$method->applyInsert() ;
			}
			public function applyUpdate(& $method)
			{
				$method->applyUpdate() ;
			}
			public function applyDelete(& $method)
			{
				$method->applyDelete() ;
			}
		}
		class PRmtSqlMSMemberEditCmd extends PRmtSqlTableEditCmdBase
		{
			public function applyDetail(& $method)
			{
				parent::ApplyInsert($method) ;
				if($method->respSuccess())
				{
					$db = $method->getDb() ;
					$membership = $method->getMembership() ;
					$resp = $this->getResp() ;
					if(count($resp->result) > 0)
					{
						$sql = "select t2.".$db->EscapeVariableName($membership->TitleProfileColumn)." PROFILE_TITLE, t3.".$db->EscapeVariableName($membership->IdRoleColumn)." ROLE_ID, t3.".$db->EscapeVariableName($membership->TitleRoleColumn)." ROLE_TITLE, t3.".$db->EscapeVariableName($membership->NameRoleColumn)." ROLE_NAME, ".$db->EscapeVariableName($membership->EnablePrivilegeColumn)." ENABLE_PRIVILEGE from ".$db->EscapeTableName($membership->PrivilegeTable)." t1
left join ".$db->EscapeTableName($membership->ProfileTable)." t2 on t2.".$db->EscapeVariableName($membership->ProfilePrivilegeForeignKey)." = t1.".$db->EscapeVariableName($membership->ProfilePrivilegeColumn)."
left join ".$db->EscapeTableName($membership->RoleTable)." t3 on t3.".$db->EscapeVariableName($membership->RolePrivilegeForeignKey)." = t1.".$db->EscapeVariableName($membership->RolePrivilegeColumn)." where t2.".$db->EscapeVariableName().":profileId" ;
						$rows = $db->FetchSqlRows($sql, array("profileId" => $resp->result[$membership->ProfileMemberColumn])) ;
						$resp->result["PRIVILEGES"] = array() ;
						$resp->result["PROFILE_TITLE"] = "" ;
						if(count($rows) > 0)
						{
							$resp->result["PROFILE_TITLE"] = $rows[0]["PROFILE_TITLE"] ;
							foreach($rows as $i => $row)
							{
								$resp->result["PRIVILEGES"][$row["ROLE_NAME"]] = $row ;
							}
						}
					}
				}
			}
		}
		class PRmtSqlMSProfileEditCmd extends PRmtSqlTableEditCmdBase
		{
			public function applyInsert(& $method)
			{
				parent::ApplyInsert($method) ;
				if($method->respSuccess())
				{
					$db = $method->getDb() ;
					$membership = $method->getMembership() ;
					$sql = "insert into ".$db->EscapeTableName($membership->PrivilegeTable)." (".$db->EscapeVariableName($membership->ProfilePrivilegeColumn).", ".$db->EscapeVariableName($membership->RolePrivilegeColumn).", ".$db->EscapeVariableName($membership->EnablePrivilegeColumn).")
select distinct t1.".$db->EscapeVariableName($membership->ProfilePrivilegeForeignKey).",
	t0.".$db->EscapeVariableName($membership->RolePrivilegeForeignKey).",
	".$membership->EnablePrivilegeFalseValue()."
from (select t11.*, 1 link1 from ".$db->EscapeTableName($membership->ProfileTable)." t11) t1
inner join (select t21.*, 1 link1 from ".$db->EscapeTableName($membership->RoleTable)." t21) t0 on t1.link1 = t0.link1
left join ".$db->EscapeTableName($membership->PrivilegeTable)." t2 on t1.".$db->EscapeVariableName($membership->ProfilePrivilegeForeignKey)." = t2.".$db->EscapeVariableName($membership->ProfilePrivilegeColumn)."
left join ".$db->EscapeTableName($membership->RoleTable)." t3 on t2.".$db->EscapeVariableName($membership->RolePrivilegeColumn)." = t3.".$db->EscapeVariableName($membership->RolePrivilegeForeignKey)."
where t3.".$db->EscapeVariableName($membership->RolePrivilegeForeignKey)." is null" ;
					$db->RunSql($sql) ;
				}
			}
			public function applyDelete(& $method)
			{
				parent::ApplyDelete($method) ;
				if($method->respSuccess())
				{
					$db = $method->getDb() ;
					$membership = $method->getMembership() ;
					$id = $method->getReqParam("key_id") ;
					$db->DeleteRow($membership->PrivilegeTable, $db->EscapeVariableName($membership->ProfilePrivilegeColumn)."=:id", array("id" => $id)) ;
				}
			}
		}
		class PRmtSqlMSRoleEditCmd extends PRmtSqlTableEditCmdBase
		{
			public function applyInsert(& $method)
			{
				parent::ApplyInsert($method) ;
				if($method->respSuccess())
				{
					$db = $method->getDb() ;
					$membership = $method->getMembership() ;
					$sql = "insert into ".$db->EscapeTableName($membership->PrivilegeTable)." (".$db->EscapeVariableName($membership->RolePrivilegeColumn).", ".$db->EscapeVariableName($membership->ProfilePrivilegeColumn).", ".$db->EscapeVariableName($membership->EnablePrivilegeColumn).")
select distinct t1.".$db->EscapeVariableName($membership->RolePrivilegeForeignKey).",
	t0.".$db->EscapeVariableName($membership->ProfilePrivilegeForeignKey).",
	".$membership->EnablePrivilegeFalseValue()."
from (select t11.*, 1 link1 from ".$db->EscapeTableName($membership->RoleTable)." t11) t1
inner join (select t21.*, 1 link1 from ".$db->EscapeTableName($membership->ProfileTable)." t21) t0 on t1.link1 = t0.link1
left join ".$db->EscapeTableName($membership->PrivilegeTable)." t2 on t1.".$db->EscapeVariableName($membership->RolePrivilegeForeignKey)." = t2.".$db->EscapeVariableName($membership->RolePrivilegeColumn)."
left join ".$db->EscapeTableName($membership->ProfileTable)." t3 on t2.".$db->EscapeVariableName($membership->ProfilePrivilegeColumn)." = t3.".$db->EscapeVariableName($membership->ProfilePrivilegeForeignKey)."
where t3.".$db->EscapeVariableName($membership->ProfilePrivilegeForeignKey)." is null" ;
					$db->RunSql($sql) ;
				}
			}
			public function applyDelete(& $method)
			{
				parent::ApplyDelete($method) ;
				if($method->respSuccess())
				{
					$db = $method->getDb() ;
					$membership = $method->getMembership() ;
					$id = $method->getReqParam("key_id") ;
					$db->DeleteRow($membership->PrivilegeTable, $db->EscapeVariableName($membership->RolePrivilegeColumn)."=:id", array("id" => $id)) ;
				}
			}
		}
		
		class PRmtSqlColumnDef
		{
			public $isSearchable = false ;
			public $httpParamName = "" ;
			public $columnName = "" ;
			public $searchParams = array() ;
			public $isSelectable = true ;
			public $selectAlias = '${column}' ;
			public $isEditable = true ;
			public $editAlias = '${column}' ;
			public $isKey = false ;
			public $keyExpr = '${column} = <self>' ;
			public $enabled = true ;
			public $autoSearch = true ;
			public function getHttpParamName()
			{
				return ($this->httpParamName != "") ? $this->httpParamName : $this->columnName ;
			}
			public function & insertSearchParam($paramName, $expr, $defaultValue='')
			{
				if(! $this->isSearchable)
				{
					$this->isSearchable = true ;
					$this->autoSearch = false ;
				}
				$param = new PRmtSqlColumnSearchParamDef() ;
				$param->expr = $expr ;
				$param->paramName = $paramName ;
				$param->defaultValue = $defaultValue ;
				$this->searchParams[$paramName] = $param ;
				return $param ;
			}
			public function removeSearchParam($paramName)
			{
				if(! array_key_exists($paramName, $this->searchParams))
				{
					return ;
				}
				unset($this->searchParams[$paramName]) ;
			}
			public function getSearchParams()
			{
				$params = $this->searchParams ;
				if($this->autoSearch)
				{
					$selfParam = new PRmtSqlColumnSearchParamDef() ;
					$selfParam->paramName = $this->httpParamName ;
					$selfParam->expr = '${column} = <self>' ;
					$params[] = $selfParam ;
				}
				return $params ;
			}
		}
		class PRmtSqlColumnSearchParamDef
		{
			public $paramName ;
			public $expr ;
			public $defaultValue = false ;
		}
	}
	
?>