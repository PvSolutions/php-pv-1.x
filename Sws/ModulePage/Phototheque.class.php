<?php
	
	if(! defined('ENTITE_PHOTOTHEQUE_SWS'))
	{
		define('ENTITE_PHOTOTHEQUE_SWS', 1) ;
		
		class ModulePhotothequeSws extends ModulePageBaseSws
		{
			public $TitreMenu = "Phototh&egrave;ques" ;
			public $NomRef = "phototheque" ;
			public $EntitePhototheque ;
			public $EntiteImagePhototheque ;
			protected function CreeEntiteImagePhototheque()
			{
				return new EntiteImagePhotothequeSws() ;
			}
			protected function CreeEntitePhototheque()
			{
				return new EntitePhotothequeSws() ;
			}
			protected function ChargeEntites()
			{
				$this->EntitePhototheque = $this->InsereEntite("phototheque", $this->CreeEntitePhototheque()) ;
				$this->EntiteImagePhototheque = $this->InsereEntite("image_phototheque", $this->CreeEntiteImagePhototheque()) ;
			}
		}
		
		class EntiteImagePhotothequeSws extends EntiteTableSws
		{
			public $InclureScriptConsult = 0 ;
			public $InclureScriptEnum = 0 ;
			public $AccepterTitre = 1 ;
			public $PresentDansMenu = 1 ;
			public $NomEntite = "image_phototheque" ;
			public $NomTable = "image_phototheque" ;
			public $TitreMenu = "Images" ;
			public $TitreAjoutEntite = "Ajout image photot&egrave;que" ;
			public $TitreModifEntite = "Modification image photot&egrave;que" ;
			public $TitreSupprEntite = "Suppression image photot&egrave;que" ;
			public $TitreListageEntite = "Liste des images photot&egrave;que" ;
			public $TitreConsultEntite = "D&eacute;tails image photot&egrave;que" ;
			public $LibDescription = "Description" ;
			public $LibCheminImage = "Image" ;
			public $LibCheminMiniature = "Miniature" ;
			public $LibIdPhototheque = "Phototheque" ;
			public $NomParamTitre = "titre" ;
			public $NomParamDescription = "description" ;
			public $NomParamCheminImage = "chemin_image" ;
			public $NomParamCheminMiniature = "chemin_miniature" ;
			public $NomParamIdPhototheque = "id_phototheque" ;
			public $NomColDescription = "description" ;
			public $NomColCheminImage = "chemin_image" ;
			public $NomColCheminMiniature = "chemin_miniature" ;
			public $NomColIdPhototheque = "id_phototheque" ;
			public $CheminTelechargImage = "images/photos" ;
			public $FltFrmElemDescription ;
			public $FltFrmElemUrl ;
			public $FltFrmElemCheminImage ;
			public $FltFrmElemCheminMiniature ;
			public $FltFrmElemIdPhototheque ;
			public $FltTblListPhototheque ;
			public $NomParamTblListPhototheque = "pIdPhototheque" ;
			public $DefColTblListPhototheque ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColDescription).' description' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminImage).' chemin_image' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminMiniature).' chemin_miniature' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Titre
				$compTitre = $this->FltFrmElemTitre->ObtientComposant() ;
				$compTitre->Largeur = "300px" ;
				// Description
				$this->FltFrmElemDescription = $frm->InsereFltEditHttpPost($this->NomParamDescription, $this->NomColDescription) ;
				$this->FltFrmElemDescription->Libelle = $this->LibDescription ;
				$comp = $this->FltFrmElemDescription->DeclareComposant("PvZoneMultiligneHtml") ;
				$comp->TotalColonnes = 120 ;
				$comp->TotalLignes = 6 ;
				// Chemin Image
				$this->FltFrmElemCheminImage = $frm->InsereFltEditHttpUpload($this->NomParamCheminImage, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargImage, $this->NomColCheminImage) ;
				$this->FltFrmElemCheminImage->Libelle = $this->LibCheminImage ;
				$this->FltFrmElemCheminImage->ToujoursRenseigner = 1 ;
				// Chemin Miniature
				$this->FltFrmElemCheminMiniature = $frm->InsereFltEditHttpUpload($this->NomParamCheminMiniature, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargImage, $this->NomColCheminMiniature) ;
				$this->FltFrmElemCheminMiniature->Libelle = $this->LibCheminMiniature ;
				// ID Phototheque
				$this->FltFrmElemIdPhototheque = $frm->InsereFltEditHttpPost($this->NomParamIdPhototheque, $this->NomColIdPhototheque) ;
				$this->FltFrmElemIdPhototheque->Libelle = $this->LibIdPhototheque ;
				$comp = $this->FltFrmElemIdPhototheque->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$comp->FournisseurDonnees = $frm->ScriptParent->ModulePage->CreeFournDonnees() ;
				$comp->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntitePhototheque->NomTable ;
				$comp->NomColonneValeur = $this->ModuleParent->EntitePhototheque->NomColId ;
				$comp->NomColonneLibelle = $this->ModuleParent->EntitePhototheque->NomColTitre ;
			}
			protected function ReqSelectTblList(& $bd)
			{
				$sql = '(select t1.*, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntitePhototheque->NomColId).' id_phototheque_parent, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntitePhototheque->NomColTitre).' titre_phototheque_parent from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($this->ModuleParent->EntitePhototheque->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdPhototheque).'=t2.'.$bd->EscapeVariableName($this->ModuleParent->EntitePhototheque->NomColId).')' ;
				return $sql ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				$tbl->FournisseurDonnees->RequeteSelection = $this->ReqSelectTblList($bd) ;
				$this->DefColTblListPhototheque = $tbl->InsereDefCol("titre_phototheque_parent", $this->LibIdPhototheque) ;
				$this->DefColTblListTitre->Largeur = "20%" ;
				$this->DefColTblListPhototheque->Largeur = "20%" ;
				$compTitre = $this->FltTblListTitre->ObtientComposant() ;
				$compTitre->Largeur = "200px" ;
				$this->FltTblListPhototheque = $tbl->InsereFltSelectHttpGet($this->NomParamTblListPhototheque, $bd->EscapeVariableName($this->NomColIdPhototheque).' = <self>') ;
				$this->FltTblListPhototheque->Libelle = $this->LibIdPhototheque ;
				$compPhototheque = $this->FltTblListPhototheque->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$compPhototheque->FournisseurDonnees = $this->CreeFournDonnees() ;
				$compPhototheque->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntitePhototheque->NomTable ;
				$fltPhototheque = $tbl->ScriptParent->CreeFiltreHttpGet($this->NomParamTblListPhototheque) ;
				$compPhototheque->FiltresSelection[] = & $fltPhototheque ;
				$compPhototheque->InclureElementHorsLigne = 1 ;
				$compPhototheque->NomColonneValeur = $this->ModuleParent->EntitePhototheque->NomColId ;
				$compPhototheque->NomColonneLibelle = $this->ModuleParent->EntitePhototheque->NomColTitre ;
			}
		}
		class EntitePhotothequeSws extends EntiteTableSws
		{
			public $InclureScriptConsult = 1 ;
			public $InclureScriptEnum = 1 ;
			protected $InclureScriptEnumPlanSite = 1 ;
			protected $InclureScriptConsultPlanSite = 1 ;
			protected $NomColTitreConsultPlanSite = "titre" ;
			public $TitreMenu = "Phototh&egrave;ques" ;
			public $TitreEnumEntite = "Phototh&egrave;ques" ;
			public $TitreAjoutEntite = "Ajout photot&egrave;que" ;
			public $TitreModifEntite = "Modification photot&egrave;que" ;
			public $TitreSupprEntite = "Suppression photot&egrave;que" ;
			public $TitreListageEntite = "Liste des photot&egrave;ques" ;
			public $TitreConsultEntite = "D&eacute;tails photot&egrave;que" ;
			public $NomEntite = "phototheque" ;
			public $NomTable = "phototheque" ;
			public $LibTitre = "Titre" ;
			public $NomParamTitre = "titre" ;
			public $NomColTitre = "titre" ;
			public $FltFrmElemTitre ;
			public $DefColTblListTitre ;
			public $LibDescription = "Description" ;
			public $NomParamDescription = "description" ;
			public $NomColDescription = "description" ;
			public $FltFrmElemDescription ;
			public $NomClasseCmdSuppr = "CmdSupprPhotothequeSws" ;
			protected $GrilleEnum ;
			public function ObtientReqSqlFluxRSS()
			{
				$this->DefFluxRSS->NomColCheminImage = "" ;
				return parent::ObtientReqSqlFluxRSS() ;
			}
			protected function PrepareScriptConsult(& $script)
			{
				parent::PrepareScriptConsult($script) ;
				$script->TitreDocument = $this->LgnEnCours["titre"] ;
				$script->Titre = $this->LgnEnCours["titre"] ;
				if($this->AccepterAttrsMeta == 1)
				{
					if($this->LgnEnCours["mots_cles_meta"] != "")
						$script->MotsCleMeta = $this->LgnEnCours["mots_cles_meta"] ;
					if($this->LgnEnCours["description_meta"] != "")
						$script->DescriptionMeta = $this->LgnEnCours["description_meta"] ;
				}
			}
			protected function CreeBlocConsult()
			{
				return new PvJQueryLightbox() ;
			}
			protected function ChargeBlocConsult(& $bloc)
			{
				$syst = ReferentielSws::$SystemeEnCours ;
				$bd = & $this->ObtientBDSupport() ;
				$script = & $bloc->ScriptParent ;
				$entiteImg = & $this->ModuleParent->EntiteImagePhototheque ;
				$bloc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$bloc->FournisseurDonnees->RequeteSelection = '(select t1.*, '.$syst->SqlCheminPubl('t1.'.$bd->EscapeVariableName($entiteImg->NomColCheminMiniature)).' chemin_miniature_publ, '.$syst->SqlCheminPubl('t1.'.$bd->EscapeVariableName($entiteImg->NomColCheminImage)).' chemin_image_publ from '.$bd->EscapeTableName($entiteImg->NomTable).' t1 order by '.$bd->EscapeVariableName($entiteImg->NomColDatePubl).' desc, '.$bd->EscapeVariableName($entiteImg->NomColHeurePubl).' desc)' ;
				$fltIdPhototq = $script->CreeFiltreHttpGet("id", $bd->EscapeVariableName($entiteImg->NomColIdPhototheque).' = <self>') ;
				$fltIdPhototq->Obligatoire = 1 ;
				$bloc->FiltresSelection["id"] = & $fltIdPhototq ;
				$bloc->NomColTitre = $entiteImg->NomColTitre ;
				$bloc->NomColCheminImage = "chemin_image_publ" ;
				$bloc->NomColCheminMiniature = "chemin_miniature_publ" ;
				$bloc->LargeurMiniature = 240 ;
			}
			protected function ChargeBlocEnum(& $bloc)
			{
				$syst = ReferentielSws::$SystemeEnCours ;
				$zone = & $bloc->ZoneParent ;
				$bloc->MaxColonnes = 4 ;
				$bloc->EspacementCell = 10 ;
				$bloc->SourceValeursSuppl->EntitePageSupport = & $this ;
				parent::ChargeBlocEnum($bloc) ;
				$bloc->InsereDefCol("titre") ;
				$bloc->InsereDefCol("chemin_miniature") ;
				$bloc->InsereDefCol("chemin_miniature_publ") ;
				$bloc->InsereDefCol("date_publication") ;
				$bloc->InsereDefCol("heure_publication") ;
				$bd = $this->ObtientBDSupport() ;
				$entiteImg = & $this->ModuleParent->EntiteImagePhototheque ;
				$sql = 'select t11.*, t21.'.$bd->EscapeVariableName($entiteImg->NomColCheminImage).', t21.'.$bd->EscapeVariableName($entiteImg->NomColCheminMiniature).', '.$syst->SqlCheminPubl('t21.'.$bd->EscapeVariableName($entiteImg->NomColCheminMiniature)).' chemin_miniature_publ from (
select t1.'.$bd->EscapeVariableName($this->NomColId).', t1.'.$bd->EscapeVariableName($this->NomColTitre).', t1.'.$bd->EscapeVariableName($this->NomColStatutPubl).', t1.'.$bd->EscapeVariableName($this->NomColDatePubl).', t1.'.$bd->EscapeVariableName($this->NomColHeurePubl).', max('.$bd->SqlConcat(array('t2.'.$bd->EscapeVariableName($entiteImg->NomColDatePubl), '\' \'', 't2.'.$bd->EscapeVariableName($entiteImg->NomColHeurePubl))).') temps_publ_max_photo, max(t2.'.$bd->EscapeVariableName($entiteImg->NomColId).') id_max_photo from '.$bd->EscapeTableName($this->NomTable).' t1 inner join '.$bd->EscapeTableName($entiteImg->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColId).' = t2.'.$bd->EscapeVariableName($entiteImg->NomColIdPhototheque).' group by t1.'.$bd->EscapeVariableName($this->NomColId).', t1.'.$bd->EscapeVariableName($this->NomColTitre).'
) t11
inner join '.$bd->EscapeTableName($entiteImg->NomTable).' t21
on t11.'.$bd->EscapeVariableName($this->NomColId).' = t21.'.$bd->EscapeVariableName($entiteImg->NomColIdPhototheque).'
where t11.id_max_photo = t21.'.$bd->EscapeVariableName($entiteImg->NomColId).'' ;
				$bloc->FournisseurDonnees->RequeteSelection = '('.$sql.')' ;
				$bloc->ContenuLigneModele = '<div align="center"><a href="?'.urlencode($zone->NomParamScriptAppele).'='.urlencode($this->NomScriptConsult.'_'.$this->NomElementModule).'&id=${id}"><img src="${chemin_miniature_publ}" border="0" /></a></div>
