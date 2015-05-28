<?php
	
	if(! defined('IMPLEM_COMMENTAIRE_SWS'))
	{
		define("IMPLEM_COMMENTAIRE_SWS", 1) ;
		
		class ImplemCommentaireSws extends ImplemTableSws
		{
			public $NomRef = "commentaire" ;
			public $NomTable = "commentaire" ;
			public $NomColId = "id" ;
			public $NomColDateCreation = "date_creation" ;
			public $NomColStatutPublication = "statut_publication" ;
			public $NomColEmail = "email" ;
			public $NomColNom = "nom" ;
			public $NomColContenu = "contenu" ;
			public $NomColNomElementModule = "nom_entite" ;
			public $NomColIdEntite = "id_entite" ;
			public $NomParamId = "id" ;
			public $NomParamDateCreation = "date_creation" ;
			public $NomParamEmail = "email" ;
			public $NomParamNom = "nom" ;
			public $NomParamContenu = "contenu" ;
			public $NomParamCaptcha = "code_securite" ;
			public $NomParamNomElementModule = "nom_entite" ;
			public $NomParamStatutPublication = "statut_publication" ;
			public $NomParamIdEntite = "id_entite" ;
			public $LibId = "ID" ;
			public $LibDateCreation = "Date cr&eacute;ation" ;
			public $LibStatutPublication = "Statut publication" ;
			public $LibEmail = "Email" ;
			public $LibNom = "Nom" ;
			public $LibContenu = "Contenu" ;
			public $LibNomElementModule = "Nom entite" ;
			public $LibIdEntite = "ID Entite" ;
			public $TotalLignesCompContenu = 5 ;
			public $LargeurCompNom = "200px" ;
			public $LargeurCompEmail = "200px" ;
			public $TotalColonnesCompContenu = 60 ;
			public $AutoPublierCmt = 1 ;
			public $NomClasseCompContenu = "PvZoneMultiligneHtml" ;
			public $FormSoumetCmt ;
			public $FormListeCmt ;
			public $SecuriserEdition = 1 ;
			public $MsgSuccesSoumetCmt = "Votre commentaire a &eacute;t&eacute; pris en compte" ;
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
	}
	
?>