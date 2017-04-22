<?php

	
	if(! defined('PV_NOYAU_PASSERELLE_PAIEMENT'))
	{
		if(! defined('PV_NOYAU_PASSERELLE_SITE_WEB'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		if(! defined('COMMON_HTTP_SESSION_INCLUDED'))
		{
			include dirname(__FILE__)."/../../../../Common/HttpSession.class.php" ;
		}
		define('PV_NOYAU_PASSERELLE_PAIEMENT', 1) ;
		
		class PvEtatExecPaiement
		{
			public $Id ;
			public $TimestampCapt ;
			public $MessageErreur ;
			public function __construct()
			{
				$this->MessageErreur = "" ;
				$this->Id = "non_demarre" ;
				$this->TimestampCapt = date("U") ;
			}
		}
		
		class PvSvcAprPaiementBase
		{
			public $NomElementInterfPaiemt ;
			public $InterfPaiemtParent ;
			public function AdopteInterfPaiemt($nom, & $interf)
			{
				$this->NomElementInterfPaiemt = $nom ;
				$this->InterfPaiemtParent = & $interf ;
			}
			public function Execute(& $transaction)
			{
			}
		}
		
		class PvCfgTransactPaiement
		{
			public $NomSvcAprPaiement ;
		}
		
		class PvTransactPaiementBase
		{
			public $IdDonnees ;
			public $IdTransaction ;
			public $Designation ;
			public $Montant ;
			public $Monnaie ;
			public $InfosSuppl ;
			public $ContenuRetourBrut = null ;
			public $Cfg = null ;
			public function __construct()
			{
				$this->IdTransaction = uniqid() ;
				$this->Cfg = new PvCfgTransactPaiement() ;
			}
		}
		class PvCompteMarchandBase
		{
		}
		
		class PvInterfacePaiementBase extends PvPasserelleSiteWebBase
		{
			protected $_SvcsAprPaiement = array() ;
			protected $_EtatExecution = null ;
			protected $_Transaction = null ;
			protected $_CompteMarchand = null ;
			protected $TransactionValidee = 0 ;
			protected $NomParamResultat = "resultat" ;
			protected $ValeurParamResultat = "" ;
			protected $ValeurParamTermine = "paiement_termine" ;
			protected $ValeurParamAnnule = "paiement_annule" ;
			protected $EnregistrerTransaction = 0 ;
			protected $NomTableTransaction = "transaction_paiement" ;
			public $CheminRelatifRepTransacts = "." ;
			public function CheminRepTransacts()
			{
				return realpath(dirname(__FILE__)."/../../../../../".$this->CheminRelatifRepTransacts) ;
			}
			protected function CreeBdTransaction()
			{
				return new AbstractSqlDB() ;
			}
			protected function LgnDonneesTransact()
			{
				return array(
					"id_transaction" => $this->_Transaction->IdTransaction,
					"designation" => $this->_Transaction->Designation,
					"montant" => $this->_Transaction->Montant,
					"monnaie" => $this->_Transaction->Monnaie,
					"nom_fournisseur" => $this->NomFournisseur(),
					"contenu_brut" => serialize($this->_Transaction),
					"id_etat" => $this->_EtatExecution->Id,
					"timestamp_etat" => $this->_EtatExecution->TimestampCapt,
					"msg_erreur_etat" => $this->_EtatExecution->MessageErreur,
				) ;
			}
			protected function ExporteFichCfgTransition()
			{
				$cheminFich = $this->CheminRepTransacts()."/".$this->_Transaction->IdTransaction.".dat" ;
				$resFich = fopen($cheminFich, "w") ;
				if($resFich != false)
				{
					fputs($resFich, serialize($this->_Transaction->Cfg)) ;
					fclose($resFich) ;
				}
			}
			protected function ImporteFichCfgTransition()
			{
				$cheminFich = $this->CheminRepTransacts()."/".$this->_Transaction->IdTransaction.".dat" ;
				$ctnFich = '' ;
				if(file_exists($cheminFich))
				{
					$resFich = fopen($cheminFich, "r") ;
					if($resFich !== false)
					{
						while(! feof($resFich))
						{
							$ctnFich .= fgets($resFich) ;
						}
						fclose($resFich) ;
						unlink($cheminFich) ;
					}
				}
				if($ctnFich != "")
				{
					$this->_Transaction->Cfg = unserialize($ctnFich) ;
				}
			}
			protected function SauveTransaction()
			{
				$bd = $this->CreeBdTransaction() ;
				if($this->_Transaction->IdDonnees == "")
				{
					$lgnTransact = $bd->FetchSqlRow("select * from ".$bd->EscapeTableName($this->NomTableTransaction)." where id_transaction=:idTransact", array("idTransact" => $this->_Transaction->IdTransaction)) ;
					if(is_array($lgnTransact) && count($lgnTransact) > 0)
					{
						$this->_Transaction->IdDonnees = $lgnTransact["id"] ;
					}
				}
				if($this->_Transaction->IdDonnees == "")
				{
					$ok = $bd->InsertRow(
						$this->NomTableTransaction,
						$this->LgnDonneesTransact()
					) ;
				}
				else
				{
					$ok = $bd->UpdateRow(
						$this->NomTableTransaction,
						$this->LgnDonneesTransact(),
						"id = :id",
						array("id" => $this->_Transaction->IdDonnees)
					) ;
					
				}
			}
			public function NomFournisseur()
			{
				return "base" ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->_EtatExecution = new PvEtatExecPaiement() ;
				$this->_CompteMarchand = $this->CreeCompteMarchand() ;
				$this->_Transaction = $this->CreeTransaction() ;
			}
			public function InsereSvcAprPaiement($nom, $svc)
			{
				$this->InscritSvcAprPaiement($nom, $svc) ;
				return $svc ;
			}
			public function InscritSvcAprPaiement($nom, & $svc)
			{
				$this->_SvcsAprPaiement[$nom] = & $svc ;
				$svc->AdopteInterfPaiemt($nom, $this) ;
			}
			public function & SvcsAprPaiement()
			{
				return $this->_SvcsAprPaiement ;
			}
			public function UrlPaiementTermine()
			{
				return $this->UrlRacine()."?".$this->NomParamResultat."=".urlencode($this->ValeurParamTermine) ;
			}
			public function UrlPaiementAnnule()
			{
				return $this->UrlRacine()."?".$this->NomParamResultat."=".urlencode($this->ValeurParamAnnule) ;
			}
			protected function CreeTransaction()
			{
				return new PvTransactPaiementBase() ;
			}
			protected function CreeCompteMarchand()
			{
				return new PvCompteMarchandBase() ;
			}
			public function & Transaction()
			{
				return $this->_Transaction ;
			}
			public function & CompteMarchand()
			{
				return $this->_CompteMarchand ;
			}
			protected function DefinitEtatExecution($id, $msgErreur="")
			{
				$this->_EtatExecution->Id = $id ;
				$this->_EtatExecution->TimestampCapt = date("U") ;
				$this->_EtatExecution->MessageErreur = $msgErreur ;
				if($this->EnregistrerTransaction == 1)
				{
					$this->SauveTransaction() ;
				}
			}
			protected function DefinitEtatExec($id, $msgErreur="")
			{
				$this->DefinitEtatExecution($id, $msgErreur) ;
			}
			public function IdEtatExecution()
			{
				return $this->_EtatExecution->Id ;
			}
			public function MsgEtatExecution()
			{
				return $this->_EtatExecution->MessageErreur ;
			}
			public function IdEtatExec()
			{
				return $this->IdEtatExecution() ;
			}
			public function TimetampCaptTransact()
			{
				return $this->_EtatExecution->TimestampCapt ;
			}
			protected function DetermineResultatPaiement()
			{
				$this->ValeurParamResultat = "" ;
				if(isset($_GET[$this->NomParamResultat]) && in_array($_GET[$this->NomParamResultat], array($this->ValeurParamTermine, $this->ValeurParamAnnule)))
				{
					$this->ValeurParamResultat = $_GET[$this->NomParamResultat] ;
				}
			}
			protected function RestaureTransactionEnCours()
			{
				$this->DetermineResultatPaiement() ;
				if($this->ValeurParamResultat == $this->ValeurParamTermine)
				{
					$this->DefinitEtatExecution("termine") ;
				}
			}
			protected function DetermineTransactionSoumise()
			{
				$envoyerErr = 0 ;
				if(isset($_SESSION[$this->IDInstanceCalc."_Transaction"]))
				{
					$this->_Transaction = @unserialize($_SESSION[$this->IDInstanceCalc."_Transaction"]) ;
					unset($_SESSION[$this->IDInstanceCalc."_Transaction"]) ;
				}
				else
				{
					$envoyerErr = 1 ;
				}
				if($this->_Transaction == null)
				{
					$envoyerErr = 1 ;
				}
				if($envoyerErr)
				{
					Header("HTTP/1.0 401 Unauthorized Transaction invalide\r\n") ;
					exit ;
				}
				else
				{
					$this->ExporteFichCfgTransition() ;
				}
			}
			protected function PrepareTransaction()
			{
			}
			protected function SoumetTransaction()
			{
				
			}
			protected function TermineTransaction()
			{
			}
			protected function TransactionEnCours()
			{
			}
			protected function TransactionEffectuee()
			{
				return $this->_Transaction->Id == "termine" || $this->TransactionReussie() || $this->TransactionEchouee() || $this->TransactionAnnulee() ;
			}
			protected function TransactionReussie()
			{
				return $this->_Transaction->Id == "paiement_reussi" ;
			}
			protected function TransactionEchouee()
			{
				return $this->_Transaction->Id == "paiement_echoue" || $this->_Transaction->Id == "exception_paiement" ;
			}
			protected function TransactionAnnulee()
			{
				return $this->_Transaction->Id != "annule" ;
			}
			protected function ConfirmeTransactionReussieAuto()
			{
				$this->ImporteFichCfgTransition() ;
				if($this->_Transaction->Cfg->NomSvcAprPaiement != '' && isset($this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement]))
				{
					$svcAprPaiement = & $this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement] ;
					$svcAprPaiement->Execute($this->_Transaction) ;
				}
			}
			protected function ConfirmeTransactionReussie()
			{
			}
			protected function ConfirmeTransactionEchouee()
			{
			}
			protected function ConfirmeTransactionAnnulee()
			{
			}
			protected function ConfirmeTransactionEnAttente()
			{
			}
			protected function ConfirmeTransactionInvalide()
			{
			}
			protected function ValideVerifTransact()
			{
				$this->DefinitEtatExecution("verification_ok") ;
			}
			protected function AfficheErreursTransaction()
			{
				if($this->_EtatExecution->Id != "paiement_reussi")
				{
					Header("HTTP/1.0 401 ".$this->_EtatExecution->Id." ".$this->_EtatExecution->MessageErreur."\r\n") ;
					exit ;
				}
			}
			public function Execute()
			{
				$this->RestaureTransactionEnCours() ;
				if($this->_Transaction->Montant != "")
				{
					if($this->TransactionEnCours())
					{
						return ;
					}
					if($this->TransactionEffectuee())
					{
						if($this->TransactionReussie())
						{
							$this->ConfirmeTransactionReussieAuto() ;
							$this->ConfirmeTransactionReussie() ;
						}
						else
						{
							$this->ConfirmeTransactionEchouee() ;
						}
					}
					elseif($this->TransactionAnnulee())
					{
						$this->ConfirmeTransactionAnnulee() ;
					}
					else
					{
						$this->ConfirmeTransactionEnAttente() ;
					}
					$this->TermineTransaction() ;
					return ;
				}
				$this->DefinitEtatExecution("verification_en_cours") ;
				$this->DetermineTransactionSoumise() ;
				$this->PrepareTransaction() ;
				if($this->_EtatExecution->Id == "verification_ok")
				{
					$this->SoumetTransaction() ;
				}
				else
				{
					$this->ConfirmeTransactionInvalide() ;
					$this->AfficheErreursTransaction() ;
				}
			}
			public function DemarreProcessus()
			{
				$_SESSION[$this->IDInstanceCalc."_Transaction"] = serialize($this->_Transaction) ;
				redirect_to($this->UrlRacine()) ;
			}
		}
	}
	
?>