<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_ELEM_CSS'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
		{
			include dirname(__FILE__)."/../FournisseurDonnees.class.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_ELEM_CSS', 1) ;
		
		class PvCercleProgressCSS extends PvComposantIUBase
		{
			public static $SourceIncluse = 0 ;
			public static $CheminFichierCSS = "css/circle.css" ;
			public $FournisseurDonnees = null ;
			public $FiltresSelection = array() ;
			public $NomColValeur = "pourcentage" ;
			public $NomClasseCSS = "" ;
			public $Align = "center" ;
			public $Taille = "" ;
			public $Couleur = "" ;
			public $MsgPreRequisNonVerif = "Le fournisseur de donn&eacute;es n'est pas configur&eacute; correctement" ;
			protected $Pourcentage = -1 ;
			protected $PourcentageBrut = null ;
			protected $LgnsDonnees = array() ;
			protected static function InclutLibSource()
			{
				if(PvCercleProgressCSS::$SourceIncluse == 1)
				{
					return '' ;
				}
				$ctn = '' ;
				$ctn .= '<link rel="stylesheet" href="'.PvCercleProgressCSS::$CheminFichierCSS.'">'.PHP_EOL ;
				PvCercleProgressCSS::$SourceIncluse = 1 ;
				return $ctn ;
			}
			public function PourcentageTrouve()
			{
				return $this->PourcentageBrut !== null ;
			}
			protected function VerifiePreRequis()
			{
				if($this->EstNul($this->FournisseurDonnees))
				{
					return 0 ;
				}
				if($this->FournisseurDonnees->RequeteSelection == "")
				{
					return 0 ;
				}
				return 1 ;
			}
			protected function CalculeDonneesRendu()
			{
				$this->LgnsDonnees = $this->FournisseurDonnees->SelectElements(array(), $this->FiltresSelection) ;
				if(! $this->FournisseurDonnees->ExceptionTrouvee())
				{
					if(count($this->LgnsDonnees) > 0 && isset($this->LgnsDonnees[0][$this->NomColValeur]))
					{
						$this->PourcentageBrut = $this->LgnsDonnees[0][$this->NomColValeur] ;
					}
				}
				$this->Pourcentage = 0 ;
				if($this->PourcentageBrut !== null)
				{
					if($this->PourcentageBrut > 100)
					{
						$this->Pourcentage = 100 ;
					}
					elseif($this->PourcentageBrut < 0)
					{
						$this->Pourcentage = 0 ;
					}
					else
					{
						$this->Pourcentage = intval($this->PourcentageBrut) ;
					}
				}
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= PvCercleProgressCSS::InclutLibSource() ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'"'.(($this->Align != '') ? ' align="'.$this->Align.'"' : '').'>'.PHP_EOL ;
				if(! $this->VerifiePreRequis())
				{
					$ctn .= '<div class="Erreur">'.$this->MsgPreRequisNonVerif.'</div>' ;
					return $ctn ;
				}
				$this->CalculeDonneesRendu() ;
				if($this->FournisseurDonnees->ExceptionTrouvee())
				{
					$ctn .= '<div class="Erreur">'.$this->FournisseurDonnees->MessageException().'</div>' ;
				}
				else
				{
					$ctn .= '<div class="c100 p'.$this->Pourcentage.''.(($this->Taille != '') ? ' '.$this->Taille : '').(($this->Couleur != '') ? ' '.$this->Couleur : '').''.(($this->NomClasseCSS != '') ? $this->NomClasseCSS : '').'">
<span>'.$this->Pourcentage.'%</span>
<div class="slice">
<div class="bar"></div>
<div class="fill"></div>
</div>
</div>' ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
	}
	
?>