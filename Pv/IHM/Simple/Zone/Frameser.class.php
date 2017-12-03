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
			public $TailleGrandTitre = "16px" ;
			public $CouleurPolice = "#666666" ;
			public $CouleurArrPlanCorps1 = "#ffffff" ;
			public $CouleurBordureCorps1 = "white" ;
			public $CouleurContenuCorps1 = "black" ;
			public $CouleurArrPlanCorps2 = "#eaeaea" ;
			public $CouleurBordureCorps2 = "black" ;
			public $CouleurContenuCorps2 = "black" ;
			public $CouleurArrPlanCorps3 = "#cbcbcb" ;
			public $CouleurBordureCorps3 = "white" ;
			public $CouleurContenuCorps3 = "#7d7e7f" ;
			public $CouleurArrPlanSurvole = "#dedede" ;
			public $CouleurContenuSurvole = "#030303" ;
			public $CouleurContenuGrdTitre = "#040404" ;
			public $TailleContenuGrdTitre = "16px" ;
			public $CouleurArrPlanEntete1 = "#cbcbcb" ;
			public $CouleurArrPlanEntete2 = "#b5dc10" ;
			public $CouleurContenuEntete1 = "black" ;
			public $CouleurContenuEntete2 = "white" ;
			public $CouleurArrPlanDoc = "white" ;
			public $CouleurLiens1 = "#3963ff" ;
			public $CouleurLiens2 = "#515151" ;
			public $CouleurArrPlanSucces = "#cfffc0" ;
			public $CouleurContenuSucces = "#124d00" ;
			public $CouleurArrPlanErreur = "#ffcbcb" ;
			public $CouleurContenuErreur = "#720000" ;
			public $CouleurBordureBarreTitre = "#AeAeAe" ;
			public function ContenuDefCSS()
			{
				$ctn = '' ;
				$ctn .= 'body { background:'.$this->CouleurArrPlanDoc.' ; font-family:'.$this->NomPolice.'; font-size:'.$this->TaillePolice.'; color:'.$this->CouleurPolice.'; padding:0px; }'.PHP_EOL ;
				$ctn .= 'a:link, a:visited { color:'.$this->CouleurLiens1.'; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-entete-1 { background-color:'.$this->CouleurArrPlanEntete1.' ; color:'.$this->CouleurContenuEntete1.' ; font-size:14px; font-weight:bold }'.PHP_EOL ;
				$ctn .= '.iu-frameser-entete-2 { background-color:'.$this->CouleurArrPlanEntete2.' ; color:'.$this->CouleurContenuEntete2.' ; font-size:14px; font-weight:bold }'.PHP_EOL ;
				$ctn .= '.iu-frameser-corps-1 { background-color:'.$this->CouleurArrPlanCorps1.' ; color:'.$this->CouleurContenuCorps1.' ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-corps-2 { background-color:'.$this->CouleurArrPlanCorps2.' ; color:'.$this->CouleurContenuCorps2.' ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail { }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail .titre { font-size:'.$this->TailleContenuGrdTitre.' ; color:'.$this->CouleurContenuGrdTitre.'; margin-top:12px; margin-bottom:12px ; font-weight:bold ; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail .Succes { background-color:'.$this->CouleurArrPlanSucces.' ; color:'.$this->CouleurContenuSucces.'; }'.PHP_EOL ;
				$ctn .= '.iu-frameser-espace-travail .Erreur { background-color:'.$this->CouleurArrPlanErreur.' ; color:'.$this->CouleurContenuErreur.'; }'.PHP_EOL ;
				$ctn .= '.ui-frameser-barre-titre { border:1px solid '.$this->CouleurBordureBarreTitre.'; }'.PHP_EOL ;
				$ctn .= '.BlocCommandes, .FormulaireFiltres, .RangeeDonnees { padding-top:4px; padding-bottom:4px; margin-top:4px; margin-bottom:4px; }'.PHP_EOL ;
				$ctn .= '.FormulaireFiltres { background-color:'.$this->CouleurArrPlanCorps1.'; border:1px solid '.$this->CouleurBordureCorps1.' ; }'.PHP_EOL ;
				$ctn .= '.BlocCommandes { background-color:'.$this->CouleurArrPlanCorps3.'; padding-top:8px ; padding-bottom:8px ; }'.PHP_EOL ;
				$ctn .= '.BlocCommandes { border:1px solid '.$this->CouleurBordureCorps3.' }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees tr, .RangeeDonnees td, .RangeeDonnees th, .RangeeDonnees { border:0px ; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Entete { background-color:'.$this->CouleurArrPlanEntete1.'; color:'.$this->CouleurContenuEntete1.'; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Pair { background-color:'.$this->CouleurArrPlanCorps1.'; color:'.$this->CouleurContenuCorps1.'; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Impair { background-color:'.$this->CouleurArrPlanCorps2.'; color:'.$this->CouleurContenuCorps2.'; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees .Survole { background-color:'.$this->CouleurArrPlanSurvole.'; color:'.$this->CouleurContenuSurvole.' ; }'.PHP_EOL ;
				$ctn .= '.RangeeDonnees { border:1px solid '.$this->CouleurArrPlanCorps3.' ; padding:0px; }' ;
				return $ctn ;
			}
		}
		
		class PvZoneFrameser extends PvZoneWebSimple
		{
            public $InclureJQuery = 1 ;
			public $CheminLogo = "images/logo.png" ;
			public $NiveauDev = "Beta" ;
			public $NomPublicateur = "" ;
			public $MargeCopyright = "8px" ;
			public $ScriptAccueil ;
			public $ActVoletNav ;
			public $BarreMenu1VoletNav ;
			public $BarreMenu2VoletNav ;
			public $LargeurVoletNav = "25%" ;
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
			public $NomClasseCSSTitre = "header_bar" ;
			public $NomClasseCSSBarreTitre = "icon" ;
			public $InclureFontAwesome = 1 ;
			public $InclureSousMenuAccueil = 1 ;
			public $AutoriserInscription = 1 ;
			public $SousMenuAccueil ;
			public $SousMenuConnexion ;
			public $SousMenuDeconnexion ;
			public $SousMenuListeMembres ;
			public $SousMenuAjoutMembre ;
			public $SousMenuListeProfils ;
			public $SousMenuAjoutProfil ;
			public $SousMenuListeRoles ;
			public $SousMenuAjoutRole ;
			public $SousMenuAPropos ;
			public $SousMenuConfidentialite ;
			public $SousMenuSupport ;
			public $BarreMembreCadrePrinc ;
			public $BarreMenuHaut1 ;
			public $BarreMenuBas1 ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPFrameser" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionFrameser" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionFrameser" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionFrameser" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseFrameser" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseFrameser" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreFrameser" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreMSFrameser" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreMSFrameser" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsFrameser" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreMSFrameser" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresMSFrameser" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilMSFrameser" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilMSFrameser" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilMSFrameser" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsMSFrameser" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleMSFrameser" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleMSFrameser" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleMSFrameser" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesMSFrameser" ;
			public $InclureIconeFa = 1 ;
			public $ClasseCSSIconeFaDefaut = "fa-file-o" ;
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
						if($this->EditMembershipPossible())
						{
							$this->SousMenuListeMembres = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptListeMembres) ;
							$this->SousMenuListeMembres->FenetreCible = $this->NomCadrePrinc ;
							$this->SousMenuAjoutMembre = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptAjoutMembre) ;
							$this->SousMenuAjoutMembre->FenetreCible = $this->NomCadrePrinc ;
							$this->SousMenuListeProfils = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptListeProfils) ;
							$this->SousMenuListeProfils->FenetreCible = $this->NomCadrePrinc ;
							$this->SousMenuAjoutProfil = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptAjoutProfil) ;
							$this->SousMenuAjoutProfil->FenetreCible = $this->NomCadrePrinc ;
							$this->SousMenuListeRoles = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptListeRoles) ;
							$this->SousMenuListeRoles->FenetreCible = $this->NomCadrePrinc ;
							$this->SousMenuAjoutRole = $this->BarreMenu1VoletNav->MenuRacine->InscritSousMenuScript($this->NomScriptAjoutRole) ;
							$this->SousMenuAjoutRole->FenetreCible = $this->NomCadrePrinc ;
						}
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
			public function ChargeBarreMenuHaut1Membership(& $menuHaut1)
			{
				$nomScriptAppele = $this->ValeurParamScriptAppele ;
				$menuRacine = & $menuHaut1->MenuRacine ;
				$menuRacine->InscritSousMenuUrl("Membres", "?appelleScript=".urlencode($this->NomScriptListeMembres)) ;
				$menuRacine->InscritSousMenuUrl("Cr&eacute;er membre", "?appelleScript=".urlencode($this->NomScriptAjoutMembre)) ;
				$menuRacine->InscritSousMenuUrl("Profils", "?appelleScript=".urlencode($this->NomScriptListeProfils)) ;
				$menuRacine->InscritSousMenuUrl("Cr&eacute;er profil", "?appelleScript=".urlencode($this->NomScriptAjoutProfil)) ;
				$menuRacine->InscritSousMenuUrl("R&ocirc;les", "?appelleScript=".urlencode($this->NomScriptListeRoles)) ;
				$menuRacine->InscritSousMenuUrl("Cr&eacute;er r&ocirc;le", "?appelleScript=".urlencode($this->NomScriptAjoutRole)) ;
			}
			public function ChargeBarreMenuHaut1NonConnecte(& $menuHaut1)
			{
				$nomScriptAppele = $this->ValeurParamScriptAppele ;
				$menuRacine = & $menuHaut1->MenuRacine ;
			}
			public function ChargeBarreMenuHaut1Connecte(& $menuHaut1)
			{
				$nomScriptAppele = $this->ValeurParamScriptAppele ;
				$menuRacine = & $menuHaut1->MenuRacine ;
			}
			protected function DetermineEnvironnement(& $script)
			{
				parent::DetermineEnvironnement($script) ;
				$this->DetermineCompsRendu($script) ;
			}
			protected function DetermineCompsRendu(& $script)
			{
				$this->ChargeMenusAuto1VoletNav() ;
				$this->ChargeAutresMenus1VoletNav() ;
				$this->ChargeMenusAuto2VoletNav() ;
				$this->ChargeAutresMenus2VoletNav() ;
				$this->DetermineBarreMenuHaut1() ;
				$this->DetermineBarreMenuBas1() ;
			}
			protected function CreeBarreMenuHaut1()
			{
				return new PvBarreLiensRelatifsFrameser() ;
			}
			protected function CreeBarreMenuBas1()
			{
				return new PvBarreLiensRelatifsFrameser() ;
			}
			protected function DetermineBarreMenuHaut1()
			{
				$this->BarreMenuHaut1 = $this->CreeBarreMenuHaut1() ;
				$this->InitBarreMenuHaut1() ;
				$this->BarreMenuHaut1->AdopteScript("menuHaut1", $this->ScriptAppele) ;
				$this->BarreMenuHaut1->ChargeConfig() ;
				if(method_exists($this->ScriptAppele, "ChargeBarreMenuHaut1"))
				{
					$this->ScriptAppele->ChargeBarreMenuHaut1($this->BarreMenuHaut1) ;
				}
			}
			protected function InitBarreMenuHaut1()
			{
			}
			protected function DetermineBarreMenuBas1()
			{
				$this->BarreMenuBas1 = $this->CreeBarreMenuBas1() ;
				$this->InitBarreMenuBas1() ;
				$this->BarreMenuBas1->AdopteScript("menuBas1", $this->ScriptAppele) ;
				$this->BarreMenuBas1->ChargeConfig() ;
				if(method_exists($this->ScriptAppele, "ChargeBarreMenuBas1"))
				{
					$this->ScriptAppele->ChargeBarreMenuBas1($this->BarreMenuBas1) ;
				}
			}
			protected function InitBarreMenuBas1()
			{
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
				$ctn .= parent::RenduEnteteCorpsDocument().PHP_EOL ;
				$ctn .= '<div class="iu-frameser-espace-travail">'.PHP_EOL ;
				$ctn .= $this->BarreMembreCadrePrinc->RenduDispositif().PHP_EOL ;
				$ctn .= '<br />' ;
				return $ctn ;
			}
			protected function RenduBarreMenuHaut1()
			{
				$ctn = '' ;
				$script = & $this->ScriptPourRendu ;
				$ctn .= '<table width="100%" cellpadding="4" cellspacing="2" class="ui-frameser-barre-titre '.$this->NomClasseCSSBarreTitre.'">'.PHP_EOL ;
				if($script->InclureRenduTitre && $script->Titre != "")
				{	
					$ctn .= '<tr>'.PHP_EOL ;
					$ctn .= '<td class="iu-frameser-entete-1 '.$this->NomClasseCSSTitre.'">'.PHP_EOL ;
					$ctnIcone = $script->RenduIcone() ;
					if($ctnIcone == '' && $this->InclureIconeFa)
					{
						$ctnIcone = '<span class="fa '.$script->ValAttrSuppl("icone-fa", $this->ClasseCSSIconeFaDefaut).'"></span>' ;
					}
					if($ctnIcone != '')
					{
						$ctn .= $ctnIcone."&nbsp;&nbsp;" ;
					}
					$ctn .= $script->Titre ;
					$ctn .= '</td>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$ctn .= '<tr>'.PHP_EOL ;
				$ctn .= '<td>'.PHP_EOL ;
				$ctn .= $this->BarreMenuHaut1->RenduDispositif().PHP_EOL ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>'.PHP_EOL ;
				$ctn .= '<br />'.PHP_EOL ;
				$script->InclureRenduTitre = 0 ;
				return $ctn ;
			}
			protected function RenduBarreMenuBas1()
			{
				$ctn = '' ;
				$ctnMenuBas = $this->BarreMenuBas1->RenduDispositif() ;
				if($ctnMenuBas != '')
				{
					$ctn .= '<p>'.$ctnMenuBas.'</p>' ;
				}
				return $ctn ;
			}
			protected function RenduContenuCorpsDocument()
			{
				$this->ScriptPourRendu->PrepareRendu() ;
				$ctn = '' ;
				$ctn .= $this->RenduBarreMenuHaut1().PHP_EOL ;
				$ctn .= $this->ScriptPourRendu->RenduDispositif().PHP_EOL ;
				$ctn .= $this->RenduBarreMenuBas1() ;
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= parent::RenduPiedCorpsDocument() ;
				return $ctn ;
			}
			protected function RenduDocumentGlobal()
			{
				$this->ScriptPourRendu->Titre = "" ;
				$this->ScriptPourRendu->TitreDocument = "" ;
				$ctn = "" ;
				$ctn .= '<!doctype html>'.PHP_EOL ;
				$ctn .= '<html>'.PHP_EOL ;
				$ctn .= $this->RenduEnteteDocument() ;
				$ctn .= '<body style="height:100%; margin:0px; padding:0px ; overflow:hidden;overflow-x:hidden;overflow-y:hidden;">'.PHP_EOL ;
				$ctn .= '<table width="100%" cellspacing=0 cellpadding="0" height="100%">
<tr>
<td valign="top" width="'.$this->LargeurVoletNav.'" height="100%" align="center">
<iframe src="'.$this->ActVoletNav->ObtientUrl().'" frameborder=0 bordercolor=0 scrolling="no" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100vh;" name="'.$this->NomCadreNav.'"></iframe>
</td>
<td valign="top" width="*" height="100%">
<iframe width="100%" src="'.$this->ScriptParDefaut->ObtientUrl().'" frameborder="0" name="'.$this->NomCadrePrinc.'" style="height:100vh;">
</td>
</tr>
</table>
</body>' ;
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
				if($this->NomPublicateur != '')
				{
					$ctn .= '<div>' ;
					if($this->ZoneParent->NiveauDev != '')
					{
						$ctn .= '<i>'.$this->ZoneParent->NiveauDev.'</i> - ' ;
					}
					$ctn .= $this->ZoneParent->NomPublicateur ;
					$ctn .= '</div>' ;
				}
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
			public $LibelleRecouvreMP = "Mot de passe oubli&eacute;" ;
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
						$ctn .= $this->LegendeNonConnecte ;
						$ctn .= ' <span class="sep">|</span> ' ;
					}
					if($this->ZoneParent->AutoriserInscription == 1)
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptInscription->ObtientUrl().'">'.$this->LibelleInscription.'</a>' ;
						$ctn .= ' &bull; ' ;
					}
					$ctn .= '<a href="'.$this->ZoneParent->ScriptRecouvreMP->ObtientUrl().'">'.$this->LibelleRecouvreMP.'</a>' ;
					$ctn .= ' &bull; ' ;
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
		
		class PvBarreLiensRelatifsFrameser extends PvListeMenuHoriz
		{
			public $InclureRenduMiniature = 0 ;
			public $SeparateurMenu = " | " ;
			public $CentrerMenu = 0 ;
			public $InclureSeparateurMenu = 1 ;
		}
		
		class PvScriptBaseFrameser extends PvScriptWebSimple
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		
		class PvScriptConnexionFrameser extends PvScriptConnexionWeb
		{
			public $NomScriptConnexionReussie = "" ;
			public $UrlConnexionReussie = "" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				if($this->TentativeConnexionValidee)
				{
					echo '<script language="javascript">
		var location = window.top.location ;
		window.top.location = "?" ;
</script>' ;
					exit ;
				}
			}
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1NonConnecte($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptInscriptionFrameser extends PvScriptInscriptionWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1NonConnecte($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptRecouvreMPFrameser extends PvScriptRecouvreMPWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1NonConnecte($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		
		class PvScriptModifPrefsFrameser extends PvScriptModifPrefsWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Connecte($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptChangeMotPasseFrameser extends PvScriptChangeMotPasseWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Connecte($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptDoitChangerMotPasseFrameser extends PvScriptDoitChangerMotPasseWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Connecte($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptDeconnexionFrameser extends PvScriptDeconnexionWeb
		{
			public $NomScriptDeconnexionReussie = "" ;
			public $UrlDeconnexionReussie = "" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				echo '<script language="javascript">
		var location = window.top.location ;
		window.top.location = "?" ;
</script>' ;
				exit ;
			}
		}
		
		class PvScriptListeMembresMSFrameser extends PvScriptListeMembresMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptAjoutMembreMSFrameser extends PvScriptAjoutMembreMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptModifMembreMSFrameser extends PvScriptModifMembreMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptSupprMembreMSFrameser extends PvScriptSupprMembreMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		
		class PvScriptListeProfilsMSFrameser extends PvScriptListeProfilsMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptAjoutProfilMSFrameser extends PvScriptAjoutProfilMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptModifProfilMSFrameser extends PvScriptModifProfilMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptSupprProfilMSFrameser extends PvScriptSupprProfilMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		
		class PvScriptListeRolesMSFrameser extends PvScriptListeRolesMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptAjoutRoleMSFrameser extends PvScriptAjoutRoleMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptModifRoleMSFrameser extends PvScriptModifRoleMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
		class PvScriptSupprRoleMSFrameser extends PvScriptSupprRoleMSWeb
		{
			public function ChargeBarreMenuHaut1(& $menuHaut1)
			{
				$this->ZoneParent->ChargeBarreMenuHaut1Membership($menuHaut1) ;
			}
			public function ChargeBarreMenuBas1(& $menuBas1)
			{
			}
		}
	}
	
?>