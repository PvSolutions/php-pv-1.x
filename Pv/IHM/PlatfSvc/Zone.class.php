<?php
	
	if(! defined('PV_ZONE_PLATF_SVC_WEB'))
	{
		define('PV_ZONE_PLATF_SVC_WEB', 1) ;
		
		class PvResultGenerClientWebPlatfSvc
		{
			public $DateDebut ;
			public $TotalFichGener ;
			public $DateFin ;
		}
		class PvCfgClientWebPlatfSvc
		{
			public $CheminDossierRacine = "client_web" ;
			public $ExtHtml = "html" ;
			public $ScriptsCertifies = 0 ;
			public $NomScriptsRejet = array() ;
			public $NomFichierJS = "pv-platf-svc.js" ;
		}
		
		class PvZonePlatfSvcWeb extends PvZoneWebSimple
		{
			public $CfgClientWeb ;
			public $EstClientWeb = 0 ;
			public $NomClasseMembership = "PvMembershipPlatfSvcWeb" ;
			public $NomClasseScriptRecouvreMP = "PvScriptRecvrMPPlatfSvcWeb" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionPlatfSvcWeb" ;
			protected $GenerClientWebEnCours = 0 ;
			public $PrivilegesClientWeb = array() ;
			public $NomScriptParDefaut = "index" ;
			public $InclureScriptsMembership = 1 ;
			public function NomParamIdMembreSession()
			{
				return "idMembreConnecte" ;
			}
			protected function CreeCfgClientWeb()
			{
				return new PvCfgClientWebPlatfSvc() ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CfgClientWeb = $this->CreeCfgClientWeb() ;
			}
			protected function ChargeScriptsMembership()
			{
				parent::ChargeScriptsMembership() ;
				if(! $this->PossedeMembreConnecte())
				{
					$this->ChargeScriptsMSConnecte() ;
				}
			}
			protected function AfficheRenduInacessible()
			{
				if($this->EstClientWeb == 1)
				{
					return ;
				}
				parent::AfficheRenduInacessible() ;
			}
			protected function AfficheRenduNonTrouve()
			{
				if($this->EstClientWeb == 1)
				{
					return ;
				}
				parent::AfficheRenduNonTrouve() ;
			}
			public function GenereClientWeb($debutScript=0, $maxScripts=16)
			{
				$this->GenerClientWebEnCours = 1 ;
				$this->GenereFichsCSSClientWeb() ;
				$this->GenereFichsJSClientWeb() ;
				$nomScripts = array_keys($this->Scripts) ;
				if($maxScripts > 0 && $debutScript > -1)
				{
					if($debutScript > 0)
					{
						array_splice($nomScripts, 0, $debutScript) ;
					}
					if($maxScripts < count($this->Scripts) - 1)
					{
						array_splice($nomScripts, $maxScripts) ;
					}
				}
				$nomScriptRendu = $this->ScriptPourRendu->NomElementZone ;
				foreach($nomScripts as $i => $nomScript)
				{
					$script = & $this->Scripts[$nomScript] ;
					if($script->NomElementZone == $nomScriptRendu || in_array($nomScript, $this->CfgClientWeb->NomScriptsRejet))
					{
						continue ;
					}
					$app = $this->ApplicationParent->CreeInstanceGener() ;
					$app->AutoDetectChemRelFichierActif = 0 ;
					$app->CheminFichierElementActifFixe = $_SERVER["SCRIPT_FILENAME"] ;
					$app->ChargeConfig() ;
					$zoneWeb = & $app->Elements[$this->NomElementApplication] ;
					$zoneWeb->AutoDetectParamScriptAppele = 0 ;
					$zoneWeb->ValeurParamScriptAppeleFixe = $nomScript ;
					$zoneWeb->EstClientWeb = 1 ;
					$app->DetecteElementActif() ;
					ob_start() ;
					$app->ExecuteElementActif() ;
					$ctn = ob_get_clean() ;
					$this->EcritFicScriptClientWeb($nomScript, $ctn) ;
				}
				$this->GenerClientWebEnCours = 0 ;
			}
			public function InclutLibrairiesExternes()
			{
				$this->InclureJQuery = 1 ;
				parent::InclutLibrairiesExternes() ;
				$this->InscritLienJs($this->CfgClientWeb->NomFichierJS) ;
				// Empecher d'acceder a la page de connexion si l'utilisateur est deja connecté
				if(in_array($this->ValeurParamScriptAppele, array($this->NomScriptConnexion, $this->NomScriptRecouvreMP)))
				{
					$this->InscritContenuJS($this->CtnJSRejetNonConnecte()) ;
				}
			}
			protected function CtnJSRejetDejaConnecte()
			{
				return 'jQuery(function() {
	var idMembreConnecte = PvPlatfSvc.sessionStorage.getKey('.svc_json_encode($this->NomParamIdMembreSession()).', 0) ;
	if(idMembreConnecte !== 0) {
		PvPlatfSvc.adressePage.redirige('.svc_json_encode($this->ScriptParDefaut->ObtientUrl()).') ;
	}
})' ;
			}
			protected function CtnJSRejetNonConnecte()
			{
				return 'jQuery(function() {
	var idMembreConnecte = PvPlatfSvc.sessionStorage.getKey('.svc_json_encode($this->NomParamIdMembreSession()).', 0) ;
	if(idMembreConnecte === 0) {
		PvPlatfSvc.adressePage.redirige('.svc_json_encode($this->ScriptParDefaut->ObtientUrl()).') ;
	}
})' ;
			}
			protected function GenereFichsJSClientWeb()
			{
				copy(dirname(__FILE__)."/".$this->CfgClientWeb->NomFichierJS, $this->ObtientChemFicJSClientWeb()) ;
			}
			protected function GenereFichsCSSClientWeb()
			{
			}
			public function ObtientChemRacineClientWeb()
			{
				return $this->CfgClientWeb->CheminDossierRacine ;
			}
			public function ObtientChemScriptClientWeb($nomScript)
			{
				return $this->ObtientChemRacineClientWeb()."/".$nomScript.".".$this->CfgClientWeb->ExtHtml ;
			}
			public function ObtientChemFicJSClientWeb()
			{
				return $this->ObtientChemRacineClientWeb()."/".$this->CfgClientWeb->NomFichierJS ;
			}
			protected function EcritFicScriptClientWeb($nomScript, $ctn)
			{
				$chemFic = $this->ObtientChemScriptClientWeb($nomScript) ;
				$fh = fopen($chemFic, "w") ;
				if($fh !== false)
				{
					fputs($fh, $ctn) ;
					fclose($fh) ;
				}
			}
		}
	}

?>