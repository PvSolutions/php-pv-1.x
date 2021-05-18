<?php

	if(! defined("COMMON_DB_INCLUDED"))
	{
		if(! defined('COMMON_ENCODING_SET_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/EncodingSet.class.php" ;
		}
		define('COMMON_DB_INCLUDED', 1) ;
		
		class AbstractSqlVariableDefinition
		{
			public $Name = "" ;
			public $Type = "" ;
			public $MaxLength = 0 ;
			public $DefaultValue = "" ;
			public function ImportConfigFromRow($row)
			{
				foreach($row as $name => $value)
				{
					$this->ImportConfigFromRowValue($name, $value) ;
				}
			}
			public function ImportConfigFromRowValue($name, $value)
			{
				return 0 ;
			}
		}
		class AbstractSqlColumnDefinition extends AbstractSqlVariableDefinition
		{
			public $IsKey = 0 ;
			public $IsNull = 0 ;
		}
		class AbstractSqlTableDefinition extends AbstractSqlVariableDefinition
		{
			public $Schema = "" ;
			public function ImportConfigFromRowValue($name, $value)
			{
				$success = parent::ImportConfigFromRowValue($name, $value) ;
				if($success)
					return 1 ;
				$success = 1 ;
				switch(strtoupper($name))
				{
					case "NAME" :
					{
						$this->Name = $value ;
					}
					break ;
					default :
					{
						$success = 0 ;
					}
					break ;
				}
				return $success ;
			}
		}
		
		class MysqlColumnDefinition extends AbstractSqlColumnDefinition
		{
			public function ImportConfigFromRow($row)
			{
				parent::ImportConfigFromRow($row) ;
				if(isset($row["Type"]))
				{
					$type_attrs = explode("(", $row["Type"]) ;
					if(isset($type_attrs[1]))
					{
						$type_attrs[1] = str_replace(")", '', $type_attrs[1]) ;
						$type_attrs[1] = -1 ;
					}
					else
					{
						$type_attrs[1] = -1 ;
					}
					$this->Type = $type_attrs[0] ;
					$this->MaxLength = $type_attrs[1] ;
					$this->Name = $row["Field"] ;
					$this->IsNull = ($row['Null'] == 'YES') ? 1 : 0 ;
					$this->IsKey = ($row['Key'] == 'PRI') ? 1 : 0 ;
					$this->DefaultValue = $row["Default"] ;
					$this->Extra = $row["Extra"] ;
				}
			}
		}
		class OciColumnDefinition extends AbstractSqlColumnDefinition
		{
			public function ImportConfigFromRow($row)
			{
				parent::ImportConfigFromRow($row) ;
				if(isset($row["COLUMN_NAME"]))
				{
					$this->Name = $row["COLUMN_NAME"] ;
					$this->IsNull = (($row['NULLABLE'] == 'Y') ? 1 : 0) ? 1 : 0 ;
					$this->DefaultValue = $row["DATA_DEFAULT"] ;
					$this->MaxLength = $row["DATA_LENGTH"] ;
					$this->Type = $row["DATA_TYPE"] ;
					$this->IsKey = $row["IS_KEY"] ;
				}
			}
		}
		
		class MysqlTableDefinition extends AbstractSqlTableDefinition
		{
		}
		class OciTableDefinition extends AbstractSqlTableDefinition
		{
			public function ImportConfigFromRowValue($name, $value)
			{
				$success = parent::ImportConfigFromRowValue($name, $value) ;
				if($success)
					return 1 ;
				$success = 1 ;
				switch(strtoupper($name))
				{
					case "TABLE_NAME" :
					{
						$this->Name = $value ;
					}
					break ;
					default :
					{
						$success = 0 ;
					}
					break ;
				}
				return $success ;
			}
		}
		
		/**
		* Abstract Database Layer
		*
		* @author  Alhassane Abdel KEITA <lebdenat@yahoo.fr>
		* @license http://www.opensource.org/licenses/mit-license.php MITE
		* @version 1.0.0.0
		*
		* Supply a robust Sql Database layer class definition for inherit.                                                             
		* This script contains database classes
		* for popular databases, such as MySQL
		* ORACLE, ODBC 
		*/
		class AbstractSqlDB
		{
		/**
		* mixed Connection Resource ID, depends on the database type.
		* @var mixed $Connection
		*/
			var $Connection = false ;
		/**
		* Array of Connection Parameters. The keys are : user, password, server, schema.
		* @var array $ConnectionParams
		*/
			var $ConnectionParams = array() ;
		/**
		* Array of Field strutures. The keys are the name of the tables.
		* @var array $FieldsCache
		*/
			var $FieldsCache = array() ;
		/**
		* Close automatically the connection when a query has been closed. Use them if you want to run one query by connection request.
		* @var bool $AutoCloseConnection
		*/
			var $AutoCloseConnection = true ;
		/**
		* Connection Exception Message.
		* @var string $ConnectionException
		*/
			var $ConnectionException = "" ;
		/**
		* Prefix for parameters.
		* @var string $ParamPrefix
		*/
			var $ParamPrefix = ":" ;
		/**
		* Pattern for parameters name, used to identify them into sql string
		* @var string $ParamPattern
		*/
			var $ParamPatternName = "[0-9a-z\_]" ;
		/**
		* Prefix used for creating parameter values used for inserting or updating rows in a table.
		* @var string $NewValuePrefix
		*/
			var $NewValuePrefix = 'NEW' ;
		/**
		 Clé du tableau qui gère les expressions dans le cas des
		 insertions et des mises à jour.
		 @var string $ExprKeyName
		*/
			var $ExprKeyName = "__EXPRS" ;
		/**
		 Modèle qui correspond au nom de la colonne dans chaque expression
		 @var string $ExprKeyName
		*/
			var $ExprParamPattern = "<SELF>" ;
		/**
		* Enable Sql Profiler for the capture of different sqls executed.
		* @var bool $EnableSqlProfiler
		*/
			var $EnableSqlProfiler = false ;
		/**
		* The file path where the differents sqls captured has been saved. You must leave it blank if you want to store it into a variable (more slow when sql number increase).
		* @var string $SqlProfilerOutputFile
		*/
			var $SqlProfilerOutputFile = '' ;
		/**
		* String variable which store different sqls captured when no file output has been set.
		* @var string $SqlProfilerOutput
		*/
			var $SqlProfilerOutput = '' ;
		/**
		* Error string when an exception has been raised during storing the sql captured.
		* @var string $SqlProfilerError
		*/
			var $SqlProfilerError = '' ;
		/**
		* Connection string when an exception has been raised during storing the sql captured.
		* @var array $LastConnectionString
		*/
			var $LastConnectionString = "" ;
		/**
		* Error string when an exception has been raised during storing the sql captured.
		* @var array $LastSqlText
		*/
			var $LastSqlText = "" ;
		/**
		* Error string when an exception has been raised during storing the sql captured.
		* @var array $LastSqlParams
		*/
			var $LastSqlParams = array() ;
		/**
		* Nom de la marque de la base de donnees supportee
		* @var string $VendorName
		*/
			var $VendorName = "" ;
		/**
		* Nom de la version minimum de marque de la base de donnees supportee.
		* @var string $VendorMinVersion
		*/
			var $VendorMinVersion = "" ;
		/**
		* Nom de la version maximum de marque de la base de donnees supportee.
		* @var string $VendorMaxVersion
		*/
			var $VendorMaxVersion = "" ;
			
			var $AutoUpperTableName = 1 ;
			
			var $StoredProcUseCursor = 1 ;
			
			var $AutoSetCharacterEncoding = 0 ;
			
			var $MustSetCharacterEncoding = 0 ;
			var $SetCharacterEncodingOnFetch = 0 ;
			
			var $CharacterEncodingFixed = 0 ;
			
			var $CharacterEncoding = "utf-8" ;
			
			var $EncodingSet ;
			public function __destruct()
			{
				if(! $this->AutoCloseConnection && $this->Connection != false)
				{
					$this->FinalConnection() ;
				}
			}
			public function ShutdownScript()
			{
				if(! $this->AutoCloseConnection)
					$this->FinalConnection() ;
			}
			public function RegisterShutdownScript()
			{
				if($this->AutoCloseConnection)
				{
					register_shutdown_function(array($this, 'ShutdownScript')) ;
				}
			}
			public function ImportConfigFromNode(& $node)
			{
			}
		/**
		* Capture SQL texts, their results and the exception generated.
		* @access protected
		* @param string $sql Sql text captured.
		* @param string $resType Resource, result of the sql's execution.
		* @param string $exceptionMsg Exception found when executing sql.
		*/
			function LaunchSqlProfiler($sql, $resType="", $exceptionMsg="")
			{
				if(! $this->EnableSqlProfiler)
				{
					return ;
				}
				$entry = date("Y-m-d H:i:s")."\t".$sql."\t".$resType."\t".$exceptionMsg."\n" ;
				if($this->SqlProfilerOutputFile == "")
				{
					$this->SqlProfilerOutput .= $entry ;
				}
				else
				{
					try
					{
						$fh = fopen($this->SqlProfilerOutput, "a") ;
						if($fh !== false)
						{
							fputs($fh, $entry) ;
							fclose($fh) ;
						}
					}
					catch(Exception $ex)
					{
						$this->SqlProfilerError = $ex->getMessage() ;
					}
				}
			}
		/**
		* Add the NewValuePrefix to all the keys of the rowData. Used to generate the new row datas for updates or insertions
		* @access protected
		* @param array Array representing a row of some table.
		* @return array The input paameters with keys updated.
		*/
			function ApplyNewValuePrefix(& $rowData)
			{
				$newValue = array() ;
				foreach($rowData as $fieldName => $fieldValue)
				{
					if($fieldName == $this->ExprKeyName)
					{
						$newValue[$fieldName] = $fieldValue ;
						continue ;
					}
					$newValue[$this->NewValuePrefix.$fieldName] = $fieldValue ;
				}
				return $newValue ;
			}
		/**
		* Clear the connection exception message. It is used before beginning an operation which can raise an exception.
		* @access protected
		*/
			function ClearConnectionException()
			{
				$this->SetConnectionException("") ;
			}
		/**
		* Set the connection exception message with the new message.
		* @access protected
		* @param string $exceptionMsg
		*/
			function SetConnectionException($exceptionMsg="")
			{
				$this->ConnectionException = $exceptionMsg ;
			}
		/**
		* Escape the table name to prevent the special caracters into the table.
		* @access public
		* @param string The table name to escape
		* @return string The table name escaped
		*/
			function EscapeTableName($tableName)
			{
				return "" ;
			}
		/**
		* Escape the field name to prevent the special caracters in the field's name.
		* @access public
		* @param string The table name containing the field name
		* @param string The field name to escape
		* @return string
		*/
			function EscapeFieldName($tableName, $fieldName)
			{
				return "" ;
			}
		/**
		* Escape the variable name so the name is interpreted as a string.
		* @access public
		* @param string The variable name to escape
		* @return string
		*/
			function EscapeVariableName($varName)
			{
				return "" ;
			}
		/**
		* Escape into sql string value the value.
		* @access public
		* @param string The value to escape
		* @return string
		*/
			function EscapeRowValue($rowValue)
			{
				return "" ;
			}
		/**
		* Return a 'false' condition that can be used into a sql table
		* @access public
		* @return string
		*/
			function FalseCond()
			{
				return "1 = 0" ;
			}
		/**
		* Return a 'true' condition that can be used into a sql table
		* @access public
		* @return string
		*/
			function TrueCond()
			{
				return "1 = 1" ;
			}
		/**
		* Prepare a sql text to be executed, testing the sql and binding parameters.
		* @access public
		* @param string $sql Sql text to be prepared.
		* @param array $params List of parameters to bind.
		* @return mixed
		*/
			public function SortSqlParams($params)
			{
				uksort($params, array($this, "SortSqlParamSpec")) ;
				return $params ;
			}
			public function SortSqlParamSpec($param1, $param2)
			{
				return strlen("$param1") < strlen("$param2") ;
			}
			public function GetParamListFromValues($prefix, $values)
			{
				$params = array() ;
				$i = 0 ;
				foreach($values as $name => $val)
				{
					$params[$prefix.$i] = $val ;
					$i++ ;
				}
				return $params ;
			}
			public function ExtractExprFromParams($params, $separator=",")
			{
				$ctn = '' ;
				foreach($params as $paramName => $val)
				{
					if($ctn != '')
					{
						$ctn .= $separator ;
					}
					$ctn .= $this->ParamPrefix.$paramName ;
				}
				return $ctn ;
			}
			function & PrepareSql($sql, $params=array(), $realParamNames=array())
			{
				$sql_res = $sql ;
				$params = $this->SortSqlParams($params) ;
				// print_r($params) ;
				// $formatsSep = '[[:space:],;\.\:`"\'\|\(\)\[\]\\#~¨\%\?\/]' ;
				foreach($params as $n => $v)
				{
					$sql_res = str_replace($this->ParamPrefix.$n, $this->EscapeRowValue($v), $sql_res) ;
				}
				return $sql_res ;
			}
		/**
		* Open the connection resource and return if the operation succeeds.
		* @access protected
		* @return bool
		*/
			function OpenCnx()
			{
				$this->ClearConnectionException() ;
			}
		/**
		* Close the connection resource and return if the operation succeeds.
		* @access protected
		* @return bool
		*/
			protected function CloseCnx()
			{
			}
		/**
		* Open a new query by executing the sql text to the database server.
		* @access public
		* @param string $sql SQL text to execute
		* @param array $params Parameters
		* @return mixed
		*/
			public function & OpenQuery($sql, $params=array())
			{
				$this->LaunchSqlProfiler($sql) ;
				$res = null ;
				return $res ;
			}
		/**
		* Fetch the fields from the query.
		* @access public
		* @param resource $res Query Reader resource
		* @return array|false
		*/
			public function ColumnsQuery(&$res)
			{
				if($res === false)
				{
					return false ;
				}
				return array() ;
			}
		/**
		* Fetch the next row present in the Query Reader.
		* @access public
		* @param resource $res Query Reader resource
		* @return array|false
		*/
			function ReadQuery(&$res)
			{
				return false;
			}
		/**
		* Close the Query Reader.
		* @access public
		* @param resource $res Query Reader resource
		* @return bool
		*/
			function CloseQuery(&$res)
			{
				$this->AutoFinalConnection() ;
			}
			public function SqlColumnDefinitions($tableName, $schema='')
			{
				return "" ;
			}
			public function FetchTableFields($tableName, $schema='')
			{
				return $this->FetchColumnDefinitions($tableName, $schema) ;
			}
			protected function ParamsColumnDefinitions($tableName, $schema='')
			{
				return array('table_name' => $tableName) ;
			}
			public function FetchColumnDefinitions($tableName, $schema='')
			{
				$fields = array() ;
				$tableParams = $this->ParamsColumnDefinitions($tableName, $schema) ;
				$res = $this->OpenQuery($this->SqlColumnDefinitions($tableName, $schema), $tableParams) ;
				if($res !== false)
				{
					while(($row = $this->ReadQuery($res)) != false)
					{
						$column = $this->CreateColumnDefinition($row) ;
						$column->ImportConfigFromRow($row) ;
						if($column->Name != '')
						{
							$fields[$column->Name] = $column ;
						}
					}
					$this->CloseQuery($res) ;
				}
				return $fields ;
			}
			protected function CreateColumnDefinition()
			{
				return new AbstractSqlColumnDefinition() ;
			}
			protected function ParamsTableDefinitions($schema='')
			{
				return array() ;
			}
			public function SqlTableDefinitions($schema='')
			{
				return '' ;
			}
			public function FetchTableDefinitions($schema='')
			{
				$tables = array() ;
				$params = $this->ParamsTableDefinitions($schema) ;
				$res = $this->OpenQuery($this->SqlTableDefinitions($schema), $params) ;
				if($res !== false)
				{
					while(($row = $this->ReadQuery($res)) != false)
					{
						$tableDefinition = $this->CreateTableDefinition($row) ;
						$tableDefinition->ImportConfigFromRow($row) ;
						if($tableDefinition->Name != '')
						{
							$tables[$tableDefinition->Name] = $tableDefinition ;
						}
					}
					$this->CloseQuery($res) ;
				}
				return $tables ;
			}
			public function CreateTableDefinition()
			{
				return new AbstractSqlTableDefinition() ;
			}
		/**
		* Save the field structure for a table
		* @access protected
		* @param string $tableName Table name, must be present in the database
		* @return bool
		*/
			function StoreFieldsCache($tableName, $schema='')
			{
				$requestTableName = $tableName ;
				$tableName = strtoupper($tableName) ;
				if(isset($this->FieldsCache[$tableName]))
				{
					return 1 ;
				}
				$fields = $this->FetchColumnDefinitions($requestTableName, $schema='') ;
				if(count($fields))
				{
					$this->FieldsCache[$tableName] = $fields ;
					return 1 ;
				}
				return 0 ;
			}
		/**
		* Extract the parameters names which are used in a Sql statement
		* @access protected
		* @param string $sql Sql statement to find
		* @return array
		*/
			function ExtractParamsFromSql($sql, $params=array())
			{
				preg_match_all('/'.preg_quote($this->ParamPrefix).'('.$this->ParamPatternName.'+)/i', $sql, $match, PREG_PATTERN_ORDER) ;
				$paramKeys = (isset($match[1])) ? $match[1] : array() ;
				$i = 0 ;
				// print_r($params) ;
				while($i < count($paramKeys))
				{
					$paramKey = $paramKeys[$i] ;
					if(! isset($params[$paramKey]))
					{
						array_splice($paramKeys, $i, 1) ;
					}
					else
					{
						$i++ ;
					}
				}
				return $paramKeys;
			}
		/**
		* Extract the parameters names which are used in a Sql statement
		* @access protected
		* @param string $sql Sql statement to parse.
		* @param array $realParamNames real parameter names.
		* @param string $paramSymbol Symbol to use instead of the parameter names.
		* @return array
		*/
			function ReplaceParamsToSql($sql, $realParamNames, $paramSymbol="?")
			{
				$result = $sql ;
				foreach($realParamNames as $i => $paramKey)
				{
					$result = str_replace($this->ParamPrefix.$paramKey, $paramSymbol, $result) ;
				}
				return $result ;
			}
			function ExtractParamValues($realParamNames, $params=array())
			{
				$result = array() ;
				foreach($realParamNames as $i => $paramKey)
				{
					$result[] = $params[$paramKey] ;
				}
				return $result ;
			}
			function RemoveExprKeyEntry($rowData)
			{
				if(isset($rowData[$this->ExprKeyName]))
				{
					unset($rowData[$this->ExprKeyName]) ;
				}
				return $rowData ;
			}
			function AutoFinalConnection()
			{
				if($this->AutoCloseConnection)
				{
					$this->FinalConnection() ;
				}
			}
			function ExtractRow($tableName, $rowData, $includeExprs=true)
			{
				$requestTableName = $tableName ;
				$tableName = strtoupper($tableName) ;
				if(! isset($this->FieldsCache[$tableName]))
				{
					if(! $this->StoreFieldsCache($requestTableName))
					{
						return array() ;
					}
				}
				$row = array() ;
				if($includeExprs)
				{
					$row[$this->ExprKeyName] = (isset($rowData[$this->ExprKeyName])) ? $rowData[$this->ExprKeyName] : array() ;
				}
				// print_r($this->FieldsCache) ;
				foreach($this->FieldsCache[$tableName] as $fieldName => $fieldData)
				{
					$val = null ;
					$valDefined = 0 ;
					if(array_key_exists($fieldName, $rowData))
					{
						$val = $rowData[$fieldName] ;
						$valDefined = 1 ;
					}
					else
					{
						if(array_key_exists(strtoupper($fieldName), $rowData))
						{
							$val = $rowData[strtoupper($fieldName)] ;
							$valDefined = 1 ;
						}
						else
						{
							if(array_key_exists(strtolower($fieldName), $rowData))
							{
								$val = $rowData[strtolower($fieldName)] ;
								$valDefined = 1 ;
							}
							elseif($includeExprs)
							{
								if(array_key_exists($fieldName, $row[$this->ExprKeyName]))
								{
									$val = $row[$this->ExprKeyName][$fieldName] ;
									$valDefined = 1 ;
								}
								elseif(array_key_exists(strtoupper($fieldName), $row[$this->ExprKeyName]))
								{
									$val = $row[$this->ExprKeyName][strtoupper($fieldName)] ;
									$valDefined = 1 ;
								}
								elseif(array_key_exists(strtolower($fieldName), $row[$this->ExprKeyName]))
								{
									$val = $row[$this->ExprKeyName][strtolower($fieldName)] ;
									$valDefined = 1 ;
								}
							}
						}
					}
					if($valDefined)
					{
						$row[$fieldName] = $val ;
					}
				}
				return $row ;
			}
			function ExtractWhereFromRow($tableName, $params=array())
			{
				if(! isset($this->FieldsCache[$tableName]))
				{
					return null ;
				}
				$row = $this->ExtractRow($tableName, $params, true) ;
				$where = "" ;
				foreach($row as $fieldName => $v)
				{
					if($where == "")
					{
						$where .= " and " ;
					}
					$exprParamValue = $this->ParamPrefix.$this->NewValuePrefix.$fieldName ;
					$currentValue = (isset($rowData[$this->ExprKeyName][$fieldName])) ?
						$rowData[$this->ExprKeyName][$fieldName] : $this->ExprParamPattern ;
					$currentValue = str_ireplace($this->ExprParamPattern, $exprParamValue, $currentValue) ;
					$where .= $this->EscapeFieldName($tableName, $fieldName).'='.$currentValue ;
				}
				return $where ;
			}
			function ExtractSqlMatchRows($tableName, $params=array())
			{
				$sql = "select * from ".$this->EscapeTableName($tableName) ;
				$where = $this->ExtractWhereFromRow($tableName, $params) ;
				if(empty($where))
				{
					$where = $this->FalseCond() ;
				}
				return $sql." where ".$where ;
			}
			function MatchRows($tableName, $params=array(), $onlyFirst=false)
			{
				$sql = $this->ExtractSqlMatchRows($tableName, $params) ;
				return $this->FetchSqlRows($sql, $params, $onlyFirst) ;
			}
			function MatchFirstRow($tableName, $params=array())
			{
				$rows = $this->MatchRows($tableName, $params, true) ;
				$res = null ;
				if($rows !== null)
				{
					$res = array() ;
					if(count($res))
					{
						$res = $rows[0] ;
					}
				}
				return $res ;
			}
			function MatchFirstValue($tableName, $params=array(), $fieldName='')
			{
				$firstRow = $this->MatchFirstRow($tableName, $params) ;
				$res = null ;
				if($firstRow !== null)
				{
					$res = "" ;
					if(count($firstRow))
					{
						if(isset($firstRow[$fieldName]))
						{
							$res = $firstRow[$fieldName] ;
						}
						else
						{
							$res = null ;
						}
					}
				}
				return $res ;
			}
			function RunSql($sql, $params=array())
			{
				$ok = false ;
				$this->MustSetCharacterEncoding = 1 ;
				$res = $this->OpenQuery($sql, $this->EncodeParams($params)) ;
				if($res)
				{
					$ok = true ;
					$this->CloseQuery($res) ;
				}
				return $ok ;
			}
			function FetchSqlEntities($sql, $params=array(), $entityClassName="", $onlyFirst=false)
			{
				$entities = null ;
				if(! class_exists($entityClassName))
				{
					die($entityClassName." ne peut pas être utilisé comme classe d'entité") ;
				}
				$res = $this->OpenQuery($sql, $params) ;
				if($res !== false)
				{
					$entities = array() ;
					while(($row = $this->ReadQuery($res)) != false)
					{
						$entity = new $entityClassName() ;
						$entity->SetParentDatabase($this) ;
						$entity->ImportConfigFromRow($row) ;
						$entities[] = $entity ;
						if($onlyFirst)
						{
							break ;
						}
					}
					$this->CloseQuery($res) ;
				}
				return $entities ;
			}
			function FetchSqlEntity($sql, $params=array(), $entityClassName='')
			{
				$res = null ;
				$firstEntity = $this->FetchSqlEntities($sql, $params, $entityClassName, true) ;
				if($firstEntity !== null)
				{
					if(count($firstEntity))
					{
						$res = $firstEntity[0] ;
					}
				}
				return $res ;
			}
			function EncodeParams($params=array())
			{
				$results = array() ;
				foreach($params as $name => $value)
				{
					$results[$name] = $this->EncodeParamValue($value) ;
				}
				return $results ;
			}
			function FetchSqlRows($sql, $params=array(), $onlyFirst=false)
			{
				$rows = null ;
				$this->MustSetCharacterEncoding = $this->SetCharacterEncodingOnFetch ;
				$res = $this->OpenQuery($sql, $this->EncodeParams($params)) ;
				if($res !== false)
				{
					$rows = array() ;
					while(($row = $this->ReadQuery($res)) != false)
					{
						$rows[] = $row ;
						if($onlyFirst)
						{
							break ;
						}
					}
					$this->CloseQuery($res) ;
				}
				return $rows ;
			}
			function FetchSqlValue($sql, $params=array(), $fieldName='')
			{
				$res = null ;
				$firstRow = $this->FetchSqlRow($sql, $params) ;
				if($firstRow !== null)
				{
					$res = '' ;
					// print_r($firstRow) ;
					if(count($firstRow))
					{
						if(isset($firstRow[$fieldName]))
						{
							$res = $firstRow[$fieldName] ;
						}
						elseif(is_int($fieldName) || $fieldName == '')
						{
							$fieldName = intval($fieldName) ;
							$fieldList = array_keys($firstRow) ;
							$res = (isset($fieldList[$fieldName])) ? $fieldList[$fieldName] : null ;
						}
						else
						{
							$res = null ;
						}
					}
				}
				return $res ;
			}
			function FetchSqlRow($sql, $params=array())
			{
				$res = null ;
				$firstRow = $this->FetchSqlRows($sql, $params, true) ;
				if($firstRow !== null)
				{
					$res = array() ;
					if(count($firstRow))
					{
						$res = $firstRow[0] ;
					}
				}
				return $res ;
			}
			function LimitSqlRows($sql, $params=array(), $start=0, $limit=1000, $extra='')
			{
				return $this->FetchSqlRows($this->LimitSqlRowsReq($sql, $params, $start, $limit, $extra), $params) ;
			}
			function FetchRangeRows($fieldNames, $where, $other, $start=0, $limit=1000)
			{
			}
			function LimitSqlRowsReq($sql, $params=array(), $start=0, $limit=1000, $extra='')
			{
				return 'select * from ('.$sql.') MAIN_REQ' ;
			}
			function CountSqlRows($sql, $params=array())
			{
				$row = $this->FetchSqlRow($this->CountSqlRowsReq($sql, $params), $params);
				$total = -1 ;
				if($row !== null)
				{
					$total = (isset($row["TOTAL"])) ? $row["TOTAL"] : 0 ;
				}
				return $total ;
			}
			function CountSqlRowsReq($sql, $params=array())
			{
				return 'select count(0) TOTAL from ('.$sql.') MAIN_REQ' ;
			}
			function RunStoredProc($procName, $params=array())
			{
				$ok = 0 ;
				$this->StoredProcUseCursor = 0 ;
				$this->MustSetCharacterEncoding = 1 ;
				$res = $this->OpenStoredProc($procName, $this->EncodeParams($params)) ;
				if($res !== false)
				{
					$ok = 1 ;
					$this->CloseStoredProc($res) ;
				}
				return $ok ;
			}
			function FetchStoredProcRows($procName, $params=array(), $onlyFirst=false)
			{
				$rows = null ;
				$this->StoredProcUseCursor = 1 ;
				$res = $this->OpenStoredProc($procName, $this->EncodeParams($params)) ;
				if($res !== false)
				{
					$rows = array() ;
					while(($row = $this->ReadQuery($res)) != false)
					{
						$rows[] = $row ;
						if($onlyFirst)
						{
							break ;
						}
					}
					$this->CloseStoredProc($res) ;
				}
				return $rows ;
			}
			function FetchStoredProcEntities($procName, $params=array(), $entityClassName='', $onlyFirst=false)
			{
				$entities = null ;
				if(! class_exists($entityClassName))
				{
					die($entityClassName." ne peut pas être utilisé comme classe d'entité") ;
				}
				$this->StoredProcUseCursor = 1 ;
				$res = $this->OpenStoredProc($procName, $this->EncodeParams($params)) ;
				if($res !== false)
				{
					$entities = array() ;
					while(($row = $this->ReadQuery($res)) != false)
					{
						$entity = new $entityClassName() ;
						$entity->SetParentDatabase($this) ;
						$entity->ImportConfigFromRow($row) ;
						$entities[] = $entity ;
						if($onlyFirst)
						{
							break ;
						}
					}
					$this->CloseStoredProc($res) ;
				}
				return $entities ;
			}
			function FetchStoredProcEntity($procName, $params=array(), $entityClassName='')
			{
				$res = null ;
				$firstEntity = $this->FetchStoredProcEntities($procName, $params, $entityClassName, true) ;
				if($firstEntity !== null)
				{
					$res = array() ;
					if(count($firstEntity))
					{
						$res = $firstEntity[0] ;
					}
				}
				return $res ;
			}
			function FetchStoredProcRow($procName, $params=array())
			{
				$res = null ;
				$firstRow = $this->FetchStoredProcRows($procName, $params, true) ;
				if($firstRow !== null)
				{
					$res = array() ;
					if(count($firstRow))
					{
						$res = $firstRow[0] ;
					}
				}
				return $res ;
			}
			function FetchStoredProcValue($procName, $params=array(), $columnName='')
			{
				$res = null ;
				$firstRow = $this->FetchStoredProcRow($procName, $params, true) ;
				if($firstRow !== null)
				{
					$res = '' ;
					if(count($firstRow))
					{
						if(isset($firstRow[$columnName]))
						{
							$res = $firstRow[$columnName] ;
						}
						elseif(is_int($columnName) || $columnName == '')
						{
							$columnName = intval($columnName) ;
							$columnList = array_keys($firstRow) ;
							$res = (isset($columnList[$columnName])) ? $columnList[$columnName] : null ;
						}
						else
						{
							$res = null ;
						}
					}
				}
				return $res ;
			}
			function & OpenStoredProc($procName, $params=array())
			{
				$procSql = $this->CallStoredProcSql($procName, $params) ;
				$procParams = $this->CallStoredProcParams($procName, $params) ;
				$res = $this->OpenQuery($procSql, $procParams) ;
				return $res ;
			}
			function CloseStoredProc(& $res)
			{
				$this->CloseQuery($res) ;
			}
			function CallStoredProcSql($procName, $params=array())
			{
			}
			function CallStoredProcParams($procName, $params=array())
			{
				return $this->RemoveExprKeyEntry($params) ;
			}
			function InsertRow($tableName, $rowData)
			{
				$ok = 0 ;
				$rowData = $this->ExtractRow($tableName, $rowData) ;
				if($rowData)
				{
					$newRowData = $this->ApplyNewValuePrefix($rowData) ;
					$insertFieldList = $this->InsertRowFieldList($tableName, $rowData) ;
					$insertValueList = $this->InsertRowValueList($tableName, $rowData) ;
					if($insertFieldList != '' && $insertValueList != '')
					{
						// print_r($newRowData) ;
						$this->MustSetCharacterEncoding = 1 ;
						$sql = 'insert into '.$this->EscapeTableName($tableName).' ('.$insertFieldList.') values ('.$insertValueList.')' ;
						$res = $this->OpenQuery($sql, $this->EncodeParams($this->RemoveExprKeyEntry($newRowData))) ;
						if($res !== false)
						{
							$this->CloseQuery($res) ;
							$ok = 1 ;
						}
					}
					else
					{
						$this->SetConnectionException("La chaine d'insertion n'a pas ete construite pour la table $tableName") ;
					}
				}
				else
				{
					$this->SetConnectionException("Aucune valeur ne correspond a un champ de la table $tableName") ;
				}
				return $ok ;
			}
			function InsertRowFieldList($tableName, &$rowData)
			{
				$res = '' ;
				foreach($rowData as $fieldName => $fieldValue)
				{
					if($fieldName == $this->ExprKeyName)
						continue ;
					if($res != '')
					{
						$res .= ', ' ;
					}
					$res .= $this->EscapeFieldName($tableName, $fieldName) ;
				}
				return $res ;
			}
			function InsertRowValueList($tableName, &$rowData)
			{
				$res = '' ;
				foreach($rowData as $fieldName => $fieldValue)
				{
					if($fieldName == $this->ExprKeyName)
						continue ;
					if($res != '')
					{
						$res .= ', ' ;
					}
					$exprParamValue = $this->ParamPrefix.$this->NewValuePrefix.$fieldName ;
					$currentValue = (isset($rowData[$this->ExprKeyName][$fieldName])) ?
						$rowData[$this->ExprKeyName][$fieldName] : $this->ExprParamPattern ;
					$currentValue = str_ireplace($this->ExprParamPattern, $exprParamValue, $currentValue) ;
					$res .= $currentValue ;
				}
				return $res ;
			}
			function UpdateRow($tableName, $rowData, $where, $whereParams=array())
			{
				$ok = 0 ;
				$rowData = $this->ExtractRow($tableName, $rowData) ;
				// print_r($rowData) ;
				$newRowData = $this->ApplyNewValuePrefix($rowData) ;
				if($rowData)
				{
					$updateList = $this->UpdateRowList($tableName, $rowData) ;
					if($updateList != '')
					{
						$sql = 'update '.$this->EscapeTableName($tableName).' set '.$updateList.' where '.$where ;
						$this->MustSetCharacterEncoding = 1 ;
						$res = $this->OpenQuery($sql, $this->EncodeParams($this->RemoveExprKeyEntry(array_merge($newRowData, $whereParams)))) ;
						if($res !== false)
						{
							$this->CloseQuery($res) ;
							$ok = 1 ;
						}
					}
					else
					{
						$this->SetConnectionException("La chaine de mise a jour n'a pas ete construite pour la table $tableName") ;
					}
				}
				else
				{
					$this->SetConnectionException("Aucune valeur ne correspond a un champ de la table $tableName") ;
				}
				return $ok ;
			}
			function UpdateRowList($tableName, $rowData)
			{
				$res = '' ;
				foreach($rowData as $fieldName => $fieldValue)
				{
					if($fieldName == $this->ExprKeyName)
						continue ;
					if($res != '')
					{
						$res .= ', ' ;
					}
					$exprParamValue = $this->ParamPrefix.$this->NewValuePrefix.$fieldName ;
					$currentValue = (isset($rowData[$this->ExprKeyName][$fieldName])) ?
						$rowData[$this->ExprKeyName][$fieldName] : $this->ExprParamPattern ;
					$currentValue = str_ireplace($this->ExprParamPattern, $exprParamValue, $currentValue) ;
					$res .= $this->EscapeFieldName($tableName, $fieldName).'='.$currentValue ;
				}
				return $res ;
			}
			function DeleteRow($tableName, $where, $whereParams=array())
			{
				$ok = 0 ;
				$this->MustSetCharacterEncoding = 1 ;
				$sql = 'delete from '.$this->EscapeTableName($tableName).' where '.$where ;
				$res = $this->OpenQuery($sql, $this->EncodeParams($whereParams)) ;
				if($res !== false)
				{
					$this->CloseQuery($res) ;
					$ok = 1 ;
				}
				return $ok ;
			}
			function InsertRows($Rows=array())
			{
				
			}
			function InitConnection()
			{
				if($this->Connection)
				{
					return 1 ;
				}
				$this->CharacterEncodingFixed = 0 ;
				return $this->OpenCnx() ;
				// return ($this->Connection !== false) ;
			}
			function FinalConnection()
			{
				$this->CharacterEncodingFixed = 0 ;
				return ($this->CloseCnx()) ;
			}
			function FixCharacterEncoding()
			{
				if($this->MustSetCharacterEncoding && $this->AutoSetCharacterEncoding && ! $this->CharacterEncodingFixed && $this->CharacterEncoding != "")
				{
					$this->ExecFixCharacterEncoding() ;
					$this->CharacterEncodingFixed = 1 ;
				}
			}
			function ExecFixCharacterEncoding()
			{
				
			}
			function SqlNow()
			{
				return 'null' ;
			}
			function SqlConcat($list)
			{
				return join (" + ", $list) ;
			}
			function SqlToDateTime($expr)
			{
				return "null" ;
			}
			function SqlToTimestamp($expr)
			{
				return "null" ;
			}
			function SqlDateDiff($expr1, $expr2)
			{
				return "null" ;
			}
			function SqlReplace($expr, $search, $replace, $start=0)
			{
				return "null" ;
			}
			function SqlSubstr($expr, $start, $length=0)
			{
				return "null" ;
			}
			function SqlLength($expr)
			{
				return "LENGTH($expr)" ;
			}
			function SqlIndexOf($expr, $search, $start=0)
			{
				return "null" ;
			}
			function SqlIsNull($expr)
			{
				return 'null' ;
			}
			function SqlStrToDateTime($dateName)
			{
			}
			function SqlStrToDate($dateName)
			{
			}
			function SqlDatePart($dateName)
			{
			}
			function SqlAddSeconds($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return $expr ;
			}
			function SqlAddMinutes($expr, $val)
			{
			}
			function SqlAddHours($expr, $val)
			{
			}
			function SqlAddDays($expr, $val)
			{
			}
			function SqlAddMonths($expr, $val)
			{
			}
			function SqlAddYears($expr, $val)
			{
			}
			function SqlDateToStr($dateName)
			{
			}
			function SqlDateTimeToStr($dateName)
			{
			}
			function SqlStrToDateFr($dateName, $includeHour=0)
			{
			}
			function SqlToInt($expression)
			{
			}
			function SqlToDouble($expression)
			{
			}
			function SqlToString($expression)
			{
			}
			public function EncodeHtmlEntity($value)
			{
				return htmlentities($value) ;
			}
			public function EncodeHtmlAttr($value)
			{
				return htmlspecialchars($value) ;
			}
			public function EncodeUrl($value)
			{
				return urlencode($value) ;
			}
			public function DecodeRowValue($value)
			{
				return $value ;
			}
			public function EncodeParamValue($value)
			{
				return $value ;
			}
			protected function CaptureQuery($sql, $params=array())
			{
				$this->LastSqlText = $sql ;
				$this->LastSqlParams = $params ;
			}
			public function __construct()
			{
				$this->EncodingSet = new DefaultEncodingSetDB() ;
				$this->RegisterShutdownScript() ;
				$this->InitConnectionParams() ;
			}
			public function InitConnectionParams()
			{
			}
			public function & CreateEntity($entityClassName)
			{
				$entity = new $entityClassName() ;
				$entity->SetParentDatabase($this) ;
				return $entity ;
			}
			public function & CreateEntityCollection($entityCollectionClassName)
			{
				$entityCollection = new $entityCollectionClassName($this) ;
				return $entityCollection ;
			}
		}
		
		class SqlLiteDB extends AbstractSqlDB
		{
			public $VendorName = "SQLLITE" ;
			public $VendorMinVersion = "0" ;
			public $VendorMaxVersion = "2" ;
			public $DatabaseFilePath = "" ;
			public $OpenPermissions = 0666 ;
			public $ReadQueryType = SQLITE_ASSOC ;
			public $UseBuffer = 0 ;
			public function OpenCnx()
			{
				$this->OpenConnectionHandle() ;
				return ($this->Connection != false) ;
			}
			protected function ValidateDatabaseFilePath()
			{
				if(! file_exists($this->DatabaseFilePath))
				{
					$this->SetConnectionException("Le fichier ".$this->DatabaseFilePath." n'existe pas") ;
					return 0 ;
				}
				return 1 ;
			}
			protected function OpenConnectionHandle()
			{
				$OK = 0 ;
				try
				{
					$this->Connection = sqlite_open($this->DatabaseFilePath, $this->OpenPermissions, $this->ConnectionException) ;
					if($this->Connection != false)
					{
						$OK = 1 ;
					}
				}
				catch(Exception $ex)
				{
				}
			}
			protected function BuildDatabaseFilePath()
			{
				$this->DatabaseFilePath = $this->ConnectionParams["server"] ;
				if($this->ConnectionParams["schema"] != '')
				{
					$this->DatabaseFilePath .= '/'.$this->ConnectionParams["schema"] ;
				}
			}
			public function & OpenQuery($sqlText, $sqlParams=array())
			{
				$res = false ;
				if(! $this->InitConnection())
				{
					return $res ;
				}
				$this->ClearConnectionException() ;
				$this->CaptureQuery($sql, $params) ;
				$sql = $this->PrepareSql($sql, $params) ;
				try
				{
					if($this->UseBuffer)
					{
						$res = sqlite_query($sql, $this->Connection) ;
					}
					else
					{
						$res = sqlite_unbuffered_query($sql, $this->Connection) ;
					}
					$exceptionMsg = "" ;
					if(! $res)
					{
						$exceptionMsg = mysql_error($this->Connection) ;
						$this->SetConnectionException($exceptionMsg) ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, strval($res), $exceptionMsg) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			public function ReadQuery(& $query)
			{
				if($query == false)
				{
					return false ;
				}
				return sqlite_fetch_array($query, $this->ReadQueryType) ;
			}
			public function CloseQuery(& $query)
			{
				parent::CloseQuery($query) ;
				if($query == false)
				{
					return true ;
				}
				$query = false ;
			}
			public function EscapeParams($sqlText, $sqlParams=array())
			{
				$result = $sqlText ;
				foreach($sqlParams as $name => $value)
				{
					$result = str_replace($result, $this->ParamPrefix.$name, $this->EscapeRowValue($value)) ;
				}
				return $result ;
			}
			public function EscapeRowValue($rowValue)
			{
				return sqlite_escape_string($rowValue) ;
			}
			public function CloseCnx()
			{
				if($this->Connection == false)
					return 1 ;
				return sqlite_close($this->Connection) ;
			}
		}

		class MysqlDB extends AbstractSqlDB
		{
		/**
		* Nom de la marque de la base de donnees supportee (MYSQL)
		* @var string $VendorName
		*/
			var $VendorName = "MYSQL" ;
		/**
		* Nom de la version minimum de la base de donnees MYSQL supportee.
		* @var string $VendorMinVersion
		*/
			var $VendorMinVersion = "4" ;
		/**
		* Nom de la version maximum de la base de donnees MYSQL supportee.
		* @var string $VendorMaxVersion
		*/
			var $VendorMaxVersion = "6" ;
			var $UseBuffer = 1 ;
			var $StoredProcConnection = false ;
			function ExecFixCharacterEncoding()
			{
				// mysql_query('SET NAMES '.$this->CharacterEncoding, $this->Connection) ;
				mysql_query('SET CHARACTER SET '.$this->CharacterEncoding, $this->Connection) ;
				mysql_set_charset($this->CharacterEncoding, $this->Connection) ;
				/*
				*/
			}
			function SqlConcat($list)
			{
				if(count($list) == 0)
					return ;
				if(count($list) == 1)
					return $list[0] ;
				$sql = "CONCAT(" ;
				for($i=0; $i<count($list) ; $i++)
				{
					if($i > 0)
					{
						$sql .= ", " ;
					}
					$sql .= $list[$i] ;
				}
				$sql .= ")" ;
				return $sql ;
			}
			function SqlToDateTime($expr)
			{
				return "TIMESTAMP(".$expr.")" ;
			}
			function SqlToTimestamp($expr)
			{
				return "UNIX_TIMESTAMP(".$expr.")" ;
			}
			function SqlAddSeconds($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATE_ADD('.$expr.', INTERVAL '.$val.' SECOND)' ;
			}
			function SqlAddMinutes($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATE_ADD('.$expr.', INTERVAL '.$val.' MINUTE)' ;
			}
			function SqlAddHours($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATE_ADD('.$expr.', INTERVAL '.$val.' HOUR)' ;
			}
			function SqlAddDays($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATE_ADD('.$expr.', INTERVAL '.$val.' DAY)' ;
			}
			function SqlAddMonths($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATE_ADD('.$expr.', INTERVAL '.$val.' MONTH)' ;
			}
			function SqlAddYears($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATE_ADD('.$expr.', INTERVAL '.$val.' YEAR)' ;
			}
			function SqlDateDiff($expr1, $expr2)
			{
				return "TIME_TO_SEC(TIMEDIFF(".$expr1.", ".$expr2."))" ;
			}
			function SqlReplace($expr, $search, $replace, $start=0)
			{
				return "replace($expr, $search, $replace)" ;
			}
			function SqlLength($expr)
			{
				return "LENGTH($expr)" ;
			}
			function SqlSubstr($expr, $start, $length=0)
			{
				$str = "SUBSTR($expr, $start)" ;
				if($length > 0)
				{
					$str = "SUBSTR($expr, $start, $length)" ;
				}
				return $str ;
			}
			function SqlIndexOf($expr, $search, $start=0)
			{
				$str = "INSTR($expr, $search)" ;
				if($start > 0)
				{
					$str = "INSTR(substr($expr, $start), $search)" ;
				}
				return $str ;
			}
			function SqlNow()
			{
				return "NOW()" ;
			}
			function SqlIsNull($expr)
			{
				return "$expr IS NULL" ;
			}
			function SqlStrToDateTime($dateName)
			{
				return 'TIMESTAMP('.$dateName.')' ;
			}
			function SqlStrToDate($dateName)
			{
				return 'TIMESTAMP('.$dateName.')' ;
			}
			function SqlDatePart($dateName)
			{
				return 'DATE('.$dateName.')' ;
			}
			function SqlTimePart($dateName)
			{
				return 'TIME('.$dateName.')' ;
			}
			function SqlDateToStr($dateName)
			{
				return 'DATE_FORMAT('.$dateName.', \'%Y-%m-%d\')' ;
			}
			function SqlDateTimeToStr($dateName)
			{
				return 'DATE_FORMAT('.$dateName.', \'%Y-%m-%d %H:%i:%s\')' ;
			}
			function SqlDateToStrFr($dateName, $includeHour=0)
			{
				$format = '%d/%m/%Y' ;
				if($includeHour)
					$format .= ' %H:%i:%s' ;
				return 'DATE_FORMAT('.$dateName.', \''.$format.'\')' ;
			}
			function SqlToInt($expression)
			{
				return 'CONVERT ('.$expression.', SIGNED)' ;
			}
			function SqlToDouble($expression)
			{
				return 'CONVERT('.$expression.', DECIMAL(18, 3))' ;
			}
			function SqlToString($expression)
			{
				return 'CAST ('.$expression.' AS STRING)' ;
			}
			function CallStoredProcSql($procName, $params=array())
			{
				$sql = '' ;
				$sql .= 'CALL '.$procName.'(' ;
				$i = 0 ;
				foreach($params as $name => $value)
				{
					if($i > 0)
						$sql .= ', ' ;
					$sql .= $this->EscapeRowValue($value) ;
					$i++ ;
				}
				$sql .= ')' ;
				return $sql ;
			}
			function GetNextAutoIncValue($tableName)
			{
				$value = $this->FetchSqlValue(
					"SELECT AUTO_INCREMENT id
FROM information_schema.TABLES WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :tableName",
					array("schema" => $this->ConnectionParams["schema"], "tableName" => $tableName),
					"id"
				) ;
				return $value ;
			}
			function OpenStoredProcCnx()
			{
				$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
				$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
				$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
				try
				{
					$this->StoredProcConnection = mysql_connect($server, $user, $password, false, 65536) ;
					if($this->StoredProcConnection !== false)
					{
						$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
						$OK = mysql_select_db($schema, $this->StoredProcConnection) ;
						if($OK === false)
						{
							$this->SetConnectionException(mysql_error($this->StoredProcConnection)) ;
							mysql_close($this->StoredProcConnection) ;
							$this->StoredProcConnection = false ;
						}
					}
					else
					{
						$this->SetConnectionException(mysql_error()) ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return ($this->StoredProcConnection !== false) ;
			}
			function & OpenStoredProc($procName, $params=array())
			{
				$this->StoredProcQuery = false ;
				if(! $this->OpenStoredProcCnx())
				{
					return $this->StoredProcQuery ;
				}
				try
				{
					$this->StoredProcQuery = mysql_query($this->CallStoredProcSql($procName, $params), $this->StoredProcConnection) ;
				}
				catch(Exception $ex)
				{
				}
				if($this->StoredProcQuery == false)
				{
					$this->SetConnectionException(mysql_error($this->StoredProcConnection)) ;
				}
				return $this->StoredProcQuery ;
			}
			function CloseStoredProc(& $res)
			{
				if($this->StoredProcQuery !== false)
				{
					mysql_free_result($this->StoredProcQuery) ;
					$this->StoredProcQuery = false ;
				}
				mysql_close($this->StoredProcConnection) ;
				$this->StoredProcConnection = false ;
				return 1 ;
			}
			function EscapeTableName($tableName)
			{
				return "`".$tableName."`" ;		
			}
			function EscapeVariableName($varName)
			{
				return "`".$varName."`" ;		
			}
			function EscapeFieldName($tableName, $fieldName)
			{
				return "`".$tableName."`.`".$fieldName."`" ;
			}
			function EscapeRowValue($rowValue)
			{
				// echo $rowValue."<br>" ;
 				return "'".mysql_real_escape_string($rowValue)."'" ;
 				// return "convert(cast(convert('".mysql_real_escape_string($rowValue)."' using  latin1) as binary) using utf8)" ;
			}
			function OpenCnx()
			{
				$this->ClearConnectionException() ;
				$OK = 0 ;
				if($this->ConnectCnx())
				{
					$OK = $this->SelectDBCnx() ;
				}
				return $OK ;
			}
			function ConnectCnx()
			{
				$res = 0 ;
				try
				{
					$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$this->Connection = mysql_connect($server, $user, $password) ;
					if(! $this->Connection)
					{
						$res = 0 ;
						$this->SetConnectionException(mysql_error()) ;
					}
					else
					{
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function SelectDBCnx()
			{
				$res = 0 ;
				if(! $this->Connection)
				{
					return $res ;
				}
				try
				{
					$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
					$OK = mysql_select_db($schema, $this->Connection) ;
					if($OK === false)
					{
						$this->SetConnectionExceptionFromCnx() ;
						$res = 0 ;
					}
					else
					{
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function ConnectionErrString()
			{
				return mysql_error($this->Connection) ;
			}
			function SetConnectionExceptionFromCnx()
			{
				return $this->SetConnectionException($this->ConnectionErrString()) ;
			}
			function & OpenQuery($sql, $params=array())
			{
				$res = false ;
				if(! $this->InitConnection())
				{
					return $res ;
				}
				$this->FixCharacterEncoding() ;
				$this->ClearConnectionException() ;
				$this->CaptureQuery($sql, $params) ;
				$sql = $this->PrepareSql($sql, $params) ;
				try
				{
					$res = mysql_unbuffered_query($sql, $this->Connection) ;
					// $res = mysql_unbuffered_query($sql, $this->Connection) ;
					$exceptionMsg = "" ;
					if(! $res)
					{
						$exceptionMsg = mysql_error($this->Connection) ;
						$this->SetConnectionException($exceptionMsg) ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, strval($res), $exceptionMsg) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				if($res == false)
				{
					$this->AutoFinalConnection() ;
				}
				return $res ;
			}
			function ReadQuery(&$res)
			{
				$row = false;
				try
				{
					$row = mysql_fetch_assoc($res) ;
					if(is_array($row))
					{
						$row = array_map(array(& $this, "DecodeRowValue"), $row) ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $row ;
			}
			function CloseQuery(&$res)
			{
				try
				{
					if(is_resource($res))
					{
						$OK = mysql_free_result($res) ;
						if($OK)
						{
							$res = false ;
						}
					}
				}
				catch(Exception $ex)
				{
				}
				$this->AutoFinalConnection() ;
			}
			function LimitSqlRowsReq($sql, $params=array(), $start=0, $limit=1000, $extra='')
			{
				$limit_clause = 'LIMIT '.intval($start).', '.intval($limit) ;
				if(stripos($sql, ' LIMIT ') === false)
				{
					return $sql.' '.$limit_clause ;
				}
				return 'select * from ('.$sql.') MAIN_REQ '.$limit_clause ;
			}
			public function CreateColumnDefinition()
			{
				return new MysqlColumnDefinition() ;
			}
			protected function ParamsColumnDefinitions($tableName, $schema='')
			{
				return array() ;
			}
			public function SqlColumnDefinitions($tableName, $schema='')
			{
				$requestTableName = $tableName ;
				$tableName = strtoupper($tableName) ;
				return 'DESCRIBE '.$this->EscapeTableName($requestTableName) ;
			}
			public function CreateTableDefinition()
			{
				return new MysqlTableDefinition() ;
			}
			public function SqlTableDefinitions($schema='')
			{
				return 'SHOW TABLE STATUS' ;
			}
			function CloseCnx()
			{
				if(! $this->Connection || ! is_resource($this->Connection))
				{
					return 1 ;
				}
				try
				{
					$res = (mysql_close($this->Connection)) ? 1 : 0 ;
					if($res)
					{
						$this->Connection = false ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $res ;
			}
		}
		class MysqliDB extends MysqlDB
		{
		/**
		* Nom de la marque de la base de donnees supportee (MYSQL)
		* @var string $VendorName
		*/
			var $VendorName = "MYSQL" ;
		/**
		* Nom de la version minimum de la base de donnees MYSQL supportee.
		* @var string $VendorMinVersion
		*/
			var $VendorMinVersion = "4" ;
		/**
		* Nom de la version maximum de la base de donnees MYSQL supportee.
		* @var string $VendorMaxVersion
		*/
			var $VendorMaxVersion = "7" ;
			function ExecFixCharacterEncoding()
			{
				// $ok = mysqli_query('SET NAMES '.$this->CharacterEncoding, $this->Connection) ;
				if(is_resource($this->Connection))
				{
					mysqli_query($this->Connection, 'SET CHARACTER SET '.$this->CharacterEncoding) ;
					mysqli_set_charset($this->Connection, $this->CharacterEncoding) ;
				}
			}
			function EscapeTableName($tableName)
			{
				return "`".$tableName."`" ;		
			}
			function EscapeFieldName($tableName, $fieldName)
			{
				return "`".$tableName."`.`".$fieldName."`" ;
			}
			function EscapeRowValue($rowValue)
			{
				$cnx = (is_object($this->StoredProcConnection)) ? $this->StoredProcConnection : $this->Connection ;
				return "'".mysqli_escape_string($cnx, $rowValue)."'" ;
			}
			function OpenCnx()
			{
				$this->ClearConnectionException() ;
				$OK = 0 ;
				if($this->ConnectCnx())
				{
					$OK = $this->SelectDBCnx() ;
				}
				return $OK ;
			}
			function ConnectCnx()
			{
				$res = 0 ;
				try
				{
					$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$this->Connection = mysqli_connect($server, $user, $password) ;
					if(! $this->Connection)
					{
						$res = 0 ;
						$this->SetConnectionException(mysqli_error($this->Connection)) ;
					}
					else
					{
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function SelectDBCnx()
			{
				$res = 0 ;
				if(! $this->Connection)
				{
					return $res ;
				}
				try
				{
					$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
					$OK = mysqli_select_db($this->Connection, $schema) ;
					if($OK === false)
					{
						$this->SetConnectionExceptionFromCnx() ;
						$res = 0 ;
					}
					else
					{
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function ConnectionErrString()
			{
				return mysqli_error((is_object($this->StoredProcConnection)) ? $this->StoredProcConnection : $this->Connection) ;
			}
			function SetConnectionExceptionFromCnx()
			{
				return $this->SetConnectionException($this->ConnectionErrString()) ;
			}
			function OpenStoredProcCnx()
			{
				$res = 0 ;
				try
				{
					$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$this->StoredProcConnection = mysqli_connect($server, $user, $password) ;
					if(! $this->StoredProcConnection)
					{
						$res = 0 ;
						$this->SetConnectionException(mysqli_error($this->StoredProcConnection)) ;
					}
					else
					{
						$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
						$OK = mysqli_select_db($this->StoredProcConnection, $schema) ;
						if($OK === false)
						{
							$this->SetConnectionExceptionFromCnx() ;
							$res = 0 ;
						}
						else
						{
							$res = 1 ;
						}
					}
				}
				catch(Exception $ex)
				{
					$this->SetStoredProcConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function & OpenStoredProc($procName, $params=array())
			{
				$this->StoredProcQuery = false ;
				if(! $this->OpenStoredProcCnx())
				{
					return $this->StoredProcQuery ;
				}
				try
				{
					$this->StoredProcQuery = mysqli_query($this->StoredProcConnection, $this->CallStoredProcSql($procName, $params)) ;
				}
				catch(Exception $ex)
				{
				}
				if($this->StoredProcQuery == false)
				{
					$this->SetConnectionException(mysqli_error($this->StoredProcConnection)) ;
				}
				return $this->StoredProcQuery ;
			}
			function CloseStoredProc(& $res)
			{
				if($this->StoredProcQuery !== false)
				{
					mysqli_free_result($this->StoredProcQuery) ;
					$this->StoredProcQuery = false ;
				}
				mysqli_close($this->StoredProcConnection) ;
				$this->StoredProcConnection = false ;
				return 1 ;
			}
			function & OpenQuery($sql, $params=array())
			{
				if(! $this->InitConnection())
				{
					return false ;
				}
				$this->ClearConnectionException() ;
				$this->CaptureQuery($sql, $params) ;
				$this->FixCharacterEncoding() ;
				$sql = $this->PrepareSql($sql, $params) ;
				$res = false ;
				try
				{
					$res = mysqli_query($this->Connection, $sql) ;
					$exceptionMsg = "" ;
					if(! $res)
					{
						$exceptionMsg = mysqli_error($this->Connection) ;
						$this->SetConnectionException($exceptionMsg) ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, ($res) ? "mysqli_object" : '', $exceptionMsg) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function ReadQuery(&$res)
			{
				$row = false;
				try
				{
					$row = mysqli_fetch_assoc($res) ;
					if(is_array($row))
					{
						$row = array_map(array(& $this, "DecodeRowValue"), $row) ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $row ;
			}
			function CloseQuery(&$res)
			{
				try
				{
					if(is_resource($res))
					{
						$OK = mysqli_free_result($res) ;
						if($OK)
						{
							$res = false ;
						}
					}
				}
				catch(Exception $ex)
				{
				}
				$this->AutoFinalConnection() ;
			}
			function CloseCnx()
			{
				if(! $this->Connection)
				{
					return 1 ;
				}
				try
				{
					$res = (mysqli_close($this->Connection)) ? 1 : 0 ;
					if($res)
					{
						$this->Connection = false ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $res ;
			}
		}

		class OciDBTnsAddress
		{
			public $Protocol = 'TCP' ;
			public $Port = '1521' ;
			public $Host = '' ;
			public function __construct($host)
			{
				$this->Host = $host ;
			}
		}
		class OciDB extends AbstractSqlDB
		{
		/**
		* Nom de la marque de la base de donnees supportee (ORACLE)
		* @var string $VendorName
		*/
			var $VendorName = "ORACLE" ;
		/**
		* Nom de la version minimum de la base de donnees ORACLE supportee.
		* @var string $VendorMinVersion
		*/
			var $VendorMinVersion = "6" ;
		/**
		* Nom de la version maximum de la base de donnees ORACLE supportee.
		* @var string $VendorMaxVersion
		*/
			var $VendorMaxVersion = "11" ;
		/**
		* Attributs complementaires de connexion dans le TNS.
		* @var array TnsConnectDataParams
		*/
			var $TnsConnectDataParams = array() ;
			var $TnsGlobalParams = array() ;
			var $TnsAddressListParams = array() ;
			var $TnsAddresses = array() ;
			public $StoredProcCursorName = 'CursorResult' ;
			public $StoredProcQueryActive = false ;
			public $StoredProcCursor = false ;
			public $StoredProcQuery = false ;
			public $OracleCharacterSet = "AL32UTF8" ;
			// public $OracleCharacterSet = "AL32UTF8" ;
			public function ImportConfigFromNode(& $node)
			{
				parent::ImportConfigFromNode($node) ;
				if(isset($node["attrs"]))
				{
					foreach($node["attrs"] as $name => $value)
					{
						if(stripos($name, 'CONNECTDATA') === 0)
						{
							$this->TnsConnectDataParams[str_replace('CONNECTDATA', '', $name)] = $value ;
						}
					}
				}
			}
			function EscapeTableName($tableName)
			{
				return "\"".$tableName."\"" ;		
			}
			function EscapeVariableName($varName)
			{
				return "\"".$varName."\"" ;		
			}
			function EscapeFieldName($tableName, $fieldName)
			{
				return "\"".$tableName."\".\"".$fieldName."\"" ;
			}
			function EscapeRowValue($rowValue)
			{
				return "q['".str_replace("'", "''", $rowValue)."']" ;
			}
			function OpenCnx()
			{
				$this->ClearConnectionException() ;
				$OK = $this->ConnectCnx() ;
				return $OK ;
			}
			function ExtractTNSName($server, $schema)
			{
				$tnsName = '' ;
				if($schema != '')
				{
					if(preg_match('/^([A-Z0-9_\.\-]+)/', $server, $host_match))
					{
						$port = (isset($this->ConnectionParams["port"])) ? $this->ConnectionParams["port"] : 1521 ;
						if(preg_match('/(^\d+)$/', $server, $port_match))
						{
							$port = $port_match[1] ;
						}
						$serviceName = (isset($this->TnsConnectDataParams["SID"])) ? "SID" : "SERVICE_NAME" ;
						$tnsName = '(DESCRIPTION =
(ADDRESS_LIST ='."\n" ;
						if(count($this->TnsAddresses))
						{
							foreach($this->TnsAddresses as $i => $tnsAddress)
							{
								$tnsName .= '(ADDRESS = (PROTOCOL = '.$tnsAddress->Protocol.')(HOST = '.$tnsAddress->Host.')(PORT = '.$tnsAddress->Port.'))'."\n" ;
							}
						}
						if($server != '')
						{
							$tnsName .= '(ADDRESS = (PROTOCOL = TCP)(HOST = '.$host_match[1].')(PORT = '.$port.'))'."\n" ;
						}
						foreach($this->TnsAddressListParams as $n => $v)
						{
							$tnsName .= '('.$n.' = '.$v.')'."\n" ;
						}
						$tnsName .= ')'."\n" ;
						foreach($this->TnsGlobalParams as $n => $v)
						{
							$tnsName .= '('.$n.' = '.$v.')'."\n" ;
						}
						$tnsName .= '(CONNECT_DATA =
('.$serviceName.' = '.$schema.')'."\n" ;
						foreach($this->TnsConnectDataParams as $n => $v)
						{
							if($n == "SID")
								continue ;
							$tnsName .= '('.$n.' = '.$v.')'."\n" ;
						}
						$tnsName .= ')'."\n" ;
						$tnsName .= ')'."\n" ;
					}
					else
					{
						$tnsName = $server.'/'.$schema ;
					}
				}
				else
				{
					$tnsName = $server ;
				}
				// echo $tnsName ;
				return $tnsName ;
			}
			function ConnectCnx()
			{
				$res = 0 ;
				try
				{
					$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
					$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
					$tnsName = $this->ExtractTNSName($server, $schema) ;
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$this->LastConnectionString = $user.":".$password."@".$tnsName."\n" ;
					if($this->OracleCharacterSet == '')
					{
						$this->Connection = oci_connect($user, $password, $tnsName) ;
					}
					else
					{
						$this->Connection = oci_connect($user, $password, $tnsName, $this->OracleCharacterSet) ;
					}
					if(! $this->Connection)
					{
						$res = 0 ;
						$this->SetConnectionExceptionFromOciError(oci_error()) ;
					}
					else
					{
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function & PrepareSql($sql, $params=array(), $realParamNames=array())
			{
				$this->CaptureQuery($sql, $params) ;
				$stmt = false ;
				try
				{
					$stmt = oci_parse($this->Connection, $sql) ;
					if($stmt !== false)
					{
						// $params = $this->SortSqlParam($params) ;
						// print $sql.' '.print_r($params, true)."\n" ;
						foreach($params as $n => $v)
						{
							// echo $params[$n]."\n" ;
							oci_bind_by_name($stmt, $this->ParamPrefix.$n, $params[$n]) ;
						}
						if($this->StoredProcQueryActive && $this->StoredProcUseCursor)
						{
							$this->StoredProcCursor = oci_new_cursor($this->Connection) ;
							oci_bind_by_name($stmt, $this->ParamPrefix.$this->StoredProcCursorName, $this->StoredProcCursor, -1, OCI_B_CURSOR);
						}
					}
					else
					{
						$this->SetConnectionExceptionFromOciError(oci_error($this->Connection)) ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $stmt ;
			}
			function & OpenQuery($sql, $params=array())
			{
				$stmt = false ;
				if(! $this->InitConnection())
				{
					return $stmt ;
				}
				$this->ClearConnectionException() ;
				$stmt = $this->PrepareSql($sql, $params) ;
				if($stmt === false)
				{
					return $stmt ;
				}
				$res = false ;
				try
				{
					$res = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS) ;
					$exceptionMsg = "" ;
					if(! $res)
					{
						// print $sql ;
						// print_r(array($sql, $params)) ;
						$exceptionMsg = $this->ReadErrorMsg(oci_error($stmt)) ;
						$this->SetConnectionException($exceptionMsg) ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, strval($res), $exceptionMsg) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				if($res && ! $stmt)
				{
					return $res ;
				}
				elseif($res && $stmt)
				{
					return $stmt ;
				}
				else
				{
					if($stmt !== false)
					{
						oci_free_statement($stmt) ;
						$stmt = false ;
					}
					$this->AutoFinalConnection() ;
					return $stmt ;
				}
			}
			function SetConnectionExceptionFromOciError($errorData)
			{
				$this->SetConnectionException($this->ReadErrorMsg($errorData)) ;
			}
			function ReadErrorMsg($errorData)
			{
				$result = '' ;
				if(isset($errorData["message"]))
				{
					$result .= $errorData["message"] ;
				}
				return $result ;
			}
			public function ColumnsQuery(&$res)
			{
				if($res === false)
				{
					return false ;
				}
				$cols = array() ;
				$colCount = oci_num_fields($res) ;
				for($i=1; $i<=$colCount; $i++)
				{
					$cols[] = oci_field_name($res, $i) ;
				}
				return $cols ;
			}
			function ReadQuery(&$res)
			{
				$row = false;
				try
				{
					$row = oci_fetch_array($res, OCI_ASSOC + OCI_RETURN_NULLS + OCI_RETURN_LOBS) ;
					if(is_array($row))
					{
						$row = array_map(array(& $this, "DecodeRowValue"), $row) ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $row ;
			}
			function CloseQuery(&$res)
			{
				try
				{
					if(is_resource($res))
					{
						$OK = oci_free_statement($res) ;
						if($OK)
						{
							$res = false ;
						}
					}
				}
				catch(Exception $ex)
				{
				}
				$this->AutoFinalConnection() ;
			}
			function CallStoredProcSql($procName, $params=array())
			{
				$sql = '' ;
				$sql .= 'BEGIN '.$procName.'(' ;
				$i = 0 ;
				foreach($params as $name => $value)
				{
					if($name != $this->ExprKeyName)
					{
						$paramString = "" ;
						if($i > 0)
						{
							$sql .= ', ' ;
						}
						if(isset($params[$this->ExprKeyName][$name]))
						{
							$paramString = str_replace(
								$this->ExprParamPattern,
								$this->ParamPrefix.$name,
								$params[$this->ExprKeyName][$name]
							) ;
						}
						else
						{
							$paramString = $this->ParamPrefix.$name ;
						}
						$i++ ;
						$sql .= $paramString ;
					}
				}
				if($this->StoredProcUseCursor)
				{
					if($i > 0)
					{
						$sql .= ', ' ;
					}
					$sql .= $this->ParamPrefix.$this->StoredProcCursorName ;
				}
				$sql .= ') ;' ;
				$sql .= 'END ;' ;
				// echo $sql ;
				return $sql ;
			}
			function & OpenStoredProc($procName, $params=array())
			{
				$this->StoredProcQueryActive = true ;
				$this->StoredProcQuery = parent::OpenStoredProc($procName, $params) ;
				if($this->StoredProcUseCursor)
				{
					oci_execute($this->StoredProcCursor, OCI_DEFAULT) ;
					return $this->StoredProcCursor ;
				}
				return $this->StoredProcQuery ;
			}
			function CloseStoredProc(& $res)
			{
				$this->StoredProcQueryActive = false ;
				$this->StoredProcQuery = false ;
				$ok = $this->CloseQuery($res) ;
				$this->StoredProcCursor = false ;
				return $ok ;
			}
			function CallStoredProcSqlInto($procName, $params=array(), $outParams=array())
			{
				$sql = '' ;
				$sql .= 'BEGIN '.$procName.'(' ;
				$i = 0 ;
				foreach($params as $name => $value)
				{
					if($name != $this->ExprKeyName)
					{
						$paramString = "" ;
						if($i > 0)
						{
							$sql .= ', ' ;
						}
						if(isset($params[$this->ExprKeyName][$name]))
						{
							$paramString = str_ireplace(
								$this->ExprParamPattern,
								$this->ParamPrefix.$name,
								$params[$this->ExprKeyName][$name]
							) ;
						}
						else
						{
							$paramString = $this->ParamPrefix.$name ;
						}
						$i++ ;
						$sql .= $paramString ;
					}
				}
				foreach($outParams as $j => $outName)
				{
					if($j > 0 || $i > 0)
					{
						$sql .= ", " ;
					}
					$sql .= $this->ParamPrefix.$outName ;
				}
				$sql .= ') ;' ;
				$sql .= 'END ;' ;
				// echo $sql ;
				return $sql ;
			}
			function & PrepareSqlInto($sql, & $outResults, $params=array())
			{
				$this->CaptureQuery($sql, $params) ;
				$stmt = false ;
				try
				{
					$stmt = oci_parse($this->Connection, $sql) ;
					if($stmt !== false)
					{
						// print $sql.' '.print_r($params, true)."\n" ;
						foreach($params as $n => $v)
						{
							oci_bind_by_name($stmt, $this->ParamPrefix.$n, $params[$n]) ;
						}
						foreach($outResults as $n => $v)
						{
							oci_bind_by_name($stmt, $this->ParamPrefix.$n, $outResults[$n], 255) ;
						}
					}
					else
					{
						$this->SetConnectionExceptionFromOciError(oci_error($this->Connection)) ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $stmt ;
			}
			function FetchStoredProcInto($storedProc, $params=array(), $outParams=array())
			{
				$stmt = false ;
				if(! $this->InitConnection())
				{
					return $stmt ;
				}
				$this->ClearConnectionException() ;
				$outResults = array_fill_keys($outParams, "") ;
				$sql = $this->CallStoredProcSqlInto($storedProc, $params, $outParams) ;
				$stmt = $this->PrepareSqlInto($sql, $outResults, $params) ;
				if($stmt === false)
				{
					return $stmt ;
				}
				$res = false ;
				try
				{
					$res = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS) ;
					$exceptionMsg = "" ;
					if(! $res)
					{
						$exceptionMsg = $this->ReadErrorMsg(oci_error($stmt)) ;
						$this->SetConnectionException($exceptionMsg) ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, strval($res), $exceptionMsg) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				if($stmt !== false)
				{
					oci_free_statement($stmt) ;
					$stmt = false ;
				}
				$this->AutoFinalConnection() ;
				if($res)
				{
					return $outResults ;
				}
				else
				{
					return false ;
				}
			}
			function LimitSqlRowsReq($sql, $params=array(), $start=0, $limit=1000, $extra='')
			{
				$sql = 'select * from 
			(
				select * from (
					select MAIN_REQ.*, ROWNUM ROW_POS from ('.$sql.') MAIN_REQ
				) where ROW_POS < '.($start + 1 + $limit).'
			) where ROW_POS >= '.($start + 1) ;
				return $sql ;
			}
			public function CreateColumnDefinition()
			{
				return new OciColumnDefinition() ;
			}
			public function SqlColumnDefinitions($tableName, $schema='')
			{
				return 'select t1.*, case when t2.COLUMN_NAME IS NULL THEN 0 ELSE 1 END IS_KEY from (
    select * from cols WHERE UPPER(cols.table_name) =UPPER(:table_name)
) t1
left join (
    SELECT cols.CONSTRAINT_NAME CONSTRAINT_NAME, COLUMN_NAME FROM all_constraints cons, all_cons_columns cols WHERE cols.table_name =:table_name AND cons.constraint_type = \'P\' AND cons.constraint_name = cols.constraint_name AND cons.owner = cols.owner ORDER BY cols.position
) t2
on t1.COLUMN_NAME = t2.COLUMN_NAME' ;
			}
			protected function ParamsColumnDefinitions($tableName, $schema='')
			{
				return array('table_name' => $tableName) ;
			}
			public function CreateTableDefinition()
			{
				return new OciTableDefinition() ;
			}
			public function ParamsTableDefinitions($schema='')
			{
				if($schema == '' && isset($this->ConnectionParams["user"]))
					$schema = $this->ConnectionParams["user"] ;
				return array("schema" => $schema) ;
			}
			public function SqlTableDefinitions($schema='')
			{
				return 'select * from SYS.all_tables where owner=:schema' ;
			}
			function CloseCnx()
			{
				if(! $this->Connection)
				{
					return 1 ;
				}
				try
				{
					$res = (oci_close($this->Connection)) ? 1 : 0 ;
					if($res)
					{
						$this->Connection = false ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $res ;
			}
			function SqlConcat($list)
			{
				$sql = '' ;
				for($i=0; $i<count($list) ; $i++)
				{
					if($i > 0)
					{
						$sql .= " || " ;
					}
					$sql .= $list[$i] ;
				}
				return $sql ;
			}
			function SqlAddDays($expr, $val)
			{
				return "cast(".$expr." as date) + ".$val ;
			}
			function SqlAddHours($expr, $val)
			{
				return "cast(".$expr." as date) + (".$val." / 24)" ;
			}
			function SqlAddMinutes($expr, $val)
			{
				return "cast(".$expr." as date) + (".$val." / (24 * 60))" ;
			}
			function SqlAddSeconds($expr, $val)
			{
				return "cast(".$expr." as date) + (".$val." / (24 * 60 * 60))" ;
			}
			function SqlAddMonths($expr, $val)
			{
				return "add_months(cast(".$expr." as date), ".$val.")" ;
			}
			function SqlAddYears($expr, $val)
			{
				return "add_months(cast(".$expr." as date), ".$val." * 12)" ;
			}
			function SqlDateDiff($expr1, $expr2)
			{
				return "(cast(".$expr1." as date) - cast(".$expr2." as date)) * 24*60*60" ;
			}
			function SqlToInt($expr1)
			{
				return "CAST(".$expr1." AS INTEGER)" ;
			}
			function SqlToDouble($expr1)
			{
				return "CAST(".$expr1." AS DECIMAL)" ;
			}
			function SqlNow()
			{
				return "SYSDATE" ;
			}
			function SqlDateExpr($dateValue)
			{
				
			}
			function SqlIndexOf($expr, $search, $start=0)
			{
				if($start == 0)
					return "instr($expr, $search)" ;
				return "instr($expr, $search, $start)" ;
			}
			function SqlDateToStr($dateName)
			{
				return "TO_CHAR(".$dateName.", 'YYYY-MM-DD HH24:MI:SS')" ;
			}
		}

		class SqlSrvDB extends AbstractSqlDB
		{
			var $VendorName = "SQLSERVER" ;
			var $LoginTimeout = 0 ;
			function ExecFixCharacterEncoding()
			{
			}
			function EscapeVariableName($varName)
			{
				return "[".$varName."]" ;		
			}
			function EscapeTableName($tableName)
			{
				return "[".$tableName."]" ;		
			}
			function EscapeFieldName($tableName, $fieldName)
			{
				return "[".$tableName."].[".$fieldName."]" ;
			}
			function EscapeRowValue($rowValue)
			{
				if(is_numeric($rowValue))
					return $rowValue;
				$unpacked = unpack('H*hex', $rowValue);
				return '0x' . $unpacked['hex'];
			}
			function OpenCnx()
			{
				$this->ClearConnectionException() ;
				$OK = 0 ;
				if($this->ConnectCnx())
				{
					$OK = $this->SelectDBCnx() ;
				}
				return $OK ;
			}
			function ConnectCnx()
			{
				$res = 0 ;
				try
				{
					$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
					$connectionInfo = array("Database" => $schema, "UID" => $user, "PWD" => $password) ;
					if($this->AutoSetCharacterEncoding && $this->CharacterEncoding != '')
					{
						$connectionInfo["CharacterSet"] = strtoupper($this->CharacterEncoding) ;
					}
					if($this->LoginTimeout > 0)
					{
						$connectionInfo["LoginTimeout"] = $this->LoginTimeout ;
					}
					$this->Connection = sqlsrv_connect($server, $connectionInfo) ;
					if(! $this->Connection)
					{
						$res = 0 ;
						$this->SetConnectionException(sqlsrv_errors($this->Connection)) ;
					}
					else
					{
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function SelectDBCnx()
			{
				return true ;
			}
			function ConnectionErrString()
			{
				return sqlsrv_errors($this->Connection) ;
			}
			function SetConnectionExceptionFromCnx()
			{
				return $this->SetConnectionException($this->ConnectionErrString()) ;
			}
			function SetConnectionException($exception='')
			{
				if(is_array($exception))
				{
					$msg = '' ;
					foreach($exception as $i => $error)
					{
						$msg .= (($i > 0) ? "\n" : '').$error["message"] ;
					}
					parent::SetConnectionException($msg) ;
				}
				else
				{
					parent::SetConnectionException($exception) ;
				}
			}
			function & OpenQuery($sql, $params=array())
			{
				if(! $this->InitConnection())
				{
					return false ;
				}
				$this->ClearConnectionException() ;
				$this->CaptureQuery($sql, $params) ;
				$this->FixCharacterEncoding() ;
				$sql = $this->PrepareSql($sql, $params) ;
				// print_r($sql) ;
				$res = false ;
				try
				{
					$res = sqlsrv_query($this->Connection, $sql) ;
					$exceptionMsg = "" ;
					if(! $res)
					{
						$exceptionMsg = sqlsrv_errors(SQLSRV_ERR_ALL) ;
						$this->SetConnectionException($exceptionMsg) ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, ($res) ? "sqlsrv_object" : '', $exceptionMsg) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function ReadQuery(&$res)
			{
				$row = false;
				try
				{
					$row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $row ;
			}
			function CloseQuery(&$res)
			{
				try
				{
					if(is_resource($res))
					{
						$OK = sqlsrv_free_stmt($res) ;
						if($OK)
						{
							$res = false ;
						}
					}
				}
				catch(Exception $ex)
				{
				}
				$this->AutoFinalConnection() ;
			}
			function CloseCnx()
			{
				if(! $this->Connection)
				{
					return 1 ;
				}
				try
				{
					$res = (sqlsrv_close($this->Connection)) ? 1 : 0 ;
					if($res)
					{
						$this->Connection = false ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $res ;
			}
		}
		
		/**
		* Beta, not tested...
		*/
		class OdbcBaseDB extends AbstractSqlDB
		{
			var $DriverKey = "" ;
			var $DsnServerKey = "Server" ;
			var $DsnSchemaKey = "Database" ;
			function EscapeTableName($tableName)
			{
				return "[".$tableName."]" ;		
			}
			function EscapeFieldName($tableName, $fieldName)
			{
				return "[".$tableName."].[".$fieldName."]" ;
			}
			function EscapeRowValue($rowValue)
			{
				return "'".str_replace("'", "''", $rowValue)."'" ;
			}
			function ExtractDsn($server, $schema)
			{
				$dsn = "" ;
				if($this->DriverKey != "")
				{
					$dsn .= 'Driver={'.$this->DriverKey.'}' ;
					if($server != '')
					{
						$dsn .= ';'.$this->DsnServerKey.'='.$server ;
					}
					if($schema != "")
					{
						$dsn .= ';'.$this->DsnSchemaKey.'='.$schema ;
					}
				}
				else
				{
					if($server != "")
					{
						$dsn = $server ;
					}
				}
				return $dsn ;
			}
			function OpenCnx()
			{
				$this->ClearConnectionException() ;
				$OK = $this->ConnectCnx() ;
				return $OK ;
			}
			function ConnectCnx()
			{
				$res = false ;
				try
				{
					$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
					$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$dsn = $this->ExtractDsn($server, $schema) ;
					$this->Connection = odbc_connect($dsn, $user, $password) ;
					if(! $this->Connection)
					{
						$res = 0 ;
						$this->SetConnectionException(odbc_errormsg()) ;
					}
					else
					{
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function ConnectionErrString()
			{
				return odbc_errormsg($this->Connection) ;
			}
			function SetConnectionExceptionFromCnx()
			{
				return $this->SetConnectionException($this->ConnectionErrString()) ;
			}
			function & PrepareSql($sql, $params=array(), $realParamNames=array())
			{
				$stmt = false ;
				try
				{
					$stmtSql =  $this->ReplaceParamsToSql($sql, $realParamNames, "?") ;
					$stmt = odbc_prepare($this->Connection, $stmtSql) ;
				}
				catch(Exception $ex)
				{
				}
				return $stmt ;
			}
			function & OpenQuery($sql, $params=array())
			{
				if(! $this->InitConnection())
				{
					return false ;
				}
				$this->ClearConnectionException() ;
				$realParamNames = $this->ExtractParamsFromSql($sql, $params) ;
				$stmt = $this->PrepareSql($sql, $params, $realParamNames) ;
				$res = false ;
				try
				{
					$stmtParams = $this->ExtractParamValues($realParamNames, $params) ;
					$res = odbc_execute($stmt, $stmtParams) ;
					$exceptionMsg = "" ;
					if(! $res)
					{
						$exceptionMsg = odbc_errormsg($this->Connection) ;
						$this->SetConnectionException($exceptionMsg) ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, ($res) ? "odbc_object" : '', $exceptionMsg) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				if(! $res)
					return false ;
				return $stmt ;
			}
			function ReadQuery(&$res)
			{
				$row = false;
				try
				{
					$row = odbc_fetch_array($res) ;
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $row ;
			}
			function CloseQuery(&$res)
			{
				try
				{
					if(is_resource($res))
					{
						$OK = odbc_free_result($res) ;
						if($OK)
						{
							$res = false ;
						}
					}
				}
				catch(Exception $ex)
				{
				}
				$this->AutoFinalConnection() ;
			}
			function CloseCnx()
			{
				if(! $this->Connection)
				{
					return 1 ;
				}
				try
				{
					$res = (odbc_close($this->Connection)) ? 1 : 0 ;
					if($res)
					{
						$this->Connection = false ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $res ;
			}
		}
		class SqlServerOdbcDB extends OdbcBaseDB
		{
			var $LinuxDriverVersion = "11" ;
			var $Win32DriverVersion = "10.0" ;
			function SqlConcat($list)
			{
				if(count($list) == 0)
					return ;
				if(count($list) == 1)
					return $list[0] ;
				$sql = "" ;
				for($i=0; $i<count($list) ; $i++)
				{
					if($i > 0)
					{
						$sql .= " + " ;
					}
					$sql .= $list[$i] ;
				}
				return $sql ;
			}
			function SqlToDateTime($expr)
			{
				return "convert(datetime, ".$expr.")" ;
			}
			function SqlToTimestamp($expr)
			{
				return "convert(datetime, ".$expr.")" ;
			}
			function SqlAddSeconds($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATEADD(second, '.$val.', '.$expr.')' ;
			}
			function SqlAddMinutes($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATEADD(minute, '.$val.', '.$expr.')' ;
			}
			function SqlAddHours($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATEADD(hour, '.$val.', '.$expr.')' ;
			}
			function SqlAddDays($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATEADD(day, '.$val.', '.$expr.')' ;
			}
			function SqlAddMonths($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATEADD(month, '.$val.', '.$expr.')' ;
			}
			function SqlAddYears($expr, $val)
			{
				if($val == 0)
				{
					return $expr ;
				}
				return 'DATEADD(year, '.$val.', '.$expr.')' ;
			}
			function SqlDateDiff($expr1, $expr2)
			{
				return "datediff(".$expr1.", ".$expr2.", second)" ;
			}
			function SqlReplace($expr, $search, $replace, $start=0)
			{
				return "replace($expr, $search, $replace)" ;
			}
			function SqlLength($expr)
			{
				return "LENGTH($expr)" ;
			}
			function SqlSubstr($expr, $start, $length=0)
			{
				$str = "SUBSTR($expr, $start)" ;
				if($length > 0)
				{
					$str = "SUBSTR($expr, $start, $length)" ;
				}
				return $str ;
			}
			function SqlIndexOf($expr, $search, $start=0)
			{
				$str = "CHARINDEX($expr, $search)" ;
				if($start > 0)
				{
					$str = "CHARINDEX(substr($expr, $start), $search)" ;
				}
				return $str ;
			}
			function SqlNow()
			{
				return "getdate()" ;
			}
			function SqlIsNull($expr)
			{
				return "$expr IS NULL" ;
			}
			function SqlStrToDateTime($dateName)
			{
				return 'convert(datetime, '.$dateName.')' ;
			}
			function SqlStrToDate($dateName)
			{
				return 'convert(date, '.$dateName.')' ;
			}
			function SqlDatePart($dateName)
			{
				return 'DATEADD(dd, 0, DATEDIFF(dd, 0, '.$dateName.'))' ;
			}
			function SqlTimePart($dateName)
			{
				return 'convert(char(8), '.$dateName.', 108)' ;
			}
			function SqlDateToStr($dateName)
			{
				return 'convert(char(10), '.$dateName.')' ;
			}
			function SqlDateTimeToStr($dateName)
			{
				return 'convert(char(19), '.$dateName.')' ;
			}
			function SqlDateToStrFr($dateName, $includeHour=0)
			{
				$size = '10' ;
				if($includeHour)
					$size .= '19' ;
				return 'convert(char('.$size.'), '.$dateName.', 131)' ;
			}
			function SqlToInt($expression)
			{
				return 'CONVERT (int, '.$expression.')' ;
			}
			function SqlToDouble($expression)
			{
				return 'CONVERT(double, '.$expression.')' ;
			}
			function SqlToString($expression)
			{
				return 'convert (nvarchar(MAX), '.$expression.')' ;
			}
			function ExtractDsn($server, $schema)
			{
				if($this->DriverKey == "")
				{
					if(stripos(PHP_OS, "WIN32") !== false || stripos(PHP_OS, "WINNT") !== false)
					{
						$this->DriverKey = "SQL Server Native Client ".$this->Win32DriverVersion ;
					}
					else
					{
						$this->DriverKey = "ODBC Driver ".$this->LinuxDriverVersion." for SQL Server" ;
					}
				}
				return parent::ExtractDsn($server, $schema) ;
			}
		}
		
		class CommonEntityRow
		{
			public $RawData = array() ;
			public $StoreData = 0 ;
			public $ParentDatabase = null ;
			public $AutoMapColumns = 0 ;
			public function SetParentDatabase(& $database)
			{
				$this->ParentDatabase = & $database ;
			}
			public function ToRawData()
			{
				return array() ;
			}
			public function ToEditData()
			{
				return array() ;
			}
			public function ToKeyData()
			{
				return array() ;
			}
			public function EncodeExprName($valeur)
			{
				$result = $valeur ;
				$result = str_replace(array(' ', "\r", "\n", "\t"), '_', $result) ;
				$result = preg_replace_callback('/(_| |\-)([a-z0-9])/i', create_function('$matches', 'return strtoupper($matches[2]) ;'), $result) ;
				return $result ;
			}
			public function EncodeAttrName($valeur)
			{
				$result = $valeur ;
				$result = ucfirst($this->EncodeExpressionVariable($result)) ;
				return $result ;
			}
			protected function MapFromRow($row)
			{
				if(! $this->AutoMapColumns)
				{
					return ;
				}
				foreach($row as $name => $val)
				{
					$attrName = $this->EncodeAttrName($name) ;
					if(property_exists($this, $attrName))
					{
						$this->$attrName = $val ;
					}
				}
			}
			public function ImportConfigFromRow($row)
			{
				if($this->StoreData)
				{
					$this->RawData = $row ;
				}
				$this->MapFromRow($row) ;
				$this->UpdateConfigBeforeImport() ;
				foreach($row as $colName => $colValue)
				{
					$this->ImportConfigFromRowValue($colName, $colValue) ;
				}
				$this->UpdateConfigAfterImport() ;
			}
			public function UpdateConfigBeforeImport()
			{
			}
			public function UpdateConfigAfterImport()
			{
			}
			protected function ImportConfigFromRowValue($name, $value)
			{
				$success = 1 ;
				switch(strtoupper($name))
				{
					default :
					{
						$success = 0 ;
					}
					break ;
				}
				return $success ;
			}
		}
		class CommonEntityRowCollection
		{
			public $ParentDatabase ;
			public $ItemClassName = "" ;
			public $FetchAllStoredProcName = "" ;
			public $FetchRangeStoredProcName = "" ;
			public $FetchRangeOffsetParamName = "0" ;
			public $FetchRangeMaxParamName = "1" ;
			public $FetchTotalStoredProcName = "" ;
			public $FetchAllSqlText = "" ;
			public $FetchTotalSqlText = "" ;
			public $FetchRangeSqlText = "" ;
			public function __construct(& $parent)
			{
				$this->InitConfig($parent) ;
			}
			protected function InitConfig(& $parent)
			{
				$this->ParentDatabase = & $parent ;
			}
			public function TotalItems()
			{
			}
			public function AllItems()
			{
			}
			public function RangeItems($offset, $max)
			{
			}
			public function LoopItems($max)
			{
			}
		}
	
		class MysqlPdoDB extends MysqlDB
		{
			public $SetCharacterEncodingOnFetch = 1 ;
			public $AutoSetCharacterEncoding = 1 ;
			public $CharacterEncoding = "utf8" ;
			public $OpenOptions = array() ;
			function ExecFixCharacterEncoding()
			{
				if($this->CharacterEncoding != '')
				{
					$this->Connection->exec('SET NAMES '.$this->CharacterEncoding) ;
				}
			}
			function EscapeRowValue($rowValue)
			{
				$cnx = (is_object($this->StoredProcConnection)) ? $this->StoredProcConnection : $this->Connection ;
				return "'".$cnx->quote($rowValue)."'" ;
			}
			function OpenCnx()
			{
				$this->ClearConnectionException() ;
				return $this->ConnectCnx() ;
			}
			function ExtractConnectionString()
			{
				$server = (isset($this->ConnectionParams["server"])) ? $this->ConnectionParams["server"] : "localhost" ;
				$schema = (isset($this->ConnectionParams["schema"])) ? $this->ConnectionParams["schema"] : "" ;
				$port = (isset($this->ConnectionParams["port"])) ? $this->ConnectionParams["port"] : "" ;
				$connectionStr = "mysql:host=$server";
				if($port != "")
				{
					$connectionStr .= ";port=$port" ;
				}
				$connectionStr .= ";dbname=$schema" ;
				if($this->CharacterEncoding != "" && $this->MustSetCharacterEncoding == 1)
				{
					$connectionStr .= ";charset=".$this->CharacterEncoding ;
				}
				return $connectionStr ;
			}
			function ConnectCnx()
			{
				$res = 0 ;
				try
				{
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$connectionStr = $this->ExtractConnectionString() ;
					$this->Connection = new PDO($connectionStr, $user, $password, $this->OpenOptions) ;
					if($this->Connection)
					{
						$this->Connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function ConnectionErrString()
			{
				$cnx = (is_object($this->StoredProcConnection)) ? $this->StoredProcConnection : $this->Connection ;
				if(! is_object($cnx))
				{
					return null ;
				}
				$errorCode = $cnx->errorCode() ;
				if($errorCode !== null && $errorCode !== "00000")
				{
					$errorInfo = $cnx->errorInfo() ;
					return $errorCode." : ".$errorInfo[2] ;
				}
				return "" ;
			}
			protected function ExtractStmtException(& $stmt)
			{
				$errorCode = $stmt->errorCode() ;
				if($errorCode !== null && $errorCode !== "00000")
				{
					$errorInfo = $stmt->errorInfo() ;
					return $errorCode." : ".$errorInfo[2] ;
				}
				return "" ;
			}
			protected function SetConnectionExceptionFromStmt(& $stmt)
			{
				return $this->SetConnectionException($this->ExtractStmtException($stmt)) ;
			}
			function SetConnectionExceptionFromCnx()
			{
				return $this->SetConnectionException($this->ConnectionErrString()) ;
			}
			function OpenStoredProcCnx()
			{
				$res = 0 ;
				try
				{
					$user = (isset($this->ConnectionParams["user"])) ? $this->ConnectionParams["user"] : "root" ;
					$password = (isset($this->ConnectionParams["password"])) ? $this->ConnectionParams["password"] : "" ;
					$connectionStr = $this->ExtractConnectionString() ;
					$this->StoredProcConnection = new PDO($connectionStr, $user, $password) ;
					if($this->StoredProcConnection)
					{
						$this->StoredProcConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$res = 1 ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function & OpenStoredProc($procName, $params=array())
			{
				$this->StoredProcQuery = false ;
				if(! $this->OpenStoredProcCnx())
				{
					return $this->StoredProcQuery ;
				}
				try
				{
					$this->StoredProcQuery = $this->StoredProcConnection->query($this->CallStoredProcSql($procName, $params)) ;
				}
				catch(PDOException $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				if($this->StoredProcQuery == false)
				{
					$this->SetConnectionExceptionFromStmt($this->StoredProcConnection) ;
				}
				return $this->StoredProcQuery ;
			}
			function CloseStoredProc(& $res)
			{
				if($this->StoredProcQuery !== false)
				{
					$this->StoredProcQuery->closeCursor() ;
					$this->StoredProcQuery = null ;
					$this->StoredProcQuery = false ;
				}
				$this->StoredProcConnection = null ;
				$this->StoredProcConnection = false ;
				return 1 ;
			}
			function & OpenQuery($sql, $params=array())
			{
				if(! $this->InitConnection())
				{
					return false ;
				}
				$this->ClearConnectionException() ;
				foreach($params as $name => $val)
				{
					if(is_int($name))
					{
						$sql = str_replace(":".$name, ":param_".$name, $sql) ;
					}
				}
				$this->CaptureQuery($sql, $params) ;
				$this->FixCharacterEncoding() ;
				$res = false ;
				try
				{
					$res = $this->Connection->prepare($sql) ;
					if(! $res)
					{
						$this->SetConnectionExceptionFromCnx() ;
						$res = false ;
						return $res ;
					}
					$paramsBound = array() ;
					foreach($params as $name => $val)
					{
						$paramType = PDO::PARAM_STR ;
						if(is_int($val))
						{
							$paramType = PDO::PARAM_INT ;
						}
						elseif(is_null($val))
						{
							$params[$name] = '' ;
						}
						if(is_int($name))
						{
							$res->bindParam("param_".$name, $params[$name], $paramType) ;
							$paramsBound["param_".$name] = $val ;
						}
						else
						{
							$res->bindParam($name, $params[$name], $paramType) ;
							$paramsBound[$name] = $val ;
						}
					}
					$ok = $res->execute($paramsBound) ;
					if($res->errorCode() !== null && $res->errorCode() !== "00000")
					{
						$this->SetConnectionExceptionFromStmt($res) ;
						$res = null ;
						$res = false ;
					}
					$this->LaunchSqlProfiler($sql, ($res) ? "pdo_mysql_object" : '', $exceptionMsg) ;
				}
				catch(PDOException $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
				}
				return $res ;
			}
			function ReadQuery(&$res)
			{
				$row = false;
				try
				{
					$row = $res->fetch(PDO::FETCH_ASSOC) ;
					if(is_array($row))
					{
						$row = array_map(array(& $this, "DecodeRowValue"), $row) ;
					}
				}
				catch(Exception $ex)
				{
					$this->SetConnectionException($ex->getMessage()) ;
					$row = false ;
				}
				return $row ;
			}
			function CloseQuery(&$res)
			{
				try
				{
					if(is_object($res))
					{
						$res->closeCursor() ;
						if($OK)
						{
							$res = null ;
							$res = false ;
						}
					}
				}
				catch(Exception $ex)
				{
				}
				$this->AutoFinalConnection() ;
			}
			function CloseCnx()
			{
				$this->Connection = null ;
				$this->Connection = false ;
				return 1 ;
			}
		}
		
	}

?>