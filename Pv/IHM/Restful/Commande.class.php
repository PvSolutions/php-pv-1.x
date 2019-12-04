<?php
	
	if(! defined('PV_COMMANDE_RESTFUL'))
	{
		define('PV_COMMANDE_RESTFUL', 1) ;
		
		class PvCommandeBaseRestful extends PvObjet
		{
			public $MessageErreurExecution = "La commande a &eacute;t&eacute; ex&eacute;cut&eacute;e avec des erreurs" ;
			public $MessageSuccesExecution = "La commande a &eacute;t&eacute; ex&eacute;cut&eacute;e avec succ&egrave;s" ;
			public $MessageExecution ;
			public $Criteres = array() ;
			public $StatutExecution = 0 ;
			public $ComposantParent ;
			public $NomElementComposant ;
			public $ParamsExecution = array() ;
			public function AdopteFormulaireDonnees($nom, & $formulaireDonnees)
			{
				$this->NomElementFormulaireDonnees = $nom ;
				$this->FormulaireDonneesParent = & $formulaireDonnees ;
				$this->AdopteComposant($nom, $formulaireDonnees) ;
			}
			public function AdopteTableauDonnees($nom, & $tableauDonnees)
			{
				$this->NomElementTableauDonnees = $nom ;
				$this->TableauDonneesParent = & $tableauDonnees ;
				$this->AdopteComposant($nom, $tableauDonnees) ;
			}
			public function ExtraitParamsExecution()
			{
				$params = $this->ParamsExecution ;
				if($this->NecessiteFormulaireDonnees)
				{
					$params = array_merge($params, $this->FormulaireDonneesParent->ExtraitValeursParametre($this->FormulaireDonneesParent->FiltresEdition)) ;
				}
				if($this->NecessiteTableauDonnees)
				{
					$params = array_merge($params, $this->TableauDonneesParent->ExtraitValeursParametre($this->TableauDonneesParent->FiltresSelection)) ;
				}
				return $params ;
			}
			public function EstSucces()
			{
				return $this->StatutExecution == 1 ;
			}
			public function ErreurNonRenseignee()
			{
				return $this->MessageErreur == "" ;
			}
			public function AdopteComposant($nom, $composant)
			{
				$this->ComposantParent = & $composant ;
				$this->NomElementComposant = $nom ;
			}
			public function Execute()
			{
				$this->PrepareExecution() ;
				$this->ExecuteInstructions() ;
				$this->TermineExecution() ;
			}
			protected function VideStatutExecution()
			{
				$this->MessageExecution = "" ;
				$this->StatutExecution = 1 ;
			}
			public function RenseigneErreur($messageErreur="")
			{
				$this->MessageExecution = $messageErreur ;
				$this->StatutExecution = 0 ;
			}
			public function ConfirmeSucces($msgSucces = '')
			{
				$this->StatutExecution = 1 ;
				$paramsSucces = $this->ExtraitParamsExecution() ;
				if(count($paramsSucces) == 0)
				{
					$this->MessageExecution = ($msgSucces == '') ? $this->MessageSuccesExecution : $msgSucces ;
				}
				else
				{
					$this->MessageExecution = _parse_pattern(($msgSucces == '') ? $this->MessageSuccesExecution : $msgSucces, $paramsSucces) ;
				}
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
			}
			protected function PrepareExecution()
			{
			}
			protected function ExecuteInstructions()
			{
			}
			protected function TermineExecution()
			{
			}
		}
	}
	
?>