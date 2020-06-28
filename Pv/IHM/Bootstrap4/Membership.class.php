<?php
	
	if(! defined('PV_MEMBERSHIP_BOOTSTRAP4'))
	{
		define('PV_MEMBERSHIP_BOOTSTRAP4', 1) ;
		
		class PvRemplisseurConfigMembershipBootstrap4 extends PvRemplisseurConfigMembershipSimple
		{
			public function CreeFormulaireDonnees()
			{
				return new PvFormulaireDonneesBootstrap4() ;
			}
		}
		
		class PvScriptConnexionBootstrap4 extends PvScriptConnexionWeb
		{
			public $MessageRecouvreMP = '<br><p>Mot de passe oubli&eacute; ? <a href="${url}">Cliquez ici</a> pour le r&eacute;cup&eacute;rer</p>' ;
			public $MessageInscription = '<br><p>Si vous n\'avez pas de compte, <a href="${url}">Inscrivez-vous</a>.</p>' ;
			public $ColXsLibelle = 5 ;
			public $TagTitre = 'h3' ;
			public $InclureIcones = 0 ;
			public $ClasseCSSCadre = "col-12 col-sm-12 col-md-4" ;
			public $ClasseCSSErreur = 'alert alert-danger alert-dismissable' ;
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClasseCSSCadre.' center-block">'.PHP_EOL ;
				$ctn .= '<form class="user_login_box '.$this->NomClsCSSFormulaireDonnees.'" action="'.$this->UrlSoumetTentativeConnexion().'" role="form" method="post">'.PHP_EOL ;
				$ctn .= '<div class="panel panel-default">'.PHP_EOL ;
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				$ctn .= $this->RenduMessageErreur() ;
				$ctn .= '<fieldset>'.PHP_EOL ;
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				$ctn .= '</fieldset>'.PHP_EOL ;
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<input type="submit" value="'.$this->LibelleBoutonSoumettre.'" class="btn btn-lg btn-success btn-block" />'.PHP_EOL ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>' ;
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
				$ctn .= '<div class="form-group">
<div class="container-fluid">
<div class="row">
<div class="col-'.$this->ColXsLibelle.'" align="center">'.$this->LibellePseudo.'</div>
<div class="col-'.(12 - $this->ColXsLibelle).'">
'.(($this->InclureIcones) ? '<div class="input-group">
<span class="input-group-addon">
<i class="glyphicon glyphicon-user"></i>
</span>' : '').'<input class="form-control" name="'.$this->NomParamPseudo.'" type="text" value="'.htmlspecialchars($this->ValeurParamPseudo).'" autofocus />
'.(($this->InclureIcones) ? '</div>' : '').'</div>
</div>
</div>
</div>
<div class="form-group">
<div class="container-fluid">
<div class="row">
<div class="col-'.$this->ColXsLibelle.'" align="center">'.$this->LibelleMotPasse.'</div>
<div class="col-'.(12 - $this->ColXsLibelle).'">
'.(($this->InclureIcones) ? '<div class="input-group">
<span class="input-group-addon">
<i class="glyphicon glyphicon-lock"></i>
</span>' : '').'<input class="form-control" name="'.$this->NomParamMotPasse.'" type="password" value="" />
'.(($this->InclureIcones) ? '</div>' : '').'</div>
</div>
</div>
</div>' ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
				if($this->InclureIcones)
				{
					$ctn .= '<style type="text/css">
.icon-addon {
    position: relative;
    color: #555;
    display: block;
}
.icon-addon:after,
.icon-addon:before {
    display: table;
    content: " ";
}

.icon-addon:after {
    clear: both;
}

.icon-addon.addon-md .glyphicon,
.icon-addon .glyphicon, 
.icon-addon.addon-md .fa,
.icon-addon .fa {
    position: absolute;
    z-index: 2;
    left: 10px;
    font-size: 14px;
    width: 20px;
    margin-left: -2.5px;
    text-align: center;
    padding: 10px 0;
    top: 1px
}
</style>' ;
				}
				return $ctn ;
			}
		}
		class PvScriptRecouvreMPBootstrap4 extends PvScriptRecouvreMPWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireRecouvreMPBootstrap4" ;
		}
		class PvScriptDeconnexionBootstrap4 extends PvScriptDeconnexionWeb
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
		
		class PvTableauMembresBootstrap4 extends PvTableauMembresMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-primary" ;
			public $ClsBstFormFiltresSelect = "col-sm-8" ;
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
					$ctn .= '<div class="card-header" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
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
				$ctn .= '<div class="card-footer">'.PHP_EOL ;
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
							$ctn .= '<form action="?'.(($this->ZoneParent->ActiverRoutes == 0) ? urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&' : '').http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
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
		class PvTableauRolesBootstrap4 extends PvTableauRolesMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-primary" ;
			public $ClsBstFormFiltresSelect = "col-sm-6" ;
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
					$ctn .= '<div class="card-header" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
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
				$ctn .= '<div class="card-footer">'.PHP_EOL ;
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
							$ctn .= '<form action="?'.(($this->ZoneParent->ActiverRoutes == 0) ? urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&' : '').http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
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
		class PvTableauProfilsBootstrap4 extends PvTableauProfilsMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-primary" ;
			public $ClsBstFormFiltresSelect = "col-sm-6" ;
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
					$ctn .= '<div class="card-header" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
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
				$ctn .= '<div class="card-footer">'.PHP_EOL ;
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
							$ctn .= '<form action="?'.(($this->ZoneParent->ActiverRoutes == 0) ? urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ZoneParent->ValeurParamScriptAppele).'&' : '').http_build_query_string($parametresRenduEdit).'" method="post">'.PHP_EOL ;
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
			
		class PvFormulaireAjoutMembreBootstrap4 extends PvFormulaireAjoutMembreMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		class PvFormulaireInscriptionMembreBootstrap4 extends PvFormulaireInscriptionMembreMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		class PvFormulaireModifMembreBootstrap4 extends PvFormulaireModifMembreMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		class PvFormulaireModifInfosBootstrap4 extends PvFormulaireModifInfosMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		class PvFormulaireSupprMembreBootstrap4 extends PvFormulaireSupprMembreMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		class PvFormulaireChangeMPMembreBootstrap4 extends PvFormulaireChangeMPMembreMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			public $MaxFiltresEditionParLigne = 1 ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		class PvFormulaireDoitChangerMotPasseBootstrap4 extends PvFormulaireDoitChangerMotPasseMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		class PvFormulaireChangeMotPasseBootstrap4 extends PvFormulaireChangeMotPasseMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			public $MaxFiltresEditionParLigne = 1 ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		
		class PvFormulaireAjoutRoleBootstrap4 extends PvFormulaireAjoutRoleMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
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
		}
		class PvFormulaireModifRoleBootstrap4 extends PvFormulaireModifRoleMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
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
		}
		class PvFormulaireSupprRoleBootstrap4 extends PvFormulaireSupprRoleMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		
		class PvFormulaireAjoutProfilBootstrap4 extends PvFormulaireAjoutProfilMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
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
		}
		class PvFormulaireModifProfilBootstrap4 extends PvFormulaireModifProfilMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
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
		}
		class PvFormulaireSupprProfilBootstrap4 extends PvFormulaireSupprProfilMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}

		class PvFormulaireRecouvreMPBootstrap4 extends PvFormulaireRecouvreMPMS
		{
			public $ClasseCSSSucces = "alert alert-primary" ;
			public $ClasseCSSErreur = "alert alert-danger" ;
			public $ClasseCSSCommandeExecuter = "btn-primary" ;
			public $ClasseCSSCommandeAnnuler = "btn-danger" ;
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
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap4() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap4() ;
			}
		}
		
		class PvScriptAjoutMembreBootstrap4 extends PvScriptAjoutMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutMembreBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptInscriptionBootstrap4 extends PvScriptInscriptionWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireInscriptionMembreBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifMembreBootstrap4 extends PvScriptModifMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifMembreBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprMembreBootstrap4 extends PvScriptSupprMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprMembreBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifPrefsBootstrap4 extends PvScriptModifPrefsWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifInfosBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptChangeMotPasseBootstrap4 extends PvScriptChangeMotPasseWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMotPasseBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptDoitChangerMotPasseBootstrap4 extends PvScriptDoitChangerMotPasseWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireDoitChangerMotPasseBootstrap4" ;
		}
		class PvScriptChangeMPMembreBootstrap4 extends PvScriptChangeMPMembreWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMPMembreBootstrap4" ;
		}
		class PvScriptListeMembresBootstrap4 extends PvScriptListeMembresMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauMembresBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptAjoutProfilBootstrap4 extends PvScriptAjoutProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutProfilBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifProfilBootstrap4 extends PvScriptModifProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifProfilBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprProfilBootstrap4 extends PvScriptSupprProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprProfilBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeProfilsBootstrap4 extends PvScriptListeProfilsMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauProfilsBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptAjoutRoleBootstrap4 extends PvScriptAjoutRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutRoleBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifRoleBootstrap4 extends PvScriptModifRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifRoleBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprRoleBootstrap4 extends PvScriptSupprRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprRoleBootstrap4" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeRolesBootstrap4 extends PvScriptListeRolesMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauRolesBootstrap4" ;
			public $TagTitre = "h3" ;
		}
	}
	
?>