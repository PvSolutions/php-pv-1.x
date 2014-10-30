<?php
	
	if(! defined('PV_COMPOSANT_MEMBERSHIP_BOOTSTRAP'))
	{
		define('PV_COMPOSANT_MEMBERSHIP_BOOTSTRAP', 1) ;
		
		class PvMenuDeroulMembreBootstrap extends PvComposantIUBase
		{
			public $LibelleInscription = "S'inscrire" ;
			public $LibelleChangeMotPasse = "Changer mot de passe" ;
			public $LibelleConnexion = "Connexion" ;
			public $LibelleModifPrefs = "Param&egrave;tres" ;
			public $LibelleListeMembres = "Membres" ;
			public $LibelleAjoutMembre = "Inscription" ;
			public $LibelleListeProfils = "Profils" ;
			public $LibelleAjoutProfil = "Cr&eacute;er profil" ;
			public $LibelleListeRoles = "R&ocirc;les" ;
			public $LibelleAjoutRole = "Cr&eacute;er r&ocirc;le" ;
			public $LibelleDeconnexion = "D&eacute;connexion" ;
			protected function RenduDispositifBrut()
			{
				$membership = & $this->ZoneParent->Membership ;
				$ctn = '' ;
				$ctn .= '<ul id="'.$this->IDInstanceCalc.'" class="nav navbar-nav navbar-right">'.PHP_EOL ;
				if($this->ZoneParent->EstNul($membership))
				{
					$ctn .= '<li><a href="javascript:;"><i class="glyphicon glyphicon-ban-circle"></i> Espace membre indisponible</a></li>'.PHP_EOL ;
					$ctn .= '</ul>' ;
					return $ctn ;
				}
				if($this->ZoneParent->PossedeMembreConnecte())
				{
					$ctn .= '<li class="dropdown">'.PHP_EOL ;
					$ctn .= '<a class="dropdown-toggle" role="button" data-toggle="dropdown" href="javascript:;"><i class="glyphicon glyphicon-user"></i> '.htmlentities($membership->MemberLogged->Login).'<span class="caret"></span></a>'.PHP_EOL ;
					$ctn .= '<ul id="'.$this->IDInstanceCalc.'_Compte" class="dropdown-menu" role="menu">'.PHP_EOL ;
					$ctn .= '<li><a href="'.$this->ZoneParent->ScriptChangeMotPasse->ObtientUrl().'">'.$this->LibelleChangeMotPasse.'</a></li>'.PHP_EOL ;
					if($this->ZoneParent->AutoriserModifPrefs && $this->ZoneParent->ScriptModifPrefs->EstAccessible())
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptModifPrefs->ObtientUrl().'">'.$this->LibelleModifPrefs.'</a></li>'.PHP_EOL ;
					}
					if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptListeMembres) && $this->ZoneParent->ScriptListeMembres->EstAccessible())
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptListeMembres->ObtientUrl().'">'.$this->LibelleListeMembres.'</a></li>'.PHP_EOL ;
					}
					if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptAjoutMembre) && $this->ZoneParent->ScriptAjoutMembre->EstAccessible())
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptAjoutMembre->ObtientUrl().'">'.$this->LibelleAjoutMembre.'</a></li>'.PHP_EOL ;
					}
					if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptListeProfils) && $this->ZoneParent->ScriptListeProfils->EstAccessible())
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptListeProfils->ObtientUrl().'">'.$this->LibelleListeProfils.'</a></li>'.PHP_EOL ;
					}
					if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptAjoutProfil) && $this->ZoneParent->ScriptAjoutProfil->EstAccessible())
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptAjoutProfil->ObtientUrl().'">'.$this->LibelleAjoutProfil.'</a></li>'.PHP_EOL ;
					}
					if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptListeRoles) && $this->ZoneParent->ScriptListeRoles->EstAccessible())
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptListeRoles->ObtientUrl().'">'.$this->LibelleListeRoles.'</a></li>'.PHP_EOL ;
					}
					if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptAjoutRole) && $this->ZoneParent->ScriptAjoutRole->EstAccessible())
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptAjoutRole->ObtientUrl().'">'.$this->LibelleAjoutRole.'</a></li>'.PHP_EOL ;
					}
					$ctn .= '</ul>'.PHP_EOL ;
					$ctn .= '</li>'.PHP_EOL ;
					$ctn .= '<li><a href="'.$this->ZoneParent->ScriptDeconnexion->ObtientUrl().'"><i class="glyphicon glyphicon-lock"></i> '.$this->LibelleDeconnexion.'</a></li>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<li><a href="'.$this->ZoneParent->ScriptConnexion->ObtientUrl().'"><i class="glyphicon glyphicon-lock"></i> '.$this->LibelleConnexion.'</a></li>'.PHP_EOL ;
					if($this->ZoneParent->AutoriserInscription)
					{
						$ctn .= '<li><a href="'.$this->ZoneParent->ScriptInscription->ObtientUrl().'"><i class="glyphicon glyphicon-user"></i> '.$this->LibelleInscription.'</a></li>'.PHP_EOL ;
					}
				}
				$ctn .= '</ul>' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('jQuery(function() {
		jQuery("[data-toggle=collapse]").click(function(){
		// toggle icon
			jQuery(this).find("i").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
	});
});') ;
				return $ctn ;
			}
		}
	}
	
?>