<?php
	
	if(! defined('COMPOSANT_IU_LIVRE_D_OR_SWS'))
	{
		define('COMPOSANT_IU_LIVRE_D_OR_SWS', 1) ;
		
		class BlocSommaireLivreDOrSws extends PvComposantIUBase
		{
			public $DonneesSupport ;
			public $InclureTitre = 0 ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$modulePage = $this->ScriptParent->ObtientModulePage() ;
				if($this->ScriptParent->EstNul($modulePage))
				{
					return '' ;
				}
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="bloc-sommaire">' ;
				$ctn .= '<div><img src="'.$modulePage->CheminIllustration.'" /></div>' ;
				if($this->InclureTitre)
					$ctn .= '<div class="titre">'.htmlentities($this->DonneesSupport["titre"]).'</div>' ;
				if($ctn != "")
				{
					$ctn .= '<div class="contenu">'.htmlentities($this->DonneesSupport["sommaire"]).'</div>' ;
				}
				$ctn .= '<p align="center"><img src="'.$modulePage->CheminIconeEcrireMsg.'" />&nbsp;&nbsp;<a href="'.$modulePage->EntiteCmtLivreDOr->ScriptPoster->ObtientUrlParam(array($modulePage->EntiteLivreDOr->NomParamId => $this->DonneesSupport["id"])).'">'.$modulePage->EntiteLivreDOr->LibelleLienEcrireMsg.'</a></p>' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class BarrePosterCmtLivreDOrSws extends PvComposantIUBase
		{
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'">' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
	}
	
?>