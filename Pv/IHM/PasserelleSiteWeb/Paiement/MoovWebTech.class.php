<?php
	
	if(! defined('PV_PASSERELLE_PAIEMENT_MOOVWEBTECH'))
	{
		define('PV_PASSERELLE_PAIEMENT_MOOVWEBTECH', 1) ;
		
		class PvTransactMoovWebTech extends PvTransactPaiementBase
		{
			public $IdWebtech ;
		}
		class PvCompteMarchandMoovWebTech extends PvCompteMarchandBase
		{
			public $User ;
			public $Password ;
		}
		
		class PvInterfacePaiementMoovWebTech extends PvInterfacePaiementBase
		{
			public $NomTableTransactMoov = "demande_moovwebtech" ;
			public $Titre = "Moov Money" ;
			public $TitreDocSoumetTransact = "Configurer le numero Moov Money" ;
			public $TitreDocVerifSessionRglt = "V&eacute;rification du r&egrave;glement" ;
			public $CheminImage = "images/moov-money.png" ;
			public $CheminBanniere = "images/banniere-moov-money.png" ;
			public $TitreDocument = "Paiement par Moov Money" ;
			public $LargeurFormSoumetTransact = 600 ;
			public $LibelleMsisdn = 'Veuillez entrer le Numero de t&eacute;l&eacute;phone pour le r&egrave;glement' ;
			public $ValeurParamDemande = 'demande' ;
			public $ValeurParamStatut = 'statut' ;
			public $EtatSessionRglt = "" ;
			public $UserCompteMarchand ;
			public $PasswordCompteMarchand ;
			public $DelaiVerifStatut = 30 ;
			public $UrlApi = "https://floozapi.webtechci.com/interface/api.php" ;
			protected function CreeBdAppels()
			{
				return new AbstractSqlDB() ;
			}
			public function NomFournisseur()
			{
				return "moovmoney" ;
			}
			protected function CreeTransaction()
			{
				return new PvTransactMoovWebTech() ;
			}
			protected function CreeCompteMarchand()
			{
				$cpt = new PvCompteMarchandMoovWebTech() ;
				$cpt->User = $this->UserCompteMarchand ;
				$cpt->Password = $this->PasswordCompteMarchand ;
				return $cpt ;
			}
			public function UrlDemande($msisdn)
			{
				return $this->UrlApi."?action=request&user=".urlencode($this->_CompteMarchand->User)."&password=".urlencode($this->_CompteMarchand->Password)."&msisdn=".urlencode($msisdn)."&amount=".urlencode($this->_Transaction->Montant)."&reference=".urlencode($this->_Transaction->IdTransaction) ;
			}
			public function UrlStatut()
			{
				return $this->UrlApi."?action=getStatus&user=".urlencode($this->_CompteMarchand->User)."&password=".urlencode($this->_CompteMarchand->Password)."&id=".urlencode($this->_Transaction->IdWebtech) ;
			}
			protected function DetermineResultatPaiement()
			{
				$this->ValeurParamResultat = "" ;
				if(isset($_GET[$this->NomParamResultat]) && in_array($_GET[$this->NomParamResultat], array($this->ValeurParamDemande, $this->ValeurParamStatut)))
				{
					$this->ValeurParamResultat = $_GET[$this->NomParamResultat] ;
				}
			}
			protected function ExtraitNumeroMoov($msisdn)
			{
				$numero = '' ;
				$msisdn = trim($msisdn) ;
				if(strpos($msisdn, '225') === 0)
				{
					$numero = substr($msisdn, 2, 8) ;
				}
				elseif(strpos($msisdn, '+225') === 0)
				{
					$numero = substr($msisdn, 3, 8) ;
				}
				elseif(strpos($msisdn, '00225') === 0)
				{
					$numero = substr($msisdn, 3, 8) ;
				}
				else
				{
					$numero = substr($msisdn, 0, 8) ;
				}
				return $numero ;
			}
			protected function RestaureTransactionEnCours()
			{
				$this->DetermineResultatPaiement() ;
				if($this->ValeurParamResultat == $this->ValeurParamDemande && isset($_SESSION["paiement_moovwebtech_en_cours"]))
				{
					$this->_Transaction->IdTransaction = _POST_def("id_transaction") ;
					$this->_Transaction->Montant = _POST_def("montant") ;
					$this->_Transaction->Monnaie = _POST_def("monnaie") ;
					$this->_Transaction->Designation = _POST_def("designation") ;
					$msisdn = $this->ExtraitNumeroMoov(_POST_def("msisdn")) ;
					if($msisdn != '')
					{
						$dateEnvoi = date("Y-m-d H:i:s") ;
						$sess = new HttpSession() ;
						$urlAppel = $this->UrlDemande($msisdn) ;
						$resultat = $sess->GetPage($urlAppel) ;
						$success = -1 ;
						$message = "" ;
						$idTransactWebTech = 0 ;
						if($resultat != '')
						{
							$ctnJson = svc_json_decode($resultat) ;
							if($ctnJson != null)
							{
								$success = (isset($ctnJson->success)) ? $ctnJson->success : -1 ;
								$message = (isset($ctnJson->message)) ? $ctnJson->message : '' ;
								if($success == 1)
								{
									$this->_Transaction->IdWebtech = $ctnJson->id ;
									$_SESSION["transaction_moovwebtech"] = serialize($this->_Transaction) ;
								}
							}
						}
						$bd = $this->CreeBdAppels() ;
						$ok = $bd->RunSql(
							'insert into '.$bd->EscapeTableName($this->NomTableTransactMoov).' (id_transaction, url_envoi_demande, date_envoi_demande, id_transact_spec, ctn_retour_demande, success_retour_demande, message_retour_demande, date_retour_demande)
values ('.$bd->ParamPrefix.'id_transaction, '.$bd->ParamPrefix.'url_envoi_demande, '.$bd->SqlStrToDateTime($bd->ParamPrefix.'date_envoi_demande').', '.$bd->ParamPrefix.'id_transact_spec, '.$bd->ParamPrefix.'ctn_retour_demande, '.$bd->ParamPrefix.'success_retour_demande, '.$bd->ParamPrefix.'message_retour_demande, '.$bd->SqlStrToDateTime($bd->ParamPrefix.'date_retour_demande').')',
							array(
								"id_transaction" => $this->_Transaction->IdTransaction,
								"url_envoi_demande" => $urlAppel,
								"date_envoi_demande" => $dateEnvoi,
								"id_transact_spec" => $this->_Transaction->IdWebtech,
								"ctn_retour_demande" => $sess->GetResponseContents(),
								"success_retour_demande" => $success,
								"message_retour_demande" => $message,
								"date_retour_demande" => date("Y-m-d H:i:s"),
							)
						) ;
						unset($_SESSION["paiement_moovwebtech_en_cours"]) ;
						if(! $ok)
						{
							$this->DefinitEtatExecution("paiement_echoue", $bd->ConnectionException) ;
						}
						elseif($success == 1)
						{
							$this->VerifStatutSessionRglt() ;
							exit ;
						}
						else
						{
							$this->DefinitEtatExecution("paiement_echoue", "L'API a renvoye l'erreur #".htmlentities($success)) ;
						}
					}
					else
					{
						$this->SoumetTransaction() ;
						exit ;
					}
				}
				elseif($this->ValeurParamResultat == $this->ValeurParamStatut && isset($_SESSION["transaction_moovwebtech"]))
				{
					$this->_Transaction = unserialize($_SESSION["transaction_moovwebtech"]) ;
					$this->ImporteFichCfgTransition() ;
					$sess = new HttpSession() ;
					$dateEnvoi = date("Y-m-d H:i:s") ;
					$urlAppel = $this->UrlStatut() ;
					$resultat = $sess->GetPage($urlAppel) ;
					$status = -1 ;
					$message = "" ;
					$idTransactWebTech = 0 ;
					if($resultat != '')
					{
						$ctnJson = svc_json_decode($resultat) ;
						if($ctnJson != null)
						{
							$status =(isset($ctnJson->status)) ?  $ctnJson->status : -1 ;
							$message = (isset($ctnJson->message)) ? $ctnJson->message : '' ;
						}
					}
					$bd = $this->CreeBdAppels() ;
					$ok = $bd->RunSql(
						"update ".$bd->EscapeTableName($this->NomTableTransactMoov)." set date_envoi_statut=".$bd->SqlStrToDateTime($bd->ParamPrefix."date_envoi_statut").", url_envoi_statut=".$bd->ParamPrefix."url_envoi_statut, ctn_retour_statut=".$bd->ParamPrefix."ctn_retour_statut, date_retour_statut=".$bd->SqlStrToDateTime($bd->ParamPrefix."date_retour_statut").", valeur_retour_statut = ".$bd->ParamPrefix."valeur_retour_statut, message_retour_statut = ".$bd->ParamPrefix."message_retour_statut, total_essais_statut = total_essais_statut+1 where id_transaction=".$bd->ParamPrefix."id_transaction",
						array(
							"date_envoi_statut" => $dateEnvoi,
							"url_envoi_statut" => $urlAppel,
							"ctn_retour_statut" => $sess->GetResponseContents(),
							"valeur_retour_statut" => $status,
							"date_retour_statut" => date("Y-m-d H:i:s"),
							"message_retour_statut" => $message,
							"id_transaction" => $this->_Transaction->IdTransaction,
						)
					) ;
					if($ok)
					{
						switch($status)
						{
							case "3" :
							{
								$this->DefinitEtatExecution("paiement_reussi") ;
								unset($_SESSION["transaction_moovwebtech"]) ;
							}
							break ;
							case "4" :
							{
								$this->DefinitEtatExecution("paiement_echoue", $message) ;
								unset($_SESSION["transaction_moovwebtech"]) ;
							}
							break ;
							default :
							{
								$this->VerifStatutSessionRglt() ;
								exit ;
							}
							break ;
						}
					}
					else
					{
						$this->DefinitEtatExecution("paiement_reussi", "Echec lors de la mise a jour de la base de donn&eacute;e") ;
						unset($_SESSION["transaction_moovwebtech"]) ;
					}
				}
				else
				{
					$this->DefinitEtatExecution("paiement_echoue", "Requete de paiement incorrecte") ;
				}
			}
			protected function VerifStatutSessionRglt()
			{
				$this->TitreDocument = $this->TitreDocVerifSessionRglt ;
				$this->EtatSessionRglt = 2 ;
				echo $this->CtnVerifStatutSessionRglt() ;
			}
			protected function RenduBlocVerifStatutSessionRglt()
			{
				$ctn = '' ;
				$ctn .= '<p align="center"> --- Veuillez patienter --- </p>' ;
				return $ctn ;
			}
			protected function CtnVerifStatutSessionRglt()
			{
				$ctn = '' ;
				$ctn .= $this->RenduEnteteDocument() ;
				$ctn .= $this->RenduEnteteCorpsDocument() ;
				$ctn .= $this->RenduBlocVerifStatutSessionRglt() ;
				$ctn .= $this->RenduPiedCorpsDocument() ;
				$ctn .= $this->RenduPiedDocument() ;
				return $ctn ;
			}
			protected function TransactionEnCours()
			{
				return 0 ;
			}
			protected function AnalyseTransactionPostee()
			{
			}
			protected function PrepareTransaction()
			{
				parent::PrepareTransaction() ;
				if($this->_EtatExecution->Id != "verification_en_cours")
				{
					return ;
				}
				$this->DefinitEtatExecution("verification_ok") ;
			}
			protected function RenduCSSCommun()
			{
				$ctn = '' ;
				$ctn .= 'body {
text-align:center ;
}
body, table, tr, td, div, form, p, th {
font-family:arial ;
font-size:12px ;
}' ;
				return $ctn ;
			}
			protected function RenduEnteteDocument()
			{
				$ctn = '' ;
				$ctn .= '<!doctype html>'.PHP_EOL ;
				$ctn .= '<html>'.PHP_EOL ;
				$ctn .= '<head>'.PHP_EOL ; ;
				$ctn .= '<title>'.$this->TitreDocument.'</title>'.PHP_EOL ;
				if($this->EtatSessionRglt == 2)
				{
					$ctn .= '<script type="text/javascript">'.PHP_EOL ;
					$ctn .= 'function reessaieVerifStatut() {
	setTimeout(function() {
		window.location = "?'.$this->NomParamResultat.'='.urlencode($this->ValeurParamStatut).'" ;
	}, '.($this->DelaiVerifStatut * 1000).') ;
}'.PHP_EOL ;
					$ctn .= '</script>'.PHP_EOL ;
				}
				$ctn .= '<style type="text/css">'.PHP_EOL ;
				$ctn .= $this->RenduCSSCommun().PHP_EOL ;
				$ctn .= '</style>'.PHP_EOL ;
				$ctn .= '</head>'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduPiedDocument()
			{
				$ctn = '' ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
			protected function RenduEnteteCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '<body'.(($this->EtatSessionRglt == 2) ? ' onload="reessaieVerifStatut()"' : '').' align="center">'.PHP_EOL ;
				if($this->CheminBanniere != '')
				{
					$ctn .= '<div><img src="'.$this->CheminBanniere.'" /></div>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduPiedCorpsDocument()
			{
				$ctn = '' ;
				$ctn .= '</body>'.PHP_EOL ;
				return $ctn ;
			}
			protected function SoumetTransaction()
			{
				$_SESSION["paiement_moovwebtech_en_cours"] = 1 ;
				$this->TitreDocument = $this->TitreDocSoumetTransact ;
				$this->EtatSessionRglt = 1 ;
				echo $this->CtnSoumetTransaction() ;			
			}
			protected function RenduFormSoumetTransaction()
			{
				$ctn = '' ;
				$ctn .= '<p>'.$this->MessagePrinc.'</p>'.PHP_EOL ;
				$ctn .= '<p>Transaction N&deg;'.htmlentities($this->_Transaction->IdTransaction).' : '.htmlentities($this->_Transaction->Designation).' ('.htmlentities($this->_Transaction->Montant).' '.$this->_Transaction->Monnaie.')</p>'.PHP_EOL ;
				$ctn .= '<form action="?'.$this->NomParamResultat.'='.urlencode($this->ValeurParamDemande).'" method="post">'.PHP_EOL ;
				$ctn .= '<table cellspacing="0" cellpadding="4" width="'.$this->LargeurFormSoumetTransact.'" align="center">'.PHP_EOL ;
				$ctn .= '<tr>'.PHP_EOL ;
				$ctn .= '<th valign="top">'.PHP_EOL ;
				$ctn .= $this->LibelleMsisdn. PHP_EOL ;
				$ctn .= '</th>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '<tr>'.PHP_EOL ;
				$ctn .= '<td valign="top">'.PHP_EOL ;
				$ctn .= '<input type="msisdn" name="msisdn" value="'.htmlspecialchars(_POST_def("msisdn")).'" />'.PHP_EOL ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '<tr>'.PHP_EOL ;
				$ctn .= '<td align="center">'.PHP_EOL ;
				$ctn .= '<input type="submit" value="Valider" />'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="id_transaction" value="'.htmlspecialchars($this->_Transaction->IdTransaction).'" />'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="designation" value="'.htmlspecialchars($this->_Transaction->Designation).'" />'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="montant" value="'.htmlspecialchars($this->_Transaction->Montant).'" />'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="monnaie" value="'.htmlspecialchars($this->_Transaction->Monnaie).'" />'.PHP_EOL ;
				$ctn .= '</td>'.PHP_EOL ;
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				return $ctn ;
			}
			protected function CtnSoumetTransaction()
			{
				$ctn = '' ;
				$ctn .= $this->RenduEnteteDocument() ;
				$ctn .= $this->RenduEnteteCorpsDocument() ;
				$ctn .= $this->RenduFormSoumetTransaction() ;
				$ctn .= $this->RenduPiedCorpsDocument() ;
				$ctn .= $this->RenduPiedDocument() ;
				return $ctn ;
			}
		}
	}
	
?>