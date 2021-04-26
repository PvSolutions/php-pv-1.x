<?php
	
	if(! defined('PV_ROUTE_ACCES_RESTFUL'))
	{
		define('PV_ROUTE_ACCES_RESTFUL', 1) ;
		
		class PvRouteConnexionRestful extends PvRouteFiltrableRestful
		{
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $NomParamLogin = "login" ;
			public $NomParamPassword = "password" ;
			public $NomParamDevice = "device" ;
			public $NomParamSeSouvenir = "remember" ;
			public $MessageErreurValidation = "Nom d'utilisateur / Mot de passe invalide." ;
			public $MessageExceptionValidation = "Une Erreur inconnue est survenue." ;
			public $MessageConnexionEchouee ;
			public $UtiliserMessageExplicite = 1 ;
			public $MessageMotPasseIncorrect = "Le mot de passe est incorrect" ;
			public $MessageMembreNonTrouve = "Utilisateur non trouv&eacute;" ;
			public $MessageMembreNonActif = "Votre compte a &eacute;t&eacute; d&eacute;sactiv&eacute;" ;
			public $MessageAuthADEchoue = "Echec de l'authentification sur le serveur Active Directory" ;
			public $MessageAuthServeurADInaccessible = "Le serveur Active Directory est indisponible" ;
			public $MessagesErreurValidation = array() ;
			public function ExecuteInstructions()
			{
				$this->FltDevice = $this->InsereFltSelectHttpCorps($this->NomParamDevice) ;
				$this->FltLogin = $this->InsereFltSelectHttpCorps($this->NomParamLogin) ;
				$this->FltMotPasse = $this->InsereFltSelectHttpCorps($this->NomParamPassword) ;
				$this->FltSeSouvenir = $this->InsereFltSelectHttpCorps($this->NomParamSeSouvenir) ;
			}
			public function TermineExecution()
			{
				$api = & $this->ApiParent ;
				$membership = & $this->ApiParent->Membership ;
				$this->IdMembre = $this->ApiParent->Auth->IdentifieMembre($this->ApiParent, $this->FltLogin->Lie(), $this->FltMotPasse->Lie()) ;
				if($this->IdMembre <= 0)
				{
					$this->MessageConnexionEchouee = $this->MessageErreurValidation ;
					switch($this->ApiParent->Membership->LastValidateError)
					{
						case AkSqlMembership::VALIDATE_ERROR_DB_ERROR :
						{
							$this->MessageConnexionEchouee = 'Exception BD : '.$this->ApiParent->Membership->Database->ConnectionException ;
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
							if(isset($this->MessagesErreurValidation[$this->ApiParent->Membership->LastValidateError]))
							{
								$this->MessageConnexionEchouee = $this->MessagesErreurValidation[$this->ApiParent->Membership->LastValidateError] ;
							}
							else
							{
								$this->MessageConnexionEchouee = $this->MessageExceptionValidation ;
							}
						}
						break ;
					}
					$this->RenseigneErreur($this->MessageConnexionEchouee) ;
				}
				else
				{
					$token = $this->ApiParent->Auth->CreeSession($this->ApiParent, $this->IdMembre, $this->FltDevice->Lie(), $this->FltSeSouvenir->Lie()) ;
					if($token != '')
					{
						$lgn = $api->Membership->FetchMemberRow($this->IdMembre) ;
						unset($lgn["MEMBER_PASSWORD"]) ;
						$lgn["token"] = $token ;
						$this->ConfirmeData($lgn) ;
					}
					else
					{
						$this->RenseigneException($this->ApiParent->Auth->MessageErreur) ;
					}
				}
			}
		}
		class PvRouteRecouvreMPRestful extends PvRouteSingleRestful
		{
			public function ExecuteInstructions()
			{
			}
		}
		class PvRouteInscriptionRestful extends PvRouteSingleRestful
		{
			public function ExecuteInstructions()
			{
			}
		}
	}
	
?>