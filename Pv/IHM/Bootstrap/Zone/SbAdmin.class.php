<?php
	
	if(! defined('ZONE_SB_ADMIN'))
	{
		define('ZONE_SB_ADMIN', 1) ;
		
		class PvZoneSbAdmin extends PvZoneBootstrap
		{
			public $NomLogo = "SB Admin v2.0" ;
			public $LangueDocument = "en" ;
			public $EncodageDocument = "utf-8" ;
			public $NavbarHeader ;
			public $NavbarTopLinks ;
			public $InclureCtnJsEntete = 0 ;
			public $InclureScriptsMembership = 1 ;
			public $SideMenu ;
			public $Footer ;
			public $CheminCSSSbAdmin = "css/sb-admin-2.css" ;
			public $CheminCSSMetisMenu = "css/plugins/metisMenu/metisMenu.min.css" ;
			public $CheminCSSTimeline = "css/plugins/timeline.css" ;
			public $CheminCSSFontAwesome = "font-awesome/css/font-awesome.min.css" ;
			public $CheminJsSbAdmin = "js/sb-admin-2.js" ;
			public $CheminJsMetisMenu = "js/plugins/metisMenu/metisMenu.min.js" ;
			public $CheminJsTimeline = "css/plugins/timeline.css" ;
			public $InclureRenduTitreScript = 1 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeCompsSpec() ;
			}
			public function InclutLibrairiesExternes()
			{
				parent::InclutLibrairiesExternes() ;
				$this->InscritLienCSS($this->CheminCSSMetisMenu) ;
				$this->InscritLienCSS($this->CheminCSSTimeline) ;
				$this->InscritLienCSS($this->CheminCSSFontAwesome) ;
				$this->InscritLienCSS($this->CheminCSSSbAdmin) ;
				$this->InscritLienJs($this->CheminJsMetisMenu) ;
				$this->InscritLienJs($this->CheminJsSbAdmin) ;
			}
			protected function ChargeCompsSpec()
			{
				$this->ChargeNavbarHeader() ;
				$this->ChargeNavbarTopLinks() ;
				$this->ChargeSideMenu() ;
				$this->ChargeFooter() ;
			}
			protected function CreeNavbarHeader()
			{
				return new PvNavbarHeaderSbAdmin() ;
			}
			protected function ChargeNavbarHeader()
			{
				$this->NavbarHeader = $this->CreeNavbarHeader() ;
				$this->NavbarHeader->AdopteZone('navbarHeader', $this) ;
				$this->NavbarHeader->ChargeConfig() ;
			}
			protected function CreeNavbarTopLinks()
			{
				return new PvNavbarTopLinksSbAdmin() ;
			}
			protected function ChargeNavbarTopLinks()
			{
				$this->NavbarTopLinks = $this->CreeNavbarTopLinks() ;
				$this->NavbarTopLinks->AdopteZone('navbarTopLinks', $this) ;
				$this->NavbarTopLinks->ChargeConfig() ;
			}
			protected function CreeFooter()
			{
				return new PvFooterSbAdmin() ;
			}
			protected function ChargeFooter()
			{
				$this->Footer = $this->CreeFooter() ;
				$this->Footer->AdopteZone('footer', $this) ;
				$this->Footer->ChargeConfig() ;
			}
			protected function CreeSideMenu()
			{
				return new PvSideMenuSbAdmin() ;
			}
			protected function ChargeSideMenu()
			{
				$this->SideMenu = $this->CreeSideMenu() ;
				$this->SideMenu->AdopteZone('sideMenu', $this) ;
				$this->SideMenu->ChargeConfig() ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '<body>' ;
				$ctn .= '<div id="wrapper">' ;
				$ctn .= '<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">' ;
				$ctn .= $this->NavbarHeader->RenduDispositif() ;
				$ctn .= $this->NavbarTopLinks->RenduDispositif() ;
				$ctn .= '<div class="navbar-default sidebar" role="navigation">' ;
				$ctn .= '<div class="sidebar-nav navbar-collapse">' ;
				$ctn .= $this->SideMenu->RenduDispositif() ;
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				$ctn .= '</nav>' ;
				$ctn .= '<div id="page-wrapper">' ;
				if($this->InclureRenduTitreScript)
				{
					$ctn .= '<div class="col-lg-12">
<h1 class="page-header">'.$this->ScriptPourRendu->ObtientTitre().'</h1>
</div>' ;
				}
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				if($this->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduCtnJs() ;
				}
				$ctn .= '</body>' ;
				return $ctn ;
			}
		}
		
		class PvNavbarHeaderSbAdmin extends PvComposantIUBase
		{
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div class="navbar-header">' ;
				$ctn .= '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="'.$this->ZoneParent->ObtientUrl().'">'.$this->ZoneParent->NomLogo.'</a>' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class PvNavbarTopLinksSbAdmin extends PvComposantIUBase
		{
			public $InclureDropdownMessages = 0 ;
			public $FournDonneesMessages ;
			public $NomColAuteurMessage ;
			public $NomColDateEnvoiMessage ;
			public $NomColContenuMessage ;
			public $InclureDropdownTasks = 1 ;
			public $FournDonneesTask ;
			public $NomColTitreTask ;
			public $NomColPourcentExecTask ;
			public $InclureDropdownAlerts = 1 ;
			public $FournDonneesAlerts ;
			public $NomColGlighIconAlert ;
			public $NomColTitreAlert ;
			public $NomColDateAlert ;
			public $InclureDropdownUser = 1 ;
			public $TitreLienConnexion = "Se connecter" ;
			public $GlyphiconConnexion = "fa-unlock-alt" ;
			public $TitreLienInscription = "S'inscrire" ;
			public $GlyphiconInscription = "fa-key" ;
			public $TitreLienChangeMP = "Changer mot de passe" ;
			public $GlyphiconChangeMP = "" ;
			public $TitreLienModifPrefs = "Param&egrave;tres" ;
			public $GlyphiconModifPrefs = "fa-gear" ;
			public $TitreLienListeMembres = "Membres" ;
			public $GlyphiconListeMembres = "fa-user" ;
			public $TitreLienAjoutMembre = "Inscription" ;
			public $GlyphiconAjoutMembre = "" ;
			public $TitreLienListeProfils = "Profils" ;
			public $GlyphiconListeProfils = "" ;
			public $TitreLienAjoutProfil = "Ajout profil" ;
			public $GlyphiconAjoutProfil = "" ;
			public $TitreLienListeRoles = "R&ocirc;les" ;
			public $GlyphiconListeRoles = "" ;
			public $TitreLienAjoutRole = "Ajout r&ocirc;le" ;
			public $GlyphiconAjoutRole = "" ;
			public $TitreLienDeconnexion = "D&eacute;connexion" ;
			public $GlyphiconDeconnexion = "" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<ul class="nav navbar-top-links navbar-right">' ;
				if($this->InclureDropdownMessages)
				{
					$ctn .= '<li class="dropdown">' ;
					$ctn .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i></a>' ;
					$ctn .= '</li>' ;
					$i = 0 ;
				}
				if($this->InclureDropdownUser && $this->ZoneParent->InclureScriptsMembership && $this->ZoneParent->MembershipActive())
				{
					$ctn .= '<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">
<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
</a>
<ul class="dropdown-menu dropdown-user">' ;
					if($this->ZoneParent->PossedeMembreConnecte())
					{
						if($this->ZoneParent->AutoriserModifPrefs && $this->ZoneParent->ScriptModifPrefs->EstAccessible())
						{
							$ctn .= '<li><a href="'.$this->ZoneParent->ScriptModifPrefs->ObtientUrl().'"><i class="fa '.$this->GlyphiconModifPrefs.' fa-fw"></i> '.$this->TitreLienModifPrefs.'</a></li>'.PHP_EOL ;
						}
						if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptListeMembres) && $this->ZoneParent->ScriptListeMembres->EstAccessible())
						{
							$ctn .= '<li><a href="'.$this->ZoneParent->ScriptListeMembres->ObtientUrl().'"><i class="fa '.$this->GlyphiconListeMembres.' fa-fw"></i> '.$this->TitreLienListeMembres.'</a></li>'.PHP_EOL ;
						}
						if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptAjoutMembre) && $this->ZoneParent->ScriptAjoutMembre->EstAccessible())
						{
							$ctn .= '<li><a href="'.$this->ZoneParent->ScriptAjoutMembre->ObtientUrl().'"><i class="fa '.$this->GlyphiconAjoutMembre.' fa-fw"></i> '.$this->TitreLienAjoutMembre.'</a></li>'.PHP_EOL ;
						}
						if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptListeProfils) && $this->ZoneParent->ScriptListeProfils->EstAccessible())
						{
							$ctn .= '<li><a href="'.$this->ZoneParent->ScriptListeProfils->ObtientUrl().'"><i class="fa '.$this->GlyphiconListeProfils.' fa-fw"></i> '.$this->TitreLienListeProfils.'</a></li>'.PHP_EOL ;
						}
						if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptAjoutProfil) && $this->ZoneParent->ScriptAjoutProfil->EstAccessible())
						{
							$ctn .= '<li><a href="'.$this->ZoneParent->ScriptAjoutProfil->ObtientUrl().'"><i class="fa '.$this->GlyphiconAjoutProfil.' fa-fw"></i> '.$this->TitreLienAjoutProfil.'</a></li>'.PHP_EOL ;
						}
						if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptListeRoles) && $this->ZoneParent->ScriptListeRoles->EstAccessible())
						{
							$ctn .= '<li><a href="'.$this->ZoneParent->ScriptListeRoles->ObtientUrl().'"><i class="fa '.$this->GlyphiconListeRoles.' fa-fw"></i> '.$this->TitreLienListeRoles.'</a></li>'.PHP_EOL ;
						}
						if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptAjoutRole) && $this->ZoneParent->ScriptAjoutRole->EstAccessible())
						{
							$ctn .= '<li><a href="'.$this->ZoneParent->ScriptAjoutRole->ObtientUrl().'"><i class="fa '.$this->GlyphiconAjoutRole.' fa-fw"></i> '.$this->TitreLienAjoutRole.'</a></li>'.PHP_EOL ;
						}
					}
					else
					{
						$ctn .= '<li><a href="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->NomScriptConnexion).'"><i class="fa '.$this->GlyphiconConnexion.' fa-fw"></i> '.$this->TitreLienConnexion.'</a></li>'.PHP_EOL ;
						if($this->ZoneParent->AutoriserInscription && $this->ZoneParent->EstPasNul($this->ZoneParent->ScriptInscription))
						{
							$ctn .= '<li><a href="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->NomScriptInscription).'"><i class="fa '.$this->GlyphiconInscription.' fa-fw"></i> '.$this->TitreLienInscription.'</a></li>'.PHP_EOL ;
						}
					}
					$ctn .= '</ul>' ;
					$ctn .= '</li>' ;
				}
				$ctn .= '</ul>' ;
				return $ctn ;
			}
		}
		
		class PvSideMenuSbAdmin extends PvBarreMenuBaseBootstrap
		{
			public $InclureRecherche ;
			public $UrlRecherche ;
			public $LibelleRecherche ;
			protected function RenduMenuRecherche()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduMenuRacine(& $menu)
			{
				$ctn = '' ;
				$ctn .= '<ul class="nav" id="'.$this->IDInstanceCalc.'">' ;
				if($this->InclureRecherche)
				{
					$ctn .= $this->RenduMenuRecherche() ;
				}
				foreach($menu->SousMenus as $i => $sousMenu)
				{
					$ctn .= $this->RenduMenuNv1($sousMenu, $i).PHP_EOL ;
				}
				$ctn .= '</ul>' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('jQuery(function() {
    jQuery("#'.$this->IDInstanceCalc.'").metisMenu();
});') ;
				return $ctn ;
			}
			protected function RenduMenuNv1($menu, $pos=-1)
			{
				$ctn = '' ;
				$ctn .= '<li><a href="'.$menu->ObtientUrl().'"><i class="fa '.$menu->ObtientValCfgSpec('glyphicon', 'fa-dashboard').' fa-fw"></i> '.$this->RenduTitreMenu($menu).'</a>' ;
				if(count($menu->SousMenus) > 0)
				{
					$ctn .= '<ul id="'.$this->IDInstanceCalc.'"  class="nav nav-second-level">'.PHP_EOL ;
					foreach($menu->SousMenus as $i => $sousMenu)
					{
						$ctn .= $this->RenduMenuNv2($sousMenu, $i).PHP_EOL ;
					}
					$ctn .= '</ul>'.PHP_EOL ;
				}
				$ctn .= '</li>' ;
				return $ctn ;
			}
			protected function RenduMenuNv2($menu, $pos=-1)
			{
				$ctn = '' ;
				$ctn .= '<li><a href="'.$menu->ObtientUrl().'"' ;
				$ctn .= '>' ;
				$ctn .= $this->RenduTitreMenu($menu) ;
				if(count($menu->SousMenus) > 0)
				{
					$ctn .= ' <span class="fa arrow"></span>' ;
				}
				$ctn .= '</a>' ;
				if(count($menu->SousMenus) > 0)
				{
					$ctn .= '<ul class="nav nav-third-level" id="SM_'.$menu->IDInstanceCalc.'">'.PHP_EOL ;
					foreach($menu->SousMenus as $i => $sousMenu)
					{
						$ctn .= $this->RenduMenuNv3($sousMenu, $i).PHP_EOL ;
					}
					$ctn .= '</ul>' ;
				}
				return $ctn ;
			}
			protected function RenduMenuNv3($menu, $pos=-1)
			{
				$ctn = '' ;
				$ctn .= '<li><a href="'.$menu->ObtientUrl().'"'.(($menu->EstSelectionne) ? ' class="active"' : '').'>'.$this->RenduTitreMenu($menu).'</a></li>' ;
				return $ctn ;
			}
		}
		class PvFooterSbAdmin extends PvComposantIUBase
		{
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
	}
	
?>