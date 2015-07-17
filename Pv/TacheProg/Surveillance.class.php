<?php
	
	if(! defined('PV_TACHE_SURVEILLANCE'))
	{
		if(! defined("PV_TACHE_PROG"))
		{
			include dirname(__FILE__)."/../TacheProg.class.php" ;
		}
		if(! class_exists('PHPMailer'))
		{
			include dirname(__FILE__)."/../../misc/phpmailer/class.phpmailer.php" ;
		}
		define('PV_TACHE_SURVEILLANCE', 1) ;
		
		class PvTacheSurveil extends PvTacheProg
		{
			public $Activites = array() ;
			public $Alerteurs = array() ;
			public $NomAlerteurs = array() ;
			public $Session = null ;
			public $TotalNonConformes = 0 ;
			public function InsereAlerteur($nom, $alerte)
			{
				return $this->InscritAlerte($nom, $alerte) ;
			}
			public function InscritAlerte($nom, & $alerte)
			{
				$this->Alerteurs[$nom] = & $alerte ;
				$alerte->DefinitTacheParent($nom, $this) ;
				return $alerte ;
			}
			public function InsereActivite($activite, $position=-1)
			{
				if($position < 0 || $position >= count($this->Activites))
				{
					$index = count($this->Activites) ;
					$this->Activites[] = & $activite ;
				}
				else
				{
					$index = $position ;
					array_splice($this->Activites, $position, 0, $activite) ;
				}
				$activite->DefinitTacheParent($index, $this) ;
				return $activite ;
			}
			public function InscritActivite(& $activite, $position=-1)
			{
				return $this->InsereActivite($activite, $position) ;
			}
			public function CreeSession()
			{
				return new PvSessionTacheSurveilBase() ;
			}
			protected function PrepareAlerteurs()
			{
				foreach($this->Alerteurs as $i => & $alerteur)
				{
					$alerteur->ChargeConfig() ;
				}
			}
			public function Execute()
			{
				$this->PrepareAlerteurs() ;
				parent::Execute() ;
			}
			protected function ExecuteSession()
			{
				$this->Session = $this->CreeSession() ;
				$this->Session->TimestmpDebut = date("U") ;
				foreach($this->Activites as $index => & $activite)
				{
					$activite->ChargeConfig() ;
					$activite->Calcule() ;
				}
				$this->TotalNonConformes = 0 ;
				foreach($this->Activites as $index => & $activite)
				{
					if(! $activite->EstConforme())
					{
						$activite->Corrige() ;
						$activite->Alerte() ;
						$this->TotalNonConformes++ ;
					}
				}
				$this->Session->TimestmpFin = date("U") ;
			}
			protected function ObtientAlerteurNommees($nomAlerteur)
			{
				$alerteurs = array() ;
				foreach($nomAlerteur as $i => $nomAlerte)
				{
					$alerteurs[$nomAlerte] = & $this->Alerteurs[$nomAlerte] ;
				}
				return $alerteurs ;
			}
			protected function ExecuteAlerteurActivite(& $alerteurs, & $activite)
			{
				foreach($alerteurs as $i => $alerteur)
				{
					$alerteur->ExecuteActivite($activite) ;
				}
			}
			public function AlerteActivite(& $activite)
			{
				$alerteurs = $this->ObtientAlerteurNommees($activite->NomAlerteurs) ;
				$this->ExecuteAlerteurActivite($alerteurs, $activite) ;
			}
			protected function EstConforme()
			{
				return $this->TotalNonConformes > 0 ;
			}
		}
		
		class PvSessionTacheSurveilBase
		{
			public $TimestmpDebut = 0 ;
			public $TimestmpFin = 0 ;
		}
		
		class PvActiviteTacheSurveilBase extends PvObjet
		{
			public $TacheParent ;
			public $IndexElementTache ;
			public $Mesure ;
			public $ActCorrect ;
			public $NomAlerteurs = array() ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ActCorrect = $this->CreeActCorrect() ;
			}
			public function DefinitTacheParent($index, & $tache)
			{
				$this->IndexElementTache = $index ;
				$this->TacheParent = & $tache ;
			}
			protected function CreeActCorrect()
			{
				return new PvActCorrectTacheSurveilBase() ;
			}
			protected function CreeMesure()
			{
				return new PvMesureTacheSurveilBase() ;
			}
			protected function EffectueCalcul()
			{
				
			}
			public function Calcule()
			{
				$this->Mesure = $this->CreeMesure() ;
				$this->EffectueCalcul() ;
				return $this->Mesure ;
			}
			public function Corrige()
			{
				if($this->EstPasNul($this->ActCorrect))
				{
					$this->ActCorrect->ExecuteActivite($this) ;
				}
			}
			public function Alerte()
			{
				$this->TacheParent->AlerteActivite($this) ;
			}
			public function EstConforme()
			{
			}
		}
		
		class PvMesureTacheSurveilBase
		{
		}
		
		class PvAlerteurTacheSurveilBase extends PvObjet
		{
			public $NomElementTache ;
			public function ExecuteActivite(& $activite)
			{
				
			}
			public function DefinitTacheParent($index, & $tache)
			{
				$this->IndexElementTache = $index ;
				$this->TacheParent = & $tache ;
			}
			protected function RapporteException($msg)
			{
				echo $msg ;
				exit ;
			}
		}
		class PvAlerteurFichierTacheSurveil extends PvAlerteurTacheSurveilBase
		{
			public $CheminFichier ;
			public function ExecuteActivite(& $activite)
			{
				if($this->CheminFichier == '')
				{
					return $this->RapporteException('Fichier non initialise') ;
				}
				$fh = fopen($this->CheminFichier, "a") ;
				if($fh !== false)
				{
					fputs($fh, $activite->IDInstanceCalc."\r\n") ;
					fputs($fh, $activite->EncodeMesure("plain")."\r\n") ;
					fputs($fh, "\r\n") ;
					fclose($fh) ;
				}
			}
		}
		class PvAlerteurMailerTacheSurveil extends PvAlerteurTacheSurveilBase
		{
			public $Mailer ;
			public $MailSent = -1 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Mailer = new PHPMailer() ;
			}
			public function ExecuteActivite(& $activite)
			{
				$this->Mailer->Subject = "Echec Activite " + $activite->IDInstanceCalc ;
				$this->Mailer->Body = "L'activite de surveillance ".$activite->IDInstanceCalc." a renvoyee une erreur dont voici le detail :\n".$activite->EncodeMesure("plain") ; ;
				$this->MailSent = $this->Mailer->Send() ;
				if($this->MailSent)
				{
					echo "Mail envoye :)\n" ;
				}
				else
				{
					echo "Mail non envoye :'(\n" ;
				}
			}
		}
		
		class PvActCorrectTacheSurveilBase extends PvObjet
		{
			public function ExecuteActivite(& $activite)
			{
				
			}
		}
	}
	
?>