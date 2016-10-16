<?php
	
	if(! defined('MODULE_CONTACT_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__).'/Noyau.class.php' ;
		}
		define('MODULE_CONTACT_SWS', 1) ;
		
		class ModuleContactSws extends ModulePageBaseSws
		{
			public $TitreMenu = "Forms contact" ;
			public $NomRef = "contact" ;
			public $EntiteMsg ;
			public $EntiteForm ;
			protected function CreeEntiteMsg()
			{
				return new EntiteMsgContactSws() ;
			}
			protected function CreeEntiteForm()
			{
				return new EntiteFormContactSws() ;
			}
			protected function ChargeEntites()
			{
				$this->EntiteForm = $this->InsereEntite("form", $this->CreeEntiteForm()) ;
				$this->EntiteMsg = $this->InsereEntite("msg", $this->CreeEntiteMsg()) ;
			}
		}
		
		class EntiteMsgContactSws extends EntiteTableSws
		{
			public $NomClasseCmdAjout = "CmdAjoutMsgContactSws" ;
			public $TitreMenu = "Messages" ;
			public $TitreAjoutEntite = "Ajout message" ;
			public $TitreModifEntite = "Modification message" ;
			public $TitreSupprEntite = "Suppression message" ;
			public $TitreListageEntite = "Liste des messages" ;
			public $TitreConsultEntite = "D&eacute;tails messages" ;
			public $NomEntite = "msg_contact" ;
			public $NomTable = "msg_contact" ;
			public $NomTableMails = "mails_msg_contact" ;
			public $NomColSujetMail = "sujet" ;
			public $NomColIdMsgMail = "id_msg" ;
			public $NomColFromMail = "from" ;
			public $NomColToMail = "to" ;
			public $NomColCorpsMail = "corps" ;
			public $NomColSuccesMail = "succes" ;
			public $AccepterSommaire = 0 ;
			public $AccepterGraphique = 0 ;
			public $SecuriserEdition = 1 ;
			public $AccepterTexte = 1 ;
			public $NomParamNom = "nom" ;
			public $NomParamEmail = "email" ;
			public $NomParamContenu = "contenu" ;
			public $NomParamIdForm = "id_form" ;
			public $ValeurParamId = 0 ;
			public $ValeurParamIdForm = 0 ;
			public $NomColIdForm = "id_form" ;
			public $NomColNom = "nom" ;
			public $NomColEmail = "email" ;
			public $NomColContenu = "contenu" ;
			public $FltFrmElemNom ;
			public $FltFrmElemEmail ;
			public $LibNom = "Nom &amp; Pr&eacute;nom" ;
			public $LibEmail = "Email" ;
			public $LibContenu = "Message" ;
			public $LibForm = "Formulaire" ;
			public $TotalColonnesContenu = 80 ;
			public $TotalLignesContenu = 6 ;
			public $LgnForm ;
			public $PosterMessageSucces = "Votre message a &eacute;t&eacute; envoy&eacute;" ;
			public $LibelleLienRetourForm = "Retour au formulaire" ;
			public $NomScriptPoster = "poster" ;
			public $ScriptPoster ;
			public $DefColTblListEmail ;
			public $DefColTblListNom;
			public $DefColTblListForm ;
			public $FltTblListEmail ;
			public $FltTblListContenu ;
			public $FltTblListForm ;
			public $NomParamTblListContenu = "pContenu" ;
			public $NomParamTblListForm = "pIdForm" ;
			public $HauteurMaxLogoContact = 60 ;
			protected function CreeScriptPoster()
			{
				return new ScriptPosterContactSws() ;
			}
			public function RemplitZonePubl(& $zone)
			{
				parent::RemplitZonePubl($zone) ;
				$this->ScriptPoster = $this->InsereScript($this->NomScriptPoster.'_'.$this->NomEntite, $this->CreeScriptPoster(), $zone, $this->PrivilegesConsult) ;
			}
			protected function SqlSelectLgnForm(& $bd)
			{
				$sql = 'select '.$this->ModuleParent->EntiteForm->SqlListeColsSelect($bd).' from (select t2.*, t1.'.$bd->EscapeVariableName($this->NomColId).' id_msg from '.$bd->EscapeVariableName($this->NomTable).' t1 inner join '.$bd->EscapeVariableName($this->ModuleParent->EntiteForm->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdForm).'=t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteForm->NomColId).') t1 where id_msg='.$bd->ParamPrefix.'id' ;
				return $sql ;
			}
			protected function SelectLgnForm($id)
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = $this->SqlSelectLgnForm($bd) ;
				$lgn = $bd->FetchSqlRow($sql, array('id' => $id)) ;
				return $lgn ;
			}
			protected function DetecteLgnForm(& $script)
			{
				$bd = $this->ObtientBDSupport() ;
				if($script->InitFrmElem->Role == "Ajout")
				{
					$this->ValeurParamIdForm = intval(isset($_GET[$this->NomParamIdForm]) ? $_GET[$this->NomParamIdForm] : "") ;
					$this->LgnForm = $this->ModuleParent->EntiteForm->SelectLgn($this->ValeurParamIdForm) ;
					// print_r($bd) ;
					// exit ;
				}
				else
				{
					$this->ValeurParamId = intval(isset($_GET[$this->NomParamId]) ? $_GET[$this->NomParamId] : "") ;
					$this->LgnForm = $this->SelectLgnForm($this->ValeurParamId) ;
				}
				return (is_array($this->LgnForm) && count($this->LgnForm) > 0) ;
			}
			protected function PrepareScriptEdit(& $script)
			{
				parent::PrepareScriptEdit($script) ;
				if(isset($script->PourPublication))
				{
					$script->TitreDocument = "Contacts" ;
				}
			}
			protected function VerifPreReqsScriptEdit(& $script)
			{
				if(is_array($this->LgnForm))
					return (count($this->LgnForm) > 0) ;
				return $this->DetecteLgnForm($script) ;
			}
			protected function RenduFormParent(& $script)
			{
				$syst = ReferentielSws::$SystemeEnCours ;
				$ctn = '' ;
				$ctn .= '<h3 class="titre">'.htmlentities($this->LgnForm["titre"]).'</h3>'.PHP_EOL ;
				$ctn .= '<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="*">'.PHP_EOL ;
				$ctn .= '<table cellspacing="0" cellpadding="2" class="details_contact">
<tr><td colspan="2"><b>'.htmlentities(strtoupper($this->LgnForm["nom_societe"])).'</b></td></tr>'.PHP_EOL ;
				if($this->LgnForm["adresse"] != "")
				{
					$ctn .= '<tr><td colspan="2">'.htmlentities($this->LgnForm["adresse"]).'</td></tr>' ;
				}
				if($this->LgnForm["tel"] != "")
				{
					$ctn .= '<tr><td>Tel : </td><td>'.htmlentities($this->LgnForm["tel"]).'</td></tr>' ;
				}
				if($this->LgnForm["fax"] != "")
				{
					$ctn .= '<tr><td>Fax : </td><td>'.htmlentities($this->LgnForm["fax"]).'</td></tr>' ;
				}
				$ctn .= '</table>
</td>'.PHP_EOL ;
				if($this->LgnForm["chemin_logo"] != "")
				{
					$ctn .= '<td width="30%" valign="top"><img src="'.$syst->ObtientCheminPubl($this->LgnForm["chemin_logo"]).'" height="'.$this->HauteurMaxLogoContact.'" /></td>'.PHP_EOL ;
				}
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>' ;
				return $ctn ;
			}
			protected function RenduLienRetourForm(& $script)
			{
				$ctn = '<p><a href="'.$script->ObtientUrlParam(array($this->NomParamIdForm => $this->LgnForm["id"])).'">'.$this->LibelleLienRetourForm.'</a></p>' ;
				return $ctn ;
			}
			public function RenduScriptEdit(& $script)
			{
				$ctn = '' ;
				$pourPoster = (isset($script->PourPublication)) ? 1 : 0 ;
				if(! $pourPoster)
				{
					$ctn .= $this->RenduAvantCtnSpec($script).PHP_EOL ;
					$ctn .= $this->RenduTitreScript($script).PHP_EOL ;
				}
				else
				{
					$ctn .= $this->RenduFormParent($script).PHP_EOL ;
					// $ctn .= $this->
				}
				$ctn .= $this->FrmElem->RenduDispositif().PHP_EOL ;
				if(! $pourPoster)
				{
					$ctn .= $this->RenduApresCtnSpec($script) ;
				}
				else
				{
					if($this->FrmElem->CommandeSelectionneeExec)
					{
						$ctn .= $this->RenduLienRetourForm($script).PHP_EOL ;
					}
				}
				return $ctn ;
			}
			protected function RemplitMenuInt(& $menu)
			{
				parent::RemplitMenuInt($menu) ;
				if($this->InclureScriptEdit)
				{
					$this->SousMenuAjout->ParamsScript = array($this->NomParamIdForm => 1) ;
				}
			}
			protected function FinalTblList(& $tabl)
			{
				parent::FinalTblList($tabl) ;
				if($this->InclureScriptEdit)
				{
					$this->CmdAjoutTblList->Parametres = array($this->NomParamIdForm => 1) ;
				}
			}
			protected function ObtientParamsUrlFrmElem(& $frm)
			{
				if($frm->ScriptParent->InitFrmElem->Role == "Ajout")
				{
					return array($this->NomParamIdForm => $this->LgnForm["id"]) ;
				}
				return parent::ObtientParamsUrlFrmElem($frm) ;
			}
			protected function InitFrmElem(& $frm, & $script)
			{
				$poster = (isset($script->PourPublication)) ? 1 : 0;
				parent::InitFrmElem($frm, $script) ;
				if($poster)
				{
					$frm->LibelleCommandeExecuter = "Envoyer" ;
					$frm->InscrireCommandeAnnuler = 0 ;
				}
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				$this->FltFrmElemNom = $frm->InsereFltEditHttpPost($this->NomParamNom, $this->NomColNom) ;
				$this->FltFrmElemNom->Libelle = $this->LibNom ;
				$this->FltFrmElemEmail = $frm->InsereFltEditHttpPost($this->NomParamEmail, $this->NomColEmail) ;
				$this->FltFrmElemEmail->Libelle = $this->LibEmail ;
				$this->FltFrmElemContenu = $frm->InsereFltEditHttpPost($this->NomParamContenu, $this->NomColContenu) ;
				$this->FltFrmElemContenu->Libelle = $this->LibContenu ;
				$comp = $this->FltFrmElemContenu->DeclareComposant("PvZoneMultiligneHtml") ;
				$comp->TotalColonnes = $this->TotalColonnesContenu ;
				$comp->TotalLignes = $this->TotalLignesContenu ;
				if(isset($frm->ScriptParent->PourPublication))
				{
					$this->FltFrmElemIdForm = $frm->InsereFltEditHttpGet($this->NomParamIdForm, $this->NomColIdForm) ;
					$this->FltFrmElemIdForm->LectureSeule = 1 ;
					$this->FigeFiltresPubl() ;
					$frm->CacherFormulaireFiltresApresCmd = 1 ;
					$frm->MsgExecSuccesCommandeExecuter = $this->PosterMessageSucces ;
				}
			}
			protected function ReqSelectTblList(& $bd)
			{
				$sql = '(select t1.*, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteForm->NomColId).' id_form_parent,  t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteForm->NomColTitre).' titre_form_parent from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($this->ModuleParent->EntiteForm->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdForm).' = t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteForm->NomColId).')' ;
				return $sql ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				$tbl->FournisseurDonnees->RequeteSelection = $this->ReqSelectTblList($bd) ;
				$this->DefColTblListNom = $tbl->InsereDefCol($this->NomColNom, $this->LibNom) ;
				$this->DefColTblListNom->Largeur = "20%" ;
				$this->DefColTblListEmail = $tbl->InsereDefCol($this->NomColEmail, $this->LibEmail) ;
				$this->DefColTblListEmail->Largeur = "20%" ;
				$this->DefColTblListForm = $tbl->InsereDefCol("titre_form_parent", $this->LibForm) ;
				$this->DefColTblListForm->Largeur = "15%" ;
				$this->FltTblListForm = $tbl->InsereFltSelectHttpGet($this->NomParamTblListForm, $bd->EscapeVariableName($this->NomColIdForm).' = <self>') ;
				$this->FltTblListForm->Libelle = $this->LibForm ;
				$compForm = $this->FltTblListForm->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$compForm->FournisseurDonnees = $this->CreeFournDonnees() ;
				$compForm->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteForm->NomTable ;
				$fltFormSelect = $tbl->ScriptParent->CreeFiltreHttpGet($this->NomParamTblListForm) ;
				$compForm->InclureElementHorsLigne = 1 ;
				$compForm->FiltresSelection[] = & $fltFormSelect ;
				$compForm->NomColonneValeur = $this->ModuleParent->EntiteForm->NomColId ;
				$compForm->NomColonneLibelle = $this->ModuleParent->EntiteForm->NomColTitre ;
			}
		}
		class EntiteFormContactSws extends EntiteTableSws
		{
			public $TitreMenu = "Formulaires" ;
			public $TitreAjoutEntite = "Ajout formulaire" ;
			public $TitreModifEntite = "Modification formulaire" ;
			public $TitreSupprEntite = "Suppression formulaire" ;
			public $TitreListageEntite = "Liste des formulaires" ;
			public $TitreConsultEntite = "D&eacute;tails formulaires" ;
			public $NomEntite = "form_contact" ;
			public $NomTable = "form_contact" ;
			public $SecuriserEdition = 1 ;
			public $AccepterSommaire = 0 ;
			public $AccepterGraphique = 0 ;
			public $InclureScriptConsult = 0 ;
			public $InclureScriptEnum = 0 ;
			public $AccepterTexte = 1 ;
			public $FltFrmElemTitre ;
			public $FltFrmElemNomSociete ;
			public $FltFrmElemAdresse ;
			public $FltFrmElemEmail ;
			public $FltFrmElemTel ;
			public $FltFrmElemFax ;
			public $FltFrmElemCheminLogo ;
			public $NomParamTitre = "titre" ;
			public $NomParamNomSociete = "nom_societe" ;
			public $NomParamAdresse = "adresse" ;
			public $NomParamEmail = "email" ;
			public $NomParamTel = "tel" ;
			public $NomParamFax = "fax" ;
			public $NomParamCheminLogo = "chemin_logo" ;
			public $NomColTitre = "titre" ;
			public $NomColNomSociete = "nom_societe" ;
			public $NomColAdresse = "adresse" ;
			public $NomColEmail  = "email";
			public $NomColTel = "tel" ;
			public $NomColFax = "fax" ;
			public $NomColCheminLogo = "chemin_logo" ;
			public $LibTitre = "Titre" ;
			public $LibNomSociete = "Nom de la soci&eacute;t&eacute;" ;
			public $LibAdresse = "Adresse" ;
			public $LibEmail  = "Email";
			public $LibTel = "Tel" ;
			public $LibFax = "Fax" ;
			public $LibCheminLogo = "Logo" ;
			public $CheminTelechargLogos = "images" ;
			public $DefColTblListTitre ;
			public $FltTblListTitre ;
			public $NomParamTblListTitre = "pTitre" ;
			public $FltFrmElemContenu ;
			public $NomParamActiverEnvoiMail = "activer_envoi_mail" ;
			public $NomColActiverEnvoiMail = "activer_envoi_mail" ;
			public $FltFrmElemActiverEnvoiMail ;
			public $FltFrmElemEmailsContact ;
			public $LibActiverEnvoi = "Envoyer par mail" ;
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColNomSociete).' nom_societe' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColAdresse).' adresse' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColEmail).' email' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTel).' tel' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColFax).' fax' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminLogo).' chemin_logo' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Titre
				$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
				$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
				// Nom de la societe
				$this->FltFrmElemNomSociete = $frm->InsereFltEditHttpPost($this->NomParamNomSociete, $this->NomColNomSociete) ;
				$this->FltFrmElemNomSociete->Libelle = $this->LibNomSociete ;
				// Adresse
				$this->FltFrmElemAdresse = $frm->InsereFltEditHttpPost($this->NomParamAdresse, $this->NomColAdresse) ;
				$this->FltFrmElemAdresse->Libelle = $this->LibAdresse ;
				// Activer envoi mail
				$this->FltFrmElemActiverEnvoiMail = $frm->InsereFltEditHttpPost($this->NomParamActiverEnvoiMail, $this->NomColActiverEnvoiMail) ;
				$this->FltFrmElemActiverEnvoiMail->DeclareComposant("PvZoneSelectBoolHtml") ;
				$this->FltFrmElemActiverEnvoiMail->Libelle = $this->LibActiverEnvoi ;
				$this->FltFrmElemActiverEnvoiMail->ValeurParDefaut = 1 ;
				// Email
				$this->FltFrmElemEmail = $frm->InsereFltEditHttpPost($this->NomParamEmail, $this->NomColEmail) ;
				$this->FltFrmElemEmail->Libelle = $this->LibEmail ;
				// Tel
				$this->FltFrmElemTel = $frm->InsereFltEditHttpPost($this->NomParamTel, $this->NomColTel) ;
				$this->FltFrmElemTel->Libelle = $this->LibTel ;
				// Fax
				$this->FltFrmElemFax = $frm->InsereFltEditHttpPost($this->NomParamFax, $this->NomColFax) ;
				$this->FltFrmElemFax->Libelle = $this->LibFax ;
				// Chemin Logos
				$this->FltFrmElemCheminLogo = $frm->InsereFltEditHttpUpload($this->NomParamCheminLogo, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargLogos, $this->NomColCheminLogo) ;
				$this->FltFrmElemCheminLogo->AccepteImgsSeulem() ;
				$this->FltFrmElemCheminLogo->Libelle = $this->LibCheminLogo ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				$this->DefColTblListTitre = $tbl->InsereDefCol($this->NomColTitre, $this->LibTitre) ;
				$this->DefColTblListTitre->Largeur = "50%" ;
				$this->FltTblListTitre = $tbl->InsereFltSelectHttpGet($this->NomParamTblListTitre, $bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColTitre).')', 'UPPER(<self>)').' > 0') ;
				$this->FltTblListTitre->Libelle = $this->LibTitre ;
			}
		}
		
		class ScriptPosterContactSws extends ScriptAjoutEntiteTableSws
		{
			public $PourPublication = 1 ;
			public $NecessiteMembreConnecte = 0 ;
		}
		
		class CmdAjoutMsgContactSws extends CmdAjoutEntiteSws
		{
			public function ExecuteInstructions()
			{
				parent::ExecuteInstructions() ;
				if($this->StatutExecution == 1)
				{
					$entitePage = $this->ObtientEntitePage() ;
					$entiteFormContact = $entitePage->ModuleParent->EntiteForm ;
					$idForm = $entitePage->FltFrmElemIdForm->Lie() ;
					$idCtrl = $entitePage->FltFrmElemIdCtrl->Lie() ;
					$bd = $this->ObtientBDSupport() ;
					$sql = "select t1.*, t2.* from ".$bd->EscapeTableName($entitePage->NomTable)." t1 left join ".$bd->EscapeTableName($entiteFormContact->NomTable)." t2 on t1.".$bd->EscapeVariableName($entitePage->NomColIdForm)." = t2.".$bd->EscapeVariableName($entiteFormContact->NomColId)." where t2.".$bd->EscapeVariableName($entiteFormContact->NomColId)." = 1 and t1.".$bd->EscapeVariableName($entitePage->NomColIdCtrl)." = ".$bd->ParamPrefix."idCtrl" ;
					$lgn = $bd->FetchSqlRow($sql, array("idCtrl" => $idCtrl)) ;
					$activerEnvoiMail = $lgn[$entiteFormContact->NomColActiverEnvoiMail] ;
					if(! $activerEnvoiMail)
						return ;
					$ok = send_html_mail($lgn[$entiteFormContact->NomColEmail], "Contact de la part de ".$entitePage->FltFrmElemNom->Lie(), $entitePage->FltFrmElemContenu->Lie(), $entitePage->FltFrmElemEmail->Lie()) ;
				}
			}
		}
	}
	
?>