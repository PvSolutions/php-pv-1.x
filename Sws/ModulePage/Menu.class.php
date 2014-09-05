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
			public $TitreConsultEntite = "D�tails menu" ;
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
					$this->FltFrmElemCheminIcone = $frm->InsereFltEditHttpUpload($this->NomParamCheminIcone, $this->CheminTelechargIcones, $this->NomColCheminIcone) ;
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
					$this->FltFrmElemCheminImage = $frm->InsereFltEditHttpUpload($this->NomParamCheminImage, $this->CheminTelechargImages, $this->NomColCheminImage) ;
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
		}
		class EntiteGroupeMenuSws extends EntiteTableSws
		{
			public $TitreMenu = "Groupe de menus" ;
			public $TitreAjoutEntite = "Ajout groupe de menus" ;
			public $TitreModifEntite = "Modification groupe de menus" ;
			public $TitreSupprEntite = "Suppression groupe de menus" ;
			public $TitreListageEntite = "Liste des groupes de menus" ;
			public $TitreConsultEntite = "D�tails groupe de menus" ;
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
			public $LibTblListTitre = "Titre" ;
			public $NomParamTblListTitre = "pTitre" ;
			public function SqlListeColsSelect(& $bd)
			{
				$sql = parent::SqlListeColsSelect($bd) ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				return $sql ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
				$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
				$this->FltFrmElemTitre->Largeur = "180" ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				$this->DefColTblListTitre = $tbl->InsereDefCol($this->NomColTitre, $this->LibTitre) ;
				$this->DefColTblListTitre->Largeur = "30%" ;
				$this->FltTblListTitre = $tbl->InsereFltSelectHttpGet($this->NomParamTblListTitre, $bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColTitre).')', 'UPPER(<self>)').' > 0') ;
				$this->FltTblListTitre->Libelle = $this->LibTblListTitre ;
			}
			protected function FinalTblList(& $tbl)
			{
				parent::FinalTblList($tbl) ;
			}
		}
		
	}
	
?>