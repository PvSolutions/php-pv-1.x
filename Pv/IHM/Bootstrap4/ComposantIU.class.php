<?php

	if(! defined('PV_COMPOSANT_IU_BOOTSTRAP4'))
	{
		define('PV_COMPOSANT_IU_BOOTSTRAP4', 1) ;
		
		class PvDessinFiltresDonneesBootstrap4 extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public $ColXs = "" ;
			public $ColSm = "" ;
			public $ColMd = "" ;
			public $ColLd = "" ;
			public $UtiliserContainerFluid = 1 ;
			public $InclureRenduLibelle = 1 ;
			public $EditeurSurligne = 0 ;
			public $ColXsLibelle = 4 ;
			public $ClsBstLibelle ;
			public $AlignLibelle ;
			public $CltBstEditeur ;
			public $AlignEditeur ;
			public $MaxFiltresParLigne = 1 ;
			protected function ObtientColXs($maxFiltres)
			{
				return ($this->ColXs != '') ? $this->ColXs :
					(($this->ColLd != '') ? $this->ColLd : 
						(($this->ColMd != '') ? $this->ColMd : 
							($this->ColSm != '') ? $this->ColSm : intval(12 / $maxFiltres)
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
					if($filtre->EstPasNul($filtre->Composant))
					{
						if(! in_array("form-control", $filtre->Composant->ClassesCSS))
						{
							$filtre->Composant->ClassesCSS[] = "form-control" ;
						}
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
				if($this->EditeurSurligne == 1 && $this->InclureLibelle == 1)
				{
					return $this->RenduEditeursSurligne($script, $composant, $parametres) ;
				}
				$filtres = $composant->ExtraitFiltresDeRendu($parametres, $this->FiltresCaches) ;
				$ctn = '' ;
				$ctn .= '<fieldset>'.PHP_EOL ;
				$ctn .= '<div' ;
				$ctn .= ' class="'.(($this->UtiliserContainerFluid) ? 'container-fluid' : 'container').'"' ;
				$ctn .= '>'.PHP_EOL ;
				if($this->MaxFiltresParLigne <= 0)
				{
					$this->MaxFiltresParLigne = 1 ;
				}
				$colXs = $this->ObtientColXs($this->MaxFiltresParLigne) ;
				$maxColonnes = 12 / $colXs ;
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
					if($filtreRendus % $maxColonnes == 0)
					{
						$ctn .= '<div class="row">'.PHP_EOL ;
					}
					$ctn .= '<div class="col-'.$colXs.(($this->ColSm != '') ? ' col-sm-'.$this->ColSm : '').''.(($this->ColMd != '') ? ' col-md-'.$this->ColMd : '').(($this->ColLd != '') ? ' col-ld-'.$this->ColLd : '').'">'.PHP_EOL ;
					$ctn .= '<div class="form-group">'.PHP_EOL ;
					if($this->InclureRenduLibelle)
					{
						if($this->EditeurSurligne == 0)
						{
							$ctn .= '<div class="container-fluid">'.PHP_EOL .'<div class="row">'.PHP_EOL .'<div class="col-12 col-sm-'.$this->ColXsLibelle.''.(($this->ClsBstLibelle == '') ? '' : ' '.$this->ClsBstLibelle).'"'.(($this->AlignLibelle == '') ? '' : ' align="'.$this->AlignLibelle.'"').'>'.PHP_EOL ;
							$ctn .= $this->RenduLibelleFiltre($filtre).PHP_EOL ;
							$ctn .= '</div>'.PHP_EOL .'<div class="col-12 col-sm-'.(12 - $this->ColXsLibelle).''.(($this->ClsBstEditeur == '') ? '' : ' '.$this->ClsBstEditeur).'"'.(($this->AlignEditeur == '') ? '' : ' align="'.$this->AlignEditeur.'"').'>'.PHP_EOL ;
						}
						else
						{
							$ctn .= '<div>'.PHP_EOL .$this->RenduLibelleFiltre($filtre).PHP_EOL .'</div>'.PHP_EOL ;
						}
					}
					if($this->EditeurSurligne == 0)
					{
						$ctn .= $this->RenduFiltre($filtre, $composant).PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL .'</div>'.PHP_EOL .'</div>'.PHP_EOL ;
					}
					else
					{
						$ctn .= '<div>'.PHP_EOL 
							.$this->RenduFiltre($filtre, $composant).PHP_EOL
							.'</div>'.PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
					$filtreRendus++ ;
					if($filtreRendus % $maxColonnes == 0)
					{
						$ctn .= '</div>'.PHP_EOL ;
					}
				}
				if($filtreRendus % $maxColonnes != 0)
				{
					$colonnesFusionnees = $maxColonnes - ($filtreRendus % $maxColonnes) ;
					$ctn .= '<div class="col-'.$colonnesFusionnees.'">&nbsp;</div>'.PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</fieldset>' ;
				return $ctn ;
			}
		}
		class PvDessinCommandesBootstrap4 extends PvDessinateurRenduHtmlCommandes
		{
			public $InclureGlyphicons = 0 ;
			public $GlyphiconParDefaut = "glyphicon-flash" ;
			public $ClasseCSSPanel = "panel-default" ;
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
						$ctn .= '<button id="'.$commande->IDInstanceCalc.'" class="Commande btn '.$commande->NomClsCSS.' '.$classeBtn.'" type="submit" rel="'.$commande->NomElementSousComposantIU.'"' ;
						$contenuJsSurClick = ($commande->ContenuJsSurClick == '') ? $composant->IDInstanceCalc.'_ActiveCommande(this) ;' : $commande->ContenuJsSurClick.' ; return false ;' ;
						$ctn .= ' onclick="'.$contenuJsSurClick.'"' ;
						if($this->InclureLibelle == 0)
						{
							$ctn .= ' title="'.htmlspecialchars($commande->Libelle).'"' ;
						}
						$ctn .= '>'.PHP_EOL ;
						if($this->InclureGlyphicons == 1)
						{
							$glyphicon = $this->GlyphiconParDefaut ;
							if($commande->ObtientValSuppl("glyphicon") != '')
							{
								$glyphicon = $commande->ObtientValSuppl("glyphicon") ;
							}
							$ctn .= '<i class="glyphicon '.$glyphicon.'"></i>'.PHP_EOL ;
						}
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
		
		class PvFormulaireDonneesBootstrap4 extends PvFormulaireDonneesHtml
		{
			public $UtiliserLargeur = 0 ;
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
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
		
		class PvTableauDonneesBootstrap4 extends PvTableauDonneesHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $ClasseCSSRangee = "table-striped table-hover" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstEnteteFormFiltres ;
			public $ClsBstPiedFormFiltres ;
			public $ClsBstFormFiltresSelect = "col-12 col-sm-8 col-md-6" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesBootstrap4() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
				$this->NavigateurRangees = new PvNav2TableauDonneesBootstrap4() ;
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
				$ctn .= '<div class="card card-primary">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="card-header'.(($this->ClsBstEnteteFormFiltres == '') ? '' : ' '.$this->ClsBstEnteteFormFiltres).'" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="card-body">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresSelect.'">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="card-footer'.(($this->ClsBstPiedFormFiltres == '') ? '' : ' '.$this->ClsBstPiedFormFiltres).'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'">'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
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
						$libelleTriAscSelectionne = '<span data-fa-transform="up-4" class="fa fa-sort-up" title="'.htmlspecialchars($this->LibelleTriAsc).'"></span>' ;
						$libelleTriDescSelectionne = '<span data-fa-transform="down-4" class="fa fa-sort-down" title="'.htmlspecialchars($this->LibelleTriDesc).'"></span>' ;
					}
					else
					{
						$libelleTriAsc = '<span class="text-muted" title="'.htmlspecialchars($this->LibelleTriAsc).'">Asc</span>' ;
						$libelleTriDesc = '<span class="text-muted fa fa-sort-down" title="'.htmlspecialchars($this->LibelleTriDesc).'">Desc</span>' ;
						$libelleTriAscSelectionne = '<span title="'.htmlspecialchars($this->LibelleTriAsc).'">Asc</span>' ;
						$libelleTriDescSelectionne = '<span class="fa" title="'.htmlspecialchars($this->LibelleTriDesc).'">Desc</span>' ;
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
							$ctn .= '<form id="FormRangee'.$this->IDInstanceCalc.'" action="?'.(($this->ZoneParent->ActiverRoutes == 0) ? urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&' : '').http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
							$ctn .= $ctnChampsPost ;
						}
						$ctn .= '<div class="panel panel-default"><div class="panel-body table-responsive">'.PHP_EOL ;
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
		class PvGrilleDonneesBootstrap4 extends PvGrilleDonneesHtml
		{
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSCellule = "" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstEnteteFormFiltres ;
			public $ClsBstPiedFormFiltres ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-12 col-sm-8 col-md-6" ;
			public $SautLigneSansCommande = 0 ;
			public $MaxColonnesXs = 0 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesBootstrap4() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
				$this->NavigateurRangees = new PvNav2TableauDonneesBootstrap4() ;
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
				$ctn .= '<div class="card card-primary">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="card-header'.(($this->ClsBstEnteteFormFiltres == '') ? '' : ' '.$this->ClsBstEnteteFormFiltres).'" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="card-body">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresSelect.'">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="card-footer'.(($this->ClsBstPiedFormFiltres == '') ? '' : ' '.$this->ClsBstPiedFormFiltres).'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'">'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
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
						$ctn .= '<div' ;
						$ctn .= ' class="RangeeDonnees container-fluid">'.PHP_EOL ;
						$inclureLargCell = 1 ;
						$maxColsXs = ($this->MaxColonnesXs > 0) ? $this->MaxColonnesXs : $this->MaxColonnes ;
						$colXs = 12 / $maxColsXs ;
						$colDef = 12 / $this->MaxColonnes ;
						foreach($this->ElementsEnCours as $j => $ligne)
						{
							if($this->MaxColonnes <= 1 || $j % $this->MaxColonnes == 0)
							{
								$ctn .= '<div class="row">'.PHP_EOL ;
							}
							$ctn .= '<div class="Contenu col-'.$colXs.' col-sm-'.$colDef.(($this->ClasseCSSCellule != '') ? ' '.$this->ClasseCSSCellule : '').'"' ;
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
							$ctn .= '</div>'.PHP_EOL ;
						}
						$ctn .= '</div>' ;
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
		
		class PvNavTableauDonneesBootstrap4 extends PvNavigateurRangeesDonneesBase
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
				$ctn .= '<div class="col-12 col-sm-6 LiensRangee">'.PHP_EOL ;
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
				$ctn .= '<div align="right" class="InfosRangees col-12 col-sm-6">'.PHP_EOL ;
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
			}
		}
		
		class PvNav2TableauDonneesBootstrap4 extends PvNavigateurRangeesDonneesBase
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
				$ctn .= '<nav aria-label="Page navigation example" class="NavigateurRangees">'.PHP_EOL ;
				$ctn .= '<ul class="pagination justify-content-center">'.PHP_EOL ;
				$paramPremiereRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => 0)) ;
				$ctn .= '<li class="page-item"><a class="page-link" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramPremiereRangee).'" title="'.$composant->TitrePremiereRangee.'">'.$composant->LibellePremiereRangee.'</a></li>'.PHP_EOL ;
				if($composant->RangeeEnCours > 0)
				{
					if($composant->RangeeEnCours - $this->MaxRangeesPrec > 0)
					{
						$ctn .= '<li class="page-item"><a class="page-link" href="javascript:;" title="'.$composant->TitrePremiereRangee.'">...</a></li>' ;
					}
					for($i=$composant->RangeeEnCours - $this->MaxRangeesPrec; $i<$composant->RangeeEnCours; $i++)
					{
						$rangeeEnCours = $i ;
						if($rangeeEnCours < 0)
						{
							continue ;
						}
						$paramRangeePrecedente = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => ($rangeeEnCours) * $composant->MaxElements)) ;
						$ctn .= '<li class="page-item"><a class="page-link" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangeePrecedente).'" title="'.($rangeeEnCours + 1).'">'.($rangeeEnCours + 1).'</a></li>'.PHP_EOL ;
					}
				}
				$paramRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => ($composant->RangeeEnCours) * $composant->MaxElements)) ;
				$ctn .= '<li class="page-item active"><a class="page-link" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangee).'" title="'.($composant->RangeeEnCours + 1).'">'.($composant->RangeeEnCours + 1).'</a></li>'.PHP_EOL ;
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
						$ctn .= '<li class="page-item"><a class="page-link" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramRangeeSuivante).'" title="'.($rangeeEnCours + 1).'">'.($rangeeEnCours + 1).'</a></li>'.PHP_EOL ;
					}
					if($composant->RangeeEnCours + $this->MaxRangeesSuiv < $composant->TotalRangees - 1)
					{
						$ctn .= '<li class="page-item"><a class="page-link" href="javascript:;" title="">...</a></li>' ;
					}
				}
				$paramDerniereRangee = array_merge($parametresRendu, array($composant->NomParamIndiceDebut() => intval($composant->TotalElements / $composant->MaxElements) * $composant->MaxElements)) ;
				$ctn .= '<li class="page-item"><a class="page-link" href="javascript:'.$composant->AppelJsEnvoiFiltres($paramDerniereRangee).'" title="'.$composant->TitreDerniereRangee.'">'.$composant->LibelleDerniereRangee.'</a></li>'.PHP_EOL ;
				$ctn .= '</ul>'.PHP_EOL ;
				$ctn .= '</nav>' ;
				return $ctn ;
				return $ctn ;
			}
		}
		
		class PvZoneSelectBootstrap4 extends PvZoneSelectHtml
		{
		}
	}

?>