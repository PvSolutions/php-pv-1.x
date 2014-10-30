<?php
	
	if(! defined('PV_COMMANDE_DONNEES_BOOTSTRAP'))
	{
		define('PV_COMMANDE_DONNEES_BOOTSTRAP', 1) ;
		
		class PvCommandeBootstrapBase extends PvCommandeComposantIUBase
		{
		}
		
		class PvActCmdBootstrapBase extends PvActCmdBase
		{
		}
		class PvActCmdModalBootstrap extends PvActCmdBootstrapBase
		{
			public $Url = "" ;
			public $NomScript = "" ;
			public $Parametres = array() ;
			public $InclureTitreDlg = 0 ;
			public $CacherDlg = 1 ;
			public $TitreDlg ;
			public $CorpsDlg ;
			public $InclureBtnFermerDlg = 1 ;
			public $LibelleBtnFermerDlg = "Fermer" ;
			public $InclureBtnValiderDlg = 0 ;
			public $LibelleBtnValiderDlg = "Valider" ;
			public $HauteurCorpsDlg = "300px" ;
			public $LargeurBordureCadre = "0" ;
			protected function DetermineUrl()
			{
				if($this->NomScript != "" && ! $this->EstNul($this->ZoneParent) && isset($this->ZoneParent->Scripts[$this->NomScript]))
				{
					$this->Url = $this->ZoneParent->Scripts[$this->NomScript]->ObtientUrl() ;
				}
				if($this->Url != '' && count($this->Parametres) > 0)
				{
					$this->Url = update_url_params($this->Url, $this->Parametres) ;
				}
			}
			public function Execute()
			{
				$this->DetermineUrl() ;
				if($this->Url != "")
				{
					$this->EnvoieRenduDlg() ;
				}
				else
				{
					$this->MessageErreur = "URL vide trouvée" ;
				}
			}
			protected function EnvoieRenduDlg()
			{
				$comp = null ;
				if($this->CommandeParent->EstPasNul($this->CommandeParent->TableauDonneesParent))
				{
					$comp = & $this->CommandeParent->TableauDonneesParent ;
				}
				elseif($this->CommandeParent->EstPasNul($this->CommandeParent->FormulaireDonneesParent))
				{
					$comp = & $this->CommandeParent->FormulaireDonneesParent ;
				}
				if($comp != null)
				{
					$comp->ContenuApresRendu .= $this->ObtientCtnHtmlDlg() ;
				}
			}
			protected function ObtientTitreDlg()
			{
				return $this->TitreDlg ;
			}
			protected function ObtientCorpsDlg()
			{
				if($this->Url != '')
				{
					return '<iframe src="'.$this->Url.'" style="zoom:1; width:99.6%; height:'.$this->HauteurCorpsDlg.'" frameborder="'.$this->LargeurBordureCadre.'"></iframe>' ;
				}
				return $this->CorpsDlg ;
			}
			protected function ObtientCtnHtmlDlg()
			{
				$ctn = '' ;
				$ctn .= '<div class="modal fade" id="Dlg'.$this->IDInstanceCalc.'" tabindex="-1" role="dialog"' ;
				$ctn .= ' aria-labelledby="TitreDlg'.$this->IDInstanceCalc.'" aria-hidden="'.(($this->CacherDlg) ? 'true' : 'false').'">' ;
				$ctn .= '<div class="modal-dialog">' ;
				$ctn .= '<div class="modal-content">' ;
				$ctn .= '<div class="modal-header">' ;
				if($this->InclureBtnFermerDlg)
				{
					$ctn .= '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->LibelleBtnFermerDlg.'</span></button>' ;
				}
				if($this->InclureTitreDlg)
				{
					$ctn .= '<h4 class="modal-title" id="TitreDlg'.$this->IDInstanceCalc.'">'.$this->ObtientTitreDlg().'</h4>' ;
				}
				$ctn .= '</div>' ;
				$ctn .= '<div class="modal-body">' ;
				$ctn .= $this->ObtientCorpsDlg() ;
				$ctn .= '</div>' ;
				$ctn .= '<div class="modal-footer">' ;
				if($this->InclureBtnFermerDlg)
				{
					$ctn .= '<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->LibelleBtnFermerDlg.'</button>' ;
				}
				if($this->InclureBtnValiderDlg)
				{
					$ctn .= '<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->LibelleBtnValiderDlg.'</button>' ;
				}
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				$ctn .= '</div>' ;
				$ctn .= $this->CommandeParent->ZoneParent->RenduContenuJsInclus('jQuery(function() {
	jQuery("#Dlg'.$this->IDInstanceCalc.'").modal() ;
})') ;
				return $ctn ;
			}
		}
	}
	
?>