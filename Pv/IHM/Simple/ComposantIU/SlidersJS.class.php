<?php
	
	if(! defined('PV_SLIDER_JS'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_IU_BASE'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_SLIDER_JS', 1) ;
		
		class PvCfgInitJQueryCamera
		{
			public $alignment = 'center' ;
			public $autoAdvance = true ;
			public $mobileAutoAdvance = true ;
			public $barDirection = 'leftToRight' ;
			public $barPosition = 'bottom' ;
			public $cols = 6 ;
			public $easing = 'easeInOutExpo' ;
			public $mobileEasing = '' ;
			public $fx = 'random' ;
			public $mobileFx = '' ;
			public $gridDifference = 250 ;
			public $height = '50%' ;
			public $hover = true ;
			public $imagePath = "images/" ;
			public $loader = 'pie' ;
			public $loaderColor = '#eeeeee' ;
			public $loaderBgColor = '#222222' ;
			public $loaderOpacity = 0.8 ;
			public $loaderPadding = 2 ;
			public $loaderStroke = 7 ;
			public $minHeight = '' ;
			public $navigation = true ;
			public $navigationHover = true ;
			public $mobileNavHover = true ;
			public $opacityOnGrid = false ;
			public $pagination = true ;
			public $playPause = true ;
			public $pauseOnClick = true ;
			public $pieDiameter = 58 ;
			public $piePosition = "rightTop" ;
			public $portrait = false ;
			public $rows = 4 ;
			public $slicedCols = 12 ;
			public $slicedRows = 8 ;
			public $slideOn = 'random' ;
			public $thumbnails = false ;
			public $time = 7000 ;
			public $transPeriod = 1500 ;
		}
		
		class PvJQueryCamera extends PvComposantJsFiltrable
		{
			public $CheminJs = "js/camera.min.js" ;
			public $CheminCSS = "css/camera.css" ;
			public $FormatCheminImage = '' ;
			public $NomColCaption = '' ;
			public $FormatUrl = '' ;
			public $ElementsEnCours = array() ;
			protected function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduInscritContenuCSS($this->CheminCSS) ;
				$ctn .= $this->RenduInscritLienJs($this->CheminJs) ;
				return $ctn ;
			}
			protected function RenduDispositifBrutSpec()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				$lgns = $this->FournisseurDonnees->SelectElements(array(), $this->ObtientFiltresSelection()) ;
				foreach($lgns as $i => $lgn)
				{
					$valeurs = $this->ExtraitValeursLgnDonnees($lgn) ;
					$cheminImage = _parse_pattern($this->FormatCheminImage, $valeurs) ;
					$url = _parse_pattern($this->FormatUrl, $valeurs) ;
					$caption = (isset($valeurs[$this->NomColCaption])) ? $valeurs[$this->NomColCaption] : '' ;
					$ctn .= '<div data-src="'.$cheminImage.'">'.PHP_EOL ;
					if($caption != '')
					{
						$ctn .= '<div class="camera_caption">'.PHP_EOL ;
						$ctn .= $caption.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
	}
	
?>