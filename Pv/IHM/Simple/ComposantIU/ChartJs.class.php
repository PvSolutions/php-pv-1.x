<?php
	
	if(! defined('PV_CHART_JS'))
	{
		class PvChartJs extends PvComposantIUDonneesSimple
		{
			public $TypeComposant = "Chart" ;
			public $Largeur = 450 ;
			public $Hauteur = 450 ;
			public $ElementsBruts = array() ;
			public $Elements = array() ;
			public $ElementsTrouves = 0 ;
			protected $ErreurTrouvee = 0 ;
			protected $ContenuErreurTrouvee = "" ;
			protected $MsgSiErreurTrouvee = "Le composant ne peut s'afficher car une erreur est survenue lors de l'affichage." ;
			public $FiltresSelection = array() ;
			public $ColonneLabel ;
			public $ColonnesDataset = array() ;
			public $CfgInit ;
			public static $SourceIncluse = false ;
			public $CheminJsSource = "js/Chart.bundle.min.js" ;
			public $CheminCSSSource = "js/Chart.min.css" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ColonneLabel = new PvColonneDataChartJs() ;
			}
			public function & InsereFltSelectRef($nom, & $filtreRef, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectCookie($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectSession($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectMembreConnecte($nom, $nomParamLie='', $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpGet($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpPost($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			protected function VideErreur()
			{
				$this->ErreurTrouvee = 0 ;
				$this->ContenuErreurTrouvee = "" ;
			}
			protected function ConfirmeErreur($msg)
			{
				$this->ErreurTrouvee = 1 ;
				$this->ContenuErreurTrouvee = $msg ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
			}
			protected function PrepareCalcul()
			{
				$this->ElementsTrouves = 0 ;
				$this->VideErreur() ;
				$this->ElementsBruts = array() ;
				$this->Elements = array() ;
			}
			protected function & ExtraitDefCols()
			{
				$defCols = array() ;
				$colsData = array($this->ColonneLabel) ;
				array_splice($colsData, count($colsData), 0, $this->ColonnesDataset) ;
				foreach($colsData as $i => $col)
				{
					$defCol = new PvDefinitionColonneDonnees() ;
					$defCol->NomDonnees = $col->NomColonne ;
					$defCol->AliasDonnees = $col->AliasColonne ;
					$defCols[] = $defCol ;
				}
				return $defCols ;
			}
			public function CalculeElementsRendu()
			{
				$this->PrepareCalcul() ;
				$this->Elements = $this->FournisseurDonnees->SelectElements($this->ExtraitDefCols(), $this->FiltresSelection) ;
				$this->CfgInit = new PvCfgInitChartJs() ;
				foreach($this->ColonnesDataset as $i => $col)
				{
					$ds = new PvDatasetChartJs() ;
					$ds->label = $col->Libelle ;
					$ds->backgroundColor = $col->CouleursBackground ;
					$this->CfgInit->data->datasets[] = $ds ;
				}
				foreach($this->Elements as $i => $lgn)
				{
					$this->CfgInit->data->labels[] = $lgn[$this->ColonneLabel->NomColonne] ;
					foreach($this->ColonnesDataset as $i => $col)
					{
						$this->CfgInit->data->datasets[$i]->data[] = $lgn[$col->NomColonne] ;
					}
				}
			}
			public function DefinitColLabel($nom, $libelle='', $alias='')
			{
				$this->ColonneLabel->NomColonne = $nom ;
				$this->ColonneLabel->Libelle = ($libelle != '') ? $libelle : $nom ;
				$this->ColonneLabel->AliasColonne = $alias ;
			}
			public function DefinitColonneLabel($nom, $libelle='', $alias='')
			{
				$this->DefinitColLabel($nom, $libelle, $alias) ;
			}
			public function & InsereColData($nom, $libelle='', $alias='')
			{
				$col = new PvColonneDataChartJs() ;
				$col->NomColonne = $nom ;
				$col->Libelle = ($libelle != '') ? $libelle : $nom ;
				$col->AliasColonne = $alias ;
				$this->ColonnesDataset[] = & $col ;
				return $col ;
			}
			public function & InsereColonne($nom, $libelle='', $alias='')
			{
				return $this->InsereColData($nom, $libelle, $alias) ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CalculeElementsRendu() ;
				$ctn = '' ;
				$ctn .= $this->RenduSource() ;
				$ctn .= '<canvas id="'.$this->IDInstanceCalc.'" width="'.$this->Largeur.'" height="'.$this->hauteur.'"></canvas>'.PHP_EOL ;
				$ctn .= $this->RenduDefsJs() ;
				return $ctn ;
			}
			protected function RenduSource()
			{
				$ctn = '' ;
				if(PvChartJs::$SourceIncluse == true)
				{
					return $ctn ;
				}
				$ctn .= $this->RenduLienCSS($this->CheminCSSSource) ;
				$ctn .= $this->RenduLienJs($this->CheminJsSource) ;
				PvChartJs::$SourceIncluse = true ;
				return $ctn ;
			}
			protected function RenduDefsJs()
			{
				$ctn = '' ;
				$ctnJs = 'jQuery(function() {
var ctx = document.getElementById("'.$this->IDInstanceCalc.'") ;
var chart'.$this->IDInstanceCalc.' = new Chart(ctx, '.svc_json_encode($this->CfgInit).') ;
})' ;
				$ctn .= $this->RenduContenuJs($ctnJs) ;
				return $ctn ;
			}
		}
		
		class PvColonneDataChartJs
		{
			public $Libelle ;
			public $NomColonne ;
			public $AliasColonne ;
			public $CouleursBackground = array() ;
			public $CouleursBordure = array() ;
		}
		
		class PvCfgInitChartJs
		{
			public $type = "bar" ;
			public $data ;
			public $options ;
			public function __construct()
			{
				$this->data = new PvDataChartJs() ;
				$this->options = new StdClass() ;
			}
		}
		class PvDataChartJs
		{
			public $labels = array() ;
			public $datasets = array() ;
		}
		class PvDatasetChartJs
		{
			public $label ;
			public $data = array() ;
			public $backgroundColor = array() ;
			public $borderColor = array() ;
			public $borderWidth = 1 ;
		}
		
	}
	
?>