<?php
	
	if(! defined('EDITEUR_SITE_WEB_SWS'))
	{
		define('EDITEUR_SITE_WEB_SWS', 1) ;
		
		class PvConfigCkEditor
		{
			public $language = "fr" ;
			public $width = "600" ;
			public $height = "200" ;
			public $baseHref = "" ;
			public $toolbar = array(
				array('Source', '-', 'NewPage', 'Preview'),
				array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
				'/',
				array('Bold', 'Italic', 'Underline'),
				array('CreateDiv', 'NumberedList', 'BulletedList', 'Link', 'Unlink', '-', 'Image', 'Flash', 'Table'),
				array('Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl'),
				'/',
				array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor')
			) ;
		}
		class PvCkEditor extends PvEditeurHtmlBase
		{
			protected static $SourceIncluse = 0 ;
			protected $NomClasseCSS ;
			protected $TotalLignes ;
			protected $TotalColonnes ;
			public $Config ;
			protected $CheminFichierJs = "js/ckeditor/ckeditor.js" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Config = new PvConfigCkEditor() ;
			}
			protected function RenduSourceBrut()
			{
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminFichierJs) ;
				return $ctn ;
			}
			protected function RenduEditeurBrut()
			{
				$ctn = '' ;
				$ctn .= '<textarea' ;
				$ctn .= ' class="ckeditor'.(($this->NomClasseCSS == '') ? '' : ' '.$this->NomClasseCSS).'"' ;
				if($this->TotalLignes != '')
					$ctn .= ' rows="'.$this->TotalLignes.'"' ;
				if($this->TotalColonnes != '')
					$ctn .= ' cols="'.$this->TotalColonnes.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' name="'.$this->NomElementHtml.'"' ;
				$ctn .= '>' ;
				$ctn .= htmlentities($this->Valeur) ;
				$ctn .= '</textarea>' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus($this->DefJsEditeur()) ;
				return $ctn ;
			}
			protected function DefJsEditeur()
			{
				$ctn = '' ;
				if($this->Hauteur != "")
					$this->Config->height = $this->Hauteur ;
				if($this->Largeur != "")
					$this->Config->width = $this->Largeur ;
				$this->Config->baseHref = get_current_url_dir() ;
				$ctn .= 'CKEDITOR.replace("'.$this->IDInstanceCalc.'", '.svc_json_encode($this->Config).') ;' ;
				// $ctn .= 'CKEDITOR.replace("'.$this->IDInstanceCalc.'", {toolbar : { name: \'document\', groups: [ \'mode\', \'document\', \'doctools\' ], items: [ \'Source\', \'-\', \'Save\', \'NewPage\', \'Preview\', \'Print\', \'-\', \'Templates\' ] }}) ;' ;
				return $ctn ;
			}
		}
		
	}
	
?>