<?php
	
	if(! defined("PV_ZONE_BOOTSTRAP"))
	{
		if(! defined('PV_MEMBERSHIP_BOOTSTRAP'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		define("PV_ZONE_BOOTSTRAP", 1) ;
		
		class PvZoneBaseBootstrap extends PvZoneWebSimple
		{
			public $InclureCtnJsEntete = 1 ;
			public $InclureJQuery = 1 ;
			public $InclureBootstrap = 1 ;
			public $InclureNavbarFlottant = 1 ;
			public $RenduExtraHead = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' ;
			public $ViewportMeta = 'width=device-width, initial-scale=1' ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPBootstrap" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionBootstrap" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionBootstrap" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionBootstrap" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseBootstrap" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseBootstrap" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreBootstrap" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreBootstrap" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreBootstrap" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsBootstrap" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreBootstrap" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresBootstrap" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilBootstrap" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilBootstrap" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilBootstrap" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsBootstrap" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleBootstrap" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleBootstrap" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleBootstrap" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesBootstrap" ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplisseurConfigMembershipBootstrap" ;
			public $InclureHelpers = 1 ;
			public $AbcisseXNavbarFlottant = "0px" ;
			public $AbcisseYNavbarFlottant = "0px" ;
			public $LargeurNavbarFlottant = "95%" ;
			public $BackgroundNavbarFlottant = "white" ;
			public $CouleurBordureNavbarFlottant = "" ;
			public $CouleurTexteNavbarFlottant = "black" ;
			public $ContenuCSSPetitsEcrans = "" ;
			public function InclutLibrairiesExternes()
			{
				parent::InclutLibrairiesExternes() ;
				if($this->ContenuCSSPetitsEcrans != '' || $this->InclureNavbarFlottant == 1)
				{
					$this->InscritContenuCSS('@media (max-width:767px) {
'.(($this->InclureNavbarFlottant == 1) ? '	.navbar-collapse {
		position:absolute ;
		top:'.$this->AbcisseXNavbarFlottant.' ;
		left :'.$this->AbcisseYNavbarFlottant.' ;
		width: '.$this->LargeurNavbarFlottant.' ;
		z-index:9999999 ;
	}
	.navbar-collapse {
		background-color:'.$this->BackgroundNavbarFlottant.' !important ;
	}
	.navbar-collapse > ul {
		width : 98%;
	}
	.navbar-collapse ul a {
		color:'.$this->CouleurTexteNavbarFlottant.' !important ;
	}
	.navbar-collapse > ul > li {
		width : 100%;
	}' : '').(($this->ContenuCSSPetitsEcrans != '') ? '
'.$this->ContenuCSSPetitsEcrans : '').'
}') ;
				}
				if($this->InclureHelpers == 1)
				{
					$this->InscritContenuJS($this->ContenuJsHelper()) ;
				}
			}
			protected function ContenuJsHelper()
			{
				$ctn = '' ;
				$ctn .= 'jQuery(function() {
jQuery(\'.pull-down\').each(function() {
  var $this = jQuery(this);
  $this.css(\'margin-top\', $this.parent().height() - $this.height()) ;
});
}) ;' ;
				return $ctn ;
			}
		}
	}
	
?>