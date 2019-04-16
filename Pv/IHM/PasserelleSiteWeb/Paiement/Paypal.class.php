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
			public $SbClientId ;
			public $LiveClient ;
			public $LiveSecret ;
			public $Monnaie = "EUR" ;
			public $TauxChange = 665 ;
		}
		
		class PvResultVerifOrderPaypal
		{
			public $ValeurAccessToken ;
			public $CtnReqAuth ;
			public $CtnRepAuth ;
			public $CtnReqCheckOrder ;
			public $CtnRepCheckOrder ;
			public $CodeErreur = "non_defini" ;
			public function EstSucces()
			{
				return $this->CodeErreur == "" ;
			}
		}
		
		class PvInterfacePaiementPaypal extends PvInterfacePaiementBase
		{
			public $LiveClientIdCompteMarchand = "Addh9YqzQXoOH1K_7Jeh2awZMvVffhcsYfWisNr-CW_XJNrHESMWy5bGJCLzTCnYzZ7EaxWMvU_Z8C11" ;
			public $LiveClientCompteMarchand ;
			public $LiveSecretCompteMarchand ;
			public $Titre = "Paypal" ;
			public $CheminImage = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_200x51.png" ;
			public $TitreSoumetFormPaiement = "Traitement Paypal" ;
			public $MsgSoumetFormPaiement = "Veuillez confirmer en cliquant sur ce bouton" ;
			public $EnregistrerTransactPaypal = 1 ;
			public $NomTableTransactPaypal = "transaction_paypal" ;
			public function UrlOAuthApi()
			{
				return ($this->EnLive()) ? 'https://api.paypal.com/v1/oauth2/token/' : 'https://api.sandbox.paypal.com/v1/oauth2/token/' ;
			}
			public function UrlOrderApi()
			{
				return ($this->EnLive()) ? 'https://api.paypal.com/v2/checkout/orders/' : 'https://api.sandbox.paypal.com/v2/checkout/orders/' ;
			}
			public function EnLive()
			{
				return ($this->LiveClientCompteMarchand != '' && $this->LiveSecretCompteMarchand != '') ;
			}
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
				$compte->SbClientId = $this->SbClientIdCompteMarchand ;
				$compte->LiveClient = $this->LiveClientCompteMarchand ;
				$compte->LiveSecret = $this->LiveSecretCompteMarchand ;
				return $compte ;
			}
			public function UrlPaiementAnnule()
			{
				return $this->UrlRacine()."?".$this->NomParamResultat."=".urlencode($this->ValeurParamAnnule)."&idTransact=".urlencode($this->_Transaction->IdTransaction) ;
			}
			protected function SauveEchecTransaction($result)
			{
				if(! $this->EnregistrerTransactPaypal)
				{
					return ;
				}
				$bd = $this->CreeBdPaypal() ;
				$bd->RunSql(
					"update ".$bd->EscapeTableName($this->NomTableTransactPaypal)." set date_verif=".$bd->SqlNow().", code_erreur_verif=".$bd->ParamPrefix."codeVerifOrder, ctn_req_auth_order=".$bd->ParamPrefix."ctnReqAuthOrder, ctn_rep_auth_order=".$bd->ParamPrefix."ctnRepAuthOrder, ctn_req_check_order=".$bd->ParamPrefix."ctnReqCheckOrder, ctn_rep_check_order=".$bd->ParamPrefix."ctnRepCheckOrder
					where id_transaction=".$bd->ParamPrefix."idTransact",
					array(
						"idTransact" => $this->_Transaction->IdTransaction,
						"codeVerifOrder" => $result->CodeErreur,
						"ctnReqAuthOrder" => $result->CtnReqAuth,
						"ctnRepAuthOrder" => $result->CtnRepAuth,
						"ctnReqCheckOrder" => $result->CtnReqCheckOrder,
						"ctnRepCheckOrder" => $result->CtnRepCheckOrder,
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
				$this->DetermineResultatPaiement() ;
				if($this->ValeurParamResultat == $this->ValeurParamTermine)
				{
					if(isset($_POST["id_transaction"]))
					{
						$this->_Transaction->IdTransaction = $_POST["id_transaction"] ;
						$this->RestaureTransactionSession() ;
						$this->DefinitEtatExecution("termine") ;
					}
				}
				elseif($this->ValeurParamResultat == $this->ValeurParamAnnule)
				{
					$this->RestaureTransactionSession() ;
					$this->DefinitEtatExecution("annule") ;
					$this->ConfirmeTransactionAnnuleeAuto() ;
					$this->ConfirmeTransactionAnnulee() ;
				}
				if($this->IdEtatExecution() == "termine")
				{
					$this->AnalyseTransactionPostee() ;
				}
			}
			protected function VerifiePaiementTransaction($orderId)
			{
				$resOrder = new PvResultVerifOrderPaypal() ;
				$httpSess = new HttpSession() ;
				$ctnAuth = $httpSess->PostData(
					$this->UrlOAuthApi(), array("grant_type" => "client_credentials"),
					array(
						"Authorization" => "Basic ".base64_encode($this->_CompteMarchand->LiveClient.":".$this->_CompteMarchand->LiveSecret),
						"Accept" => "application/json"
					)
				) ;
				$resOrder->CtnReqAuth = $httpSess->GetRequestContents() ;
				$resOrder->CtnRepAuth = $httpSess->GetResponseContents() ;
				if($ctnAuth != "")
				{
					$objAuth = svc_json_decode($ctnAuth) ;
					if(is_object($ctnAuth))
					{
						if(isset($ctnAuth->access_token))
						{
							$resOrder->ValeurAccessToken = $ctnAuth->access_token ;
						}
						else
						{
							$resOrder->CodeErreur = "auth_echoue" ;
						}
					}
					else
					{
						$resOrder->CodeErreur = "auth_exception" ;
					}
				}
				else
				{
					$resOrder->CodeErreur = "auth_contenu_vide" ;
				}
				if(! $resOrder->EstSucces())
				{
					return $resOrder ;
				}
				$ctnVerif = $httpSess->GetData(
					$this->UrlOrderApi(), array(),
					array(
						"Authorization" => "Bearer ".$resOrder->ValeurAccessToken,
						"Accept" => "application/json"
					)
				) ;
				$resOrder->CtnReqCheckOrder = $httpSess->GetRequestContents() ;
				$resOrder->CtnRepCheckOrder = $httpSess->GetResponseContents() ;
				if($ctnVerif != "")
				{
					$objVerif = svc_json_decode($ctnVerif) ;
					if(is_object($objVerif))
					{
						if(isset($objVerif->error))
						{
							$resOrder->CodeErreur = "erreur_commande" ;
						}
						else
						{
							$resOrder->CodeErreur = "" ;
						}
					}
					else
					{
						$resOrder->CodeErreur = "commande_introuvable" ;
					}
				}
				else
				{
					$resOrder->CodeErreur = "exception_commande" ;
				}
				return $resOrder ;
			}
			protected function AnalyseTransactionPostee()
			{
				$resOrder = $this->VerifiePaiementTransaction() ;
				if(! $resOrder->EstSucces())
				{
					$this->SauveEchecTransaction($resOrder) ;
					return ;
				}
				$this->_Transaction->IdTransaction = $_POST["id_transaction"] ;
				$this->_Transaction->Montant = $_POST["montant"] ;
				$this->_Transaction->Monnaie = $_POST["monnaie"] ;
				$this->_Transaction->Designation = $_POST["designation"] ;
				if($this->EnregistrerTransactPaypal == 1)
				{
					$bd = $this->CreeBdPaypal() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactPaypal)." set date_regle=".$bd->SqlNow().", montant=".$bd->ParamPrefix."montant, est_regle = ".$bd->ParamPrefix."estRegle, monnaie = ".$bd->ParamPrefix."monnaie, nom_client = ".$bd->ParamPrefix."nomClient, prenom_client = ".$bd->ParamPrefix."prenomClient, id_client = ".$bd->ParamPrefix."idClient, id_commande = ".$bd->ParamPrefix."idCommande, id_achat = ".$bd->ParamPrefix."idAchat where id_transaction=".$bd->ParamPrefix."idTransact",
						array(
							"idTransact" => $this->_Transaction->IdTransaction,
							"montant" => $_POST["montant"],
							"estRegle" => 1,
							"monnaie" => $_POST["monnaie"],
							"nomClient" => $_POST["nom_client"],
							"prenomClient" => $_POST["prenom_client"],
							"idClient" => $_POST["id_client"],
							"idCommande" => $_POST["id_commande"],
							"idAchat" => $_POST["id_achat"],
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
						"insert into ".$bd->EscapeTableName($this->NomTableTransactPaypal)." (id_transaction, date_envoi)
values (".$bd->ParamPrefix."idTransact, ".$bd->SqlNow().")",
						array(
							"idTransact" => $this->_Transaction->IdTransaction
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
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body align="center">
<div>'.$this->MsgSoumetFormPaiement.'</div>
<div style="display:none">
<form id="confirme_transact" action="about:blank">
<input type="hidden" name="id_transaction" id="id_transaction" value="'.htmlspecialchars($this->_Transaction->IdTransaction).'" />
<input type="hidden" name="montant" id="montant" value="'.htmlspecialchars($this->_Transaction->Montant).'" />
<input type="hidden" name="monnaie" id="monnaie" value="'.htmlspecialchars($this->_Transaction->Monnaie).'" />
<input type="hidden" name="designation" id="designation" value="'.htmlspecialchars(substr($this->_Transaction->Designation, 127)).'" />
<input type="hidden" name="id_commande" id="id_commande" value="" />
<input type="hidden" name="nom_client" id="nom_client" value="" />
<input type="hidden" name="prenom_client" id="prenom_client" value="" />
<input type="hidden" name="email_client" id="email_client" value="" />
<input type="hidden" name="id_client" id="id_client" value="" />
<input type="hidden" name="id_achat" id="id_achat" value="" />
<input type="submit" value="Soumettre" />
</form>
</div>
<div id="paypal-button"></div>
<script src="https://www.paypal.com/sdk/js?client-id='.urlencode($this->SbClientIdCompteMarchand).'"></script>
<script>' ;
			if($this->EnLive())
			{
				$ctn .= 'var PAYPAL_CLIENT = '.svc_json_encode($this->LiveClientCompteMarchand).' ;
var PAYPAL_SECRET = '.svc_json_encode($this->LiveSecretCompteMarchand).' ;
// Point your server to the PayPal API
var PAYPAL_ORDER_API = \'https://api.paypal.com/v2/checkout/orders/\';' ;
			}
			$ctn .= 'Paypal.Buttons({
createOrder: function(data, actions) {
return actions.order.create({
purchase_units: [{
invoice_id : '.svc_json_encode($this->_Transaction->IdTransaction).',
description : '.svc_json_encode($this->_Transaction->Designation).',
amount: {
value: '.svc_json_encode(ceil($this->_Transaction->Montant / $this->_Transaction->TauxChange)).',
currency_code: \''.$this->_Transaction->Monnaie.'\'
}
}]
});
},
onApprove: function(data, actions) {
return actions.order.capture().then(function(details) {
document.getElementById("nom_client").value = details.payer.name.surname ;
document.getElementById("prenom_client").value = details.payer.given_name ;
document.getElementById("email_client").value = details.payer.email_address ;
document.getElementById("id_client").value = details.payer.payer_id ;
document.getElementById("id_achat").value = details.purchase_units[0].reference_id ;
document.getElementById("montant").value = details.purchase_units[0].amount.value ;
document.getElementById("monnaie").value = details.purchase_units[0].amount.currency_code ;
document.getElementById("confirme_transact").action = "?'.$this->NomParamResultat.'='.urlencode($this->ValeurParamTermine).'" ;
document.getElementById("confirme_transact").submit() ;
});
},
onCancel : function(data) {
document.getElementById("confirme_transact").action = "?'.$this->NomParamResultat.'='.urlencode($this->ValeurParamAnnule).'" ;
document.getElementById("confirme_transact").submit() ;
}
}).render(\'#paypal-button\');
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