<?php
	
	if(! defined('EDITEUR_BASE_WSM'))
	{
		define('EDITEUR_BASE_WSM') ;
		
		class EditeurDonneesExtraWsm
		{
			public $ColonneSupport = null ;
			public $ValeursSupport = null ;
			public $ContenuRendu = "" ;
			public function Rendu(& $colonne, $valeurs)
			{
				$this->ColonneSupport = $colonne ;
				$this->ValeursSupport = $valeurs ;
				$this->ContenuRendu = "" ;
				$this->ConstruitRendu() ;
				$this->ColonneSupport = null ;
				$this->ValeursSupport = null ;
				return $this->ContenuRendu ;
			}
			protected function ConstruitRendu()
			{
			}
		}
	}
	
?>