<?php
	
	if(! defined('PV_PASSERELLE_PAIEMENT_CINETPAY'))
	{
		if(! defined('PV_NOYAU_PASSERELLE_PAIEMENT'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_PASSERELLE_PAIEMENT_CINETPAY', 1) ;
		
		class PvTransactSkrill extends PvTransactPaiementBase
		{
			public $SessionId ;
			public $ConfigPaiement ;
		}
		class PvCompteMarchandSkrill extends PvCompteMarchandBase
		{
			public $EmailBenef ;
			public $Monnaie = "EUR" ;
			public $TauxChange = 665 ;
		}
		
		class PvInterfacePaiementSkrill extends PvInterfacePaiementBase
		{
			public $EmailBenefCompteMarchand = "lebdenat@gmail.com" ;
			public $Titre = "Skrill" ;
			public $UrlSession = "https://pay.skrill.com/" ;
			public $CheminImage = "images/skrill.png" ;
			public $TitreSoumetFormPaiement = "SKRILL, redirection en cours" ;
			public $MsgSoumetFormPaiement = "Redirection vers le site web de SKRILL, veuillez patienter..." ;
			public $EnregistrerTransactSkrill = 1 ;
			public $NomTableTransactSkrill = "transaction_skrill" ;
			public function CreeBdSkrill()
			{
				return new AbstractSqlDB() ;
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
			protected function RestaureTransactionEnCours()
			{
				parent::RestaureTransactionEnCours() ;
				if($this->IdEtatExecution() == "termine")
				{
					$this->AnalyseTransactionPostee() ;
				}
				elseif($this->IdEtatExecution() == "annule" && $this->EnregistrerTransactSkrill == 1)
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
			protected function AnalyseTransactionPostee()
			{
				$this->_Transaction->IdTransaction = $_POST["cpm_trans_id"] ;
				$this->_Transaction->Montant = $_POST["cpm_amount"] ;
				$this->_Transaction->Monnaie = $_POST["cpm_currency"] ;
				$this->_Transaction->SiteId = $_POST["cpm_site_id"] ;
				$this->_Transaction->Langage = $_POST["cpm_language"] ;
				$this->_Transaction->Version = $_POST["cpm_version"] ;
				$this->_Transaction->ConfigPaiement = $_POST["cpm_payment_config"] ;
				$this->_Transaction->ActionPage = $_POST["cpm_page_action"] ;
				$this->_Transaction->Cfg = @svc_json_decode($_POST["cpm_custom"]) ;
				$this->_Transaction->MethodePaiement = $_POST["payment_method"] ;
				$this->_Transaction->Session = $_POST["signature"] ;
				$this->_Transaction->Msisdn = $_POST["cel_phone_num"] ;
				$this->_Transaction->Indicatif = $_POST["cpm_phone_prefixe"] ;
				if($this->EnregistrerTransactSkrill == 1)
				{
					$bd = $this->CreeBdSkrill() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactSkrill)." set date_retour=".$bd->SqlNow().", ctn_res_retour=".$bd->ParamPrefix."ctnRetour where id_transaction=:idTransact",
						array(
							"idTransact" => $this->_Transaction->IdTransaction,
							"ctnRetour" => http_build_query_string($_POST),
						)
					) ;
				}
				$httpSess = new HttpSession() ;
				$codeErrVerif = "" ;
				$msgErrVerif = "" ;
				$resultat = $httpSess->PostData(
					$this->UrlVerif(),
					array(
						"apikey" => $this->_CompteMarchand->ApiKey,
						"cpm_site_id" => $this->_CompteMarchand->SiteId,
						"cpm_trans_id" => $this->_Transaction->IdTransaction,
					)
				) ;
				if($resultat == "")
				{
					$this->DefinitEtatExecution("exception_paiement", (($httpSess->RequestException != "") ? $httpSess->RequestException : "Contenu vide recu a partir de l'URL de verification de la transaction")) ;
					$codeErrVerif = -1 ;
					$msgErrVerif = "EMPTY_CONTENT_RETURNED" ;
				}
				else
				{
					$resultDecode = svc_json_decode($resultat) ;
					$this->_Transaction->ContenuRetourBrut = $resultDecode ;
					if($resultDecode == null)
					{
						$this->DefinitEtatExecution("exception_paiement", "Impossible de decoder le resultat de l'URL de verification de la transaction") ;
						$codeErrVerif = -2 ;
						$msgErrVerif = "WRONG_CONTENT_RETURNED" ;
					}
					else
					{
						if(isset($resultDecode->transaction))
						{
							$transaction = & $resultDecode->transaction ;
							// $this->_Transaction->Cfg = @svc_json_decode($transaction->cpm_custom) ;
							if($transaction->cpm_result == "00")
							{
								$codeErrVerif = 0 ;
								$msgErrVerif = "" ;
								$this->DefinitEtatExecution("paiement_reussi") ;
							}
							else
							{
								$codeErrVerif = $transaction->cpm_result ;
								$msgErrVerif = $transaction->cpm_error_message ;
								$this->DefinitEtatExecution("paiement_echoue", $transaction->cpm_result.":".$transaction->cpm_error_message) ;
							}
						}
						else
						{
							$codeErrVerif = -4 ;
							$msgErrVerif = "NO_STATUS_FOUND" ;
							$this->DefinitEtatExecution("exception_paiement", "Impossible d'obtenir le statut de la transaction a partir de l'URL de verification") ;
						}
					}
					if($this->EnregistrerTransactSkrill == 1)
					{
						$bd = $this->CreeBdSkrill() ;
						$bd->RunSql(
							"update ".$bd->EscapeTableName($this->NomTableTransactSkrill)." set date_verif=".$bd->SqlNow().", url_verif=".$bd->ParamPrefix."urlVerif, ctn_req_verif=".$bd->ParamPrefix."ctnReqVerif, ctn_res_verif=".$bd->ParamPrefix."ctnResVerif, est_regle=".$bd->ParamPrefix."estRegle, code_err_verif=".$bd->ParamPrefix."codeErrVerif, msg_err_verif=".$bd->ParamPrefix."msgErrVerif where id_transaction=:idTransact",
							array(
								"idTransact" => $this->_Transaction->IdTransaction,
								"urlVerif" => $this->UrlVerif(),
								"ctnReqVerif" => $httpSess->GetRequestContents(),
								"ctnResVerif" => $httpSess->GetResponseContents(),
								"estRegle" => ($codeErrVerif == 0) ? 0 : 1,
								"codeErrVerif" => $codeErrVerif,
								"msgErrVerif" => $msgErrVerif,
							)
						) ;
					}
				}
			}
			protected function PrepareTransaction()
			{
				parent::PrepareTransaction() ;
				if($this->_EtatExecution->Id != "verification_en_cours")
				{
					return ;
				}
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
						"detail1_text" => $this->_Transaction->Designation
					)
				) ;
				if(empty($resultat))
				{
					$this->DefinitEtatExecution("verification_echoue", "Echec sur la session : ".($httpSess->RequestException != '') ? $httpSess->RequestException : '') ;
					$codeErrSession = -1 ;
					$msgErrSession = "EMPTY_CONTENT_RETURNED" ;
					// print_r($this->_StatutVerifTransact) ;
				}
				else
				{
					parse_str($resultat, $paramDecode) ;
					if($paramDecode == null)
					{
						$this->DefinitEtatExecution("verification_echoue", "Impossible de decoder le contenu HTML de la session") ;
						$codeErrSession = -2 ;
						$msgErrSession = "WRONG_CONTENT_RETURNED" ;
					}
					else
					{
						if(isset($paramDecode["SESSION_ID"])
						{
							$codeErrSession = 0 ;
							$valSession = $ctnDecode ;
							$this->_Transaction->SessionId = $paramDecode["SESSION_ID"] ;
						}
						else
						{
							$codeErrSession = 2 ;
							$valSession = "SESSION_ID_NOT_GENERATED" ;
						}
					}
				}
				if($this->EnregistrerTransactSkrill == 1)
				{
					$bd = $this->CreeBdSkrill() ;
					$bd->RunSql(
						"insert into ".$bd->EscapeTableName($this->NomTableTransactSkrill)." (id_transaction, date_session, url_session, ctn_req_session, ctn_res_session, valeur_session, code_err_session, msg_err_session)
values (".$bd->ParamPrefix."idTransact, ".$bd->SqlNow().", ".$bd->ParamPrefix."urlSession, ".$bd->ParamPrefix."ctnReqSession, ".$bd->ParamPrefix."ctnResSession, ".$bd->ParamPrefix."valSession, ".$bd->ParamPrefix."codeErrSession, ".$bd->ParamPrefix."msgErrSession)",
						array(
							"idTransact" => $this->_Transaction->IdTransaction,
							"urlSession" => $this->UrlSession(),
							"ctnReqSession" => $httpSess->GetRequestContents(),
							"ctnResSession" => $httpSess->GetResponseContents(),
							"valSession" => $this->_Transaction->SessionId,
							"codeErrSession" => $codeSession,
							"msgErrSession" => $msgErrSession,
						)
					) ;
				}
			}
			protected function CtnFormSoumetTransaction()
			{
				$ctnForm = '' ;
				$ctnForm .= '<form action="'.$this->UrlSession.'?sid='.urlencode($this->_Transaction->SessionId).'" method="post">'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="pay_to_email" value="'.htmlspecialchars($this->_CompteMarchand->EmailBenef).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="amount" value="'.htmlspecialchars($this->_Transaction->Montant).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="currency" value="'.htmlspecialchars($this->_CompteMarchand->Monnaie).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="transaction_id" value="'.htmlspecialchars($this->_Transaction->IdTransaction).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="merchant_fields" value="cfg_paiement" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cfg_paiement" value="'.htmlspecialchars(svc_json_encode($this->_Transaction->Cfg)).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="detail1_description" value="'.htmlspecialchars($this->_Transaction->Designation).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="return_url" value="'.htmlspecialchars($this->UrlPaiementTermine()).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cancel_url" value="'.htmlspecialchars($this->UrlPaiementAnnule()).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="submit" />'.PHP_EOL ;
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