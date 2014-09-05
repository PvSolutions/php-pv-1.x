<?php
	
	if(! defined('COMP_PAGE_AFFICH_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../../../../_PVIEW/Pv/IHM/Compose.class.php" ;
		}
		define('COMP_PAGE_AFFICH_WSM', 1) ;
		
		class DonneesSupportLignePage
		{
			public $Url = array() ;
			public $Brut = array() ;
		}
		class CompBasePageAffichWsm extends PvComposantIUBase
		{
			public $ValeursPageAffich = array() ;
			public $PageAffich ;
			public $LierPageAffichScript = 1 ;
			public $ModelePage ;
			public $InclureClasseCSSBase = 1 ;
			public $NomClasseCSSBase = "" ;
			public $ClasseCSSSuppl = "" ;
			public function ExtraitDonneesSupportTabl($valeursPage)
			{
				$donneesSupport = new DonneesSupportLignePage() ;
				$donneesSupport->Brut = $valeursPage ;
				$donneesSupport->Url = array_merge(
					array(
						"nom_param_script_appele" => $this->ZoneParent->NomParamScriptAppele,
						"valeur_param_script_appele" => $this->ZoneParent->ValeurParamScriptAppele,
						"nom_param_id_page_affich" => $this->ScriptParent->NomParamIdPageAffich,
					),
					$donneesSupport->Brut
				) ;
				$donneesSupport->Url = array_map("urlencode", $donneesSupport->Url) ;
				return $donneesSupport ;
			}
			public function ExtraitDonneesSupport(& $page)
			{
				return $this->ExtraitDonneesSupportTabl($page->ValeursSupport()) ;
			}
			protected function CtnAttrClassComp()
			{
				$ctn = '' ;
				$ctn .= ' class="' ;
				if($this->InclureClasseCSSBase && $this->NomClasseCSSBase != "")
				{
					$ctn .= $this->NomClasseCSSBase ;
					if($this->ClasseCSSSuppl != '')
					{
						$ctn .= ' ' ;
					}
				}
				$ctn .= $this->ClasseCSSSuppl ;
				$ctn .= '"' ;
				return $ctn ;
			}
			public function ExistePageAffich()
			{
				if($this->LierPageAffichScript)
					return ($this->ScriptParent->EstPasNul($this->ScriptParent->PageAffich)) ? 1 : 0 ;
				return ($this->EstPasNul($this->PageAffich)) ? 1 : 0 ;
			}
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				if(! $ok)
					return 0 ;
				$ok = $this->ExistePageAffich() ;
				return $ok ;
			}
			protected function PrepareEnvPage()
			{
				if(! $this->ExistePageAffich())
					return ;
				if($this->LierPageAffichScript)
				{
					$this->PageAffich = & $this->ScriptParent->PageAffich ;
				}
				$this->ValeursPageAffich = $this->PageAffich->ValeursSupport() ;
				$this->ModelePage = & $this->ScriptParent->PageAffich->Modele ;
			}
			public function RenduDispositif()
			{
				$this->PrepareEnvPage() ;
				return parent::RenduDispositif() ;
			}
		}
		
		class GrillePageAffichWsm extends PvGrilleDonneesHtml
		{
			public $ContenuLigneModele = '' ;
			public $ContenuLigneModeleUse = '' ;
			public $EmpilerValeursSiModLigVide = 1 ;
			public $OrientationValeursEmpilees = "vertical" ;
			public $TotalColonnes = 1 ;
			public $LargeurBordure = 0 ;
		}
		
		class FilArianePageAffichWsm extends CompBasePageAffichWsm
		{
			public $FormatLibellePage = '${title_page}' ;
			public $FormatUrlPage = '?${nom_param_script_appele}=${valeur_param_script_appele}&${nom_param_id_page_affich}=${id_page}' ;
			public $SepLiens = " &gt; " ;
			public $InscrireLienAccueil = 1 ;
			public $InscrireLienPageAffich = 0 ;
			public $LibelleAccueil = "Accueil" ;
			public $UrlAccueil = "?" ;
			public $AttrsLienSupplAccueil = "" ;
			public $AttrsLienSupplPage = "" ;
			public $NomClasseCSSBase = "path" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div class="'.$this->NomClasseCSSBase.$this->ClasseCSSSuppl.'">'.PHP_EOL ;
				if($this->InscrireLienAccueil)
				{
					$ctn .= '<a href="'.$this->UrlAccueil.'"'.$this->AttrsLienSupplAccueil.'>'.htmlentities($this->LibelleAccueil).'</a>' ;
				}
				foreach($this->ScriptParent->PageAffich->IdCheminPages as $i => $idPage)
				{
					$page = & $this->ScriptParent->PageAffich->CheminPages[$idPage] ;
					$donneesSupport = $this->ExtraitDonneesSupport($page) ;
					if($page->Id == $this->PageAffich->Id && ! $this->InscrireLienPageAffich)
					{
						continue ;
					}
					if($this->InscrireLienAccueil || $i > 0)
						$ctn .= $this->SepLiens ;
					$ctn .= '<a href="'._parse_pattern($this->FormatUrlPage, $donneesSupport->Url).'"'.$this->AttrsLienSupplPage.'>'.htmlentities(_parse_pattern($this->FormatLibellePage, $donneesSupport->Brut)).'</a>'.PHP_EOL ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class ParcoursAncetrePageAffichWsm extends FilArianePageAffichWsm
		{
		}
		
		class BarreTitrePageAffichWsm extends CompBasePageAffichWsm
		{
			public $FormatTexte = '${title_page}' ;
			public $FormatCheminIcone = '${child_icon_page}' ;
			public $InclureIcone = 0 ;
			public $UseIconeDefautSiInexistant = 1 ;
			public $UrlIconeDefaut = "images/default_icon_page.png" ;
			public $LargeurIcone = "16" ;
			public $HauteurIcone = "0" ;
			public $SepIconeTitre = " " ;
			public $NomClasseCSSBase = "main_title" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '<h3' ;
				$ctn .= $this->CtnAttrClassComp() ;
				$ctn .= '>' ;
				if($this->InclureIcone)
				{
					$urlIcone = _parse_pattern($this->FormatCheminIcone, array_map('htmlentities', $this->ValeursPageAffich)) ;
					if(! file_exists($urlIcone) && $this->UseIconeDefautSiInexistant && $this->UrlIconeDefaut)
					{
						$urlIcone = $this->UrlIconeDefaut ;
					}
					$ctn .= '<img src="'.$urlIcone.'"'.(($this->LargeurIcone > "0") ? ' width="'.$this->LargeurIcone.'"' : '').(($this->HauteurIcone > 0) ? ' height="'.$this->HauteurIcone.'"' : '').' />'.$this->SepIconeTitre ;
				}
				$ctn .= _parse_pattern($this->FormatTexte, $this->ValeursPageAffich) ;
				$ctn .= '</h3>' ;
				return $ctn ;
			}
		}
		class BlocTextePageAffichWsm extends CompBasePageAffichWsm
		{
			public $FormatTexte = '${text_page}' ;
			public $SupprCtnSuspicieux = 1 ;
			public $InclureImage = 0 ;
			public $AlignImage = "left" ;
			public $EspacerImage = 1 ;
			public $FormatCheminImage = '${image_page}' ;
			public $LargeurImage = '' ;
			public $HauteurImage = '' ;
			public $NomClasseCSSImage = "illustration" ;
			public $InclureImageSiExiste = 1 ;
			public $NomClasseCSSBase = 'main_text' ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn = '<div' ;
				$ctn .= $this->CtnAttrClassComp() ;
				$ctn .= '>'.PHP_EOL ;
				if($this->InclureImage)
				{
					$cheminImage = _parse_pattern($this->FormatImage, $this->ValeursPageAffich) ;
					if($cheminImage == '')
					{
						$ctn .= '<span class="'.$this->NomClasseCSSImage.'" style="float:'.$this->AlignImage.'"><img src="'.$cheminImage.'"'.(($this->EspacerImage) ? ' style="padding:2px"' : '').(($this->LargeurImage != '') ? ' width="'.$this->LargeurImage.'"' : '').(($this->HauteurImage != '') ? ' width="'.$this->HauteurImage.'"' : '').' /></span>'.PHP_EOL ;
					}
				}
				$ctn .= _parse_pattern($this->FormatTexte, $this->ValeursPageAffich).PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class BlocInfosExtraWsm extends CompBasePageAffichWsm
		{
			public $NomClasseCSS = 'infos' ;
			public $DefInfos = array() ;
			public $SepInfo = " / " ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div class="'.$this->NomClasseCSS.'">' ;
				$ctnInfos = '' ;
				foreach($this->DefInfos as $nomColId => $nomColTitre)
				{
					$valId = $this->PageAffich->ObtientValeurExtra($nomColId) ;
					$valTitre = $this->PageAffich->ObtientValeurExtra($nomColTitre) ;
					if($valId != "" && $valTitre != null)
					{
						if($ctnInfos != '')
							$ctnInfos .= $this->SepInfo ;
						$ctnInfos .= '<a href="?action=show_page&id_page='.htmlentities($valId).'">'.htmlentities($valTitre).'</a>' ;
					}
				}
				$ctn .= $ctnInfos ;
				// print_r($this->PageAffich->ListeValeursExtra) ;
				$ctn .= '</div>'.PHP_EOL ;
				return $ctn ;
			}
		}
		class BlocLiensExtraPageAffichWsm extends CompBasePageAffichWsm
		{
			public $NomColonneEnum = "" ;
			public $SeparateurValEnum = "," ;
			public $FormatLibellePage = '${title_page}' ;
			public $FormatUrlPage = '?${nom_param_script_appele}=${valeur_param_script_appele}&${nom_param_id_page_affich}=${id_page}' ;
			public $AttrsLienSuppl = "," ;
			public $SepLiens = ", " ;
			public $CtnAvantLiens = "" ;
			public $CtnApresLiens = "" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$listeEnum = $this->PageAffich->ObtientValeurExtra($this->NomColonneEnum) ;
				if($listeEnum == '')
				{
					return ;
				}
				$bd = & $this->PageAffich->BaseDonneesParent ;
				$params = $bd->GetParamListFromValues("idPage_", explode($this->SeparateurValEnum, $listeEnum)) ;
				$sql = $bd->SqlObtientPageParIds($params) ;
				$ctn .= '<div class="'.$this->NomClasseCSSBase.$this->ClasseCSSSuppl.'">'.PHP_EOL ;
				$lignes = $bd->FetchSqlRows($sql, $params) ;
				$ctn .= $this->CtnAvantLiens ;
				foreach($lignes as $i => $ligne)
				{
					if($i > 0)
					{
						$ctn .= $this->SepLiens ;
					}
					$donneesSupport = $this->ExtraitDonneesSupportTabl($ligne) ;
					$ctn .= '<a href="'._parse_pattern($this->FormatUrlPage, $donneesSupport->Url).'"'.$this->AttrsLienSuppl.'>'.htmlentities(_parse_pattern($this->FormatLibellePage, $donneesSupport->Brut)).'</a>'.PHP_EOL ;
				}
				$ctn .= $this->CtnApresLiens ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class FormatLienSpecPageAffichWsm
		{
			public $CheminMiniature ;
			public $CheminIcone ;
			public $Libelle ;
			public $Visible = 1 ;
			public function ExtraitContenu(& $barre, & $pageAffich)
			{
				if($this->Visible == 0)
				{
					return '' ;
				}
				return $this->ExtraitContenuBrut($barre, $pageAffich) ;
			}
			protected function ExtraitContenuBrut(& $barre, & $pageAffich)
			{
			}
		}
		class BarreLiensSpecPageAffichWsm extends CompBasePageAffichWsm
		{
			public $NomClasseCSS = 'page_links' ;
			public $NomClasseCSSActions = 'actions' ;
			public $InclureLienRecommend = 1 ;
			public $LibelleLienRecommend = "Envoyer &agrave; un ami" ;
			public $CheminIconeRecommend = "images/recommend_icon.gif" ;
			public $LargeurIconeRecommend = 16 ;
			public $HauteurIconeRecommend = 16 ;
			public $ValParamScriptRecommend = "recommend_page" ;
			public $ValParamMdlPageAffichRecommend = "" ;
			public $InclureLienImprim = 1 ;
			public $LibelleLienImprim = "Imprimer" ;
			public $CheminIconeImprim = "images/print_icon.gif" ;
			public $LargeurIconeImprim = 16 ;
			public $HauteurIconeImprim = 16 ;
			public $ValParamScriptImprim = "show_printable_page" ;
			public $ValParamMdlPageAffichImprim = "" ;
			public $InclureLienTelecharg = 1 ;
			public $LibelleLienTelecharg = "T&eacute;l&eacute;charger" ;
			public $CheminIconeTelecharg = "images/download_file_icon.gif" ;
			public $CheminIconeTelechargPdf = "images/download_pdf_icon.gif" ;
			public $UseIconeTelechargPdf = 1 ;
			public $LargeurIconeTelecharg = 16 ;
			public $HauteurIconeTelecharg = 16 ;
			public $ValParamScriptTelecharg = "download_file_page" ;
			public $ValParamMdlPageAffichTelecharg = "" ;
			public $InclureLienAjtFav = 1 ;
			public $LibelleLienAjtFav = "Ajouter aux favoris" ;
			public $CheminIconeAjtFav = "images/bookmark_icon.gif" ;
			public $LargeurIconeAjtFav = 16 ;
			public $HauteurIconeAjtFav = 16 ;
			public $InclureActions = 1 ;
			public $SepLien = '&nbsp;&nbsp;&nbsp;' ;
			public $Formats = array() ;
			protected function RenduLien($nomParamScriptAppele, $valParamScriptAppele, $valParamPageAffich, $cibleFenetre, $cheminIcone, $largeurIcone, $hauteurIcone, $libelleLien, $ctnHref='')
			{
				$ctn = '' ;
				$urlPageAffich = '' ;
				if($ctnHref == '')
				{
					$nomParamScriptAppele = ($nomParamScriptAppele == '') ? $this->ZoneParent->NomParamScriptAppele : $nomParamScriptAppele ;
					$urlPageAffich = '?'.urlencode($nomParamScriptAppele).'='.urlencode($valParamScriptAppele) ;
					$urlPageAffich .= '&'.urlencode($this->ScriptParent->NomParamIdPageAffich).'='.urlencode($this->PageAffich->Id) ;
					if($valParamPageAffich != '')
					{
						$urlPageAffich .= '&'.urlencode($this->PageAffich->ObtientNomParamAffichSelect()).'='.urlencode($valParamPageAffich) ;
					}
				}
				else
				{
					$urlPageAffich = $ctnHref ;
				}
				$ctn .= '<a href="'.$urlPageAffich.'"'.(($cibleFenetre != '') ? ' target="'.$cibleFenetre.'"' : '').'><img src="'.$cheminIcone.'"'.(($largeurIcone) ? ' width="'.$largeurIcone.'"' : '').(($hauteurIcone) ? ' height="'.$hauteurIcone.'"' : '').' border="0" /> '.$libelleLien.'</a>' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div class="'.$this->NomClasseCSS.'">'.PHP_EOL ;
				$lienRendu = 0 ;
				if($this->InclureActions == 1)
				{
					$ctn .= '<div class="'.$this->NomClasseCSSActions.'">'.PHP_EOL ;
					if($this->InclureLienRecommend)
					{
						$ctn .= $this->RenduLien('', $this->ValParamScriptRecommend, '', '_blank', $this->CheminIconeRecommend, $this->LargeurIconeRecommend, $this->HauteurIconeRecommend, $this->LibelleLienRecommend) ;
						$lienRendu = 1 ;
					}
					if($this->InclureLienImprim)
					{
						if($lienRendu == 1)
						{
							$ctn .= $this->SepLien ;
						}
						$ctn .= $this->RenduLien('', $this->ValParamScriptImprim, $this->ValParamMdlPageAffichImprim, '_printable', $this->CheminIconeImprim, $this->LargeurIconeImprim, $this->HauteurIconeImprim, $this->LibelleLienImprim) ;
						$lienRendu = 1 ;
					}
					if($this->InclureLienTelecharg && $this->PageAffich->Fichier != '' && file_exists($this->PageAffich->Fichier))
					{
						if($lienRendu == 1)
						{
							$ctn .= $this->SepLien ;
						}
						$attrsFichier = pathinfo($this->PageAffich->Fichier) ;
						$cheminIcoTelecharg = $this->CheminIconeTelecharg ;
						if($this->UseIconeTelechargPdf && $attrsFichier["extension"] == 'pdf')
						{
							$cheminIcoTelecharg = $this->CheminIconeTelechargPdf ;
						}
						$ctn .= $this->RenduLien('', $this->ValParamScriptTelecharg, $this->ValParamMdlPageAffichTelecharg, '', $cheminIcoTelecharg, $this->LargeurIconeTelecharg, $this->HauteurIconeTelecharg, $this->LibelleLienTelecharg) ;
						$lienRendu = 1 ;
					}
					if($this->InclureLienAjtFav)
					{
						// print $this->PageAffich->Titre ;
						if($lienRendu == 1)
						{
							$ctn .= $this->SepLien ;
						}
						$ctn .= $this->RenduLien('', '', '', '', $this->CheminIconeAjtFav, $this->LargeurIconeAjtFav, $this->HauteurIconeAjtFav, $this->LibelleLienAjtFav, 'javascript:bookmarksite('.htmlentities(svc_json_encode(htmlentities($this->PageAffich->Titre))).', '.htmlentities(svc_json_encode(get_current_url())).') ;') ;
						$lienRendu = 1 ;
					}
					$ctn .= '</div>'.PHP_EOL ;
					/*
					*/
				}
				$ctn .= '</div>'.PHP_EOL ;
				return $ctn ;
			}
		}
	}
	
?>