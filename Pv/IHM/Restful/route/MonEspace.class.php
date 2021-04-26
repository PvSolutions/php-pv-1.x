<?php
	
	if(! defined('PV_ROUTE_MON_ESPACE_RESTFUL'))
	{
		define('PV_ROUTE_MON_ESPACE_RESTFUL', 1) ;
		
		class PvRouteModifPrefsRestful extends PvRouteFiltrableRestful
		{
			public $AutoriserAjout = 0 ;
			public $AutoriserModif = 1 ;
			public $AutoriserSuppr = 0 ;
			public $AutoriserDesact = 0 ;
			protected function PrepareExecution()
			{
				parent::PrepareExecution() ;
				$api = & $this->ApiParent ;
				$mb = & $this->ApiParent->Membership ;
				$bd = & $mb->Database ;
				$this->FltIdMembre = $this->InsereFltSelectFixe("id_connecte", $api->IdMembreConnecte(), $bd->EscapeVariableName($mb->IdMemberColumn)." = <self>") ;
				$this->FltLogin = $this->InsereFltEditHttpCorps("login", $mb->LoginMemberColumn) ;
				if($mb->LoginWithEmail == false)
				{
					$this->FltEmail = $this->InsereFltEditHttpCorps("email", $mb->EmailMemberColumn) ;
				}
				$this->FltNom = $this->InsereFltEditHttpCorps("nom", $mb->LastNameMemberColumn) ;
				$this->FltPrenom = $this->InsereFltEditHttpCorps("prenom", $mb->FirstNameMemberColumn) ;
				$this->FltAdresse = $this->InsereFltEditHttpCorps("adresse", $mb->AddressMemberColumn) ;
				$this->FltContact = $this->InsereFltEditHttpCorps("contact", $mb->ContactMemberColumn) ;
			}
		}
		class PvRouteChangeMotPasseRestful extends PvRouteFiltrableRestful
		{
			public $AutoriserAjout = 0 ;
			public $AutoriserModif = 1 ;
			public $AutoriserSuppr = 0 ;
			public $MsgMotPasseIncorrect = "L'ancien mot de passe est incorrect" ;
			public $AutoriserDesact = 0 ;
			protected function PrepareExecution()
			{
				parent::PrepareExecution() ;
				$api = & $this->ApiParent ;
				$mb = & $this->ApiParent->Membership ;
				$bd = & $mb->Database ;
				$this->FltIdMembre = $this->InsereFltSelectFixe("id_connecte", $api->IdMembreConnecte(), $bd->EscapeVariableName($mb->IdMemberColumn)." = <self>") ;
				if($mb->PasswordMemberExpr != '')
				{
					$this->FltAncMotPasse = $this->InsereFltSelectHttpCorps("ancien_mot_passe", $bd->EscapeVariableName($mb->PasswordMemberColumn)." = <self>") ;
				}
				else
				{
					$this->FltAncMotPasse = $this->InsereFltSelectHttpCorps("ancien_mot_passe", $mb->PasswordMemberExpr."(".$bd->EscapeVariableName($mb->PasswordMemberColumn).") = <self>") ;
				}
				$this->FltNouvMotPasse = $this->InsereFltEditHttpCorps("nouveau_mot_passe", "") ;
			}
			protected function AppliqueEdition()
			{
				$api = & $this->ApiParent ;
				$mb = & $this->ApiParent->Membership ;
				$bd = & $mb->Database ;
				if($mb->ValidateConnection($api->LoginMembreConnecte(), $this->FltAncMotPasse->Lie()))
				{
					$ok = $bd->RunSql(
						"update ".$bd->EscapeTableName($mb->MemberTable)." set ".$bd->EscapeVariableName($mb->PasswordMemberColumn)."=".(($mb->PasswordMemberExpr != '') ? $mb->PasswordMemberExpr."(".$bd->ParamPrefix."mot_passe)" : $bd->ParamPrefix."mot_passe")." where ".$bd->EscapeVariableName($mb->PasswordMemberColumn)."=".$bd->ParamPrefix."id",
						array(
							"mot_passe" => $this->FltNouvMotPasse->Lie(),
							"id" => $this->FltIdMembre->Lie(),
						)
					) ;
					if(! $ok)
					{
						$this->RenseigneException("Exception SQL : ".$bd->ConnectionException) ;
					}
					return $ok ;
				}
				else
				{
					$this->RenseigneErreur($this->MsgMotPasseIncorrect) ;
				}
			}
		}
		class PvRouteDeconnexionRestful extends PvRouteNoyauRestful
		{
			protected function TermineExecution()
			{
				$ok = $this->ApiParent->Auth->SupprimeSession($this->ApiParent) ;
				if($ok)
				{
					$this->ConfirmeSucces("Deconnexion reussie") ;
				}
				else
				{
					$this->RenseigneException() ;
				}
			}
		}
	}
	
?>