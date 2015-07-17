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
			}
			protected function ChargeEntites()
			{
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
			public $NomTableRubr = "newsletter_rubr" ;
			public $NomColIdRubr = "id" ;
			public $NomColTitreRubr = "titre" ;
			public $NomColIdMdlEntRubr = "ref_modele_rubrique" ;
			public $NomColIdEntSupportRubr = "id_entite_support" ;
			public $NomColNomEntSupportRubr = "nom_entite_support" ;
			public $NomColDateCreationRubr = "date_creation" ;
			public $NomColDateModifRubr = "date_modif" ;
			public $NomColDatePublRubr = "date_publication" ;
			public $NomColHeurePublRubr = "heure_publication" ;
			public $NomColStatutPublRubr = "statut_publication" ;
			public $NomColIdMembreCreationRubr = "id_membre_creation" ;
			public $NomColIdMembreModifRubr = "id_membre_modif" ;
			public $ScriptListeRubrs ;
			public $LienListeRubrsTblList ;
			public $LibListeRubrsTblList = "Rubriques" ;
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
			}
			public function RemplitZoneAdmin(& $zone)
			{
				parent::RemplitZoneAdmin($zone) ;
				$this->ScriptListeRubrs = $this->InsereScript('liste_rubrs_'.$this->NomEntite, new ScriptListeRubrsNewsletterSws(), $zone, $this->ObtientPrivilegesEdit()) ;
			}
		}
		
		class SommaireNewsletterSws extends PvFormulaireDonneesHtml
		{
			public $FltId ;
			public $FltTitre ;
			public $InclureTotalElements = 1 ;
			public $InclureElementEnCours = 1 ;
			public $Editable = 0 ;
			public $InscrireCommandeAnnuler = 0 ;
			public $InscrireCommandeExecuter = 0 ;
			public function ChargeFiltresSelection()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$this->FltId = $this->InsereFltLgSelectHttpGet($entite->NomParamId, $bd->EscapeVariableName($entite->NomColId)."=<self>") ;
				$this->FltId->Obligatoire = 1 ;
			}
			public function ChargeFiltresEdition()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$this->FltTitre = $this->InsereFltEditHttpPost($entite->NomParamTitre, $entite->NomColTitre) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$this->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->FournisseurDonnees->RequeteSelection = $entite->NomTable ;
				$this->FournisseurDonnees->TableEdition = $entite->NomTable ;
			}
		}
		
		class MdlRubrBaseNewsletterSws
		{
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
			public function SqlSelectEntiteSupport(& $entite)
			{
				$bd = $entite->ObtientBDSupport() ;
				$sql = '' ;
				return $sql ;
			}
			public function SqlSelectResultsSupport(& $entite, $lgn)
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
				return 'Liste des articles d\'une rubrique' ;
			}
			public function RemplitMenuEntite(& $menu, & $entite)
			{
			}
			public function SqlSelectEntiteSupport(& $entite)
			{
				$bd = $entite->ObtientBDSupport() ;
				$entiteRubr = $entite->ModuleParent->SystemeParent->EntiteRubrique ;
				$sql = 'select '.$bd->EscapeVariableName($entiteRubr->NomColId).' id, '.$bd->EscapeVariableName($entiteRubr->NomColTitre).' titre from '.$bd->EscapeTableName($entiteRubr->NomTable).' order by '.$bd->EscapeVariableName($entiteRubr->NomColDatePubl).' desc, '.$bd->EscapeVariableName($entiteRubr->NomColHeurePubl).' desc' ;
				return $sql ;
			}
			public function SqlSelectResultsSupport(& $entite, $lgn)
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
				return 'Liste des articles d\'une rubrique ou de ses sous-rubriques' ;
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
		
		class ScriptBaseRubrNewsletterSws extends ScriptBaseSws
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
			public $DefColIdTablRubr ;
			public $DefColTitleTablRubr ;
			public $DefColIdMdlTablRubr ;
			public $DefColNomEntTablRubr ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineTablRubrs() ;
			}
			protected function DetermineTablRubrs()
			{
				$this->TablRubrs = new PvTableauDonneesHtml() ;
				$this->TablRubrs->AdopteScript('tablRubrs', $this) ;
				$this->TablRubrs->ChargeConfig() ;
				// $this->DefColIdMdlTablRubr = $this->TablRubrs->InsereDefCol() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = parent::RenduDispositifBrut() ;
				return $ctn ;
			}
		}
	}
	
?>