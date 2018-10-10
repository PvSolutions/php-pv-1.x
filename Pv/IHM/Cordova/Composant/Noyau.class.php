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
		}
		class PvDessinCommandesCordova extends PvDessinateurRenduHtmlCommandes
		{
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
				$ctn .= '<div class="panel panel-default"><div class="panel-footer">'.PHP_EOL ;
				$ctn .= '<div class="NavigateurRangees container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="col-xs-6 LiensRangee">'.PHP_EOL ;
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
				$ctn .= '<div align="right" class="InfosRangees col-xs-6">'.PHP_EOL ;
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