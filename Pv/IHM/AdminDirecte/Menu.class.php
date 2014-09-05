<?php
	
	if(! defined('PV_MENU_ADMIN_DIRECTE'))
	{
		if(! defined('PV_MENU_SIMPLE'))
		{
			include dirname(__FILE__)."/../Simple/ComposantIU.class.php" ;
		}
		if(! defined('PV_MENU_IHM'))
		{
			include dirname(__FILE__)."/../Menu.class.php" ;
		}
		define('PV_MENU_ADMIN_DIRECTE', 1) ;
		
		class PvBarreMenuAdminDirecte extends PvBarreMenuWebBase
		{
			public $NomClasseMenuRacine = "PvMenuAdminDirecteRacine" ;
			public function InscritSousMenuRedirScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu("PvMenuRedirectScript", $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
		}
		
		class PvMenuAdminDirecteRacine extends PvMenuIHMRacine
		{
			public $NomClasseSousMenuScript = "PvMenuAdminDirecteScript" ;
			public $NomClasseSousMenuFige = "PvMenuAdminDirecteFige" ;
			public function InscritSousMenuRedirScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu("PvMenuRedirectScript", $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
		}
		class PvMenuAdminDirecteScript extends PvMenuRedirectScript
		{
			public $NomClasseSousMenuScript = "PvMenuAdminDirecteScript" ;
			public $NomClasseSousMenuFenetre = "PvMenuAdminDirecteFenetreScript" ;
			public $NomClasseSousMenuFige = "PvMenuAdminDirecteFige" ;
			public function InscritSousMenuRedirScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu("PvMenuRedirectScript", $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
			public function & InscritSousMenuFenetreUrl($titre, $url)
			{
				$nom = 'SousMenuUrl'.count($this->SousMenus) ;
				$menu = $this->InscritSousMenu($this->NomClasseSousMenuFenetre, $nom) ;
				$menu->Url = $url ;
				$menu->Titre = $titre ;
				return $menu ;
			}
			public function & InscritSousMenuFenetreScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu($this->NomClasseSousMenuFenetre, $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
			public function ObtientUrl()
			{
				$script = $this->ObtientScript() ;
				if($script != null)
				{
					$url = remove_url_params(
						get_current_url()
					)."?".urlencode($this->ZoneParent->NomParamScriptAppele)."=".urlencode($script->NomElementZone) ;
					$url = update_url_params($url, $this->ParamsScript) ;
					$nomScript = svc_json_encode($script->NomElementZone) ;
					$cheminIcone = $this->ObtientCheminIcone() ;
					if($cheminIcone == '' && $this->ComposantSupport != null)
						$cheminIcone = $this->ComposantSupport->CheminIconeParDefaut ;
					$cheminIcone = svc_json_encode($cheminIcone) ;
					$titre = svc_json_encode($this->ObtientTitre()) ;
					return $this->ObtientLienJs($nomScript, $cheminIcone, $titre, $url) ;
				}
				return "" ;
			}
			public function ObtientLienJs($nomScript, $cheminIcone, $titre, $url)
			{
				return htmlentities('javascript:ouvreOngletCadre('.$nomScript.', '.$cheminIcone.', '.$titre.', \''.$url.'\') ;') ;
			}
		}
		class PvMenuAdminDirecteFige extends PvMenuFige
		{
			public $NomClasseSousMenuScript = "PvMenuAdminDirecteScript" ;
			public $NomClasseSousMenuFenetre = "PvMenuAdminDirecteFenetreScript" ;
			public $NomClasseSousMenuFige = "PvMenuAdminDirecteFige" ;
			public function InscritSousMenuRedirScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu("PvMenuRedirectScript", $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
			public function & InscritSousMenuFenetreUrl($titre, $url)
			{
				$nom = 'SousMenuUrl'.count($this->SousMenus) ;
				$menu = $this->InscritSousMenu($this->NomClasseSousMenuFenetre, $nom) ;
				$menu->Url = $url ;
				$menu->Titre = $titre ;
				return $menu ;
			}
			public function & InscritSousMenuFenetreScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu($this->NomClasseSousMenuFenetre, $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
		}
		class PvMenuAdminDirecteOngletScript extends PvMenuAdminDirecteScript
		{
		}
		class PvMenuAdminDirecteFenetreScript extends PvMenuAdminDirecteScript
		{
			public $Modal = 1 ;
			public $Largeur = 0 ;
			public $Hauteur = 0 ;
			public $BoutonFermer = 1 ;
			public $OptionsOnglet = array() ;
			protected function ExtraitOptionsOuverture()
			{
				$options = $this->OptionsOnglet ;
				$options["Modal"] = ($this->Modal) ? true : false ;
				if($this->Largeur > 0)
				{
					$options["Largeur"] = $this->Largeur ;
				}
				if($this->Hauteur > 0)
				{
					$options["Hauteur"] = $this->Hauteur ;
				}
				$options["BoutonFermer"] = ($this->BoutonFermer) ? true : false ;
				return $options ;
			}
			public function ObtientLienJs($nomScript, $cheminIcone, $titre, $url)
			{
				return htmlentities('javascript:ouvreFenetreCadre('.$nomScript.', '.$cheminIcone.', '.$titre.', \''.$url.'\', '.svc_json_encode($this->ExtraitOptionsOuverture()).') ;') ;
			}
		}
	}
	
?>