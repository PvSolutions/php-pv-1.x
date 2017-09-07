<?php
	
	if(! defined("COMMON_ENCODING_SET_DB_INCLUDED"))
	{
		define("COMMON_ENCODING_SET_DB_INCLUDED", 1) ;
		
		class EncodingSetBaseCommonDB
		{
			public function PrepareDB(& $db)
			{
			}
			public function PrepareRunQuery(& $db)
			{
			}
			public function PrepareFetchRowsQuery(& $db)
			{
			}
			public function EncodeValue(& $db, $value)
			{
				return $value ;
			}
			public function DecodeValue(& $db, $value)
			{
				return $value ;
			}
			public function ReleaseRunQuery(& $db)
			{
			}
			public function ReleaseFetchRowsQuery(& $db)
			{
			}
			public function ReleaseDB(& $db)
			{
			}
		}
		class DefaultEncodingSetDB extends EncodingSetBaseCommonDB
		{
		}
		
		class Utf8EncodingSetMysqlDB extends DefaultEncodingSetDB
		{
			protected $InputQueryDone = false ;
			protected $OutputQueryDone = false ;
			public function PrepareDB(& $db)
			{
			}
			public function PrepareRunQuery(& $db)
			{
				if(! $this->InputQueryDone)
				{
					mysql_query('SET CHARACTER SET `UTF8`', $db->Connection) ;
					$this->InputQueryDone = true ;
				}
			}
			public function PrepareFetchRowsQuery(& $db)
			{
				$this->PrepareRunQuery($db) ;
			}
			public function EncodeValue(& $db, $value)
			{
				return $value ;
			}
			public function DecodeValue(& $db, $value)
			{
				return $value ;
			}
			public function ReleaseRunQuery(& $db)
			{
			}
			public function ReleaseFetchRowsQuery(& $db)
			{
			}
			public function ReleaseDB(& $db)
			{
			}
		}
	}
	
?>