<?php
	
	if(! defined('PV_NOYAU_COMPOSANT_IU_BOOTSTRAP'))
	{
		define('PV_NOYAU_COMPOSANT_IU_BOOTSTRAP', 1) ;
		
		class PvComposantIUBootstrap extends PvComposantIUBase
		{
		}
		
		class PvDessinCmdsBoostrap extends PvDessinateurRenduHtmlCommandes
		{
			public $CheminIconeParDefaut = "images/execute_icon.png" ;
			public $SeparateurIconeLibelle = "&nbsp;&nbsp;" ;
			public $SeparateurCommandes = "&nbsp;&nbsp;&nbsp;&nbsp;" ;
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$commandes = $parametres ;
				$nomCommandes = array_keys($commandes) ;
				foreach($nomCommandes as $i => $nomCommande)
				{
					$commande = & $commandes[$nomCommande] ;
					if($commande->Visible == 0 || $commande->EstBienRefere() == 0 || $commande->EstAccessible() == 0)
					{
						continue ;
					}
					if($ctn != '')
					{
						$ctn .= $this->SeparateurCommandes. PHP_EOL ;
					}
					if($commande->UtiliserRenduDispositif)
					{
						$ctn .= $commande->RenduDispositif() ;
					}
					else
					{
						$ctn .= $this->DebutExecParam($script, $composant, $i, $commande) ;
						if($commande->ContenuAvantRendu != '')
						{
							$ctn .= $commande->ContenuAvantRendu ;
						}
						$ctn .= '<button class="Commande btn '.$commande->NomClsCSS.'" type="submit" rel="'.$commande->NomElementSousComposantIU.'"' ;
						$ctn .= ' onclick="'.$composant->IDInstanceCalc.'_ActiveCommande(this) ;"' ;
						$ctn .= '>'.PHP_EOL ;
						if($this->InclureIcones)
						{
							$cheminIcone = $this->CheminIconeParDefaut ;
							if($commande->CheminIcone != '')
							{
								$cheminIcone = $commande->CheminIcone ;
							}
							if(file_exists($cheminIcone))
							{
								$ctn .= '<img src="'.$cheminIcone.'" height="'.$this->HauteurIcone.'" border="0" />' ;
							}
							$ctn .= $this->SeparateurIconeLibelle ;
						}
						$ctn .= $commande->Libelle ;
						$ctn .= '</button>'.PHP_EOL ;
						if($commande->ContenuApresRendu != '')
						{
							$ctn .= $commande->ContenuApresRendu ;
						}
						$ctn .= $this->FinExecParam($script, $composant, $i, $commande) ;
					}
				}
				return $ctn ;
			}
		}
		class PvDessinFiltresBootstrap extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$filtres = $parametres ;
				$ctn = '' ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				// echo count($filtres) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $parametres[$i] ;
					if(! $filtre->RenduPossible())
					{
						continue ;
					}
					if($filtre->LectureSeule)
					{
						$ctn .= '<input type="hidden" name="'.htmlentities($filtre->ObtientNomComposant()).'" value="'.htmlentities($filtre->Lie()).'" />'.PHP_EOL ;
						continue ;
					}
					$ctn .= '<div class="form-group">'.PHP_EOL ;
					if($this->InclureRenduLibelle)
					{
						$ctn .= '<label for="'.$filtre->ObtientIDElementHtmlComposant().'">'.$this->RenduLibelleFiltre($filtre).'</label>'.PHP_EOL ;
					}
					if($composant->Editable)
					{
						// $ctn .= $filtre->Lie() ;
						$ctn .= $filtre->Rendu().PHP_EOL ;
					}
					else
					{
						$ctn .= $filtre->Etiquette().PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
				}
				return $ctn ;
			}

		}
		
		class PvDessinCommandesBootstrap extends PvDessinateurRenduHtmlCommandes
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$commandes = $parametres ;
				$nomCommandes = array_keys($commandes) ;
				foreach($nomCommandes as $i => $nomCommande)
				{
					$commande = & $commandes[$nomCommande] ;
					if($commande->Visible == 0 || $commande->EstBienRefere() == 0 || $commande->EstAccessible() == 0)
					{
						continue ;
					}
					if($ctn != '')
					{
						$ctn .= $this->SeparateurCommandes. PHP_EOL ;
					}
					if($commande->UtiliserRenduDispositif)
					{
						$ctn .= $commande->RenduDispositif() ;
					}
					else
					{
						$ctn .= $this->DebutExecParam($script, $composant, $i, $commande) ;
						if($commande->ContenuAvantRendu != '')
						{
							$ctn .= $commande->ContenuAvantRendu ;
						}
						$ctn .= '<button class="Commande '.$commande->NomClsCSS.'" type="submit" rel="'.$commande->NomElementSousComposantIU.'"' ;
						$ctn .= ' onclick="'.$composant->IDInstanceCalc.'_ActiveCommande(this) ;"' ;
						$ctn .= '>'.PHP_EOL ;
						if($this->InclureIcones)
						{
							$cheminIcone = $this->CheminIconeParDefaut ;
							if($commande->CheminIcone != '')
							{
								$cheminIcone = $commande->CheminIcone ;
							}
							if(file_exists($cheminIcone))
							{
								$ctn .= '<img src="'.$cheminIcone.'" height="'.$this->HauteurIcone.'" border="0" />' ;
							}
							$ctn .= $this->SeparateurIconeLibelle ;
						}
						$ctn .= $commande->Libelle ;
						$ctn .= '</button>'.PHP_EOL ;
						if($commande->ContenuApresRendu != '')
						{
							$ctn .= $commande->ContenuApresRendu ;
						}
						$ctn .= $this->FinExecParam($script, $composant, $i, $commande) ;
					}
				}
				return $ctn ;
			}
		}
		
		class PvBarreTitre1Bootstrap extends PvComposantIUBootstrap
		{
			public $TitreParDefaut ;
			public $UrlParDefaut ;
			public $NomClsIconeParDefaut ;
			public $NomScriptLie ;
			protected function ObtientNomClsIcone()
			{
				return $this->NomClsIconeParDefaut ;
			}
			protected function ObtientTitre()
			{
				return $this->TitreParDefaut ;
			}
			protected function ObtientUrl()
			{
				return $this->UrlParDefaut ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<a href="'.$this->ObtientUrl().'"><strong><i class="glyphicon '.$this->ObtientNomClsIcone().'"></i> '.$this->ObtientTitre().'</strong></a>'.PHP_EOL ;
				$ctn .= '<hr>' ;
				return $ctn ;
			}
		}
	}
	
?>