<?php
	
	if(! defined('PV_SCRIPT_PLATF_SVC_WEB'))
	{
		define('PV_SCRIPT_PLATF_SVC_WEB', 1) ;
		
		class PvScriptPlatfSvcWeb extends PvScriptWebSimple
		{
		}
		class PvScriptGenCltWebPlatfSvcWeb extends PvScriptPlatfSvcWeb
		{
		}
		class PvScriptRecvrMPPlatfSvcWeb extends PvScriptRecouvreMPWeb
		{
			public $DansClientWeb = 1 ; 
		}
		
		class PvActValidCnxPlatfSvcWeb extends PvActBasePlatfSvcWeb
		{
			protected function ConstruitResultSpec()
			{
				$script = & $this->ScriptParent ;
				if($script->TentativeConnexionValidee)
				{
					$this->Resultat->ConfirmeSucces($script->IdMembre) ;
				}
				else
				{
					$this->Resultat->ConfirmeErreur(1, $script->MessageConnexionEchouee, "authentify_error") ;
				}
			}
		}
		class PvScriptConnexionPlatfSvcWeb extends PvScriptConnexionWeb
		{
			public $DansClientWeb = 1 ;
			protected $ActValidCnx ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->ActValidCnx = $this->InsereActionAvantRendu("SoumetTentative", new PvActValidCnxPlatfSvcWeb()) ;
				$this->ActValidCnx->ChargeConfig() ;
			}
			protected function RedirigeConnexionReussie()
			{
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$zone = & $this->ZoneParent ;
				$ctn .= '<script type="text/javascript">
PvPlatfSvc.scriptEnCours.SoumetTentativeConnexion = function(btn) {
	var jqBtn = jQuery(btn) ;
	jqBtn.attr("disabled", "disabled") ;
	jQuery.ajax({
		type: "POST",
		url: '.svc_json_encode($this->ActValidCnx->ObtientUrl()).',
		data: '.svc_json_encode($this->NomParamPseudo."=").' + jQuery("#'.$this->NomParamPseudo.'").val() + "&" + '.svc_json_encode($this->NomParamMotPasse."=").' + jQuery("#'.$this->NomParamMotPasse.'").val() + "&" + '.svc_json_encode($this->NomParamSoumetTentative."=").' + '.svc_json_encode($this->ValeurParamSoumetTentative).',
		success: function(data) {
			if(data.erreur.code !== 0)
			{
				jQuery(".erreur").html(data.erreur.message) ;
				jQuery(".erreur").show() ;
			}
			else
			{
				jQuery(".erreur").hide() ;
				PvPlatfSvc.sessionStorage.setKey('.svc_json_encode($zone->NomParamIdMembreSession()).', data.contenu) ;
				PvPlatfSvc.scriptEnCours.confirmeConnexionReussie() ;
			}
			jqBtn.removeAttr("disabled") ;
		} ,
		error: function(jqXHR, textStatus, errorThrown) {
			PvPlatfSvc.boiteDlg.afficheExceptionAjax(jqXHR, textStatus, errorThrown) ;
			jqBtn.removeAttr("disabled") ;
		},
		dataType : "json"
	}) ;
}
PvPlatfSvc.scriptEnCours.SoumetTentativeConnexion = function() {
	var url = '.svc_json_encode($this->ExtraitUrlConnexionReussie()).' ;
	if(url != "") {
		PvPlatfSvc.adressePage.redirige(url) ;
	}
}
</script>'.PHP_EOL ;
				$ctn .= '<div class="user_login_box '.$this->NomClsCSSFormulaireDonnees.'">'.PHP_EOL ;
				$ctn .= '<div align="center">'.PHP_EOL ;
				$ctn .= '<div class="erreur" style="display:none"></div>'.PHP_EOL ;
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<p align="'.$this->AlignBoutonSoumettre.'"><input type="button" value="'.$this->LibelleBoutonSoumettre.'" onclick="PvPlatfSvc.scriptEnCours.SoumetTentativeConnexion(this)" /></p>'.PHP_EOL ;
				}
				$ctn .= '</div>' ;
				$ctn .= '</form>' ;
				return $ctn ;
			}

		}
		
		class PvScriptDeconnexionPlatfSvcWeb extends PvScriptDeconnexionWeb
		{
		}
	}

?>