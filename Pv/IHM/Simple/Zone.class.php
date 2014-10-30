<?php
	
	if(! defined('PV_ZONE_SIMPLE_IHM'))
	{
		if(! defined('PV_ZONE_IHM'))
		{
			include dirname(__FILE__)."/../Zone.class.php" ;
		}
		if(! defined('PV_SCRIPT_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		if(! defined('PV_HABILLAGE_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/Habillage.class.php" ;
		}
		define('PV_ZONE_SIMPLE_IHM', 1) ;
		
		class PvZoneWebSimple extends PvZoneWeb
		{
			public $TypeDocument ;
			public $DefinitionTypeDocument ;
			public $LangueDocument = "fr" ;
			public $EncodageDocument = "iso-8859-1" ;
			public $TitreDocument ;
			public $MotsCleMeta ;
			public $DescriptionMeta ;
			public $ViewportMeta ;
			public $AuteurMeta ;
			public $RenduExtraMeta ;
			public $ModeCache ;
			public $ScriptPourRendu ;
			public $InclureCtnJsEntete = 1 ;
			public $RenduExtraHead = '' ;
			public $InclureJQuery = 0 ;
			public $CheminJQuery = "js/jquery.min.js" ;
			public $InclureJQueryMigrate = 1 ;
			public $CheminJQueryMigrate = "js/jquery-migrate.min.js" ;
			public $InclureJQueryUi = 0 ;
			public $CheminJsJQueryUi = "js/jquery-ui.min.js" ;
			public $CheminCSSJQueryUi = "css/jquery-ui.css" ;
			public $ContenusCSS = array() ;
			public $ContenusJs = array() ;
			public $CheminIconeScript = "" ;
			public $InclureRenduTitre = 1 ;
			public $InclureRenduIcone = 1 ;
			public $DetectIconeCorresp = 0 ;
			public $CheminDossierIconeCorresp = "images/icones" ;
			public $ExtIconeCorresp = "png" ;
			public $InclureRenduChemin = 1 ;
			public $InclureRenduDescription = 1 ;
			public $ActionsAvantRendu = array() ;
			public $ActionsApresRendu = array() ;
			public $NomParamActionAppelee = "appelleAction" ;
			public $ValeurParamActionAppelee = false ;
			public $ActionsAppelees = array() ;
			public $AnnulerRendu = 0 ;
			public $RenduEnCours = 0 ;
			public $Habillage = null ;
			public $InclureScriptsMembership = 1 ;
			public $NomClasseHabillage = "PvHabillageSimpleBase" ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPWeb" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionWeb" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionWeb" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionWeb" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseWeb" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseWeb" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreWeb" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreMSWeb" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreMSWeb" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsWeb" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreMSWeb" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresMSWeb" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilMSWeb" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilMSWeb" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilMSWeb" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsMSWeb" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleMSWeb" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleMSWeb" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleMSWeb" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesMSWeb" ;
			public function ObtientUrlAction($nomAction)
			{
				return $this->ObtientUrlActionAvantRendu($nomAction) ;
			}
			public function ObtientUrlActionAvantRendu($nomAction)
			{
				return $this->ObtientUrlActionDansListe($nomAction, $this->ActionsAvantRendu) ;
			}
			public function ObtientUrlActionApresRendu($nomAction)
			{
				return $this->ObtientUrlActionDansListe($nomAction, $this->ActionsApresRendu) ;
			}
			protected function ObtientUrlActionDansListe($nomAction, & $actions)
			{
				$url = false ;
				if(isset($actions[$nomAction]))
				{
					$url = $actions[$nomAction]->ObtientUrl() ;
				}
				return $url ;
			}
			protected function ChargeScripts()
			{
				$this->ChargeActionsAvantRendu() ;
				$this->ChargeActionsApresRendu() ;
				parent::ChargeScripts() ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				if(class_exists($this->NomClasseHabillage))
				{
					$nomClasse = $this->NomClasseHabillage ;
					$this->Habillage = new $nomClasse() ;
				}
			}
			protected function ChargeActionsAvantRendu()
			{
			}
			protected function ChargeActionsApresRendu()
			{
			}
			protected function DetecteActionAppelee()
			{
				$this->ValeurParamActionAppelee = false ;
				if(isset($_GET[$this->NomParamActionAppelee]))
					$this->ValeurParamActionAppelee = $_GET[$this->NomParamActionAppelee] ;
			}
			protected function ExecuteActionAppelee(& $actions)
			{
				return $this->ExecuteAction($actions, $this->ValeurParamActionAppelee) ;
			}
			protected function ExecuteAction(& $actions, $valeurAction)
			{
				$nomActions = array_keys($actions) ;
				foreach($nomActions as $i => $nomAction)
				{
					$action = & $actions[$nomAction] ;
					if($action->Accepte($valeurAction))
					{
						$this->ActionsAppelees[] = & $action ;
						$action->Execute() ;
					}
				}
			}
			protected function & CreeAction($nomClasseAction)
			{
				if(! class_exists($nomClasseAction))
				{
					die("La classe $nomClasseAction n'existe pas !!!") ;
				}
				$action = new $nomClasseAction() ;
				return $action ;
			}
			public function InscritActionAvantRendu($nomAction, & $action)
			{
				$this->ActionsAvantRendu[$nomAction] = & $action ;
				$action->AdopteZone($nomAction, $this) ;
			}
			public function InscritActionApresRendu($nomAction, & $action)
			{
				$this->ActionsApresRendu[$nomAction] = & $action ;
				$action->AdopteZone($nomAction, $this) ;
			}
			public function CreeScript($nomClasse, $titre='')
			{
				if(! class_exists($nomClasse))
					return new PvNul() ;
				$script = new $nomClasse() ;
				if($titre == '')
				{
					$titre = ucfirst($nomClasse) ;
				}
				$script->Titre = $titre ;
				$script->TitreDocument = $titre ;
				return $script ;
			}
			public function RenduDocument()
			{
				$ctn = '' ;
				$ctn .= $this->RenduDefinitionTypeDocument().PHP_EOL ;
				$ctn .= '<html lang="'.$this->LangueDocument.'">'.PHP_EOL ;
				$ctn .= $this->RenduEnteteDocument().PHP_EOL ;
				if($this->ScriptPourRendu->UtiliserCorpsDocZone)
				{
					$ctn .= $this->RenduCorpsDocument().PHP_EOL ;
				}
				else
				{
					$ctn .= $this->RenduDebutCorpsDocument().PHP_EOL ;
					$ctn .= $this->RenduContenuCorpsDocument().PHP_EOL ;
					$ctn .= $this->RenduDebutCorpsDocument().PHP_EOL ;
				}
				$ctn .= $this->RenduPiedDocument().PHP_EOL ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
			protected function RenduDebutCorpsDocument()
			{
				return '<body>' ;
			}
			protected function RenduFinCorpsDocument()
			{
				return '</body>' ;
			}
			protected function RenduDefinitionTypeDocument()
			{
				return '<!DOCTYPE html>' ;
			}
			public function InclutLibrairiesExternes()
			{
				if($this->InclureJQueryUi)
				{
					$ctnJs = new PvLienFichierJs() ;
					$ctnJs->Src = $this->CheminJsJQueryUi ;
					array_splice($this->ContenusJs, 0, 0, array($ctnJs)) ;
					$this->InscritLienCSS($this->CheminCSSJQueryUi) ;
				}
				if($this->InclureJQueryUi || $this->InclureJQuery)
				{
					$ctnJs = new PvLienFichierJs() ;
					$ctnJs->Src = $this->CheminJQuery ;
					$lstCtnJs = array($ctnJs) ;
					if($this->InclureJQueryMigrate)
					{
						$ctnJs = new PvLienFichierJs() ;
						$ctnJs->Src = $this->CheminJQueryMigrate ;
						$lstCtnJs[] = $ctnJs ;
					}
					array_splice($this->ContenusJs, 0, 0, $lstCtnJs) ;
				}
			}
			protected function RenduEnteteDocument()
			{
				$this->InclutLibrairiesExternes() ;
				$ctn = '' ;
				$ctn .= '<head>'.PHP_EOL ;
				if($this->EncodageDocument != '')
					$ctn .= '<meta charset="'.$this->EncodageDocument.'" />'.PHP_EOL ;
				$titreDocument = $this->ScriptPourRendu->ObtientTitreDocument() ;
				$ctn .= '<title>'.(($titreDocument != "") ? $titreDocument : $this->TitreDocument).'</title>'.PHP_EOL ;
				$ctn .= '<meta name="keywords" value="'.htmlentities(($this->ScriptPourRendu->MotsCleMeta != "") ? $this->ScriptPourRendu->MotsCleMeta : $this->MotsCleMeta).'" />'.PHP_EOL ;
				$viewport = ($this->ScriptPourRendu->ViewportMeta != "") ? $this->ScriptPourRendu->ViewportMeta : $this->ViewportMeta ;
				if($viewport != '')
				{
					$ctn .= '<meta name="viewport" value="'.htmlentities($viewport).'" />'.PHP_EOL ;
				}
				$auteur = ($this->ScriptPourRendu->AuteurMeta != "") ? $this->ScriptPourRendu->AuteurMeta : $this->AuteurMeta ;
				if($auteur != '')
				{
					$ctn .= '<meta name="author" value="'.htmlentities($auteur).'" />'.PHP_EOL ;
				}
				$ctn .= '<meta name="description" value="'.htmlentities(($this->ScriptPourRendu->DescriptionMeta != "") ? $this->ScriptPourRendu->DescriptionMeta : $this->DescriptionMeta).'" />'.PHP_EOL ;
				for($i=0; $i<count($this->ContenusCSS); $i++)
				{
					$ctnCSS = $this->ContenusCSS[$i] ;
					$ctn .= $ctnCSS->RenduDispositif().PHP_EOL ;
				}
				if($this->InclureCtnJsEntete)
				{
					$ctn .= $this->RenduCtnJs() ;
				}
				$ctn .= $this->RenduExtraHead ;
				$ctn .= '</head>' ;
				return $ctn ;
			}
			protected function RenduCtnJs()
			{
				$ctn = '' ;
				for($i=0; $i<count($this->ContenusJs); $i++)
				{
					$ctnJs = $this->ContenusJs[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduPiedDocument()
			{
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '<body>' ;
				return $ctn ;
			}
			protected function RenduContenuCorpsDocument()
			{
				$this->ScriptPourRendu->PrepareRendu() ;
				return $this->ScriptPourRendu->RenduDispositif() ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				if($this->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduCtnJs() ;
				}
				$ctn .= '</body>' ;
				return $ctn ;
			}
			protected function RenduCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= $this->RenduEnteteCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduContenuCorpsDocument().PHP_EOL ;
				$ctn .= $this->RenduPiedCorpsDocument() ;
				return $ctn ;
			}
			public function ExecuteScript(& $script)
			{
				$this->RapporteRequeteEnvoyee() ;
				if($script->EstBienRefere() == 0)
				{
					$this->ExecuteScriptMalRefere($script) ;
					return ;
				}
				$this->VerifieValiditeMotPasse($script) ;
				if($script->EstAccessible() == 0)
				{
					// print_r(get_class($this->Membership->MemberLogged)) ;
					// print_r(get_class($script)) ;
					$this->ExecuteScriptInaccessible($script) ;
					return ;
				}
				// print_r(array_keys($this->ActionsAvantRendu)) ;
				$this->DetermineEnvironnement($script) ;
				$this->ExecuteRequeteSoumise($script) ;
				$this->DetecteActionAppelee() ;
				// $script->PrepareRendu() ;
				if($this->ValeurParamActionAppelee !== false)
				{
					$this->ExecuteActionAppelee($this->ActionsAvantRendu) ;
				}
				if($this->AnnulerRendu)
				{
					return ;
				}
				$this->ScriptPourRendu = $script ;
				$this->RenduEnCours = 1 ;
				$ctn = $this->RenduDocument() ;
				$this->RenduEnCours = 0 ;
				$this->ScriptPourRendu = null ;
				if($this->ValeurParamActionAppelee !== false)
				{
					$this->ExecuteActionAppelee($this->ActionsApresRendu) ;
				}
				echo $ctn ;
			}
			protected function ExecuteRequeteSoumise(& $script)
			{
			}
			// Incrire un fichier CSS
			public function InscritContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritContenuJs($contenu)
			{
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritContenuJsCmpIE($contenu, $versionMin=9)
			{
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJs($src)
			{
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJsCmpIE($src, $versionMin=9)
			{
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function RenduLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuJsInclus($contenu)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduContenuJsCmpIEInclus($contenu, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsInclus($src)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsCmpIEInclus($src, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
		}
	}
	
?>