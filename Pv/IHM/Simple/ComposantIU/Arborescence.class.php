<?php

	if(! defined('PV_COMPOSANT_SIMPLE_IU_ARBORESCENCE'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_IU_TABLEAU'))
			include dirname(__FILE__)."/Tableau.class.php" ;
		define('PV_COMPOSANT_SIMPLE_IU_ARBORESCENCE', 1) ;

		class PvComposantNoeudDonneesBase extends PvComposantIUBase
		{
			public $Contenu ;
			public $DonneesRendu = null ;
		}

		class PvDefinitionNoeudDonnees extends PvDefinitionColonneDonnees
		{
			public $TriPrealable = 1 ;
			public $TriPossible = 0 ;
			public $RequeteSelection = '' ;
			public $ExpressionLiaison = '' ;
			public $ValeurEnCours = false ;
			public $ComposantFin ;
			public $ComposantVide ;
			public $ComposantDebut ;
			public $RenduDebutParDefaut = '<li>${VALEUR_ACTUELLE}<ul>' ;
			public $RenduVideParDefaut = '' ;
			public $RenduFinParDefaut = '</ul></li>' ;
			public $IndLgnActuelle ;
			public $IndColActuelle ;
			public $LigneActuelle ;
			public $ArborescenceActuelle ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				/*
				$this->ComposantDebut = new PvComposantNoeudDonneesBase() ;
				$this->ComposantVide = new PvComposantNoeudDonneesBase() ;
				$this->ComposantFin = new PvComposantNoeudDonneesBase() ;
				*/
			}
			protected function PrepareRendu(& $arbr, $indLgn, $indCol, $donnees)
			{
				$this->ArborescenceActuelle = $arbr ;
				$this->IndLgnActuelle = $indLgn ;
				$this->IndColActuelle = $indCol ;
				$this->LigneActuelle = $donnees ;
			}
			public function RenduComposantDebut(& $arbr, $indLgn, $indCol, $donnees)
			{
				$this->PrepareRendu($arbr, $indLgn, $indCol, $donnees) ;
				if($this->EstNul($this->ComposantDebut))
					return _parse_pattern($this->RenduDebutParDefaut, $donnees) ;
				return $this->ComposantDebut->RenduDispositif() ;
			}
			public function RenduComposantVide(& $arbr, $indLgn, $indCol, $donnees)
			{
				$this->PrepareRendu($arbr, $indLgn, $indCol, $donnees) ;
				if($this->EstNul($this->ComposantVide))
					return _parse_pattern($this->RenduVideParDefaut, $donnees) ;
				return $this->ComposantVide->RenduDispositif() ;
			}
			public function RenduComposantFin(& $arbr, $indLgn, $indCol, $donnees)
			{
				$this->PrepareRendu($arbr, $indLgn, $indCol, $donnees) ;
				if($this->EstNul($this->ComposantFin))
					return _parse_pattern($this->RenduFinParDefaut, $donnees) ;
				return $this->ComposantFin->RenduDispositif() ;
			}
		}

		class PvArborescenceDonneesHtml extends PvTableauDonneesHtml
		{
			public $RenduDebutRangee = '<ul class="RangeeDonnees">' ;
			public $RenduFinRangee = '</ul>' ;
			public function ObtientDefColsRendu()
			{
				$defCols = parent::ObtientDefColsRendu() ;
				foreach($this->DefinitionsColonnes as $i => $defCol)
				{
					$colTemp = new PvDefinitionColonneDonnees() ;
					$colTemp->Visible = 0 ;
					$colTemp->NomDonnees = "VAL_NOEUD_".$i ;
					if($defCol->NomDonneesTri != '')
					{
						$colTemp->AliasDonnees = ($defCol->AliasDonneesTri == '') ? $defCol->NomDonneesTri : $defCol->AliasDonneesTri ;
					}
					else
					{
						$colTemp->AliasDonnees = $defCol->NomDonnees ;
					}
					$defCols[] = $colTemp ;
				}
				return $defCols ;
			}
			public function CalculeElementsRendu()
			{
				$this->FournisseurDonnees->RequeteSelection = "(".$this->ObtientRequeteSelection().")" ;
				// $this->AjusteDefinitionsColonnes() ;
				parent::CalculeElementsRendu() ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
			}
			protected function ObtientRequeteSelection()
			{
				$requeteSql = '' ;
				$liaisons = '' ;
				$indiceCol = 0 ;
				$nomCols = array_keys($this->DefinitionsColonnes) ;
				foreach($nomCols as $i => $nomCol)
				{
					$col = $this->DefinitionsColonnes[$nomCol] ;
					if($indiceCol > 0)
					{
						$liaisons .= ' left join ' ;
					}
					$liaisons .= $col->RequeteSelection.' NOEUD_'.$indiceCol ;
					if($indiceCol > 0)
					{
						$exprLiaison = $col->ExpressionLiaison ;
						$exprLiaison = str_replace('<noeud_parent>', 'NOEUD_'.($indiceCol - 1), $exprLiaison) ;
						$exprLiaison = str_replace('<noeud_en_cours>', 'NOEUD_'.$indiceCol, $exprLiaison) ;
						$liaisons .= ' on '.$exprLiaison ;
					}
					$indiceCol++ ;
				}
				$requeteSql = 'select * from '.$liaisons ;
				return $requeteSql ;
			}
			protected function RenduEnteteRangeeDonnees()
			{
				return "" ;
			}
			protected function RenduRangeeDonnees()
			{
				$ctn = '' ;
				$nomCols = array_keys($this->DefinitionsColonnes) ;
				$ctn .= $this->RenduEnteteRangeeDonnees() ;
				$ctn .= $this->RenduDebutRangee ;
				foreach($this->ElementsEnCours as $i => $ligne)
				{
					$attrsLign = array_keys($ligne) ;
					foreach($nomCols as $j => $nomCol)
					{
						$ligne["VALEUR_ACTUELLE"] = $ligne[$attrsLign[$j]] ;
						$defCol = & $this->DefinitionsColonnes[$nomCol] ;
						if($ligne["VAL_NOEUD_".$j] != $defCol->ValeurEnCours)
						{
							if($i > 0)
							{
								$ctn .= $defCol->RenduComposantFin($this, $i, $j, $ligne) ;
							}
							$ctn .= $defCol->RenduComposantDebut($this, $i, $j, $ligne) ;
						}
						else
						{
							$ctn .= $defCol->RenduComposantVide($this, $i, $j, $ligne) ;
						}
						$defCol->ValeurEnCours = $ligne["VAL_NOEUD_".$j] ;
					}
				}
				if(count($this->ElementsEnCours) > 0)
				{
					foreach($nomCols as $j => $nomCol)
					{
						$ctn .= $defCol->RenduComposantFin($this, $i, $j, $ligne) ;
					}
				}
				$ctn .= $this->RenduFinRangee ;
				return $ctn ;
			}
			protected function RenduValeurColonne($nomCol, $j, $ligne)
			{
				$colonne = $this->DefinitionsColonnes[$nomCol] ;
				$ctn = '' ;
				if($colonne->ValeurEnCours === false || $valeurEnCours != $colonne->ValeurEnCours)
				{
					$ctn .= '<li>' ;
					$ctn .= htmlentities($valeurEnCours) ;
					$ctn .= '</li>' ;
				}
				return $ctn ;

			}
		}
		
		class PvRapportDonneesHtml extends PvArborescenceDonneesHtml
		{
			public $RenduDebutRangee = '<table width="100%" cellspacing="0" cellpadding="0" class="RangeeDonnees">
<tr>' ;
			public $RenduFinRangee = '</tr>
</table>' ;
		}
		
		class PvDefColDonneesHtml extends PvDefinitionNoeudDonnees
		{
			public $IntegrAutoValActuelle = 1 ;
			public $RenduDebutParDefaut = '' ;
			public $RenduVideParDefaut = '' ;
			public $RenduFinParDefaut = '</td></tr></table>' ;
			protected function RenduCtnDebutCell()
			{
				$ctn = '' ;
				$ctn .= '<td' ;
				if($this->Largeur != '')
					$ctn .= ' width="'.$this->LargeurElement.'"' ;
				if($this->AlignElement != '')
					$ctn .= ' align="'.$this->AlignElement.'"' ;
				if($this->AlignVElement != '')
					$ctn .= ' valign="'.$this->AlignVElement.'"' ;
				$ctn .= '>' ;
				if($this->IntegrAutoValActuelle)
				{
					$ctn .= '${VALEUR_ACTUELLE}</td>' ;
				}
				return $ctn ;
			}
			protected function PrepareRendu(& $arbr, $indLgn, $indCol, $donnees)
			{
				parent::PrepareRendu($arbr, $indLgn, $indCol, $donnees) ;
				$this->RenduDebutParDefaut = $this->RenduCtnDebutCell() ;
			}
		}
		class PvDefGrpColDonneesHtml extends PvDefColDonneesHtml
		{
			public $RenduDebutParDefaut = '' ;
			public $RenduVideParDefaut = '' ;
			public $IntegrAutoValActuelle = 0 ;
			protected function PrepareRendu(& $arbr, $indLgn, $indCol, $donnees)
			{
				parent::PrepareRendu($arbr, $indLgn, $indCol, $donnees) ;
				$this->RenduDebutParDefaut .= 
				$this->RenduDebutParDefaut .= '${VALEUR_ACTUELLE}</td><td width="100%"><table width="100%" cellspacing="0" cellpadding="0"><tr>' ;
			}
		}
		
	}

?>