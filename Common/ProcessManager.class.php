<?php
	
	if(! defined('PROCESS_MANAGER_INCLUDED'))
	{
		define('PROCESS_MANAGER_INCLUDED', 1) ;
		
		if(! defined("PROCESS_MANAGER_GENERATE_CURRENT"))
		{
			define('PROCESS_MANAGER_GENERATE_CURRENT', 1) ;
		}
		
		class OsProcessPipe
		{
			const InputNo = 0 ;
			const OutputNo = 1 ;
			const ErrNo = 2 ;
			protected $ProcessRes = false ;
			protected $Pipes = array() ;
			public $ErrorFilePath ;
			public $StoreError = true ;
			public $EnvData = array() ;
			public $CurrentDirPath = null ;
			public $LastLimitFound = false ;
			public $ReadTimeout = 2 ;
			public $ReadMaxLength = 1024 ;
			function FixReadTimeout(&$handle)
			{
				stream_set_timeout($handle, $this->ReadTimeout) ;
			}
			public function Open($cmd, $params=array())
			{
				$descriptorSpec = array(
					self::InputNo => array("pipe", "r"),
					self::OutputNo => array("pipe", "w"),
				);
				if($this->StoreError)
				{
					if($this->ErrorFilePath != "")
					{
						$descriptorSpec[self::ErrNo] = array("file", $this->ErrorFilePath, "a") ;
					}
					else
					{
						$descriptorSpec[self::ErrNo] = array("pipe", "w") ;
					}
				}
				$this->ProcessRes = @proc_open($cmd, $descriptorSpec, $this->Pipes, $this->CurrentDirPath, $this->EnvData) ;
				return ($this->ProcessRes !== false) ;
			}
			public function Write($ctn)
			{
				if(! $this->ProcessRes)
					return false ;
				$this->FixReadTimeout($this->Pipes[self::InputNo]) ;
				$ok = fwrite($this->Pipes[self::InputNo], $ctn) ;
				fflush($this->Pipes[self::InputNo]) ;
				return $ok ;
			}
			public function ReadHandle(&$handle)
			{
				// stream_set_blocking($handle, false) ;
				$this->CloseInput() ;
				if(! $this->ProcessRes || feof($handle))
					return false ;
				$this->FixReadTimeout($handle) ;
				// $line = $this->fGetsPending($handle, $this->ReadMaxLength) ;
				$line = fread($handle, $this->ReadMaxLength) ;
				// echo $line ;
				return $line ;
			}
			public function ReadHandleUntil(&$readHandle, $limit="")
			{
				$res = "" ;
				$this->LastLimitFound = false ;
				while(($line = $this->ReadHandle($readHandle)) !== false)
				{
					$res .= $line ;
					if($limit == "")
					{
						continue ;
					}
					$posLimit = strpos($res, $limit) ;
					if($posLimit !== false)
					{
						$res = substr($res, $posLimit + strlen($limit), strlen($res) - $posLimit) ;
						$this->LastLimitFound = true ;
						break ;
					}
					if($line == '')
						break ;
				}
				return $res ;
			}
			public function Read()
			{
				return $this->ReadHandle($this->Pipes[self::OutputNo]) ;
			}
			public function ReadUntil($limit)
			{
				return $this->ReadHandleUntil($this->Pipes[self::OutputNo], $limit) ;
			}
			public function ReadError()
			{
				return $this->ReadHandleUntil($this->Pipes[self::ErrNo], "") ;
			}
			public function GetError()
			{
				return $this->ReadHandleUntil($this->Pipes[self::ErrNo], "") ;
			}
			public function CloseErr()
			{
				if(! isset($this->Pipes[self::ErrNo]))
					return true ;
				if(! $this->Pipes[self::ErrNo])
					return false ;
				$ok = fclose($this->Pipes[self::ErrNo]) ;
				$this->Pipes[self::ErrNo] = false ;
				return $ok ;
			}
			public function CloseOutput()
			{
				if(! $this->Pipes[self::OutputNo])
					return false ;
				$ok = fclose($this->Pipes[self::OutputNo]) ;
				$this->Pipes[self::OutputNo] = false ;
				return $ok ;
			}
			public function CloseInput()
			{
				if(! $this->Pipes[self::InputNo])
					return false ;
				$ok = fclose($this->Pipes[self::InputNo]) ;
				$this->Pipes[self::InputNo] = false ;
				return $ok ;
			}
			public function ReadUntilEOF()
			{
				return $this->ReadUntil("") ;
			}
			public function Close()
			{
				if(! $this->ProcessRes)
					return false ;
				$pipeCount = count($this->Pipes) ;
				$this->CloseInput() ;
				$this->CloseOutput() ;
				$this->CloseErr() ;
				proc_close($this->ProcessRes) ;
			}
		}
		class LinuxProcessPipe extends OsProcessPipe
		{
		}
		class WinProcessPipe extends OsProcessPipe
		{
		}
		
		class OsProcessManager
		{
			var $LastCommand = "" ;
			var $LastResponse = "" ;
			var $LastException = "" ;
			var $ReadTimeout = 20 ;
			var $LastLimitFound = false ;
			public static function & Current()
			{
				$osProcessMgr = null ;
				if(PHP_OS == "WINNT" || PHP_OS == "WIN32")
				{
					$osProcessMgr = new WinProcessManager() ;
				}
				else
				{
					$osProcessMgr = new LinuxProcessManager() ;
				}
				return $osProcessMgr ;
			}
			function FixReadTimeout(&$handle)
			{
				stream_set_timeout($handle, $this->ReadTimeout) ;
			}
			function clearLastCommand()
			{
				$this->setLastCommand("") ;
				$this->LastException = "" ;
			}
			function setLastCommand($cmd)
			{
				$this->LastCommand = $cmd ;
			}
			function clearLastResponse()
			{
				$this->setLastResponse("") ;
			}
			function setLastResponse($cmd)
			{
				$this->LastResponse = $cmd ;
			}
			function RunAsyncCommandString($cmd)
			{
				return $cmd ;
			}
			function RunAsync($cmd)
			{
				return ($this->Run($cmd, false) !== false) ;
			}
			public function OpenPipes($cmd)
			{
				
			}
			function & OpenProcessExecution($cmd, $mode='r')
			{
				$handle = false ;
				try
				{
					$handle = @popen($cmd, $mode) ;
				}
				catch(Exception $ex)
				{
					$this->LastException = $ex->getMessage() ;
				}
				return $handle ;
			}
			function ReadProcessExecution(&$handle)
			{
				if(! $handle || feof($handle))
					return false ;
				$this->FixReadTimeout($handle) ;
				return fgets($handle);
			}
			function ReadProcessExecutionUntilEOF(&$handle)
			{
				return $this->ReadProcessExecutionUntil($handle, "") ;
			}
			function ReadProcessExecutionUntil(&$handle, $limit="")
			{
				$res = "" ;
				$this->LastLimitFound = false ;
				while(($line = $this->ReadProcessExecution($handle)) !== false)
				{
					$res .= $line ;
					if($limit == "")
					{
						continue ;
					}
					$posLimit = strpos($res, $limit) ;
					if($posLimit !== false)
					{
						$res = substr($res, $posLimit + strlen($limit), strlen($res) - $posLimit) ;
						$this->LastLimitFound = true ;
						break ;
					}
				}
				return $res ;
			}
			function WriteProcessExecution(&$handle, $ctn)
			{
				if(! $handle)
					return false ;
				return fwrite($handle, $ctn) ;
			}
			function CloseProcessExecution(& $handle)
			{
				if(! $handle)
					return false ;
				pclose($handle) ;
				return true ;
			}
			function Run($cmd, $synchronous=true)
			{
				$handle = false ;
				$ctn = false ;
				$realCmd = ($synchronous) ? $cmd : $this->RunAsyncCommandString($cmd) ;
				$handle = $this->OpenProcessExecution($realCmd) ;
				if($handle)
				{
					$ctn = "" ;
					if($synchronous)
					{
						while(($entry = $this->ReadProcessExecution($handle)) !== false)
						{
							$ctn .= $entry ;
						}
					}
					$this->CloseProcessExecution($handle) ;
				}
				return $ctn ;
			}
			function Query($cmd)
			{
				return $this->Run($cmd, true) ;
			}
			function __construct()
			{
			}
			function OsProcessManager()
			{
				$this->__construct() ;
			}
			function ExecuteCommand($Cmd)
			{
				$resp = $this->Query($Cmd) ;
				return $resp ;
			}
			function BeginCapture($Cmd)
			{
				$this->clearLastCommand() ;
				$this->clearLastResponse() ;
			}
			function EndCapture($cmd, $res)
			{
				$this->setLastCommand($cmd) ;
				$this->setLastResponse($res) ;
			}
			function CaptureCommand($cmd)
			{
				$this->BeginCapture($cmd) ;
				$res = $this->ExecuteCommand($cmd) ;
				$this->EndCapture($cmd, $res) ;
				return $res ;
			}
			function ExtractProcessEntries($list='', $exceptCmd='')
			{
				$process_entries = array() ;
				$process_list_data = explode("\n", $list) ;
				foreach($process_list_data as $i => $process_data)
				{
					$process_entry = $this->ExtractProcessEntry($process_data) ;
					if($process_entry)
					{
						if($exceptCmd != "" && strpos($process_entry->CMD, $exceptCmd) === true)
						{
							continue ;
						}
						$process_entries[] = $process_entry ;
					}
				}
				return $process_entries ;
			}
			function ExtractProcessEntry($process_data)
			{
				$process_entry = null ;
				return $process_entry ;
			}
			function FetchAll()
			{
				return $this->LocateByName("") ;
			}
			function LocateByName($name='')
			{
				$Cmd = $this->LocateByNameCommand($name) ;
				$Res = $this->CaptureCommand($Cmd) ;
				$processList = $this->ExtractProcessEntries($Res, $Cmd) ;
				$results = array() ;
				foreach($processList as $i => $processEntry)
				{
					if(strpos($processEntry->CMD, $name) === false)
						continue ;
					$results[] = $processEntry ;
				}
				return $results ;
			}
			function LocateByNameCommand($name='')
			{
				return "" ;
			}
			function KillProcessList($pid_list=array())
			{
			}
			function KillProcessCommand($pids, $force=0)
			{
				if($pids == "")
				{
					return "" ;
				}
				return "" ;
			}
			function KillProcessEntries($ProcessEntries=array())
			{
				$this->CaptureCommand($this->KillProcessCommand($ProcessEntries)) ;
			}
			function ExtractProcessListFromEntries($processEntries)
			{
				$pid_list = array() ;
				// print_r($processEntries) ;
				if(is_array($processEntries))
				{
					foreach($processEntries as $i => $entry)
					{
						$pid_list[] = $entry->PID ;
					}
				}
				return $pid_list ;
			}
			function Start($cmd)
			{
				return $this->RunAsync($cmd) ;
			}
			function Restart($cmd)
			{
				$processList = $this->FetchAll() ;
				$processEntries = $this->LocateInto($processList, $cmd) ;
				$ok = true ;
				if(count($processEntries))
				{
					$ok = $this->KillProcessEntries($processEntries) ;
				}
				return $this->Start($cmd) ;
			}
			function LocateInto(& $processList, $cmd)
			{
				$processEntries = array() ;
				for($i=0; $i<count($processList); $i++)
				{
					if(strpos($processList[$i]->CMD, $cmd) !== false)
					{
						$processEntries[] = $processList[$i] ;
					}
				}
				return $processEntries ;
			}
		}
		
		class OsProcessEntry
		{
			var $UID ;
			var $PID ;
			var $PPID ;
			var $C ;
			var $STIME ;
			var $TTY ;
			var $TIME ;
			var $CMD ;
			function ImportFromNotSet()
			{
				$this->UID = false ;
				$this->PID = false ;
				$this->PPID = false ;
				$this->C = false ;
				$this->STIME = false ;
				$this->TTY = false ;
				$this->TIME = false ;
				$this->CMD = false ;
			}
			public static function NotSet()
			{
				$notSetEntry = new OsProcessEntry() ;
				$notSetEntry->ImportFromNotSet() ;
				return $notSetEntry ;
			}
		}
		class LinuxProcessEntry extends OsProcessEntry
		{
			function ImportFromPsEfEntry($process_data)
			{
				if(strlen($process_data) > 48)
				{
					$this->UID = trim(substr($process_data, 0, 8)) ;
					$this->PID = trim(substr($process_data, 8, 6)) ;
					$this->PPID = trim(substr($process_data, 14, 8)) ;
					$this->C = trim(substr($process_data, 22, 2)) ;
					$this->STIME = trim(substr($process_data, 24, 5)) ;
					$this->TTY = trim(substr($process_data, 30, 9)) ;
					$this->TIME = trim(substr($process_data, 39, 9)) ;
					$this->CMD = trim(substr($process_data, 48)) ;
				}
				else
				{
					$this->ImportFromNotSet() ;
				}
			}
		}
		
		class LinuxProcessManager extends OsProcessManager
		{
			function RunAsyncCommandString($cmd)
			{
				return $cmd.' >/dev/null 2>&1 &' ;
			}
			function ExtractProcessEntries($list='', $exceptCmd='')
			{
				$process_entries = array() ;
				$process_list_data = explode("\n", $list) ;
				foreach($process_list_data as $i => $process_data)
				{
					$process_entry = $this->ExtractProcessEntry($process_data) ;
					if($process_entry)
					{
						if($exceptCmd != "" && strpos($process_entry->CMD, $exceptCmd) === true)
						{
							continue ;
						}
						$process_entries[] = $process_entry ;
					}
				}
				return $process_entries ;
			}
			function ExtractProcessEntry($process_data)
			{
				$process_entry = null ;
				$process_data = trim($process_data) ;
				if($process_data != "")
				{
					if(strlen($process_data) > 48)
					{
						$process_entry = new LinuxProcessEntry() ;
						$process_entry->ImportFromPsEfEntry($process_data) ;
					}
				}
				return $process_entry ;
			}
			function LocateByName($name='')
			{
				$Cmd = $this->LocateByNameCommand($name) ;
				$Res = $this->CaptureCommand($Cmd) ;
				$processList = $this->ExtractProcessEntries($Res, $Cmd) ;
				$results = array() ;
				foreach($processList as $i => $processEntry)
				{
					if($name != "")
					{
						if(strpos($processEntry->CMD, $name) === false)
							continue ;
					}
					$results[] = $processEntry ;
				}
				return $results ;
			}
			function LocateByNameCommand($name='')
			{
				return 'ps -ef' ;
			}
			function KillProcessCommand($pids, $force=0)
			{
				if(is_array($pids))
				{
					$pids = join(" ", $pids) ;
				}
				if($pids == "")
				{
					return "" ;
				}
				return 'kill'.(($force) ? ' -9' : '').' '.$pids ;
			}
			function KillProcessList($pid_list=array())
			{
				if(! is_array($pid_list))
				{
					return "" ;
				}
				return join(" ", $pid_list) ;
			}
			function KillProcessEntries($ProcessEntries=array())
			{
				$this->CaptureCommand($this->KillProcessEntriesCommand($ProcessEntries)) ;
			}
			function KillProcessIDs($pids=array())
			{
				$this->CaptureCommand($this->KillProcessCommand($pids)) ;
			}
			function KillProcessEntriesCommand($ProcessEntries)
			{
				return $this->KillProcessCommand($this->ExtractProcessListFromEntries($ProcessEntries)) ;
			}
		}
		class WinProcessManager extends OsProcessManager
		{
			function RunAsyncCommandString($cmd)
			{
				return 'start /b '.$cmd ;
			}
			function ExtractProcessEntries($list='', $exceptCmd='')
			{
				$process_entries = array() ;
				$process_list_data = explode("\n", $list) ;
				foreach($process_list_data as $i => $process_data)
				{
					$process_entry = $this->ExtractProcessEntry($process_data) ;
					if($process_entry)
					{
						if($exceptCmd != "" && strpos($process_entry->CMD, $exceptCmd) === true)
						{
							continue ;
						}
						$process_entries[] = $process_entry ;
					}
				}
				return $process_entries ;
			}
			function ExtractProcessEntry($process_data)
			{
				$process_entry = null ;
				$process_data = trim($process_data) ;
				if($process_data != "")
				{
					if(strlen($process_data) > 48)
					{
						$process_entry = new WindowsProcessEntry() ;
						$process_entry->ImportFromPsEfEntry($process_data) ;
					}
				}
				return $process_entry ;
			}
			function LocateByName($name='')
			{
				$Cmd = $this->LocateByNameCommand($name) ;
				$Res = $this->CaptureCommand($Cmd) ;
				$processList = $this->ExtractProcessEntries($Res, $Cmd) ;
				$results = array() ;
				foreach($processList as $i => $processEntry)
				{
					if($name != "")
					{
						if(strpos($processEntry->CMD, $name) === false)
							continue ;
					}
					$results[] = $processEntry ;
				}
				return $results ;
			}
			function LocateByNameCommand($name='')
			{
				return 'tasklist' ;
			}
			function KillProcessCommand($pids, $force=0)
			{
				if($pids == "")
				{
					return "" ;
				}
				return 'taskkill '.(($force) ? '/F ' : '').$pids ;
			}
			function KillProcessIDs($pid_list=array())
			{
				if(! is_array($pid_list))
				{
					return false ;
				}
				$pids = join(" ", $pid_list) ;
				return $this->CaptureCommand($this->KillProcessCommand($pids)) ;
			}
			function KillProcessEntries($ProcessEntries=array())
			{
				$this->CaptureCommand($this->KillProcessEntriesCommand($ProcessEntries)) ;
			}
			function KillProcessEntriesCommand($ProcessEntries)
			{
				return $this->KillProcessCommand($this->ExtractProcessListFromEntries($ProcessEntries)) ;
			}
		}
		
		if(PROCESS_MANAGER_GENERATE_CURRENT)
		{
			$GLOBALS["ProcessManager"] = new LinuxProcessManager() ;
		}
	}
	
?>