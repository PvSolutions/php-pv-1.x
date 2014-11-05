<?php
	
	if(! defined('PV_TABLEAU_DONNEES_BOOTSTRAP'))
	{
		define('PV_TABLEAU_DONNEES_BOOTSTRAP', 1) ;
		
		class PvTableauDonneesBootstrap extends PvTableauDonneesHtml
		{
			public $TypeComposant = "TableauDonneesBootstrap" ;
			public $NomRoleFiltres = "form" ;
			public $NomClasseGrilleFiltres = "col-lg-6" ;
			public $NomClasseGrilleRangee = "col-lg-12" ;
			public $NomClasseFormFiltres = "panel-primary" ;
			public $NomClasseTblRangees = "table-hover" ;
			public $Largeur = "100%" ;
			public $LargeurBordure = "0" ;
			public $AppliquerHabillageSpec = 1 ;
			public function & InsereCmdModal($nomCmd, $libelle, $url, $params=array())
			{
				$cmd = new PvCommandeBootstrapBase() ;
				$cmd->Libelle = $libelle ;
				$this->InscritCommande($nomCmd, $cmd) ;
				$actCmd = new PvActCmdModalBootstrap() ;
				$actCmd->Url = $url ;
				$actCmd->Parametres = $params ;
				$cmd->InsereNouvActCmd($actCmd) ;
				return $cmd ;
			}
			public function RenduComposants()
			{
				$ctn = '' ;
				$ctn .= parent::RenduComposants() ;
				$ctn .= $this->RenduHabillageSpec() ;
				return $ctn ;
			}
			protected function RenduHabillageSpec()
			{
				$ctn = '' ;
				if(! $this->AppliquerHabillageSpec)
				{
					return $ctn ;
				}
				$ctnJS = 'jQuery(function () {
	var comp = jQuery("#'.$this->IDInstanceCalc.'") ;
	if(comp.length == 0)
	{
		return ;
	}
	comp.find(".FormulaireFiltres :input").addClass("form-control") ;
	comp.find(".FormulaireFiltres :button").removeClass("form-control").addClass("btn btn-primary") ;
}) ' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus($ctnJS) ;
				return $ctn ;
			}
			protected function InitDessinateurFiltresSelection()
			{
				$this->DessinateurFiltresSelection = new PvDessinFiltresBootstrap() ;
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
				$ctn .= '<div class="row">' ;
				$ctn .= '<div class="'.$this->NomClasseGrilleFiltres.'">' ;
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="SoumetFormulaire'.$this->IDInstanceCalc.'(this)" role="'.$this->NomRoleFiltres.'">'.PHP_EOL ;
				$ctn .= '<div class="panel '.$this->NomClasseFormFiltres.'">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="panel-heading" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="panel-footer Boutons" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'">'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '<button type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
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
						$libelleTriAsc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAsc.'" />' ;
						$libelleTriDesc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDesc.'" />' ;
						$libelleTriAscSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAscSelectionne.'" />' ;
						$libelleTriDescSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDescSelectionne.'" />' ;
					}
					$parametresRendu = $this->ParametresCommandeSelectionnee() ;
					if(count($this->ElementsEnCours) > 0)
					{
						$ctn .= '<div class="row">' ;
						$ctn .= '<div class="'.$this->NomClasseGrilleRangee.'">' ;
						$ctn .= '<table' ;
						$ctn .= ' class="RangeeDonnees table '.$this->NomClasseTblRangees.'"' ;
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
						$ctn .= '<thead>'.PHP_EOL ;
						$ctn .= '<tr class="Entete">'.PHP_EOL ;
						foreach($this->DefinitionsColonnes as $i => $colonne)
						{
							if($colonne->Visible == 0)
								continue ;
							$triPossible = ($this->TriPossible && $colonne->TriPossible) ;
							$ctn .= ($triPossible) ? '<td' : '<th' ;
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
						$ctn .= '</thead>'.PHP_EOL ;
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
						$ctn .= '</tbody>'.PHP_EOL ;
						$ctn .= '</table>'.PHP_EOL ;
						$ctn .= '</div>' ;
						$ctn .= '</div>' ;
					}
					else
					{
						$ctn .= '<p class="text-info AucunElement">'.$this->MessageAucunElement.'</p>' ;
					}
				}
				else
				{
					$ctn .= $this->RenduFiltresNonRenseignes() ;
				}
				return $ctn ;
			}
			public function RenduFiltresNonRenseignes()
			{
				$ctn = '' ;
				$ctn .= '<p class="text-info FiltresNonRenseignes">'.$this->MessageFiltresNonRenseignes.'</p>' ;
				return $ctn ;
			}
			protected function RenduNavigateurRangees()
			{
				$ctn = '' ;
				$ctn .= '<div class="row">' ;
				$ctn .= '<div class="'.$this->NomClasseGrilleRangee.'">' ;
				$ctn .= parent::RenduNavigateurRangees() ;
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsBoostrap() ;
			}
		}
	}
	
?>