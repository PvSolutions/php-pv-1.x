<?php
	
	if(! defined('PV_PROCESSUS_PERSISTANT'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_PROCESSUS_PERSISTANT', 1) ;
		
		class PvElemProcessusPersistantBase extends PvObjet
		{
			public function ImportConfigParValeurs($valeurs=array())
			{
				if($valeurs == null)
					return ;
				foreach($valeurs as $nom => $valeur)
				{
					$this->ImportConfigParValeur($nom, $valeur) ;
				}
			}
			protected function ImportConfigParValeur($nom, $valeur)
			{
				return 0 ;
			}
		}
		
		class PvProcessusQueueElements extends PvProcessusPersistant
		{
			public $Elements = array() ;
			public $TotalElements = 0 ;
			public $ConvertirElementEnClasse = 0 ;
			public $ArreterSiDelaiMaxTraitElem = 1 ;
			public $DelaiMaxTraitElem = 60 ;
			public $TimestmpDebutTraitElem = 0 ;
			public $TimestmpFinTraitElem = 0 ;
			public $DelaiTraitElem = 0 ;
			public $DelaiMaxRecupElements = 60 ;
			public $ArreterSiDelaiMaxRecupElems = 1 ;
			public $TimestmpDebutRecupElems = 0 ;
			public $TimestmpFinRecupElems = 0 ;
			public $DelaiRecupElems = 0 ;
			public $ClusterHLR1 ;
			public $ClusterHLR2 ;
			public function CreeInstanceElement()
			{
				return new PvElemProcessusPersistantBase() ;
			}
			public function TotalElementsEnAttente()
			{
				return 0 ;
			}
			public function AjouteElement($element)
			{
			}
			protected function RecupElements()
			{
				$elements = $this->RecupElementsBruts() ;
				if($this->ConvertirElementEnClasse)
				{
					/*
					if(! class_exists($this->NomClasseElement))
					{
						die("La classe ".$this->NomClasseElement." n'existe pas. Elle ne peut etre utilise pour la recuperation d'elements.") ;
					}
					$classeElements = array() ;
					$nomClasse = $this->NomClasseElement ;
					*/
					$classeElements = array() ;
					foreach($elements as $i => $element)
					{
						$classeElement = $this->CreeInstanceElement() ;
						if(method_exists($classeElement, "ImportConfigParValeurs"))
						{
							$classeElement->ImportConfigParValeurs($element) ;
						}
						$classeElements[] = $classeElement ;
					}
					return $classeElements ;
				}
				return $elements ;
			}
			protected function RecupElementsBruts()
			{
				return array() ;
			}
			protected function ChargeElements()
			{
				$this->TimestmpDebutTraitElem = date("U") ;
				$this->Elements = $this->RecupElements() ;
				$this->TotalElements = count($this->Elements) ;
				$this->TimestmpFinTraitElem = date("U") ;
				$this->DelaiTraitElem = $this->TimestmpFinTraitElem - $this->TimestmpDebutTraitElem ;
				if($this->ArreterSiDelaiMaxTraitElem && $this->DelaiTraitElem <= $this->DelaiMaxTraitElem)
				{
					$this->DefinitMotifArret(2001, 'Le delai max. pour la recuperation des éléments est atteint', array()) ;
					$this->Arreter = 1 ;
				}
			}
			public function ExecuteTraitement()
			{
				$this->ChargeElements() ;
				while($this->TotalElements > 0)
				{
					$this->TraiteElements() ;
					$this->ChargeElements() ;
				}
			}
			protected function TraiteElements()
			{
				foreach($this->Elements as $i => $element)
				{
					$this->TimestmpDebutRecupElems = date("U") ;
					$this->TraiteElement($element, $i) ;
					$this->TimestmpFinRecupElems = date("U") ;
					$this->DelaiRecupElems = $this->TimestmpFinRecupElems - $this->TimestmpDebutRecupElems ;
					if($this->ArreterSiDelaiMaxRecupElems && $this->DelaiRecupElems <= $this->DelaiMaxRecupElements)
					{
						$this->DefinitMotifArret(2002, 'Le delai max. pour la recuperation des éléments est atteint', array()) ;
						$this->Arreter = 1 ;
					}
				}
			}
			protected function ValideDemandeLever($element)
			{
				$ok = 1 ;
				
				return $ok ;
			}
			protected function OuvreCnxHLR()
			{
			}
			protected function FermeCnxHLR()
			{
			}
			public function TraiteElement($element, $i=0)
			{
				if($this->ValideDemandeLever($element) == 0)
					return ;
			}
		}
	}
	
?>