<?php
	
	if(! defined('PV_SERVICE_PERSISTANT'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_SERVICE_PERSISTANT', 1) ;
		
		class PvPaquetSocket
		{
			public $ContenuBrut ;
			public $Contenu ;
		}
		
		class PvFormatPaquetBase
		{
			public function Encode($contenu)
			{
				return null ;
			}
			public function Decode($contenu)
			{
				return null ;
			}
		}
		class PvFormatPaquetBrut extends PvFormatPaquetBase
		{
			public function Decode($contenu)
			{
				return $contenu ;
			}
			public function Encode($contenu)
			{
				return $contenu ;
			}
		}
		class PvFormatPaquetNatif extends PvFormatPaquetBase
		{
			public function Decode($contenu)
			{
				$resultat = false ;
				@eval('$resultat = '.$contenu.' ;') ;
				return $resultat ;
			}
			public function Encode($contenu)
			{
				return var_export($contenu) ;
			}
		}
		class PvFormatPaquetJSON extends PvFormatPaquetBase
		{
			public function Decode($contenu)
			{
				return @svc_json_decode($contenu) ;
			}
			public function Encode($contenu)
			{
				return svc_json_encode($contenu) ;
			}
		}
		
		class PvElementQueue
		{
			public $Index = -1 ;
			public $ContenuBrut ;
			public function ImporteContenu($ctnBrut)
			{
				$this->ContenuBrut = $ctnBrut ;
				$this->ImporteContenuInt() ;
			}
			protected function ImporteContenuInt()
			{
			}
		}
		
		class PvProcesseurQueueBase extends PvServicePersist
		{
			public $MaxElements = 20 ;
			public $ElementsBruts = array() ;
			public $ElementActif ;
			protected function CreeElement()
			{
				return new PvElementQueue() ;
			}
			protected function ExecuteSession()
			{
				do
				{
					$this->ElementActif = null ;
					$this->ElementsBruts = array() ;
					$this->ChargeElements() ;
					foreach($this->ElementsBruts as $i => $elemBrut)
					{
						$this->ElementActif = $this->CreeElement() ;
						$this->ElementActif->Index = $i ;
						$this->ElementActif->ImporteContenu($elemBrut) ;
						$this->TraiteElementActif() ;
					}
					$this->VideElements() ;
				}
				while(count($this->ElementsBruts) > 0) ;
			}
			protected function TraiteElementActif()
			{
			}
			protected function ChargeElements()
			{
			}
		}
		class PvProcesseurDossier extends PvProcesseurQueueBase
		{
			public $CheminAbsoluDossier ;
			protected $Flux ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				register_shutdown_function(array(& $this, 'FermeFlux'), array()) ;
			}
			protected function ChargeElements()
			{
				if($this->CheminAbsoluDossier == '' || ! is_dir($this->CheminAbsoluDossier))
				{
					return ;
				}
				if($this->OuvreFlux() !== false)
				{
					while(count($this->ElementsBruts) < $this->MaxElements && ($nomFichier = readdir($this->Flux)) !== false)
					{
						if($nomFichier == '.' || $nomFichier == '..')
						{
							continue ;
						}
						if($this->AccepteFichier($nomFichier))
						{
							$this->ElementsBruts[] = $this->CheminAbsoluDossier.'/'.$nomFichier ;
						}
					}
					$this->FermeFlux() ;
				}
			}
			protected function VideElements()
			{
				foreach($this->ElementsBruts as $i => $cheminFichier)
				{
					unlink($cheminFichier) ;
				}
			}
			protected function OuvreFlux()
			{
				$this->Flux = opendir($this->CheminAbsoluDossier) ;
				return ($this->Flux != false) ;
			}
			public function FermeFlux()
			{
				if(is_resource($this->Flux))
				{
					closedir($this->Flux) ;
					$this->Flux = false ;
				}
			}
			protected function AccepteFichier($nomFichier)
			{
				return 1 ;
			}
		}
		
		class PvErreurOuvrSocket
		{
			public $No ;
			public $Contenu ;
			public function Trouve()
			{
				return $this->No != '' ;
			}
		}
		
		class PvServeurSocketBase extends PvServicePersist
		{
			protected $Flux = false ;
			protected $FluxClient = false ;
			protected $FluxEnvoi = false ;
			public $Scheme = "tcp" ;
			public $Hote = "localhost" ;
			protected $Adresse = "" ;
			public $Port = 6868 ;
			public $DelaiOuvrFlux = 30 ;
			public $DelaiLectFlux = 2 ;
			public $DelaiInactivite = 30 ;
			public $TaillePaquetFlux = 1024 ;
			public $FormatPaquet ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				register_shutdown_function(array(& $this, 'AnnuleFlux')) ;
			}
			protected function CreeFormatPaquet()
			{
				return new PvFormatPaquetNatif() ;
			}
			protected function ExecuteSession()
			{
				$this->FormatPaquet = $this->CreeFormatPaquet() ;
				$this->OuvreFlux() ;
				if($this->ErreurOuvr->Trouve())
				{
					return ;
				}
				$this->PrepareReception() ;
				$this->RecoitDemandes() ;
				$this->TermineReception() ;
				$this->FermeFlux() ;
			}
			protected function PrepareReception()
			{
			}
			protected function TermineReception()
			{
			}
			protected function ExtraitAdresse()
			{
				return $this->Scheme.'://'.$this->Hote.':'.$this->Port ;
			}
			protected function OuvreFlux()
			{
				$this->ErreurOuvr = new PvErreurOuvrSocket() ;
				$this->Adresse = $this->ExtraitAdresse() ;
				$this->Flux = stream_socket_server($this->Adresse, $this->ErreurOuvr->No, $this->ErreurOuvr->Contenu, $this->DelaiOuvrFlux) ;
				if($this->Flux === false && $this->ErreurOuvr->No == 0)
				{
					$this->ErreurOuvr->No = -1 ;
					$this->ErreurOuvr->Contenu = 'Impossible d\'ouvrir une connexion socket' ;
				}
			}
			public function EnvoieDemande($contenu)
			{
				$this->FluxEnvoi = stream_socket_client($this->ExtraitAdresse, $this->Port, $codeErreur, $msgErreur) ;
				$partieResult = '' ;
				$resultat = '' ;
				$longueurMax = 1024 ;
				if($this->FluxEnvoi !== false)
				{
					fputs($this->FluxEnvoi, $this->FormatPaquet->Encode($contenu)) ;
					do
					{
						$partieResult = fgets($this->FluxEnvoi, $longueurMax) ;
						$resultat .= $partieResult ;
					}
					while(strlen($partieResult) == $longueurMax) ;
					$this->FermeFluxEnvoi() ;
				}
				return $this->FormatPaquet->Decode($resultat) ;
			}
			protected function RecoitDemandes()
			{
				while($this->FluxClient = stream_socket_accept($this->SocketHandle, $this->DelaiInactivite))
				{
					$paquet = new PvPaquetSocket() ;
					do
					{
						stream_set_timeout($this->FluxClient, $this->DelaiLectFlux) ;
						$partiePaquet = fgets($this->FluxClient, $this->TaillePaquetFlux) ;
						$paquet->Contenu .= $partiePaquet ;
					}
					while(strlen($partiePaquet) == $this->TaillePaquetFlux && ! feof($this->FluxClient)) ;
					$resultat = $this->TraitePaquet($paquet) ;
					fputs($this->FluxClient, $resultat) ;
					$this->FermeFluxClient() ;
				}
			}
			protected function FermeFluxEnvoi()
			{
				if(is_resource($this->FluxEnvoi))
				{
					fclose($this->FluxEnvoi) ;
					$this->FluxEnvoi = false ;
				}
			}
			protected function FermeFluxClient()
			{
				if(is_resource($this->FluxClient))
				{
					fclose($this->FluxClient) ;
					$this->FluxClient = false ;
				}
			}
			protected function TraitePaquet($paquet)
			{
				$contenuDecode = $this->FormatPaquet->Decode($paquet->Contenu) ;
				$resultat = $this->RepondDemande($paquet, $contenu) ;
				return $this->FormatPaquet->Encode($resultat) ;
			}
			protected function RepondDemande($contenu)
			{
				return null ;
			}
			protected function FermeFlux()
			{
				if(is_resource($this->Flux))
				{
					fclose($this->Flux) ;
					$this->Flux = false ;
				}
			}
			public function AnnuleFlux()
			{
				$this->FermeFluxEnvoi() ;
				$this->FermeFluxClient() ;
				$this->FermeFlux() ;
			}
		}
		
		class PvMethodeSocketBase
		{
			public $ArgsParDefaut = array() ;
			public $Args ;
			public $ArgsBruts ;
			protected $Serveur ;
			protected $ServeurIndef ;
			public $Resultat ;
			public $CodeErreur = 0 ;
			public $NomAppel = "" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ServeurIndef = new PvServeurAppelsSocket() ;
			}
			protected function CreeResultat()
			{
				return null ;
			}
			protected function PrepareExecution(& $serveur, $nom, $args=array())
			{
				$this->Resultat = $this->CreeResultat() ;
				$this->NomAppel = $nom ;
				$this->Args = $this->ArgsParDefaut ;
				$this->ArgsBruts = $args ;
				foreach($this->Args as $nom => $arg)
				{
					if(isset($args[$nom]))
					{
						$this->Args[$nom] = $arg ;
					}
				}
				$this->Serveur = & $serveur ;
			}
			protected function ExecuteInstructions()
			{
			}
			protected function TermineExecution()
			{
				$this->Serveur = & $this->ServeurIndef ;
			}
			public function Execute(& $serveur, $nom, $args=array())
			{
				$this->PrepareExecution($serveur, $nom, $args) ;
				$this->ExecuteInstructions() ;
				$this->TermineExecution() ;
			}
		}
		class PvMethodeSocketTest extends PvMethodeSocketBase
		{
			public $ValeurTest = "OK" ;
			protected function ExecuteInstructions()
			{
				$this->Resultat = $this->ValeurTest ;
			}
		}
		class PvMethodeSocketVerif extends PvMethodeSocketBase
		{
			public $ValeurSucces = 1 ;
			protected function ExecuteInstructions()
			{
				$this->Resultat = $this->ValeurSucces ;
			}
		}
		class PvMethodeSocketNonTrouve extends PvMethodeSocketBase
		{
			public $Message = "le nom de la methode a appeler est invalide" ;
			protected function ExecuteInstructions()
			{
				$this->Resultat = $this->Message ;
				$this->CodeErreur = -1 ;
			}
		}
		
		class PvRetourAppelSocket
		{
			public $resultat ;
			public $codeErreur = 0 ;
			public function erreurTrouvee()
			{
				return $codeErreur != 0 ;
			}
		}
		class PvEnvoiAppelSocket
		{
			public $nom ;
			public $args = array() ;
		}
		
		class PvServeurAppelsSocket extends PvServeurSocketBase
		{
			public $Methodes = array() ;
			public $NomMethodeTest = 'test' ;
			public $NomMethodeVerif = 'verifie' ;
			public $NomMethodeNonTrouve = 'non_trouve' ;
			public $MethodeTest ;
			public $MethodeVerif ;
			public $MethodeNonTrouve ;
			public function CreeEnvoi($nom, $args=array())
			{
				$envoi = new PvEnvoiAppelSocket() ;
				$envoi->nom = $nom ;
				$envoi->args = $args ;
				return $envoi ;
			}
			public function AppelleMethode($nom, $args=array())
			{
				$envoi = $this->CreeEnvoi($nom, $args) ;
				$retour = $this->EnvoieDemande($this->FormatPaquet->Encode($envoi)) ;
				return $retour ;
			}
			public function Test()
			{
				return $this->AppelleMethode($this->NomMethodeTest, array()) ;
			}
			public function Verifie()
			{
				$retour = $this->AppelleMethode($this->NomMethodeVerif, array()) ;
				return $retour->resultat == $this->MethodeVerif->ValeurSucces ;
			}
			protected function CreeMethodeTest()
			{
				return new PvMethodeSocketTest() ;
			}
			protected function CreeMethodeVerif()
			{
				return new PvMethodeSocketVerif() ;
			}
			protected function CreeMethodeNonTrouve()
			{
				return new PvMethodeSocketNonTrouve() ;
			}
			public function InsereMethode($nom, $methode)
			{
				$this->InscritMethode($nom, $methode) ;
				return $methode ;
			}
			public function InscritMethode($nom, & $methode)
			{
				$this->Methode[$nom] = & $appel ;
			}
			protected function ObtientMethodes()
			{
				$methodes = array() ;
				foreach($this->Methodes as $nom => $methode)
				{
					$methodes[$nom] = & $this->Methodes[$nom] ;
				}
				$methodes[$this->NomMethodeNonTrouve] = & $this->MethodeNonTrouve ;
				$methodes[$this->NomMethodeTest] = & $this->MethodeTest ;
				$methodes[$this->NomMethodeVerif] = & $this->MethodeVerif ;
				return $methodes ;
			}
			protected function RepondDemande($contenu)
			{
				$nomMethode = null ;
				$retour = new PvRetourAppelSocket() ;
				$methodes = $this->ObtientMethodes() ;
				if(! is_object($contenu))
				{
					$nomMethode = $this->NomMethodeNonTrouve ;
				}
				else
				{
					if(isset($contenu->nom) && isset($methodes[$contenu->nom]))
					{
						$nomMethode = $contenu->nom ;
					}
					else
					{
						$nomMethode = $this->NomMethodeNonTrouve ;
					}
				}
				$methodes[$nomMethode]->Execute($this, $nomMethode, (isset($contenu->args)) ? $contenu->args : array()) ;
				$retour->resultat = $methodes[$nomMethode]->Resultat ;
				$retour->codeErreur = $methodes[$nomMethode]->CodeErreur ;
				return $retour ;
			}
		}
		
	}
	
?>