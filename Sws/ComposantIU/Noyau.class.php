<?php
	
	if(! defined('COMPOSANT_IU_BASE_SWS'))
	{
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		define('COMPOSANT_IU_BASE_SWS', 1) ;
		
		class ComposantIUBaseSws extends PvComposantIUBase
		{
			public $NomRef = "" ;
			public $NomClsCSS = "";
			public $CacherSiVide = 1 ;
			protected function RenduDebutTag()
			{
				$ctn = '<div id="'.$this->IDInstanceCalc.'"' ;
				if($this->NomClsCSS != '')
				{
					$ctn .= ' class="'.$this->NomClsCSS.'"' ;
				}
				$ctn .= '>' ;
				return $ctn ;
			}
			protected function RenduFinTag()
			{
				return '</div>' ;
			}
			public function EstVide()
			{
				return 0;
			}
			protected function RenduVideActif()
			{
				return ($this->CacherSiVide == 1 && $this->EstVide()) ? 1 : 0;
			}
		}
		
		class TableauDonneesBaseSws extends PvTableauDonneesHtml
		{
		}
		class FormulaireDonneesBaseSws extends PvFormulaireDonneesHtml
		{
		}
		class TableauDonneesAdminSws extends PvTableauDonneesHtml
		{
			protected function InitDessinateurBlocCommandes()
			{
				parent::InitDessinateurBlocCommandes() ;
				$this->DessinateurBlocCommandes->InclureIcone = 1 ;
				$this->DessinateurBlocCommandes->InclureLibelle = 0 ;
			}
		}
		class FormulaireDonneesAdminSws extends PvFormulaireDonneesHtml
		{
		}
		
		class DefNiveauFilArianeSws
		{
			public $ModeleUrl = "";
			public $ModeleLibelle = "";
			public $CibleNiveau = "";
			public $NomClsCSSNiveau = "";
			public $AttrsSupplNiveau = "";
		}
		class FilArianeSws extends ComposantIUBaseSws
		{
			public $DonneesNiveaux = array();
			public $ContenuAvantNiveau = "<a href='?'>/</a>";
			public $SepLiens = " &gt; ";
			public $DefNiveaux = null ;
			public $IndNiveauMin = 0;
			public $IndNiveauMax = -1;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DefNiveaux = new DefNiveauFilArianeSws() ;
			}
			protected function RenduLien($defNiveau, $donneesNv)
			{
				$url = _parse_pattern($defNiveau->ModeleUrl, array_map(htmlentities('urlencode', $donneesNv))) ;
				$libelle = _parse_pattern($defNiveau->ModeleLibelle, array_map('htmlentities', $donneesNv)) ;
				$ctn = '<a' ;
				if($defNiveau->CibleNiveau != "")
					$ctn .= ' target="'.$defNiveau->CibleNiveau.'"' ;
				if($defNiveau->NomClsCSSNiveau != "")
					$ctn .= ' class="'.$defNiveau->NomClsCSSNiveau.'"' ;
				$ctn .= ' href="'.$url.'"' ;
				if($defNiveau->AttrsSupplNiveau != "")
					$ctn .= ' '.$defNiveau->AttrsSupplNiveau ;
				$ctn .= '>' ;
				$ctn .= $libelle ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->RenduVideActif())
				{
					return $ctn ;
				}
				$min = $this->IndNiveauMin < 0 ? 0 : $this->IndNiveauMin ;
				$max = $this->IndNiveauMax < 0 ? count($this->DonneesNiveaux) - 1 : $this->IndNiveauMax ;
				$ctn .= $this->RenduDebutTag() ;
				$ctn .= $this->ContenuAvantNiveau ;
				for($i=$min; $i<$max; $i++)
				{
					if($i > 0)
						$ctn .= $this->SepLiens ;
					$ctn .= $this->RenduLien($this->DonneesNiveaux[$i]) ;
				}
				$ctn .= $this->RenduFinTag() ;
				return $ctn ;
			}
		}
		
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