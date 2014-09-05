<?php
	
	if(! defined('CONTENEURS_CHARISMA'))
	{
		if(! defined('UI_FEATURES_CHARISMA'))
		{
			include dirname(__FILE__).'/UiFeatures.class.php' ;
		}
		define('CONTENEURS_CHARISMA', 1) ;
		
		class ConteneurBaseCharisma extends PvComposantIUBase
		{
			public $ChargeAutoCompsAvantRendu = 1 ;
			public $Composants = array() ;
			public function InscritComposant(& $comp)
			{
				$this->Composants[] = & $comp ;
			}
			public function InscritNouvComposant($comp)
			{
				$this->InscritComposant($comp) ;
			}
			public function InscritComp(& $comp)
			{
				$this->InscritComposant($comp) ;
			}
			public function InscritNouvComp($comp)
			{
				$this->InscritComposant($comp) ;
			}
			public function InscritComposants()
			{
				$comps = func_get_args() ;
				foreach($comps as $i => & $comp)
				{
					$this->Composants[] = & $comp ;
				}
			}
			public function InscritComposantAIndex()
			{
				$comps = func_get_args() ;
				$index = $comps[0] ;
				array_splice($comps, 0, 1) ;
				if(count($comps) > 0)
				{
					array_splice($this->Composants, $index, 0, $comps) ;
				}
			}
			protected function RenduComposantsPropres()
			{
				return $this->RenduComposants($this->Composants) ;
			}
			protected function RenduComposants(& $comps)
			{
				$ctn = '' ;
				foreach($comps as $i => $comp)
				{
					if($this->ChargeAutoCompsAvantRendu)
					{
						$comp->ChargeConfig() ;
					}
					$ctn .= $comp->RenduDispositif() ;
				}
				return $ctn ;
			}
		}
		
		class RowFluidCharisma extends ConteneurBaseCharisma
		{
			public $Sortable = 1 ;
			protected function DebutRendu()
			{
				$ctn = '' ;
				$nomClasseCSS = 'row-fluid'.(($this->Sortable) ? ' sortable' : '') ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="'.$nomClasseCSS.'">' ;
				return $ctn ;
			}
			protected function FinRendu()
			{
				$ctn = '' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function RenduComposantsInt()
			{
				return $this->RenduComposantsPropres() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->DebutRendu().PHP_EOL ;
				$ctn .= $this->RenduComposantsInt().PHP_EOL ;
				$ctn .= $this->FinRendu() ;
				return $ctn ;
			}
		}
		
		class BarreWellTopBlockCharisma extends RowFluidCharisma
		{
			public $Block1 ;
			public $Block2 ;
			public $Block3 ;
			public $Block4 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Block1 = new WellTopBlock1Charisma() ;
				$this->InscritComp($this->Block1) ;
				$this->Block2 = new WellTopBlock2Charisma() ;
				$this->InscritComp($this->Block2) ;
				$this->Block3 = new WellTopBlock3Charisma() ;
				$this->InscritComp($this->Block3) ;
				$this->Block4 = new WellTopBlock4Charisma() ;
				$this->InscritComp($this->Block4) ;
			}
		}
		
		class RowFluidBoxCharisma extends RowFluidCharisma
		{
			public $CSSIconeEntete = "icon-info-sign" ;
			public $InclureBoxIcons = 1 ;
			public $InclureBoxIconSettings = 1 ;
			public $InclureBoxIconMinimize = 1 ;
			public $InclureBoxIconClose = 1 ;
			public $InclureHeader = 1 ;
			public $Titre = "" ;
			public $Span = '12' ;
			protected function DebutRendu()
			{
				$ctn = parent::DebutRendu() ;
				$ctn .= '<div class="box span'.$this->Span.'">'.PHP_EOL ;
				if($this->InclureHeader)
				{
					$ctn .= '<div class="box-header well">'.PHP_EOL ;
					$ctn .= '<h2><i class="'.$this->CSSIconeEntete.'"></i> '.$this->Titre.'</h2>'.PHP_EOL ;
					if($this->InclureBoxIcons)
					{
						$ctn .= '<div class="box-icon">'.PHP_EOL ;
						if($this->InclureBoxIconSettings)
						{
							$ctn .= '<a href="javascript:;" class="btn btn-setting btn-round"><i class="icon-cog"></i></a>'.PHP_EOL ;
						}
						if($this->InclureBoxIconMinimize)
						{
							$ctn .= '<a href="javascript:;" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>'.PHP_EOL ;
						}
						if($this->InclureBoxIconSettings)
						{
							$ctn .= '<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>'.PHP_EOL ;
						}
						$ctn .= '</div>'.PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="box-content">' ;
				return $ctn ;
			}
			protected function FinRendu()
			{
				$ctn = parent::FinRendu().PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class TabBoxCharisma extends RowFluidBoxCharisma
		{
			public $TabPrinc ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeTabPrinc() ;
			}
			protected function CreeTabPrinc()
			{
				return new TabCharisma() ;
			}
			protected function ChargeTabPrinc()
			{
				$this->TabPrinc = $this->CreeTabPrinc() ;
				$this->TabPrinc->AdopteComposantIU('tabPrinc', $this) ;
				$this->InscritComp($this->TabPrinc) ;
				$this->TabPrinc->ChargeConfig() ;
			}
		}
	}
	
?>