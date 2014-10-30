<?php
	
	if(! defined('PV_FORMULAIRE_DONNEES_BOOTSTRAP'))
	{
		define('PV_FORMULAIRE_DONNEES_BOOTSTRAP', 1) ;
		
		class PvFormulaireDonneesBootstrap extends PvFormulaireDonneesHtml
		{
			public $TypeComposant = "FormulaireDonneesBootstrap" ;
			public $NomClasseFormFiltres = "panel-primary" ;
			public $NomRole = "form" ;
			public $AppliquerHabillageSpec = 1 ;
			public $TitreBtnFermerDlg = "Fermer" ;
			public $TitreDlg = "R&eacute;sultat" ;
			protected function RenduComposants()
			{
				$ctn = '' ;
				if(count($this->DispositionComposants))
				{
					$ctn .= '<form class="FormulaireDonnees'.(($this->NomClasseCSS != '') ? ' '.$this->NomClasseCSS : '').'" method="post" enctype="multipart/form-data" onsubmit="SoumetFormulaire'.$this->IDInstanceCalc.'(this)" role="'.$this->NomRole.'">'.PHP_EOL ;
					$ctn .= '<div class="panel '.$this->NomClasseFormFiltres.'">'.PHP_EOL ;
					foreach($this->DispositionComposants as $i => $id)
					{
						if($i > 0)
						{
							$ctn .= PHP_EOL ;
						}
						switch($id)
						{
							case PvDispositionFormulaireDonnees::BlocEntete :
							{
								$ctn .= $this->RenduBlocEntete() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::FormulaireFiltresEdition :
							{
								$ctn .= $this->RenduFormulaireFiltres() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::ResultatCommandeExecutee :
							{
								$ctn .= $this->RenduResultatCommandeExecutee() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::BlocCommandes :
							{
								$ctn .= $this->RenduBlocCommandes() ;
							}
							break ;
							default :
							{
								$ctn .= $this->RenduAutreComposantSupport($id) ;
							}
							break ;
						}
					}
					$ctn .= '</div>'.PHP_EOL ;
					$ctn .= '</form>' ;
				}
				$ctn .= $this->RenduHabillageSpec() ;
				return $ctn ;
			}
			protected function RenduHabillageSpec()
			{
				$ctn = '' ;
				if(! $this->AppliquerHabillageSpec)
				{
					return $ctn ;
				}
				$ctnJS = 'jQuery(function() {
	var comp = jQuery("#'.$this->IDInstanceCalc.'") ;
	if(comp.length == 0)
	{
		return ;
	}
	comp.find(".FormulaireFiltres :input").addClass("form-control") ;
	comp.find(".BlocCommandes :button").addClass("btn btn-primary") ;
})' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus($ctnJS) ;
				return $ctn ;
			}
			protected function RenduBlocEntete()
			{
				$ctn = '' ;
				if($this->Titre != '')
				{
					$titre = _parse_pattern($this->Titre, array_map('htmlentities', $this->ElementEnCours)) ;
					$ctn .= '<div align="'.$this->AlignTitre.'" class="panel-header '.$this->NomClasseCSSTitre.'">'.$titre.'</div>'.PHP_EOL ;
				}
				if($this->Description != '')
				{
					$desc = _parse_pattern($this->Description, array_map('htmlentities', $this->ElementEnCours)) ;
					$ctn .= '<p class="text-muted '.$this->NomClasseCSSDescription.'">'.$desc.'</p>' ;
				}
				return $ctn ;
			}
			protected function RenduResultatCommandeExecutee()
			{
				$ctn = '' ;
				if($this->EstNul($this->CommandeSelectionnee))
				{
					return $ctn ;
				}
				$classeCSS = ($this->CommandeSelectionnee->StatutExecution == 1) ? "text-success Succes" : "text-danger Erreur" ;
				if($this->PopupMessageExecution)
				{
					if(! $this->ZoneParent->InclureBootstrap)
					{
						$ctn .= '<script language="javascript">'.PHP_EOL ;
						$ctn .= 'alert('.svc_json_encode(html_entity_decode($this->CommandeSelectionnee->MessageExecution)).') ;' ;
						$ctn .= '</script>'.PHP_EOL ;
					}
					else
					{
						$ctn .= '<div class="modal fade" id="DialogMsg'.$this->IDInstanceCalc.'" tabindex="-1" role="dialog" aria-labelledby="TitreDialog'.$this->IDInstanceCalc.'" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->TitreBtnFermerDlg.'</span></button>
        <h4 class="modal-title" id="TitreDialog'.$this->IDInstanceCalc.'">'.$this->TitreDlg.'</h4>
      </div>
      <div class="modal-body">
        <p class="'.$classeCSS.'">'.$this->CommandeSelectionnee->MessageExecution.'</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">'.$this->TitreBtnFermerDlg.'</button>
      </div>
    </div>
  </div>
</div>' ;
						$ctnJS = 'jQuery(function() {
	jQuery("#DialogMsg'.$this->IDInstanceCalc.'").modal() ;
})'.PHP_EOL ;
						$ctn .= $this->ZoneParent->RenduContenuJsInclus($ctnJS).PHP_EOL ;
					}
				}
				else
				{
					$ctn .= '<div' ;
					$ctn .= ' class="'.$classeCSS.'"' ;
					$ctn .= '>' ;
					$ctn .= $this->CommandeSelectionnee->MessageExecution ;
					$ctn .= '</div>' ;
				}
				return $ctn ;
			}
			protected function RenduFormulaireFiltres()
			{
				$ctn = "" ;
				if(! $this->CacherFormulaireFiltres)
				{
					if($this->ElementEnCoursTrouve)
					{
						if($this->EstNul($this->DessinateurFiltresEdition))
						{
							$this->InitDessinateurFiltresEdition() ;
						}
						if($this->EstNul($this->DessinateurFiltresEdition))
						{
							return "<p class=''>Le dessinateur de filtres n'est pas défini</p>" ;
						}
						$ctn .= '<div class="panel-body FormulaireFiltres">'.PHP_EOL ;
						$ctn .= $this->DessinateurFiltresEdition->Execute($this->ScriptParent, $this, $this->FiltresEdition) ;
						$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresEdition).PHP_EOL ;
						$ctn .= '</div>' ;
					}
					else
					{
						if(! $this->EstNul($this->FournisseurDonnees))
						{
							// echo 'Err Sql : '.$this->FournisseurDonnees->BaseDonnees->LastSqlText ;
						}
						$ctn .= '<p class="text-danger">'.$this->MessageAucunElement.'</p>' ;
					}
				}
				return $ctn ;
			}
			protected function RenduBlocCommandes()
			{
				$ctn = '' ;
				if(! $this->CacherBlocCommandes && ! $this->CacherFormulaireFiltres)
				{
					if($this->ElementEnCoursTrouve)
					{
						if($this->EstNul($this->DessinateurBlocCommandes))
						{
							$this->InitDessinateurBlocCommandes() ;
						}
						if($this->EstNul($this->DessinateurBlocCommandes))
						{
							return "<p>Le dessinateur de filtres n'est pas défini</p>" ;
						}
						$ctn .= '<div class="panel-footer BlocCommandes">'.PHP_EOL ;
						$ctn .= $this->DessinateurBlocCommandes->Execute($this->ScriptParent, $this, $this->Commandes) ;
						$ctn .= $this->DeclarationJsActiveCommande().PHP_EOL ;
						$ctn .= '</div>' ;
					}
				}
				return $ctn ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresBootstrap() ;
			}
		}
	}
	
?>