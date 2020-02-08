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
			public $NomZoneWebRendu ;
			public $InterfPaiemtParent ;
			public $UrlSucces = "" ;
			public $UrlEchec = "" ;
			public $LargeurBoiteDialogue = "600" ;
			public function & ApplicationParent()
			{
				return $this->InterfPaiemtParent->ApplicationParent ;
			}
			protected function DefinitEtatExecution($id, $msg="")
			{
				$this->InterfPaiemtParent->DefinitEtatExecution($id, $msg) ;
			}
			protected function DefinitEtatExec($id, $msg="")
			{
				$this->InterfPaiemtParent->DefinitEtatExec($id, $msg) ;
			}
			public function AdopteInterfPaiemt($nom, & $interf)
			{
				$this->NomElementInterfPaiemt = $nom ;
				$this->InterfPaiemtParent = & $interf ;
			}
			public function Prepare(& $transaction)
			{
			}
			public function EstEffectue(& $transaction)
			{
				return 0 ;
			}
			public function Rembourse(& $transaction)
			{
			}
			public function ConfirmeSucces(& $transaction)
			{
			}
			public function ConfirmeEchec(& $transaction)
			{
			}
			public function Annule(& $transaction)
			{
			}
			protected function AfficheBoiteDialogue($niveau, $titre, $message="")
			{
				echo '<!doctype html>
<html>
<head>
<title>'.$titre.'</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style type="text/css">
.boite-dialogue-0 { border : 1px solid #ea9797 }
.boite-dialogue-0 th { background-color : #ea9797 }
.boite-dialogue-1 { border : 1px solid #97c2ea }
.boite-dialogue-1 th { background-color : #97c2ea }
.boite-dialogue-2 { border : 1px solid #eadb97 }
.boite-dialogue-2 th { background-color : #eadb97 }
</style>
</head>
<body align="center" style="background-color:#EDEDED">
<table class="boite-dialogue-'.$niveau.'" align="center" width ="'.$this->LargeurBoiteDialogue.'" cellspacing=0 cellpadding="4">
<tr>
<th>'.$titre.'</th>
</tr>
<tr>
<td>'.$message.'</td>
</tr>
<tr>
<td align="center"><a href="'.(($niveau == 1) ? $this->UrlSucces : $this->UrlEchec).'">Terminer</a></td>
</tr>
</table>
</body>
</html>' ;
				exit ;
			}
		}
		
		class PvCfgTransactPaiement
		{
			public $NomSvcAprPaiement ;
			public $Arg01 ;
			public $Arg02 ;
			public $Arg03 ;
			public $Arg04 ;
			public $Arg05 ;
			public $Arg06 ;
			public $Arg07 ;
			public $Arg08 ;
			public $Arg09 ;
			public $Arg10 ;
			public $Arg11 ;
			public $Arg12 ;
		}
		
		class PvTransactPaiementBase
		{
			public $IdDonnees ;
			public $IdTransaction ;
			public $Designation ;
			public $Montant ;
			public $Langage ;
			public $Monnaie ;
			public $InfosSuppl ;
			public $ContenuRetourBrut = null ;
			public $Cfg = null ;
			public function __construct()
			{
				$this->IdTransaction = uniqid() ;
				$this->Cfg = new PvCfgTransactPaiement() ;
			}
			public function ImporteParLgn($lgn)
			{
				$this->IdDonnees = $lgn["id"] ;
				$this->IdTransaction = $lgn["id_transaction"] ;
				$this->Designation = $lgn["designation"] ;
				$this->Montant = $lgn["montant"] ;
				$this->Monnaie = $lgn["monnaie"] ;
				$this->InfosSuppl = $lgn["infos_suppl"] ;
				$this->ContenuRetourBrut = null ;
				$this->Cfg = ($lgn["cfg"] != '') ? unserialize($lgn["cfg"]) : new PvCfgTransactPaiement() ;
			}
			public function ExporteVersLgn()
			{
				return array(
					"id_transaction" => $this->IdTransaction,
					"designation" => $this->Designation,
					"montant" => $this->Montant,
					"monnaie" => $this->Monnaie,
					"infos_suppl" => $this->InfosSuppl,
					"cfg" => ($this->Cfg != null) ? serialize($this->Cfg) : ''
				) ;
			}
		}
		class PvCompteMarchandBase
		{
		}
		
		class PvInterfacePaiementBase extends PvPasserelleSiteWebBase
		{
			protected $_SvcsAprPaiement = array() ;
			protected $_EtatExecution ;
			protected $_Transaction ;
			protected $_CompteMarchand ;
			public $CheminImage = "images/paiement-base.png" ;
			public $CheminIcone = "" ;
			public $Titre = "Ne rien faire" ;
			public $Description = "" ;
			protected $DelaiControleTransacts = 600 ; // En secondes
			protected $MaxTransactsAControler = 5 ; // En secondes
			protected $TransactionValidee = 0 ;
			protected $DelaiExpirCfgsTransact = 24 ;
			protected $NomParamResultat = "resultat" ;
			protected $ValeurParamResultat = "" ;
			protected $ValeurParamTermine = "paiement_termine" ;
			protected $ValeurParamAnnule = "paiement_annule" ;
			protected $EnregistrerTransaction = 1 ;
			public $UtiliserBdTransactionSoumise = 0 ;
			protected $NomTableTransactSoumise = "transaction_soumise" ;
			protected $NomTableTransaction = "transaction_paiement" ;
			protected $MsgPaiementNonFinalise = "Votre paiement a r&eacute;ussi, mais aucune action n'a &eacute;t&eacute; trouv&eacute;e pour le suivi." ;
			public $CheminRelatifRepTransacts = "." ;
			public $AfficherErreurs404 = 0 ;
			public $TitresEtatExecution = array(
				"paiement_annule" => "Annul&eacute;",
				"paiement_termine" => "Termin&eacute;",
				"paiement_echoue" => "Echou&eacute;",
				"paiement_reussi" => "R&eacute;ussi",
			) ;
			public $TitreEtatExecutionNonTrouve = "En cours" ;
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
					"nom_interf_paiemt" => $this->NomElementApplication,
					"contenu_brut" => serialize($this->_Transaction),
					"id_etat" => $this->_EtatExecution->Id,
					"timestamp_etat" => $this->_EtatExecution->TimestampCapt,
					"msg_erreur_etat" => $this->_EtatExecution->MessageErreur,
				) ;
			}
			protected function ExporteFichCfgTransition()
			{
				if($this->EnregistrerTransaction)
				{
					$this->SauveTransaction() ;
					return ;
				}
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
				if($this->EnregistrerTransaction == 1)
				{
					$bd = $this->CreeBdTransaction() ;
					$lgn = $bd->FetchSqlRow('select * from '.$bd->EscapeTableName($this->NomTableTransaction).' where id_transaction='.$bd->ParamPrefix.'id', array('id' => $this->_Transaction->IdTransaction)) ;
					if(is_array($lgn) && count($lgn) > 0 && $lgn["contenu_brut"] != '')
					{
						$transactTemp = unserialize($lgn["contenu_brut"]) ;
						$this->_Transaction->Cfg = $transactTemp->Cfg ;
					}
					return ;
				}
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
					}
				}
				if($ctnFich != "")
				{
					$this->_Transaction->Cfg = unserialize($ctnFich) ;
				}
			}
			protected function VideCfgsTransactsExpirs()
			{
				if(is_dir($this->CheminRepTransacts()))
				{
					$dh = opendir($this->CheminRepTransacts()) ;
					$timestampActuel = date("U") ;
					if(is_resource($dh))
					{
						while(($nomFich = readdir($dh)) !== false)
						{
							if($nomFich == '.' || $nomFich == '..')
							{
								continue ;
							}
							$cheminFich = $this->CheminRepTransacts()."/".$nomFich ;
							$infoFich = pathinfo($cheminFich) ;
							if($infoFich["extension"] != "dat")
							{
								continue ;
							}
							if($timestampActuel > filemtime($cheminFich) + $this->DelaiExpirCfgsTransact * 3600)
							{
								unlink($cheminFich) ;
							}
						}
						closedir($dh) ;
					}
				}
			}
			protected function SauveTransaction()
			{
				$bd = $this->CreeBdTransaction() ;
				if($this->_Transaction->Montant == '')
				{
					$this->_Transaction->Montant = 0 ;
				}
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
					// print_r($this->_Transaction) ;
					$ok = $bd->InsertRow(
						$this->NomTableTransaction,
						$this->LgnDonneesTransact()
					) ;
					// print_r($bd) ;
				}
				else
				{
					$ok = $bd->UpdateRow(
						$this->NomTableTransaction,
						$this->LgnDonneesTransact(),
						"id = ".$bd->ParamPrefix."id",
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
			public function IdTransaction()
			{
				return $this->_Transaction->IdTransaction ;
			}
			public function & CompteMarchand()
			{
				return $this->_CompteMarchand ;
			}
			public function DefinitEtatExecution($id, $msgErreur="")
			{
				$this->_EtatExecution->Id = $id ;
				$this->_EtatExecution->TimestampCapt = date("U") ;
				$this->_EtatExecution->MessageErreur = $msgErreur ;
				if($this->EnregistrerTransaction == 1)
				{
					$this->SauveTransaction() ;
				}
			}
			public function DefinitEtatExec($id, $msgErreur="")
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
					$this->RestaureTransactionSession() ;
					$this->ImporteFichCfgTransition() ;
					$this->DefinitEtatExecution("termine") ;
				}
				elseif($this->ValeurParamResultat == $this->ValeurParamAnnule)
				{
					$this->RestaureTransactionSession() ;
					$this->ImporteFichCfgTransition() ;
					$this->DefinitEtatExecution("annule") ;
					$this->ConfirmeTransactionAnnuleeAuto() ;
					$this->ConfirmeTransactionAnnulee() ;
				}
			}
			protected function ImporteTransactSoumiseSession()
			{
				$envoyerErr = 0 ;
				if(isset($_SESSION[$this->IDInstanceCalc."_Transaction"]))
				{
					$idDonnees = $this->_Transaction->IdDonnees ;
					$this->_Transaction = @unserialize($_SESSION[$this->IDInstanceCalc."_Transaction"]) ;
					$this->_Transaction->IdDonnees = $idDonnees ;
					unset($_SESSION[$this->IDInstanceCalc."_Transaction"]) ;
				}
				else
				{
					$envoyerErr = 1 ;
				}
				return $envoyerErr ;
			}
			protected function ImporteTransactSoumiseBd()
			{
				$envoyerErr = 0 ;
				$idTransact = _GET_def("idTransactSoumise") ;
				if($this->UtiliserBdTransactionSoumise == 0 || $idTransact == "")
				{
					return 1 ;
				}
				$bd = $this->CreeBdTransaction() ;
				$lgn = $bd->FetchSqlRow("select * from ".$bd->EscapeTableName($this->NomTableTransactSoumise)." where id_transaction=:id", array("id" => $idTransact)) ;
				if(! is_array($lgn) || count($lgn) == 0)
				{
					return 1 ;
				}
				$idDonnees = $this->_Transaction->IdDonnees ;
				$this->_Transaction->ImporteParLgn($lgn) ;
				$this->_Transaction->IdDonnees = $idDonnees ;
				$bd->RunSql("delete from ".$bd->EscapeTableName($this->NomTableTransactSoumise)." where id_transaction=:id", array("id" => $idTransact)) ;
				return 0 ;
			}
			protected function ExporteTransactSoumiseSession()
			{
				$_SESSION[$this->IDInstanceCalc."_Transaction"] = serialize($this->_Transaction) ;
			}
			protected function ExporteTransactSoumiseBd()
			{
				$bd = $this->CreeBdTransaction() ;
				$ok = $bd->RunSql(
					"insert into ".$bd->EscapeTableName($this->NomTableTransactSoumise)." (id_transaction, nom_interface_paiement, designation, montant, monnaie, infos_suppl, cfg) values (".$bd->ParamPrefix."id_transaction, ".$bd->ParamPrefix."nom_interface_paiement, ".$bd->ParamPrefix."designation, ".$bd->ParamPrefix."montant, ".$bd->ParamPrefix."monnaie, ".$bd->ParamPrefix."infos_suppl, ".$bd->ParamPrefix."cfg)", 
					array_merge($this->_Transaction->ExporteVersLgn(), array("nom_interface_paiement" => $this->NomElementApplication))
				) ;
				return $ok ;
			}
			protected function DetermineTransactionSoumise()
			{
				$envoyerErr = $this->ImporteTransactSoumiseSession() ;
				if($envoyerErr == 1)
				{
					$envoyerErr = $this->ImporteTransactSoumiseBd() ;
				}
				if($this->_Transaction == null)
				{
					$envoyerErr = 1 ;
				}
				if($envoyerErr)
				{
					if($this->AfficherErreurs404 == 1)
					{
						Header("HTTP/1.0 401 Unauthorized\r\n") ;
					}
					else
					{
						$this->DefinitEtatExec("transaction_invalide", "Aucune transaction n'a ete soumise pour paiement.") ;
						$this->AfficheErreurHtml() ;
					}
					exit ;
				}
				else
				{
					// print_r($this->_Transaction) ;
					$this->ExporteFichCfgTransition() ;
				}
			}
			protected function PrepareTransaction()
			{
				$nomSvcAprPaiement = $this->_Transaction->Cfg->NomSvcAprPaiement ;
				if($nomSvcAprPaiement == '' || ! isset($this->_SvcsAprPaiement[$nomSvcAprPaiement]))
				{
					$this->DefinitEtatExecution("svc_apr_paiement_inexistant", "Aucune action n'a ete definie pour le suivi du reglement de la transaction") ;
				}
				else
				{
					$this->_SvcsAprPaiement[$nomSvcAprPaiement]->Prepare($this->_Transaction) ;
				}
			}
			protected function SoumetTransaction()
			{
			}
			protected function TermineTransaction()
			{
			}
			protected function TransactionEnCours()
			{
				return 0 ;
			}
			protected function TransactionEffectuee()
			{
				return $this->_EtatExecution->Id == "termine" || $this->TransactionReussie() || $this->TransactionEchouee() ;
			}
			protected function TransactionReussie()
			{
				return $this->_EtatExecution->Id == "paiement_reussi" ;
			}
			protected function TransactionEchouee()
			{
				return $this->_EtatExecution->Id == "paiement_echoue" || $this->_EtatExecution->Id == "exception_paiement" || $this->_EtatExecution->Id == "paiement_expire" ;
			}
			protected function TransactionAnnulee()
			{
				return $this->_EtatExecution->Id != "annule" || $this->_EtatExecution->Id != "paiement_annule" ;
			}
			protected function ConfirmeTransactionReussieAuto()
			{
				$this->ImporteFichCfgTransition() ;
				if($this->_Transaction->Cfg->NomSvcAprPaiement != '' && isset($this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement]))
				{
					$svcAprPaiement = & $this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement] ;
					if($svcAprPaiement->EstEffectue($this->_Transaction))
					{
						$svcAprPaiement->Rembourse($this->_Transaction) ;
					}
					else
					{
						$svcAprPaiement->ConfirmeSucces($this->_Transaction) ;
					}
				}
				else
				{
					echo '<p style="color:red">'.$this->MsgPaiementNonFinalise.'</p>' ;
					exit ;
				}
			}
			protected function ConfirmeTransactionReussie()
			{
			}
			protected function ConfirmeTransactionEchoueeAuto()
			{
				$this->ImporteFichCfgTransition() ;
				if($this->_Transaction->Cfg->NomSvcAprPaiement != '' && isset($this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement]))
				{
					$svcAprPaiement = & $this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement] ;
					$svcAprPaiement->ConfirmeEchec($this->_Transaction) ;
				}
			}
			protected function ConfirmeTransactionEchouee()
			{
			}
			protected function ConfirmeTransactionAnnuleeAuto()
			{
				if($this->_Transaction->Cfg->NomSvcAprPaiement != '' && isset($this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement]))
				{
					$svcAprPaiement = & $this->_SvcsAprPaiement[$this->_Transaction->Cfg->NomSvcAprPaiement] ;
					$svcAprPaiement->Annule($this->_Transaction) ;
				}
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
					if($this->AfficherErreurs404 == 1)
					{
						Header("HTTP/1.0 401 Unauthorized ".$this->_EtatExecution->Id." ".$this->_EtatExecution->MessageErreur."\r\n") ;
					}
					else
					{
						$this->AfficheErreurHtml() ;
					}
					exit ;
				}
			}
			protected function AfficheErreurHtml()
			{
				echo '<!doctype html>
<html>
<head>
<title>'.$this->Titre.' - Erreur #'.$this->_EtatExecution->Id.'</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body align="center">
<h3>'.$this->Titre.'</h3>
<hr />
<h1>ERREUR #'.$this->_EtatExecution->Id.'</h1>
<p>'.strtoupper($this->MsgEtatExecution()).'</p>
</body>
</html>' ;
			}
			protected function SauveTransactionSession()
			{
				$_SESSION[$this->NomElementApplication."_id_transaction"] = $this->_Transaction->IdTransaction ;
			}
			protected function RestaureTransactionSession()
			{
				$this->_Transaction->IdTransaction = $_SESSION[$this->NomElementApplication."_id_transaction"] ;
				unset($_SESSION[$this->NomElementApplication."_id_transaction"]) ;
			}
			public function Execute()
			{
				$this->VideCfgsTransactsExpirs() ;
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
							$this->ConfirmeTransactionEchoueeAuto() ;
							$this->ConfirmeTransactionEchouee() ;
						}
					}
					elseif($this->TransactionAnnulee())
					{
						$this->ConfirmeTransactionAnnuleeAuto() ;
						$this->ConfirmeTransactionAnnulee() ;
					}
					else
					{
						$this->ConfirmeTransactionEnAttente() ;
					}
					$this->TermineTransaction() ;
					return ;
				}
				$this->DefinitEtatExecution("verification_en_cours", "Verification de la conformite du paiement demande") ;
				$this->DetermineTransactionSoumise() ;
				$this->PrepareTransaction() ;
				if($this->_EtatExecution->Id == "verification_ok")
				{
					$this->SauveTransactionSession() ;
					$this->SoumetTransaction() ;
				}
				else
				{
					$this->ConfirmeTransactionInvalide() ;
					$this->AfficheErreursTransaction() ;
				}
			}
			public function PrepareProcessus()
			{
				$ok = true ;
				if($this->UtiliserBdTransactionSoumise == 1)
				{
					$ok = $this->ExporteTransactSoumiseBd() ;
				}
				else
				{
					$ok = false ;
				}
				if(! $ok)
				{
					$this->ExporteTransactSoumiseSession() ;
				}
				return $ok ;
			}
			public function DemarreProcessus()
			{
				$this->PrepareProcessus() ;
				$urlRedirect = $this->UrlRacine() ;
				if($this->UtiliserBdTransactionSoumise == 1)
				{
					$urlRedirect .= '?idTransactSoumise='.urlencode($this->_Transaction->IdTransaction) ;
				}
				redirect_to($urlRedirect) ;
			}
			public function RemplitTablTransactsPaie(& $tabl)
			{
				$bd = $this->CreeBdTransaction() ;
				$tabl->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$tabl->FournisseurDonnees->BaseDonnees = $this->CreeBdTransaction() ;
				$tabl->FournisseurDonnees->RequeteSelection = $this->NomTableTransaction ;
				$this->FltIdTransactPaie = $tabl->InsereFltSelectHttpGet("id_transaction", "id_transaction = <self>") ;
				$this->FltIdTransactPaie->Libelle = "N&deg; Transaction" ;
				$this->FltDesign = $tabl->InsereFltSelectHttpGet("designation", $bd->SqlIndexOf("designation", "<self>").' > 0') ;
				$this->FltDesign->Libelle = "Designation" ;
				$tabl->SensColonneTri = "desc" ;
				$tabl->InsereDefColCachee("nom_interf_paiemt") ;
				$tabl->InsereDefColTimestamp("timestamp_etat", "Date") ;
				$tabl->InsereDefColHtml('${interf_paiemt}', "Moyen de paiement") ;
				$tabl->InsereDefCol("id_transaction", "N&deg; Transaction") ;
				$tabl->InsereDefCol("designation", "Designation") ;
				$tabl->InsereDefColHtml('${titre_etat}', "Etat") ;
				$tabl->InsereDefCol("montant", "Montant") ;
				$tabl->InsereDefColCachee("id_etat") ;
				$tabl->SourceValeursSuppl = new PvSrvValsSupplTransactInterfPaie() ;
				$tabl->SourceValeursSuppl->InterfPaiemtParent = & $this ;
			}
			public function ControleTransactionsEnAttente()
			{
				if($this->EnregistrerTransaction == 0)
				{
					return ;
				}
				$bd = $this->CreeBdTransaction() ;
				$lgnsTransact = array() ;
				do
				{
					$lgnsTransact = $bd->LimitSqlRows(
						"select * from ".$bd->EscapeTableName($this->NomTableTransaction)." where nom_interf_paiemt=".$bd->ParamPrefix."nom and id_etat not in ('paiement_reussi', 'paiement_annule', 'paiement_echoue', 'paiement_expire') and timestamp_etat + ".$this->DelaiControleTransacts." <= ".date("U"),
						array("nom" => $this->NomElementApplication),
						0, $this->MaxTransactsAControler
					) ;
					if(! is_array($lgnsTransact))
					{
						break ;
					}
					foreach($lgnsTransact as $i => $lgnTransact)
					{
						$this->_Transaction = $this->CreeTransaction() ;
						$this->_Transaction->ImporteParLgn($lgnTransact) ;
						$this->_EtatExecution->Id = $lgnTransact["id_etat"] ;
						$this->_EtatExecution->TimestampCapt = $lgnTransact["timestamp_etat"] ;
						$this->ControleTransactionEnAttente() ;
						if(! in_array($this->_EtatExecution->Id, array('paiement_reussi', 'paiement_annule', 'paiement_echoue', 'paiement_expire')))
						{
							$this->DefinitEtatExecution("verification_ok") ;
						}
						else
						{
							if($this->TransactionEffectuee())
							{
								if($this->TransactionReussie())
								{
									$this->ConfirmeTransactionReussieAuto() ;
									$this->ConfirmeTransactionReussie() ;
								}
								else
								{
									$this->ConfirmeTransactionEchoueeAuto() ;
									$this->ConfirmeTransactionEchouee() ;
								}
							}
							elseif($this->TransactionAnnulee())
							{
								$this->ConfirmeTransactionAnnuleeAuto() ;
								$this->ConfirmeTransactionAnnulee() ;
							}
							$this->TermineTransaction() ;
						}
					}
				}
				while(is_array($lgnsTransact) && count($lgnsTransact) > 0) ;
			}
			protected function ControleTransactionEnAttente()
			{
			}
		}
		
		class PvSrvValsSupplTransactInterfPaie extends PvSrcValsSupplLgnDonnees
		{
			public $InterfPaiemtParent ;
			public function Applique(& $composant, $ligneDonnees)
			{
				$result = parent::Applique($composant, $ligneDonnees) ;
				$interfPaie = & $this->InterfPaiemtParent ;
				// print_r($result) ;
				$interfExt = $interfPaie->ApplicationParent->InterfPaiement($result["nom_interf_paiemt"]) ;
				$result["interf_paiemt"] = "(Non trouv&eacute;)" ;
				if($interfExt != null)
				{
					$result["interf_paiemt"] = $interfExt->Titre ;
				}
				$result["titre_etat"] = (isset($interfPaie->TitresEtatExecution[$result["id_etat"]])) ? $interfPaie->TitresEtatExecution[$result["id_etat"]] : $interfPaie->TitreEtatExecutionNonTrouve ;
				return $result ;
			}
		}
		
	}

?>