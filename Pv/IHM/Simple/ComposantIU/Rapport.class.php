<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_RAPPORT'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_IU_BASE'))
			include dirname(__FILE__)."/Noyau.class.php" ;
		
		class PvDefRequeteRapportHtml
		{
			public $Colonnes ;
			public $Requete ;
			public $Condition ;
			public $Groupage ;
			public $Tri ;
			public function CommeSql()
			{
				$sql = "" ;
				$sql .= 'select ' ;
				$sql .= ($this->Colonnes != "") ? $this->Colonnes : "*" ;
				$sql .= ' from '.$this->Requete ;
				if($this->Condition != "")
					$sql .= ' where '.$this->Condition ;
				if($this->Tri != "")
					$sql .= ' order by '.$this->Tri ;
				if($this->Groupage != "")
					$sql .= ' group by '.$this->Groupage ;
				return $sql ;
			}
		}
		class PvDefProcedureRapportHtml extends PvDefRequeteRapportHtml
		{
			public $NomProcedure ;
			public $Parametres = array() ;
		}
		class PvRapportHtml extends PvComposantIUDonneesSimple
		{
			public $TypeComposant = "RapportHTML" ;
			public $BaseDonnees ;
			public $DefRequete ;
			public $DefVolet ;
			public $DefFeuille ;
			public $CompVolet ;
			public $CompFeuille ;
			public $NomCompVolet = "Volet" ;
			public $NomCompFeuille = "Feuille" ;
			public $FiltresSelection = array() ;
			public $DessinFltsSelect ;
			public $TitreFormulaireFiltres = "" ;
			public $AlignBoutonSoumettreFormulaireFiltres = "left" ;
			public $TitreBoutonSoumettreFormulaireFiltres = "Appliquer" ;
			public $ToujoursAfficher = 0 ;
			public $CacherFormulaireFiltres = 0 ;
			public $SuffixeParamFiltresSoumis = "filtre" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DefRequete = new PvDefRequeteRapportHtml() ;
				$this->DefVolet = new PvDefVoletRapportHtml() ;
				$this->DefFeuille = new PvDefFeuilleRapportHtml() ;
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
			public function FiltresSoumis()
			{
				$nomParamFiltresSoumis = $this->NomParamFiltresSoumis() ;
				return ($this->ToujoursAfficher || (isset($_GET[$nomParamFiltresSoumis]))) ? 1 : 0 ;
			}
			public function NomParamFiltresSoumis()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamFiltresSoumis ;
			}
			protected function CreeDessinFltsSelect()
			{
				return new PvDessinFltsDonneesHtml() ;
			}
			protected function CreeCompVolet()
			{
				return new PvCompVoletRapportHtml() ;
			}
			protected function CreeCompFeuille()
			{
				return new PvCompFeuilleRapportHtml() ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeCompVolet() ;
				$this->ChargeCompFeuille() ;
				$this->ChargeDessinFltsSelect() ;
			}
			protected function ChargeCompVolet()
			{
				$this->CompVolet = $this->CreeCompVolet() ;
				$this->CompVolet->AdopteRapport($this->NomCompVolet, $this) ;
				$this->CompVolet->ChargeConfig() ;
			}
			protected function ChargeCompFeuille()
			{
				$this->CompFeuille = $this->CreeCompFeuille() ;
				$this->CompFeuille->AdopteRapport($this->NomCompFeuille, $this) ;
				$this->CompFeuille->ChargeConfig() ;
			}
			protected function ChargeDessinFltsSelect()
			{
				$this->DessinFltsSelect = $this->CreeDessinFltsSelect() ;
			}
			public function PossedeFiltresRendus()
			{
				$nomFiltres = array_keys($this->FiltresSelection) ;
				$ok = 0 ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$ok = $this->FiltresSelection[$nomFiltre]->RenduPossible() ;
					if($ok)
					{
						break ;
					}
				}
				return $ok ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->CacherFormulaireFiltres == 1 || ! $this->PossedeFiltresRendus())
				{
					return '' ;
				}
				$ctn = '' ;
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="SoumetFormulaire'.$this->IDInstanceCalc.'(this)">'.PHP_EOL ;
				$ctn .= '<table width="100%" cellspacing="0">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<tr>'.PHP_EOL ;
					$ctn .= '<th align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</th>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$ctn .= '<tr>'.PHP_EOL ;
				$ctn .= '<td>'.PHP_EOL ;
				$ctn .= $this->DessinFltsSelect->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '<tr class="Boutons">'.PHP_EOL ;
				$ctn .= '<td align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'">'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '<button type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresSelection) ;
				return $ctn ;
			}
			public function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduFormulaireFiltres() ;
				return $ctn ;
			}
		}
		
		class PvElementIURapportHtml extends PvComposantIUBase
		{
			public $NomElementRapport ;
			public $RapportParent ;
			public function AdopteRapport($nom, & $rapport)
			{
				$this->NomElementRapport = $nom ;
				$this->RapportParent = & $rapport ;
			}
		}
		
		class PvDefVoletRapportHtml extends PvObjet
		{
			public $Noeuds = array() ;
		}
		class PvDefNoeudVoletRapportHtml
		{
			public $LibelleDonnees ;
			public $NomDonnees ;
			public $AliasDonnees ;
		}
		
		class PvDefFeuilleRapportHtml extends PvObjet
		{
			public $Sections = array() ;
		}
	
		class PvCompVoletRapportHtml extends PvElementIURapportHtml
		{
			public $Elements = array() ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
			}
			protected function ExtraitSqlSelection()
			{
			}
			protected function CalculeDonneesRendu()
			{
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
		class PvCompFeuilleRapportHtml extends PvElementIURapportHtml
		{
		}
		
		class PvCompSectionBaseRapportHtml extends PvElementIURapportHtml
		{
		}
		class PvPortionRapportHtml extends PvCompSectionBaseRapportHtml
		{
			public $Contenu ;
		}
		class PvFormulaireRapportHtml extends PvCompSectionBaseRapportHtml
		{
		}
		class PvTableauRapportHtml extends PvCompSectionBaseRapportHtml
		{
		}
	}
?>