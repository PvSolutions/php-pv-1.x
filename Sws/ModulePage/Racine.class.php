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
			public $NomScriptRecherche = "recherche" ;
			public $NomScriptPlanSite = "plan_site" ;
			public $ScriptAccueil ;
			public $InclurePlanSite = 1 ;
			public $FournitFluxRSS = 1 ;
			protected $PresentDansFluxRSS = 0 ;
			protected function CreeActionFluxRSS()
			{
				return new ActionFluxRSSRacineSws() ;
			}
			protected function CreeScriptAccueil()
			{
				return new ScriptAccueilBaseSws() ;
			}
			protected function CreeScriptPlanSite()
			{
				return new ScriptPlanSiteBaseSws() ;
			}
			protected function CreeScriptRecherche()
			{
				return new ScriptRechercheBaseSws() ;
			}
			protected function CreeScriptAccueilAdmin()
			{
				return new ScriptAccueilAdminBaseSws() ;
			}
			public function RemplitZonePublValide(& $zone)
			{
				$this->ScriptAccueil = $this->InsereScript($zone->NomScriptParDefaut, $this->CreeScriptAccueil(), $zone) ;
				/*
				if($this->InclureRecherche == 1)
				{
					$this->ScriptRecherche = $this->InsereScript($this->NomScriptPlanSite, $this->CreeScriptPlanSite(), $zone) ;
				}
				*/
				if($this->InclurePlanSite == 1)
				{
					$this->ScriptPlanSite = $this->InsereScript($this->NomScriptPlanSite, $this->CreeScriptPlanSite(), $zone) ;
					// echo "hds : ".$this->ScriptPlanSite->NomElementZone ;
				}
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
		class ScriptRechercheBaseSws extends ScriptBaseSws
		{
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
			}
			protected function DetermineTablRes()
			{
			}
		}
		class ScriptPlanSiteBaseSws extends ScriptBaseSws
		{
			protected $BarrePlanSite ;
			public $Titre = "Plan du site" ;
			public $TitreDocument = "Plan du site" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineBarrePlanSite() ;
			}
			protected function CreeBarrePlanSite()
			{
				return new PvBarreMenuWebBase() ;
			}
			protected function DetermineBarrePlanSite()
			{
				$this->BarrePlanSite = $this->CreeBarrePlanSite() ;
				$this->BarrePlanSite->AdopteScript("barrePlanSite", $this) ;
				$this->BarrePlanSite->InclureRenduIcone = 0 ;
				$this->BarrePlanSite->ChargeConfig() ;
				$this->MenuAccueil = $this->BarrePlanSite->MenuRacine->InscritSousMenuScript($this->ZoneParent->NomScriptParDefaut) ;
				$this->MenuAccueil->Titre = "Accueil" ;
				$systemeSws = $this->ObtientSystemeSws() ;
				$moduleParent = $this->ObtientModulePage() ;
				foreach($systemeSws->ModulesPage as $nomModule => $module)
				{
					$module->RemplitMenuPlanSite($this->BarrePlanSite->MenuRacine) ;
				}
				if($moduleParent->FournitFluxRSS == 1)
				{
					$this->MenuRSS = $this->BarrePlanSite->MenuRacine->InscritSousMenuUrl("RSS", $moduleParent->ActionFluxRSS->ObtientUrl()) ;
				}
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->BarrePlanSite->RenduDispositif() ;
				return $ctn ;
			}
		}
		class ScriptAccueilAdminBaseSws extends ScriptBaseSws
		{
			public $AliasMsgBienvenue = "" ;
			public $BlocModules ;
			public $BlocImplems ;
			public $GrilleModules ;
			public $GrilleImplems ;
			public $TableauBord ;
			public $CtnMsgBienvenue = "Bienvenue sur l'espace d'administration" ;
			protected function DetermineTableauBord()
			{
				$this->TableauBord = new TableauBordSws() ;
				$this->TableauBord->AdopteScript("tableauBord", $this) ;
				$this->TableauBord->ChargeConfig() ;
				$this->ChargeTableauBord() ;
				$this->ChargeElemsRenduTableauBord() ;
			}
			protected function ChargeTableauBord()
			{
				$systemeSws = $this->ObtientSystemeSws() ;
				foreach($systemeSws->ImplemsPage as $i => & $implem)
				{
					$implem->RemplitTableauBordAdmin($this->TableauBord, $this) ;
				}
				foreach($systemeSws->ModulesPage as $i => & $module)
				{
					$module->RemplitTableauBordAdmin($this->TableauBord, $this) ;
				}
			}
			protected function ChargeElemsRenduTableauBord()
			{
				$this->BlocModules = $this->TableauBord->InsereBlocVide() ;
				$this->BlocModules->Titre = "Modules" ;
				$this->GrilleModules = $this->BlocModules->DefinitCompPrinc(new GrilleModulesSws()) ;
				$this->BlocImplems = $this->TableauBord->InsereBlocVide() ;
				$this->BlocImplems->Titre = "Impl&eacute;mentations" ;
				$this->GrilleImplems = $this->BlocImplems->DefinitCompPrinc(new GrilleImplemsSws()) ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineTableauBord() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div align="center" class="ui-widget ui-widget-content">' ;
				$ctn .= '<p align="center">'.$this->CtnMsgBienvenue.'</p>' ;
				if($this->ZoneParent->PossedeMembreConnecte())
				{
					$ctn .= $this->TableauBord->RenduDispositif() ;
				}
				else
				{
					$ctn .= '<p><a href="'.$this->ZoneParent->ScriptConnexion->ObtientUrl().'">CONNEXION</a></p>'.PHP_EOL ;
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
			protected function PrepareDoc()
			{
				$sqls = array() ;
				$systemeSws = & ReferentielSws::$SystemeEnCours ;
				$bd = & $systemeSws->BDSupport ;
				$modulePage = $this->ObtientModulePage() ;
				foreach($systemeSws->ModulesPage as $nomModule => $module)
				{
					foreach($module->Entites as $nomEntite => $entite)
					{
						$sql = $entite->ObtientReqSqlFluxRSS() ;
						if($sql != '')
						{
							$sqls[] = $sql.PHP_EOL ;
						}
					}
				}
				foreach($systemeSws->ImplemsPage as $nomImplem => $implem)
				{
					$sql = $implem->ObtientReqSqlFluxRSS() ;
					if($sql != '')
					{
						$sqls[] = $sql.PHP_EOL ;
					}
				}
				$sqlFluxRSS = join(" union ".PHP_EOL, $sqls) ;
				// echo $sqlFluxRSS ;
				if($sqlFluxRSS != "")
				{
					$sqlFluxRSS .= " order by date_publication desc, heure_publication desc limit 0, ".$modulePage->MaxElemsFluxRSS ;
				}
				if($sqlFluxRSS != "")
				{
					$lgns = $bd->FetchSqlRows($sqlFluxRSS) ;
					foreach($lgns as $i => $lgn)
					{
						$elemRendu = $this->ObtientElemRendu($lgn) ;
						$elemRendu->FormatElemLienLgnRSS($lgn) ;
						$this->InscritElemLienLgn($lgn) ;
					}
				}
			}
			protected function & ObtientElemRendu(& $lgn)
			{
				$systemeSws = & ReferentielSws::$SystemeEnCours ;
				$elemRendu = new ElementRenduBaseSws() ;
				if($lgn["nature_rendu"] == "entite")
				{
					$elemRendu = & $systemeSws->ModulesPage[$lgn["groupe_rendu"]]->Entites[$lgn["elem_rendu"]] ;
				}
				elseif($lgn["nature_rendu"] == "implem")
				{
					$elemRendu = & $systemeSws->ImplemsPage[$lgn["elem_rendu"]] ;
				}
				return $elemRendu ;
			}
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