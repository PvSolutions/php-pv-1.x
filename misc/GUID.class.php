<?php
	
	if(! defined('GUID_INCLUDED'))
	{
		define("GUID_INCLUDED", 1) ;
		
		class Guid
		{
			public $Hyphen = '-' ;
			public $UUid = '' ;
			public $CharId = '' ;
			public $LeftBrace = '{' ;
			public $RightBrace = '}' ;
			function getGUID()
			{
				if (function_exists('com_create_guid'))
				{
					return com_create_guid();
				}
				else
				{
					mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
					$charid = strtoupper(md5(uniqid(rand(), true)));
					$hyphen = $this->Hyphen ;// "-"
					$uuid = $this->LeftBrace// "{"
					.substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12)
					.$this->RightBrace ;// "}"
					return $uuid;
				}
			}
		}
	}
	
?>