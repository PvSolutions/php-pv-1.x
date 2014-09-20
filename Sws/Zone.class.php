<?php
	
	if(! defined('ZONE_SWS'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/IHM/Simple.class.php" ;
		}
		define('ZONE_SWS', 1) ;
		
		class ZoneAdminSws extends PvZoneWebSimple
		{
			public $BarreLiensMembre ;
			public $InclureScriptsMembership = 1 ;
			public $CheminLogo = "images/logo.png" ;
			public $MsgCopyright = "SWS (C) Tous droits r&eacute;serv&eacute;s" ;
			public $InclureJQuery = 1 ;
			public $InclureJQueryUi = 1 ;
			protected function ObtientDefCSS()
			{
				$ctn = '' ;
				$ctn .= 'body, p, div, form, table, tr, td, th {
	font-family:tahoma ;
	font-size:12px ;
}
.titre {
	font-size:16px;
	margin-top:12px;
	margin-bottom:12px;
	font-weight:bold;
}
.menu-haut {
	background:black ;
	padding:4px;
}
.menu-haut * {
	color:white ;
}' ;
				return $ctn ;
			}
			protected function ChargeBarreLiensMembre()
			{
				$this->BarreLiensMembre = new PvBarreLiensEditMembre() ;
				$this->BarreLiensMembre->AdopteZone('barreLiensMembre', $this) ;
				$this->BarreLiensMembre->ChargeConfig() ;
				$this->BarreLiensMembre->InclureLienAccueil = 1 ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InscritContenuCSS($this->ObtientDefCSS()) ;
				$this->ChargeBarreLiensMembre() ;
				ReferentielSws::$SystemeEnCours->RemplitZoneAdmin($this) ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '<body>'.PHP_EOL ;
				$ctn .= '<table align="center" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td class="menu-haut">'.$this->BarreLiensMembre->RenduDispositif().'</td>
</tr>
<tr>
<td><img src="../'.$this->CheminLogo.'" /></td>
</tr>
<tr>
<td>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '</td>
</tr>
<tr>
<td class="copyrights" align="center">'.$this->MsgCopyright.'</td>
</tr>
</table>' ;
				$ctn .= '</body>' ;
				return $ctn ;
			}
		}
		class ZoneMembreSws extends PvZoneWebSimple
		{
			public $InclureScriptsMembership = 1 ;
			public $InclureJQuery = 1 ;
			public $InclureJQueryUi = 1 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				ReferentielSws::$SystemeEnCours->RemplitZoneMembre($this) ;
			}
		}
		
	}
	
?>