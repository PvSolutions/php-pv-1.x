<?php
	
	if(! defined('SYSTEM_MORECUST'))
	{
		define('SYSTEM_MORECUST', 1) ;
		
		class DefaultSystMoreCust extends SystBaseMoreCust
		{
			public $PrdNightly ;
			public $PrdWeekly ;
			public $PrdFirstMonth ;
			public $PrdLastMonth ;
			public $PrdFirstDayOfMonth ;
			public $PrdLastDayOfMonth ;
			public $PrdChrismas ;
			public $PrdStScotts ;
			public $PrdNewYear ;
			public $NamePrdNightly = "nightly" ;
			public $NamePrdWeekly = "weekly" ;
			public $NamePrdFirstMonth = "firstMonth" ;
			public $NamePrdLastMonth = "lastMonth" ;
			public $NamePrdFirstDayOfMonth = "firstDayOfMonth" ;
			public $NamePrdLastDayOfMonth = "lastDayOfMonth" ;
			public $NamePrdChrismas = "chrismas" ;
			public $NamePrdStScotts = "stScotts" ;
			public $NamePrdNewYear = "newYear" ;
			protected function LoadPrds()
			{
				parent::LoadPrds() ;
				$this->PrdNightly = $this->InsertPrd($this->NamePrdNightly, new PrdNightlyMoreCust()) ;
				$this->PrdWeekly = $this->InsertPrd($this->NamePrdWeekly, new PrdWeeklyMoreCust()) ;
				$this->PrdFirstMonth = $this->InsertPrd($this->NamePrdFirstMonth, new PrdFirstMonthMoreCust()) ;
				$this->PrdLastMonth = $this->InsertPrd($this->NamePrdLastMonth, new PrdLastMonthMoreCust()) ;
				$this->PrdFirstDayOfMonth = $this->InsertPrd($this->NamePrdFirstDayOfMonth, new PrdFirstDayOfMonthMoreCust()) ;
				$this->PrdLastDayOfMonth = $this->InsertPrd($this->NamePrdLastDayOfMonth, new PrdLastDayOfMonthMoreCust()) ;
				$this->PrdChrismas = $this->InsertPrd($this->NamePrdChrismas, new PrdChrismasMoreCust()) ;
				$this->PrdStScotts = $this->InsertPrd($this->NamePrdStScotts, new PrdStScottsMoreCust()) ;
				$this->PrdNewYear = $this->InsertPrd($this->NamePrdNewYear, new PrdNewYearMoreCust()) ;
			}
		}
		
		class SqlSystMoreCust extends DefaultSystMoreCust
		{
		}
		
	}
	
?>