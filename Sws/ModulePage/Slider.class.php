<?php
	
	if(! defined('ENTITE_SLIDER_SWS'))
	{
		define('ENTITE_SLIDER_SWS', 1) ;
		
		class ModuleSliderSws extends ModulePageBaseSws
		{
			public $TitreMenu = "Sliders" ;
			public $NomRef = "slider" ;
			public $EntiteSlider ;
			public $EntiteElemSlider ;
			protected function CreeEntiteElemSlider()
			{
				return new EntiteElemSliderSws() ;
			}
			protected function CreeEntiteSlider()
			{
				return new EntiteSliderSws() ;
			}
			protected function ChargeEntites()
			{
				$this->EntiteSlider = $this->InsereEntite("slider", $this->CreeEntiteSlider()) ;
				$this->EntiteElemSlider = $this->InsereEntite("elem_slider", $this->CreeEntiteElemSlider()) ;
			}
		}
		
		class EntiteElemSliderSws extends EntiteTableSws
		{
			public $AccepterTitre = 1 ;
			public $PresentDansMenu = 1 ;
			public $NomEntite = "elem_slider" ;
			public $NomTable = "elem_slider" ;
			public $TitreMenu = "&Eacute;l&eacute;m. Slide" ;
			public $TitreAjoutEntite = "Ajout &eacute;l&eacute;ment slider" ;
			public $TitreModifEntite = "Modification &eacute;l&eacute;ment slider" ;
			public $TitreSupprEntite = "Suppression &eacute;l&eacute;ment slider" ;
			public $TitreListageEntite = "Liste des &eacute;l&eacute;ments slider" ;
			public $TitreConsultEntite = "D&eacute;tails &eacute;l&eacute;ment slider" ;
			public $LibUrl = "Url" ;
			public $LibDescription = "Description" ;
			public $LibCheminImage = "Image" ;
			public $LibIdSlider = "Slider" ;
			public $NomParamUrl = "url" ;
			public $NomParamTitre = "titre" ;
			public $NomParamDescription = "description" ;
			public $NomParamCheminImage = "chemin_image" ;
			public $NomParamIdSlider = "id_slider" ;
			public $NomColDescription = "description" ;
			public $NomColUrl = "url" ;
			public $NomColCheminImage = "chemin_image" ;
			public $NomColIdSlider = "id_slider" ;
			public $CheminTelechargImage = "images/bannieres" ;
			public $FltFrmElemDescription ;
			public $FltFrmElemUrl ;
			public $FltFrmElemCheminImage ;
			public $FltFrmElemIdSlider ;
			public $FltTblListSlider ;
			public $NomParamTblListSlider = "pIdSlider" ;
			public $DefColTblListSlider ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColUrl).' url' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColDescription).' description' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminImage).' chemin_image' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Url
				$this->FltFrmElemUrl = $frm->InsereFltEditHttpPost($this->NomParamUrl, $this->NomColUrl) ;
				$this->FltFrmElemUrl->Libelle = $this->LibUrl ;
				$comp = $this->FltFrmElemUrl->ObtientComposant() ;
				$comp->Largeur = "320px" ;
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
				// ID Slider
				$this->FltFrmElemIdSlider = $frm->InsereFltEditHttpPost($this->NomParamIdSlider, $this->NomColIdSlider) ;
				$this->FltFrmElemIdSlider->Libelle = $this->LibIdSlider ;
				$comp = $this->FltFrmElemIdSlider->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$comp->FournisseurDonnees = $frm->ScriptParent->ModulePage->CreeFournDonnees() ;
				$comp->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteSlider->NomTable ;
				$comp->NomColonneValeur = $this->ModuleParent->EntiteSlider->NomColId ;
				$comp->NomColonneLibelle = $this->ModuleParent->EntiteSlider->NomColTitre ;
			}
			protected function ReqSelectTblList(& $bd)
			{
				$sql = '(select t1.*, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteSlider->NomColId).' id_slider_parent, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteSlider->NomColTitre).' titre_slider_parent from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($this->ModuleParent->EntiteSlider->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdSlider).'=t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteSlider->NomColId).')' ;
				return $sql ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				$tbl->FournisseurDonnees->RequeteSelection = $this->ReqSelectTblList($bd) ;
				// echo $this->ReqSelectTblList($bd) ;
				$this->DefColTblListSlider = $tbl->InsereDefCol("titre_slider_parent", $this->LibIdSlider) ;
				$this->DefColTblListTitre->Largeur = "20%" ;
				$this->DefColTblListSlider->Largeur = "20%" ;
				$compTitre = $this->FltTblListTitre->ObtientComposant() ;
				$compTitre->Largeur = "200px" ;
				$this->FltTblListSlider = $tbl->InsereFltSelectHttpGet($this->NomParamTblListSlider, $bd->EscapeVariableName($this->NomColIdSlider).' = <self>') ;
				$this->FltTblListSlider->Libelle = $this->LibIdSlider ;
				$compSlider = $this->FltTblListSlider->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$compSlider->FournisseurDonnees = $this->CreeFournDonnees() ;
				$compSlider->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteSlider->NomTable ;
				$fltSlider = $tbl->ScriptParent->CreeFiltreHttpGet($this->NomParamTblListSlider) ;
				$compSlider->FiltresSelection[] = & $fltSlider ;
				$compSlider->InclureElementHorsLigne = 1 ;
				$compSlider->NomColonneValeur = $this->ModuleParent->EntiteSlider->NomColId ;
				$compSlider->NomColonneLibelle = $this->ModuleParent->EntiteSlider->NomColTitre ;
			}
		}
		class EntiteSliderSws extends EntiteTableSws
		{
			public $TitreMenu = "Sliders" ;
			public $TitreAjoutEntite = "Ajout slider" ;
			public $TitreModifEntite = "Modification slider" ;
			public $TitreSupprEntite = "Suppression slider" ;
			public $TitreListageEntite = "Liste des sliders" ;
			public $TitreConsultEntite = "D&eacute;tails slider" ;
			public $NomEntite = "slider" ;
			public $NomTable = "slider" ;
			public $LibTitre = "Titre" ;
			public $NomParamTitre = "titre" ;
			public $NomColTitre = "titre" ;
			public $DefColTblListTitre ;
			public $FltFrmElemTitre ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
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
		}
		
	}
	
?>