<?php
	
	if(! defined('PV_ELEMENT_FORMULAIRE_BOOTSTRAP'))
	{
		define('PV_ELEMENT_FORMULAIRE_BOOTSTRAP', 1) ;
		
		class PvZoneEntreeBootstrap extends PvZoneEntreeHtml
		{
			public $TypeElementFormulaire = "text" ;
			public $Explicatif = "" ;
			public function ObtientExplicatif()
			{
				if($this->Explicatif == "")
				{
					return $this->Titre ;
				}
				return $this->Explicatif ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$styleCSS = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="'.$this->TypeElementFormulaire.'"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				if($this->Explicatif != '')
				{
					$ctn .= ' placeholder="'.htmlentities($this->ObtientExplicatif()).'"' ;
				}
				$ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />' ;
				return $ctn ;
			}
		}
		class PvZonePasswordBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "password" ;
		}
		class PvZoneEmailBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "email" ;
		}
		class PvZoneDateTimeBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "datetime" ;
		}
		class PvZoneDateTimeLocalBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "datetime-local" ;
		}
		class PvZoneDateBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "date" ;
		}
		class PvZoneMonthBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "month" ;
		}
		class PvZoneWeekBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "week" ;
		}
		class PvZoneNumberBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "number" ;
		}
		class PvZoneUrlBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "url" ;
		}
		class PvZoneSearchBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "search" ;
		}
		class PvZoneTelBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "tel" ;
		}
		class PvZoneColorBootstrap extends PvZoneEntreeBootstrap
		{
			public $TypeElementFormulaire = "color" ;
		}
	}
	
?>