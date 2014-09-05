<?php
	
	if(! defined('LIGNE_PAGE_BD_WSM'))
	{
		if(! defined('NOYAU_BD_WSM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('LIGNE_PAGE_BD_WSM', 1) ;
		
		class LignePageWsm extends LigneBaseWsm
		{
			public $Id = 0 ;
			public $ScriptParent = null ;
			public $IdPageParent = "0" ;
			public $IdCheminPages = array() ;
			public $CheminPages = array() ;
			public $Chemin ;
			public $TitreChemin ;
			public $TitreCourt ;
			public $Titre ;
			public $NomFichier ;
			public $Sommaire ;
			public $Texte ;
			public $IdAdminCreateur = 0 ;
			public $IdAdminModif = 0 ;
			public $DateCreation = "0001-01-01" ;
			public $HeureCreation = "00:00" ;
			public $DateModif = "0001-01-01" ;
			public $HeureModif = "00:00" ;
			public $StatutPublication = 0 ;
			public $DatePublication = "0001-01-01" ;
			public $HeurePublication = "00:00" ;
			public $TitreRecherche ;
			public $SommaireRecherche ;
			public $TexteRecherche ;
			public $MotsCleMeta ;
			public $DescriptionMeta ;
			public $ContenuExtra ;
			public $ListeValeursExtra ;
			public $CodeEvalue ;
			public $ScriptInclusion ;
			public $Type ;
			public $NomLang ;
			public $ExprLang ;
			public $NomModele ;
			public $ListageFilsModele ;
			public $ModeUseModele ;
			public $UrlRedirection ;
			public $Image ;
			public $Banniere ;
			public $Icone ;
			public $Video ;
			public $Document ;
			public $Son ;
			public $Fichier ;
			public $PresentDansMenu ;
			public $Modele = null ;
			public $TableEdition = "page" ;
			public $TotalSousPages = -1 ;
			public $TotalRelSrcPages = -1 ;
			public $TotalRelDestPages = -1 ;
			public $TotalSousPagesPubl = -1 ;
			public $TotalDescendantPages = -1 ;
			public $TotalDescendantPagesPubl = -1 ;
			public function AdopteScript(& $script)
			{
				$this->ScriptParent = & $script ;
				if($this->ScriptParent->EstPasNul($script->ZoneParent))
					$this->ZoneParent = & $script->ZoneParent ;
			}
			public function EstTrouve()
			{
				return ($this->Id > 0) ? 1 : 0 ;
			}
			public function EstVide()
			{
			}
			public function EstRacine()
			{
				return ($this->Id == $this->BaseDonneesParent->IdPageRacine) ? 1 : 0 ;
			}
			public function EstCorbeille()
			{
				return ($this->Id == $this->BaseDonneesParent->IdPageCorbeille && $this->BaseDonneesParent->IdPageCorbeille != 0) ? 1 : 0 ;
			}
			public function CreeModele()
			{
				$modele = null ;
				$nomModele = $this->NomModele ;
				$modele = $this->BaseDonneesParent->ObtientModelePage($nomModele) ;
				return $modele ;
			}
			protected function DeclareModele($nomModele, $modele)
			{
				$this->BaseDonneesParent->DeclareModelePage($nomModele, $modele) ;
			}
			public function ObtientNomParamAffichSelect()
			{
				$val = '' ;
				if($this->EstNul($this->Modele))
				{
					return $val ;
				}
				return $this->Modele->NomParamAffichageSelect ;
			}
			public function ObtientNomParamAffichageSelect()
			{
				return $this->ObtientNomParamAffichSelect() ;
			}
			public function & ObtientAffichSelect()
			{
				$affichSelect = '' ;
				if($this->EstNul($this->Modele))
				{
					return $affichSelect ;
				}
				return $this->Modele->AffichageSelect ;
			}
			public function & ObtientAffichageSelect()
			{
				$affichSelect = $this->ObtientAffichSelect() ;
				return $affichSelect ;
			}
			protected function ModeleVide()
			{
				return ($this->Modele == null || get_class($this->Modele) == get_class($this)) ;
			}
			public function DetecteModele()
			{
				$this->Modele = $this->CreeModele() ;
				$this->Modele->DeclarePageInstanciee($this) ;
			}
			public function DetecteListeValeursExtra()
			{
				if($this->ModeleVide())
				{
					$this->DetecteModele() ;
				}
				$this->ListeValeursExtra = $this->Modele->CreeListeValeursExtra() ;
				$this->ListeValeursExtra->ImportParContenu($this->ContenuExtra) ;
				// print_r($this->ListeValeursExtra) ;
			}
			public function DetermineVarsLocales()
			{
				$this->DetecteModele() ;
				$this->DetecteListeValeursExtra() ;
				$this->ChargeCheminPages() ;
				$this->ChargeStatsSousPages() ;
				$this->ChargeStatsPagesDescendant() ;
			}
			protected function ChargeStatsSousPages()
			{
				if($this->Modele->DefinitFamillePage == 0)
					return ;
				$this->TotalSousPages = $this->ParentDatabase->FetchSqlValue('select count(0) total from '.$this->ParentDatabase->Prefixe.'page where id_page_parent_page='.$this->ParentDatabase->ParamPrefix.'id', array('id' => $this->Id), 'total') ;
				$this->TotalSousPagesPubl = $this->ParentDatabase->FetchSqlValue('select count(0) total from '.$this->ParentDatabase->Prefixe.'page where id_page_parent_page='.$this->ParentDatabase->ParamPrefix.'id and is_publish_page=1', array('id' => $this->Id), 'total') ;
			}
			protected function ChargeStatsPagesDescendant()
			{
				if($this->Modele->DefinitFamillePage == 0)
					return ;
				$this->TotalPagesDescendant = $this->ParentDatabase->FetchSqlValue(
					'select count(0) total from '.$this->ParentDatabase->Prefixe.'page where '.$this->ParentDatabase->SqlIndexOf('path_page', $this->ParentDatabase->ParamPrefix.'chemin_page').' = 0 and id_page <> '.$this->ParentDatabase->ParamPrefix.'id', 
					array('chemin_page' => $this->Chemin, 'id' => $this->Id), 'total'
				) ;
				// print $this->ParentDatabase->LastSqlText ;
				$this->TotalPagesDescendantPubl = $this->ParentDatabase->FetchSqlValue('select count(0) total from '.$this->ParentDatabase->Prefixe.'page where '.$this->ParentDatabase->SqlIndexOf('path_page', $this->ParentDatabase->ParamPrefix.'chemin_page').' = 0 and id_page <> '.$this->ParentDatabase->ParamPrefix.'id and is_publish_page=1', array('chemin_page' => $this->Chemin, 'id' => $this->Id), 'total') ;
			}
			protected function ChargeCheminPages()
			{
				$this->IdCheminPages = explode(", ", $this->Chemin) ;
				$this->CheminPages = array() ;
				if(count($this->IdCheminPages) == 0)
					return ;
				// echo "llll" ;
				// print_r($this->IdCheminPages) ;
				array_splice($this->IdCheminPages, 0, 1) ;
				$this->CheminPages = array_assign_value(array(), $this->IdCheminPages, $this->BaseDonneesParent->ObtientPageNonTrouve()) ;
				$params = $this->BaseDonneesParent->GetParamListFromValues("idPage_", $this->IdCheminPages) ;
				$sql = $this->BaseDonneesParent->SqlObtientPageParIds($params) ;
				$lignes = $this->BaseDonneesParent->FetchSqlRows($sql, $params) ;
				// print $this->BaseDonneesParent->LastSqlText.'<br>' ;
				foreach($lignes as $i => $ligne)
				{
					$idPage = $ligne["id_page"] ;
					$this->CheminPages[$idPage] = $this->BaseDonneesParent->ObtientPageNonTrouve() ;
					$this->CheminPages[$idPage]->ImportConfigFromRow($ligne) ;
				}
			}
			public function ContientSousPages()
			{
				return ($this->TotalSousPages > 0) ;
			}
			public function ContientSousPagesPubl()
			{
				return ($this->TotalSousPagesPubl > 0) ;
			}
			public function ContientPagesDescendant()
			{
				return ($this->TotalPagesDescendant > 0) ;
			}
			public function ContientPagesDescendantPubl()
			{
				return ($this->TotalPagesDescendantPubl > 0) ;
			}
			public function ObtientValeurExtra($nom, $valeurDefaut=null)
			{
				return $this->RecupValeurExtra($nom, $valeurDefaut) ;
			}
			public function RecupValeurExtra($nom, $valeurDefaut=null)
			{
				$valeur = $valeurDefaut ;
				if($this->EstPasNul($this->ListeValeursExtra))
				{
					$valeur = $this->ListeValeursExtra->RecupValeur($nom, $valeurDefaut) ;
				}
				return $valeur ;
			}
			public function FixeValeurExtra($nom, $valeur)
			{
				$valeur = $valeurDefaut ;
				if($this->EstPasNul($this->ListeValeursExtra))
				{
					$valeur = $this->ListeValeursExtra->FixeValeur($nom, $valeur) ;
				}
				return $valeur ;
			}
			protected function ImportConfigFromRowValue($name, $value)
			{
				$success = parent::ImportConfigFromRowValue($name, $value) ;
				if($success)
				{
					return 1 ;
				}
				$success = 1 ;
				switch(strtolower($name))
				{
					case $this->PrefixeLigne."id_page".$this->SuffixeLigne : { $this->Id = $value ; } break ;
					case $this->PrefixeLigne."id_page_parent_page".$this->SuffixeLigne : { $this->IdPageParent = $value ; } break ;
					case $this->PrefixeLigne."path_page".$this->SuffixeLigne : { $this->Chemin = $value ; } break ;
					case $this->PrefixeLigne."path_title_page".$this->SuffixeLigne : { $this->TitreChemin = $value ; } break ;
					case $this->PrefixeLigne."short_title_page".$this->SuffixeLigne : { $this->TitreCourt = $value ; } break ;
					case $this->PrefixeLigne."title_page".$this->SuffixeLigne : { $this->Titre = $value ; } break ;
					case $this->PrefixeLigne."file_name_page".$this->SuffixeLigne : { $this->NomFichier = $value ; } break ;
					case $this->PrefixeLigne."summary_page".$this->SuffixeLigne : { $this->Sommaire = $value ; } break ;
					case $this->PrefixeLigne."text_page".$this->SuffixeLigne : { $this->Texte = $value ; } break ;
					case $this->PrefixeLigne."id_admin_creator_page".$this->SuffixeLigne : { $this->IdAdminCreateur = $value ; } break ;
					case $this->PrefixeLigne."id_admin_modif_page".$this->SuffixeLigne : { $this->IdAdminModif = $value ; } break ;
					case $this->PrefixeLigne."date_creation_page".$this->SuffixeLigne : { $this->DateCreation = $value ; } break ;
					case $this->PrefixeLigne."time_creation_page".$this->SuffixeLigne : { $this->HeureCreation = $value ; } break ;
					case $this->PrefixeLigne."date_modif_page".$this->SuffixeLigne : { $this->DateModif = $value ; } break ;
					case $this->PrefixeLigne."time_modif_page".$this->SuffixeLigne : { $this->HeureModif = $value ; } break ;
					case $this->PrefixeLigne."is_publish_page".$this->SuffixeLigne : { $this->StatutPublication = $value ; } break ;
					case $this->PrefixeLigne."date_publish_page".$this->SuffixeLigne : { $this->DatePublication = $value ; } break ;
					case $this->PrefixeLigne."time_publish_page".$this->SuffixeLigne : { $this->HeurePublication = $value ; } break ;
					case $this->PrefixeLigne."search_title_page".$this->SuffixeLigne : { $this->TitreRecherche = $value ; } break ;
					case $this->PrefixeLigne."search_summary_page".$this->SuffixeLigne : { $this->SommaireRecherche = $value ; } break ;
					case $this->PrefixeLigne."search_text_page".$this->SuffixeLigne : { $this->TexteRecherche = $value ; } break ;
					case $this->PrefixeLigne."meta_keywords_page".$this->SuffixeLigne : { $this->MotsCleMeta = $value ; } break ;
					case $this->PrefixeLigne."meta_description_page".$this->SuffixeLigne : { $this->DescriptionMeta = $value ; } break ;
					case $this->PrefixeLigne."extra_page".$this->SuffixeLigne : { $this->ContenuExtra = $value ; } break ;
					case $this->PrefixeLigne."evaluated_code_page".$this->SuffixeLigne : { $this->CodeEvalue = $value ; } break ;
					case $this->PrefixeLigne."included_script_page".$this->SuffixeLigne : { $this->ScriptInclusion = $value ; } break ;
					case $this->PrefixeLigne."type_page".$this->SuffixeLigne : { $this->Type = $value ; } break ;
					case $this->PrefixeLigne."name_lang_page".$this->SuffixeLigne : { $this->NomLang = $value ; } break ;
					case $this->PrefixeLigne."expr_lang_page".$this->SuffixeLigne : { $this->ExprLang = $value ; } break ;
					case $this->PrefixeLigne."template_name_page".$this->SuffixeLigne : { $this->NomModele = $value ; } break ;
					case $this->PrefixeLigne."template_child_listing_page".$this->SuffixeLigne : { $this->ListageFilsModele = $value ; } break ;
					case $this->PrefixeLigne."template_use_mode_page".$this->SuffixeLigne : { $this->ModeUseModele = $value ; } break ;
					case $this->PrefixeLigne."redirect_url_page".$this->SuffixeLigne : { $this->UrlRedirection = $value ; } break ;
					case $this->PrefixeLigne."image_page".$this->SuffixeLigne : { $this->Image = $value ; } break ;
					case $this->PrefixeLigne."banner_page".$this->SuffixeLigne : { $this->Banniere = $value ; } break ;
					case $this->PrefixeLigne."child_icon_page".$this->SuffixeLigne : { $this->Icone = $value ; } break ;
					case $this->PrefixeLigne."video_page".$this->SuffixeLigne : { $this->Video = $value ; } break ;
					case $this->PrefixeLigne."document_page".$this->SuffixeLigne : { $this->Document = $value ; } break ;
					case $this->PrefixeLigne."sound_page".$this->SuffixeLigne : { $this->Son = $value ; } break ;
					case $this->PrefixeLigne."file_page".$this->SuffixeLigne : { $this->Fichier = $value ; } break ;
					case $this->PrefixeLigne."appear_in_menu_page".$this->SuffixeLigne : { $this->PresentDansMenu = $value ; } break ;
					default : { $success = 0 ; } break ;
				}
				return $success ;
			}
			public function UpdateConfigBeforeImport()
			{
			}
			public function UpdateConfigAfterImport()
			{
			}
			public function ExpressionIdentifiant()
			{
				return "id_page=".$this->BaseDonneesParent->ParamPrefix."id_page" ;
			}
			public function ValeursIdentifiant()
			{
				$values = array() ;
				$values["id_page"] = $this->Id ;
				return $values ;
			}
			public function ValeursEdition()
			{
				$values = array() ;
				$values["id_page_parent_page"] = $this->IdPageParent ;
				$values["path_page"] = $this->Chemin ;
				$values["path_title_page"] = $this->TitreChemin ;
				$values["short_title_page"] = $this->TitreCourt ;
				$values["title_page"] = $this->Titre ;
				$values["file_name_page"] = $this->NomFichier ;
				$values["summary_page"] = $this->Sommaire ;
				$values["text_page"] = $this->Texte ;
				$values["id_admin_creator_page"] = $this->IdAdminCreateur ;
				$values["id_admin_modif_page"] = $this->IdAdminModif ;
				$values["date_creation_page"] = $this->DateCreation ;
				$values["time_creation_page"] = $this->HeureCreation ;
				$values["date_modif_page"] = $this->DateModif ;
				$values["time_modif_page"] = $this->HeureModif ;
				$values["is_publish_page"] = $this->StatutPublication ;
				$values["date_publish_page"] = $this->DatePublication ;
				$values["time_publish_page"] = $this->HeurePublication ;
				$values["search_title_page"] = $this->TitreRecherche ;
				$values["search_summary_page"] = $this->SommaireRecherche ;
				$values["search_text_page"] = $this->TexteRecherche ;
				$values["meta_keywords_page"] = $this->MotsCleMeta ;
				$values["meta_description_page"] = $this->DescriptionMeta ;
				$values["extra_page"] = $this->ContenuExtra ;
				$values["evaluated_code_page"] = $this->CodeEvalue ;
				$values["included_script_page"] = $this->ScriptInclusion ;
				$values["type_page"] = $this->Type ;
				$values["name_lang_page"] = $this->NomLang ;
				$values["expr_lang_page"] = $this->ExprLang ;
				$values["template_name_page"] = $this->NomModele ;
				$values["template_child_listing_page"] = $this->ListageFilsModele ;
				$values["template_use_mode_page"] = $this->ModeUseModele ;
				$values["redirect_url_page"] = $this->UrlRedirection ;
				$values["image_page"] = $this->Image ;
				$values["banner_page"] = $this->Banniere ;
				$values["child_icon_page"] = $this->Icone ;
				$values["video_page"] = $this->Video ;
				$values["document_page"] = $this->Document ;
				$values["sound_page"] = $this->Son ;
				$values["file_page"] = $this->Fichier ;
				$values["appear_in_menu_page"] = $this->PresentDansMenu ;
				return $values ;
			}
			public function ValeursSupport()
			{
				$valeurs = $this->ValeursEdition() ;
				$valeurs["id_page"] = $this->Id ;
				if($valeurs["child_icon_page"] == '' && $this->ParentDatabase != null)
				{
					$valeurs["child_icon_page"] = $this->ParentDatabase->SystemeParent->CheminIconePageParDefaut ;
				}
				return $valeurs ;
			}
		}
	}
	
?>