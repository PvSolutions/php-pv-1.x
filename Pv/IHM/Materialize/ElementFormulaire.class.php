<?php
	
	if(! defined('PV_ELEMENT_FORMULAIRE_MATERIALIZE'))
	{
		define('PV_ELEMENT_FORMULAIRE_MATERIALIZE', 1) ;
		
		class PvZoneTexteMaterialize extends PvZoneTexteHtml
		{
		}
		
		class PvZoneSelectMaterialize extends PvZoneSelectHtml
		{
		}
		
		class PvZoneMultiligneMaterialize extends PvZoneMultiligneHtml
		{
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ClassesCSS[] = "materialize-textarea" ;
			}
		}
		
		class PvCfgZoneDateMaterialize
		{
			public $autoClose = false ;
			public $format = "yyyy-mm-dd" ;
			public $defaultDate ;
			public $setDefaultDate = false ;
			public $disableWeekends = false ;
			public $firstDay = 1 ;
			public $minDate ;
			public $maxDate ;
			public $yearRange = 10 ;
			public $isRTL = false ;
			public $showMonthAfterYear = false ;
			public $showDaysInNextAndPreviousMonths = false ;
			public $showClearBtn = false ;
		}
		class PvDatepickerMaterialize extends PvZoneTexteHtml
		{
			public $Cfg ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Cfg = new PvCfgZoneDateMaterialize() ;
				$this->ClassesCSS[] = "datepicker" ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctnCfg = json_encode($this->Cfg) ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('document.addEventListener("DOMContentLoaded", function() {
var elems = [document.getElementById("'.$this->IDInstanceCalc.'")] ;
var instances = M.Datepicker.init(elems, '.$ctnCfg.');
});') ;
				return $ctn ;
			}
		}
	
		
		class PvZoneUploadMaterialize extends PvZoneUploadHtml
		{
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ClassesCSS[] = "validate" ;
			}
		}
		
		class PvInputUploadMaterialize extends PvZoneTexteHtml
		{
			public $LibelleTelecharger = "Telecharger" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div class="btn">
<span>Fichier</span>
<input type="file" />
</div>
<div class="file-path-wrapper">
<input class="file-path validate" type="text" value="'.htmlspecialchars($this->Valeur).'">
</div>
</div>' ;
				return $ctn ;
			}
		}
		
	}
	
?>