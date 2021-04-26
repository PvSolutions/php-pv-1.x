<?php
	
	if(! defined('PV_ACCES_MEMBERSHIP_CORDOVA'))
	{
		define('PV_ACCES_MEMBERSHIP_CORDOVA', 1) ;
		
		class PvMtdSoumetConnexionCordova extends PvMethodeJsonCordova
		{
			protected function ConstruitResultat()
			{
				$script = & $this->ScriptParent ;
				$resultat = & $this->Resultat ;
				$resultat->idMembre = $script->IdMembre ;
				$resultat->lgnMembre = null ;
				$resultat->messageErreur = "" ;
				$script->ExecuteTentativeConnexion() ;
				if($script->TentativeConnexionEnCours == 1)
				{
					if($script->TentativeConnexionValidee == 1)
					{
						$membership = & $script->ZoneParent->Membership ;
						$resultat->idMembre = $script->IdMembre ;
						$resultat->lgnMembre = $membership->FetchMemberRow($resultat->idMembre) ;
						$resultat->lgnMembre["PROFILE_PRIVILEGES"] = $membership->FetchProfileRows($resultat->lgnMembre["PROFILE_ID"]) ;
						$resultat->messageErreur = "" ;
					}
					else
					{
						$resultat->messageErreur = $script->MessageConnexionEchouee ;
					}
				}
				else
				{
					$resultat->messageErreur = $script->MessageErreurValidation ;
				}
			}
		}		
		class PvMtdChargeMembreCordova extends PvMethodeJsonCordova
		{
			protected function ConstruitResultat()
			{
				$resultat = & $this->Resultat ;
				$membership = & $this->ZoneParent->Membership ;
				$resultat->idMembre = _GET_def("idMembre") ;
				$resultat->lgnMembre = null ;
				$resultat->messageErreur = "Non defini" ;
				if($resultat->idMembre != "")
				{
					$resultat->lgnMembre = $membership->FetchMemberRow($resultat->idMembre) ;
					if($resultat->lgnMembre != null)
					{
						if(count($resultat->lgnMembre) > 0)
						{
							if($resultat->lgnMembre["MEMBER_ENABLE"] == false)
							{
								$resultat->lgnMembre = array() ;
								$resultat->messageErreur = 'Utilisateur desactiv&eacute;' ;
							}
							else
							{
								$resultat->lgnMembre["PROFILE_PRIVILEGES"] = $membership->FetchProfileRows($resultat->lgnMembre["PROFILE_ID"]) ;
								$resultat->messageErreur = "" ;
							}
						}
						else
						{
							$resultat->messageErreur = "Utilisateur non trouv&eacute;" ;
						}
					}
					else
					{
						$resultat->messageErreur = ($membership->Database->ConnectionException != '') ? $membership->Database->ConnectionException : "Exception inconnue survenue" ;
					}
				}
				else
				{
					$resultat->messageErreur = "ID Membre non renseign&eacute;" ;
				}
			}
		}
		
		class PvScriptConnexionCordova extends PvScriptConnexionWeb
		{
			public $MessageRecouvreMP = '<br><p>Mot de passe oubli&eacute; ? <a href="${url}">Cliquez ici</a> pour le r&eacute;cup&eacute;rer</p>' ;
			public $MessageInscription = '<br><p>Si vous n\'avez pas de compte, <a href="${url}">Inscrivez-vous</a>.</p>' ;
			public $ColXsLibelle = 5 ;
			public $TagTitre = 'h3' ;
			public $InclureIcones = 0 ;
			public $ClasseCSSCadre = "col-12 col-sm-12 col-md-4 col-lg-4" ;
			public $ClasseCSSErreur = 'alert alert-danger alert-dismissable' ;
			public $ActPrincSoumetConnexion ;
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				$this->ActPrincSoumetConnexion = $this->InsereActionPrinc("soumetConnexion", new PvMtdSoumetConnexionCordova()) ;
				$this->ActPrincChargeMembre = $this->InsereActionPrinc("chargeMembre", new PvMtdChargeMembreCordova()) ;
				$this->ZoneParent->InscritContenuJs('function '.$this->IDInstanceCalc.'_soumetConnexion() {
pvZoneCordova.soumetForm(
	'.svc_json_encode($this->ActPrincSoumetConnexion->ObtientUrl()).',
	jQuery("#'.$this->IDInstanceCalc.'").find(".'.$this->NomClsCSSFormulaireDonnees.'"),
	function(resultat, xhr) {
		resultCnx = JSON.parse(resultat) ;
		if(resultCnx.messageErreur !== "") {
			pvZoneCordova.alerteErreur(pvZoneCordova.htmlEntityDecode(resultCnx.messageErreur)) ;
		}
		else {
			pvZoneCordova.definitMembreConnecte(resultCnx.lgnMembre) ;
			pvZoneCordova.afficheEcran("'.$this->ZoneParent->ScriptAccueil->IDInstanceCalc.'") ;
		}
	}) ;
}') ;
			}
			public function DetermineEnvironnement()
			{
			}
			public function ExecuteTentativeConnexion()
			{
				$this->DetecteTentativeConnexion() ;
				$this->IdMembre = -1 ;
				$this->TentativeConnexionValidee = 0 ;
				if($this->TentativeConnexionEnCours && ! $this->ZoneParent->EstNul($this->ZoneParent->Membership) && $this->ValideTentativeConnexion())
				{
					$this->IdMembre = $this->ZoneParent->Membership->ValidateConnection(trim($this->ValeurParamPseudo), trim($this->ValeurParamMotPasse)) ;
					$this->TentativeConnexionValidee = ($this->IdMembre != $this->ZoneParent->Membership->IdMemberNotFoundValue) ? 1 : 0 ;
				}
				if($this->TentativeConnexionValidee == 0)
				{
					if($this->UtiliserMessageExplicite)
					{
						$this->MessageConnexionEchouee = $this->MessageErreurValidation ;
						switch($this->ZoneParent->Membership->LastValidateError)
						{
							case AkSqlMembership::VALIDATE_ERROR_DB_ERROR :
							{
								$this->MessageConnexionEchouee = 'Exception BD : '.$this->ZoneParent->Membership->Database->ConnectionException ;
							}
							break ;
							case AkSqlMembership::VALIDATE_ERROR_MEMBER_NOT_FOUND :
							{
								$this->MessageConnexionEchouee = $this->MessageMembreNonTrouve ;
							}
							break ;
							case AkSqlMembership::VALIDATE_ERROR_MEMBER_NOT_ENABLED :
							{
								$this->MessageConnexionEchouee = $this->MessageMembreNonActif ;
							}
							break ;
							case AkSqlMembership::VALIDATE_ERROR_PASSWORD_INCORRECT :
							{
								$this->MessageConnexionEchouee = $this->MessageMotPasseIncorrect ;
							}
							break ;
							case AkSqlMembership::VALIDATE_ERROR_AD_AUTH_FAILED :
							{
								$this->MessageConnexionEchouee = $this->MessageAuthADEchoue ;
							}
							break ;
							case AkSqlMembership::VALIDATE_ERROR_AD_SERVER_CONNECT_ERROR :
							{
								$this->MessageConnexionEchouee = $this->MessageAuthServeurADInaccessible ;
							}
							break ;
							case AkSqlMembership::VALIDATE_ERROR_AD_PASSWORD_EMPTY :
							{
								$this->MessageConnexionEchouee = $this->MessageMotPasseIncorrect ;
							}
							break ;
							default :
							{
								if(isset($this->MessagesErreurValidation[$this->ZoneParent->Membership->LastValidateError]))
								{
									$this->MessageConnexionEchouee = $this->MessagesErreurValidation[$this->ZoneParent->Membership->LastValidateError] ;
								}
								else
								{
									$this->MessageConnexionEchouee = $this->MessageExceptionValidation ;
								}
							}
							break ;
						}
					}
					else
					{
						$this->MessageConnexionEchouee = $this->MessageErreurValidation ;
					}
				}
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<form class="user_login_box '.$this->NomClsCSSFormulaireDonnees.'" action="'.$this->UrlSoumetTentativeConnexion().'" method="post">'.PHP_EOL ;
				$ctn .= $this->RenduMessageErreur() ;
				$ctn .= '<fieldset>'.PHP_EOL ;
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<input type="button" value="'.$this->LibelleBoutonSoumettre.'" class="btn btn-lg btn-success btn-block" onclick="'.$this->IDInstanceCalc.'_soumetConnexion()" />'.PHP_EOL ;
				}
				$ctn .= '</fieldset>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				if($this->ZoneParent->AutoriserInscription == 1 && $this->ZoneParent->EstPasNul($this->ZoneParent->ScriptInscription))
				{
					$ctn .= _parse_pattern($this->MessageInscription, array("url" => htmlspecialchars('javascript:pvZoneCordova.afficheEcran("'.$this->ZoneParent->ScriptInscription->IDInstanceCalc.'", '.svc_json_encode($this->ParamsUrlInscription).')'))) ;
				}
				if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptRecouvreMP))
				{
					$ctn .= _parse_pattern($this->MessageRecouvreMP, array("url" => htmlspecialchars('javascript:pvZoneCordova.afficheEcran("'.$this->ZoneParent->ScriptRecouvreMP->IDInstanceCalc.'", '.svc_json_encode($this->ParamsUrlRecouvreMP).')'))) ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
			public function RenduTableauParametres()
			{
				$ctn = '' ;
				$ctn .= '<div class="form-group row">
<label class="col-sm-'.$this->ColXsLibelle.'">'.$this->LibellePseudo.'</label>
<div class="col-sm-'.(12 - $this->ColXsLibelle).'">
'.(($this->InclureIcones) ? '<div class="input-group">
<span class="input-group-addon">
<i class="glyphicon glyphicon-user"></i>
</span>' : '').'<input class="form-control" name="'.$this->NomParamPseudo.'" type="text" value="'.htmlspecialchars($this->ValeurParamPseudo).'" autofocus />
'.(($this->InclureIcones) ? '</div>' : '').'</div>
</div>
<div class="form-group row">
<label class="col-sm-'.$this->ColXsLibelle.'">'.$this->LibelleMotPasse.'</label>
<div class="col-sm-'.(12 - $this->ColXsLibelle).'">
'.(($this->InclureIcones) ? '<div class="input-group">
<span class="input-group-addon">
<i class="glyphicon glyphicon-lock"></i>
</span>' : '').'<input class="form-control" name="'.$this->NomParamMotPasse.'" type="password" value="" />
'.(($this->InclureIcones) ? '</div>' : '').'</div>
</div>' ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
				if($this->InclureIcones)
				{
					$ctn .= '<style type="text/css">
.icon-addon {
    position: relative;
    color: #555;
    display: block;
}
.icon-addon:after,
.icon-addon:before {
    display: table;
    content: " ";
}

.icon-addon:after {
    clear: both;
}

.icon-addon.addon-md .glyphicon,
.icon-addon .glyphicon, 
.icon-addon.addon-md .fa,
.icon-addon .fa {
    position: absolute;
    z-index: 2;
    left: 10px;
    font-size: 14px;
    width: 20px;
    margin-left: -2.5px;
    text-align: center;
    padding: 10px 0;
    top: 1px
}
</style>' ;
				}
				return $ctn ;
			}
		}
		class PvScriptRecouvreMPCordova extends PvScriptRecouvreMPWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireRecouvreMPCordova" ;
			public function RenduSpecifique()
			{
				$ctn = "" ;
				if($this->ConfirmParUrl == 1 && $this->MessageConfirm != "")
				{
					$ctn .= '<p>'.$this->MessageConfirm.'</p>' ;
				}
				else
				{
					$ctn .= PvFormulaireWebDonneesSimple::RenduSpecifique() ;
					$ctn .= '<br />
<p><a href="javascript:pvZoneCordova.afficheEcran(&quot;'.$this->ZoneParent->ScriptConnexion->IDInstanceCalc.'&quot;)">'.$this->LibelleRetourConnexion.'</a></p>' ;
				}
				return $ctn ;
			}
		}
		class PvScriptDeconnexionCordova extends PvScriptDeconnexionWeb
		{
			public $TagTitre = "h3" ;
			public function RenduSpecifique()
			{
				$ctnForm = parent::RenduSpecifique() ;
				$ctn = '<div class="card">
<div class="card-body">
'.$ctnForm.'
</div>
</div>' ;
				return $ctn ;
			}
		}
		
	}
	
?>