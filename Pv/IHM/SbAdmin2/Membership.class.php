<?php
	
	if(! defined('PV_MEMBERSHIP_SB_ADMIN2'))
	{
		define('PV_MEMBERSHIP_SB_ADMIN2', 1) ;
		
		class PvScriptConnexionSbAdmin2 extends PvScriptConnexionWeb
		{
			public $NomDocumentWeb = "non_connecte" ;
			public $MessageRecouvreMP = '<br><p>Nouveau ? <a href="${url}">Inscrivez-vous</a></p>' ;
			public $MessageInscription = '<br><p>Mot de passe oubli&eacute; ? <a href="${url}">Cliquez ici</a> pour le r&eacute;cup&eacute;rer</p>' ;
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<form class="user_login_box '.$this->NomClsCSSFormulaireDonnees.'" action="'.$this->ObtientUrl().'" role="form" method="post">'.PHP_EOL ;
				if($this->TentativeConnexionEnCours && $this->TentativeConnexionValidee == 0)
				{
					$ctn .= '<div class="erreur alert alert-danger alert-dismissable">'.$this->MessageConnexionEchouee.'</div>'.PHP_EOL ;
				}
				$ctn .= '<fieldset>'.PHP_EOL ;
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<input type="submit" value="'.$this->LibelleBoutonSoumettre.'" class="btn btn-lg btn-success btn-block" />'.PHP_EOL ;
				}
				$ctn .= '</fieldset>'.PHP_EOL ;
				$ctn .= '</form>' ;
				if($this->ZoneParent->AutoriserInscription == 1 && $this->ZoneParent->EstPasNul($this->ZoneParent->ScriptInscription))
				{
					$ctn .= _parse_pattern($this->MessageInscription, array("url" => $this->ZoneParent->ScriptRecouvreMP->ObtientUrlParam($this->ParamsUrlInscription))) ;
				}
				if($this->ZoneParent->EstPasNul($this->ZoneParent->ScriptRecouvreMP))
				{
					$ctn .= _parse_pattern($this->MessageRecouvreMP, array("url" => $this->ZoneParent->ScriptRecouvreMP->ObtientUrlParam($this->ParamsUrlRecouvreMP))) ;
				}
				return $ctn ;
			}
			public function RenduTableauParametres()
			{
				$ctn = '' ;
				$ctn .= '<div class="form-group">
<input class="form-control" placeholder="'.htmlspecialchars($this->LibellePseudo).'" name="'.$this->NomParamPseudo.'" type="text" value="'.htmlspecialchars($this->ValeurParamPseudo).'" autofocus>
</div>
<div class="form-group">
<input class="form-control" placeholder="'.htmlspecialchars($this->LibelleMotPasse).'" name="'.$this->NomParamMotPasse.'" type="password" value="">
</div>' ;
				/*
				$ctn .= '<div class="checkbox">
<label>
<input name="remember" type="checkbox" value="Remember Me">Remember Me
</label>
</div>' ;
				*/
				$ctn .= '<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
				return $ctn ;
			}
		}
		class PvScriptRecouvreMPSbAdmin2 extends PvScriptRecouvreMPWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireRecouvreMPSbAdmin2" ;
			public $NomDocumentWeb = "non_connecte" ;
		}
		class PvScriptDeconnexionSbAdmin2 extends PvScriptDeconnexionWeb
		{
			public $NomDocumentWeb = "non_connecte" ;
		}
		
		class PvTableauMembresSbAdmin2 extends PvTableauMembresMSHtml
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
		class PvTableauRolesSbAdmin2 extends PvTableauRolesMSHtml
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
		class PvTableauProfilsSbAdmin2 extends PvTableauProfilsMSHtml
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
			
		class PvFormulaireAjoutMembreSbAdmin2 extends PvFormulaireAjoutMembreMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		class PvFormulaireInscriptionMembreSbAdmin2 extends PvFormulaireInscriptionMembreMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		class PvFormulaireModifMembreSbAdmin2 extends PvFormulaireModifMembreMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		class PvFormulaireModifInfosSbAdmin2 extends PvFormulaireModifInfosMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		class PvFormulaireSupprMembreSbAdmin2 extends PvFormulaireSupprMembreMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		class PvFormulaireChangeMPMembreSbAdmin2 extends PvFormulaireChangeMPMembreMS
		{
			public $MaxFiltresEditionParLigne = 1 ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		class PvFormulaireDoitChangerMotPasseSbAdmin2 extends PvFormulaireDoitChangerMotPasseMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		class PvFormulaireChangeMotPasseSbAdmin2 extends PvFormulaireChangeMotPasseMS
		{
			public $MaxFiltresEditionParLigne = 1 ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		
		class PvFormulaireAjoutRoleSbAdmin2 extends PvFormulaireAjoutRoleMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
			protected function DeclareCompListeProfils()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstProfils = & $this->FiltreListeProfilsRole ;
				$comp = $filtreLstProfils->DeclareComposant("PvZoneBoiteOptionsCocherSbAdmin2") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $form->InclureElementEnCours)
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForNewRole().")" ;
				}
				else
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForRole().")" ;
					$filtreIdRole = $form->ScriptParent->CreeFiltreHttpGet("idRole") ;
					$filtreIdRole->Obligatoire = 1 ;
					$filtreIdRole->ExpressionDonnees = 'ROLE_ID = <self>' ;
					$comp->FiltresSelection[] = $filtreIdRole ;
				}
				$comp->NomColonneValeur = "PROFILE_ID" ;
				$comp->NomColonneLibelle = "PROFILE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->DeclareCompListeProfils() ;
			}
		}
		class PvFormulaireModifRoleSbAdmin2 extends PvFormulaireModifRoleMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
			protected function DeclareCompListeProfils()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstProfils = & $this->FiltreListeProfilsRole ;
				$comp = $filtreLstProfils->DeclareComposant("PvZoneBoiteOptionsCocherSbAdmin2") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForRole().")" ;
				$filtreIdRole = $this->ScriptParent->CreeFiltreHttpGet("idRole") ;
				$filtreIdRole->Obligatoire = 1 ;
				$filtreIdRole->ExpressionDonnees = 'ROLE_ID = <self>' ;
				$comp->FiltresSelection[] = $filtreIdRole ;
				$comp->NomColonneValeur = "PROFILE_ID" ;
				$comp->NomColonneLibelle = "PROFILE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->DeclareCompListeProfils() ;
			}
		}
		class PvFormulaireSupprRoleSbAdmin2 extends PvFormulaireSupprRoleMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		
		class PvFormulaireAjoutProfilSbAdmin2 extends PvFormulaireAjoutProfilMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
			protected function DeclareCompListeRoles()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstRoles = & $this->FiltreListeRolesProfil ;
				$comp = $filtreLstRoles->DeclareComposant("PvZoneBoiteOptionsCocherSbAdmin2") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $this->InclureElementEnCours)
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForNewProfile().")" ;
				}
				else
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForProfile().")" ;
					$filtreIdProfil = $form->ScriptParent->CreeFiltreHttpGet("idProfil") ;
					$filtreIdProfil->Obligatoire = 1 ;
					$filtreIdProfil->ExpressionDonnees = 'PROFILE_ID = <self>' ;
					$comp->FiltresSelection[] = $filtreIdProfil ;
				}
				$comp->NomColonneValeur = "ROLE_ID" ;
				$comp->NomColonneLibelle = "ROLE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->DeclareCompListeRoles() ;
			}
		}
		class PvFormulaireModifProfilSbAdmin2 extends PvFormulaireModifProfilMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
			protected function DeclareCompListeRoles()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstRoles = & $this->FiltreListeRolesProfil ;
				$comp = $filtreLstRoles->DeclareComposant("PvZoneBoiteOptionsCocherSbAdmin2") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForProfile().")" ;
				$filtreIdProfil = $this->ScriptParent->CreeFiltreHttpGet("idProfil") ;
				$filtreIdProfil->Obligatoire = 1 ;
				$filtreIdProfil->ExpressionDonnees = 'PROFILE_ID = <self>' ;
				$comp->FiltresSelection[] = $filtreIdProfil ;
				$comp->NomColonneValeur = "ROLE_ID" ;
				$comp->NomColonneLibelle = "ROLE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->DeclareCompListeRoles() ;
			}
		}
		class PvFormulaireSupprProfilSbAdmin2 extends PvFormulaireSupprProfilMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}

		class PvFormulaireRecouvreMPSbAdmin2 extends PvFormulaireRecouvreMPMS
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
		}
		
		class PvScriptAjoutMembreSbAdmin2 extends PvScriptAjoutMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutMembreSbAdmin2" ;
		}
		class PvScriptInscriptionSbAdmin2 extends PvScriptInscriptionWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireInscriptionMembreSbAdmin2" ;
		}
		class PvScriptModifMembreSbAdmin2 extends PvScriptModifMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifMembreSbAdmin2" ;
		}
		class PvScriptSupprMembreSbAdmin2 extends PvScriptSupprMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprMembreSbAdmin2" ;
		}
		class PvScriptModifPrefsSbAdmin2 extends PvScriptModifPrefsWeb
		{
			public $NomDocumentWeb = "connecte" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireModifInfosSbAdmin2" ;
		}
		class PvScriptChangeMotPasseSbAdmin2 extends PvScriptChangeMotPasseWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMotPasseSbAdmin2" ;
			public $NomDocumentWeb = "connecte" ;
		}
		class PvScriptDoitChangerMotPasseSbAdmin2 extends PvScriptDoitChangerMotPasseWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireDoitChangerMotPasseSbAdmin2" ;
		}
		class PvScriptChangeMPMembreSbAdmin2 extends PvScriptChangeMPMembreWeb
		{
			public $NomDocumentWeb = "connecte" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMPMembreSbAdmin2" ;
		}
		class PvScriptListeMembresSbAdmin2 extends PvScriptListeMembresMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauMembresSbAdmin2" ;
		}
		class PvScriptAjoutProfilSbAdmin2 extends PvScriptAjoutProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutProfilSbAdmin2" ;
		}
		class PvScriptModifProfilSbAdmin2 extends PvScriptModifProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifProfilSbAdmin2" ;
		}
		class PvScriptSupprProfilSbAdmin2 extends PvScriptSupprProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprProfilSbAdmin2" ;
		}
		class PvScriptListeProfilsSbAdmin2 extends PvScriptListeProfilsMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauProfilsSbAdmin2" ;
		}
		class PvScriptAjoutRoleSbAdmin2 extends PvScriptAjoutRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutRoleSbAdmin2" ;
		}
		class PvScriptModifRoleSbAdmin2 extends PvScriptModifRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifRoleSbAdmin2" ;
		}
		class PvScriptSupprRoleSbAdmin2 extends PvScriptSupprRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprRoleSbAdmin2" ;
		}
		class PvScriptListeRolesSbAdmin2 extends PvScriptListeRolesMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauRolesSbAdmin2" ;
		}
	}
	
?>