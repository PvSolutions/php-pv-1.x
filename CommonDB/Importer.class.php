<?php
	
	if(! defined('COMMON_IMPORTER_DB_INCLUDED'))
	{
		define("COMMON_IMPORTER_DB_INCLUDED", 1) ;
		
		if(! defined('COMMON_BASE_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonBase.class.php" ;
		}
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/Base.class.php" ;
		}
		
		class CommonDBList extends CommonRootList
		{
			public $AcceptedItemClassName = "AbstractSqlDB" ;
		}
		class ImporterDB
		{
			public static function ExtractDatabasesFromNode(&$app, &$databasesNode)
			{
				if($app->DBList == null)
					$app->DBList = new CommonDBList($app) ;
				else
					$app->DBList->Clear() ;
				          $app->DBList->ImportConfigFromNode($databasesNode) ;
				if(! isset($databasesNode["child"]))
					return ;
				for($i=0; $i<count($databasesNode["child"]); $i++)
				{
					$databaseNode = &$databasesNode["child"][$i] ;
					ImporterDB::ExtractDatabaseFromNode($app, $databaseNode) ;
				}
			}
			public static function CreateDatabaseFromNode(&$databaseNode)
			{
				$db = null ;
				if(! isset($databaseNode["attrs"]))
					return $db ;
				$databaseName = _value_def($databaseNode["attrs"], "NAME") ;
				if(empty($databaseName))
					return $db ;
				$dbClassName = _value_def($databaseNode["attrs"], "CLASSNAME", "MysqlDB") ;
				if(! class_exists($dbClassName))
					return $db ;
				$db = new $dbClassName() ;
				$db->ConnectionParams["server"] = _value_def($databaseNode["attrs"], "SERVER", "") ;
				$db->ConnectionParams["user"] = _value_def($databaseNode["attrs"], "USERNAME", "") ;
				$db->ConnectionParams["password"] = _value_def($databaseNode["attrs"], "PASSWORD", "") ;
				$db->ConnectionParams["schema"] = _value_def($databaseNode["attrs"], "SCHEMA", "") ;
				$db->AutoCloseConnection = (intval(_value_def($databaseNode["attrs"], "AUTOCLOSECONNECTION", ""))) ? true : false ;
				$db->EnableSqlProfiler = (intval(_value_def($databaseNode["attrs"], "ENABLESQLPROFILER", ""))) ? true : false ;
				$db->SqlProfilerOutputFile = _value_def($databaseNode["attrs"], "SQLPROFILEROUTPUTFILE", "") ;
				$db->ImportConfigFromNode($databaseNode) ;
				/*
				if(isset($db->TnsConnectDataParams))
				{
					print_r($db->TnsConnectDataParams) ;
				}
				*/
				return $db ;
			}
			public static function ExtractDatabaseFromNode(&$app, &$databaseNode)
			{
				$db = ImporterDB::CreateDatabaseFromNode($databaseNode) ;
				if($db == null)
					return ;
				$asDefault = (intval(_value_def($databaseNode["attrs"], "ASDEFAULT", ""))) ? true : false ;
				if($asDefault)
				{
					$app->MainDB = &$db ;
				}
				$databaseName = _value_def($databaseNode["attrs"], "NAME") ;
				if(empty($databaseName))
					return ;
				$app->DBList->Add($databaseName, $db) ;
			}
		}
	}
	
?>