<div align="center"><a href="?'.urlencode($zone->NomParamScriptAppele).'='.urlencode($this->NomScriptConsult.'_'.$this->NomElementModule).'&id=${id}">${titre}</a></div>' ;
 			}
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColDescription).' description'	;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Titre
				$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
				$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
				$comp = $this->FltFrmElemTitre->ObtientComposant() ;
				$comp->Largeur = '300px' ;
				// Description
				$this->FltFrmElemDescription = $frm->InsereFltEditHttpPost($this->NomParamDescription, $this->NomColDescription) ;
				$this->FltFrmElemDescription->Libelle = $this->LibDescription ;
				$comp = $this->FltFrmElemDescription->DeclareComposant("PvZoneMultiligneHtml") ;
				$comp->TotalColonnes = 80 ;
				$comp->TotalLignes = 6 ;
			}
			protected function ChargeTblList(& $tabl)
			{
				parent::ChargeTblList($tabl) ;
				$this->DefColTblListTitre = $tabl->InsereDefCol($this->NomColTitre, $this->LibTitre) ;
			}
		}
		
		class CmdSupprPhotothequeSws extends CmdSupprEntiteSws
		{
			public function ExecuteInstructions()
			{
				parent::ExecuteInstructions() ;
				if($this->StatutExecution == 1)
				{
					$bd = $this->ScriptParent->ObtientBDSupport() ;
					$entite = $this->ScriptParent->ObtientEntitePage() ;
					$entiteImg = & $entite->ModuleParent->EntiteImagePhototheque ;
					$sql = "delete from ".$bd->EscapeTableName($entiteImg->NomTable)." where ".$bd->EscapeVariableName($entiteImg->NomColIdPhototheque)." = :idPhototheque" ;
					$idPhototheque = $entite->FltFrmElemId->Lie() ;
					$ok = $bd->RunSql($sql, array("idPhototheque" => $idPhototheque)) ;
				}
			}
		}
		
	}
	
?>