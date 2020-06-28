<?php
	
	if(! defined('PV_PASSERELLE_PAIEMENT_SKRILL'))
	{
		if(! defined('PV_NOYAU_PASSERELLE_PAIEMENT'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_PASSERELLE_PAIEMENT_SKRILL', 1) ;
		
		class PvTransactSkrill extends PvTransactPaiementBase
		{
			public $SessionId ;
			public $IdMarchand ;
			public $IdTransactSkrill ;
			public $EmailMarchand ;
			public $EmailPayeur ;
		}
		class PvCompteMarchandSkrill extends PvCompteMarchandBase
		{
			public $EmailBenef ;
			public $Monnaie = "EUR" ;
			public $TauxChange = 665 ;
		}
		
		class PvInterfacePaiementSkrill extends PvInterfacePaiementBase
		{
			public $EmailBenefCompteMarchand = "demoqco@sun-fish.com" ;
			public $Titre = "Skrill" ;
			public $UrlSession = "https://pay.skrill.com/" ;
			public $CheminImage = "https://www.skrill.com/fileadmin/content/images/brand_centre/Skrill_Logos/skrill-200x87_en.gif" ;
			public $EnregistrerTransactSkrill = 1 ;
			public $NomTableTransactSkrill = "transaction_skrill" ;
			public function CreeBdSkrill()
			{
				return $this->CreeBdTransaction() ;
			}
			public function NomFournisseur()
			{
				return "skrill" ;
			}
			protected function CreeTransaction()
			{
				return new PvTransactSkrill() ;
			}
			protected function CreeCompteMarchand()
			{
				$compte = new PvCompteMarchandSkrill() ;
				$compte->EmailBenef = $this->EmailBenefCompteMarchand ;
				return $compte ;
			}
			protected function PrepareTransaction()
			{
				parent::PrepareTransaction() ;
				if($this->_EtatExecution->Id != "verification_en_cours")
				{
					return ;
				}
				if($this->_Transaction->TauxChange > 0)
				{
					$this->_Transaction->Monnaie = "EUR" ;
					$this->_Transaction->Montant = round($this->_Transaction->Montant / $this->_Transaction->TauxChange, 2) ;
				}
				$idControle = rand(0, 999999) ;
				$httpSess = new HttpSession() ;
				$resultat = $httpSess->PostData(
					$this->UrlSession,
					array(
						"transaction_id" => $this->_Transaction->IdTransaction,
						"language" => $this->_Transaction->Langage,
						"amount" => $this->_Transaction->Montant,
						"currency" => $this->_Transaction->Monnaie,
						"prepare_only" => 1,
						"pay_to_email" => $this->_CompteMarchand->EmailBenef,
						"detail1_text" => $this->_Transaction->Designation,
						"merchant_fields" => "id_ctrl",
						"id_ctrl" => $idControle,
						"return_url" => $this->UrlPaiementTermine(),
						"status_url" => $this->UrlRacine()."?".$this->NomParamResultat."=maj_statut",
						"cancel_url" => $this->UrlPaiementAnnule(),
					)
				) ;
				if(empty($resultat))
				{
					$this->DefinitEtatExecution("verification_echoue", "Echec sur la session : ".($httpSess->RequestException != '') ? $httpSess->RequestException : '') ;
					$codeErrSession = -1 ;
					$msgErrSession = "EMPTY_CONTENT_RETURNED" ;
				}
				else
				{
					if($httpSess->ResponseHttpStatusCode != 200)
					{
						$this->DefinitEtatExecution("verification_echoue", "Impossible de decoder le contenu HTML de la session") ;
						$codeErrSession = -2 ;
						$msgErrSession = "WRONG_CONTENT_RETURNED" ;
					}
					else
					{
						$codeErrSession = 0 ;
						$valSession = $resultat ;
						$this->_Transaction->SessionId = $valSession ;
						$this->DefinitEtatExecution("verification_ok") ;
					}
				}
				if($codeErrSession !== 0)
				{
					$this->DefinitEtatExecution("verification_echoue", $msgErrSession) ;
				}
				$bd = $this->CreeBdSkrill() ;
				$ok = $bd->RunSql(
					"insert into ".$bd->EscapeTableName($this->NomTableTransactSkrill)." (id_transaction, date_session, id_controle, ctn_req_session, ctn_res_session, valeur_session, code_err_session, msg_err_session)
values (".$bd->ParamPrefix."idTransact, ".$bd->SqlNow().", ".$bd->ParamPrefix."idCtrl, ".$bd->ParamPrefix."ctnReqSession, ".$bd->ParamPrefix."ctnResSession, ".$bd->ParamPrefix."valSession, ".$bd->ParamPrefix."codeErrSession, ".$bd->ParamPrefix."msgErrSession)",
					array(
						"idTransact" => $this->_Transaction->IdTransaction,
						"idCtrl" => $idControle,
						"ctnReqSession" => $httpSess->GetRequestContents(),
						"ctnResSession" => $httpSess->GetResponseContents(),
						"valSession" => $this->_Transaction->SessionId,
						"codeErrSession" => $codeSession,
						"msgErrSession" => $msgErrSession,
					)
				) ;
				if(! $ok)
				{
					$this->DefinitEtatExecution("verification_echoue", "Exception SQL : ".$bd->ConnectionException) ;
				}
			}
			protected function SoumetTransaction()
			{
				redirect_to($this->UrlSession.'?sid='.urlencode($this->_Transaction->SessionId)) ;
			}
			protected function DetermineResultatPaiement()
			{
				parent::DetermineResultatPaiement() ;
				if($this->ValeurParamResultat == '' && isset($_GET[$this->NomParamResultat]))
				{
					if($_GET[$this->NomParamResultat] == "maj_statut")
					{
						$this->ValeurParamResultat = "maj_statut" ;
						$this->AnalyseTransactionRecue() ;
					}
				}
			}
			protected function TransactionEnCours()
			{
				return ($this->ValeurParamResultat == "maj_statut") ;
			}
			protected function RestaureTransactionEnCours()
			{
				parent::RestaureTransactionEnCours() ;
				if($this->IdEtatExecution() == "termine")
				{
					$this->ConfirmeTransactionRecue() ;
				}
				elseif($this->IdEtatExecution() == "annule")
				{
					$bd = $this->CreeBdSkrill() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactSkrill)." set date_annule=".$bd->SqlNow().", est_annule=1 where id_transaction=:idTransact",
						array(
							"idTransact" => $this->_Transaction->IdTransaction
						)
					) ;
				}
			}
			protected function AnalyseTransactionRecue()
			{
				if(! isset($_POST["transaction_id"]) || ! isset($_POST["status"]))
				{
					return ;
				}
				if($_POST["status"] != 2 && $_POST["status"] != -2)
				{
					return ;
				}
				$idControle = $_POST["id_ctrl"] ;
				$bd = $this->CreeBdSkrill() ;
				$lgnVerif = $bd->FetchSqlRow(
					'select id_transaction from '.$bd->EscapeTableName($this->NomTableTransactSkrill).' where id_controle=:idCtrl and id_transaction=:idTransact', array("idCtrl" => $idControle, "idTransact" => $_POST["transaction_id"])
				) ;
				if(! is_array($lgnVerif) || count($lgnVerif) == 0)
				{
					return ;
				}
				$this->_Transaction->IdTransaction = $_POST["transaction_id"] ;
				$this->ImporteFichCfgTransaction() ;
				$this->_Transaction->IdTransactSkrill = $_POST["mb_transaction_id"] ;
				$this->_Transaction->EmailPayeur = $_POST["pay_from_email"] ;
				$this->_Transaction->EmailMarchand = $_POST["pay_to_email"] ;
				$this->_Transaction->Montant = $_POST["mb_amount"] ;
				$this->_Transaction->Monnaie = $_POST["mb_currency"] ;
				$this->_Transaction->IdMarchand = $_POST["merchant_id"] ;
				$this->_Transaction->TypePaiement = $_POST["payment_type"] ;
				$ok = $bd->RunSql(
					"update ".$bd->EscapeTableName($this->NomTableTransactSkrill)." set date_statut=".$bd->SqlNow().", ctn_res_statut=".$bd->ParamPrefix."ctnResStatut, valeur_statut=".$bd->ParamPrefix."valStatut, est_regle=".$bd->ParamPrefix."estRegle where id_transaction=:idTransact",
					array(
						"idTransact" => $this->_Transaction->IdTransaction,
						"ctnResStatut" => file_get_contents('php://input'),
						"valStatut" => $_POST["status"],
						"estRegle" => ($_POST["status"] == 2) ? 1 : 0,
					)
				) ;
				if(! $ok)
				{
					$this->DefinitEtatExecution("paiement_exception", $bd->ConnectionException) ;
				}
			}
			protected function ConfirmeTransactionRecue()
			{
				$bd = $this->CreeBdSkrill() ;
				$lgnVerif = $bd->FetchSqlRow(
					'select id_transaction, est_regle from '.$bd->EscapeTableName($this->NomTableTransactSkrill).' where (valeur_statut=2 or valeur_statut=-2) and id_transaction=:idTransact and date_termine is null',
					array("idTransact" => $this->_Transaction->IdTransaction)
				) ;
				if(! is_array($lgnVerif))
				{
					$this->DefinitEtatExecution("paiement_exception", "Erreur SQL : ".$bd->ConnectionException) ;
					return ;
				}
				if(count($lgnVerif) == 0)
				{
					return ;
				}
				$ok = $bd->RunSql("update ".$bd->EscapeTableName($this->NomTableTransactSkrill)." set date_termine=".$bd->SqlNow()." where id_transaction=:id_transaction", array("id_transaction" => $this->_Transaction->IdTransaction)) ;
				if($ok)
				{
					if($lgnVerif["est_regle"] == 1)
					{
						$this->DefinitEtatExecution("paiement_reussi") ;
					}
					else
					{
						$this->DefinitEtatExecution("paiement_echoue", $bd->ConnectionException) ;
					}
				}
			}
			protected function ControleTransactionEnAttente()
			{
				$this->DefinitEtatExecution("paiement_expire") ;
				$bd = $this->CreeBdSkrill() ;
				$bd->RunSql(
					"update ".$bd->EscapeTableName($this->NomTableTransactSkrill)." set date_annule=".$bd->SqlNow().", est_annule=1 where id_transaction=:idTransact",
					array(
						"idTransact" => $this->_Transaction->IdTransaction
					)
				) ;
			}
		}
	}
	
?>