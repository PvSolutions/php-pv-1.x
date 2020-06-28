<?php
	
	if(! defined('PV_MEMBERSHIP_BOOTSTRAP'))
	{
		define('PV_MEMBERSHIP_BOOTSTRAP', 1) ;
		
		class PvRemplisseurConfigMembershipBootstrap extends PvRemplisseurConfigMembershipSimple
		{
			public function CreeFormulaireDonnees()
			{
				return new PvFormulaireDonneesBootstrap() ;
			}
		}
		
		class PvScriptConnexionBootstrap extends PvScriptConnexionWeb
		{
			public $MessageRecouvreMP = '<br><p>Mot de passe oubli&eacute; ? <a href="${url}">Cliquez ici</a> pour le r&eacute;cup&eacute;rer</p>' ;
			public $MessageInscription = '<br><p>Si vous n\'avez pas de compte, <a href="${url}">Inscrivez-vous</a>.</p>' ;
			public $ColXsLibelle = 5 ;
			public $TagTitre = 'h3' ;
			public $InclureIcones = 0 ;
			public $ClasseCSSCadre = "col-xs-12 col-sm-12 col-md-4" ;
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
<div class="col-xs-'.$this->ColXsLibelle.'" align="center">'.$this->LibellePseudo.'</div>
<div class="col-xs-'.(12 - $this->ColXsLibelle).'">
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
<div class="col-xs-'.$this->ColXsLibelle.'" align="center">'.$this->LibelleMotPasse.'</div>
<div class="col-xs-'.(12 - $this->ColXsLibelle).'">
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
		class PvScriptRecouvreMPBootstrap extends PvScriptRecouvreMPWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireRecouvreMPBootstrap" ;
		}
		class PvScriptDeconnexionBootstrap extends PvScriptDeconnexionWeb
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
		
		class PvTableauMembresBootstrap extends PvTableauMembresMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-sm-8" ;
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
								$ctn .= $colonne->FormatteValeur($this, $ligne) ;
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
		class PvTableauRolesBootstrap extends PvTableauRolesMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-sm-6" ;
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
								$ctn .= $colonne->FormatteValeur($this, $ligne) ;
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
		class PvTableauProfilsBootstrap extends PvTableauProfilsMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-sm-6" ;
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
								$ctn .= $colonne->FormatteValeur($this, $ligne) ;
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
			
		class PvFormulaireAjoutMembreBootstrap extends PvFormulaireAjoutMembreMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		class PvFormulaireInscriptionMembreBootstrap extends PvFormulaireInscriptionMembreMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		class PvFormulaireModifMembreBootstrap extends PvFormulaireModifMembreMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		class PvFormulaireModifInfosBootstrap extends PvFormulaireModifInfosMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		class PvFormulaireSupprMembreBootstrap extends PvFormulaireSupprMembreMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		class PvFormulaireChangeMPMembreBootstrap extends PvFormulaireChangeMPMembreMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		class PvFormulaireDoitChangerMotPasseBootstrap extends PvFormulaireDoitChangerMotPasseMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		class PvFormulaireChangeMotPasseBootstrap extends PvFormulaireChangeMotPasseMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		
		class PvFormulaireAjoutRoleBootstrap extends PvFormulaireAjoutRoleMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
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
		class PvFormulaireModifRoleBootstrap extends PvFormulaireModifRoleMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
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
		class PvFormulaireSupprRoleBootstrap extends PvFormulaireSupprRoleMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		
		class PvFormulaireAjoutProfilBootstrap extends PvFormulaireAjoutProfilMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
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
		class PvFormulaireModifProfilBootstrap extends PvFormulaireModifProfilMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
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
		class PvFormulaireSupprProfilBootstrap extends PvFormulaireSupprProfilMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}

		class PvFormulaireRecouvreMPBootstrap extends PvFormulaireRecouvreMPMS
		{
			public $ClasseCSSSucces = "bg-primary text-primary" ;
			public $ClasseCSSErreur = "bg-danger text-danger" ;
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
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesBootstrap() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesBootstrap() ;
			}
		}
		
		class PvScriptAjoutMembreBootstrap extends PvScriptAjoutMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutMembreBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptInscriptionBootstrap extends PvScriptInscriptionWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireInscriptionMembreBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifMembreBootstrap extends PvScriptModifMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifMembreBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprMembreBootstrap extends PvScriptSupprMembreMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprMembreBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifPrefsBootstrap extends PvScriptModifPrefsWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifInfosBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptChangeMotPasseBootstrap extends PvScriptChangeMotPasseWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMotPasseBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptDoitChangerMotPasseBootstrap extends PvScriptDoitChangerMotPasseWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireDoitChangerMotPasseBootstrap" ;
		}
		class PvScriptChangeMPMembreBootstrap extends PvScriptChangeMPMembreWeb
		{
			public $TagTitre = "h3" ;
			public $NomClasseFormulaireDonnees = "PvFormulaireChangeMPMembreBootstrap" ;
		}
		class PvScriptListeMembresBootstrap extends PvScriptListeMembresMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauMembresBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptAjoutProfilBootstrap extends PvScriptAjoutProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutProfilBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifProfilBootstrap extends PvScriptModifProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifProfilBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprProfilBootstrap extends PvScriptSupprProfilMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprProfilBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeProfilsBootstrap extends PvScriptListeProfilsMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauProfilsBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptAjoutRoleBootstrap extends PvScriptAjoutRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireAjoutRoleBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptModifRoleBootstrap extends PvScriptModifRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireModifRoleBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptSupprRoleBootstrap extends PvScriptSupprRoleMSWeb
		{
			public $NomClasseFormulaireDonnees = "PvFormulaireSupprRoleBootstrap" ;
			public $TagTitre = "h3" ;
		}
		class PvScriptListeRolesBootstrap extends PvScriptListeRolesMSWeb
		{
			public $NomClasseTableauDonnees = "PvTableauRolesBootstrap" ;
			public $TagTitre = "h3" ;
		}
	}
	
?>