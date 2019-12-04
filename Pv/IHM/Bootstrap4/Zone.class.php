<?php
	
	if(! defined("PV_ZONE_BOOTSTRAP4"))
	{
		if(! defined('PV_MEMBERSHIP_BOOTSTRAP4'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		define("PV_ZONE_BOOTSTRAP4", 1) ;
		
		class PvZoneBaseBootstrap4 extends PvZoneWebSimple
		{
			public $InclureCtnJsEntete = 1 ;
			public $InclureJQuery = 1 ;
			public $InclureBootstrap = 1 ;
			public $InclureNavbarFlottant = 0 ;
			public $RenduExtraHead = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' ;
			public $ViewportMeta = 'width=device-width, initial-scale=1' ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecouvreMPBootstrap4" ;
			public $NomClasseScriptInscription = "PvScriptInscriptionBootstrap4" ;
			public $NomClasseScriptDeconnexion = "PvScriptDeconnexionBootstrap4" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionBootstrap4" ;
			public $NomClasseScriptChangeMotPasse = "PvScriptChangeMotPasseBootstrap4" ;
			public $NomClasseScriptDoitChangerMotPasse = "PvScriptDoitChangerMotPasseBootstrap4" ;
			public $NomClasseScriptChangeMPMembre = "PvScriptChangeMPMembreBootstrap4" ;
			public $NomClasseScriptAjoutMembre = "PvScriptAjoutMembreBootstrap4" ;
			public $NomClasseScriptModifMembre = "PvScriptModifMembreBootstrap4" ;
			public $NomClasseScriptModifPrefs = "PvScriptModifPrefsBootstrap4" ;
			public $NomClasseScriptSupprMembre = "PvScriptSupprMembreBootstrap4" ;
			public $NomClasseScriptListeMembres = "PvScriptListeMembresBootstrap4" ;
			public $NomClasseScriptAjoutProfil = "PvScriptAjoutProfilBootstrap4" ;
			public $NomClasseScriptModifProfil = "PvScriptModifProfilBootstrap4" ;
			public $NomClasseScriptSupprProfil = "PvScriptSupprProfilBootstrap4" ;
			public $NomClasseScriptListeProfils = "PvScriptListeProfilsBootstrap4" ;
			public $NomClasseScriptAjoutRole = "PvScriptAjoutRoleBootstrap4" ;
			public $NomClasseScriptModifRole = "PvScriptModifRoleBootstrap4" ;
			public $NomClasseScriptSupprRole = "PvScriptSupprRoleBootstrap4" ;
			public $NomClasseScriptListeRoles = "PvScriptListeRolesBootstrap4" ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplisseurConfigMembershipBootstrap4" ;
			public $InclureHelpers = 1 ;
			public $InclureTableauFixe = 0 ;
			public $HauteurTableauFixe = '600px' ;
			public $BackgroundEnteteTableauFixe = 'white' ;
			public $ClasseCSSMsgExecSucces = "alert alert-success" ;
			public $ClasseCSSMsgExecErreur = "alert alert-danger" ;
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
				if($this->InclureTableauFixe == 1)
				{
					$this->InscritContenuCSS('@media not print {
.TableauDonneesHTML {
  overflow-y: auto;
  height:'.$this->HauteurTableauFixe.';
}
.TableauDonneesHTML table.RangeeDonnees {
  border-collapse: collapse;
}
.TableauDonneesHTML table.RangeeDonnees th,
.TableauDonneesHTML table.RangeeDonnees td {
  padding: 8px 16px;
}
.TableauDonneesHTML table.RangeeDonnees th {
  position: sticky;
  top: 0;
  background: '.$this->BackgroundEnteteTableauFixe.' ;
}
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