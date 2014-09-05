<?php
	
	if(! defined('NOYAU_SYST_ABON'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/Noyau.class.php" ;
		}
		define('NOYAU_SYST_ABON', 1) ;
		
		class ReferencielSystAbon
		{
		}
		
		class ApplicationSystAbon
		{
		}
		
		class LigneBaseSystAbon extends CommonEntityRow
		{
			public $Id ;
		}
		class ObjetBaseSystAbon extends PvObjet
		{
			public $Nom ;
			public $Titre ;
			public $Description ;
		}
		
		class AbonneBaseSystAbon extends LigneBaseSystAbon
		{
			public $Nom = "" ;
			public $Prenom = "" ;
			public $IdAbon1 = "" ;
			public $IdAbon2 = "" ;
			public $IdAbon3 = "" ;
			public $DateInscript = "" ;
			public function Souscrit($forfait)
			{
			}
			public function Refuse($forfait)
			{
			}
		}
		
		class ForfaitBaseSystAbon extends LigneBaseSystAbon
		{
			public $Nom ;
			public $Titre = "" ;
			public $Description ;
			public $DelaiExpiration = 0 ;
			// Periodes de disponibilites du forfait
			public $PrdsDispo = array() ;
			// Moyens de paiement
			public $MoyenPaiements = array() ;
			// Prerequis que doivent respecter les abonnés
			// qui desirent souscrire
			public $PreRequis = array() ;
			// Avantages octroyés lors d'une souscription
			public $Avantages = array() ;
			// Renouvellements
			public $Renouvs = array() ;
		}
		
		class PrdDispoBaseSystAbon extends ObjetBaseSystAbon
		{
			public $Nom = '' ;
			public $Titre = 'Base' ;
			public function EstActive()
			{
			}
		}
		class PrdToujoursSystAbon extends PrdDispoBaseSystAbon
		{
			public $Titre = 'Toujours' ;
			public function EstActive()
			{
				return 1 ;
			}
		}
		class PrdIntervalSystAbon extends PrdDispoBaseSystAbon
		{
			public $Titre = 'Interval de temps' ;
			public $DateDebutNulle = 1 ;
			public $DateDebut = '' ;
			public $DateFinNulle = 1 ;
			public $DateFin = '' ;
			public function EstActive()
			{
				if($this->DateDebut == '' && $this->DateFin == '')
					return 0 ;
				$dateActuelle = date("Y-m-d H:i:s") ;
				if(($this->DateDebut != '' && $this->DateDebut > $dateActuelle) || ($this->DateDebut == '' && $this->DateDebutNulle == 0))
					return 0 ;
				if(($this->DateFin != '' && $this->DateFin < $dateActuelle) || ($this->DateFin == '' && $this->DateFinNulle == 0))
					return 0 ;
				return 1 ;
			}
		}
		class PrdNuitSystAbon extends PrdDispoBaseSystAbon
		{
			public $Titre = "Nuit" ;
			public $HeureMin = '22' ;
			public $HeureMax = '06' ;
			public function EstActive()
			{
				$heureActuelle = date("H") ;
				if($this->HeureMin > $heureActuelle || $this->HeureMin == '')
				{
					return 0 ;
				}
				if($this->HeureMax < $heureActuelle || $this->HeureMax == '')
				{
					return 0 ;
				}
				return 1 ;
			}
		}
		class PrdWeekendSystAbon extends PrdDispoBaseSystAbon
		{
			public $Titre = "Weekend" ;
			public function EstActive()
			{
				$heureActuelle = intval(date("d")) ;
				if(5 < $heureActuelle)
				{
					return 0 ;
				}
				if(1 > $heureActuelle)
				{
					return 0 ;
				}
				return 1 ;
			}
		}
		class PrdJoursOuvrSystAbon extends PrdDispoBaseSystAbon
		{
			public $Titre = "Jours ouvrables" ;
			public function EstActive()
			{
				$jourActuelle = intval(date("d")) ;
				if(5 > $jourActuelle)
				{
					return 0 ;
				}
				if(1 < $jourActuelle)
				{
					return 0 ;
				}
				return 1 ;
			}
		}
		
		class MoyenPaiementBaseSystAbon extends ObjetBaseSystAbon
		{
			public function EstDisponible()
			{
				return 1 ;
			}
			public function Debite(& $abon, & $forfait)
			{
				return 0 ;
			}
		}
		
		class PreRequisBaseSystAbon extends LigneBaseSystAbon
		{
			public $Nom ;
			public $Description ;
			public function EstDisponible()
			{
				return 1 ;
			}
			public function Respecte(& $abon)
			{
				if(! $this->EstDisponible())
				{
					return 0 ;
				}
				return $this->VerifieAbonne($abon) ;
			}
			protected function VerifieAbonne(& $abon)
			{
			}
		}
		
		class RenouvBaseSystAbon extends LigneBaseSystAbon
		{
			public $PrdDispo = null ;
		}
		
		class AvantageBaseSystAbon extends LigneBaseSystAbon
		{
			public $Nom ;
			public $Titre ;
			public $Description ;
			public function Attache(& $abon)
			{
			}
			public function ConfirmeAttach(& $abon)
			{
			}
			public function Retire(& $abon)
			{
			}
			public function ConfirmeRetrait(& $abon)
			{
			}
		}
	}
	
?>