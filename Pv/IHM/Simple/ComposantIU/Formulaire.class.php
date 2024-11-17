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
			public $UtiliserLargeur = 1 ;
			public $IdTagForm = "" ;
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
			public $AnnulerRenduFiltres = 0 ;
			public $ClasseCSSSucces = "Succes" ;
			public $ClasseCSSErreur = "Erreur" ;
			public $Titre = "" ;
			public $AlignTitre = "left" ;
			public $NomClasseCSS = "" ;
			public $ClasseCSSTitre = "titre" ;
			public $ClasseCSSDescription = "description" ;
			public $ClasseCSSDispositif = "" ;
			public $ClasseCSSBlocCommandes ;
			public $ClasseCSSFormulaireFiltres ;
			public $Description = "" ;
			public $MessageException ;
			public $InscrireCommandeExecuter = 1 ;
			public $InscrireCommandeAnnuler = 1 ;
			public $EncoderCaracteresZone = 1 ;
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
			public $AfficherCommandesAucunElement = 0 ;
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
			public $NomScriptExecSuccesCommandeExecuter = "" ;
			public $ParamsScriptExecSuccesCommandeExecuter = array() ;
			public $AlignBlocCommandes ;
			public $ActCmdsCommandeExecuter = array() ;
			public $CriteresCommandeExecuter = array() ;
			public $ClasseCSSCommandeExecuter = "" ;
			public $ClasseBoutonCommandeExecuter = "" ;
			public $MsgExecSuccesCommandeAnnuler = "" ;
			public $MsgExecEchecCommandeAnnuler = "" ;
			public $ClasseCSSCommandeAnnuler = "" ;
			public $ClasseBoutonCommandeAnnuler = "" ;
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
			public function & InsereFltEditCaptcha($nom, $nomCmd="", $nomClsComp="PvZoneCaptcha")
			{
				$flt = $this->InsereFltEditHttpPost($nom) ;
				$comp = $flt->DeclareComposant($nomClsComp) ;
				if($nomClsCmd == '' && $this->InscrireCommandeExecuter == 1)
				{
					$critr = $this->CommandeExecuter->InsereNouvCritere(new PvCritereValideCaptcha()) ;
					$critr->FltCaptchaParent = & $flt ;
				}
				elseif(isset($this->Commandes[$nomCmd]))
				{
					$critr = $this->Commandes[$nomCmd]->InsereNouvCritere(new PvCritereValideCaptcha()) ;
					$critr->FltCaptchaParent = & $flt ;
				}
				return $flt ;
			}
			public function & InsereFltEditRecaptcha($nom, $nomCmd="")
			{
				return $this->InsereFltEditCaptcha($nom, $nomCmd, "PvRecaptcha2") ;
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
				$this->PrepareLiaisonParametres() ;
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
			protected function PrepareLiaisonParametres()
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
					$this->MessageException = $this->FournisseurDonnees->MessageException() ;
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
					if(is_array($this->ElementsEnCours) && count($this->ElementsEnCours) > 0)
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
			public function NomCommandeSoumise()
			{
				if($this->CacherBlocCommandes)
				{
					return '' ;
				}
				$nomParam = $this->IDInstanceCalc."_".$this->NomParamIdCommande ;
				if(isset($_POST[$nomParam]))
				{
					return $_POST[$nomParam] ;
				}
				return '' ;
			}
			public function PossedeCommandeSelectionnee()
			{
				return ($this->ValeurParamIdCommande != '') ? 1 : 0 ;
			}
			public function NomCommandeSelectionnee()
			{
				return $this->ValeurParamIdCommande ;
			}
			public function CommandeExecuterSelectionnee()
			{
				return $this->ValeurParamIdCommande == $this->NomCommandeExecuter ;
			}
			public function CommandeAnnulerSelectionnee()
			{
				return $this->ValeurParamIdCommande == $this->NomCommandeAnnuler ;
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
			public function AppliqueCommandeSelectionnee()
			{
				$this->MAJConfigFiltresSelection() ;
				$this->ExecuteCommandeSelectionnee() ;
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
				$ctn = '<div id="'.$this->IDInstanceCalc.'"'.(($this->ClasseCSSDispositif != '') ? ' class="'.$this->ClasseCSSDispositif.'"' : '').'>'.PHP_EOL ;
				$ctn .= $this->AppliqueHabillage().PHP_EOL ;
				if($this->MessageException == null)
				{
					$ctn .= $this->ContenuAvantRendu ;
					$ctn .= $this->RenduComposants().PHP_EOL ;
					$ctn .= $this->ContenuApresRendu ;
				}
				else
				{
					$ctn .= $this->RenduMessageException() ;
				}
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
					$ctn .= '<form class="FormulaireDonnees'.(($this->NomClasseCSS != '') ? ' '.$this->NomClasseCSS : '').'" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this)" accept-charset="'.$this->ZoneParent->EncodageDocument.'">'.PHP_EOL ;
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
			protected function RenduMessageException()
			{
				return '<div class="'.$this->ClasseCSSErreur.'">'.$this->MessageException.'</div>' ;
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
					$ctn .= '<div align="'.$this->AlignTitre.'" class="'.$this->ClasseCSSTitre.'">'.$titre.'</div>'.PHP_EOL ;
				}
				if($this->Description != '')
				{
					$desc = _parse_pattern($this->Description, array_map('htmlentities', $this->ElementEnCours)) ;
					$ctn .= '<div class="'.$this->ClasseCSSDescription.'">'.$desc.'</div>' ;
				}
				return $ctn ;
			}
			protected function RenduFormulaireFiltres()
			{
				$ctn = "" ;
				// echo "Cacher form : ".$this->CacherFormulaireFiltres."<br/>" ;
				if(! $this->CacherFormulaireFiltres && ! $this->AnnulerRenduFiltres)
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
						if($this->MaxFiltresEditionParLigne > 0)
						{
							$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
						}
						$this->DessinateurFiltresEdition->InclureRenduLibelle = $this->InclureRenduLibelleFiltresEdition ;
						$ctn .= '<div class="FormulaireFiltres">'.PHP_EOL ;
						if($this->UtiliserLargeur == 1)
						{
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
						}
						$ctn .= $this->RenduFormulaireFiltreElemEnCours() ;
						if($this->UtiliserLargeur == 1)
						{
							$ctn .= '</td>'.PHP_EOL ;
							$ctn .= '</tr>'.PHP_EOL ;
							$ctn .= '</table>'.PHP_EOL ;
						}
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
				$filtresGets = array() ;
				if($this->ZoneParent->ActiverRoutes == 0)
				{
					$filtresGets[] = $this->ZoneParent->NomParamScriptAppele ;
				}
				$nomFiltresGets = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					if($filtres[$nomFiltre]->TypeLiaisonParametre == "get")
					{
						$filtresGets[] = $filtres[$nomFiltre]->ObtientIDElementHtmlComposant() ;
						$nomFiltresGets[] = $filtres[$nomFiltre]->NomParametreLie ;
					}
				}
				foreach($this->ChampsGetSoumetFormulaire as $n => $v)
				{
					if(! in_array($v, $filtresGet))
					{
						$filtresGets[] = $v ;
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
					if($this->ElementEnCoursTrouve || $this->AfficherCommandesAucunElement)
					{
						if($this->EstNul($this->DessinateurBlocCommandes))
						{
							$this->InitDessinateurBlocCommandes() ;
						}
						if($this->EstNul($this->DessinateurBlocCommandes))
						{
							return "<p>Le dessinateur de filtres n'est pas défini</p>" ;
						}
						$ctn .= '<div class="BlocCommandes'.(($this->ClasseCSSBlocCommandes)).'"'.(($this->AlignBlocCommandes != '') ? ' align="'.$this->AlignBlocCommandes.'"' : '').'>'.PHP_EOL ;
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
					$classeCSS = ($this->CommandeSelectionnee->StatutExecution == 1) ? $this->ClasseCSSSucces : $this->ClasseCSSErreur ;
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
			protected function CtnJsActualiseFormulaireFiltres()
			{
				$ctn = '' ;
				$ctn .= 'var elem = document.getElementById("'.$this->IDInstanceCalc.'") ;
if(elem !== null) {
var form = elem.getElementsByTagName("form")[0] ;
SoumetFormulaire'.$this->IDInstanceCalc.'(form) ;
form.submit() ;
}' ;
				return $ctn ;
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
				if($this->NomScriptExecSuccesCommandeExecuter != '')
				{
					$this->CommandeExecuter->NomScriptExecutionSucces = $this->NomScriptExecSuccesCommandeExecuter ;
					$this->CommandeExecuter->ParamsScriptExecutionSucces = $this->ParamsScriptExecutionSucces ;
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
				if($this->ClasseBoutonCommandeExecuter != '')
				{
					$this->CommandeExecuter->AffecteAttrSuppl('classe-btn', $this->ClasseBoutonCommandeExecuter) ; 
				}
				if($this->ClasseCSSCommandeExecuter != '')
				{
					$this->CommandeExecuter->NomClsCSS = $this->ClasseCSSCommandeExecuter ; 
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
				if($this->ClasseBoutonCommandeAnnuler != '')
				{
					$this->CommandeAnnuler->AffecteAttrSuppl('classe-btn', $this->ClasseBoutonCommandeAnnuler) ; 
				}
				if($this->ClasseCSSCommandeAnnuler != '')
				{
					$this->CommandeAnnuler->NomClsCSS = $this->ClasseCSSCommandeAnnuler ; 
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
			public $UtiliserLargeur = 1 ;
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
			public $LibelleLienAnnuler = "Retour" ;
			public $UrlLienAnnuler = "" ;
			public $NomScriptExecutionSucces = "" ;
			public $ParamsScriptExecutionSucces = array() ;
			public $InclureLibelle = 1 ;
			protected function VerifiePreRequis()
			{
				$this->VerifieFichiersUpload($this->FormulaireDonneesParent->FiltresEdition) ;
			}
			public function Execute()
			{
				parent::Execute() ;
				$this->RedirigeScriptExecutionSucces() ;
			}
			protected function RedirigeScriptExecutionSucces()
			{
				if($this->StatutExecution != 1 || $this->NomScriptExecutionSucces == '')
				{
					return ;
				}
				$script = $this->ZoneParent->Scripts[$this->NomScriptExecutionSucces] ;
				if($this->EstPasNul($script))
				{
					$this->ZoneParent->SauveMessageExecutionSession($this->StatutExecution, $this->MessageExecution, $this->ScriptParent->NomElementZone) ;
					redirect_to($script->ObtientUrlParam($this->ParamsScriptExecutionSucces)) ;
				}
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
			public $AnnuleLiaisonParametresSiSucces = 0 ;
			public function AdopteFormulaireDonnees($nom, & $formulaireDonnees)
			{
				parent::AdopteFormulaireDonnees($nom, $formulaireDonnees) ;
				if($formulaireDonnees->Editable == 1)
				{
					$this->InsereNouvCritere(new PvCritereValideRegexpForm()) ;
				}
			}
			public function Execute()
			{
				parent::Execute() ;
				if($this->StatutExecution == 1)
				{
					if($this->CacherFormulaireFiltresSiSucces)
					{
						$this->FormulaireDonneesParent->CacherFormulaireFiltres = 1 ;
						$this->FormulaireDonneesParent->InclureElementEnCours = 0 ;
						$this->FormulaireDonneesParent->InclureTotalElements = 0 ;
					}
					elseif($this->AnnuleLiaisonParametresSiSucces)
					{
						$this->FormulaireDonneesParent->AnnuleLiaisonParametres() ;
					}
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
					$this->ConfirmeSucces() ;
				}
				if($this->Mode == 3 && $this->StatutExecution == 1)
				{
					$this->CacherFormulaireFiltres = 1 ;
					$this->Visible = 0 ;
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
			
		class PvCommandeAppelDistantBase extends PvCommandeExecuterBase
		{
			public $NomZoneAppelDistant ;
			public $NomMethodeDistante ;
			public $AppelDistant ;
			public $ResultDistant ;
			protected function VerifiePreRequis()
			{
				parent::VerifiePreRequis() ;
				if(! $this->ErreurNonRenseignee())
				{
					return ;
				}
				if($this->NomZoneAppelDistant == "" || ! isset($this->ZoneParent->ApplicationParent->IHMs[$this->NomZoneAppelDistant]))
				{
					$this->RenseigneErreur("Zone d'appels distants inexistante") ;
				}
				$this->ZoneAppelDistant = & $this->ZoneParent->ApplicationParent->IHMs[$this->NomZoneAppelDistant] ;
				$this->ZoneAppelDistant->ChargeConfig() ;
			}
			protected function CreeAppelDistant()
			{
				$appel = new PvAppelJsonDistant() ;
				$appel->method = $this->NomMethodeDistante ;
				$appel->args = $this->ExtraitArgsAppelDistant() ;
				return $appel ;
			}
			protected function ExecuteInstructions()
			{
				$this->AppelDistant = $this->CreeAppelDistant() ;
				$this->ResultDistant = $this->ZoneAppelDistant->TraiteAppel($this->AppelDistant) ;
				if($this->ResultDistant->Succes())
				{
					$this->ConfirmeSucces() ;
				}
				else
				{
					$msgErreur = '' ;
					if($this->ResultDistant->erreur->message != '')
					{
						$msgErreur .= $this->ResultDistant->erreur->code."#".$this->ResultDistant->erreur->message ;
					}
					else
					{
						$msgErreur = 'Erreur rencontr&eacute; : #'.$this->ResultDistant->erreur->code ;
					}
					$this->RenseigneErreur($msgErreur) ;
				}
			}
		}
		class PvCommandeEnvoiDirectAppelDistant extends PvCommandeAppelDistantBase
		{
			protected function ExtraitArgsAppelDistant()
			{
				return $this->FormulaireDonneesParent->ExtraitObjetColonneLiee($this->FormulaireDonneesParent->FiltresEdition) ;
			}
		}
		class PvCommandeEnvoiCompletAppelDistant extends PvCommandeAppelDistantBase
		{
			protected function ExtraitArgsAppelDistant()
			{
				return array(
					'filtresGlobauxSelect' => $this->FormulaireDonneesParent->ExtraitObjetColonneLiee($this->FormulaireDonneesParent->FiltresGlobauxSelection),
					'filtresLgSelect' => $this->FormulaireDonneesParent->ExtraitObjetColonneLiee($this->FormulaireDonneesParent->FiltresLigneSelection),
					'filtresEdit' => $this->FormulaireDonneesParent->ExtraitObjetColonneLiee($this->FormulaireDonneesParent->FiltresEdition),
				) ;
			}
		}
		
		class PvCommandeImportElement extends PvCommandeFormulaireDonnees
		{
			public $FormatFichier ;
			public $NomParametreFiltreEdit ;
			public $MessageErreurFiltreNonDefini = "Le champ fichier n'est pas defini" ;
			public $MessageErreurMauvaiseExtension = "Extension de fichier non prise en charge" ;
			public $MessageErreurAucuneColonne = "Aucune colonne n'est définie" ;
			protected $ColonnesSelection = array() ;
			protected $ColonnesEdition = array() ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->FormatFichier = new PvFmtCsvCommandeImportElement() ;
			}
			protected function ExecuteInstructions()
			{
				$filtreNonRenseigne = 0 ;
				$filtreEdit = null ;
				if($this->NomParametreFiltreEdit == '')
				{
					$filtreNonRenseigne = 1 ;
				}
				else
				{
					foreach($this->FiltresEdition as $i => $filtreTemp)
					{
						if($filtreTemp->NomParametreLie == $this->NomParametreFiltreEdit)
						{
							$filtreEdit = & $filtreTemp ;
							break ;
						}
					}
				}
				if($this->EstNul($filtreEdit))
				{
					$this->RenseigneErreur($this->MessageErreurFiltreNonDefini) ;
					return ;
				}
				$this->FormulaireDonneesParent->LieTousLesFiltres() ;
				$cheminFichier = $filtreEdit->Lie() ;
				if($cheminFichier == '')
				{
					$this->RenseigneErreur($this->MessageErreurMauvaiseExtension) ;
					return ;
				}
				else
				{
					$infosFichier = pathinfo($cheminFichier) ;
					if(! in_array(strtolower($infosFichier["extension"]), $this->FormatFichier->Extensions))
					{
						$this->RenseigneErreur($this->MessageErreurMauvaiseExtension) ;
						return ;
					}
				}
				if($this->FormatFichier->Ouvre($cheminFichier))
				{
					$colonnes = $this->ColonnesSelection ;
					array_splice($colonnes, count($colonnes), 0, $this->ColonnesEdition) ;
					$enteteBrute = $this->FormatFichier->LitEntete() ;
					$entete = array() ;
					foreach($enteteBrute as $i => $nomCol)
					{
						$nomCol = strtolower(trim($nomCol)) ;
						if($nomCol == '')
						{
							continue ;
						}
						foreach($colonnes as $j => $colonne)
						{
							$nomsParamsAccept = array_map('strtolower', $this->NomsParametresAcceptes) ;
							if($nomCol == strtolower($colonne->NomParametreLie) || in_array($nomCol, $nomsParamsAccept))
							{
								$entete[$i] = $j ;
							}
						}
					}
					if(count($entete) == 0)
					{
						$this->RenseigneErreur($this->MessageErreurAucuneColonne) ;
					}
					else
					{
						while(($ligneBrute = $this->FormatFichier->LitLigne()) !== false)
						{
							$ligne = $this->CorrigeLigne($ligneBrute, $entete) ;
							$this->TraiteLigne($ligne) ;
						}
					}
					$this->FormatFichier->Ferme() ;
				}
			}
			protected function TraiteLigne($ligne)
			{
				$exprSelection = '' ;
				$fourn = & $this->FormulaireDonneesParent->FournisseurDonnees ;
				if(count($this->ColonnesSelection) > 0)
				{
					foreach($this->ColonnesSelection as $i => $colonne)
					{
						if($colonne->ExpressionDonnees == '')
						{
							continue ;
						}
						if($exprSelection != '')
						{
							$exprSelection .= ' and ' ;
						}
						$exprSelection = str_ireplace(array('<self>', '${luimeme}', '${this}'), $fourn->BaseDonnees->ParamPrefix.$colonne->IDInstanceCalc, $colonne->ExpressionDonnees) ;
					}
				}
				if($exprSelection != '')
				{
					
				}
			}
			protected function CorrigeLigne($ligneBrute, & $entete)
			{
				$ligne = array() ;
				foreach($entete as $i => $indexCol)
				{
					$colonne = & $this->Colonnes[$indexCol] ;
					if(! isset($ligneBrute[$i]))
					{
						$ligne = $colonne->ValeurParDefaut ;
					}
					else
					{
						$ligne[$indexCol] = $colonne->ObtientValeur($ligneBrute[$i]) ;
					}
				}
				return $ligne ;
			}
		}
		
		class PvFmtFichBaseCommandeImportElement
		{
			public $Extensions = array() ;
			public function Ouvre($cheminFichier)
			{
				return false ;
			}
			public function LitEntete()
			{
				return array() ;
			}
			public function LitLigne()
			{
				return array() ;
			}
			public function Ferme()
			{
			}
		}
		class PvFmtFichCsvCommandeImportElement extends PvFmtFichBaseCommandeImportElement
		{
			public $Extensions = array('csv', 'txt') ;
			public $SeparateurLigne = "\r\n" ;
			public $SeparateurColonne = ";" ;
			public $SupportFichier = false ;
			public function Ouvre($cheminFichier)
			{
				$this->SupportFichier = fopen($cheminFichier, "r") ;
				return ($this->SupportFichier !== false) ;
			}
			public function LitEntete()
			{
				$ligne = fgets($this->SupportFichier) ;
				return explode($this->SeparateurColonne, $ligne) ;
			}
			public function LitLigne()
			{
				$ligne = fgets($this->SupportFichier) ;
				return explode($this->SeparateurColonne, $ligne) ;
			}
			public function Ferme()
			{
				return fclose($this->SupportFichier) ;
			}
		}
		
		class PvColonneCommandeImportElement extends PvObjet
		{
			public $EstObligatoire = 0 ;
			public $NomParametreFiltreEdit ;
			public $NomColonneLiee ;
			public $ExpressionColonneLiee ;
			public $NomParametreLie ;
			public $NomsParametresAcceptes = array() ;
			public $ValeurParDefaut ;
			public function ObtientValeur($valeur)
			{
				return $valeur ;
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