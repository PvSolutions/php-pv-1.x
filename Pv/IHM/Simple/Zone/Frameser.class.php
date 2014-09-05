<?php
	
	if(! defined('PV_ZONE_FRAMESER'))
	{
		if(! defined('PV_ZONE_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Zone.class.php" ;
		}
		
		class PvThemeBaseFrameser
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
			public $CouleurArrPlanEntete1 = "#3963ff" ;
			public $CouleurArrPlanEntete2 = "#b5dc10" ;
			public $CouleurContenuEntete1 = "#FFFF00" ;
			public $CouleurContenuEntete2 = "white" ;
			public $CouleurArrPlanDoc = "white" ;
			public $CouleurLiens1 = "#3963ff" ;
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
				$ctn .= '.iu-frameser-entete-1 { background-color:'.$this->CouleurArrPlanEntete1.' ; color:'.$this->CouleurContenuEntete1.' ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-entete-2 { background-color:'.$this->CouleurArrPlanEntete2.' ; color:'.$this->CouleurContenuEntete2.' ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-corps-1 { background-color:'.$this->CouleurArrPlanCorps1.' ; color:'.$this->CouleurContenuCorps1.' ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-corps-2 { background-color:'.$this->CouleurArrPlanCorps2.' ; color:'.$this->CouleurContenuCorps2.' ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail { }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail .titre { font-size:'.$this->TailleContenuGrdTitre.' ; color:'.$this->CouleurContenuGrdTitre.'; margin-top:12px; margin-bottom:12px ; font-weight:bold ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail .Succes { background-color:'.$this->CouleurArrPlanSucces.' ; color:'.$this->CouleurContenuSucces.'; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail .Erreur { background-color:'.$this->CouleurArrPlanErreur.' ; color:'.$this->CouleurContenuErreur.'; }'.PHP_EOL ;
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
		
		class PvZoneFrameser extends PvZoneWebSimple
		{
			public $CheminLogo = "images/logo.png" ;
			public $NiveauDev = "Beta" ;
			public $NomPublicateur = "" ;
			public $MargeCopyright = "8px" ;
			public $ScriptAccueil ;
			public $ActVoletNav ;
			public $BarreMenu1VoletNav ;
			public $BarreMenu2VoletNav ;
			public $LargeurVoletNav = "250" ;
			public $NomCadreNav = "navigation" ;
			public $NomCadrePrinc = "contenu" ;
			public $LargeurCadrePrinc = "*" ;
			public $UrlPageFacebook = "" ;
			public $ScriptRSS ;
			public $NomScriptRSS = "rss" ;
			public $ScriptAPropos ;
			public $NomScriptAPropos = "a_propos" ;
			public $ScriptConfidentialite ;
			public $NomScriptConfidentialite = "confidentialite" ;
			public $ScriptSupport ;
			public $NomScriptSupport = "support" ;
			public $InclureSousMenuAccueil = 1 ;
			public $SousMenuAccueil ;
			public $SousMenuConnexion ;
			public $SousMenuDeconnexion ;
			public $SousMenuAPropos ;
			public $SousMenuConfidentialite ;
			public $SousMenuSupport ;
			public $BarreMembreCadrePrinc ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Theme = new PvThemeBaseFrameser() ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeActVoletNav() ;
				$this->ChargeBarreMenu1VoletNav() ;
				$this->ChargeBarreMenu2VoletNav() ;
				$this->ChargeBarreMembreCadrePrinc() ;
			}
			protected function CreeBarreMembreCadrePrinc()
			{
				return new PvBarreMembreFrameser() ;
			}
			protected function ChargeBarreMembreCadrePrinc()
			{
				$this->BarreMembreCadrePrinc = $this->CreeBarreMembreCadrePrinc() ;
				$this->BarreMembreCadrePrinc->AdopteZone('barreMembre', $this) ;
				$this->BarreMembreCadrePrinc->ChargeConfig() ;
			}
			protected function ContenuCSSGlobal()
			{
				$ctn = '' ;
				$ctn .= '.iu-frameser-logo, .iu-frameser-logo span { }'.PHP_EOL ;
				$ctn .= '.iu-frameser-copyright { padding-top:'.$this->MargeCopyright.'; padding-bottom:'.$this->MargeCopyright.'; }'.PHP_EOL ;
				$ctn .= $this->Theme->ContenuDefCSS() ;
				return $ctn ;
			}
			public function InclutLibrairiesExternes()
			{
				parent::InclutLibrairiesExternes() ;
				$this->InscritContenuCSS($this->ContenuCSSGlobal()) ;
			}
			protected function CreeVoletNav()
			{
				return new PvActVoletNavFrameser() ;
			}
			protected function ChargeActVoletNav()
			{	
				$this->ActVoletNav = $this->CreeVoletNav() ;
				$this->InscritActionAvantRendu('voletNav', $this->ActVoletNav) ;
				$this->ActVoletNav->ChargeConfig() ;
				$this->ActVoletNav->InscritContenuCSS($this->ContenuCSSGlobal()) ;
			}
			protected function CreeBarreMenu1VoletNav()
			{
				return new PvBlocMenuVertic() ;
			}
			protected function ChargeBarreMenu1VoletNav()
			{
				$this->BarreMenu1VoletNav = $this->CreeBarreMenu1VoletNav() ;
				$this->BarreMenu1VoletNav->AdopteZone("barreMenu1VoletNav", $this) ;
				$this->BarreMenu1VoletNav->ChargeConfig() ;
			}
			protected function CreeBarreMenu2VoletNav()
			{
				return new PvBlocMenuVertic() ;
			}
			protected function ChargeBarreMenu2VoletNav()
			{	
				$this->BarreMenu2VoletNav = $this->CreeBarreMenu2VoletNav() ;
				$this->BarreMenu2VoletNav->AdopteZone("barreMenu2VoletNav", $this) ;
				$this->BarreMenu2VoletNav->ChargeConfig() ;
			}
			protected function ChargeMenusAuto1VoletNav()
			{
				if($this->InclureSousMenuAccueil)
				{
					$this->SousMenuAccueil = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptParDefaut) ;
					$this->SousMenuAccueil->FenetreCible = $this->NomCadrePrinc ;
				}
				if($this->InclureScriptsMembership == 1 && $this->EstPasNul($this->Membership))
				{
					if(! $this->PossedeMembreConnecte())
					{
						$this->SousMenuConnexion = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptConnexion) ;
						$this->SousMenuConnexion->FenetreCible = $this->NomCadrePrinc ;
					}
					else
					{
						$this->SousMenuDeconnexion = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptDeconnexion) ;
						$this->SousMenuDeconnexion->FenetreCible = $this->NomCadrePrinc ;
					}
				}
			}
			protected function ChargeMenusAuto2VoletNav()
			{
				if($this->EstPasNul($this->ScriptAPropos))
				{
					$this->SousMenuAPropos = $this->BarreMenu2VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptAPropos) ;
					$this->SousMenuAPropos->FenetreCible = $this->NomCadrePrinc ;
				}
				if($this->EstPasNul($this->ScriptConfidentialite))
				{
					$this->SousMenuConfidentialite = $this->BarreMenu2VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptConfidentialite) ;
					$this->SousMenuConfidentialite->FenetreCible = $this->NomCadrePrinc ;
				}
				if($this->EstPasNul($this->ScriptSupport))
				{
					$this->SousMenuSupport = $this->BarreMenu2VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptSupport) ;
					$this->SousMenuSupport->FenetreCible = $this->NomCadrePrinc ;
				}
			}
			protected function ChargeAutresMenus1VoletNav()
			{
			}
			protected function ChargeAutresMenus2VoletNav()
			{
			}
			protected function DetermineEnvironnement(& $script)
			{
				parent::DetermineEnvironnement($script) ;
				$this->DetermineCompsRendu() ;
			}
			protected function DetermineCompsRendu()
			{
				$this->ChargeMenusAuto1VoletNav() ;
				$this->ChargeAutresMenus1VoletNav() ;
				$this->ChargeMenusAuto2VoletNav() ;
				$this->ChargeAutresMenus2VoletNav() ;
			}
			public function RenduDocument()
			{
				$ctn = "" ;
				if($this->ValeurBruteParamScriptAppele == "")
				{
					$ctn .= $this->RenduDocumentGlobal() ;
				}
				else
				{
					$ctn .= parent::RenduDocument() ;
				}
				return $ctn ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= parent::RenduEnteteCorpsDocument() ;
				$ctn .= $this->BarreMembreCadrePrinc->RenduDispositif() ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= parent::RenduPiedCorpsDocument() ;
				return $ctn ;
			}
			protected function RenduDocumentGlobal()
			{
				$ctn = "" ;
				$ctn .= '<!doctype html>'.PHP_EOL ;
				$ctn .= '<html>'.PHP_EOL ;
				$ctn .= $this->RenduEnteteDocument() ;
				$ctn .= '<frameset cols="'.$this->LargeurVoletNav.','.$this->LargeurCadrePrinc.'">
	<frame src="'.$this->ActVoletNav->ObtientUrl().'" frameborder=0 bordercolor=black scrolling="no" name="'.$this->NomCadreNav.'">
	<frame src="'.$this->ScriptParDefaut->ObtientUrl().'" frameborder=0 name="'.$this->NomCadrePrinc.'">
	<noframes>
		Mettez a jour votre navigateur pour qu\'il prenne en charge des cadres !!!
	</noframes>' ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
		}
		class PvActVoletNavFrameser extends PvActionRenduPageWeb
		{
			protected function RenduCorpsDoc()
			{
				$ctn = '' ;
				$ctn .= '<div class="logo">' ;
				$ctn .= '<div><img src="'.$this->ZoneParent->CheminLogo.'" /></div>' ;
				$ctn .= '<div>' ;
				$ctn .= '<i>'.$this->ZoneParent->NiveauDev.'</i> - ' ;
				$ctn .= $this->ZoneParent->NomPublicateur ;
				$ctn .= '</div>' ;
				$ctn .= '<hr />' ;
				$ctn .= $this->ZoneParent->BarreMenu1VoletNav->RenduDispositif() ;
				$ctn .= '<hr />' ;
				$ctn .= $this->ZoneParent->BarreMenu2VoletNav->RenduDispositif() ;
				$ctn .= '<hr />' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class PvBarreMembreFrameser extends PvComposantIUBase
		{
			public $NomClasseCSS = "barre-membre" ;
			public $Align = "right" ;
			public $LegendeNonConnecte = "Nouveau ?" ;
			public $LibelleInscription = "Inscription" ;
			public $LibelleConnexion = "Connexion" ;
			public $LibelleDeconnexion = "Deconnexion" ;
			public $LibelleModifInfos = "Param&egrave;tres" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSS.'" align="'.$this->Align.'">'.PHP_EOL ;
				if(! $this->ZoneParent->PossedeMembreConnecte())
				{
					if($this->LegendeNonConnecte != "")
					{
						$ctn .= $this->LegendeNonConnecte." " ;
						$ctn .= '<span class="sep">|</span> ' ;
					}
					if($this->ZoneParent->AutoriserInscription == 1)
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptInscription->ObtientUrl().'">'.$this->LibelleInscription.'</a>' ;
						$ctn .= ' &bull; ' ;
					}
					$ctn .= '<a href="'.$this->ZoneParent->ScriptConnexion->ObtientUrl().'">'.$this->LibelleConnexion.'</a>' ;
				}
				else
				{
					$ctn .= htmlentities($this->ZoneParent->Membership->MemberLogged->Login) ;
					$ctn .= '<span class="sep">|</span> ' ;
					$ctn .= '<a href="'.$this->ZoneParent->ScriptModifMembre->ObtientUrl().'">'.$this->LibelleModifInfos.'</a>' ;
					$ctn .= ' &bull; ' ;
					$ctn .= '<a href="'.$this->ZoneParent->ScriptDeconnexion->ObtientUrl().'">'.$this->LibelleDeconnexion.'</a>' ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class PvBlocTitreScriptFrameser extends PvComposantIUBase
		{
			public $NomClasseCSS = "titre" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSS.'">'.$this->ZoneParent->ScriptPourRendu->Titre.'</div>' ;
				return $ctn ;
			}
		}
	}
	
?>