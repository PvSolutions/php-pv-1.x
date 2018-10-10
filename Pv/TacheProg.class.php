<?php
	
	if(! defined('PV_TACHE_PROG'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_TACHE_PROG', 1) ;
		
		class PvActionCtrlBase extends PvObjet
		{
			public $NomElementTacheCtrl ;
			public $TacheCtrlParent ;
			public $ApplicationParent ;
			public function AdopteTacheCtrl($nom, & $tacheCtrl)
			{
				$this->NomElementTacheCtrl = $nom ;
				$this->TacheCtrlParent = & $tacheCtrl ;
				$this->ApplicationParent = & $tacheCtrl->ApplicationParent ;
			}
			public function ExecuteArgs($args)
			{
			}
		}
		
		class PvEtatCtrlBase
		{
			public $Statut = 0 ;
			public $TimestmpCapt = 0 ;
			public $ActionsAttente = array() ;
		}
		
		class PvTacheCtrlBase extends PvTacheProg
		{
			protected $NaturePlateforme = "console" ;
			public $Message = "La tache est terminee" ;
			public $Actions = array() ;
			public $ActionParDefaut ;
			public $Etat ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ArgsParDefaut["action"] = "" ;
				$this->ArgsParDefaut["serv_persist"] = "" ;
				$this->Etat = $this->CreeEtat() ;
			}
			protected function CreeEtat()
			{
				return new PvEtatCtrlBase() ;
			}
			protected function CreeActionParDefaut()
			{
				return new PvActionCtrlBase() ;
			}
			protected function CreeDeclenchParDefaut()
			{
				return new PvDeclenchTjrTache() ;
			}
			public function InscritAction($nom, & $action)
			{
				$this->Actions[$nom] = & $action ;
				$action->AdopteTacheCtrl($nom, $this) ;
			}
			public function & InsereActionParDefaut(& $action)
			{
				$this->ActionParDefaut = & $action ;
				return $action ;
			}
			public function & InsereAction($nom, & $action)
			{
				$this->InscritAction($nom, $action) ;
				return $action ;
			}
			public function Execute()
			{
				$this->ChargeEtat() ;
				$this->ActionParDefaut = $this->CreeActionParDefaut() ;
				$this->ActionParDefaut->AdopteTacheCtrl('par_defaut', $this) ;
				$this->ChargeActions() ;
				parent::Execute() ;
			}
			protected function ChargeActions()
			{
			}
			public function ChemFichEtat()
			{
				if($this->ApplicationParent->ChemRelRegServsPersists == '')
				{
					return null ;
				}
				return dirname(__FILE__)."/../../".$this->ApplicationParent->ChemRelRegServsPersists. DIRECTORY_SEPARATOR . $this->NomElementApplication.".dat" ;
			}
			protected function LitEtat()
			{
				$cheminFich = $this->ChemFichEtat() ;
				if($cheminFich === null)
				{
					return null ;
				}
				if(! file_exists($cheminFich))
				{
					return $this->CreeEtat() ;
				}
				$fh = fopen($cheminFich, "r") ;
				$ctn = '' ;
				if(is_resource($fh))
				{
					$ctn = '' ;
					while(! feof($fh))
					{
						$ctn .= fread($fh, 1024) ;
					}
					fclose($fh) ;
				}
				if($ctn != '')
				{
					$etat = unserialize($ctn) ;
					if(! is_object($etat))
					{
						$etat = $this->CreeEtat() ;
					}
					return $etat ;
				}
				return $this->CreeEtat() ;
			}
			protected function ChargeEtat()
			{
				$this->Etat = $this->LitEtat() ;
			}
			public function SauveEtat()
			{
				$this->Etat->TimestmpCapt = date("U") ;
				$cheminFich = $this->ChemFichEtat() ;
				if($cheminFich === null)
				{
					return false ;
				}
				$fh = fopen($cheminFich, "w") ;
				if(is_resource($fh))
				{
					fputs($fh, serialize($this->Etat)) ;
					fclose($fh) ;
				}
				else
				{
					return false ;
				}
				return true ;
			}
			public function InsereActionAttente($nom, $params=array())
			{
				$codeErreur = '' ;
				if($this->Etat === null)
				{
					return 'fich_actions_attente_introuvable' ;
				}
				$this->Etat->ActionsAttente[] = array($nom, $params) ;
				$ok = $this->SauveEtat() ;
				if($ok == false)
				{
					return 'impossible_ecrire_fich_actions_attente' ;
				}
				return '' ;
			}
			protected function ExecuteSession()
			{
				if($this->Etat != null)
				{
					if(count($this->Etat->ActionsAttente) > 0)
					{
						foreach($this->Etat->ActionsAttente as $i => $infos)
						{
							$this->Actions[$infos[0]]->ExecuteArgs($infos[1]) ;
						}
						$this->Etat->ActionsAttente = array() ;
						$this->SauveEtat() ;
					}
				}
				if($this->Args["action"] != "" && isset($this->Actions[$this->Args["action"]]))
				{
					$this->Actions[$this->Args["action"]]->ExecuteArgs($this->Args) ;
				}
				else
				{
					$this->ActionParDefaut->ExecuteArgs($this->Args) ;
				}
				$this->SauveEtat() ;
				// print_r($this->Etat) ;
				echo $this->Message."\n" ;
			}
		}
		
		class PvCtrlTachesProgsApp extends PvTacheCtrlBase
		{
			public $DelaiTransition = 0 ;
			protected function ExecuteSession()
			{
				$nomTaches = array_keys($this->ApplicationParent->TachesProgs) ;
				foreach($nomTaches as $i => $nomTache)
				{
					$tacheProg = & $this->ApplicationParent->TachesProgs[$nomTache] ;
					if($tacheProg->NomElementApplication == $this->NomElementApplication)
					{
						continue ;
					}
					$tacheProg->LanceProcessus() ;
					if($this->DelaiTransition > 0)
					{
						sleep($this->DelaiTransition) ;
					}
				}
				echo $this->Message."\n" ;
			}
		}
		
		class PvEtatCtrlServsPersists extends PvEtatCtrlBase
		{
			public $ServsPersistsDesact = array() ;
		}
		
		class PvActCtrlDemarrSvcsPersInact extends PvActionCtrlBase
		{
			public function ExecuteArgs($args)
			{
				$nomServsPersists = array_keys($this->ApplicationParent->ServsPersists) ;
				foreach($nomServsPersists as $i => $nomServPersist)
				{
					if($this->TacheCtrlParent->Etat != null && in_array($nomServPersist, $this->TacheCtrlParent->Etat->ServsPersistsDesact))
					{
						continue ;
					}
					$servPersist = & $this->ApplicationParent->ServsPersists[$nomServPersist] ;
					// print get_class($servPersist)." :\n" ;
					if(! $servPersist->EstServiceDemarre() || ! $servPersist->Verifie())
					{
						// echo get_class($servPersist)." doit etre redemarre\n" ;
						$servPersist->DemarreService() ;
						if($this->TacheCtrlParent->DelaiTransition > 0)
						{
							sleep($this->TacheCtrlParent->DelaiTransition) ;
						}
					}
				}
			}
		}
		
		class PvActCtrlManipSvcPers extends PvActionCtrlBase
		{
			public $ServPersistSelect ;
			protected function ExtraitNomServPersist($args)
			{
				$result = "" ;
				if(isset($args["serv_persist"]) && isset($this->TacheCtrlParent->ApplicationParent->ServsPersists[$args["serv_persist"]]))
				{
					$result = $args["serv_persist"] ;
				}
				return $result ;
			}
			public function ExecuteArgs($args)
			{
				$nomServPersist = $this->ExtraitNomServPersist($args) ;
				if($nomServPersist == "")
				{
					return ;
				}
				$this->ServPersistSelect = & $this->TacheCtrlParent->ApplicationParent->ServsPersists[$nomServPersist] ;
				$this->ExecuteManip($args) ;
			}
			protected function ExecuteManip($args)
			{
			}
		}
		class PvActCtrlDemarreSvcPers extends PvActCtrlManipSvcPers
		{
			protected function ExecuteManip($args)
			{
				$this->ServPersistSelect->DemarreService() ;
				$this->TacheCtrlParent->ActiveServPersist($this->ServPersistSelect->NomElementApplication) ;
			}
		}
		class PvActCtrlArreteSvcPers extends PvActCtrlManipSvcPers
		{
			protected function ExecuteManip($args)
			{
				$this->ServPersistSelect->ArreteService() ;
				$this->TacheCtrlParent->DesactiveServPersist($this->ServPersistSelect->NomElementApplication) ;
			}
		}
		
		class PvActCtrlDemarrTousSvcsPers extends PvActionCtrlBase
		{
			public function ExecuteArgs($args)
			{
				$nomServsPersists = array_keys($this->TacheCtrlParent->ApplicationParent->ServsPersists) ;
				foreach($nomServsPersists as $i => $nomServPersist)
				{
					$servPersist = & $this->ApplicationParent->ServsPersists[$nomServPersist] ;
					$servPersist->ArreteService() ;
				}
				$this->TacheCtrlParent->Etat->ServsPersistsDesact = array() ;
				$this->TacheCtrlParent->SauveEtat() ;
			}
		}
		class PvActCtrlArretTousSvcsPers extends PvActionCtrlBase
		{
			public function ExecuteArgs($args)
			{
				$nomServsPersists = array_keys($this->TacheCtrlParent->ApplicationParent->ServsPersists) ;
				foreach($nomServsPersists as $i => $nomServPersist)
				{
					$servPersist = & $this->ApplicationParent->ServsPersists[$nomServPersist] ;
					$servPersist->ArreteService() ;
					if(in_array($nomServPersist, $this->TacheCtrlParent->Etat->ServsPersistsDesact))
					{
						continue ;
					}
					$this->TacheCtrlParent->Etat->ServsPersistsDesact[] = $nomServPersist ;
				}
				$this->TacheCtrlParent->SauveEtat() ;
			}
		}
		
		class PvCtrlServsPersistsApp extends PvTacheCtrlBase
		{
			public $DelaiTransition = 0 ;
			public function ActiveServPersist($nom)
			{
				if($this->Etat == null)
				{
					return ;
				}
				$index = array_search($nom, $this->Etat->ServsPersistsDesact) ;
				if($index === false)
				{
					return ;
				}
				array_splice($this->Etat->ServsPersistsDesact, $index, 1) ;
				$this->SauveEtat() ;
			}
			public function DesactiveServPersist($nom)
			{
				if($this->Etat == null)
				{
					return ;
				}
				$index = array_search($nom, $this->Etat->ServsPersistsDesact) ;
				if($index !== false)
				{
					return ;
				}
				$this->Etat->ServsPersistsDesact[] = $nom ;
				$this->SauveEtat() ;
			}
			protected function CreeEtat()
			{
				return new PvEtatCtrlServsPersists() ;
			}
			protected function CreeActionParDefaut()
			{
				return new PvActCtrlDemarrSvcsPersInact() ;
			}
			protected function ChargeActions()
			{
				$this->InsereAction("demarre", new PvActCtrlDemarreSvcPers()) ;
				$this->InsereAction("arrete", new PvActCtrlArreteSvcPers()) ;
				$this->InsereAction("demarre_tous", new PvActCtrlDemarrTousSvcsPers()) ;
				$this->InsereAction("arrete_tous", new PvActCtrlArretTousSvcsPers()) ;
			}
			public function RemplitTableauDonnees(& $tabl)
			{
			}
		}
		
		class PvStopServsPersistsApp extends PvTacheCtrlBase
		{
			protected function ExecuteSession()
			{
				$nomServsPersists = array_keys($this->ApplicationParent->ServsPersists) ;
				foreach($nomServsPersists as $i => $nomServPersist)
				{
					$servPersist = & $this->ApplicationParent->ServsPersists[$nomServPersist] ;
					$servPersist->ArreteService() ;
				}
				echo $this->Message."\n" ;
			}
		}
		
		class PvTacheDossierSE extends PvTacheProg
		{
			public $CheminAbsoluDossier ;
			protected $Flux ;
			public $AutoSupprFichier = 1 ;
			public $AnnuleSupprFichier = 0 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				register_shutdown_function(array(& $this, 'FermeFlux'), array()) ;
			}
			protected function ExecuteSession()
			{
				if($this->CheminAbsoluDossier == '' || ! is_dir($this->CheminAbsoluDossier))
				{
					return ;
				}
				if($this->OuvreFlux() !== false)
				{
					while(($nomFichier = readdir($this->Flux)) !== false)
					{
						if($nomFichier == '.' || $nomFichier == '..')
						{
							continue ;
						}
						if($this->AccepteFichier($nomFichier))
						{
							$this->AnnuleSupprFichier = 0 ;
							$this->TraiteFichier($nomFichier) ;
							if($this->AutoSupprFichier && ! $this->AnnuleSupprFichier)
							{
								$cheminFichier = $this->CheminAbsoluFichier.'/'.$nomFichier ;
								if(! is_dir($cheminFichier))
								{
									unlink($cheminFichier) ;
								}
							}
						}
					}
					$this->FermeFlux() ;
				}
			}
			protected function TraiteFichier($nomFichier)
			{
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
		
		class PvTacheProgTachesZoneWeb extends PvTacheProg
		{
			public $NomsZone = array() ;
			protected function ExecuteSession()
			{
				echo "Demarrage des verifications...\n" ;
				foreach($this->NomsZone as $i => $nomZone)
				{
					if(! isset($this->ApplicationParent->IHMs[$nomZone]))
					{
						continue ;
					}
					$zone = & $this->ApplicationParent->IHMs[$nomZone] ;
					$zone->ChargeConfig() ;
					$zone->DemarreTachesWeb() ;
				}
				echo "Fin.\n" ;
			}
		}
	}
	
?>