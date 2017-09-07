<?php
	
	if(! defined('PV_CHART_MORRIS_JS'))
	{
		define('PV_CHART_MORRIS_JS', 1) ;
		
		class PvCfgChartMorrisJs
		{
			public $element ;
			public $data = array() ;
			public $xkey = "" ;
			public $ykeys = array() ;
			public $labels = array() ;
			public $pointSize = 1 ;
			public $hideHover = "auto" ;
			public $resize = true ;
		}
		
		class PvChartMorrisJs extends PvComposantJsFiltrable
		{
			public $NomColonneX ;
			public $NomColonnesY = array() ;
			public $LibellesY = array() ;
			public $CfgInit ;
			public $TypeChart = "Area" ;
			public $CheminFichierJs = "vendor/morrisjs/morris.min.js" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CfgInit = new PvCfgChartMorrisJs() ;
				$this->CfgInit->element = $this->IDInstanceCalc ;
			}
			public function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduInscritLienJs($this->CheminFichierJs) ;
				$ctn .= $this->RenduInscritContenuJs('jQuery(function() {
	Morris.'.$this->TypeChart.'('.svc_json_encode($this->CfgInit).') ;
}) ;') ;
				return $ctn ;
			}
			public function CalculeElementsRendu()
			{
				$this->CfgInit->data = array() ;
				if($this->NomColonneX == "")
				{
					return ;
				}
				$fourn = & $this->FournisseurDonnees ;
				$lgns = $fourn->SelectElements(array(), $this->ObtientFiltresSelection()) ;
				if(is_array($lgns))
				{
					foreach($lgns as $i => $lgn)
					{
						$donneesStat = array() ;
						$donneesStat[$this->NomColonneX] = (isset($lgn[$this->NomColonneX])) ? $lgn[$this->ColonneY] : 0 ;
						foreach($this->NomColonnesY as $j => $nomCol)
						{
							$donneesStat[$nomCol] = (isset($lgn[$nomCol])) ? $lgn[$nomCol] : 0 ;
						}
						$this->CfgInit->data[] = $donneesStat ;
					}
				}
				$this->CfgInit->xkey = $this->NomColonneX ;
				$this->CfgInit->ykeys = $this->NomColonnesY ;
				$this->CfgInit->labels = $this->LibellesY ;
			}
			protected function RenduDispositifBrutSpec()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'"></div>' ;
				return $ctn ;
			}
		}
		
	}
	
?>