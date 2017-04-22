<?php
	
	if(! defined('PV_COMPOSANT_SB_ADMIN2'))
	{
		if(! defined('PV_CHART_MORRIS_JS'))
		{
			include dirname(__FILE__)."/../Simple/ComposantIU/MorrisJs.class.php" ;
		}
		define('PV_COMPOSANT_SB_ADMIN2', 1) ;
		
		class PvDessinFiltresSbAdmin2 extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public $MaxFiltresParLigne = 2 ;
			public $PrefxBstGrilleFiltres = "col-lg" ;
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
				$filtres = $composant->ExtraitFiltresDeRendu($parametres) ;
				$ctn = '' ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				$ctnCols = array() ;
				$totalFlts = 0 ;
				$maxFltsParLgn = 1 ;
				if($this->MaxFiltresParLigne > 1)
				{
					$maxFltsParLgn = ($this->MaxFiltresParLigne < 12) ? $this->MaxFiltresParLigne : 12 ;
				}
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $parametres[$i] ;
					if(! $filtre->RenduPossible())
					{
						continue ;
					}
					$idxCol = $totalFlts % $maxFltsParLgn ;
					if(! isset($ctnCols[$idxCol]))
					{
						$ctnCols[$idxCol] = '' ;
					}
					$ctnCol = '' ;
					$ctnCol .= '<div class="form-group">'.PHP_EOL ;
					if($this->InclureRenduLibelle)
					{
						$ctnCol .= $this->RenduLibelleFiltre($filtre).PHP_EOL ;
					}
					$ctnCol .= $this->RenduFiltre($filtre, $composant).PHP_EOL ;
					$ctnCol .= '</div>' ;
					$ctnCols[$idxCol] .= $ctnCol ;
					$totalFlts++ ;
				}
				if($maxFltsParLgn == 1)
				{
					$ctn .= $ctnCols[0] ;
				}
				else
				{
					$clsCol = $this->PrefxBstGrilleFiltres.'-'.intval(12 / $maxFltsParLgn) ;
					$ctn .= '<div class="row">'.PHP_EOL ;
					foreach($ctnCols as $i => $ctnCol)
					{
						$ctn .= '<div class="'.$clsCol.'">'.PHP_EOL ;
						$ctn .= $ctnCol.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
					}
					$ctn .= '</div>' ;
				}
				return $ctn ;
			}
		}
		
		class PvCmdOuvreBoiteDlgUrl extends PvCommandeExecuterBase
		{
			public $TitreDlg ;
			public $UrlDlg ;
			public $LargeurDlg = null ;
			public $HauteurDlg = null ;
			public $RafraichPageSurFerm = null ;
			protected function ExecuteInstructions()
			{
				$ctn = '<script type="text/javascript">
	jQuery(function() {
		BoiteDlgUrl.ouvre('.svc_json_encode($this->TitreDlg).', '.svc_json_encode($this->UrlDlg).', '.svc_json_encode($this->LargeurDlg).', '.svc_json_encode($this->HauteurDlg).', '.svc_json_encode($this->RafraichPageSurFerm).') ;
	}) ;
</script>' ;
				if($this->EstPasNul($this->TableauDonneesParent))
				{
					$this->TableauDonneesParent->ContenuAvantRendu .= $ctn ;
				}
				elseif($this->EstPasNul($this->FormulaireDonneesParent))
				{
					$this->FormulaireDonneesParent->ContenuAvantRendu .= $ctn ;
				}
			}
		}
		
		class PvDessinCmdsSbAdmin2 extends PvDessinateurRenduHtmlCommandes
		{
			public $ClsBstBtnCommande = "btn-primary" ;
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
						$ctn .= '<button class="Commande btn '.$this->ClsBstBtnCommande.' '.$commande->NomClsCSS.'" type="submit" rel="'.$commande->NomElementSousComposantIU.'"' ;
						$ctn .= ' onclick="'.$composant->IDInstanceCalc.'_ActiveCommande(this) ;"' ;
						if($this->InclureLibelle == 0)
						{
							$ctn .= ' title="'.htmlspecialchars($commande->Libelle).'"' ;
						}
						$ctn .= '>'.PHP_EOL ;
						if($this->InclureIcones)
						{
							$cheminIcone = $this->CheminIconeParDefaut ;
							if($commande->CheminIcone != '')
							{
								$cheminIcone = $commande->CheminIcone ;
							}
							if(file_exists($cheminIcone))
							{
								$ctn .= '<img src="'.$cheminIcone.'" height="'.$this->HauteurIcone.'" border="0" />' ;
							}
							if($commande->InclureLibelle == 1)
							{
								$ctn .= $this->SeparateurIconeLibelle ;
							}
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
		
		class PvNavbarStaticTopSbAdmin2 extends PvComposantIUBase
		{
			public $IndicatifDeroulNavig = "D&eacute;rouler la navigation" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="sr-only">'.$this->IndicatifDeroulNavig.'</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="?">'.$this->ZoneParent->Titre.'</a>
</div>' ;
				return $ctn ;
			}
		}
		
		class PvNavbarTopLinksSbAdmin2 extends PvComposantIUBase
		{
			public $InclureMessages = 0 ;
			public $InclureTaches = 0 ;
			public $InclureAlertes = 0 ;
			public $FournisseurDonneesMsgs ;
			public $FournisseurDonneesCommun ;
			public $NomColonneDateMsg ;
			public $NomColonneExpeditMsg ;
			public $NomColonneApercuMsg ;
			public $FormatUrlMsg ;
			public $UrlLienTousMsgs = "?" ;
			public $LibelleLienTousMsgs = "Tous les messages" ;
			public $FournisseurDonneesTaches ;
			public $NomColonneTitreTache ;
			public $NomColonnePourcentTache ;
			public $NomColonneCouleurTache ;
			public $CouleurParDefautTache ;
			public $FormatUrlTache ;
			public $FournisseurDonneesAlertes ;
			public $NomColonneClasseCSSAlerte ;
			public $NomColonneTitreAlerte ;
			public $NomColonneDateAlerte ;
			public $FormatUrlAlerte ;
			public $ClasseCSSAlerteDefaut = "fa-comment" ;
			public $UrlLienToutesAlertes = "?" ;
			public $LibelleLienToutesAlertes = "Toutes les alertes" ;
			public $LibelleLienModifPrefs = "Profil" ;
			public $LibelleLienChangerMotPasse = "Changer de mot de passe" ;
			public $LibelleLienDeconnexion = "D&eacute;connexion" ;
			protected function RenduListeMessages()
			{
				$fournDonnees = ($this->EstPasNul($this->FournisseurDonneesMsgs)) ? $this->FournisseurDonneesMsgs : (($this->EstPasNul($this->FournisseurDonneesCommun)) ? $this->FournisseurDonneesCommun : null) ;
				if($fournDonnees == null)
				{
					return "" ;
				}
				$ctn = '' ;
				$lgns = $fournDonnees->SelectElements(array(), array()) ;
				if(is_array($lgns) && count($lgns) > 0)
				{
					$ctn .= '<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;">
<i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
</a>
<ul class="dropdown-menu dropdown-messages">'.PHP_EOL ;
					foreach($lgns as $i => $lgn)
					{
						if($i > 0)
						{
							$ctn .= '<li class="divider"></li>'.PHP_EOL ;
						}
						$url = ($this->FormatUrlMsg == "") ? "javascript:;" : _parse_pattern($this->FormatUrlMsg, array_map('urlencode', $lgn)) ;
						$expedit = $lgn[$this->NomColonneExpeditMsg] ;
						$dateMsg = $lgn[$this->NomColonneDateMsg] ;
						$apercuMsg = strip_tags($lgn[$this->NomColonneApercuMsg]) ;
						$ctn .= '<li>
<a href="'.htmlspecialchars($url).'">
<div>
<strong>'.htmlentities($expedit).'</strong>
<span class="pull-right text-muted">
<em>'.htmlentities($dateMsg).'</em>
</span>
</div>
<div>'.htmlentities($apercuMsg).'</div>
</a>
</li>'.PHP_EOL ;
					}
				}
				$ctn .= '<li class="divider"></li>
<li>
<a class="text-center" href="'.$this->UrlLienTousMsgs.'">
<strong>'.$this->LibelleLienTousMsgs.'</strong>
<i class="fa fa-angle-right"></i>
</a>
</li>
</ul>
</li>' ;
				return $ctn ;
			}
			protected function RenduListeTaches()
			{
				$ctn = '' ;
				$ctn .= '<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">
<i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
</a>
<ul class="dropdown-menu dropdown-tasks">
<li>
<a href="#">
<div>
<p>
<strong>Task 1</strong>
<span class="pull-right text-muted">40% Complete</span>
</p>
<div class="progress progress-striped active">
<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
<span class="sr-only">40% Complete (success)</span>
</div>
</div>
</div>
</a>
</li>
<li class="divider"></li>
<li>
<a href="#">
<div>
<p>
<strong>Task 2</strong>
<span class="pull-right text-muted">20% Complete</span>
</p>
<div class="progress progress-striped active">
<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
<span class="sr-only">20% Complete</span>
</div>
</div>
</div>
</a>
</li>
<li class="divider"></li>
<li>
<a href="#">
<div>
<p>
<strong>Task 3</strong>
<span class="pull-right text-muted">60% Complete</span>
</p>
<div class="progress progress-striped active">
<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
<span class="sr-only">60% Complete (warning)</span>
</div>
</div>
</div>
</a>
</li>
<li class="divider"></li>
<li>
<a href="#">
<div>
<p>
<strong>Task 4</strong>
<span class="pull-right text-muted">80% Complete</span>
</p>
<div class="progress progress-striped active">
<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
<span class="sr-only">80% Complete (danger)</span>
</div>
</div>
</div>
</a>
</li>
<li class="divider"></li><li>
<a class="text-center" href="#">
<strong>See All Tasks</strong>
<i class="fa fa-angle-right"></i>
</a>
</li>
</ul>
</li>' ;
				return $ctn ;
			}
			protected function RenduListeAlertes()
			{
				$fournDonnees = ($this->EstPasNul($this->FournisseurDonneesMsgs)) ? $this->FournisseurDonneesMsgs : (($this->EstPasNul($this->FournisseurDonneesCommun)) ? $this->FournisseurDonneesCommun : null) ;
				if($fournDonnees == null)
				{
					return "" ;
				}
				$ctn = '' ;
				$lgns = $fournDonnees->SelectElements(array(), array()) ;
				if(is_array($lgns) && count($lgns) > 0)
				{
					$ctn .= '<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;">
<i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
</a>
<ul class="dropdown-menu dropdown-alerts">'.PHP_EOL ;
					foreach($lgns as $i => $lgn)
					{
						$url = ($this->FormatUrlAlerte == "") ? "javascript:;" : _parse_pattern($this->FormatUrlAlerte, array_map('urlencode', $lgn)) ;
						$titre = strip_tags($lgn[$this->NomColonneTitreAlerte]) ;
						$dateAlerte = $lgn[$this->NomColonneDateAlerte] ;
						$classeCSS = (isset($lgn[$this->NomColonneClasseCSSAlerte]) && $this->NomColonneClasseCSSAlerte != "") ? $lgn[$this->NomColonneClasseCSSAlerte] : $this->ClasseCSSAlerteDefaut ;
						$ctn .= '<li>
<a href="'.htmlspecialchars($url).'">
<div>
<i class="fa '.$classeCSS.' fa-fw"></i> '.$titre.'
<span class="pull-right text-muted small">'.$dateAlerte.'</span>
</div>
</a>
</li>'.PHP_EOL ;
						$ctn .= '<li class="divider"></li>'.PHP_EOL ;
					}
					$ctn .= '<li>
<a class="text-center" href="'.$this->UrlLienToutesAlertes.'">
<strong>'.$this->LibelleLienToutesAlertes.'</strong>
<i class="fa fa-angle-right"></i>
</a>
</li>
</ul>
</li>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<ul class="nav navbar-top-links navbar-right">'.PHP_EOL ;
				$ctn .= '<li><b>'.$this->ZoneParent->LoginMembreConnecte().'</b></li>' ;
				if($this->InclureMessages == 1)
				{
					$ctn .= $this->RenduListeMessages() ;
				}
				if($this->InclureTaches == 1)
				{
					$ctn .= $this->RenduListeTaches() ;
				}
				if($this->InclureAlertes == 1)
				{
					$ctn .= $this->RenduListeAlertes() ;
				}
				$ctn .= '<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">
<i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>' ;
				$ctn .= '</a>'.PHP_EOL ;
				$ctn .= '<ul class="dropdown-menu dropdown-user">'.PHP_EOL ;
				if($this->ZoneParent->AutoriserModifPrefs)
				{
					$ctn .= '<li><a href="'.$this->ZoneParent->ScriptModifPrefs->ObtientUrl().'"><i class="fa fa-user fa-fw"></i> '.$this->LibelleLienModifPrefs.'</a>
</li>'.PHP_EOL ;
				}
				$ctn .= '<li><a href="'.$this->ZoneParent->ScriptChangeMotPasse->ObtientUrl().'"><i class="fa fa-gear fa-fw"></i> '.$this->LibelleLienChangerMotPasse.'</a></li>'.PHP_EOL ;
				$ctn .= '<li class="divider"></li>'.PHP_EOL ;
				$ctn .= '<li><a href="'.$this->ZoneParent->ScriptDeconnexion->ObtientUrl().'"><i class="fa fa-sign-out fa-fw"></i> '.$this->LibelleLienDeconnexion.'</a></li>'.PHP_EOL ;
				$ctn .= '</ul>
</li>
</ul>' ;
				return $ctn ;
			}
		}
		
		class PvZoneBoiteOptionsRadioSbAdmin2 extends PvZoneBoiteOptionsRadioHtml
		{
			protected function RenduListeElements()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= $this->RenduFoncJs() ;
				$ctn .= $this->RenduLiens() ;
				$ctn .= '<table' ;
				$ctn .= ' name="Conteneur_'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="Conteneur_'.$this->IDInstanceCalc.'"' ;
				$ctn .= $this->RenduAttrsSupplHtml() ;
				$ctn .= '>'.PHP_EOL ;
				$totalLignes = 0 ;
				$indexLigne = 0 ;
				$pourcentageColonne = intval(100 / $this->MaxColonnesParLigne) ;
				$this->OuvreRequeteSupport() ;
				while($ligne = $this->LitRequeteSupport())
				{
					if($indexLigne % $this->MaxColonnesParLigne == 0)
					{
						$ctn .= '<tr>'.PHP_EOL ;
					}
					$ctn .= '<td' ;
					$ctn .= ' width="'.$pourcentageColonne.'%"' ;
					$ctn .= ' valign="top"' ;
					$ctn .= '>'.PHP_EOL ;
					$valeur = $this->ExtraitValeur($ligne, $this->NomColonneValeur) ;
					$libelle = $this->ExtraitValeur($ligne, $this->NomColonneLibelle) ;
					$ctn .= $this->RenduElement($valeur, $libelle, $ligne, $this->RequeteSupport->Position).PHP_EOL ;
					$ctn .= '</td>'.PHP_EOL ;
					if($indexLigne % $this->MaxColonnesParLigne == $this->MaxColonnesParLigne - 1)
					{
						$ctn .= '</tr>'.PHP_EOL ;
					}
					$indexLigne++ ;
				}
				if($indexLigne % $this->MaxColonnesParLigne != 0)
				{
					$colonnesFusionnees = $this->MaxColonnesParLigne - ($indexLigne % $this->MaxColonnesParLigne) ;
					$ctn .= '<td colspan="'.$colonnesFusionnees.'"></td>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$this->FermeRequeteSupport() ;
				$ctn .= '</table>' ;
				$ctn .= '<input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" />' ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
		}
		class PvZoneBoiteOptionsCocherSbAdmin2 extends PvZoneBoiteOptionsCocherHtml
		{
			protected function RenduListeElements()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= $this->RenduFoncJs() ;
				$ctn .= $this->RenduLiens() ;
				$ctn .= '<table' ;
				$ctn .= ' name="Conteneur_'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="Conteneur_'.$this->IDInstanceCalc.'"' ;
				$ctn .= $this->RenduAttrsSupplHtml() ;
				$ctn .= '>'.PHP_EOL ;
				$totalLignes = 0 ;
				$indexLigne = 0 ;
				$pourcentageColonne = intval(100 / $this->MaxColonnesParLigne) ;
				$this->OuvreRequeteSupport() ;
				while($ligne = $this->LitRequeteSupport())
				{
					if($indexLigne % $this->MaxColonnesParLigne == 0)
					{
						$ctn .= '<tr>'.PHP_EOL ;
					}
					$ctn .= '<td' ;
					$ctn .= ' width="'.$pourcentageColonne.'%"' ;
					$ctn .= ' valign="top"' ;
					$ctn .= '>'.PHP_EOL ;
					$valeur = $this->ExtraitValeur($ligne, $this->NomColonneValeur) ;
					$libelle = $this->ExtraitValeur($ligne, $this->NomColonneLibelle) ;
					$ctn .= $this->RenduElement($valeur, $libelle, $ligne, $this->RequeteSupport->Position).PHP_EOL ;
					$ctn .= '</td>'.PHP_EOL ;
					if($indexLigne % $this->MaxColonnesParLigne == $this->MaxColonnesParLigne - 1)
					{
						$ctn .= '</tr>'.PHP_EOL ;
					}
					$indexLigne++ ;
				}
				if($indexLigne % $this->MaxColonnesParLigne != 0)
				{
					$colonnesFusionnees = $this->MaxColonnesParLigne - ($indexLigne % $this->MaxColonnesParLigne) ;
					$ctn .= '<td colspan="'.$colonnesFusionnees.'"></td>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$this->FermeRequeteSupport() ;
				$ctn .= '</table>' ;
				$ctn .= '<input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" />' ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
		}
		
		class PvBlocNbreSurlignSbAdmin2 extends PvComposantIUBase
		{
			public $ClasseCSSBloc = "panel-primary" ;
			public $ValeurNbre = "0" ;
			public $ClasseCSSNbre = "fa-comments" ;
			public $LibelleNbre = "(Vide)" ;
			public $Url = "javascript:;" ;
			public $LibelleLienDetails = "Voir d&eacute;tails" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn = '<div class="panel '.$this->ClasseCSSBloc.'">
<div class="panel-heading">
<div class="row">
<div class="col-xs-3">
<i class="fa '.$this->ClasseCSSNbre.' fa-5x"></i>
</div>
<div class="col-xs-9 text-right">
<div class="huge">'.$this->ValeurNbre.'</div>
<div>'.$this->LibelleNbre.'</div>
</div>
</div>
</div>
<a href="'.$this->Url.'">
<div class="panel-footer">
<span class="pull-left">'.$this->LibelleLienDetails.'</span>
<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
<div class="clearfix"></div>
</div>
</a>
</div>' ;
				return $ctn ;
			}
		}
		
		class PvChartMorrisSbAdmin2 extends PvChartMorrisJs
		{
			public function AdopteScript($nom, & $script)
			{
				parent::AdopteScript($nom, $script) ;
				$this->CheminFichierJs = $this->ZoneParent->CheminJsMorrisCharts ;
			}
		}
	}
	
?>