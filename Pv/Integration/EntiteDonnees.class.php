<?php
	
	if(! defined('PV_INTEGR_ENTITE_DONNEES'))
	{
		/*
		 *
		 Require : Zone
		 * */
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../Base.class.php" ;
		}
		define('PV_INTEGR_ENTITE_DONNEES', 1) ;
		
		class PvIntegrEntiteDonnees extends PvIntegration
		{
			protected $_Entites = array() ;
			public $NomZoneWeb = "" ;
			public $BaseDonnees = null ;
			public $NomScriptRead = "liste" ;
			public $NomScriptCreat = "ajout" ;
			public $NomScriptUpd = "modif" ;
			public $NomScriptDel = "del" ;
			public $MaxFltsEditParLigne = 1 ;
			public $NomClasseCmdExecCreat = "PvCommandeAjoutElement" ;
			public $NomClasseCmdExecUpd = "PvCommandeModifElement" ;
			public $NomClasseCmdExecDel = "PvCommandeSupprElement" ;
			public function CreeScriptReadEntite()
			{
				return new PvScriptReadEntDonnees() ;
			}
			public function CreeScriptCreatEntite()
			{
				return new PvScriptCreatEntDonnees() ;
			}
			public function CreeScriptUpdEntite()
			{
				return new PvScriptUpdEntDonnees() ;
			}
			public function CreeScriptDelEntite()
			{
				return new PvScriptDelEntDonnees() ;
			}
			public function CreeTableauDonnees()
			{
				return new PvTableauDonneesHtml() ;
			}
			public function CreeGrilleDonnees()
			{
				return new PvGrilleDonneesHtml() ;
			}
			public function CreeFournDonnees()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->BaseDonnees ;
				return $fourn ;
			}
			public function CreeFormulaireDonnees()
			{
				$form = new PvFormulaireDonneesHtml() ;
				$form->MaxFiltresEditionParLigne = $this->MaxFltsEditParLigne ;
				return $form ;
			}
			public function ChargeConfig()
			{
				$this->BaseDonnees = $this->CreeBaseDonnees() ;
				$this->ChargeEntites() ;
				$this->ChargeConfigSuppl() ;
			}
			protected function CreeBaseDonnees()
			{
				return new AbstractSqlDb() ;
			}
			protected function ChargeEntites()
			{
			}
			protected function ChargeConfigSuppl()
			{
			}
			public function CreeAspectCrudWeb()
			{
				return new PvAspectCrudWebEntDonnees() ;
			}
			public function RemplitApplication($nomIntegration, & $app)
			{
				$entites = $this->Entites() ;
				foreach($entites as $nom => $entite)
				{
					$entites[$nom]->ChargeConfig() ;
				}
				parent::RemplitApplication($nomIntegration, $app) ;
			}
			protected function RemplitIHM(& $ihm)
			{
				if($ihm->NomElementApplication == $this->NomZoneWeb)
				{
					$entites = $this->Entites() ;
					foreach($entites as $nom => $entite)
					{
						$entite->RemplitZoneWeb($ihm) ;
					}
				}
			}
			public function & InsereScriptAspect($nom, $script, & $aspect, & $zone, $privs=array())
			{
				$res = $this->InsereScript($nom, $script, $zone, $privs) ;
				$res->NecessiteMembreConnecte = $aspect->EntiteDonneesParent->NecessiteMembreConnecte ;
				$res->AttrsSuppl[$this->IDInstanceCalc."_Entite"] = $aspect->EntiteParent->NomElemIntegr ;
				$res->AttrsSuppl[$this->IDInstanceCalc."_Aspect"] = $aspect->NomElemEntite ;
				// print_r($res->AttrsSuppl) ;
				return $res ;
			}
			public function & ObtientAspect(& $obj)
			{
				$aspect = new PvAspectEntDonneesNul() ;
				$nomEnt = $obj->ValAttrSuppl($this->NomElementApplication."_Entite") ;
				if($nomEnt != null)
				{
					$nomAspect = $obj->ValAttrSuppl($this->NomElementApplication."_Aspect") ;
					return $this->_Entites[$nomEnt]->Aspect($nomAspect) ;
				}
				return $aspect ;
			}
			public function & InscritEntite($nom, & $entite)
			{
				$this->_Entites[$nom] = & $entite ;
				$entite->AdopteIntegration($nom, $this) ;
				return $entite ;
			}
			public function & InsereEntite($nom, $entite)
			{
				return $this->InscritEntite($nom, $entite) ;
			}
			public function & Entite($nomEntite)
			{
				$entite = new PvEntiteDonneesNul() ;
				if(isset($this->_Entites[$nomEntite]))
				{
					$entite = & $this->_Entites[$nomEntite] ;
				}
				return $entite ;
			}
			public function & Entites()
			{
				$ents = array() ;
				foreach($this->_Entites as $nom => $ent)
				{
					if($ent->EstAccessible() == 0)
					{
						continue ;
					}
					$ents[$nom] = & $this->_Entites[$nom] ;
				}
				return $ents ;
			}
			public function ToutesEntites()
			{
				$ents = array() ;
				foreach($this->_Entites as $nom => $ent)
				{
					$ents[$nom] = & $this->_Entites[$nom] ;
				}
				return $ents ;
			}
		}
		
		class PvEntiteDonneesBase extends PvObjet
		{
			public $TableEditSql ;
			public $RequeteSelectSql ;
			public $NecessiteMembreConnecte = 1 ;
			protected $_Cols = array() ;
			protected $_Aspects = array() ;
			public $AspectCrudWeb ;
			public $InscrireAspectCrud = 1 ;
			public $InscrCmdCreatDansRead = 1 ;
			public $InscrLienUpdDansRead = 1 ;
			public $InscrLienDelDansRead = 1 ;
			public $RedirCmdAnnulVersRead = 1 ;
			public $NomElemIntegr ;
			public $IntegrationParent ;
			public $Active = 1 ;
			public $TxtsAction = array() ;
			public $TxtActionCreat ;
			public $TxtActionUpd ;
			public $TxtActionDel ;
			public $TxtActionRead ;
			public $TxtActionGlobal ;
			public function EstAccessible()
			{
				return $this->Active ;
			}
			public function CreeFournDonnees()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->IntegrationParent->BaseDonnees ;
				return $fourn ;
			}
			public function ObtientBDSupport()
			{
				return $this->IntegrationParent->BaseDonnees ;
			}
			public function AdopteIntegration($nom, & $integr)
			{
				$this->NomElemIntegr = $nom ;
				$this->IntegrationParent = & $integr ;
			}
			public function ChargeConfig()
			{
				$this->ChargeConfigAuto() ;
				$this->ChargeTxtsAction() ;
				$this->ChargeAspects() ;
				$this->ChargeCols() ;
				$this->ChargeConfigSuppl() ;
			}
			protected function ChargeConfigAuto()
			{
				if($this->InscrireAspectCrud == 1)
				{
					$this->AspectCrudWeb = $this->InsereAspect("aspect_crud", $this->IntegrationParent->CreeAspectCrudWeb()) ;
				}
			}
			protected function ChargeTxtsAction()
			{
				$this->InitTxtsAction() ;
				$this->ChargeTxtsActionSpec() ;
			}
			protected function InitTxtsAction()
			{
				$this->TxtActionCreat = $this->InsereTxtAction("creat") ;
				$this->TxtActionRead = $this->InsereTxtAction("read") ;
				$this->TxtActionUpd = $this->InsereTxtAction("upd") ;
				$this->TxtActionDel = $this->InsereTxtAction("del") ;
				$this->TxtActionGlobal = $this->InsereTxtAction("global") ;
				$this->TxtActionCreat->DefinitTitre("Ajout entit&eacute;") ;
				$this->TxtActionRead->DefinitTitre("Liste des entit&eacute;s") ;
				$this->TxtActionUpd->DefinitTitre("Modification entit&eacute;") ;
				$this->TxtActionDel->DefinitTitre("Suppression entit&eacute;") ;
				$this->TxtActionCreat->Exprs["lib_cmd"] = "Ajouter" ;
				$this->TxtActionUpd->Exprs["lib_cmd"] = "Modifier" ;
				$this->TxtActionDel->Exprs["lib_cmd"] = "Supprimer" ;
				$this->TxtActionRead->Exprs["lib_cmd"] = "Lister" ;
				$this->TxtActionGlobal->Exprs["lib_cmd_actions"] = "Actions" ;
				$this->TxtActionGlobal->Exprs["lib_cmd_annul"] = "Annuler" ;
				$this->TxtActionGlobal->Exprs["lib_cmd_exec"] = "Valider" ;
			}
			protected function ChargeTxtsActionSpec()
			{
			}
			public function TxtAction($nom)
			{
				return (isset($this->TxtsAction[$nom])) ? $this->TxtsAction[$nom] : new PvTxtActionEntiteDonnees() ;
			}
			public function ExprTxtAction($nom, $valeurDefaut=null)
			{
				$txtAction = $this->TxtAction($nom) ;
				return $txtAction->Expr($nom, $valeurDefaut) ;
			}
			protected function ChargeConfigSuppl()
			{
			}
			protected function & InsereTxtAction($nom)
			{
				$this->TxtsAction[$nom] = new PvTxtActionEntiteDonnees() ;
				return $this->TxtsAction[$nom] ;
			}
			protected function ChargeAspects()
			{
			}
			protected function ChargeCols()
			{
			}
			public function & InsereCol($nom, $col=null)
			{
				if($col == null)
				{
					$col = new PvColEntDonneesBase() ;
				}
				return $this->InscritCol($nom, $col) ;
			}
			public function & InsereColCle($nom)
			{
				$col = new PvColEntDonneesBase() ;
				$col->EstCleSql = true ;
				return $this->InsereCol($nom, $col) ;
			}
			public function & InsereColAutoIncr($nom)
			{
				$col = $this->InsereCol($nom) ;
				$col->EstCleSql = 1 ;
				$col->EstAutoIncrementSql = 1 ;
				return $col ;
			}
			public function & InsereColBool($nom)
			{
				$bd = $this->ObtientBDSupport() ;
				$col = $this->InsereCol($nom) ;
				$col->Listable = 1 ;
				$col->Editable = 1 ;
				$col->NomClasseCompEdit = "PvZoneSelectBoolHtml" ;
				$col->FormatteurCol = new PvFormatteurColonneBooleen() ;
				$col->InclureDansRech = 1 ;
				return $col ;
			}
			public function & InsereColDateFr($nom)
			{
				$bd = $this->ObtientBDSupport() ;
				$col = $this->InsereCol($nom) ;
				$col->Listable = 1 ;
				$col->Editable = 1 ;
				$col->NomClasseCompEdit = "PvCalendarDateInput" ;
				$col->FormatteurCol = new PvFormatteurColonneDateFr() ;
				$col->ExprRechSql = $bd->SqlDateToStr($bd->SqlDatePart('${col}')).' = <self>' ;
				$col->InclureDansRech = 0 ;
				return $col ;
			}
			public function & InsereColDateTimeFr($nom)
			{
				$bd = $this->ObtientBDSupport() ;
				$col = $this->InsereCol($nom) ;
				$col->Listable = 1 ;
				$col->Editable = 1 ;
				$col->NomClasseCompEdit = "PvCalendarDateInput" ;
				$col->FormatteurCol = new FmtColDateTimeFrEntDonnees() ;
				$col->ExprRechSql = $bd->SqlDateToStr($bd->SqlDatePart('${col}')).' = <self>' ;
				$col->InclureDansRech = 0 ;
				return $col ;
			}
			public function & InsereColTitre($nom)
			{
				$bd = $this->ObtientBDSupport() ;
				$col = $this->InsereCol($nom) ;
				$col->Listable = 1 ;
				$col->Editable = 1 ;
				$col->NomClasseCompEdit = "ZoneTitreEntDonnees" ;
				$col->InclureDansRech = 1 ;
				$col->ExprRechSql = 'upper('.$bd->SqlIndexOf('${col}', '<self>').') >= 1' ;
				return $col ;
			}
			public function & InsereColDescr($nom)
			{
				$col = $this->InsereCol($nom) ;
				$col->Listable = 1 ;
				$col->Editable = 1 ;
				$col->NomClasseCompEdit = "ZoneDescEntDonnees" ;
				return $col ;
			}
			public function & InsereColAlias($nom)
			{
				$col = $this->InsereCol($nom) ;
				$col->Listable = 1 ;
				$col->Editable = 0 ;
				return $col ;
			}
			public function & InscritCol($nom, & $col)
			{
				$this->_Cols[$nom] = & $col ;
				$col->NomColSql = $nom ;
				$col->NomParamHttp = $nom ;
				$col->Titre = $nom ;
				$col->AdopteEntite($nom, $this) ;
				return $col ;
			}
			public function & InsereAspect($nom, $aspect=null)
			{
				if($aspect == null)
				{
					$aspect = new PvAspectEntDonneesBase() ;
				}
				return $this->InscritAspect($nom, $aspect) ;
			}
			public function & InscritAspect($nom, & $aspect)
			{
				$this->_Aspects[$nom] = & $aspect ;
				$aspect->AdopteEntite($nom, $this) ;
				return $aspect ;
			}
			public function RemplitZoneWeb(& $zone)
			{
				$aspects = $this->Aspects() ;
				foreach($aspects as $n => $aspect)
				{
					// $aspect->ChargeConfig() ;
					$aspect->RemplitZoneWeb($zone) ;
				}
			}
			public function & Cols()
			{
				$cols = array() ;
				foreach($this->_Cols as $nom => $col)
				{
					if($col->EstAccessible() == 0)
					{
						continue ;
					}
					$cols[$nom] = & $this->_Cols[$nom] ;
				}
				return $cols ;
			}
			public function & ToutesCols()
			{
				$cols = array() ;
				foreach($this->_Cols as $nom => $col)
				{
					$cols[$nom] = & $this->_Cols[$nom] ;
				}
				return $cols ;
			}
			public function & Col($nomCol)
			{
				$col = new PvColEntDonneesNul() ;
				if(isset($this->Cols[$nomCol]))
				{
					$col = $this->_Cols[$nomCol] ;
				}
				return $col ;
			}
			public function ExisteCol($nomCol)
			{
				return (isset($this->_COls[$nomCol])) ;
			}
			public function & Aspects()
			{
				$aspects = array() ;
				foreach($this->_Aspects as $nom => $aspect)
				{
					if($aspect->EstAccessible() == 0)
					{
						continue ;
					}
					$aspects[$nom] = & $this->_Aspects[$nom] ;
				}
				return $aspects ;
			}
			public function & TousAspects()
			{
				$aspects = array() ;
				foreach($this->_Aspects as $nom => $aspect)
				{
					$aspects[$nom] = & $this->_Aspects[$nom] ;
				}
				return $aspects ;
			}
			public function & Aspect($nomAspect)
			{
				$aspect = new PvAspectEntDonneesNul() ;
				if(isset($this->_Aspects[$nomAspect]))
				{
					$aspect = $this->_Aspects[$nomAspect] ;
				}
				return $aspect ;
			}
			public function ExisteAspect($nomAspect)
			{
				return (isset($this->_Aspects[$nomAspect])) ;
			}
			public function InitFormEdit(& $form)
			{
			}
			public function ChargeFormEdit(& $form)
			{
			}
			public function InitTablList(& $form)
			{
			}
			public function ChargeTablList(& $form)
			{
			}
		}
		class PvEntiteDonneesNul extends PvEntiteDonneesBase
		{
		}
		
		class PvTxtActionEntiteDonnees
		{
			public $Exprs = array() ;
			public function ExisteExpr($nom)
			{
				return (isset($this->Exprs[$nom])) ;
			}
			public function InsereExpr($nom, $valeur)
			{
				$this->Exprs[$nom] = $valeur ;
			}
			public function Expr($nom, $valeurDefaut='')
			{
				return (isset($this->Exprs[$nom])) ? $this->Exprs[$nom] : $valeurDefaut ;
			}
			public function Titre()
			{
				return $this->Expr("titre") ;
			}
			public function TitreDocument()
			{
				return $this->Expr("titre", $this->Expr("titre_document")) ;
			}
			public function DefinitTitre($titre)
			{
				$this->Exprs["titre"] = $titre ;
			}
		}
		
		class PvElemEntDonneesBase
		{
			public $NomElemEntite ;
			public $EntiteParent ;
			public $Active = 1 ;
			public function IntegrationParent()
			{
				return $this->EntiteParent->IntegrationParent ;
			}
			public function ObtientBDSupport()
			{
				return $this->IntegrationParent()->BaseDonnees ;
			}
			public function AdopteEntite($nom, & $entite)
			{
				$this->NomElemEntite = $nom ;
				$this->EntiteParent = & $entite ;
			}
			public function EstAccessible()
			{
				return $this->Active ;
			}
		}
		
		class PvColEntDonneesBase extends PvElemEntDonneesBase
		{
			public $NomColSql = "" ;
			public $ExprColSql = "" ;
			public $AliasColSql = "" ;
			public $AliasColTriSql = "" ;
			public $AlignElemList = "" ;
			public $NomParamHttp = "" ;
			public $EstCleSql = false ;
			public $EstAutoIncrementSql = false ;
			public $Titre ;
			public $Description ;
			public $LibelleEdit = "" ;
			public $TitreList = "" ;
			public $PresentDansAspects = 1 ;
			public $Visible = 1 ;
			public $Listable = 0 ;
			public $Editable = 1 ;
			public $LectureSeule = 0 ;
			public $InclureDansRech = 0 ;
			public $FormatteurCol = null ;
			public $NomClasseCompEdit = "PvZoneTexteHtml" ;
			public $NomClasseCompRech = "" ;
			public $ExprRechSql = '${col} = <self>' ;
			public $FltEdit ;
			public $FltSelect ;
			public $CompEdit ;
			public $CompSelect ;
			public $DefCol ;
			public function EstNul()
			{
				return 0 ;
			}
			public function EstCle()
			{
				return $this->EstCleSql == 1 || $this->EstAutoIncrementSql == 1 ;
			}
			public function DansListage()
			{
				return $this->Visible == 1 && $this->Listable == 1 ;
			}
			public function DansRecherche()
			{
				return $this->Visible == 1 && $this->InclureDansRech == 1 ;
			}
			public function DansEdition()
			{
				return $this->Visible == 1 && $this->Editable == 1 ;
			}
			public function ObtientTitreList()
			{
				return ($this->TitreList != "") ? $this->TitreList : $this->ObtientTitre() ;
			}
			public function ObtientLibelleEdit()
			{
				return ($this->LibelleEdit != "") ? $this->LibelleEdit : $this->ObtientTitre() ;
			}
			public function ObtientNomParamHttp()
			{
				return ($this->NomParamHttp != "") ? $this->NomParamHttp : $this->NomElemEntite ;
			}
			public function ObtientNomColSql()
			{
				return ($this->NomColSql != "") ? $this->NomColSql : $this->NomElemEntite ;
			}
			public function ObtientTitre()
			{
				return ($this->Titre != "") ? $this->Titre : $this->NomElemEntite ;
			}
			public function ObtientNomClasseCompEdit()
			{
				return $this->NomClasseCompEdit ;
			}
			public function ObtientNomClasseCompRech()
			{
				return ($this->NomClasseCompRech != "") ? $this->NomClasseCompRech : $this->NomClasseCompEdit ;
			}
			public function & DefinitCompEdit(& $flt)
			{
				return $flt->DeclareComposant($this->ObtientNomClasseCompEdit()) ;
			}
			public function & DefinitCompRech(& $flt)
			{
				return $flt->DeclareComposant($this->ObtientNomClasseCompRech()) ;
			}
			public function & DefinitDefColList(& $defCol)
			{
				if($this->FormatteurCol != null)
				{
					$defCol->Formatteur = $this->FormatteurCol ;
				}
			}
			public function ChargeCfgCompEdit(& $comp)
			{
			}
			public function ChargeDefColList(& $defCol, & $tablParent)
			{
			}
		}
		class PvColEntDonneesNul extends PvColEntDonneesBase
		{
			public function EstNul()
			{
				return 1 ;
			}
		}

		class PvAspectEntDonneesBase extends PvElemEntDonneesBase
		{
			public function EstNul()
			{
				return 0 ;
			}
			protected function InsereScript($nom, $script, & $zone, $privs=array())
			{
				$integr = $this->IntegrationParent() ;
				$integr->InsereScriptAspect($nom."_".$this->EntiteParent->NomElemIntegr, $script, $this, $zone, $privs) ;
				return $script ;
			}
			public function RemplitZoneWeb(& $ihm)
			{
			}
		}
		class PvAspectEntDonneesNul extends PvAspectEntDonneesBase
		{
			public function EstNul()
			{
				return 1 ;
			}
		}
		class PvAspectCrudWebEntDonnees extends PvAspectEntDonneesBase
		{
			public $AccepterRead = 1 ;
			public $AccepterEdit = 1 ;
			public $PrivilegesRead = array() ;
			public $PrivilegesEdit = array() ;
			public $PrivilegesDel = array() ;
			public $PrivilegesCreat = array() ;
			public $PrivilegesUpd = array() ;
			public $ScriptRead ;
			public $ScriptCreat ;
			public $ScriptUpd ;
			public $ScriptDel ;
			public $UtiliserScriptsSpec = 0 ;
			protected function CreeScriptReadEntiteSpec()
			{
				return new PvScriptReadEntDonnees() ;
			}
			protected function CreeScriptReadEntite()
			{
				return ($this->UtiliserScriptsSpec) ? $this->CreeScriptReadEntiteSpec() : $this->IntegrationParent()->CreeScriptReadEntite() ;
			}
			protected function CreeScriptCreatEntiteSpec()
			{
				return new PvScriptCreatEntDonnees() ;
			}
			protected function CreeScriptCreatEntite()
			{
				return ($this->UtiliserScriptsSpec) ? $this->CreeScriptCreatEntiteSpec() : $this->IntegrationParent()->CreeScriptCreatEntite() ;
			}
			protected function CreeScriptUpdEntiteSpec()
			{
				return new PvScriptUpdEntDonnees() ;
			}
			protected function CreeScriptUpdEntite()
			{
				return ($this->UtiliserScriptsSpec) ? $this->CreeScriptUpdEntiteSpec() : $this->IntegrationParent()->CreeScriptUpdEntite() ;
			}
			protected function CreeScriptDelEntiteSpec()
			{
				return new PvScriptDelEntDonnees() ;
			}
			protected function CreeScriptDelEntite()
			{
				return ($this->UtiliserScriptsSpec) ? $this->CreeScriptDelEntiteSpec() : $this->IntegrationParent()->CreeScriptDelEntite() ;
			}
			public function RemplitZoneWeb(& $zone)
			{
				$integr = $this->IntegrationParent() ;
				// Read
				if($this->AccepterRead == 1)
				{
					$privs = $this->PrivilegesRead ;
					$this->ScriptRead = $this->InsereScript($integr->NomScriptRead, $this->CreeScriptReadEntite(), $zone, $privs) ;
				}
				if($this->AccepterEdit == 1)
				{
					// Creat
					$privs = $this->PrivilegesEdit ;
					if(count($this->PrivilegesCreat) > 0)
					{
						array_splice($privs, count($privs), 0, $this->PrivilegesCreat) ;
					}
					$this->ScriptCreat = $this->InsereScript($integr->NomScriptCreat, $this->CreeScriptCreatEntite(), $zone, $privs) ;
					// Upd
					$privs = $this->PrivilegesEdit ;
					if(count($this->PrivilegesUpd) > 0)
					{
						array_splice($privs, count($privs), 0, $this->PrivilegesUpd) ;
					}
					$this->ScriptUpd = $this->InsereScript($integr->NomScriptUpd, $this->CreeScriptUpdEntite(), $zone, $privs) ;
					// Del
					$privs = $this->PrivilegesEdit ;
					if(count($this->PrivilegesDel) > 0)
					{
						array_splice($privs, count($privs), 0, $this->PrivilegesDel) ;
					}
					$this->ScriptDel = $this->InsereScript($integr->NomScriptDel, $this->CreeScriptDelEntite(), $zone, $privs) ;
				}
			}
		}
		
		class PvScriptWebBaseEntDonnees extends PvScriptWebSimple
		{
			protected $NomTxtAction ;
			public function CreeFournDonnees()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->IntegrationParent()->BaseDonnees ;
				return $fourn ;
			}
			public function ObtientBDSupport()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->IntegrationParent()->BaseDonnees ;
				return $fourn ;
			}
			public function & EntiteDonneesParent()
			{
				$integr = $this->IntegrationParent() ;
				$nomEntite = $this->ObtientAttrSuppl($integr->IDInstanceCalc."_Entite") ;
				$entite = $integr->Entite($nomEntite) ;
				return $entite ;
			}
			public function & AspectDonneesParent()
			{
				$integr = $this->IntegrationParent() ;
				$entite = $this->EntiteDonneesParent() ;
				$nomAspect = $this->ObtientAttrSuppl($integr->IDInstanceCalc."_Aspect") ;
				$aspect = $entite->Aspect($nomAspect) ;
				return $aspect ;
			}
			public function DetermineEnvironnement()
			{
				$this->DetermineEnvScript() ;
				$this->DetermineEnvDonnees() ;
				$this->DetermineEnvSpec() ;
			}
			protected function DetermineEnvScript()
			{
				$entite = $this->EntiteDonneesParent() ;
				$txtAction = $entite->TxtAction($this->NomTxtAction) ;
				$this->TitreDocument = $txtAction->TitreDocument() ;
				$this->Titre = $txtAction->Titre() ;
			}
			protected function DetermineEnvDonnees()
			{
			}
			protected function DetermineEnvSpec()
			{
			}
		}
		
		class PvScriptReadEntDonnees extends PvScriptWebBaseEntDonnees
		{
			public $TablPrinc ;
			protected $NomTxtAction = "read" ;
			public $DefColsPrinc = array() ;
			public $FltsSelectPrinc = array() ;
			public $CmdAjoutPrinc ;
			public $DefColActsPrinc ;
			public $LienUpdPrinc ;
			public $LienDelPrinc ;
			protected function InitTablPrinc()
			{
				$entite = $this->EntiteDonneesParent() ;
				$entite->InitTablList($this->TablPrinc) ;
			}
			protected function ChargeTablPrinc()
			{
				$integr = $this->IntegrationParent() ;
				$entite = $this->EntiteDonneesParent() ;
				$aspect = $this->AspectDonneesParent() ;
				$this->TablPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->TablPrinc->FournisseurDonnees->RequeteSelection = $entite->RequeteSelectSql ;
				$cols = $entite->Cols() ;
				$paramsCle = '' ;
				foreach($cols as $nomCol => $col)
				{
					if($col->EstCle() == 1)
					{
						if($paramsCle != '')
						{
							$paramsCle .= '&' ;
						}
						$paramsCle .= 'cle_'.urlencode($col->ObtientNomParamHttp()).'=${'.$nomCol.'}' ;
						$this->DefColsPrinc[$nomCol] = $this->TablPrinc->InsereDefColCachee($nomCol) ;
						$cols[$nomCol]->DefCol = & $this->DefColsPrinc[$nomCol] ;
					}
					elseif($col->DansListage() == 1)
					{
						// echo $nomCol.' '.$col->Listable.'<br>' ;
						$this->DefColsPrinc[$nomCol] = $this->TablPrinc->InsereDefCol($nomCol, $col->ObtientTitreList(), $col->AliasColSql) ;
						$this->DefColsPrinc[$nomCol]->AliasDonneesTri = $col->AliasColTriSql ;
						$this->DefColsPrinc[$nomCol]->AlignElement = $col->AlignElemList ;
						$cols[$nomCol]->DefCol = & $this->DefColsPrinc[$nomCol] ;
						$cols[$nomCol]->DefinitDefColList($cols[$nomCol]->DefCol) ;
					}
					if($col->DansRecherche() == 1)
					{
						$this->FltsSelectPrinc[$nomCol] = $this->TablPrinc->InsereFltSelectHttpGet(
							"rech_".$col->ObtientNomParamHttp(),
							_parse_pattern($col->ExprRechSql, array('col' => $col->NomColSql))
						) ;
						$cols[$nomCol]->FltSelect = & $this->FltsSelectPrinc[$nomCol] ;
						$this->FltsSelectPrinc[$nomCol]->Libelle = $col->ObtientLibelleEdit() ;
						$cols[$nomCol]->DefinitCompRech($cols[$nomCol]->FltSelect) ;
					}
				}
				$this->DefColActsPrinc = $this->TablPrinc->InsereDefColActions($entite->TxtActionUpd->Expr("lib_cmd_actions")) ;
				if($entite->InscrLienUpdDansRead == 1)
				{
					$this->LienUpdPrinc = $this->TablPrinc->InsereLienAction($this->DefColActsPrinc, $aspect->ScriptUpd->ObtientUrl().(($paramsCle != '') ? '&'.$paramsCle : ''), $entite->TxtActionUpd->Expr("lib_cmd")) ;
				}
				if($entite->InscrLienDelDansRead == 1)
				{
					$this->LienDelPrinc = $this->TablPrinc->InsereLienAction($this->DefColActsPrinc, $aspect->ScriptDel->ObtientUrl().(($paramsCle != '') ? '&'.$paramsCle : ''), $entite->TxtActionDel->Expr("lib_cmd")) ;
				}
				if($entite->InscrCmdCreatDansRead == 1)
				{
					$this->CmdAjoutPrinc = $this->TablPrinc->InsereCmdRedirectUrl("ajout", $aspect->ScriptCreat->ObtientUrl()) ;
					$this->CmdAjoutPrinc->Libelle = $entite->TxtActionCreat->Expr("lib_cmd") ;
				}
				$entite->ChargeTablList($this->TablPrinc) ;
			}
			protected function DetermineTablPrinc()
			{
				$integr = $this->IntegrationParent() ;
				$this->TablPrinc = $integr->CreeTableauDonnees() ;
				$this->InitTablPrinc() ;
				$this->TablPrinc->AdopteScript("tablPrinc", $this) ;
				$this->TablPrinc->ChargeConfig() ;
				$this->ChargeTablPrinc() ;
			}
			public function DetermineEnvDonnees()
			{
				$this->DetermineTablPrinc() ;
			}
			public function RenduSpecifique()
			{
				return $this->TablPrinc->RenduDispositif() ;
			}
		}
		
		class PvScriptEditEntDonnees extends PvScriptWebBaseEntDonnees
		{
			public $FormPrinc ;
			public $FltsSelectPrinc ;
			public $FltsEditPrinc = array() ;
			protected function InitFormPrincAuto()
			{
			}
			protected function InitFormPrinc()
			{
				$entite = $this->EntiteDonneesParent() ;
				$entite->InitFormEdit($this->FormPrinc) ;
			}
			protected function ChargeFormPrinc()
			{
				$integr = $this->IntegrationParent() ;
				$entite = $this->EntiteDonneesParent() ;
				$aspect = $this->AspectDonneesParent() ;
				$cols = $entite->Cols() ;
				$bd = $this->ObtientBDSupport() ;
				foreach($cols as $nomCol => $col)
				{
					if($col->DansEdition() == 1 && $col->EstAutoIncrementSql == 0)
					{
						$this->FltsEditPrinc[$nomCol] = $this->FormPrinc->InsereFltEditHttpPost($nomCol, $col->NomColSql) ;
						$this->FltsEditPrinc[$nomCol]->Libelle = $col->ObtientLibelleEdit() ;
						$cols[$nomCol]->FltEdit = & $this->FltsEditPrinc[$nomCol] ;
						$cols[$nomCol]->DefinitCompEdit($cols[$nomCol]->FltEdit) ;
					}
					if($col->EstCle() == 1)
					{
						$this->FltsSelectPrinc[$nomCol] = $this->FormPrinc->InsereFltLgSelectHttpGet(
							"cle_".$col->ObtientNomParamHttp(),
							_parse_pattern($col->ExprRechSql, array('col' => $col->NomColSql))
						) ;
						$this->FltsSelectPrinc[$nomCol]->Libelle = $col->ObtientLibelleEdit() ;
						$cols[$nomCol]->FltSelect = & $this->FltsSelectPrinc[$nomCol] ;
					}
				}
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $entite->RequeteSelectSql ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $entite->TableEditSql ;
				$entite->ChargeFormEdit($this->FormPrinc) ;
			}
			protected function DetermineFormPrinc()
			{
				$integr = $this->IntegrationParent() ;
				$entite = $this->EntiteDonneesParent() ;
				$aspect = $this->AspectDonneesParent() ;
				$this->FormPrinc = $integr->CreeFormulaireDonnees() ;
				$this->FormPrinc->LibelleCommandeAnnuler = $entite->TxtActionGlobal->Expr("lib_cmd_annul") ;
				$this->FormPrinc->LibelleCommandeExecuter = $entite->TxtActionGlobal->Expr("lib_cmd_exec") ;
				$this->InitFormPrincAuto() ;
				$this->InitFormPrinc() ;
				$this->FormPrinc->AdopteScript("formPrinc", $this) ;
				$this->FormPrinc->ChargeConfig() ;
				if($entite->RedirCmdAnnulVersRead == 1)
				{
					$this->FormPrinc->RedirigeAnnulerVersUrl($aspect->ScriptRead->ObtientUrl()) ;
				}
				$this->ChargeFormPrinc() ;
			}
			protected function DetermineEnvDonnees()
			{
				$this->DetermineFormPrinc() ;
			}
			public function RenduSpecifique()
			{
				return $this->FormPrinc->RenduDispositif() ;
			}
		}
		class PvScriptCreatEntDonnees extends PvScriptEditEntDonnees
		{
			protected $NomTxtAction = "creat" ;
			protected function InitFormPrincAuto()
			{
				$this->FormPrinc->InclureElementEnCours = 0 ;
				$this->FormPrinc->InclureTotalElements = 0 ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->IntegrationParent()->NomClasseCmdExecCreat ;
			}
		}
		class PvScriptUpdEntDonnees extends PvScriptEditEntDonnees
		{
			protected $NomTxtAction = "upd" ;
			protected function InitFormPrincAuto()
			{
				$this->FormPrinc->InclureElementEnCours = 1 ;
				$this->FormPrinc->InclureTotalElements = 1 ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->IntegrationParent()->NomClasseCmdExecUpd ;
			}
		}
		class PvScriptDelEntDonnees extends PvScriptEditEntDonnees
		{
			protected $NomTxtAction = "del" ;
			protected function InitFormPrincAuto()
			{
				$this->FormPrinc->InclureElementEnCours = 1 ;
				$this->FormPrinc->InclureTotalElements = 1 ;
				$this->FormPrinc->Editable = 0 ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->IntegrationParent()->NomClasseCmdExecDel ;
			}
		}
		
		class ZoneTitreEntDonnees extends PvZoneTexteHtml
		{
			public $Largeur = "210px" ;
		}
		class ZoneDescEntDonnees extends PvZoneMultiligneHtml
		{
			public $TotalLignes = "6" ;
			public $TotalColonnes = "80" ;
		}
		
		class FmtColDateTimeFrEntDonnees extends PvFormatteurColonneDateFr
		{
			public $InclureHeure = 1 ;
		}
	}
	
?>