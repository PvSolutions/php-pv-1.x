<?php
	
	if(! defined('PV_TABLEAU_DONNEES_SB_ADMIN2'))
	{
		define('PV_TABLEAU_DONNEES_SB_ADMIN2', 1) ;
		
		class PvCfgFmtColOuvreBoiteDlgUrl extends PvConfigFormatteurColonneLien
		{
			public $FormatTitreDlg ;
			public $LargeurDlg = null ;
			public $HauteurDlg = null ;
			public $RafraichPageSurFerm = null ;
			protected function RenduBrut($donnees)
			{
				$donneesUrl = array_map("urlencode", $donnees) ;
				$href = _parse_pattern($this->FormatURL, $donneesUrl) ;
				$libelle = _parse_pattern($this->FormatLibelle, $donnees) ;
				$titreDlg = _parse_pattern($this->FormatTitreDlg, $donnees) ;
				$scriptJs = 'BoiteDlgUrl.ouvre('.svc_json_encode($titreDlg).', '.svc_json_encode($href).', '.svc_json_encode($this->LargeurDlg).', '.svc_json_encode($this->HauteurDlg).', '.svc_json_encode($this->RafraichPageSurFerm).')' ;
				$ctn = '' ;
				$ctn .= '<a href="javascript:'.htmlentities($scriptJs).'"' ;
				if($this->ChaineAttributs != '')
				{
					$ctn .= ' '.$this->ChaineAttributs ;
				}
				if($this->ClasseCSS != '')
				{
					$ctn .= ' class="'.$this->ClasseCSS.'"' ;
				}
				$ctn .= '>' ;
				$ctn .= $this->RenduIcone($donnees, $donneesUrl) ;
				if($this->EncodeHtmlLibelle)
				{
					$libelle = htmlentities($libelle) ;
				}
				$ctn .= $libelle ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
		}
		
		class PvTableauDonneesSbAdmin2 extends PvTableauDonneesHtml
		{
			public $ClsBstFormFiltresEdition = "col-lg-6" ;
			public $ClsBstBtnNavRangeeDonnees = "btn-primary" ;
			public $PrefxBstGrilleFiltresEdition = "col-lg" ;
			public $ClasseBstRangeeDonnees = "table-striped table-bordered table-hover" ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsFaTriAsc = "fa-chevron-up" ;
			public $ClsFaTriDesc = "fa-chevron-down" ;
			public $ClsFaTriAscSelectionne = "fa-arrow-circle-up" ;
			public $ClsFaTriDescSelectionne = "fa-arrow-circle-down" ;
			public function CreeLienOuvreBoiteDlgUrl()
			{
				return new PvCfgFmtColOuvreBoiteDlgUrl() ;
			}
			public function InsereLienOuvreBoiteDlgUrl(& $col, $formatUrl='', $formatLib='', $formatTitreDlg='', $largeur=null, $hauteur=null, $rafraichPageSurFerm=null)
			{
				$lien = null ;
				if($this->EstNul($col) || $col->Formatteur == null)
				{
					return $lien ;
				}
				$lien = $this->CreeLienOuvreBoiteDlgUrl() ;
				$lien->FormatURL = $formatUrl ;
				$lien->FormatLibelle = $formatLib ;
				$lien->FormatTitreDlg = $formatTitreDlg ;
				$lien->LargeurDlg = $largeur ;
				$lien->HauteurDlg = $hauteur ;
				$col->Formatteur->Liens[] = & $lien ;
				return $col ;
			}
			protected function InitDessinateurFiltresSelection()
			{
				$this->DessinateurFiltresSelection = new PvDessinFiltresSbAdmin2() ;
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
				$this->DessinateurFiltresSelection->PrefxBstGrilleFiltres = $this->PrefxBstGrilleFiltresEdition ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresEdition.'">'.PHP_EOL ;
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this) ;" role="form">'.PHP_EOL ;
				$ctn .= '<div class="panel panel-default">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="panel-heading" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresSelection) ;
				return $ctn ;
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
						$libelleTriAsc = '<i class="fa '.$this->ClsFaTriAsc.'"></i>' ;
						$libelleTriDesc = '<i class="fa '.$this->ClsFaTriDesc.'"></i>' ;
						$libelleTriAscSelectionne = '<i class="fa '.$this->ClsFaTriAscSelectionne.'"></i>' ;
						$libelleTriDescSelectionne = '<i class="fa '.$this->ClsFaTriDescSelectionne.'"></i>' ;
					}
					$parametresRendu = $this->ParametresCommandeSelectionnee() ;
					if(count($this->ElementsEnCours) > 0)
					{
						$ctn .= '<table' ;
						$ctn .= ' class="RangeeDonnees table '.$this->ClasseBstRangeeDonnees.'"' ;
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<thead><tr class="Entete">'.PHP_EOL ;
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
						$ctn .= '</tr></thead>'.PHP_EOL ;
						$ctn .= '<tbody>'.PHP_EOL ;
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
								$ctn .= $colonne->FormatteValeur($this, $ligne) ;
								$ctn .= '</td>'.PHP_EOL ;
							}
							$ctn .= '</tr>'.PHP_EOL ;
						}
						$ctn .= '</tbody>'.PHP_EOL ;
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
				$ctn .= '<a class="btn '.$this->ClsBstBtnNavRangeeDonnees.'" href="javascript:'.$this->AppelJsEnvoiFiltres($paramPremiereRangee).'" title="'.$this->TitrePremiereRangee.'">'.$this->LibellePremiereRangee.'</a>'.PHP_EOL ;
				$ctn .= $this->SeparateurLiensRangee ;
				if($this->RangeeEnCours > 0)
				{
					$paramRangeePrecedente = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => ($this->RangeeEnCours - 1) * $this->MaxElements)) ;
					$ctn .= '<a class="btn '.$this->ClsBstBtnNavRangeeDonnees.'" href="javascript:'.$this->AppelJsEnvoiFiltres($paramRangeePrecedente).'" title="'.$this->TitreRangeePrecedente.'">'.$this->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a class="btn disabled '.$this->ClsBstBtnNavRangeeDonnees.'" title="'.$this->TitreRangeePrecedente.'">'.$this->LibelleRangeePrecedente.'</a>'.PHP_EOL ;
				}
				$ctn .= $this->SeparateurLiensRangee ;
				$ctn .= '<input type="text" size="4" onChange="var nb = 0 ; try { nb = parseInt(this.value) ; } catch(ex) { } if (isNaN(nb) == true) { nb = 0 ; } SoumetEnvoiFiltres'.$this->IDInstanceCalc.'({'.htmlentities(svc_json_encode($this->NomParamIndiceDebut())).' : (nb - 1) * '.$this->MaxElements.'}) ;" value="'.($this->RangeeEnCours + 1).'" style="text-align:center" />'.PHP_EOL ;
				$ctn .= $this->SeparateurLiensRangee ;
				//echo $this->RangeeEnCours." &lt; ".(intval($this->TotalElements / $this->MaxElements) - 1) ;
				if($this->RangeeEnCours < intval($this->TotalElements / $this->MaxElements))
				{
					$paramRangeeSuivante = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => ($this->RangeeEnCours + 1) * $this->MaxElements)) ;
					$ctn .= '<a class="btn '.$this->ClsBstBtnNavRangeeDonnees.'" href="javascript:'.$this->AppelJsEnvoiFiltres($paramRangeeSuivante).'" title="'.$this->TitreRangeeSuivante.'">'.$this->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<a class="btn disabled '.$this->ClsBstBtnNavRangeeDonnees.'" title="'.$this->TitreRangeeSuivante.'">'.$this->LibelleRangeeSuivante.'</a>'.PHP_EOL ;
				}
				$paramDerniereRangee = array_merge($parametresRendu, array($this->NomParamIndiceDebut() => intval($this->TotalElements / $this->MaxElements) * $this->MaxElements)) ;
				$ctn .= $this->SeparateurLiensRangee ;
				$ctn .= '<a class="btn '.$this->ClsBstBtnNavRangeeDonnees.'" href="javascript:'.$this->AppelJsEnvoiFiltres($paramDerniereRangee).'" title="'.$this->TitreDerniereRangee.'" class="btn">'.$this->LibelleDerniereRangee.'</a>'.PHP_EOL ;
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
		}
	}

?>