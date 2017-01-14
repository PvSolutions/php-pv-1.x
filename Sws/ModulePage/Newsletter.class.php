<?php
	
	if(! defined('ENTITE_NEWSLETTER_SWS'))
	{
		if(! class_exists('phpmailer'))
		{
			include dirname(__FILE__)."/../../misc/phpmailer/class.phpmailer.php" ;
		}
		define('ENTITE_NEWSLETTER_SWS', 1) ;
		
		class ModuleNewsletterSws extends ModulePageBaseSws
		{
			public $TitreMenu = "Newsletter" ;
			public $NomRef = "newsletter" ;
			public $MdlsRubrNewsletter = array() ;
			public $EntiteNewsletter ;
			public $EntiteRubrNewsletter ;
			protected function CreeEntiteNewsletter()
			{
				return new EntiteNewsletterSws() ;
			}
			protected function CreeEntiteAbonNewsletter()
			{
				return new EntiteAbonNewsletterSws() ;
			}
			protected function InsereElemNewsletter($nom, $elem)
			{
				$this->ElemsNewsletter[$nom] = & $elem ;
				$this->InscritEntite($nom, $elem) ;
				return $elem ;
			}
			protected function InsereMdlRubrNewsletter($mdlRubrNewsletter)
			{
				$this->MdlsRubrNewsletter[$mdlRubrNewsletter->RefMdl()] = $mdlRubrNewsletter ;
			}
			protected function ChargeMdlsRubrNewsletter()
			{
				$this->MdlsRubrNewsletter = array() ;
				$this->InsereMdlRubrNewsletter(new MdlLstArtsNewsletterSws()) ;
				$this->InsereMdlRubrNewsletter(new MdlDescArtsNewsletterSws()) ;
			}
			public function ObtientMdlRubrNewsletterNomme($nom)
			{
				return (isset($this->MdlsRubrNewsletter[$nom])) ? $this->MdlsRubrNewsletter[$nom] : null ;
			}
			public function TitreMdlsRubrNewsletter()
			{
				$results = array() ;
				foreach($this->MdlsRubrNewsletter as $i => & $mdl)
				{
					$results[$mdl->RefMdl()] = $mdl->TitreMdl() ;
				}
				return $results ;
			}
			protected function ChargeEntites()
			{
				$this->ChargeMdlsRubrNewsletter() ;
				$this->EntiteNewsletter = $this->InsereEntite("newsletter", $this->CreeEntiteNewsletter()) ;
			}
		}
		
		class TacheEnvoiJournauxNewsletterSws extends TacheWebBaseSws
		{
			public $DelaiExecution = 1 ;
			public function ExecuteInstructions()
			{
				$timestmp = date("U") ;
				$noSemaine = date("W", $timestmp) ;
				$noAnnee = date("Y", $timestmp) ;
				$module = $this->ObtientModulePage() ;
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				$lgnDiffusion = $bd->FetchSqlRow("select * from ".$bd->EscapeTableName($entite->NomTableDiffusion)." where no_annee=".$bd->ParamPrefix."noAnnee and no_semaine=".$bd->ParamPrefix."noSemaine", array("noAnnee" => $noAnnee, "noSemaine" => $noSemaine)) ;
				if(! is_array($lgnDiffusion) || count($lgnDiffusion) > 0)
				{
					return ;
				}
				$idCtrl = uniqid() ;
				$bd->InsertRow($entite->NomTableDiffusion, array("no_annee" => $noAnnee, "no_semaine" => $noSemaine, "id_ctrl" => $idCtrl)) ;
				$lgnDiffusion = $bd->FetchSqlRow("select * from ".$bd->EscapeTableName($entite->NomTableDiffusion)." where id_ctrl=".$bd->ParamPrefix."idCtrl", array("idCtrl" => $idCtrl)) ;
				if(! is_array($lgnDiffusion))
				{
					return ;
				}
				$lgns = $bd->FetchSqlRows("select t1.* from ".$bd->EscapeTableName($entite->NomTableRubr)." t1 left join ".$bd->EscapeTableName($entite->NomTable)." t2 on t1.".$bd->EscapeVariableName($entite->NomColIdNewsletterRubr)." = t2.".$bd->EscapeVariableName($entite->NomColId)) ;
				$blocsJournal = array() ;
				$zone = $this->ZoneParent() ;
				foreach($lgns as $i => $lgn)
				{
					$mdlRubrNewsletter = $module->ObtientMdlRubrNewsletterNomme($lgn["ref_modele_rubrique"]) ;
					$blocJournal = '' ;
					if($mdlRubrNewsletter != null)
					{
						$lgnsResults = $mdlRubrNewsletter->LgnsSelectResultsSupport($entite, $lgn, $zone) ;
						// print_r($lgnResults) ;
						if(is_array($lgnsResults))
						{
							$total = count($lgnsResults) ;
							foreach($lgnsResults as $i => $lgnRes)
							{
								$blocJournal .= $mdlRubrNewsletter->RenduResultSupport($zone, $entite, $lgnRes, $i, $total).PHP_EOL ;
							}
						}
					}
					$blocsJournal[$lgn[$entite->NomColIdRubr]] = $blocJournal ;
				}
				$sqlAbonmts = 'select t1.*, t2.'.$bd->EscapeVariableName($entite->NomColIdCtrlAbon).' id_ctrl_abon, t2.'.$bd->EscapeVariableName($entite->NomColNomAbon).' nom_abon, t2.'.$bd->EscapeVariableName($entite->NomColPrenomAbon).' prenom_abon, t2.'.$bd->EscapeVariableName($entite->NomColEmailAbon).' email_abon from '.$bd->EscapeTableName($entite->NomTableAbonmt).' t1 inner join '.$bd->EscapeTableName($entite->NomTableAbon).' t2 on t1.'.$bd->EscapeVariableName($entite->NomColIdAbonAbonmt).' = t2.'.$bd->EscapeVariableName($entite->NomColIdAbon).' where t2.'.$bd->EscapeVariableName($entite->NomColActiveAbon).' = 1 and t1.'.$bd->EscapeVariableName($entite->NomColActiveAbonmt).'= 1 order by t2.'.$bd->EscapeVariableName($entite->NomColIdAbon).' asc, t1.'.$bd->EscapeVariableName($entite->NomColIdRubrAbonmt).' asc' ;
				$lgnsAbonmts = $bd->FetchSqlRows($sqlAbonmts) ;
				if(is_array($lgnsAbonmts))
				{
					$idAbon = 0 ;
					$journalAbon = "" ;
					$lgnAbonmtPrec = array() ;
					foreach($lgnsAbonmts as $i => $lgnAbonmt)
					{
						$idAbonEnCours = $lgnAbonmt[$entite->NomColIdAbonAbonmt] ;
						if($idAbon != $idAbonEnCours)
						{
							if($idAbon != 0)
							{
								$journalAbon .= $entite->RenduPiedJournal($lgnAbonmtPrec, $zone).PHP_EOL ;
								$mailer = $entite->CreateMailer() ;
								$mailer->AddAddress($lgnAbonmt["email_abon"], $lgnAbonmt["prenom_abon"]." ".$lgnAbonmt["nom_abon"]) ;
								$mailer->Subject = $entite->CreeSujetJournal($lgnAbonmt) ;
								$mailer->Body = $journalAbon ;
								$ok = $mailer->Send() ;
								$bd->InsertRow($entite->NomTableJournal,
									array(
										"id_abonne" => $idAbon,
										"contenu" => $journalAbon,
										"id_diffusion" => $lgnDiffusion["id"],
										"succes" => $ok,
										"msg_erreur" => $mailer->ErrorInfo,
									)
								) ;
							}
							$journalAbon = '' ;
							$journalAbon .= $entite->RenduEnteteJournal($lgnAbonmt, $zone).PHP_EOL ;
						}
						// echo $entite->NomColIdRubrAbonmt." : ".$lgnAbonmt[$entite->NomColIdRubrAbonmt] ;
						$journalAbon .= $blocsJournal[$lgnAbonmt[$entite->NomColIdRubrAbonmt]] ;
						$lgnAbonmtPrec = $lgnAbonmt ;
						$idAbon = $lgnAbonmt[$entite->NomColIdAbonAbonmt] ;
					}
				}
				// print_r($blocsJournal) ;
				// $this->TerminerExecution = 0 ;
			}
		}
		
		class EntiteNewsletterSws extends EntiteTableSws
		{
			public $TitreMenu = "Newsletters" ;
			public $TitreAjoutEntite = "Ajout newsletter" ;
			public $TitreModifEntite = "Modification newsletter" ;
			public $TitreSupprEntite = "Suppression newsletter" ;
			public $TitreListageEntite = "Liste des newsletters" ;
			public $TitreConsultEntite = "D&eacute;tails newsletter" ;
			public $NomEntite = "newsletter" ;
			public $NomTable = "newsletter" ;
			public $LibTitre = "Titre" ;
			public $NomColTitre = "titre" ;
			public $NomParamTitre = "titre" ;
			public $DefColTblListTitre ;
			public $FltFrmElemTitre ;
			public $InclureScriptEdit = 1 ;
			public $InclureScriptLst = 1 ;
			public $InclureScriptConsult = 0 ;
			public $InclureScriptEnum = 0 ;
			public $NomTableDiffusion = "diffusion_newsletter" ;
			public $NomTableJournal = "journal_newsletter" ;
			// Definition membres rubrique newsletter
			public $NomTableRubr = "rubr_newsletter" ;
			public $NomColIdRubr = "id" ;
			public $NomColTitreRubr = "titre" ;
			public $NomColRefMdlRubr = "ref_modele_rubrique" ;
			public $NomColIdNewsletterRubr = "id_newsletter" ;
			public $NomColIdEntSupportRubr = "id_entite_support" ;
			public $NomColNomEntSupportRubr = "nom_entite_support" ;
			public $NomColDateCreationRubr = "date_creation" ;
			public $NomColDateModifRubr = "date_modif" ;
			public $NomColDatePublRubr = "date_publication" ;
			public $NomColHeurePublRubr = "heure_publication" ;
			public $NomColStatutPublRubr = "statut_publication" ;
			public $NomColIdMembreCreationRubr = "id_membre_creation" ;
			public $NomColIdMembreModifRubr = "id_membre_modif" ;
			// Definition membres abonne newsletter
			public $NomTableAbon = "abon_newsletter" ;
			public $NomColIdAbon = "id" ;
			public $NomColIdCtrlAbon = "id_ctrl" ;
			public $NomColNomAbon = "nom" ;
			public $NomColPrenomAbon = "prenom" ;
			public $NomColEmailAbon = "email" ;
			public $NomColDateCreationAbon = "date_creation" ;
			public $NomColDateModifAbon = "date_modif" ;
			public $NomColActiveAbon = "active" ;
			// Definition membres abonnement newsletter
			public $NomTableAbonmt = "abonnement_newsletter" ;
			public $NomColIdAbonmt = "id" ;
			public $NomColIdAbonAbonmt = "id_abonne" ;
			public $NomColIdRubrAbonmt = "id_rubr_newsletter" ;
			public $NomColActiveAbonmt = "active" ;
			// Attrs script
			public $LienListeRubrsTblList ;
			public $LibListeRubrsTblList = "Rubriques" ;
			public $LibAjoutRubrTblList = "Ajout" ;
			public $LibTitreRubr = "Titre" ;
			public $LibIdNewsletterRubr = "Newsletter" ;
			public $LibIdEntSupportRubr = "Entit&eacute; support" ;
			public $LibSousMenuListeAbons = "Abonn&eacute;s" ;
			public $LibSousMenuListeJournaux = "Journaux" ;
			public $SousMenuListeAbons ;
			public $ScriptListeRubrs ;
			public $ScriptAjoutRubr ;
			public $ScriptModifRubr ;
			public $ScriptSupprRubr ;
			public $ScriptInscritAbon ;
			public $ScriptListeAbons ;
			public $ScriptDetailAbon ;
			public $ScriptSupprAbon ;
			public $ScriptListeJournaux ;
			public $Mailer ;
			public $HoteMailer = "smtp.gmail.com" ;
			public $PortMailer = "465" ;
			public $CompteMailer = "lebdenat@gmail.com" ;
			public $MotPasseMailer = "alhprog" ;
			public $FromMailer = "lebdenat@gmail.com" ;
			public $FromNameMailer = "Alhassane Abdel" ;
			public $ReplyToMailer ;
			public $SMTPSecureMailer = "ssl" ;
			protected $PresentDansFluxRSS = 0 ;
			public function CreeSujetJournal(& $lgn)
			{
				return 'Newsletter du '.date("d/m/Y") ;
			}
			public function RenduEnteteJournal(& $lgn, & $zone)
			{
				$ctn = '' ;
				$ctn .= '<p>Bonjour '.$lgn["prenom_abon"].' '.$lgn["nom_abon"].', vous recevez ce mail parce que vous &ecirc;tes abonn&eacute; &agrave; la newsletter.</p>' ;
				return $ctn ;
			}
			public function RenduPiedJournal(& $lgn, & $zone)
			{
				$ctn = '' ;
				$url = $zone->ObtientUrlParam(array("id_ctrl" => $lgn["id_ctrl_abon"], "email" => $lgn["email_abon"])) ;
				$ctn .= '<p>Si vous souhaitez ne plus recevoir ces mails. Veuillez annuler votre abonnement en cliquant sur ce <a href="'.$url.'" target="_blank">lien</a></p>' ;
				return $ctn ;
			}
			public function & CreateMailer()
			{
				$mailer = new PHPMailer();
				$mailer->IsSMTP();
				if($this->SMTPSecureMailer != '')
				{
					$mailer->SMTPSecure = $this->SMTPSecureMailer;
				}
				$mailer->Host = $this->HoteMailer;
				$mailer->Port = $this->PortMailer;

				if($this->MotPasseMailer != '')
				{
					$mailer->SMTPAuth = true;
					$mailer->Username = $this->CompteMailer;
					$mailer->Password = $this->MotPasseMailer;
				}
				$mailer->From = $this->FromMailer ;
				$mailer->FromName = $this->FromNameMailer ;
				return $mailer ;
			}
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Titre
				$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
				$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
				$comp = $this->FltFrmElemTitre->ObtientComposant() ;
				$comp->Largeur = "200px" ;
			}
			protected function ChargeTblList(& $tabl)
			{
				parent::ChargeTblList($tabl) ;
				$this->DefColTblListTitre = $tabl->InsereDefCol($this->NomColTitre, $this->LibTitre) ;
			}
			protected function FinalTblList(& $tabl)
			{
				parent::FinalTblList($tabl) ;
				$this->LienListeRubrsTblList = $tabl->InsereLienAction($this->DefColTblListActs, $this->ScriptListeRubrs->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')), $this->LibListeRubrsTblList) ; ;
				$this->LienAjoutRubrTblList = $tabl->InsereLienAction($this->DefColTblListActs, $this->ScriptAjoutRubr->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')), $this->LibAjoutRubrTblList) ;
			}
			public function RemplitZoneAdmin(& $zone)
			{
				parent::RemplitZoneAdmin($zone) ;
				$this->ScriptListeRubrs = $this->InsereScript('liste_rubrs_'.$this->NomEntite, new ScriptListeRubrsNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptAjoutRubr = $this->InsereScript('ajout_rubr_'.$this->NomEntite, new ScriptAjoutRubrNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptModifRubr = $this->InsereScript('modif_rubr_'.$this->NomEntite, new ScriptModifRubrNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptSupprRubr = $this->InsereScript('suppr_rubr_'.$this->NomEntite, new ScriptSupprRubrNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptListeAbons = $this->InsereScript('liste_abons_'.$this->NomEntite, new ScriptListeAbonsNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptDetailAbon = $this->InsereScript('detail_abon_'.$this->NomEntite, new ScriptDetailAbonNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptListeJournaux = $this->InsereScript('liste_journaux_'.$this->NomEntite, new ScriptListeJournauxNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptContenuJournal = $this->InsereScript('contenu_journal_'.$this->NomEntite, new ScriptContenuJournalNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				// $this->RemplitTachesWebGlobal($zone) ;
			}
			protected function RemplitTachesWebGlobal(& $zone)
			{
				$this->TacheEnvoiNewsletter = $this->InsereTacheWeb('envoi_journaux_'.$this->NomElementModule, new TacheEnvoiJournauxNewsletterSws(), $zone) ;
			}
			public function RemplitZonePubl(& $zone)
			{
				parent::RemplitZonePubl($zone) ;
				$this->RemplitTachesWebGlobal($zone) ;
				$this->ScriptInscritAbon = $this->InsereScript('inscrit_abonne_'.$this->NomEntite, new ScriptInscritAbonNewsletterSws(), $zone, $this->ObtientPrivilegesConsult()) ;
				$this->ScriptInscritAbon = $this->InsereScript('annule_abonne_'.$this->NomEntite, new ScriptAnnuleAbonNewsletterSws(), $zone, $this->ObtientPrivilegesConsult()) ;
			}
			protected function RemplitMenuInt(& $menu)
			{
				parent::RemplitMenuInt($menu) ;
				$this->SousMenuListeAbons = $menu->InscritSousMenuScript('liste_abons_'.$this->NomEntite) ;
				$this->SousMenuListeAbons->Titre = $this->LibSousMenuListeAbons ;
				$this->SousMenuListeJournaux = $menu->InscritSousMenuScript('liste_journaux_'.$this->NomEntite) ;
				$this->SousMenuListeJournaux->Titre = $this->LibSousMenuListeJournaux ;
			}
			public function SqlSelectDiffus()
			{
				$bd = $this->ObtientBDSupport() ;
				return "(
					select t1.*, t2.".$bd->EscapeVariableName($this->NomColIdCtrlAbon)." id_ctrl_abon, t2.".$bd->EscapeVariableName($this->NomColNomAbon)." nom_abon, t2.".$bd->EscapeVariableName($this->NomColPrenomAbon)." prenom_abon, t2.".$bd->EscapeVariableName($this->NomColEmailAbon)." email_abon, t3.no_semaine, t3.no_annee
					from ".$bd->EscapeTableName($this->NomTableJournal)." t1 left join ".$bd->EscapeTableName($this->NomTableAbon)." t2 on t1.id_abonne = t2.".$bd->EscapeVariableName($this->NomColIdAbon)."
					left join ".$bd->EscapeTableName($this->NomTableDiffusion)." t3
					on t1.id_diffusion = t3.id
				)" ;
			}
		}
		
		class SommaireNewsletterSws extends PvFormulaireDonneesHtml
		{
			public $TitreCadre = "D&eacute;tails newsletter" ;
			public $FltIdEdit ;
			public $FltId ;
			public $FltTitre ;
			public $CacherBlocCommandes = 1 ;
			public $InclureTotalElements = 1 ;
			public $InclureElementEnCours = 1 ;
			public $MaxFiltresEditionParLigne = 1 ;
			public $Editable = 0 ;
			public $InscrireCommandeAnnuler = 0 ;
			public $InscrireCommandeExecuter = 0 ;
			protected $TypeEntiteScript = "" ;
			public function ChargeFiltresSelection()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				if(isset($_GET[$entite->NomParamId]))
				{
					$this->TypeEntiteScript = "Newsletter" ;
				}
				elseif(isset($_GET["idRubr"]))
				{
					$this->TypeEntiteScript = "RubrNewsletter" ;
				}
				if($this->TypeEntiteScript == "Newsletter")
				{
					$this->FltId = $this->InsereFltLgSelectHttpGet($entite->NomParamId, $bd->EscapeVariableName($entite->NomColId)."=<self>") ;
					$this->FltId->Obligatoire = 1 ;
				}
				elseif($this->TypeEntiteScript == "RubrNewsletter")
				{
					$this->FltId = $this->InsereFltLgSelectHttpGet("idRubr", "id_rubrique=<self>") ;
					$this->FltId->Obligatoire = 1 ;
				}
			}
			public function ChargeFiltresEdition()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$this->FltIdEdit = $this->InsereFltEditHttpPost($entite->NomParamId, $entite->NomColId) ;
				$this->FltIdEdit->Libelle = "ID" ;
				$this->FltTitre = $this->InsereFltEditHttpPost($entite->NomParamTitre, $entite->NomColTitre) ;
				$this->FltTitre->Libelle = "Titre" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$this->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->FournisseurDonnees->RequeteSelection = $entite->NomTable ;
				if($this->TypeEntiteScript == "RubrNewsletter")
				{
					$this->FournisseurDonnees->RequeteSelection = "(select t1.*, t2.".$bd->EscapeVariableName($entite->NomColIdRubr)." id_rubrique from ".$bd->EscapeTableName($entite->NomTable)." t1 left join ".$bd->EscapeTableName($entite->NomTableRubr)." t2 on t1.".$bd->EscapeVariableName($entite->NomColId)." = t2.".$bd->EscapeVariableName($entite->NomColIdNewsletterRubr).")" ;
					// print $this->FournisseurDonnees->RequeteSelection ;
				}
				$this->FournisseurDonnees->TableEdition = $entite->NomTable ;
			}
			protected function RenduDispositifBrut()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$ctn = '' ;
				$ctn .= '<div class="ui-widget ui-widget-header">'.$this->TitreCadre.'</div>' ;
				$ctn .= parent::RenduDispositifBrut() ;
				if($this->ElementEnCoursTrouve)
				{
					$ctn .= '<div class="ui-widget ui-widget-content" style="padding:4px">
<a class="ui-state-hover" href="'.$entite->ScriptListeRubrs->ObtientUrlParam(array($entite->NomParamId => $this->ElementEnCours[$entite->NomParamId])).'">Parcourir les rubriques</a>
<a class="ui-state-hover" href="'.$entite->ScriptAjoutRubr->ObtientUrlParam(array($entite->NomParamId => $this->ElementEnCours[$entite->NomParamId])).'">Ajouter une rubrique</a>
</div>
<br />' ;
				}
				return $ctn ;
			}
		}
		class FormEditRubrNewsletterSws extends PvFormulaireDonneesHtml
		{
			public $FltIdNewsletter ;
			public $FltId ;
			public $FltTitre ;
			public $FltRefMdl ;
			public $FltIdEntSupport ;
			public $MdlRubrSelect ;
			public $CompIdNewsletter ;
			public $CompIdEntSupport ;
			public $FltStatutPubl ;
			public $FltDatePubl ;
			public $FltHeurePubl ;
			public $FltDateModif ;
			public $FltNomEntSupport ;
			public $FltIdMembreCreation ;
			public $FltIdMembreModif ;
			public $MaxFiltresEditionParLigne = 1 ;
			public $NomClasseCommandeExecuter = "PvCommandeAjoutElement" ;
			public $InscrireCommandeAnnuler = 0 ;
			protected function ChargeMdlRubrSelect()
			{
				$this->MdlRubrSelect = null ;
				$val = _GET_def("modele") ;
				$entite = $this->ObtientEntitePage() ;
				$mdls = $entite->ModuleParent->MdlsRubrNewsletter ;
				if(isset($mdls[$val]))
				{
					$this->MdlRubrSelect = $mdls[$val] ;
				}
				elseif(count($mdls) > 0)
				{
					$nomRefs = array_keys($mdls) ;
					$this->MdlRubrSelect = $mdls[$nomRefs[0]] ;
				}
			}
			protected function ObtientEntitePage()
			{
				return $this->ScriptParent->ObtientEntitePage() ;
			}
			protected function ObtientBDSupport()
			{
				return $this->ScriptParent->ObtientBDSupport() ;
			}
			public function ChargeConfig()
			{
				$this->ChargeMdlRubrSelect() ;
				parent::ChargeConfig() ;
			}
			protected function ChargeFiltresSelection()
			{
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				$this->FltId = $this->InsereFltLgSelectHttpGet("idRubr", $bd->EscapeVariableName($entite->NomColIdRubr)."=<self>") ;
				$this->FltId->EstObligatoire = 1 ;
			}
			protected function ChargeFiltresEdition()
			{
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				$this->FltTitre = $this->InsereFltEditHttpPost("titre", $entite->NomColTitreRubr) ;
				$this->FltTitre->Libelle = "Titre" ;
				if(! $this->InclureElementEnCours)
				{
					$this->FltRefMdl = $this->InsereFltEditFixe("ref_modele_rubrique", $this->MdlRubrSelect->RefMdl(), $entite->NomColRefMdlRubr) ;
				}
				else
				{
					$this->FltRefMdl = $this->InsereFltEditFixe("ref_modele_rubrique", "", $entite->NomColRefMdlRubr) ;
					$this->FltRefMdl->NePasLierColonne = 1 ;
				}
				$this->FltIdEntSupport = $this->InsereFltEditHttpPost("id_entite_support", $entite->NomColIdEntSupportRubr) ;
				$this->FltIdEntSupport->Libelle = "Entit&eacute; support" ;
				$this->CompIdEntSupport = $this->FltIdEntSupport->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$this->CompIdEntSupport->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->CompIdEntSupport->NomColonneLibelle = "titre" ;
				$this->CompIdEntSupport->NomColonneValeur = "id" ;
				// Id newsletter
				$this->FltIdNewsletter = $this->InsereFltEditHttpPost("id_newsletter", $entite->NomColIdNewsletterRubr) ;
				$this->FltIdNewsletter->Libelle = "Newsletter" ;
				$this->CompIdNewsletter = $this->FltIdNewsletter->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$this->CompIdNewsletter->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->CompIdNewsletter->FournisseurDonnees->RequeteSelection = $entite->NomTable ;
				$this->CompIdNewsletter->NomColonneLibelle = $entite->NomColTitre ;
				$this->CompIdNewsletter->NomColonneValeur = $entite->NomColId ;
				// Statut publication
				$this->FltStatutPubl = $this->InsereFltEditHttpPost("statut_publication", $entite->NomColStatutPublRubr) ;
				$this->FltStatutPubl->Libelle = "Statut publication" ;
				$this->FltStatutPubl->ValeurParDefaut = 1 ;
				$this->FltStatutPubl->DeclareComposant("PvZoneSelectBoolHtml") ;
				// Date publication
				$this->FltDatePubl = $this->InsereFltEditHttpPost("date_publication", $entite->NomColDatePublRubr) ;
				$this->FltDatePubl->Libelle = "Date publication" ;
				$this->FltDatePubl->DeclareComposant("PvCalendarDateInput") ;
				// Date publication
				$this->FltHeurePubl = $this->InsereFltEditHttpPost("heure_publication", $entite->NomColHeurePublRubr) ;
				$this->FltHeurePubl->Libelle = "Heure publication" ;
				$this->FltHeurePubl->DeclareComposant("PvTimeInput") ;
				// Date modif
				$this->FltDateModif = $this->InsereFltEditFixe("date_modif", date("Y-m-d H:i:s"), $entite->NomColDateModifRubr) ;
				// Membre creation
				if($this->InclureElementEnCours == 0)
				{
					$this->FltIdMembreCreation = $this->InsereFltEditFixe("id_membre_creation", $this->ZoneParent->IdMembreConnecte(), $entite->NomColIdMembreCreationRubr) ;
				}
				// Membre modificateur
				$this->FltIdMembreModif = $this->InsereFltEditFixe("id_membre_modif", $this->ZoneParent->IdMembreConnecte(), $entite->NomColIdMembreModifRubr) ;
				// Nom entite
				$this->FltNomEntSupport = $this->InsereFltEditFixe("nom_entite", "", $entite->NomColNomEntSupportRubr) ;
			}
			protected function DetecteCommandeSelectionnee()
			{
				parent::DetecteCommandeSelectionnee() ;
				if($this->ValeurParamIdCommande == "" || $this->Editable == 0)
					return ;
				$this->CalculeNomEntite() ;
			}
			protected function CalculeNomEntite()
			{
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$mdlSelect = $this->MdlRubrSelect ;
				$idEntite = $this->FltIdEntSupport->Lie() ;
				if($this->InclureElementEnCours == 1)
				{
					$lgnRubr = $bd->FetchSqlRow("select * from ".$entite->NomTableRubr." where ".$bd->EscapeVariableName($entite->NomColIdRubr)."=:idRubr", array("idRubr" => $this->FltId->Lie())) ;
					if(is_array($lgnRubr) && count($lgnRubr) > 0 && isset($entite->ModuleParent->MdlsRubrNewsletter[$lgnRubr[$entite->NomColRefMdlRubr]]))
					{
						// print_r($entite->ModuleParent->MdlsRubrNewsletter[$lgnRubr[$entite->NomColRefMdlRubr]]) ;
						$mdlSelect = $entite->ModuleParent->MdlsRubrNewsletter[$lgnRubr[$entite->NomColRefMdlRubr]] ;
					}
					else
					{
						$mdlSelect = new MdlRubrBaseNewsletterSws() ;
					}
				}
				$lgn = $bd->FetchSqlRow('select * from ('.$mdlSelect->SqlSelectEntiteSupport($entite, $this->ScriptParent).') t1 where id=:id', array("id" => $idEntite)) ;
				$ok = 0 ;
				if(is_array($lgn))
				{
					if(count($lgn) > 0)
					{
						$this->FltNomEntSupport->DejaLie = 0 ;
						$this->FltNomEntSupport->ValeurParDefaut = $lgn["titre"] ;
						$ok = 1 ;
					}
				}
				return $ok ;
			}
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				$entite = $this->ObtientEntitePage() ;
				if($ok && ! $this->MdlRubrSelect->EstPossible($entite, $this->ScriptParent))
				{
					return 0 ;
				}
				return 1 ;
			}
			public function CalculeElementsRendu()
			{
				parent::CalculeElementsRendu() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				if($this->InclureElementEnCours && isset($entite->ModuleParent->MdlsRubrNewsletter[$this->ElementEnCours[$entite->NomColRefMdlRubr]]))
				{
					$this->MdlRubrSelect = $entite->ModuleParent->MdlsRubrNewsletter[$this->ElementEnCours[$entite->NomColRefMdlRubr]] ;
				}
				$this->CompIdEntSupport->FournisseurDonnees->RequeteSelection = '('.$this->MdlRubrSelect->SqlSelectEntiteSupport($entite, $this->ScriptParent).')' ;
			}
			protected function ChargeFournisseurDonnees()
			{
				$entite = $this->ObtientEntitePage() ;
				$this->FournisseurDonnees = $entite->CreeFournDonnees() ;
				$this->FournisseurDonnees->RequeteSelection = $entite->NomTableRubr ;
				$this->FournisseurDonnees->TableEdition = $entite->NomTableRubr ;
			}
		}
		
		class MdlRubrBaseNewsletterSws
		{
			public $Active = 1 ;
			public $MaxResultsSupport = 8 ;
			public function EstPossible(& $entite, & $script)
			{
				$bd = $script->ObtientBDSupport() ;
				$sqlSupport = $this->SqlSelectEntiteSupport($entite, $script) ;
				if($sqlSupport != '')
				{
					$sql = 'select count(0) total from ('.$sqlSupport.') t1' ;
					$total = $bd->FetchSqlValue($sql, array(), 'total') ;
					if($total == 0)
					{
						return 0 ;
					}
				}
				return $this->Active ;
				
			}
			public function RefMdl()
			{
				return '' ;
			}
			public function TitreMdl()
			{
				return '' ;
			}
			public function RemplitMenuEntite(& $menu, & $entite)
			{
			}
			public function SqlSelectEntiteSupport(& $entite, & $script)
			{
				$bd = $entite->ObtientBDSupport() ;
				$sql = '' ;
				return $sql ;
			}
			public function LgnsSelectResultsSupport(& $entite, $lgn, & $zone)
			{
				return array() ;
			}
			public function RenduResultSupport(& $zone, & $entite, $lgn, $position=0, $total=0)
			{
				$entiteArt = & $entite->ModuleParent->SystemeParent->ModuleArticle->EntiteArticle ;
				$ctn = '' ;
				$ctn .= '<div>'.PHP_EOL ;
				$ctn .= '<a href="'.$zone->ObtientUrl().'?'.urlencode($zone->NomParamScriptAppele).'='.urlencode($zone->ValeurParamScriptAppele).'&'.urlencode($entiteArt->NomParamId).'='.intval($lgn["id"]).'" target="_blank">'.$lgn["titre"].'</a>'.PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class MdlLstArtsNewsletterSws extends MdlRubrBaseNewsletterSws
		{
			public function RefMdl()
			{
				return 'listage_articles_rubrique' ;
			}
			public function TitreMdl()
			{
				return 'Derniers articles de rubrique' ;
			}
			public function RemplitMenuEntite(& $menu, & $entite)
			{
			}
			public function LgnsSelectResultsSupport(& $entite, $lgn, & $zone)
			{
				$bd = $entite->ObtientBDSupport() ;
				$entiteRubr = & $entite->ModuleParent->SystemeParent->ModuleArticle->EntiteRubrique ;
				$entiteArt = & $entiteRubr->ModuleParent->EntiteArticle ;
				$sql = 'select '.$bd->EscapeVariableName($entiteArt->NomColId).' id, '.$bd->EscapeVariableName($entiteArt->NomColTitre).' titre from '.$bd->EscapeTableName($entiteArt->NomTable).' where '.$bd->EscapeVariableName($entiteArt->NomColIdRubr).'='.$bd->ParamPrefix.'idSupport order by '.$bd->EscapeVariableName($entiteArt->NomColDatePubl).' desc, '.$bd->EscapeVariableName($entiteArt->NomColHeurePubl).' desc' ;
				$lgns = $bd->LimitSqlRows($sql, array("idSupport" => $lgn["id_entite_support"]), 0, $this->MaxResultsSupport) ;
				return $lgns ;
			}
			public function SqlSelectResultsSupport(& $entite, $lgn)
			{
				$bd = $entite->ObtientBDSupport() ;
				$entiteArt = & $entite->ModuleParent->SystemeParent->ModuleArticle->EntiteArticle ;
				$sql = 'select '.$bd->EscapeVariableName($entiteArt->NomColId).' id, '.$bd->EscapeVariableName($entiteArt->NomColTitre).' titre, '.$bd->EscapeVariableName($entiteArt->NomColCheminImage).' chemin_image, '.$bd->EscapeVariableName($entiteArt->NomColDescription).' description, '.$bd->EscapeVariableName($entiteArt->NomColDatePubl).' date_publication, '.$bd->EscapeVariableName($entiteArt->NomColHeurePubl).' heure_publication from '.$bd->EscapeTableName($entiteArt->NomTable).' where '.$bd->EscapeVariableName($entiteArt->NomColIdRubr).'=:idSupport order by '.$bd->EscapeVariableName($entiteArt->NomColDatePubl).' desc, '.$bd->EscapeVariableName($entiteArt->NomColHeurePubl).' desc' ;
				return $sql ;
			}
		}
		class MdlDescArtsNewsletterSws extends MdlLstArtsNewsletterSws
		{
			public function RefMdl()
			{
				return 'descendants_articles_rubrique' ;
			}
			public function TitreMdl()
			{
				return 'Derniers articles de rubrique et sous-rubriques' ;
			}
			public function SqlSelectResultsSupport(& $entite, $lgn)
			{
				$bd = $entite->ObtientBDSupport() ;
				$entiteArt = & $entite->ModuleParent->SystemeParent->ModuleArticle->EntiteArticle ;
				$entiteRubr = & $entite->ModuleParent->SystemeParent->ModuleArticle->EntiteRubrique ;
				$sql = 'select t1.'.$bd->EscapeVariableName($entiteArt->NomColId).' id, t1.'.$bd->EscapeVariableName($entiteArt->NomColTitre).' titre, t1.'.$bd->EscapeVariableName($entiteArt->NomColCheminImage).' chemin_image, t1.'.$bd->EscapeVariableName($entiteArt->NomColDescription).' description, t1.'.$bd->EscapeVariableName($entiteArt->NomColDatePubl).' date_publication, t1.'.$bd->EscapeVariableName($entiteArt->NomColHeurePubl).' heure_publication from '.$bd->EscapeTableName($entiteArt->NomTable).' t1 left join '.$bd->EscapeTableName($entiteRubr->NomTable).' t2 on t1.'.$bd->EscapeVariableName($entiteArt->NomColIdRubr).' = t2.'.$bd->EscapeVariableName($entiteArt->NomColId).' where '.$bd->SqlIndexOf($bd->SqlConcat(array("', '", ':idSupport', "','")), $bd->EscapeVariableName($entiteRubr->NomColIdChemin)).' > 0 order by t1.'.$bd->EscapeVariableName($entiteArt->NomColDatePubl).' desc, t1.'.$bd->EscapeVariableName($entiteArt->NomColHeurePubl).' desc' ;
				return $sql ;
			}
		}
		
		class ScriptBaseRubrNewsletterSws extends ScriptAdminBaseSws
		{
			protected $SommaireNewsletter ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$entite = $this->ObtientEntitePage() ;
				$entite->PrepareScriptAdmin($this) ;
				$this->DetermineSommaireNewsletter() ;
			}
			public function DetermineSommaireNewsletter()
			{
				$this->SommaireNewsletter = new SommaireNewsletterSws() ;
				$this->SommaireNewsletter->AdopteScript("sommaireNewsletter", $this) ;
				$this->SommaireNewsletter->ChargeConfig() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$entite = $this->ObtientEntitePage() ;
				$ctn .= $entite->RenduAvantCtnSpec($this) ;
				$ctn .= $this->SommaireNewsletter->RenduDispositif() ;
				$ctn .= $entite->RenduApresCtnSpec($this) ;
				return $ctn ;
			}
		}
		
		class ScriptListeRubrsNewsletterSws extends ScriptBaseRubrNewsletterSws
		{
			public $TablRubrs ;
			public $TitreDocument = "Liste des rubriques newsletter" ;
			public $Titre = "Liste des rubriques newsletter" ;
			public $FltIdNewsletterTablRubr ;
			public $CompIdNewsletterTablRubr ;
			public $DefColIdTablRubr ;
			public $DefColTitreTablRubr ;
			public $DefColIdMdlTablRubr ;
			public $DefColNomEntTablRubr ;
			public $DefColTitreNewslTablRubr ;
			public $DefColActionsTablRubr ;
			public $LienModifTablRubr ;
			public $LienSupprTablRubr ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineTablRubrs() ;
			}
			protected function DetermineTablRubrs()
			{
				$entite = $this->ObtientEntitePage() ;
				$zone = & $this->ZoneParent ;
				$bd = $this->ObtientBDSupport() ;
				$this->TablRubrs = new TableauDonneesAdminSws() ;
				$this->TablRubrs->ToujoursAfficher = 1 ;
				$this->TablRubrs->AdopteScript('tablRubrs', $this) ;
				$this->TablRubrs->ChargeConfig() ;
				$this->FltIdNewsletterTablRubr = $this->TablRubrs->InsereFltSelectHttpGet($entite->NomParamId) ;
				$this->FltIdNewsletterTablRubr->Libelle = "Newsletter" ;
				$this->CompIdNewsletterTablRubr = $this->FltIdNewsletterTablRubr->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$this->CompIdNewsletterTablRubr->NomColonneValeur = $entite->NomColId ;
				$this->CompIdNewsletterTablRubr->NomColonneLibelle = $entite->NomColTitre ;
				$this->CompIdNewsletterTablRubr->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->CompIdNewsletterTablRubr->FournisseurDonnees->RequeteSelection = $entite->NomTable ;
				$this->DefColIdTablRubr = $this->TablRubrs->InsereDefCol($entite->NomColIdRubr) ;
				$this->DefColIdNewsletterTablRubr = $this->TablRubrs->InsereDefCol($entite->NomColTitreRubr, 'Titre') ;
				$this->DefColTitreNewslTablRubr = $this->TablRubrs->InsereDefCol('titre_newsletter', 'Newsletter') ;
				$this->DefColIdMdlTablRubr = $this->TablRubrs->InsereDefColChoix($entite->NomColRefMdlRubr, 'Mod&ecirc;le', '', $entite->ModuleParent->TitreMdlsRubrNewsletter()) ;
				// $this->DefColActionsRubr-> ;
				$this->DefColNomEntTablRubr = $this->TablRubrs->InsereDefCol($entite->NomColNomEntSupportRubr, 'entit&eacute; support') ;
				$this->DefColActionsTablRubr = $this->TablRubrs->InsereDefColActions("Actions") ;
				$this->LienModifTablRubr = $this->TablRubrs->InsereLienAction($this->DefColActionsTablRubr, "?".urlencode($zone->NomParamScriptAppele)."=".urlencode($entite->ScriptModifRubr->NomElementZone).'&idRubr=${'.$entite->NomColIdRubr.'}', "Modifier") ;
				$this->LienSupprTablRubr = $this->TablRubrs->InsereLienAction($this->DefColActionsTablRubr, "?".urlencode($zone->NomParamScriptAppele)."=".urlencode($entite->ScriptSupprRubr->NomElementZone).'&idRubr=${'.$entite->NomColIdRubr.'}', "Supprimer") ;
				$this->TablRubrs->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->TablRubrs->FournisseurDonnees->RequeteSelection = '(select t1.*, t2.'.$bd->EscapeVariableName($entite->NomColTitre).' titre_newsletter from '.$bd->EscapeTableName($entite->NomTableRubr).' t1 left join '.$bd->EscapeTableName($entite->NomTable).' t2 on t1.'.$bd->EscapeVariableName($entite->NomColIdNewsletterRubr).' = t2.'.$bd->EscapeVariableName($entite->NomColId).')' ;
				// $this->CmdAjoutRubr = $this->TablRubrs->InsereCmdRedirectUrl("ajout_rubr", $entite->ScriptAjoutRubr->ObtientUrl(), "Ajouter") ;
			}
			protected function RenduDispositifBrut()
			{
				$entite = $this->ObtientEntitePage() ;
				$ctn = parent::RenduDispositifBrut() ;
				// $this->CmdAjoutRubr->Url = $entite->ScriptAjoutRubr->ObtientUrlParam(array($entite->NomParamId => $this->FltIdNewsletterTablRubr->Lie())) ;
				$ctn .= $this->TablRubrs->RenduDispositif() ;
				return $ctn ;
			}
		}
		class ScriptEditRubrNewsletterSws extends ScriptBaseRubrNewsletterSws
		{
			protected $FormPrinc ;
			public $InclureElemFormPrinc = 0 ;
			public $EditerFormPrinc = 1 ;
			public $NomClasseCmdExecFormPrinc = "" ;
			protected function DetermineFormPrinc()
			{
				$this->FormPrinc = new FormEditRubrNewsletterSws() ;
				$this->FormPrinc->InclureElementEnCours = $this->InclureElemFormPrinc ;
				$this->FormPrinc->InclureTotalElements = $this->InclureElemFormPrinc ;
				$this->FormPrinc->Editable = $this->EditerFormPrinc ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->NomClasseCmdExecFormPrinc ;
				$this->FormPrinc->AdopteScript('formPrinc', $this) ;
				$this->FormPrinc->ChargeConfig() ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineFormPrinc() ;
			}
			protected function RenduMdlsRubrNewsletter()
			{
				$zone = & $this->ZoneParent ;
				$entite = $this->ObtientEntitePage() ;
				$mdls = $entite->ModuleParent->MdlsRubrNewsletter ;
				$ctn = '' ;
				foreach($mdls as $nom => $mdl)
				{
					if(! $mdl->EstPossible($entite, $this))
					{
						continue ;
					}
					$classeCSSSuppl = (is_object($this->FormPrinc->MdlRubrSelect) && $this->FormPrinc->MdlRubrSelect->RefMdl() == $mdl->RefMdl()) ? ' ui-state-default' : '' ;
					$ctn .= '<div class="ui-widget ui-widget-content'.$classeCSSSuppl.'" style="padding:4px;">- <a style="text-decoration:none;" href="?'.urlencode($zone->NomParamScriptAppele).'='.urlencode($zone->ValeurParamScriptAppele).'&'.urlencode($entite->NomParamId).'='.intval(_GET_def($entite->NomParamId)).'&modele='.urlencode($nom).'">'.$mdl->TitreMdl().'</a></div>'.PHP_EOL ;
				}
				if($ctn == '')
				{
					$ctn .= '<div class="ui-widget">Aucun mod&egrave;le disponible</div>
<div class="ui-widget" style="font-weight:normal; font-style:italic">Veuillez cr&eacute;er de nouvelles pages (rubriques etc) pour d&eacute;bloquer les mod&egrave;les</div>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$entite = $this->ObtientEntitePage() ;
				$mdls = $entite->ModuleParent->MdlsRubrNewsletter ;
				$ctn .= $entite->RenduAvantCtnSpec($this) ;
				$ctn .= $this->RenduTitre() ;
				$ctn .= $this->SommaireNewsletter->RenduDispositif() ;
				$ctnForm = $this->FormPrinc->RenduDispositif() ;
				$ctn .= '<table width="100%" cellspacing="0" cellpadding="2">
<tr>
<td valign="top" width="25%">'.PHP_EOL ;
				if($this->FormPrinc->InclureElementEnCours == 0)
				{
					$ctn .= '<div class="ui-widget ui-widget-content ui-state-hover">Mod&egrave;les</div>
<div class="ui-widget ui-widget-content ui-priority-primary">'.PHP_EOL ;
					$ctn .= $this->RenduMdlsRubrNewsletter() ;
				}
				else
				{
					$ctn .= '<div class="ui-widget ui-widget-content ui-state-hover">Mod&egrave;le en cours</div>
<div class="ui-widget ui-widget-content ui-priority-primary">'.PHP_EOL ;
					if(isset($mdls[$this->FormPrinc->ElementEnCours[$entite->NomColRefMdlRubr]]))
					{
						$ctn .= '<p>'.$mdls[$this->FormPrinc->ElementEnCours[$entite->NomColRefMdlRubr]]->TitreMdl().'</p>' ;
					}
					else
					{
						$ctn .= '<p>Mod&ecirc;le selectionn&eacute; inconnu</p>' ;
					}
				}
				$ctn .= '</div>
</td>
<td valign="top" width="*">
<div class="ui-widget ui-widget-content ui-state-hover">Propri&eacute;t&eacute;s de la rubrique</div>
<div class="ui-widget ui-widget-content">'.$ctnForm.'</div>
</td>
</tr>
</table>' ;
				$ctn .= $entite->RenduApresCtnSpec($this) ;
				return $ctn ;
			}
		}
		class ScriptAjoutRubrNewsletterSws extends ScriptEditRubrNewsletterSws
		{
			public $TitreDocument = "Ajout rubrique newsletter" ;
			public $Titre = "Ajout rubrique newsletter" ;
			public $InclureElemFormPrinc = 0 ;
			public $NomClasseCmdExecFormPrinc = "PvCommandeAjoutElement" ;
		}
		class ScriptModifRubrNewsletterSws extends ScriptEditRubrNewsletterSws
		{
			public $TitreDocument = "Modification rubrique newsletter" ;
			public $Titre = "Modification rubrique newsletter" ;
			public $InclureElemFormPrinc = 1 ;
			public $NomClasseCmdExecFormPrinc = "PvCommandeModifElement" ;
		}
		class ScriptSupprRubrNewsletterSws extends ScriptEditRubrNewsletterSws
		{
			public $TitreDocument = "Suppression rubrique newsletter" ;
			public $Titre = "Suppression rubrique newsletter" ;
			public $InclureElemFormPrinc = 1 ;
			public $EditerFormPrinc = 0 ;
			public $NomClasseCmdExecFormPrinc = "PvCommandeSupprElement" ;
		}
		
		class ScriptListeAbonsNewsletterSws extends ScriptAdminBaseSws
		{
			public $TitreDocument = "Abonn&eacute;s newsletter" ;
			public $Titre = "Abonn&eacute;s newsletter" ;
			protected $FltNom ;
			protected $FltDateMinInscript ;
			protected $FltDateMaxInscript ;
			protected $FltEmail ;
			protected $DefColId ;
			protected $DefColNom ;
			protected $DefColPrenom ;
			protected $DefColEmail ;
			protected $DefColActive ;
			protected $LienActDetails ;
			protected $LienActSuppr ;
			protected $DefColDateCreation ;
			protected $TablPrinc ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$entite = $this->ObtientEntitePage() ;
				$entite->PrepareScriptAdmin($this) ;
				$this->DetermineTablPrinc() ;
			}
			protected function DetermineTablPrinc()
			{
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				$this->TablPrinc = new TableauDonneesAdminSws() ;
				$this->TablPrinc->AdopteScript('tablPrinc', $this) ;
				$this->TablPrinc->ChargeConfig() ;
				$this->FltEmail = $this->TablPrinc->InsereFltSelectHttpGet('email', $bd->SqlIndexOf('<self>', $bd->EscapeVariableName($entite->NomColEmailAbon).' > 0')) ;
				$this->FltEmail->Libelle = "Email" ;
				$this->FltNom = $this->TablPrinc->InsereFltSelectHttpGet('nom', $bd->SqlIndexOf('upper(<self>)', 'upper('.$bd->EscapeVariableName($entite->NomColNomAbon).')').' > 0 or '.$bd->SqlIndexOf('upper(<self>)', 'upper('.$bd->EscapeVariableName($entite->NomColNomAbon).')').' > 0') ;
				$this->FltNom->Libelle = "Nom" ;
				$this->FltDateMinInscript = $this->TablPrinc->InsereFltSelectHttpGet('date_debut', $bd->SqlStrToDate('<self>').' <= '.$bd->EscapeVariableName($entite->NomColDateCreationAbon)) ;
				$this->FltDateMinInscript->Libelle = "Date debut" ;
				$this->FltDateMinInscript->ValeurParDefaut = date("Y-m-d", date("U") - 84600 * 30) ;
				$this->FltDateMinInscript->DeclareComposant("PvCalendarDateInput") ;
				$this->FltDateMaxInscript = $this->TablPrinc->InsereFltSelectHttpGet('date_fin', $bd->SqlStrToDate('<self>').' >= '.$bd->EscapeVariableName($entite->NomColDateCreationAbon)) ;
				$this->FltDateMaxInscript->Libelle = "Date fin" ;
				$this->FltDateMaxInscript->DeclareComposant("PvCalendarDateInput") ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee($entite->NomColIdAbon) ;
				$this->DefColNom = $this->TablPrinc->InsereDefCol($entite->NomColNomAbon, "Nom") ;
				$this->DefColPrenom = $this->TablPrinc->InsereDefCol($entite->NomColPrenomAbon, "Prenoms") ;
				$this->DefColEmail = $this->TablPrinc->InsereDefCol($entite->NomColEmailAbon, "Email") ;
				$this->DefColActive = $this->TablPrinc->InsereDefColBool($entite->NomColActiveAbon, "Actif") ;
				$this->DefColActive->AlignElement = "center" ;
				$this->DefColDateCreation = $this->TablPrinc->InsereDefCol($entite->NomColDateCreationAbon, "Date inscription", $bd->SqlDateToStrFr($bd->EscapeVariableName($entite->NomColDateCreationAbon), 1)) ;
				$this->DefColActions = $this->TablPrinc->InsereDefColActions("Actions") ;
				$this->LienDetails = $this->TablPrinc->InsereLienAction($this->DefColActions, '?'.urlencode($this->ZoneParent->NomParamScriptAppele).'=detail_abon_'.$entite->NomEntite.'&id=${id}', 'D&eacute;tails') ;
				$this->TablPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->TablPrinc->FournisseurDonnees->RequeteSelection = $entite->NomTableAbon ;
			}
			protected function RenduDispositifBrut()
			{
				$entite = $this->ObtientEntitePage() ;
				$ctn = parent::RenduDispositifBrut() ;
				$ctn .= $entite->RenduAvantCtnSpec($this) ;
				$ctn .= $this->TablPrinc->RenduDispositif() ;
				$ctn .= $entite->RenduApresCtnSpec($this) ;
				return $ctn ;
			}
		}
		
		class ScriptEditAbonNewsletterSws extends ScriptPublBaseSws
		{
			public $FormPrinc ;
			public $InclureElemFormPrinc ;
			public $EditableFormPrinc = 1 ;
			public $NomClasseCmdExecFormPrinc ;
			public $FltIdFormPrinc ;
			public $FltIdNewsletterFormPrinc ;
			public $FltIdCtrlFormPrinc ;
			public $FltNomFormPrinc ;
			public $CompNomFormPrinc ;
			public $LargeurNomFormPrinc = "240px" ;
			public $FltPrenomFormPrinc ;
			public $CompPrenomFormPrinc ;
			public $LargeurPrenomFormPrinc = "350px" ;
			public $FltEmailFormPrinc ;
			public $CompEmailFormPrinc ;
			public $LargeurEmailFormPrinc = "350px" ;
			public $FltRubrsFormPrinc ;
			public $CompRubrsFormPrinc ;
			public $FltIdNewsletterRubrsFormPrinc ;
			public $LibCmdExecFormPrinc = "" ;
			protected $ValeurParamIdNewsletter = "" ;
			protected $ValeurIdNewsletter = "" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineNewletterEnCours() ;
				$this->DetermineFormPrinc() ;
			}
			protected function DetermineFormPrinc()
			{
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				$this->FormPrinc = new PvFormulaireDonneesHtml() ;
				$this->FormPrinc->InclureElementEnCours = $this->InclureElemFormPrinc ;
				$this->FormPrinc->InclureTotalElements = $this->InclureElemFormPrinc ;
				$this->FormPrinc->LibelleCommandeExecuter = $this->LibCmdExecFormPrinc ;
				$this->FormPrinc->InscrireCommandeAnnuler = 0 ;
				$this->FormPrinc->MaxFiltresEditionParLigne = 1 ;
				$this->FormPrinc->Editable = $this->EditableFormPrinc ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->NomClasseCmdExecFormPrinc ;
				$this->FormPrinc->AdopteScript("formPrinc", $this) ;
				$this->FormPrinc->ChargeConfig() ;
				$this->FltIdFormPrinc = $this->FormPrinc->InsereFltLgSelectHttpGet("id", $bd->EscapeVariableName($entite->NomColIdAbon)." = <self>") ;
				$this->FltIdCtrlFormPrinc = $this->FormPrinc->InsereFltEditFixe("id_ctrl", uniqid(), $entite->NomColIdCtrlAbon) ;
				$this->FltIdNewsletterFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("id_newsletter", "") ;
				$this->FltIdNewsletterFormPrinc->ValeurParDefaut = $this->ValeurIdNewsletter ;
				$this->FltIdNewsletterFormPrinc->LectureSeule = 1 ;
				$this->FltNomFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("nom", $entite->NomColNomAbon) ;
				$this->FltNomFormPrinc->Libelle = "Nom" ;
				$this->CompNomFormPrinc = $this->FltNomFormPrinc->ObtientComposant() ;
				$this->CompNomFormPrinc->Largeur = $this->LargeurNomFormPrinc ;
				$this->FltPrenomFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("prenom", $entite->NomColPrenomAbon) ;
				$this->FltPrenomFormPrinc->Libelle = "Prenom" ;
				$this->CompPrenomFormPrinc = $this->FltPrenomFormPrinc->ObtientComposant() ;
				$this->CompPrenomFormPrinc->Largeur = $this->LargeurPrenomFormPrinc ;
				$this->FltEmailFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("email", $entite->NomColEmailAbon) ;
				$this->FltEmailFormPrinc->Libelle = "Email" ;
				$this->CompEmailFormPrinc = $this->FltEmailFormPrinc->ObtientComposant() ;
				$this->CompEmailFormPrinc->Largeur = $this->LargeurEmailFormPrinc ;
				$this->FltRubrsFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("id_rubrique", "") ;
				$this->FltRubrsFormPrinc->Libelle = "Rubriques" ;
				$this->CompRubrsFormPrinc = $this->FltRubrsFormPrinc->DeclareComposant("PvZoneBoiteOptionsCocherHtml") ;
				$this->CompRubrsFormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->CompRubrsFormPrinc->FournisseurDonnees->RequeteSelection = $entite->NomTableRubr ;
				$this->CompRubrsFormPrinc->NomColonneValeur = $entite->NomColIdRubr ;
				$this->CompRubrsFormPrinc->NomColonneLibelle = $entite->NomColTitreRubr ;
				$this->CompRubrsFormPrinc->MaxColonnesParLigne = 1 ;
				$this->CompRubrsFormPrinc->CocherAutoPremiereOption = 0 ;
				$this->FltIdNewsletterRubrsFormPrinc = $this->CreeFiltreHttpGet("id", $bd->EscapeVariableName($entite->NomColIdNewsletterRubr)." = <self>") ;
				$this->FltIdNewsletterRubrsFormPrinc->EstObligatoire = 1 ;
				$this->CompRubrsFormPrinc->FiltresSelection[] = & $this->FltIdNewsletterRubrsFormPrinc ;
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $entite->NomTableAbon ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $entite->NomTableAbon ;
				// Criteres
				$this->FormPrinc->CommandeExecuter->InsereCritereNonVide(array("nom", "prenom", "id_rubrique")) ;
				$this->FormPrinc->CommandeExecuter->InsereCritereFormatEmail(array("email")) ;
				$this->FormPrinc->CommandeExecuter->InsereNouvCritere(new CritrDejaEnregNewsletterSws(), array("email")) ;
			}
			public function DetermineNewletterEnCours()
			{
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				// print_r(get_class($entite)) ;
				$this->ValeurIdNewsletter = 0 ;
				$this->ValeurParamIdNewsletter = _GET_def("id") ;
				$lgn = $bd->FetchSqlRow("select t2.* from ".$bd->EscapeTableName($entite->NomTable)." t1 left join ".$bd->EscapeTableName($entite->NomTableRubr)." t2 on t1.".$bd->EscapeVariableName($entite->NomColId)." = t2.".$bd->EscapeVariableName($entite->NomColIdNewsletterRubr)." where t2.".$bd->EscapeVariableName($entite->NomColIdNewsletterRubr)." is not null and t1.".$bd->EscapeVariableName($entite->NomColId)." = :id", array("id" => $this->ValeurParamIdNewsletter)) ;
				if(is_array($lgn) && count($lgn) > 0)
					$this->ValeurIdNewsletter = $lgn[$entite->NomColIdNewsletterRubr] ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				if($this->ValeurIdNewsletter != 0)
				{
					$ctn .= $this->FormPrinc->RenduDispositif() ;
				}
				else
				{
					$ctn .= '<p>-- La newsletter que vous avez demand&eacute;e n\'a pas &eacute;t&eacute; trouv&eacute;e --</p>' ;
				}
				return $ctn ;
			}
		}
		class ScriptInscritAbonNewsletterSws extends ScriptEditAbonNewsletterSws
		{
			public $TitreDocument = "Inscription &agrave; la newsletter" ;
			public $Titre = "Inscription newsletter" ;
			public $NomClasseCmdExecFormPrinc = "CmdEditAbonNewsletterSws" ;
			public $LibCmdExecFormPrinc = "S'incrire" ;
		}
		class ScriptAnnuleAbonNewsletterSws extends ScriptPublBaseSws
		{
			protected $ValeurParamEmail ;
			protected $ValeurParamIdCtrl ;
			public $Titre = "Annulation abonnement newsletter" ;
			public $TitreDocument = "Annulation abonnement newsletter" ;
			public $MessageErreur = "" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineParamsAnnulation() ;
			}
			protected function DetermineParamsAnnulation()
			{
				$this->ValeurParamEmail = trim(_GET_def("email")) ;
				$this->ValeurParamIdCtrl = trim(_GET_def("ref_ctrl")) ;
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				if($this->ValeurParamEmail != '' && $this->ValeurParamIdCtrl != '')
				{
					$sql = "select * from ".$bd->EscapeTableName($entite->NomTableAbon)." where ".$bd->EscapeVariableName($entite->NomColIdCtrlAbon)."=:idCtrl and ".$bd->EscapeVariableName($entite->NomColEmailAbon)."=:email and ".$bd->EscapeVariableName($entite->NomColActiveAbon)." = 1" ;
					$lgn = $bd->FetchSqlRow($sql, array("idCtrl" => $this->ValeurParamIdCtrl, "email" => $this->ValeurParamEmail)) ;
					if(is_array($lgn))
					{
						if(count($lgn) > 0)
						{
							$bd->RunSql("update ".$bd->EscapeTableName($entite->NomTableAbon)." set ".$bd->EscapeVariableName($entite->NomColActiveAbon)." = 0 where ".$bd->EscapeVariableName($entite->NomColIdAbon)." = :id", array("id" => $lgn["id"])) ;
						}
						else
						{
							$this->MessageErreur = "L'adresse Email n'est pas abonn&eacute; &agrave; la newsletter ou le code de v&eacute;rification n'est plus valide" ;
						}
					}
					else
					{
						$this->MessageErreur = "La base de donn&eacute;e des newsletter est actuellement indisponible. Veuillez r&eacute;essayer plus tard" ;
					}
				}
			}
			public function RenduSpecifique()
			{
				$ctn = "" ;
				if($this->MessageErreur != "")
				{
					$ctn .= '<div class="Erreur">'.$this->MessageErreur.'</div>' ;
				}
				else
				{
					$ctn .= "<div class='Succes'>L'abonnement &agrave; la newsletter de l'adresse email ".htmlentities($this->ValeurParamEmail)." a &eacute;t&eacute; annul&eacute;.</div>"  ;
				}
				return $ctn ;
			}
		}
		
		class ScriptDetailAbonNewsletterSws extends ScriptAdminBaseSws
		{
			public $FormPrinc ;
			public $FltIdFormPrinc ;
			public $FltIdNewsletterFormPrinc ;
			public $FltIdCtrlFormPrinc ;
			public $FltNomFormPrinc ;
			public $CompNomFormPrinc ;
			public $LargeurNomFormPrinc = "240px" ;
			public $FltPrenomFormPrinc ;
			public $CompPrenomFormPrinc ;
			public $LargeurPrenomFormPrinc = "350px" ;
			public $FltEmailFormPrinc ;
			public $CompEmailFormPrinc ;
			public $LargeurEmailFormPrinc = "350px" ;
			public $FltRubrsFormPrinc ;
			public $CompRubrsFormPrinc ;
			public $FltIdNewsletterRubrsFormPrinc ;
			public $EditableFormPrinc = 0 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$entite = $this->ObtientEntitePage() ;
				$entite->PrepareScriptAdmin($this) ;
				$this->DetermineFormPrinc() ;
			}
			protected function DetermineFormPrinc()
			{
				$entite = $this->ObtientEntitePage() ;
				$bd = $this->ObtientBDSupport() ;
				$this->FormPrinc = new PvFormulaireDonneesHtml() ;
				$this->FormPrinc->InclureElementEnCours = 1 ;
				$this->FormPrinc->InclureTotalElements = 1 ;
				$this->FormPrinc->LibelleCommandeExecuter = "Changer statut" ;
				$this->FormPrinc->MaxFiltresEditionParLigne = 1 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "CmdChangeStatutAbonNewsletterSws" ;
				$this->FormPrinc->Editable = 0 ;
				$this->FormPrinc->AdopteScript("formPrinc", $this) ;
				$this->FormPrinc->ChargeConfig() ;
				$this->FltIdFormPrinc = $this->FormPrinc->InsereFltLgSelectHttpGet("id", $bd->EscapeVariableName($entite->NomColIdAbon)." = <self>") ;
				$this->FltNomFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("nom", $entite->NomColNomAbon) ;
				$this->FltNomFormPrinc->Libelle = "Nom" ;
				$this->CompNomFormPrinc = $this->FltNomFormPrinc->ObtientComposant() ;
				$this->CompNomFormPrinc->Largeur = $this->LargeurNomFormPrinc ;
				$this->FltPrenomFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("prenom", $entite->NomColPrenomAbon) ;
				$this->FltPrenomFormPrinc->Libelle = "Prenom" ;
				$this->CompPrenomFormPrinc = $this->FltPrenomFormPrinc->ObtientComposant() ;
				$this->CompPrenomFormPrinc->Largeur = $this->LargeurPrenomFormPrinc ;
				$this->FltEmailFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("email", $entite->NomColEmailAbon) ;
				$this->FltEmailFormPrinc->Libelle = "Email" ;
				$this->CompEmailFormPrinc = $this->FltEmailFormPrinc->ObtientComposant() ;
				$this->FltActiveFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("active", $entite->NomColActiveAbon) ;
				$this->FltActiveFormPrinc->Libelle = "Actif" ;
				$this->FltActiveFormPrinc = $this->FltActiveFormPrinc->DeclareComposant("PvZoneSelectBoolHtml") ;
				$this->FltRubrsFormPrinc = $this->FormPrinc->InsereFltEditHttpPost("id_rubrique", "") ;
				$this->FltRubrsFormPrinc->Libelle = "Rubriques" ;
				$this->CompRubrsFormPrinc = $this->FltRubrsFormPrinc->DeclareComposant("PvZoneBoiteOptionsCocherHtml") ;
				$this->CompRubrsFormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->CompRubrsFormPrinc->FournisseurDonnees->RequeteSelection = "(select t1.*, t2.".$bd->EscapeVariableName($entite->NomColIdAbonAbonmt)." id_abonne, t2.".$bd->EscapeVariableName($entite->NomColActiveAbonmt)." from ".$bd->EscapeTableName($entite->NomTableRubr)." t1 left join ".$bd->EscapeTableName($entite->NomTableAbonmt)." t2 on t1.".$bd->EscapeVariableName($entite->NomColIdRubr)."=t2.".$bd->EscapeVariableName($entite->NomColIdRubrAbonmt)." order by ".$bd->EscapeVariableName($entite->NomColIdNewsletterRubr)." desc)" ;
				$this->CompRubrsFormPrinc->NomColonneValeur = $entite->NomColIdRubr ;
				$this->CompRubrsFormPrinc->NomColonneLibelle = $entite->NomColTitreRubr ;
				$this->CompRubrsFormPrinc->NomColonneValeurParDefaut = $entite->NomColActiveAbonmt ;
				$this->FltIdAbonRubrsFormPrinc = $this->CreeFiltreHttpGet("id", "id_abonne = <self>") ;
				$this->FltIdAbonRubrsFormPrinc->EstObligatoire = 1 ;
				$this->CompRubrsFormPrinc->FiltresSelection[] = & $this->FltIdAbonRubrsFormPrinc ;
				$this->CompRubrsFormPrinc->MaxColonnesParLigne = 1 ;
				$this->CompRubrsFormPrinc->CocherAutoPremiereOption = 0 ;
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $entite->NomTableAbon ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $entite->NomTableAbon ;
				$this->FormPrinc->RedirigeAnnulerVersScript("liste_abons_".$entite->NomEntite) ;
			}
			protected function RenduDispositifBrut()
			{
				$entite = $this->ObtientEntitePage() ;
				$ctn = '' ;
				$ctn .= $entite->RenduAvantCtnSpec($this) ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctn .= $entite->RenduApresCtnSpec($this) ;
				return $ctn ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->FormPrinc->RenduDispositif() ;
				// print_r($this->CompRubrsFormPrinc->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
		}
		
		class ScriptListeJournauxNewsletterSws extends ScriptAdminBaseSws
		{
			public $FltEmail ;
			public $FltNom ;
			public $FltDateDebut ;
			public $FltDateFin ;
			public $DefColNomAbon ;
			public $DefColEmail ;
			public $DefColDateCreation ;
			public $DefColSemaine ;
			public $DefColAnnee ;
			public $DefColActions ;
			public $LienDetails ;
			public $TablPrinc ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$entite = $this->ObtientEntitePage() ;
				$entite->PrepareScriptAdmin($this) ;
				$this->DetermineTablPrinc() ;
			}
			protected function DetermineTablPrinc()
			{
				$bd = $this->ObtientBDSupport() ;
				$entite = $this->ObtientEntitePage() ;
				$this->TablPrinc = new TableauDonneesAdminSws() ;
				$this->TablPrinc->AdopteScript("tablPrinc", $this) ;
				$this->TablPrinc->ChargeConfig() ;
				$this->FltEmail = $this->TablPrinc->InsereFltSelectHttpGet("email", $bd->SqlIndexOf("upper(email_abon)", "upper(<self>)")." > 0") ;
				$this->FltEmail->Libelle = "Email" ;
				$this->FltNom = $this->TablPrinc->InsereFltSelectHttpGet("nom", $bd->SqlIndexOf("upper(nom_abon)", "upper(<self>)")." > 0 or ".$bd->SqlIndexOf("upper(prenom_abon)", "upper(<self>)")." > 0") ;
				$this->FltNom->Libelle = "Nom" ;
				$this->FltDateDebut = $this->TablPrinc->InsereFltSelectHttpGet("date_debut", $bd->SqlDatePart("date_creation")." >= ".$bd->SqlDatePart("<self>")) ;
				$this->FltDateDebut->Libelle = "Date debut" ;
				$this->FltDateDebut->DeclareComposant("PvCalendarDateInput") ;
				$this->FltDateFin = $this->TablPrinc->InsereFltSelectHttpGet("date_fin", $bd->SqlDatePart("date_creation")." <= ".$bd->SqlDatePart("<self>")) ;
				$this->FltDateFin->Libelle = "Date fin" ;
				$this->FltDateFin->DeclareComposant("PvCalendarDateInput") ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("id") ;
				$this->DefColNom = $this->TablPrinc->InsereDefCol("nom_abon", "Nom") ;
				$this->DefColPrenom = $this->TablPrinc->InsereDefCol("prenom_abon", "Pr&eacute;noms") ;
				$this->DefColEmail = $this->TablPrinc->InsereDefCol("email_abon", "Email") ;
				$this->DefColDateCreation = $this->TablPrinc->InsereDefCol("date_creation", "Date envoi") ;
				$this->DefColDateCreation->AlignElement = "center" ;
				$this->DefColDateCreation->AliasDonnees = $bd->SqlDateToStrFr("date_creation", 1) ;
				$this->DefColSemaine = $this->TablPrinc->InsereDefCol("no_semaine", "Semaine") ;
				$this->DefColSemaine->AlignElement = "center" ;
				$this->DefColAnnee = $this->TablPrinc->InsereDefCol("no_annee", "Ann&eacute;e") ;
				$this->DefColAnnee->AlignElement = "center" ;
				$this->DefColActions = $this->TablPrinc->InsereDefColActions("Actions") ;
				$this->LienDetails = $this->TablPrinc->InsereLienAction($this->DefColActions, "javascript:afficheContenuJournal(\${id})", "D&eacute;tails") ;
				$this->TablPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$hauteurCadre = 450 ;
				$largeurCadre = 676 ;
				$this->TablPrinc->FournisseurDonnees->RequeteSelection = $entite->SqlSelectDiffus() ;
				$this->TablPrinc->ContenuAvantRendu .= '<div id="Fenetre_'.$this->TablPrinc->IDInstanceCalc.'" class="ui-dialog"><iframe id="Cadre_'.$this->TablPrinc->IDInstanceCalc.'" style="width:98%; height:'.($hauteurCadre - 80).'px;" frameborder="0"></iframe></div>
<script type="text/javascript">
	function afficheContenuJournal(id) {
		jQuery("#Fenetre_'.$this->TablPrinc->IDInstanceCalc.'").dialog({
				autoOpen : true, width:'.$largeurCadre.', height:'.$hauteurCadre.', resizable : false,
		modal : true, buttons : { "Fermer" : function () { jQuery( this ).dialog("close"); }}
		}) ;
		jQuery("#Cadre_'.$this->TablPrinc->IDInstanceCalc.'").attr("src", "?'.urlencode($this->ZoneParent->NomParamScriptAppele).'=contenu_journal_'.urlencode($entite->NomEntite).'&id=" + id.toString()) ;
	}
</script>' ;
			}
			protected function RenduDispositifBrut()
			{
				$entite = $this->ObtientEntitePage() ;
				$ctn = '' ;
				$ctn .= $entite->RenduAvantCtnSpec($this) ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctn .= $entite->RenduApresCtnSpec($this) ;
				return $ctn ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->TablPrinc->RenduDispositif() ;
				return $ctn ;
			}
		}
		class ScriptContenuJournalNewsletterSws extends ScriptAdminBaseSws
		{
			public $ValeurParamIdDiffus ;
			public $LgnDiffus = array() ;
			public $UtiliserCorpsDocZone = 0 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineDiffus() ;
			}
			protected function DetermineDiffus()
			{
				$this->ValeurParamIdDiffus = _GET_def("id") ;
				$bd = $this->ObtientBDSupport() ;
				$entite = $this->ObtientEntitePage() ;
				$this->LgnDiffus = $bd->FetchSqlRow("select * from ".$entite->SqlSelectDiffus()." t1 where id=:id", array("id" => $this->ValeurParamIdDiffus)) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if(! count($this->LgnDiffus))
				{
					$ctn .= '<div class="ui-widget ui-state-error">Journal non trouv&eacute;</div>' ;
				}
				else
				{
					$ctn .= '<table cellspacing="0" cellpadding="4" class="ui-widget ui-widget-content" align="center" width="100%">
	<tr><td class="ui-widget ui-state-default"><b>Semaine</b> #'.$this->LgnDiffus["no_semaine"].' '.$this->LgnDiffus["no_annee"].'</td></tr>
	<tr><td class="ui-widget ui-state-default"><b>Destinataire</b> : '.$this->LgnDiffus["prenom_abon"].' '.$this->LgnDiffus["nom_abon"].' &lt;'.$this->LgnDiffus["email_abon"].'&gt;</td></tr>
	<tr><td class="ui-widget">'.$this->LgnDiffus["contenu"].'</td></tr>
</table>' ;
				}
				return $ctn ;
			}
		}
		
		class CritrDejaEnregNewsletterSws extends PvCritereBase
		{
			public function EstRespecte()
			{
				if($this->ScriptParent->FltEmailFormPrinc->Lie() == '')
				{
					return 1 ;
				}
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$sql = 'select * from '.$bd->EscapeTableName($entite->NomTableAbon).' where '.$bd->EscapeVariableName($entite->NomColEmailAbon).' = :email' ;
				$lgn = $bd->FetchSqlRow($sql, array("email" => $this->ScriptParent->FltEmailFormPrinc->Lie())) ;
				if(is_array($lgn) && count($lgn) == 0)
				{
					return 1 ;
				}
				elseif(! is_array($lgn))
				{
					$this->MessageErreur = "Les inscriptions sont actuellement impossibles. Veuillez r&eacute;essayer plus tard." ;
					return 0 ;
				}
				else
				{
					$this->MessageErreur = "Une personne avec la m&ecirc;me adresse email s'est deja inscrite" ;
					return 0 ;
				}
			}
		}
		
		class CmdEditAbonNewsletterSws extends PvCommandeEditionElementBase
		{
			public $Mode = 1 ;
			public $MessageSuccesExecution = "Votre inscription a &eacute;t&eacute; prise en compte" ;
			public function ExecuteInstructions()
			{
				$this->ScriptParent->FltRubrsFormPrinc->Lie() ;
				parent::ExecuteInstructions() ;
				$form = & $this->FormulaireDonneesParent ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				if($this->StatutExecution == 1)
				{
					$lgnAbon = $bd->FetchSqlRow("select * from ".$bd->EscapeVariableName($entite->NomTableAbon)." where ".$bd->EscapeVariableName($entite->NomColIdCtrlAbon).' = :idCtrl', array("idCtrl" => $this->ScriptParent->FltIdCtrlFormPrinc->Lie())) ;
					$idAbon = 0 ;
					if(is_array($lgnAbon) && count($lgnAbon) > 0)
					{
						$idAbon = $lgnAbon[$entite->NomColIdAbon] ;
					}
					if($idAbon > 0)
					{
						$idNewsletter = $this->ScriptParent->FltIdNewsletterFormPrinc->Lie() ;
						$lgns = $bd->FetchSqlRows("select * from ".$bd->EscapeTableName($entite->NomTableRubr)." where ".$bd->EscapeVariableName($entite->NomColIdNewsletterRubr)." = :idNewsletter", array("idNewsletter" => $this->ScriptParent->FltIdNewsletterFormPrinc->Lie())) ;
						$idRubrsPossible = array() ;
						foreach($lgns as $i => $lgn)
						{
							$idRubrsPossible[] = $lgn[$entite->NomColIdRubr] ;
							$bd->RunSql("delete from ".$bd->EscapeTableName($entite->NomTableAbonmt)." where ".$bd->EscapeVariableName($entite->NomColIdAbonAbonmt)." = :idAbon and ".$bd->EscapeVariableName($entite->NomColIdRubrAbonmt)." = :idRubr", array("idRubr" => $lgn[$entite->NomColIdRubr])) ;
						}
						$bd->RunSql("insert into ".$bd->EscapeTableName($entite->NomTableAbonmt)." (".$bd->EscapeVariableName($entite->NomColIdAbonAbonmt).", ".$bd->EscapeVariableName($entite->NomColIdRubrAbonmt).") select :idAbon, ".$bd->EscapeVariableName($entite->NomColIdRubr)." from ".$bd->EscapeVariableName($entite->NomTableRubr)." where ".$bd->EscapeVariableName($entite->NomColIdNewsletterRubr)." = :idNewsletter", array("idNewsletter" => $idNewsletter, "idAbon" => $idAbon)) ;
						$idRubrsSelect = $this->ScriptParent->FltRubrsFormPrinc->ValeurBrute ;
						foreach($idRubrsSelect as $i => $idRubr)
						{
							if(! in_array($idRubr, $idRubrsPossible))
							{
								continue ;
							}
							$bd->UpdateRow(
								$entite->NomTableAbonmt,
								array($entite->NomColActiveAbon => 1),
								$bd->EscapeVariableName($entite->NomColIdAbonAbonmt)." = :idAbon and ".$bd->EscapeVariableName($entite->NomColIdRubrAbonmt)." = :idRubr",
								array("idAbon" => $idAbon, "idRubr" => $idRubr)
							) ;
						}
						// print_r($bd) ;
					}
				}
				// 
				// foreach()
			}
		}
		class CmdChangeStatutAbonNewsletterSws extends PvCommandeExecuterBase
		{
			public $Mode = 1 ;
			public $MessageSuccesExecution = "Le statut de l'abonn&eacute; a chang&eacute;" ;
			public function ExecuteInstructions()
			{
				$this->ScriptParent->FltIdFormPrinc->Lie() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				if($this->StatutExecution == 1)
				{
					$sql = "update ".$bd->EscapeTableName($entite->NomTableAbon)." set ".$bd->EscapeVariableName($entite->NomColActiveAbon)." = case when ".$bd->EscapeVariableName($entite->NomColActiveAbon)." = 1 then 0 else 1 end where ".$bd->EscapeVariableName($entite->NomColIdAbon)." = :idAbon" ;
					$ok = $bd->RunSql($sql, array("idAbon" => $this->ScriptParent->FltIdFormPrinc->Lie())) ;
					$this->StatutExecution = $ok ;
					if(! $ok)
					{
						$this->RenseigneErreur("Erreur : ".$bd->ConnectionException) ;
					}
					else
					{
						$this->ConfirmeSucces($this->MessageSuccesExecution) ;
					}
				}
			}
		}
	}
	
?>