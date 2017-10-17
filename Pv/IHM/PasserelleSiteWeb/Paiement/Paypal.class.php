<?php
	
	if(! defined('PV_PASSERELLE_PAYPAL'))
	{
		if(! defined('PV_NOYAU_PASSERELLE_PAIEMENT'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_PASSERELLE_PAYPAL', 1) ;
		
		class PvTransactPaypal extends PvTransactPaiementBase
		{
			public $Langage = "fr" ;
		}
		class PvCompteMarchandPaypal extends PvCompteMarchandBase
		{
			public $SandboxClientId = "" ;
			public $LiveClientId = "" ;
			public $Monnaie = "EUR" ;
			public $TauxChange = 665 ;
		}
		
		class PvInterfacePaiementPaypal extends PvInterfacePaiementBase
		{
			public $LiveClientIdCompteMarchand = "Addh9YqzQXoOH1K_7Jeh2awZMvVffhcsYfWisNr-CW_XJNrHESMWy5bGJCLzTCnYzZ7EaxWMvU_Z8C11" ;
			public $SandboxClientIdCompteMarchand = "ATlpVU2UTNk463rcCFLB2SmasaSPgaJSXd-swkXUqIzDVvaNIuhwn2m4GuRjPjtDwI93LwW993gsPEwi" ;
			public $Titre = "Paypal" ;
			public $CheminImage = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_200x51.png" ;
			public $TitreSoumetFormPaiement = "Traitement Paypal" ;
			public $MsgSoumetFormPaiement = "Veuillez confirmer en cliquant sur ce bouton" ;
			public $EnregistrerTransactPaypal = 1 ;
			public $NomTableTransactPaypal = "transaction_paypal" ;
			public function CreeBdPaypal()
			{
				return new AbstractSqlDB() ;
			}
			public function NomFournisseur()
			{
				return "paypal" ;
			}
			protected function CreeTransaction()
			{
				return new PvTransactPaypal() ;
			}
			protected function CreeCompteMarchand()
			{
				$compte = new PvCompteMarchandPaypal() ;
				$compte->SandboxClientId = $this->SandboxClientIdCompteMarchand ;
				$compte->LiveClientId = $this->LiveClientIdCompteMarchand ;
				return $compte ;
			}
			public function UrlPaiementAnnule()
			{
				return $this->UrlRacine()."?".$this->NomParamResultat."=".urlencode($this->ValeurParamAnnule)."&idTransact=".urlencode($this->_Transaction->IdTransaction) ;
			}
			protected function SauveEchecIPNTransact($nv, $result)
			{
				if(! $this->EnregistrerTransactPaypal)
				{
					return ;
				}
				$bd = $this->CreeBdPaypal() ;
				$bd->RunSql(
					"update ".$bd->EscapeTableName($this->NomTableTransactPaypal)." set succes_confirm_ipn_".$nv."=".$bd->ParamPrefix."succesConfirmIPN, mtd_confirm_ipn_".$nv."=".$bd->ParamPrefix."mtdConfirmIPN, param1_confirm_ipn_".$nv."=".$bd->ParamPrefix."param1ConfirmIPN, param2_confirm_ipn_".$nv."=".$bd->ParamPrefix."param2ConfirmIPN where id_transaction=".$bd->ParamPrefix."idTransact",
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
				if($this->EnregistrerTransactPaypal == 1)
				{
					$this->_Transaction->IdTransaction = _GET_def("idTransact") ;
					$bd = $this->CreeBdPaypal() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactPaypal)." set date_annule=".$bd->SqlNow().", est_annule=1 where id_transaction=".$bd->ParamPrefix."idTransact",
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
				if($this->EnregistrerTransactPaypal == 1)
				{
					$statutTransact = $_POST["status"] ;
					$bd = $this->CreeBdPaypal() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactPaypal)." set date_retour=".$bd->SqlNow().", ctn_res_retour=".$bd->ParamPrefix."ctnRetour, est_regle = ".$bd->ParamPrefix."estRegle, code_err_retour = ".$bd->ParamPrefix."codeErrRetour, msg_err_retour = ".$bd->ParamPrefix."msgErrRetour where id_transaction=".$bd->ParamPrefix."idTransact",
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
				if($this->EnregistrerTransactPaypal == 1)
				{
					$bd = $this->CreeBdPaypal() ;
					$bd->RunSql(
						"insert into ".$bd->EscapeTableName($this->NomTableTransactPaypal)." (id_transaction, date_envoi, url_envoi, ctn_req_envoi)
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
			protected function CtnHtmlSoumetTransaction()
			{
				$ctn = '' ;
				$ctn .= '<!doctype html>
<html>
<head>
<title>'.$this->TitreSoumetFormPaiement.'</title>
</head>
<body align="center">
<div>'.$this->MsgSoumetFormPaiement.'</div>
<div style="display:none">
<form id="confirmeTransact" action="?'.$this->NomParamResultat.'='.urlencode($this->ValeurParamTermine).'">
<input type="hidden" name="transactionData" id="transactionData" value="" />
<input type="hidden" name="id_session" value="'.htmlentities(session_id()).'" />
<input type="submit" value="Soumettre" />
</form>
</div>
<div id="paypal-button"></div>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
paypal.Button.render({
env: \'production\', // Or \'sandbox\'
client: {
sandbox:    \''.$this->CompteMarchand->SandboxClientId.'\',
production: \''.$this->CompteMarchand->LiveClientId.'\'
},
commit: true, // Show a \'Pay Now\' button
payment: function(data, actions) {
return actions.payment.create({
payment: {
transactions: [
{
invoice_number : '.svc_json_encode($this->_Transaction->IdTransaction).',
description : '.svc_json_encode($this->_Transaction->Designation).',
amount: {
total: '.svc_json_encode($this->_Transaction->Montant).'\',
currency: \''.$this->_Transaction->Monnaie.'\'
},
custom: '.svc_json_encode($this->_Transaction->Cfg).'
}
]
}
});
},
onAuthorize: function(data, actions) {
return actions.payment.execute().then(function(payment) {
// The payment is complete!
// You can now show a confirmation message to the customer
document.getElementById("transactionData").value = JSON.encode(payment) ;
document.getElementById("confirmeTransact").submit() ;
});
}
}, \'#paypal-button\');
</script>
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