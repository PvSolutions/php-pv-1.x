<?php
	
	if(! defined('PV_METHODE_DISTANTE_NOYAU'))
	{
		define('PV_METHODE_DISTANTE_NOYAU', 1) ;
		
		class PvComposantIUIndefAppelDistant extends PvObjet
		{
			public $Visible = 1 ;
			public function AdopteZone($nom, & $zone)
			{
			}
			public function & ZoneParent()
			{
				$val = null ;
				return $val ;
			}
		}
		
		class PvParamAppelDistant
		{
			public $Args ;
			public $NomClasseArgs ;
			public function Arg($nom, $valeurDefaut=null)
			{
				if(! is_object($this->Args))
				{
					return $valeurDefaut ;
				}
				return (isset($this->Args->$nom)) ? $this->Args->$nom : $valeurDefaut ;
			}
			public function FiltreEdition($nom, $valeurDefaut=null)
			{
				if(! is_object($this->Args) || ! isset($this->Args->filtresEdition))
				{
					return $valeurDefaut ;
				}
				return (isset($this->Args->filtresEdition->$nom)) ? $this->Args->filtresEdition->$nom : $valeurDefaut ;
			}
			public function FiltreSelect($nom, $valeurDefaut=null)
			{
				if(! is_object($this->Args) || ! isset($this->Args->filtresSelect))
				{
					return $valeurDefaut ;
				}
				return (isset($this->Args->filtresSelect->$nom)) ? $this->Args->filtresSelect->$nom : $valeurDefaut ;
			}
			public function FiltreGlobalSelect($nom, $valeurDefaut=null)
			{
				if(! is_object($this->Args) || ! isset($this->Args->filtresGlobauxSelect))
				{
					return $valeurDefaut ;
				}
				return (isset($this->Args->filtresGlobauxSelect->$nom)) ? $this->Args->filtresGlobauxSelect->$nom : $valeurDefaut ;
			}
			public function FiltreLigneSelect($nom, $valeurDefaut=null)
			{
				if(! is_object($this->Args) || ! isset($this->Args->filtresLignesSelect))
				{
					return $valeurDefaut ;
				}
				return (isset($this->Args->filtresLignesSelect->$nom)) ? $this->Args->filtresLignesSelect->$nom : $valeurDefaut ;
			}
		}
		
		class PvErreurResultAppelDistant
		{
			public $code = -1 ;
			public $message = "Non defini" ;
			public $alias = "undefined" ;
			public $params = array() ;
			public $surlignes = array() ;
			public function ConfirmeSucces()
			{
				$this->code = 0 ;
				$this->message = null ;
				$this->alias = null ;
				$this->params = array() ;
				$this->surlignes = array() ;
			}
			public function Renseigne($code, $msg='', $alias='', $params=array(), $surlignes=array())
			{
				$this->code = $code ;
				$this->message = $msg ;
				$this->alias = ($alias == '') ? 'error_found' : $alias ;
				$this->params = $params ;
				$this->surlignes = $surlignes ;
			}
		}
		class PvResultAppelDistant
		{
			public $erreur ;
			public $valeur ;
			public function  __construct()
			{
				$this->erreur = new PvErreurResultAppelDistant() ;
			}
			public function RenseigneErreur($code, $msg='', $alias='', $params=array(), $surlignes=array())
			{
				$this->valeur = null ;
				$this->erreur->Renseigne($code, $msg, $alias, $params, $surlignes) ;
			}
			public function ConfirmeSucces($valeur)
			{
				$this->valeur = $valeur ;
				$this->erreur->code = 0 ;
				$this->erreur->message = null ;
				$this->erreur->alias = null ;
				$this->erreur->params = array() ;
				$this->erreur->surlignes = array() ;
			}
			public function ErreurTrouvee()
			{
				return $this->erreur->code !== 0 ;
			}
			public function ErreurDefinie()
			{
				return $this->ErreurTrouvee() && ($this->erreur->code !== -1 || $this->erreur->alias !== "undefined") ;
			}
			public function Succes()
			{
				return $this->erreur->code === 0 ;
			}
			public function EstSucces()
			{
				return $this->Succes() ;
			}
		}
		
		class PvMethodeDistanteBase extends PvElemZoneAppelDistant
		{
			protected $_Param ;
			protected $_Result ;
			public $NomElementComposantIU ;
			public $ComposantIUParent ;
			public $CommandeParent ;
			public $ElemFormParent ;
			public $DefColParent ;
			public $MessageSuccesExecution = "L'action a &eacute;t&eacute; execut&eacute;e avec succ&egrave;s" ;
			public $StructRequete ;
			public $StructReponse ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ComposantIUParent = new PvComposantIUIndefAppelDistant() ;
				$this->StructRequete = new PvStructMsgAppelDistant() ;
				$this->StructReponse = new PvStructMsgAppelDistant() ;
				$this->DefinitStructMessages() ;
			}
			protected function DefinitStructMessages()
			{
			}
			protected function CreeParam()
			{
				return new PvParamAppelDistant() ;
			}
			protected function CreeResult()
			{
				return new PvResultAppelDistant() ;
			}
			public function AdopteComposantIU($nom, & $composantIU)
			{
				$this->NomElementComposantIU = $nom ;
				$this->ComposantIUParent = & $composantIU ;
			}
			protected function InitMessages($args)
			{
				$this->_Param = $this->CreeParam() ;
				$this->_Param->Args = $args ;
				$this->_Result = $this->CreeResult() ;
			}
			public function Execute($args=array())
			{
				$this->InitMessages($args) ;
				$this->PrepareExecution() ;
				if($this->ErreurDefinie())
				{
					$this->TermineExecution() ;
					return ;
				}
				if($this->EstNul($this->CommandeParent))
				{
					$this->ExecuteInstructions() ;
				}
				else
				{
					$this->CommandeParent->Execute() ;
					if($this->CommandeParent->EstErreur())
					{
						$this->RenseigneErreur(2, $this->CommandeParent->MessageExecution) ;
					}
					else
					{
						$this->ConfirmeSucces($this->CommandeParent->MessageExecution) ;
					}
				}
				$this->TermineExecution() ;
			}
			public function ExecuteCommandeParent()
			{
				
			}
			protected function PrepareExecution()
			{
			}
			protected function ExecuteInstructions()
			{
			}
			protected function TermineExecution()
			{
			}
			public function & Param()
			{
				return $this->_Param ;
			}
			public function & Result()
			{
				return $this->_Result ;
			}
			public function ArgParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->Arg($nom, $valeurDefaut) ;
			}
			public function FiltreEditionParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->FiltreEdition($nom, $valeurDefaut) ;
			}
			public function FiltreSelectParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->FiltreSelect($nom, $valeurDefaut) ;
			}
			public function FiltreSelectionParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->FiltreSelect($nom, $valeurDefaut) ;
			}
			public function FiltreLigneSelectParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->FiltreLigneSelect($nom, $valeurDefaut) ;
			}
			public function FiltreLgSelectParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->FiltreLigneSelect($nom, $valeurDefaut) ;
			}
			public function FiltreGlobalSelectParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->FiltreGlobalSelect($nom, $valeurDefaut) ;
			}
			public function FiltreGlobSelectParam($nom, $valeurDefaut = null)
			{
				return $this->_Param->FiltreGlobalSelect($nom, $valeurDefaut) ;
			}
			public function ConfirmeSucces($valeur='')
			{
				if(is_null($valeur) || $valeur === '')
				{
					$valeur = $this->MessageSuccesExecution ;
				}
				$this->_Result->ConfirmeSucces($valeur) ;
			}
			public function RenseigneErreur($code, $msg="", $alias="", $params=array(), $surlignes=array())
			{
				$this->_Result->RenseigneErreur($code, $msg, $alias, $params, $surlignes) ;
			}
			public function ErreurTrouvee()
			{
				return $this->_Result->ErreurTrouvee() ;
			}
			public function ErreurDefinie()
			{
				return $this->_Result->ErreurDefinie() ;
			}
			public function ObtientUrl($params=array())
			{
			}
			public function AppelJs($params=array())
			{
			}
		}
		
		class PvMtdDistNonTrouvee extends PvMethodeDistanteBase
		{
			protected function ExecuteInstructions()
			{
				$this->RenseigneErreur(-2, $this->ZoneParent->MessageMtdDistNonTrouvee, "remote_method_not_found") ;
			}
		}
		
		class PvMtdDistFormDonnees extends PvMethodeDistanteBase
		{
			public function PrepareExecution()
			{
				$this->ComposantIUParent->LieTousLesFiltres() ;
			}
		}
		class PvMtdDistTablDonnees extends PvMethodeDistanteBase
		{
			public function PrepareExecution()
			{
				$this->ComposantIUParent->LieTousLesFiltres() ;
			}
		}
		
		class PvArgsDistPrepareProcessPaiement
		{
			public $nomInterfPaie ;
			public $nomSvcAprPaie ;
			public $designation ;
			public $montant ;
			public $monnaie ;
			public $argTransact01 ;
			public $argTransact02 ;
			public $argTransact03 ;
			public $argTransact04 ;
			public $argTransact05 ;
			public $argTransact06 ;
			public $argTransact07 ;
			public $argTransact08 ;
			public function __construct(& $mtd)
			{
				$this->nomInterfPaie = $mtd->ArgParam("nomInterfPaie") ;
				$this->nomSvcAprPaie = $mtd->ArgParam("nomSvcAprPaie") ;
				$this->designation = $mtd->ArgParam("designation") ;
				$this->montant = $mtd->ArgParam("montant") ;
				$this->monnaie = $mtd->ArgParam("monnaie", $mtd->MonnaieParDefaut) ;
				$this->argTransact01 = $mtd->ArgParam("arg_transact_01") ;
				$this->argTransact02 = $mtd->ArgParam("arg_transact_02") ;
				$this->argTransact03 = $mtd->ArgParam("arg_transact_03") ;
				$this->argTransact04 = $mtd->ArgParam("arg_transact_04") ;
				$this->argTransact05 = $mtd->ArgParam("arg_transact_05") ;
				$this->argTransact06 = $mtd->ArgParam("arg_transact_06") ;
				$this->argTransact07 = $mtd->ArgParam("arg_transact_07") ;
				$this->argTransact08 = $mtd->ArgParam("arg_transact_08") ;
			}
		}
		class PvMtdDistPrepareProcessPaiement extends PvMethodeDistanteBase
		{
			public $MonnaieParDefaut = "XOF" ;
			public $ArgsPrepareProcess ;
			protected function ValidePreparationProcess()
			{
			}
			protected function ExecuteInstructions()
			{
				$app = $this->ApplicationParent() ;
				$this->ArgsPrepareProcess = new PvArgsDistPrepareProcessPaiement($this) ;
				if(! $app->ExisteInterfPaiement($this->ArgsPrepareProcess->nomInterfPaie))
				{
					$this->RenseigneErreur(-2, "Interface de paiement inexistante") ;
					return ;
				}
				if($montant === 0)
				{
					$this->RenseigneErreur(1, "Le montant de la transaction doit etre superieur a 0") ;
					return ;
				}
				$this->ValidePreparationProcess() ;
				if($this->ErreurDefinie())
				{
					return ;
				}
				$interfPaiement = $app->InterfPaiement($this->ArgsPrepareProcess->nomInterfPaie) ;
				$transaction = $interfPaiement->Transaction() ;
				$transaction->Montant = $this->ArgsPrepareProcess->montant ;
				$transaction->Monnaie = $this->ArgsPrepareProcess->monnaie ;
				$transaction->Designation = $this->ArgsPrepareProcess->designation ;
				$transaction->Cfg->NomSvcAprPaiement = $this->ArgsPrepareProcess->nomSvcAprPaie ;
				$transaction->Cfg->Arg01 = $this->ArgsPrepareProcess->argTransact01 ;
				$transaction->Cfg->Arg02 = $this->ArgsPrepareProcess->argTransact02 ;
				$transaction->Cfg->Arg03 = $this->ArgsPrepareProcess->argTransact03 ;
				$transaction->Cfg->Arg04 = $this->ArgsPrepareProcess->argTransact04 ;
				$transaction->Cfg->Arg05 = $this->ArgsPrepareProcess->argTransact05 ;
				$transaction->Cfg->Arg06 = $this->ArgsPrepareProcess->argTransact06 ;
				$transaction->Cfg->Arg07 = $this->ArgsPrepareProcess->argTransact07 ;
				$transaction->Cfg->Arg08 = $this->ArgsPrepareProcess->argTransact08 ;
				$interfPaiement->PrepareProcessus() ;
				$this->ConfirmeSucces($interfPaiement->ObtientUrl()."?idTransactSoumise=".urlencode( $interfPaiement->IdTransaction())) ;
			}
		}
		
	}
	
?>