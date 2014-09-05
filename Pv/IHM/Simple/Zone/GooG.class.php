<?php
	
	if(! defined('PV_ZONE_GOOG'))
	{
		if(! defined('PV_ZONE_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Zone.class.php" ;
		}
		
		class PvThemeBaseGoog
		{
			public $NomPolice = "tahoma" ;
			public $TaillePolice = "12px" ;
			public $CouleurPolice = "#666666" ;
			public $CouleurArrPlanCorps1 = "#EFF4FA" ;
			public $CouleurBordureCorps1 = "white" ;
			public $CouleurContenuCorps1 = "black" ;
			public $CouleurArrPlanCorps2 = "#d2ddea" ;
			public $CouleurBordureCorps2 = "black" ;
			public $CouleurContenuCorps2 = "black" ;
			public $CouleurArrPlanCorps3 = "#b2d0ef" ;
			public $CouleurBordureCorps3 = "white" ;
			public $CouleurContenuCorps3 = "#7d7e7f" ;
			public $CouleurArrPlanSurvole = "#dedede" ;
			public $CouleurContenuSurvole = "#030303" ;
			public $CouleurContenuGrdTitre = "#040404" ;
			public $TailleContenuGrdTitre = "16px" ;
			public $CouleurArrPlanEntete1 = "#29537C" ;
			public $CouleurArrPlanEntete2 = "#b5dc10" ;
			public $CouleurContenuEntete1 = "#FFFF00" ;
			public $CouleurContenuEntete2 = "white" ;
			public $CouleurArrPlanDoc = "#e5f2fe" ;
			public $CouleurLiens1 = "#29537C" ;
			public $CouleurLiens2 = "#515151" ;
			public $CouleurArrPlanSucces = "#cfffc0" ;
			public $CouleurContenuSucces = "#124d00" ;
			public $CouleurArrPlanErreur = "#ffcbcb" ;
			public $CouleurContenuErreur = "#720000" ;
			public function ContenuDefCSS()
			{
				$ctn = '' ;
				$ctn .= 'body { background:'.$this->CouleurArrPlanDoc.' ; font-family:'.$this->NomPolice.'; font-size:'.$this->TaillePolice.'; color:'.$this->CouleurPolice.'; }'.PHP_EOL ;
				$ctn .= 'a:link, a:visited { color:'.$this->CouleurLiens1.'; }'.PHP_EOL ;
				$ctn .= '.iu-goog-entete-1 { background-color:'.$this->CouleurArrPlanEntete1.' ; color:'.$this->CouleurContenuEntete1.' ; }'.PHP_EOL ;
				$ctn .= '.iu-goog-entete-2 { background-color:'.$this->CouleurArrPlanEntete2.' ; color:'.$this->CouleurContenuEntete2.' ; }'.PHP_EOL ;
				$ctn .= '.iu-goog-corps-1 { background-color:'.$this->CouleurArrPlanCorps1.' ; color:'.$this->CouleurContenuCorps1.' ; }'.PHP_EOL ;
				$ctn .= '.iu-goog-corps-2 { background-color:'.$this->CouleurArrPlanCorps2.' ; color:'.$this->CouleurContenuCorps2.' ; }'.PHP_EOL ;
				$ctn .= '.iu-goog-espace-travail { }'.PHP_EOL ;
				$ctn .= '.iu-goog-espace-travail .titre { font-size:'.$this->TailleContenuGrdTitre.' ; color:'.$this->CouleurContenuGrdTitre.'; margin-top:12px; margin-bottom:12px ; font-weight:bold ; }'.PHP_EOL ;
				$ctn .= '.iu-goog-espace-travail .Succes { background-color:'.$this->CouleurArrPlanSucces.' ; color:'.$this->CouleurContenuSucces.'; }'.PHP_EOL ;
				$ctn .= '.iu-goog-espace-travail .Erreur { background-color:'.$this->CouleurArrPlanErreur.' ; color:'.$this->CouleurContenuErreur.'; }'.PHP_EOL ;
				$ctn .= '.BlocCommandes, .FormulaireFiltres, .RangeeDonnees { padding-top:4px; padding-bottom:4px; margin-top:4px; margin-bottom:4px; }'.PHP_EOL ;
				$ctn .= '.FormulaireFiltres { background-color:'.$this->CouleurArrPlanCorps1.'; border:1px solid '.$this->CouleurBordureCorps1.' ; }'.PHP_EOL ;
				$ctn .= '.BlocCommandes { background-color:'.$this->CouleurArrPlanCorps3.'; padding-top:8px ; padding-bottom:8px ; }'.PHP_EOL ;
				$ctn .= '.BlocCommandes { border:1px solid '.$this->CouleurBordureCorps3.' }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees tr, .RangeeDonnees td, .RangeeDonnees th, .RangeeDonnees { border:0px ; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Entete { background-color:'.$this->CouleurArrPlanEntete1.'; color:'.$this->CouleurContenuEntete1.'; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Pair { background-color:'.$this->CouleurArrPlanCorps1.'; color:'.$this->CouleurContenuCorps1.'; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Impair { background-color:'.$this->CouleurArrPlanCorps2.'; color:'.$this->CouleurContenuCorps2.';   }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Survole { background-color:'.$this->CouleurArrPlanSurvole.'; color:'.$this->CouleurContenuSurvole.' ; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees { border:1px solid '.$this->CouleurArrPlanCorps3.' ; padding:0px; }' ;
				return $ctn ;
			}
		}
		class PvThemeVertGoog extends PvThemeBaseGoog
		{
		}
		
		class PvZoneGoog extends PvZoneWebSimple
		{
			public $NomLogo = "GooG" ;
			public $Texte1Logo = "Goo" ;
			public $Texte2Logo = "OoG" ;
			public $TexteCopyright = "(c) Zone Goog pour Personal View" ;
			public $Couleur1Logo = "#CC0066" ;
			public $Couleur2Logo = "#336600" ;
			public $TaillePoliceLogo = "48px" ;
			public $MargeCopyright = "8px" ;
			public $NomClasseHabillage = null ;
			public $BarreMenu ;
			public $InclureMenuAccueil = 1 ;
			public $InclureMenuMembership = 1 ;
			public $TitreMenuAccueil = "Accueil" ;
			public $TitreMenuListeMembres = "" ;
			public $TitreMenuListeProfils = "" ;
			public $TitreMenuListeRoles = "" ;
			public $TitreMenuDeconnexion = "" ;
			public $TitreMenuConnexion = "" ;
			public $TitreMenuInscription = "" ;
			public $MenuAccueil ;
			public $MenuListeMembres ;
			public $MenuListeProfils ;
			public $MenuListeRoles ;
			public $MenuDeconnexion ;
			public $MenuConnexion ;
			public $MenuInscription ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Theme = new PvThemeBaseGoog() ;
			}
			public function DetermineEnvironnement(& $script)
			{
				parent::DetermineEnvironnement($script) ;
				$this->ChargeBarreMenu() ;
			}
			protected function CreeBarreMenu()
			{
				return new PvListeMenuHoriz() ;
			}
			protected function ChargeBarreMenu()
			{
				$this->BarreMenu = $this->CreeBarreMenu() ;
				$this->BarreMenu->AdopteZone("barreMenu", $this) ;
				$this->BarreMenu->ChargeConfig() ;
				if($this->InclureMenuAccueil)
				{
					$this->MenuAccueil = $this->BarreMenu->MenuRacine->InscritSousMenuScript($this->NomScriptParDefaut) ;
					if($this->TitreMenuAccueil != "")
					{
						$this->MenuAccueil->Titre = $this->TitreMenuAccueil ;
					}
				}
				$this->ChargeAutresMenus() ;
				if($this->InclureMenuMembership && $this->InclureScriptsMembership)
				{
					if($this->PossedeMembreConnecte())
					{
						$this->MenuListeMembres = $this->BarreMenu->MenuRacine->InscritSousMenuScript($this->NomScriptListeMembres) ;
						$this->MenuDeconnexion = $this->BarreMenu->MenuRacine->InscritSousMenuScript($this->NomScriptDeconnexion) ;
					}
					else
					{
						$this->MenuConnexion = $this->BarreMenu->MenuRacine->InscritSousMenuScript($this->NomScriptConnexion) ;
						$this->MenuInscription = $this->BarreMenu->MenuRacine->InscritSousMenuScript($this->NomScriptInscription) ;
					}
					/*
					$this->MenuMembres = $this->BarreMenu->MenuRacine->InscritMenuScript($this->NomScriptParDefaut) ;
					if($this->LibelleMenuAccueil != "")
					{
						$this->MenuAccueil->Libelle = $this->LibelleMenuAccueil ;
					}
					*/
				}
			}
			protected function ChargeAutresMenus()
			{
			}
			protected function ContenuCSSGlobal()
			{
				$ctn = '' ;
				$ctn .= '.iu-goog-logo, .iu-goog-logo span { font-size:'.$this->TaillePoliceLogo.'; font-weight:normal; }'.PHP_EOL ;
				$ctn .= '.iu-goog-logo-1 { color:'.$this->Couleur1Logo.' ; font-weight:bold ; }'.PHP_EOL ;
				$ctn .= '.iu-goog-logo-2 { color:'.$this->Couleur2Logo.' ; font-weight:bold ; }'.PHP_EOL ;
				$ctn .= '.iu-goog-copyright { padding-top:'.$this->MargeCopyright.'; padding-bottom:'.$this->MargeCopyright.'; }'.PHP_EOL ;
				$ctn .= $this->Theme->ContenuDefCSS() ;
				return $ctn ;
			}
			public function InclutLibrairiesExternes()
			{
				parent::InclutLibrairiesExternes() ;
				$this->InscritContenuCSS($this->ContenuCSSGlobal()) ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = parent::RenduEnteteCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduBlocLogo().PHP_EOL ;
				$ctn .= $this->RenduBarreMenu().PHP_EOL ;
				$ctn .= '<div class="iu-goog-espace-travail">' ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= $this->RenduBlocCopyright().PHP_EOL ;
				$ctn .= parent::RenduPiedCorpsDocument() ;
				return $ctn ;
			}
			protected function RenduBarreMenu()
			{
				return $this->BarreMenu->RenduDispositif() ;
			}
			protected function RenduBlocLogo()
			{
				$ctn = '<h1 class="iu-goog-logo" align="center"><span class="iu-goog-logo-1">'.$this->Texte1Logo.'</span><span class="iu-goog-logo-2">'.$this->Texte2Logo.'</span></h1>' ;
				return $ctn ;
			}
			protected function RenduBlocCopyright()
			{
				$ctn = '<div align="center" class="iu-goog-copyright">'.$this->TexteCopyright.'</div>' ;
				return $ctn ;
			}
		}
		
	}
	
?>