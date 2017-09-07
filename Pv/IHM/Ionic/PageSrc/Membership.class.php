<?php
	
	if(! defined('PV_PAGE_SRC_MEMBERSHIP_IONIC'))
	{
		if(! defined('PV_PAGE_SRC_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_PAGE_SRC_MEMBERSHIP_IONIC', 1) ;
		
		class PvPageSrcNonAutoriseIonic extends PvPageSrcNoyauIonic
		{
			public $Privileges = array() ;
			public $Icone = "lock" ;
			public $Titre = "Acc&egrave;s non autoris&eacute;" ;
			public $Description = "Vous n'avez pas le droit d'acc&eacute;der &agrave; cette page." ;
		}
		
		class PvPageSrcRestreintIonic extends PvPageSrcNoyauIonic
		{
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			protected $MtdAuthentifie ;
			public $PageDecoratorTs ;
			public function & Membership()
			{
				return $this->ZoneParent->Membership ;
			}
			public function & BdMembership()
			{
				return $this->ZoneParent->Membership->Database ;
			}
			public function CreeFournMembership()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->BdMembership() ;
				return $fourn ;
			}
			protected function ChargeFichHtmlAuto()
			{
				// Html
				$this->TagHeader = $this->FichHtml->TagRacine()->InsereTagFils(new PvTagIonHeader()) ;
				$this->TagContent = $this->FichHtml->TagRacine()->InsereTagFils(new PvTagIonContent()) ;
				$this->TagHeaderNavbar = $this->TagHeader->InsereTagFils(new PvTagIonNavbar()) ;
				$tagBtn = $this->TagHeaderNavbar->InsereTagFils(new PvRenduHtmlIonic()) ;
				$tagBtn->Contenu = '<button ion-button menuToggle>
<ion-icon name="menu"></ion-icon>
</button>' ;
				$this->TagHeaderTitle = $this->TagHeaderNavbar->InsereTagFils(new PvTagIonTitle()) ;
				$this->TagHeaderTitle->InsereContent($this->Titre) ;
			}
			protected function ChargeFichTsAuto()
			{
				parent::ChargeFichTsAuto() ;
				$this->FichTs->InsereImportGlobal(array("MenuController"), 'ionic-angular') ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public menuCtrl:MenuController" ;
				if($this->ZoneParent->InscrireMtdsAccesPageSrc == 1)
				{
					$this->ZoneParent->RemplitMtdsAccesPageSrc($this) ;
				}
				else
				{
					if($this->NecessiteMembreConnecte == 1)
					{
						$membership = & $this->ZoneParent->ServiceSrcMembership ;
						$this->InsereImportServiceSrcTs($membership) ;
						$this->ClasseTs->MtdConstruct->Arguments[] = "public svcMembership:".$membership->NomClasse() ;
					}
				}
			}
			protected function CalculeCorpsBrutViewDidEnter()
			{
				if($this->ZoneParent->InscrireMtdsAccesPageSrc == 1)
				{
					$this->MtdViewDidEnter->CorpsBrut = 'let _self :any = this ;'.PHP_EOL
					.'_self.determineMembreConnecte(function() {
'.(($this->ContenuTsAccesAutorise != '') ? $this->ContenuTsAccesAutorise. PHP_EOL : '').'}) ;'.PHP_EOL ;
					return ;
				}
				if($this->NecessiteMembreConnecte == 1)
				{
					$this->MtdViewDidEnter->CorpsBrut = 'this.menuCtrl.enable(true, \'non_connecte\') ;
this.menuCtrl.enable(false, \'connecte\') ;
let _self:any = this ;
this.svcMembership.valideAcces(true, '.svc_json_encode($this->Privileges).', this.navCtrl, function() {
'.(($this->ContenuTsAccesAutorise != '') ? $this->ContenuTsAccesAutorise. PHP_EOL : '').'_self.menuCtrl.enable(false, "non_connecte") ;
_self.menuCtrl.enable(true, "connecte") ;
}) ;' ;
				}
				else
				{
					parent::CalculeCorpsBrutViewDidEnter() ;
				}
			}
		}
		
		class PvPageSrcListRestrIonic extends PvPageSrcRestreintIonic
		{
		}
		class PvPageSrcEditRestrIonic extends PvPageSrcRestreintIonic
		{
			public $InclureElementFormPrinc = 0 ;
			public $InscrireCmdAnnulFormPrinc = 1 ;
			public $InscrireCmdExecFormPrinc = 1 ;
			public $MsgSuccesCmdExecFormPrinc = "" ;
			public $FormPrincEditable = 1 ;
			public $ReqSelectFormPrinc ;
			public $TablEditFormPrinc ;
			public $LibelleCmdExecFormPrinc = "Connexion" ;
			public $LibelleCmdAnnulFormPrinc = "Annuler" ;
			public $NomClasseCmdExecFormPrinc = "PvCommandeExecuterBase" ;
			public $NomClasseCmdAnnulFormPrinc = "PvCommandeAnnulerBase" ;
			protected function CreeCritrCmdExecFormPrinc()
			{
				return new PvCritereBase() ;
			}
			public function CreeFournFormPrinc()
			{
				return new PvFournisseurDonneesSql() ;
			}
			public function ChargeComposantsIU()
			{
				$this->DetermineFormPrinc() ;
			}
			protected function InitFormPrinc()
			{
				$this->FormPrinc->InclureElementEnCours = $this->InclureElementFormPrinc ;
				$this->FormPrinc->InclureTotalElements = $this->InclureElementFormPrinc ;
				$this->FormPrinc->LibelleCommandeExecuter = $this->LibelleCmdExecFormPrinc ;
				$this->FormPrinc->LibelleCommandeAnnuler = $this->LibelleCmdAnnulFormPrinc ;
				$this->FormPrinc->NomClasseCommandeAnnuler = $this->NomClasseCmdAnnulFormPrinc ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->NomClasseCmdExecFormPrinc ;
				$this->FormPrinc->Editable = $this->FormPrincEditable ;
				$this->FormPrinc->InscrireCommandeAnnuler = $this->InscrireCmdAnnulFormPrinc ;
				$this->FormPrinc->InscrireCommandeExecuter = $this->InscrireCmdExecFormPrinc ;
				if($this->MsgSuccesCmdExecFormPrinc != '')
				{
					$this->FormPrinc->MsgExecSuccesCommandeExecuter = $this->MsgSuccesCmdExecFormPrinc ;
				}
			}
			protected function ChargeFormPrinc()
			{
			}
			protected function ChargeFltsSelectFormPrinc()
			{
			}
			protected function ChargeFltsEditFormPrinc()
			{
			}
			protected function ChargeCritrFormPrinc()
			{
				if($this->FormPrinc->InscrireCommandeExecuter == 1 && $this->FormPrinc->Editable == 1)
				{
					$this->FormPrinc->CommandeExecuter->InsereNouvCritere($this->CreeCritrCmdExecFormPrinc()) ;
				}
			}
			protected function ChargeFournDonnees()
			{
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournFormPrinc() ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $this->ReqSelectFormPrinc ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $this->TablEditFormPrinc ;
			}
			protected function DetermineFormPrinc()
			{
				$this->FormPrinc = $this->InsereComposantIU("formPrinc", new PvFormulaireDonneesIonic()) ;
				$this->InitFormPrinc() ;
				$this->FormPrinc->ChargeConfig() ;
				$this->ChargeFournDonnees() ;
				$this->ChargeFltsSelectFormPrinc() ;
				$this->ChargeFltsEditFormPrinc() ;
				$this->ChargeFormPrinc() ;
				$this->ChargeCritrFormPrinc() ;
			}
		}
		
		class PvPageSrcEditMembreIonic extends PvPageSrcEditRestrIonic
		{
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $Titre = "Edition membre" ;
			public $Icone = "person-add" ;
			public $InclureElementFormPrinc = 0 ;
			public $CompMotPasse ;
			public $LibelleCmdExecFormPrinc = "Connexion" ;
			public $LibelleCmdAnnulFormPrinc = "Annuler" ;
			public $NomClasseCmdExecFormPrinc = "PvCommandeNavCtrlPushIonic" ;
			public $NomClasseCmdAnnulFormPrinc = "PvCommandeNavCtrlPushIonic" ;
			public $LibelleLogin = "Login" ;
			public $LibelleMotPasse = "Mot de passe" ;
			public $LibelleNom = "Nom" ;
			public $LibellePrenom = "Prenom" ;
			public $LibelleEmail = "Email" ;
			public $LibelleProfil = "Profil" ;
			protected function ChargeFournDonnees()
			{
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournMembership() ;
				$membership = $this->Membership() ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $membership->MemberTable ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $membership->MemberTable ;
			}
			protected function CreeCritrCmdExecFormPrinc()
			{
				return new PvCritrEditMembreIonic() ;
			}
			protected function ChargeFltsSelectFormPrinc()
			{
				$membership = $this->Membership() ;
				$this->FltId = $this->FormPrinc->InsereFltLgSelectTs("id", "return this.navParams.get('id')") ;
			}
			protected function ChargeFltsEditFormPrinc()
			{
				$membership = $this->Membership() ;
				$this->FltLogin = $this->FormPrinc->InsereFltEditHttpRequest("login", $membership->LoginMemberColumn) ;
				$this->FltLogin->Libelle = $this->LibelleLogin ;
				$this->FltMotPasse = $this->FormPrinc->InsereFltEditHttpRequest("mot_passe", $membership->PasswordMemberColumn) ;
				if($membership->PasswordMemberExpr != '')
				{
					$this->FltMotPasse->ExpressionColonneLiee = $membership->PasswordMemberExpr.'(<self>)' ;
				}
				$this->FltMotPasse->Libelle = $this->LibelleMotPasse ;
				$this->FltMotPasse->DeclareComposant("PvTagIonInputPassword") ;
				$this->FltNom = $this->FormPrinc->InsereFltEditHttpRequest("nom", $membership->FirstNameMemberColumn) ;
				$this->FltNom->Libelle = $this->LibelleNom ;
				$this->FltPrenom = $this->FormPrinc->InsereFltEditHttpRequest("prenom", $membership->LastNameMemberColumn) ;
				$this->FltPrenom->Libelle = $this->LibellePrenom ;
				$this->FltEmail = $this->FormPrinc->InsereFltEditHttpRequest("email", $membership->EmailMemberColumn) ;
				$this->FltEmail->Libelle = $this->LibelleEmail ;
				$this->FltEmail->DeclareComposant("PvTagIonInputEmail") ;
				$this->FltActive = $this->FormPrinc->InsereFltEditFixe("active", $this->ValeurDefautActive, $membership->EnableMemberColumn) ;
			}
			protected function ChargeFltProfil()
			{
				$this->FltProfil = $this->FormPrinc->InsereFltEditHttpRequest("profil", $membership->ProfileMemberColumn) ;
				$this->CompProfil = $this->FltProfil->DeclareComposant("PvZoneSelectIonic") ;
				$this->CompProfil->FournisseurDonnees = $this->CreeFournMembership() ;
				$bd = & $membership->Database ;
				$this->CompProfil->FournisseurDonnees->RequeteSelection = $membership->ProfileTable ;
				$this->CompProfil->NomColonneLibelle = $membership->TitleProfileColumn ;
				$this->CompProfil->NomColonneValeur = $membership->IdProfileColumn ;
			}
		}
		
		class PvCritrEditMembreIonic extends PvCritereBaseIonic
		{
			public $MessageMembreExistant = "Un membre poss&eacute;dant le m&ecirc;me login ou email existe d&eacute;j&agrave;" ;
			public $MessageFormatIncorrectLogin = "Le login a un format incorrect" ;
			public $MessageFormatIncorrectMotPasse = "Le login a un format incorrect" ;
			public $MessageFormatIncorrectEmail = "L'email a un format incorrect" ;
			public $MessageFormatIncorrectNom = "Le nom a un format incorrect (taille inferieure &agrave; 3)" ;
			public $MessageFormatIncorrectPrenom = "Le prenom a un format incorrect (taille inferieure &agrave; 3)" ;
			public function EstRespecte()
			{
				$pageSrc = $this->PageSrcParent() ;
				$membership = $pageSrc->Membership() ;
				$bd = $membership->Database ;
				if(validate_name_user_format($pageSrc->FltLogin->Lie()) == false)
				{
					return $this->RenseigneErreur($this->MessageFormatIncorrectLogin) ;
				}
				if(validate_password_format($pageSrc->FltMotPasse->Lie()) == false)
				{
					return $this->RenseigneErreur($this->MessageFormatIncorrectMotPasse) ;
				}
				if(validate_email_format($pageSrc->FltEmail->Lie()) == false)
				{
					return $this->RenseigneErreur($this->MessageFormatIncorrectEmail) ;
				}
				if(strlen($pageSrc->FltNom->Lie()) < 3)
				{
					return $this->RenseigneErreur($this->MessageFormatIncorrectNom) ;
				}
				if(strlen($pageSrc->FltPrenom->Lie()) < 3)
				{
					return $this->RenseigneErreur($this->MessageFormatIncorrectPrenom) ;
				}
				$idMembreActuel = 0 ;
				if($this->FormulaireDonneesParent->InclureElementEnCours == 1)
				{
					$idMembreActuel = $pageSrc->FltId->Lie() ;
				}
				$lgnMembreSimil = $bd->FetchSqlRow(
					"select * from ".$bd->EscapeTableName($membership->MemberTable)." where (upper(".$bd->EscapeVariableName($membership->LoginMemberColumn).") = upper(:login) or upper(".$bd->EscapeVariableName($membership->EmailMemberColumn).") = upper(:email)) and ".$bd->EscapeVariableName($membership->IdMemberColumn)." <> :idMembreActu",
					array(
						"login" => trim($pageSrc->FltLogin->Lie()),
						"email" => trim($pageSrc->FltEmail->Lie()),
						"idMembreActu" => $idMembreActuel,
					)
				) ;
				if(is_array($lgnMembreSimil))
				{
					if(count($lgnMembreSimil) > 0)
					{
						return $this->RenseigneErreur($this->MessageMembreExistant) ;
					}
				}
				else
				{
					return $this->RenseigneErreur("Erreur Lors de la verification : ".$bd->ConnectionException) ;
				}
				return 1 ;
			}
		}
		
		class PvPageSrcInscriptionIonic extends PvPageSrcEditMembreIonic
		{
			public $ValeurDefautActive = 1 ;
			public $IdsProfil = array() ;
			public $IdProfilParDefaut = 1 ;
			public $InclureElementFormPrinc = 0 ;
			public $NomClasseCmdExecFormPrinc = "PvCmdAjoutElemDonneesIonic" ;
			public $InscrireCmdAnnulFormPrinc = 0 ;
			public $Icone = "log-in" ;
			public $Titre = "Inscription" ;
			public $MsgSuccesCmdExecFormPrinc = "Votre inscription a &eacute;t&eacute; enregistr&eacute;e, vous pouvez d&eacute;sormais vous connecter." ;
			public $LibelleCmdExecFormPrinc = "S'inscrire" ;
			protected function ChargeFormPrinc()
			{
				parent::ChargeFormPrinc() ;
				$this->FormPrinc->CommandeExecuter->ContenuTsDlgSucces = "_self.navCtrl.push(".$this->ZoneParent->PageSrcConnexion->NomClasse().")" ;
			}
			protected function ChargeFltProfil()
			{
				if(count($this->IdsProfil) > 1)
				{
					$this->FltProfil = $this->FormPrinc->InsereFltEditHttpRequest("profil", $membership->ProfileMemberColumn) ;
					$this->CompProfil = $this->FltProfil->DeclareComposant("PvZoneSelectIonic") ;
					$this->CompProfil->FournisseurDonnees = $this->CreeFournMembership() ;
					$bd = & $membership->Database ;
					$this->CompProfil->FournisseurDonnees->RequeteSelection = '(select * from '.$bd->EscapeTableName($membership->ProfileTable).' where '.$bd->EscapeFieldName($membership->ProfileTable, $membership->IdProfileColumn).' in ('.join(", ", $this->IdsProfil).'))' ;
					$this->CompProfil->NomColonneLibelle = $membership->TitleProfileColumn ;
					$this->CompProfil->NomColonneValeur = $membership->IdProfileColumn ;
				}
				else
				{
					if(count($this->IdsProfil) == 0)
					{
						$this->IdsProfil = array($this->IdProfilParDefaut) ;
					}
					$this->FltProfil = $this->FormPrinc->InsereFltEditFixe("profil", $this->IdsProfil[0], $membership->ProfileMemberColumn) ;
				}
			}
			protected function ChargeFichTsAuto()
			{
				parent::ChargeFichTsAuto() ;
				$this->InsereImportPageSrcTs($this->ZoneParent->PageSrcConnexion) ;
			}
		}
		
		class PvPageSrcModifPrefsIonic extends PvPageSrcEditMembreIonic
		{
			public $InclureElementFormPrinc = 1 ;
			public $Icone = "person" ;
			public $Titre = "Vos pr&eacute;f&eacute;rences" ;
			protected function InitFormPrinc()
			{
				parent::InitFormPrinc() ;
				$this->FormPrinc->NgIf = "possedeMembreConnecte() === true" ;
			}
			protected function ChargeFltsSelectFormPrinc()
			{
				$membership = $this->Membership() ;
				$this->FltId = $this->FormPrinc->InsereFltLgSelectTs("id", "return (this.possedeMembreConnecte() === true) ? this.membreConnecte.Id : 0", "id = <self>") ;
			}
			protected function ChargeFltsEditFormPrinc()
			{
				parent::ChargeFltsEditFormPrinc() ;
				$this->FltMotPasse->Invisible = 1 ;
				$this->FltMotPasse->NePasLierColonne = 1 ;
			}
		}
		
		class PvPageSrcConnexionIonic extends PvPageSrcRestreintIonic
		{
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $Icone = "log-in" ;
			public $Titre = "Connexion" ;
			public $FormPrinc ;
			public $FltLogin ;
			public $FltMotPasse ;
			public $LibelleLogin = "Login" ;
			public $LibelleMotPasse = "Mot de passe" ;
			public $LibelleCmdExecFormPrinc = "Connexion" ;
			public $CompMotPasse ;
			public $NomClasseCmdExecFormPrinc = "PvCmdAuthentifieIonic" ;
			public $ContenuTsConnexionSucces = "_self.navCtrl.pop() ;" ;
			protected function CreeFormPrinc()
			{
				return new PvFormulaireDonneesIonic() ;
			}
			public function ChargeComposantsIU()
			{
				$this->ChargeFormPrinc() ;
			}
			protected function InitFormPrinc()
			{
				$this->FormPrinc->InclureElementEnCours = 0 ;
				$this->FormPrinc->InclureTotalElements = 0 ;
				$this->FormPrinc->InscrireCommandeAnnuler = 0 ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->NomClasseCmdExecFormPrinc ;
				$this->FormPrinc->LibelleCommandeExecuter = $this->LibelleCmdExecFormPrinc ;
			}
			protected function ChargeFormPrinc()
			{
				$svcMembership = & $this->ZoneParent->ServiceSrcMembership ;
				$this->FormPrinc = $this->InsereComposantIU("princ", $this->CreeFormPrinc()) ;
				$this->InitFormPrinc() ;
				$this->FormPrinc->ChargeConfig() ;
				$this->FltLogin = $this->FormPrinc->InsereFltEditHttpRequest("login") ;
				$this->FltLogin->Libelle = $this->LibelleLogin ;
				$this->FltMotPasse = $this->FormPrinc->InsereFltEditHttpRequest("motPasse") ;
				$this->FltMotPasse->Libelle = $this->LibelleMotPasse ;
				$this->CompMotPasse = $this->FltMotPasse->DeclareComposant("PvTagIonInputPassword") ;
			}
		}
		
		class PvCmdAuthentifieIonic extends PvCommandeAppelDistantIonic
		{
			public function CorpsBrutMethodeTs()
			{
				$appSrc = & $this->ZoneParent()->AppSrc ;
				$this->ContenuTsSucces = '_self.storage.set("membreConnecte", JSON.stringify(result.valeur)) ;'.PHP_EOL
				.$this->PageSrcParent()->ContenuTsConnexionSucces ;
				$svcMembership = & $this->ZoneParent()->ServiceSrcMembership ;
				$this->NomMtdDist = $svcMembership->NomMethodeDistante("authentifie") ;
				return parent::CorpsBrutMethodeTs() ;
			}
		}
	}
	
?>