<?php
	
	if(! defined('IMPLEM_COMMENTAIRE_SWS'))
	{
		define("IMPLEM_COMMENTAIRE_SWS", 1) ;
		
		class ImplemCommentaireSws extends ImplemTableSws
		{
			public $NomRef = "commentaire" ;
			public $NomTable = "commentaire" ;
			public $TitreMenu = "Commentaires" ;
			public $Titre = "Commentaires" ;
			public $NomColId = "id" ;
			public $NomColDateCreation = "date_creation" ;
			public $NomColStatutPublication = "statut_publication" ;
			public $NomColEmail = "email" ;
			public $NomColNom = "nom" ;
			public $NomColContenu = "contenu" ;
			public $NomColNomElementModule = "nom_entite" ;
			public $NomColIdEntite = "id_entite" ;
			public $NomColTitreEntite = "titre_entite" ;
			public $NomParamId = "id" ;
			public $NomParamDateCreation = "date_creation" ;
			public $NomParamEmail = "email" ;
			public $NomParamNom = "nom" ;
			public $NomParamContenu = "contenu" ;
			public $NomParamCaptcha = "code_securite" ;
			public $NomParamNomElementModule = "nom_entite" ;
			public $NomParamStatutPublication = "statut_publication" ;
			public $NomParamIdEntite = "id_entite" ;
			public $NomParamTitreEntite = "titre_entite" ;
			public $LibId = "ID" ;
			public $LibDateCreation = "Date cr&eacute;ation" ;
			public $LibStatutPublication = "Statut publication" ;
			public $LibEmail = "Email" ;
			public $LibNom = "Nom" ;
			public $LibContenu = "Contenu" ;
			public $LibNomElementModule = "Nom entite" ;
			public $LibIdEntite = "ID Entite" ;
			public $LibTitreEntite = "Titre Entite" ;
			public $TotalLignesCompContenu = 5 ;
			public $LargeurCompNom = "200px" ;
			public $LargeurCompEmail = "200px" ;
			public $TotalColonnesCompContenu = 60 ;
			public $AutoPublierCmt = 1 ;
			public $LibActions = "Actions" ;
			public $LibLienRejetCmt = "Rejeter" ;
			public $LibLienValideCmt = "Valider" ;
			public $NomClasseCompContenu = "PvZoneMultiligneHtml" ;
			public $FormSoumetCmt ;
			public $FormListeCmt ;
			public $SecuriserEdition = 1 ;
			public $ScriptListeCmt ;
			public $ScriptPublieCmt ;
			public $MsgSuccesSoumetCmt = "Votre commentaire a &eacute;t&eacute; pris en compte" ;
			public function ObtientUrlAdmin()
			{
				return $this->ScriptListeCmt->ObtientUrl() ;
			}
			protected function RemplitZoneAdminValide(& $zone)
			{
				$this->ScriptListeCmt = $this->InscritNouvScript("liste_msgs_".$this->NomElementSyst, new ScriptListeMsgsCmtSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptPublieCmt = $this->InscritNouvScript("publie_msg_".$this->NomElementSyst, new ScriptPublieMsgCmtSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				// print_r($this->ObtientPrivilegesEdit()) ;
			}
			protected function CreeFormSoumetCmt()
			{
				return new FormSoumetCommentaireSws() ;
			}
			protected function CreeTablListeCmt()
			{
				return new TablListeCommentaireSws() ;
			}
			protected function DetermineFormSoumetCmt(& $script, & $entite)
			{
				$this->FormSoumetCmt = $this->CreeFormSoumetCmt() ;
				$this->InitFormSoumetCmt() ;
				$this->FormSoumetCmt->AdopteScript("formSoumetCmt", $script) ;
				$this->FormSoumetCmt->ChargeConfig() ;
				$this->ChargeFormSoumetCmt() ;
			}
			protected function DetermineTablListeCmt(& $script, & $entite)
			{
				$this->TablListeCmt = $this->CreeTablListeCmt() ;
				$this->InitTablListeCmt() ;
				$this->TablListeCmt->AdopteScript("tablListeCmt", $script) ;
				$this->TablListeCmt->ChargeConfig() ;
				$this->ChargeTablListeCmt() ;
			}
			public function PrepareScriptConsult(& $script, & $entite)
			{
				$this->DetermineFormSoumetCmt($script, $entite) ;
				$this->DetermineTablListeCmt($script, $entite) ;
			}
			protected function InitFormSoumetCmt()
			{
			}
			protected function ChargeFormSoumetCmt()
			{
			}
			protected function InitTablListeCmt()
			{
			}
			protected function ChargeTablListeCmt()
			{
			}
		}
		
		class FormSoumetCommentaireSws extends PvFormulaireDonneesHtml
		{
			public $NomImplemPage = "commentaire" ;
			public $InclureTotalElements = 0 ;
			public $InclureElementEnCours = 0 ;
			public $MaxFiltresEditionParLigne = 1 ;
			public $InscrireCommandeAnnuler = 0 ;
			public $LibelleCommandeExecuter = "Envoyer" ;
			public $NomClasseCommandeExecuter = "PvCommandeAjoutElement" ;
			public $FltStatutPubl ;
			public $FltId ;
			public $FltIdEntite ;
			public $FltTitreEntite ;
			public $FltIdCtrl ;
			public $FltNom ;
			public $CompNom ;
			public $FltEmail ;
			public $CompEmail ;
			public $FltContenu ;
			public $CompContenu ;
			public $FltCaptcha ;
			public $CompCaptcha ;
			protected function ObtientImplemPage()
			{
				return ImplemPageBaseSws::ObtientImplemPageComp($this) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$implem = $this->ObtientImplemPage() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				// Fournisseur de donnees
				$this->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->FournisseurDonnees->RequeteSelection = $implem->NomTable ;
				$this->FournisseurDonnees->TableEdition = $implem->NomTable ;
				// Criteres validation
				$this->CommandeExecuter->InsereCritereFormatEmail(array($implem->NomParamEmail)) ;
				$this->CommandeExecuter->InsereCritereNonVide(array($implem->NomParamNom, $implem->NomParamContenu)) ;
				// Securite
				if($implem->SecuriserEdition)
				{
					$this->FltCaptcha = $this->InsereFltEditHttpPost($implem->NomParamCaptcha) ;
					$this->FltCaptcha->Libelle = "Code de s&eacute;curit&eacute;" ;
					$this->CompCaptcha = $this->FltCaptcha->DeclareComposant("PvZoneCommonCaptcha") ;
					$this->CompCaptcha->ActionAffichImg->Params = array($entite->NomParamId => $entite->LgnEnCours["id"]) ;
					$this->CommandeExecuter->InsereNouvCritere(new CritrCodeSecurValideCmtSws()) ;
				}
				// Messages
				$this->CommandeExecuter->MessageSuccesExecution = $implem->MsgSuccesSoumetCmt ;
			}
			protected function ChargeFiltresSelection()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$implem = $this->ObtientImplemPage() ;
				$bd = $entite->ModuleParent->ObtientBDSupport() ;
				$this->InsereFltSelectHttpGet($entite->NomParamId, $bd->EscapeVariableName($implem->NomColIdEntite)."=<self>") ;
			}
			protected function ChargeFiltresEdition()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$implem = $this->ObtientImplemPage() ;
				// ID Entite
				$this->FltIdEntite = $this->InsereFltEditFixe($implem->NomParamIdEntite, $entite->LgnEnCours["id"], $implem->NomColIdEntite) ;
				// Titre Entite
				$this->FltTitreEntite = $this->InsereFltEditFixe($implem->NomParamTitreEntite, $entite->LgnEnCours["titre"], $implem->NomColTitreEntite) ;
				// Nom Entite
				$this->FltNomElementModule = $this->InsereFltEditFixe($implem->NomParamNomElementModule, $entite->NomElementModule, $implem->NomColNomElementModule) ;
				// Statut publication
				$this->FltStatutPubl = $this->InsereFltEditFixe($implem->NomParamStatutPublication, $implem->AutoPublierCmt, $implem->NomColStatutPublication) ;
				// Nom
				$this->FltNom = $this->InsereFltEditHttpPost($implem->NomParamNom, $implem->NomColNom) ;
				$this->FltNom->Libelle = $implem->LibNom ;
				$this->FltNom->AccepteTagsHtml = 0 ;
				$this->FltNom->Obligatoire = 1 ;
				$this->CompNom = $this->FltNom->ObtientComposant() ;
				$this->CompNom->Largeur = $implem->LargeurCompNom ;
				// Email
				$this->FltEmail = $this->InsereFltEditHttpPost($implem->NomParamEmail, $implem->NomColEmail) ;
				$this->FltEmail->Libelle = $implem->LibEmail ;
				$this->FltEmail->Obligatoire = 1 ;
				$this->FltEmail->AccepteTagsHtml = 0 ;
				$this->CompEmail = $this->FltEmail->ObtientComposant() ;
				$this->CompEmail->Largeur = $implem->LargeurCompEmail ;
				// Contenu
				$this->FltContenu = $this->InsereFltEditHttpPost($implem->NomParamContenu, $implem->NomColContenu) ;
				$this->FltContenu->AccepteTagsHtml = 0 ;
				$this->FltContenu->Libelle = $implem->LibContenu ;
				$this->FltContenu->Obligatoire = 1 ;
				// Comp contenu
				$this->CompContenu = $this->FltContenu->DeclareComposant($implem->NomClasseCompContenu) ;
				$this->CompContenu->TotalLignes = $implem->TotalLignesCompContenu ;
				$this->CompContenu->TotalColonnes = $implem->TotalColonnesCompContenu ;
			}
		}
		class TablListeCommentaireSws extends PvGrilleDonneesHtml
		{
			public $NomImplemPage = "commentaire" ;
			public $FltIdEntite ;
			public $FltNomEntite ;
			public $FltStatutPubl ;
			public $DefColDateCreation ;
			public $DefColDateCreationFmt ;
			public $DefColId ;
			public $DefColNom ;
			public $DelColContenu ;
			public $AlerterAucunElement = 0 ;
			public $MessageAucunElement = "Aucun commentaire n'a &eacute;t&eacute; post&eacute;" ;
			protected function ObtientImplemPage()
			{
				return ImplemPageBaseSws::ObtientImplemPageComp($this) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$implem = $this->ObtientImplemPage() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$this->ChargeFltsSelect() ;
				$this->ChargeDefCols() ;
				// Fournisseur de donnees
				$this->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->FournisseurDonnees->RequeteSelection = $implem->NomTable ;
				$this->FournisseurDonnees->TableEdition = $implem->NomTable ;
				// Tri
				$this->AccepterTriColonneInvisible = 1 ;
				$this->TriPossible = 0 ;
				$this->SensColonneTri = "desc" ;
				// Parametres suppls
				$this->ParamsGetSoumetFormulaire[] = $entite->NomParamId ;
				// 
				$this->ContenuLigneModele = '<div class="lgn_cmt"><div><b class="auteur_cmt">${'.$implem->NomColNom.'}</b> <i class="date_cmt">${date_creation_fmt}</i></div><div class="contenu_cmt">${'.$implem->NomColContenu.'}</div></div>' ;
			}
			protected function ChargeFltsSelect()
			{
				$implem = $this->ObtientImplemPage() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $implem->ObtientBDSupport() ;
				$this->FltIdEntite = $this->InsereFltSelectFixe('fltIdEntite', $entite->LgnEnCours["id"], $bd->EscapeVariableName($implem->NomColIdEntite).' = <self>') ;
				$this->FltNomEntite = $this->InsereFltSelectFixe('fltNomEntite', $entite->NomElementModule, $bd->EscapeVariableName($implem->NomColNomElementModule).' = <self>') ;
				$this->FltStatutPubl = $this->InsereFltSelectFixe('fltStatutPubl', 1, $bd->EscapeVariableName($implem->NomColStatutPublication).' = <self>') ;
			}
			protected function ChargeDefCols()
			{
				$implem = $this->ObtientImplemPage() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $implem->ObtientBDSupport() ;
				$this->DefColDateCreation = $this->InsereDefColCachee($implem->NomColDateCreation) ;
				$this->DefColId = $this->InsereDefColCachee($implem->NomColId) ;
				$this->DefColNom = $this->InsereDefCol($implem->NomColNom, $implem->LibNom) ;
				$this->DefColDateCreationFmt = $this->InsereDefCol($implem->NomColDateCreation, $implem->LibDateCreation) ;
				$this->DefColDateCreationFmt = $this->InsereDefCol("date_creation_fmt", $implem->LibDateCreation) ;
				$this->DefColDateCreationFmt->AliasDonnees = $bd->SqlDateToStrFr($implem->NomColDateCreation, 1) ;
				$this->DefColContenu = $this->InsereDefCol($implem->NomColContenu, $implem->LibContenu) ;
			}
		}
		class CritrCodeSecurValideCmtSws extends PvCritereBase
		{
			public $MessageErreur = "Le code de s&eacute;curit&eacute; saisi est incorrect" ;
			public function EstRespecte()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$form = & $this->FormulaireDonneesParent ;
				if($form->Editable == 0)
				{
					return 1 ;
				}
				$ok = $form->FltCaptcha->Composant->ActionAffichImg->VerifieValeurSoumise($form->FltCaptcha->Lie()) ;
				return $ok ;
			}
		}
		
		class ScriptListeMsgsCmtSws extends ScriptAdminBaseSws
		{
			public $Titre = "Commentaires post&eacute;s" ;
			public $TablListeCmt ;
			public $CompTypeEntiteCmt ;
			public $FltTypeEntiteCmt ;
			public $FltContenuCmt ;
			public $FltDateDebutCmt ;
			public $FltDateFinCmt ;
			public $DefColIdCmt;
			public $DefColNomCmt;
			public $DefColEmailCmt;
			public $DefColTitreEntiteCmt;
			public $DefColContenuCmt;
			public $DefColDateCreationCmt;
			public $DefColNomEntiteCmt ;
			public $DefColStatutPublCmt ;
			public $DefColActionsCmt;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineTableListeCmt() ;
			}
			protected function DetermineTableListeCmt()
			{
				$implem = $this->ObtientImplemPage() ;
				$bd = $this->ObtientBDSupport() ;
				$this->TablListeCmt = $this->CreeTableListeCmt() ;
				$this->TablListeCmt->AdopteScript("liste_cmts", $this) ;
				$this->TablListeCmt->ChargeConfig() ;
				$this->FltContenuCmt = $this->TablListeCmt->InsereFltSelectHttpPost("titre", "(".$bd->SqlIndexOf('upper('.$bd->EscapeVariableName($implem->NomColContenu).')', 'upper(<self>)').' >= 1 or '.$bd->SqlIndexOf('upper('.$bd->EscapeVariableName($implem->NomColEmail).')', 'upper(<self>)').' >= 1 or '.$bd->SqlIndexOf('upper('.$bd->EscapeVariableName($implem->NomColNom).')', 'upper(<self>)')." >= 1)") ;
				$this->FltContenuCmt->Libelle = $implem->LibContenu ;
				$this->FltTypeEntiteCmt = $this->TablListeCmt->InsereFltSelectHttpPost("nom_elem_module", $bd->EscapeVariableName($implem->NomColNomElementModule).'=<self>') ;
				$this->FltTypeEntiteCmt->Libelle = $implem->LibNomElementModule ;
				$this->CompTypeEntiteCmt = $this->FltTypeEntiteCmt->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$this->CompTypeEntiteCmt->FournisseurDonnees = $implem->ObtientFournEntitesAppl() ;
				$this->CompTypeEntiteCmt->InclureElementHorsLigne = 1 ;
				$this->CompTypeEntiteCmt->FournisseurDonnees->RequeteSelection = "entitesAppl" ;
				$this->CompTypeEntiteCmt->NomColonneLibelle = "titre" ;
				$this->CompTypeEntiteCmt->NomColonneValeur = "nom" ;
				$this->FltDateDebutCmt = $this->TablListeCmt->InsereFltSelectHttpPost("date_debut", $bd->SqlDatePart($bd->EscapeVariableName($implem->NomColDateCreation)).' >= <self>') ;
				$this->FltDateDebutCmt->Libelle = "Date debut" ;
				$this->FltDateDebutCmt->DeclareComposant("PvCalendarDateInput") ;
				$this->FltDateFinCmt = $this->TablListeCmt->InsereFltSelectHttpPost("date_fin", $bd->SqlDatePart($bd->EscapeVariableName($implem->NomColDateCreation)).' <= <self>') ;
				$this->FltDateFinCmt->Libelle = "Date fin" ;
				$this->FltDateFinCmt->DeclareComposant("PvCalendarDateInput") ;
				$this->TablListeCmt->ToujoursAfficher = 1 ;
				$this->TablListeCmt->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->TablListeCmt->FournisseurDonnees->RequeteSelection = $implem->NomTable ;
				$this->DefColIdCmt = $this->TablListeCmt->InsereDefColCachee($implem->NomColId) ;
				$this->DefColDateCreationCmt = $this->TablListeCmt->InsereDefCol("date_creation_fmt", $implem->LibDateCreation) ;
				$this->DefColDateCreationCmt->AliasDonnees = $bd->SqlDateToStrFr($implem->NomColDateCreation, true) ;
				$this->DefColDateCreationCmt->NomDonneesTri = $implem->NomColDateCreation ;
				$this->DefColDateCreationCmt->Largeur = "12%" ;
				$this->DefColDateCreationCmt->AlignElement = "center" ;
				$this->DefColNomEntiteCmt = $this->TablListeCmt->InsereDefColChoix($implem->NomColNomElementModule, $implem->LibNomElementModule, "", $implem->ObtientNomEntitesAppl()) ;
				$this->DefColNomEntiteCmt->Largeur = "10%" ;
				$this->DefColNomEntiteCmt->AlignElement = "center" ;
				$this->DefColTitreEntiteCmt = $this->TablListeCmt->InsereDefCol($implem->NomColTitreEntite, $implem->LibTitreEntite) ;
				$this->DefColTitreEntiteCmt->Largeur = "18%" ;
				$this->DefColStatutPublCmt = $this->TablListeCmt->InsereDefColBool($implem->NomColStatutPublication, $implem->LibStatutPublication) ;
				$this->DefColStatutPublCmt->AlignElement = "center" ;
				$this->DefColStatutPublCmt->Largeur = "6%" ;
				$this->DefColNomCmt = $this->TablListeCmt->InsereDefCol($implem->NomColNom, $implem->LibNom) ;
				$this->DefColNomCmt->Largeur = "12%" ;
				$this->DefColEmailCmt = $this->TablListeCmt->InsereDefCol($implem->NomColEmail, $implem->LibEmail) ;
				$this->DefColEmailCmt->Largeur = "10%" ;
				$this->DefColIntroContenuCmt = $this->TablListeCmt->InsereDefColHtml("", $implem->LibContenu) ;
				$this->DefColIntroContenuCmt->Largeur = "20%" ;
				$this->DefColIntroContenuCmt->NomDonnees = $implem->NomColContenu ;
				$this->DefColIntroContenuCmt->ExtracteurValeur = new PvExtracteurIntroDonnees() ;
				$this->DefColIntroContenuCmt->Formatteur->ModeleHtml = '${contenu_intro}' ;
				$this->DefColActionsCmt = $this->TablListeCmt->InsereDefColActions($implem->LibActions) ;
				$this->LienRejetCmt = $this->TablListeCmt->InsereLienAction($this->DefColActionsCmt, $implem->ScriptPublieCmt->ObtientUrl().'&id=${'.$implem->NomColId.'}&valeur_publ=0', $implem->LibLienRejetCmt) ;
				$this->LienRejetCmt->NomDonneesValid = $implem->NomColStatutPublication ;
				$this->LienRejetCmt->ValeurVraiValid = 1 ;
				$this->LienValideCmt = $this->TablListeCmt->InsereLienAction($this->DefColActionsCmt, $implem->ScriptPublieCmt->ObtientUrl().'&id=${'.$implem->NomColId.'}&valeur_publ=0', $implem->LibLienValideCmt) ;
				$this->LienValideCmt->NomDonneesValid = $implem->NomColStatutPublication ;
				$this->LienValideCmt->ValeurVraiValid = 0 ;
			}
			protected function CreeTableListeCmt()
			{
				return new PvTableauDonneesHtml() ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				// $ctn .= '<p>mmmm</p>' ;
				$ctn .= $this->TablListeCmt->RenduDispositif() ;
				return $ctn ;
			}
		}
		class ScriptPublieMsgCmtSws extends ScriptAdminBaseSws
		{
			public $Titre = "Modification statut de publication du commentaire" ;
			public $ValIdCmtSoumis ;
			public $LgnCmtSoumis ;
			public $ValPublSoumis = 0 ;
			public $SuccesChangPubl = 0 ;
			protected $MsgResultat ;
			public $FormatMsgSucces ;
			public $LibCmtValide = "Valid&eacute;";
			public $LibCmtRejete = "Rejet&eacute;" ;
			public $LibRetourListeCmt = "Retour aux commentaires post&eacute;s" ;
			public $MsgErreur = "Le commentaire &agrave; modifier n'existe pas." ;
			public function DetermineEnvironnement()
			{
				$this->DetermineMsgs() ;
				$this->DetecteLgnCmtSoumis() ;
			}
			protected function DetermineMsgs()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->FormatMsgSucces = 'Le commentaire <b>${'.$implem->NomColContenu.'_intro}</b> de ${'.$implem->NomColNom.'} sur <b>${'.$implem->NomColTitreEntite.'} a &eacute;t&eacute; ${statut}</b>' ;
			}
			protected function DetecteLgnCmtSoumis()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->ValIdCmtSoumis = intval((isset($_GET["id"])) ? $_GET["id"] : '') ;
				$this->ValPublSoumis = (intval((isset($_GET["valeur_publ"])) ? $_GET["valeur_publ"] : '') == 0) ? 0 : 1 ;
				$bd = $this->ObtientBDSupport() ;
				$this->LgnCmtSoumis = $bd->FetchSqlRow('select * from '.$bd->EscapeTableName($implem->NomTable).' where '.$bd->EscapeVariableName($implem->NomColId).' = :idSoumis', array('idSoumis' => $this->ValIdCmtSoumis)) ;
				if(is_array($this->LgnCmtSoumis))
				{
					// print_r($this->LgnCmtSoumis) ;
					if(count($this->LgnCmtSoumis) > 0)
					{
						$this->SuccesChangPubl = $bd->RunSql('update '.$bd->EscapeTableName($implem->NomTable).' set '.$bd->EscapeVariableName($implem->NomColStatutPublication).'=:statutPublication where '.$bd->EscapeVariableName($implem->NomColId).' = :idSoumis', array('idSoumis' => $this->ValIdCmtSoumis, 'statutPublication' => $this->ValPublSoumis)) ;
						if($this->SuccesChangPubl)
						{
							$lgnCmt = array_merge($this->LgnCmtSoumis, array($implem->NomColContenu.'_intro' => intro($this->LgnCmtSoumis[$implem->NomColContenu]), 'statut' => ($this->ValPublSoumis == 0) ? $this->LibCmtRejete : $this->LibCmtValide)) ;
							$this->MsgResultat = _parse_pattern($this->FormatMsgSucces, $lgnCmt) ;
						}
						else
						{
							$this->MsgResultat = $this->MsgErreur ;
						}
					}
					else
					{
						$this->MsgResultat = $this->MsgErreur ;
					}
				}
				else
				{
					$this->MsgResultat = $this->MsgErreur ;
				}
			}
			public function RenduSpecifique()
			{
				$implem = $this->ObtientImplemPage() ;
				$ctn = '' ;
				$ctn .= '<div class="ui-widget ui-widget-content">'.PHP_EOL ;
				$ctn .= '<p>'.$this->MsgResultat.'</p>'.PHP_EOL ;
				$ctn .= '<p><a href="'.$implem->ScriptListeCmt->ObtientUrl().'">'.$this->LibRetourListeCmt.'</a></p>'.PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
	}
	
?>