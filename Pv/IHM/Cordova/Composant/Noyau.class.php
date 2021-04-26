<?php
	
	if(! defined('COMPOSANT_NOYAU_CORDOVA'))
	{
		define('COMPOSANT_NOYAU_CORDOVA', 1) ;
		
		class PvMethodeJsonCordova extends PvActionResultatJSONZoneWeb
		{
			protected function FixeAccesCrossOrigin()
			{
				// Détecter les requetes HTTP
				if (isset($_SERVER['HTTP_ORIGIN'])) {
					header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
					header('Access-Control-Allow-Credentials: true');
					header('Access-Control-Max-Age: 86400');    // cache for 1 day
				}

				// Access-Control headers are received during OPTIONS requests
				if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

					if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
						header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

					if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
						header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
					exit(0);
				}
			}
			public function Execute()
			{
				$this->FixeAccesCrossOrigin() ;
				parent::Execute() ;
			}
			protected function ConstruitResultat()
			{
			}
		}
		
		class PvDessinFiltresDonneesCordova extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public $ColXs = "" ;
			public $ColSm = "" ;
			public $ColMd = "" ;
			public $ColLd = "" ;
			public $UtiliserContainerFluid = 1 ;
			public $UtiliserContainer = 0 ;
			public $UtiliserContainerFiltre = 0 ;
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
				$val = 0 ;
				if($this->ColXs != '')
				{
					$val = $this->ColXs ;
				}
				elseif($this->ColLd != '')
				{
					$val = $this->ColLd ;
				}
				elseif($this->ColMd != '')
				{
					$val = $this->ColMd ;
				}
				elseif($this->ColSm != '')
				{
					$val = $this->ColSm ;
				}
				else
				{
					$val = intval(12 / $maxFiltres) ;
				}
				return $val ;
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
				if($this->UtiliserContainer == 1)
				{
					$ctn .= '<div' ;
					$ctn .= ' class="'.(($this->UtiliserContainerFluid) ? 'container-fluid' : 'container').'"' ;
					$ctn .= '>'.PHP_EOL ;
				}
				if($this->MaxFiltresParLigne <= 0)
				{
					$this->MaxFiltresParLigne = 1 ;
				}
				$colXs = $this->ObtientColXs($this->MaxFiltresParLigne) ;
				$maxColonnes = 12 / $colXs ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $filtres[$nomFiltre] ;
					if($filtre->LectureSeule)
					{
						$ctn .= '<input type="hidden" id="'.htmlspecialchars($filtre->ObtientIDComposant()).'" name="'.htmlspecialchars($filtre->ObtientNomComposant()).'" value="'.htmlspecialchars($filtre->Lie()).'" />'.PHP_EOL ;
						continue ;
					}
					$ctn .= '<div class=" '.'col-'.$colXs.(($this->ColSm != '') ? ' col-sm-'.$this->ColSm : '').''.(($this->ColMd != '') ? ' col-md-'.$this->ColMd : '').(($this->ColLd != '') ? ' col-ld-'.$this->ColLd : '').'">'.PHP_EOL ;
					$ctn .= '<div class="form-group">'.PHP_EOL ;
					if($this->InclureRenduLibelle)
					{
						if($this->EditeurSurligne == 0)
						{
							if($this->UtiliserContainerFiltre == 1)
							{
								$ctn .= '<div class="container-fluid">'.PHP_EOL ;
							}
							$ctn .= '<div class="row">'.PHP_EOL ;
							$ctn .= '<div class="col-12 col-sm-'.$this->ColXsLibelle.''.(($this->ClsBstLibelle == '') ? '' : ' '.$this->ClsBstLibelle).'"'.(($this->AlignLibelle == '') ? '' : ' align="'.$this->AlignLibelle.'"').'>'.PHP_EOL ;
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
						$ctn .= '</div>'.PHP_EOL .'</div>'.PHP_EOL ;
						if($this->UtiliserContainerFiltre == 1)
						{
							$ctn .= '</div>'.PHP_EOL ;
						}
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
				}
				$ctn .= '</div>'.PHP_EOL ;
				if($this->UtiliserContainer == 1)
				{
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '</fieldset>' ;
				return $ctn ;
			}
		}
		class PvDessinCommandesCordova extends PvDessinateurRenduHtmlCommandes
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
		class PvNavTableauDonneesCordova extends PvNavigateurRangeesDonneesBase
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
				$ctn .= '<div class="card"><div class="card-footer">'.PHP_EOL ;
				$ctn .= '<div class="NavigateurRangees container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="col-6 LiensRangee">'.PHP_EOL ;
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
				$ctn .= '<div align="right" class="InfosRangees col-6">'.PHP_EOL ;
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
	}
	
?>