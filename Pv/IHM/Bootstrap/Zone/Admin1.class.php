<?php
	
	if(! defined('PV_ZONE_ADMIN1_BOOTSTRAP'))
	{
		if(! defined('PV_NOYAU_ZONE_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		
		class PvZoneAdmin1Bootstrap extends PvZoneBootstrap
		{
			public $Titre = "Boostrap Admin 1" ;
			public $MenuDeroulMembre ;
			public $BarreMenuPrinc ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeMenuDeroulMembre() ;
				$this->ChargeBarreMenuPrinc() ;
			}
			protected function CreeMenuDeroulMembre()
			{
				return new PvMenuDeroulMembreBootstrap() ;
			}
			protected function ChargeMenuDeroulMembre()
			{
				$this->MenuDeroulMembre = $this->CreeMenuDeroulMembre() ;
				$this->MenuDeroulMembre->AdopteZone('menuDeroulMembre', $this) ;
				$this->MenuDeroulMembre->ChargeConfig() ;
			}
			protected function CreeBarreMenuPrinc()
			{
				return new PvGrdBarreMenu1Bootstrap() ;
			}
			protected function ChargeBarreMenuPrinc()
			{
				$this->BarreMenuPrinc = $this->CreeBarreMenuPrinc() ;
				$this->BarreMenuPrinc->AdopteZone('menuDeroulMembre', $this) ;
				$this->BarreMenuPrinc->ChargeConfig() ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '<div id="Entete_Doc_'.$this->IDInstanceCalc.'" class="navbar navbar-inverse navbar-static-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="'.$this->ObtientUrl().'">'.$this->Titre.'</a>
    </div>
	'.$this->MenuDeroulMembre->RenduDispositif().'
	</div>
</div>' ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="col-sm-3">'.PHP_EOL ;
				$ctn .= $this->BarreMenuPrinc->RenduDispositif().PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="col-sm-9">' ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				if($this->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduCtnJs() ;
				}
				$ctn .= '</body>' ;
				return $ctn ;
			}
		}
	}
	
?>