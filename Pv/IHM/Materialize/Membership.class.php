<?php
	
	if(! defined('PV_MEMBERSHIP_MATERIALIZE'))
	{
		define('PV_MEMBERSHIP_MATERIALIZE', 1) ;
		
		class PvRemplisseurConfigMembershipMaterialize extends PvRemplisseurConfigMembershipSimple
		{
			public function CreeFormulaireDonnees()
			{
				return new PvFormulaireDonneesMaterialize() ;
			}
		}
		
		class PvScriptConnexionMaterialize extends PvScriptConnexionWeb
		{
			public $MessageRecouvreMP = '<p>Mot de passe oubli&eacute; ? <a href="${url}">Cliquez ici</a> pour le r&eacute;cup&eacute;rer</p>' ;
			public $MessageInscription = '<p>Si vous n\'avez pas de compte, <a href="${url}">Inscrivez-vous</a>.</p>' ;
			public $ColXsLibelle = 5 ;
			public $TagTitre = 'h3' ;
			public $InclureIcones = 0 ;
			public $ClasseCSSCadre = "col l6 s12 m10 xl6" ;
			public $ClasseCSSErreur = 'card-panel red lighten-4' ;
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<form class="user_login_box '.$this->ClasseCSSCadre.' '.$this->NomClsCSSFormulaireDonnees.'" action="'.$this->UrlSoumetTentativeConnexion().'" method="post">'.PHP_EOL ;
				$ctn .= '<div class="card-panel">'.PHP_EOL ;
				$ctn .= $this->RenduMessageErreur() ;
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<input type="submit" value="'.$this->LibelleBoutonSoumettre.'" class="btn btn-lg btn-success btn-block" />'.PHP_EOL ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				if($this->ZoneParent->AutoriserInscription == 1 && $this->ZoneParent->EstPasNul($this->ZoneParent->ScriptInscription))
				{
					if($this->AutoriserUrlsRetour == 1 && $this->ZoneParent->ScriptInscription->AutoriserUrlsRetour == 1)
					{
						$this->ParamsUrlInscription[$this->ZoneParent->ScriptInscription->NomParamUrlRetour] = $this->ValeurUrlRetour ;
					}
					$ctn .= _parse_pattern($this->MessageInscription, array("url" => $this->ZoneParent->ScriptInscription->ObtientUrlParam($this->ParamsUrlInscription))) ;
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
				$ctn .= '<div class="row">
<div class="input-field">
<input class="validate" id="login" name="'.$this->NomParamPseudo.'" type="text" value="'.htmlspecialchars($this->ValeurParamPseudo).'" autofocus />
<label for="login">'.$this->LibellePseudo.'</label>
</div>
<div class="input-field">
<input class="form-control" name="'.$this->NomParamMotPasse.'" id="password" type="password" value="" />
<label for="password">'.$this->LibelleMotPasse.'</label>
</div>
</div>' ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
				return $ctn ;
			}
		}
		class PvScriptRecouvreMPMaterialize extends PvScriptRecouvreMPWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireRecouvreMPMaterialize" ;
		}
		class PvScriptDeconnexionMaterialize extends PvScriptDeconnexionWeb
		{
			public $TagTitre = "h3" ;
			public function RenduSpecifique()
			{
				$ctnForm = parent::RenduSpecifique() ;
				$ctn = '<div class="panel panel-default">
<div class="panel-body">
'.$ctnForm.'
</div>
</div>' ;
				return $ctn ;
			}
		}
		
		class PvTableauMembresMaterialize extends PvTableauMembresMSHtml
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
							$ctn .= '<form action="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&'.http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
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
		class PvTableauRolesMaterialize extends PvTableauRolesMSHtml
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
							$ctn .= '<form action="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&'.http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
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
		class PvTableauProfilsMaterialize extends PvTableauProfilsMSHtml
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
							$ctn .= '<form action="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&'.http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
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
			
		class PvFormulaireAjoutMembreMaterialize extends PvFormulaireAjoutMembreMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireInscriptionMembreMaterialize extends PvFormulaireInscriptionMembreMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireModifMembreMaterialize extends PvFormulaireModifMembreMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireModifInfosMaterialize extends PvFormulaireModifInfosMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireSupprMembreMaterialize extends PvFormulaireSupprMembreMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireChangeMPMembreMaterialize extends PvFormulaireChangeMPMembreMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireDoitChangerMotPasseMaterialize extends PvFormulaireDoitChangerMotPasseMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireChangeMotPasseMaterialize extends PvFormulaireChangeMotPasseMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		
		class PvFormulaireAjoutRoleMaterialize extends PvFormulaireAjoutRoleMS
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
			protected function DeclareCompListeProfils()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstProfils = & $this->FiltreListeProfilsRole ;
				$comp = $filtreLstProfils->DeclareComposant("PvZoneBoiteOptionsCocherBootstrap") ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireModifRoleMaterialize extends PvFormulaireModifRoleMS
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
			protected function DeclareCompListeProfils()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstProfils = & $this->FiltreListeProfilsRole ;
				$comp = $filtreLstProfils->DeclareComposant("PvZoneBoiteOptionsCocherBootstrap") ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireSupprRoleMaterialize extends PvFormulaireSupprRoleMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		
		class PvFormulaireAjoutProfilMaterialize extends PvFormulaireAjoutProfilMS
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
			protected function DeclareCompListeRoles()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstRoles = & $this->FiltreListeRolesProfil ;
				$comp = $filtreLstRoles->DeclareComposant("PvZoneBoiteOptionsCocherBootstrap") ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireModifProfilMaterialize extends PvFormulaireModifProfilMS
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
			protected function DeclareCompListeRoles()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstRoles = & $this->FiltreListeRolesProfil ;
				$comp = $filtreLstRoles->DeclareComposant("PvZoneBoiteOptionsCocherBootstrap") ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		class PvFormulaireSupprProfilMaterialize extends PvFormulaireSupprProfilMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}

		class PvFormulaireRecouvreMPMaterialize extends PvFormulaireRecouvreMPMS
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesMaterialize() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesMaterialize() ;
			}
		}
		
		class PvScriptAjoutMembreMaterialize extends PvScriptAjoutMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutMembreMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptInscriptionMaterialize extends PvScriptInscriptionWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireInscriptionMembreMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifMembreMaterialize extends PvScriptModifMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifMembreMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprMembreMaterialize extends PvScriptSupprMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprMembreMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifPrefsMaterialize extends PvScriptModifPrefsWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifInfosMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptChangeMotPasseMaterialize extends PvScriptChangeMotPasseWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMotPasseMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptDoitChangerMotPasseMaterialize extends PvScriptDoitChangerMotPasseWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireDoitChangerMotPasseMaterialize" ;
		}
		class PvScriptChangeMPMembreMaterialize extends PvScriptChangeMPMembreWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMPMembreMaterialize" ;
		}
		class PvScriptListeMembresMaterialize extends PvScriptListeMembresMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauMembresMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptAjoutProfilMaterialize extends PvScriptAjoutProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutProfilMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifProfilMaterialize extends PvScriptModifProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifProfilMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprProfilMaterialize extends PvScriptSupprProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprProfilMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeProfilsMaterialize extends PvScriptListeProfilsMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauProfilsMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptAjoutRoleMaterialize extends PvScriptAjoutRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutRoleMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifRoleMaterialize extends PvScriptModifRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifRoleMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprRoleMaterialize extends PvScriptSupprRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprRoleMaterialize" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeRolesMaterialize extends PvScriptListeRolesMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauRolesMaterialize" ;
			public $TagTitre = "h3" ;
		}
	}
	
?>