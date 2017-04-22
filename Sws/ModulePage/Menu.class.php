<?php
	
	if(! defined('MODULE_MENU_SWS'))
	{
		if(! defined('NOYAU_MODULE_PAGE_SWS'))
		{
			include dirname(__FILE__).'/Noyau.class.php' ;
		}
		define('MODULE_MENU_SWS', 1) ;
		
		class ModuleMenuSws extends ModulePageBaseSws
		{
			public $NomRef = "menu" ;
			public $TitreMenu = "Menus" ;
			public $EntiteMenu ;
			public $EntiteGroupeMenu ;
			protected function CreeEntiteGroupeMenu()
			{
				return new EntiteGroupeMenuSws() ;
			}
			protected function CreeEntiteMenu()
			{
				return new EntiteMenuSws() ;
			}
			protected function ChargeEntites()
			{
				$this->EntiteGroupeMenu = $this->InsereEntite("groupe_menu", $this->CreeEntiteGroupeMenu()) ;
				$this->EntiteMenu = $this->InsereEntite("menu", $this->CreeEntiteMenu()) ;
			}
		}
		
		class EntiteMenuSws extends EntiteTableSws
		{
			public $TitreMenu = "Menu" ;
			public $TitreAjoutEntite = "Ajout menu" ;
			public $TitreModifEntite = "Modification menu" ;
			public $TitreSupprEntite = "Suppression menu" ;
			public $TitreListageEntite = "Liste des menus" ;
			public $TitreConsultEntite = "Détails menu" ;
			public $InclureScriptConsult = 0 ;
			public $NomTable = "menu" ;
			public $NomEntite = "menu" ;
			public $LibCheminIcone = "Icone" ;
			public $LibTitre = "Titre" ;
			public $LibUrl = "URL" ;
			public $LibSommaire = "Sommaire" ;
			public $LibCheminImage = "Image" ;
			public $LibNomEntite = "Nom Entite" ;
			public $LibIdEntite = "ID Entite" ;
			public $LibIdGroupe = "Groupe" ;
			public $NomParamCheminIcone = "chemin_icone" ;
			public $NomParamTitre = "titre" ;
			public $NomParamUrl = "url" ;
			public $NomParamNomEntite = "nom_entite" ;
			public $NomParamSommaire = "sommaire" ;
			public $NomParamCheminImage = "chemin_image" ;
			public $NomParamIdEntite = "id_entite" ;
			public $NomParamIdGroupe = "id_groupe" ;
			public $NomColCheminIcone = "chemin_icone" ;
			public $NomColTitre = "titre" ;
			public $NomColSommaire = "sommaire" ;
			public $NomColUrl = "url" ;
			public $NomColCheminImage = "chemin_image" ;
			public $NomColIdGroupe = "id_groupe" ;
			public $AccepterImage = 1 ;
			public $AccepterIcone = 1 ;
			public $CheminTelechargImages = "images" ;
			public $CheminTelechargIcones = "images" ;
			public $FltFrmElemTitre ;
			public $FltFrmElemUrl ;
			public $FltFrmElemNomEntite ;
			public $FltFrmElemSommaire ;
			public $FltFrmElemCheminImage ;
			public $FltFrmElemCheminIcone ;
			public $FltFrmElemIdEntite ;
			public $FltFrmElemIdGroupe ;
			public $DefColTblListTitre ;
			public $DefColTblListGroupe ;
			public $NomParamTblListGroupe = "pGroupe" ;
			public $FltTblListGroupe ;
			public $TotalColonnesSommaire = 60 ;
			public $TotalLignesSommaire = 6 ;
			public $FltTblListTitre ;
			public $LibTblListTitre = "Titre" ;
			public $NomParamTblListTitre = "pTitre" ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				if($this->AccepterIcone == 1)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminIcone).' chemin_icone' ;
				}
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColUrl).' url' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColSommaire).' sommaire' ;
				if($this->AccepterImage == 1)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminImage).' chemin_image' ;
				}
				$sql .= ', '.$bd->EscapeVariableName($this->NomColIdGroupe).' id_groupe' ;
				return $sql ;
			}
			public function VerifPreReqsScriptEdit(& $script)
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = 'select count(0) total from '.$bd->EscapeVariableName($this->ModuleParent->EntiteGroupeMenu->NomTable) ;
				$total = $bd->FetchSqlValue($sql, array(), 'total') ;
				return $total > 0 ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				if($this->AccepterIcone == 1)
				{
					// CheminIcone
					$this->FltFrmElemCheminIcone = $frm->InsereFltEditHttpUpload($this->NomParamCheminIcone, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargIcones, $this->NomColCheminIcone) ;
					$this->FltFrmElemCheminIcone->Libelle = $this->LibCheminIcone ;
				}
				// Titre
				$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
				$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
				$comp1 = $this->FltFrmElemTitre->ObtientComposant() ;
				$comp1->Largeur = "180px" ;
				// URL
				$this->FltFrmElemUrl = $frm->InsereFltEditHttpPost($this->NomParamUrl, $this->NomColUrl) ;
				$this->FltFrmElemUrl->Libelle = $this->LibUrl ;
				$comp2 = $this->FltFrmElemUrl->ObtientComposant() ;
				$comp2->Largeur = "320px" ;
				// Sommaire
				$this->FltFrmElemSommaire = $frm->InsereFltEditHttpPost($this->NomParamSommaire, $this->NomColSommaire) ;
				$this->FltFrmElemSommaire->Libelle = $this->LibSommaire ;
				$comp = $this->FltFrmElemSommaire->DeclareComposant("PvZoneMultiligneHtml") ;
				$comp->TotalColonnes = $this->TotalColonnesSommaire ;
				$comp->TotalLignes = $this->TotalLignesSommaire ;
				if($this->AccepterImage == 1)
				{
					// Image
					$this->FltFrmElemCheminImage = $frm->InsereFltEditHttpUpload($this->NomParamCheminImage, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargImages, $this->NomColCheminImage) ;
					$this->FltFrmElemCheminImage->Libelle = $this->LibCheminImage ;
				}
				/*
				$this->FltFrmElemNomEntite = $frm->InsereFltEditHttpPost($this->NomParamNomEntite, $this->NomColNomEntite) ;
				$this->FltFrmElemNomEntite->Libelle = $this->LibNomEntite ;
				$comp = $this->FltFrmElemNomEntite->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$comp->FournisseurDonnees = $this->ModuleParent->SystemeParent->CreeFournEntites() ;
				$comp->RequeteSelection = "entites" ;
				$comp->NomColonneValeur = "nom" ;
				$comp->NomColonneLibelle = "libelle" ;
				$this->FltFrmElemIdEntite = $frm->InsereFltEditHttpPost($this->NomParamIdEntite, $this->NomColIdEntite) ;
				$this->FltFrmElemIdEntite->Libelle = $this->LibIdEntite ;
				*/
				$this->FltFrmElemIdGroupe = $frm->InsereFltEditHttpPost($this->NomParamIdGroupe, $this->NomColIdGroupe) ;
				$this->FltFrmElemIdGroupe->Libelle = $this->LibIdGroupe	;
				$comp = $this->FltFrmElemIdGroupe->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$comp->FournisseurDonnees = $frm->ScriptParent->ModulePage->CreeFournDonnees() ;
				$comp->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteGroupeMenu->NomTable ;
				$comp->NomColonneValeur = $this->ModuleParent->EntiteGroupeMenu->NomColId ;
				$comp->NomColonneLibelle = $this->ModuleParent->EntiteGroupeMenu->NomColTitre ;
			}
			protected function ReqSelectTblList(& $bd)
			{
				$sql = '(select t1.*, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteGroupeMenu->NomColId).' id_groupe_menu, t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteGroupeMenu->NomColTitre).' titre_groupe_menu from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($this->ModuleParent->EntiteGroupeMenu->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColIdGroupe).' = t2.'.$bd->EscapeVariableName($this->ModuleParent->EntiteGroupeMenu->NomColId).')' ;
				return $sql ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$this->DefColTblListTitre = $tbl->InsereDefCol($this->NomColTitre, $this->LibTitre) ;
				$this->DefColTblListTitre->Largeur = "20%" ;
				$this->DefColTblListGroupe = $tbl->InsereDefCol("titre_groupe_menu", $this->LibIdGroupe) ;
				$this->DefColTblListGroupe->Largeur = "20%" ;
				$bd = $this->ObtientBDSupport() ;
				$tbl->FournisseurDonnees->RequeteSelection = $this->ReqSelectTblList($bd) ;
				$this->FltTblListTitre = $tbl->InsereFltSelectHttpGet($this->NomParamTblListTitre, $bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColTitre).')', 'UPPER(<self>)').' > 0') ;
				$this->FltTblListTitre->Libelle = $this->LibTblListTitre ;
				$this->FltTblListGroupe = $tbl->InsereFltSelectHttpGet($this->NomParamTblListGroupe, $bd->EscapeVariableName($this->NomColIdGroupe).' = <self>') ;
				$this->FltTblListGroupe->Libelle = $this->LibIdGroupe ;
				$compGroupe = $this->FltTblListGroupe->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$compGroupe->FournisseurDonnees = $this->CreeFournDonnees() ;
				$compGroupe->FournisseurDonnees->RequeteSelection = $this->ModuleParent->EntiteGroupeMenu->NomTable ;
				$fltGroupeSelect = $tbl->ScriptParent->CreeFiltreHttpGet($this->NomParamTblListGroupe) ;
				$compGroupe->InclureElementHorsLigne = 1 ;
				$compGroupe->FiltresSelection[] = & $fltGroupeSelect ;
				$compGroupe->NomColonneValeur = $this->ModuleParent->EntiteGroupeMenu->NomColId ;
				$compGroupe->NomColonneLibelle = $this->ModuleParent->EntiteGroupeMenu->NomColTitre ;
			}
			protected function NomColsSommaire()
			{
				$result = parent::NomColsSommaire() ;
				$result[] = new ColSommaireEntiteSws($this->NomColTitre, $this->LibTitre) ;
				return $result ;
			}
		}
		class EntiteGroupeMenuSws extends EntiteTableSws
		{
			public $TitreMenu = "Groupe de menus" ;
			public $TitreAjoutEntite = "Ajout groupe de menus" ;
			public $TitreModifEntite = "Modification groupe de menus" ;
			public $TitreSupprEntite = "Suppression groupe de menus" ;
			public $TitreListageEntite = "Liste des groupes de menus" ;
			public $TitreConsultEntite = "Détails groupe de menus" ;
			public $InclureScriptConsult = 0 ;
			public $NomTable = "groupe_menu" ;
			public $NomEntite = "groupe_menu" ;
			public $LibEntite = "groupe de menus" ;
			public $LibTitre = "Titre" ;
			public $NomParamTitre = "titre" ;
			public $NomColTitre = "titre" ;
			public $FltFrmElemTitre ;
			public $DefColTblListTitre ;
			public $FltTblListTitre ;
			protected $PresentDansFluxRSS = 0 ;
			protected $PresentDansRech = 0 ;
			public $LibTblListTitre = "Titre" ;
			public $LibTotalMenus = "Total menus" ;
			public $NomParamTblListTitre = "pTitre" ;
			public $TitreDocScriptMenus = "Menus du groupe de menu" ;
			public $TitreScriptMenus = "Menus du groupe de menu" ;
			protected $ScriptMenus ;
			protected $FltFrmElemTotMenus ;
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				$sql .= ', total_menus titre' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				$bd = $this->ObtientBDSupport() ;
				$entMenu = & $this->ModuleParent->EntiteMenu ;
				$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
				$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
				$this->CompFrmElemTitre = $this->FltFrmElemTitre->ObtientComposant() ;
				$this->CompFrmElemTitre->Largeur = "300px" ;
				if($frm->InclureElementEnCours == 1)
				{
					$this->FltFrmElemTotMenus = $frm->InsereFltEditHttpPost("total_menus", $this->NomColId) ;
					$this->FltFrmElemTotMenus->NePasLierParametre = 1 ;
					$this->FltFrmElemTotMenus->NePasLierColonne = 1 ;
					$this->FltFrmElemTotMenus->Libelle = $this->LibTotalMenus ;
					$this->CompFrmElemTotMenus = $this->FltFrmElemTotMenus->DeclareComposant("PvZoneCorrespHtml") ;
					$this->CompFrmElemTotMenus->FournisseurDonnees = $this->CreeFournDonnees() ;
					$this->CompFrmElemTotMenus->FournisseurDonnees->RequeteSelection = '(select '.$bd->EscapeVariableName($entMenu->NomColIdGroupe).' id, count(0) total from '.$bd->EscapeTableName($entMenu->NomTable).' group by '.$bd->EscapeVariableName($entMenu->NomColIdGroupe).')' ;
					$this->CompFrmElemTotMenus->NomColonneValeur = $entMenu->NomColId ;
					$this->CompFrmElemTotMenus->NomColonneLibelle = "total" ;
				}
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				$entMenu = & $this->ModuleParent->EntiteMenu ;
				$tbl->FournisseurDonnees->RequeteSelection = '(select t1.*, count(0) total_menus from '.$bd->EscapeVariableName($this->NomTable).' t1 left join '.$bd->EscapeVariableName($entMenu->NomTable).' t2 on t1.'.$bd->EscapeVariableName($this->NomColId).' = t2.'.$bd->EscapeVariableName($entMenu->NomColIdGroupe).' group by t1.'.$bd->EscapeVariableName($this->NomColId).')' ;
				$this->DefColTblListTitre = $tbl->InsereDefCol($this->NomColTitre, $this->LibTitre) ;
				$this->DefColTblListTitre->Largeur = "30%" ;
				$this->DefColTblTotalMenus = $tbl->InsereDefCol("total_menus", $this->LibTotalMenus) ;
				$this->DefColTblTotalMenus->AlignElement = "center" ;
				$this->DefColTblTotalMenus->Largeur = "15%" ;
				$this->FltTblListTitre = $tbl->InsereFltSelectHttpGet($this->NomParamTblListTitre, $bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColTitre).')', 'UPPER(<self>)').' > 0') ;
				$this->FltTblListTitre->Libelle = $this->LibTblListTitre ;
			}
			protected function NomColsSommaire()
			{
				$result = parent::NomColsSommaire() ;
				$result[] = new ColSommaireEntiteSws($this->NomColTitre, $this->LibTitre) ;
				return $result ;
			}
			protected function FinalTblList(& $tbl)
			{
				$this->LienMenus = $tbl->InsereIconeAction($this->DefColTblListActs, $this->ScriptMenus->ObtientUrlFmt(array('id' => '${id}')), "images/icones/menus-groupe-menu.png", "Menus") ;
				parent::FinalTblList($tbl) ;
			}
			public function RemplitZoneAdmin(& $zone)
			{
				parent::RemplitZoneAdmin($zone) ;
				$this->ScriptMenus = $this->InsereScript("menus_groupe_menu", new ScriptMenusGroupeMenuSws(), $zone) ;
			}
		}
		
		class ScriptMenusGroupeMenuSws extends ScriptSommEntiteTableSws
		{
			public $EstScriptSession = 1 ;
			protected $DefColId ;
			protected $DefColTitre ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$entite = $this->ObtientEntitePage() ;
				$this->TitreDocument = $entite->TitreDocScriptMenus ;
				$this->Titre = $entite->TitreScriptMenus ;
			}
			protected function DetermineCompPrinc()
			{
				$entite = $this->ObtientEntitePage() ;
				$entMenu = & $entite->ModuleParent->EntiteMenu ;
				$bd = & $entite->ObtientBDSupport() ;
				$this->CompPrinc = new PvTableauDonneesHtml() ;
				$this->CompPrinc->AdopteScript("compPrinc", $this) ;
				$this->CompPrinc->ChargeConfig() ;
				$this->DefColId = $this->CompPrinc->InsereDefColCachee($this->NomColId) ;
				$this->DefColTitre = $this->CompPrinc->InsereDefCol($entMenu->NomColTitre, $entMenu->NomLibTitre) ;
				$fournDonnees = $entite->CreeFournDonnees() ;
				$fournDonnees->RequeteSelection = $bd->EscapeTableName($entMenu->NomTable) ;
				$this->CompPrinc->FournisseurDonnees = & $fournDonnees ;				
			}
		}
	}
	
?>