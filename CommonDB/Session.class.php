<?php
	
	include dirname(__FILE__)."/Base.class.php" ;
	
	class CommonDBSessionStepStatus
	{
		const NotStarted = 0 ;
		const Started = 1 ;
		const Failure = 2 ;
		const Success = 3 ;
		const DatabaseNotFound = 4 ;
		const DatabaseMethodNotFound = 5 ;
		const SessionNotFound = 6 ;
	}
	class CommonDBSessionStepExecutionFailure
	{
		const Ignore = 0 ;
		const AlwaysContinue = 1 ;
		const Abort = 2 ;
	}
	
	class CommonDBSessionStep
	{
		public $Results = array() ;
		public $Session = null ;
		public $CallMethod = "" ;
		public $Status = 0 ;
		public $ExecutionFailure = 0 ;
		public $SqlText = "" ;
		public $SqlParams = array() ;
		function __construct(& $session, $sqlText, $sqlParams=array())
		{
			$this->Session = & $session ;
			$this->Session->Steps[] = & $this ;
			$this->SqlText = $sqlText ;
			$this->Status = CommonDBSessionStepStatus::NotStarted ;
			$this->ExecutionFailure = CommonDBSessionStepExecutionFailure::AlwaysContinue ;
		}
		function Execute()
		{
			$this->Status = CommonDBSessionStepStatus::Started ;
			if(! $this->Session)
			{
				$this->Status = CommonDBSessionStepStatus::SessionNotFound ;
				return $this->Status ;
			}
			if(! $this->Session->Database)
			{
				$this->Status = CommonDBSessionStepStatus::DatabaseNotFound ;
				return $this->Status ;
			}
			if(! method_exists($this->Session->Database, $this->CallMethod))
			{
				$this->Status = CommonDBSessionStepStatus::DatabaseMethodNotFound ;
				return $this->Status ;
			}
			$callMethod = $this->CallMethod ;
			$this->Results = $this->Session->Database->$callMethod($this->SqlText, $this->SqlParams) ;
			if($this->Results !== false)
			{
				$this->Status = CommonDBSessionStepStatus::Success ;
			}
			else
			{
				$this->Status = CommonDBSessionStepStatus::Failure ;
			}
			return $this->Status ;
		}
	}
	class CommonDBSessionRunSqlStep extends CommonDBSessionStep
	{
		public $CallMethod = "RunSql" ;
	}
	class CommonDBSessionFetchSqlRowStep extends CommonDBSessionStep
	{
		public $CallMethod = "FetchSqlRow" ;
	}
	class CommonDBSessionFetchSqlRowsStep extends CommonDBSessionStep
	{
		public $CallMethod = "FetchSqlRows" ;
	}
	
	class CommonDBSessionParseStepResult
	{
		const BreakExecution = 0 ;
		const ContinueExecution = -1 ;
		const NextStep = 1 ;
	}
	
	class CommonDBSession
	{
		public $Steps = array() ;
		public $StepStatus = array() ;
		public $StepFailures = array() ;
		public $StepSuccess = array() ;
		public $ExecutionSuccess = 0 ;
		public $Database = null ;
		function __construct(& $database)
		{
			$this->Database = & $database ;
		}
		function EmptyStepResults()
		{
			$this->ExecutionSuccess = 0 ;
			$this->StepFailures = array() ;
			$this->StepSuccess = array() ;
			$this->StepStatus = array() ;
		}
		public function Execute()
		{
			$this->EmptyStepResults() ;
			if(! $this->Database)
			{
				return $this->ExecutionSuccess ;
			}
			$closeConnection = $this->Database->AutoCloseConnection ;
			$this->Database->AutoCloseConnection = false ;
			$this->Database->InitConnection() ;
			if(count($this->Steps))
			{
				$this->ExecutionSuccess = 1 ;
			}
			for($i=0; $i<count($this->Steps); $i++)
			{
				$step = & $this->Steps[$i] ;
				$step->Execute() ;
				$result = $this->ParseStepResult($step, $i) ;
				if($result == CommonDBSessionParseStepResult::BreakExecution)
				{
					break ;
				}
			}
			$this->Database->FinalConnection() ;
			$this->Database->AutoCloseConnection = $closeConnection ;
			return $this->ExecutionSuccess ;
		}
		function ParseStepResult(& $step, $i)
		{
			$result = CommonDBSessionParseStepResult::NextStep ;
			$this->StepStatus[$i] = $step->Status ;
			if($step->Status != CommonDBSessionStepStatus::Success)
			{
				$this->ExecutionSuccess = 0 ;
				if($step->ExecutionFailure == CommonDBSessionStepExecutionFailure::Ignore)
				{
					$result = CommonDBSessionParseStepResult::ContinueExecution ;
					return $result ;
				}
				$this->StepFailures[] = $i ;
				if($step->ExecutionFailure == CommonDBSessionStepExecutionFailure::Abort)
				{
					$result = CommonDBSessionParseStepResult::BreakExecution ;
					return $result ;
				}
			}
			else
			{
				$this->StepSuccess[] = $i ;
			}
			return $result ;
		}
	}
	
?>