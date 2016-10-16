<?php
	
	if(! defined('ZONE_SWS'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/IHM/Simple.class.php" ;
		}
		define('ZONE_SWS', 1) ;
		
		class ZoneBaseSws extends PvZoneWebSimple
		{
			public function NiveauAdmin()
			{
				return "public" ;
			}
		}
		class ZoneAdminSws extends ZoneBaseSws
		{
			public $BarreLiensMembre ;
			public $InclureScriptsMembership = 1 ;
			public $CheminLogo = "../images/logo.png" ;
			public $TitreLogo = "SWS" ;
			public $MsgCopyright = "SWS (C) Tous droits r&eacute;serv&eacute;s" ;
			public $InclureJQuery = 1 ;
			public $InclureJQueryUi = 1 ;
			public $IncRenduBarreLiensMembre = 1 ;
			public $IncRenduLogo = 0 ;
			public function NiveauAdmin()
			{
				return "admin" ;
			}
			protected function ObtientDefCSS()
			{
				$ctn = '' ;
				$ctn .= 'body, p, div, form, table, tr, td, th {
	font-family:tahoma ;
	font-size:12px ;
}
.logo {
	font-size:48px ;
	font-family:tahoma ;
	font-weight:normal ;
	padding:0px ;
	margin-top:8px ;
	margin-bottom:8px ;
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
}
.sws-ui-padding-2 {
	padding:2px ;
}
.sws-ui-padding-4 {
	padding:4px ;
}
.sws-ui-padding-8 {
	padding:8px ;
}
.sws-ui-padding-12 {
	padding:12px ;
}
.sws-ui-margin-2 {
	margin:2px ;
}
.sws-ui-margin-4 {
	margin:4px ;
}
.sws-ui-margin-8 {
	margin:8px ;
}
.sws-ui-margin-12 {
	margin:12px ;
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
				$ctn .= '<table align="center" width="100%" cellspacing="0" cellpadding="2">' ;
				if($this->IncRenduBarreLiensMembre)
				{
					$ctn .= '<tr>
<td class="menu-haut">'.$this->BarreLiensMembre->RenduDispositif().'</td>
</tr>' ;
				}
				$ctn .= '<tr><td>' ;
				if($this->IncRenduLogo)
				{
					$ctn .= '<img src="'.$this->CheminLogo.'" />' ;
				}
				else
				{
					$ctn .= '<h1 class="logo">'.$this->TitreLogo.'</h1>' ;
				}
				$ctn .= '</td></tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '</td>
</tr>
<tr>
<td class="copyrights" align="center"><p class="ui-widget ui-widget-content">'.$this->MsgCopyright.'</p></td>
</tr>
</table>' ;
				$ctn .= '</body>' ;
				return $ctn ;
			}
		}
		class ZoneMembreSws extends ZoneBaseSws
		{
			public $InclureScriptsMembership = 1 ;
			public $AutoriserInscription = 1 ;
			public $AutoriserModifPrefs = 1 ;
			public $InclureJQuery = 1 ;
			public $InclureJQueryUi = 1 ;
			public function NiveauAdmin()
			{
				return "membre" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				ReferentielSws::$SystemeEnCours->RemplitZoneMembre($this) ;
			}
		}
		
	}
	
?>