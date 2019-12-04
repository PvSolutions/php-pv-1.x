<?php
	
	if(! defined('PV_SCRIPT_SIMPLE_IHM'))
	{
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_IU_SIMPLE'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
		{
			include dirname(__FILE__)."/FournisseurDonnees.class.php" ;
		}
		define('PV_SCRIPT_SIMPLE_IHM', 1) ;
		
		class PvScriptWebSimple extends PvScriptIHMDeBase
		{
			public $EstScriptSession = 0 ;
			public $Titre ;
			public $NomDocumentWeb ;
			public $TitreDocument ;
			public $MotsCleMeta ;
			public $DescriptionMeta ;
			public $ViewportMeta ;
			public $AuteurMeta ;
			public $Chemin = array("") ;
			public $Description = "" ;
			public $ComposantSpecifique = null ;
			public $Composant1 = null ;
			public $Composant2 = null ;
			public $Composant3 = null ;
			public $DetectIconeCorresp = 0 ;
			public $CheminIcone = null ;
			public $UrlsReferantsSurs = array() ;
			public $HotesReferantsSurs = array() ;
			public $RefererHoteLocal = 0 ;
			public $RefererUrlLocale = 0 ;
			public $ScriptsReferantsSurs = array() ;
			public $RefererScriptLocal = 0 ;
			public $UtiliserCorpsDocZone = 1 ;
			public $InclureRenduTitre = 1 ;
			public $InclureRenduDescription = 1 ;
			public $InclureRenduMessageExecution = 1 ;
			public $InclureRenduIcone = 1 ;
			public $InclureRenduChemin = 1 ;
			public $ActiverAutoRafraich = 0 ;
			public $DelaiAutoRafraich = 0 ;
			public $TagTitre = "" ;
			public $ParamsAutoRafraich = array() ;
			public $Imprimable = 0 ;
			public $NomActionImprime = "imprimeScript" ;
			public $ActionImprime ;
			public $MessageExecution ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->MessageExecution = new PvMessageExecutionZoneWeb() ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeActionsAuto() ;
			}
			protected function CreeActionImprime()
			{
				return new PvActionImprimeScript() ;
			}
			protected function ChargeActionsAuto()
			{
				if($this->Imprimable)
				{
					$this->ActionImprime = $this->InsereActionAvantRendu($this->NomActionImprime, $this->CreeActionImprime()) ;
				}
			}
			public function DoitAutoRafraich()
			{
				return $this->ActiverAutoRafraich && $this->DelaiAutoRafraich > 0;
			}
			public function EstBienRefere()
			{
				return PvVerificateurReferantsSursWeb::Valide($this) ;
			}
			public function & InsereActionPrinc($nomAction, $action)
			{
				$actionResult = $this->ZoneParent->InsereActionPrinc($this->NomElementZone."_".$nomAction, $action) ;
				$actionResult->NomElementScript = $nomAction ;
				$actionResult->ScriptParent = & $this ;
				return $actionResult ;
			}
			public function & InsereActionAvantRendu($nomAction, $action)
			{
				$this->InscritActionAvantRendu($nomAction, $action) ;
				return $action ;
			}
			public function & InsereActionApresRendu($nomAction, $action)
			{
				$this->InscritActionApresRendu($nomAction, $action) ;
				return $action ;
			}
			public function InscritActionAvantRendu($nomAction, & $action)
			{
				$this->ZoneParent->ActionsAvantRendu[$nomAction] = & $action ;
				$action->AdopteScript($nomAction, $this) ;
			}
			public function InscritActionApresRendu($nomAction, & $action)
			{
				$this->ZoneParent->ActionsApresRendu[$nomAction] = & $action ;
				$action->AdopteScript($nomAction, $this) ;
			}
			public function InvoqueAction($valeurAction, $params=array(), $valeurPost=array(), $async=1)
			{
				return $this->ZoneParent->InvoqueAction($valeurAction, $params, $valeurPost, $async) ;
			}
			public function & CreeFiltreRef($nom, & $filtreRef)
			{
				$filtre = new PvFiltreDonneesRef() ;
				$filtre->Source = & $filtreRef ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreDonneesFixe() ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreCookie($nom)
			{
				$filtre = new PvFiltreDonneesCookie() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreSession($nom)
			{
				$filtre = new PvFiltreDonneesSession() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreMembreConnecte($nom, $nomParamLie='')
			{
				$filtre = new PvFiltreDonneesMembreConnecte() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->NomParametreLie = $nomParamLie ;
				return $filtre ;
			}
			public function & CreeFiltreHttpUpload($nom, $cheminDossierDest="")
			{
				$filtre = new PvFiltreDonneesHttpUpload() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CheminDossier = $cheminDossierDest ;
				return $filtre ;
			}
			public function & CreeFiltreHttpGet($nom, $exprDonnees="")
			{
				$filtre = new PvFiltreDonneesHttpGet() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ExpressionDonnees = $exprDonnees ;
				return $filtre ;
			}
			public function & CreeFiltreHttpPost($nom, $exprDonnees="")
			{
				$filtre = new PvFiltreDonneesHttpPost() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ExpressionDonnees = $exprDonnees ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom, $exprDonnees="")
			{
				$filtre = new PvFiltreDonneesHttpRequest() ;
				$filtre->AdopteScript($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ExpressionDonnees = $exprDonnees ;
				return $filtre ;
			}
			public function PrepareRendu()
			{
			}
			public function RenduIcone()
			{
				$ctn = '' ;
				if($this->ZoneParent->InclureRenduIcone && $this->InclureRenduTitre)
				{
					$cheminIcone = ($this->CheminIcone != '' && file_exists($this->CheminIcone)) ? $this->CheminIcone : $this->ZoneParent->CheminIconeScript ;
					if($cheminIcone != '')
					{
						$ctn .= '<img src="'.$cheminIcone.'" height="22" />' ;
					}
				}
				return $ctn ;
			}
			public function RenduTitre()
			{
				return $this->ZoneParent->RenduTitre() ;
			}
			public function ObtientTitreDocument()
			{
				return $this->TitreDocument ;
			}
			public function RenduChemin()
			{
				if(! $this->ZoneParent->InclureRenduChemin || ! $this->InclureRenduChemin)
				{
					return '' ;
				}
			}
			public function DefinitMessageExecution($statut, $contenu)
			{
				$this->MessageExecution->Statut = $statut ;
				$this->MessageExecution->Contenu = $contenu ;
			}
			public function ObtientMessageExecution()
			{
				$msg = $this->MessageExecution ;
				if($msg->NonRenseigne() || $msg->EstVide())
				{
					$msg = $this->ZoneParent->RestaureMessageExecutionSession() ;
				}
				return $msg ;
			}
			public function RenduMessageExecution()
			{
				if(! $this->ZoneParent->InclureRenduMessageExecution || ! $this->InclureRenduMessageExecution)
				{
					return '' ;
				}
				$msg = $this->ObtientMessageExecution() ;
				if($msg->NonRenseigne() || $msg->EstVide())
				{
					return '' ;
				}
				$classeCSSMsgExecSucces = $this->ZoneParent->ClasseCSSMsgExecSucces ;
				$classeCSSMsgExecErreur = $this->ZoneParent->ClasseCSSMsgExecErreur ;
				$ctn = '<div class="'.(($msg->Succes()) ? $classeCSSMsgExecSucces : $classeCSSMsgExecErreur).'">'.$msg->Contenu.'</div>' ;
				return $ctn ;
			}
			public function RenduDescription()
			{
				if(! $this->ZoneParent->InclureRenduDescription || ! $this->InclureRenduDescription)
				{
					return '' ;
				}
			}
			public function RenduSpecifique()
			{
			}
			public function RenduComposant1()
			{
			}
			public function RenduComposant2()
			{
			}
			public function RenduComposant3()
			{
			}
			public function RenduDispositif()
			{
				return $this->RenduDispositifBrut() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduChemin().PHP_EOL ;
				$ctn .= $this->RenduTitre().PHP_EOL ;
				$ctn .= $this->RenduMessageExecution().PHP_EOL ;
				$ctn .= $this->RenduDescription().PHP_EOL ;
				$ctn .= $this->RenduSpecifique() ;
				return $ctn ;
			}
			public function ObtientUrl()
			{
				return $this->ZoneParent->ObtientUrlScript($this->NomElementZone) ;
			}
			public function ObtientUrlParam($params=array())
			{
				return $this->ZoneParent->ObtientUrlScript($this->NomElementZone, $params) ;
			}
			public function ObtientUrlFmt($params=array(), $autresParams=array())
			{
				$url = $this->ZoneParent->ObtientUrlScript($this->NomElementZone, $autresParams) ;
				foreach($params as $nom => $val)
				{
					$url .= '&'.urlencode($nom).'='.$val ;
				}
				return $url ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				if($this->DetectIconeCorresp || $this->ZoneParent->DetectIconeCorresp)
				{
					$cheminIcone = $this->ZoneParent->CheminDossierIconeCorresp.'/'.$this->NomElementZone.'.'.$this->ZoneParent->ExtIconeCorresp ;
					if($this->CheminIcone == '' && file_exists($cheminIcone))
					{
						$this->CheminIcone = $cheminIcone ;
					}
				}
			}
			public function ImpressionEnCours()
			{
				return $this->EstPasNul($this->ZoneParent) && $this->ZoneParent->ImpressionEnCours() ;
			}
		}
		
		class PvScriptWebDonneesBase extends PvScriptWebSimple
		{
		}
		class PvFormulaireWebDonneesSimple extends PvScriptWebDonneesBase
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireDonneesHtml" ;
			public $ComposantFormulaireDonnees = null ;
			public $InitComposantEnvironnement = 1 ;
			public function DetermineEnvironnement()
			{
				if($this->InitComposantEnvironnement)
				{
					$this->InitComposantFormulaireDonnees() ;
					if($this->EstPasNul($this->ComposantFormulaireDonnees))
						$this->ChargeConfigComposantFormulaireDonnees() ;
				}
			}
			protected function InitComposantFormulaireDonnees()
			{
				$nomClasse = $this->NomClasseFormulaireDonnees ;
				if(class_exists($nomClasse))
				{
					$this->ComposantFormulaireDonnees = new $nomClasse() ;
					$this->ComposantFormulaireDonnees->AdopteScript("specifique", $this) ;
					// print get_class($this->ComposantFormulaireDonnees->ScriptParent) ;
				}
				else
				{
					$this->ComposantFormulaireDonnees = null ;
				}
			}
			protected function ChargeConfigComposantFormulaireDonnees()
			{
				$this->ComposantFormulaireDonnees->ChargeConfig() ;
			}
			public function RenduSpecifique()
			{
				if(! $this->InitComposantEnvironnement)
				{
					$this->InitComposantFormulaireDonnees() ;
					if(! $this->EstNul($this->ComposantFormulaireDonnees))
					{
						$this->ChargeConfigComposantFormulaireDonnees() ;
					}
				}
				$ctn = '' ;
				if(! $this->EstNul($this->ComposantFormulaireDonnees))
				{
					$ctn .= $this->ComposantFormulaireDonnees->RenduDispositif() ;
				}
				else
				{
					$ctn .= '-- composant non initialise --' ;
				}
				// print_r($this->ComposantFormulaireDonnees->FournisseurDonnees) ;
				return $ctn ;
			}
		}
		class PvFormulaireAjoutDonneesSimple extends PvFormulaireWebDonneesSimple
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutDonneesHtml" ;
		}
		class PvFormulaireModifDonneesSimple extends PvFormulaireWebDonneesSimple
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifDonneesHtml" ;
		}
		class PvFormulaireSupprDonneesSimple extends PvFormulaireWebDonneesSimple
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprDonneesHtml" ;
		}
		
		class PvScriptTableauDonneesSimple extends PvScriptWebDonneesBase
		{
			public $NomClasseTableauDonnees = "PvTableauDonneesHtml" ;
			public $ComposantTableauDonnees = null ;
			public $InitComposantEnvironnement = 1 ;
			public function DetermineEnvironnement()
			{
				if($this->InitComposantEnvironnement)
				{
					$this->InitComposantTableauDonnees() ;
					if($this->EstPasNul($this->ComposantTableauDonnees))
						$this->ChargeConfigComposantTableauDonnees() ;
				}
			}
			protected function InitComposantTableauDonnees()
			{
				$nomClasse = $this->NomClasseTableauDonnees ;
				if(class_exists($nomClasse))
				{
					$this->ComposantTableauDonnees = new $nomClasse() ;
					$this->ComposantTableauDonnees->AdopteScript("specifique", $this) ;
				}
				else
				{
					$this->ComposantTableauDonnees = null ;
				}
			}
			protected function ChargeConfigComposantTableauDonnees()
			{
				$this->ComposantTableauDonnees->ChargeConfig() ;
			}
			public function RenduSpecifique()
			{
				if($this->InitComposantEnvironnement == 0)
				{
					$this->InitComposantTableauDonnees() ;
				}
				if(! $this->EstNul($this->ComposantTableauDonnees))
				{
					if($this->InitComposantEnvironnement == 0)
					{
						$this->ChargeConfigComposantTableauDonnees() ;
					}
					$ctn = $this->ComposantTableauDonnees->RenduDispositif() ;
					return $ctn ;
				}
				else
				{
					return '-- composant non initialise --' ;
				}
			}
		}
		
		class PvScriptConnexionWeb extends PvScriptWebSimple
		{
			public $Titre = "Connexion" ;
			public $TitreDocument = "Connexion" ;
			public $LibellePseudo = "Nom d'utilisateur" ;
			public $NomParamPseudo = "pseudo" ;
			public $ValeurParamPseudo = "" ;
			public $LibelleMotPasse = "Mot de passe" ;
			public $NomParamMotPasse = "motDePasse" ;
			public $ValeurParamMotPasse = "" ;
			public $NomParamSoumetTentative = "tentativeConnexion" ;
			public $NomClsCSSFormulaireDonnees = "FormulaireConnexion" ;
			public $ValeurParamSoumetTentative = 1 ;
			public $TentativeConnexionEnCours = 0 ;
			public $TentativeConnexionValidee = 0 ;
			public $UrlConnexionReussie = "" ;
			public $UrlConnexionEchouee = "" ;
			public $NomScriptConnexionReussie = "accueil" ;
			public $NomScriptConnexionEchouee = "" ;
			public $MessageConnexionReussie = 'Bienvenue, ${PSEUDO}. Vous vous &ecirc;tes connect&eacute; avec succ&egrave;s' ;
			public $IdMembre = -1 ;
			public $NecessiteMembreConnecte = 0 ;
			public $AfficherBoutonSoumettre = 1 ;
			public $AlignBoutonSoumettre = "center" ;
			public $AfficherMessageErreur = 1 ;
			public $LibelleBoutonSoumettre = "Se connecter" ;
			public $MessageConnexionEchouee = "" ;
			public $MessageErreurValidation = "Nom d'utilisateur / Mot de passe invalide." ;
			public $MessageExceptionValidation = "Une Erreur inconnue est survenue." ;
			public $UtiliserMessageExplicite = 1 ;
			public $MessageMotPasseIncorrect = "Le mot de passe est incorrect" ;
			public $MessageMembreNonTrouve = "Utilisateur non trouv&eacute;" ;
			public $MessageMembreNonActif = "Votre compte a &eacute;t&eacute; d&eacute;sactiv&eacute;" ;
			public $MessageAuthADEchoue = "Echec de l'authentification sur le serveur Active Directory" ;
			public $MessageAuthServeurADInaccessible = "Le serveur Active Directory est indisponible" ;
			public $AutoriserUrlsRetour = 0 ;
			public $ValeurUrlRetour = "" ;
			public $NomParamUrlRetour = "urlRetour" ;
			public $ClasseCSSErreur = "" ;
			public $MessageAccesUrlRetour = "Vous devez vous connecter pour avoir acc&egrave;s &agrave; cette page." ;
			public $ParamsUrlInscription = array() ;
			public $ParamsUrlRecouvreMP = array() ;
			public $MessagesErreurValidation = array() ;
			protected function DetecteTentativeConnexion()
			{
				$this->TentativeConnexionEnCours = 0 ;
				if(isset($_POST[$this->NomParamSoumetTentative]))
				{
					$this->TentativeConnexionEnCours = ($_POST[$this->NomParamSoumetTentative] == $this->ValeurParamSoumetTentative) ? 1 : 0 ;
					$this->ValeurParamPseudo = (isset($_POST[$this->NomParamPseudo])) ? $_POST[$this->NomParamPseudo] : "" ;
					$this->ValeurParamMotPasse = (isset($_POST[$this->NomParamMotPasse])) ? $_POST[$this->NomParamMotPasse] : "" ;
				}
				if($this->AutoriserUrlsRetour == 1)
				{
					$this->ValeurUrlRetour = (isset($_GET[$this->NomParamUrlRetour])) ? $_GET[$this->NomParamUrlRetour] : "" ;
					if($this->ValeurUrlRetour != '' && validate_url_format($this->ValeurUrlRetour) == 0)
					{
						$this->ValeurUrlRetour = '' ;
					}
				}
			}
			protected function UrlSoumetTentativeConnexion()
			{
				return $this->ObtientUrl().(($this->AutoriserUrlsRetour == 1) ? '&'.$this->NomParamUrlRetour.'='.urlencode($this->ValeurUrlRetour) : '') ;
			}
			protected function RenduMessageErreur()
			{
				$ctn = '' ;
				$msgErreur = '' ;
				if($this->TentativeConnexionEnCours && $this->TentativeConnexionValidee == 0)
				{
					$msgErreur = $this->MessageConnexionEchouee ;
				}
				elseif($this->AutoriserUrlsRetour == 1 && $this->ValeurUrlRetour != '')
				{
					$msgErreur = $this->MessageAccesUrlRetour ;
				}
				if($msgErreur != '')
				{
					$ctn .= '<div class="erreur'.(($this->ClasseCSSErreur != '') ? ' '.$this->ClasseCSSErreur : '').'">'.$msgErreur.'</div>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function ValideTentativeConnexion()
			{
				return 1 ;
			}
			public function DetermineEnvironnement()
			{
				$this->DetecteTentativeConnexion() ;
				$this->IdMembre = -1 ;
				$this->TentativeConnexionValidee = 0 ;
				if($this->TentativeConnexionEnCours && ! $this->ZoneParent->EstNul($this->ZoneParent->Membership) && $this->ValideTentativeConnexion())
				{
					$this->IdMembre = $this->ZoneParent->Membership->ValidateConnection(trim($this->ValeurParamPseudo), trim($this->ValeurParamMotPasse)) ;
					$this->TentativeConnexionValidee = ($this->IdMembre != $this->ZoneParent->Membership->IdMemberNotFoundValue) ? 1 : 0 ;
					// print_r($this->ZoneParent->Membership->Database) ;
					// print_r($this->IdMembre.' jjj') ;
					// exit ;
				}
				if($this->TentativeConnexionValidee == 1)
				{
					$this->ZoneParent->Membership->LogonMember($this->IdMembre) ;
					$this->RedirigeConnexionReussie() ;
				}
				else
				{
					$url = '' ;
					if($this->NomScriptConnexionEchouee != '' && isset($this->ZoneParent->Scripts[$this->NomScriptConnexionEchouee]))
					{
						$url = $this->ZoneParent->Scripts[$this->NomScriptConnexionEchouee]->ObtientUrl() ;
					}
					elseif($this->UrlConnexionEchouee != '')
					{
						$url = $this->UrlConnexionEchouee ;
					}
					if($url != '')
					{
						$url = update_url_params(array('connexionEchouee' => 1)) ;
						redirect_to($url) ;
					}
					elseif($this->UtiliserMessageExplicite)
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
			protected function RedirigeConnexionReussie()
			{
				$url = $this->ExtraitUrlConnexionReussie() ;
				if($url != '')
				{
					redirect_to($url) ;
				}
			}
			protected function ExtraitUrlConnexionReussie()
			{
				$url = '' ;
				if($this->AutoriserUrlsRetour == 1 && $this->ValeurUrlRetour != "")
				{
					return $this->ValeurUrlRetour ;
				}
				if($this->NomScriptConnexionReussie != '' && isset($this->ZoneParent->Scripts[$this->NomScriptConnexionReussie]))
				{
					$url = $this->ZoneParent->Scripts[$this->NomScriptConnexionReussie]->ObtientUrl() ;
				}
				elseif($this->UrlConnexionReussie != '')
				{
					$url = $this->UrlConnexionReussie ;
				}
				return $url ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->TentativeConnexionValidee)
				{
					$message = _parse_pattern(
						$this->MessageConnexionReussie,
						array(
							"PSEUDO" => $this->ValeurParamPseudo
						)
					) ;
					$ctn .= '<p>'.htmlentities($message).'</p>' ;
				}
				else
				{
					$ctn .= parent::RenduDispositifBrut() ;
				}
				return $ctn ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<form class="user_login_box '.$this->NomClsCSSFormulaireDonnees.'" action="'.$this->UrlSoumetTentativeConnexion().'" method="post">'.PHP_EOL ;
				$ctn .= '<div align="center">'.PHP_EOL ;
				$ctn .= $this->RenduMessageErreur() ;
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<p align="'.$this->AlignBoutonSoumettre.'"><input type="submit" value="'.$this->LibelleBoutonSoumettre.'" /></p>'.PHP_EOL ;
				}
				$ctn .= '</div>' ;
				$ctn .= '</form>' ;
				return $ctn ;
			}
			public function RenduTableauParametres()
			{
				$ctn = '' ;
				$ctn .= '<table align="center" cellspacing="0" cellpadding="4" class="user_login_form">
			<tr>
				<td width="50%" align="left">
					<label for="'.$this->NomParamPseudo.'">'.$this->LibellePseudo.'</label>
				</td>
				<td width="*" align="left">
					<input type="text" name="'.$this->NomParamPseudo.'" id="'.$this->NomParamPseudo.'" value="'.htmlentities($this->ValeurParamPseudo).'" />
				</td>
			</tr>
			<tr>
				<td align="left">
					<label for="'.$this->NomParamMotPasse.'">'.$this->LibelleMotPasse.'</label>
				</td>
				<td align="left">
					<input type="password" name="'.$this->NomParamMotPasse.'" id="'.$this->NomParamMotPasse.'" value="" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
				return $ctn ;
			}
		}
		class PvScriptRecouvreMPWeb extends PvFormulaireWebDonneesSimple
		{
			public $Titre = "Mot de passe oubli&eacute;" ;
			public $TitreDocument = "Mot de passe oubli&eacute;" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireRecouvreMPMS" ;
			public $EmailEnvoiRecouvr = '' ;
			public $SujetMailSuccesRecouvr = 'R&eacute;initialisation de mot de passe' ;
			public $CorpsMailSuccesRecouvr = '<p>Votre mot de passe a &eacute;t&eacute; r&eacute;initialis&eacute; avec succ&egrave;s :</p>
<div><b>Login :</b> ${login}</div>
<div><b>Mot de passe :</b> ${motPasse}</div>
<p>Cordialement.</p>' ;
			public $SujetMailDemRecouvr = 'Réinitialisation de mot de passe' ;
			public $CorpsMailDemRecouvr = '<p>Vous avez demand&eacute; de r&eacute;initialiser votre mot de passe.</p>
<p>Veuillez cliquer <a href="${url}">ICI</a> pour confirmer.</p>
<p>Cordialement</p>' ;
			public $MessageSuccesEnvoiMail = "Les instructions &agrave; suivre pour recup&eacute;rer votre mot de passe vous ont &eacute;t&eacute; envoy&eacute;es par mail" ;
			public $MessageErreurEnvoiMail = "Impossible d'envoyer un mail de confirmation." ;
			public $MessageSuccesDansMail = "Votre mot de passe vous a &eacute;t&eacute; envoy&eacute; par mail" ;
			public $MessageSuccesAffiche = "Voici votre nouveau mot de passe : " ;
			public $LibelleRetourConnexion = "Retour &agrave; la page de connexion" ;
			public $MessageErreur = "Invalide Nom d'utilisateur / Email" ;
			public $EnvoiParMail = 0 ;
			public $ConfirmParUrl = 0 ;
			public $NomParamLogin = "login" ;
			public $NomParamEmail = "email" ;
			public $NomParamConfirm = "confirm" ;
			public $MessageConfirm = "" ;
			public $LibelleCmdExecuter = "R&eacute;cup&eacute;rer" ;
			public $MotPasseGenere ;
			public $MessageExceptionRecouvr ;
			protected $DemandeConfirm = 0 ;
			public $LgnMembreRecouvr = array() ;
			protected function GenereNouvMotPasse()
			{
				return uniqid() ;
			}
			protected function ExtraitLgnMembre(& $filtres)
			{
				$membership = & $this->ZoneParent->Membership ;
				$basedonnees = & $membership->Database ;
				$sql = "select * from ".$membership->MemberTable.' MEMBER_TABLE where '.$basedonnees->EscapeFieldName('MEMBER_TABLE', $membership->LoginMemberColumn).'='.$basedonnees->ParamPrefix.'Login' ;
				$params = array('Login' => $filtres[0]->Lie()) ;
				if($membership->LoginWithEmail == 0)
				{
					$sql .= ' and '.$basedonnees->EscapeFieldName('MEMBER_TABLE', $membership->EmailMemberColumn).'='.$basedonnees->ParamPrefix.'Email' ;
					$params["Email"] = $filtres[1]->Lie() ;
				}
				$ligneMembre = $basedonnees->FetchSqlRow($sql, $params) ;
				return $ligneMembre ;
			}
			protected function ExtraitEmailMembre($ligneMembre)
			{
				$membership = & $this->ZoneParent->Membership ;
				return ($membership->LoginWithEmail == 1) ? $ligneMembre[$membership->LoginMemberColumn] : $ligneMembre[$membership->EmailMemberColumn] ;
			}
			public function ReinitMotPasse(& $filtres)
			{
				$ligneMembre = $this->ExtraitLgnMembre($filtres) ;
				$ok = 0 ;
				$membership = & $this->ZoneParent->Membership ;
				$basedonnees = & $membership->Database ;
				if(is_array($ligneMembre) && count($ligneMembre) > 0)
				{
					$this->MotPasseGenere = $this->GenereNouvMotPasse() ;
					$ligneMembre["motPasse"] = $this->MotPasseGenere ;
					$ligneMembre["login"] = $ligneMembre[$membership->LoginMemberColumn] ;
					$nouvValeurs = array($membership->PasswordMemberColumn => $this->MotPasseGenere) ;
					if($membership->MustChangePasswordMemberColumn != "")
					{
						$nouvValeurs[$membership->MustChangePasswordMemberColumn] = $membership->MustChangePasswordMemberTrueValue ;
					}
					if($membership->PasswordMemberExpr != "")
					{
						$nouvValeurs[$basedonnees->ExprKeyName] = array(
							$membership->PasswordMemberColumn => $membership->PasswordMemberExpr.'('.$basedonnees->ExprParamPattern.')'
						) ;
					}
					$ok = $basedonnees->UpdateRow(
						$membership->MemberTable,
						$nouvValeurs,
						$membership->IdMemberColumn.' = '.$basedonnees->ParamPrefix.'Id',
						array('Id' => $ligneMembre[$membership->IdMemberColumn])
					) ;
				}
				else
				{
					$ok = 0 ;
				}
				if($ok && $this->EnvoiParMail == 1)
				{
					$email = $this->ExtraitEmailMembre($ligneMembre) ;
					$sujetMail = _parse_pattern($this->SujetMailSuccesRecouvr, $ligneMembre) ;
					$corpsMail = _parse_pattern($this->CorpsMailSuccesRecouvr, $ligneMembre) ;
					send_html_mail($email, $sujetMail, $corpsMail, $this->EmailEnvoiRecouvr) ;
				}
				$this->LgnMembreRecouvr = $ligneMembre ;
				return $ok ;
			}
			public function EnvoiMailConfirm(& $filtres)
			{
				$ligneMembre = $this->ExtraitLgnMembre($filtres) ;
				$ok = 1 ;
				$membership = & $this->ZoneParent->Membership ;
				$basedonnees = & $membership->Database ;
				if(! is_array($ligneMembre) || count($ligneMembre) == 0)
				{
					$ok = 0 ;
				}
				else
				{
					$email = $this->ExtraitEmailMembre($ligneMembre) ;
					$sujetMail = _parse_pattern($this->SujetMailDemRecouvr, $ligneMembre) ;
					$corpsMail = _parse_pattern($this->CorpsMailDemRecouvr, $ligneMembre) ;
					$ok = send_html_mail($email, $sujetMail, $corpsMail, $this->EmailEnvoiRecouvr) ;
					$ok = 1 ;
				}
				return $ok ;
			}
			public function EnvoiMailDem(& $filtres)
			{
				$ligneMembre = $this->ExtraitLgnMembre($filtres) ;
				$ok = 1 ;
				$membership = & $this->ZoneParent->Membership ;
				$basedonnees = & $membership->Database ;
				if(! is_array($ligneMembre) || count($ligneMembre) == 0)
				{
					$ok = 0 ;
				}
				else
				{
					$email = $this->ExtraitEmailMembre($ligneMembre) ;
					$ligneMembre["url"] = $this->ObtientUrl()."&".$this->NomParamLogin."=".urlencode($ligneMembre[$membership->LoginMemberColumn])."&".$this->NomParamEmail."=".urlencode(($membership->LoginWithEmail == 1) ? $ligneMembre[$membership->LoginMemberColumn] : $ligneMembre[$membership->EmailMemberColumn])."&".$this->NomParamConfirm."=1" ;
					$ligneMembre["login"] = $ligneMembre[$membership->LoginMemberColumn] ;
					$sujetMail = _parse_pattern($this->SujetMailDemRecouvr, $ligneMembre) ;
					$corpsMail = _parse_pattern($this->CorpsMailDemRecouvr, $ligneMembre) ;
					// echo $email."<br>".$sujetMail."<br>".$corpsMail."<br>".$this->EmailEnvoiRecouvr."<br>" ;
					$ok = send_html_mail($email, $sujetMail, $corpsMail, $this->EmailEnvoiRecouvr) ;
				}
				return $ok ;
			}
			public function DetermineEnvironnement()
			{
				$this->DetermineConfirm() ;
				parent::DetermineEnvironnement() ;
			}
			protected function DetermineConfirm()
			{
				if($this->ConfirmParUrl == 0 || _GET_def($this->NomParamConfirm) != 1)
				{
					return ;
				}
				$this->DemandeConfirm = 1 ;
				$filtres = array($this->CreeFiltreHttpGet($this->NomParamLogin), $this->CreeFiltreHttpGet($this->NomParamEmail)) ;
				$ok = $this->ReinitMotPasse($filtres) ;
				if($ok)
				{
					if($this->EnvoiParMail == 1)
					{
						$this->MessageConfirm = $this->MessageSuccesDansMail ;
					}
					else
					{
						$this->MessageConfirm = $this->MessageSuccesAffiche.' '.$this->MotPasseGenere ;
					}
				}
				else
				{
					$this->MessageConfirm = $this->MessageErreur ;
				}
			}
			protected function ChargeConfigComposantFormulaireDonnees()
			{
				if($this->LibelleCmdExecuter != '')
				{
					$this->ComposantFormulaireDonnees->LibelleCommandeExecuter = $this->LibelleCmdExecuter ;
				}
				parent::ChargeConfigComposantFormulaireDonnees() ;
			}
			public function RenduSpecifique()
			{
				$ctn = "" ;
				if($this->ConfirmParUrl == 1 && $this->MessageConfirm != "")
				{
					$ctn .= '<p>'.$this->MessageConfirm.'</p>' ;
				}
				else
				{
					$ctn .= parent::RenduSpecifique() ;
					$ctn .= '<br />
<p><a href="'.$this->ZoneParent->ScriptConnexion->ObtientUrl().'">'.$this->LibelleRetourConnexion.'</a></p>' ;
				}
				return $ctn ;
			}
		}
		class PvScriptDeconnexionWeb extends PvScriptWebSimple
		{
			public $Titre = "D&eacute;connexion" ;
			public $TitreDocument = "D&eacute;connexion" ;
			public $UrlDeconnexionReussie = "" ;
			public $NomScriptDeconnexionReussie = "" ;
			public $MessageDeconnexionReussie = "Vous avez &eacute;t&eacute; d&eacute;connect&eacute; avec succ&egrave;s." ;
			public $NecessiteMembreConnecte = 1 ;
			public $MessageRetourAccueil = "Retour &agrave; la page d'accueil" ;
			public function DetermineEnvironnement()
			{
				if(! $this->ZoneParent->EstNul($this->ZoneParent->Membership) && $this->ZoneParent->PossedeMembreConnecte())
				{
					$this->ZoneParent->Membership->LogoutMember($this->ZoneParent->Membership->MemberLogged->Id) ;
				}
				$url = '' ;
				if($this->NomScriptDeconnexionReussie != '' && isset($this->ZoneParent->Scripts[$this->NomScriptDeconnexionReussie]))
				{
					$url = $this->ZoneParent->Scripts[$this->NomScriptDeconnexionReussie] ;
				}
				elseif($this->UrlDeconnexionReussie != '')
				{
					$url = $this->UrlDeconnexionReussie ;
				}
				if($url != '')
				{
					redirect_to($url) ;
				}
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->MessageDeconnexionReussie != '')
				{
					$ctn .= '<p>'.$this->MessageDeconnexionReussie.'</p>' ;
				}
				$ctn .= '<p align="center"><a href="'.$this->ZoneParent->ObtientUrl().'">'.$this->MessageRetourAccueil.'</a></p>' ;
				return $ctn ;
			}
		}
		class PvScriptAjoutMembreMSWeb extends PvFormulaireAjoutDonneesSimple
		{
			public $Titre = "Ajouter un membre" ;
			public $TitreDocument = "Ajouter un membre" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutMembreMS" ;
		}
		class PvScriptInscriptionWeb extends PvFormulaireAjoutDonneesSimple
		{
			public $Titre = "Inscription" ;
			public $TitreDocument = "Inscription" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireInscriptionMembreMS" ;
			public $NomClsCSSFormulaireDonnees = "FormulaireInscription" ;
			public $Securiser = 0 ;
			public $FltCaptcha ;
			public $CompCaptcha ;
			public $NomParamCaptcha = "image_securise" ;
			public $LibelleFltCaptcha = "Code de s&eacute;curit&eacute;" ;
			public $IdProfilsAcceptes = array() ;
			public $LibelleCmdExecuter = "S'inscrire" ;
			public $IdProfilParDefaut = 1 ;
			public $ValeurActiveParDefaut = 0 ;
			public $InclureMsgConnexion = 1 ;
			public $AlignMsgConnexion = "center" ;
			public $FormatMsgConnexion = 'D&eacute;j&agrave; inscrit ? <a href="${url}">Connectez-vous !</a>' ;
			public $ActiverConfirmMail = 0 ;
			public $MsgSuccesConfirmMail = '<b>${login_member}</b>, Votre inscription a &eacute;t&eacute; confirm&eacute;e. Vous pouvez d&eacute;sormais vous connecter sur le site web' ;
			public $EmailEnvoiConfirm = 'inscriptions@localhost' ;
			public $MsgErreurConfirmMail = 'Votre inscription n\'a pas &eacute;t&eacute; confirm&eacute;e. Veuillez v&eacute;rifier dans votre bo&icirc;te mail.' ;
			public $SujetMailConfirm = 'Confirmation inscription membre' ;
			public $CorpsMailConfirm = '<p>Bonjour ${login_member},</p>
<p>Veuillez cliquer sur ce lien pour confirmer votre inscription.</p>
<p><a href="${url}">${url}</a></p>
Cordialement' ;
			public $EnvoiMailSucces = 0 ;
			public $EnvoiMailSuccesConfirm = 0 ;
			public $SujetMailSuccesConfirm = 'Compte ${login_member} confirme' ;
			public $CorpsMailSuccesConfirm = '<p>Bonjour ${login_member},</p>
<p>Votre compte a ete bien confirme. Bienvenue sur notre site web.</p>
Cordialement' ;
			public $MsgSuccesCmdExecuter = 'Votre inscription a &eacute;t&eacute; prise en compte' ;
			public $MsgSuccesEnvoiMailConfirm = 'Veuillez v&eacute;rifier votre bo&icirc;te e-mail pour confirmer votre inscription.' ;
			protected $NomColConfirmMail = "enable_confirm_mail" ;
			protected $NomColCodeConfirmMail = "code_confirm_mail" ;
			protected $_FltConfirmMail ;
			protected $_FltCodeConfirm ;
			protected $NomParamLoginConfirm = "login_confirm" ;
			protected $NomParamCodeConfirm = "code_confirm" ;
			protected $NomParamEmailConfirm = "email_confirm" ;
			protected $DemandeConfirmMail = -1 ;
			protected $MailSuccesConfirmEnvoye = false ;
			protected $LgnMembreConfirm = null ;
			public $Detaille = 1 ;
			protected $ValeurDefautNomMembre = "Utilisateur" ;
			protected $ValeurDefautPrenomMembre = "Sans nom" ;
			protected $ValeurDefautAdresseMembre = "" ;
			protected $ValeurDefautContactMembre = "" ;
			protected $ConfirmMailSoumis = 0 ;
			public $AutoriserUrlsRetour = 0 ;
			public $NomParamUrlRetour = "urlRetour" ;
			public $ValeurUrlRetour = "" ;
			public $ConnecterNouveauMembre = 0 ;
			public $UrlAutoConnexionMembre = "?" ;
			public function DetermineEnvironnement()
			{
				$this->DetermineUrlRetour() ;
				parent::DetermineEnvironnement() ;
				$this->DetermineConfirm() ;
			}
			protected function DetermineUrlRetour()
			{
				if($this->AutoriserUrlsRetour == 1)
				{
					$this->ValeurUrlRetour = _GET_def($this->NomParamUrlRetour) ;
					if($this->ValeurUrlRetour != '' && validate_url_format($this->ValeurUrlRetour) == 0)
					{
						$this->ValeurUrlRetour = '' ;
					}
				}
			}
			protected function DetermineConfirm()
			{
				if(! $this->DoitConfirmMail() || (! isset($_GET[$this->NomParamLoginConfirm]) || ! isset($_GET[$this->NomParamCodeConfirm]) || ! isset($_GET[$this->NomParamEmailConfirm])))
				{
					return ;
				}
				$this->ConfirmMailSoumis = 1 ;
				$login = $_GET[$this->NomParamLoginConfirm] ;
				$code = $_GET[$this->NomParamCodeConfirm] ;
				$email = $_GET[$this->NomParamEmailConfirm] ;
				$membership = & $this->ZoneParent->Membership ;
				$bd = $membership->Database ;
				$nomColEmail = ($membership->LoginWithEmail == 0) ? $membership->EmailMemberColumn : $membership->LoginMemberColumn ;
				$sql = 'select * from '.$bd->EscapeTableName($membership->MemberTable).' where '.$bd->EscapeFieldName($membership->MemberTable, $membership->LoginMemberColumn).' = :login and '.$bd->EscapeFieldName($membership->MemberTable, $nomColEmail).'= :email and '.$bd->EscapeFieldName($membership->MemberTable, $this->NomColCodeConfirmMail).'= :code and '.$bd->EscapeFieldName($membership->MemberTable, $this->NomColConfirmMail).'=1' ;
				$lgn = $bd->FetchSqlRow($sql, array("login" => $login, "email" => $email, "code" => $code)) ;
				if(is_array($lgn) && count($lgn) > 0)
				{
					$this->LgnMembreConfirm = $lgn ;
					$this->LgnMembreConfirm["login_member"] = $lgn[$membership->LoginMemberColumn] ;
					$email = ($membership->LoginWithEmail == 0) ? $this->LgnMembreConfirm[$membership->EmailMemberColumn] : $this->LgnMembreConfirm[$membership->LoginMemberColumn] ;
					$ok = $bd->UpdateRow(
						$membership->MemberTable,
						array(
							$this->NomColCodeConfirmMail => '',
							$membership->EnableMemberColumn => $membership->EnableMemberTrueValue,
							$this->NomColConfirmMail => 0
						),
						$bd->EscapeFieldName($membership->MemberTable, $membership->LoginMemberColumn).' = :login',
						array("login" => $login)
					) ;
					if($ok)
					{
						$this->DemandeConfirmMail = 1 ;
						if($this->EnvoiMailSuccesConfirm)
						{
							$sujetMail = _parse_pattern($this->SujetMailSuccesConfirm, $this->LgnMembreConfirm) ;
							$corpsMail = _parse_pattern($this->CorpsMailSuccesConfirm, $this->LgnMembreConfirm) ;
							$this->MailSuccesConfirmEnvoye = send_html_mail($email, $sujetMail, $corpsMail, $this->EmailEnvoiConfirm) ;
						}
						if(($this->AutoriserUrlsRetour== 1 && $this->ValeurUrlRetour != '') || $this->ConnecterNouveauMembre == 1)
						{
							$this->AutoConnecteNouveauMembre($this->LgnMembreConfirm[$membership->IdMemberColumn]) ;
						}
						if($this->AutoriserUrlsRetour== 1 && $this->ValeurUrlRetour != '')
						{
							redirect_to($this->ValeurUrlRetour) ;
						}
					}
					else
					{
						$this->DemandeConfirmMail = 0 ;
					}
				}
				else
				{
					$this->DemandeConfirmMail = 0 ;
				}
			}
			public function AutoConnecteNouveauMembre($idMembre)
			{
				$this->ZoneParent->Membership->LogonMember($idMembre) ;
			}
			protected function ChargeConfigComposantFormulaireDonnees()
			{
				$form = & $this->ComposantFormulaireDonnees ;
				$membership = & $this->ZoneParent->Membership ;
				parent::ChargeConfigComposantFormulaireDonnees() ;
				if($this->AutoriserUrlsRetour == 1 && $this->ValeurUrlRetour != '')
				{
					$form->ParamsGetSoumetFormulaire[$this->NomParamUrlRetour] = $this->ValeurUrlRetour ;
				}
				if($this->Securiser)
				{
					$this->FltCaptcha = $form->InsereFltEditHttpPost($this->NomParamCaptcha) ;
					$this->FltCaptcha->Libelle = $this->LibelleFltCaptcha ;
					$this->CompCaptcha = $this->FltCaptcha->DeclareComposant("PvZoneCommonCaptcha") ;
					$form->CommandeExecuter->InsereNouvCritere(new CritrCodeSecurValideInscriptionWeb()) ;
				}
				$fltActiver = & $form->FiltreActiverMembre ;
				$fltActiver->ValeurParDefaut = ($this->DoitConfirmMail()) ? $membership->EnableMemberFalseValue() : $this->ValeurActiveParDefaut ;
				$fltActiver->Invisible = 1 ;
				$fltProfil = & $form->FiltreProfilMembre ;
				if(count($this->IdProfilsAcceptes) > 0)
				{
					$fourn = & $fltProfil->Composant->FournisseurDonnees ;
					$params = array_apply_prefix($this->IdProfilsAcceptes, "idProfil") ;
					$fourn->ParamsSelection = array() ;
					$fourn->RequeteSelection = "(select * from ".$fourn->BaseDonnees->EscapeTableName($this->ZoneParent->Membership->ProfileTable).' where '.$fourn->BaseDonnees->EscapeFieldName($this->ZoneParent->Membership->ProfileTable, $this->ZoneParent->Membership->IdProfileColumn)." in (" ;
					foreach($this->IdProfilsAcceptes as $i => $idProfil)
					{
						if($i > 0)
							$fourn->RequeteSelection .= ", " ;
						$fourn->ParamsSelection["idProfil".$i] = $idProfil ;
						$fourn->RequeteSelection .= $fourn->BaseDonnees->ParamPrefix."idProfil".$i ;
					}
					$fourn->RequeteSelection .= "))" ;
				}
				else
				{
					$fltProfil->ValeurParDefaut = $this->IdProfilParDefaut ;
					$fltProfil->Invisible = 1 ;
				}
				if($this->Detaille == 0)
				{
					$nomFltsOblig = array("filtreLoginMembre", "filtreMotPasseMembre", "filtreEmailMembre", "filtreProfilMembre") ;
					if($membership->ConfirmSetPasswordEnabled == 1)
					{
						$nomFltsOblig[] = "filtreConfirmMotPasseMembre" ;
					}
					foreach($form->FiltresEdition as $i => & $flt)
					{
						if($flt->TypeLiaisonParametre != "get" && $flt->TypeLiaisonParametre != "post")
						{
							continue ;
						}
						if(! in_array($flt->NomParametreLie, $nomFltsOblig))
						{
							$flt->Invisible = 1 ;
						}
					}
					$form->FiltreNomMembre->ValeurParDefaut = $this->ValeurDefautNomMembre ;
					$form->FiltrePrenomMembre->ValeurParDefaut = $this->ValeurDefautPrenomMembre ;
					$form->FiltreAdresseMembre->ValeurParDefaut = $this->ValeurDefautAdresseMembre ;
					$form->FiltreContactMembre->ValeurParDefaut = $this->ValeurDefautContactMembre ;
				}
				if($this->DoitConfirmMail())
				{
					$this->_FltConfirmMail = $form->InsereFltEditFixe("confirm_mail", 1, $this->NomColConfirmMail) ;
					$this->_FltCodeConfirm = $form->InsereFltEditFixe("code_confirm", rand(1000, 9999), $this->NomColCodeConfirmMail) ;
				}
				else
				{
					if($this->AutoriserUrlsRetour == 1 && $this->ValeurUrlRetour != '')
					{
						$form->RedirigeExecuterVersUrl($this->ValeurUrlRetour) ;
					}
				}
				$form->CommandeExecuter->Libelle = $this->LibelleCmdExecuter ;
				$form->CommandeExecuter->MessageSuccesExecution = $this->MsgSuccesCmdExecuter ;
				if($this->AutoriserUrlsRetour == 1 && $this->ValeurUrlRetour != '')
				{
					$form->RedirigeAnnulerVersUrl($this->ValeurUrlRetour) ;
				}
				else
				{
					$form->RedirigeAnnulerVersScript($this->ZoneParent->NomScriptConnexion) ;
				}
			}
			public function CodeConfirmMail()
			{
				return $this->_FltCodeConfirm->Lie() ;
			}
			public function DoitConfirmMail()
			{
				return ($this->ActiverConfirmMail && $this->NomColConfirmMail != '' && $this->NomColCodeConfirmMail != '') ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				if($this->DemandeConfirmMail == -1)
				{
					$ctn = parent::RenduSpecifique() ;
					if($this->InclureMsgConnexion == 1)
					{
						$ctn .= $this->RenduMsgConnexion() ;
					}
				}
				elseif($this->DemandeConfirmMail == 0)
				{
					$ctn .= '<p>'.$this->MsgErreurConfirmMail.'</p>' ;
				}
				else
				{
					$ctn .= '<p>'._parse_pattern($this->MsgSuccesConfirmMail, $this->LgnMembreConfirm).'</p>' ;
				}
				return $ctn ;
			}
			protected function RenduMsgConnexion()
			{
				$ctn = '' ;
				$paramsUrlCnx = array() ;
				if($this->ZoneParent->ScriptConnexion->AutoriserUrlsRetour == 1 && $this->ValeurUrlRetour != '')
				{
					$paramsUrlCnx[$this->ZoneParent->ScriptConnexion->NomParamUrlRetour] = $this->ValeurUrlRetour ;
				}
				$params = array(
					'url' => $this->ZoneParent->ScriptConnexion->ObtientUrlParam($paramsUrlCnx),
					'chemin_icone' => $this->ZoneParent->ScriptConnexion->CheminIcone,
					'titre' => $this->ZoneParent->ScriptConnexion->Titre
				) ;
				$ctn .= '<p align="'.$this->AlignMsgConnexion.'">'._parse_pattern($this->FormatMsgConnexion, $params).'</p>' ;
				return $ctn ;
			}
		}
		class PvScriptModifMembreMSWeb extends PvFormulaireModifDonneesSimple
		{
			public $Titre = "Modifier un membre" ;
			public $TitreDocument = "Modifier un membre" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireModifMembreMS" ;
		}
		class PvScriptSupprMembreMSWeb extends PvFormulaireSupprDonneesSimple
		{
			public $Titre = "Supprimer un membre" ;
			public $TitreDocument = "Supprimer un membre" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprMembreMS" ;
		}
		class PvScriptModifPrefsWeb extends PvFormulaireModifDonneesSimple
		{
			public $Titre = "Modifier informations perso." ;
			public $TitreDocument = "Modifier informations perso." ;
			public $NomClasseFormulaireDonnees = "PvFormulaireModifInfosMS" ;
		}
		class PvScriptChangeMotPasseWeb extends PvFormulaireModifDonneesSimple
		{
			public $Titre = "Changer de mot de passe" ;
			public $TitreDocument = "Changer de mot de passe" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMotPasseMS" ;
		}
		class PvScriptDoitChangerMotPasseWeb extends PvFormulaireModifDonneesSimple
		{
			public $Titre = "Changer obligatoire de mot de passe" ;
			public $TitreDocument = "Changer obligatoire de mot de passe" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireDoitChangerMotPasseMS" ;
		}
		class PvScriptChangeMPMembreWeb extends PvFormulaireModifDonneesSimple
		{
			public $Titre = "Changer mot de passe membre" ;
			public $TitreDocument = "Changer mot de passe membre" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMPMembreMS" ;
		}
		class PvScriptListeMembresMSWeb extends PvScriptTableauDonneesSimple
		{
			public $Titre = "Liste des membres" ;
			public $TitreDocument = "Liste des membres" ;
			public $NomClasseTableauDonnees = "PvTableauMembresMSHtml" ;
		}
		class PvScriptAjoutProfilMSWeb extends PvFormulaireAjoutDonneesSimple
		{
			public $Titre = "Ajouter un profil" ;
			public $TitreDocument = "Ajouter un profil" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutProfilMS" ;
		}
		class PvScriptModifProfilMSWeb extends PvFormulaireModifDonneesSimple
		{
			public $Titre = "Modifier un profil" ;
			public $TitreDocument = "Modifier un profil" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireModifProfilMS" ;
		}
		class PvScriptSupprProfilMSWeb extends PvFormulaireSupprDonneesSimple
		{
			public $Titre = "Supprimer un profil" ;
			public $TitreDocument = "Supprimer un profil" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprProfilMS" ;
		}
		class PvScriptListeProfilsMSWeb extends PvScriptTableauDonneesSimple
		{
			public $Titre = "Liste des profils" ;
			public $TitreDocument = "Liste des profils" ;
			public $NomClasseTableauDonnees = "PvTableauProfilsMSHtml" ;
		}
		class PvScriptAjoutRoleMSWeb extends PvFormulaireAjoutDonneesSimple
		{
			public $Titre = "Ajouter un r&ocirc;le" ;
			public $TitreDocument = "Ajouter un r&ocirc;le" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutRoleMS" ;
		}
		class PvScriptModifRoleMSWeb extends PvFormulaireModifDonneesSimple
		{
			public $Titre = "Modifier un r&ocirc;le" ;
			public $TitreDocument = "Modifier un r&ocirc;le" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireModifRoleMS" ;
		}
		class PvScriptSupprRoleMSWeb extends PvFormulaireSupprDonneesSimple
		{
			public $Titre = "Supprimer un r&ocirc;le" ;
			public $TitreDocument = "Supprimer un r&ocirc;le" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprRoleMS" ;
		}
		class PvScriptListeRolesMSWeb extends PvScriptTableauDonneesSimple
		{
			public $Titre = "Liste des r&ocirc;les" ;
			public $TitreDocument = "Liste des r&ocirc;les" ;
			public $NomClasseTableauDonnees = "PvTableauRolesMSHtml" ;
		}
		
		class PvScriptListeServeursADWeb extends PvScriptTableauDonneesSimple
		{
			public $TitreDocument = "Liste des serveurs Active Directory" ;
			public $Titre = "Liste des serveurs Active Directory" ;
			protected function ChargeConfigComposantTableauDonnees()
			{
				parent::ChargeConfigComposantTableauDonnees() ;
				$tabl = & $this->ComposantTableauDonnees ;
				$membership = & $this->ZoneParent->Membership ;
				$bd = & $membership->Database ;
				$this->DefColId = $tabl->InsereDefColCachee($membership->IdADServerColumn) ;
				$this->DefColHote = $tabl->InsereDefCol($membership->HostADServerColumn, $membership->HostADServerLabel) ;
				$this->DefColPort = $tabl->InsereDefCol($membership->PortADServerColumn, $membership->PortADServerLabel) ;
				$this->DefColDomaine = $tabl->InsereDefCol($membership->DomainADServerColumn, $membership->DomainADServerLabel) ;
				$this->DefColDn = $tabl->InsereDefCol($membership->DnADServerColumn, $membership->DnADServerLabel) ;
				$tabl->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$tabl->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$tabl->FournisseurDonnees->RequeteSelection = $bd->EscapeTableName($membership->ADServerTable) ;
			}
		}
		
		class PvScriptEditServeurADWeb extends PvFormulaireWebDonneesSimple
		{
			public $FltId ;
			public $FltHote ;
			public $FltPort ;
			public $FltDomaine ;
			public $FltUserProtoV3 ;
			public $FltSuivreReferants ;
			public $MessageErreurDejaEnregistre = "Le serveur Active Directory avec les m&ecirc;mes param&egrave;tres existe d&eacute;j&agrave;" ;
			protected function InitComposantFormulaireDonnees()
			{
				parent::InitComposantFormulaireDonnees() ;
			}
			protected function ChargeConfigComposantFormulaireDonnees()
			{
				parent::ChargeConfigComposantFormulaireDonnees() ;
				$membership = & $this->ZoneParent->Membership ;
				$remplCfgMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
				$bd = & $membership->Database ;
				$form = & $this->ComposantFormulaireDonnees ;
				$this->FltId = $form->InsereFltSelectHttpGet("id", $bd->EscapeTableName($membership->IdADServerColumn).' = <self>') ;
				$this->FltHote = $form->InsereFltEditHttpPost("hote", $membership->HostADServerColumn) ;
				$this->FltHote->Libelle = $membership->HostADServerLabel ;
				$this->FltPort = $form->InsereFltEditHttpPost("port", $membership->PortADServerColumn) ;
				$this->FltPort->Libelle = $membership->PortADServerLabel ;
				$this->FltDomaine = $form->InsereFltEditHttpPost("domaine", $membership->DomainADServerColumn) ;
				$this->FltDomaine->Libelle = $membership->DnADServerLabel ;
				$this->FltDn = $form->InsereFltEditHttpPost("dn", $membership->DnADServerColumn) ;
				$this->FltDn->Libelle = $membership->DnADServerLabel ;
				$this->FltUserProtoV3 = $form->InsereFltEditHttpPost("user_proto_v3", $membership->UseProtocolV3ADServerColumn) ;
				$this->FltUserProtoV3->Libelle = $membership->UseProtocolV3ADServerLabel ;
				$this->FltUserProtoV3->DeclareComposant("PvZoneSelectBoolHtml") ;
				$this->FltSuivreReferants = $form->InsereFltEditHttpPost("suivre_referants", $membership->FollowReferralsADServerColumn) ;
				$this->FltSuivreReferants->Libelle = $membership->FollowReferralsADServerLabel ;
				$this->FltSuivreReferants->DeclareComposant("PvZoneSelectBoolHtml") ;
				$form->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$form->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$form->FournisseurDonnees->RequeteSelection = $bd->EscapeTableName($membership->ADServerTable) ;
				$form->FournisseurDonnees->TableEdition = $membership->ADServerTable ;
				$form->CommandeExecuter->InsereCritereNonVide(array("hote", "port", "dn")) ;
				$form->CommandeExecuter->InsereNouvCritere(new PvCritereEditServeurADWeb()) ;
				$form->RedirigeAnnulerVersScript($this->ZoneParent->NomScriptListeServeursAD) ;
			}
		}
		class PvScriptAjoutServeurADWeb extends PvScriptEditServeurADWeb
		{
			public $TitreDocument = "Ajout serveur Active Directory" ;
			public $Titre = "Ajout serveur Active Directory" ;
			protected function InitComposantFormulaireDonnees()
			{
				parent::InitComposantFormulaireDonnees() ;
				$form = & $this->ComposantFormulaireDonnees ;
				$form->InclureElementEnCours = 0 ;
				$form->InclureTotalElements = 0 ;
				$form->LibelleCommandeExecuter = "Ajouter" ;
				$form->NomClasseCommandeExecuter = "PvCommandeAjoutElement" ;
			}
		}
		class PvScriptModifServeurADWeb extends PvScriptEditServeurADWeb
		{
			public $TitreDocument = "Modification serveur Active Directory" ;
			public $Titre = "Modification serveur Active Directory" ;
			protected function InitComposantFormulaireDonnees()
			{
				parent::InitComposantFormulaireDonnees() ;
				$form = & $this->ComposantFormulaireDonnees ;
				$form->InclureElementEnCours = 1 ;
				$form->InclureTotalElements = 1 ;
				$form->LibelleCommandeExecuter = "Modifier" ;
				$form->NomClasseCommandeExecuter = "PvCommandeModifElement" ;
			}
		}
		class PvScriptSupprServeurADWeb extends PvScriptEditServeurADWeb
		{
			public $TitreDocument = "Suppression serveur Active Directory" ;
			public $Titre = "Suppression serveur Active Directory" ;
			protected function InitComposantFormulaireDonnees()
			{
				parent::InitComposantFormulaireDonnees() ;
				$form = & $this->ComposantFormulaireDonnees ;
				$form->InclureElementEnCours = 1 ;
				$form->InclureTotalElements = 1 ;
				$form->LibelleCommandeExecuter = "Supprimer" ;
				$form->Editable = 0 ;
				$form->NomClasseCommandeExecuter = "PvCommandeSupprElement" ;
			}
		}
		class PvCritereEditServeurADWeb extends PvCritereBase
		{
			public function EstRespecte()
			{
				$form = & $this->FormulaireDonneesParent ;
				if($form->InclureElementEnCours == 0)
				{
					return 1 ;
				}
				$script = $form->ScriptParent ;
				$bd = $form->FournisseurDonnees->BaseDonnees ;
				$membership = $form->ZoneParent->Membership ;
				$lgnSimilaire = $bd->FetchSqlRow(
					"select * from ".$bd->EscapeTableName($membership->ADServerTable)." where ".$bd->EscapeTableName($membership->ADServerTable, $membership->HostADServerColumn)." = ".$bd->ParamPrefix."hote and ".$bd->EscapeTableName($membership->ADServerTable, $membership->DomainADServerColumn)." = ".$bd->ParamPrefix."domaine",
					array(
						"domaine" => $script->FltDomaine->Lie(),
						"hote" => $script->FltHote->Lie(),
					)
				) ;
				if(! is_array($lgnSimilaire))
				{
					$this->MessageErreur = "Erreur SQL : ".$bd->ConnectionException ;
					return 0 ;
				}
				if(count($lgnSimilaire) > 0)
				{
					$this->MessageErreur = $script->MessageErreurDejaEnregistre ;
					return 0 ;
				}
				return 1 ;
			}
		}
		
		class PvScriptImportMembreMSWeb extends PvScriptWebSimple
		{
			public $TitreDocument = "Importer membres" ;
			public $Titre = "Importer membres" ;
			public function DetermineEnvironnement()
			{
				$this->ZoneParent->RemplisseurConfigMembership->DetermineFormImporteMembreMS($this) ;
			}
			public function RenduSpecifique()
			{
				return $this->ZoneParent->RemplisseurConfigMembership->RenduFormImporteMembreMS($this) ;
			}
		}

		class CritrCodeSecurValideInscriptionWeb extends PvCritereBase
		{
			public $MessageErreur = "Le code de s&eacute;curit&eacute; saisi est incorrect" ;
			public function EstRespecte()
			{
				if($this->FormulaireDonneesParent->Editable == 0)
				{
					return 1 ;
				}
				$ok = $this->ScriptParent->FltCaptcha->Composant->VerifieValeurSoumise($this->ScriptParent->FltCaptcha->Lie()) ;
				return $ok ;
			}
		}
		
		class PvFournisseurComposantIUMS
		{
			public static function RemplitFiltreEditionMembre(& $script)
			{
				$membership = $script->ZoneParent->Membership ;
				$i = 0 ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreLoginMembre") ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->LoginMemberColumn ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->LoginMemberColumn ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->LoginMemberLabel ;
				$i++ ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreMotPasseMembre") ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneMotPasseHtml") ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->PasswordMemberColumn ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->PasswordMemberColumn ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->PasswordMemberLabel ;
				if($membership->PasswordMemberExpr != "")
				{
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->ExpressionColonneLiee = $membership->PasswordMemberExpr.'('.$membership->Database->ExprParamPattern.')' ;
				}
				if($membership->FirstNameMemberColumn != "")
				{
					$i++ ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreNomMembre") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->FirstNameMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->FirstNameMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->FirstNameMemberLabel ;
				}
				if($membership->LastNameMemberColumn != "")
				{
					$i++ ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtrePrenomMembre") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->LastNameMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->LastNameMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->LastNameMemberLabel ;
				}
				if($membership->EmailMemberColumn != "")
				{
					$i++ ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreEmailMembre") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->EmailMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->EmailMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->EmailMemberLabel ;
				}
				if($membership->AddressMemberColumn != "")
				{
					$i++ ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreAdresseMembre") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->AddressMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->AddressMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->AddressMemberLabel ;
				}
				if($membership->ContactMemberColumn != "")
				{
					$i++ ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreContactMembre") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->ContactMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->ContactMemberLabel ;
				}
				if($membership->ADActivatedMemberColumn != "")
				{
					$i++ ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreActiverADMembre") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->ADActivatedMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->ADActivatedMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->ADActivatedMemberLabel ;
				}
				if($membership->EnableMemberColumn != "")
				{
					$i++ ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreActiverMembre") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->EnableMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->EnableMemberColumn ;
					$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->EnableMemberLabel ;
				}
				$script->ComposantFormulaireDonnees->FiltresEdition[$i] = $script->CreeFiltreHttpPost("filtreProfilMembre") ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomColonneLiee = $membership->ProfileMemberColumn ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->NomParametreDonnees = $membership->ProfileMemberColumn ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Libelle = $membership->ProfileMemberLabel ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Composant->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = $membership->ProfileTable ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Composant->NomColonneValeur = $membership->ProfileMemberForeignKey ;
				$script->ComposantFormulaireDonnees->FiltresEdition[$i]->Composant->NomColonneLibelle = $membership->TitleProfileColumn ;
			}
			public static function InitFormulaireMembre(& $script)
			{
				$membership = $script->ZoneParent->Membership ;
				$script->ComposantFormulaireDonnees->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$script->ComposantFormulaireDonnees->FournisseurDonnees->RequeteSelection = $membership->MemberTable ;
				$script->ComposantFormulaireDonnees->FournisseurDonnees->TableEdition = $membership->MemberTable ;
			}
			public static function RemplitFormulaireGlobalMembre(& $script)
			{
				PvFournisseurComposantIUMS::InitFormulaireMembre($script) ;
				$i = 0 ;
				$membership = $script->ZoneParent->Membership ;
				$script->ComposantFormulaireDonnees->FiltresLigneSelection[$i] = $script->CreeFiltreHttpGet("idMembre") ;
				$script->ComposantFormulaireDonnees->FiltresLigneSelection[$i]->ExpressionDonnees = $membership->Database->EscapeVariableName($membership->IdMemberColumn).' = <self>' ;
				PvFournisseurComposantIUMS::RemplitFiltreEditionMembre($script) ;
			}
			public static function RemplitFormulairePrefsMembre(& $script)
			{
				$membership = $script->ZoneParent->Membership ;
				PvFournisseurComposantIUMS::InitFormulaireMembre($script) ;
				$i = 0 ;
				$script->ComposantFormulaireDonnees->FiltresLigneSelection[$i] = $script->CreeFiltreMembreConnecte($membership->IdMemberColumn) ;
				$script->ComposantFormulaireDonnees->FiltresLigneSelection[$i]->ExpressionDonnees = $membership->Database->EscapeFieldName($membership->MemberTable, $membership->IdMemberColumn).' = <self>' ;
				PvFournisseurComposantIUMS::RemplitFiltreEditionMembre($script) ;
			}
		}
		
		class PvScriptConsoleSimple extends PvScriptIHMDeBase
		{
			public function RenduDispositif()
			{
				return $this->RenduDispositifBrut() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
	}
	
?>