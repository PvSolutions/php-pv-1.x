<?php
	
	if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
	{
		if(! defined('PV_IHM_NOYAU_SIMPLE'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_FOURNISSEUR_DONNEES_SIMPLE', 1) ;
		
		class PvExpressionFiltre
		{
			public $Texte = "" ;
			public $Parametres = array() ;
			public function EstVide()
			{
				return ($this->Texte == "") ? 1 : 0 ;
			}
		}
		class PvRequeteFournisseurDonnees
		{
			public $RessourceSupport = false ;
			public $Position = 0 ;
		}
		
		class PvFournisseurDonneesBase extends PvObjet
		{
			public $DerniereException = null ;
			protected function CreeException()
			{
				return new PvExceptionSimpleBase() ;
			}
			public function ExceptionTrouvee()
			{
				return ($this->DerniereException != null) ? 1 : 0 ;
			}
			public function MessageException()
			{
				if($this->DerniereException == null)
				{
					return "" ;
				}
				return $this->DerniereException->Message ;
			}
			protected function VideDerniereException()
			{
			}
			public function ExecuteRequete($requete, $params=array())
			{
				return array() ;
			}
			public function SelectElementsTries($colonnes, $filtres, $indiceColonneTri=0, $sensColonneTri="asc")
			{
				
			}
			public function RangeeElementsTries($colonnes, $filtres, $indiceDebut=0, $maxElements=100, $indiceColonneTri=0, $sensColonneTri="asc")
			{
				
			}
			public function SelectElements($colonnes, $filtres, $indiceColonneTri=0, $sensColonneTri="asc")
			{
			}
			public function RangeeElements($colonnes, $filtres, $indiceDebut=0, $maxElements=100, $indiceColonneTri=0, $sensColonneTri="asc")
			{
			}
			public function CompteElements($colonnes, $filtres)
			{
			}
			public function LigneElement($colonnes, $filtresGlobaux, $filtresLigne, $indiceColonneTri=0, $sensColonneTri="asc")
			{
			}
			public function AjoutElement($filtresEdition)
			{
			}
			public function ModifElement($filtresSelection, $filtresEdition)
			{
			}
			public function SupprElement($filtresSelection)
			{
			}
			public function OuvreRequeteSelectElements($filtres, $colonnes=array())
			{
			}
			public function LitRequete(& $requete)
			{
			}
			public function FermeRequete(& $requete)
			{
			}
			public function RechExacteElements($filtres, $nomColonne, $valeur)
			{
			}
			public function RechsExactesElements($filtres, $nomColonne, $valeurs)
			{
			}
			public function RechPartielleElements($filtres, $nomColonnes, $valeur)
			{
			}
			public function RechDebuteElements($filtres, $nomColonnes, $valeur)
			{
			}
			public function EncodeEntiteHtml($valeur)
			{
				return htmlentities($valeur) ;
			}
			public function EncodeAttrHtml($valeur)
			{
				return htmlspecialchars($valeur) ;
			}
			public function EncodeUrl($valeur)
			{
				return urlencode($valeur) ;
			}
		}
		class PvFournisseurDonneesNatif extends PvFournisseurDonneesBase
		{
			public $RequeteSelection ;
		}
		class PvFournisseurDonneesDirect extends PvFournisseurDonneesNatif
		{
			public $Valeurs = array() ;
			public $UtiliserPremValeurSiVide = 1 ;
			public static function CreePourValeurs($valeurs=array(), $reqSelection='valeurs')
			{
				$fournisseur = new PvFournisseurDonneesDirect() ;
				if(empty($reqSelect))
					$reqSelect = 'valeurs' ;
				$fournisseur->RequeteSelection = $reqSelect ;
				$fournisseur->Valeurs[$reqSelect] = $valeurs ;
				return $fournisseur ;
			}
			protected function ExtraitListeColonnes($colonnes)
			{
				$liste = array() ;
				foreach($colonnes as $i => $colonne)
				{
					if($colonne->NomDonnees != '')
					{
						$liste[] = $colonne->NomDonnees ;
					}
				}
				if(count($colonnes) == 0)
				{
					$liste[] = "*" ;
				}
				return $liste ;
			}
			public function RechExacteElements($filtres, $nomColonne, $valeur)
			{
				$lignes = array() ;
				if($this->RequeteSelection == "")
				{
					$nomValeurs = array_keys($this->Valeurs) ;
					if(count($nomValeurs))
						$this->RequeteSelection = $nomValeurs[0] ;
				}
				if(! isset($this->Valeurs[$this->RequeteSelection]))
				{
					return array() ;
				}
				$valeursSel = $this->Valeurs[$this->RequeteSelection] ;
				foreach($valeursSel as $i => $ligneTemp)
				{
					if(! isset($ligneTemp[$nomColonne]))
						break ;
					if($ligneTemp[$nomColonne] == $valeur)
					{
						$lignes[] = $ligneTemp ;
					}
				}
				return $lignes ;
			}
			public function SelectElements($colonnes, $filtres, $indiceColonneTri=0, $sensColonneTri="asc")
			{
				if($this->RequeteSelection == "")
				{
					$nomValeurs = array_keys($this->Valeurs) ;
					if(count($nomValeurs))
						$this->RequeteSelection = $nomValeurs[0] ;
				}
				if(! isset($this->Valeurs[$this->RequeteSelection]))
				{
					return array() ;
				}
				$listeColonnes = $this->ExtraitListeColonnes($colonnes) ;
				$resultats = array() ;
				foreach($this->Valeurs[$this->RequeteSelection] as $i => $element)
				{
					$resultat = array() ;
					foreach($listeColonnes as $i => $colonne)
					{
						if($colonne == "*")
						{
							$resultat = $element ;
							break ;
						}
						if(isset($element[$colonne]))
						{
							$resultat[$colonne] = $element[$colonne] ;
						}
					}
					$resultats[] = $resultat ;
				}
				return $resultats ;
			}
			public function RangeeElements($colonnes, $filtres, $indiceDebut=0, $maxElements=100, $indiceColonneTri=0, $sensColonneTri="asc")
			{
				if($this->RequeteSelection == "")
				{
					$nomValeurs = array_keys($this->Valeurs) ;
					if(count($nomValeurs))
						$this->RequeteSelection = $nomValeurs[0] ;
				}
				if(! isset($this->Valeurs[$this->RequeteSelection]))
				{
					return array() ;
				}
				$listeColonnes = $this->ExtraitListeColonnes($colonnes) ;
				$resultats = array() ;
				foreach($this->Valeurs[$this->RequeteSelection] as $i => $element)
				{
					if($i < $indiceDebut || $i >= $indiceDebut + $maxElements)
						continue ;
					$resultat = array() ;
					foreach($listeColonnes as $i => $colonne)
					{
						if($colonne == "*")
						{
							$resultat = $element ;
							break ;
						}
						// print_r($element[$colonne]) ;
						if(isset($element[$colonne]))
						{
							$resultat[$colonne] = $element[$colonne] ;
						}
					}
					$resultats[] = $resultat ;
				}
				return $resultats ;
			}
			public function CompteElements($colonnes, $filtres)
			{
				if($this->RequeteSelection == "")
				{
					$nomValeurs = array_keys($this->Valeurs) ;
					if(count($nomValeurs))
						$this->RequeteSelection = $nomValeurs[0] ;
				}
				if(! isset($this->Valeurs[$this->RequeteSelection]))
				{
					return 0 ;
				}
				return count($this->Valeurs[$this->RequeteSelection]) ;
			}
			public function LigneElement($colonnes, $filtresGlobaux, $filtresLigne, $indiceColonneTri=0, $sensColonneTri="asc")
			{
			}
			public function AjoutElement($filtresEdition)
			{
			}
			public function ModifElement($filtresSelection, $filtresEdition)
			{
			}
			public function SupprElement($filtresSelection)
			{
			}
			public function OuvreRequeteSelectElements($filtres, $colonnes=array())
			{
				$requete = false ;
				if(! isset($this->Valeurs[$this->RequeteSelection]))
				{
					return $requete ;
				}
				// print_r($this->Valeurs) ;
				$requete = new PvRequeteFournisseurDonnees() ;
				$requete->RessourceSupport = $this->RequeteSelection ;
				return $requete ;
			}
			public function LitRequete(& $requete)
			{
				if($requete == false or $requete->RessourceSupport == false)
					return false ;
				$ligne = false ;
				if(isset($this->Valeurs[$requete->RessourceSupport]) && isset($this->Valeurs[$requete->RessourceSupport][$requete->Position]))
				{
					$ligne = $this->Valeurs[$requete->RessourceSupport][$requete->Position] ;
				}
				$requete->Position++ ;
				return $ligne ;
			}
			public function FermeRequete(& $requete)
			{
				if($requete == false or $requete->RessourceSupport == false)
					return false ;
				$requete->RessourceSupport = "" ;
				return true ;
			}
		}
		class PvFournisseurDonneesBool extends PvFournisseurDonneesDirect
		{
			public $NomCleBool = "Bool" ;
			public $ValeurVrai = "1" ;
			public $ValeurFaux = "0" ;
			public $LibelleVrai = "Oui" ;
			public $LibelleFaux = "Non" ;
			public $NomAttributValeur = "Valeur" ;
			public $NomAttributLibelle = "Libelle" ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->RemplitValeursBool() ;
			}
			protected function RemplitValeursBool()
			{
				$this->Valeurs[$this->NomCleBool] = array(
					array(
						$this->NomAttributLibelle => $this->LibelleVrai,
						$this->NomAttributValeur => $this->ValeurVrai,
					),
					array(
						$this->NomAttributLibelle => $this->LibelleFaux,
						$this->NomAttributValeur => $this->ValeurFaux,
					),
				) ;
			}
			public static function Cree($valeurVrai=null, $libelleVrai=null, $valeurFaux=null, $libelleFaux=null)
			{
				$fournisseur = new PvFournisseurDonneesBool() ;
				if($valeurVrai !== null)
					$fournisseur->ValeurVrai = $valeurVrai ;
				if($libelleVrai !== null)
					$fournisseur->LibelleVrai = $libelleVrai ;
				if($valeurFaux !== null)
					$fournisseur->ValeurFaux = $valeurFaux ;
				if($libelleFaux !== null)
					$fournisseur->LibelleFaux = $libelleFaux ;
				return $fournisseur ;
			}
		}
		class PvFournisseurDonneesRangee extends PvFournisseurDonneesDirect
		{
			public $NomCleRangee = "Rangee" ;
			public $ValeurMin = 0 ;
			public $ValeurMax = 0 ;
			public $NomAttributValeur = "Valeur" ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$vals = array() ;
				for($i=$this->ValeurMin; $i <= $this->ValeurMax; $i++)
				{
					$vals[] = array($this->NomAttributValeur => $i) ;
				}
				$this->Valeurs[$this->NomCleRangee] = $vals ;
			}
			public static function Cree($valeurMin, $valeurMax)
			{
				$fournisseur = new PvFournisseurDonneesRangee() ;
				$fournisseur->ValeurMin = $valeurMin ;
				$fournisseur->ValeurMax = $valeurMax ;
				return $fournisseur ;
			}
		}
		
		class PvFournisseurDonneesSql extends PvFournisseurDonneesBase
		{
			public $BaseDonnees = null ;
			public $RequeteSelection = "" ;
			public $ParamsSelection = array() ;
			public $TableEdition = "" ;
			public $ParamsEdition = array() ;
			public $UtiliserProcedures = 1 ;
			public $NomProcSelectElements = "" ;
			public $NomProcCompteElements = "" ;
			public $NomProcRangeeElements = "" ;
			public $NomProcLigneElement = "" ;
			public $NomProcAjoutElement = "" ;
			public $NomProcModifElement = "" ;
			public $NomProcSupprElement = "" ;
			public $NommerRequeteSelection = 1 ;
			public $AliasRequeteSelection = "t1" ;
			public function ExecuteRequete($requete, $params=array())
			{
				if(! $this->BaseDonneesValide())
					return null ;
				$lgns = $this->BaseDonnees->FetchSqlRows($requete, $params) ;
				return $lgns ;
			}
			protected function SauveExceptionBaseDonnees()
			{
				$this->DerniereException = null ;
				if($this->BaseDonnees == null or $this->BaseDonnees->ConnectionException == '')
				{
					return ;
				}
				$this->DerniereException = $this->CreeException() ;
				$this->DerniereException->Code = 1 ;
				$this->DerniereException->CheminFichier = __FILE__ ;
				$this->DerniereException->NumeroLigne = __LINE__ ;
				$this->DerniereException->Message = $this->BaseDonnees->ConnectionException ;
				$this->DerniereException->Parametres = array(
					$this->BaseDonnees->LastSqlText,
					$this->BaseDonnees->LastSqlParams,
				) ;
			}
			public function BaseDonneesValide()
			{
				return ($this->BaseDonnees != null) ;
			}
			public function ExtraitTexteColonnes($colonnes)
			{
				$texte = "" ;
				foreach($colonnes as $i => $colonne)
				{
					if($texte != "")
					{
						$texte .= ", " ;
					}
					if($colonne->NomDonnees == '')
					{
						$texte .= "'' ".$colonne->IDInstanceCalc ;
					}
					else
					{
						if($colonne->AliasDonnees != "")
						{
							$texte .= $colonne->AliasDonnees." " ;
						}
						$texte .= $this->BaseDonnees->EscapeVariableName($colonne->NomDonnees) ;
					}
				}
				if(count($colonnes) == 0)
				{
					$texte = "*" ;
				}
				return $texte ;
			}
			protected function ExtraitTexteTriColonnes($colonnes)
			{
				$ctn = '' ;
				foreach($colonnes as $i => $colonne)
				{
					$nomDonnees = ($colonne->NomDonneesTri != '') ? $colonne->NomDonneesTri : $colonne->NomDonnees ;
					$aliasDonnees = "" ;
					$aliasDonnees = ($colonne->AliasDonneesTri != '') ? $colonne->AliasDonneesTri : $colonne->NomDonnees ;
					if($nomDonnees != '' && $colonne->TriPrealable == 1)
					{
						if($ctn != '')
						{
							$ctn .= ', ' ;
						}
						$orientation = (strtolower($colonne->OrientationTri) == "asc") ? "asc" : "desc" ;
						$valeurTri = ($aliasDonnees != "") ? $aliasDonnees : $this->BaseDonnees->EscapeVariableName($nomDonnees) ;
						$ctn .= $valeurTri." ".$orientation ;
					}
				}
				return $ctn ;
			}
			public function ExtraitTexteTri($colonnes, $indiceColonneTri=0, $sensColonneTri="asc")
			{
				$texteTri = "" ;
				if($indiceColonneTri > -1 && count($colonnes) > 0 && isset($colonnes[$indiceColonneTri]) && $colonnes[$indiceColonneTri]->TriPrealable == 0)
				{
					if($colonnes[$indiceColonneTri]->AliasDonneesTri != '')
					{
						$valeurTri = $colonnes[$indiceColonneTri]->AliasDonneesTri ;
					}
					elseif($colonnes[$indiceColonneTri]->NomDonneesTri != '')
					{
						$valeurTri = $this->BaseDonnees->EscapeVariableName($colonnes[$indiceColonneTri]->NomDonneesTri) ;
					}
					elseif($colonnes[$indiceColonneTri]->AliasDonnees != "")
					{
						$valeurTri = $colonnes[$indiceColonneTri]->AliasDonnees ;
					}
					elseif($colonnes[$indiceColonneTri]->NomDonnees != "")
					{
						$valeurTri = $this->BaseDonnees->EscapeVariableName($colonnes[$indiceColonneTri]->NomDonnees) ;
					}
					if($valeurTri == '')
					{
						$valeurTri = "''" ;
					}
					$texteTri .= $valeurTri." ".$sensColonneTri ;
				}
				return $texteTri ;
			}
			public function ExtraitExpressionFiltres($filtres)
			{
				$expression = new PvExpressionFiltre() ;
				foreach($filtres as $i => $filtre)
				{
					if($filtre->NePasIntegrerParametre || $filtre->NomParametreDonnees == "")
					{
						continue ;
					}
					$valeur = $filtre->LiePourTraitement() ;
					if($filtre->NePasInclure())
					{
						continue ;
					}
					$texte = str_ireplace(array('${this}', '<SELF>', '${luimeme}'), $this->BaseDonnees->ParamPrefix.$filtre->NomParametreDonnees, $filtre->ExpressionDonnees) ;
					if($texte == "")
						continue ;
					if($expression->Texte != "")
					{
						$expression->Texte .= " and " ;
					}
					$expression->Parametres[$filtre->NomParametreDonnees] = $valeur ;
					$expression->Texte .= $texte ;
				}
				return $expression ;
			}
			public function ExtraitParametresFiltres($filtres)
			{
				$parametres = array() ;
				foreach($filtres as $i => $filtre)
				{
					if($filtre->NePasLierColonne || $filtre->NomColonneLiee == "")
					{
						continue ;
					}
					$valeur = $filtre->LiePourTraitement() ;
					if($filtre->NePasInclure())
					{
						continue ;
					}
					$parametres[$filtre->NomColonneLiee] = $valeur ;
					if($filtre->ExpressionColonneLiee != '')
					{
						$expression = str_ireplace(array('${this}', '<SELF>', '${luimeme}'), $this->BaseDonnees->ExprParamPattern, $filtre->ExpressionColonneLiee) ;
						$parametres[$this->BaseDonnees->ExprKeyName][$filtre->NomColonneLiee] = $expression ;
					}
				}
				return $parametres ;
			}
			protected function ChaineRequeteSelection()
			{
				$req = "" ;
				if($this->NommerRequeteSelection == 0)
				{
					$req = $this->RequeteSelection ;
				}
				else
				{
					$req = $this->RequeteSelection." ".$this->AliasRequeteSelection ;
				}
				return $req ;
			}
			public function SelectElements($colonnes, $filtres, $indiceColonneTri=0, $sensColonneTri="asc")
			{
				$this->VideDerniereException() ;
				if(! $this->BaseDonneesValide())
					return null ;
				$lignes = array() ;
				if($this->UtiliserProcedures && $this->NomProcSelectElements != "")
				{
					$expression = $this->ExtraitExpressionFiltres($filtres) ;
					$lignes = $this->BaseDonnees->FetchStoredProcRows($this->NomProcSelectElements, $expression->Parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				else
				{
					$expression = $this->ExtraitExpressionFiltres($filtres) ;
					$texteColonnes = $this->ExtraitTexteColonnes($colonnes) ;
					$requeteSql = "select ".$texteColonnes." from ".$this->ChaineRequeteSelection() ;
					if(count($expression->Parametres) > 0)
					{
						$requeteSql .= " where ".$expression->Texte ;
					}
					if(count($colonnes) > 0)
					{
						$texteTri = $this->ExtraitTexteTri($colonnes, $indiceColonneTri, $sensColonneTri) ;
						$texteTriCols = $this->ExtraitTexteTriColonnes($colonnes) ;
						if($texteTri != "" || $texteTriCols != "")
						{
							$texteTriCplt = $texteTriCols ;
							if($texteTri != "")
							{
								if($texteTriCplt != "")
									$texteTriCplt .= ", " ;
								$texteTriCplt .= $texteTri ;
							}
							$requeteSql .= " order by ".$texteTriCplt ;
						}
					}
					// echo $requeteSql.'<br>' ;
					$lignes = $this->BaseDonnees->FetchSqlRows($requeteSql, array_merge($expression->Parametres, $this->ParamsSelection)) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				return $lignes ;
			}
			public function RangeeElements($colonnes, $filtres, $indiceDebut=0, $maxElements=100, $indiceColonneTri=0, $sensColonneTri="asc")
			{
				if(! $this->BaseDonneesValide())
					return null ;
				$lignes = array() ;
				if($this->UtiliserProcedures && $this->NomProcRangeeElements != "")
				{
					$expression = $this->ExtraitExpressionFiltres($filtres) ;
					$lignes = $this->BaseDonnees->FetchStoredProcRows($this->NomProcRangeeElements, $expression->Parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				else
				{
					// print count($colonnes).'<br>' ;
					$expression = $this->ExtraitExpressionFiltres($filtres) ;
					$texteColonnes = $this->ExtraitTexteColonnes($colonnes) ;
					$requeteSql = "select ".$texteColonnes." from ".$this->ChaineRequeteSelection() ;
					if(count($expression->Parametres) > 0)
					{
						$requeteSql .= " where ".$expression->Texte ;
					}
					$texteTri = $this->ExtraitTexteTri($colonnes, $indiceColonneTri, $sensColonneTri) ;
					$texteTriCols = $this->ExtraitTexteTriColonnes($colonnes) ;
					// echo $texteTri.' jjjj' ;
					if($texteTri != "" || $texteTriCols != "")
					{
						$texteTriCplt = $texteTriCols ;
						if($texteTri != "")
						{
							if($texteTriCplt != "")
								$texteTriCplt .= ", " ;
							$texteTriCplt .= $texteTri ;
						}
						$requeteSql .= " order by ".$texteTriCplt ;
					}
					// print $requeteSql ;
					$lignes = $this->BaseDonnees->LimitSqlRows($requeteSql, array_merge($expression->Parametres, $this->ParamsSelection), $indiceDebut, $maxElements) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				return $lignes ;
			}
			public function CompteElements($colonnes, $filtres)
			{
				if(! $this->BaseDonneesValide())
					return null ;
				$total = -1 ;
				if($this->UtiliserProcedures && $this->NomProcSelectElements != "")
				{
					$expression = $this->ExtraitExpressionFiltres($filtres) ;
					$total = $this->BaseDonnees->FetchStoredProcRows($this->NomProcSelectElements, $expression->Parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				else
				{
					$expression = $this->ExtraitExpressionFiltres($filtres) ;
					$texteColonnes = $this->ExtraitTexteColonnes($colonnes) ;
					// print_r($expression) ;
					$requeteSql = "select count(0) TOTAL from ".$this->ChaineRequeteSelection() ;
					if(count($expression->Parametres) > 0)
					{
						$requeteSql .= " where ".$expression->Texte ;
					}
					$total = $this->BaseDonnees->FetchSqlValue($requeteSql, array_merge($expression->Parametres, $this->ParamsSelection), "TOTAL") ;
					$this->SauveExceptionBaseDonnees() ;
				}
				return $total ;
			}
			public function AjoutElement($filtres)
			{
				if(! $this->BaseDonneesValide())
					return null ;
				$succes = 0 ;
				if($this->UtiliserProcedures && $this->NomProcAjoutElement != "")
				{
					$parametres = $this->ExtraitParametresFiltres($filtres) ;
					$succes = $this->BaseDonnees->RunStoredProc($this->NomProcAjoutElement, $parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				else
				{
					$parametres = $this->ExtraitParametresFiltres($filtres) ;
					$succes = $this->BaseDonnees->InsertRow($this->TableEdition, $parametres) ;
					$this->SauveExceptionBaseDonnees() ;
					// print_r($this->BaseDonnees->ConnectionParams) ;
				}
				return $succes ;
			}
			public function ModifElement($filtresSelection, $filtresEdition)
			{
				if(! $this->BaseDonneesValide())
					return null ;
				$succes = 0 ;
				if($this->UtiliserProcedures && $this->NomProcModifElement != "")
				{
					$parametres = $this->ExtraitParametresFiltres($filtresEdition) ;
					$expression = $this->ExtraitExpressionFiltres($filtresSelection) ;
					$succes = $this->BaseDonnees->RunStoredProc($this->NomProcModifElement, array_merge($expression->Parametres, $parametres)) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				else
				{
					$parametres = $this->ExtraitParametresFiltres($filtresEdition) ;
					$expression = $this->ExtraitExpressionFiltres($filtresSelection) ;
					$succes = $this->BaseDonnees->UpdateRow($this->TableEdition, $parametres, $expression->Texte, $expression->Parametres) ;
					// print_r($parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				return $succes ;
			}
			public function SupprElement($filtresSelection)
			{
				if(! $this->BaseDonneesValide())
					return null ;
				$succes = 0 ;
				if($this->UtiliserProcedures && $this->NomProcModifElement != "")
				{
					$expression = $this->ExtraitExpressionFiltres($filtresSelection) ;
					$succes = $this->BaseDonnees->RunStoredProc($this->NomProcModifElement, $expression->Parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				else
				{
					$expression = $this->ExtraitExpressionFiltres($filtresSelection) ;
					$succes = $this->BaseDonnees->DeleteRow($this->TableEdition, $expression->Texte, $expression->Parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				return $succes ;
			}
			public function OuvreRequeteSelectElements($filtres, $colonnes = array())
			{
				if(! $this->BaseDonneesValide())
					return null ;
				$parametres = $this->ExtraitParametresFiltres($filtres) ;
				$requete = new PvRequeteFournisseurDonnees() ;
				if($this->UtiliserProcedures && $this->NomProcSelectElements != "")
				{
					$requete->RessourceSupport = $this->BaseDonnees->OpenStoredProc($this->NomProcSelectElements, $parametres) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				else
				{
					$expression = $this->ExtraitExpressionFiltres($filtres) ;
					// print_r(count($filtres)) ;
					$requeteSql = "select ".$this->ExtraitTexteColonnes($colonnes)." from ".$this->ChaineRequeteSelection() ;
					if(count($expression->Parametres) > 0)
					{
						$requeteSql .= " where ".$expression->Texte ;
					}
					// echo $requeteSql ;
					$requete->RessourceSupport = $this->BaseDonnees->OpenQuery($requeteSql, array_merge($expression->Parametres, $this->ParamsSelection)) ;
					$this->SauveExceptionBaseDonnees() ;
				}
				return $requete ;
			}
			public function LitRequete(& $requete)
			{
				if($requete == false or $requete->RessourceSupport == false)
					return false ;
				$ligne = $this->BaseDonnees->ReadQuery($requete->RessourceSupport) ;
				$requete->Position++ ;
				return $ligne ;
			}
			public function FermeRequete(& $requete)
			{
				if($requete == false or $requete->RessourceSupport == false)
					return false ;
				return $this->BaseDonnees->CloseQuery($requete->RessourceSupport) ;
			}
			public function RechPartielleElements($filtres, $nomColonnes, $valeur)
			{
				$valeur = strtoupper($valeur) ;
				$expression = $this->ExtraitExpressionFiltres($filtres) ;
				$requeteSql = "select * from ".$this->RequeteSelection." t1" ;
				if($expression->Texte != '')
				{
					$requeteSql .= " where ".$expression->Texte ;
				}
				$filtresExtra = array() ;
				if($valeur != '')
				{
					$exprDebute = '' ;
					foreach($nomColonnes as $i => $nomColonne)
					{
						$nomFiltre = uniqid('Flt').$i ;
						$condFiltre = $this->BaseDonnees->SqlIndexOf('UPPER('.$this->BaseDonnees->EscapeVariableName($nomColonne).')', $this->BaseDonnees->ParamPrefix.$nomFiltre).' >= 1' ;
						$filtresExtra[$nomFiltre] = $valeur ;
						if($i > 0)
						{
							$exprDebute .= " or " ;
						}
						$exprDebute .= $condFiltre ;
					}
				}
				if($expression->Texte != '')
				{
					$requeteSql .= ' and ('.(($exprDebute != '') ? $exprDebute : '1=1').')' ;
				}
				else
				{
					$requeteSql .= ' where ('.(($exprDebute != '') ? $exprDebute : '1=1').')' ;
				}
				// print $requeteSql."\n" ;
				$params = array_merge($expression->Parametres, $this->ParamsSelection, $filtresExtra) ;
				// print_r($params) ;
				$lignes = $this->BaseDonnees->FetchSqlRows($requeteSql, $params) ;
				return $lignes ;
			}
			public function RechDebuteElements($filtres, $nomColonnes, $valeur)
			{
				$valeur = strtoupper($valeur) ;
				$expression = $this->ExtraitExpressionFiltres($filtres) ;
				$requeteSql = "select * from ".$this->RequeteSelection." t1" ;
				if($expression->Texte != '')
				{
					$requeteSql .= " where ".$expression->Texte ;
				}
				$filtresExtra = array() ;
				if($valeur != '')
				{
					$exprDebute = '' ;
					foreach($nomColonnes as $i => $nomColonne)
					{
						$nomFiltre = uniqid('Flt').$i ;
						$condFiltre = $this->BaseDonnees->SqlIndexOf('UPPER('.$this->BaseDonnees->EscapeVariableName($nomColonne).')', $this->BaseDonnees->ParamPrefix.$nomFiltre).' = 1' ;
						$filtresExtra[$nomFiltre] = $valeur ;
						if($i > 0)
						{
							$exprDebute .= " or " ;
						}
						$exprDebute .= $condFiltre ;
					}
				}
				if($expression->Texte != '')
				{
					$requeteSql .= ' and ('.(($exprDebute != '') ? $exprDebute : '1=1').')' ;
				}
				else
				{
					$requeteSql .= ' where ('.(($exprDebute != '') ? $exprDebute : '1=1').')' ;
				}
				// print $requeteSql."\n" ;
				$params = array_merge($expression->Parametres, $this->ParamsSelection, $filtresExtra) ;
				// print_r($params) ;
				$lignes = $this->BaseDonnees->FetchSqlRows($requeteSql, $params) ;
				return $lignes ;
			}
			public function RechExacteElements($filtres, $nomColonne, $valeur)
			{
				$expression = $this->ExtraitExpressionFiltres($filtres) ;
				$requeteSql = "select * from ".$this->RequeteSelection." t1" ;
				$nomFiltre = uniqid('Flt') ;
				$condFiltre = $this->BaseDonnees->EscapeVariableName($nomColonne).' = '.$this->BaseDonnees->ParamPrefix.$nomFiltre ;
				if($expression->Texte != "")
				{
					$requeteSql .= " where ".$expression->Texte.' and '.$condFiltre ;
				}
				else
				{
					$requeteSql .= ' where '.$condFiltre ;
				}
				$lignes = $this->BaseDonnees->FetchSqlRows($requeteSql, array_merge($expression->Parametres, $this->ParamsSelection, array($nomFiltre => $valeur))) ;
				return $lignes ;
			}
			public function RechsExactesElements($filtres, $nomColonne, $valeurs)
			{
				$expression = $this->ExtraitExpressionFiltres($filtres) ;
				$requeteSql = "select * from ".$this->RequeteSelection." t1" ;
				$nomFiltre = uniqid('Flt') ;
				$filtresValeur = array() ;
				$condFiltre = '' ;
				foreach($valeurs as $i => $valeur)
				{
					if($condFiltre != '')
					{
						$condFiltre .= ' or ' ;
					}
					$condFiltre .= $this->BaseDonnees->EscapeVariableName($nomColonne).' = '.$this->BaseDonnees->ParamPrefix.$nomFiltre.$i ;
					$filtresValeur[$nomFiltre.$i] = $valeur ;
				}
				if($expression->Texte != "")
				{
					$requeteSql .= " where ".$expression->Texte.' and ('.$condFiltre.')' ;
				}
				else
				{
					$requeteSql .= ' where ('.$condFiltre.')' ;
				}
				$lignes = $this->BaseDonnees->FetchSqlRows($requeteSql, array_merge($expression->Parametres, $this->ParamsSelection, $filtresValeur)) ;
				return $lignes ;
			}
			public function EncodeEntiteHtml($valeur)
			{
				if($this->EstNul($this->BaseDonnees))
				{
					return parent::EncodeEntiteHtml($valeur) ;
				}
				return $this->BaseDonnees->EncodeHtmlEntity($valeur) ;
			}
			public function EncodeAttrHtml($valeur)
			{
				if($this->EstNul($this->BaseDonnees))
				{
					return parent::EncodeAttrHtml($valeur) ;
				}
				return $this->BaseDonnees->EncodeHtmlAttr($valeur) ;
			}
			public function EncodeUrl($valeur)
			{
				if($this->EstNul($this->BaseDonnees))
				{
					return parent::EncodeUrl($valeur) ;
				}
				return $this->BaseDonnees->EncodeUrl($valeur) ;
			}
		}
	
	}
	
?>