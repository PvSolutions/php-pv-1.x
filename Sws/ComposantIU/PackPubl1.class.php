<?php
	
	if(! defined('COMP_PACK_PUBL_1_SWS'))
	{
		define('COMP_PACK_PUBL_1_SWS', 1) ;
		
		class CompArtEvidencePublSws extends PvPortionRenduHtml
		{
			public $NomClasseCSS="float-left" ;
			public $IdArt = 1 ;
			public $MaxMots = 325 ;
			public $MsgAucunArt = "L'article n'a pas encore &eacute;t&eacute; publi&eacute;" ;
			public $Titre = 'QUI SOMMES-NOUS' ;
			public $LirePlus = 'Lire &gt;&gt;' ;
			protected function RenduDispositifBrut()
			{
				$systemeSws = & ReferentielSws::$SystemeEnCours ;
				$bd = $systemeSws->ObtientBDSupport() ;
				$entiteArt = $systemeSws->ModuleArticle->EntiteArticle ;
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSS.'">'.PHP_EOL ;
				$lgn = $bd->FetchSqlRow('select * from '.$bd->EscapeTableName($entiteArt->NomTable).' where '.$bd->EscapeVariableName($entiteArt->NomColId).'='.$bd->ParamPrefix.'id', array('id' => $this->IdArt)) ;
				if(count($lgn) > 0)
				{
					$nomScriptConsult = $entiteArt->NomScriptConsult.'_'.$entiteArt->NomEntite ;
					$ctn .= '<h3>'.htmlentities(strtoupper($this->Titre)).'</h3>'.PHP_EOL ;
					$ctn .= '<div style="float:left; padding-right:4px; padding-bottom:4px"><img src="'.htmlentities($systemeSws->ObtientCheminPubl($lgn[$entiteArt->NomColCheminImage])).'" /></div>'.PHP_EOL ;
					$ctn .= '<p align="justify">'.intro(HTMLTag::ExtractSafeContent($lgn[$entiteArt->NomColDescription]), $this->MaxMots).'</p>'.PHP_EOL ;
					$ctn .= '<p align="right"><a href="?appelleScript='.$nomScriptConsult.'&id='.$this->IdArt.'">'.$this->LirePlus.'</a></p>' ;
				}
				else
				{
					$ctn .= $this->MsgAucunArt. PHP_EOL ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class RtShowcaseSliderSws extends PvPortionRenduHtml
		{
			protected static $SourceRtShowcase = 0 ;
			public $IdSlider = 1 ;
			public $MsgAucunSlider = 'Aucun slider trouvé...' ;
			protected function RenduIncSourceRtShowcase()
			{
				if(RtShowcaseSliderSws::$SourceRtShowcase == 1)
					return '' ;
				$ctn = '' ;
				$ctn .= '<link rel="stylesheet" href="./engine1/style.css">
<!--[if IE]><link rel="stylesheet" href="./engine1/ie.css"><![endif]-->
<!--[if lte IE 9]><script type="text/javascript" src="./engine1/ie.js"></script><![endif]-->'.PHP_EOL ;
				RtShowcaseSliderSws::$SourceRtShowcase = 1 ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$bd = & $this->ApplicationParent->BDPrinc ;
				$systemeSws = ReferentielSws::$SystemeEnCours ;
				$ctn = '' ;
				$ctn .= $this->RenduIncSourceRtShowcase() ;
				$sql = 'select * from elem_slider where id_slider='.$bd->ParamPrefix.'idSlider order by date_publication desc, heure_publication desc limit 0, 4' ;
				$lgns = $bd->FetchSqlRows($sql, array('idSlider' => $this->IdSlider)) ;
				if(count($lgns) > 0)
				{
					$ctncsslider1 = '' ;
					$ctnnum0img = '' ;
					$ctnnum0label = '' ;
					$ctncs = '' ;
					$ctncsbullet = '' ;
					$premBanniere = '' ;
					foreach($lgns as $i => $lgn)
					{
						if($premBanniere == '')
							$premBanniere = $lgn["chemin_image"] ;
						$ctncsslider1 .= '<input name="cs_anchor1" autocomplete="off" id="cs_slide1_'.$i.'" type="radio" class="cs_anchor slide" >'.PHP_EOL ;
						$ctncs .= '<label class="num0" for="cs_slide1_'.$i.'"></label>'.PHP_EOL ;
						$ctncsbullet .= '<label class="num'.$i.'" for="cs_slide1_'.$i.'">
<span class="cs_point"></span>
<span class="cs_thumb"><img src="'.htmlentities($systemeSws->ObtientCheminPubl($lgn["chemin_image"])).'" width="60" alt="'.htmlentities($lgn["titre"]).'" title="'.htmlentities($lgn["titre"]).'" /></span>
</label>'.PHP_EOL ;
						$ctnnum0img .= '<li class="num'.$i.' img"><img src="'.htmlentities($systemeSws->ObtientCheminPubl($lgn["chemin_image"])).'" width="60" alt="'.htmlentities($lgn["titre"]).'" title="'.htmlentities($lgn["titre"]).'" /></li>'.PHP_EOL ;
						$ctnnum0label .= '<label class="num'.$i.'">
<span class="cs_title"><span class="cs_wrapper">'.htmlentities($lgn["titre"]).'</span></span>
<br/><span class="cs_descr"><span class="cs_wrapper">'.htmlentities($lgn["description"]).'</span></span>
</label>'.PHP_EOL ;
					}
					$ctn .= '<section id="rt-showcase-surround">
<div id="rt-showcase" class="slider-container rt-overlay-dark">
<div class="rt-container slider-container">
<div class="rt-grid-12 rt-alpha rt-omega">
<div class="csslider1 autoplay">'.$ctncsslider1.'
<input name="cs_anchor1" autocomplete="off" id="cs_play1" type="radio" class="cs_anchor" checked>
<input name="cs_anchor1" autocomplete="off" id="cs_pause1" type="radio" class="cs_anchor" >
<ul>
<div style="width: 100%; visibility: hidden; font-size: 0px; line-height: 0;">
<img src="'.htmlentities($systemeSws->ObtientCheminPubl($premBanniere)).'" style="width: 100%;">
</div>
'.$ctnnum0img.'
</ul>
<div class="cs_description">
'.$ctnnum0label.'
</div>
<div class="cs_arrowprev">
'.$ctncs.'
</div>
<div class="cs_arrownext">
'.$ctncs.'
</div>
<div class="cs_bullets">
'.$ctncsbullet.'
</div>
</div>
</div>
<div class="clear"></div>
</div>
</div>
</section>
'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<div>'.$this->MsgAucunSlider.'</div>'.PHP_EOL ;
				}
				return $ctn ;
			}
		}
		
		class CompEnumPagesPublSws extends PvPortionRenduHtml
		{
			public $NomClasseCSS="float-left" ;
			public $IdRubr = 4 ;
			public $StyleCSS = "margin-left:12px" ;
			public $MaxMots = 325 ;
			public $MsgAucunePage = "Aucune page n'est presente dans cette rubrique" ;
			public $Titre = '' ;
			public $TitreLienLirePlus = 'Plus &gt;&gt;' ;
			public $AlignLienLirePlus = 'right' ;
			protected function ObtientSqlSupport()
			{
				$bd = & $this->ApplicationParent->BDPrinc ;
				$sql = 'select * from article where id_rubrique='.$bd->ParamPrefix.'idRubr order by date_publication asc, heure_publication asc' ;
				return $sql ;
			}
			protected function RenduDispositifBrut()
			{
				$bd = & $this->ApplicationParent->BDPrinc ;
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSS.'" style="'.$this->StyleCSS.'">'.PHP_EOL ;
				$lgns = $bd->FetchSqlRows($this->ObtientSqlSupport(), array('idRubr' => $this->IdRubr)) ;
				if(count($lgns) > 0)
				{
					$ctn .= '<h3>'.htmlentities(strtoupper($this->Titre)).'</h3>'.PHP_EOL ;
					$ctn .= '<ul>'.PHP_EOL ;
					foreach($lgns as $i => $lgn)
					{
						$ctn .= '<li><a href="?appelleScript=consult_article&id='.$lgn["id"].'">'.htmlentities($lgn["titre"]).'</a></li>'.PHP_EOL ;
					}
					$ctn .= '</ul>' ;
					$ctn .= '<div class="lien-lire-plus" align="'.$this->AlignLienLirePlus.'"><a href="?appelleScript=consult_rubrique&id='.$this->IdRubr.'">'.$this->TitreLienLirePlus.'</a></div>' ;
				}
				else
				{
					$ctn .= $this->MsgAucunePage. PHP_EOL ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class CompGrdMenusPagesPublSws extends PvPortionRenduHtml
		{
			public $NomClasseCSSElement="" ;
			public $NomClasseCSS="specialites" ;
			public $IdGroupe = 4 ;
			public $MaxColonnes = 0 ;
			public $NomRelPage = 'gd_menus_accueil' ;
			public $TitreLienVoir = 'Voir' ;
			public $MsgAucunePage = 'Aucune page n\'a ete trouvee' ;
			protected function ObtientSqlSupport()
			{
				$bd = & $this->ApplicationParent->BDPrinc ;
				$sql = 'select t1.* from menu t1 where id_groupe='.$bd->ParamPrefix.'idGroupe and statut_publication=1' ;
				if($this->MaxColonnes > 0)
				{
					$sql = $bd->LimitSqlRowsReq($sql, array(), 0, $this->MaxColonnes) ;
				}
				return $sql ;
			}
			public function RenduDispositifBrut()
			{
				$ctn = '' ;
				$systemeSws = & ReferentielSws::$SystemeEnCours ;
				$bd = & $this->ApplicationParent->BDPrinc ;
				$sql = $this->ObtientSqlSupport() ;
				$lgns = $bd->FetchSqlRows($sql, array('idGroupe' => $this->IdGroupe)) ;
				$ctn .= '<table width="100%" id="'.$this->IDInstanceCalc.'" cellspacing="0" cellpadding="0" class="'.$this->NomClasseCSS.'">'.PHP_EOL ;
				if(is_array($lgns))
				{
					if(count($lgns) > 0)
					{
						$pourcentage = intval(100 / count($lgns)) - 6 ;
						$ctn .= '<tr>'.PHP_EOL ;
						foreach($lgns as $i => $lgn)
						{
							if($i > 0)
							{
								$ctn .= '<td>&nbsp;</td>'.PHP_EOL ;
							}
							$ctn .= '<td width="'.$pourcentage.'%" class="'.$this->NomClasseCSSElement.'" valign="bottom">'.PHP_EOL ;
							$ctn .= '<h3>'.htmlentities(strtoupper($lgn["titre"])).'</h3>'.PHP_EOL ;
							$ctn .= '<div><a href="'.htmlentities($lgn["url"]).'"><img src="'.htmlentities($systemeSws->ObtientCheminPubl($lgn["chemin_image"])).'" border="0" /></a></div>'.PHP_EOL ;
							$ctn .= '<div><a href="'.htmlentities($lgn["url"]).'">'.$this->TitreLienVoir.'</a></div>'.PHP_EOL ;
							$ctn .= '</td>'.PHP_EOL ;
						}
					}
					else
					{
						$ctn .= '<td>'.$this->MsgAucunePage.'</td>'.PHP_EOL ;
					}
					$ctn .= '</tr>'.PHP_EOL ;
				}
				else
				{
					$ctn .= '<tr><td>'.htmlentities($bd->LastSqlText.' : '.$bd->ConnectionException).'</td></tr>' ;
				}
				$ctn .= '</table>'.PHP_EOL ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('
jQuery(\'#'.$this->IDInstanceCalc.' td\').hover(function() {
	jQuery(this).find(\'img\').fadeTo(200, 0.6) ;
}, function (){
	jQuery(this).find(\'img\').fadeTo(200, 1) ;
}) ;') ;
				return $ctn ;
			}
		}
	}
	
?>