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
				if(empty($contenu))
				{
					return $resultat ;
				}
				$resultat = unserialize($contenu) ;
				return $resultat ;
			}
			public function Encode($contenu)
			{
				if(empty($contenu))
				{
					return "" ;
				}
				return serialize($contenu) ;
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
					$this->SauveEtat() ;
					foreach($this->ElementsBruts as $i => $elemBrut)
					{
						$this->ElementActif = $this->CreeElement() ;
						$this->ElementActif->Index = $i ;
						$this->ElementActif->ImporteContenu($elemBrut) ;
						$this->TraiteElementActif() ;
						$this->SauveEtat() ;
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
			protected function VideElements()
			{
				$this->ElementActif = null ;
				$this->ElementsBruts = array() ;
			}
		}
		class PvProcesseurDossierSE extends PvProcesseurQueueBase
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
			public $Hote = "127.0.0.1" ;
			protected $Adresse = "" ;
			public $Port = 4401 ;
			public $DelaiOuvrFlux = 30 ;
			public $SauveEtatChaqueDemande = 1 ;
			public $DelaiLectFlux = 0 ;
			public $DelaiOuvrEnvoi = 30 ;
			public $DelaiLectEnvoi = 0 ;
			public $LimiterDelaiBoucle = 0 ;
			public $DelaiInactivite = 30 ;
			public $EcartInactiviteBoucle = 5 ;
			public $TaillePaquetFlux = 1024 ;
			public $FormatPaquet ;
			public $DelaiAttente = 0 ;
			public $MaxSessions = 0 ;
			protected $DernErrEnvoiDemande ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->FormatPaquet = $this->CreeFormatPaquet() ;
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
					echo $this->ErreurOuvr->No."# ".$this->ErreurOuvr->Contenu."\n" ;
					exit ;
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
			public function ExtraitAdresse()
			{
				return $this->Scheme.'://'.$this->Hote.':'.$this->Port ;
			}
			protected function OuvreFlux()
			{
				$this->ErreurOuvr = new PvErreurOuvrSocket() ;
				$this->Adresse = $this->ExtraitAdresse() ;
				$this->Flux = stream_socket_server($this->Adresse, $this->ErreurOuvr->No, $this->ErreurOuvr->Contenu) ;
				if($this->Flux === false && $this->ErreurOuvr->No == 0)
				{
					$this->ErreurOuvr->No = -1 ;
					$this->ErreurOuvr->Contenu = 'Impossible d\'ouvrir une connexion socket' ;
				}
			}
			public function EnvoieDemande($contenu)
			{
				$msgErreur = "" ;
				// echo $this->ExtraitAdresse() ;
				$this->FluxEnvoi = stream_socket_client($this->ExtraitAdresse(), $codeErreur, $msgErreur, $this->DelaiOuvrFlux, STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT) ;
				$partieResult = '' ;
				$resultat = '' ;
				$this->DernErrEnvoiDemande = '' ;
				$longueurMax = 1024 ;
				if($this->FluxEnvoi !== false)
				{
					$ctnEncode = $this->FormatPaquet->Encode($contenu) ;
					$ok = true ;
					$msgErreur = null ;
					if($ctnEncode != '')
					{
						try
						{
							$ok = fputs($this->FluxEnvoi, $ctnEncode) ;
						}
						catch(Exception $ex)
						{
							$msgErreur = $ex->getMessage() ;
						}
					}
					if($ok)
					{
						if($this->DelaiLectEnvoi > 0)
						{
							stream_set_timeout($this->FluxEnvoi, $this->DelaiLectEnvoi) ;
						}
						do
						{
							$partieResult = fread($this->FluxEnvoi, $longueurMax) ;
							if($partieResult !== false)
							{
								$resultat .= $partieResult ;
							}
							else
							{
								$this->DernErrEnvoiDemande = "lecture_flux_socket_echoue" ;
								break ;
							}
						}
						while(strlen($partieResult) == $longueurMax) ;
					}
					else
					{
						if($msgErreur != null)
						{
							$this->DernErrEnvoiDemande = $msgErreur ;
						}
						else
						{
							$this->DernErrEnvoiDemande = "ecriture_flux_socket_echoue" ;
						}
					}
					$this->FermeFluxEnvoi() ;
				}
				else
				{
					$this->DernErrEnvoiDemande = $codeErreur.'#'.$msgErreur ;
				}
				return $this->FormatPaquet->Decode($resultat) ;
			}
			public function ObtientErrEnvoiDemande()
			{
				return $this->DernErrEnvoiDemande ;
			}
			protected function RecoitDemandes()
			{
				$delaiInactivite = ($this->LimiterDelaiBoucle) ? $this->DelaiBoucle - $this->EcartInactiviteBoucle : $this->DelaiInactivite ;
				// print_r(get_resource_type($this->Flux)) ;
				while($this->FluxClient = @stream_socket_accept($this->Flux, $delaiInactivite))
				{
					$paquet = new PvPaquetSocket() ;
					if($this->DelaiLectFlux > 0)
					{
						stream_set_timeout($this->FluxClient, $this->DelaiLectFlux) ;
					}
					do
					{
						$partiePaquet = fread($this->FluxClient, $this->TaillePaquetFlux) ;
						$paquet->Contenu .= $partiePaquet ;
					}
					while(strlen($partiePaquet) == $this->TaillePaquetFlux && ! feof($this->FluxClient)) ;
					$resultat = $this->TraitePaquet($paquet) ;
					fputs($this->FluxClient, $resultat) ;
					$this->FermeFluxClient() ;
					if($this->SauveEtatChaqueDemande == 1)
					{
						$this->SauveEtat() ;
					}
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
				// print "Decode : ".$paquet->Contenu."\n\t".$contenuDecode."\n" ;
				$resultat = $this->RepondDemande($contenuDecode) ;
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
			public $RetourAppel ;
			public $NomAppel = "" ;
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
				$this->ServeurIndef = new PvServeurAppelsSocket() ;
				$this->RetourAppel = $this->CreeRetourAppel() ;
			}
			protected function CreeRetourAppel()
			{
				return new PvRetourAppelSocket() ;
			}
			protected function PrepareExecution(& $serveur, $nom, $args=array())
			{
				$this->RetourAppel = $this->CreeRetourAppel() ;
				$this->NomAppel = $nom ;
				$this->ArgsBruts = $args ;
				$this->Args = $this->ArgsParDefaut ;
				foreach($this->Args as $nom => $arg)
				{
					if(is_object($args) && isset($args->$nom))
					{
						$this->Args[$nom] = $args->$nom ;
					}
					elseif(is_array($args) && isset($args[$nom]))
					{
						$this->Args[$nom] = $args[$nom] ;
					}
				}
				// print_r($args) ;
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
			protected function EstErreur()
			{
				return $this->RetourAppel->erreurTrouvee() ;
			}
			protected function ErreurTrouvee()
			{
				return $this->RetourAppel->erreurTrouvee() ;
			}
			protected function EstSucces()
			{
				return $this->RetourAppel->succes() ;
			}
			protected function ConfirmeSucces($msg, $resultat=null)
			{
				$this->RetourAppel->codeErreur = 0 ;
				$this->RetourAppel->message = $msg ;
				$this->RetourAppel->resultat = $resultat ;
			}
			protected function SignaleErreur($code, $msg, $resultat=null)
			{
				$this->RetourAppel->codeErreur = $code ;
				$this->RetourAppel->message = $msg ;
				$this->RetourAppel->resultat = $resultat ;
			}
			protected function RenseigneErreur($code, $msg, $resultat=null)
			{
				return $this->SignaleErreur($code, $msg, $resultat) ;
			}
		}
		class PvMethodeSocketTest extends PvMethodeSocketBase
		{
			public $MessageTest = "Test reussi" ;
			public $ValeurTest = "OK" ;
			protected function ExecuteInstructions()
			{
				$this->ConfirmeSucces($this->MessageTest, $this->ValeurTest) ;
			}
		}
		class PvMethodeSocketVerif extends PvMethodeSocketBase
		{
			protected function ExecuteInstructions()
			{
				$this->ConfirmeSucces('Tests effectues avec succes') ;
			}
		}
		class PvMethodeSocketNonTrouve extends PvMethodeSocketBase
		{
			public $MessageRetour = "le nom de la methode a appeler est invalide" ;
			public $CodeRetour = -1 ;
			protected function ExecuteInstructions()
			{
				$this->SignaleErreur($this->CodeRetour, $this->MessageRetour) ;
			}
		}
		
		class PvRetourAppelSocket
		{
			public $message = "resultat non defini" ;
			public $resultat ;
			public $codeErreur = -1 ;
			public function succes()
			{
				return $this->codeErreur == 0 ;
			}
			public function erreurTrouvee()
			{
				return $this->codeErreur != 0 ;
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
				$retour = $this->EnvoieDemande($envoi) ;
				return $retour ;
			}
			public function Test()
			{
				return $this->AppelleMethode($this->NomMethodeTest, array()) ;
			}
			public function Verifie()
			{
				$retour = $this->AppelleMethode($this->NomMethodeVerif, array()) ;
				// print 'Retour : '.print_r($retour, true)."\n" ;
				// $methodes = $this->ObtientMethodes() ;
				if(! is_object($retour))
					return false ;
				return $retour->succes() ;
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
			public function & InsereMethode($nom, $methode)
			{
				$this->InscritMethode($nom, $methode) ;
				return $methode ;
			}
			public function InscritMethode($nom, & $methode)
			{
				$this->Methodes[$nom] = & $methode ;
			}
			protected function ObtientMethodes()
			{
				$methodes = array() ;
				foreach($this->Methodes as $nom => $methode)
				{
					$methodes[$nom] = & $this->Methodes[$nom] ;
				}
				$methodes[$this->NomMethodeNonTrouve] = $this->CreeMethodeNonTrouve() ;
				$methodes[$this->NomMethodeTest] = $this->CreeMethodeTest() ;
				$methodes[$this->NomMethodeVerif] = $this->CreeMethodeVerif() ;
				return $methodes ;
			}
			protected function RepondDemande($contenu)
			{
				$nomMethode = null ;
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
				// echo "Methode : ".$nomMethode."\n" ;
				$methodes[$nomMethode]->Execute($this, $nomMethode, (isset($contenu->args)) ? $contenu->args : array()) ;
				$retour = $methodes[$nomMethode]->RetourAppel ;
				return $retour ;
			}
		}
		
		class PvServiceProcessus extends PvServicePersist
		{
			public function EstActif($cheminFichierAbsolu, $cheminFichierElementActif)
			{
				$ok = parent::EstActif($cheminFichierAbsolu, $cheminFichierElementActif) ;
				if(! $ok)
				{
					return $ok ;
				}
				$ok = true ;
				foreach($this->ArgsParDefaut as $nom => $valeur)
				{
					if(! isset($this->Args[$nom]) || $this->ArgsParDefaut[$nom] != $this->Args[$nom])
					{
						$ok = false ;
						break ;
					}
				}
				return $ok ;
			}
		}
		
	}
	
?>