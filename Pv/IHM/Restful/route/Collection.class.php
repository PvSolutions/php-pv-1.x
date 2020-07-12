<?php
	
	if(! defined('PV_ROUTE_COLLECTION_RESTFUL'))
	{
		define('PV_ROUTE_COLLECTION_RESTFUL', 1) ;
		
		class PvRouteCollectionRestful extends PvRouteFiltrableRestful
		{
			public $MethodeHttp = "GET" ;
			public $DefinitionsColonnes = array() ;
			public $MaxElementsPossibles = array(20) ;
			public $ToujoursAfficher = 0 ;
			public $IndiceDebut = 0 ;
			public $AccepterTriColonneInvisible = 0 ;
			public $IndiceFin = 0 ;
			public $MaxElements = 0 ;
			public $TotalElements = 0 ;
			public $TotalRangees = 0 ;
			public $IndiceColonneTriSelect = -1 ;
			public $IndiceColonneTri = 0 ;
			public $NePasTrier = 0 ;
			public $SensColonneTri ;
			public $TriPossible = 1 ;
			public $RangeeEnCours = -1 ;
			public $MessageAucunElement ;
			public $Commandes = array() ;
			public $CommandeSelectionnee ;
			public $ExtraireValeursElements = 1 ;
			public $ElementsEnCours = array() ;
			public $NomsColonne = array() ;
			public $ElementsEnCoursBruts = array() ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->SourceValeursSuppl = new PvSrcValsSupplRestful() ;
			}
			public function ObtientValeursExtraites($lignes)
			{
				$extracteurs = array() ;
				foreach($this->DefinitionsColonnes as $i => $col)
				{
					if($col->NomDonnees != '' && $col->EstPasNul($col->ExtracteurValeur))
					{
						$extracteurs[$col->NomDonnees] = $col->ExtracteurValeur ;
					}
				}
				if(count($extracteurs) == 0)
				{
					return $lignes ;
				}
				$lignesResultat = array() ;
				foreach($lignes as $i => $ligne)
				{
					$lignesResultat[$i] = $ligne ;
					foreach($extracteurs as $nomDonnees => $extracteur)
					{
						if(! isset($ligne[$nomDonnees]))
						{
							continue ;
						}
						$valeursSuppl = $extracteur->Execute($ligne[$nomDonnees], $this) ;
						// print_r($valeursSuppl) ;
						if(is_array($valeursSuppl))
						{
							$lignesResultat[$i] = array_merge($lignesResultat[$i], array_apply_prefix($valeursSuppl, $nomDonnees.'_')) ;
						}
						// print_r(array_keys($lignesResultat[$i])) ;
					}
				}
				return $lignesResultat ;
			}
			public function InscritExtractValsIndex(& $extractVals, $indexCol)
			{
				if(! isset($this->DefinitionsColonnes[$indexCol]))
					return ;
				$this->DefinitionsColonnes[$indexCol]->ExtracteurValeur = & $extractVals ;
			}
			public function InscritExtractVals(& $extractVals, & $col)
			{
				$col->ExtracteurValeur = & $extractVals ;
			}
			public function InsereTablDefsCol($cols=array())
			{
				foreach($cols as $i => $nom)
				{
					$this->InsereDefCol($nom) ;
				}
			}
			public function InsereDefsColCachee()
			{
				$noms = func_get_args() ;
				foreach($noms as $i => $nom)
				{
					$this->InsereDefColCachee($nom) ;
				}
			}
			public function & InsereDefColCachee($nomDonnees, $aliasDonnees="")
			{
				$defCol = $this->InsereDefColInvisible($nomDonnees, $aliasDonnees) ;
				return $defCol ;
			}
			public function & InsereDefColInvisible($nomDonnees, $aliasDonnees="")
			{
				$defCol = new PvDefinitionColonneDonnees() ;
				$defCol->NomDonnees = $nomDonnees ;
				$defCol->AliasDonnees = $aliasDonnees ;
				$defCol->Visible = 0 ;
				$this->DefinitionsColonnes[] = & $defCol ;
				return $defCol ;
			}
			public function & InsereDefCol($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = new PvDefinitionColonneDonnees() ;
				if(is_array($nomDonnees))
				{
					$aliasDonnees = (isset($nomDonnees[1])) ? $nomDonnees[1] : '' ;
					$nomDonnees = $nomDonnees[0] ;
				}
				$defCol->NomDonnees = $nomDonnees ;
				$defCol->Libelle = $libelle ;
				$defCol->AliasDonnees = $aliasDonnees ;
				$this->DefinitionsColonnes[] = & $defCol ;
				return $defCol ;
			}
			public function & InsereDefColBool($nomDonnees, $libelle="", $aliasDonnees="", $valPositive="", $valNegative="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneBooleen() ;
				if($valPositive != "")
					$defCol->ValeurPositive = $valPositive ;
				if($valNegative != "")
					$defCol->ValeurNegative = $valNegative ;
				return $defCol ;
			}
			public function & InsereDefColChoix($nomDonnees, $libelle="", $aliasDonnees="", $valsChoix=array())
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneChoix() ;
				$defCol->Formatteur->ValeursChoix = $valsChoix ;
				return $defCol ;
			}
			public function & InsereDefColEditable($nomDonnees, $libelle="", $aliasDonnees="", $nomClsComp="PvZoneTexteHtml")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneEditable() ;
				if($nomClsComp != '')
				{
					$defCol->Formatteur->DeclareComposant($nomClsComp) ;
				}
				return $defCol ;
			}
			public function & InsereDefColMonnaie($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefColMoney($nomDonnees, $libelle, $aliasDonnees) ;
				return $defCol ;
			}
			public function & InsereDefColMoney($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneMonnaie() ;
				return $defCol ;
			}
			public function & InsereDefColDateFr($nomDonnees, $libelle="", $inclureHeure=0)
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneDateFr() ;
				$defCol->Formatteur->InclureHeure = $inclureHeure ;
				return $defCol ;
			}
			public function & InsereDefColDateTimeFr($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonneDateFr() ;
				$defCol->Formatteur->InclureHeure = 1 ;
				return $defCol ;
			}
			public function & InsereDefColDetail($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->Formatteur = new PvFormatteurColonnePlusDetail() ;
				return $defCol ;
			}
			public function & InsereDefColFixe($valeur, $libelle="")
			{
				$defCol = $this->InsereDefCol("", $libelle, "") ;
				$defCol->Formatteur = new PvFormatteurColonneFixe() ;
				$defCol->Formatteur->ValeurParDefaut = $valeur ;
				return $defCol ;
			}
			public function & InsereDefColHtml($modeleHtml="", $libelle="")
			{
				$defCol = $this->InsereDefCol("", $libelle, "") ;
				$defCol->Formatteur = new PvFormatteurColonneModeleHtml() ;
				$defCol->Formatteur->ModeleHtml = $modeleHtml ;
				return $defCol ;
			}
			public function & InsereDefColTimestamp($nomDonnees, $libelle="", $formatDate="d/m/Y H:i:s")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, "") ;
				$defCol->Formatteur = new PvFormatteurColonneTimestamp() ;
				$defCol->Formatteur->FormatDate = $formatDate ;
				return $defCol ;
			}
			public function & InsereDefColSansTri($nomDonnees, $libelle="", $aliasDonnees="")
			{
				$defCol = $this->InsereDefCol($nomDonnees, $libelle, $aliasDonnees) ;
				$defCol->TriPossible = 0 ;
				return $defCol ;
			}
			public function DefinitionsColonnesExport()
			{
				$colonnes = array() ;
				foreach($this->DefinitionsColonnes as $i => $colonne)
				{
					if($colonne->PeutExporterDonnees())
					{
						$colonnes[] = $colonne ;
					}
				}
				return $colonnes ;
			}
			public function ExtraitValeursExport($ligne, & $cmd)
			{
				$valeurs = array() ;
				$colonnes = $this->DefinitionsColonnesExport() ;
				foreach($colonnes as $i => $colonne)
				{
					$valeur = $colonne->FormatteValeur($this, $ligne) ;
					if($valeur == $colonne->ValeurVide)
						$valeur = $cmd->ValeurVideExport ;
					$valeurs[] = $valeur ;
				}
				return $valeurs ;
			}
			public function ExtraitLibellesExport()
			{
				$valeurs = array() ;
				$colonnes = $this->DefinitionsColonnesExport() ;
				foreach($colonnes as $i => $colonne)
				{
					$valeurs[] = $colonne->ObtientLibelle() ;
				}
				return $valeurs ;
			}
			public function ObtientDefColsRendu()
			{
				$defCols = $this->DefinitionsColonnes ;
				return $defCols ;
			}
			protected function DetecteParametresLocalisation()
			{
				$nomParamMaxElements = $this->ApiParent->NomParamMaxElementsCollection ;
				$nomParamIndiceDebut = $this->ApiParent->NomParamIndiceDebutCollection ;
				$nomParamSensTri = $this->ApiParent->NomParamSensTriCollection ;
				$nomParamCols = $this->ApiParent->NomParamColonnesCollection ;
				$texteNomsColonne = (isset($_GET[$nomParamCols])) ? $_GET[$nomParamCols] : "" ;
				if($texteNomsColonne != "")
				{
					$this->NomsColonne = explode(",", $texteNomsColonne) ;
					foreach($this->DefinitionsColonnes as $j => $defCol)
					{
						if($defCol->Visible == false)
						{
							continue ;
						}
						for($k=0; $k<count($this->NomsColonnes); $k++)
						{
							if(strtolower($defCol->NomDonnees) == strtolower($this->NomsColonnes[$k]))
							{
								$this->DefinitionsColonnes[$j]->Visible = false ;
								break ;
							}
						}
					}
				}
				$this->MaxElements = (isset($_GET[$nomParamMaxElements])) ? $_GET[$nomParamMaxElements] : 0 ;
				if(! in_array($this->MaxElements, $this->MaxElementsPossibles))
					$this->MaxElements = $this->MaxElementsPossibles[0] ;
				$this->IndiceDebut = (isset($_GET[$nomParamIndiceDebut])) ? intval($_GET[$nomParamIndiceDebut]) : 0 ;
				if($this->NePasTrier == 0)
				{
					$this->IndiceColonneTri = 0 ;
					$this->ValeurSensTri = (isset($_GET[$this->nomParamSensTri])) ? $_GET[$this->nomParamSensTri] : "" ;
					if($this->ValeurSensTri != "" && strrpos($this->ValeurSensTri, "_") !== false)
					{
						$this->SensColonneTri = substr($this->ValeurSensTri, strrpos($this->ValeurSensTri, "_")) ;
						$this->NomColonneTri = substr($this->ValeurSensTri, 0, strrpos($this->ValeurSensTri, "_")) ;
					}
					foreach($this->DefinitionsColonnes as $index => $defCol)
					{
						if(($this->AccepterTriColonneInvisible || $defCol->Visible == 1) && $defCol->NomDonnees != '' && $this->NomColonneTri == $defCol->NomDonnees)
						{
							$this->IndiceColonneTri = $index ;
							break ;
						}
					}
					if($this->IndiceColonneTri >= count($this->DefinitionsColonnes) || $this->IndiceColonneTri < 0)
						$this->IndiceColonneTri = 0 ;
					// Gerer les tri sur des colonnes invisibles...
					if(count($this->DefinitionsColonnes) > 0)
					{
						if(! $this->AccepterTriColonneInvisible && $this->DefinitionsColonnes[$this->IndiceColonneTri]->Visible == 0)
						{
							for($i=$this->IndiceColonneTri+1; $i<count($this->DefinitionsColonnes); $i++)
							{
								if($this->DefinitionsColonnes[$i]->Visible == 1 && $this->DefinitionsColonnes[$i]->NomDonnees != '')
								{
									$this->IndiceColonneTri = $i ;
									break ;
								}
							}
						}
					}
				}
					if($this->SensColonneTri != "desc")
						$this->SensColonneTri = "asc" ;
			}
			public function CalculeElementsRendu()
			{
				$defCols = $this->ObtientDefColsRendu() ;
				$this->TotalElements = $this->FournisseurDonnees->CompteElements($defCols, $this->FiltresSelection) ;
				if($this->FournisseurDonnees->ExceptionTrouvee())
				{
					$this->AlerteExceptionFournisseur() ;
				}
				else
				{
					// Ajuster l'indice début
					if($this->IndiceDebut < 0)
						$this->IndiceDebut = 0 ;
					if($this->IndiceDebut >= $this->TotalElements)
						$this->IndiceDebut = $this->TotalElements ;
					if($this->TotalElements > 0)
					{
						$this->IndiceDebut = intval($this->IndiceDebut / $this->MaxElements) * $this->MaxElements ;
						$this->ElementsEnCoursBruts = $this->FournisseurDonnees->RangeeElements($defCols, $this->FiltresSelection, $this->IndiceDebut, $this->MaxElements, $this->IndiceColonneTri, $this->SensColonneTri) ;
						if($this->FournisseurDonnees->ExceptionTrouvee())
						{
							$this->TotalElements = 0 ;
							$this->IndiceDebut = 0 ;
							$this->TotalRangees = 0 ;
							$this->IndiceFin = 0 ;
							$this->RangeeEnCours = -1 ;
							$this->ElementsEnCours = array() ;
							$this->AlerteExceptionFournisseur() ;
						}
						else
						{
							if($this->ExtraireValeursElements)
							{
								$this->ElementsEnCours = $this->ObtientValeursExtraites($this->ElementsEnCoursBruts) ;
							}
							else
							{
								$this->ElementsEnCours = $this->ElementsEnCoursBruts ;
							}
							// echo "Sql : ".$this->FournisseurDonnees->BaseDonnees->LastSqlText ;
							// print_r($this->ElementsEnCours) ;
							$this->RangeeEnCours = $this->IndiceDebut / $this->MaxElements ;
							$nbRangees = intval($this->TotalElements / $this->MaxElements) ;
							$nbRangeesDec = $this->TotalElements / $this->MaxElements ;
							$this->TotalRangees = ($nbRangees == $nbRangeesDec) ? $nbRangeesDec : $nbRangees + 1 ;
							$this->IndiceFin = $this->IndiceDebut + count($this->ElementsEnCours) ;
							if($this->IndiceFin >= $this->TotalElements)
							{
								$this->IndiceFin = $this->TotalElements ;
							}
						}
					}
					else
					{
						$this->IndiceDebut = 0 ;
						$this->TotalRangees = 0 ;
						$this->IndiceFin = 0 ;
						$this->RangeeEnCours = -1 ;
						$this->ElementsEnCours = array() ;
					}
				}
			}
			protected function TermineExecution()
			{
				if(! $this->Reponse->EstSucces())
				{
					return ;
				}
				$this->DetecteParametresLocalisation() ;
				$this->CalculeElementsRendu() ;
				if($this->MessageAucunElement != "")
				{
					$this->Reponse->ConfirmeInvalide($this->MessageAucunElement) ;
				}
				else
				{
					$this->ContenuReponse->data = $this->ElementsEnCours ;
					$this->ApiParent->Metadatas["page"] = $this->RangeeEnCours + 1 ;
					$this->ApiParent->Metadatas["per_page"] = $this->MaxElements ;
					$this->ApiParent->Metadatas["page_count"] = $this->TotalRangees ;
					$this->ApiParent->Metadatas["total_count"] = intval($this->TotalElements) ;
				}
			}
		}
	}
	
?>