<?php
	
	if(! defined('AK_CORE'))
	{
		if(! defined('UTILS_INCLUDED'))
		{
			include dirname(__FILE__)."/../misc/utils.php" ;
		}
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		define('AK_CORE', 1) ;
		
		class AkObject
		{
			public $ObjectName = "" ;
			public function LoadConfig()
			{
			}
			public function NullValue()
			{
				$nullValue = null ;
				return $nullValue ;
			}
		}
		
		class AkItemBase extends AkObject
		{
			public $ParentObject = null ;
			public $ItemName = "" ;
			public function __construct(& $parent)
			{
				$this->ParentObject = & $parent ;
				$this->InitConfig($parent) ;
			}
			protected function InitConfig(& $parent)
			{
			}
		}
	}
	
?>