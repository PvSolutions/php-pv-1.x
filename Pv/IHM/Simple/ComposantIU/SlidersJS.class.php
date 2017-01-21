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
			public $CheminJQueryEasing = "js/jquery.easing.js" ;
			public $InclureJQueryEasing = 1 ;
			public $FormatCheminImage = '' ;
			public $NomColCaption = '' ;
			public $FormatUrl = '' ;
			public $ElementsEnCours = array() ;
			protected function CreeCfgInit()
			{
				return new PvCfgInitJQueryCamera() ;
			}
			protected function CtnJsInstall()
			{
				return 'jQuery("#'.$this->IDInstanceCalc.'").camera('.svc_json_encode($this->CfgInit).') ;' ;
			}
			protected function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduInscritLienCSS($this->CheminCSS) ;
				if($this->InclureJQueryEasing == 1)
				{
					$ctn .= $this->RenduInscritLienJs($this->CheminJQueryEasing) ;
				}
				$ctn .= $this->RenduInscritLienJs($this->CheminJs) ;
				$ctn .= $this->RenduInscritContenuJs('jQuery(function() {
'.$this->CtnJsInstall().'
}) ;') ;
				return $ctn ;
			}
			protected function RenduDispositifBrutSpec()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				$lgns = $this->FournisseurDonnees->SelectElements(array(), $this->ObtientFiltresSelection()) ;
				$this->ElementsEnCours = array() ;
				foreach($lgns as $i => $lgn)
				{
					$valeurs = $this->ExtraitValeursLgnDonnees($lgn) ;
					$this->ElementsEnCours[] = $valeurs ;
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
		
		class PvJqMenuCamera extends PvJQueryCamera
		{
			public $NomColLibMenu = "" ;
			public $CouleurArrMenu = "red" ;
			public $CouleurTxtMenu = "white" ;
			public $TailleTxtMenu = "16px" ;
			public $Largeur = "100%" ;
			public $PourcentLargeurMenu = "30" ;
			protected function CtnJsInstall()
			{
				$ctn = 'var cfgInit = '.svc_json_encode($this->CfgInit).' ;
var menu'.$this->IDInstanceCalc.' = jQuery("#Conteneur_'.$this->IDInstanceCalc.'") ;
var slide'.$this->IDInstanceCalc.' = jQuery("#'.$this->IDInstanceCalc.'") ;
cfgInit.onEndTransition = function(){
	var ind = slide'.$this->IDInstanceCalc.'.find(".camera_target .cameraSlide.cameranext").index();
	menu'.$this->IDInstanceCalc.'.find(".menu_item").hide().each(function(index) {
		if(index == ind - 1)  {
			jQuery(this).show() ;
		}
	}) ;
} ;
slide'.$this->IDInstanceCalc.'.camera(cfgInit) ;' ;
				return $ctn ;
			}
			protected function RenduMenu()
			{
				$ctn = '' ;
				$ctn .= '<div class="menu" style="background:'.$this->CouleurArrMenu.'">'.PHP_EOL ;
				foreach($this->ElementsEnCours as $i => $elem)
				{
					$ctnMenu = ($this->NomColLibMenu != "" && isset($elem[$this->NomColLibMenu])) ? $elem[$this->NomColLibMenu] : "Slide ".($i + 1) ;
					$ctn .= '<div class="menu_item" style="display:none; font-size:'.$this->TailleTxtMenu.'; font-weight:bold; color:'.$this->CouleurTxtMenu.'">'.PHP_EOL ;
					$ctn .= $ctnMenu.PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function RenduDispositifBrutSpec()
			{
				$ctn = '' ;
				$ctnCamera = parent::RenduDispositifBrutSpec() ;
				$ctn .= '<table id="Conteneur_'.$this->IDInstanceCalc.'" width="'.$this->Largeur.'" cellspacing="0" cellpadding="0">
<tr>
<td width="'.$this->LargeurMenu.'" bgcolor="'.$this->CouleurArrMenu.'" style="padding:8px">'.PHP_EOL ;
				$ctn .= $this->RenduMenu().PHP_EOL ;
				$ctn .= '</td>
<td width="'.(100 - $this->PourcentLargeurMenu).'%">'.PHP_EOL ;
				$ctn .= $ctnCamera.PHP_EOL ;
				$ctn .= '</td>
</tr>
</table>' ;
				return $ctn ;
			}
		}
	}
	
?>