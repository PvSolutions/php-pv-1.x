<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_FORMULAIRE'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
		{
			include dirname(__FILE__)."/../FournisseurDonnees.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_CRITERE'))
		{
			include dirname(__FILE__)."/Critere.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_ACT_CMD'))
		{
			include dirname(__FILE__)."/ActCmd.class.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_FORMULAIRE', 1) ;
		
		class PvFormulaireDonneesHtml extends PvComposantIUDonneesSimple
		{
			public $TypeComposant = "FormulaireDonneesHTML" ;
			public $Largeur = 0 ;
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
			public $NomClasseCommandeExecuter = "PvCommandeExecuterBase" ;
			public $NomClasseCommandeAnnuler = "PvCommandeAnnulerBase" ;
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
			public function & InsereFltEditRef($nom, & $filtreRef, $colLiee='', $nomComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditFixe($nom, $valeur, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditCookie($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditSession($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditMembreConnecte($nom, $nomParamLie='', $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpUpload($nom, $cheminDossierDest="", $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpGet($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpPost($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpRequest($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresEdition[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectRef($nom, & $filtreRef, $exprDonnees='', $nomComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectCookie($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectSession($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectMembreConnecte($nom, $nomParamLie='', $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectHttpGet($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectHttpPost($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltLgSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresLigneSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectRef($nom, & $filtreRef, $exprDonnees='', $nomComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectCookie($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectSession($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectMembreConnecte($nom, $nomParamLie='', $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpGet($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpPost($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresGlobauxSelection[] = & $flt ;
				return $flt ;
			}
			protected function LieColDonnees(& $flt, $nomParamDonnees='', $nomColLiee='')
			{
				$flt->NomColonneLiee = $nomColLiee ;
				$flt->NomParametreDonnees = $nomParamDonnees ;
			}
			protected function DetecteParametresLocalisation()
			{
			}
			public function PrepareRendu()
			{
				parent::PrepareRendu() ;
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
				// print_r($this->FournisseurDonnees) ;
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
			public function & InsereTailleFiltreImageRef(& $filtre, $largeurMax=0, $hauteurMax=0, $operation="")
			{
				if($this->InscrireCommandeExecuter == 0 || $this->EstNul($this->CommandeExecuter))
				{
					return false ;
				}
				return $this->ActCmdTailleImage->InsereTailleFiltre($filtre->NomElementScript, $largeurMax, $hauteurMax, $operation);
			}
			public function & InsereTailleFiltreImage($nomFiltre, $largeurMax=0, $hauteurMax=0, $operation="")
			{
				if($this->InscrireCommandeExecuter == 0 || $this->EstNul($this->CommandeExecuter))
				{
					return false ;
				}
				return $this->ActCmdTailleImage->InsereTailleFiltre($nomFiltre, $largeurMax, $hauteurMax, $operation);
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
			protected function RenduDispositifBrut()
			{
				if(! $this->EstBienRefere())
				{
					return $this->RenduMalRefere() ;
				}
				$this->MAJConfigFiltresSelection() ;
				$this->ExecuteCommandeSelectionnee() ;
				$this->PrepareRendu() ;
				$ctn = '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				$ctn .= $this->AppliqueHabillage().PHP_EOL ;
				$ctn .= $this->ContenuAvantRendu ;
				$ctn .= $this->RenduComposants().PHP_EOL ;
				$ctn .= $this->ContenuApresRendu ;
				$ctn .= '</div>' ;
				return $ctn ;
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
			public function LieFiltres(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtres[$nomFiltre]->Lie() ;
				}
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
			protected function RenduComposants()
			{
				$ctn = '' ;
				if(count($this->DispositionComposants))
				{
					$ctn .= '<form class="FormulaireDonnees'.(($this->NomClasseCSS != '') ? ' '.$this->NomClasseCSS : '').'" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this)">'.PHP_EOL ;
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
					$ctn .= '</form>' ;
				}
				return $ctn ;
			}
			protected function RenduAutreComposantSupport($id)
			{
			}
			protected function RenduBlocEntete()
			{
				$ctn = '' ;
				if($this->Titre != '')
				{
					$titre = _parse_pattern($this->Titre, array_map('htmlentities', $this->ElementEnCours)) ;
					$ctn .= '<div align="'.$this->AlignTitre.'" class="'.$this->NomClasseCSSTitre.'">'.$titre.'</div>'.PHP_EOL ;
				}
				if($this->Description != '')
				{
					$desc = _parse_pattern($this->Description, array_map('htmlentities', $this->ElementEnCours)) ;
					$ctn .= '<div class="'.$this->NomClasseCSSDescription.'">'.$desc.'</div>' ;
				}
				return $ctn ;
			}
			protected function RenduFormulaireFiltres()
			{
				$ctn = "" ;
				// echo "Cacher form : ".$this->CacherFormulaireFiltres."<br/>" ;
				if(! $this->CacherFormulaireFiltres)
				{
					if($this->ElementEnCoursTrouve)
					{
						if($this->EstNul($this->DessinateurFiltresEdition))
						{
							$this->InitDessinateurFiltresEdition() ;
						}
						if($this->EstNul($this->DessinateurFiltresEdition))
						{
							return "<p>Le dessinateur de filtres n'est pas défini</p>" ;
						}
						$ctn .= '<div class="FormulaireFiltres">'.PHP_EOL ;
						$ctn .= '<table' ;
						$ctn .= ' cellpadding="2"' ;
						if($this->Largeur != "")
						{
							$ctn .= ' width="'.$this->Largeur.'"' ;
						}
						$ctn .= ' cellspacing="0"' ;
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<tr>'.PHP_EOL ;
						$ctn .= '<td>'.PHP_EOL ;
						$ctn .= $this->RenduFormulaireFiltreElemEnCours() ;
						$ctn .= '</td>'.PHP_EOL ;
						$ctn .= '</tr>'.PHP_EOL ;
						$ctn .= '</table>'.PHP_EOL ;
						$ctn .= '</div>' ;
						$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresEdition).PHP_EOL ;
					}
					else
					{
						if(! $this->EstNul($this->FournisseurDonnees))
						{
							// echo 'Err Sql : '.$this->FournisseurDonnees->BaseDonnees->LastSqlText ;
						}
						// print_r($this->FournisseurDonnees->BaseDonnees->LastSqlParams) ;
						$ctn .= $this->MessageAucunElement ;
					}
				}
				return $ctn ;
			}
			public function ObtientUrlInitiale()
			{
				$filtresSelect = $this->FiltresGlobauxSelection ;
				if(count($this->FiltresLigneSelection) > 0)
				{
					array_splice($filtresSelect, count($filtresSelect), 0, $this->FiltresLigneSelection) ;
				}
				$nomFiltres = array_keys($filtresSelect) ;
				$filtresGets = array($this->ZoneParent->NomParamScriptAppele) ;
				$nomFiltresGets = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					if($filtres[$nomFiltre]->TypeLiaisonParametre == "get")
					{
						$filtresGets[] = $filtres[$nomFiltre]->ObtientIDElementHtmlComposant() ;
						$nomFiltresGets[] = $filtres[$nomFiltre]->NomParametreLie ;
					}
				}
				foreach($this->ParamsGetSoumetFormulaire as $n => $v)
				{
					if(! in_array($v, $filtresGet))
					{
						$filtresGets[] = $v ;
					}
				}
				$params = extract_array_without_keys($_GET, $nomFiltresGets) ;
				$indexMinUrl = (count($params) > 0) ? 0 : 1 ;
				$urlFormulaire = remove_url_params(get_current_url()) ;
				$urlFormulaire .= '?'.http_build_query_string($params) ;
				$instrDesactivs = '' ;
				if($this->ForcerDesactCache)
				{
					$urlFormulaire .= '&'.urlencode($this->NomParamIdAleat()).'='.htmlspecialchars(rand(0, 999999)) ;
				}
				return $urlFormulaire ;
			}
			protected function RenduFormulaireFiltreElemEnCours()
			{
				$ctn = '' ;
				$ctn .= $this->DessinateurFiltresEdition->Execute($this->ScriptParent, $this, $this->FiltresEdition) ;
				return $ctn ;
			}
			protected function RenduBlocCommandes()
			{
				$ctn = '' ;
				if(! $this->CacherBlocCommandes && ! $this->CacherFormulaireFiltres && ! $this->ImpressionEnCours())
				{
					if($this->ElementEnCoursTrouve)
					{
						if($this->EstNul($this->DessinateurBlocCommandes))
						{
							$this->InitDessinateurBlocCommandes() ;
						}
						if($this->EstNul($this->DessinateurBlocCommandes))
						{
							return "<p>Le dessinateur de filtres n'est pas défini</p>" ;
						}
						$ctn .= '<div class="BlocCommandes">'.PHP_EOL ;
						$ctn .= $this->DessinateurBlocCommandes->Execute($this->ScriptParent, $this, $this->Commandes) ;
						$ctn .= $this->DeclarationJsActiveCommande().PHP_EOL ;
						$ctn .= '</div>' ;
					}
				}
				return $ctn ;
			}
			protected function RenduResultatCommandeExecutee()
			{
				$ctn = '' ;
				if($this->EstNul($this->CommandeSelectionnee) || $this->CacherMessageExecution == 1)
				{
					return $ctn ;
				}
				$msgExecution = html_entity_decode($this->CommandeSelectionnee->MessageExecution) ;
				if($this->PopupMessageExecution)
				{
					if(! $this->ZoneParent->InclureJQueryUi)
					{
						$ctn .= '<script language="javascript">'.PHP_EOL ;
						$ctn .= 'alert('.@svc_json_encode($msgExecution).') ;' ;
						$ctn .= '</script>'.PHP_EOL ;
					}
					else
					{
						$ctn .= '<div id="DialogMsg'.$this->IDInstanceCalc.'" class="ui-dialog" align="center">'.htmlentities($msgExecution).''.$this->RenduLiensCommandeExecutee().'</div>' ;
						$ctn .= '<script language="javascript">'.PHP_EOL ;
						$ctn .= 'jQuery(function() {
	jQuery("#DialogMsg'.$this->IDInstanceCalc.'").dialog({
		autoOpen : true,
		resizable : false,
		modal : true
	}) ;
})'.PHP_EOL ;
						$ctn .= '</script>'.PHP_EOL ;
					}
				}
				else
				{
					$ctn .= '<div' ;
					$classeCSS = ($this->CommandeSelectionnee->StatutExecution == 1) ? "Succes" : "Erreur" ;
					$ctn .= ' class="'.$classeCSS.'"' ;
					$ctn .= '>' ;
					$ctn .= htmlentities($msgExecution) ;
					$ctn .= $this->RenduLiensCommandeExecutee() ;
					$ctn .= '</div>' ;
				}
				return $ctn ;
			}
			protected function RenduLiensCommandeExecutee()
			{
				$msgExecution = '' ;
				$liensCmd = $this->CommandeSelectionnee->ObtientLiens() ;
				if(count($liensCmd) > 0)
				{
					foreach($liensCmd as $i => $lienCmd)
					{
						$msgExecution .= ' ' ;
						$msgExecution .= $lienCmd->RenduDispositif($this, $i) ;
					}
				}
				return $msgExecution ;
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
				$this->DessinateurBlocCommandes = new PvDessinateurRenduHtmlCommandes() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinateurRenduHtmlFiltresDonnees() ;
				if($this->MaxFiltresEditionParLigne > 0)
				{
					$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
				}
				$this->DessinateurFiltresEdition->InclureRenduLibelle = $this->InclureRenduLibelleFiltresEdition ;
			}
			public function NotifieParMail($de, $a, $cc='', $cci='')
			{
				if($this->EstNul($this->CommandeExecuter))
				{
					throw new Exception("La commande 'Executer' n'a pas ete initialisee pour les envois de mail") ;
					return ;
				}
				$actCmd = $this->CommandeExecuter->InsereActCmd("PvActCmdFormMail", array()) ;
				$actCmd->A = $a ;
				$actCmd->De = $de ;
				$actCmd->Cc = $cc ;
				$actCmd->Cci = $cci ;
			}
			public function RedirigeCmdAnnulerVersUrl($url)
			{
				return $this->RedirigeAnnulerVersUrl($url) ;
			}
			public function RedirigeCmdAnnulerVersScript($nomScript, $parametres=array())
			{
				return $this->RedirigeAnnulerVersScript($nomScript, $parametres) ;
			}
			public function RedirigeAnnulerVersUrl($url)
			{
				if($this->EstNul($this->CommandeAnnuler))
				{
					throw new Exception("La commande 'Annuler' n'a pas ete initialisee avant d'assigner une redirection") ;
					return ;
				}
				$actCmd = $this->CommandeAnnuler->InsereActCmd("PvActCmdRedirectionHttp", array()) ;
				$actCmd->Url = $url ;
				return $actCmd ;
			}
			public function RedirigeAnnulerVersScript($nomScript, $parametres=array())
			{
				if(! $this->InscrireCommandeAnnuler)
				{
					return ;
				}
				if($this->EstNul($this->CommandeAnnuler))
				{
					throw new Exception("La commande 'Annuler' n'a pas ete initialisee avant d'assigner une redirection") ;
					return ;
				}
				$actCmd = $this->CommandeAnnuler->InsereActCmd("PvActCmdRedirectionHttp", array()) ;
				$actCmd->NomScript = $nomScript ;
				$actCmd->Parametres = $parametres ;
				return $actCmd ;
			}
			public function RedirigeCmdExecuterVersUrl($url)
			{
				return $this->RedirigeExecuterVersUrl($url) ;
			}
			public function RedirigeCmdExecuterVersScript($nomScript, $parametres=array())
			{
				return $this->RedirigeExecuterVersScript($nomScript, $parametres) ;
			}
			public function RedirigeExecuterVersUrl($url)
			{
				if($this->EstNul($this->CommandeExecuter))
				{
					throw new Exception("La commande 'Executer' n'a pas ete initialisee avant d'assigner une redirection") ;
					return ;
				}
				$actCmd = $this->CommandeExecuter->InsereActCmd("PvActCmdRedirectionHttp", array()) ;
				$actCmd->Url = $url ;
				return $actCmd ;
			}
			public function RedirigeExecuterVersScript($nomScript, $parametres=array())
			{
				if(! $this->InscrireCommandeExecuter)
				{
					return ;
				}
				if($this->EstNul($this->CommandeExecuter))
				{
					throw new Exception("La commande 'Executer' n'a pas ete initialisee avant d'assigner une redirection") ;
					return ;
				}
				$actCmd = $this->CommandeExecuter->InsereActCmd("PvActCmdRedirectionHttp", array()) ;
				$actCmd->NomScript = $nomScript ;
				$actCmd->Parametres = $parametres ;
				return $actCmd ;
			}
		}
		
		class PvFormulaireDonneesBootstrap extends PvFormulaireDonneesHtml
		{
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
			protected function RenduComposants()
			{
				$ctn = '' ;
				if(count($this->DispositionComposants))
				{
					$ctn .= '<form class="FormulaireDonnees'.(($this->NomClasseCSS != '') ? ' '.$this->NomClasseCSS : '').'" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this)" role="form">'.PHP_EOL ;
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
					$ctn .= '</form>' ;
				}
				return $ctn ;
			}
		}
		
		class PvFormulaireAjoutDonneesHtml extends PvFormulaireDonneesHtml
		{
			public $NomClasseCommandeExecuter = "PvCommandeAjoutElement" ;
			public $InclureElementEnCours = 0 ;
			public $InclureTotalElements = 0 ;
		}
		class PvFormulaireModifDonneesHtml extends PvFormulaireDonneesHtml
		{
			public $NomClasseCommandeExecuter = "PvCommandeModifElement" ;
			public $InclureElementEnCours = 1 ;
			public $InclureTotalElements = 1 ;
		}
		class PvFormulaireSupprDonneesHtml extends PvFormulaireDonneesHtml
		{
			public $NomClasseCommandeExecuter = "PvCommandeSupprElement" ;
			public $InclureElementEnCours = 1 ;
			public $InclureTotalElements = 1 ;
			public $Editable = 0 ;
		}
		
		class PvCommandeFormulaireDonnees extends PvCommandeComposantIUBase
		{
			public $NecessiteFormulaireDonnees = 1 ;
			public $InscrireLienAnnuler = 0 ;
			public $InscrireLienReprendre = 0 ;
			public $LibelleLienReprendre = "Reprendre" ;
			public $LibelleLienAnnuler = "Annuler" ;
			public $UrlLienAnnuler = "" ;
			protected function VerifiePreRequis()
			{
				$this->VerifieFichiersUpload($this->FormulaireDonneesParent->FiltresEdition) ;
			}
		}
		class PvCommandeAnnulerBase extends PvCommandeFormulaireDonnees
		{
			/* Ne verifie pas le telechargement des fichiers :) */
			protected function VerifiePreRequis()
			{
			}
		}
		class PvCommandeExecuterBase extends PvCommandeFormulaireDonnees
		{
			public $CacherFormulaireFiltresSiSucces = 0 ;
			public function Execute()
			{
				parent::Execute() ;
				if($this->StatutExecution == 1 && $this->CacherFormulaireFiltresSiSucces)
				{
					$this->FormulaireDonneesParent->CacherFormulaireFiltres = 1 ;
					$this->FormulaireDonneesParent->InclureElementEnCours = 0 ;
					$this->FormulaireDonneesParent->InclureTotalElements = 0 ;
				}
			}
		}
		class PvCommandeEditionElementBase extends PvCommandeExecuterBase
		{
			public $Mode = 1 ;
			protected function ExecuteInstructions()
			{
				$this->StatutExecution = 0 ;
				if($this->EstNul($this->FormulaireDonneesParent->FournisseurDonnees))
				{
					$this->RenseigneErreur("La base de donnée du formulaire n'est renseigné.") ;
					return ;
				}
				$succes = 0 ;
				/*
				 * Debogages
				foreach($this->FormulaireDonneesParent->FiltresEdition as $i => & $fltEdit)
				{
					echo $fltEdit->IDInstanceCalc."@".$fltEdit->NomParametreLie." : ".intro($fltEdit->Lie())."<br>" ;
				}
				* */
				switch($this->Mode)
				{
					case PvModeEditionElement::Ajout :
					{
						$succes = $this->FormulaireDonneesParent->FournisseurDonnees->AjoutElement($this->FormulaireDonneesParent->FiltresEdition) ;
					}
					break ;
					case PvModeEditionElement::Modif :
					{
						// print_r($this->FormulaireDonneesParent->FiltresLigneSelection[0]->NomParametreDonnees) ;
						$succes = $this->FormulaireDonneesParent->FournisseurDonnees->ModifElement($this->FormulaireDonneesParent->FiltresLigneSelection, $this->FormulaireDonneesParent->FiltresEdition) ;
					}
					break ;
					case PvModeEditionElement::Suppr :
					{
						$succes = $this->FormulaireDonneesParent->FournisseurDonnees->SupprElement($this->FormulaireDonneesParent->FiltresLigneSelection) ;
					}
					break ;
					default :
					{
						$this->RenseigneErreur("Le mode d'&eacute;dition de la commande est inconnue") ;
					}
					break ;
				}
				// print_r($this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees) ;
				if(count($this->FormulaireDonneesParent->FiltresEdition) == 0)
				{
					$this->RenseigneErreur("Aucun filtre d'edition n'a &eacute;t&eacute; d&eacute;fini") ;
				}
				elseif(! $succes && $this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees->ConnectionException != "")
				{
					/// print_r($this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees) ;
					$this->RenseigneErreur("Erreur SQL : ".$this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees->ConnectionException) ;
					// $this->FormulaireDonneesParent->AfficheExceptionFournisseurDonnees() ;
				}
				else
				{
					$this->StatutExecution = 1 ;
					$this->MessageExecution = $this->MessageSuccesExecution ;
				}
				if($this->Mode == 3 && $this->StatutExecution == 1)
				{
					$this->CacherFormulaireFiltresSiSucces = 1 ;
				}
			}
		}
		class PvCommandeAjoutElement extends PvCommandeEditionElementBase
		{
			public $Mode = 1 ;
		}
		class PvCommandeModifElement extends PvCommandeEditionElementBase
		{
			public $Mode = 2 ;
		}
		class PvCommandeSupprElement extends PvCommandeEditionElementBase
		{
			public $Mode = 3 ;
		}
		class PvCommandeRedirectScriptSession extends PvCommandeAnnulerBase
		{
			public $UrlDefaut = '' ;
			protected function ExecuteInstructions()
			{
				$adr = & $this->ZoneParent->AdrScriptSession ;
				$ctn = '' ;
				if($adr->ChaineGet != '')
				{
					$ctn .= '<!doctype html>
<html>
<head><title>Redirection en cours...</title></head>
<body>
<form style="display:none" id="FormRetour" action="'.htmlspecialchars($adr->ChaineGet).'" method="post">' ;
					foreach($adr->DonneesPost as $nom => $valeur)
					{
						if(is_array($valeur))
						{
							$valeur = join(",", $valeur) ;
						}
						$ctn .= '<input type="hidden" name="'.htmlspecialchars($nom).'" value="'.htmlspecialchars($valeur).'" />' ;
						
					}
					$ctn .= '<input type="submit" value="envoyer" /></form>
<script language="javascript">
	document.getElementById("FormRetour").submit() ;
</script>
</body>
</html>' ;
					echo $ctn ;
					exit ;
				}
				elseif($this->UrlDefaut != '')
				{
					redirect_to($this->UrlDefaut) ;
				}
			}
		}
		
		class PvModeEditionElement
		{
			const Ajout = 1 ;
			const Modif = 2 ;
			const Suppr = 3 ;
		}
		
		class PvDispositionFormulaireDonnees
		{
			const FormulaireFiltresEdition = 1 ;
			const BlocCommandes = 2 ;
			const ResultatCommandeExecutee = 3 ;
			const BlocEntete = 4 ;
		}
		
	}
	
?>