<?php
	
	if(! defined('AK_MEMBER_CONNECTION'))
	{
		define('AK_MEMBER_CONNECTION', 1) ;
		
		class AkMemberConnectionMgr
		{
			public $ConnectionTable = 'membership_connection' ;
			public $IdConnectionColumn = 'id' ;
			public $SessionIdConnectionColumn = 'session_id' ;
			public $MemberConnectionColumn = 'member_id' ;
			public $DateRegConnectionColumn = 'date_action' ;
			public $MemberConnectionForeignKey = 'id' ;
			public $MemberTable = 'membership_member' ;
			public $TimeoutInactiveMembers = 300 ;
			public $MaxSessions = 10 ;
			public $Database = null ;
			public $MsgBadConfig = "The member connection mgr is not configured !!!" ;
			public $FailureTable = 'membership_cnx_fails' ;
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
			}
			protected function IsConfigured()
			{
				$ok = 1 ;
				if($this->Database == null || get_class($this->Database) == get_class($this))
					$ok = 0 ;
				return $ok ;
			}
			protected function DieScript($msg)
			{
				print $msg ;
				exit ;
			}
			protected function SqlCountSessions($memberId)
			{
				$sql = '' ;
				$sql .= 'select count(0) TOTAL from '.$this->Database->EscapeTableName($this->ConnectionTable) ;
				$sql .= ' where '.$this->Database->EscapeVariableName($this->MemberConnectionColumn).'='.$this->Database->ParamPrefix.'memberId' ;
				$sql .= ' and '.$this->Database->EscapeVariableName($this->SessionIdConnectionColumn).' <> '.$this->Database->ParamPrefix.'sessionId' ;
				return $sql ;
			}
			protected function ClearInactiveSessions()
			{
				if($this->TimeoutInactiveMembers == 0)
				{
					return ;
				}
				$colDateEnreg = $this->Database->EscapeVariableName($this->DateRegConnectionColumn) ;
				$sql = 'delete from '.$this->Database->EscapeTableName($this->ConnectionTable).' where '.$this->Database->SqlDateDiff($this->Database->SqlNow(), $colDateEnreg).' >= '.intval($this->TimeoutInactiveMembers) ;
				$this->Database->RunSql($sql) ;
			}
			public function ClearSession()
			{
				$this->DeleteCurrentSession() ;
			}
			protected function DeleteCurrentSession()
			{
				$sql = 'delete from '.$this->Database->EscapeTableName($this->ConnectionTable).' where '.$this->Database->EscapeVariableName($this->SessionIdConnectionColumn).'='.$this->Database->ParamPrefix.'sessionId' ;
				$this->Database->RunSql($sql, array('sessionId' => session_id())) ;
			}
			public function Register($memberId)
			{
				if(session_id() == '')
					@session_start() ;
				$ok = 1 ;
				if(! $this->IsConfigured())
				{
					$this->DieScript($this->MsgBadConfig) ;
				}
				$this->ClearInactiveSessions() ;
				if($this->MaxSessions > 0)
				{
					$sql = $this->SqlCountSessions($memberId) ;
					$total = $this->Database->FetchSqlValue(
						$sql,
						array('memberId' => $memberId, 'sessionId' => session_id()), 'TOTAL'
					) ;
					if($total >= $this->MaxSessions)
					{
						$this->Database->InsertRow(
							$this->FailureTable,
							$this->ExtractConnectionRow($memberId)
						) ;
						return 0 ;
					}
				}
				$this->DeleteCurrentSession() ;
				$this->Database->InsertRow(
					$this->ConnectionTable,
					$this->ExtractConnectionRow($memberId)
				) ;
				return 1;
			}
			protected function ExtractConnectionRow($memberId)
			{
				$row = array(
					$this->DateRegConnectionColumn => '0',
					$this->MemberConnectionColumn => $memberId,
					$this->SessionIdConnectionColumn => session_id(),
					$this->Database->ExprKeyName => array(
						$this->DateRegConnectionColumn => $this->Database->SqlAddSeconds($this->Database->SqlNow(), '<self>')
					)
				) ;
				return $row ;
			}
		}
	}
	
?>