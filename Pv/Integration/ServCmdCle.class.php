<?php
	
	if(! defined('PV_INTEGR_SVC_CMD_CLE'))
	{
		define('PV_INTEGR_SVC_CMD_CLE', 1) ;
		
		class PvServiceCmdCle extends PvIntegration
		{
			protected $_Commandes = array() ;
			protected $_Tunnels = array() ;
			public $NomTunnelAttrib = "" ;
			public $NomTunnelRendu = "" ;
			public $LgnMembreConnecte = null ;
			protected $_Requete ;
			protected $_Reponse ;
			protected $NomCommandeActive ;
			protected $_MotCleCommande ;
			public $InscrireTunnelsAuto = 1 ;
			public $TunnelRedirHttp ;
			public $TunnelMobile ;
			public $ChemRelZoneRedirHttp = "" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->_Requete = new PvRequeteServCmdCle() ;
				$this->_Reponse = new PvReponseServCmdCle() ;
			}
			public function DefinitTunnels($nomTunnel)
			{
				$this->TunnelAttrib = $nomTunnel ;
				$this->TunnelRendu = $nomTunnel ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeTunnelsAuto() ;
				$this->ChargeTunnels() ;
				$this->ChargeCommandes() ;
			}
			public function & Requete()
			{
				return $this->_Requete ;
			}
			public function & Reponse()
			{
				return $this->_Reponse ;
			}
			public function ConfirmeSuccesReponse($msgSucces, $paramsSucces=array())
			{
				$this->_Reponse->ConfirmeSucces($msgSucces, $paramsSucces) ;
			}
			public function RenseigneErreurReponse($codeErreur, $msgErreur, $aliasErreur="", $paramsErreur=array())
			{
				$this->_Reponse->RenseigneErreur($codeErreur, $msgErreur, $aliasErreur, $paramsErreur) ;
			}
			public function ErreurReponseTrouvee()
			{
				return $this->_Reponse->ErreurTrouvee() ;
			}
			public function ErreurReponseDefinie()
			{
				return $this->_Reponse->ErreurDefinie() ;
			}
			protected function CreeTunnelRedirHttp()
			{
				return new PvTunnelRedirHttpServCmdCle() ;
			}
			protected function CreeTunnelRedirMobile()
			{
				return new PvTunnelMobileServCmdCle() ;
			}
			protected function ChargeTunnelsAuto()
			{
				$this->TunnelRedirHttp = $this->InsereTunnel("redir_http", $this->CreeTunnelRedirHttp()) ;
				$this->TunnelMobile = $this->InsereTunnel("mobile", $this->CreeTunnelRedirMobile()) ;
			}
			public function & InsereTunnel($nomTunnel, $tunnel)
			{
				$this->Tunnels[$nomTunnel] = & $tunnel ;
				return $tunnel ;
			}
			public function & Tunnels()
			{
				return $this->_Tunnels ;
			}
			protected function ChargeTunnels()
			{
			}
			protected function ChargeCommandes()
			{
			}
			public function & Tunnel($nomTunnel)
			{
				$cles = array_keys($this->Tunnels) ;
				$nomTunnelSelect = $cles[0] ;
				if($nomTunnel != "" && isset($this->Tunnels[$nomTunnel]))
				{
					$nomTunnelSelect = $nomTunnel ;
				}
				return $this->Tunnels[$nomTunnelSelect] ;
			}
			public function & TunnelAttrib()
			{
				return $this->Tunnel($this->NomTunnelAttrib) ;
			}
			public function & TunnelRendu()
			{
				return $this->Tunnel($this->NomTunnelRendu) ;
			}
			public function CreeLgnMembreConnecte()
			{
				return PvLgnMembreServCmdCle() ;
			}
			public function & CommandeActive()
			{
				return $this->Commande($this->NomCommandeActive) ;
			}
			public function & Commande($nom)
			{
				$cmd = new PvCmdNonTrouveeServCmdCle() ;
				$cmd->AdopteIntegration("non_trouve", $this) ;
				if($nom != "" && isset($this->_Commandes[$nom]))
				{
					$cmd = & $this->_Commandes[$nom] ;
				}
				return $cmd ;
			}
			public function ExisteCommande($nomCommande)
			{
				return (isset($this->_Commandes[$nomCommande])) ;
			}
			public function & Commandes()
			{
				return $this->_Commandes ;
			}
			public function & InsereCommande($nomCommande, $commande)
			{
				return $this->InscritCommande($nomCommande, $commande) ;
			}
			public function & InscritCommande($nomCommande, & $commande)
			{
				$this->_Commandes[$nomCommande] = & $commande ;
				$commande->AdopteIntegration($nomCommande, $this) ;
				return $commande ;
			}
			protected function RemplitApplicationSpec(& $app)
			{
				parent::RemplitApplicationSpec($app) ;
				$this->ZoneRedirHttp = $this->InsereIHM("http_redir", new PvZoneRedirHttpServCmdCle(), $app) ;
				$this->ZoneRedirHttp->CheminFichierRelatif = $this->ChemRelZoneRedirHttp ;
			}
			public function TraiteCommande()
			{
				$this->PrepareExecution() ;
				$this->DetecteCommandeActive() ;
				$this->ExecuteCommandeActive() ;
				$this->TermineExecution() ;
			}
			protected function PrepareExecution()
			{
				$this->TunnelAttrib()->AttribRequete($this->_Requete, $this) ;
			}
			protected function DetecteCommandeActive()
			{
				$this->_MotCleCommande = $this->_Requete->MotCleCommande() ;
				$this->NomCommandeActive = "" ;
				foreach($this->_Commandes as $nom => $cmd)
				{
					if($cmd->AccepteMotCle($this->_MotCleCommande))
					{
						$this->NomCommandeActive = $nom ;
						break ;
					}
				}
			}
			protected function ExecuteCommandeActive()
			{
				$cmd = $this->CommandeActive() ;
				$cmd->ChargeConfig() ;
				$cmd->Execute() ;
			}
			protected function TermineExecution()
			{
				$this->TunnelAttrib()->RenduReponse($this->_Reponse, $this) ;
			}
		}
		
		class PvZoneRedirHttpServCmdCle extends PvZoneWebSimple
		{
			protected function ChargeScripts()
			{
				parent::ChargeScripts() ;
				$this->InsereScriptParDefaut(new PvScriptRedirHttpServCmdCle()) ;
			}
		}
		class PvScriptRedirHttpServCmdCle extends PvScriptWebSimple
		{
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$integr = $this->ZoneParent->IntegrationParent() ;
				$integr->TraiteCommande() ;
				$ctn .= $integr->Reponse()->FormatHtml() ;
				return $ctn ;
			}
		}
		
		class PvTunnelBaseServCmdCle extends PvObjet
		{
			public function Msisdn()
			{
				return "" ;
			}
			public function AttribRequete(& $requete, & $integr)
			{
			}
			public function RenduReponse(& $reponse, & $integr)
			{
				echo $reponse->FormatTexte() ;
			}
		}
		class PvTunnelRedirHttpServCmdCle extends PvTunnelBaseServCmdCle
		{
			public $NomParamSOA = "SOA" ;
			public $NomParamDA = "DA" ;
			public $NomParamContent = "Content" ;
			public $NomParamCtrl = "" ;
			public $ValeurParamCtrl = "" ;
			public $AfficherReponse = 0 ;
			public function Msisdn()
			{
				return _REQUEST_def($this->NomParamSOA) ;
			}
			// public $
			public function AttribRequete(& $requete, & $integr)
			{
				$requete->Expressions = explode("*", strtoupper(_REQUEST_def($this->NomParamContent))) ;
			}
			public function RenduReponse(& $reponse, & $integr)
			{
				if($this->AfficherReponse == 1)
				{
					echo $reponse->FormatHtml() ;
					exit ;
				}
			}
		}
		class PvTunnelMobileServCmdCle extends PvTunnelBaseServCmdCle
		{
			public function Msisdn()
			{
				return _SESSION_def("msisdn_auth") ;
			}
			public function AttribRequete(& $requete, & $integr)
			{
				$nomCmd = _POST_def("cmd") ;
				$cmd = $integr->Commande($nomCmd) ;
				$requete->Expressions[] = $nomCmd ;
				foreach($cmd->ExpressionsSyntaxe() as $i => $expr)
				{
					$requete->Expressions[] = _POST_def($expr->NomParam) ;
				}
			}
			public function RenduReponse(& $reponse, & $integr)
			{
			}
		}
		
		class PvRequeteServCmdCle
		{
			public $Expressions = array() ;
			public function MotCleCommande()
			{
				return $this->Expression(0) ;
			}
			public function Expression($index)
			{
				if(count($this->Expressions) < $index)
				{
					return "" ;
				}
				return strtoupper($this->Expressions[$index]) ;
			}
		}
		class PvReponseServCmdCle
		{
			public $CodeErreur = -1 ;
			public $MsgErreur = "REPONSE NON INITIALISEE." ;
			public $AliasErreur = "not_initialized" ;
			public $ParamsErreur = array() ;
			public $MsgSucces = "" ;
			public $ParamsSucces = array() ;
			public function ErreurTrouvee()
			{
				return $this->CodeErreur != 0 ;
			}
			public function Succes()
			{
				return $this->CodeErreur == 0 ;
			}
			public function RenseigneErreur($codeErreur, $msgErreur, $aliasErreur="not_defined", $paramsErreur=array())
			{
				$this->MsgSucces = null ;
				$this->ParamsSucces = null ;
				$this->CodeErreur = $codeErreur ;
				$this->MsgErreur = $msgErreur ;
				$this->AliasErreur = $aliasErreur ;
				$this->ParamsErreur = $paramsErreur ;
			}
			public function ConfirmeSucces($msgSucces, $paramsSucces=array())
			{
				$this->MsgSucces = $msgSucces ;
				$this->ParamsSucces = $paramsSucces ;
				$this->CodeErreur = 0 ;
				$this->MsgErreur = "" ;
				$this->AliasErreur = "" ;
				$this->ParamsErreur = array() ;
			}
			protected function AppliqueParamsHtml($format, $params=array())
			{
				$result = $format ;
				if($params != null)
				{
					foreach($params as $nom => $val)
					{
						$result = str_replace('${'.$nom.'}', '<span class="surligne">'.htmlentities($val).'</span>', $result) ;
					}
				}
				return $result ;
			}
			protected function AppliqueParamsTexte($format, $params=array())
			{
				$result = $format ;
				if($params != null)
				{
					foreach($params as $nom => $val)
					{
						$result = str_replace('${'.$nom.'}', htmlentities($val), $result) ;
					}
				}
				return $result ;
			}
			public function FormatTexte()
			{
				$result = ($this->ErreurTrouvee()) ? $this->MsgErreur : $this->MsgSucces ;
				$params = ($this->ErreurTrouvee()) ? $this->ParamsErreur : $this->ParamsSucces ;
				return $this->AppliqueParamsTexte($result, $params) ;
			}
			public function FormatHtml()
			{
				$classeCss = ($this->ErreurTrouvee()) ? "Erreur" : "Succes" ;
				$result = ($this->ErreurTrouvee()) ? $this->MsgErreur : $this->MsgSucces ;
				$params = ($this->ErreurTrouvee()) ? $this->ParamsErreur : $this->ParamsSucces ;
				return '<span class="'.$classeCss.'">'.$this->AppliqueParamsHtml($result, $params).'</span>' ;
			}
		}
		
		class PvExprServCmdCle extends PvObjet
		{
			public $IndexElementCommande ;
			public $CommandeParent ;
			public $NomParam ;
			public $NomClasseComposant ;
			public $TitreGlobal ;
			public $LibelleEdit ;
			public $TitreList ;
			public $Visible = 1 ;
			public $Editable = 1 ;
			public $Consultable = 1 ;
			public $Valeur = "" ;
			public function ObtientTitreList()
			{
				return (($this->TitreList) != '') ? $this->TitreList : $this->TitreGlobal ;
			}
			public function ObtientLibelleEdit()
			{
				return (($this->LibelleEdit) != '') ? $this->LibelleEdit : $this->TitreGlobal ;
			}
			public function AdopteCommmande($index, & $commande)
			{
				$this->IndexElementCommande = $index ;
				$this->CommandeParent = & $commande ;
			}
			public function & IntegrationParent()
			{
				return $this->CommandeParent->IntegrationParent ;
			}
			public function & ApplicationParent()
			{
				return $this->CommandeParent->ApplicationParent() ;
			}
			public function ChargeComposant(& $comp)
			{
			}
			public function ChargeDefCol(& $defCol)
			{
			}
		}
		
		class PvCmdBaseServCmdCle extends PvObjet
		{
			public $NomElemIntegration ;
			public $IntegrationParent ;
			public $MotsClesAcceptes = array() ;
			protected $_Expressions = array() ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeExpressions() ;
			}
			protected function ChargeExpressions()
			{
			}
			public function AccepteMotCle($motCle)
			{
				return (strtoupper($this->NomElemIntegration) == $motCle) || in_array($motCle, $this->MotsClesAcceptes) ;
			}
			public function & Requete()
			{
				return $this->IntegrationParent->Requete() ;
			}
			public function & Reponse()
			{
				return $this->IntegrationParent->Reponse() ;
			}
			public function CodeErreurReponse()
			{
				return $this->IntegrationParent->Reponse()->CodeErreur ;
			}
			public function ErreurDefinie()
			{
				return ($this->ErreurTrouvee() && $this->CodeErreurReponse() != -1) ;
			}
			public function ErreurTrouvee()
			{
				return $this->IntegrationParent->Reponse()->ErreurTrouvee() ;
			}
			public function ErreurReponseTrouvee()
			{
				return $this->IntegrationParent->Reponse()->ErreurTrouvee() ;
			}
			public function ConfirmeSuccesReponse($msgSucces, $paramsSucces=array())
			{
				$this->Reponse()->ConfirmeSucces($msgSucces, $paramsSucces) ;
			}
			public function RenseigneErreurReponse($codeErreur, $msgErreur, $aliasErreur="", $paramsErreur=array())
			{
				$this->Reponse()->RenseigneErreur($codeErreur, $msgErreur, $aliasErreur, $paramsErreur) ;
			}
			public function ConfirmeSucces($msgSucces, $paramsSucces=array())
			{
				$this->Reponse()->ConfirmeSucces($msgSucces, $paramsSucces) ;
			}
			public function RenseigneErreur($codeErreur, $msgErreur, $aliasErreur="", $paramsErreur=array())
			{
				$this->Reponse()->RenseigneErreur($codeErreur, $msgErreur, $aliasErreur, $paramsErreur) ;
			}
			public function & InsereExpression($nom, $expr=null)
			{
				if($expr == null)
				{
					$expr = new PvExprServCmdCle() ;
				}
				return $this->InscritExpression($nom, $expr) ;
			}
			public function & InscritExpression($nom, & $expr)
			{
				$index = count($this->_Expressions) ;
				$expr->NomParam = $nom ;
				$expr->TitreGlobal = $nom ;
				$this->_Expressions[$index] = & $expr ;
				$expr->AdopteCommmande($index, $this) ;
				return $expr ;
			}
			public function AdopteIntegration($nom, & $proc)
			{
				$this->NomElemIntegration = $nom ;
				$this->IntegrationParent = & $proc ;
			}
			public function & TunnelAttrib()
			{
				return $this->IntegrationParent->TunnelAttrib() ;
			}
			public function & TunnelRendu()
			{
				return $this->IntegrationParent->TunnelRendu() ;
			}
			public function & Tunnel($nom)
			{
				return $this->IntegrationParent->Tunnel($nom) ;
			}
			public function & ApplicationParent()
			{
				return $this->IntegrationParent->ApplicationParent ;
			}
			public function IHM($nom)
			{
				return $this->IntegrationParent->ApplicationParent->IHMs[$nom] ;
			}
			public function Execute()
			{
				$this->PrepareExecution() ;
				if($this->ErreurDefinie())
				{
					return ;
				}
				$this->ExecuteInstructions() ;
				$this->TermineExecution() ;
			}
			protected function PrepareExecution()
			{
				$exprs = $this->ExpressionsSyntaxe() ;
				foreach($exprs as $i => $expr)
				{
					$exprs[$i]->Valeur = $this->Requete()->Expression($i + 1) ;
				}
			}
			public function ExpressionsSyntaxe()
			{
				$result = array() ;
				foreach($this->_Expressions as $i => $expr)
				{
					if($expr->Visible == 0 || $expr->Editable == 0)
					{
						continue ;
					}
					$result[] = $this->_Expressions[$i] ;
				}
				return $result ;
			}
			protected function ExecuteInstructions()
			{
			}
			protected function TermineExecution()
			{
			}
			public function ValeursExpression()
			{
				$vals = array() ;
				foreach($this->_Expressions as $i => $expr)
				{
					$vals[$expr->NomParam] = $expr->Valeur ;
				}
				return $vals ;
			}
			public function ValeursExpr()
			{
				return $this->ValeursExpression() ;
			}
		}
		class PvCommandeBaseServCmdCle extends PvCmdBaseServCmdCle
		{
		}
		
		class PvCmdNonTrouveeServCmdCle extends PvCmdBaseServCmdCle
		{
			public $MsgErreur = "SYNTAXE INCORRECTE" ;
			public $MsgAlias = "syntaxe_incorrect" ;
			public $MsgParams = array() ;
			protected function ExecuteInstructions()
			{
				$this->RenseigneErreurReponse(-1, $this->MsgErreur) ;
			}
		}
	}
	
?>