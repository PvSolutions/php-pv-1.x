<?php
	
	if(! defined('COMPOSANT_IU_ARTICLE_SWS'))
	{
		define('COMPOSANT_IU_ARTICLE_SWS', 1) ;
		
		class LienSpecArtSws extends PvObjet
		{
			public $CheminIcone ;
			public $Visible = 1 ;
			public $FormatLibelle ;
			public $FenetreCible ;
			public $FormatUrl ;
			public $NomClsCSS ;
			public $StyleCSS ;
			public $AttrsSuppl ;
			public function EstVisible()
			{
				return $this->Visible == 1 ;
			}
			protected function Prepare(& $script, & $composant, $donnees)
			{
			}
			public function Applique(& $script, & $composant, $donnees)
			{
				$this->Prepare($script, $composant, $donnees) ;
				$donneesUrl = array_map('urlencode', $donnees) ;
				$donneesHtml = array_map('htmlentities', $donnees) ;
				$ctn = '' ;
				$ctn .= '<a' ;
				if($this->FormatUrl != '')
				{
					$ctn .= ' href="'.htmlentities(_parse_pattern($this->FormatUrl, $donneesUrl)).'"' ;
				}
				if($this->FenetreCible != '')
				{
					$ctn .= ' target="'.$this->FenetreCible.'"' ;
				}
				if($this->NomClsCSS != '')
				{
					$ctn .= ' class="'.$this->NomClsCSS.'"' ;
				}
				if($this->StyleCSS != '')
				{
					$ctn .= ' style="'.$this->StyleCSS.'"' ;
				}
				if($this->AttrsSuppl != '')
				{
					$ctn .= ' '.$this->AttrsSuppl.'' ;
				}
				$ctn .= '>' ;
				if($composant->InclureRenduIcone)
				{
					$ctn .= '<img src="'.$this->CheminIcone.'"' ;
					if($composant->LargeurIcone != "")
						$ctn .= ' width="'.$composant->LargeurIcone.'"' ;
					if($composant->HauteurIcone != "")
						$ctn .= ' width="'.$composant->HauteurIcone.'"' ;
					$ctn .= ' border="0"' ;
					$ctn .= '>' ;
					$ctn .= $composant->SepIconeLibelle ;
				}
				$ctn .= _parse_pattern($this->FormatLibelle, $donneesHtml) ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
		}
		class LienAjoutFavArtSws extends LienSpecArtSws
		{
			public $FormatTitreFav = "Nouveau favori" ;
			public $CheminIcone = "images/bookmark_icon.gif" ;
			public $FormatLibelle = "Ajouter aux favoris" ;
			protected static $SourceIncluse = 0 ;
			protected function Prepare(& $script, & $composant, $donnees)
			{
				$this->FormatUrl = 'javascript:bookmarksite('.svc_json_encode($this->FormatTitreFav).', '.svc_json_encode(get_current_url()).') ;' ;
			}
			protected function RenduDefJs()
			{
				return '<script type="text/javascript">
/***********************************************
* Bookmark site script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/
/* Modified to support Opera */
function bookmarksite(title, url){
if (window.sidebar)
{
	window.sidebar.addPanel(title, url, "");
}
else if(window.opera && window.print){
	var elem = document.createElement(\'a\');
	elem.setAttribute(\'href\',url);
	elem.setAttribute(\'title\',title);
	elem.setAttribute(\'rel\',\'sidebar\');
	elem.click() ;
} 
else if(document.all) {
	window.external.AddFavorite(url, title);
}
}
</script>' ;
			}
			public function Applique(& $script, & $composant, $donnees)
			{
				$ctn = '' ;
				if($this->ObtientValeurStatique("SourceIncluse") == 0)
				{
					$ctn .= $this->RenduDefJs() ;
					$this->AffecteValeurStatique("SourceIncluse", 1) ;
				}
				$ctn .= parent::Applique($script, $composant, $donnees) ;
				return $ctn ;
			}
		}
		class LienVersionImprArtSws extends LienSpecArtSws
		{
			public $FenetreCible = "_imprimable" ;
			public $CheminIcone = "images/print_icon.gif" ;
			public $FormatLibelle = "Imprimer" ;
			protected function Prepare(& $script, & $composant, $donnees)
			{
			}
		}
		class LienRecommenderArtSws extends LienSpecArtSws
		{
			public $FenetreCible = "_recommende" ;
			public $FormatLibelle = "Recommender" ;
			public $CheminIcone = "images/recommend_icon.gif" ;
			protected function Prepare(& $script, & $composant, $donnees)
			{
			}
		}
		class BarreLiensSpecArtSws extends PvComposantIUBase
		{
			public $SepLiens = "&nbsp;&nbsp;&nbsp;&nbsp;" ;
			public $Liens = array() ;
			public $NomClsCSS = "" ;
			public $DonneesSupport = array() ;
			public $InclureLiensAuto = 1 ;
			public $InclureLienAjoutFav = 1 ;
			public $InclureLienVersionImpr = 1 ;
			public $InclureLienRecommender = 1 ;
			public $LienAjoutFav ;
			public $LienVersionImpr ;
			public $LienRecommender ;
			public $InclureRenduIcone = 1 ;
			public $LargeurIcone = 16 ;
			public $HauteurIcone = 16 ;
			public $SepIconeLibelle = '<span style="text-decoration:none;">&nbsp;&nbsp;</span>' ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeLiensAuto() ;
			}
			protected function ChargeLiensAuto()
			{
				if(! $this->InclureLiensAuto)
					return ;
				if($this->InclureLienAjoutFav)
				{
					$this->LienAjoutFav = new LienAjoutFavArtSws() ;
					$this->InscritLien($this->LienAjoutFav) ;
				}
				if($this->InclureLienVersionImpr)
				{
					$this->LienVersionImpr = new LienVersionImprArtSws() ;
					$this->InscritLien($this->LienVersionImpr) ;
				}
				if($this->InclureLienRecommender)
				{
					$this->LienRecommender = new LienRecommenderArtSws() ;
					$this->InscritLien($this->LienRecommender) ;
				}
			}
			public function InscritLien(& $lien)
			{
				$this->Liens[] = & $lien ;
			}
			public function InscritNouvLien($lien)
			{
				$this->InscritLien($lien) ;
			}
			public function InscritLienPos(& $lien, $index)
			{
				if($index <= -1)
				{
					$index = 0 ;
				}
				elseif($index >= count($this->Liens))
				{
					$index = count($this->Liens) - 1 ;
				}
				if(count($this->Liens) == 0)
				{
					$this->InscritLien($lien) ;
				}
				else
				{
					array_splice($this->Liens, $index, 0, array(& $lien)) ;
				}
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'"' ;
				if($this->NomClsCSS != "")
				{
					$ctn .= ' class="'.$this->NomClsCSS.'"' ;
				}
				$ctn .= '>' ;
				$ctnLiens = '' ;
				if(count($this->Liens) > 0)
				{
					foreach($this->Liens as $i => & $lien)
					{
						if($lien->EstVisible() == 0)
						{
							continue ;
						}
						if($ctnLiens != '')
						{
							$ctnLiens .= $this->SepLiens ;
						}
						$ctnLiens .= $lien->Applique($this->ScriptParent, $this, $this->DonneesSupport) ;
					}
				}
				$ctn .= $ctnLiens ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class DonneesContenuArtSws
		{
			public $LegendeImage ;
			public $CheminImage ;
			public $AuteurImage ;
			public $Description ;
			public $Sommaire ;
			public function Importe($donnees, & $composant)
			{
				if(isset($donnees[$composant->NomColAuteurImage]))
				{
					$this->AuteurImage = $donnees[$composant->NomColAuteurImage] ;
				}
				if(isset($donnees[$composant->NomColCheminImage]))
				{
					$this->CheminImage = $donnees[$composant->NomColCheminImage] ;
				}
				if(isset($donnees[$composant->NomColLegendeImage]))
				{
					$this->LegendeImage = $donnees[$composant->NomColLegendeImage] ;
				}
				if(isset($donnees[$composant->NomColSommaire]))
				{
					$this->Sommaire = $donnees[$composant->NomColSommaire] ;
				}
				if(isset($donnees[$composant->NomColDescription]))
				{
					$this->Description = $donnees[$composant->NomColDescription] ;
				}
			}
		}
		class BlocContenuArtSws extends PvComposantIUBase
		{
			public $DonneesSupport = array() ;
			public $NomClsCSS = "contenu" ;
			public $NomClsCSSSommaire = "sommaire" ;
			public $InclureRenduImage = 1 ;
			public $InclureRenduSommaire = 1 ;
			public $DonneesBrutes ;
			public $NomColAuteurImage = "auteur_image" ;
			public $NomColLegendeImage = "legende_image" ;
			public $NomColCheminImage = "chemin_image" ;
			public $NomColDescription = "description" ;
			public $NomColSommaire = "sommaire" ;
			public $NomClsCSSImage ;
			public $AlignImage = "left" ;
			public $MargeImageDesc = "8px" ;
			public $LargeurImage = "200" ;
			public $HauteurImage = "" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DonneesBrutes = new DonneesContenuArtSws() ;
			}
			protected function RenduDispositifBrut()
			{
				$this->DonneesBrutes->Importe($this->DonneesSupport, $this) ;
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'"' ;
				if($this->NomClsCSS != '')
				{
					$ctn .= ' class="'.$this->NomClsCSS.'"' ;
				}
				$ctn .= '>' ;
				if($this->InclureRenduSommaire && $this->DonneesBrutes->Sommaire != '')
				{
					$ctn .= '<div class="'.$this->NomClsCSSSommaire.'">'.htmlentities($this->DonneesBrutes->Sommaire).'</div>' ;
				}
				if($this->InclureRenduImage && $this->DonneesBrutes->CheminImage != "")
				{
					$ctn .= '<span' ;
					if($this->NomClsCSSImage != "")
						$ctn .= ' class="'.$this->NomClsCSSImage.'"' ;
					$ctn .= ' style="' ;
					switch(strtolower($this->AlignImage))
					{
						case "left" :
						{
							$ctn .= 'float:left; padding-right:'.$this->MargeImageDesc.'; padding-bottom:'.$this->MargeImageDesc ;
						}
						break ;
						case "right" :
						{
							$ctn .= 'float:right; padding-left:'.$this->MargeImageDesc.'; padding-bottom:'.$this->MargeImageDesc ;
						}
						break ;
					}
					$ctn .= '">' ;
					$ctn .= '<img src="'.htmlentities($this->DonneesBrutes->CheminImage).'"' ;
					if($this->LargeurImage != "")
						$ctn .= ' width="'.$this->LargeurImage.'"' ;
					if($this->HauteurImage != "")
						$ctn .= ' height="'.$this->HauteurImage.'"' ;
					if($this->DonneesBrutes->LegendeImage != "")
						$ctn .= ' title="'.htmlentities($this->DonneesBrutes->LegendeImage).'"' ;
					$ctn .= ' />' ;
					$ctn .= '</span>' ;
				}
				$ctn .= HTMLTag::ExtractSafeContent($this->DonneesBrutes->Description) ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class GrilleSousRubrSws extends PvGrilleDonneesHtml
		{
			public $ToujoursAfficher = 1 ;
			public $AlerterAucunElement = 0 ;
			public $CacherNavigateurRangeesAuto = 1 ;
			public $CacherFormulaireFiltres = 1 ;
			public $DefColId ;
			public $DefColTitre ;
			public $DefColSommaire ;
			public $ExtracteursIntroPage = array() ;
			public $FltIdConteneur ;
			public $MaxColonnes = 2 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeDefColsAuto() ;
				$this->ChargeFournDonnees() ;
				$this->ChargeModeleContenu() ;
			}
			protected function ChargeFournDonnees()
			{
				$fourn = $this->ScriptParent->CreeFournDonnees() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$this->FournisseurDonnees = $fourn ;
				$this->FournisseurDonnees->RequeteSelection = $entite->NomTable ;
				$this->FltIdConteneur = $this->InsereFltSelectHttpGet($entite->NomParamId, $fourn->BaseDonnees->EscapeVariableName($entite->NomColIdConteneur).'=<self>') ;
				$this->FltIdConteneur->Obligatoire = 1 ;
				$this->FltIdConteneur->LectureSeule = 1 ;
			}
			protected function ChargeDefColsAuto()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$this->DefColId = $this->InsereDefCol($entite->NomColId) ;
				$this->DefColTitre = $this->InsereDefCol($entite->NomColTitre) ;
				$this->DefColSommaire = $this->InsereDefCol($entite->NomColSommaire) ;
				foreach($this->DefinitionsColonnes as $i => & $col)
				{
					if(in_array($col->NomDonnees, array($entite->NomColTitre, $entite->NomColSommaire)))
					{
						$col->ExtracteurValeur = new PvExtracteurIntroDonnees() ;
						$this->ExtracteursIntroPage[$col->NomDonnees] = & $col->ExtracteurValeur ;
					}
				}
			}
			protected function ChargeModeleContenu()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$this->ContenuLigneModele = '<a href="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($entite->NomScriptConsult.'_'.$entite->NomEntite).'&'.urlencode($entite->NomParamId).'=${'.$entite->NomColId.'}">${'.$entite->NomColTitre.'_html}</a>' ;
			}
		}
		class GrilleArtsRubrSws extends PvGrilleDonneesHtml
		{
			public $ToujoursAfficher = 1 ;
			public $AlerterAucunElement = 0 ;
			public $CacherNavigateurRangeesAuto = 1 ;
			public $CacherFormulaireFiltres = 1 ;
			public $DefColId ;
			public $DefColTitre ;
			public $DefColSommaire ;
			public $ExtracteursIntroPage = array() ;
			public $FltIdRubr ;
			public $MaxColonnes = 1 ;
			public $SymboleElement = '&bull; ' ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeDefColsAuto() ;
				$this->ChargeFournDonnees() ;
				$this->ChargeModeleContenu() ;
			}
			protected function ChargeFournDonnees()
			{
				$fourn = $this->ScriptParent->CreeFournDonnees() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$this->FournisseurDonnees = $fourn ;
				$this->FournisseurDonnees->RequeteSelection = $entite->ModuleParent->EntiteArticle->NomTable ;
				$this->FltIdConteneur = $this->InsereFltSelectHttpGet($entite->NomParamId, $fourn->BaseDonnees->EscapeVariableName($entite->ModuleParent->EntiteArticle->NomColIdRubr).'=<self>') ;
				$this->FltIdConteneur->Obligatoire = 1 ;
				$this->FltIdConteneur->LectureSeule = 1 ;
			}
			protected function ChargeDefColsAuto()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$this->DefColId = $this->InsereDefCol($entite->ModuleParent->EntiteArticle->NomColId) ;
				$this->DefColTitre = $this->InsereDefCol($entite->ModuleParent->EntiteArticle->NomColTitre) ;
				$this->DefColSommaire = $this->InsereDefCol($entite->ModuleParent->EntiteArticle->NomColSommaire) ;
				foreach($this->DefinitionsColonnes as $i => & $col)
				{
					if(in_array($col->NomDonnees, array($entite->ModuleParent->EntiteArticle->NomColTitre, $entite->ModuleParent->EntiteArticle->NomColSommaire)))
					{
						$col->ExtracteurValeur = new PvExtracteurIntroDonnees() ;
						$this->ExtracteursIntroPage[$col->NomDonnees] = & $col->ExtracteurValeur ;
					}
				}
			}
			protected function ChargeModeleContenu()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$this->ContenuLigneModele = '<div class="titre_article">'.$this->SymboleElement.'<a href="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($entite->ModuleParent->EntiteArticle->NomScriptConsult.'_'.$entite->ModuleParent->EntiteArticle->NomEntite).'&'.urlencode($entite->NomParamId).'=${'.$entite->ModuleParent->EntiteArticle->NomColId.'}">${titre_html}</a></div>
				<div class="description_article"><a href="?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($entite->ModuleParent->EntiteArticle->NomScriptConsult.'_'.$entite->ModuleParent->EntiteArticle->NomEntite).'&'.urlencode($entite->ModuleParent->EntiteArticle->NomParamId).'=${'.$entite->ModuleParent->EntiteArticle->NomColId.'}">${sommaire_html}</a></div>' ;
			}
		}
	}
	
?>