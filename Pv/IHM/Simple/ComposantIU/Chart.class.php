<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_CHART'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_IU_BASE'))
			include dirname(__FILE__)."/Noyau.class.php" ;
		define('CHEM_REP_PCHART', dirname(__FILE__)."/../../../../misc/pChart") ;
		if(! class_exists('pChart'))
			include CHEM_REP_PCHART."/pChart.class" ;
		if(! class_exists('pData'))
			include CHEM_REP_PCHART."/pData.class" ;
		define('PV_COMPOSANT_SIMPLE_IU_CHART', 1) ;
		
		class PvDefinitionSeriePChart
		{
			public $IndexChart = -1 ;
			public $Libelle ;
			public $NomDonnees = '' ;
			public $EtiquetteDonnees = '' ;
			public function ObtientLibelle()
			{
				return $this->Libelle != '' ? $this->Libelle : $this->NomDonnees ;
			}
		}
		class PvAbcissePChart
		{
			public $ValeurMin = null ;
			public $ValeurMax = null ;
			public $Libelle ;
			public $Unite ;
		}
		class PvConfigEchellePChart
		{
			public $Mode = SCALE_NORMAL ;
			public $InclureMarques = true ;
			public $Angle=0 ;
			public $TotalDecimaux=1 ;
			public $AvecMarge =true ;
			public $AnnulerEtiquettes=1 ;
			public $PositionDroite = false;
		}
		
		class PvPChart extends PvComposantIUDonneesSimple
		{
			public $InclureTitre = 1 ;
			public $Titre = "Statistiques" ;
			public $InclureArrPlan = 1 ;
			public $ArrPlan ;
			public $InclureLegende = 1 ;
			public $Legende ;
			public $Forme ;
			public $Support ;
			public $DonneesSupport ;
			public $JeuDonnees ;
			public $ActionImage ;
			public $NomActionImage ;
			public $Largeur = 750 ;
			public $Hauteur = 525 ;
			public $MargeGaucheForme = 100 ;
			public $MargeDroiteForme = 115 ;
			public $MargeHautForme = 30 ;
			public $MargeBasForme = 20 ;
			public $NomFichierPolice = "tahoma.ttf" ;
			public $TaillePoliceLegende = 8 ;
			public $TaillePoliceDonnees = 4 ;
			public $TaillePoliceEtiquette = 8 ;
			public $TaillePoliceTitre = 12 ;
			public $TaillePoliceEtiquetteSerie = 6 ;
			public $TailleCourbeArrPlan = 7 ;
			public $DefinitionsSeries = array() ;
			public $Points = array() ;
			public $FiltresSelection = array() ;
			public $Abcisse = null ;
			public function InitConfig()
			{
				parent::InitConfig() ;
				$this->Abcisse = new PvAbcissePChart() ;
				$this->Forme = $this->CreeForme() ;
			}
			protected function CreeForme()
			{
				return new PvOndulationPChart() ;
			}
			public function & InsereDefSerie($nomDonnees, $libelle="")
			{
				$defSerie = new PvDefinitionSeriePChart() ;
				$defSerie->NomDonnees = $nomDonnees ;
				$defSerie->Libelle = $libelle ;
				$this->DefinitionsSeries[] = & $defSerie ;
				return $defSerie ;
			}
			public function & InsereDefinitionSerie($nomDonnees, $libelle="")
			{
				$defSerie = $this->InsereDefSerie($nomDonnees, $libelle) ;
				return $defSerie ;
			}
			public function AdopteScript($nom, & $script)
			{
				parent::AdopteScript($nom, $script) ;
				$this->NomActionImage = $this->IDInstanceCalc.'_Image' ;
				$this->ActionImage = new PvActionImagePChart() ;
				$this->InscritActionAvantRendu($this->NomActionImage, $this->ActionImage) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<img' ;
				$ctn .= ' src="'.$this->ActionImage->ObtientUrl().'"' ;
				if($this->Largeur != '')
				{
					$ctn .= ' width="'.$this->Largeur.'"' ;
				}
				if($this->Hauteur != '')
				{
					$ctn .= ' height="'.$this->Hauteur.'"' ;
				}
				$ctn .= ' />' ;
				return $ctn ;
			}
			protected function CalculeJeuDonnees()
			{
				$this->JeuDonnees = new pData() ;
				$this->Points = array() ;
				if($this->FournisseurDonnees == null)
					return ;
				$this->DonneesSupport = $this->FournisseurDonnees->SelectElements(array(), $this->FiltresSelection) ;
				if($this->DonneesSupport == null)
				{
					return ;
				}
				foreach($this->DefinitionsSeries as $j => $defSerie)
				{
					$this->DefinitionsSeries[$j]->IndexChart = $j ;
					$this->Points[$j] = array() ;
				}
				foreach($this->DonneesSupport as $i => $ligne)
				{
					foreach($this->DefinitionsSeries as $j => $defSerie)
					{
						if($defSerie->NomDonnees != '' && isset($ligne[$defSerie->NomDonnees]))
						{
							$this->Points[$j][] = $ligne[$defSerie->NomDonnees] ;
						}
						else
						{
							$this->Points[$j][] = 0 ;
						}
					}
				}
				foreach($this->Points as $i => $point)
				{
					$this->JeuDonnees->AddPoint($point, "Serie".($i + 1));
				}
				// Dataset definition
				$this->JeuDonnees->AddAllSeries();
				$this->JeuDonnees->SetAbsciseLabelSerie();
				foreach($this->DefinitionsSeries as $j => $defSerie)
				{
					$this->JeuDonnees->SetSerieName($defSerie->ObtientLibelle(), "Serie".($j + 1));
				}
			}
			public function EnvoieImage()
			{
				$this->CalculeJeuDonnees() ;
				$cheminPolice = CHEM_REP_PCHART."/Fonts/".$this->NomFichierPolice ;

				// Initialise the graph
				$this->Support = new pChart($this->Largeur, $this->Hauteur);
				if($this->Abcisse->ValeurMin !== null && $this->Abcisse->ValeurMax !== null)
				{
					$this->Support->setFixedScale($this->Abcisse->ValeurMin, $this->Abcisse->ValeurMax);
				}
				
				$this->Support->setFontProperties($cheminPolice, $this->TaillePoliceTitre);
				$this->Support->setGraphArea($this->MargeGaucheForme, $this->MargeHautForme, $this->Largeur - $this->MargeDroiteForme, $this->Hauteur - $this->MargeBasForme);
				$this->Support->drawFilledRoundedRectangle($this->TailleCourbeArrPlan, $this->TailleCourbeArrPlan, $this->Largeur - $this->TailleCourbeArrPlan, $this->Hauteur - $this->TailleCourbeArrPlan, 5, 240, 240, 240);
				$this->Support->drawRoundedRectangle($this->TailleCourbeArrPlan - 2, $this->TailleCourbeArrPlan - 2, $this->Largeur + 2 - $this->TailleCourbeArrPlan, $this->Hauteur + 2 - $this->TailleCourbeArrPlan, 5, 230, 230, 230);
				$this->Support->drawGraphArea(255, 255, 255, TRUE) ;
				
				// Draw the cubic curve graph
				if($this->Forme != null && count($this->Points) > 0)
				{
					$this->Forme->Applique($this) ;
					$this->Support->setFontProperties($cheminPolice, $this->TaillePoliceEtiquetteSerie);
					foreach($this->DefinitionsSeries as $i => $defSerie)
					{
						$this->AppliqueEtiquetteSerie($defSerie) ;
					}
				}
				
				// Finish the graph
				if(count($this->Points) > 0)
				{
					$this->Support->setFontProperties($cheminPolice, $this->TaillePoliceLegende);
					$this->Support->drawLegend($this->Largeur - 100, 30, $this->JeuDonnees->GetDataDescription(), 255, 255, 255) ;
				}
				
				$this->Support->setFontProperties($cheminPolice, $this->TaillePoliceTitre);
				$this->Support->drawTitle(50, 22, $this->Titre, 50, 50, 50, $this->Largeur) ;
				//$this->Support->Render("example2.png");
				$this->Support->Stroke();
			}
			protected function AppliqueEtiquetteSerie(& $defSerie)
			{
				if($defSerie->EtiquetteDonnees == "")
				{
					return ;
				}
				foreach($this->DonneesSupport as $i => $ligne)
				{
					if(! isset($ligne[$defSerie->EtiquetteDonnees]))
					{
						break ;
					}
					$etiq = $ligne[$defSerie->EtiquetteDonnees] ;
					if($etiq != "")
					{
						$index = $defSerie->IndexChart + 1 ;
						$this->Support->setLabel($this->JeuDonnees->GetData(),$this->JeuDonnees->GetDataDescription(),"Serie".$index, $i, $etiq, 221,230,174);
					}
				}
			}
		}
		
		class PvFormeBasePChart extends PvObjet
		{
			protected function RecupCheminPolice(& $graphe)
			{
				$cheminPolice = CHEM_REP_PCHART."/Fonts/".$graphe->NomFichierPolice ;
				return $cheminPolice ;
			}
			public function Applique(& $graphe)
			{
			}
		}
		class PvDiagrammePChart extends PvFormeBasePChart
		{
			public $InscrirePointilles = 1 ;
			public $ConfigEchelle ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ConfigEchelle = new PvConfigEchellePChart() ;
			}
			public function Applique(& $graphe)
			{
				$this->CommenceRendu($graphe) ;
				$this->AppliqueRendu($graphe) ;
				$this->TermineRendu($graphe) ;
			}
			protected function CommenceRendu(& $graphe)
			{
				$cheminPolice = $this->RecupCheminPolice($graphe) ;
				
				$graphe->Support->drawScale(
					$graphe->JeuDonnees->GetData(),
					$graphe->JeuDonnees->GetDataDescription(),
					$this->ConfigEchelle->Mode,
					150, 150, 150,
					$this->ConfigEchelle->InclureMarques,
					$this->ConfigEchelle->Angle,
					$this->ConfigEchelle->TotalDecimaux,
					$this->ConfigEchelle->AvecMarge,
					$this->ConfigEchelle->AnnulerEtiquettes,
					$this->ConfigEchelle->PositionDroite
				);
				$graphe->Support->drawGrid(4, TRUE, 230, 230, 230, 50);
				// Draw the 0 line
				$graphe->Support->setFontProperties($cheminPolice, $graphe->TaillePoliceDonnees);
				$graphe->Support->drawTreshold(0, 143, 55, 72, TRUE, TRUE);
			}
			protected function AppliqueRendu(& $graphe)
			{
			}
			protected function TermineRendu(& $graphe)
			{
				$cheminPolice = $this->RecupCheminPolice($graphe) ;
				if($this->InscrirePointilles)
				{
					$graphe->Support->drawPlotGraph($graphe->JeuDonnees->GetData(), $graphe->JeuDonnees->GetDataDescription());
				}
			}
		}
		class PvLigneBatonPChart extends PvDiagrammePChart
		{
			public $InscrirePointilles = 0 ;
			protected function AppliqueRendu(& $graphe)
			{
				$graphe->Support->drawCubicCurve(
					$graphe->JeuDonnees->GetData(),
					$graphe->JeuDonnees->GetDataDescription()
				);
			}
		}
		class PvPointillesPChart extends PvDiagrammePChart
		{
			public $InclureMarques = 1 ;
		}
		class PvOndulationPChart extends PvDiagrammePChart
		{
			protected function AppliqueRendu(& $graphe)
			{
				$graphe->Support->drawLineGraph($graphe->JeuDonnees->GetData(),$graphe->JeuDonnees->GetDataDescription());
			}
		}
		class PvLigneLimiteePChart extends PvDiagrammePChart
		{
			public $InscrirePointilles = 0 ;
			protected function AppliqueRendu(& $graphe)
			{
				$graphe->Support->drawLimitsGraph($graphe->JeuDonnees->GetData(),$graphe->JeuDonnees->GetDataDescription(),3,2, 255, 255, 255);
			}
		}
		class PvLigneRempliePChart extends PvDiagrammePChart
		{
			public $InscrirePointilles = 0 ;
			protected function AppliqueRendu(& $graphe)
			{
				$graphe->Support->drawFilledLineGraph($graphe->JeuDonnees->GetData(),$graphe->JeuDonnees->GetDataDescription(), 50, TRUE);
			}
		}
		class PvCourbeRempliePChart extends PvDiagrammePChart
		{
			public $InscrirePointilles = 0 ;
			public $Precision = 0.1 ;
			public $ValeurAlpha = 60 ;
			public $AutourZero = false ;
			protected function AppliqueRendu(& $graphe)
			{
				$graphe->Support->drawFilledCubicCurve(
					$graphe->JeuDonnees->GetData(),
					$graphe->JeuDonnees->GetDataDescription(),
					$this->Precision,
					$this->ValeurAlpha,
					$this->AutourZero
				);
			}
		}
		class PvBarreBandesPChart extends PvDiagrammePChart
		{
			public $InscrirePointilles = 0 ;
			public $UtiliserOmbre = true ;
			public $ValeurOmbre = 80 ;
			protected function AppliqueRendu(& $graphe)
			{
				$graphe->Support->drawBarGraph($graphe->JeuDonnees->GetData(),$graphe->JeuDonnees->GetDataDescription(), $this->UtiliserOmbre, $this->ValeurOmbre);
			}
		}
		class PvBandesEmpileesPChart extends PvDiagrammePChart
		{
			public $InscrirePointilles = 0 ;
			public $ValeurAlpha = 100 ;
			public $Continu = false ;
			protected function AppliqueRendu(& $graphe)
			{
				$graphe->Support->drawStackedBarGraph($graphe->JeuDonnees->GetData(),$graphe->JeuDonnees->GetDataDescription(), $this->ValeurAlpha, $this->Continu);
			}
		}
		
		class PvGrapheCirculairePChart extends PvDiagrammePChart
		{
			public $QualiteAntiAlias = 0 ;
			public $TypeLibelle = PIE_PERCENTAGE_LABEL ;
			public $MargeRayon = 60 ;
			public $ValeurOblique = 50 ;
			public $ValeurEpaisseur = 20 ;
			public $TotalDecimaux = 0 ;
			public $DistanceEpaisseur = 5 ;
			public $AccentuerCouleurs = false ;
			public function Applique(& $graphe)
			{
				$this->CommenceRendu($graphe) ;
				$this->AppliqueRendu($graphe) ;
				$this->TermineRendu($graphe) ;
			}
			protected function CommenceRendu(& $graphe)
			{
				$cheminPolice = $this->RecupCheminPolice($graphe) ;
				$graphe->Support->createColorGradientPalette(195,204,56,223,110,41,5);
			}
			protected function AppliqueRendu(& $graphe)
			{
				$ancRapportErr = error_reporting(0);
				$cheminPolice = $this->RecupCheminPolice($graphe) ;
				$graphe->Support->setFontProperties($cheminPolice, $graphe->TaillePoliceEtiquette);
				$rayon = $this->ExtraitRayon($graphe) ;
				$graphe->Support->AntialiasQuality = $this->QualiteAntiAlias;
				$graphe->Support->drawPieGraph(
					$graphe->JeuDonnees->GetData(), $graphe->JeuDonnees->GetDataDescription(),
					$graphe->MargeGaucheForme + $rayon + ($this->MargeRayon / 2),
					$graphe->MargeHautForme + $rayon,
					$rayon,
					$this->TypeLibelle, $this->AccentuerCouleurs, $this->ValeurOblique, $this->ValeurEpaisseur, $this->DistanceEpaisseur, $this->TotalDecimaux
				);
				$graphe->Support->setFontProperties($cheminPolice, $graphe->TaillePoliceEtiquette);
				$graphe->Support->drawPieLegend($graphe->Largeur - $graphe->MargeDroiteForme - 100, $graphe->MargeHautForme + 20, $graphe->JeuDonnees->GetData(), $graphe->JeuDonnees->GetDataDescription(), 250,250,250);
				error_reporting($ancRapportErr) ;
			}
			protected function ExtraitRayon(& $graphe)
			{
				$largeurTravail = $graphe->Largeur - ($graphe->MargeDroiteForme + $graphe->MargeGaucheForme) ;
				$hauteurTravail = $graphe->Hauteur - ($graphe->MargeHautForme + $graphe->MargeBasForme) ;
				return intval((($largeurTravail < $hauteurTravail) ? $largeurTravail - $this->MargeRayon : $hauteurTravail - $this->MargeRayon) / 2) ;
			}
			protected function TermineRendu(& $graphe)
			{
				$cheminPolice = $this->RecupCheminPolice($graphe) ;
			}
		}
		
		class PvActionImagePChart extends PvActionEnvoiFichierBaseZoneWeb
		{
			public $TypeMime = "image/png" ;
			public $NomFichierAttache = "graphe" ;
			public $ExtensionFichierAttache = "png" ;
			protected function AfficheContenu()
			{
				$this->ComposantIUParent->EnvoieImage() ;
				exit ;
			}
		}
	}
	
?>