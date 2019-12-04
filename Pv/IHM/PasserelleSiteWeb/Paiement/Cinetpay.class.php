<?php
	
	if(! defined('PV_PASSERELLE_PAIEMENT_CINETPAY'))
	{
		if(! defined('PV_NOYAU_PASSERELLE_PAIEMENT'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_PASSERELLE_PAIEMENT_CINETPAY', 1) ;
		
		class PvTransactCinetpay extends PvTransactPaiementBase
		{
			public $Signature ;
			public $Monnaie = 'CFA' ;
			public $ConfigPaiement ;
			public $ActionPage ;
			public $Version ;
			public $DatePaiement ;
			public $MethodePaiement ;
			public $Msisdn ;
			public $Indicatif ;
			public $ApiKey ;
			public $SiteId ;
		}
		class PvCompteMarchandCinetpay extends PvCompteMarchandBase
		{
			public $ApiKey ;
			public $SiteId ;
			public $Version = "V1" ;
			public $Langage = "fr" ;
		}
		
		class PvInterfacePaiementCinetpay extends PvInterfacePaiementBase
		{
			public $Test = 1 ;
			public $ApiKeyCompteMarchand = "" ;
			public $SiteIdCompteMarchand = "" ;
			public $Titre = "Cinetpay" ;
			public $CheminImage = "images/cinetpay.png" ;
			public $UrlSignatureTest = "http://api.sandbox.cinetpay.com/v1/?method=getSignatureByPost" ;
			public $UrlSignatureProd = "https://api.cinetpay.com/v1/?method=getSignatureByPost" ;
			public $UrlPaiementTest = "http://secure.sandbox.cinetpay.com" ;
			public $UrlPaiementProd = "https://secure.cinetpay.com" ;
			public $UrlVerifTest = "http://api.sandbox.cinetpay.com/v1/?method=checkPayStatus" ;
			public $UrlVerifProd = "https://api.cinetpay.com/v1/?method=checkPayStatus" ;
			public $TitreSoumetFormPaiement = "CINETPAY, redirection en cours" ;
			public $MsgSoumetFormPaiement = "Redirection vers le site web de CINETPAY, veuillez patienter..." ;
			public $EnregistrerTransactCinetpay = 1 ;
			public $NomTableTransactCinetpay = "transaction_cinetpay" ;
			public function CreeBdCinetpay()
			{
				return new AbstractSqlDB() ;
			}
			public function NomFournisseur()
			{
				return "cinetpay" ;
			}
			public function UrlSignature()
			{
				return ($this->Test) ? $this->UrlSignatureTest : $this->UrlSignatureProd ;
			}
			public function UrlPaiement()
			{
				return ($this->Test) ? $this->UrlPaiementTest : $this->UrlPaiementProd ;
			}
			public function UrlVerif()
			{
				return ($this->Test) ? $this->UrlVerifTest : $this->UrlVerifProd ;
			}
			protected function CreeTransaction()
			{
				return new PvTransactCinetpay() ;
			}
			protected function CreeCompteMarchand()
			{
				$compte = new PvCompteMarchandCinetpay() ;
				$compte->ApiKey = $this->ApiKeyCompteMarchand ;
				$compte->SiteId = $this->SiteIdCompteMarchand ;
				return $compte ;
			}
			protected function RestaureTransactionEnCours()
			{
				parent::RestaureTransactionEnCours() ;
				if($this->IdEtatExecution() == "termine")
				{
					$this->AnalyseTransactionPostee() ;
				}
			}
			protected function ConfirmeTransactionAnnuleeAuto()
			{
				if($this->EnregistrerTransactCinetpay == 1)
				{
					$bd = $this->CreeBdCinetpay() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactCinetpay)." set date_annule=".$bd->SqlNow().", est_annule=1 where id_transaction=:idTransact",
						array(
							"idTransact" => $this->_Transaction->IdTransaction
						)
					) ;
				}
				parent::ConfirmeTransactionAnnuleeAuto() ;
			}
			protected function AnalyseTransactionPostee()
			{
				$this->_Transaction->IdTransaction = $_POST["cpm_trans_id"] ;
				$this->_Transaction->Montant = $_POST["cpm_amount"] ;
				$this->_Transaction->Monnaie = $_POST["cpm_currency"] ;
				$this->_Transaction->DatePaiement = $_POST["cpm_trans_date"] ;
				$this->_Transaction->SiteId = $_POST["cpm_site_id"] ;
				$this->_Transaction->Langage = $_POST["cpm_language"] ;
				$this->_Transaction->Version = $_POST["cpm_version"] ;
				$this->_Transaction->ConfigPaiement = $_POST["cpm_payment_config"] ;
				$this->_Transaction->ActionPage = $_POST["cpm_page_action"] ;
				$this->_Transaction->Cfg = @svc_json_decode($_POST["cpm_custom"]) ;
				$this->_Transaction->MethodePaiement = $_POST["payment_method"] ;
				$this->_Transaction->Signature = $_POST["signature"] ;
				$this->_Transaction->Msisdn = $_POST["cel_phone_num"] ;
				$this->_Transaction->Indicatif = $_POST["cpm_phone_prefixe"] ;
				if($this->EnregistrerTransactCinetpay == 1)
				{
					$bd = $this->CreeBdCinetpay() ;
					$bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactCinetpay)." set date_retour=".$bd->SqlNow().", ctn_res_retour=".$bd->ParamPrefix."ctnRetour where id_transaction=".$bd->ParamPrefix."idTransact",
						array(
							"idTransact" => $this->_Transaction->IdTransaction,
							"ctnRetour" => http_build_query_string($_POST),
						)
					) ;
				}
				$this->VerifieFinTransaction() ;
			}
			protected function VerifieFinTransaction()
			{
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
					if($this->EnregistrerTransactCinetpay == 1)
					{
						$bd = $this->CreeBdCinetpay() ;
						$bd->RunSql(
							"update ".$bd->EscapeTableName($this->NomTableTransactCinetpay)." set date_verif=".$bd->SqlNow().", url_verif=".$bd->ParamPrefix."urlVerif, ctn_req_verif=".$bd->ParamPrefix."ctnReqVerif, ctn_res_verif=".$bd->ParamPrefix."ctnResVerif, est_regle=".$bd->ParamPrefix."estRegle, code_err_verif=".$bd->ParamPrefix."codeErrVerif, msg_err_verif=".$bd->ParamPrefix."msgErrVerif where id_transaction=:idTransact",
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
			protected function ControleTransactionEnAttente()
			{
				$this->VerifieFinTransaction() ;
			}
			protected function PrepareTransaction()
			{
				parent::PrepareTransaction() ;
				if($this->_EtatExecution->Id != "verification_en_cours")
				{
					return ;
				}
				$valSignature = '' ;
				$codeErrSignature = '' ;
				$msgErrSignature = '' ;
				$this->_Transaction->DatePaiement = date('YmdHis') ;
				$monnaie = ($this->_Transaction->Monnaie == "XOF") ? "CFA" : $this->_Transaction->Monnaie ;
				$httpSess = new HttpSession() ;
				$resultat = $httpSess->PostData(
					$this->UrlSignature(),
					array(
						"cpm_amount" => $this->_Transaction->Montant,
						"cpm_currency" => $monnaie,
						"cpm_site_id" => $this->_CompteMarchand->SiteId,
						"cpm_trans_id" => $this->_Transaction->IdTransaction,
						"cpm_trans_date" => $this->_Transaction->DatePaiement,
						"cpm_payment_config" => "SINGLE",
						"cpm_page_action" => "PAYMENT",
						"cpm_version" => $this->_CompteMarchand->Version,
						"cpm_language" => $this->_CompteMarchand->Langage,
						"cpm_designation" => clean_special_chars($this->_Transaction->Designation),
						"cpm_custom" => svc_json_encode($this->_Transaction->Cfg),
						"apikey" => $this->_CompteMarchand->ApiKey,
					)
				) ;
				if(empty($resultat))
				{
					$this->DefinitEtatExecution("verification_echoue", "Echec sur la signature : ".($httpSess->RequestException != '') ? $httpSess->RequestException : '') ;
					$codeErrSignature = -1 ;
					$msgErrSignature = "EMPTY_CONTENT_RETURNED" ;
					// print_r($this->_StatutVerifTransact) ;
				}
				else
				{
					$ctnDecode = @svc_json_decode($resultat) ;
					if($ctnDecode == null)
					{
						$this->DefinitEtatExecution("verification_echoue", "Impossible de decoder le contenu JSON de la signature") ;
						$codeErrSignature = -2 ;
						$msgErrSignature = "WRONG_CONTENT_RETURNED" ;
					}
					else
					{
						if(is_object($ctnDecode))
						{
							if(isset($ctnDecode->status))
							{
								$codeErrSignature = $ctnDecode->status->code ;
								$msgErrSignature = $ctnDecode->status->message ;
								$this->DefinitEtatExecution("verification_rejetee", $ctnDecode->status->code." : ".$ctnDecode->status->message) ;
							}
							else
							{
								$codeErrSignature = -4 ;
								$msgErrSignature = "NO_STATUS_FOUND" ;
								$this->DefinitEtatExecution("verification_echoue", "Impossible d'obtenir le statut d'erreur de la signature") ;
							}
						}
						else
						{
							$codeErrSignature = 0 ;
							$valSignature = $ctnDecode ;
							$this->_Transaction->Signature = $ctnDecode ;
							$this->ValideVerifTransact() ;
						}
					}
				}
				if($this->EnregistrerTransactCinetpay == 1)
				{
					$bd = $this->CreeBdCinetpay() ;
					$bd->RunSql(
						"insert into ".$bd->EscapeTableName($this->NomTableTransactCinetpay)." (id_transaction, date_signature, url_signature, ctn_req_signature, ctn_res_signature, valeur_signature, code_err_signature, msg_err_signature, ctn_form_transact)
values (".$bd->ParamPrefix."idTransact, ".$bd->SqlNow().", ".$bd->ParamPrefix."urlSignature, ".$bd->ParamPrefix."ctnReqSignature, ".$bd->ParamPrefix."ctnResSignature, ".$bd->ParamPrefix."valSignature, ".$bd->ParamPrefix."codeErrSignature, ".$bd->ParamPrefix."msgErrSignature, ".$bd->ParamPrefix."ctnFormTransact)",
						array(
							"idTransact" => $this->_Transaction->IdTransaction,
							"urlSignature" => $this->UrlSignature(),
							"ctnReqSignature" => $httpSess->GetRequestContents(),
							"ctnResSignature" => $httpSess->GetResponseContents(),
							"valSignature" => $valSignature,
							"ctnFormTransact" => $this->CtnFormSoumetTransaction(),
							"codeErrSignature" => $codeSignature,
							"msgErrSignature" => $msgErrSignature,
						)
					) ;
				}
			}
			protected function CtnFormSoumetTransaction()
			{
				$ctnForm = '' ;
				$monnaie = ($this->_Transaction->Monnaie == "XOF") ? "CFA" : $this->_Transaction->Monnaie ;
				$ctnForm .= '<form action="'.$this->UrlPaiement().'" method="post">'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_amount" value="'.htmlspecialchars($this->_Transaction->Montant).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_currency" value="'.htmlspecialchars($monnaie).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_site_id" value="'.htmlspecialchars($this->_CompteMarchand->SiteId).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_trans_id" value="'.htmlspecialchars($this->_Transaction->IdTransaction).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_trans_date" value="'.$this->_Transaction->DatePaiement.'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_payment_config" value="'."SINGLE".'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_page_action" value="'."PAYMENT".'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_version" value="'.htmlspecialchars($this->_CompteMarchand->Version).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_language" value="'.htmlspecialchars($this->_CompteMarchand->Langage).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_designation" value="'.htmlspecialchars(clean_special_chars($this->_Transaction->Designation)).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_custom" value="'.htmlspecialchars(svc_json_encode($this->_Transaction->Cfg)).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="apikey" value="'.htmlspecialchars($this->_CompteMarchand->ApiKey).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="signature" value="'.htmlspecialchars($this->_Transaction->Signature).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="notify_url" value="'.htmlspecialchars($this->UrlPaiementTermine()).'" />'.PHP_EOL ;
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
<body onload="soumetFormPaiement()">
<div>'.$this->MsgSoumetFormPaiement.'</div>
<div style="display:none">
'.$this->CtnFormSoumetTransaction().'
</div>
<script language="javascript">
function soumetFormPaiement()
{
	document.forms[0].submit() ;
}
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