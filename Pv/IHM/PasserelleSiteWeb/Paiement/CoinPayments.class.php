<?php
	
	if(! defined('PV_PASSERELLE_COIN_PAYMENTS'))
	{
		if(! defined('PV_NOYAU_PASSERELLE_PAIEMENT'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_PASSERELLE_COIN_PAYMENTS', 1) ;
		
		class PvTransactCoinPayments extends PvTransactPaiementBase
		{
			public $Langage = "fr" ;
		}
		class PvCompteMarchandCoinPayments extends PvCompteMarchandBase
		{
			public $Merchant ;
			public $IPNSecret ;
			public $Monnaie = "EUR" ;
			public $TauxChange = 665 ;
		}
		
		class ResultConfirmIPN
		{
			public $Methode = -1 ;
			public $CodeErreur = "non_initialise" ;
			public $Param1 ;
			public $Param2 ;
			public function ConfirmeSucces($methode=1)
			{
				$this->Methode = $methode ;
				$this->CodeErreur = "" ;
			}
			public function RenseigneErreur($methode=1, $codeErreur="")
			{
				$this->Methode = $methode ;
				$this->CodeErreur = $codeErreur ;
			}
			public function EstSucces()
			{
				return $this->CodeErreur == "" ;
			}
		}
		
		class PvInterfacePaiementCoinPayments extends PvInterfacePaiementBase
		{
			public $MerchantCompteMarchand = "" ;
			public $IPNSecretCompteMarchand = "" ;
			public $Titre = "CoinPayments" ;
			public $CheminImage = "https://www.coinpayments.net/images/pub/buynow-grey.png" ;
			public $TitreSoumetFormPaiement = "CoinPayments, redirection en cours" ;
			public $MsgSoumetFormPaiement = "Redirection vers le site web de CoinPayments, veuillez patienter..." ;
			public $EnregistrerTransactCoinPayments = 1 ;
			public $NomTableTransactCoinPayments = "transaction_coinpayments" ;
			public function CreeBdCoinPayments()
			{
				return new AbstractSqlDB() ;
			}
			public function UrlPaiement()
			{
				return "https://www.coinpayments.net/index.php" ;
			}
			public function NomFournisseur()
			{
				return "coinpayments" ;
			}
			protected function CreeTransaction()
			{
				return new PvTransactCoinPayments() ;
			}
			protected function CreeCompteMarchand()
			{
				$compte = new PvCompteMarchandCoinPayments() ;
				$compte->Merchant = $this->MerchantCompteMarchand ;
				$compte->IPNSecret = $this->IPNSecretCompteMarchand ;
				return $compte ;
			}
			public function UrlPaiementAnnule()
			{
				return $this->UrlRacine()."?".$this->NomParamResultat."=".urlencode($this->ValeurParamAnnule)."&idTransact=".urlencode($this->_Transaction->IdTransaction) ;
			}
			protected function ConfirmeIPN()
			{
				$result = new ResultConfirmIPN() ;
				if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
				{
					$result->Param1 = $_SERVER["PHP_AUTH_USER"] ;
					$result->Param2 = $_SERVER["PHP_AUTH_PW"] ;
					if($_SERVER['PHP_AUTH_USER'] == $this->_CompteMarchand->Merchant && $_SERVER['PHP_AUTH_PW'] == $this->_CompteMarchand->IPNSecret)
					{
						$result->ConfirmeSucces(1) ;
					}
					else
					{
						$result->RenseigneErreur(1, "merchant_ipn_secret_fail") ;
					}
					return $result ;
				}
				elseif(isset($_SERVER['HTTP_HMAC']))
				{
					if (empty($_SERVER['HTTP_HMAC'])) {
						$result->RenseigneErreur(2, "hmac_vide") ;
						return $result ;
					}
					$request = file_get_contents('php://input') ;
					$result->Param2 = $request ;
					if ($request === FALSE || empty($request)) {
						$result->RenseigneErreur(2, "requete_http_vide") ;
						return $result ;
					}
					$merchant = isset($_POST['merchant']) ? $_POST['merchant'] : '';
					if (empty($merchant)) {
						$result->RenseigneErreur(2, "merchant_id_vide") ;
						return $result ;
					}
					$result->Param1 = $merchant ;
					if ($merchant != $this->_CompteMarchand->Merchant) {
						$result->RenseigneErreur(2, "merchant_id_incorrect") ;
						return $result ;
					}
					$hmac = hash_hmac("sha512", $request, $this->_CompteMarchand->IPNSecret);
					if ($hmac != $_SERVER['HTTP_HMAC']) {
						$result->RenseigneErreur(2, "secret_ipn_secret_incorrect") ;
						return $result ;
					}
					$result->ConfirmeSucces(2) ;
					return $result ;
				}
				else
				{
					$result->RenseigneErreur(0, "methode_incorrecte") ;
					return $result ;
				}
			}
			protected function SauveEchecIPNTransact($nv, $result)
			{
				if(! $this->EnregistrerTransactCoinPayments)
				{
					return ;
				}
				$bd = $this->CreeBdCoinPayments() ;
				$bd->RunSql(
					"update ".$bd->EscapeTableName($this->NomTableTransactCoinPayments)." set succes_confirm_ipn_".$nv."=".$bd->ParamPrefix."succesConfirmIPN, mtd_confirm_ipn_".$nv."=".$bd->ParamPrefix."mtdConfirmIPN, param1_confirm_ipn_".$nv."=".$bd->ParamPrefix."param1ConfirmIPN, param2_confirm_ipn_".$nv."=".$bd->ParamPrefix."param2ConfirmIPN where id_transaction=".$bd->ParamPrefix."idTransact",
					array(
						"idTransact" => $this->_Transaction->IdTransaction,
						"succesConfirmIPN" => $result->EstSucces(),
						"mtdConfirmIPN" => $result->Methode,
						"param1ConfirmIPN" => $result->Param1,
						"param2ConfirmIPN" => $result->Param2,
					)
				) ;
			}
			protected function ConfirmeTransactionAnnuleeAuto()
			{
				if($this->EnregistrerTransactCoinPayments == 1)
				{
					$this->_Transaction->IdTransaction = _GET_def("idTransact") ;
					$bd = $this->CreeBdCoinPayments() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactCoinPayments)." set date_annule=".$bd->SqlNow().", est_annule=1 where id_transaction=".$bd->ParamPrefix."idTransact",
						array(
							"idTransact" => $this->_Transaction->IdTransaction
						)
					) ;
				}
				parent::ConfirmeTransactionAnnuleeAuto() ;
			}
			protected function RestaureTransactionEnCours()
			{
				parent::RestaureTransactionEnCours() ;
				if($this->IdEtatExecution() == "termine")
				{
					$this->AnalyseTransactionPostee() ;
				}
			}
			protected function AnalyseTransactionPostee()
			{
				$resultConfirmIPN = $this->ConfirmeIPN() ;
				if(! $resultConfirmIPN->EstSucces())
				{
					$this->SauveEchecIPNTransact("retour", $resultConfirmIPN) ;
					return ;
				}
				$this->_Transaction->IdTransaction = $_POST["invoice"] ;
				$this->_Transaction->Montant = ($this->_CompteMarchand->TauxChange * $_POST["amount1"]) ;
				$this->_Transaction->Monnaie = $_POST["currency"] ;
				$this->_Transaction->Langage = $_POST["language"] ;
				$this->_Transaction->Cfg = svc_json_decode($_POST["custom"]) ;
				$this->_Transaction->Designation = $_POST["item_name"] ;
				if($this->EnregistrerTransactCoinPayments == 1)
				{
					$statutTransact = $_POST["status"] ;
					$bd = $this->CreeBdCoinPayments() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactCoinPayments)." set date_retour=".$bd->SqlNow().", ctn_res_retour=".$bd->ParamPrefix."ctnRetour, est_regle = ".$bd->ParamPrefix."estRegle, code_err_retour = ".$bd->ParamPrefix."codeErrRetour, msg_err_retour = ".$bd->ParamPrefix."msgErrRetour where id_transaction=".$bd->ParamPrefix."idTransact",
						array(
							"idTransact" => $this->_Transaction->IdTransaction,
							"ctnRetour" => http_build_query_string($_POST),
							"estRegle" => ($statutTransact >= 100) ? 1 : 0,
							"codeErrRetour" => $statutTransact,
							"msgErrRetour" => ($statutTransact >= 100) ? "" : (($statutTransact < 0) ? "failure ".$statutTransact : "pending ".$statutTransact),
						)
					) ;
				}
			}
			protected function PrepareTransaction()
			{
				parent::PrepareTransaction() ;
				if($this->_EtatExecution->Id != "verification_en_cours")
				{
					return ;
				}
				if($this->EnregistrerTransactCoinPayments == 1)
				{
					$bd = $this->CreeBdCoinPayments() ;
					$bd->RunSql(
						"insert into ".$bd->EscapeTableName($this->NomTableTransactCoinPayments)." (id_transaction, date_envoi, url_envoi, ctn_req_envoi)
values (".$bd->ParamPrefix."idTransact, ".$bd->SqlNow().", ".$bd->ParamPrefix."urlEnvoi, ".$bd->ParamPrefix."ctnReqEnvoi)",
						array(
							"idTransact" => $this->_Transaction->IdTransaction,
							"urlEnvoi" => $this->UrlPaiement(),
							"ctnReqEnvoi" => $this->CtnFormSoumetTransaction()
						)
					) ;
				}
				$this->DefinitEtatExec("verification_ok") ;
			}
			protected function CtnFormSoumetTransaction()
			{
				$ctnForm = '' ;
				$ctnForm .= '<form action="'.$this->UrlPaiement().'" method="post">'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cmd" value="_pay_simple" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="reset" value="1" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="merchant" value="'.htmlspecialchars($this->_CompteMarchand->Merchant).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="currency" value="'.htmlspecialchars($this->_CompteMarchand->Monnaie).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="invoice" value="'.htmlspecialchars($this->_Transaction->IdTransaction).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="item_name" value="'.htmlspecialchars($this->_Transaction->Designation).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="amountf" value="'.ceil($this->_Transaction->Montant / $this->_CompteMarchand->TauxChange).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="custom" value="'.htmlspecialchars(svc_json_encode($this->_Transaction->Cfg)).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="success_url" value="'.htmlspecialchars($this->UrlPaiementTermine()).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cancel_url" value="'.htmlspecialchars($this->UrlPaiementAnnule()).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="lang" value="'.htmlspecialchars($this->_Transaction->Langage).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="image" src="https://www.coinpayments.net/images/pub/buynow-grey.png" alt="Acheter Maintenant avec CoinPayments.net" />'.PHP_EOL ;
				$ctnForm .= '</form>' ;
				return $ctnForm ;
			}
			protected function CtnHtmlSoumetTransaction()
			{
				$ctn = '' ;
				$ctn .= '<!doctype html>
<html>
<head>
<title>'.$this->TitreSoumetFormPaiement.'</title>
</head>
<script language="javascript">
function soumetFormPaiement()
{
	document.forms[0].submit() ;
}
</script>
<body onload="soumetFormPaiement()">
<div>'.$this->MsgSoumetFormPaiement.'</div>
<div style="display:none">
'.$this->CtnFormSoumetTransaction().'
</div>
</body>
</html>' ;
				return $ctn ;
			}
			protected function SoumetTransaction()
			{
				echo $this->CtnHtmlSoumetTransaction() ;
			}
		}
	}
	
?>