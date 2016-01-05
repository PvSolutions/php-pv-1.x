<?php
	
	if(! defined('PV_MENU_IHM'))
	{
		if(! defined('PV_NOYAU_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_IU'))
		{
			include dirname(__FILE__)."/ComposantIU.class.php" ;
		}
		define('PV_MENU_IHM', 1) ;
		
		class PvMenuIHMBase extends PvObjet
		{
			protected $EstRacine = 0 ;
			public $BarreMenu ;
			public $InclureRenduTitre = 1 ;
			public $InclureRenduIcone = 1 ;
			public $InclureRenduMiniature = 1 ;
			public $EstVisible = 1 ;
			public $FenetreCible = "" ;
			public $Titre = "" ;
			public $Chemin = "" ;
			public $Url = "" ;
			public $CheminIcone = "" ;
			public $CheminMiniature = "" ;
			public $NomClasseCSS = '' ;
			public $Tips = "" ;
			public $Description = "" ;
			public $SousMenus = array() ;
			public $MenuParent = null ;
			public $NomElementMenu = null ;
			public $ZoneParent = null ;
			public $NomElementZone = "" ;
			public $ApplicationParent = null ;
			public $EstSelectionne = 0 ;
			public $StatutSelectionDetecte = 0 ;
			public $ComposantSupport = null ;
			public $NomClasseSousMenuFige = "PvMenuFige" ;
			public $NomClasseSousMenuUrl = "PvMenuRedirectHttp" ;
			public $NomClasseSousMenuScript = "PvMenuRedirectScript" ;
			public $Privileges = array() ;
			public $ValsConfigSpec = array() ;
			public function EstAccessible()
			{
				if($this->EstNul($this->ZoneParent) || count($this->Privileges) == 0)
				{
					return 1 ;
				}
				return $this->ZoneParent->PossedePrivileges($this->Privileges) ;
			}
			public function EstMenuRacine()
			{
				return $this->EstRacine ;
			}
			public function ObtientDefinitions()
			{
				return "" ;
			}
			public function ObtientTitre()
			{
				$valeur = "" ;
				if($this->Titre != "")
				{
					$valeur = $this->Titre ;
				}
				return $valeur ;
			}
			public function ObtientCheminIcone()
			{
				$valeur = "" ;
				if($this->CheminIcone != "")
				{
					$valeur = $this->CheminIcone ;
				}
				return $valeur ;
			}
			public function ObtientCheminMiniature()
			{
				$valeur = "" ;
				if($this->CheminMiniature != "")
				{
					$valeur = $this->CheminMiniature ;
				}
				return $valeur ;
			}
			public function ObtientChemin()
			{
				$valeur = "" ;
				if($this->Chemin != "")
				{
					$valeur = $this->Chemin ;
				}
				return $valeur ;
			}
			public function ObtientUrl()
			{
				$valeur = "" ;
				if($this->Url != "")
				{
					$valeur = $this->Url ;
				}
				return $valeur ;
			}
			public function AdopteZone($nom, & $zone)
			{
				$this->NomElementZone = $nom ;
				$this->ZoneParent = & $zone ;
			}
			public function AdopteMenu($nom, & $menu)
			{
				$this->NomElementMenu = $nom ;
				$this->MenuParent = & $menu ;
				$this->AdopteZone($nom, $menu->ZoneParent) ;
			}
			public function CreeSousMenu($nomClasseSousMenu)
			{
				$menu = $this->ValeurNulle() ;
				if(! class_exists($nomClasseSousMenu))
				{
					return $menu ;
				}
				$menu = new $nomClasseSousMenu() ;
				return $menu ;
			}
			protected function ObtientNomNouvSousMenu($nom)
			{
				$nom = ($nom == '') ? uniqid("SousMenu_") : $nom ;
				return $nom ;
			}
			public function & InscritSousMenu($nomClasseSousMenu, $nom)
			{
				$menu = $this->CreeSousMenu($nomClasseSousMenu) ;
				if($this->EstNul($menu))
				{
					return $menu ;
				}
				$this->ValideInscriptionSousMenu($nom, $menu) ;
				return $menu ;
			}
			protected function ValideInscriptionSousMenu($nom, & $menu)
			{
				$nom = $this->ObtientNomNouvSousMenu($nom) ;
				$menu->AdopteMenu($nom, $this) ;
				$this->SousMenus[$nom] = & $menu ;
			}
			public function DeclareSousMenu($nomClasseSousMenu, $nom)
			{
				$menu = $this->InscritSousMenu($nomClasseSousMenu) ;
				if($this->EstNul($menu))
				{
					return $menu ;
				}
				$nomPropriete = 'SousMenu'.ucfirst($nom) ;
				$this->$nomPropriete = & $menu ;
			}
			public function DetecteStatutSelection()
			{
				if($this->StatutSelectionDetecte)
					return ;
				$this->EstSelectionne = $this->ObtientStatutSelection() ;
				if(! $this->EstSelectionne)
				{
					$nomSousMenus = array_keys($this->SousMenus) ;
					foreach($nomSousMenus as $i => $nom)
					{
						$this->SousMenus[$nom]->DetecteStatutSelection() ;
						$this->EstSelectionne = $this->SousMenus[$nom]->EstSelectionne ;
						if($this->EstSelectionne)
						{
							break ;
						}
					}
				}
			}
			protected function ObtientStatutSelection()
			{
				$selectionne = 0 ;
				return $selectionne ;
			}
			public function & InscritSousMenuFige($nom, $titre="")
			{
				$nom = $this->ObtientNomNouvSousMenu($nom) ;
				$menu = $this->InscritSousMenu($this->NomClasseSousMenuFige, $nom) ;
				$menu->Titre = $titre ;
				return $menu ;
			}
			public function & InscritSousMenuUrl($titre, $url)
			{
				$nom = 'SousMenuUrl'.count($this->SousMenus) ;
				$menu = $this->InscritSousMenu($this->NomClasseSousMenuUrl, $nom) ;
				$menu->Url = $url ;
				$menu->Titre = $titre ;
				return $menu ;
			}
			public function & InscritSousMenuScript($nomScript)
			{
				$nom = $nomScript ;
				$menu = $this->InscritSousMenu($this->NomClasseSousMenuScript, $nom) ;
				$menu->NomScript = $nomScript ;
				return $menu ;
			}
			public function DefinitValeurConfigSpec($nom, $val)
			{
				$this->ValsConfigSpec[$nom] = $val ;
			}
			public function DefinitValConfigSpec($nom, $val)
			{
				$this->DefinitValeurConfigSpec($nom, $val) ;
			}
			public function DefinitValCfgSpec($nom, $val)
			{
				$this->DefinitValeurConfigSpec($nom, $val) ;
			}
			public function ObtientValeurConfigSpec($nom, $valParDefaut=false)
			{
				return (isset($this->ValsConfigSpec[$nom])) ? $this->ValsConfigSpec[$nom] : $valParDefaut ;
			}
			public function ObtientValConfigSpec($nom, $valParDefaut)
			{
				return $this->ObtientValeurConfigSpec($nom, $valParDefaut) ;
			}
			public function ObtientValCfgSpec($nom, $valParDefaut)
			{
				return $this->ObtientValeurConfigSpec($nom, $valParDefaut) ;
			}
		}
		
		class PvMenuIHMRacine extends PvMenuIHMBase
		{
			protected $EstRacine = 1 ;
		}
		
		class PvMenuFige extends PvMenuIHMBase
		{
			public function ObtientUrl()
			{
				return "javascript:;" ;
			}
		}
		class PvMenuRedirectHttp extends PvMenuIHMBase
		{
			public function DetecteStatutSelection()
			{
				$selectionne = 0 ;
				if($this->Url != '')
					return 0 ;
				$partiesEnCours = parse_url(get_current_url()) ;
				$url = make_abs_url($this->Url, get_current_url_dir()) ;
				$partiesDemandees = parse_url($url) ;
				return ($partiesEnCours == $partiesDemandees) ? 1 : 0 ;
			}
		}
		class PvMenuRedirectScript extends PvMenuIHMBase
		{
			public $NomScript = "" ;
			public $ParamsScript = array() ;
			public $NomScriptsSelect = array() ;
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				if(! $ok)
					return 0 ;
				$script = $this->ObtientScript() ;
				$ok = 0 ;
				if($script != null)
				{
					$ok = $script->EstAccessible() ;
					/*
					print $script->NomElementZone.' : '.$ok ;
					print_r($script->Privileges) ;
					print "<br>" ;
					*/
				}
				return $ok ;
			}
			protected function ObtientScript()
			{
				$script = null ;
				if($this->EstNul($this->ZoneParent))
				{
					return $script ;
				}
				if(isset($this->ZoneParent->Scripts[$this->NomScript]))
				{
					$script = $this->ZoneParent->Scripts[$this->NomScript] ;
				}
				return $script ;
			}
			public function ObtientTitre()
			{
				$valeur = parent::ObtientTitre() ;
				if($valeur == "")
				{
					$script = $this->ObtientScript() ;
					if($script != null)
					{
						$valeur = $script->Titre ;
					}
				}
				return $valeur ;
			}
			public function ObtientCheminIcone()
			{
				$valeur = parent::ObtientCheminIcone() ;
				if($valeur == "")
				{
					$script = $this->ObtientScript() ;
					if($script != null)
					{
						$valeur = $script->CheminIcone ;
					}
				}
				return $valeur ;
			}
			public function ObtientUrl()
			{
				$valeur = parent::ObtientUrl() ;
				if($valeur == "")
				{
					$script = $this->ObtientScript() ;
					if($script != null)
					{
						$valeur = remove_url_params(
							get_current_url()
						)."?".urlencode($this->ZoneParent->NomParamScriptAppele)."=".urlencode($script->NomElementZone) ;
						$valeur = update_url_params($valeur, $this->ParamsScript) ;
					}
				}
				return $valeur ;
			}
			public function ObtientStatutSelection()
			{
				if($this->EstNul($this->ZoneParent))
					return 0 ;
				$script = $this->ObtientScript() ;
				if($script == null)
					return 0 ;
				return ($this->ZoneParent->ScriptAppele->NomElementZone == $this->NomScript || (count($this->NomScriptsSelect) > 0 && in_array($this->ZoneParent->ScriptAppele->NomElementZone, $this->NomScriptsSelect))) ? 1 : 0 ;
			}
		}
		
		class PvBarreMenuWebBase extends PvComposantIUBase
		{
			public $TypeComposant = 'BarreMenuHTML' ;
			public $NomClasseCSSSelect = 'Selectionne' ;
			public $MenuRacine = null ;
			public $InclureSelection = 1 ;
			public $InclureRenduIcone = 1 ;
			public $InclureRenduTitre = 1 ;
			public $InclureRenduMiniature = 1 ;
			public $LargeurIcone = 21 ;
			public $HauteurMiniature = 42 ;
			public $NomClasseMenuRacine = "PvMenuIHMRacine" ;
			public $NomClasseCSSMenuRacine = "MenuRacine sf-menu" ;
			public $CheminIconeParDefaut = "images/icones/menu-defaut.png" ;
			public $CheminMiniatureParDefaut = "images/icones/menu-defaut.png" ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeMenuRacine() ;
			}
			public function AdopteScript($nom, & $script)
			{
				parent::AdopteScript($nom, $script) ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				$this->InitMenuRacine() ;
				if($this->EstPasNul($this->MenuRacine))
				{
					$this->MenuRacine->AdopteZone($nom."MenuRacine", $this->ZoneParent) ;
				}
			}
			protected function InitMenuRacine()
			{
				// echo "kkkkk " ;
				if(! $this->EstNul($this->MenuRacine))
					return ;
				$nomClasseMenuRacine = $this->NomClasseMenuRacine ;
				if(class_exists($nomClasseMenuRacine))
				{
					$this->MenuRacine = new $nomClasseMenuRacine() ;
					$this->MenuRacine->BarreMenu = & $this ;
				}
			}
			protected function ChargeMenuRacine()
			{
			}
			protected function AppliqueHabillage()
			{
				if(! isset($this->ZoneParent->Habillage) || $this->ZoneParent->EstNul($this->ZoneParent->Habillage))
				{
					return ;
				}
				$this->ZoneParent->Habillage->AppliqueSur($this) ;
				return $this->ZoneParent->Habillage->Rendu ;
			}
			protected function RenduDispositifBrut()
			{
				if($this->Visible == 0)
				{
					return '' ;
				}
				if($this->InclureSelection)
				{
					$this->MenuRacine->DetecteStatutSelection() ;
				}
				$ctn = '' ;
				$ctn .= $this->RenduMenuRacine($this->MenuRacine).PHP_EOL ;
				$ctn .= $this->RenduDefinitionsMenuRacine($this->MenuRacine) ;
				$ctn .= $this->AppliqueHabillage() ;
				return $ctn ;
			}
			protected function RenduDefinitionsMenu(& $menu)
			{
				$ctn = '' ;
				$ctn .= $menu->ObtientDefinitions() ;
				foreach($menu->SousMenus as $i => $sousMenu)
				{
					$ctn .= $this->RenduDefinitionsMenu($sousMenu) ;
				}
				return $ctn ;
			}
			protected function RenduDefinitionsMenuRacine()
			{
				return $this->RenduDefinitionsMenu($this->MenuRacine) ;
			}
			protected function ObtientUrlMenu(& $menu)
			{
			}
			protected function RenduMenuRacine(& $menu)
			{
				return $this->RenduMenu($menu) ;
			}
			protected function RenduMenu($menu)
			{
				$ctn = '' ;
				// print count($menu->SousMenus) ;
				if(! $menu->EstVisible || ! $menu->EstAccessible())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				if(! $menu->EstMenuRacine())
				{
					$ctn .= '<li>'.PHP_EOL ;
					// echo get_class($menu) ;
					$ctn .= $this->RenduTagOuvrLien($menu).PHP_EOL ;
					$ctn .= $this->RenduIconeMenu($menu).PHP_EOL ;
					$ctn .= $this->RenduTitreMenu($menu).PHP_EOL ;
					$ctn .= $this->RenduTagFermLien($menu).PHP_EOL ;
				}
				if(count($menu->SousMenus))
				{
					$ctn .= '<ul' ;
					if($menu->EstMenuRacine())
					{
						$ctn .= ' id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSSMenuRacine.'"' ;
					}
					$ctn .= '>'.PHP_EOL ;
					$nomSousMenus = array_keys($menu->SousMenus) ;
					foreach($nomSousMenus as $i => $nomSousMenu)
					{
						$sousMenu = $menu->SousMenus[$nomSousMenu] ;
						if(! $sousMenu->EstVisible)
						{
							continue ;
						}
						$ctn .= $this->RenduMenu($sousMenu).PHP_EOL ;
					}
					$ctn .= '</ul>'.PHP_EOL ;
				}
				if(! $menu->EstMenuRacine())
				{
					$ctn .= '</li>' ;
				}
				$menu->ComposantSupport = null ;
				return $ctn ;
			}
			protected function RenduTagOuvrLien(& $menu)
			{
				$ctn = '' ;
				$ctn .= '<a href="'.$menu->ObtientUrl().'"' ;
				if($menu->NomClasseCSS != '')
				{
					$ctn .= ' class="'.$this->NomClasseCSS.((! $menu->EstSelectionne) ? '' : ' '.$this->NomClasseCSSSelect).'"' ;
				}
				elseif($menu->EstSelectionne)
					$ctn .= ' class="'.$this->NomClasseCSSSelect.'"' ;
				if($menu->Tips != '')
				{
					$ctn .= ' title="'.htmlentities($menu->Tips).'"' ;
				}
				if($menu->FenetreCible != '')
				{
					$ctn .= ' target="'.htmlentities($menu->FenetreCible).'"' ;
				}
				$ctn .= '>' ;
				return $ctn ;
			}
			protected function RenduTagFermLien(& $menu)
			{
				$ctn = '' ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
			protected function RenduMiniatureMenu(& $menu)
			{
				$ctn = '' ;
				if($this->InclureRenduMiniature && $menu->InclureRenduMiniature)
				{
					$cheminMiniature = $menu->ObtientCheminMiniature() ;
					if($cheminMiniature == '')
					{
						$cheminMiniature = $this->CheminMiniatureParDefaut ;
					}
					$attrHauteur = (intval($this->HauteurMiniature) > 0) ? ' width="'.$this->HauteurMiniature.'"' : '' ;
					$ctn .= '<img src="'.$cheminMiniature.'"'.$attrHauteur.' border="0" />' ;
				}
				return $ctn ;
			}
			protected function RenduIconeMenu(& $menu)
			{
				$ctn = '' ;
				if($this->InclureRenduIcone && $menu->InclureRenduIcone)
				{
					$cheminIcone = $menu->ObtientCheminIcone() ;
					if($cheminIcone == '')
					{
						$cheminIcone = $this->CheminIconeParDefaut ;
					}
					$ctn .= '<img src="'.$cheminIcone.'" border="0" /> ' ;
				}
				return $ctn ;
			}
			protected function RenduTitreMenu(& $menu)
			{
				$ctn = '' ;
				if($this->InclureRenduTitre && $menu->InclureRenduTitre)
				{
					$ctn .= $menu->ObtientTitre() ;
				}		
				return $ctn ;
			}
		}
		class PvBlocMenuVertic extends PvBarreMenuWebBase
		{
			public $InclureRenduIcone = 0 ;
			public $InclureRenduBulle = 1 ;
			public $SymboleBulle = "- " ;
			protected function RenduMenuRacine(& $menu)
			{
				return $this->RenduMenu($menu) ;
			}
			protected function RenduMenu($menu)
			{
				$ctn = '' ;
				if(! $menu->EstVisible || ! $menu->EstAccessible())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				if(! $menu->EstMenuRacine())
				{
					$ctn .= '<div>'.PHP_EOL ;
					$ctn .= $this->RenduBulleMenu($menu) ;
					$ctn .= $this->RenduTagOuvrLien($menu) ;
					$ctn .= $this->RenduIconeMenu($menu) ;
					$ctn .= $this->RenduTitreMenu($menu) ;
					$ctn .= $this->RenduTagFermLien($menu).PHP_EOL ;
				}
				if(count($menu->SousMenus))
				{
					$ctn .= '<div' ;
					if($menu->EstMenuRacine())
					{
						$ctn .= ' id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSSMenuRacine.'"' ;
					}
					$ctn .= '>'.PHP_EOL ;
					$nomSousMenus = array_keys($menu->SousMenus) ;
					foreach($nomSousMenus as $i => $nomSousMenu)
					{
						$sousMenu = $menu->SousMenus[$nomSousMenu] ;
						if(! $sousMenu->EstVisible)
						{
							continue ;
						}
						$ctn .= $this->RenduMenu($sousMenu).PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
				}
				if(! $menu->EstMenuRacine())
				{
					$ctn .= '</div>' ;
				}
				$menu->ComposantSupport = null ;
				return $ctn ;
			}
			protected function RenduBulleMenu($menu)
			{
				if($this->InclureRenduBulle == 0)
					return "" ;
				return $this->SymboleBulle ;
			}
		}
		class PvTablMenuHoriz extends PvBarreMenuWebBase
		{
			public $NomClasseCSSMenuRacine = "MenuRacine menu_horiz" ;
			public $NomClasseCSSCellSelect = "" ;
			public $InclureRenduMiniature = 1 ;
			public $SeparateurMenu = "" ;
			public $InclureSeparateurMenu = 0 ;
			public $CentrerSousMenu = 1 ;
			public $LargeurSousMenu = "75" ;
			public $CentrerMenu = 1 ;
			public $MaxColonnes = 8 ;
			public $TotalColonnes = 0 ;
			protected function RenduEnteteTabl()
			{
				$ctn = '' ;
				$ctn .= '<table' ;
				if($this->CentrerMenu)
				{
					$ctn .= ' align="center"' ;
				}
				$ctn .= ' cellspacing="0" cellpadding="4"' ;
				$ctn .= '>'.PHP_EOL ;
				$ctn .= '<tr>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduPiedTabl()
			{
				$ctn = '' ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduMenu($menu)
			{
				$ctn = '' ;
				$this->TotalColonnes = 0 ;
				if(! $menu->EstVisible || ! $menu->EstMenuRacine())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				if(count($menu->SousMenus))
				{
					$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSSMenuRacine.'">'.PHP_EOL ;
					$nomSousMenus = array_keys($menu->SousMenus) ;
					$totalMenus = 0 ; 
					foreach($nomSousMenus as $i => $nomSousMenu)
					{
						$sousMenu = $menu->SousMenus[$nomSousMenu] ;
						if(! $sousMenu->EstVisible)
						{
							if($i == count($menu->SousMenus) - 1)
							{
								$ctn .= $this->RenduPiedTabl() ;
							}
							continue ;
						}
						if($totalMenus % $this->MaxColonnes == 0)
						{
							if($totalMenus > 0)
							{
								$ctn .= $this->RenduPiedTabl() ;
							}
							$ctn .= $this->RenduEnteteTabl() ;
							$totalMenus = 0 ;
						}
						if($totalMenus > 0 && $this->InclureSeparateurMenu && $this->SeparateurMenu != '')
						{
							$ctn .= '<td>'.$this->SeparateurMenu.'</td>' ;
						}
						$attr = '' ;
						if($this->CentrerSousMenu)
							$attr .= ' align="center"' ;
						if($this->LargeurSousMenu > "")
							$attr .= ' width="'.$this->LargeurSousMenu.'"' ;
						if($sousMenu->EstSelectionne)
							$attr .= ' class="'.$this->NomClasseCSSCellSelect.'"' ;
						$ctn .= '<td'.$attr.' valign="bottom">'.$this->RenduSousMenu($sousMenu).'</td>'.PHP_EOL ;
						$totalMenus++ ;
						if($totalMenus % $this->MaxColonnes == $this->MaxColonnes || $i == count($menu->SousMenus) - 1)
						{
							$ctn .= $this->RenduPiedTabl() ;
						}
					}
					$ctn .= '</div>' ;
				}
				$menu->ComposantSupport = null ;
				return $ctn ;
			}
			protected function RenduSousMenu(& $sousMenu)
			{
				$ctn = '' ;
				$ctn .= $this->RenduTagOuvrLien($sousMenu) ;
				$ctn .= '<div>'.$this->RenduMiniatureMenu($sousMenu).'</div>' ;
				$ctn .= '<div>'.$this->RenduTitreMenu($sousMenu).'</div>' ;
				$ctn .= $this->RenduTagFermLien($sousMenu) ;
				return $ctn ;
			}
		}
		class PvListeMenuHoriz extends PvTablMenuHoriz
		{
			public $LargeurSousMenu = "" ;
			protected function RenduSousMenu(& $sousMenu)
			{
				$ctn = '' ;
				$ctn .= $this->RenduTagOuvrLien($sousMenu) ;
				$ctn .= '<div>'.$this->RenduTitreMenu($sousMenu).'</div>' ;
				$ctn .= $this->RenduTagFermLien($sousMenu) ;
				return $ctn ;
			}
		}
		class PvBarreBtnsHoriz extends PvListeMenuHoriz
		{
			public $LargeurSousMenu = "" ;
			protected function RenduSousMenu(& $sousMenu)
			{
				$ctn = '' ;
				$ctn .= $this->RenduTagOuvrLien($sousMenu) ;
				$ctn .= '<button type="button" onclick="javascript:window.location = '.htmlentities(svc_json_encode($sousMenu->ObtientUrl())).'"
				>'.$this->RenduTitreMenu($sousMenu).'</button>' ;
				$ctn .= $this->RenduTagFermLien($sousMenu) ;
				return $ctn ;
			}
			protected function AppliqueHabillage()
			{
				$ctn = parent::AppliqueHabillage() ;
				if($this->ZoneParent->InclureJQueryUi)
				{
				$ctn .= '<script type="text/javascript">
	jQuery(function() {
		jQuery("#'.$this->IDInstanceCalc.'").find("button").button() ;
	}) ;
</script>' ;
				}
				return $ctn ;
			}
		}
		class PvCadreMenuWeb extends PvBarreMenuWebBase
		{
			public $NomClasseCSSMenuRacine = "MenuRacine cadre_menu" ;
			public $NomClasseCSSMenuNv1 = "sous-menu-nv1" ;
			public $NomClasseCSSMenuNv2 = "sous-menu-nv2" ;
			public $AlignMenuRacine = "center" ;
			public $AlignMenuNv1 = "center" ;
			public $AlignMenuNv2 = "center" ;
			protected function RenduMenuRacine(& $menu)
			{
				$ctn = '' ;
				if(! $menu->EstVisible || ! $menu->EstAccessible())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				if(count($menu->SousMenus))
				{
					$ctn .= '<table' ;
					$ctn .= ' align="'.$this->AlignMenuRacine.'"' ;
					$ctn .= ' id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSSMenuRacine.'"' ;
					$ctn .= '>'.PHP_EOL ;
					$ctn .= '<tr>'.PHP_EOL ;
					$nomSousMenus = array_keys($menu->SousMenus) ;
					foreach($nomSousMenus as $i => $nomSousMenu)
					{
						$sousMenu = $menu->SousMenus[$nomSousMenu] ;
						if(! $sousMenu->EstVisible)
						{
							continue ;
						}
						$ctn .= '<td class="cadre-sous-menu">'.$this->RenduMenuNv1($sousMenu).'</td>'.PHP_EOL ;
					}
					$ctn .= '</tr>'.PHP_EOL ;
					$ctn .= '</table>'.PHP_EOL ;
				}
				$menu->ComposantSupport = null ;
				return $ctn ;
			}
			protected function RenduMenuNv1($menu)
			{
				$ctn = '' ;
				if(! $menu->EstVisible || ! $menu->EstAccessible())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				$ctn .= '<div align="'.$this->AlignMenuNv1.'" class="'.$this->NomClasseCSSMenuNv1.'">'.PHP_EOL ;
				$ctn .= $this->RenduTagOuvrLien($menu).PHP_EOL ;
				$ctn .= $this->RenduTitreMenu($menu).PHP_EOL ;
				$ctn .= $this->RenduTagFermLien($menu).PHP_EOL ;
				$ctn .= '</div>' ;
				if(count($menu->SousMenus))
				{
					$ctn .= '<table>'.PHP_EOL ;
					$ctn .= '<tr>'.PHP_EOL ;
					$nomSousMenus = array_keys($menu->SousMenus) ;
					foreach($nomSousMenus as $i => $nomSousMenu)
					{
						$sousMenu = $menu->SousMenus[$nomSousMenu] ;
						if(! $sousMenu->EstVisible)
						{
							continue ;
						}
						$ctn .= '<td>' ;
						$ctn .= $this->RenduMenuNv2($sousMenu) ;
						$ctn .= '</td>' ;
					}
					$ctn .= '</tr>'.PHP_EOL ;
					$ctn .= '</table>'.PHP_EOL ;
				}
				$menu->ComposantSupport = null ;
				return $ctn ;
			}
			protected function RenduMenuNv2($menu)
			{
				$ctn = '' ;
				$ctn .= '<div class="'.$this->NomClasseCSSMenuNv2.'">'.PHP_EOL ;
				$ctn .= $this->RenduTagOuvrLien($menu) ;
				$ctn .= $this->RenduTitreMenu($menu) ;
				$ctn .= $this->RenduTagFermLien($menu) ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
	}

?>