<?php
	
	if(! defined('PV_MENU_BOOTSTRAP'))
	{
		define('PV_MENU_BOOTSTRAP', 1) ;
		
		class PvStyleMenuBaseBootstrap
		{
			public function Execute(& $script, & $barreMenu, & $menu, $pos=-1)
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
		
		class PvMenuBaseBootstrap extends PvMenuIHMBase
		{
			public $StyleRendu ;
			public $NomClasseSousMenuFige = "PvMenuFigeBootstrap" ;
			public function CreeSousMenuModal()
			{
				return new PvMenuModalBootstrap() ;
			}
			public function CreeSousMenuScriptModal()
			{
				return new PvMenuModalScriptBootstrap() ;
			}
			public function CreeSousMenuUrlModal()
			{
				return new PvMenuModalUrlBootstrap() ;
			}
			public function InscritSousMenuModal($nom, $corpsDlg='', $titreDlg='')
			{
				$menu = $this->CreeSousMenuModal() ;
				$nom = $this->ObtientNomNouvSousMenu($nom) ;
				$menu->CorpsDlg = $corpsDlg ;
				$menu->TitreDlg = $titreDlg ;
				$this->ValideInscriptionSousMenu($nom, $menu) ;
				return $menu ;
			}
			public function InscritSousMenuScriptModal($nom, $nomScript, $paramsScript=array())
			{
				$menu = $this->CreeSousMenuScriptModal() ;
				$nom = $this->ObtientNomNouvSousMenu($nom) ;
				$menu->NomScript = $nomScript ;
				$menu->ParamsScript = $paramsScript ;
				$this->ValideInscriptionSousMenu($nom, $menu) ;
				return $menu ;
			}
			public function InscritSousMenuUrlModal($nom, $url='', $titre='')
			{
				$menu = $this->CreeSousMenuUrlModal() ;
				$nom = $this->ObtientNomNouvSousMenu($nom) ;
				$menu->Url = $url ;
				$menu->Titre = $titre ;
				$this->ValideInscriptionSousMenu($nom, $menu) ;
				return $menu ;
			}
		}
		class PvMenuRacineBootstrap extends PvMenuBaseBootstrap
		{
			public $EstRacine = 1 ;
		}
		class PvMenuModalBootstrap extends PvMenuBaseBootstrap
		{
			public $InclureTitreDlg = 0 ;
			public $CacherDlg = 1 ;
			public $TitreDlg ;
			public $CorpsDlg ;
			public $InclureBtnFermerDlg = 1 ;
			public $LibelleBtnFermerDlg = "Fermer" ;
			public $InclureBtnValiderDlg = 0 ;
			public $LibelleBtnValiderDlg = "Valider" ;
			protected function ObtientUrlSpec()
			{
				return parent::ObtientUrl() ;
			}
			protected function ObtientTitreDlg()
			{
				return $this->TitreDlg ;
			}
			protected function ObtientCorpsDlg()
			{
				return $this->CorpsDlg ;
			}
			protected function ObtientCtnHtmlDlg()
			{
				$ctn = '' ;
				$ctn .= '<div class="modal fade" id="Dlg'.$this->IDInstanceCalc.'" tabindex="-1" role="dialog"' ;
				$ctn .= ' aria-labelledby="TitreDlg'.$this->IDInstanceCalc.'" aria-hidden="'.(($this->CacherDlg) ? 'true' : 'false').'">' ;
				$ctn .= '<div class="modal-dialog">' ;
				$ctn .= '<div class="modal-content">' ;
				$ctn .= '<div class="modal-header">' ;
				if($this->InclureBtnFermerDlg)
				{
					$ctn .= '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->LibelleBtnFermerDlg.'</span></button>' ;
				}
				if($this->InclureTitreDlg)
				{
					$ctn .= '<h4 class="modal-title" id="TitreDlg'.$this->IDInstanceCalc.'">'.$this->ObtientTitreDlg().'</h4>' ;
				}
				$ctn .= '</div>' ;
				$ctn .= '<div class="modal-body">' ;
				$ctn .= $this->ObtientCorpsDlg() ;
				$ctn .= '</div>' ;
				$ctn .= '<div class="modal-footer">' ;
				if($this->InclureBtnFermerDlg)
				{
					$ctn .= '<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->LibelleBtnFermerDlg.'</button>' ;
				}
				if($this->InclureBtnValiderDlg)
				{
					$ctn .= '<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->LibelleBtnValiderDlg.'</button>' ;
				}
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			public function ObtientUrl()
			{
				$ctn = '' ;
				$ctn .= 'javascript:ouvre'.$this->IDInstanceCalc.'()' ;
				return $ctn ;
			}
			public function ObtientDefinitions()
			{
				$ctn = '' ;
				$ctn .= $this->ObtientCtnHtmlDlg() ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('function ouvre'.$this->IDInstanceCalc.'() {
	var dlg = jQuery("#Dlg'.$this->IDInstanceCalc.'") ;
	dlg.modal() ;
}') ;
				return $ctn ;
			}
		}
		class PvMenuModalUrlBootstrap extends PvMenuModalBootstrap
		{
			public $HauteurCorpsDlg = "300px" ;
			public $LargeurBordureCadre = "0" ;
			protected function ObtientCorpsDlg()
			{
				return '<iframe src="'.$this->ObtientUrlSpec().'" style="zoom:1; width:99.6%; height:'.$this->HauteurCorpsDlg.'" frameborder="'.$this->LargeurBordureCadre.'"></iframe>' ;
			}
		}
		class PvMenuModalScriptBootstrap extends PvMenuModalUrlBootstrap
		{
			public $InclureTitreDlg = 1 ;
			public $NomScript = "" ;
			public $ParamsScript = array() ;
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
			protected function ObtientTitreDlg()
			{
				$valeur = parent::ObtientTitreDlg() ;
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
			protected function ObtientUrlSpec()
			{
				$valeur = parent::ObtientUrlSpec() ;
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
				return ($this->ZoneParent->ScriptAppele->NomElementZone == $this->NomScript) ? 1 : 0 ;
			}
		}
		
		class PvMenuFigeBootstrap extends PvMenuBaseBootstrap
		{
			public function ObtientUrl()
			{
				return "javascript:;" ;
			}
		}
		
		class PvBarreMenuBaseBootstrap extends PvBarreMenuWebBase
		{
			public $NomClasseMenuRacine = "PvMenuRacineBootstrap" ;
		}
		
		class PvGrdBarreMenu1Bootstrap extends PvBarreMenuBaseBootstrap
		{
			public $NomClasseCSSMenuRacine = "MenuRacine sf-menu" ;
			protected function RenduMenuRacine(& $menu)
			{
				$ctn = '' ;
				foreach($menu->SousMenus as $i => $sousMenu)
				{
					$ctn .= $this->RenduMenuNv1($sousMenu, $i).PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduMenuNv1($menu, $pos=-1)
			{
				$ctn = '' ;
				$ctn .= '<a href="'.$menu->ObtientUrl().'"><strong><i class="glyphicon '.$menu->ObtientValCfgSpec('glyphicon', 'glyphicon-folder-open').'"></i> '.$this->RenduTitreMenu($menu).'</strong></a>' ;
				$ctn .= '<hr>' ;
				if(count($menu->SousMenus) > 0)
				{
					$ctn .= '<ul id="'.$menu->IDInstanceCalc.'" class="list-unstyled">'.PHP_EOL ;
					foreach($menu->SousMenus as $i => $sousMenu)
					{
						$ctn .= $this->RenduMenuNv2($sousMenu, $i).PHP_EOL ;
					}
					$ctn .= '</ul>'.PHP_EOL ;
					$ctn .= '<hr>' ;
				}
				return $ctn ;
			}
			protected function RenduMenuNv2($menu, $pos=-1)
			{
				$ctn = '' ;
				$ctn .= '<li class="nav-header"><a href="'.$menu->ObtientUrl().'"' ;
				if(count($menu->SousMenus) > 0)
				{
					$ctn .= ' data-toggle="collapse" data-target="#SM_'.$menu->IDInstanceCalc.'"' ;
				}
				$ctn .= '>' ;
				$ctn .= '<h5>'.$this->RenduTitreMenu($menu) ;
				if(count($menu->SousMenus) > 0)
				{
					$ctn .= ' <i class="glyphicon glyphicon-chevron-down"></i>' ;
				}
				$ctn .= '</h5>' ;
				$ctn .= '</a>' ;
				if(count($menu->SousMenus) > 0)
				{
					$ctn .= '<ul class="list-unstyled collapse in" id="SM_'.$menu->IDInstanceCalc.'">'.PHP_EOL ;
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
				$ctn .= '<li><i class="glyphicon glyphicon-cog"></i> <a href="'.$menu->ObtientUrl().'"'.(($menu->EstSelectionne) ? ' class="active"' : '').'>'.$this->RenduTitreMenu($menu).'</a></li>' ;
				return $ctn ;
			}
		}
	}
	
?>