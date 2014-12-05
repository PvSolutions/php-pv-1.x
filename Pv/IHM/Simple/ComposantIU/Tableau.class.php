<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_TABLEAU'))
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
		define('PV_COMPOSANT_SIMPLE_IU_TABLEAU', 1) ;
		
		class PvTableauDonneesHtml extends PvComposantIUDonneesSimple
		{
			public $Titre = "" ;
			public $TypeComposant = "TableauDonneesHTML" ;
			public $Largeur = "100%" ;
			public $Hauteur = "" ;
			public $EspacementCell = "4" ;
			public $MargesCell = "0" ;
			public $LargeurBordure = "1" ;
			public $CouleurBordure = "black" ;
			public $DefinitionsColonnes = array() ;
			public $Lignes = array() ;
			public $FiltresSelection = array() ;
			public $MaxElementsPossibles = array(20) ;
			public $ToujoursAfficher = 0 ;
			public $CacherFormulaireFiltres = 0 ;
			public $SuffixeParamFiltresSoumis = "filtre" ;
			public $SuffixeParamMaxElements = "max" ;
			public $SuffixeParamIndiceDebut = "debut" ;
			public $SuffixeParamIndiceColonneTri = "indice_tri" ;
			public $SuffixeParamSensColonneTri = "sens_tri" ;
			public $ForcerDesactCache = 0 ;
			public $CacherNavigateurRangees = 0 ;
			public $CacherNavigateurRangeesAuto = 0 ;
			public $IndiceDebut = 0 ;
			public $IndiceFin = 0 ;
			public $MaxElements = 0 ;
			public $TotalElements = 0 ;
			public $TotalRangees = 0 ;
			public $IndiceColonneTriSelect = -1 ;
			public $IndiceColonneTri = 0 ;
			public $NePasTrier = 0 ;
			public $SensColonneTri = "" ;
			public $TitreFormulaireFiltres = "Rechercher" ;
			public $AlignTitreFormulaireFiltres = "left" ;
			public $TitreBoutonSoumettreFormulaireFiltres = "GO" ;
			public $AlignBoutonSoumettreFormulaireFiltres = "left" ;
			public $TitreBoutonRAZFormulaireFiltres = "Effacer" ;
			public $LibelleTriAsc = "asc" ;
			public $LibelleTriDesc = "desc" ;
			public $LibelleTriAscSelectionne = "asc" ;
			public $LibelleTriDescSelectionne = "desc" ;
			public $ElementsEnCours = array() ;
			public $ElementsEnCoursBruts = array() ;
			public $DispositionComposants = array(1, 2, 3, 4) ;
			public $TriPossible = 1 ;
			public $RangeeEnCours = -1 ;
			public $LibellePremiereRangee = "|&lt;" ;
			public $LibelleRangeePrecedente = "&lt;&lt;" ;
			public $LibelleRangeeSuivante = "&gt;&gt;" ;
			public $LibelleDerniereRangee = "&gt;|" ;
			public $TitrePremiereRangee = "Premi&egrave;re rang&eacute;e" ;
			public $TitreRangeePrecedente = "Rang&eacute;e pr&eacute;c&eacute;dente" ;
			public $TitreRangeeSuivante = "Rang&eacute;e suivante" ;
			public $TitreDerniereRangee = "Derni&egrave;re rang&eacute;e" ;
			public $SeparateurLiensRangee = "&nbsp;&nbsp;&nbsp;&nbsp;" ;
			public $FormatInfosRangee = 'Affichage de ${NoDebut} - ${NoFin} sur ${TotalElements}' ;
			public $MessageAucunElement = "Aucun element n'a &eacute;t&eacute; trouv&eacute;" ;
			public $AlerterAucunElement = 1 ;
			public $UtiliserIconesTri = 1 ;
			public $AccepterTriColonneInvisible = 0 ;
			public $CheminRelativeIconesTri = "images" ;
			public $NomIconeTriAsc = "IconAsc.png" ;
			public $NomIconeTriDesc = "IconDesc.png" ;
			public $NomIconeTriAscSelectionne = "IconAscSelect.png" ;
			public $NomIconeTriDescSelectionne = "IconDescSelect.png" ;
			public $DessinateurFiltresSelection = null ;
			public $MessageFiltresNonRenseignes  = "Veuillez renseigner tous les param&egrave;tres." ;
			public $Commandes = array() ;
			public $CommandeSelectionnee = null ;
			public $SuffixeParamCommandeSelectionnee = "Commande" ;
			public $ValeurParamCommandeSelectionnee = "" ;
			public $DessinateurBlocCommandes = null ;
			public $SurvolerLigneFocus = 1 ;
			public $ExtraireValeursElements = 1 ;
			public $NavigateurRangees = null ;
			public function InscritExtractValsIndex(& $extractVals, $indexCol)
			{
				if(! isset($this->DefinitionsColonnes[$indexCol]))
					return ;
				$this->DefinitionsColonnes[$indexCol]->ExtracteurValeur = & $extractVals ;
			}
			public function InscritExtractVals(& $extractVals, & $col)
			{
				$col->ExtracteurValeur = & $extractVals ;
			}
			protected function DeclarationJsActiveCommande()
			{
				$ctn = '' ;
				$ctn .= '<script type="text/javascript">
	if(typeof '.$this->IDInstanceCalc.'_ActiveCommande != "function")
	{
		function '.$this->IDInstanceCalc.'_ActiveCommande(btn)
		{
			var nomCommande = (btn.rel == undefined) ? btn.getAttribute("rel") : btn.rel ;
			SoumetEnvoiFiltres'.$this->IDInstanceCalc.'({'.svc_json_encode($this->NomParamCommandeSelectionnee()).': nomCommande}) ;
		}
	}
</script>' ;
				return $ctn ;
			}
			public function InscritCommande($nom, & $commande)
			{
				$this->Commandes[$nom] = & $commande ;
				$commande->AdopteTableauDonnees($nom, $this) ;
			}
			public function InscritNouvCommande($nom, $commande)
			{
				$this->InscritCommande($nom, $commande) ;
			}
			public function InscritCmd($nom, & $commande)
			{
				$this->InscritCommande($nom, $commande) ;
			}
			public function InscritNouvCmd($nom, $commande)
			{
				$this->InscritCommande($nom, $commande) ;
			}
			public function & InsereCommande($nom, $commande)
			{
				$this->InscritCommande($nom, $commande) ;
				return $commande ;
			}
			public function & InsereCmd($nom, $commande)
			{
				$this->InscritCommande($nom, $commande) ;
				return $commande ;
			}
			protected function CreeCmdRafraich()
			{
				return new PvCommandeSoumetFiltresTabl() ;
			}
			public function InscritCmdRafraich($libelle='Actualiser', $cheminIcone='')
			{
				$cmd = $this->CreeCmdRafraich() ;
				$cmd->Libelle = $libelle ;
				$cmd->CheminIcone = $cheminIcone ;
				$this->InscritCmd('rafraich', $cmd) ;
				return $cmd ;
			}
			public function DetecteCommandeSelectionnee()
			{
				$this->ValeurParamCommandeSelectionnee = (isset($_GET[$this->NomParamCommandeSelectionnee()])) ? $_GET[$this->NomParamCommandeSelectionnee()] : "" ;
				$valeurNulle = null ;
				$this->CommandeSelectionnee = & $valeurNulle ;
				if($this->ValeurParamCommandeSelectionnee != "")
				{
					foreach($this->Commandes as $i => $commande)
					{
						if($commande->NomElementTableauDonnees == $this->ValeurParamCommandeSelectionnee)
						{
							$this->CommandeSelectionnee = & $this->Commandes[$i] ;
							break ;
						}
					}
					if($this->CommandeSelectionnee == null)
					{
						$this->ValeurParamCommandeSelectionnee = "" ;
					}
				}
			}
			public function ExecuteCommandeSelectionnee()
			{
				$this->DetecteCommandeSelectionnee() ;
				if($this->CommandeSelectionnee != null)
				{
					$this->CommandeSelectionnee->Execute() ;
				}
			}
			protected function DetecteParametresLocalisation()
			{
				$nomParamMaxElements = $this->NomParamMaxElements() ;
				$nomParamIndiceDebut = $this->NomParamIndiceDebut() ;
				$nomParamIndiceColonneTri = $this->NomParamIndiceColonneTri() ;
				$nomParamSensColonneTri = $this->NomParamSensColonneTri() ;
				$this->MaxElements = (isset($_GET[$nomParamMaxElements])) ? $nomParamMaxElements : 0 ;
				if(! in_array($this->MaxElements, $this->MaxElementsPossibles))
					$this->MaxElements = $this->MaxElementsPossibles[0] ;
				$this->IndiceDebut = (isset($_GET[$nomParamIndiceDebut])) ? intval($_GET[$nomParamIndiceDebut]) : 0 ;
				$this->IndiceColonneTri = -1 ;
				if($this->NePasTrier == 0)
				{
					$this->IndiceColonneTri = (isset($_GET[$nomParamIndiceColonneTri])) ? intval($_GET[$nomParamIndiceColonneTri]) : 0 ;
					if($this->IndiceColonneTri >= count($this->DefinitionsColonnes) || $this->IndiceColonneTri < 0)
						$this->IndiceColonneTri = 0 ;
					// Gerer les tri sur des colonnes invisibles...
					if(count($this->DefinitionsColonnes) > 0)
					{
						if(! $this->AccepterTriColonneInvisible && $this->DefinitionsColonnes[$this->IndiceColonneTri]->Visible == 0)
						{
							for($i=$this->IndiceColonneTri+1; $i<count($this->DefinitionsColonnes); $i++)
							{
								if($this->DefinitionsColonnes[$i]->Visible == 1)
								{
									$this->IndiceColonneTri = $i ;
									break ;
								}
							}
						}
					}
					$this->SensColonneTri = strtolower((isset($_GET[$nomParamSensColonneTri])) ? $_GET[$nomParamSensColonneTri] : $this->SensColonneTri) ;
					// echo $this->SensColonneTri.' jjj' ;
					if($this->SensColonneTri != "desc")
						$this->SensColonneTri = "asc" ;
				}
			}
			public function NomParamFiltresSoumis()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamFiltresSoumis ;
			}
			public function NomParamMaxElements()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamMaxElements ;
			}
			public function NomParamIndiceDebut()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamIndiceDebut ;
			}
			public function NomParamIndiceColonneTri()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamIndiceColonneTri ;
			}
			public function NomParamSensColonneTri()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamSensColonneTri ;
			}
			public function NomParamCommandeSelectionnee()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamCommandeSelectionnee ;
			}
			public function & InsereFltSelectRef($nom, & $filtreRef, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectCookie($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectSession($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectMembreConnecte($nom, $nomParamLie='', $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpGet($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpPost($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function InsereTablDefsCol($cols=array())
			{
				foreach($cols as $i => $nom)
				{
					$this->InsereDefCol($nom) ;
				}
			}
			public function InsereDefsColCachee()
			{
				$noms = func_get_args() ;
				foreach($noms as $i => $nom)
				{
					$this->InsereDefColCachee($nom) ;
				}
			}
			public function & InsereDefColCachee($nomDonnees, $aliasDonnees="")
			{
				$defCol = $this->InsereDefColInvisible($nomDonnees, $aliasDonnees) ;
				return $defCol ;
			}
			public function & InsereDefColInvisible($nomDonnees, $aliasDonnees="")
			{
				$defCol = new PvDefinitionColonneDonnees() ;
				$defCol->NomDonnees = $nomDonnees ;
				$defCol->AliasDonnees = $aliasDonnees ;
				$defCol->Visible = 0 ;
				$this->DefinitionsColonnes[] = & $defCol ;
				return $defCol ;
			}
			public function & InsereDefCol($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = new PvDefinitionColonneDonnees() ;
				if(is_array($nomDonnees))
				{
					$aliasDonnees = (isset($nomDonnees[1])) ? $nomDonnees[1] : '' ;
					$nomDonnees = $nomDonnees[0] ;
				}
				$defCol->NomDonnees = $nomDonnees ;
				$defCol->Libelle = $libelle ;
				$defCol->AliasDonnees = $aliasDonnees ;
				$this->DefinitionsColonnes[] = & $defCol ;
				return $defCol ;
			}
			public function & InsereDefColBool($nomDonnees, $libelle="", $aliasDonnees="", $valPositive="", $valNegative="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneBooleen() ;
				if($valPositive != "")
					$defCol->ValeurPositive = $valPositive ;
				if($valNegative != "")
					$defCol->ValeurNegative = $valNegative ;
				return $defCol ;
			}
			public function & InsereDefColMonnaie($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefColMoney($nomDonnees, $libelle, $aliasDonnees) ;
				return $defCol ;
			}
			public function & InsereDefColMoney($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneMonnaie() ;
				return $defCol ;
			}
			public function & InsereDefColHtml($modeleHtml="", $libelle="")
			{
				$defCol = $this->InsereDefCol("", $libelle, "") ;
				$defCol->Formatteur = new PvFormatteurColonneModeleHtml() ;
				$defCol->Formatteur->ModeleHtml = $modeleHtml ;
				return $defCol ;
			}
			public function & InsereDefColSansTri($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->TriPossible = 0 ;
				return $defCol ;
			}
			public function & InsereDefColActions($libelle, $actions=array())
			{
				$col = new PvDefinitionColonneDonnees() ;
				$col->TriPossible = 0 ;
				$col->Libelle = $libelle ;
				$col->AlignEntete = "center" ;
				$col->AlignElement = "center" ;
				$col->Formatteur = new PvFormatteurColonneLiens() ;
				$col->Liens = $actions ;
				$this->DefinitionsColonnes[] = & $col ;
				return $col ;
			}
			public function & InsereLienAction(& $col, $formatUrl='', $formatLib='')
			{
				$lien = null ;
				if($this->EstNul($col) || $col->Formatteur == null)
				{
					return $lien ;
				}
				$lien = $this->CreeLienAction() ;
				$lien->FormatURL = $formatUrl ;
				$lien->FormatLibelle = $formatLib ;
				$col->Formatteur->Liens[] = & $lien ;
				return $lien ;
			}
			public function & InsereLienActionAvant(& $col, $index, $formatUrl='', $formatLib='')
			{
				$lien = null ;
				if($this->EstNul($col) || $col->Formatteur == null)
				{
					return $lien ;
				}
				$lien = $this->CreeLienAction() ;
				$lien->FormatURL = $formatUrl ;
				$lien->FormatLibelle = $formatLib ;
				array_splice($col->Formatteur->Liens, $index, 0, array(& $lien)) ;
				return $lien ;
			}
			public function CreeLienAction()
			{
				return new PvConfigFormatteurColonneLien() ;
			}
			public function & InsereCmdRedirectUrl($nomCmd, $url, $libelle='')
			{
				$cmd = $this->CreeCmdRedirectUrl() ;
				$cmd->Url = $url ;
				$cmd->Libelle = $libelle ;
				$this->InscritCommande($nomCmd, $cmd) ;
				return $cmd ;
			}
			public function & InsereCmdRedirectScript($nomCmd, $nomScript, $libelle='', $params=array())
			{
				$cmd = $this->CreeCmdRedirectScript() ;
				$cmd->NomScript = $nomScript ;
				$cmd->Libelle = $libelle ;
				$cmd->Parametres = $params ;
				$this->InscritCommande($nomCmd, $cmd) ;
				return $cmd ;
			}
			public function DefinitionsColonnesExport()
			{
				$colonnes = array() ;
				foreach($this->DefinitionsColonnes as $i => $colonne)
				{
					if($colonne->PeutExporterDonnees())
					{
						$colonnes[] = $colonne ;
					}
				}
				return $colonnes ;
			}
			public function ExtraitValeursExport($ligne)
			{
				$valeurs = array() ;
				$colonnes = $this->DefinitionsColonnesExport() ;
				foreach($colonnes as $i => $colonne)
				{
					$valeurs[] = $colonne->FormatteValeur($this, $ligne) ;
				}
				return $valeurs ;
			}
			public function ExtraitLibellesExport()
			{
				$valeurs = array() ;
				$colonnes = $this->DefinitionsColonnesExport() ;
				foreach($colonnes as $i => $colonne)
				{
					$valeurs[] = $colonne->ObtientLibelle() ;
				}
				return $valeurs ;
			
			}
			public function FiltresSoumis()
			{
				$nomParamFiltresSoumis = $this->NomParamFiltresSoumis() ;
				return ($this->ToujoursAfficher || (isset($_GET[$nomParamFiltresSoumis]))) ? 1 : 0 ;
			}
			public function PrepareRendu()
			{
				parent::PrepareRendu() ;
				$this->ExecuteCommandeSelectionnee() ;
				if(! in_array($this->NomParamFiltresSoumis(), $this->ParamsGetSoumetFormulaire))
				{
					$this->ParamsGetSoumetFormulaire[] = $this->NomParamFiltresSoumis() ;
					$this->ParamsGetSoumetFormulaire[] = $this->NomParamCommandeSelectionnee() ;
				}
				if(! $this->FiltresSoumis() && $this->PossedeFiltresRendus())
				{
					return ;
				}
				$this->DetecteParametresLocalisation() ;
				$this->CalculeElementsRendu() ;
			}
			protected function ObtientValeursExtraites($lignes)
			{
				$extracteurs = array() ;
				foreach($this->DefinitionsColonnes as $i => $col)
				{
					if($col->NomDonnees != '' && $col->EstPasNul($col->ExtracteurValeur))
					{
						$extracteurs[$col->NomDonnees] = $col->ExtracteurValeur ;
					}
				}
				if(count($extracteurs) == 0)
				{
					return $lignes ;
				}
				$lignesResultat = array() ;
				foreach($lignes as $i => $ligne)
				{
					$lignesResultat[$i] = $ligne ;
					foreach($extracteurs as $nomDonnees => $extracteur)
					{
						if(! isset($ligne[$nomDonnees]))
						{
							continue ;
						}
						$valeursSuppl = $extracteur->Execute($ligne[$nomDonnees], $this) ;
						// print_r($valeursSuppl) ;
						if(is_array($valeursSuppl))
						{
							$lignesResultat[$i] = array_merge($lignesResultat[$i], array_apply_prefix($valeursSuppl, $nomDonnees.'_')) ;
						}
						// print_r(array_keys($lignesResultat[$i])) ;
					}
				}
				return $lignesResultat ;
			}
			protected function ObtientDefColsRendu()
			{
				$defCols = $this->DefinitionsColonnes ;
				return $defCols ;
			}
			public function CalculeElementsRendu()
			{
				$defCols = $this->ObtientDefColsRendu() ;
				$this->TotalElements = $this->FournisseurDonnees->CompteElements($defCols, $this->FiltresSelection) ;
				// echo $this->FournisseurDonnees->BaseDonnees->LastSqlText ;
				// Ajuster l'indice début
				if($this->IndiceDebut < 0)
					$this->IndiceDebut = 0 ;
				if($this->IndiceDebut >= $this->TotalElements)
					$this->IndiceDebut = $this->TotalElements ;
				if($this->TotalElements > 0)
				{
					$this->IndiceDebut = intval($this->IndiceDebut / $this->MaxElements) * $this->MaxElements ;
					$this->ElementsEnCoursBruts = $this->FournisseurDonnees->RangeeElements($defCols, $this->FiltresSelection, $this->IndiceDebut, $this->MaxElements, $this->IndiceColonneTri, $this->SensColonneTri) ;
					if($this->ExtraireValeursElements)
					{
						$this->ElementsEnCours = $this->ObtientValeursExtraites($this->ElementsEnCoursBruts) ;
					}
					else
					{
						$this->ElementsEnCours = $this->ElementsEnCoursBruts ;
					}
					// echo "Sql : ".$this->FournisseurDonnees->BaseDonnees->LastSqlText ;
					// print_r($this->FournisseurDonnees->BaseDonnees) ;
					// print_r($this->ElementsEnCours) ;
					$this->RangeeEnCours = $this->IndiceDebut / $this->MaxElements ;
					$nbRangees = intval($this->TotalElements / $this->MaxElements) ;
					$nbRangeesDec = $this->TotalElements / $this->MaxElements ;
					$this->TotalRangees = ($nbRangees == $nbRangeesDec) ? $nbRangeesDec : $nbRangees + 1 ;
					$this->IndiceFin = $this->IndiceDebut + count($this->ElementsEnCours) ;
					if($this->IndiceFin >= $this->TotalElements)
					{
						$this->IndiceFin = $this->TotalElements ;
					}
				}
				else
				{
					$this->IndiceDebut = 0 ;
					$this->TotalRangees = 0 ;
					$this->IndiceFin = 0 ;
					$this->RangeeEnCours = -1 ;
					$this->ElementsEnCours = array() ;
				}
			}
			protected function RenduDispositifBrut()
			{
				if(! $this->EstBienRefere())
				{
					return $this->RenduMalRefere() ;
				}
				$this->PrepareRendu() ;
				$ctn = '' ;
				$ctn .= $this->AppliqueHabillage() ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="TableauDonneesHTML">'.PHP_EOL ;
				$ctn .= $this->ContenuAvantRendu ;
				$ctn .= $this->RenduComposants().PHP_EOL ;
				$ctn .= $this->ContenuApresRendu ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			public function AppelJsEnvoiFiltres($parametres)
			{
				return 'SoumetEnvoiFiltres'.$this->IDInstanceCalc.'('.htmlentities(svc_json_encode($parametres)).')' ;
			}
			protected function RenduEnvoiFiltres()
			{
				$parametresRendu = $this->ParametresCommandeSelectionnee() ;
				foreach($this->ParamsGetSoumetFormulaire as $j => $n)
				{
					if(isset($_GET[$n]))
						$parametresRendu[$n] = $_GET[$n] ;
				}
				$nomFiltres = array_keys($this->FiltresSelection) ;
				$ctn = '' ;
				$ctn .= '<form id="FormulaireEnvoiFiltres'.$this->IDInstanceCalc.'" action="?" method="post" style="display:none;">'.PHP_EOL ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FiltresSelection[$nomFiltre] ;
					if(! $filtre->RenduPossible() || $filtre->TypeLiaisonParametre != 'get')
					{
						continue ;
					}
					$ctn .= '<input type="hidden" name="'.htmlentities($filtre->ObtientNomComposant()).'" value="'.htmlentities($filtre->Lie()).'" />'.PHP_EOL ;
				}
				$ctn .= '<input type="submit" value="Envoyer" />'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= '<script type="text/javascript">'.PHP_EOL ;
				$ctn .= 'function SoumetEnvoiFiltres'.$this->IDInstanceCalc.'(parametres)
{
	var parametresGet = '.svc_json_encode($parametresRendu).' ;
	var idFormulaire = '.svc_json_encode('FormulaireEnvoiFiltres'.$this->IDInstanceCalc).' ;
	for(var nom in parametres)
	{
		if(parametresGet[nom] != undefined)
		{
			parametresGet[nom] = parametres[nom] ;
		}
		else
		{
			var tableauNoeuds = document.getElementsByName(nom) ;
			if(tableauNoeuds.length > 0)
			{
				for(var j=0; j<tableauNoeuds.length; j++)
				{
					if(tableauNoeuds[j].form != null && tableauNoeuds[j].form.id != idFormulaire)
					{
						tableauNoeuds[j].value = parametres[nom] ;
					}
				}
			}
		}
		var formulaire = document.getElementById(idFormulaire) ;
		if(formulaire != null)
		{
			var url = "?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ScriptParent->NomElementZone).'" ;
			for(var nom in parametresGet)
			{
				if(url != "")
					url += "&" ;
				url += encodeURIComponent(nom) + "=" + encodeURIComponent(parametresGet[nom]) ;
			}
			formulaire.action = url ;
			formulaire.submit() ;
		}
	}
}'.PHP_EOL ;
				$ctn .= '</script>' ;
				return $ctn ;
			}
			public function RenduFiltresNonRenseignes()
			{
				$ctn = '' ;
				$ctn .= '<p class="FiltresNonRenseignes">'.$this->MessageFiltresNonRenseignes.'</p>' ;
				return $ctn ;
			}
			public function RenduComposants()
			{
				$ctn = "" ;
				$ctn .= $this->RenduEnvoiFiltres().PHP_EOL ;
				if($this->Titre != "")
				{
					$ctn .= '<div class="Titre">'.$this->Titre.'</div>'.PHP_EOL ;
				}
				foreach($this->DispositionComposants as $i => $indice)
				{
					if($i > 0)
					{
						$ctn .= PHP_EOL ;
					}
					switch($indice)
					{
						case PvDispositionTableauDonnees::FormulaireFiltres :
						{
							$ctn .= $this->RenduFormulaireFiltres() ;
						}
						break ;
						case PvDispositionTableauDonnees::BlocCommandes :
						{
							$ctn .= $this->RenduBlocCommandes() ;
						}
						break ;
						case PvDispositionTableauDonnees::RangeeDonnees :
						{
							$ctn .= $this->RenduRangeeDonnees() ;
						}
						break ;
						case PvDispositionTableauDonnees::NavigateurRangees :
						{
							$ctn .= $this->RenduNavigateurRangees() ;
						}
						break ;
						default :
						{
							$ctn .= $this->RenduAutreComposantSupport($indice) ;
						}
						break ;
					}
				}
				return $ctn ;
			}
			protected function RenduAutreComposantSupport($indice)
			{
			}
			public function PossedeFiltresRendus()
			{
				$nomFiltres = array_keys($this->FiltresSelection) ;
				$ok = 0 ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$ok = $this->FiltresSelection[$nomFiltre]->RenduPossible() ;
					if($ok)
					{
						break ;
					}
				}
				return $ok ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->CacherFormulaireFiltres)
					return '' ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					$this->InitDessinateurFiltresSelection() ;
				}
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					return "<p>Le dessinateur de filtres n'est pas défini</p>" ;
				}
				$ctn = "" ;
				if(! $this->PossedeFiltresRendus())
				{
					return '' ;
				}
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="SoumetFormulaire'.$this->IDInstanceCalc.'(this)">'.PHP_EOL ;
				$ctn .= '<table width="100%" cellspacing="0">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<tr>'.PHP_EOL ;
					$ctn .= '<th align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</th>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$ctn .= '<tr>'.PHP_EOL ;
				$ctn .= '<td>'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '<tr class="Boutons">'.PHP_EOL ;
				$ctn .= '<td align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'">'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '<button type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>'.PHP_EOL ;
				$ctn .= '</form>' ;
				$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresSelection) ;
				return $ctn ;
			}
			protected function ExtraitCommandesRendu()
			{
				return $this->Commandes ;
			}
			protected function RenduBlocCommandes()
			{
				$ctn = '' ;
				if(! $this->FiltresSoumis() && $this->PossedeFiltresRendus())
				{
					return $ctn ;
				}
				$commandes = $this->ExtraitCommandesRendu() ;
				if(count($commandes) == 0)
				{
					return '<br>' ;
				}
				// $parametres = $this->Filtre
				if($this->EstNul($this->DessinateurBlocCommandes))
				{
					$this->InitDessinateurBlocCommandes() ;
				}
				if($this->EstNul($this->DessinateurBlocCommandes))
				{
					return "<p>Le dessinateur de filtres n'est pas défini</p>" ;
				}
				$ctn .= '<div class="BlocCommandes">'.PHP_EOL ;
				$ctn .= $this->DessinateurBlocCommandes->Execute($this->ScriptParent, $this, $commandes) ;
				$ctn .= $this->DeclarationJsActiveCommande().PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			public function ParametresRendu()
			{
				$nomParamMaxElements = $this->NomParamMaxElements() ;
				$nomParamIndiceDebut = $this->NomParamIndiceDebut() ;
				$nomParamIndiceColonneTri = $this->NomParamIndiceColonneTri() ;
				$nomParamSensColonneTri = $this->NomParamSensColonneTri() ;
				$parametres = array(
					$nomParamMaxElements => $this->MaxElements,
					$nomParamIndiceDebut => $this->IndiceDebut,
					$nomParamIndiceColonneTri => $this->IndiceColonneTri,
					$nomParamSensColonneTri => $this->SensColonneTri,
				) ;
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
			protected function ParametresCommandeSelectionnee()
			{
				$parametres = $this->ParametresRendu() ;
				$parametres[$this->NomParamFiltresSoumis()] = 1 ;
				$parametres[$this->NomParamCommandeSelectionnee()] = "" ;
				if($this->ForcerDesactCache)
				{
					$parametres[$this->NomParamIdAleat()] = rand(0, 999999) ;
				}
				return $parametres ;
			}
			protected function ObtientNomClsCSSElem($index, & $elem)
			{
				$classePair = ($index % 2 == 0) ? "Pair" : "Impair" ;
				return 'Contenu '.$classePair ;
			}
			protected function RenduRangeeDonnees()
			{
				$ctn = '' ;
				if($this->FiltresSoumis() || ! $this->PossedeFiltresRendus())
				{
					$libelleTriAsc = $this->LibelleTriAsc ;
					$libelleTriDesc = $this->LibelleTriDesc ;
					$libelleTriAscSelectionne = $this->LibelleTriAscSelectionne ;
					$libelleTriDescSelectionne = $this->LibelleTriDescSelectionne ;
					if($this->UtiliserIconesTri)
					{
						$libelleTriAsc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAsc.'" />' ;
						$libelleTriDesc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDesc.'" />' ;
						$libelleTriAscSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAscSelectionne.'" />' ;
						$libelleTriDescSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDescSelectionne.'" />' ;
					}
					$parametresRendu = $this->ParametresCommandeSelectionnee() ;
					if(count($this->ElementsEnCours) > 0)
					{
						$ctn .= '<table' ;
						$ctn .= ' class="RangeeDonnees"' ;
						if($this->Largeur != "")
						{
							$ctn .= ' width="'.$this->Largeur.'"' ;
						}
						if($this->Hauteur != "")
						{
							$ctn .= ' height="'.$this->Hauteur.'"' ;
						}
						if($this->EspacementCell != "")
						{
							$ctn .= ' cellpadding="'.$this->EspacementCell.'"' ;
						}
						if($this->MargesCell != "")
						{
							$ctn .= ' cellspacing="'.$this->MargesCell.'"' ;
						}
						if($this->LargeurBordure != "")
						{
							$ctn .= ' border="'.$this->LargeurBordure.'"' ;
							if($this->CouleurBordure != "")
							{
								$ctn .= ' bordercolor="'.$this->CouleurBordure.'"' ;
							}
						}
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<tr class="Entete">'.PHP_EOL ;
						foreach($this->DefinitionsColonnes as $i => $colonne)
						{
							if($colonne->Visible == 0)
								continue ;
							$triPossible = ($this->TriPossible && $colonne->TriPossible) ;
							$ctn .= ($triPossible) ? '<td' : '<th' ;
							if($colonne->Largeur != "")
							{
								$ctn .= ' width="'.$colonne->Largeur.'"' ;
							}
							if($colonne->AlignEntete != "")
							{
								$ctn .= ' align="'.$colonne->AlignEntete.'"' ;
							}
							$ctn .= '>' ;
							if($triPossible)
							{
								$ctn .= '<table width="100%" cellspacing="0" cellpadding="2">' ;
								$ctn .= '<tr>' ;
								$ctn .= '<th width="*" rowspan="2">' ;
							}
							$ctn .= $colonne->ObtientLibelle() ;
							if($triPossible)
							{
								$ctn .= '</th>' ;
								$selectionne = ($this->IndiceColonneTri == $i && $this->SensColonneTri == "asc") ;
								$paramColAsc = array_merge($parametresRendu, array($this->NomParamSensColonneTri() => "asc", $this->NomParamIndiceColonneTri() => $i, $this->NomParamIndiceDebut() => 0)) ;
								$ctn .= '<td'.(($selectionne) ? ' class="ColonneTriee"' : '').'>' ;
								$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramColAsc).'">'.(($selectionne && $libelleTriAscSelectionne != "") ? $libelleTriAscSelectionne : $libelleTriAsc).'</a>' ;
								$ctn .= '</td>' ;
								$ctn .= '</tr>' ;
								$ctn .= '<tr>' ;
								$selectionne = ($this->IndiceColonneTri == $i && $this->SensColonneTri == "desc") ;
								$paramColAsc = array_merge($parametresRendu, array($this->NomParamSensColonneTri() => "desc", $this->NomParamIndiceColonneTri() => $i, $this->NomParamIndiceDebut() => 0)) ;
								$ctn .= '<td'.(($selectionne) ? ' class="ColonneTriee"' : '').'>' ;
								$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramColAsc).'">'.(($selectionne && $libelleTriDescSelectionne != "") ? $libelleTriDescSelectionne : $libelleTriDesc).'</a>' ;
								$ctn .= '</td>' ;
								$ctn .= '</tr>' ;
								$ctn .= '</table>' ;
							}
							$ctn .= (($triPossible) ? '</td>' : '</th>').PHP_EOL ;
						}
						$ctn .= '</tr>'.PHP_EOL ;
						foreach($this->ElementsEnCours as $j => $ligne)
						{
							$ctn .= '<tr' ;
							$ctn .= ' class="'.htmlentities($this->ObtientNomClsCSSElem($j, $ligne)) .'"' ;
							if($this->SurvolerLigneFocus)
							{
								$ctn .= ' onMouseOver="this.className = this.className + &quot; Survole&quot;;" onMouseOut="this.className = this.className.split(&quot; Survole&quot;).join(&quot; &quot;) ;"' ;
							}
							$ctn .= '>'.PHP_EOL ;
							foreach($this->DefinitionsColonnes as $i => $colonne)
							{
								if($colonne->Visible == 0)
									continue ;
								$ctn .= '<td' ;
								if($colonne->AlignElement != "")
								{
									$ctn .= ' align="'.$colonne->AlignElement.'"' ;
								}
								$ctn .= '>' ;
								$ctn .= $colonne->FormatteValeur($this, $ligne) ;
								$ctn .= '</td>'.PHP_EOL ;
							}
							$ctn .= '</tr>'.PHP_EOL ;
						}
						$ctn .= '</table>' ;
					}
					else
					{
						$ctn .= '<p class="AucunElement">'.$this->MessageAucunElement.'</p>' ;
					}
				}
				else
				{
					$ctn .= $this->RenduFiltresNonRenseignes() ;
				}
				return $ctn ;
			}
			protected function RenduNavigateurRangeesInt()
			{
				$ctn = '' ;
				$parametresRendu = $this->ParametresRendu() ;
				$ctn .= '<table class="NavigateurRangees"' ;
				if($this->Largeur != '')
					$ctn .= ' width="'.$this->Largeur.'"' ;
				$ctn .= ' cellspacing="0">'.PHP_EOL ;
				$ctn .= '<tr>'.PHP_EOL ;
				$ctn .= '<td align="left" width="50%" class="LiensRangee">'.PHP_EOL ;
				$paramPremiereRangee = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => 0)) ;
				$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramPremiereRangee).'" title="'.htmlentities($this->TitrePremiereRangee).'">'.$this->LibellePremiereRangee.'</a>'.PHP_EOL ;
				$ctn .= $this->SeparateurLiensRangee ;
				if($this->RangeeEnCours > 0)
				{
					$paramRangeePrecedente = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => ($this->RangeeEnCours - 1) * $this->MaxElements)) ;
					$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramRangeePrecedente).'" title="'.htmlentities($this->TitreRangeePrecedente).'">'.$this->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a title="'.htmlentities($this->TitreRangeePrecedente).'">'.$this->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				$ctn .= $this->SeparateurLiensRangee ;
				$ctn .= '<input type="text" size="4" onChange="var nb = 0 ; try { nb = parseInt(this.value) ; } catch(ex) { } if (isNaN(nb) == true) { nb = 0 ; } SoumetEnvoiFiltres'.$this->IDInstanceCalc.'({'.htmlentities(svc_json_encode($this->NomParamIndiceDebut())).' : (nb - 1) * '.$this->MaxElements.'}) ;" value="'.($this->RangeeEnCours + 1).'" style="text-align:center" />'.PHP_EOL ;
				$ctn .= $this->SeparateurLiensRangee ;
				//echo $this->RangeeEnCours." &lt; ".(intval($this->TotalElements / $this->MaxElements) - 1) ;
				if($this->RangeeEnCours < intval($this->TotalElements / $this->MaxElements))
				{
					$paramRangeeSuivante = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => ($this->RangeeEnCours + 1) * $this->MaxElements)) ;
					$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramRangeeSuivante).'" title="'.htmlentities($this->TitreRangeeSuivante).'">'.$this->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a title="'.htmlentities($this->TitreRangeeSuivante).'">'.$this->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				$paramDerniereRangee = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => intval($this->TotalElements / $this->MaxElements) * $this->MaxElements)) ;
				$ctn .= $this->SeparateurLiensRangee ;
				$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramDerniereRangee).'" title="'.htmlentities($this->TitreDerniereRangee).'">'.$this->LibelleDerniereRangee.'</a>'.PHP_EOL ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '<td align="right" class="InfosRangees" width="*">'.PHP_EOL ;
				$valeursRangee = array(
					'IndiceDebut' => $this->IndiceDebut,
					'NoDebut' => $this->IndiceDebut + 1,
					'IndiceFin' => $this->IndiceFin,
					'NoFin' => $this->IndiceFin,
					'TotalElements' => $this->TotalElements,
				) ;
				$ctn .= _parse_pattern($this->FormatInfosRangee, $valeursRangee) ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>' ;
				return $ctn ;
			}
			protected function RenduNavigateurRangees()
			{
				$ctn = '' ;
				if(! $this->CacherNavigateurRangees && ! ($this->CacherNavigateurRangeesAuto && $this->TotalElements <= $this->MaxElements) && $this->TotalElements > 0)
				{
					if($this->EstNul($this->NavigateurRangees))
					{
						$ctn .= $this->RenduNavigateurRangeesInt() ;
					}
					else
					{
						$ctn .= $this->NavigateurRangees->Execute($this->ScriptParent, $this) ;
					}
				}
				return $ctn ;
			}
			protected function InitDessinateurFiltresSelection()
			{
				$this->DessinateurFiltresSelection = new PvDessinateurRenduHtmlFiltresDonnees() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinateurRenduHtmlCommandes() ;
			}
		}
		class PvGrilleDonneesHtml extends PvTableauDonneesHtml
		{
			public $TypeComposant = 'GrilleDonneesHTML' ;
			public $ContenuLigneModele = '' ;
			public $ContenuLigneModeleUse = '' ;
			public $EmpilerValeursSiModLigVide = 1 ;
			public $OrientationValeursEmpilees = "vertical" ;
			public $AccepterTriColonneInvisible = 1 ;
			public $MaxColonnes = 1 ;
			public $LargeurBordure = 0 ;
			public $SourceValeursSuppl ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->SourceValeursSuppl = new PvSrcValsSupplGrilleDonnees() ;
			}
			protected function DetecteContenuLigneModeleUse()
			{
				$this->ContenuLigneModeleUse = $this->ContenuLigneModele ;
				if(empty($this->ContenuLigneModeleUse) && $this->EmpilerValeursSiModLigVide)
				{
					$this->ContenuLigneModeleUse .= '<table width="100%" cellspacing="0">'.PHP_EOL ;
					switch($this->OrientationValeursEmpilees)
					{
						case "vertical" :
						{
							foreach($this->DefinitionsColonnes as $i => $colonne)
							{
								$this->ContenuLigneModeleUse .= '<tr><td>${VALEUR_COL_'.$i.'}</td></tr>'.PHP_EOL ;
							}
						}
						break ;
						default :
						{
							$this->ContenuLigneModeleUse .= '<tr>'.PHP_EOL ;
							foreach($this->DefinitionsColonnes as $i => $colonne)
							{
								$this->ContenuLigneModeleUse .= '<td>${VALEUR_COL_'.$i.'}</td>'.PHP_EOL ;
							}
							$this->ContenuLigneModeleUse .= '</tr>'.PHP_EOL ;
						}
						break ;
					}
					$this->ContenuLigneModeleUse .= '</table>' ;
				}
			}
			protected function RenduRangeeDonnees()
			{
				$ctn = '' ;
				if($this->FiltresSoumis() || ! $this->PossedeFiltresRendus())
				{
					$this->DetecteContenuLigneModeleUse() ;
					$libelleTriAsc = $this->LibelleTriAsc ;
					$libelleTriDesc = $this->LibelleTriDesc ;
					$libelleTriAscSelectionne = $this->LibelleTriAscSelectionne ;
					$libelleTriDescSelectionne = $this->LibelleTriDescSelectionne ;
					if($this->UtiliserIconesTri)
					{
						$libelleTriAsc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAsc.'" />' ;
						$libelleTriDesc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDesc.'" />' ;
						$libelleTriAscSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAscSelectionne.'" />' ;
						$libelleTriDescSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDescSelectionne.'" />' ;
					}
					$parametresRendu = $this->ParametresCommandeSelectionnee() ;
					if(count($this->ElementsEnCours) > 0)
					{
						$ctn .= '<table' ;
						$ctn .= ' class="RangeeDonnees"' ;
						if($this->Largeur != "")
						{
							$ctn .= ' width="'.$this->Largeur.'"' ;
						}
						if($this->Hauteur != "")
						{
							$ctn .= ' height="'.$this->Hauteur.'"' ;
						}
						if($this->EspacementCell != "")
						{
							$ctn .= ' cellpadding="'.$this->EspacementCell.'"' ;
						}
						if($this->MargesCell != "")
						{
							$ctn .= ' cellspacing="'.$this->MargesCell.'"' ;
						}
						if($this->LargeurBordure != "")
						{
							$ctn .= ' border="'.$this->LargeurBordure.'"' ;
							if($this->CouleurBordure != "")
							{
								$ctn .= ' bordercolor="'.$this->CouleurBordure.'"' ;
							}
						}
						$ctn .= '>'.PHP_EOL ;
						$inclureLargCell = 1 ;
						foreach($this->ElementsEnCours as $j => $ligne)
						{
							if($this->MaxColonnes <= 1 || $j % $this->MaxColonnes == 0)
							{
								$ctn .= '<tr>'.PHP_EOL ;
							}
							$classePair = ($j % 2 == 0) ? "Pair" : "Impair" ;
							$ctn .= '<td' ;
							if($inclureLargCell)
							{
								$pourcentCol = ($this->MaxColonnes > 1) ? intval(100 / $this->MaxColonnes) : "100" ;
								$ctn .= ' width="'.$pourcentCol.'%"' ;
							}
							$ctn .= ' class="Contenu '.$classePair.'"' ;
							if($this->SurvolerLigneFocus)
							{
								$ctn .= ' onMouseOver="this.className = this.className + &quot; Survole&quot;;" onMouseOut="this.className = this.className.split(&quot; Survole&quot;).join(&quot; &quot;) ;"' ;
							}
							$ctn .= '>'.PHP_EOL ;
							$ligneDonnees = $ligne ;
							$ligneDonnees["POSITION"] = $j ;
							$ligneDonnees["NO"] = $j + 1 ;
							foreach($this->DefinitionsColonnes as $i => $colonne)
							{
								if($colonne->Visible == 0)
									continue ;
								$ligneDonnees["VALEUR_COL_".$i] = $colonne->FormatteValeur($this, $ligne) ;
								if($colonne->NomDonnees != "")
								{
									$ligneDonnees["VALEUR_COL_".$colonne->NomDonnees] = $ligneDonnees["VALEUR_COL_".$i] ;
								}
							}
							$ligneDonnees = $this->SourceValeursSuppl->Applique($this, $ligneDonnees) ;
							$ctn .= _parse_pattern($this->ContenuLigneModeleUse, $ligneDonnees) ;
							$ctn .= '</td>'.PHP_EOL ;
							if($this->MaxColonnes <= 1 || $j % $this->MaxColonnes == $this->MaxColonnes - 1)
							{
								$ctn .= '</tr>'.PHP_EOL ;
								$inclureLargCell = 0 ;
							}
						}
						if($this->MaxColonnes > 1 && count($this->ElementsEnCours) % $this->MaxColonnes != 0)
						{
							$colFusionnees = $this->MaxColonnes - (count($this->ElementsEnCours) % $this->MaxColonnes) ;
							$ctn .= '<td colspan="'.$colFusionnees.'"></td>'.PHP_EOL ;
							$ctn .= '</tr>'.PHP_EOL ;
						}
						$ctn .= '</table>' ;
					}
					else
					{
						$ctn .= '<p class="AucunElement">'.$this->MessageAucunElement.'</p>' ;
					}
				}
				else
				{
					$ctn .= $this->RenduFiltresNonRenseignes() ;
				}
				return $ctn ;
			}
		}
		class PvSrcValsSupplGrilleDonnees
		{
			public $InclureHtml = 1 ;
			public $SuffixeHtml = "_html" ;
			public $InclureUrl = 1 ;
			public $SuffixeUrl = "_query_string" ;
			public $LignesDonneesBrutes = null ;
			public function Applique(& $composant, $ligneDonnees)
			{
				$this->LigneDonneesBrutes = $ligneDonnees ;
				// print_r($ligneDonneesBrutes) ;
				if($this->InclureHtml)
				{
					$ligneDonnees = array_merge(
						$ligneDonnees,
						array_apply_suffix(array_map('htmlentities', $this->LigneDonneesBrutes), $this->SuffixeHtml)
					) ;
				}
				if($this->InclureUrl)
				{
					$ligneDonnees = array_merge(
						$ligneDonnees,
						array_apply_suffix(
							array_map(
								'urlencode',$this->LigneDonneesBrutes
							), $this->SuffixeUrl
						)
					) ;
				}
				return $ligneDonnees ;
			}
		}
		
		class PvNavTableauDonneesHtml extends PvNavigateurRangeesDonneesBase
		{
			public $TotalPremRangees = 3 ;
			public $TotalRangeesAvant = 2 ;
			public $TotalRangeesApres = 2 ;
			public $TotalDernRangees = 3 ;
			public $SepLiens = "&nbsp;&nbsp;" ;
			public $LibelleEtc = "..." ;
			public $CtnAvantListe ;
			public $CtnApresListe ;
			public $NomClasseSelect = "Selectionne" ;
			protected function ExecuteInstructions(& $script, & $comp)
			{
				$ctn = '' ;
				$ctn .= $this->CtnAvantListe ;
				$dernNoRangeeAffich = -1 ;
				for($i=0; $i<$comp->TotalRangees; $i++)
				{
					$dessineRangees = 0 ;
					if($i <= $this->TotalPremRangees || $i >= $comp->TotalRangees - $this->TotalDernRangees || ($i >= $comp->RangeeEnCours - $this->TotalRangeesAvant && $i <= $comp->RangeeEnCours + $this->TotalRangeesAvant))
					{
						$dessineRangees = 1 ;
					}
					if(! $dessineRangees)
					{
						$dernNoRangeeAffich = -1 ;
						continue ;
					}
					if($dernNoRangeeAffich != $i - 1)
					{
						$ctn .= $this->LibelleEtc. PHP_EOL ;
					}
					if($ctn != "")
						$ctn .= $this->SepLiens. PHP_EOL ;
					$ctn .= $this->RenduLienRangee($script, $comp, $i) ;
					$dernNoRangeeAffich = $i ;
				}
				$ctn .= $this->CtnApresListe ;
				$ctn = '<div class="NavigateurRangees">'.PHP_EOL
					.$ctn.'</div>' ;
				return $ctn ;
			}
			protected function RenduLienRangee(& $script, & $comp, $noRangee)
			{
				$ctn = '' ;
				$paramsRendu = $comp->ParametresRendu() ;
				$paramsRendu[$comp->NomParamIndiceDebut()] = $noRangee * $comp->MaxElements ;
				$ctn .= '<a href="javascript:' ;
				$ctn .= $comp->AppelJsEnvoiFiltres($paramsRendu).'"' ;
				if($noRangee == $comp->RangeeEnCours)
				{
					$ctn .= ' class="'.$this->NomClasseSelect.'"' ;
				}
				$ctn .= '>' ;
				$ctn .= ($noRangee + 1) ;
				$ctn .= '</a>'.PHP_EOL ;
				return $ctn ;
			}
		}
		
		class PvCommandeTableauDonneesBase extends PvCommandeComposantIUBase
		{
			public $NecessiteTableauDonnees = 1 ;
		}
		
		class PvCommandeSoumetFiltresTabl extends PvCommandeTableauDonneesBase
		{
			protected function ExecuteInstructions()
			{
			}
		}
		
		class PvCommandeExportBase extends PvCommandeTableauDonneesBase
		{
			public $NomFichier = "" ;
			public $TypeContenu = "application/octet-stream" ;
			public $TransfertBinaire = 1 ;
			public $InclureEntete = 1 ;
			public $RenseignerEntetesRequete = 1 ; 
			protected function ExecuteInstructions()
			{
				if($this->RenseignerEntetesRequete)
				{
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private", false);
					if($this->NomFichier != "")
					{
						header("Content-Disposition: attachment; filename=\"".$this->NomFichier."\";");
					}
					if($this->TransfertBinaire == 1)
					{
						header("Content-Transfer-Encoding: binary");
					}
					Header("Content-type: ".$this->TypeContenu) ;
				}
				$this->EnvoieContenu() ;
				exit ;
			}
			protected function EnvoieContenu()
			{
			}
		}
		class PvCommandeExportVersTexte extends PvCommandeExportBase
		{
			public $SeparateurColonnes = ";" ;
			public $SeparateurLignes = "\r\n" ;
			public $TotalLignes = 0 ;
			public $NomFichier = "resultat.txt" ;
			protected function EnvoieContenu()
			{
				$requete = $this->TableauDonneesParent->FournisseurDonnees->OuvreRequeteSelectElements($this->TableauDonneesParent->FiltresSelection) ;
				$this->EnvoieEntete() ;
				$this->TotalLignes = 0 ;
				while($ligne = $this->TableauDonneesParent->FournisseurDonnees->LitRequete($requete))
				{
					$valeurs = $this->TableauDonneesParent->ExtraitValeursExport($ligne) ;
					$this->EnvoieValeurs($valeurs) ;
					$this->TotalLignes++ ;
				}
				$this->TableauDonneesParent->FournisseurDonnees->FermeRequete($requete) ;
				$this->EnvoiePied() ;
			}
			protected function EnvoieEntete()
			{
				if(! $this->InclureEntete)
				{
					return ;
				}
				$libelles = $this->TableauDonneesParent->ExtraitLibellesExport() ;
				foreach($libelles as $i => $libelle)
				{
					if($i > 0)
					{
						echo $this->SeparateurColonnes ;
					}
					echo $libelle ;
				}
				echo $this->SeparateurLignes ;
			}
			protected function EnvoieValeurs($valeurs)
			{
				foreach($valeurs as $i => $valeur)
				{
					if($i != 0)
					{
						echo $this->SeparateurColonnes ;
					}
					echo $valeur ;
				}
				echo $this->SeparateurLignes ;
			}
			protected function EnvoiePied()
			{
			
			}
		}
		class PvCommandeExportVersExcel extends PvCommandeExportVersTexte
		{
			public $NomFichier = "resultat.xls" ;
			protected function EnvoieEntete()
			{
				echo '<!doctype html>
<html>
<head>
<style type="text/css">
tr {mso-height-source:auto;}
col {mso-width-source:auto;}
br {mso-data-placement:same-cell;}
.style0 {
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	white-space:nowrap;
	mso-rotate:0;
	mso-background-source:auto;
	mso-pattern:auto;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	border:none;
	mso-protection:locked visible;
	mso-style-name:Normal;
	mso-style-id:0;
}
td {
	mso-style-parent:style0;
	padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:locked visible;
	white-space:nowrap;
	mso-rotate:0;
}
.xl65 {mso-style-parent:style0; mso-number-format:"\@";}
</style>
<body>
<table>'.PHP_EOL ;
				if($this->InclureEntete)
				{
					echo '<tr>' ;
					$colonnes = $this->TableauDonneesParent->DefinitionsColonnesExport() ;
					foreach($colonnes as $i => $colonne)
					{
						echo '<th align="'.$colonne->AlignEntete.'" width="'.$colonne->Largeur.'" valign="'.$colonne->AlignVEntete.'">'.PHP_EOL ;
						echo $colonne->ObtientLibelle(). PHP_EOL ;
						echo '</th>'. PHP_EOL ;
					}
					echo '</tr>'.PHP_EOL ;
				}
			}
			protected function EnvoieValeurs($valeurs)
			{
				echo '<tr>'.PHP_EOL ;
				$colonnes = $this->TableauDonneesParent->DefinitionsColonnesExport() ;
				foreach($valeurs as $i => $valeur)
				{
					$colonne = $colonnes[$i] ;
					echo '<td class="xl65" align="'.$colonne->AlignElement.'" valign="'.$colonne->AlignVElement.'">'.$valeur.'</td>'.PHP_EOL ;
				}
				echo '</tr>'.PHP_EOL ;
			}
			protected function EnvoiePied()
			{
				echo '</table>
	</body>
</html>' ;
			}
		}
		
		class PvDispositionTableauDonnees
		{
			const FormulaireFiltres = 1 ;
			const BlocCommandes = 2 ;
			const RangeeDonnees = 3 ;
			const NavigateurRangees = 4 ;
		}
		
	}
	
?>