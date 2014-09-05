<?php
	
	if(! defined('MDL_PAGE_COMMENTAIRE_WSM'))
	{
		if(! defined('MDL_PAGE_BASE'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('MDL_PAGE_COMMENTAIRE_WSM', 1) ;
		
		class MdlPageSystCommentsWsm extends LigneModelePageWsm
		{
			public $EstVarGlobal = 1 ;
			public $AffichImpossible = 1 ;
		}
		
		class FormPosterCommentWsm extends PvFormulaireDonneesHtml
		{
			public $InclureElementEnCours = 0 ;
			public $InclureTotalElements = 0 ;
			public $DefinitAutoPremIdSystComment = 1 ;
			public $IdPageSystComment = 0 ;
			public $LibelleFltNotation = "Note" ;
			public $LibelleFltNom = "Nom" ;
			public $LibelleFltEmail = "Email" ;
			public $LibelleFltTitre = "Titre" ;
			public $LibelleFltTexte = "Contenu" ;
			public $FltNotation ;
			public $FltNom ;
			public $FltEmail ;
			public $FltTitre ;
			public $FltTexte ;
			public $ValeurNoteParDefaut = 3 ;
			public $NomClasseCommandeExecuter = "CmdPosterCommentWsm" ;
			public $LibelleCommandeExecuter = "Valider" ;
			public $InscrireCommandeAnnuler = 0 ;
			public $MaxFiltresEditionParLigne = 1 ;
			public function PrepareRendu()
			{
				if(isset($this->ScriptParent->SystemeWsm) && $this->DefinitAutoPremIdSystComment && $this->IdPageSystComment == 0)
				{
					$this->IdPageSystComment = $this->ScriptParent->SystemeWsm->StatsMdlsPageGlobaux["reviews"]->PremIdPage ;
				}
				parent::PrepareRendu() ;
			}
			protected function ChargeFiltresEdition()
			{
				parent::ChargeFiltresEdition() ;
				$this->FltNotation = $this->InsereFltEditHttpPost("review_note") ;
				$this->FltNotation->DeclareComposant("PvEvalEtoilesPur") ;
				$this->FltNotation->ValeurParDefaut = $this->ValeurNoteParDefaut ;
				$this->FltNotation->Libelle = $this->LibelleFltNotation ;
				$this->FltNom = $this->InsereFltEditHttpPost("review_name") ;
				$this->FltNom->Libelle = $this->LibelleFltNom ;
				$this->FltEmail = $this->InsereFltEditHttpPost("review_email") ;
				$this->FltEmail->Libelle = $this->LibelleFltEmail ;
				$this->FltTitre = $this->InsereFltEditHttpPost("review_title") ;
				$this->FltTitre->Libelle = $this->LibelleFltTitre ;
				$this->FltTexte = $this->InsereFltEditHttpPost("review_text") ;
				$this->FltTexte->Libelle = $this->LibelleFltTexte ;
				$comp = $this->FltTexte->DeclareComposant("PvZoneMultiligneHtml") ;
				$comp->TotalLignes = 6 ;
				$comp->TotalColonnes = 60 ;
			}
		}
		class CmdPosterCommentWsm extends PvCommandeExecuterBase
		{
			public $BDActive ;
			public $MessageSuccesExecution = "Votre commentaire a été bien soumis" ;
			public $MsgErrNotationHorsLimite = "La valeur de la note n'est pas valide" ;
			protected function InitBDActive()
			{
				$this->BDActive = & $this->FormulaireDonneesParent->ApplicationParent->BDWsm ;
			}
			protected function EstSystCommentPage($idPage)
			{
				$lignes = $this->BDActive->FetchSqlRows('select * from '.$this->BDActive->Prefixe.'page where id_page='.$this->BDActive->ParamPrefix.' and template_name_page="reviews"') ;
				return (count($lignes) > 0) ? 1 : 0 ;
			}
			protected function ExecuteInstructions()
			{
				$this->InitBDActive() ;
				$note = intval($this->FormulaireDonneesParent->FltNotation->Lie()) ;
				$nom = $this->FormulaireDonneesParent->FltNom->Lie() ;
				$email = $this->FormulaireDonneesParent->FltEmail->Lie() ;
				$titre = $this->FormulaireDonneesParent->FltTitre->Lie() ;
				$texte = $this->FormulaireDonneesParent->FltTexte->Lie() ;
				if($note < $this->FormulaireDonneesParent->FltNotation->Composant->MinUnites || $note > $this->FormulaireDonneesParent->FltNotation->Composant->MaxUnites)
				{
					$this->RenseigneErreur($this->MsgErrNotationHorsLimite) ;
					return ;
				}
				$this->ConfirmeSucces() ;
				// if(! $this->)
			}
		}
		
		class MdlPageCommentaireWsm extends LigneModelePageWsm
		{
			public $EstVarGlobal = 0 ;
		}
	}
	
?>