<?php
	
	if(! defined('BASE_REQ_SQL_DIFF'))
	{
		/*
		 *
		 Require : Zone with JQuery UI
		 * */
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../Base.class.php" ;
		}
		define('BASE_REQ_SQL_DIFF', 1) ;
		
		class RequeteSqlReqSqlDiff
		{
			public $Contenu ;
			public $Params = array() ;
			public $NomFichier = "" ;
		}
		
		class RptBaseReqSqlDiff
		{
			public $Active = 1 ;
			protected $FltIDFormEditDem ;
			public $DelaiExpirFich = 1 ;
			public $DelaiExpirTrt = 180 ;
			public $TablesCacheUse = array() ;
			public function Titre()
			{
				return "Base" ;
			}
			public function CheminIcone()
			{
				return "" ;
			}
			public function LgnDonnees()
			{
				return array(
					"nom" => $this->NomElementIntegr,
					"titre" => $this->Titre(),
					"chemin_icone" => $this->CheminIcone(),
				) ;
			}
			public function EstAccessible()
			{
				return $this->Active ;
			}
			public function CreeFormEditDemande()
			{
				return new FormEditDemReqSqlDiff() ;
			}
			public function InstalleFormEditDemande(& $script, $inclureElem=0, $editable=1, $cfgForm=null)
			{
				$form = $this->CreeFormEditDemande() ;
				$integr = $script->IntegrationParent() ;
				$this->InitFormEditDemande($form, $inclureElem, $editable, $cfgForm) ;
				$form->AdopteScript("formDemande", $script) ;
				$form->ChargeConfig() ;
				$this->ChargeFormEditDemandeAuto($form, $inclureElem, $editable, $cfgForm) ;
				$this->ChargeFormEditDemande($form, $inclureElem, $editable, $cfgForm) ;
				$form->FournisseurDonnees = $integr->CreeFournDonneesSupport() ;
				$form->FournisseurDonnees->RequeteSelection = $integr->NomTableDemande ;
				$form->FournisseurDonnees->TableEdition = $integr->NomTableDemande ;
				return $form ;
			}
			protected function InitFormEditDemande(& $form, $inclureElem, $editable, $cfgForm=null)
			{
				$form->InclureElementEnCours = $inclureElem ;
				$form->InclureTotalElements = $inclureElem ;
				$form->Editable = $editable ;
				$form->MaxFiltresEditionParLigne = 1 ;
				$form->LibelleCommandeAnnuler = "Annuler" ;
				if($editable == 0)
				{
					$form->NomClasseCommandeExecuter = "PvCommandeSupprElement" ;
					$form->LibelleCommandeExecuter = "Supprimer" ;
					$form->MsgExecSuccesCommandeExecuter = "Votre requ&ecirc;te a &eacute;t&eacute; supprim&eacute;e" ;
				}
				elseif($inclureElem == 0)
				{
					$form->NomClasseCommandeExecuter = "PvCommandeAjoutElement" ;
					$form->LibelleCommandeExecuter = "Ajouter" ;
					$form->MsgExecSuccesCommandeExecuter = "Votre requ&ecirc;te a &eacute;t&eacute; ajout&eacute;e." ;
				}
				else
				{
					$form->NomClasseCommandeExecuter = "PvCommandeModifElement" ;
					$form->LibelleCommandeExecuter = "Modifier" ;
					$form->MsgExecSuccesCommandeExecuter = "Votre requ&ecirc;te a &eacute;t&eacute; modifi&eacute;e." ;
				}
				$form->MessageAucunElement = "Aucune requ&ecirc;te n'a &eacute;t&eacute; trouv&eacute;e" ;
				// $form->CacherFormulaireFiltresApresCmd = 1 ;
			}
			protected function ChargeFormEditDemandeAuto(& $form, $inclureElem, $editable, $cfgForm=null)
			{
				$integr = $form->ScriptParent->IntegrationParent() ;
				$bd = $integr->CreeBDSupport() ;
				$rpt = $form->ScriptParent->ObtientRptBaseSelect() ;
				$this->FltIDFormEditDem = $form->InsereFltLgSelectHttpGet("id", "id = <self>") ;
				// Source
				$this->FltNomSrcFormEditDem = $form->InsereFltEditHttpPost("NOM_SOURCE", "NOM_SOURCE") ;
				$this->FltNomSrcFormEditDem->Libelle = "RAPPORT" ;
				$this->FltNomSrcFormEditDem->ValeurParDefaut = $rpt->NomElementIntegr ;
				$this->FltNomSrcFormEditDem->LectureSeule = 1 ;
				// Format de fichier
				$this->FltFichSrtFormEditDem = $form->InsereFltEditHttpPost("FICH_SORTIE", "FICH_SORTIE") ;
				$this->FltFichSrtFormEditDem->Libelle = "EXPORTER EN" ;
				$this->FltFichSrtFormEditDem->ValeurParDefaut = "CSV" ;
				$this->CompFichSrtEditDem = $this->FltFichSrtFormEditDem->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$this->CompFichSrtEditDem->FournisseurDonnees = $integr->CreeFournFichsSortie() ;
				$this->CompFichSrtEditDem->NomColonneValeur = "id" ;
				$this->CompFichSrtEditDem->NomColonneLibelle = "titre" ;
				// Fixer la progression
				$totalCache = $bd->FetchSqlValue("select count(0) TOTAL from ".$bd->EscapeTableName($integr->NomTableDemande)." where id_progression = 6 and nom_source=:nom_source", array("nom_source" => $rpt->NomElementIntegr), "TOTAL") ;
				$valProgress = ($totalCache > 0) ? 6 : 0 ;
				$this->FltIdProgrFormEditDem = $form->InsereFltEditFixe("ID_PROGRESSION", $valProgress, "ID_PROGRESSION") ;
				$this->FltIdProgrFormEditDem->Obligatoire = 1 ;
				// Rediriger la commande
				$form->RedirigeAnnulerVersUrl($integr->ScriptListDemande->ObtientUrlParam(array("rpt_base" => $rpt->NomElementIntegr))) ;
				// Critere sur la commande executer
				$form->CommandeExecuter->InsereNouvCritere($this->CreeCritrEditDemande()) ;
			}
			protected function CreeCritrEditDemande()
			{
				return new CritrEditDemandeReqSqlDiff() ;
			}
			protected function ChargeFormEditDemande(& $form, $inclureElem, $editable, $cfgForm=null)
			{
			}
			public function CreeTablListDemande()
			{
				return new PvTableauDonneesHtml() ;
			}
			public function InstalleTablListDemande(& $script)
			{
				$tabl = $this->CreeTablListDemande() ;
				$tabl->ToujoursAfficher = 1 ;
				$this->InitTablListDemande($tabl) ;
				$tabl->AdopteScript("tablDemande", $script) ;
				$tabl->ChargeConfig() ;
				$this->ChargeTablListDemande($tabl) ;
				return $tabl ;
			}
			protected function InitTablListDemande(& $tabl)
			{
			}
			protected function ChargeTablListDemande(& $tabl)
			{
				$integr = & $tabl->ScriptParent->IntegrationParent() ;
				$bd = $integr->CreeBDSupport() ;
				$rpt = $tabl->ScriptParent->ObtientRptBaseSelect() ;
				$this->ChargeFltsTablListDemande($tabl) ;
				$this->FltNomSrcListDem = $tabl->InsereFltSelectFixe("COND_SRC", $rpt->NomElementIntegr, "NOM_SOURCE = <self>") ;
				$this->FltIdAgtListDem = $tabl->InsereFltSelectFixe("COND_AGT", $tabl->ScriptParent->ZoneParent->IdMembreConnecteNatif(), "ID_AGENT = <self>") ;
				$this->DefIDTablListDem = $tabl->InsereDefColCachee("ID") ;
				$this->DefEditTablListDem = $tabl->InsereDefColCachee("EDITABLE", "CASE WHEN ID_PROGRESSION = 0 THEN 1 ELSE 0 END") ;
				$this->DefDateCreaTablListDem = $tabl->InsereDefColDateFr("DATE_CREATION", "CREE LE", 1) ;
				$this->DefDateCreaTablListDem->AliasDonnees = $bd->SqlDateToStr("DATE_CREATION", true) ;
				$this->DefDateCreaTablListDem->AliasDonneesTri = "DATE_CREATION" ;
				$this->DefDateExpirTablListDem = $tabl->InsereDefColDateFr("DATE_EXPIRATION", "EXPIRE LE", 1) ;
				$this->DefDateExpirTablListDem->AliasDonnees = $bd->SqlDateToStr("DATE_EXPIRATION", true) ;
				$this->DefDateExpirTablListDem->AliasDonneesTri = "DATE_EXPIRATION" ;
				$tabl->SensColonneTri = "desc" ;
				$this->DefEtatProgressTablListDem = $tabl->InsereDefColChoix("ID_PROGRESSION", "STATUT", "ID_PROGRESSION", $integr->ValeursProgressCSS()) ;
				$this->DefEtatProgressTablListDem->EncodeHtmlValeur = 0 ;
				$this->DefEtatProgressTablListDem->AlignElement = "center" ;
				$this->DefEtatProgressTablListDem->AliasDonneesTri = "ID_PROGRESSION asc, DATE_CREATION" ;
				$this->ChargeDefColsTablListDemande($tabl) ;
				$this->DefColActsTablListDem = $tabl->InsereDefColActions("ACTIONS") ;
				$this->ChargeLienActsTablListDemande($tabl, $this->DefColActsTablListDem) ;
				$this->ChargeFournTablListDemande($tabl) ;
			}
			protected function ChargeDefColsTablListDemande(& $tabl)
			{
			}
			protected function ChargeFltsTablListDemande(& $tabl)
			{
			}
			protected function ReqSelectTablListDemande(& $tabl)
			{
				$integr = $tabl->ScriptParent->IntegrationParent() ;
				return $integr->NomTableDemande ;
			}
			protected function ChargeFournTablListDemande(& $tabl)
			{
				$integr = $tabl->ScriptParent->IntegrationParent() ;
				$tabl->FournisseurDonnees = $integr->CreeFournDonneesSupport() ;
				$tabl->FournisseurDonnees->RequeteSelection = $this->ReqSelectTablListDemande($tabl) ;
			}
			protected function ChargeLienActsTablListDemande(& $tabl, & $defColActs)
			{
				$integr = $tabl->ScriptParent->IntegrationParent() ;
				$rptBase = $tabl->ScriptParent->ObtientRptBaseSelect() ;
				// $this->LienDetailListDem = $tabl->InsereLienAction($defColActs, $integr->ScriptDetailDemande->ObtientUrlFmt(array("id" => '${id}')), "D&eacute;tails") ;
				$this->LienModifListDem = $tabl->InsereLienAction($defColActs, $integr->ScriptModifDemande->ObtientUrlFmt(array("id" => '${id}', 'rpt_base' => $rptBase->NomElementIntegr)), "Modifier") ;
				$this->LienModifListDem->NomDonneesValid = "EDITABLE" ;
				$this->LienTelechListDem = $tabl->InsereLienAction($defColActs, $integr->ScriptListDemande->ActTelecharg1->ObtientUrlFmt(array("id" => '${id}', 'rpt_base' => $rptBase->NomElementIntegr)), "T&eacute;l&eacute;charger") ;
				$this->LienTelechListDem->NomDonneesValid = "ID_PROGRESSION" ;
				$this->LienTelechListDem->ValeurVraiValid = 3 ;
				$this->LienSupprListDem = $tabl->InsereLienAction($defColActs, $integr->ScriptSupprDemande->ObtientUrlFmt(array("id" => '${id}', 'rpt_base' => $rptBase->NomElementIntegr)), "Supprimer") ;
				$this->LienSupprListDem->NomDonneesValid = "EDITABLE" ;
			}
			public function ExtraitRequetesSql(& $bd, $lgnDem)
			{
				return array() ;
			}
			public function EnregReponse(&$bd, $lgnDem, $lgnRep)
			{
				return 1 ;
			}
			public function NomColsExportVide()
			{
				return array() ;
			}
		}
		
		class TableCacheReqSqlDiff
		{
			public $ColsSrc = "t1.*" ;
			public $NomTableSrc = "" ;
			public $NomTableDest = "" ;
			public $DelaiExpir = 3600 ; // En Seconde
			public $DelaiInactivite = 600 ; // En Seconde
			public function __construct($nomTableSrc, $nomTableDest, $delai=3600)
			{
				$this->NomTableSrc = $nomTableSrc ;
				$this->NomTableDest = $nomTableDest ;
				$this->DelaiExpir = $delai ;
			}
		}
		
		class FichSortieBaseReqSqlDiff
		{
			public function __construct()
			{
				register_shutdown_function(array(& $this, "FermeSupport")) ;
			}
			public function Id()
			{
				return "Base" ;
			}
			public function Titre()
			{
				return "Base" ;
			}
			public function LgnDonnees()
			{
				return array(
					"id" => $this->Id(),
					"titre" => $this->Titre(),
				) ;
			}
			public function ObtientChemin(& $integr, $id)
			{
				return "" ;
			}
			public function OuvreSupport(& $integr, $idDem, $lgn)
			{
			}
			public function EcritSupport($lgn)
			{
			}
			public function FermeSupport()
			{
			}
		}
		class FichSortieCsvReqSqlDiff extends FichSortieBaseReqSqlDiff
		{
			protected $ResFich = false ;
			public $Separateur = "," ;
			public $SymboleDebut = "\"" ;
			public $SymboleFin = "\"" ;
			public $SymboleEOL = "\n" ;
			public function Id()
			{
				return "CSV" ;
			}
			public function Titre()
			{
				return "CSV" ;
			}
			public function ObtientChemin(& $integr, $id)
			{
				return $integr->CheminDossierSorties."/".$id.".csv" ;
			}
			public function OuvreSupport(& $integr, $idDem, $lgn)
			{
				// echo "ID Dem : ".$idDem."\n" ;
				$this->ResFich = fopen($this->ObtientChemin($integr, $idDem), "w") ;
				$i = 0 ;
				if($this->ResFich !== false)
				{
					foreach($lgn as $n => $v)
					{
						if($i > 0){
							fputs($this->ResFich, $this->Separateur) ;
						}
						fputs($this->ResFich, $this->SymboleDebut.$n.$this->SymboleFin) ;
						$i++ ;
					}
					fputs($this->ResFich, $this->SymboleEOL) ;
				}
			}
			public function EcritSupport($lgn)
			{
				$i = 0 ;
				if($this->ResFich === false)
				{
					return ;
				}
				foreach($lgn as $n => $v)
				{
					if($i > 0){
						fputs($this->ResFich, $this->Separateur) ;
					}
					fputs($this->ResFich, $this->SymboleDebut.$v.$this->SymboleFin) ;
					$i++ ;
				}
				fputs($this->ResFich, $this->SymboleEOL) ;
			}
			public function FermeSupport()
			{
				if(! is_resource($this->ResFich))
				{
					return ;
				}
				fclose($this->ResFich) ;
			}
		}
		
		class ActTelechargFichReqSqlDiff extends PvActionTelechargFichier
		{
			public $NomFichierAttache = "resultats" ;
			public function Execute()
			{
				$this->DetermineDemandeReq() ;
				parent::Execute() ;
			}
			protected function DetermineDemandeReq()
			{
				$integr = $this->ScriptParent->IntegrationParent() ;
				$bd = $integr->CreeBDSupport() ;
				$this->LgnDem = $bd->FetchSqlRow("select * from ".$bd->EscapeTableName($integr->NomTableDemande)." where id=:id", array("id" => _GET_def("id"))) ;
				if(count($this->LgnDem) > 0)
				{
					// $fichSortie = $integr->ObtientFichSortie($this->LgnDem["FICH_SORTIE"]) ;
					// $this->CheminFichierSource = $fichSortie->ObtientChemin($integr, $this->LgnDem["ID"]) ;
					$this->CheminFichierSource = $integr->CheminDossierSorties."/".$this->LgnDem["ID"].".zip" ;
				}
				else
				{
					die("Le fichier que vous souhaitez t&eacute;l&eacute;charger n'est pas disponible sur ce serveur.") ;
				}
			}			
		}
		
		class FormEditDemReqSqlDiff extends PvFormulaireDonneesHtml
		{
			protected function ExecuteCommandeSelectionnee()
			{
				parent::ExecuteCommandeSelectionnee() ;
				if($this->PossedeCommandeSelectionnee())
				{
					$integr = $this->ScriptParent->IntegrationParent() ;
					if($this->CommandeSelectionnee->StatutExecution == 1)
					{
						$rpt = $this->ScriptParent->ObtientRptBaseSelect() ;
						redirect_to($integr->ScriptListDemande->ObtientUrlParam(array("rpt_base" => $rpt->NomElementIntegr))) ;
					}
					else
					{
						
					}
				}
			}
		}
		
		class IntegrBaseReqSqlDiff extends PvIntegration
		{
			public $NomTableDemande = "REQ_DIFF_DEMANDE" ;
			public $NomTableCaptTable = "REQ_DIFF_CAPT_TABLE" ;
			protected $RptsBase = array() ;
			protected $TablesCache = array() ;
			protected $FichsSortie = array() ;
			public $TotalProcessus = 2 ;
			public $CheminDossierSorties = "./resultats" ;
			public $CheminFichRelQueueTrtm = "" ;
			public $ScriptListDemande ;
			public $ScriptAjoutDemande ;
			public $ScriptModifDemande ;
			public $CacherBlocSelectRpt = 0 ;
			public $ScriptSupprDemande ;
			protected $BDSupport ;
			protected $FichSortieCsv ;
			public function CreeBDSupport()
			{
				return new AbstractSqlDB() ;
			}
			public function & InsereFichSortie($fichSortie)
			{
				$this->FichsSortie[$fichSortie->Id()] = & $fichSortie ;
				return $fichSortie ;
			}
			public function ObtientFichSortie($nom)
			{
				$res = null ;
				if(isset($this->FichsSortie[$nom]))
				{
					$res = $this->FichsSortie[$nom] ;
				}
				return $res ;
			}
			public function & InsereRptBase($nom, $source)
			{
				$this->RptsBase[$nom] = & $source ;
				$source->NomElementIntegr = $nom ;
				return $source ;
			}
			public function ObtientRptsBase()
			{
				return $this->RptsBase ;
			}
			public function ObtientRptBase($nom)
			{
				$src = null ;
				if(isset($this->RptsBase[$nom]))
				{
					$src = $this->RptsBase[$nom] ;
				}
				return $src ;
			}
			public function ExisteRptBase($nom)
			{
				if(isset($this->RptsBase[$nom]))
				{
					return 1 ;
				}
				return 0 ;
			}
			public function ValeursProgress()
			{
				return array(
					"En attente",
					"En cours",
					"Exportation",
					"Termine",
					"Annule",
					"Rejete",
					"Mise en cache",
					"Expire"
				) ;
			}
			public function ValeursProgressCSS()
			{
				return array(
					"<span style='color:maroon'>En attente</span>",
					"<span style='color:blue'>En cours</span>",
					"<span style='color:blue'>Exportation</span>",
					"<span style='color:green'>Termin&eacute;</span>",
					"<span style='color:red'>Annul&eacute;</span>",
					"<span style='color:red'>Rejet&eacute;</span>",
					"<span style='color:orange'>Mise en cache</span>",
					"<span style='color:orange'>Expir&eacute;</span>"
				) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->FichSortieCsv = $this->InsereFichSortie(new FichSortieCsvReqSqlDiff()) ;
				$this->ChargeFichsSortie() ;
				$this->ChargeTablesCache() ;
				$this->ChargeRptsBase() ;
			}
			public function ObtientTablesCache()
			{
				return $this->TablesCache ;
			}
			public function & InsereTableCache($nomTableSrc, $nomTableDest, $delai=3600)
			{
				$this->TablesCache[$nomTableDest] = new TableCacheReqSqlDiff($nomTableSrc, $nomTableDest, $delai) ;
				return $this->TablesCache[$nomTableDest] ;
			}
			public function ObtientTableCache($nom)
			{
				$res = null ;
				if(isset($this->TablesCache[$nom]))
				{
					$res = $this->TablesCache[$nom] ;
				}
				return $res ;
			}
			protected function ChargeFichsSortie()
			{
			}
			protected function ChargeTablesCache()
			{
			}
			protected function ChargeRptsBase()
			{
			}
			public function RemplitServsPersists(& $app)
			{
				for($i=0; $i<$this->TotalProcessus; $i++)
				{
					$serv = $this->InsereServPersist("queue_trt_".($i + 1), $this->CreeQueueTrtm(), $app) ;
					$serv->ArgsParDefaut["no_processus"] = $i + 1 ;
					$serv->CheminFichierRelatif = $this->CheminFichRelQueueTrtm ;
				}
			}
			public function ObtientQueueTrtm($index)
			{
				$svc = (isset($this->ApplicationParent->ServsPersists[$this->NomIntegration."_queue_trt_".($index + 1)])) ? $this->ApplicationParent->ServsPersists[$this->NomIntegration."_queue_trt_".($index + 1)] : null ;
				return $svc ;
			}
			public function ObtientQueuesTrtm()
			{
				$svcs = array() ;
				for($i=0; $i<$this->TotalProcessus; $i++)
				{
					$svcs[$this->NomIntegration."_queue_trt_".($index + 1)] = & $this->ApplicationParent->ServsPersists[$this->NomIntegration."_queue_trt_".($index + 1)] ;
				}
				return $svcs ;
			}
			public function & CreeFournFichsSortie()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$valeurs = array() ;
				foreach($this->FichsSortie as $i => $fichSrt)
				{
					$valeurs[] = $fichSrt->LgnDonnees() ;
				}
				$fourn->Valeurs["fichs_sortie"] = $valeurs ;
				return $fourn ;
			}
			public function & CreeFournRptsBase()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$valeurs = array() ;
				foreach($this->RptsBase as $i => $src)
				{
					$valeurs[] = $src->LgnDonnees() ;
				}
				$fourn->Valeurs["sources_base"] = $valeurs ;
				return $fourn ;
			}
			public function & CreeFournDonneesSupport()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->CreeBDSupport() ;
				return $fourn ;
			}
			public function NomPremRptBase()
			{
				$noms = array_keys($this->RptsBase) ;
				if(count($noms) == 0)
				{
					return null ;
				}
				return $noms[0] ;
			}
			protected function CreeScriptListDemande()
			{
				return new ScriptListDemandeReqSqlDiff() ;
			}
			protected function CreeScriptAjoutDemande()
			{
				return new ScriptAjoutDemandeReqSqlDiff() ;
			}
			protected function CreeScriptModifDemande()
			{
				return new ScriptModifDemandeReqSqlDiff() ;
			}
			protected function CreeScriptSupprDemande()
			{
				return new ScriptSupprDemandeReqSqlDiff() ;
			}
			protected function CreeScriptDetailDemande()
			{
				return new ScriptDetailDemandeReqSqlDiff() ;
			}
			protected function CreeFormSelectRptsBase()
			{
				return new PvFormulaireDonneesHtml() ;
			}
			protected function InitFormSelectRptsBase(& $form)
			{
				$form->InscrireCommandeAnnuler = false ;
				$form->LibelleCommandeExecuter = "Appliquer" ;
				$form->InclureElementEnCours = 0 ;
				$form->InclureTotalElements = 0 ;
				$form->InclureRenduLibelleFiltresEdition = 0 ;
				$form->CacherMessageExecution = 1 ;
			}
			protected function ChargeFormSelectRptsBase(& $form)
			{
				$this->FltRptBaseFormSelSB = $form->InsereFltEditHttpGet("rpt_base", "rpt_base") ;
				$this->FltRptBaseFormSelSB->Libelle = "Choisir" ;
				$this->FltRptBaseFormSelSB->ValeurParDefaut = $this->NomPremRptBase() ;
				$this->CompRptBaseFormSelSB = $this->FltRptBaseFormSelSB->DeclareComposant("PvZoneBoiteOptionsRadioHtml") ;
				$this->CompRptBaseFormSelSB->MaxColonnesParLigne = 1 ;
				$this->CompRptBaseFormSelSB->FournisseurDonnees = $this->CreeFournRptsBase() ;
				$this->CompRptBaseFormSelSB->NomColonneValeur = "nom" ;
				$this->CompRptBaseFormSelSB->NomColonneLibelle = "titre" ;
			}
			public function & InstalleFormSelectRptsBase(& $script)
			{
				$form = $this->CreeFormSelectRptsBase() ;
				$this->InitFormSelectRptsBase($form) ;
				$form->AdopteScript("formSelectRptsBase", $script) ;
				$form->ChargeConfig() ;
				$this->ChargeFormSelectRptsBase($form) ;
				return $form ;
			}
			protected function CreeQueueTrtm()
			{
				return new PvQueueDemandeReqSqlDiff() ;
			}
			protected function RemplitIHM(& $ihm)
			{
				if($ihm->NomElementApplication == $this->NomZoneWeb)
				{
					$this->RemplitZoneWeb($ihm) ;
				}
			}
			protected function RemplitZoneWeb(& $zone)
			{
				$this->ScriptListDemande = $this->InsereScript("liste_demande", $this->CreeScriptListDemande(), $zone) ;
				$this->ScriptDetailDemande = $this->InsereScript("detail_demande", $this->CreeScriptDetailDemande(), $zone) ;
				$this->ScriptAjoutDemande = $this->InsereScript("ajout_demande", $this->CreeScriptAjoutDemande(), $zone) ;
				$this->ScriptModifDemande = $this->InsereScript("modif_demande", $this->CreeScriptModifDemande(), $zone) ;
				$this->ScriptSupprDemande = $this->InsereScript("suppr_demande", $this->CreeScriptSupprDemande(), $zone) ;
				// print_r(array_keys($zone->Scripts)) ;
			}
			public function NomsRptsBasePrTableCache($nomTable)
			{
				$res = array() ;
				foreach($this->RptsBase as $nom => $rpt)
				{
					if(in_array($nomTable, $rpt->TablesCacheUse))
					{
						$res[$nom] = $nom ;
					}
				}
				return $res ;
			}
			public function AnnuleTablesCacheInactives()
			{
				$tbCachs = array() ;
				$bd = $this->CreeBDSupport() ;
				$lgns = $bd->FetchSqlRows(
					"select distinct NOM_TABLE from ".$bd->EscapeTableName($this->NomTableCaptTable)." where date_fin is null and ".$bd->SqlNow()." < date_inactivite",
					array()
				) ;
				$nomRpts = array() ;
				if(is_array($lgns) || count($lgns) == 0)
				{
					return ;
				}
				foreach($lgns as $i => $lgn)
				{
					$tbCach = $this->ObtientTableCache($lgn["NOM_TABLE"]) ;
					if($tbCach == null)
					{
						continue ;
					}
					$nomRpts = array_merge($nomRpts, $this->NomsRptsBasePrTableCache($lgn["NOM_TABLE"])) ;
				}
				$chaineLstRpts = ":".join(",:", $nomRpts) ;
				$bd->RunSql("update ".$bd->EscapeTableName($this->NomTableCaptTable)." set date_fin=".$bd->SqlNow()." where date_fin is null and ".$bd->SqlNow()." < date_inactivite", array()) ;
				if(count($nomRpts) > 0)
				{
					$bd->RunSql("update ".$bd->EscapeTableName($this->NomTableDemande)." set ID_PROGRESSION=0 where ID_PROGRESSION=6 and NOM_SOURCE in (".$chaineLstRpts.")", $nomsRpts) ;
				}
			}
		}
		
		class ResTrtElemQueueReqSqlDiff
		{
			public $ErreurSurvenue ;
			public $LgnDem ;
		}
		
		class PvQueueDemandeReqSqlDiff extends PvProcesseurQueueBase
		{
			public $MaxElementsChargmt = 10 ;
			public $DelaiEtatInactif = 600 ;
			public $DelaiAttente = 15 ;
			protected $BDSupport ;
			protected $AnnulerChargElems = false ;
			protected $ResTrtElem ;
			protected function VideFichsDemsExpir()
			{
				$integr = $this->IntegrationParent() ;
				$bd = & $this->BDSupport ;
				$query = $bd->OpenQuery("select * from ".$bd->EscapeTableName($integr->NomTableDemande)." where (date_expiration is not null and ".$bd->SqlNow()." > date_expiration) and id_progression=3") ;
				if($query !== false)
				{
					while($lgnDem = $bd->ReadQuery($query))
					{
						$chemFich = $integr->CheminDossierSorties."/".$lgnDem["ID"].".zip" ;
						if(file_exists($chemFich))
						{
							unlink($chemFich) ;
						}
					}
					$bd->CloseQuery($query) ;
				}
				$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=7 where (date_expiration is not null and ".$bd->SqlNow()." > date_expiration) and id_progression=3") ;
				$rpts = $integr->ObtientRptsBase() ;
				foreach($rpts as $j => $rpt)
				{
					$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=0, date_traitement=null where id_progression in (1, 2) and (date_traitement is not null and ".$bd->SqlAddSeconds("date_traitement", $rpt->DelaiExpirTrt)." < ".$bd->SqlNow().")") ;
				}
			}
			protected function SauveProgress($idDem, $idProgress)
			{
				$integr = $this->IntegrationParent() ;
				$bd = & $this->BDSupport ;
				$ok = $bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=:idProgress where id=:idDem and no_processus=:noProcessus",
					array(
						"idDem" => $idDem,
						"idProgress" => $idProgress,
						"noProcessus" => $this->NoProcessus(),
					)
				) ;
				return $ok ;
			}
			public function NoProcessus()
			{
				return (isset($this->ArgsParDefaut["no_processus"])) ? $this->ArgsParDefaut["no_processus"] : -1 ;
			}
			public function EstActif($cheminFichierAbsolu, $cheminFichierElementActif)
			{
				$ok = parent::EstActif($cheminFichierAbsolu, $cheminFichierElementActif) ;
				if(! $ok)
				{
					return $ok ;
				}
				$no_processus = -1 ;
				if(isset($this->Args["no_processus"]))
				{
					$no_processus = $this->Args["no_processus"] ;
				}
				if($no_processus != $this->NoProcessus())
				{
					$ok = false ;
				}
				return $ok ;
			}
			protected function ExecuteSession()
			{
				$integr = $this->IntegrationParent() ;
				$this->BDSupport = $integr->CreeBDSupport() ;
				$this->BDSupport->AutoCloseConnection = false ;
				$this->BDSupport->InitConnection() ;
				$this->VideFichsDemsExpir() ;
				$this->AnnulerChargElems = false ;
				parent::ExecuteSession() ;
				$this->BDSupport->FinalConnection() ;
			}
			protected function ConstruitTablesCache()
			{
				$integr = $this->IntegrationParent() ;
				$tablesCache = $integr->ObtientTablesCache() ;
				$bd = & $this->BDSupport ;
				$ixTbsExpir = array() ;
				foreach($tablesCache as $i => $tbCach)
				{
					$sqlVerifExpir = 'select '.$bd->SqlDateDiff($bd->SqlNow(), 'max(date_capture)').' DELAI_CAPTURE from '.$bd->EscapeTableName($tbCach->NomTableDest) ;
					$delaiCapt = $bd->FetchSqlValue($sqlVerifExpir, array(), "DELAI_CAPTURE", 0) ;
					if($delaiCapt === null || $delaiCapt >= $tbCach->DelaiExpir)
					{
						$ixTbsExpir[] = $i ;
					}
				}
				// print count($ixTbsExpir)." uuu\n" ;
				// print_r("Exception : ".$bd->LastSqlText." / ".$bd->ConnectionException) ;
				if(count($ixTbsExpir) > 0)
				{
					$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=6 where id_progression=0") ;
					$totalEnCours = $bd->FetchSqlValue("select count(0) TOTAL from ".$bd->EscapeTableName($integr->NomTableDemande)." t1 where id_progression in (1, 2)", array(), "TOTAL", 0) ;
					if($totalEnCours !== null && $totalEnCours == 0)
					{
						foreach($ixTbsExpir as $i => $idx)
						{
							$tbCach = & $tablesCache[$idx] ;
							$sqlSupprTbCach = "drop table ".$bd->EscapeTableName($tbCach->NomTableDest) ;
							$sqlInsertTbCach = "create table ".$bd->EscapeTableName($tbCach->NomTableDest)." as select ".$tbCach->ColsSrc.", ".$bd->SqlNow()." DATE_CAPTURE from ".$tbCach->NomTableSrc." t1" ;
							$bd->RunSql($sqlSupprTbCach) ;
							// echo "Creating ".$tbCach->NomTableDest."...\n" ;
							$ok = $bd->RunSql($sqlInsertTbCach) ;
							// print $bd->LastSqlText."\n" ;
						}
						$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=0 where id_progression=6") ;
					}
					else
					{
						$this->AnnulerChargElems = true ;
					}
				}
			}
			protected function ChargeElements()
			{
				$integr = $this->IntegrationParent() ;
				$bd = & $this->BDSupport ;
				$lgns = $bd->LimitSqlRows("select * from ".$bd->EscapeTableName($integr->NomTableDemande)." where (no_processus=0 or no_processus is null) and id_progression=0 order by date_creation asc", array(), 0, $this->MaxElementsChargmt) ;
				foreach($lgns as $i => $lgn)
				{
					$ok = $bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set no_processus=:no_processus where id=:id and (no_processus=0 or no_processus is null) and id_progression=0", array("no_processus" => $this->NoProcessus(), "id" => $lgn["ID"])) ;
				}
				$lgns = $bd->LimitSqlRows("select * from ".$bd->EscapeTableName($integr->NomTableDemande)." where no_processus = :no_processus and id_progression=0 order by date_creation desc", array("no_processus" => $this->NoProcessus()), 0, $this->MaxElementsChargmt) ;
				$this->ElementsBruts = $lgns ;
			}
			protected function ObtientNomsRptsTable($nomTable)
			{
				$integr = $this->IntegrationParent() ;
				$rpts = $integr->ObtientRptsBase() ;
				$res = array() ;
				foreach($rpts as $nom => & $rpt)
				{
					if(in_array($nomTable, $rpt->TablesCacheUse))
					{
						$res[$nom] = $nom ;
					}
				}
				return $res ;
			}
			protected function ConstruitTablesCacheRpt(& $rpt)
			{
				$integr = $this->IntegrationParent() ;
				$bd = & $this->BDSupport ;
				$tablesCache = array() ;
				foreach($rpt->TablesCacheUse as $i => $nomTable)
				{
					$tableCache = $integr->ObtientTableCache($nomTable) ;
					if($tableCache != null)
					{
						$tablesCache[$nomTable] = $tableCache ;
					}
				}
				foreach($tablesCache as $nomTable => $tbCach)
				{
					$sqlVerifExpir = 'select '.$bd->SqlDateDiff($bd->SqlNow(), 'max(date_capture)').' DELAI_CAPTURE from '.$bd->EscapeTableName($tbCach->NomTableDest) ;
					$delaiCapt = $bd->FetchSqlValue($sqlVerifExpir, array(), "DELAI_CAPTURE", 0) ;
					if($delaiCapt === null || $delaiCapt >= $tbCach->DelaiExpir)
					{
						$ixTbsExpir[] = $nomTable ;
					}
				}
				if(count($ixTbsExpir) > 0)
				{
					foreach($ixTbsExpir as $i => $idx)
					{
						$tbCach = & $tablesCache[$idx] ;
						$nomRpts = $this->ObtientNomsRptsTable($idx) ;
						if(count($nomRpts) == 0)
						{
							continue ;
						}
						$lstRpts = ":".join(",:", $nomRpts) ;
						$totalEnCours = $bd->FetchSqlValue(
							"select count(0) TOTAL from ".$bd->EscapeTableName($integr->NomTableDemande)." t1 where id_progression in (1, 2) and nom_source in (".$lstRpts.")",
							$nomRpts,
							"TOTAL",
							0
						) ;
						$totalCapt = $bd->FetchSqlValue(
							"select count(0) TOTAL from ".$bd->EscapeTableName($integr->NomTableCaptTable)." t1 where nom_table= :nomTable and date_fin = null",
							array("nomTable" => $idx),
							"TOTAL",
							0
						) ;
						if($totalEnCours !== null && $totalEnCours == 0 && $totalCapt !== null && $totalCapt == 0)
						{
							// Inserer la capture
							$idCapt = uniqid() ;
							$bd->RunSql(
								"insert into ".$bd->EscapeTableName($integr->NomTableCaptTable)." (NOM_TABLE, NO_PROCESSUS, DATE_DEBUT, ID_CAPTURE, DATE_INACTIVITE) values (:nomTable, :noProcessus, ".$bd->SqlNow().", :idCapt, ".$bd->SqlAddSeconds($bd->SqlNow(), $tbCach->DelaiInactivite).")", 
								array(
									"nomTable" => $idx,
									"noProcessus" => $this->NoProcessus(),
									"idCapt" => $idCapt,
								)
							) ;
							$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=6 where id_progression=0 and nom_source in (".$lstRpts.")", $nomRpts) ;
							$sqlSupprTbCach = "drop table ".$bd->EscapeTableName($tbCach->NomTableDest) ;
							$sqlInsertTbCach = "create table ".$bd->EscapeTableName($tbCach->NomTableDest)." as select ".$tbCach->ColsSrc.", ".$bd->SqlNow()." DATE_CAPTURE from ".$tbCach->NomTableSrc." t1" ;
							$bd->RunSql($sqlSupprTbCach) ;
							// echo "Creating ".$tbCach->NomTableDest."...\n" ;
							$ok = $bd->RunSql($sqlInsertTbCach) ;
							$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=0 where id_progression=6 and nom_source in (".$lstRpts.")", $nomRpts) ;
							// Mettre a jour la capture
							$bd->RunSql(
								"update ".$bd->EscapeTableName($integr->NomTableCaptTable)." set DATE_FIN=".$bd->SqlNow()." where ID_CAPTURE=:idCapt",
								array("idCapt" => $idCapt)
							) ;
							// print $bd->LastSqlText."\n" ;
						}
						else
						{
							$this->AnnulerChargElems = true ;
							break ;
						}
					}
				}
			}
			protected function TraiteElementActif()
			{
				$integr = $this->IntegrationParent() ;
				$elem = & $this->ElementActif ;
				$bd = & $this->BDSupport ;
				$lgnDem = $elem->ContenuBrut ;
				$this->ResTrtElem = new ResTrtElemQueueReqSqlDiff() ;
				$lgnConfirm = $bd->FetchSqlRow("select ID from ".$bd->EscapeTableName($integr->NomTableDemande)." where (no_processus=:no_processus and id_progression =0) and id=:idDem", array("idDem" => $lgnDem["ID"], "no_processus" => $this->NoProcessus())) ;
				if(count($lgnConfirm) == 0)
				{
					return ;
				}
				$rptBase = $integr->ObtientRptBase($lgnDem["NOM_SOURCE"]) ;
				$fichSortie = $integr->ObtientFichSortie($lgnDem["FICH_SORTIE"]) ;
				if($rptBase == null || $fichSortie == null)
				{
					return ;
				}
				$this->ConstruitTablesCacheRpt($rptBase) ;
				if($this->AnnulerChargElems)
				{
					return ;
				}
				$idElem = intval($lgnDem["ID"]) ;
				$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=1, date_traitement=".$bd->SqlNow()." where id=".$idElem) ;
				$reqsSql = $rptBase->ExtraitRequetesSql($bd, $lgnDem) ;
				$erreurSurvenue = 0 ;
				foreach($reqsSql as $j => $reqSql)
				{
					$query = $bd->OpenQuery($reqSql->Contenu, $reqSql->Params) ;
					$total = 0 ;
					if($query !== false)
					{
						$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=2 where id=".$idElem) ;
						while(($lgnRep = $bd->ReadQuery($query)) != false)
						{
							if($total == 0)
							{
								$nomFichReq = ($reqSql->NomFichier != "") ? $reqSql->NomFichier : $j ;
								$fichSortie->OuvreSupport($integr, $idElem."_".$nomFichReq, $lgnRep) ;
							}
							$fichSortie->EcritSupport($lgnRep) ;
							$total++ ;
						}
						$bd->CloseQuery($query) ;
						if($total == 0)
						{
							$nomFichReq = ($reqSql->NomFichier != "") ? $reqSql->NomFichier : $j ;
							$fichSortie->OuvreSupport($integr, $idElem."_".$nomFichReq, $rptBase->NomColsExportVide()) ;
						}
						$fichSortie->FermeSupport() ;
					}
					else
					{
						$erreurSurvenue = 1 ;
						break ;
					}
				}
				if($erreurSurvenue == 0)
				{
					$zipFile = new PersZip() ;
					foreach($reqsSql as $j => $reqSql)
					{
						$chemFich = $fichSortie->ObtientChemin($integr, $idElem."_".$j) ;
						if(! file_exists($chemFich))
						{
							continue ;
						}
						$pathInfoFich = pathinfo($chemFich) ;
						$zipFile->insertFile($chemFich, $pathInfoFich["basename"]) ;
						unlink($chemFich) ;
					}
					$zipFile->finalize();
					$zipFile->setZipFile($integr->CheminDossierSorties."/".$idElem.".zip");
					$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=3, date_publication=".$bd->SqlNow().", date_expiration=".$bd->SqlAddDays($bd->SqlNow(), $rptBase->DelaiExpirFich)." where id=".$idElem) ;
				}
				else
				{
					$bd->RunSql("update ".$bd->EscapeTableName($integr->NomTableDemande)." set id_progression=5, date_publication=".$bd->SqlNow()." where id=".$idElem) ;
				}
				$this->ResTrtElem->ErreurSurvenue = 0 ;
				$this->ResTrtElem->LgnDem = $lgnDem ;
			}
		}
		
		class Script1RptBaseReqSqlDiff extends PvScriptWebSimple
		{
			protected $FormSelectRptBase ;
			protected $NomRptBaseSelect ;
			protected $RptBaseSelect ;
			public function & ObtientRptBaseSelect()
			{
				return $this->RptBaseSelect ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$integr = $this->IntegrationParent() ;
				$this->FormSelectRptBase = $integr->InstalleFormSelectRptsBase($this) ;
				$this->DetermineRptBaseSelect() ;
			}
			protected function DetermineNomRptBaseSelect()
			{
				$integr = $this->IntegrationParent() ;
				$this->NomRptBaseSelect = $integr->NomPremRptBase() ;
				if($integr->FltRptBaseFormSelSB->Lie() != "" && $integr->ExisteRptBase($integr->FltRptBaseFormSelSB->Lie()))
				{
					$this->NomRptBaseSelect = $integr->FltRptBaseFormSelSB->Lie() ;
				}
			}
			protected function DetermineRptBaseSelect()
			{
				$this->DetermineNomRptBaseSelect() ;
				$integr = $this->IntegrationParent() ;
				$this->RptBaseSelect = $integr->ObtientRptBase($this->NomRptBaseSelect) ;
				$this->TitreDocument = $this->Titre." - ".$this->RptBaseSelect->Titre() ;
				$this->Titre = $this->Titre." - ".$this->RptBaseSelect->Titre() ;
			}
			public function RenduSpecifique()
			{
				$integr = $this->IntegrationParent() ;
				$ctn = '' ;
				$ctn .= '<table width="100%" cellspacing="0" cellpadding="2">'.PHP_EOL ;
				$ctn .= '<tr>'.PHP_EOL ;
				if($integr->CacherBlocSelectRpt == 0)
				{
					$ctn .= '<td width="30%" valign="top">'.PHP_EOL ;
					$ctn .= '<h4>Source des requ&ecirc;tes</h4>' ;
					$ctn .= $this->FormSelectRptBase->RenduDispositif() ;
					$ctn .= '</td>'.PHP_EOL ;
					$ctn .= '<td width="4" valign="top">&nbsp;</td>'.PHP_EOL ;
				}
				$ctn .= '<td width="*" valign="top">'.PHP_EOL ;
				// $ctn .= '<h4>'.$this->RptBaseSelect->Titre().'</h4>' ;
				$ctn .= $this->RenduComposantsSpec() ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduComposantsSpec()
			{
				return "" ;
			}
		}
		
		class ScriptListDemandeReqSqlDiff extends Script1RptBaseReqSqlDiff
		{
			public $Titre = "REQUETES" ;
			protected $Tabl1 ;
			public $ActTelecharg1 ;
			public $DelaiRafraichPage = 15 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->ActTelecharg1 = $this->InsereActionAvantRendu("telecharge", new ActTelechargFichReqSqlDiff()) ;
				$this->Tabl1 = $this->RptBaseSelect->InstalleTablListDemande($this) ;
			}
			public function RenduComposantsSpec()
			{
				$integr = $this->IntegrationParent() ;
				$ctn = '' ;
				$ctn .= '<div class="ui-widget"><a href="'.$integr->ScriptAjoutDemande->ObtientUrlParam(array("rpt_base" => $this->RptBaseSelect->NomElementIntegr)).'">Cr&eacute;er</a></div>' ;
				$ctn .= '<hr />' ;
				$ctn .= $this->Tabl1->RenduDispositif() ;
				$ctn .= '<script type="text/javascript">
	jQuery("document").ready(function() {
		setTimeout(
			function() {
				window.location = "?appelleScript='.urlencode($this->NomElementZone).'&rpt_base='.urlencode($this->RptBaseSelect->NomElementIntegr).'&'.$this->Tabl1->IDInstanceCalc.'_indice_tri='.urlencode($this->Tabl1->IndiceColonneTri).'&'.$this->Tabl1->IDInstanceCalc.'_sens_tri='.urlencode($this->Tabl1->SensColonneTri).'" ;
			},
			'.($this->DelaiRafraichPage * 1000).'
		) ;
	}) ;
</script>' ;
				return $ctn ;
			}
		}
		
		class CritrEditDemandeReqSqlDiff extends PvCritereBase
		{
			public function EstRespecte()
			{
				return 1 ;
			}
		}
		
		class ScriptEditDemandeReqSqlDiff extends Script1RptBaseReqSqlDiff
		{
			protected $InclureElemFormEditDem = 0 ;
			protected $EditableFormEditDem = 1 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->FormEditDem = $this->RptBaseSelect->InstalleFormEditDemande($this, $this->InclureElemFormEditDem, $this->EditableFormEditDem) ;
			}
			public function RenduComposantsSpec()
			{
				$integr = $this->IntegrationParent() ;
				$ctn = '' ;
				$ctn .= $this->FormEditDem->RenduDispositif() ;
				/*
				if($this->FormEditDem->PossedeCommandeSelectionnee())
				{
					$ctn .= '<div class="ui-widget" style="padding:8px"><a href="'.$integr->ScriptListDemande->ObtientUrl(array("rpt_base" => $this->RptBaseSelect->NomElementIntegr)).'">Retour aux requ&ecirc;tes</a></div>' ;
				}
				*/
				return $ctn ;
			}
		}
		class ScriptAjoutDemandeReqSqlDiff extends ScriptEditDemandeReqSqlDiff
		{
			public $Titre = "NOUVELLE REQUETE" ;
		}
		class ScriptModifDemandeReqSqlDiff extends ScriptEditDemandeReqSqlDiff
		{
			public $Titre = "MODIFICATION REQUETE" ;
			protected $InclureElemFormEditDem = 1 ;
		}
		class ScriptSupprDemandeReqSqlDiff extends ScriptEditDemandeReqSqlDiff
		{
			public $Titre = "SUPPRESSION REQUETE" ;
			protected $InclureElemFormEditDem = 1 ;
			protected $EditableFormEditDem = 0 ;
		}
		class ScriptDetailDemandeReqSqlDiff extends ScriptEditDemandeReqSqlDiff
		{
			public $Titre = "DETAIL REQUETE" ;
			protected $InclureElemFormEditDem = 1 ;
			protected $EditableFormEditDem = 0 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->FormEditDem->CacherBlocCommandes = 1 ;
			}
			public function RenduComposantsSpec()
			{
				$integr = $this->IntegrationParent() ;
				$ctn = parent::RenduComposantsSpec().PHP_EOL ;
				$ctn .= '<div class="ui-widget"><a href="'.$integr->ScriptListDemande->ObtientUrl().'">Retour aux demandes</a></div>' ;
				return $ctn ;
			}
		}
		
	}
	
?>