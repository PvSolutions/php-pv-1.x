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
			public $SautLigneSansCommande = 1 ;
			public $NavigateurRangees = null ;
			public $SourceValeursSuppl ;
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
								if($this->DefinitionsColonnes[$i]->Visible == 1 && $this->DefinitionsColonnes[$i]->NomDonnees != '')
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
			public function & InsereDefColDateTimeFr($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneDateFr() ;
				$defCol->Formatteur->InclureHeure = 1 ;
				return $defCol ;
			}
			public function & InsereDefColDetail($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonnePlusDetail() ;
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
					if($this->EstPasNul($this->CommandeSelectionnee) && $this->CommandeSelectionnee->InclureEnvoiFiltres())
					{
						$this->ParamsGetSoumetFormulaire[] = $this->NomParamCommandeSelectionnee() ;
					}
				}
				if(! $this->FiltresSoumis() && $this->PossedeFiltresRendus())
				{
					return ;
				}
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
				$this->MessageAucunElement = "Exception survenue : ".$this->FournisseurDonnees->DerniereException->Message ;
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
						// print_r($this->FournisseurDonnees) ;
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
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
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
					$ctn .= '<input type="hidden" name="'.htmlspecialchars($filtre->ObtientNomComposant()).'" value="'.htmlspecialchars($filtre->Lie()).'" />'.PHP_EOL ;
				}
				$ctn .= '<input type="submit" value="Envoyer" />'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= '<script type="text/javascript">'.PHP_EOL ;
				$ctn .= $this->CtnJSEnvoiFiltres($parametresRendu).PHP_EOL ;
				$ctn .= '</script>' ;
				return $ctn ;
			}
			protected function CtnJSEnvoiFiltres(& $parametresRendu)
			{
				$ctn = '' ;
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
			// alert(url) ;
			formulaire.submit() ;
		}
	}
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
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this) ;">'.PHP_EOL ;
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
						if($this->PossedeColonneEditable())
						{
							$ctnChampsPost = "" ;
							$nomFiltres = array_keys($this->FiltresSelection) ;
							$parametresRenduEdit = $this->ParametresCommandeSelectionnee() ;
							foreach($this->ParamsGetSoumetFormulaire as $j => $n)
							{
								if(isset($_GET[$n]))
									$parametresRenduEdit[$n] = $_GET[$n] ;
							}
							foreach($nomFiltres as $i => $nomFiltre)
							{
								$filtre = & $this->FiltresSelection[$nomFiltre] ;
								if($filtre->RenduPossible())
								{
									if($filtre->TypeLiaisonParametre == 'post')
									{
										$ctnChampsPost .= '<input type="hidden" name="'.htmlspecialchars($filtre->ObtientNomComposant()).'" value="'.htmlspecialchars($filtre->Lie()).'" />'.PHP_EOL ;
									}
									elseif($filtre->TypeLiaisonParametre == 'get')
									{
										$parametresRenduEdit[$filtre->ObtientNomComposant()] = $filtre->Lie() ;
									}
								}
							}
							$ctn .= '<form action="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&'.http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
							$ctn .= $ctnChampsPost ;
						}
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
							if(! $colonne->EstVisible($this->ZoneParent))
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
							$ligneDonnees = $ligne ;
							$ligneDonnees = $this->SourceValeursSuppl->Applique($this, $ligneDonnees) ;
							foreach($this->DefinitionsColonnes as $i => $colonne)
							{
								// print_r($ligne) ;
								if(! $colonne->EstVisible($this->ZoneParent))
									continue ;
								$ctn .= '<td' ;
								if($colonne->AlignElement != "")
								{
									$ctn .= ' align="'.$colonne->AlignElement.'"' ;
								}
								if($colonne->StyleCSS != '')
								{
									$ctn .= ' style="'.htmlentities($colonne->StyleCSS).'"' ;
								}
								if($colonne->NomClasseCSS != '')
								{
									$ctn .= ' class="'.htmlentities($colonne->NomClasseCSS).'"' ;
								}
								$ctn .= '>' ;
								$ctn .= $colonne->FormatteValeur($this, $ligneDonnees) ;
								$ctn .= '</td>'.PHP_EOL ;
							}
							$ctn .= '</tr>'.PHP_EOL ;
						}
						$ctn .= '</table>' ;
						if($this->PossedeColonneEditable())
						{
							$ctn .= PHP_EOL .'<div style="display:none"><input type="submit" /></div>
</form>' ;
						}
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
				$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramPremiereRangee).'" title="'.$this->TitrePremiereRangee.'">'.$this->LibellePremiereRangee.'</a>'.PHP_EOL ;
				$ctn .= $this->SeparateurLiensRangee ;
				if($this->RangeeEnCours > 0)
				{
					$paramRangeePrecedente = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => ($this->RangeeEnCours - 1) * $this->MaxElements)) ;
					$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramRangeePrecedente).'" title="'.$this->TitreRangeePrecedente.'">'.$this->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a title="'.$this->TitreRangeePrecedente.'">'.$this->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				$ctn .= $this->SeparateurLiensRangee ;
				$ctn .= '<input type="text" size="4" onChange="var nb = 0 ; try { nb = parseInt(this.value) ; } catch(ex) { } if (isNaN(nb) == true) { nb = 0 ; } SoumetEnvoiFiltres'.$this->IDInstanceCalc.'({'.htmlentities(svc_json_encode($this->NomParamIndiceDebut())).' : (nb - 1) * '.$this->MaxElements.'}) ;" value="'.($this->RangeeEnCours + 1).'" style="text-align:center" />'.PHP_EOL ;
				$ctn .= $this->SeparateurLiensRangee ;
				//echo $this->RangeeEnCours." &lt; ".(intval($this->TotalElements / $this->MaxElements) - 1) ;
				if($this->RangeeEnCours < intval($this->TotalElements / $this->MaxElements))
				{
					$paramRangeeSuivante = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => ($this->RangeeEnCours + 1) * $this->MaxElements)) ;
					$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramRangeeSuivante).'" title="'.$this->TitreRangeeSuivante.'">'.$this->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a title="'.$this->TitreRangeeSuivante.'">'.$this->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				$paramDerniereRangee = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => intval($this->TotalElements / $this->MaxElements) * $this->MaxElements)) ;
				$ctn .= $this->SeparateurLiensRangee ;
				$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramDerniereRangee).'" title="'.$this->TitreDerniereRangee.'">'.$this->LibelleDerniereRangee.'</a>'.PHP_EOL ;
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
			public $AlignVCellule = "middle" ;
			public $AlignCellule = "" ;
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
							$ctn .= ' valign="'.$this->AlignVCellule.'"' ;
							if($this->AlignCellule != '')
							{
								$ctn .= ' align="'.$this->AlignCellule.'"' ;
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
		class PvSrcValsSupplGrilleDonnees extends PvSrcValsSupplLgnDonnees
		{
			public $InclureHtml = 1 ;
			public $SuffixeHtml = "_html" ;
			public $InclureUrl = 1 ;
			public $SuffixeUrl = "_query_string" ;
		}
		
		class PvTableauDonneesBootstrap extends PvTableauDonneesHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-xs-6" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesBootstrap() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
				$this->NavigateurRangees = new PvNavTableauDonneesBootstrap() ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->CacherFormulaireFiltres)
					return '' ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					$this->InitDessinateurFiltresSelection() ;
				}
				// print_r(get_class($this->DessinateurFiltresSelection)) ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					return "<p>Le dessinateur de filtres n'est pas d&eacute;fini</p>" ;
				}
				$ctn = "" ;
				if(! $this->PossedeFiltresRendus())
				{
					return '' ;
				}
				$this->DessinateurFiltresSelection->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this) ;" role="form">'.PHP_EOL ;
				$ctn .= '<div class="panel panel-default">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="panel-heading" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresSelect.'">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="panel-footer">'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresSelection) ;
				return $ctn ;
			}
			protected function RenduBlocCommandes()
			{
				$ctn = trim(parent::RenduBlocCommandes()) ;
				if(count($this->Commandes) > 0)
				{
					$ctn = '<div class="panel panel-default"><div class="panel-footer">'.PHP_EOL
						.$ctn.PHP_EOL
						.'</div></div>' ;
				}
				return $ctn ;
			}
			protected function RenduRangeeDonnees()
			{
				$ctn = '' ;
				if($this->FiltresSoumis() || ! $this->PossedeFiltresRendus())
				{
					$libelleTriAsc = '<span class="text-muted glyphicon glyphicon-menu-up" title="'.htmlspecialchars($this->LibelleTriAsc).'"></span>' ;
					$libelleTriDesc = '<span class="text-muted glyphicon glyphicon-menu-down" title="'.htmlspecialchars($this->LibelleTriDesc).'"></span>' ;
					$libelleTriAscSelectionne = '<span class="glyphicon glyphicon-menu-up" title="'.htmlspecialchars($this->LibelleTriAsc).'"></span>' ;
					$libelleTriDescSelectionne = '<span class="glyphicon glyphicon-menu-down" title="'.htmlspecialchars($this->LibelleTriDesc).'"></span>' ;
					$parametresRendu = $this->ParametresCommandeSelectionnee() ;
					if(count($this->ElementsEnCours) > 0)
					{
						if($this->PossedeColonneEditable())
						{
							$ctnChampsPost = "" ;
							$nomFiltres = array_keys($this->FiltresSelection) ;
							$parametresRenduEdit = $this->ParametresCommandeSelectionnee() ;
							foreach($this->ParamsGetSoumetFormulaire as $j => $n)
							{
								if(isset($_GET[$n]))
									$parametresRenduEdit[$n] = $_GET[$n] ;
							}
							foreach($nomFiltres as $i => $nomFiltre)
							{
								$filtre = & $this->FiltresSelection[$nomFiltre] ;
								if($filtre->RenduPossible())
								{
									if($filtre->TypeLiaisonParametre == 'post')
									{
										$ctnChampsPost .= '<input type="hidden" name="'.htmlspecialchars($filtre->ObtientNomComposant()).'" value="'.htmlspecialchars($filtre->Lie()).'" />'.PHP_EOL ;
									}
									elseif($filtre->TypeLiaisonParametre == 'get')
									{
										$parametresRenduEdit[$filtre->ObtientNomComposant()] = $filtre->Lie() ;
									}
								}
							}
							$ctn .= '<form action="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&'.http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
							$ctn .= $ctnChampsPost ;
						}
						$ctn .= '<div class="panel panel-default"><div class="panel-body">'.PHP_EOL ;
						$ctn .= '<table' ;
						$ctn .= ' class="RangeeDonnees table '.$this->ClasseCSSRangee.'"' ;
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<thead>'.PHP_EOL ;
						$ctn .= '<tr class="Entete">'.PHP_EOL ;
						foreach($this->DefinitionsColonnes as $i => $colonne)
						{
							if(! $colonne->EstVisible($this->ZoneParent))
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
								$ctn .= '<th width="*" rowspan="2">'.PHP_EOL ;
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
						$ctn .= '</thead>'.PHP_EOL ;
						$ctn .= '<tbody>'.PHP_EOL ;
						foreach($this->ElementsEnCours as $j => $ligne)
						{
							$ctn .= '<tr>'.PHP_EOL ;
							foreach($this->DefinitionsColonnes as $i => $colonne)
							{
								if(! $colonne->EstVisible($this->ZoneParent))
									continue ;
								$ctn .= '<td' ;
								if($colonne->AlignElement != "")
								{
									$ctn .= ' align="'.$colonne->AlignElement.'"' ;
								}
								if($colonne->StyleCSS != '')
								{
									$ctn .= ' style="'.htmlentities($colonne->StyleCSS).'"' ;
								}
								if($colonne->NomClasseCSS != '')
								{
									$ctn .= ' class="'.htmlentities($colonne->NomClasseCSS).'"' ;
								}
								$ctn .= '>' ;
								$ligneDonnees = $ligne ;
								$ligneDonnees = $this->SourceValeursSuppl->Applique($this, $ligneDonnees) ;
								$ctn .= $colonne->FormatteValeur($this, $ligneDonnees) ;
								$ctn .= '</td>'.PHP_EOL ;
							}
							$ctn .= '</tr>'.PHP_EOL ;
						}
						$ctn .= '</tbody>'.PHP_EOL ;
						$ctn .= '</table>'.PHP_EOL ;
						$ctn .= '</div></div>' ;
						if($this->PossedeColonneEditable())
						{
							$ctn .= PHP_EOL .'<div style="display:none"><input type="submit" /></div>
</form>' ;
						}
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
		class PvGrilleDonneesBootstrap extends PvGrilleDonneesHtml
		{
			public $SautLigneSansCommande = 0 ;
			protected function RenduRangeeDonnees()
			{
				$ctn = '' ;
				if($this->FiltresSoumis() || ! $this->PossedeFiltresRendus())
				{
					$this->DetecteContenuLigneModeleUse() ;
					$parametresRendu = $this->ParametresCommandeSelectionnee() ;
					if(count($this->ElementsEnCours) > 0)
					{
						$ctn .= '<table' ;
						$ctn .= ' class="RangeeDonnees table "' ;
						if($this->Largeur != "")
						{
							$ctn .= ' width="'.$this->Largeur.'"' ;
						}
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<tr><td><div class="container-fluid">'.PHP_EOL ;
						$inclureLargCell = 1 ;
						$colXs = 12 / $this->MaxColonnes ;
						foreach($this->ElementsEnCours as $j => $ligne)
						{
							if($this->MaxColonnes <= 1 || $j % $this->MaxColonnes == 0)
							{
								$ctn .= '<div class="row">'.PHP_EOL ;
							}
							$ctn .= '<div class="Contenu col-xs-'.$colXs.'"' ;
							$ctn .= ' align="'.$this->AlignCellule.'"' ;
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
							$ctn .= '</div>'.PHP_EOL ;
							if($this->MaxColonnes <= 1 || $j % $this->MaxColonnes == $this->MaxColonnes - 1)
							{
								$ctn .= '</div>'.PHP_EOL ;
								$inclureLargCell = 0 ;
							}
						}
						if($this->MaxColonnes > 1 && count($this->ElementsEnCours) % $this->MaxColonnes != 0)
						{
							$colFusionnees = intval(($this->MaxColonnes - (count($this->ElementsEnCours) % $this->MaxColonnes)) * 12 / $this->MaxColonnes) ;
							$ctn .= '<div class="col-xs-'.$colFusionnees.'"></div>'.PHP_EOL ;
							$ctn .= '</div>'.PHP_EOL ;
						}
						$ctn .= '</div></td></tr>' ;
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
		
		class PvNavTableauDonneesBootstrap extends PvNavigateurRangeesDonneesBase
		{
			public function Execute(& $script, & $composant)
			{
				return $this->ExecuteInstructions($script, $composant) ;
			}
			protected function ExecuteInstructions(& $script, & $composant)
			{
				$ctn = '' ;
				$classeCSSBtn = $composant->ClasseCSSBtnNav ;
				$parametresRendu = $composant->ParametresRendu() ;
				$ctn .= '<div class="panel panel-default"><div class="panel-footer">'.PHP_EOL ;
				$ctn .= '<div class="NavigateurRangees container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="col-xs-6 LiensRangee">'.PHP_EOL ;
				$paramPremiereRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => 0)) ;
				$ctn .= '<a class="btn '.$classeCSSBtn.'" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramPremiereRangee).'" title="'.$composant->TitrePremiereRangee.'">'.$composant->LibellePremiereRangee.'</a>'.PHP_EOL ;
				$ctn .= $composant->SeparateurLiensRangee ;
				if($composant->RangeeEnCours > 0)
				{
					$paramRangeePrecedente = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => ($composant->RangeeEnCours - 1) * $composant->MaxElements)) ;
					$ctn .= '<a class="btn '.$classeCSSBtn.'" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangeePrecedente).'" title="'.$composant->TitreRangeePrecedente.'">'.$composant->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a class="btn '.$classeCSSBtn.'" title="'.$composant->TitreRangeePrecedente.'">'.$composant->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				$ctn .= $composant->SeparateurLiensRangee ;
				$ctn .= '<input type="text" size="4" onChange="var nb = 0 ; try { nb = parseInt(this.value) ; } catch(ex) { } if (isNaN(nb) == true) { nb = 0 ; } SoumetEnvoiFiltres'.$composant->IDInstanceCalc.'({'.htmlentities(svc_json_encode($composant->NomParamIndiceDebut())).' : (nb - 1) * '.$composant->MaxElements.'}) ;" value="'.($composant->RangeeEnCours + 1).'" style="text-align:center" />'.PHP_EOL ;
				$ctn .= $composant->SeparateurLiensRangee ;
				//echo $composant->RangeeEnCours." &lt; ".(intval($composant->TotalElements / $composant->MaxElements) - 1) ;
				if($composant->RangeeEnCours < intval($composant->TotalElements / $composant->MaxElements))
				{
					$paramRangeeSuivante = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => ($composant->RangeeEnCours + 1) * $composant->MaxElements)) ;
					$ctn .= '<a class="btn '.$classeCSSBtn.'" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangeeSuivante).'" title="'.$composant->TitreRangeeSuivante.'">'.$composant->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a class="btn '.$classeCSSBtn.'" class="btn" title="'.$composant->TitreRangeeSuivante.'">'.$composant->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				$paramDerniereRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => intval($composant->TotalElements / $composant->MaxElements) * $composant->MaxElements)) ;
				$ctn .= $composant->SeparateurLiensRangee ;
				$ctn .= '<a class="btn '.$classeCSSBtn.'" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramDerniereRangee).'" title="'.$composant->TitreDerniereRangee.'">'.$composant->LibelleDerniereRangee.'</a>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div align="right" class="InfosRangees col-xs-6">'.PHP_EOL ;
				$valeursRangee = array(
					'IndiceDebut' => $composant->IndiceDebut,
					'NoDebut' => $composant->IndiceDebut + 1,
					'IndiceFin' => $composant->IndiceFin,
					'NoFin' => $composant->IndiceFin,
					'TotalElements' => $composant->TotalElements,
				) ;
				$ctn .= _parse_pattern($composant->FormatInfosRangee, $valeursRangee) ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div></div>' ;
				return $ctn ;
				return $ctn ;
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
			public $ValeurVideExport = "" ;
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
			public $ExprAvantValeur = "" ;
			public $ExprApresValeur = "" ;
			public $TotalLignes = 0 ;
			public $NomFichier = "resultat.txt" ;
			protected function EnvoieContenu()
			{
				$requete = $this->TableauDonneesParent->FournisseurDonnees->OuvreRequeteSelectElements($this->TableauDonneesParent->FiltresSelection) ;
				$this->EnvoieEntete() ;
				$this->TotalLignes = 0 ;
				while($ligne = $this->TableauDonneesParent->FournisseurDonnees->LitRequete($requete))
				{
					$valeurs = $this->TableauDonneesParent->ExtraitValeursExport($ligne, $this) ;
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
					echo $this->ExprAvantValeur.$valeur.$this->ExprApresValeur ;
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