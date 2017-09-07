<?php
	
	if(! defined('PV_COMMANDE_IU_IONIC'))
	{
		define('PV_COMMANDE_IU_IONIC', 1) ;
		
		class PvCommandeNavCtrlPopIonic extends PvCommandeTsIonic
		{
			public $ContenuTs = 'this.navCtrl.pop() ;' ;
		}
		class PvCommandeNavCtrlPushIonic extends PvCommandeTsIonic
		{
			public $ContenuTs = '' ;
			public $ContenuNavParams = '{}' ;
			public $NomPageSrc = '' ;
			public function CorpsBrutMethodeTs()
			{
				if($this->NomPageSrc == '' || ! $this->ZoneParent()->ExistePageSrc($this->NomPageSrc))
				{
					return '' ;
				}
				$pageSrc = $this->PageSrcParent()->PagesSrc[$this->NomPageSrc] ;
				$pageSrc->InsereImportPageSrcTs($pageSrc) ;
				return 'this.navCtrl.push('.$pageSrc->NomClasse().', '.$this->ContenuNavParams.')' ;
			}
		}
		
		class PvCommandeMethodeDistanteIonic extends PvCommandeAppelDistantIonic
		{
			protected $Mode = 1 ;
			protected $MtdDistPrinc ;
			protected function CreeMethodeDistante()
			{
				return new PvMtdDistNonTrouveeIonic() ;
			}
			public function FournitMethodesDistantes()
			{
				$this->MtdDistPrinc = $this->InsereMethodeDistante("Princ", $this->CreeMethodeDistante()) ;
			}
			public function CorpsBrutMethodeTs()
			{
				$this->NomMtdDist = $this->ComposantIUParent->NomMethodeDistante("Cmd_".$this->NomElementComposantIU."_Princ") ;
				return parent::CorpsBrutMethodeTs() ;
			}
		}
		
		class PvCmdEditElemDonneesIonic extends PvCommandeMethodeDistanteIonic
		{
			public $Mode = 1 ;
			protected function ExecuteInstructions()
			{
				if($this->EstNul($this->ComposantIUParent->FournisseurDonnees))
				{
					$this->RenseigneErreur("La base de donn&eacute;e du formulaire n'est renseign&eacute;e.") ;
					return ;
				}
				$succes = 0 ;
				/*
				 * Debogages
				foreach($this->ComposantIUParent->FiltresEdition as $i => & $fltEdit)
				{
					echo $fltEdit->IDInstanceCalc."@".$fltEdit->NomParametreLie." : ".intro($fltEdit->Lie())."<br>" ;
				}
				* */
				switch($this->Mode)
				{
					case PvModeEditionElement::Ajout :
					{
						$succes = $this->ComposantIUParent->FournisseurDonnees->AjoutElement($this->ComposantIUParent->FiltresEdition) ;
					}
					break ;
					case PvModeEditionElement::Modif :
					{
						// print_r($this->ComposantIUParent->FiltresLigneSelection[0]->NomParametreDonnees) ;
						$succes = $this->ComposantIUParent->FournisseurDonnees->ModifElement($this->ComposantIUParent->FiltresLigneSelection, $this->ComposantIUParent->FiltresEdition) ;
					}
					break ;
					case PvModeEditionElement::Suppr :
					{
						$succes = $this->ComposantIUParent->FournisseurDonnees->SupprElement($this->ComposantIUParent->FiltresLigneSelection) ;
					}
					break ;
					default :
					{
						$this->RenseigneErreur("Le mode d'&eacute;dition de la commande est inconnue") ;
					}
					break ;
				}
				// echo "Classe ".get_class($this->ComposantIUParent->FournisseurDonnees) ;
				// print_r($this->ComposantIUParent->FournisseurDonnees->BaseDonnees) ;
				if(count($this->ComposantIUParent->FiltresEdition) == 0)
				{
					$this->RenseigneErreur("Aucun filtre d'edition n'a &eacute;t&eacute; d&eacute;fini") ;
				}
				elseif(! $succes && $this->ComposantIUParent->FournisseurDonnees->BaseDonnees->ConnectionException != "")
				{
					/// print_r($this->ComposantIUParent->FournisseurDonnees->BaseDonnees) ;
					$this->RenseigneErreur("Erreur SQL : ".$this->ComposantIUParent->FournisseurDonnees->BaseDonnees->ConnectionException) ;
					// $this->ComposantIUParent->AfficheExceptionFournisseurDonnees() ;
				}
				else
				{
					$this->ConfirmeSucces() ;
				}
			}
		}
		
		class PvCmdAjoutElemDonneesIonic extends PvCmdEditElemDonneesIonic
		{
			public $Mode = 1 ;
		}
		class PvCmdModifElemDonneesIonic extends PvCmdEditElemDonneesIonic
		{
			public $Mode = 2 ;
		}
		class PvCmdSupprElemDonneesIonic extends PvCmdEditElemDonneesIonic
		{
			public $Mode = 3 ;
		}
	}
	
?>