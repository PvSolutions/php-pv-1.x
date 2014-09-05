<?php
	
	if(! defined('PV_SERVICE_REQUETE'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_SERVICE_REQUETE', 1) ;
		
		class PvConfigSupportProcReqBase
		{
		}
		class PvErreurProcessRequete
		{
			public $NoInterne = 0 ;
			public $Message = "" ;
			public $ExceptionBrute = null ;
			public function Definit($no, $msg, $except=null)
			{
				$this->NoInterne = $no ;
				$this->Message = $msg ;
				$this->ExceptionBrute = $except ;
			}
			public function EstTrouvee()
			{
				return ($this->NoInterne == 0) ? 0 : 1 ;
			}
		}
		
		class PvProcessRequeteBase extends PvServiceRequete
		{
			public $MethodeNonTrouvee = null ;
			public $Methodes = array() ;
			public $Config = null ;
			public $SupportClt = null ;
			public $Passerelles = array() ;
			public function FermeSupport()
			{
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeMethodes() ;
				$this->ChargePasserelles() ;
			}
			protected function ChargeMethodes()
			{
			}
			protected function ChargePasserelles()
			{
			}
			protected function InitConfig()
			{
				$this->InitMethodeNonTrouvee() ;
			}
			protected function InitMethodeNonTrouvee()
			{
				$this->MethodeNonTrouvee = new PvMethodeNonTrouvee() ;
			}
			protected function CreeErreur()
			{
				return new PvErreurProcessRequete() ;
			}
			protected function CreeConfig()
			{
				return new PvConfigSupportProcReqBase() ;
			}
			public function InscritMethode($nom, & $methode)
			{
				$this->Methodes[$nom] = & $methode ;
				$methode->AdopteProcess($nom, $this) ;
			}
			public function InscritNouvMethode($nom, $methode)
			{
				$this->InscritMethode($nom, $methode) ;
			}
			public function Appelle($nomMethode, $requete)
			{
				if(! isset($this->Methodes[$nomMethode]))
				{
					return $this->MethodeNonTrouvee->Traite($requete) ;
				}
				$methode = & $this->Methodes[$nomMethode] ;
				$reponse = $methode->Traite($requete) ;
				return $reponse ;
			}
		}
		
		class PvMethodeProcessBase extends PvObjet
		{
			public $ApplicationParent ;
			public $ProcessParent ;
			public $NomElementProcess ;
			public $DernTimestmpDebut = null ;
			public $DernTimestmpFin = null ;
			public $DernRequete = null ;
			public $DernReponse = null ;
			public function CreeRequete()
			{
				return new PvRequeteProcessBase() ;
			}
			public function CreeReponse()
			{
				return new PvReponseProcessBase() ;
			}
			public function AdopteProcess($nom, & $process)
			{
				$this->NomElementProcess = $nom ;
				$this->ProcessParent = & $process ;
				$this->ApplicationParent = & $process->ApplicationParent ;
			}
			public function Traite($requete)
			{
				$this->InitTraitement($requete) ;
				$this->ExecuteInstructions() ;
				$this->FinalTraitement() ;
				return $this->DernReponse ;
			}
			protected function InitTraitement($requete)
			{
				$this->DernTimestmpDebut = date("U") ;
				$this->DernTimestmpFin = null ;
				$this->DernRequete = $requete ;
				$this->DernReponse = $this->CreeReponse() ;
			}
			protected function ExecuteInstructions()
			{
			}
			protected function FinalTraitement()
			{
				$this->DernTimestmpFin = date("U") ;
			}
			public function DernTempsTrait()
			{
				if($this->DernTimestmpFin == null)
					return -1 ;
				return $this->DernTimestmpFin - $this->DernTimestmpDebut ;
			}
		}
		class PvMethodeNonTrouvee extends PvMethodeProcessBase
		{
			protected function ExecuteInstructions()
			{
				$this->DernReponse->Erreur->Definit(-1, "La methode que vous demandee n'existe pas dans le service", null) ;
			}
		}
		
		class PvFluxInfoProcessBase
		{
			public function ObtientMembresPubl()
			{
				return call_user_func('get_object_vars', $this);
			}
		}
		class PvRequeteProcessBase extends PvFluxInfoProcessBase
		{
			public $AttrsCorreps = array() ;
			public $CorrespAttrsAuto = 0 ;
			public $CorrespTousAttrsAuto = 1 ;
			public $AttrsNonCorresp = array() ;
			protected function FixeAttrsCorresps($valeurs)
			{
				if($this->CorrespAttrsAuto)
				{
					foreach($this->AttrsCorreps as $i => $nom)
					{
						if(isset($valeurs[$nom]))
						{
							$this->$nom = $valeurs[$nom] ;
						}
					}
				}
				elseif($this->CorrespTousAttrsAuto)
				{
					$membres = $this->ObtientMembresPubl() ;
					foreach($membres as $nom => $val)
					{
						if(isset($valeurs[$nom]) && ! in_array($nom, $this->AttrsNonCorresp))
						{
							$this->$nom = $valeurs[$nom] ;
						}
					}
				}
			}
			public function ImportConfigParVals($valeurs)
			{
				if(! is_array($valeurs))
				{
					return ;
				}
				foreach($valeurs as $nom => $val)
				{
					$this->ImportConfigParVal($nom, $val) ;
				}
			}
			protected function ImportConfigParVal($nom, $val)
			{
				$succes = 0 ;
				return $succes ;
			}
		}
		class PvReponseProcessBase extends PvFluxInfoProcessBase
		{
			public $Erreur ;
			public $AttrsExposes = array() ;
			public $AttrsNonExposes = array() ;
			public $ExposeTousAttrsAuto = 1 ;
			public $ExposeAttrsAuto = 0 ;
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
				$this->Erreur = new PvErreurProcessRequete() ;
			}
			public function PourPasserelle()
			{
				$vals = array() ;
				if($this->ExposeAttrsAuto)
				{
					foreach($this->AttrsExposes as $i => $nom)
					{
						$vals[$nom] = $this->Nom ;
					}
				}
				elseif($this->ExposeTousAttrsAuto)
				{
					$membres = $this->ObtientMembresPubl($this) ;
					foreach($membres as $nom => $val)
					{
						if(! in_array($nom, $this->AttrsNonExposes))
						{
							$vals[$nom] = $val ;
						}
					}
				}
				return $vals ;
			}
		}
		
	}
	
?>