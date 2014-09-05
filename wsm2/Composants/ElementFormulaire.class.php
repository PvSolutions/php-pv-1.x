<?php
	
	if(! defined('ELEMENT_FORM_SITE_WEB'))
	{
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/../../Pv/Base.class.php" ;
		}
		define('ELEMENT_FORM_SITE_WEB', 1) ;
		
		class PvEvalEtoilesPur extends PvElementFormulaireHtml
		{
			public $CheminJeuIcones = "images/star_rate.gif" ;
			public $MinUnites = 1 ;
			public $MaxUnites = 5 ;
			public $PasUnites = 1 ;
			protected function RenduDispositifBrut()
			{
				if($this->MaxUnites <= $this->MinUnites)
				{
					return '' ;
				}
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$styleCSS = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="hidden"' ;
				$ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />'.PHP_EOL ;
				$ctn .= '<div id="conteneur_'.$this->IDInstanceCalc.'"' ;
				if($this->Largeur != '')
				{
					$styleCSS .= 'width:'.$this->Largeur.';' ;
				}
				if($this->Hauteur != '')
				{
					$styleCSS .= 'height:'.$this->Hauteur.';' ;
				}
				if($this->StyleCSS != '')
				{
					$styleCSS .= $this->StyleCSS ;
				}
				if($styleCSS != '')
				{
					$ctn .= ' style="'.$styleCSS.'"' ;
				}
				$ctn .= '>'.PHP_EOL ;
				$ctn .= '<div>Par defaut : '.htmlentities($this->Valeur).'<b></b></div>' ;
				$ctn .= '<ul>'.PHP_EOL ;
				for($i=$this->MinUnites; $i<=$this->MaxUnites; $i+= $this->PasUnites)
				{
					$ctn .= '<li><a href="javascript:definitVal_'.$this->IDInstanceCalc.'('.$i.')"><span>&nbsp;&nbsp;</span></a>' ;
					if($this->Valeur == $i)
					{
						$ctn .= '<b></b>' ;
					}
					$ctn .= '</li>'.PHP_EOL ;
				}
				$ctn .= '</ul>'.PHP_EOL ;
				$ctn .= '</div>' ;
				$ctn .= '<script language="javascript">
	function definitVal_'.$this->IDInstanceCalc.'(nouvVal)
	{
		document.getElementById("'.$this->IDInstanceCalc.'").value = nouvVal ;
	}
</script>'.PHP_EOL ;
				$ctn .= '<style type="text/css">
#conteneur_'.$this->IDInstanceCalc.' {position:relative; margin:0px; overflow:hidden; zoom:1;}
#conteneur_'.$this->IDInstanceCalc.' ul {width:160px; margin:0; padding:0;}
#conteneur_'.$this->IDInstanceCalc.' li {display:inline; list-style:none;}
#conteneur_'.$this->IDInstanceCalc.' a, #conteneur_'.$this->IDInstanceCalc.' b {background:url('.$this->CheminJeuIcones.') left top repeat-x;}
#conteneur_'.$this->IDInstanceCalc.' a {float:right; margin:0 80px 0 -144px; width:80px; height:16px; background-position:left 16px; color:#000; text-decoration:none;}
#conteneur_'.$this->IDInstanceCalc.' a:hover {background-position:left -32px;}
#conteneur_'.$this->IDInstanceCalc.' b {position:absolute; z-index:-1; width:80px; height:16px; background-position:left -16px;}
#conteneur_'.$this->IDInstanceCalc.' div b {left:0px; bottom:0px; background-position:left top;}
#conteneur_'.$this->IDInstanceCalc.' a span {position:absolute; left:-300px;}
#conteneur_'.$this->IDInstanceCalc.' a:hover span {left:90px; width:100%;}
</style>' ;
				return $ctn ;
			}
		}
	}
	
?>