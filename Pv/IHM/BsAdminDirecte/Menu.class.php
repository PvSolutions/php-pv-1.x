<?php
	
	if(! defined('PV_MENU_BS_ADMIN_DIRECTE'))
	{
		if(! defined('PV_MENU_SIMPLE'))
		{
			include dirname(__FILE__)."/../Simple/ComposantIU.class.php" ;
		}
		if(! defined('PV_MENU_IHM'))
		{
			include dirname(__FILE__)."/../Menu.class.php" ;
		}
		define('PV_MENU_BS_ADMIN_DIRECTE', 1) ;
		
		class PvBarreMenuBsAdminDirecte extends PvBarreMenuWebBase
		{
			public $ProfondeurMenu ;
			public $NomClasseCSSMenuRacine = "MenuRacine sf-menu nav navbar-nav" ;
			public $NomClasseMenuRacine = "PvMenuBsAdminDirecteRacine" ;
			public function InscritSousMenuRedirScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu("PvMenuRedirectScript", $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
			protected function RenduMenuRacine(& $menu)
			{
				$ctn = '' ;
				$ctn .= '<nav class="navbar navbar-default">
<div class="container-fluid">
<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="#">'.$this->ZoneParent->Titre.'</a>
</div>
<!-- Collect the nav links, forms, and other content for toggling -->
<div class="collapse navbar-collapse" id="bs-'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				$this->ProfondeurMenu = 0 ;
				$ctn .= parent::RenduMenuRacine($menu).PHP_EOL ;
				$this->ProfondeurMenu = 0 ;
				$ctn .= '</div>
</div>
</nav>' ;
				return $ctn ;
			}
			protected function RenduMenu($menu)
			{
				$ctn = '' ;
				// print count($menu->SousMenus) ;
				if(! $menu->EstAffichable())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				$this->ProfondeurMenu++ ;
				$menus = $menu->SousMenusAffichables() ;
				if(! $menu->EstMenuRacine())
				{
					$ctn .= '<li'.((count($menus) > 0) ? ' class="dropdown"' : '').'>'.PHP_EOL ;
					// echo get_class($menu) ;
					if(count($menus) > 0)
					{
						$ctn .= $this->RenduTagOuvrLien2($menu).PHP_EOL ;
						$ctn .= $this->RenduIconeMenu($menu).PHP_EOL ;
						$ctn .= $this->RenduTitreMenu($menu).PHP_EOL ;
						$ctn .= $this->RenduTagFermLien2($menu).PHP_EOL ;
					}
					else
					{
						$ctn .= $this->RenduTagOuvrLien($menu).PHP_EOL ;
						$ctn .= $this->RenduIconeMenu($menu).PHP_EOL ;
						$ctn .= $this->RenduTitreMenu($menu).PHP_EOL ;
						$ctn .= $this->RenduTagFermLien($menu).PHP_EOL ;
					}
				}
				if(count($menus) > 0)
				{
					$ctn .= '<ul' ;
					if($menu->EstMenuRacine())
					{
						$ctn .= ' id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSSMenuRacine.'"' ;
					}
					else
					{
						$ctn .= ' class="'.(($this->ProfondeurMenu > 2) ? 'dropdown-submenu' : 'dropdown-menu').'"' ;
					}
					$ctn .= '>'.PHP_EOL ;
					$nomSousMenus = array_keys($menus) ;
					foreach($nomSousMenus as $i => $nomSousMenu)
					{
						$sousMenu = $menus[$nomSousMenu] ;
						$ctn .= $this->RenduMenu($sousMenu).PHP_EOL ;
					}
					$ctn .= '</ul>'.PHP_EOL ;
				}
				if(! $menu->EstMenuRacine())
				{
					$ctn .= '</li>' ;
				}
				$this->ProfondeurMenu-- ;
				$menu->ComposantSupport = null ;
				return $ctn ;
			}
			protected function RenduTagOuvrLien2(& $menu)
			{
				$ctn = '' ;
				$ctn .= '<a href="'.$menu->ObtientUrl().'"' ;
				$ctn .= ' class="dropdown-toggle '.$this->NomClasseCSS.((! $menu->EstSelectionne) ? '' : ' '.$this->NomClasseCSSSelect).'"' ;
				if($menu->Tips != '')
				{
					$ctn .= ' title="'.htmlspecialchars($menu->Tips).'"' ;
				}
				if($menu->FenetreCible != '')
				{
					$ctn .= ' target="'.htmlspecialchars($menu->FenetreCible).'"' ;
				}
				$ctn .= '  data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"' ;
				$ctn .= '>' ;
				return $ctn ;
			}
			protected function RenduTagFermLien2(& $menu)
			{
				$ctn = '' ;
				$ctn .= ' <span class="caret"></span>' ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
		}
		
		class PvMenuBsAdminDirecteRacine extends PvMenuIHMRacine
		{
			public $NomClasseSousMenuScript = "PvMenuBsAdminDirecteScript" ;
			public $NomClasseSousMenuFige = "PvMenuBsAdminDirecteFige" ;
			public function InscritSousMenuRedirScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu("PvMenuRedirectScript", $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
		}
		class PvMenuBsAdminDirecteScript extends PvMenuRedirectScript
		{
			public $NomClasseSousMenuScript = "PvMenuBsAdminDirecteScript" ;
			public $NomClasseSousMenuFenetre = "PvMenuBsAdminDirecteFenetreScript" ;
			public $NomClasseSousMenuFige = "PvMenuBsAdminDirecteFige" ;
			public $TitreOnglet = "" ;
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
					$titre = svc_json_encode(($this->TitreOnglet != '') ? $this->TitreOnglet : $this->ObtientTitre()) ;
					return $this->ObtientLienJs($nomScript, $cheminIcone, $titre, $url) ;
				}
				return "" ;
			}
			public function ObtientLienJs($nomScript, $cheminIcone, $titre, $url)
			{
				return htmlentities('javascript:ouvreOngletCadre('.$nomScript.', '.$cheminIcone.', '.$titre.', \''.$url.'\') ;') ;
			}
		}
		
		class PvMenuBsAdminDirecteFige extends PvMenuFige
		{
			public $NomClasseSousMenuScript = "PvMenuBsAdminDirecteScript" ;
			public $NomClasseSousMenuFenetre = "PvMenuBsAdminDirecteFenetreScript" ;
			public $NomClasseSousMenuFige = "PvMenuBsAdminDirecteFige" ;
			public function & InscritSousMenuFermeOnglActif()
			{
				$menu = $this->InscritSousMenu("PvMenuBsAdminDirecteFermeOnglActif", "SousMenu_".count($this->Menus)) ;
				return $menu ;
			}
			public function & InscritSousMenuFermeTousOngls()
			{
				$menu = $this->InscritSousMenu("PvMenuBsAdminDirecteFermeTousOngls", "SousMenu_".count($this->Menus)) ;
				return $menu ;
			}
			public function & InscritSousMenuFermeAutresOngls()
			{
				$menu = $this->InscritSousMenu("PvMenuBsAdminDirecteFermeAutresOngls", "SousMenu_".count($this->Menus)) ;
				return $menu ;
			}
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
		class PvMenuBsAdminDirecteFermeOnglActif extends PvMenuBsAdminDirecteFige
		{
			public function ObtientUrl()
			{
				return "javascript:fermeOngletActif() ;" ;
			}
		}
		class PvMenuBsAdminDirecteFermeTousOngls extends PvMenuBsAdminDirecteFige
		{
			public function ObtientUrl()
			{
				return "javascript:fermeTousLesOnglets() ;" ;
			}
		}
		class PvMenuBsAdminDirecteFermeAutresOngls extends PvMenuBsAdminDirecteFige
		{
			public function ObtientUrl()
			{
				return "javascript:fermeOngletsNonSelection() ;" ;
			}
		}
		class PvMenuBsAdminDirecteOngletScript extends PvMenuBsAdminDirecteScript
		{
		}
		class PvMenuBsAdminDirecteFenetreScript extends PvMenuBsAdminDirecteScript
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