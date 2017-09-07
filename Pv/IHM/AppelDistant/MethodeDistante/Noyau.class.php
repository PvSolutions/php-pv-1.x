<?php
	
	if(! defined('PV_METHODE_DISTANTE_NOYAU'))
	{
		define('PV_METHODE_DISTANTE_NOYAU', 1) ;
		
		class PvParamDistant
		{
			public $Args ;
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
		
		class PvErreurResultDistant
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
		class PvResultDistant
		{
			public $erreur ;
			public $valeur ;
			public function  __construct()
			{
				$this->erreur = new PvErreurResultDistant() ;
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
			public function Succes()
			{
				return $this->erreur->code === 0 ;
			}
		}
		
		class PvMethodeDistanteNoyau extends PvElemZone
		{
			protected $_Param ;
			protected $_Result ;
			public $NomElementComposantIU ;
			public $ComposantIUParent ;
			public $CommandeParent ;
			public $ElemFormParent ;
			public $DefColParent ;
			public $MessageSuccesExecution = "L'action a &eacute;t&eacute; execut&eacute;e avec succ&egrave;s" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ComposantIUParent = new PvComposantIUIndef() ;
			}
			protected function CreeParam()
			{
				return new PvParamDistant() ;
			}
			protected function CreeResult()
			{
				return new PvResultDistant() ;
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
				if($valeur == '')
				{
					$valeur = $this->MessageSuccesExecution ;
				}
				$this->_Result->ConfirmeSucces($valeur) ;
			}
			public function RenseigneErreur($code, $msg="", $alias="", $params=array(), $surlignes=array())
			{
				$this->_Result->RenseigneErreur($code, $msg, $alias, $params, $surlignes) ;
			}
			public function ObtientUrl($params=array())
			{
			}
			public function AppelJs($params=array())
			{
			}
		}
		
		class PvMtdDistNonTrouvee extends PvMethodeDistanteNoyau
		{
			protected function ExecuteInstructions()
			{
				$this->RenseigneErreur(-2, $this->ZoneParent->MessageMtdDistNonTrouvee, "remote_method_not_found") ;
			}
		}
		
		class PvMtdDistFormDonnees extends PvMethodeDistanteNoyau
		{
			public function PrepareExecution()
			{
				$this->ComposantIUParent->LieTousLesFiltres() ;
			}
		}
		class PvMtdDistTablDonnees extends PvMethodeDistanteNoyau
		{
			public function PrepareExecution()
			{
				$this->ComposantIUParent->LieTousLesFiltres() ;
			}
		}
	}
	
?>