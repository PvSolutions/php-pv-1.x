<?php
	
	if(! defined('MODULE_PAGE_RACINE_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__).'/Noyau.class.php' ;
		}
		if(! defined('COMPOSANT_UI_MODULE_BASE_SWS'))
		{
			include dirname(__FILE__).'/ComposantIU/Noyau.class.php' ;
		}
		define('MODULE_PAGE_RACINE_SWS', 1) ;
		
		class ModulePageRacineSws extends ModulePageBaseSws
		{
			public $NomRef = "racine" ;
			public $TitreMenu = "Accueil" ;
			public $ScriptAccueil ;
			public $FournitFluxRSS = 1 ;
			protected function CreeActionFluxRSS()
			{
				return new ActionFluxRSSRacineSws() ;
			}
			protected function CreeScriptAccueil()
			{
				return new ScriptAccueilBaseSws() ;
			}
			protected function CreeScriptAccueilAdmin()
			{
				return new ScriptAccueilAdminBaseSws() ;
			}
			public function RemplitZonePublValide(& $zone)
			{
				$this->ScriptAccueil = $this->InsereScript($zone->NomScriptParDefaut, $this->CreeScriptAccueil(), $zone) ;
			}
			public function RemplitZoneAdminValide(& $zone)
			{
				$this->ScriptAccueil = $this->InsereScript($zone->NomScriptParDefaut, $this->CreeScriptAccueilAdmin(), $zone) ;
			}
		}
		
		class ScriptAccueilBaseSws extends ScriptBaseSws
		{
			protected function RenduDispositifBrut()
			{
				return "Bienvenue sur le site web !!!" ;
			}
		}
		class ScriptAccueilAdminBaseSws extends ScriptBaseSws
		{
			public $AliasMsgBienvenue = "" ;
			public $GrilleModules ;
			public $CtnMsgBienvenue = "Bienvenue sur l'espace d'administration" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->GrilleModules = new GrilleModulesSws() ;
				$this->GrilleModules->AdopteScript("grilleModules", $this) ;
				$this->GrilleModules->ChargeConfig() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div align="center">' ;
				$ctn .= '<p align="center">'.$this->CtnMsgBienvenue.'</p>' ;
				if($this->ZoneParent->PossedeMembreConnecte())
				{
					$ctn .= $this->GrilleModules->RenduDispositif() ;
				}
				else
				{
					$ctn .= '<p><a href="'.$this->ZoneParent->ScriptConnexion->ObtientUrl().'">CONNEXION</a></p>' ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class ScriptRecommandEntiteTableSws extends ScriptConsultEntiteTableSws
		{
			public $UtiliserCorpsDocZone = 0 ;
			public $FormPrinc ;
			public $CmdEnvoyer ;
			public $FltEmailExpedit ;
			public $FltEmailRecept ;
			public $FltMessage ;
			public $FltCaptcha ;
			public $CompMessage ;
			public $CritrNonVide ;
			public $CritrCodeSecur ;
			public $CritrFormatEmail ;
			public $EnregBDSupport = 1 ;
			public $NomTableRecommand = "recommand_entite" ;
			public $NomColUrlRecommand = "url" ;
			public $NomColNomEntiteRecommand = "nom_entite" ;
			public $NomColIdEntiteRecommand = "id_entite" ;
			public $NomColNomScriptRecommand = "nom_script" ;
			public $NomColEmailExpeditRecommand = "email_expediteur" ;
			public $NomColEmailReceptRecommand = "email_recepteur" ;
			public $NomColSujetRecommand = "sujet" ;
			public $NomColCorpsRecommand = "corps" ;
			public $NomColResultEnvoiRecommand = "result" ;
			public $MessageSuccesEnvoiMail = "Votre message a &eacute;t&eacute; envoy&eacute; avec succ&egrave;s" ;
			public $MessageErreurEnvoiMail = "Echec survenu lors de l'envoi du mail" ;
			public $FormatSujetRecommand = 'Recommandation de ${email_expediteur}' ;
			public $MsgRecommandDefaut = "" ;
			public $MsgErrEmailExpeditInvalide = "L'adresse Email de l'expediteur a un mauvais format" ;
			public $MsgErrEmailReceptInvalide = "L'adresse Email de votre ami a un mauvais format" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$entite = $this->ObtientEntitePage() ;
				$this->TitreDocument = $entite->LgnEnCours["titre"] ;
				$this->DetermineFormPrinc() ;
			}
			public function UrlRecommandee()
			{
				$entitePage = $this->ObtientEntitePage() ;
				return $entitePage->ScriptConsult->ObtientUrlParam(array($entitePage->NomParamId => $entitePage->LgnEnCours['id'])) ;
			}
			protected function CalculeMsgRecommandParDefaut()
			{
				$entitePage = $this->ObtientEntitePage() ;
				if($this->MsgRecommandDefaut == '' && $entitePage->AccepterTitre)
				{
					$this->MsgRecommandDefaut = $entitePage->LgnEnCours["titre"] ;
				}
			}
			public function DetermineFormPrinc()
			{
				$entitePage = $this->ObtientEntitePage() ;
				$this->FormPrinc = $this->CreeFormPrinc() ;
				$this->FormPrinc->InscrireCommandeAnnuler = 0 ;
				$this->FormPrinc->InclureTotalElements = 0 ;
				$this->FormPrinc->InclureElementEnCours = 0 ;
				$this->FormPrinc->MaxFiltresEditionParLigne = 1 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "CmdEnvoiRecommandEntiteSws" ;
				$this->FormPrinc->LibelleCommandeExecuter = "Envoyer" ;
				$this->FormPrinc->AdopteScript('formPrinc', $this) ;
				$this->FormPrinc->ChargeConfig() ;
				// Expedition
				$this->FltEmailExpedit = $this->FormPrinc->InsereFltEditHttpPost("email_expediteur") ;
				$this->FltEmailExpedit->AccepteTagsHtml = 0 ;
				$this->FltEmailExpedit->ObtientComposant()->Largeur = "300px" ;
				$this->FltEmailExpedit->Libelle = "Votre email" ;
				// Reception
				$this->FltEmailRecept = $this->FormPrinc->InsereFltEditHttpPost("email_recepteur") ;
				$this->FltEmailRecept->AccepteTagsHtml = 0 ;
				$this->FltEmailRecept->ObtientComposant()->Largeur = "300px" ;
				$this->FltEmailRecept->Libelle = "Email de votre ami" ;
				// Message
				$this->FltMessage = $this->FormPrinc->InsereFltEditHttpPost("message") ;
				$this->FltMessage->Libelle = "Message" ;
				$this->FltMessage->AccepteTagsHtml = 0 ;
				$this->CompMessage = $this->FltMessage->DeclareComposant("PvZoneMultiligneHtml") ;
				$this->CompMessage->TotalColonnes = "60" ;
				$this->CompMessage->TotalLignes = "3" ;
				$this->CalculeMsgRecommandParDefaut() ;
				$this->FltMessage->ValeurParDefaut = _parse_pattern(
					$this->MsgRecommandDefaut,
					array_merge(
						array('url_script' => $this->UrlRecommandee()),
						$entitePage->LgnEnCours
					)
				) ;
				// Captcha
				$this->FltCaptcha = $this->FormPrinc->InsereFltEditHttpPost("code_securite") ;
				$this->FltCaptcha->Libelle = "Code de s&eacute;curit&eacute;" ;
				$comp = $this->FltCaptcha->DeclareComposant("PvZoneCommonCaptcha") ;
				$comp->ActionAffichImg->Params = array($entitePage->NomParamId => $entitePage->LgnEnCours["id"]) ;
				// Commandes
				$this->CritrNonVide = $this->FormPrinc->CommandeExecuter->InsereCritereNonVide(array('email_expediteur', 'email_recepteur', 'message')) ;
				$this->CritrFormatEmail = $this->FormPrinc->CommandeExecuter->InsereCritereFormatEmail(array('email_expediteur', 'email_recepteur')) ;
				$this->CritrCodeSecur = $this->FormPrinc->CommandeExecuter->InsereNouvCritere(new CritrCodeSecurValideRecommandEntSws()) ;
			}
			public function CreeFormPrinc()
			{
				return new PvFormulaireDonneesHtml() ;
			}
			protected function RenduDispositifBrut()
			{
				$url = $this->UrlRecommandee() ;
				$ctn = '' ;
				$ctn .= '<div align="center" style="background:white;">' ;
				$ctn .= '<p>URL : <b>'.$url.'</b></p>' ;
				$ctn .= $this->FormPrinc->RenduDispositif() ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class CmdEnvoiRecommandEntiteSws extends CmdEditEntiteBaseSws
		{
			public $ValEmailExpedit ;
			public $ValEmailRecept ;
			public $ValMessage ;
			public $ValSujet ;
			public $SuccesEnvoiMail ;
			public $SuccesEnregBD = 0 ;
			public function ExecuteInstructions()
			{
				$script = & $this->ScriptParent ;
				$entitePage = $this->ObtientEntitePage() ;
				$this->ValEmailExpedit = $script->FltEmailExpedit->Lie() ;
				$this->ValEmailRecept = $script->FltEmailRecept->Lie() ;
				$this->ValMessage = $script->FltMessage->Lie() ;
				$this->ValSujet = _parse_pattern($this->ScriptParent->FormatSujetRecommand, array_merge(array("email_expediteur" => $this->ValEmailExpedit), $entitePage->LgnEnCours)) ;
				$msg = trim($this->ValMessage) ;
				if($msg != '')
					$msg .= "\r\n\r\n" ;
				$msg .= htmlentities($this->ScriptParent->UrlRecommandee())."\r\n" ;
				$this->SuccesEnvoiMail = send_plain_mail($this->ValEmailRecept, $this->ValSujet, $msg, $this->ValEmailExpedit) ;
				if($this->SuccesEnvoiMail)
				{
					$this->ConfirmeSucces($script->MessageSuccesEnvoiMail) ;
					$this->FormulaireDonneesParent->CacherFormulaireFiltres = 1 ;
				}
				else
				{
					$this->RenseigneErreur($script->MessageErreurEnvoiMail) ;
				}
				if($script->EnregBDSupport == 1)
				{
					$lgn = array(
						$script->NomColUrlRecommand => $entitePage->ScriptConsult->ObtientUrlParam(array($entitePage->NomParamId => $entitePage->LgnEnCours["id"])),
						$script->NomColNomScriptRecommand => $script->NomElementZone,
						$script->NomColIdEntiteRecommand => $entitePage->LgnEnCours["id"],
						$script->NomColNomEntiteRecommand => $entitePage->NomEntite,
						$script->NomColEmailExpeditRecommand => $this->ValEmailExpedit,
						$script->NomColEmailReceptRecommand => $this->ValEmailRecept,
						$script->NomColSujetRecommand => $this->ValSujet,
						$script->NomColCorpsRecommand => $this->ValMessage,
						$script->NomColResultEnvoiRecommand => $this->SuccesEnvoiMail ? 1 : 0,
					) ;
					$bd = $this->ObtientBDSupport() ;
					$this->SuccesEnregBD = $bd->InsertRow($script->NomTableRecommand, $lgn) ;
				}
				else
				{
					$this->SuccesEnregBD = -1 ;
				}
			}
		}

		class ScriptInteretEntiteTableSws extends ScriptConsultEntiteTableSws
		{
			public $UtiliserCorpsDocZone = 0 ;
			public $FormPrinc ;
			public $CmdEnvoyer ;
			public $FltEmailExpedit ;
			public $FltEmailRecept ;
			public $FltMessage ;
			public $FltCaptcha ;
			public $EmailRecept ;
			public $CompMessage ;
			public $CritrNonVide ;
			public $CritrCodeSecur ;
			public $CritrFormatEmail ;
			public $EnregBDSupport = 0 ;
			public $NomTableInteret = "interet_entite" ;
			public $NomColUrlInteret = "url" ;
			public $NomColNomEntiteInteret = "nom_entite" ;
			public $NomColIdEntiteInteret = "id_entite" ;
			public $NomColNomScriptInteret = "nom_script" ;
			public $NomColEmailExpeditInteret = "email_expediteur" ;
			public $NomColSujetInteret = "sujet" ;
			public $NomColCorpsInteret = "corps" ;
			public $NomColResultEnvoiInteret = "result" ;
			public $IntroCorpsInteret = "L'adresse email suivante a manifeste son interet pour l'URL en dessous.\r\n\r\n" ;
			public $MessageSuccesEnvoiMail = "Votre message a &eacute;t&eacute; envoy&eacute; avec succ&egrave;s" ;
			public $MessageNonDisponible = "Ce service est actuellement indisponible." ;
			public $MessageErreurEnvoiMail = "Echec survenu lors de l'envoi du mail" ;
			public $FormatSujetInteret = 'Interet de ${email_expediteur}' ;
			public $MsgInteretDefaut = "" ;
			public $MsgErrEmailExpeditInvalide = "L'adresse Email de l'expediteur a un mauvais format" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$entite = $this->ObtientEntitePage() ;
				$this->TitreDocument = $entite->LgnEnCours["titre"] ;
				$this->DetermineFormPrinc() ;
			}
			public function UrlInteret()
			{
				$entitePage = $this->ObtientEntitePage() ;
				return $entitePage->ScriptConsult->ObtientUrlParam(array($entitePage->NomParamId => $entitePage->LgnEnCours['id'])) ;
			}
			protected function CalculeMsgInteretParDefaut()
			{
				$entitePage = $this->ObtientEntitePage() ;
				if($this->MsgInteretDefaut == '' && $entitePage->AccepterTitre)
				{
					// $this->MsgInteretDefaut = $entitePage->LgnEnCours["titre"] ;
				}
			}
			public function DetermineFormPrinc()
			{
				$entitePage = $this->ObtientEntitePage() ;
				$this->FormPrinc = $this->CreeFormPrinc() ;
				$this->FormPrinc->InscrireCommandeAnnuler = 0 ;
				$this->FormPrinc->InclureTotalElements = 0 ;
				$this->FormPrinc->InclureElementEnCours = 0 ;
				$this->FormPrinc->MaxFiltresEditionParLigne = 1 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "CmdEnvoiInteretEntiteSws" ;
				$this->FormPrinc->LibelleCommandeExecuter = "Envoyer" ;
				$this->FormPrinc->AdopteScript('formPrinc', $this) ;
				$this->FormPrinc->ChargeConfig() ;
				// Expedition
				$this->FltEmailExpedit = $this->FormPrinc->InsereFltEditHttpPost("email_expediteur") ;
				$this->FltEmailExpedit->AccepteTagsHtml = 0 ;
				$this->FltEmailExpedit->ObtientComposant()->Largeur = "300px" ;
				$this->FltEmailExpedit->Libelle = "Votre email" ;
				// Message
				$this->FltMessage = $this->FormPrinc->InsereFltEditHttpPost("message") ;
				$this->FltMessage->Libelle = "Message" ;
				$this->FltMessage->AccepteTagsHtml = 0 ;
				$this->CompMessage = $this->FltMessage->DeclareComposant("PvZoneMultiligneHtml") ;
				$this->CompMessage->TotalColonnes = "60" ;
				$this->CompMessage->TotalLignes = "3" ;
				$this->CalculeMsgInteretParDefaut() ;
				$this->FltMessage->ValeurParDefaut = _parse_pattern(
					$this->MsgInteretDefaut,
					array_merge(
						array('url_script' => $this->UrlInteret()),
						$entitePage->LgnEnCours
					)
				) ;
				// Captcha
				$this->FltCaptcha = $this->FormPrinc->InsereFltEditHttpPost("code_securite") ;
				$this->FltCaptcha->Libelle = "Code de s&eacute;curit&eacute;" ;
				$comp = $this->FltCaptcha->DeclareComposant("PvZoneCommonCaptcha") ;
				$comp->ActionAffichImg->Params = array($entitePage->NomParamId => $entitePage->LgnEnCours["id"]) ;
				// Commandes
				$this->CritrNonVide = $this->FormPrinc->CommandeExecuter->InsereCritereNonVide(array('email_expediteur', 'message')) ;
				$this->CritrFormatEmail = $this->FormPrinc->CommandeExecuter->InsereCritereFormatEmail(array('email_expediteur')) ;
				$this->CritrCodeSecur = $this->FormPrinc->CommandeExecuter->InsereNouvCritere(new CritrCodeSecurValideInteretEntSws()) ;
			}
			public function CreeFormPrinc()
			{
				return new PvFormulaireDonneesHtml() ;
			}
			protected function RenduDispositifBrut()
			{
				$url = $this->UrlInteret() ;
				$entitePage = $this->ObtientEntitePage() ;
				$ctn = '' ;
				if($this->EmailRecept != '')
				{
					$ctn .= '<div align="center" style="background:white;">' ;
					$ctn .= '<p>URL : <b>'.$url.'</b></p>' ;
					$ctn .= '<p>'.$entitePage->LgnEnCours["titre"].'</b></p>' ;
					$ctn .= $this->FormPrinc->RenduDispositif() ;
					$ctn .= '</div>' ;
				}
				else
				{
					$ctn .= '<p>'.$this->MessageNonDisponible.'</p>' ;
				}
				return $ctn ;
			}
		}
		class CmdEnvoiInteretEntiteSws extends CmdEditEntiteBaseSws
		{
			public $ValEmailExpedit ;
			public $ValMessage ;
			public $ValSujet ;
			public $SuccesEnvoiMail ;
			public $SuccesEnregBD = 0 ;
			public function ExecuteInstructions()
			{
				$script = & $this->ScriptParent ;
				$entitePage = $this->ObtientEntitePage() ;
				$this->ValEmailExpedit = $script->FltEmailExpedit->Lie() ;
				$this->ValEmailRecept = $script->EmailRecept ;
				$this->ValMessage = $script->FltMessage->Lie() ;
				$this->ValSujet = _parse_pattern($this->ScriptParent->FormatSujetInteret, array_merge(array("email_expediteur" => $this->ValEmailExpedit), $entitePage->LgnEnCours)) ;
				$msg = trim($this->ValMessage) ;
				if($msg != '')
				{
					$msg = '"'.$msg.'"' ;
					$msg .= "\r\n\r\n" ;
				}
				$msg = $this->ScriptParent->IntroCorpsInteret.$msg ;
				$msg .= $entitePage->LgnEnCours["titre"]."\r\n".htmlentities($this->ScriptParent->UrlInteret())."\r\n" ;
				$this->SuccesEnvoiMail = send_plain_mail($this->ValEmailRecept, $this->ValSujet, $msg, $this->ValEmailExpedit) ;
				if($this->SuccesEnvoiMail)
				{
					$this->ConfirmeSucces($script->MessageSuccesEnvoiMail) ;
					$this->FormulaireDonneesParent->CacherFormulaireFiltres = 1 ;
				}
				else
				{
					$this->RenseigneErreur($script->MessageErreurEnvoiMail) ;
				}
				if($script->EnregBDSupport == 1)
				{
					$lgn = array(
						$script->NomColUrlInteret => $entitePage->ScriptConsult->ObtientUrlParam(array($entitePage->NomParamId => $entitePage->LgnEnCours["id"])),
						$script->NomColNomScriptInteret => $script->NomElementZone,
						$script->NomColIdEntiteInteret => $entitePage->LgnEnCours["id"],
						$script->NomColNomEntiteInteret => $entitePage->NomEntite,
						$script->NomColEmailExpeditInteret => $this->ValEmailExpedit,
						$script->NomColEmailReceptInteret => $this->ValEmailRecept,
						$script->NomColSujetInteret => $this->ValSujet,
						$script->NomColCorpsInteret => $this->ValMessage,
						$script->NomColResultEnvoiInteret => $this->SuccesEnvoiMail ? 1 : 0,
					) ;
					$bd = $this->ObtientBDSupport() ;
					$this->SuccesEnregBD = $bd->InsertRow($script->NomTableInteret, $lgn) ;
				}
				else
				{
					$this->SuccesEnregBD = -1 ;
				}
			}
		}
		
		class ScriptVersionImprEntiteTableSws extends ScriptConsultEntiteTableSws
		{
		}
		class ScriptPosterCommentEntiteSws extends ScriptConsultEntiteTableSws
		{
		}
		class ScriptListeCommentEntiteSws extends ScriptConsultEntiteTableSws
		{
		}
		
		class ActionFluxRSSRacineSws extends ActionFluxRSSModuleSws
		{
		}
		
		class CritrCodeSecurValideRecommandEntSws extends PvCritereBase
		{
			public $MessageErreur = "Le code de s&eacute;curit&eacute; saisi est incorrect" ;
			public function EstRespecte()
			{
				$ok = $this->ScriptParent->FltCaptcha->Composant->ActionAffichImg->VerifieValeurSoumise($this->ScriptParent->FltCaptcha->Lie()) ;
				return $ok ;
			}
		}
		class CritrCodeSecurValideInteretEntSws extends PvCritereBase
		{
			public $MessageErreur = "Le code de s&eacute;curit&eacute; saisi est incorrect" ;
			public function EstRespecte()
			{
				$ok = $this->ScriptParent->FltCaptcha->Composant->ActionAffichImg->VerifieValeurSoumise($this->ScriptParent->FltCaptcha->Lie()) ;
				return $ok ;
			}
		}

	}
	
?>