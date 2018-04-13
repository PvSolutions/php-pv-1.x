<?php
	
	if(! defined('MODULE_ARTICLE_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__).'/Noyau.class.php' ;
		}
		if(! defined('COMPOSANT_IU_ARTICLE_SWS'))
		{
			include dirname(__FILE__).'/ComposantIU/Article.class.php' ;
		}
		define('MODULE_ARTICLE_SWS', 1) ;
		
		class ModuleArticleSws extends ModulePageBaseSws
		{
			public $TitreMenu = "Articles" ;
			public $NomRef = "article" ;
			public $EntiteRubrique ;
			public $EntiteArticle ;
			protected function CreeEntiteRubrique()
			{
				return new EntiteRubriqueSws() ;
			}
			protected function CreeEntiteArticle()
			{
				return new EntiteArticleSws() ;
			}
			protected function ChargeEntites()
			{
				$this->EntiteRubrique = $this->InsereEntite("rubrique", $this->CreeEntiteRubrique()) ;
				$this->EntiteArticle = $this->InsereEntite("article", $this->CreeEntiteArticle()) ;
			}
		}
		
		class EntiteArticleSws extends EntitePageWebSws
		{
			public $SecuriserEdition = 0 ;
			public $TitreMenu = "Articles" ;
			public $TitreAjoutEntite = "Ajout article" ;
			public $TitreModifEntite = "Modification article" ;
			public $TitreSupprEntite = "Suppression article" ;
			public $TitreListageEntite = "Liste des articles" ;
			public $TitreConsultEntite = "D&eacute;tails article" ;
			public $NomParamIdRubr = "id_rubrique" ;
			public $NomColIdRubr = "id_rubrique" ;
			public $LibIdRubr = "Rubrique" ;
			public $ValeurParamIdRubr = 0 ;
			public $ValeurParamId = 0 ;
			public $FltFrmElemIdRubr ;
			public $NomEntite = "article" ;
			public $LibEntite = "article" ;
			public $NomTable = "article" ;
			public $LgnRubrEnCours = array() ;
			public $LgnArtEnCours = array() ;
			public $BlocTitre ;
			public $BarreLiensSpec ;
			public $BlocContenu ;
			public $NomScriptRecommand = "recommand" ;
			public $NomScriptVersionImpr = "version_impr" ;
			public $ScriptRecommand ;
			public $ScriptVersionImpr ;
			public $LibTitreParent = "Rubrique" ;
			public $DefColTblListTitreParent ;
			protected function CreeScriptRecommand()
			{
				return new ScriptRecommandEntiteTableSws() ;
			}
			protected function CreeScriptVersionImpr()
			{
				return new ScriptVersionImprEntiteTableSws() ;
			}
			public function RemplitZonePubl(& $zone)
			{
				parent::RemplitZonePubl($zone) ;
				if($this->InclureScriptConsult)
				{
					$this->ScriptRecommand = $this->InsereScript($this->NomScriptRecommand.'_'.$this->NomEntite, $this->CreeScriptRecommand(), $zone, $this->ObtientPrivilegesConsult()) ;
					$this->ScriptVersionImpr = $this->InsereScript($this->NomScriptVersionImpr.'_'.$this->NomEntite, $this->CreeScriptVersionImpr(), $zone, $this->ObtientPrivilegesConsult()) ;
				}
			}
			public function RemplitScriptConsult(& $script)
			{
				parent::RemplitScriptConsult($script) ;
				if($this->ValidScriptConsult == 1)
				{
					// Bloc de titre
					$this->BlocTitre = new BlocTitreLgnEnCoursSws() ;
					$this->BlocTitre->AdopteScript("blocTitre", $script) ;
					$this->BlocTitre->ChargeConfig() ;
					// Barre de liens spec
					$this->BarreLiensSpec = new BarreLiensSpecArtSws() ;
					$this->BarreLiensSpec->AdopteScript("barreLiensSpec", $script) ;
					$this->BarreLiensSpec->ChargeConfig() ;
					$this->BarreLiensSpec->LienAjoutFav->FormatTitreFav = $this->LgnEnCours["titre"] ;
					$this->BarreLiensSpec->LienRecommander->FormatUrl = $this->ScriptRecommand->ObtientUrlParam(array($this->NomParamId => $this->LgnEnCours["id"])) ;
					$this->BarreLiensSpec->LienVersionImpr->FormatUrl = $this->ScriptVersionImpr->ObtientUrlParam(array($this->NomParamId => $this->LgnEnCours["id"])) ;
					// Bloc de contenu
					$this->BlocContenu = new BlocContenuArtSws() ;
					$this->BlocContenu->AdopteScript("blocContenu", $script) ;
					$this->BlocContenu->ChargeConfig() ;
					$this->BlocContenu->DonneesSupport = $this->LgnEnCours ;
				}
			}
			public function VerifPreReqsScriptEdit(& $script)
			{
				if($script->InitFrmElem->Role == "Ajout")
				{
					$this->ValeurParamIdRubr = intval((isset($_GET[$this->NomParamIdRubr])) ? $_GET[$this->NomParamIdRubr] : 0) ;
					$this->LgnRubrEnCours = $this->ModuleParent->EntiteRubrique->SelectLgn($this->ValeurParamIdRubr) ;
					return (is_array($this->LgnRubrEnCours) && count($this->LgnRubrEnCours) > 0) ? 1 : 0 ;
				}
				else
				{
					$this->ValeurParamId = intval((isset($_GET[$this->NomParamId])) ? $_GET[$this->NomParamId] : 0) ;
					$this->LgnArtEnCours = $this->SelectLgn($this->ValeurParamId) ;
					return (is_array($this->LgnArtEnCours) && count($this->LgnArtEnCours) > 0) ? 1 : 0 ;
				}
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Id rubrique
				$this->FltFrmElemIdRubr = $frm->InsereFltEditHttpPost($this->NomParamIdRubr, $this->NomColIdRubr) ;
				$this->FltFrmElemIdRubr->Libelle = $this->LibIdRubr ;
				if($frm->ScriptParent->InitFrmElem->Role == "Ajout")
				{
					$this->FltFrmElemIdRubr->ValeurParDefaut = $this->LgnRubrEnCours["id"] ;
				}
				$comp = $this->FltFrmElemIdRubr->DeclareComposant("PvZoneCorrespHtml") ;
				$comp->FournisseurDonnees = $frm->ScriptParent->CreeFournDonnees() ;
				$comp->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteRubrique->NomTable ;
				$comp->NomColonneValeur = $this->ModuleParent->EntiteRubrique->NomColId ;
				$comp->NomColonneLibelle = $this->ModuleParent->EntiteRubrique->NomColTitre ;
			}
			protected function RemplitMenuInt(& $menu)
			{
				parent::RemplitMenuInt($menu) ;
				if($this->InclureScriptEdit)
				{
					$this->SousMenuAjout->ParamsScript = array($this->NomParamIdRubr => 1) ;
				}
			}
			protected function ChargeTblList(& $tabl)
			{
				parent::ChargeTblList($tabl) ;
				$this->DefColTblListTitreParent = $tabl->InsereDefCol("titre_conteneur", $this->LibTitreParent) ;
				$this->DefColTblListTitreParent->Largeur = "35%" ;
				$this->DefColTblListTitre->Largeur = "15%" ;
			}
			protected function FinalTblList(& $tabl)
			{
				parent::FinalTblList($tabl) ;
				$bd = & $tabl->FournisseurDonnees->BaseDonnees ;
				$tabl->FournisseurDonnees->RequeteSelection = '(select t1.*, '.$bd->SqlConcat(array('t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteRubrique->NomColTitreChemin), "'; '", 't2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteRubrique->NomColTitre))).' titre_conteneur from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($this->ModuleParent->EntiteRubrique->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdRubr).' = t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteRubrique->NomColId).')' ;
				// echo $tabl->FournisseurDonnees->RequeteSelection ;
				if($this->InclureScriptEdit)
				{
					$this->CmdAjoutTblList->Parametres = array($this->NomParamIdRubr => 1) ;
				}
			}
			protected function ObtientParamsUrlFrmElem(& $frm)
			{
				if($frm->ScriptParent->InitFrmElem->Role == "Ajout")
				{
					return array($this->NomParamIdRubr => $this->LgnRubrEnCours["id"]) ;
				}
				return array($this->NomParamId => $this->LgnArtEnCours["id"]) ;
			}
			protected function PrepareScriptConsult(& $script)
			{
				$script->TitreDocument = $script->ApplicationParent->SystemeSws->ModulePageRacine->ScriptAccueil->Titre.' :: '.$this->LgnEnCours["titre"] ;
				$script->Titre = $script->TitreDocument ;
				$script->MotsClesMeta = $this->LgnEnCours["mots_cles_meta"] ;
				$script->DescriptionMeta = $this->LgnEnCours["description_meta"] ;
			}
			public function RenduScriptConsult(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->BlocTitre->RenduDispositif() ;
				$ctn .= $this->BarreLiensSpec->RenduDispositif() ;
				$ctn .= $this->BlocContenu->RenduDispositif() ;
				return $ctn ;
			}
		}
		class EntiteRubriqueSws extends EntitePageWebSws
		{
			public $TitreMenu = "Rubriques" ;
			public $TitreAjoutEntite = "Ajout rubrique" ;
			public $TitreModifEntite = "Modification rubrique" ;
			public $TitreSupprEntite = "Suppression rubrique" ;
			public $TitreListageEntite = "Liste des rubriques" ;
			public $TitreConsultEntite = "Détails rubrique" ;
			public $SecuriserEdition = 0 ;
			public $NomEntite = "rubrique" ;
			public $NomTable = "rubrique" ;
			public $LibEntite = "rubrique" ;
			public $NomParamIdConteneur = "id_conteneur" ;
			public $NomColIdConteneur = "id_conteneur" ;
			public $LibIdConteneur = "Conteneur" ;
			public $AccepterAttrsMeta = 0 ;
			public $AccepterSommaire = 0 ;
			public $AccepterAttrsGraphique = 0 ;
			public $NomColIdChemin = "id_chemin" ;
			public $NomColTitreChemin = "titre_chemin" ;
			public $FltFrmElemIdChemin = "id_chemin" ;
			public $FltFrmElemTitreChemin = "titre_chemin" ;
			public $LgnRubrEnCours = array() ;
			public $CheminRubrEnCours = array() ;
			public $NomClasseCmdAjout = "CmdAjoutRubriqueSws" ;
			public $NomClasseCmdModif = "CmdModifRubriqueSws" ;
			public $NomClasseCmdSuppr = "CmdSupprRubriqueSws" ;
			public $LienAjoutTblList ;
			public $LienAjoutArtTblList ;
			public $LibAjoutTblList = "Ajouter rubrique" ;
			public $LibAjoutArtTblList = "Ajouter article" ;
			public $BlocTitre ;
			public $DefColTblListTitreParent ;
			public $LibTitreParent = "Rubr. conteneur" ;
			public $GrilleSousRubr ;
			public $GrilleArts ;
			protected $InclureScriptConsultPlanSite = 1 ;
			protected $NomColTitreConsultPlanSite = "titre" ;
			public function ObtientReqSqlFluxRSS()
			{
				$this->DefFluxRSS->NomColCheminImage = "" ;
				return parent::ObtientReqSqlFluxRSS() ;
			}
			protected function SqlConsultPlanSite()
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = "select ".$bd->EscapeVariableName($this->NomColIdConsultPlanSite)." id" ;
				$sql .= ", ".$bd->EscapeVariableName($this->NomColTitreConsultPlanSite)." titre" ;
				$sql .= " from ".$bd->EscapeTableName($this->NomTable) ;
				$sql .= " where ".$bd->EscapeVariableName($this->NomColStatutPubl)." = 1" ;
				$sql .= " and ".$bd->EscapeVariableName($this->NomColIdConteneur)." = 1" ;
				$sql .= " order by ".$bd->EscapeVariableName($this->NomColDatePubl)." desc, ".$bd->EscapeVariableName($this->NomColHeurePubl)." desc" ;
				return $sql ;
			}
			protected function RemplitMenuInt(& $menu)
			{
				parent::RemplitMenuInt($menu) ;
				if($this->InclureScriptEdit)
				{
					$this->SousMenuAjout->ParamsScript = array($this->NomParamId => 1) ;
				}
			}
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColIdChemin).' id_chemin' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitreChemin).' titre_chemin' ;
				return $sql ;
			}
			public function VerifPreReqsScriptEdit(& $script)
			{
				$this->ValeurParamId = intval((isset($_GET[$this->NomParamId])) ? $_GET[$this->NomParamId] : 0) ;
				$this->LgnRubrEnCours = $this->SelectLgn($this->ValeurParamId) ;
				return (is_array($this->LgnRubrEnCours) && count($this->LgnRubrEnCours) > 0) ? 1 : 0 ;
			}
			public function RemplitScriptConsult(& $script)
			{
				parent::RemplitScriptConsult($script) ;
				if($this->ValidScriptConsult == 1)
				{
					// Bloc de titre
					$this->BlocTitre = new BlocTitreLgnEnCoursSws() ;
					$this->BlocTitre->AdopteScript("blocTitre", $script) ;
					$this->BlocTitre->ChargeConfig() ;
					// Grille des sous rubriques
					$this->GrilleSousRubr = new GrilleSousRubrSws() ;
					$this->GrilleSousRubr->AdopteScript("grilleSousRubr", $script) ;
					$this->GrilleSousRubr->ChargeConfig() ;
					// Grille des articles
					$this->GrilleArts = new GrilleArtsRubrSws() ;
					$this->GrilleArts->AdopteScript("grilleArts", $script) ;
					$this->GrilleArts->ChargeConfig() ;			
				}
			}
			protected function PrepareScriptConsult(& $script)
			{
				$script->TitreDocument = $script->ApplicationParent->SystemeSws->ModulePageRacine->ScriptAccueil->Titre.' :: '.$this->LgnEnCours["titre"] ;
				$script->Titre = $script->TitreDocument ;
			}
			public function RenduScriptConsult(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->BlocTitre->RenduDispositif() ;
				$ctn .= $this->GrilleSousRubr->RenduDispositif() ;
				$ctn .= $this->GrilleArts->RenduDispositif() ;
				// $ctn .= $this->BlocContenu->RenduDispositif() ;
				return $ctn ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				// Id rubrique parent
				$this->FltFrmElemIdConteneur = $frm->InsereFltEditHttpPost($this->NomParamIdConteneur, $this->NomColIdConteneur) ;
				$this->FltFrmElemIdConteneur->Libelle = $this->LibIdConteneur ;
				if($frm->ScriptParent->InitFrmElem->Role == "Ajout")
				{
					$this->FltFrmElemIdConteneur->ValeurParDefaut = $this->LgnRubrEnCours["id"] ;
				}
				$comp = $this->FltFrmElemIdConteneur->DeclareComposant("PvZoneCorrespHtml") ;
				$comp->FournisseurDonnees = $frm->ScriptParent->CreeFournDonnees() ;
				$comp->FournisseurDonnees->RequeteSelection = $this->NomTable ;
				$comp->FournisseurDonnees->TableEdition = $this->NomTable ;
				$comp->NomColonneValeur = $this->NomColId ;
				$comp->NomColonneLibelle = $this->NomColTitre ;
				$this->FltFrmElemIdChemin = $frm->InsereFltEditFixe("idChemin", '', $this->NomColIdChemin) ;
				$this->FltFrmElemTitreChemin = $frm->InsereFltEditFixe("titreChemin", '', $this->NomColTitreChemin) ;
			}
			protected function ChargeTblList(& $tabl)
			{
				parent::ChargeTblList($tabl) ;
				$this->DefColTblListTitreParent = $tabl->InsereDefCol("titre_conteneur", $this->LibTitreParent) ;
				$this->DefColTblListTitreParent->Largeur = "35%" ;
				$this->DefColTblListTitre->Largeur = "15%" ;
			}
			protected function FinalTblList(& $tabl)
			{
				parent::FinalTblList($tabl) ;
				$bd = & $tabl->FournisseurDonnees->BaseDonnees ;
				$tabl->FournisseurDonnees->RequeteSelection = '(select t1.*, '.$bd->SqlConcat(array('t2.'.$bd->EscapeVariableName($this->NomColTitreChemin), "'; '", 't2.'.$bd->EscapeVariableName($this->NomColTitre))).' titre_conteneur from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($this->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdConteneur).' = t2.'.$bd->EscapeVariableName($this->NomColId).')' ;
				// echo $tabl->FournisseurDonnees->RequeteSelection ;
				if($this->InclureScriptEdit)
				{
					$this->CmdAjoutTblList->Parametres = array($this->NomParamId => 1) ;
					$this->LienAjoutTblList = $tabl->InsereLienActionAvant($this->DefColTblListActs, count($this->DefColTblListActs->Formatteur->Liens) - 1, $this->ScriptAjout->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')), $this->LibAjoutTblList) ;
					$this->LienAjoutArtTblList = $tabl->InsereLienActionAvant($this->DefColTblListActs, count($this->DefColTblListActs->Formatteur->Liens) - 1, $this->ModuleParent->EntiteArticle->ScriptAjout->ObtientUrlFmt(array($this->ModuleParent->EntiteArticle->NomColIdRubr => '${'.$this->NomColId.'}')), $this->LibAjoutArtTblList) ;
				}
			}
		}
		
		class CmdEditRubriqueSws extends PvCommandeEditionElementBase
		{
			protected $LgnRubrEdite ;
			protected $LgnRubrConteneur ;
			protected function CalculeFltsSpec()
			{
			}
			public function ExecuteInstructions()
			{
				$this->CalculeFltsSpec() ;
				parent::ExecuteInstructions() ;
				$this->MajChemins() ;
			}
			protected function MajChemins()
			{
				if($this->StatutExecution == 0)
				{
					return ;
				}
				if($this->Mode == 3)
				{
					return ;
				}
				$this->LgnRubrEdite = $this->ScriptParent->ModulePage->EntiteRubrique->SelectLgnCtrl($this->ScriptParent->ModulePage->EntiteRubrique->FltFrmElemIdCtrl->Lie()) ;
				if(is_array($this->LgnRubrEdite) && count($this->LgnRubrEdite) > 0)
				{
					$this->LgnRubrConteneur = $this->ScriptParent->ModulePage->EntiteRubrique->SelectLgn($this->ScriptParent->ModulePage->EntiteRubrique->FltFrmElemIdConteneur->Lie()) ;
					$bd = $this->ScriptParent->ModulePage->ObtientBDSupport() ;
					$ok = $bd->UpdateRow(
						$this->ScriptParent->ModulePage->EntiteRubrique->NomTable,
						array(
							$this->ScriptParent->ModulePage->EntiteRubrique->NomColIdChemin => $this->LgnRubrConteneur["id_chemin"].", ".$this->LgnRubrConteneur["id"],
							$this->ScriptParent->ModulePage->EntiteRubrique->NomColTitreChemin => $this->LgnRubrConteneur["titre_chemin"]."; ".$this->LgnRubrConteneur["titre"],
						),
						$bd->EscapeVariableName($this->ScriptParent->ModulePage->EntiteRubrique->NomColId).' = '.$bd->ParamPrefix.'idRubr',
						array('idRubr' => $this->LgnRubrEdite["id"])
					) ;
					if(! $ok)
					{
						$this->RenseigneErreur("Erreur SQL : ".$bd->ConnectionException) ;
					}
				}
			}
		}
		class CmdAjoutRubriqueSws extends CmdEditRubriqueSws
		{
			public $Mode = 1 ;
		}
		class CmdModifRubriqueSws extends CmdEditRubriqueSws
		{
			public $Mode = 2 ;
		}
		class CmdSupprRubriqueSws extends CmdEditRubriqueSws
		{
			public $Mode = 3 ;
		}
		
		class EncGraphiquesPortionRenduFmt extends EncBasePortionRenduFmt
		{
			public $EntiteParent ;
			public function Execute($params=array(), $elem=array())
			{
				if($this->EntiteParent == null)
					return array() ;
				$params = array() ;
				;
			}
		}
	}
	
?>