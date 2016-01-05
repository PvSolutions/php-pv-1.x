<?php
	
	if(! defined('ENTITE_NEWSLETTER_SWS'))
	{
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
				$this->EntiteAbonNewsletter = $this->InsereEntite("abonne_newsletter", $this->CreeEntiteAbonNewsletter()) ;
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
			public $ScriptListeRubrs ;
			public $ScriptAjoutRubr ;
			public $ScriptModifRubr ;
			public $ScriptSupprRubr ;
			public $ScriptInscritAbon ;
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
			}
			public function RemplitZonePubl(& $zone)
			{
				parent::RemplitZoneAdmin($zone) ;
				$this->ScriptInscritAbon = $this->InsereScript('inscrit_abonne_'.$this->NomEntite, new ScriptInscritAbonNewsletterSws(), $zone, $this->ObtientPrivilegesConsult()) ;
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
			public function SqlSelectResultsSupport(& $entite, & $script, $lgn)
			{
				return '' ;
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
			public function SqlSelectEntiteSupport(& $entite, & $script)
			{
				$bd = $script->ObtientBDSupport() ;
				$entiteRubr = $entite->ModuleParent->SystemeParent->ModuleArticle->EntiteRubrique ;
				$sql = 'select '.$bd->EscapeVariableName($entiteRubr->NomColId).' id, '.$bd->EscapeVariableName($entiteRubr->NomColTitre).' titre from '.$bd->EscapeTableName($entiteRubr->NomTable).' order by '.$bd->EscapeVariableName($entiteRubr->NomColDatePubl).' desc, '.$bd->EscapeVariableName($entiteRubr->NomColHeurePubl).' desc' ;
				return $sql ;
			}
			public function SqlSelectResultsSupport(& $entite, & $script, $lgn)
			{
				return '' ;
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
		}
		
		class EntiteAbonNewsletterSws extends EntiteTableSws
		{
			public $TitreMenu = "Abonn&eacute;s" ;
			public $TitreAjoutEntite = "Ajout abonn&eacute;" ;
			public $TitreModifEntite = "Modification abonn&eacute;" ;
			public $TitreSupprEntite = "Suppression abonn&eacute;" ;
			public $TitreListageEntite = "Liste des abonn&eacute;s" ;
			public $TitreConsultEntite = "D&eacute;tails abonn&eacute;" ;
			public $NomEntite = "abonne_newsletter" ;
			public $NomTable = "abonne_newsletter" ;
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
				$this->TablRubrs = new PvTableauDonneesHtml() ;
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
	}
	
?>