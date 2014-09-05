<?php
	
	if(! defined('MDL_PAGE_DEFAUT_WSM'))
	{
		if(! defined('MDL_PAGE_BASE'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('MDL_PAGE_DEFAUT_WSM', 1) ;
		
		class ModelePageDefautWsm extends LigneModelePageWsm
		{
			public $AffichAccueil ;
			public $NomAffichAccueil = "accueil" ;
			public $AffichVersionImprim ;
			public $NomAffichVersionImprim = "show_printable_page" ;
			public $AffichFormComment ;
			public $NomAffichFormComment = "show_comment_form" ;
			protected function ChargeAffichages()
			{
				$this->AffichAccueil = $this->InscritNouvAffichage($this->NomAffichAccueil, new AffichMdlPageDefautWsm()) ;
				$this->AffichVersionImprim = $this->InscritNouvAffichage($this->NomAffichFormComment, new AffichMdlFormCommentWsm()) ;
				$this->AffichFormComment = $this->InscritNouvAffichage($this->NomAffichFormComment, new AffichMdlFormCommentWsm()) ;
			}
			protected function CreeRemplFiltresPage()
			{
				return new RemplFiltresPageDefautWsm() ;
			}
		}
		
		class RemplFiltresPageDefautWsm extends RemplisseurFiltresBaseWsm
		{
			public $FltId ;
			public $NomParamId = 'id_page_edite' ;
			public $ExprId = 'id_page_edite' ;
			public $InclureId = 1 ;
			public $LibelleId = "ID" ;
			public $FltTitre ;
			public $NomParamTitre = 'titre' ;
			public $ExprTitre = 'titre' ;
			public $InclureTitre = 1 ;
			public $EditerTitre = 1 ;
			public $LibelleTitre = "Titre" ;
			public $FltIdPageParent ;
			public $NomParamIdPageParent = 'id_page_parent' ;
			public $ExprIdPageParent = 'id_page_parent' ;
			public $InclureIdPageParent = 1 ;
			public $EditerIdPageParent = 1 ;
			public $LibelleIdPageParent = "Page Parent" ;
			public $FltPresentDansMenu ;
			public $NomParamPresentDansMenu = 'presence_menu' ;
			public $ExprPresentDansMenu = 'presence_menu' ;
			public $InclurePresentDansMenu = 1 ;
			public $EditerPresentDansMenu = 1 ;
			public $LibellePresentDansMenu = "Pr&eacute;sent dans les menus" ;
			public $FltStatutPublication ;
			public $NomParamStatutPublication = 'statut' ;
			public $ExprStatutPublication = 'statut' ;
			public $InclureStatutPublication = 1 ;
			public $EditerStatutPublication = 1 ;
			public $LibelleStatutPublication = 'Statut de publication' ;
			public $FltDatePublication ;
			public $NomParamDatePublication = 'date_publication' ;
			public $ExprDatePublication = 'date_publication' ;
			public $InclureDatePublication = 1 ;
			public $EditerDatePublication = 1 ;
			public $LibelleDatePublication = 'Date de publication' ;
			public $FltHeurePublication ;
			public $NomParamHeurePublication = 'heure_publication' ;
			public $ExprHeurePublication = 'heure_publication' ;
			public $InclureHeurePublication = 1 ;
			public $EditerHeurePublication = 1 ;
			public $LibelleHeurePublication = 'Heure de publication' ;
			public $FltTitreCourt ;
			public $NomParamTitreCourt = 'titre_court' ;
			public $ExprTitreCourt = 'titre_court' ;
			public $InclureTitreCourt = 1 ;
			public $EditerTitreCourt = 1 ;
			public $LibelleTitreCourt = 'Titre court' ;
			public $FltNomFichier ;
			public $NomParamNomFichier = 'nom_fichier' ;
			public $ExprNomFichier = 'nom_fichier' ;
			public $InclureNomFichier = 1 ;
			public $EditerNomFichier = 1 ;
			public $LibelleNomFichier = 'Nom de fichier' ;
			public $FltSommaire ;
			public $NomParamSommaire = 'sommaire' ;
			public $ExprSommaire = 'sommaire' ;
			public $InclureSommaire = 1 ;
			public $EditerSommaire = 1 ;
			public $LibelleSommaire = 'Sommaire' ;
			public $FltTexte ;
			public $NomParamTexte = 'texte' ;
			public $ExprTexte = 'texte' ;
			public $InclureTexte = 1 ;
			public $EditerTexte = 1 ;
			public $LibelleTexte = 'Texte' ;
			public $FltMotsCleMeta ;
			public $NomParamMotsCleMeta = 'mots_cle_meta' ;
			public $ExprMotsCleMeta = 'mots_cle_meta' ;
			public $InclureMotsCleMeta = 1 ;
			public $EditerMotsCleMeta = 1 ;
			public $LibelleMotsCleMeta = 'Mots cl&eacute;s Meta' ;
			public $FltDescriptionMeta ;
			public $NomParamDescriptionMeta = 'description_meta' ;
			public $ExprDescriptionMeta = 'description_meta' ;
			public $InclureDescriptionMeta = 1 ;
			public $EditerDescriptionMeta = 1 ;
			public $LibelleDescriptionMeta = 'Description Meta' ;
			public $FltCodeEvalue ;
			public $NomParamCodeEvalue = 'code_evalue' ;
			public $ExprCodeEvalue = 'code_evalue' ;
			public $InclureCodeEvalue = 1 ;
			public $EditerCodeEvalue = 1 ;
			public $LibelleCodeEvalue = 'Code &eacute;valu&eacute;' ;
			public $FltScriptInclusion ;
			public $NomParamScriptInclusion = 'script_inclus' ;
			public $ExprScriptInclusion = 'script_inclus' ;
			public $InclureScriptInclusion = 1 ;
			public $EditerScriptInclusion = 1 ;
			public $LibelleScriptInclusion = 'Script inclus' ;
			public $FltType ;
			public $NomParamType = 'type' ;
			public $ExprType = 'type' ;
			public $InclureType = 0 ;
			public $EditerType = 1 ;
			public $LibelleType = 'Type de page' ;
			public $FltNomLang ;
			public $NomParamNomLang = 'nom_langue' ;
			public $ExprNomLang = 'nom_langue' ;
			public $InclureNomLang = 1 ;
			public $EditerNomLang = 1 ;
			public $LibelleNomLang = 'Nom de la langue' ;
			public $FltExprLang ;
			public $NomParamExprLang = 'expr_langue' ;
			public $ExprExprLang = 'expr_langue' ;
			public $InclureExprLang = 1 ;
			public $EditerExprLang = 1 ;
			public $LibelleExprLang = 'Page traduite' ;
			public $FltNomModele ;
			public $NomParamNomModele = 'nom_modele' ;
			public $ExprNomModele = 'nom_modele' ;
			public $InclureNomModele = 1 ;
			public $EditerNomModele = 1 ;
			public $LibelleNomModele = 'Nom du mod&egrave;le' ;
			public $NomParamListageFilsModele = 'listage_fils_modele' ;
			public $ExprListageFilsModele = 'listage_fils_modele' ;
			public $FltListageFilsModele ;
			public $InclureListageFilsModele = 1 ;
			public $EditerListageFilsModele = 1 ;
			public $LibelleListageFilsModele = 'Mod&ecirc;le pour le listage des sous-pages' ;
			public $FltModeUseModele ;
			public $NomParamModeUseModele = 'mode_usage_modele' ;
			public $ExprModeUseModele = 'mode_usage_modele' ;
			public $InclureModeUseModele = 1 ;
			public $EditerModeUseModele = 1 ;
			public $LibelleModeUseModele = 'Mode d\'utilisation du mod&ecirc;le' ;
			public $FltUrlRedirection ;
			public $NomParamUrlRedirection = 'url_redirect' ;
			public $ExprUrlRedirection = 'url_redirect' ;
			public $InclureUrlRedirection = 1 ;
			public $EditerUrlRedirection = 1 ;
			public $LibelleUrlRedirection = 'URL de redirection' ;
			public $FltImage ;
			public $NomParamImage = 'image' ;
			public $ExprImage = 'image' ;
			public $InclureImage = 1 ;
			public $EditerImage = 1 ;
			public $LibelleImage = 'Image' ;
			public $FltBanniere ;
			public $NomParamBanniere = 'banniere' ;
			public $ExprBanniere = 'banniere' ;
			public $InclureBanniere = 1 ;
			public $EditerBanniere = 1 ;
			public $LibelleBanniere = 'Banni&egrave;re' ;
			public $FltIcone ;
			public $NomParamIcone = 'icone' ;
			public $ExprIcone = 'icone' ;
			public $InclureIcone = 1 ;
			public $EditerIcone = 1 ;
			public $LibelleIcone = 'Icone' ;
			public $FltVideo ;
			public $NomParamVideo = 'video' ;
			public $ExprVideo = 'video' ;
			public $InclureVideo = 1 ;
			public $EditerVideo = 1 ;
			public $LibelleVideo = 'Video' ;
			public $FltDocument ;
			public $NomParamDocument = 'document' ;
			public $ExprDocument = 'document' ;
			public $InclureDocument = 1 ;
			public $EditerDocument = 1 ;
			public $LibelleDocument = "Document" ;
			public $FltSon ;
			public $NomParamSon = 'son' ;
			public $ExprSon = 'son' ;
			public $InclureSon = 1 ;
			public $EditerSon = 1 ;
			public $LibelleSon = "Son" ;
			public $FltFichier ;
			public $NomParamFichier = 'fichier' ;
			public $ExprFichier = 'fichier' ;
			public $InclureFichier = 1 ;
			public $EditerFichier = 1 ;
			public $LibelleFichier = 'Fichier' ;
			public $FltContenuExtra ;
			public $NomParamContenuExtra = 'contenu_extra' ;
			public $ExprContenuExtra = 'contenu_extra' ;
			public $InclureContenuExtra = 1 ;
			public $EditerContenuExtra = 1 ;
			public $LibelleContenuExtra = 'Contenu extra' ;
			protected function InitFltId()
			{
				$this->FltId = $this->FormActuel->InsereFltLgSelectHttpGet($this->NomParamId) ;
				$this->FltId->Invisible = ! $this->InclureId ;
				$this->FltId->EstEtiquette = 1 ;
				$this->FltId->Obligatoire = 1 ;
				$this->FltId->NomParametreDonnees = $this->NomParamId ;
				$this->FltId->ExpressionDonnees = 'id_page = <self>' ;
			}
			protected function InitFltTitre()
			{
				$this->FltTitre = $this->FormActuel->InsereFltEditHttpPost($this->NomParamTitre) ;
				$this->FltTitre->Invisible = ! $this->InclureTitre ;
				$this->FltTitre->EstEtiquette = ! $this->EditerTitre ;
				$this->FltTitre->DefinitColLiee("title_page") ;
			}
			protected function CreeCompTitre()
			{
				$comp = new PvZoneTexteHtml() ;
				return $comp ;
			}
			protected function CreeCompIdPageParent()
			{
				$comp = new PvZoneEtiquetteHtml() ;
				return $comp ;
			}
			protected function CreeCompPresentDansMenu()
			{
				$comp = new PvZoneSelectBoolHtml() ;
				return $comp ;
			}
			/*
			protected function InitFltTitre()
			{
				$this->FltTitre = $this->FormActuel->InsereFltEditHttpPost($this->NomParamTitre) ;
				$this->FltTitre->Invisible = ! $this->InclureTitre ;
				$this->FltTitre->EstEtiquette = ! $this->EditerTitre ;
				$this->FltTitre->RemplaceComposant($this->CreeCompTitre()) ;
				$this->FltTitre->DefinitColLiee("title_page") ;
			}
			*/
			protected function RemplitFormDirect(& $form, & $modele, $mode)
			{
				$this->InitFltId() ;
				$this->InitFltTitre() ;
			}
		}
		
		class FormAjoutPageDefautWsm extends PvFormulaireAjoutDonneesHtml
		{
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				// $this->ScriptParent->
			}
		}
		
		class AffichMdlPageDefautWsm extends AffichMdlPageBaseWsm
		{
			public $FilAriane ;
			public $InclureFilAriane = 1 ;
			public $BarreTitre ;
			public $InclureBarreTitre = 1 ;
			public $BlocLiensSpecExtra ;
			public $InclureBlocLiensSpecExtra = 1 ;
			public $BlocInfosExtra ;
			public $InclureBlocInfosExtra = 1 ;
			public $BarreLiensSpec ;
			public $InclureBarreLiensSpec = 1 ;
			public $BlocTexte ;
			public $InclureBlocTexte = 1 ;
			public $GrilleSousPages ;
			public $CtnLgnMdlGrilleSousPages ;
			public $InclureGrilleSousPages = 1 ;
			protected function ChargeComposants()
			{
				$this->FilAriane = new FilArianePageAffichWsm() ;
				$this->FilAriane->Visible = $this->InclureFilAriane ;
				$this->CompsCorpsDocument[] = & $this->FilAriane ;
				$this->BarreTitre = new BarreTitrePageAffichWsm() ;
				$this->BarreTitre->InclureIcone = 1 ;
				$this->BarreTitre->Visible = $this->InclureBarreTitre ;
				$this->CompsCorpsDocument[] = & $this->BarreTitre ;
				$this->BarreLiensSpec = new BarreLiensSpecPageAffichWsm() ;
				$this->BarreLiensSpec->Visible = $this->InclureBarreLiensSpec ;
				$this->CompsCorpsDocument[] = & $this->BarreLiensSpec ;
				$this->BlocTexte = new BlocTextePageAffichWsm() ;
				$this->CompsCorpsDocument[] = & $this->BlocTexte ;
				$this->GrilleSousPages = new GrilleDonneesSousPagesWsm() ;
				$this->GrilleSousPages->PourIdPageAffich = 1 ;
				$this->GrilleSousPages->Visible = $this->InclureGrilleSousPages ;
				$this->CompsCorpsDocument[] = & $this->GrilleSousPages ;
			}
			protected function PrepareRemplissage(& $script)
			{
				parent::PrepareRemplissage($script) ;
				$this->GrilleSousPages->ContenuLigneModele = '<div><a href="?'.urlencode($script->ZoneParent->NomParamScriptAppele).'='.urlencode($script->ZoneParent->ValeurParamScriptAppele).'&'.urlencode($script->NomParamIdPageAffich).'=${id_page}">${title_page}</a></div>
				<div>${search_text_page_intro}</div>' ;
			}
		}
		class AffichMdlImprimDefautWsm extends AffichMdlPageDefautWsm
		{
			protected function ChargeComposants()
			{
				parent::ChargeComposants() ;
			}
		}
		class AffichMdlFormCommentWsm extends AffichMdlPageDefautWsm
		{
			public $InclureFormComment = 1 ;
			public $FormComment ;
			protected function ChargeComposants()
			{
				parent::ChargeComposants() ;
				$this->FormComment = new FormPosterCommentWsm() ;
				$this->FormComment->Visible = $this->InclureFormComment ;
				$this->CompsCorpsDocument[] = & $this->FormComment ; 
			}
		}
	}
	
?>