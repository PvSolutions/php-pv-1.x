<?php
	
	if(! defined('PV_COMPOSANT_IU_ZONE_CHARISMA'))
	{
		if(! defined('PV_COMPOSANT_IU_SIMPLE'))
		{
			include dirname(__FILE__).'/../../Simple/Composant.class.php' ;
		}
		define('PV_COMPOSANT_IU_ZONE_CHARISMA', 1) ;
		
		class ComposantIUBaseCharisma extends PvComposantIUBase
		{
		}
		
		class TopBarCharisma extends ComposantIUBaseCharisma
		{
			public $NomClasseCSS = 'navbar' ;
			public $NomClasseCSSMembre = 'icon-user' ;
			public $LibelleLienModifProfil = 'Profil' ;
			public $LibelleLienDeconnexion = 'Deconnexion' ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				/*
				if(! $this->ZoneParent->PossedeMembreConnecte())
				{
					return $ctn ;
				}
				*/
				$ctn .= '<div class="navbar">'.PHP_EOL ;
				$ctn .= '<div class="navbar-inner">'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>'.PHP_EOL ;
				$ctn .= '<a class="brand" href="?"> <img alt="" src="'.$this->ZoneParent->CheminLogo.'" /> <span>'.htmlentities($this->ZoneParent->NomSite).'</span></a>' ;
				if($this->ZoneParent->PossedeMembreConnecte())
				{
					$ctn .= '<div class="btn-group pull-right" >
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
<i class="'.$this->NomClasseCSSMembre.'"></i><span class="hidden-phone"> '.htmlentities($this->ZoneParent->Membership->MemberLogged->Login).'</span>
<span class="caret"></span>
</a>'.PHP_EOL ;
					if($this->ZoneParent->InclureScriptsMembership)
					{
						$ctn .= '<ul class="dropdown-menu">'.PHP_EOL ;
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptModifMembre->ObtientUrl().'">'.htmlentities($this->LibelleLienModifProfil).'</a></li>
		<li class="divider"></li>'.PHP_EOL ;
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptDeconnexion->ObtientUrl().'">'.htmlentities($this->LibelleLienDeconnexion).'</a></li>' ;
						$ctn .= '</ul>'.PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="top-nav nav-collapse">
<ul class="nav">
<li><a href="#">Visit Site</a></li>
<li>
<form class="navbar-search pull-left">
<input placeholder="Search" class="search-query span2" name="query" type="text">
</form>
</li>
</ul>
</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class LeftMenuCharisma extends PvBarreMenuWebBase
		{
			public $NomClasseCSSMenuRacine = 'nav nav-tabs nav-stacked main-menu' ;
			public $NomClasseCSSMenu = 'icon-chevron-right' ;
			protected function RenduMenuNv1($menu)
			{
				$ctn = '' ;
				if(! $menu->EstVisible || ! $menu->EstAccessible())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				$ctn .= '<li class="nav-header hidden-tablet '.$menu->NomClasseCSS.'">'.PHP_EOL ;
				$ctn .= $this->RenduTitreMenu($menu).PHP_EOL ;
				$ctn .= '</li>'.PHP_EOL ;
				if(count($menu->SousMenus) > 0)
				{
					$nomSousMenus = array_keys($menu->SousMenus) ;
					foreach($nomSousMenus as $i => $nomSousMenu)
					{
						$sousMenu = $menu->SousMenus[$nomSousMenu] ;
						$ctn .= $this->RenduMenuNv2($sousMenu).PHP_EOL ;
					}
				}
				return $ctn ;
			}
			protected function RenduMenuNv2($menu)
			{
				$ctn = '' ;
				if(! $menu->EstVisible || ! $menu->EstAccessible())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				$nomClasseCSS = ($menu->NomClasseCSS != '') ? $menu->NomClasseCSS : $this->NomClasseCSSMenu ;
				$ctn .= '<li><a class="ajax-link" href="'.$menu->ObtientUrl().'"><i class="'.$nomClasseCSS.'"></i><span class="hidden-tablet"> '.$this->RenduTitreMenu($menu).'</span></a></li>' ;
				return $ctn ;
			}
			protected function RenduMenuRacine($menu)
			{
				$ctn = '' ;
				if(! $menu->EstVisible || ! $menu->EstAccessible())
				{
					return '' ;
				}
				$menu->ComposantSupport = $this ;
				$ctn .= '<div class="span2 main-menu-span">
<div class="well nav-collapse sidebar-nav">'.PHP_EOL ;
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
						$ctn .= $this->RenduMenuNv1($sousMenu).PHP_EOL ;
					}
					$ctn .= '</ul>'.PHP_EOL ;
				}
				$ctn .= '</div>
</div>'.PHP_EOL ;
				$ctn .= '<script language="javascript">
jQuery("document").ready(function() {
	//highlight current / active link
	jQuery("ul.main-menu li a").each(function() {
		if(jQuery(jQuery(this))[0].href == String(window.location))
			jQuery(this).parent().addClass("active");
	});
	jQuery("ul.main-menu li:not(.nav-header)").hover(function(){
		jQuery(this).animate({"margin-left":"+=5"},300);
	},
	function(){
		jQuery(this).animate({"margin-left":"-=5"},300);
	});
}) ;
</script>' ;
				$menu->ComposantSupport = null ;
				return $ctn ;
			}
			protected function RenduMenu($menu)
			{
				return $this->RenduMenuRacine($menu) ;
			}
		}
		class FooterCharisma extends ComposantIUBaseCharisma
		{
			public $NomSite = 'My Website' ;
			public $UrlSite = '?' ;
			public $NomCadreFenSite = '_blank' ;
			public $Annee = 2012 ;
			public $IntervalleAnActuelle = 1 ;
			public $LibelleFourniPar = 'Powered by : ' ;
			public $NomFournisseur = 'Charisma' ;
			public $NomCadreFenFournisseur = '_blank' ;
			public $UrlFournisseur = '?' ;
			protected function ObtientAnnee()
			{
				return $this->Annee.(($this->IntervalleAnActuelle && $this->Annee != date('Y')) ? ' - '.date('Y') : '') ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '<hr>
<footer>
<p class="pull-left">&copy; <a href="'.$this->UrlSite.'" target="'.$this->NomCadreFenSite.'">'.$this->NomSite.'</a> '.$this->ObtientAnnee().'</p>
<p class="pull-right">'.$this->LibelleFourniPar.' <a href="'.$this->UrlFournisseur.'" target="'.$this->NomCadreFenFournisseur.'">'.$this->NomFournisseur.'</a></p>
</footer>' ;
				return $ctn ;
			}
		}
	}
	
?>