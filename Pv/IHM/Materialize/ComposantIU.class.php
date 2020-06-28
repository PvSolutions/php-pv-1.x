<?php

	if(! defined('PV_COMPOSANT_IU_MATERIALIZE'))
	{
		define('PV_COMPOSANT_IU_MATERIALIZE', 1) ;
		
		class PvDessinFiltresDonneesMaterialize extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public $ColS = "" ;
			public $ColM = "" ;
			public $ColL = "" ;
			public $ColXl = "" ;
			public $InclureRenduLibelle = 1 ;
			public $MaxFiltresParLigne = 1 ;
			protected function ObtientColS($maxFiltres)
			{
				return ($this->ColS != '') ? $this->ColS :
					(($this->ColXl != '') ? $this->ColXl : 
						(($this->ColL != '') ? $this->ColL : 
							($this->ColM != '') ? $this->ColM : intval(12 / $maxFiltres)
						)
					) ;
			}
			protected function RenduFiltre(& $filtre, & $composant)
			{
				$ctn = '' ;
				if($composant->Editable)
				{
					if($filtre->EstNul($filtre->Composant))
					{
						$filtre->DeclareComposant($filtre->NomClasseComposant) ;
					}
					$ctn .= $filtre->Rendu() ;
				}
				else
				{
					$ctn .= $filtre->Etiquette() ;
				}
				return $ctn ;
			}
			public function Execute(& $script, & $composant, $parametres)
			{
				$filtres = $composant->ExtraitFiltresDeRendu($parametres, $this->FiltresCaches) ;
				$ctn = '' ;
				if($this->MaxFiltresParLigne <= 0)
				{
					$this->MaxFiltresParLigne = 1 ;
				}
				$colS = $this->ObtientColS($this->MaxFiltresParLigne) ;
				$maxColonnes = 12 / $colS ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $filtres[$nomFiltre] ;
					if($filtre->LectureSeule)
					{
						$ctn .= '<input type="hidden" id="'.htmlspecialchars($filtre->ObtientIDComposant()).'" name="'.htmlspecialchars($filtre->ObtientNomComposant()).'" value="'.htmlspecialchars($filtre->Lie()).'" />'.PHP_EOL ;
						continue ;
					}
					$ctn .= '<div class="input-field col s'.$colS.(($this->ColM != '') ? ' m'.$this->ColM : '').''.(($this->ColL != '') ? ' l'.$this->ColL : '').(($this->ColXl != '') ? ' xl'.$this->ColXl : '').'">'.PHP_EOL ;
					$ctn .= $this->RenduFiltre($filtre, $composant).PHP_EOL ;
					if($this->InclureRenduLibelle)
					{
						$ctn .= $this->RenduLibelleFiltre($filtre).PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
					$filtreRendus++ ;
				}
				return $ctn ;
			}
		}
		class PvDessinCommandesMaterialize extends PvDessinateurRenduHtmlCommandes
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$commandes = $parametres ;
				$nomCommandes = array_keys($commandes) ;
				foreach($nomCommandes as $i => $nomCommande)
				{
					$commande = & $commandes[$nomCommande] ;
					if($this->PeutAfficherCmd($commande) == 0)
					{
						continue ;
					}
					if($ctn != '')
					{
						$ctn .= $this->SeparateurCommandes. PHP_EOL ;
					}
					if($commande->UtiliserRenduDispositif)
					{
						$ctn .= $commande->RenduDispositif() ;
					}
					else
					{
						$ctn .= $this->DebutExecParam($script, $composant, $i, $commande) ;
						if($commande->ContenuAvantRendu != '')
						{
							$ctn .= $commande->ContenuAvantRendu ;
						}
						$classeBtn = $commande->ObtientValSuppl("classe-btn", "btn-primary") ;
						$ctn .= '<button id="'.$commande->IDInstanceCalc.'" class="Commande waves-effect waves-light btn '.$commande->NomClsCSS.' '.$classeBtn.'" type="submit" rel="'.$commande->NomElementSousComposantIU.'"' ;
						$contenuJsSurClick = ($commande->ContenuJsSurClick == '') ? $composant->IDInstanceCalc.'_ActiveCommande(this) ;' : $commande->ContenuJsSurClick.' ; return false ;' ;
						$ctn .= ' onclick="'.$contenuJsSurClick.'"' ;
						if($this->InclureLibelle == 0)
						{
							$ctn .= ' title="'.htmlspecialchars($commande->Libelle).'"' ;
						}
						$ctn .= '>'.PHP_EOL ;
						if($this->InclureLibelle)
						{
							$ctn .= $commande->Libelle ;
						}
						$ctn .= '</button>'.PHP_EOL ;
						if($commande->ContenuApresRendu != '')
						{
							$ctn .= $commande->ContenuApresRendu ;
						}
						$ctn .= $this->FinExecParam($script, $composant, $i, $commande) ;
					}
				}
				return $ctn ;
			}
		}
		
		class PvFormulaireDonneesMaterialize extends PvFormulaireDonneesHtml
		{
			public $ClasseCSSSucces = "card-panel teal lighten-4" ;
			public $ClasseCSSErreur = "card-panel red lighten-4" ;
			public $ClasseCSSCommandeExecuter = "" ;
			public $ClasseCSSCommandeAnnuler = "red" ;
			public $ClasseCSSBlocCommandes = "row" ;
			public $UtiliserLargeur = 0 ;
			public $ClasseCSSLargeur = 's12 m8 l6 xl6' ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
			protected function RenduComposants()
			{
				$ctn = '' ;
				if(count($this->DispositionComposants))
				{
					$ctn .= '<div class="row">
<div class="col '.$this->ClasseCSSLargeur.'">
<div class="card-panel">'.PHP_EOL ;
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
					$ctn .= '</form>'.PHP_EOL ;
					$ctn .= '</div>
</div>
</div>' ;
				}
				return $ctn ;
			}
		}
		
		class PvTableauDonneesMaterialize extends PvTableauDonneesHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $ClasseCSSTitre = "teal lighten-4" ;
			public $ClasseCSSRangee = "highlight" ;
			public $ClasseCSSBtnNav = "waves-effect waves-light btn" ;
			public $ClsBstBoutonSoumettre = "waves-effect waves-light btn" ;
			public $ClsBstFormFiltresSelect = "col s12 m10 l8 xl6" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesMaterialize() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
				$this->NavigateurRangees = new PvNavTableauDonneesMaterialize() ;
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
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this) ;">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresSelect.'">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="card-panel '.$this->ClasseCSSTitre.'" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="card-panel">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
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
					if($this->ZoneParent->InclureFontAwesome == 1)
					{
						$libelleTriAsc = '<span data-fa-transform="up-4" class="text-muted fa fa-sort-up" title="'.htmlspecialchars($this->LibelleTriAsc).'"></span>' ;
						$libelleTriDesc = '<span data-fa-transform="down-4" class="text-muted fa fa-sort-down" title="'.htmlspecialchars($this->LibelleTriDesc).'"></span>' ;
						$libelleTriAscSelectionne = '<span data-fa-transform="up-4" class="grey-text fa fa-sort-up" title="'.htmlspecialchars($this->LibelleTriAsc).'"></span>' ;
						$libelleTriDescSelectionne = '<span data-fa-transform="down-4" class="grey-text fa fa-sort-down" title="'.htmlspecialchars($this->LibelleTriDesc).'"></span>' ;
					}
					else
					{
						$libelleTriAsc = '<span title="'.htmlspecialchars($this->LibelleTriAsc).'">Asc</span>' ;
						$libelleTriDesc = '<span title="'.htmlspecialchars($this->LibelleTriDesc).'">Desc</span>' ;
						$libelleTriAscSelectionne = '<span class="grey-text" title="'.htmlspecialchars($this->LibelleTriAsc).'">Asc</span>' ;
						$libelleTriDescSelectionne = '<span class="grey-text" title="'.htmlspecialchars($this->LibelleTriDesc).'">Desc</span>' ;
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
							$ctn .= '<form action="?'.(($this->ZoneParent->ActiverRoutes == 0) ? urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&' : '').http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
							$ctn .= $ctnChampsPost ;
						}
						$ctn .= '<table' ;
						$ctn .= ' class="RangeeDonnees responsive-table '.$this->ClasseCSSRangee.'"' ;
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<thead>'.PHP_EOL ;
						$ctn .= '<tr class="Entete">'.PHP_EOL ;
						foreach($this->DefinitionsColonnes as $i => $colonne)
						{
							if(! $colonne->EstVisible($this->ZoneParent))
								continue ;
							$triPossible = ($this->TriPossible && $colonne->TriPossible) ;
							$ctn .= '<th' ;
							if($colonne->Largeur != "")
							{
								$ctn .= ' width="'.$colonne->Largeur.'"' ;
							}
							if($colonne->AlignEntete != "")
							{
								$ctn .= ' align="'.$colonne->AlignEntete.'"' ;
							}
							$ctn .= '>' ;
							$ctn .= $colonne->ObtientLibelle() ;
							if($triPossible)
							{
								$selectionne = ($this->IndiceColonneTri == $i && $this->SensColonneTri == "asc") ;
								$paramColAsc = array_merge($parametresRendu, array($this->NomParamSensColonneTri() => "asc", $this->NomParamIndiceColonneTri() => $i, $this->NomParamIndiceDebut() => 0)) ;
								$ctn .= ' <a href="javascript:'.$this->AppelJsEnvoiFiltres($paramColAsc).'"'.(($selectionne) ? ' class="ColonneTriee"' : '').'>' ;
								$ctn .= (($selectionne && $libelleTriAscSelectionne != "") ? $libelleTriAscSelectionne : $libelleTriAsc) ;
								$ctn .= '</a>' ;
								$selectionne = ($this->IndiceColonneTri == $i && $this->SensColonneTri == "desc") ;
								$paramColAsc = array_merge($parametresRendu, array($this->NomParamSensColonneTri() => "desc", $this->NomParamIndiceColonneTri() => $i, $this->NomParamIndiceDebut() => 0)) ;
								$ctn .= ' <a href="javascript:'.$this->AppelJsEnvoiFiltres($paramColAsc).'"'.(($selectionne) ? ' class="ColonneTriee"' : '').'>' ;
								$ctn .= (($selectionne && $libelleTriDescSelectionne != "") ? $libelleTriDescSelectionne : $libelleTriDesc) ;
								$ctn .= '</a>' ;
							}
							$ctn .= '</th>'.PHP_EOL ;
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
						if($this->PossedeColonneEditable())
						{
							$ctn .= PHP_EOL .'<div style="display:none"><input type="submit" /></div>
</form>' ;
						}
					}
					else
					{
						$ctn .= '<div class="AucunElement card-panel teal lighten-4">'.$this->MessageAucunElement.'</div>' ;
					}
				}
				else
				{
					$ctn .= $this->RenduFiltresNonRenseignes() ;
				}
				return $ctn ;
			}
		}
		class PvGrilleDonneesMaterialize extends PvGrilleDonneesHtml
		{
			public $ClasseCSSRangee = "striped" ;
			public $ClasseCSSCellule = "" ;
			public $ClasseCSSBtnNav = "waves-effect waves-light btn" ;
			public $ClsBstBoutonSoumettre = "waves-effect waves-light btn" ;
			public $ClsBstFormFiltresSelect = "col s12 m10 l8 xl6" ;
			public $SautLigneSansCommande = 0 ;
			public $MaxColonnesXs = 0 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesMaterialize() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
				$this->NavigateurRangees = new PvNavTableauDonneesMaterialize() ;
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
				return $ctn ;
			}
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
						$ctn .= ' class="RangeeDonnees"' ;
						if($this->Largeur != "")
						{
							$ctn .= ' width="'.$this->Largeur.'"' ;
						}
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<tr><td'.(($this->AlignVCellule != '') ? ' valign="'.$this->AlignVCellule.'"' : '').'><div class="container-fluid">'.PHP_EOL ;
						$inclureLargCell = 1 ;
						$maxColsXs = ($this->MaxColonnesXs > 0) ? $this->MaxColonnesXs : $this->MaxColonnes ;
						$colS = 12 / $maxColsXs ;
						$colDef = 12 / $this->MaxColonnes ;
						foreach($this->ElementsEnCours as $j => $ligne)
						{
							if($this->MaxColonnes <= 1 || $j % $this->MaxColonnes == 0)
							{
								$ctn .= '<div class="row">'.PHP_EOL ;
							}
							$ctn .= '<div class="Contenu col-'.$colS.' col-sm-'.$colDef.(($this->ClasseCSSCellule != '') ? ' '.$this->ClasseCSSCellule : '').'"' ;
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
							$ctn .= '<div class="col-'.$colFusionnees.'"></div>'.PHP_EOL ;
							$ctn .= '</div>'.PHP_EOL ;
						}
						$ctn .= '</div></td></tr>' ;
						$ctn .= '</table>' ;
					}
					elseif($this->AlerterAucunElement == 1)
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
		
		class PvNavTableauDonneesMaterialize extends PvNavigateurRangeesDonneesBase
		{
			public $MaxRangeesPrec = 3 ;
			public $MaxRangeesSuiv = 3 ;
			public function Execute(& $script, & $composant)
			{
				return $this->ExecuteInstructions($script, $composant) ;
			}
			protected function ExecuteInstructions(& $script, & $composant)
			{
				$ctn = '' ;
				$parametresRendu = $composant->ParametresRendu() ;
				$ctn .= '<ul class="pagination">'.PHP_EOL ;
				$paramPremiereRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => 0)) ;
				$ctn .= '<li class="waves-effect"><a href="javascript:'.$composant->AppelJsEnvoiFiltres($paramPremiereRangee).'" title="'.$composant->TitrePremiereRangee.'">'.$composant->LibellePremiereRangee.'</a></li>'.PHP_EOL ;
				if($composant->RangeeEnCours - $this->MaxRangeesPrec > 1)
				{
					$ctn .= '<li class="waves-effect"><a href="javascript:'.$composant->AppelJsEnvoiFiltres($paramPremiereRangee).'" title="'.$composant->TitrePremiereRangee.'">1</a></li>'.PHP_EOL ;
				}
				if($composant->RangeeEnCours > 0)
				{
					if($composant->RangeeEnCours - $this->MaxRangeesPrec > 0)
					{
						$ctn .= '<li class="waves-effect"><a href="javascript:;" title="'.$composant->TitrePremiereRangee.'">...</a></li>' ;
					}
					for($i=$composant->RangeeEnCours - $this->MaxRangeesPrec; $i<$composant->RangeeEnCours; $i++)
					{
						$rangeeEnCours = $i ;
						if($rangeeEnCours < 0)
						{
							continue ;
						}
						$paramRangeePrecedente = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => ($rangeeEnCours) * $composant->MaxElements)) ;
						$ctn .= '<li class="waves-effect"><a href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangeePrecedente).'" title="'.($rangeeEnCours + 1).'">'.($rangeeEnCours + 1).'</a></li>'.PHP_EOL ;
					}
				}
				$paramRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => ($composant->RangeeEnCours) * $composant->MaxElements)) ;
				$ctn .= '<li class="waves-effect active"><a href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangee).'" title="'.($composant->RangeeEnCours + 1).'">'.($composant->RangeeEnCours + 1).'</a></li>'.PHP_EOL ;
				if($composant->RangeeEnCours < $composant->TotalRangees - 1)
				{
					for($i=$composant->RangeeEnCours + 1; $i<$composant->RangeeEnCours + $this->MaxRangeesSuiv + 1 && $i < $composant->TotalRangees; $i++)
					{
						$rangeeEnCours = $i ;
						if($rangeeEnCours >= $composant->TotalRangees)
						{
							break ;
						}
						$paramRangeeSuivante = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => ($rangeeEnCours) * $composant->MaxElements)) ;
						$ctn .= '<li class="waves-effect"><a href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangeeSuivante).'" title="'.($rangeeEnCours + 1).'">'.($rangeeEnCours + 1).'</a></li>'.PHP_EOL ;
					}
					if($composant->RangeeEnCours + $this->MaxRangeesSuiv < $composant->TotalRangees - 1)
					{
						$ctn .= '<li class="waves-effect"><a href="javascript:;" title="">...</a></li>' ;
					}
				}
				$paramDerniereRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => intval($composant->TotalElements / $composant->MaxElements) * $composant->MaxElements)) ;
				if($composant->RangeeEnCours + $this->MaxRangeesSuiv < $composant->TotalRangees - 1)
				{
					$ctn .= '<li class="waves-effect"><a href="javascript:'.$composant->AppelJsEnvoiFiltres($paramDerniereRangee).'" title="'.$composant->TitreDerniereRangee.'">'.($composant->TotalRangees).'</a></li>'.PHP_EOL ;
				}
				$ctn .= '<li class="waves-effect"><a href="javascript:'.$composant->AppelJsEnvoiFiltres($paramDerniereRangee).'" title="'.$composant->TitreDerniereRangee.'">'.$composant->LibelleDerniereRangee.'</a></li>'.PHP_EOL ;
				$ctn .= '</ul>'.PHP_EOL ;
				return $ctn ;
				return $ctn ;
			}
		}
	}

?>