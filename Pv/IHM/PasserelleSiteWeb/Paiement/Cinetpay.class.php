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
			public $Monnaie ;
			public $ConfigPaiement ;
			public $ActionPage ;
			public $Version ;
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
		}
		
		class PvInterfacePaiementCinetpay extends PvInterfacePaiementBase
		{
			public $Test = 1 ;
			public $UrlSignatureTest = "http://api.sandbox.cinetpay.com/v1/?method=getSignatureByPost" ;
			public $UrlSignatureProd = "https://api.cinetpay.com/v1/?method=getSignatureByPost" ;
			public $UrlPaiementTest = "http://secure.sandbox.cinetpay.com" ;
			public $UrlPaiementProd = "https://secure.cinetpay.com" ;
			public $UrlVerifTest = "http://api.sandbox.cinetpay.com/v1/?method=checkPayStatus" ;
			public $UrlVerifProd = "https://api.cinetpay.com/v1/?method=checkPayStatus" ;
			public $MsgSoumetFormPaiement = "Redirection en cours, veuillez patienter..." ;
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
				return new PvCompteMarchandCinetpay() ;
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
				$this->_Transaction->IdTransaction = $_POST["cpm_trans_id"] ;
				$this->_Transaction->Montant = $_POST["cpm_amount"] ;
				$this->_Transaction->Monnaie = $_POST["cpm_currency"] ;
				$this->_Transaction->SiteId = $_POST["cpm_site_id"] ;
				$this->_Transaction->Langage = $_POST["cpm_language"] ;
				$this->_Transaction->Version = $_POST["cpm_version"] ;
				$this->_Transaction->ConfigPaiement = $_POST["cpm_payment_config"] ;
				$this->_Transaction->ActionPage = $_POST["cpm_page_action"] ;
				$this->_Transaction->InfosSuppl = $_POST["cpm_custom"] ;
				$this->_Transaction->MethodePaiement = $_POST["payment_method"] ;
				$this->_Transaction->Signature = $_POST["signature"] ;
				$this->_Transaction->Msisdn = $_POST["cel_phone_num"] ;
				$this->_Transaction->Indicatif = $_POST["cpm_phone_prefixe"] ;
				$httpSess = new HttpSession() ;
				$result = $httpSess->PostData(
					$this->UrlVerif(),
					array(
						"apikey" => $this->_Transaction->ApiKey,
						"cpm_site_id" => $this->_Transaction->SiteId,
						"cpm_trans_id" => $this->_Transaction->IdTransaction,
					)
				) ;
				if($result == "")
				{
					$this->DefinitEtatExecution("exception_paiement", (($httpSess->RequestException != "") ? $httpSess->RequestException : "Contenu vide recu a partir de l'URL de verification de la transaction")) ;
				}
				else
				{
					$resultDecode = svc_json_decode($result) ;
					$this->_Transaction->ContenuRetourBrut = $resultDecode ;
					if($resultDecode == null)
					{
						$this->DefinitEtatExecution("exception_paiement", "Impossible de decoder le resultat de l'URL de verification de la transaction") ;
					}
					else
					{
						if(isset($resultDecode->transaction))
						{
							$transaction = & $resultDecode->transaction ;
							if($transaction->cpm_result == "00")
							{
								$this->DefinitEtatExecution("paiement_reussi") ;
							}
							else
							{
								$this->DefinitEtatExecution("paiement_echoue", $transaction->cpm_result.":".$transaction->cpm_error_message) ;
							}
						}
						else
						{
							$this->DefinitEtatExecution("exception_paiement", "Impossible d'obtenir le statut de la transaction a partir de l'URL de verification") ;
						}
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
				$result = $httpSess->PostData(
					$this->UrlSignature(),
					array(
						"cpm_amount" => $this->_Transaction->Montant,
						"cpm_currency" => $this->_Transaction->Monnaie,
						"cpm_site_id" => $this->_CompteMarchand->SiteId,
						"cpm_trans_id" => $this->_Transaction->IdTransaction,
						"cpm_trans_date" => date("YmdHis"),
						"cpm_payment_config" => "SINGLE",
						"cpm_page_action" => "PAYMENT",
						"cpm_version" => $this->_CompteMarchand->Version,
						"cpm_language" => $this->_CompteMarchand->Langage,
						"cpm_designation" => $this->_Transaction->Designation,
						"cpm_custom" => $this->_Transaction->InfosSuppl,
						"apikey" => $this->_Transaction->ApiKey,
					)
				) ;
				if(empty($result))
				{
					$this->DefinitEtatExecution("verification_echoue", "Echec sur la signature : ".($httpSess->RequestException != '') ? $httpSess->RequestException : '') ;
					// print_r($this->_StatutVerifTransact) ;
				}
				else
				{
					$ctnDecode = @svc_json_decode($result) ;
					if($ctnDecode == null)
					{
						$this->DefinitEtatExecution("verification_echoue", "Impossible de decoder le contenu JSON de la signature") ;
					}
					else
					{
						if(is_object($ctnDecode))
						{
							if(isset($ctnDecode->status))
							{
								$this->DefinitEtatExecution("verification_rejetee", $ctnDecode->status->code." : ".$ctnDecode->status->message) ;
							}
							else
							{
								$this->DefinitEtatExecution("verification_echoue", "Impossible d'obtenir le statut d'erreur de la signature") ;
							}
						}
						else
						{
							$this->_Transaction->Signature = $ctnDecode ;
							$this->ValideVerifTransact() ;
						}
					}
				}
			}
			protected function CtnFormSoumetTransaction()
			{
				$ctnForm = '' ;
				$ctnForm .= '<form action="'.$this->UrlPaiement().'" method="post">'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="" value="" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_amount" value="'.htmlspecialchars($this->_Transaction->Montant).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_currency" value="'.htmlspecialchars($this->_Transaction->Monnaie).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_site_id" value="'.htmlspecialchars($this->_CompteMarchand->SiteId).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_trans_id" value="'.htmlspecialchars($this->_Transaction->IdTransaction).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_trans_date" value="'.date("YmdHis").'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_payment_config" value="'."SINGLE".'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_page_action" value="'."PAYMENT".'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_version" value="'.htmlspecialchars($this->_CompteMarchand->Version).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_language" value="'.htmlspecialchars($this->_CompteMarchand->Langage).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_designation" value="'.htmlspecialchars($this->_Transaction->Designation).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cpm_custom" value="'.htmlspecialchars($this->_Transaction->InfosSuppl).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="apikey" value="'.htmlspecialchars($this->_Transaction->ApiKey).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="notify_url" value="'.htmlspecialchars($this->UrlPaiementTermine()).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="cancel_url" value="'.htmlspecialchars($this->UrlPaiementAnnule()).'" />'.PHP_EOL ;
				$ctnForm .= '<input type="hidden" name="signature" value="'.htmlspecialchars($this->_Transaction->Signature).'" />'.PHP_EOL ;
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
<title>Passerelle CINETPAY</title>
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