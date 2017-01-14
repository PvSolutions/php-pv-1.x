<?php
	
	if(! defined('SCRIPT_BASE_SB_ADMIN2'))
	{
		define('SCRIPT_BASE_SB_ADMIN2', 1) ;
		
		class PvScriptBaseSbAdmin2 extends PvScriptWebSimple
		{
			public $NecessiteMembreConnecte = 1 ;
			public $NomDocumentWeb = "cadre" ;
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				$this->RedirigeNonConnecte() ;
				return $ok ;
			}
			public function DetermineEnvironnement()
			{
				$this->DetermineEnvironnementSpec() ;
			}
			protected function RedirigeNonConnecte()
			{
				if($this->NecessiteMembreConnecte == 1 && ! $this->ZoneParent->PossedeMembreConnecte())
				{
					// echo "mmm" ;
					redirect_to($this->ZoneParent->ScriptConnexion->ObtientUrl()) ;
				}
			}
			protected function DetermineEnvironnementSpec()
			{
			}
		}
		
		class PvScriptCadreBaseSbAdmin2 extends PvScriptBaseSbAdmin2
		{
			public $NomDocumentWeb = "cadre" ;
		}
		class PvScriptAccueilSbAdmin2 extends PvScriptBaseSbAdmin2
		{
			public $Titre = "Tableau de bord" ;
			public $TitreDocument = "Tableau de bord" ;
			public $NecessiteMembreConnecte = 1 ;
			public $NomDocumentWeb = "connecte" ;
			public $InclureBarreNbresSurlign = 1 ;
			public $BlocNbreSurlign1 = null ;
			public $BlocNbreSurlign2 = null ;
			public $BlocNbreSurlign3 = null ;
			public $BlocNbreSurlign4 = null ;
			public $BlocChart1 = null ;
			public $InclureBarreCharts = 1 ;
			protected function DetermineEnvironnementSpec()
			{
				parent::DetermineEnvironnementSpec() ;
				$this->DetermineBarreNbresSurlign() ;
				$this->DetermineBlocChart1() ;
			}
			protected function DetermineBlocChart1()
			{
				$this->BlocChart1 = new PvChartMorrisSbAdmin2() ;
				$this->BlocChart1->AdopteScript("chart1", $this) ;
				$this->BlocChart1->ChargeConfig() ;
			}
			protected function DetermineBarreNbresSurlign()
			{
				if($this->InclureBarreNbresSurlign == 0)
				{
					return ;
				}
				$this->BlocNbreSurlign1 = new PvBlocNbreSurlignSbAdmin2() ;
				$this->BlocNbreSurlign1->AdopteScript("blocNbreSurlign1", $this) ;
				$this->BlocNbreSurlign1->ChargeConfig() ;
				$this->BlocNbreSurlign1->ClasseCSSBloc = "panel-primary" ;
				$this->BlocNbreSurlign1->ClasseCSSNbre = "fa-comments" ;
				$this->BlocNbreSurlign2 = new PvBlocNbreSurlignSbAdmin2() ;
				$this->BlocNbreSurlign2->AdopteScript("blocNbreSurlign2", $this) ;
				$this->BlocNbreSurlign2->ChargeConfig() ;
				$this->BlocNbreSurlign2->ClasseCSSBloc = "panel-green" ;
				$this->BlocNbreSurlign2->ClasseCSSNbre = "fa-tasks" ;
				$this->BlocNbreSurlign3 = new PvBlocNbreSurlignSbAdmin2() ;
				$this->BlocNbreSurlign3->AdopteScript("blocNbreSurlign3", $this) ;
				$this->BlocNbreSurlign3->ChargeConfig() ;
				$this->BlocNbreSurlign3->ClasseCSSBloc = "panel-yellow" ;
				$this->BlocNbreSurlign3->ClasseCSSNbre = "fa-shopping-cart" ;
				$this->BlocNbreSurlign4 = new PvBlocNbreSurlignSbAdmin2() ;
				$this->BlocNbreSurlign4->AdopteScript("blocNbreSurlign4", $this) ;
				$this->BlocNbreSurlign4->ChargeConfig() ;
				$this->BlocNbreSurlign4->ClasseCSSBloc = "panel-red" ;
				$this->BlocNbreSurlign4->ClasseCSSNbre = "fa-support" ;
			}
			protected function RenduBarreCharts()
			{
				$ctn = '' ;
				if($this->InclureBarreCharts == 0)
				{
					return $ctn ;
				}
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="col-lg-8">'.PHP_EOL ;
				$ctn .= $this->BlocChart1->RenduDispositif() ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function RenduBarreNbresSurlign()
			{
				$ctn = '' ;
				if($this->InclureBarreNbresSurlign == 0)
				{
					return $ctn ;
				}
				$ctn .= '<div class="row">
<div class="col-lg-3 col-md-6">'.$this->BlocNbreSurlign1->RenduDispositif().'</div>
<div class="col-lg-3 col-md-6">'.$this->BlocNbreSurlign2->RenduDispositif().'</div>
<div class="col-lg-3 col-md-6">'.$this->BlocNbreSurlign3->RenduDispositif().'</div>
<div class="col-lg-3 col-md-6">'.$this->BlocNbreSurlign4->RenduDispositif().'</div>
</div>' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduBarreNbresSurlign() ;
				$ctn .= $this->RenduBarreCharts() ;
				return $ctn ;
			}
		}
		
	}
	
?>