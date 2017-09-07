<?php
	
	if(! defined('PV_TABLEAU_DONNEES_IONIC'))
	{
		define('PV_TABLEAU_DONNEES_IONIC', 1) ;
		
		class PvTableauDonneesIonic extends PvComposantDonneesBaseIonic
		{
			public $Titre = "" ;
			public $TypeComposant = "TableauDonneesIonic" ;
			public $Hauteur = "" ;
			public $ContenuModele = "" ;
			public $ContenuAvantRangee = "" ;
			public $ContenuApresRangee = "" ;
			public $ContenuModeleUse = "" ;
			public $DefinitionsColonnes = array() ;
			public $Actions = array() ;
			public $Lignes = array() ;
			public $FiltresSelection = array() ;
			public $MaxElementsPossibles = array(20) ;
			public $ToujoursAfficher = 0 ;
			public $CacherFormulaireFiltres = 0 ;
			public $CacherBlocCommandes = 0 ;
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
			public $MessageAucunElement = "Aucun element n'a &eacute;t&eacute; trouv&eacute;" ;
			public $AlerterAucunElement = 1 ;
			public $UtiliserIconesTri = 1 ;
			public $AccepterTriColonneInvisible = 0 ;
			public $CheminRelativeIconesTri = "images" ;
			public $DessinateurFiltresSelection = null ;
			public $MessageFiltresNonRenseignes  = "Veuillez renseigner tous les param&egrave;tres." ;
			public $Commandes = array() ;
			public $CommandeSelectionnee = null ;
			public $SuffixeParamCommandeSelectionnee = "Commande" ;
			public $ValeurParamCommandeSelectionnee = "" ;
			public $DessinateurBlocCommandes = null ;
			public $SurvolerLigneFocus = 1 ;
			public $ExtraireValeursElements = 1 ;
			public $SautLigneSansCommande = 1 ;
			public $NavigateurRangees = null ;
			public $SourceValeursSuppl ;
			public $TagRacine ;
			public $MtdAfficheMsg ;
			public $MtdCalculeElemsRendu ;
			public $MtdAtteintPremRangee ;
			public $MtdAtteintDernRangee ;
			public $MtdAtteintRangeePrec ;
			public $MtdAtteintRangeeSuiv ;
			public $MtdAfficheActions ;
			public $FormatInfosRangee = '${NoDebut} - ${NoFin} / ${TotalElements}' ;
			public $FormatTitrePageSelect = 'Changer de page' ;
			public $FormatMsgPageSelect = 'Page actuelle : ${NumeroRangee} / ${TotalRangees}' ;
			public $LibelleFltPageActuSelect = 'Page' ;
			public $LibelleBtnAnnulSelect = 'Annuler' ;
			public $LibelleBtnExecSelect = 'OK' ;
			public $TitreActions = 'ACTIONS' ;
			public $MessageExecution = '' ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->SourceValeursSuppl = new PvSrcValsSupplLgnDonnees() ;
			}
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
			protected function DetecteParametresLocalisation()
			{
				$param = $this->ParamAppelDistant() ;
				$nomParamMaxElements = $this->NomParamMaxElements() ;
				$nomParamIndiceDebut = $this->NomParamIndiceDebut() ;
				$nomParamIndiceColonneTri = $this->NomParamIndiceColonneTri() ;
				$nomParamSensColonneTri = $this->NomParamSensColonneTri() ;
				$this->MaxElements = (isset($param->localisation->maxElements)) ? $param->localisation->maxElements : 0 ;
				if(! in_array($this->MaxElements, $this->MaxElementsPossibles))
					$this->MaxElements = $this->MaxElementsPossibles[0] ;
				$this->IndiceDebut = (isset($param->localisation->numeroRangee)) ? (intval($param->localisation->numeroRangee) - 1) * $this->MaxElements : 0 ;
				$this->IndiceColonneTri = -1 ;
				if($this->NePasTrier == 0)
				{
					$this->IndiceColonneTri = (isset($param->localisation->indiceColonneTri)) ? intval($param->localisation->indiceColonneTri) : 0 ;
					if($this->IndiceColonneTri >= count($this->DefinitionsColonnes) || $this->IndiceColonneTri < 0)
						$this->IndiceColonneTri = 0 ;
					// Gerer les tri sur des colonnes invisibles...
					if(count($this->DefinitionsColonnes) > 0)
					{
						if(! $this->AccepterTriColonneInvisible && $this->DefinitionsColonnes[$this->IndiceColonneTri]->Visible == 0)
						{
							for($i=$this->IndiceColonneTri+1; $i<count($this->DefinitionsColonnes); $i++)
							{
								if($this->DefinitionsColonnes[$i]->Visible == 1 && $this->DefinitionsColonnes[$i]->NomDonnees != '')
								{
									$this->IndiceColonneTri = $i ;
									break ;
								}
							}
						}
					}
					$this->SensColonneTri = strtolower((isset($param->localisation->sensColonneTri)) ? $param->localisation->sensColonneTri : $this->SensColonneTri) ;
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
			protected function DetermineContenuModeleUse()
			{
				if($this->ContenuModele == '')
				{
					$this->ContenuModeleUse = '' ;
					$colonnes = $this->ObtientDefColsRendu() ;
					$this->ContenuModeleUse .= '<ion-grid>'.PHP_EOL ;
					foreach($colonnes as $i => $defCol)
					{
						$this->ContenuModeleUse .= '<ion-row>'.PHP_EOL ;
						$this->ContenuModeleUse .= '<ion-col col-12>${'.$defCol->NomDonnees.'}</ion-col>'.PHP_EOL ;
						$this->ContenuModeleUse .= '</ion-row>'.PHP_EOL ;
					}
					$this->ContenuModeleUse .= '</ion-grid>' ;
				}
				else
				{
					$this->ContenuModeleUse = $this->ContenuModele ;
				}
				foreach($this->DefinitionsColonnes as $i => $defCol)
				{
					$this->ContenuModeleUse = str_replace('${'.$defCol->NomDonnees.'}', '{{element.'.$defCol->NomDonnees.'}}', $this->ContenuModeleUse) ;
				}
			}
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->NomGroupeFiltre = "filtresSelect" ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				$flt->NomGroupeFiltre = "filtresSelect" ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectTs($nom, $corpsBrut, $exprDonnees='')
			{
				$flt = $this->CreeFiltreTs($nom, $corpsBrut) ;
				$flt->CorpsBrut = $corpsBrut ;
				$flt->NomGroupeFiltre = "filtresSelect" ;
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
			public function & InsereDefColChoix($nomDonnees, $libelle="", $aliasDonnees="", $valsChoix=array())
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneChoix() ;
				$defCol->Formatteur->ValeursChoix = $valsChoix ;
				return $defCol ;
			}
			public function & InsereDefColEditable($nomDonnees, $libelle="", $aliasDonnees="", $nomClsComp="PvZoneTexteHtml")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneEditable() ;
				if($nomClsComp != '')
				{
					$defCol->Formatteur->DeclareComposant($nomClsComp) ;
				}
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
			public function & InsereDefColDateFr($nomDonnees, $libelle="", $inclureHeure=0)
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneDateFr() ;
				$defCol->Formatteur->InclureHeure = $inclureHeure ;
				return $defCol ;
			}
			public function & InsereDefColDateTimeFr($nomDonnees, $libelle="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneDateFr() ;
				$defCol->Formatteur->InclureHeure = 1 ;
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
			public function & InsereIconeAction(& $col, $formatUrl='', $formatCheminIcone='', $formatLib='')
			{
				$lien = null ;
				if($this->EstNul($col) || $col->Formatteur == null)
				{
					return $lien ;
				}
				$lien = $this->CreeLienAction() ;
				$lien->FormatURL = $formatUrl ;
				$lien->FormatCheminIcone = $formatCheminIcone ;
				$lien->FormatLibelle = $formatLib ;
				$lien->InclureLibelle = 0 ;
				$col->Formatteur->Liens[] = & $lien ;
				return $lien ;
			}
			public function & InsereIconeActionAvant(& $col, $index, $formatUrl='', $formatCheminIcone='', $formatLib='')
			{
				$lien = null ;
				if($this->EstNul($col) || $col->Formatteur == null)
				{
					return $lien ;
				}
				$lien = $this->CreeLienAction() ;
				$lien->FormatURL = $formatUrl ;
				$lien->FormatCheminIcone = $formatCheminIcone ;
				$lien->FormatLibelle = $formatLib ;
				$lien->InclureLibelle = 0 ;
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
			public function & InsereCmdExportTxt($nomCmd, $libelle='')
			{
				return $this->InsereCmdExportTexte($nomCmd, $libelle) ;
			}
			public function & InsereCmdExportXls($nomCmd, $libelle='')
			{
				return $this->InsereCmdExportExcel($nomCmd, $libelle) ;
			}
			public function & InsereCmdExportTexte($nomCmd, $libelle='')
			{
				$cmd = new PvCommandeExportVersTexte() ;
				if($libelle != '')
					$cmd->Libelle = $libelle ;
				$this->InscritCommande($nomCmd, $cmd) ;
				return $cmd ;
			}
			public function & InsereCmdExportExcel($nomCmd, $libelle='')
			{
				$cmd = new PvCommandeExportVersExcel() ;
				if($libelle != '')
					$cmd->Libelle = $libelle ;
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
			public function ExtraitValeursExport($ligne, & $cmd)
			{
				$valeurs = array() ;
				$colonnes = $this->DefinitionsColonnesExport() ;
				foreach($colonnes as $i => $colonne)
				{
					$valeur = $colonne->FormatteValeur($this, $ligne) ;
					if($valeur == $colonne->ValeurVide)
						$valeur = $cmd->ValeurVideExport ;
					$valeurs[] = $valeur ;
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
				return $this->ZoneParent()->PossedeMtdDistSelect() ;
			}
			public function PrepareRendu()
			{
				parent::PrepareRendu() ;
				$this->DetecteParametresLocalisation() ;
				$this->CalculeElementsRendu() ;
			}
			public function ObtientValeursExtraites($lignes)
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
			public function ObtientDefColsRendu()
			{
				$defCols = $this->DefinitionsColonnes ;
				return $defCols ;
			}
			protected function AlerteExceptionFournisseur()
			{
				$this->MessageExecution = "Exception survenue : ".$this->FournisseurDonnees->DerniereException->Message ;
			}
			public function CalculeElementsRendu()
			{
				$defCols = $this->ObtientDefColsRendu() ;
				$this->TotalElements = $this->FournisseurDonnees->CompteElements($defCols, $this->FiltresSelection) ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				// print_r($this->FournisseurDonnees) ;
				if($this->FournisseurDonnees->ExceptionTrouvee())
				{
					$this->AlerteExceptionFournisseur() ;
				}
				else
				{
					// Ajuster l'indice début
					if($this->IndiceDebut < 0)
						$this->IndiceDebut = 0 ;
					if($this->IndiceDebut >= $this->TotalElements)
						$this->IndiceDebut = $this->TotalElements ;
					if($this->TotalElements > 0)
					{
						$this->IndiceDebut = intval($this->IndiceDebut / $this->MaxElements) * $this->MaxElements ;
						$this->ElementsEnCoursBruts = $this->FournisseurDonnees->RangeeElements($defCols, $this->FiltresSelection, $this->IndiceDebut, $this->MaxElements, $this->IndiceColonneTri, $this->SensColonneTri) ;
						// echo "Sql : ".$this->FournisseurDonnees->BaseDonnees->LastSqlText ;
						if($this->FournisseurDonnees->ExceptionTrouvee())
						{
							$this->TotalElements = 0 ;
							$this->IndiceDebut = 0 ;
							$this->TotalRangees = 0 ;
							$this->IndiceFin = 0 ;
							$this->RangeeEnCours = -1 ;
							$this->ElementsEnCours = array() ;
							$this->AlerteExceptionFournisseur() ;
						}
						else
						{
							if($this->ExtraireValeursElements)
							{
								$this->ElementsEnCours = $this->ObtientValeursExtraites($this->ElementsEnCoursBruts) ;
							}
							else
							{
								$this->ElementsEnCours = $this->ElementsEnCoursBruts ;
							}
							// echo "Sql : ".$this->FournisseurDonnees->BaseDonnees->LastSqlText ;
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
			}
			public function Deploie()
			{
				$this->DetermineContenuModeleUse() ;
				$this->DeploieContenuHtml() ;
				$this->DeploieContenuTs() ;
			}
			protected function DeploieContenuHtml()
			{
				$tagContent = & $this->PageSrcParent->TagContent ;
				$this->TagRacine = $tagContent->InsereTagFils(new PvRenduHtmlIonic()) ;
				$this->TagRacine->Contenu .= '<div'.$this->AttrsHtmlNg().'>'.PHP_EOL ;
				$this->TagRacine->Contenu .= $this->ContenuAvantRendu ;
				$this->TagRacine->Contenu .= $this->RenduComposants() ;
				$this->TagRacine->Contenu .= $this->ContenuApresRendu ;
				$this->TagRacine->Contenu .= '</div>' ;
			}
			protected function DeploieContenuTs()
			{
				$classeTs = & $this->PageSrcParent->ClasseTs ;
				$this->DeploieContenuFiltresTs($this->FiltresSelection, $classeTs) ;
				$this->DeploieContenuCommandesTs($this->Commandes, $classeTs) ;
				$classeTs->InsereMembre('numeroRangee'.$this->IDInstanceCalc, 0, 'number') ;
				$classeTs->InsereMembre('maxElements'.$this->IDInstanceCalc, 0, 'number') ;
				$classeTs->InsereMembre('totalElements'.$this->IDInstanceCalc,0,  'number') ;
				$classeTs->InsereMembre('totalRangees'.$this->IDInstanceCalc, 0, 'number') ;
				$classeTs->InsereMembre('noDebut'.$this->IDInstanceCalc, 0, 'number') ;
				$classeTs->InsereMembre('noFin'.$this->IDInstanceCalc, 0, 'number') ;
				$classeTs->InsereMembre('indiceColonneTri'.$this->IDInstanceCalc, 0, 'number') ;
				$classeTs->InsereMembre('sensColonneTri'.$this->IDInstanceCalc, "''", 'string') ;
				$classeTs->InsereMembre('rangee'.$this->IDInstanceCalc, '[]', '') ;
				$this->PageSrcParent->ContenuTsAccesAutorise .= "_self.calcule".$this->IDInstanceCalc.'() ;'.PHP_EOL ;
				$this->MtdCalculeElemsRendu = $classeTs->InsereMethode('calcule'.$this->IDInstanceCalc) ;
				$this->MtdCalculeElemsRendu->CorpsBrut = 'let _self = this ;
'.$this->AppelTsMtdDist(
					$this->NomMethodeDistante("calculeElementsRendu"),
					$this->ArgsTsFiltresDistants(),
					'function(result) {
'.$this->CorpsTsReceptionDistant().'}',
					'function(error) {
_self.rangee'.$this->IDInstanceCalc.' = [] ;
this.afficheMsg("Erreur - Chargement des r&eacute;sultats", (error.toString())) ;
}') ;
				$this->MtdAffichePageSelect = $classeTs->InsereMethode("afficheSelectPage".$this->IDInstanceCalc) ;
				$paramsPageSelect = array('NumeroRangee' => "' + this.numeroRangee".$this->IDInstanceCalc." + '", 'TotalRangees' => "' + this.totalRangees".$this->IDInstanceCalc." + '") ;
				$this->MtdAffichePageSelect->CorpsBrut = 'let alert = this.alertCtrl.create({
title: \''._parse_pattern($this->FormatTitrePageSelect, $paramsPageSelect).'\',
message: \''._parse_pattern($this->FormatMsgPageSelect, $paramsPageSelect).'\',
inputs: [
{
name: '.svc_json_encode($this->LibelleFltPageActuSelect).',
placeholder: this.numeroRangee'.$this->IDInstanceCalc.'.toString()
},
],
buttons: [
{
text: '.svc_json_encode($this->LibelleBtnAnnulSelect).',
handler: data => {}
},
{
text: '.svc_json_encode($this->LibelleBtnExecSelect).',
handler: data => {
if(parseInt(data) !== NaN)
this.numeroRangee'.$this->IDInstanceCalc.' = parseInt(data) ;
this.calcule'.$this->IDInstanceCalc.'() ;
}
}
]
}) ;
alert.present() ;' ;
				$this->MtdAtteintPremRangee = $classeTs->InsereMethode("atteintPremRangee".$this->IDInstanceCalc) ;
				$this->MtdAtteintPremRangee->CorpsBrut = 'this.numeroRangee'.$this->IDInstanceCalc.' = 1 ;
this.calcule'.$this->IDInstanceCalc.'() ;' ;
				$this->MtdAtteintDernRangee = $classeTs->InsereMethode("atteintDernRangee".$this->IDInstanceCalc) ;
				$this->MtdAtteintDernRangee->CorpsBrut = 'this.numeroRangee'.$this->IDInstanceCalc.' = this.totalRangees'.$this->IDInstanceCalc.' ;
this.calcule'.$this->IDInstanceCalc.'() ;' ;
				$this->MtdAtteintRangeePrec = $classeTs->InsereMethode("atteintRangeePrec".$this->IDInstanceCalc) ;
				$this->MtdAtteintRangeePrec->CorpsBrut = 'this.numeroRangee'.$this->IDInstanceCalc.'-- ;
this.calcule'.$this->IDInstanceCalc.'() ;' ;
				$this->MtdAtteintRangeeSuiv = $classeTs->InsereMethode("atteintRangeeSuiv".$this->IDInstanceCalc) ;
				$this->MtdAtteintRangeeSuiv->CorpsBrut = 'this.numeroRangee'.$this->IDInstanceCalc.'++ ;
this.calcule'.$this->IDInstanceCalc.'() ;' ;
				$this->MtdAfficheActions = $classeTs->InsereMethode("insereActions".$this->IDInstanceCalc, array("element:any")) ;
				if(count($this->Actions) > 0)
				{
					$this->MtdAfficheActions->CorpsBrut = 'let _self = this ;
let actionSheet = this.actionSheetCtrl.create({
title: '.svc_json_encode($this->TitreActions).',
buttons: ['.PHP_EOL ;
					foreach($this->Actions as $i => $action)
					{
						$corpsBrutTs = $action->CorpsBrutTs() ;
						$this->MtdAfficheActions->CorpsBrut .= '{'.PHP_EOL ;
						if($action->Icone != '')
						{
							$this->MtdAfficheActions->CorpsBrut .= 'icon: '.svc_json_encode($action->Icone).','.PHP_EOL ;
						}
						$this->MtdAfficheActions->CorpsBrut .= 'text: '.svc_json_encode($this->Titre).',
role: '.svc_json_encode($this->RoleAction).',
handler: () => {'.(($corpsBrutTs != '') ? PHP_EOL .$corpsBrutTs : '').PHP_EOL
.'}'.PHP_EOL ;
						$this->MtdAfficheActions->CorpsBrut .= '}'.PHP_EOL ;
					}
					$this->MtdAfficheActions->CorpsBrut = ']
}) ;
actionSheet.present() ;' ;
				}
			}
			protected function CorpsTsReceptionDistant()
			{
				$ctn = '' ;
				$ctn .= 'if(result.valeur !== null) {
_self.rangee'.$this->IDInstanceCalc.' = result.valeur.rangee ;
_self.numeroRangee'.$this->IDInstanceCalc.' = result.valeur.numeroRangee ;
_self.maxElements'.$this->IDInstanceCalc.' = result.valeur.maxElements ;
_self.totalElements'.$this->IDInstanceCalc.' = result.valeur.totalElements ;
_self.totalRangees'.$this->IDInstanceCalc.' = result.valeur.totalRangees ;
_self.noDebut'.$this->IDInstanceCalc.' = parseInt(result.valeur.indiceDebut) + 1 ;
_self.noFin'.$this->IDInstanceCalc.' = parseInt(result.valeur.indiceFin) ;
} else {'.(($this->NgIf == '') ? '_self.afficheMsg("Erreur - Chargement des r&eacute;sultats", result.erreur.message) ;' : '').'
_self.rangee'.$this->IDInstanceCalc.' = [] ;
_self.numeroRangee'.$this->IDInstanceCalc.' = 0 ;
_self.maxElements'.$this->IDInstanceCalc.' = 0 ;
_self.totalElements'.$this->IDInstanceCalc.' = 0 ;
_self.totalRangees'.$this->IDInstanceCalc.' = 0 ;
_self.noDebut'.$this->IDInstanceCalc.' = 0 ;
_self.noFin'.$this->IDInstanceCalc.' = 0 ;
}' ;
				return $ctn ;
			}
			public function PossedeColonneEditable()
			{
				$ok = 0 ;
				foreach($this->DefinitionsColonnes as $i => $defCol)
				{
					if($defCol->EstVisible($this->ZoneParent) && $defCol->EstEditable())
					{
						$ok = 1 ;
						break ;
					}
				}
				return $ok ;
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
				$ctn = '<ion-list>' ;
				$ctn = '<ion-item>' ;
				$this->DessinateurFiltresEdition->Execute($this->PageSrcParent, $this, $this->FiltresEdition) ;
				$ctn = '</ion-item>' ;
				$ctn = '<ion-item>' ;
				$ctn = '<button ion-button (click)="calcule'.$this->IDInstanceCalc.'"><ion-icon name="search"></ion-icon> GO</button>' ;
				$ctn = '</ion-item>' ;
				$ctn = '</ion-list>' ;
			}
			protected function ExtraitCommandesRendu()
			{
				return $this->Commandes ;
			}
			protected function RenduBlocCommandes()
			{
				$ctn = '' ;
				if($this->CacherBlocCommandes || (! $this->FiltresSoumis() && $this->PossedeFiltresRendus()))
				{
					return $ctn ;
				}
				$commandes = $this->ExtraitCommandesRendu() ;
				if(count($commandes) == 0 && $this->SautLigneSansCommande == 1)
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
			protected function RenduRangeeDonnees()
			{
				$ctn = '' ;
				$defCols = $this->ObtientDefColsRendu() ;
				$ctn .= '<ion-list *ngIf="rangee'.$this->IDInstanceCalc.'.length &gt; 0">
<button ion-item *ngFor="let element of rangee'.$this->IDInstanceCalc.'">
'.$this->ContenuModeleUse.'</button>
</ion-list>' ;
				return $ctn ;
			}
			protected function RenduNavigateurRangeesInt()
			{
				$ctn = '' ;
				$ctn .= '<ion-grid align="center" *ngIf="totalElements'.$this->IDInstanceCalc.' &gt; 0">
<ion-row center>
<ion-col style="text-align: left">
<button ion-button icon-only (click)="atteintPremRangee'.$this->IDInstanceCalc.'()"><ion-icon name="skip-backward"></ion-icon></button>
<button ion-button icon-only (click)="atteintRangeePrec'.$this->IDInstanceCalc.'()"><ion-icon name="arrow-back"></ion-icon></button>
<button ion-button icon-only (click)="afficheSelectPage'.$this->IDInstanceCalc.'()"><ion-icon name="navigate"></ion-icon></button>
<button ion-button icon-only (click)="atteintRangeeSuiv'.$this->IDInstanceCalc.'()"><ion-icon name="arrow-forward"></ion-icon></button>
<button ion-button icon-only (click)="atteintDernRangee'.$this->IDInstanceCalc.'()"><ion-icon name="skip-forward"></ion-icon></button>
</ion-col>
<ion-col style="text-align: right" *ngIf="totalElements'.$this->IDInstanceCalc.' &gt; 0">
'._parse_pattern(
$this->FormatInfosRangee, array(
	"NoDebut" => '{{noDebut'.$this->IDInstanceCalc.'}}',
	"NoFin" => '{{noFin'.$this->IDInstanceCalc.'}}',
	"TotalElements" => '{{totalElements'.$this->IDInstanceCalc.'}}',
)).'
</ion-col>
</ion-row>
</ion-grid>' ;
				return $ctn ;
			}
			protected function RenduNavigateurRangees()
			{
				$ctn = '' ;
				$ctn .= $this->RenduNavigateurRangeesInt() ;
				return $ctn ;
			}
			protected function InitDessinateurFiltresSelection()
			{
				$this->DessinateurFiltresSelection = new PvDessinFiltres1Ionic() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinBlocCommandes1Ionic() ;
			}
			public function ArgsTsFiltresDistants()
			{
				return '{
filtresSelect : '.$this->ArgsTsFiltres($this->FiltresSelection).',
localisation : '.$this->ArgsTsLocalisation().',
}' ;
			}
			protected function ArgsTsLocalisation()
			{
				return '{
numeroRangee : this.numeroRangee'.$this->IDInstanceCalc.',
indiceColonneTri : this.indiceColonneTri'.$this->IDInstanceCalc.',
sensColonneTri : this.sensColonneTri'.$this->IDInstanceCalc.'
}' ;
			}
			protected function CreeMtdCalculElementsRendu()
			{
				return new PvMtdCalculTablDonneesIonic() ;
			}
			public function FournitMethodesDistantes()
			{
				$this->InsereMethodeDistante("calculeElementsRendu", $this->CreeMtdCalculElementsRendu()) ;
				foreach($this->Commandes as $nom => $commande)
				{
					$commande->FournitMethodesDistantes() ;
				}
				foreach($this->Actions as $j => $action)
				{
					$action->FournitMethodesDistantes() ;
				}
				foreach($this->FiltresSelection as $nom => $filtre)
				{
					$filtre->FournitMethodesDistantes() ;
				}
			}
			public function & InsereAction($titre, $action)
			{
				$action->Titre = $titre ;
				$this->Actions[] = & $action ;
			}
			public function & InsereActionTs($titre, $contenuTs)
			{
				$action = new PvActionTsTablDonneesIonic() ;
				$action->ContenuTs = $contenuTs ;
				return $this->InsereAction($titre, $action) ;
			}
			public function & InsereActionPageSrc($titre, & $pageSrc, $contenuArgs='{}')
			{
				$action = new PvActionTsTablDonneesIonic() ;
				$action->ContenuTs = 'this.navCtrl.push('.$pageSrc->NomClasse().', '.$contenuArgs.')' ;
				return $this->InsereAction($titre, $action) ;
			}
			public function LieTousLesFiltres()
			{
				$this->LieFiltres($this->FiltresSelection) ;
			}
		}
		
		class PvActionBaseTablDonneesIonic extends PvObjet
		{
			public $Titre ;
			public $Icone ;
			public $ClasseCSS ;
			public $RoleAction = '' ;
			public function FournitMethodesDistantes(& $tabl)
			{
			}
			public function CorpsBrutTs(& $tabl)
			{
			}
		}
		class PvActionTsTablDonneesIonic extends PvActionBaseTablDonneesIonic
		{
			public $ContenuTs ;
			public function CorpsBrutTs()
			{
				return $this->ContenuTs ;
			}
		}
		
		class PvResultCalculTablDonneesIonic
		{
			public $totalElements = 0 ;
			public $totalRangees = 0 ;
			public $indiceDebut = 0 ;
			public $indiceFin = 0 ;
			public $numeroRangee = 0 ;
			public $maxElements = 0 ;
			public $rangee = array() ;
		}
		
		class PvMtdCalculTablDonneesIonic extends PvMtdDistTablDonneesIonic
		{
			protected function ExecuteInstructions()
			{
				$tabl = & $this->ComposantIUParent ;
				$tabl->PrepareRendu() ;
				$tabl->CalculeElementsRendu() ;
				if($tabl->MessageExecution == '')
				{
					$result = new PvResultCalculTablDonneesIonic() ;
					$result->totalElements = $tabl->TotalElements ;
					$result->totalRangees = $tabl->TotalRangees ;
					$result->maxElements = $tabl->MaxElements ;
					$result->indiceDebut = $tabl->IndiceDebut ;
					$result->indiceFin = $tabl->IndiceFin ;
					$result->numeroRangee = $tabl->RangeeEnCours + 1 ;
					$result->rangee = array() ;
					foreach($tabl->ElementsEnCours as $i => $ligneDonnees)
					{
						$defCols = $tabl->DefinitionsColonnes ;
						$elemRangee = array() ;
						$ligneDonnees = $tabl->SourceValeursSuppl->Applique($this, $ligneDonnees) ;
						foreach($defCols as $j => $defCol)
						{
							$elemRangee[$defCol->NomDonnees] = $defCol->FormatteValeur($this, $ligneDonnees) ;
						}
						$result->rangee[] = $elemRangee ;
					}
					$this->ConfirmeSucces($result) ;
				}
				else
				{
					$this->RenseigneErreur(1, $tabl->MessageExecution) ;
				}
			}
		}
	}
	
?>