<?php
	
	if(! defined('PV_ZONE_CHARISMA'))
	{
		if(! defined('PV_NOYAU_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_ZONE_CHARISMA', 1) ;
		
		class PvZoneCharisma extends PvZoneWebSimple
		{
			public $CompTopBar ;
			public $CompLeftMenu ;
			public $CompContent ;
			public $CompFooter ;
			public $InclureCtnJsEntete = 0 ;
			public $InclureJQuery = 1 ;
			public $InclureJQueryUi = 1 ;
			public $AuteurMeta = 'Muhammad Usman' ;
			public $NomClasseHabillage = "" ;
			public $ThemeCharisma = "cerulean" ;
			public $CheminLogo = "charisma/img/logo20.png" ;
			public $CheminDossierImgs = "charisma/img" ;
			public $CheminJQueryMigrate = "js/jquery-migrate.min.js" ;
			public $NomSite = "Charisma" ;
			public $CheminDossierCSS = "charisma/css" ;
			public $CheminDossierJs = "charisma/js" ;
			public $ViewportMeta = "width=device-width, initial-scale=1.0" ;
			public $NomClasseScriptConnexion = "ScriptConnexionCharisma" ;
			public $EncadrerCorpsDocument = 1 ;
			public $InscrireMenuMembership = 1 ;
			public $PrivilegesMenuMembership = array() ;
			public $LibelleMenuMembership = "Authentification" ;
			public $InscrireMenuDeconnexion = 1 ;
			public $LibelleMenuDeconnexion = "D&eacute;connexion" ;
			public $InscrireMenuChangeMotPasse = 1 ;
			public $LibelleMenuChangeMotPasse = "Changer mot de passe" ;
			public $InscrireMenuListeMembres = 1 ;
			public $LibelleMenuListeMembres = "Tous les Membres" ;
			public $InscrireMenuAjoutMembre = 1 ;
			public $LibelleMenuAjoutMembre = "Ajout membre" ;
			public $InscrireMenuListeRoles = 1 ;
			public $LibelleMenuListeRoles = "Tous les roles" ;
			public $InscrireMenuAjoutRole = 1 ;
			public $LibelleMenuAjoutRole = "Ajout role" ;
			public $InscrireMenuListeProfils = 1 ;
			public $LibelleMenuListeProfils = "Tous les profils" ;
			public $InscrireMenuAjoutProfil = 1 ;
			public $LibelleMenuAjoutProfil = "Ajout profil" ;
			public $MenuChangeMotPasse ;
			public $MenuListeMembres ;
			public $MenuAjoutMembre ;
			public $MenuListeRoles ;
			public $MenuAjoutRole ;
			public $MenuListeProfils ;
			public $MenuAjoutProfil ;
			public $MenuDeconnexion ;
			public $MenuAuthentification = null ;
			public $Span = 10 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CompTopBar = $this->CreeCompTopBar() ;
				$this->CompLeftMenu = $this->CreeCompLeftMenu() ;
				$this->CompFooter = $this->CreeCompFooter() ;
			}
			public function RenduDocument()
			{
				$this->ChargeComps() ;
				return parent::RenduDocument() ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
			}
			protected function CreeCompTopBar()
			{
				return new TopBarCharisma() ;
			}
			protected function CreeCompLeftMenu()
			{
				return new LeftMenuCharisma() ;
			}
			protected function CreeCompFooter()
			{
				return new FooterCharisma() ;
			}
			protected function ChargeComps()
			{
				$this->ChargeTopBar() ;
				$this->ChargeLeftMenu() ;
				$this->ChargeFooter() ;
			}
			protected function ChargeTopBar()
			{
				$this->CompTopBar->AdopteZone("topBar", $this) ;
				$this->CompTopBar->ChargeConfig() ;
			}
			protected function ChargeFooter()
			{
				$this->CompFooter->AdopteZone("footer", $this) ;
				$this->CompFooter->ChargeConfig() ;
			}
			protected function ChargeLeftMenu()
			{
				$this->CompLeftMenu->AdopteZone("leftMenu", $this) ;
				$this->CompLeftMenu->ChargeConfig() ;
				$this->RemplitLeftMenu() ;
				$this->RemplitLeftMenuAuto() ;
			}
			protected function RemplitLeftMenu()
			{
			}
			protected function RemplitLeftMenuAuto()
			{
				$ok = $this->PossedePrivileges($this->PrivilegesMenuMembership) ;
				if($this->InscrireMenuMembership && $ok)
				{
					$this->CompLeftMenu->MenuRacine->InscritSousMenuFige("authentification") ;
					$this->MenuAuthentification = & $this->CompLeftMenu->MenuRacine->SousMenus["authentification"] ;
					$this->MenuAuthentification->Titre = $this->LibelleMenuMembership ;
					if($this->InscrireMenuDeconnexion)
					{
						$this->MenuDeconnexion = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptDeconnexion) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptDeconnexion]->Titre = $this->LibelleMenuDeconnexion ;
					}
					if($this->InscrireMenuChangeMotPasse)
					{
						$this->MenuChangeMotPasse = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptChangeMotPasse) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptChangeMotPasse]->Titre = $this->LibelleMenuChangeMotPasse ;
					}
					if($this->InscrireMenuAjoutMembre)
					{
						$this->MenuAjoutMembre = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptAjoutMembre) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptAjoutMembre]->Titre = $this->LibelleMenuAjoutMembre ;
					}
					if($this->InscrireMenuListeMembres)
					{
						$this->MenuListeMembres = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptListeMembres) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptListeMembres]->Titre = $this->LibelleMenuListeMembres ;
					}
					$this->ChargeAutresMenusMembres() ;
					if($this->InscrireMenuAjoutProfil)
					{
						$this->MenuAjoutProfil = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptAjoutProfil) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptAjoutProfil]->Titre = $this->LibelleMenuAjoutProfil ;
					}
					if($this->InscrireMenuListeProfils)
					{
						$this->MenuListeProfils = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptListeProfils) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptListeProfils]->Titre = $this->LibelleMenuListeProfils ;
					}
					$this->ChargeAutresMenusProfils() ;
					if($this->InscrireMenuAjoutRole)
					{
						$this->MenuAjoutRole = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptAjoutRole) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptAjoutRole]->Titre = $this->LibelleMenuAjoutRole ;
					}
					if($this->InscrireMenuListeRoles)
					{
						$this->MenuListeRoles = $this->MenuAuthentification->InscritSousMenuScript($this->NomScriptListeRoles) ;
						$this->MenuAuthentification->SousMenus[$this->NomScriptListeRoles]->Titre = $this->LibelleMenuListeRoles ;
					}
					$this->ChargeAutresMenusRoles() ;
				}
			}
			protected function ChargeAutresMenusMembres()
			{
			}
			protected function ChargeAutresMenusProfils()
			{
			}
			protected function ChargeAutresMenusRoles()
			{
			}
			public function InclutLibrairiesExternes()
			{
				$this->RenduExtraHead .= '<script type="text/javascript" src="'.$this->CheminJQuery.'"></script>'.PHP_EOL ;
				$this->RenduExtraHead .= '<script type="text/javascript" src="'.$this->CheminJQueryMigrate.'"></script>'.PHP_EOL ;
				$this->RenduExtraHead .= '<script type="text/javascript" src="'.$this->CheminJsJQueryUi.'"></script>'.PHP_EOL ;
				$this->RenduExtraHead .= '<link rel="stylesheet" type="text/css" href="'.$this->CheminCSSJQueryUi.'" />'.PHP_EOL ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/bootstrap-'.$this->ThemeCharisma.'.css') ;
				$this->InscritContenuCSS('body { padding-bottom: 40px; }
.sidebar-nav { padding: 9px 0; }') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/bootstrap-responsive.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/charisma-app.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/fullcalendar.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/fullcalendar.print.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/chosen.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/uniform.default.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/colorbox.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/jquery.cleditor.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/jquery.noty.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/noty_theme_default.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/elfinder.min.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/elfinder.theme.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/opa-icons.css') ;
				$this->InscritLienCSS($this->CheminDossierCSS.'/uploadify.css') ;
				$this->InscritLienJsCmpIE($this->CheminDossierJs.'/html5.js', 9) ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-transition.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-alert.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-modal.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-dropdown.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-scrollspy.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-tab.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-tooltip.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-button.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-collapse.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-typeahead.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/bootstrap-tour.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.cookie.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/fullcalendar.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/excanvas.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.flot.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.flot.pie.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.flot.stack.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.flot.resize.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.dataTables.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.uniform.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.colorbox.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.cleditor.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.noty.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.elfinder.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.elfinder.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.uploadify-3.1.min.js') ;
				$this->InscritLienJs($this->CheminDossierJs.'/jquery.history.js') ;
				// $this->InscritLienJs($this->CheminDossierJs.'/charisma.js') ;
				$this->InclureJQuery = 0 ;
				$this->InclureJQueryUi = 0 ;
				parent::InclutLibrairiesExternes() ;
				$this->InclureJQuery = 1 ;
				$this->InclureJQueryUi = 1 ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = parent::RenduEnteteCorpsDocument().PHP_EOL ;
				if($this->EncadrerCorpsDocument)
				{
					if($this->PossedeMembreConnecte())
					{
						if($this->EstPasNul($this->CompTopBar))
							$ctn .= $this->CompTopBar->RenduDispositif().PHP_EOL ;
						$ctn .= '<div class="container-fluid">
<div class="row-fluid">'.PHP_EOL ;
						if($this->EstPasNul($this->CompLeftMenu))
							$ctn .= $this->CompLeftMenu->RenduDispositif().PHP_EOL ;
						$ctn .= '<div id="content" class="span'.$this->Span.'">'.PHP_EOL ;
					}
				}
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				if($this->EncadrerCorpsDocument)
				{
					if($this->PossedeMembreConnecte())
					{
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
						if($this->EstPasNul($this->CompFooter))
							$ctn .= $this->CompFooter->RenduDispositif() ;
						$ctn .= '</div>'.PHP_EOL ;
					}
				}
				$ctn .= parent::RenduPiedCorpsDocument() ;
				return $ctn ;
			}
		}
	}
	
?>