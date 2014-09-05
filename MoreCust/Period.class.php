<?php
	
	if(! defined('PERIOD_MORECUST'))
	{
		if(! defined('CORE_MORECUST'))
		{
			include dirname(__FILE__)."/Core.class.php" ;
		}
		define('PERIOD_MORECUST', 1) ;
		
		class PrdIntervalUnitMoreCust extends PrdBaseMoreCust
		{
			public $Min = -1 ;
			public $Max = -1 ;
			protected function GetUnitValue()
			{
				return null ;
			}
			protected function _IsAvailable()
			{
				if($this->Min == -1 || $this->Max == -1)
				{
					return 0 ;
				}
				$currentHour = intval($this->GetUnitValue()) ;
				$ok = 0 ;
				if($this->Min < $this->Max)
				{
					if($currentHour <= $this->Min || $currentHour >= $this->Max)
					{
						$ok = 1 ;
					}
				}
				else
				{
					if($currentHour >= $this->Min && $currentHour <= $this->Max)
					{
						$ok = 1 ;
					}
				}
			}
		}
		class PrdIntervalMinuteMoreCust extends PrdIntervalUnitMoreCust
		{
			protected function GetUnitValue()
			{
				return intval(date("i")) ;
			}
		}
		class PrdIntervalHourMoreCust extends PrdIntervalUnitMoreCust
		{
			protected function GetUnitValue()
			{
				return date("G") ;
			}
		}
		class PrdNightlyMoreCust extends PrdIntervalHourMoreCust
		{
			public $Min = 22 ;
			public $Max = 6 ;
		}
		class PrdIntervalDayMoreCust extends PrdIntervalUnitMoreCust
		{
			protected function GetUnitValue()
			{
				return date("w") ;
			}
		}
		class PrdWeeklyMoreCust extends PrdIntervalDayMoreCust
		{
			public $Min = 6 ;
			public $Max = 0 ;
		}		
		class PrdIntervalMonthMoreCust extends PrdIntervalUnitMoreCust
		{
			protected function GetUnitValue()
			{
				return date("n") ;
			}
		}
		class PrdLastMonthMoreCust extends PrdIntervalMonthMoreCust
		{
			public $Min = 12 ;
			public $Max = 12 ;
		}
		class PrdFirstMonthMoreCust extends PrdIntervalMonthMoreCust
		{
			public $Min = 1 ;
			public $Max = 1 ;
		}
		class PrdFirstDayOfMonthMoreCust extends PrdIntervalMonthMoreCust
		{
			protected function _IsAvailable()
			{
				return date("j") == 1 ;
			}
		}
		class PrdLastDayOfMonthMoreCust extends PrdIntervalMonthMoreCust
		{
			protected function _IsAvailable()
			{
				return date("j") == date("t") ;
			}
		}
		class PrdChrismasMoreCust extends PrdIntervalMonthMoreCust
		{
			protected function _IsAvailable()
			{
				return date("j") == 25 && date("n") == 12 ;
			}
		}
		class PrdStScottsMoreCust extends PrdIntervalMonthMoreCust
		{
			protected function _IsAvailable()
			{
				return date("j") == date("t") && date("n") == 12 ;
			}
		}
		class PrdNewYearMoreCust extends PrdIntervalMonthMoreCust
		{
			protected function _IsAvailable()
			{
				return date("j") == 1 && date("n") == 1 ;
			}
		}
	}
	
?>