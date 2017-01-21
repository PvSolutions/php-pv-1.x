<?php
	
	if(! defined('ENTITE_LIVRE_D_OR_SWS'))
	{
		if(! defined('COMPOSANT_LIVRE_D_OR_SWS'))
		{
			include dirname(__FILE__)."/ComposantIU/LivreDOr.class.php" ;
		}
		define('ENTITE_LIVRE_D_OR_SWS', 1) ;
		
		class ModuleLivreDOrSws extends ModulePageBaseSws
		{
			public $TitreMenu = "Livres d'Or" ;
			public $NomRef = "livre_d_or" ;
			public $EntiteLivreDOr ;
			public $EntiteCmtLivreDOr ;
			public $CheminIllustration = "images/entete_livre_dor.jpg" ;
			public $CheminIconeEcrireMsg = "images/signer.gif" ;
			protected function CreeEntiteCmtLivreDOr()
			{
				return new EntiteCmtLivreDOrSws() ;
			}
			protected function CreeEntiteLivreDOr()
			{
				return new EntiteLivreDOrSws() ;
			}
			protected function ChargeEntites()
			{
				$this->EntiteLivreDOr = $this->InsereEntite("livre_d_or", $this->CreeEntiteLivreDOr()) ;
				$this->EntiteCmtLivreDOr = $this->InsereEntite("cmd_livre_d_or", $this->CreeEntiteCmtLivreDOr()) ;
			}
		}
		
		class EntiteCmtLivreDOrSws extends EntiteTableSws
		{
			public $TitreMenu = "Commentaires" ;
			public $NomEntite = "cmt_livre_d_or" ;
			public $NomTable = "cmt_livre_d_or" ;
			public $AccepterTitre = 1 ;
			public $TitreAjoutEntite = "Ajout commentaire" ;
			public $TitreModifEntite = "Modification commentaire" ;
			public $TitreSupprEntite = "Suppression commentaire" ;
			public $TitreListageEntite = "Liste des commentaires" ;
			public $TitreConsultEntite = "Détails commentaire" ;
			public $LibTitre = "Titre" ;
			public $LibDescription = "Description" ;
			public $LibIdLivreDOr = "Livre d'or" ;
			public $NomParamDescription = "description" ;
			public $NomParamIdLivreDOr = "id_livre_d_or" ;
			public $NomColDescription = "description" ;
			public $NomColCheminImage = "chemin_image" ;
			public $NomColIdLivreDOr = "id_livre_d_or" ;
			public $LibAuteur = "Nom" ;
			public $NomParamAuteur = "auteur" ;
			public $NomColAuteur = "auteur" ;
			public $FltFrmElemAuteur ;
			public $FltFrmElemDescription ;
			public $FltFrmElemIdLivreDOr ;
			public $NomScriptPoster = "poster" ;
			public $LgnLivreDOr ;
			public $ValeurParamLivreDOr ;
			public $BlocSommaireLivreDOr ;
			public $InclureScriptConsult = 0 ;
			public $TotalLignesDescription = 6 ;
			public $TotalColonnesDescription = 80 ;
			public $SecuriserEdition = 1 ;
			public $FltTblListIdLivreDOr ;
			public $DefColTblListLivreDOr ;
			public $NomParamTblListIdLivreDOr = "pIdLivreDOr" ;
			public $LibelleCmdEnvoi = "Envoyer" ;
			public $MsgSuccesCmdEnvoi = "Votre message a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s. Il sera valid&eacute; ulterieurement." ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
			protected function CreeScriptPoster()
			{
				return new ScriptPosterCmtLivreDOr() ;
			}
			public function RemplitZonePubl(& $zone)
			{
				parent::RemplitZonePubl($zone) ;
				$this->ScriptPoster = $this->InsereScript($this->NomScriptPoster.'_'.$this->NomEntite, $this->CreeScriptPoster(), $zone, $this->PrivilegesConsult) ;
			}
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' auteur' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColDescription).' description' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColIdLivreDOr).' id_livre_d_or' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Auteur
				$this->FltFrmElemAuteur = $frm->CreeFiltreHttpPost($this->NomParamAuteur, $this->NomColAuteur) ;
				array_splice($frm->FiltresEdition, count($frm->FiltresEdition) - 2, 0, array(& $this->FltFrmElemAuteur)) ;
				$this->FltFrmElemAuteur->Libelle = $this->LibAuteur ;
				$comp = $this->FltFrmElemAuteur->ObtientComposant() ;
				$comp->Largeur = "180px" ;
				// Titre
				$comp = $this->FltFrmElemTitre->ObtientComposant() ;
				$comp->Largeur = "200px" ;
				// Description
				$this->FltFrmElemDescription = $frm->InsereFltEditHttpPost($this->NomParamDescription, $this->NomColDescription) ;
				$this->FltFrmElemDescription->Libelle = $this->LibDescription ;
				$comp = $this->FltFrmElemDescription->DeclareComposant("PvZoneMultiligneHtml") ;
				$comp->TotalColonnes = $this->TotalColonnesDescription ;
				$comp->TotalLignes = $this->TotalLignesDescription ;
				// Livre d'or
				$this->FltFrmElemIdLivreDOr = $frm->InsereFltEditHttpPost($this->NomParamIdLivreDOr, $this->NomColIdLivreDOr) ;
				$this->FltFrmElemIdLivreDOr->Libelle = $this->LibIdLivreDOr ;
				$comp = $this->FltFrmElemIdLivreDOr->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$comp->FournisseurDonnees = $frm->ScriptParent->ModulePage->CreeFournDonnees() ;
				$comp->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteLivreDOr->NomTable ;
				$comp->NomColonneValeur = $this->ModuleParent->EntiteLivreDOr->NomColId ;
				$comp->NomColonneLibelle = $this->ModuleParent->EntiteLivreDOr->NomColTitre ;
			}
			protected function ReqSelectTblList(& $bd)
			{
				$sql = '(select t1.*, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteLivreDOr->NomColId).' id_livre_d_or_parent, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteLivreDOr->NomColTitre).' titre_livre_d_or_parent from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($this->ModuleParent->EntiteLivreDOr->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdLivreDOr).' = t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteLivreDOr->NomColId).')' ;
				return $sql ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				$tbl->FournisseurDonnees->RequeteSelection = $this->ReqSelectTblList($bd) ;
				$this->DefColTblListLivreDOr = $tbl->InsereDefCol("titre_livre_d_or_parent", $this->LibIdLivreDOr) ;
				$this->FltTblListIdLivreDOr = $tbl->InsereFltSelectHttpGet($this->NomParamTblListIdLivreDOr, $bd->EscapeVariableName($this->NomColIdLivreDOr).' = <self>') ;
				$this->FltTblListIdLivreDOr->Libelle = $this->LibIdLivreDOr ;
				$compIdLivreDOr = $this->FltTblListIdLivreDOr->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$compIdLivreDOr->FournisseurDonnees = $this->CreeFournDonnees() ;
				$compIdLivreDOr->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteLivreDOr->NomTable ;
				$fltIdLivreDOrSelect = $tbl->ScriptParent->CreeFiltreHttpGet($this->NomParamTblListIdLivreDOr) ;
				$compIdLivreDOr->InclureElementHorsLigne = 1 ;
				$compIdLivreDOr->FiltresSelection[] = & $fltIdLivreDOrSelect ;
				$compIdLivreDOr->NomColonneValeur = $this->ModuleParent->EntiteLivreDOr->NomColId ;
				$compIdLivreDOr->NomColonneLibelle = $this->ModuleParent->EntiteLivreDOr->NomColTitre ;
			}
			public function RemplitScriptPoster(& $script)
			{
				$livreDOrTrouve = $this->DetecteLgnLivreDOr() ;
				parent::RemplitScriptEdit($script) ;
				if($this->ValidScriptEdit == 1)
				{
					$this->FrmElem->CommandeAnnuler->Visible = 0 ;
					$this->FrmElem->CommandeExecuter->Libelle = $this->LibelleCmdEnvoi ;
					$this->FrmElem->CommandeExecuter->MessageSuccesExecution = $this->MsgSuccesCmdEnvoi ;
					$this->FrmElem->CacherFormulaireFiltresApresCmd = 1 ;
					$this->FigeFiltresPubl() ;
					if($livreDOrTrouve)
					{
						$this->BlocSommaireLivreDOr = $this->ModuleParent->EntiteLivreDOr->CreeBlocSommaire() ;
						$this->BlocSommaireLivreDOr->DonneesSupport = & $this->LgnLivreDOr ;
						$this->BlocSommaireLivreDOr->AdopteScript("blocSommaireLivreDOr", $script) ;
						$this->BlocSommaireLivreDOr->ChargeConfig() ;
					}
					else
					{
						$this->NotifieScriptEditIndisp($script) ;
					}
				}
			}
			protected function DetecteLgnLivreDOr()
			{
				$this->ValeurParamLivreDOr = intval(_GET_def($this->ModuleParent->EntiteLivreDOr->NomParamId)) ;
				$this->LgnLivreDOr = $this->ModuleParent->EntiteLivreDOr->SelectLgn($this->ValeurParamLivreDOr) ;
				return (is_array($this->LgnLivreDOr) && count($this->LgnLivreDOr) > 0) ;
			}
			public function RenduScriptPoster(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->BlocSommaireLivreDOr->RenduDispositif() ;
				$ctn .= $this->FrmElem->RenduDispositif() ;
				return $ctn ;
			}
			protected function ObtientParamsUrlFrmElem(& $frm)
			{
				if($frm->ScriptParent->InitFrmElem->Role == "Ajout" && count($this->LgnLivreDOr))
				{
					return array($this->ModuleParent->EntiteLivreDOr->NomParamId => $this->LgnLivreDOr["id"]) ;
				}
				return parent::ObtientParamsUrlFrmElem($frm) ;
			}
		}
		class EntiteLivreDOrSws extends EntiteTableSws
		{
			public $TitreMenu = "Livre d'Or" ;
			public $TitreAjoutEntite = "Ajout livre d'or" ;
			public $TitreModifEntite = "Modification livre d'or" ;
			public $TitreSupprEntite = "Suppression livre d'or" ;
			public $TitreListageEntite = "Liste des livres d'or" ;
			public $TitreConsultEntite = "Détails livre d'or" ;
			public $NomEntite = "livre_d_or" ;
			public $NomTable = "livre_d_or" ;
			public $AccepterTitre = 1 ;
			public $LibSommaire = "Sommaire" ;
			public $NomParamSommaire = "sommaire" ;
			public $NomColSommaire = "sommaire" ;
			public $FltFrmElemSommaire ;
			public $ScriptListageCmt ;
			public $NomScriptListageCmt = "comments" ;
			public $TblCmt ;
			public $DefColTblCmtId ;
			public $DefColTblCmtDescription ;
			public $DefColTblCmtDatePubl ;
			public $DefColTblCmtHeurePubl ;
			public $DefColTblCmtAuteur ;
			public $FltTblCmtId ;
			public $InclureScriptConsult = 0 ;
			public $TotalColonnesSommaire = 80 ;
			public $TotalLignesSommaire = 6 ;
			public $BlocSommaire ;
			public $MsgAucunCmt = "Aucun commentaire n'a encore &eacute;t&eacute; post&eacute;" ;
			public $LibelleLienEcrireMsg = "Ecrire un message" ;
			public $CouleurTitreCmt = "#9a6951" ;
			public $TailleTitreCmt = "14px" ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
			protected function VerifScriptListageCmt()
			{
				return $this->DetecteLgnEnCours() ;
			}
			protected function CreeScriptListageCmt()
			{
				return new ScriptListageCmtLivreDOr() ;
			}
			public function RemplitZonePubl(& $zone)
			{
				parent::RemplitZonePubl($zone) ;
				$this->ScriptListageCmt = $this->InsereScript($this->NomScriptListageCmt.'_'.$this->NomEntite, $this->CreeScriptListageCmt(), $zone, $this->PrivilegesConsult) ;
			}
			protected function InclutCSSListageCmt(& $script)
			{
				$ctnCSS = '.cmt {
	margin-bottom:8px ;
}
.cmt .titre {
	font-size:'.$this->TailleTitreCmt.' ;
	color:'.$this->CouleurTitreCmt.' ;
}
.cmt .description {
}
.cmt .auteur {
}' ;
				$script->ZoneParent->InscritContenuCSS($ctnCSS) ;
			}
			public function RemplitScriptListageCmt(& $script)
			{
				// parent::RemplitScriptEdit($script) ;
				$this->InclutCSSListageCmt($script) ;
				if($this->VerifScriptListageCmt())
				{
					$this->FrmElem->CacherBlocCommandes = 1 ;
					$this->FrmElem->Editable = 0 ;
					$this->FigeFiltresPubl() ;
					$this->RemplitTblCmt($script) ;
					$this->RemplitBlocSommaire($script) ;
					$script->Titre = $this->LgnEnCours["titre"] ;
					$script->TitreDocument = $this->LgnEnCours["titre"] ;
				}
				else
				{
					$this->NotifieScriptEditIndisp($script) ;
				}
			}
			public function CreeBlocSommaire()
			{
				return new BlocSommaireLivreDOrSws() ;
			}
			protected function RemplitBlocSommaire(& $script)
			{
				$this->BlocSommaire = $this->CreeBlocSommaire() ;
				$this->BlocSommaire->DonneesSupport = & $this->LgnEnCours ;
				$this->BlocSommaire->AdopteScript("blocSommaire", $script) ;
				$this->BlocSommaire->ChargeConfig() ;
			}
			protected function RemplitTblCmt(& $script)
			{
				$this->TblCmt = $this->CreeTblCmt() ;
				$this->TblCmt->CacherFormulaireFiltres = 1 ;
				$this->TblCmt->AdopteScript('tblCmt', $script) ;
				$this->TblCmt->ChargeConfig() ;
				$this->DefColTblCmtId = $this->TblCmt->InsereDefCol($this->ModuleParent->EntiteCmtLivreDOr->NomColId) ;
				$this->DefColTblCmtAuteur = $this->TblCmt->InsereDefCol($this->ModuleParent->EntiteCmtLivreDOr->NomColAuteur) ;
				$this->DefColTblCmtAuteur->FormatValeur = '<div class="auteur">Par ${luimeme}</div>' ;
				$this->DefColTblCmtTitre = $this->TblCmt->InsereDefCol($this->ModuleParent->EntiteCmtLivreDOr->NomColTitre) ;
				$this->DefColTblCmtDescription = $this->TblCmt->InsereDefCol($this->ModuleParent->EntiteCmtLivreDOr->NomColDescription) ;
				$this->DefColTblCmtDatePubl = $this->TblCmt->InsereDefCol($this->ModuleParent->EntiteCmtLivreDOr->NomColDatePubl) ;
				$this->DefColTblCmtHeurePubl = $this->TblCmt->InsereDefCol($this->ModuleParent->EntiteCmtLivreDOr->NomColHeurePubl) ;
				$this->TblCmt->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->TblCmt->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteCmtLivreDOr->NomTable ;
				$this->FltTblCmtId = $this->TblCmt->InsereFltSelectHttpGet($this->ModuleParent->EntiteCmtLivreDOr->NomParamId) ;
				$this->FltTblCmtId->LectureSeule = 1 ;
				$this->TblCmt->ToujoursAfficher = 1 ;
				$this->TblCmt->ContenuLigneModele = '<div class="cmt">
<div class="titre"><span class="date">${VALEUR_COL_'.$this->ModuleParent->EntiteCmtLivreDOr->NomColDatePubl.'} ${VALEUR_COL_'.$this->ModuleParent->EntiteCmtLivreDOr->NomColHeurePubl.'}</span> ${VALEUR_COL_'.$this->ModuleParent->EntiteCmtLivreDOr->NomColTitre.'}</div>
<div class="description">${VALEUR_COL_'.$this->ModuleParent->EntiteCmtLivreDOr->NomColDescription.'}</div>
${VALEUR_COL_'.$this->ModuleParent->EntiteCmtLivreDOr->NomColAuteur.'}
</div>' ;
				$this->TblCmt->MessageAucunElement = $this->MsgAucunCmt ;
				$this->TblCmt->NavigateurRangees = new PvNavTableauDonneesHtml() ;
			}
			protected function CreeTblCmt()
			{
				return new PvGrilleDonneesHtml() ;
			}
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColSommaire).' sommaire' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Titre
				$comp = $this->FltFrmElemTitre->ObtientComposant() ;
				$comp->Largeur = "200px" ;
				// Sommaire
				$this->FltFrmElemSommaire = $frm->InsereFltEditHttpPost($this->NomParamSommaire, $this->NomColSommaire) ;
				$this->FltFrmElemSommaire->Libelle = $this->LibSommaire ;
				$comp = $this->FltFrmElemSommaire->DeclareComposant("PvZoneMultiligneHtml") ;
				$comp->TotalColonnes = $this->TotalColonnesSommaire ;
				$comp->TotalLignes = $this->TotalLignesSommaire ;
			}
			protected function ChargeTblList(& $tabl)
			{
				parent::ChargeTblList($tabl) ;
			}
			public function RenduScriptListageCmt(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->RenduTitreScript($script) ;
				$ctn .= $this->BlocSommaire->RenduDispositif() ;
				$ctn .= $this->TblCmt->RenduDispositif() ;
				return $ctn ;
			}
		}
		
		class ScriptPosterCmtLivreDOr extends ScriptAjoutEntiteTableSws
		{
			public $NecessiteMembreConnecte = 0 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->EntitePage->RemplitScriptPoster($this) ;
			}
			protected function RenduDispositifBrut()
			{
				return $this->EntitePage->RenduScriptPoster($this) ;
			}
		}
		class ScriptListageCmtLivreDOr extends ScriptModifEntiteTableSws
		{
			public $NecessiteMembreConnecte = 0 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->EntitePage->RemplitScriptListageCmt($this) ;
			}
			protected function RenduDispositifBrut()
			{
				return $this->EntitePage->RenduScriptListageCmt($this) ;
			}
		}
		
	}
	
?>