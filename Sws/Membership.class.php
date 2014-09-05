<?php
	
	if(! defined('MEMBERSHIP_SWS'))
	{
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		define('MEMBERSHIP_SWS', 1) ;
		
		class MembershipSws extends AkSqlMembership
		{
			public $PasswordMemberExpr = "password" ;
			public $RootMemberId = 1 ;
			public $GuestMemberId = "2" ;
			public $UseGuestMember = 1 ;
		}
		
	}
	
?>