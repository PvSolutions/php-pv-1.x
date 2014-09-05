<?php
	
	if(! defined('COMPOSANT_IU_ENTITE_SWS'))
	{
		if(! defined('COMPOSANT_IU_BASE_SWS'))
		{
			include dirname(__FILE__).'/Noyau.class.php' ;
		}
		define('COMPOSANT_IU_ENTITE_SWS', 1) ;
		
		class BlocTitreLgnEnCoursSws extends PvComposantIUBase
		{
			public $NomClsCSS = "titre" ;
			public $NomColTitre = "titre" ;
			public $NomColIcone = "chemin_icone" ;
			protected function RenduDispositifBrut()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$ctn = '' ;
				$ctn .= '<h3 id="'.$this->IDInstanceCalc.'"' ;
				if($this->NomClsCSS != '')
					$ctn .= ' class="'.$this->NomClsCSS.'"' ;
				$ctn .= '>'.PHP_EOL ;
				$ctn .= htmlentities($entite->LgnEnCours[$entite->NomColTitre]) ;
				$ctn .= '</h3>' ;
				return $ctn ;
			}
		}
	}
	
?>