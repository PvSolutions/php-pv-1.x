<?php
	
	if(! defined('PV_FORMULAIRE_DONNEES_IONIC'))
	{
		define('PV_FORMULAIRE_DONNEES_IONIC', 1) ;
		
		class PvResultCalculFormDonneesIonic
		{
			public $totalElements = -1 ;
			public $elementEnCours ;
		}
		
		class PvFormulaireDonneesIonic extends PvComposantDonneesBaseIonic
		{
			public $TypeComposant = "FormulaireDonneesIonic" ;
			public $Largeur = 0 ;
			public $Editable = 1 ;
			public $InclureElementEnCours = 1 ;
			public $InclureTotalElements = 1 ;
			public $RequeteSelection = "" ;
			public $FiltresSelectionObligatoires = 1 ;
			public $FiltresGlobauxSelection = array() ;
			public $FiltresLigneSelection = array() ;
			public $FiltresEdition = array() ;
			public $Commandes = array() ;
			public $ElementsEnCours = array() ;
			public $ElementEnCours = array() ;
			public $ElementEnCoursTrouve = 0 ;
			public $CommandeSelectionnee = null ;
			public $AnnulerCommandeSelectionnee = 0 ;
			public $Titre = "" ;
			public $AlignTitre = "left" ;
			public $NomClasseCSS = "" ;
			public $NomClasseCSSTitre = "titre" ;
			public $NomClasseCSSDescription = "description" ;
			public $Description = "" ;
			public $InscrireCommandeExecuter = 1 ;
			public $InscrireCommandeAnnuler = 1 ;
			public $NomClasseCommandeExecuter = "PvCommandeExecuterIonic" ;
			public $NomClasseCommandeAnnuler = "PvCommandeAnnulerIonic" ;
			public $NomCommandeExecuter = "executer" ;
			public $NomCommandeAnnuler = "annuler" ;
			public $LibelleCommandeExecuter = "Executer" ;
			public $LibelleCommandeAnnuler = "Annuler" ;
			public $CommandeAnnuler = null ;
			public $CommandeExecuter = null ;
			public $DessinateurFiltresEdition = null ;
			public $DessinateurBlocCommandes = null ;
			public $CacherBlocCommandes = 0 ;
			public $AnnulerLiaisonParametre = 0 ;
			public $DispositionComposants = array(4, 3, 1, 2) ;
			public $MessageResultatCalculElements = "" ;
			public $MessageAucunElement = "Aucun &eacute;l&eacute;ment trouv&eacute;" ;
			public $CacherFormulaireFiltres = 0 ;
			public $CacherFormulaireFiltresApresCmd = 0 ;
			public $MaxFiltresEditionParLigne = 0 ;
			public $InclureRenduLibelleFiltresEdition = 1 ;
			public $CommandeSelectionneeExec = 0 ;
			public $MsgExecSuccesCommandeExecuter = "" ;
			public $MsgExecEchecCommandeExecuter = "" ;
			public $ActCmdsCommandeExecuter = array() ;
			public $CriteresCommandeExecuter = array() ;
			public $MsgExecSuccesCommandeAnnuler = "" ;
			public $MsgExecEchecCommandeAnnuler = "" ;
			public $ActCmdsCommandeAnnuler = array() ;
			public $CriteresCommandeAnnuler = array() ;
			public $PopupMessageExecution = 0 ;
			public $CacherMessageExecution = 0 ;
			public $ElementsEnCoursEditables = 0 ;
			public $TotalElementsEditables = 1 ;
			public $ActCmdTailleImage ;
			public $TagRacine ;
			public $MtdCalculeElemsRendu ;
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->Obligatoire = 1 ;
				$flt->NomGroupeFiltre = "filtresGlobauxSelect" ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectTs($nom, $corpsBrut, $exprDonnees='')
			{
				$flt = $this->CreeFiltreTs($nom, $corpsBrut) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->Obligatoire = 1 ;
				$flt->NomGroupeFiltre = "filtresGlobauxSelect" ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->Obligatoire = 1 ;
				$flt->NomGroupeFiltre = "filtresGlobauxSelect" ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectFixe($nom, $valeur, $exprDonnees='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->Obligatoire = 1 ;
				$flt->NomGroupeFiltre = "filtresLigneSelect" ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectTs($nom, $corpsBrut, $exprDonnees='')
			{
				$flt = $this->CreeFiltreTs($nom, $corpsBrut) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->Obligatoire = 1 ;
				$flt->NomGroupeFiltre = "filtresLigneSelect" ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->NomGroupeFiltre = "filtresLigneSelect" ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditFixe($nom, $valeur, $colLiee='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->DefinitColLiee($colLiee) ;
				$flt->NomGroupeFiltre = "filtresEdition" ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditTs($nom, $corpsBrut, $colLiee='')
			{
				$flt = $this->CreeFiltreTs($nom, $corpsBrut) ;
				$flt->DefinitColLiee($colLiee) ;
				$flt->NomGroupeFiltre = "filtresEdition" ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpRequest($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$flt->NomGroupeFiltre = "filtresEdition" ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			protected function DetecteParametresLocalisation()
			{
			}
			public function PrepareDeploiement()
			{
				parent::PrepareDeploiement() ;
				$this->DetecteParametresLocalisation() ;
				$this->CalculeElementsRendu() ;
				$this->PrepareRenduPourCmds() ;
				$this->PrepareLiaisonParametre() ;
			}
			public function ReinitParametres()
			{
				foreach($this->FiltresEdition as $i => & $filtre)
				{
					$filtre->DejaLie = 0 ;
				}
			}
			public function AnnuleLiaisonParametres()
			{
				foreach($this->FiltresEdition as $i => & $filtre)
				{
					$filtre->DejaLie = 0 ;
					$filtre->NePasLierParametre = 1 ;
				}
			}
			protected function PrepareLiaisonParametre()
			{
				if($this->AnnulerLiaisonParametre)
				{
					$this->AnnuleLiaisonParametres() ;
				}
			}
			protected function PrepareRenduPourCmds()
			{
				$nomCmds = array_keys($this->Commandes) ;
				foreach($nomCmds as $i => $nomCmd)
				{
					$nomCritrs = array_keys($this->Commandes[$nomCmd]->Criteres) ;
					$this->Commandes[$nomCmd]->PrepareRendu($this) ;
					foreach($nomCritrs as $j => $nomCritr)
					{
						$critr = & $this->Commandes[$nomCmd]->Criteres[$nomCritr] ;
						$critr->PrepareRendu($this) ;
					}
				}
			}
			protected function CalculeTotalElements()
			{
				$this->TotalElements = $this->FournisseurDonnees->CompteElements(array(), $this->FiltresGlobauxSelection) ;
				if($this->FournisseurDonnees->ExceptionTrouvee())
				{
					$this->MessageExecution = $this->FournisseurDonnees->DerniereException->Message ;
				}
				// $this->AfficheExceptionFournisseurDonnees() ;
			}
			protected function CalculeElementsEnCours()
			{
				$filtresSelection = $this->FiltresGlobauxSelection ;
				array_splice($filtresSelection, count($filtresSelection), 0, $this->FiltresLigneSelection) ;
				$this->ElementsEnCours = $this->FournisseurDonnees->SelectElements($this->ExtraitColonnesDonnees($this->FiltresEdition), $filtresSelection) ;
				if($this->FournisseurDonnees->ExceptionTrouvee())
				{
					$this->MessageExecution = $this->FournisseurDonnees->DerniereException->Message ;
				}
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				// $this->ElementsEnCours = $this->FournisseurDonnees->SelectElements($this->ExtraitColonnesDonnees($filtresSelection), $filtresSelection) ;
				// $this->AfficheExceptionFournisseurDonnees() ;
				// print_r($this->ElementsEnCours) ;
			}
			protected function ExtraitColonnesDonnees(& $filtres)
			{
				$cols = array() ;
				foreach($filtres as $i => & $filtre)
				{
					if($filtre->NePasLireColonne == 1)
					{
						continue ;
					}
					$cols[$i] = new PvDefinitionColonneDonnees() ;
					$cols[$i]->NomDonnees = $filtre->NomColonneLiee ;
					$cols[$i]->AliasDonnees = $filtre->AliasParametreDonnees ;
				}
				// print_r($cols) ;
				return $cols ;
			}
			public function DoitInclureElement()
			{
				return $this->InclureTotalElements && $this->InclureElementEnCours ;
			}
			public function CalculeElementsRendu()
			{
				$this->ElementsEnCours = array() ;
				$this->ElementEnCours = array() ;
				$this->ElementEnCoursTrouve = 0 ;
				$this->TotalElements = 0 ;
				if($this->InclureTotalElements)
				{
					$this->CalculeTotalElements() ;
				}
				if($this->InclureElementEnCours)
				{
					$this->CalculeElementsEnCours() ;
					// echo "Err : ".$this->FournisseurDonnees->BaseDonnees->ConnectionException ;
					// print_r($this->FournisseurDonnees->BaseDonnees) ;
					// file_put_contents("kkk.txt", print_r($this->FournisseurDonnees, true)) ;
					// print_r($this->ElementsEnCours) ;
					if(count($this->ElementsEnCours) > 0)
					{
						$this->ElementEnCours = $this->ElementsEnCours[0] ;
						$this->AssigneValeursFiltresEdition() ;
						$this->ElementEnCoursTrouve = 1 ;
					}
				}
				else
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
			}
			protected function AssigneValeursFiltresEdition()
			{
				$nomFiltres = array_keys($this->FiltresEdition) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FiltresEdition[$nomFiltre] ;
					if(isset($this->ElementEnCours[$filtre->NomParametreDonnees]))
					{
						$filtre->DejaLie = 0 ;
						$filtre->ValeurParDefaut = $this->ElementEnCours[$filtre->NomParametreDonnees] ;
					}
				}
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeFiltresSelection() ;
				$this->ChargeFiltresEdition() ;
				$this->ChargeCommandeAnnuler() ;
				$this->ChargeCommandeExecuter() ;
				$this->ChargeConfigAuto() ;
			}
			protected function ChargeConfigAuto()
			{
			}
			protected function ChargeFiltresSelection()
			{
			}
			protected function ChargeFiltresEdition()
			{
			}
			public function ParametresRendu()
			{
				$parametres = array() ;
				$nomFiltres = array_keys($this->FiltresSelection) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FiltresSelection[$nomFiltre] ;
					if($filtre->TypeLiaisonParametre == "get")
					{
						$valeur = $filtre->Lie() ;
						if($filtre->NePasInclure())
							continue ;
						$parametres[$filtre->NomParametreLie] = $valeur ;
					}
				}
				return $parametres ;
			}
			public function PossedeCommandeSelectionnee()
			{
				return ($this->ValeurParamIdCommande != '') ? 1 : 0 ;
			}
			public function SuccesCommandeSelectionnee()
			{
				return $this->PossedeCommandeSelectionnee() && $this->CommandeSelectionnee->EstSucces() ;
			}
			protected function DetecteCommandeSelectionnee()
			{
				if($this->CacherBlocCommandes)
				{
					return ;
				}
				$nomParam = $this->IDInstanceCalc."_".$this->NomParamIdCommande ;
				$this->ValeurParamIdCommande = (isset($_POST[$nomParam])) ? $_POST[$nomParam] : "" ;
				if(! in_array($this->ValeurParamIdCommande, array_keys($this->Commandes)))
				{
					$this->ValeurParamIdCommande = "" ;
				}
			}
			protected function MAJConfigFiltresSelection()
			{
				if($this->FiltresSelectionObligatoires == 0)
					return ;
				$this->FixeFiltresSelectionObligatoires($this->FiltresGlobauxSelection) ;
				$this->FixeFiltresSelectionObligatoires($this->FiltresLigneSelection) ;
			}
			protected function FixeFiltresSelectionObligatoires(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtres[$nomFiltre]->Obligatoire = 1 ;
				}
			}
			protected function ExecuteCommandeSelectionnee()
			{
				$this->DetecteCommandeSelectionnee() ;
				$this->CommandeSelectionnee = null ;
				$this->CommandeSelectionneeExec = 0 ;
				if($this->ValeurParamIdCommande != "" && isset($this->Commandes[$this->ValeurParamIdCommande]))
				{
					$this->CommandeSelectionnee = & $this->Commandes[$this->ValeurParamIdCommande] ;
				}
				if(! $this->EstNul($this->CommandeSelectionnee))
				{
					$this->AnnulerCommandeSelectionnee = 0 ;
					$this->ValideCommandeSelectionnee() ;
					if($this->AnnulerCommandeSelectionnee == 0)
					{
						$this->CommandeSelectionnee->Execute() ;
						$this->CommandeSelectionneeExec = 1 ;
						if($this->CacherFormulaireFiltresApresCmd == 1)
						{
							$this->CacherFormulaireFiltres = 1 ;
						}
					}
				}
			}
			protected function ValideCommandeSelectionnee()
			{
			}
			public function ObtientFiltresSelection()
			{
				$filtres = $this->FiltresGlobauxSelection ;
				if(count($this->FiltresLigneSelection) > 0)
				{
					array_splice($filtres, count($filtres), 0, $this->FiltresLigneSelection) ;
				}
				return $filtres ;
			}
			public function LieFiltresEdition()
			{
				$this->LieFiltres($this->FiltresEdition) ;
			}
			public function LieFiltresSelection()
			{
				$this->LieFiltres($this->FiltresGlobauxSelection) ;
				$this->LieFiltres($this->FiltresLigneSelection) ;
			}
			public function LieTousLesFiltres()
			{
				$this->LieFiltresSelection() ;
				$this->LieFiltresEdition() ;
			}
			public function NeLiePasParamFiltresEdition()
			{
				$nomFiltres = array_keys($this->FiltresEdition) ;
				foreach($nomFiltres as $i => $nom)
				{
					$this->FiltresEdition[$nom]->NePasLierParametre = 1 ;
				}
			}
			public function NeLiePasParamFltsEdit()
			{
				$this->NeLiePasParamFiltresEdition() ;
			}
			public function FigeFltsEdit()
			{
				$this->FigeFiltresEdition() ;
			}
			public function FigeFiltresEdition()
			{
				$nomFiltres = array_keys($this->FiltresEdition) ;
				foreach($nomFiltres as $i => $nom)
				{
					$this->FiltresEdition[$nom]->LectureSeule = 1 ;
				}
			}
			public function CacheFltsEdit()
			{
				$this->CacheFiltresEdition() ;
			}
			public function CacheFiltresEdition()
			{
				$nomFiltres = array_keys($this->FiltresEdition) ;
				foreach($nomFiltres as $i => $nom)
				{
					$this->FiltresEdition[$nom]->Visible = 1 ;
				}
			}
			protected function CreeMtdCalculElementsRendu()
			{
				return new PvMtdCalculFormDonneesIonic() ;
			}
			public function FournitMethodesDistantes()
			{
				$this->InsereMethodeDistante("calculeElementsRendu", $this->CreeMtdCalculElementsRendu()) ;
				foreach($this->Commandes as $nom => $commande)
				{
					$commande->FournitMethodesDistantes() ;
				}
				foreach($this->FiltresEdition as $nom => $filtre)
				{
					$filtre->FournitMethodesDistantes() ;
				}
			}
			public function RemplaceCommandeAnnuler($nomClasse)
			{
				$this->NomClasseCommandeAnnuler = $nomClasse ;
				if($this->EstNul($this->CommandeAnnuler))
				{
					return ;
				}
				$this->ChargeCommandeAnnuler() ;
			}
			public function RemplaceCommandeExecuter($nomClasse)
			{
				$this->NomClasseCommandeExecuter = $nomClasse ;
				if($this->EstNul($this->CommandeExecuter))
				{
					return ;
				}
				$this->ChargeCommandeExecuter() ;
			}
			public function & InsereCommande($nomCommande, $commande)
			{
				$this->InscritCommande($nomCommande, $commande) ;
				return $commande ;
			}
			public function InscritCommande($nomCommande, & $commande)
			{
				$this->Commandes[$nomCommande] = & $commande ;
				$commande->AdopteFormulaireDonnees($nomCommande, $this) ;
			}
			public function & DeclareCommande($nomCommande, $nomClasseCommande, $libelleCommande="")
			{
				if(! class_exists($nomClasseCommande))
				{
					die("Impossible de creer une commande a partir de la classe ".$nomClasseCommande." inexistante") ;
				}
				$commande = new $nomClasseCommande() ;
				$commande->Libelle = $libelleCommande ;
				$commande->ChargeConfig() ;
				$commande->AdopteFormulaireDonnees($nomCommande, $this) ;
				$this->Commandes[$nomCommande] = & $commande ;
				return $commande ;
			}
			protected function ChargeCommandeExecuter()
			{
				if(! $this->InscrireCommandeExecuter)
				{
					return 0 ;
				}
				$this->CommandeExecuter = $this->DeclareCommande($this->NomCommandeExecuter, $this->NomClasseCommandeExecuter, $this->LibelleCommandeExecuter) ;
				if($this->EstNul($this->CommandeExecuter))
				{
					return 0 ;
				}
				if($this->MsgExecEchecCommandeExecuter != '')
				{
					$this->CommandeExecuter->MessageEchecExecution = $this->MsgExecEchecCommandeExecuter ;
				}
				if($this->MsgExecSuccesCommandeExecuter != '')
				{
					$this->CommandeExecuter->MessageSuccesExecution = $this->MsgExecSuccesCommandeExecuter ;
				}
				if(count($this->ActCmdsCommandeExecuter) > 0)
				{
					foreach($this->ActCmdsCommandeExecuter as $i => $actCmd)
					{
						$this->CommandeExecuter->InscritNouvActCmd($this->ActCmdsCommandeExecuter[$i]) ;
					}
				}
				$this->ActCmdTailleImage = $this->CommandeExecuter->InsereNouvActCmd(new PvActCmdTailleImageGd()) ;
				if(count($this->CriteresCommandeExecuter) > 0)
				{
					foreach($this->CriteresCommandeExecuter as $i => $actCmd)
					{
						$this->CommandeExecuter->InscritNouvCritere($this->CriteresCommandeExecuter[$i]) ;
					}
				}
				return 1 ;
			}
			protected function ChargeCommandeAnnuler()
			{
				if(! $this->InscrireCommandeAnnuler)
				{
					return 0 ;
				}
				$this->CommandeAnnuler = $this->DeclareCommande($this->NomCommandeAnnuler, $this->NomClasseCommandeAnnuler, $this->LibelleCommandeAnnuler) ;
				if($this->MsgExecEchecCommandeAnnuler != '')
				{
					$this->CommandeAnnuler->MessageEchecExecution = $this->MsgExecEchecCommandeAnnuler ;
				}
				if($this->MsgExecSuccesCommandeAnnuler != '')
				{
					$this->CommandeAnnuler->MessageSuccesExecution = $this->MsgExecSuccesCommandeAnnuler ;
				}
				if(count($this->ActCmdsCommandeAnnuler) > 0)
				{
					foreach($this->ActCmdsCommandeAnnuler as $i => $actCmd)
					{
						$this->CommandeAnnuler->InscritNouvActCmd($this->ActCmdsCommandeAnnuler[$i]) ;
					}
				}
				if(count($this->CriteresCommandeAnnuler) > 0)
				{
					foreach($this->CriteresCommandeAnnuler as $i => $actCmd)
					{
						$this->CommandeAnnuler->InscritNouvCritere($this->CriteresCommandeAnnuler[$i]) ;
					}
				}
				return 1 ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinBlocCommandes1Ionic() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltres1Ionic() ;
				$this->DessinateurFiltresEdition->InclureRenduLibelle = $this->InclureRenduLibelleFiltresEdition ;
			}
			public function Deploie()
			{
				$this->MAJConfigFiltresSelection() ;
				// $this->ExecuteCommandeSelectionnee() ;
				// $this->PrepareRendu() ;
				$this->DeploieContenuHtml() ;
				$this->DeploieContenuTs() ;
			}
			protected function DeploieContenuHtml()
			{
				$tagContent = & $this->PageSrcParent->TagContent ;
				$this->TagRacine = $tagContent->InsereTagFils(new PvRenduHtmlIonic()) ;
				$this->TagRacine->Contenu .= '<div'.$this->AttrsHtmlNg().'>'.PHP_EOL ;
				// $this->TagRacine->Contenu .= '<form>'.PHP_EOL ;
				$this->TagRacine->Contenu .= $this->ContenuAvantRendu ;
				$this->TagRacine->Contenu .= $this->RenduComposants() ;
				$this->TagRacine->Contenu .= $this->ContenuApresRendu ;
				$this->TagRacine->Contenu .= '</div>' ;
				// $this->TagRacine->Contenu .= '</form>' ;
			}
			protected function DeploieContenuTs()
			{
				$classeTs = & $this->PageSrcParent->ClasseTs ;
				$this->DeploieContenuFiltresTs($this->FiltresGlobauxSelection, $classeTs) ;
				$this->DeploieContenuFiltresTs($this->FiltresLigneSelection, $classeTs) ;
				$this->DeploieContenuFiltresTs($this->FiltresEdition, $classeTs) ;
				$this->DeploieContenuCommandesTs($this->Commandes, $classeTs) ;
				$this->MtdCalculeElemsRendu = $classeTs->InsereMethode("calcule".$this->IDInstanceCalc) ;
				if($this->InclureElementEnCours == 1)
				{
					$this->MtdCalculeElemsRendu->CorpsBrut = 'let _self = this ;
'.$this->AppelTsMtdDist(
						$this->NomMethodeDistante("calculeElementsRendu"),
						$this->ArgsTsFiltresDistants(),
						'function(result) {
'.$this->CorpsTsReceptionDistant().''.$this->CorpsTsCalculeFiltresEdit().'}',
						'function (error) {
this.afficheMsg("Erreur - Chargement formulaire", (error.toString())) ;
}').' ;' ;
				}
				else
				{
					$this->MtdCalculeElemsRendu->CorpsBrut .= $this->CorpsTsCalculeFiltresEdit() ;
				}
				$this->PageSrcParent->ContenuTsAccesAutorise .= "_self.calcule".$this->IDInstanceCalc.'() ;'.PHP_EOL ;
			}
			protected function CorpsTsCalculeFiltresEdit()
			{
				$ctn = '' ;
				foreach($this->FiltresEdition as $j => $filtre)
				{
					if($filtre->RenduPossible() == 1 && $filtre->PeutCalculerElemsRendu())
					{
						$comp = $filtre->ObtientComposant() ;
						$ctn .= 'this.calculeResultat'.$comp->IDInstanceCalc.'() ;'.PHP_EOL ;
					}
				}
				return $ctn ;
			}
			protected function CorpsTsReceptionDistant()
			{
				$ctn = '' ;
				$ctn .= 'if(result.valeur !== null) {'.PHP_EOL ;
				foreach($this->FiltresEdition as $n => $flt)
				{
					if($flt->TypeLiaisonParametre == "request" && $flt->Invisible == 0)
					{
						// $ctn .= 'console.log(_self.'.$flt->IDInstanceCalc.') ;'.PHP_EOL ;
						$ctn .= '_self.'.$flt->IDInstanceCalc.' = result.valeur.elementEnCours.'.$flt->NomParametreLie.' ;'.PHP_EOL ;
					}
				}
				$ctn .= '}'.PHP_EOL ;
				$ctn .= 'else {'.PHP_EOL ;
				$ctn .= '_self.afficheMsg("Erreur - Chargement formulaire", result.erreur.message) ;'.PHP_EOL ;
				$ctn .= '}' ;
				return $ctn ;
			}
			public function PrepareRendu()
			{
				parent::PrepareRendu() ;
				$this->DetecteParametresLocalisation() ;
				$this->CalculeElementsRendu() ;
				$this->PrepareRenduPourCmds() ;
				$this->PrepareLiaisonParametre() ;
			}
			public function RenduComposants()
			{
				$ctn = '' ;
				if(count($this->DispositionComposants) > 0)
				{
					foreach($this->DispositionComposants as $i => $id)
					{
						if($i > 0)
						{
							$ctn .= PHP_EOL ;
						}
						switch($id)
						{
							case PvDispositionFormulaireDonnees::BlocEntete :
							{
								$ctn .= $this->RenduBlocEntete() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::FormulaireFiltresEdition :
							{
								$ctn .= $this->RenduFormulaireFiltres() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::ResultatCommandeExecutee :
							{
								$ctn .= $this->RenduResultatCommandeExecutee() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::BlocCommandes :
							{
								$ctn .= $this->RenduBlocCommandes() ;
							}
							break ;
							default :
							{
								$ctn .= $this->RenduAutreComposantSupport($id) ;
							}
							break ;
						}
					}
				}
				return $ctn ;
			}
			protected function RenduBlocEntete()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduResultatCommandeExecutee()
			{
			}
			protected function RenduFormulaireFiltres()
			{
				$ctn = "" ;
				if(! $this->CacherFormulaireFiltres)
				{
					if($this->EstNul($this->DessinateurFiltresEdition))
					{
						$this->InitDessinateurFiltresEdition() ;
					}
					if($this->EstNul($this->DessinateurFiltresEdition))
					{
					}
					$ctn .= $this->RenduFormulaireFiltreElemEnCours() ;
					return $ctn ;
				}
			}
			protected function RenduFormulaireFiltreElemEnCours()
			{
				return $this->DessinateurFiltresEdition->Execute($this->PageSrcParent, $this, $this->FiltresEdition) ;
			}
			protected function RenduBlocCommandes()
			{
				if($this->EstNul($this->DessinateurBlocCommandes))
				{
					$this->InitDessinateurBlocCommandes() ;
				}
				return $this->RenduBlocCommandesSpec() ;
			}
			protected function RenduBlocCommandesSpec()
			{
				return $this->DessinateurBlocCommandes->Execute($this->PageSrcParent, $this, $this->Commandes) ;
			}
			protected function RenduAutreComposantSupport($id)
			{
			}
			public function ArgsTsFltsEdit()
			{
				return $this->ArgsTsFiltresEdition() ;
			}
			public function ArgsTsFiltresEdition()
			{
				return $this->ArgsTsFiltres($this->FiltresEdition) ;
			}
			public function ArgsTsFiltresSelect()
			{
				return $this->ArgsTsFiltres($this->FiltresGlobauxSelection) ;
			}
			public function ArgsTsFiltresLgSelect()
			{
				return $this->ArgsTsFiltres($this->FiltresLigneSelection) ;
			}
			public function ArgsTsFiltresDistants()
			{
				return '{
filtresGlobauxSelect : '.$this->ArgsTsFiltresSelect().',
filtresLigneSelect : '.$this->ArgsTsFiltresLgSelect().',
filtresEdition : '.$this->ArgsTsFiltresEdition().'
}' ;
			}
		}
		
		class PvMtdCalculFormDonneesIonic extends PvMethodeDistanteNoyauIonic
		{
			protected function ExecuteInstructions()
			{
				$form = & $this->ComposantIUParent ;
				$form->CalculeElementsRendu() ;
				if($form->ElementEnCoursTrouve == 1)
				{
					$result = new PvResultCalculFormDonneesIonic() ;
					$result->totalElements = $form->TotalElements ;
					$result->elementEnCours = $form->ElementEnCours ;
					$this->ConfirmeSucces($result) ;
				}
				else
				{
					$this->RenseigneErreur(1, $form->MessageAucunElement) ;
				}
			}
		}
		
		class PvCommandeAnnulerIonic extends PvCommandeBaseIonic
		{
			public $Libelle = "Annuler" ;
		}
		class PvCommandeExecuterIonic extends PvCommandeBaseIonic
		{
			public $Libelle = "Executer" ;
		}
	}
	
?>