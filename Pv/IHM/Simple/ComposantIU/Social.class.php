<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_SOCIAL'))
	{
		define("PV_ID_APP_FACEBOOK_DEFAUT", "1783057932024961") ;
		
		define('PV_COMPOSANT_SIMPLE_SOCIAL', 1) ;
		
		class PvCommentaireFacebook extends PvComposantIUBase
		{
			public $AppId = PV_ID_APP_FACEBOOK_DEFAUT ; // 
			public $Version = '2.9' ;
			public $DataWidth = "550" ;
			public $DataNumPosts = 8 ;
			public $DataColorScheme = "social" ;
			public $DataOrderBy = "light" ;
			public $ParamsUrl = array() ;
			protected function RenduDispositifBrut()
			{
				$url = $this->ZoneParent->ScriptPourRendu->ObtientUrlParam($this->ParamsUrl) ;
				$ctn = '' ;
				$ctn .= '<div id="fb-root"></div>'.PHP_EOL ;
				$ctn .= '<div class="fb-comments" data-href="'.htmlspecialchars($url).'" data-numposts="'.intval($this->DataNumPosts).'"' ;
				if($this->DataWidth != '')
				{
					$ctn .= ' data-width="'.$this->DataWidth.'"' ;
				}
				if($this->DataColorScheme != '')
				{
					$ctn .= ' data-colorscheme="'.$this->DataColorScheme.'"' ;
				}
				if($this->DataOrderBy != '')
				{
					$ctn .= ' data-order-by="'.$this->DataOrderBy.'"' ;
				}
				$ctn .= '></div>'.PHP_EOL ;
				$this->ZoneParent->InscritContenuJsPied('jQuery("document").ready(function() {
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v'.$this->Version.'&appId='.urlencode($this->AppId).'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));
}) ;') ;
				return $ctn ;
			}
		}
	}

?>