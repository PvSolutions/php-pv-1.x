<?php

	if(! defined('PV_SLIDE_BOOTSTRAP'))
	{
		define('PV_SLIDE_BOOTSTRAP', 1) ;
		
		class PvLienBlueimpGallery
		{
			public $title ;
			public $href ;
			public $type ;
			public $poster ;
		}
		class PvOptsInstBlueimpGallery
		{
			public $container ;
			public $carousel = false ;
			public $index = null ;
			public $event = null ;
		}
		
		class PvBlueimpGalleryBase extends PvComposantJsFiltrable
		{
			public $CheminFichierGlobalCSS = "css/blueimp-gallery.min.css" ;
			public $CheminFichierIndicatorCSS = "css/blueimp-gallery-indicator.css" ;
			public $CheminFichierVideoCSS = "css/blueimp-gallery-video.css" ;
			public $CheminFichierBaseJs = "js/blueimp-gallery.min.js" ;
			public $CheminFichierFullscreenJs = "js/blueimp-gallery-fullscreen.js" ;
			public $CheminFichierIndicatorJs = "js/blueimp-gallery-indicator.js" ;
			public $CheminFichierVideoJs = "js/blueimp-gallery-video.js" ;
			public $CheminFichierVimeoJs = "js/blueimp-gallery-vimeo.js" ;
			public $CheminFichierYoutubeJs = "js/blueimp-gallery-youtube.js" ;
			public $CheminFichierHelperJs = "js/blueimp-gallery-helper.js" ;
			public $NomColonneCheminFichier = "chemin_fichier" ;
			public $NomColonneTitre = "titre" ;
			public $NomColonneTypeFichier = "" ;
			public $TypeFichierParDefaut = "" ;
			public $NomColonneCheminMiniature = "" ;
			public $OptsInst = null ;
			public $LiensInst = array() ;
			public $MessageAucunElement = "Aucun &eacute;l&eacute;ment n'a &eacute;t&eacute; trouv&eacute;" ;
			public $MessageMauvaiseCfg = "Mauvaise configuration du composant : Colonne des chemins fichiers inexistante" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->OptsInst = new PvOptsInstBlueimpGallery() ;
			}
			protected function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduLienCSS($this->CheminFichierGlobalCSS) ;
				$ctn .= $this->RenduLienCSS($this->CheminFichierIndicatorCSS) ;
				$ctn .= $this->RenduLienCSS($this->CheminFichierVideoCSS) ;
				$ctn .= $this->RenduLienJs($this->CheminFichierBaseJs) ;
				// $ctn .= $this->RenduLienJs($this->CheminFichierFullscreenJs) ;
				// $ctn .= $this->RenduLienJs($this->CheminFichierIndicatorJs) ;
				// $ctn .= $this->RenduLienJs($this->CheminFichierVideoJs) ;
				// $ctn .= $this->RenduLienJs($this->CheminFichierYoutubeJs) ;
				// $ctn .= $this->RenduLienJs($this->CheminFichierVimeoJs) ;
				$ctn .= '<div id="blueimp-gallery" class="blueimp-gallery">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>' ;
				return $ctn ;
			}
			protected function RenduBlocErreur($msg)
			{
				return '<div class="Erreur text-error">'.$msg.'</div>' ;
			}
			protected function RenduGallery(& $lgns)
			{
			}
			protected function RenduDispositifBrutSpec()
			{
				$ctn = '' ;
				if($this->EstNul($this->FournisseurDonnees))
				{
					return $this->RenduBlocErreur("Aucun fournisseur de donn&eacute;e n'a &eacute;t&eacute; d&eacute;fini") ;
				}
				$lgns = $this->FournisseurDonnees->SelectElements(array(), $this->ObtientFiltresSelection()) ;
				if(is_array($lgns))
				{
					if(count($lgns) > 0)
					{
						$ctn .= $this->RenduGallery($lgns) ;
					}
					else
					{
						$ctn .= $this->RenduBlocErreur($this->MessageAucunElement) ;
					}
				}
				else
				{
					$ctn .= $this->RenduBlocErreur(htmlentities($this->FournisseurDonnees->DerniereException->Texte)) ;
				}
				return $ctn ;
			}
		}
		
		class PvBlueimpLightbox extends PvBlueimpGalleryBase
		{
			protected function RenduGallery(& $lgns)
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				foreach($lgns as $i => $lgn)
				{
					if(! isset($lgn[$this->NomColonneCheminFichier]))
					{
						$ctn .= $this->RenduBlocErreur($this->MessageMauvaiseCfg) ;
						return $ctn ;
					}
					$lien = new PvLienBlueimpGallery() ;
					$lien->href = $lgn[$this->NomColonneCheminFichier] ;
					$lien->title = (isset($lgn[$this->NomColonneTitre])) ? $lgn[$this->NomColonneTitre] : '' ;
					$lien->type = (isset($lgn[$this->NomColonneTypeFichier])) ? $lgn[$this->NomColonneTypeFichier] : '' ;
					$lien->poster = (isset($lgn[$this->NomColonneCheminMiniature])) ? $lgn[$this->NomColonneCheminMiniature] : '' ;
					$this->LiensInst[] = $lien ;
					$ctn .= '<a href="'.htmlspecialchars($lien->href).'" title="'.htmlspecialchars($lien->title).'">
<img src="'.htmlspecialchars($lien->poster).'" alt="'.htmlspecialchars($lien->title).'">
</a>'.PHP_EOL ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctnJs = 'jQuery(function() {
document.getElementById("'.$this->IDInstanceCalc.'").onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement ;
var link = target.src ? target.parentNode : target ;
var options = '.svc_json_encode($this->OptsInst).' ;'.PHP_EOL ;
				$ctnJs .= 'options.index = link ;'.PHP_EOL  ;
				$ctnJs .= 'options.event = event ;'.PHP_EOL  ;
				$ctnJs .= 'options.container = document.getElementById("blueimp-gallery") ;'.PHP_EOL  ;
				$ctnJs .= 'var links = this.getElementsByTagName("a");
blueimp.Gallery(links, options);
} ;
}) ;' ;
				$ctn .= $this->RenduContenuJs($ctnJs) ;
				return $ctn ;
			}
		}
		class PvBlueimpBannerBox extends PvBlueimpLightbox
		{
			public $HauteurBanniere = 270 ;
			public $MargeHautBanniere = 30 ;
			public $MargeGaucheBanniere = 10 ;
			protected function RenduGallery(& $lgns)
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				foreach($lgns as $i => $lgn)
				{
					if(! isset($lgn[$this->NomColonneCheminFichier]))
					{
						$ctn .= $this->RenduBlocErreur($this->MessageMauvaiseCfg) ;
						return $ctn ;
					}
					$lien = new PvLienBlueimpGallery() ;
					$lien->href = $lgn[$this->NomColonneCheminFichier] ;
					$lien->title = (isset($lgn[$this->NomColonneTitre])) ? $lgn[$this->NomColonneTitre] : '' ;
					$lien->type = (isset($lgn[$this->NomColonneTypeFichier])) ? $lgn[$this->NomColonneTypeFichier] : '' ;
					$lien->poster = (isset($lgn[$this->NomColonneCheminMiniature])) ? $lgn[$this->NomColonneCheminMiniature] : '' ;
					$this->LiensInst[] = $lien ;
					$ctn .= '<div class="'.(($i > 0) ? 'invisible' : 'banniere').'">
<a href="'.htmlspecialchars($lien->href).'" title="'.htmlspecialchars($lien->title).'">
<img src="'.htmlspecialchars($lien->poster).'" alt="'.htmlspecialchars($lien->title).'">
</a>
</div>'.PHP_EOL ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctnJs = 'jQuery(function() {
document.getElementById("'.$this->IDInstanceCalc.'").onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement ;
var link = target.src ? target.parentNode : target ;
var options = '.svc_json_encode($this->OptsInst).' ;'.PHP_EOL ;
				$ctnJs .= 'options.index = link ;'.PHP_EOL  ;
				$ctnJs .= 'options.event = event ;'.PHP_EOL  ;
				$ctnJs .= 'options.container = document.getElementById("blueimp-gallery") ;'.PHP_EOL  ;
				$ctnJs .= 'var links = this.getElementsByTagName("a");
blueimp.Gallery(links, options);
} ;
// Afficher la banniere
var jqContainer = jQuery("#'.$this->IDInstanceCalc.'") ;
var largeurMargeGauche = parseInt(jqContainer.innerWidth() * '.$this->MargeGaucheBanniere.' / 100) ;
jqContainer.find(".banniere").css({
	height : '.$this->HauteurBanniere.' + "px",
	overflow : "hidden"
}) ;
jqContainer.find(".banniere").show() ;
jqContainer.find(".banniere").find("img").css({
	width : (jqContainer.innerWidth() + largeurMargeGauche) + "px",
	marginTop : "-" + parseInt('.$this->MargeHautBanniere.' * jqContainer.find(".banniere").find("img").innerHeight() / 100) + "px",
	marginLeft : "-" + parseInt('.$this->MargeGaucheBanniere.' * jqContainer.width() / 100) + "px"
}) ;
// alert() ;
// alert(jqContainer.find("a.banniere").length) ;
}) ;' ;
				$ctn .= $this->RenduContenuJs($ctnJs) ;
				$ctn .= $this->RenduContenuCSS('#'.$this->IDInstanceCalc.' .invisible, #'.$this->IDInstanceCalc.' .banniere {
display:none ;
}
#'.$this->IDInstanceCalc.' .banniere img {
width: 100%;
}') ;
				return $ctn ;
			}
		}
	}

?>