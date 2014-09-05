<?php
	
	if(! defined('CORE_MORECUST'))
	{
		define('CORE_MORECUST', 1) ;
		
		class ObjectBaseMoreCust
		{
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
			}
			protected function LoadConfig()
			{
			}
		}
		
		class SystBaseMoreCust extends ObjectBaseMoreCust
		{
			public $Acts = array() ;
			public $Prds = array() ;
			public $Envs = array() ;
			public $Packs = array() ;
			protected $SelectedPackNames = array() ;
			public $ActDoNothing ;
			public $PrdAlways ;
			public $EnvAlways ;
			public function & GetAct($name)
			{
				$act = new ActUndefinedMoreCust() ;
				if(isset($this->Acts[$name]))
				{
					$act = & $this->Acts[$name] ;
				}
				return $act ;
			}
			public function & GetPrd($name)
			{
				$prd = new PrdUndefinedMoreCust() ;
				if(isset($this->Prds[$name]))
				{
					$prd = & $this->Prds[$name] ;
				}
				return $prd ;
			}
			public function & GetEnv($name)
			{
				$env = new EnvUndefinedMoreCust() ;
				if(isset($this->Envs[$name]))
				{
					$env = & $this->Envs[$name] ;
				}
				return $env ;
			}
			public function & InsertAct($name, & $act)
			{
				$this->RegisterAct($name, $act) ;
				return $act ;
			}
			public function & InsertEnv($name, & $env)
			{
				$this->RegisterEnv($name, $env) ;
				return $env ;
			}
			public function & InsertPrd($name, & $prd)
			{
				$this->RegisterPrd($name, $prd) ;
				return $prd ;
			}
			public function RegisterAct($name, & $act)
			{
				$this->Acts[$name] = & $act ;
				$act->SetSystParent($name, $this) ;
			}
			public function RegisterEnv($name, & $env)
			{
				$this->Envs[$name] = & $env ;
				$act->SetSystParent($name, $this) ;
			}
			public function RegisterPrd($name, & $prd)
			{
				$this->Prds[$name] = & $prd ;
				$prd->SetSystParent($name, $this) ;
			}
			public function IsAvailable()
			{
				return $this->_IsAvailable() ;
			}
			protected function _IsAvailable()
			{
				return 1 ;
			}
			public function LoadConfig()
			{
				$this->LoadPrds() ;
				$this->LoadActs() ;
				$this->LoadEnvs() ;
				$this->LoadContext() ;
			}
			protected function LoadPrds()
			{
			}
			protected function LoadActs()
			{
			}
			protected function LoadEnvs()
			{
			}
			protected function LoadContext()
			{
			}
			public function Execute()
			{
				if(! $this->PrepareExecution())
					return ;
				return $this->_Execute() ;
			}
			protected function PrepareExecution()
			{
				$this->SelectedPackNames = array() ;
				$this->DetectSelectedPacks() ;
				return count($this->SelectedPackNames) > 0 ;
			}
			protected function DetectSelectedPacks()
			{
				foreach($this->Packs as $name => & $pack)
				{
					if($pack->IsAvailable())
					{
						$this->SelectedPackNames[] = $name ;
					}
				}
			}
			public function & GetSelectedPacks()
			{
				$packs = array() ;
				foreach($this->SelectedPackNames as $i => $name)
				{
					$packs[$name] = & $this->Packs[$name] ;
				}
				return $packs ;
			}
		}
		
		class SystItemMoreCust extends ObjectBaseMoreCust
		{
			protected $Undefined = 0 ;
			public $Enabled = 1 ;
			protected $SystParent ;
			protected $Param ;
			protected function StoreParam($param)
			{
				$this->Param = $param ;
			}
			protected function ClearParam()
			{
				$this->Param = null ;
			}
			public function IsAvailable($param = null)
			{
				if($this->Undefined == 1 || $this->Enabled == 0)
					return 0 ;
				$this->StoreParam($param) ;
				$ok = $this->_IsAvailable() ;
				$this->ClearParam() ;
				return $ok ;
			}
			protected function _IsAvailable()
			{
				return 0 ;
			}
			public function SetSystParent($name, & $systParent)
			{
				$this->NameSyst = $name ;
				$this->SystParent = & $systParent ;
			}
			protected function & SystParent()
			{
				return $this->SystParent ;
			}
		}
		
		class PackMoreCust extends SystItemMoreCust
		{
			public $ActName ;
			public $ActParam ;
			public $PrdName ;
			public $PrdParam ;
			public $EnvName ;
			public $EnvParam ;
			public function & GetAct()
			{
				$act = $this->SystParent->GetAct($this->ActName) ;
				return $act ;
			}
			public function & GetPrd()
			{
				$prd = $this->SystParent->GetPrd($this->PrdName) ;
				return $prd ;
			}
			public function & GetEnv()
			{
				$env = $this->SystParent->GetEnv($this->EnvName) ;
				return $env ;
			}
			public function _IsAvailable()
			{
				return $this->GetPrd()->IsAvailable() && $this->GetEnv()->IsAvailable() && $this->GetAct()->IsAvailable() ;
			}
		}
		class PackUndefinedMoreCust extends PackMoreCust
		{
			protected $Undefined = 1 ;
		}
		
		class ActBaseMoreCust extends SystItemMoreCust
		{
			public function Execute($param=null)
			{
				$this->StoreParam($param) ;
				$result = $this->_Execute() ;
				$this->ClearParam() ;
				return $result ;
			}
			protected function _Execute()
			{
			}
		}
		class PrdBaseMoreCust extends SystItemMoreCust
		{
		}
		class EnvBaseMoreCust extends SystItemMoreCust
		{
		}
		
		class ActUndefinedMoreCust extends ActBaseMoreCust
		{
			protected $Undefined = 1 ;
		}
		class PrdUndefinedMoreCust extends ActBaseMoreCust
		{
			protected $Undefined = 1 ;
		}
		class EnvUndefinedMoreCust extends ActBaseMoreCust
		{
			protected $Undefined = 1 ;
		}

		class ActDoNothingMoreCust extends ActBaseMoreCust
		{
		}
		class PrdAlwaysMoreCust extends ActBaseMoreCust
		{
			protected function _IsAvailable()
			{
				return 1 ;
			}
		}
		class EnvAlwaysMoreCust extends ActBaseMoreCust
		{
			protected function _IsAvailable()
			{
				return 1 ;
			}
		}
	}
	
?>