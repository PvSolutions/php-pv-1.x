<?php
	
	if(! defined('PV_COMPOSANT1_SITE_WEB'))
	{
		define('PV_COMPOSANT1_SITE_WEB', 1) ;
		
		class VisionneuseBaseSiteWeb extends PvBaliseHtmlBase
		{
		}
		
		class ConfigFlexPaperSiteWeb
		{
			public $Scale ;
			public $ZoomTransition = 'easeOut' ;
			public $ZoomTime = 0.5 ;
			public $ZoomInterval = 0.2 ;
			public $FitPageOnLoad = false ;
			public $FitWidthOnLoad = true ;
			public $PrintEnabled = true ;
			public $FullScreenAsMaxWindow = false ;
			public $ProgressiveLoading = false ;
			public $MinZoomSize = 0.2 ;
			public $MaxZoomSize = 5 ;
			public $SearchMatchAll = false ;
			public $InitViewMode = 'Portrait' ;
			public $ViewModeToolsVisible = true ;
			public $ZoomToolsVisible = true ;
			public $NavToolsVisible = true ;
			public $CursorToolsVisible = true ;
			public $SearchToolsVisible = true ;
			public $localeChain = 'fr_FR' ;
			public $SwfFile = '' ;
		}
		class FlexPaperSiteWeb extends VisionneuseBaseSiteWeb
		{
			public $Config = null ;
			public static $CheminSource = "js/flexpaper_flash.js" ;
			public $CheminDocSwf = null ;
			public $Largeur = "100%" ;
			public $Hauteur = "480px" ;
			public static $SourceInclus = 0 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Config = new ConfigFlexPaperSiteWeb() ;
			}
			protected function RenduDispositifBrut()
			{
				$this->Config->SwfFile = $this->CheminDocSwf ;
				if(! file_exists($this->CheminDocSwf) || is_dir($this->CheminDocSwf))
				{
					return '' ;
				}
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= $this->InclutSource() ;
				$ctn .= '<a id="'.$this->IDInstanceCalc.'" style="width:'.$this->Largeur.';height:'.$this->Hauteur.';display:block"></a>' ;
				$ctn .= '<script type="text/javascript"> 
var fp'.$this->IDInstanceCalc.' = new FlexPaperViewer(	
	"FlexPaperViewer",
	'.svc_json_encode($this->IDInstanceCalc).',
	{
		config : '.svc_json_encode($this->Config).'
	}
) ;
</script>' ;
				return $ctn ;	
			}
		}
		class MapGoogleSiteWeb extends VisionneuseBaseSiteWeb
		{
			public $Zoom = 14 ;
			public $NomSite = '' ; 
			public $NomRue = '' ;
			public $NomVille = '' ;
			public $Largeur = '600px' ;
			public $Hauteur = '500px' ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				$ctn .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script><div style="overflow:hidden;height:'.$this->Hauteur.';width:'.$this->Largeur.';"><div id="gmap_canvas" style="height:'.$this->Hauteur.';width:'.$this->Largeur.';"></div><style>#gmap_canvas img{max-width:none!important;background:none!important}</style><a class="google-map-code" href="http://www.mapsembed.com/goertz-gutschein/" id="get-map-data">http://www.mapsembed.com/goertz-gutschein/</a></div><script type="text/javascript"> function init_map(){var myOptions = {zoom:'.intval($this->Zoom).',center:new google.maps.LatLng(5.380144700000001,-3.989596699999993),mapTypeId: google.maps.MapTypeId.ROADMAP};map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);marker = new google.maps.Marker({map: map,position: new google.maps.LatLng(5.380144700000001, -3.989596699999993)});infowindow = new google.maps.InfoWindow({content:'.svc_json_encode('<b>'.$this->NomSite.'</b><br/>'.$this->NomRue.'</br/>'.$this->NomVille.'</br/>').' });google.maps.event.addListener(marker, "click", function(){infowindow.open(map,marker);});infowindow.open(map,marker);}google.maps.event.addDomListener(window, \'load\', init_map);</script>'.PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class LiensPartageReseauxSociaux extends PvComposantIUBase
		{
			public $Url ;
			public $Libelle = "Partager : " ;
			public $CheminIconeFacebook = "images/share_facebook.png" ;
			public $CheminIconeTwitter = "images/share_twitter.png" ;
			public $CheminIconeGooglePlus = "images/share_google_p.png" ;
			public $CheminIconeLinkedIn = "images/share_linkedin.png" ;
			public $SeparateurLiens = "&nbsp;&nbsp;" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->Libelle ;
				$ctn .= '<a href="https://www.facebook.com/sharer/sharer.php?u='.urlencode($this->Url).'" target="_share"><img src="'.$this->CheminIconeFacebook.'" border="0" /></a>' ;
				$ctn .= $this->SeparateurLiens ;
				$ctn .= '<a href="https://twitter.com/home?status='.urlencode($this->Url).'" target="_share"><img src="'.$this->CheminIconeTwitter.'" border="0" /></a>' ;
				$ctn .= $this->SeparateurLiens ;
				$ctn .= '<a href="https://plus.google.com/share?url='.urlencode($this->Url).'" target="_share"><img src="'.$this->CheminIconeGooglePlus.'" border="0" /></a>' ;
				return $ctn ;
			}
		}
		class BtnLikeFacebook extends PvComposantIUBase
		{
			public $Url ;
			public $DataLayout = "standard" ;
			public $DataShowFaces = "true" ;
			public $DataShare = "true" ;
			public $DataAction = "like" ;
			public static $SdkInclus = 0 ;
			protected function RenduSdkInclus()
			{
				if($this->ObtientValStatique("SdkInclus") == 1)
				{
					return "" ;
				}
				$this->AffecteValStatique("SdkInclus", 1) ;
				$ctn = '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->Url == "")
				{
					return $ctn ;
				}
				$ctn .= $this->RenduSdkInclus() ;
				$ctn .= '<div class="fb-like" data-href="https://developers.facebook.com/docs/plugins/" data-layout="'.htmlentities($this->DataLayout).'" data-action="'.htmlentities($this->DataAction).'" data-show-faces="'.htmlentities($this->DataShowFaces).'" data-share="'.htmlentities($this->DataShare).'"></div>' ;
				return $ctn ;
			}
		}
		
	}

?>