<?php
	
	if(! defined('PV_ZONE_BASE_IONIC'))
	{
		if(! defined('PV_IHM_APPEL_DISTANT'))
		{
			include dirname(__FILE__)."/../AppelDistant.class.php" ;
		}
		if(! defined('PV_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('PV_METHODE_DISTANTE_BASE_IONIC'))
		{
			include dirname(__FILE__)."/MethodeDistante.class.php" ;
		}
		if(! defined('PV_APP_SRC_BASE_IONIC'))
		{
			include dirname(__FILE__)."/AppSrc.class.php" ;
		}
		if(! defined('PV_PAGE_SRC_BASE_IONIC'))
		{
			include dirname(__FILE__)."/PageSrc.class.php" ;
		}
		if(! defined('PV_SERVICE_SRC_BASE_IONIC'))
		{
			include dirname(__FILE__)."/ServiceSrc.class.php" ;
		}
		if(! defined('PV_SCRIPT_WEB_IONIC'))
		{
			include dirname(__FILE__)."/ScriptWeb.class.php" ;
		}
		define('PV_ZONE_BASE_IONIC', 1) ;
		
		class PvAppelJsonIonic
		{
			public $method ;
			public $args ;
		}
		
		class PvAppelRecuIonic
		{
			public $IdDonnees ;
			public $Id ;
			public $Origine ;
			public $Adresse ;
			public $Contenu ;
			public $Resultat ;
		}
		
		class PvIHMBaseIonic extends PvZoneAppelDistant
		{
			public $PageSrcAccueil ;
			public $PagesSrc = array() ;
			public $ServicesSrc = array() ;
			public $AppSrc ;
			public $NomPageAccueil = "Accueil" ;
			public $CheminProjetIonic ;
			public $MessageSuccesGener = "Fichiers generes avec succes..." ;
			public $ServiceSrcUtils ;
			public $NomServiceSrcUtils = "PvUtilitesIonic" ;
			public $UrlDistant = "?" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->AppelRecu = $this->CreeAppelRecuVide() ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreFixeIonic() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteZone($nom, $this) ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreHttpRequestIonic() ;
				$filtre->AdopteZone($nom, $this) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreFixe($nom, $valeur) ;
			}
			public function & CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
			public function ImpressionEnCours()
			{
				return 0 ;
			}
			protected function CreeAppSrc()
			{
				return new PvAppSrcBaseIonic() ;
			}
			protected function CreeServiceSrcUtils()
			{
				return new PvServiceSrcUtilsIonic() ;
			}
			protected function ChargeConfigAuto()
			{
				parent::ChargeConfigAuto() ;
				$this->AppSrc = $this->CreeAppSrc() ;
				$this->AppSrc->AdopteZone("app", $this) ;
				$this->PageSrcAccueil = $this->CreePageSrcAccueil() ;
				$this->PageSrcAccueil->AdopteZone($this->NomPageAccueil, $this) ;
				$this->ServiceSrcUtils = $this->CreeServiceSrcUtils() ;
				$this->ServiceSrcUtils->AdopteZone($this->NomServiceSrcUtils, $this) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargePagesSrc() ;
				$this->ChargeServicesSrc() ;
			}
			protected function ChargePagesSrc()
			{
			}
			protected function ChargeServicesSrc()
			{
			}
			public function & ToutesPagesSrc()
			{
				$res = array($this->PageSrcAccueil, $this->PageSrcNonAutorise) ;
				array_splice($res, count($res), 0, $this->PagesSrc) ;
				return $res ;
			}
			public function & TousServicesSrc()
			{
				$res = array($this->ServiceSrcUtils) ;
				array_splice($res, count($res), 0, $this->ServicesSrc) ;
				return $res ;
			}
			protected function CreePageSrcAccueil()
			{
				return new PvPageSrcAccueilIonic() ;
			}
			public function ExistePageSrc($nom)
			{
				return (isset($this->PagesSrc[$nom])) ? 1 : 0 ;
			}
			public function & InsereServiceSrc($nom, $servSrc)
			{
				$this->ServicesSrc[$nom] = & $servSrc ;
				$servSrc->AdopteZone($nom, $this) ;
				return $servSrc ;
			}
			public function & InserePageSrc($nom, $pageSrc)
			{
				$this->PagesSrc[$nom] = & $pageSrc ;
				$pageSrc->AdopteZone($nom, $this) ;
				return $pageSrc ;
			}
			protected function ChargeConfigElems()
			{
				$pagesSrc = $this->ToutesPagesSrc() ;
				$servsSrc = $this->TousServicesSrc() ;
				foreach($pagesSrc as $nom => $pageSrc)
				{
					$pageSrc->ChargeConfig() ;
					$pageSrc->ChargeComposantsIU() ;
				}
				foreach($servsSrc as $nom => $servSrc)
				{
					$servSrc->ChargeConfig() ;
				}
				$this->AppSrc->ChargeConfig() ;
			}
			protected function VideDossier($chemRep)
			{
				if(! is_dir($chemRep))
				{
					return ;
				}
				$chemReps = array() ;
				$dh = opendir($chemRep) ;
				while(($nomFich = readdir($dh)) !== false)
				{
					if($nomFich == "." || $nomFich == "..")
					{
						continue ;
					}
					$chemFich = $chemRep."/".$nomFich ;
					if(is_dir($chemFich))
					{
						$chemReps[] = $chemFich ;
					}
					else
					{
						// @unlink($chemFich) ;
					}
				}
				closedir($dh) ;
				foreach($chemReps as $i => $chemRep)
				{
					$this->VideDossier($chemRep) ;
					// @unlink($chemRep) ;
				}
			}
			protected function GenereFichiersElems()
			{
				$pagesSrc = $this->ToutesPagesSrc() ;
				$servicesSrc = $this->TousServicesSrc() ;
				$this->VideDossier($this->CheminProjetIonic."/src/pages") ;
				foreach($pagesSrc as $nom => $pageSrc)
				{
					$pageSrc->GenereFichiers() ;
				}
				$this->VideDossier($this->CheminProjetIonic."/src/providers") ;
				foreach($servicesSrc as $nom => $servSrc)
				{
					$servSrc->GenereFichiers() ;
				}
				$this->AppSrc->GenereFichiers() ;
			}
			public function GenereFichiers()
			{
				$this->ChargeConfigElems() ;
				$this->GenereFichiersElems() ;
			}
			protected function ChargeMtdsDistsElems()
			{
				$servicesSrc = $this->TousServicesSrc() ;
				$pagesSrc = $this->ToutesPagesSrc() ;
				foreach($pagesSrc as $i => $pageSrc)
				{
					$pageSrc->ChargeComposantsIU() ;
					foreach($pageSrc->ComposantsIU as $nomComp => $comp)
					{
						$comp->FournitMethodesDistantes() ;
					}
				}
				foreach($servicesSrc as $i => $serviceSrc)
				{
					$serviceSrc->FournitMethodesDistantes() ;
				}
				// file_put_contents("appel.txt", print_r(array_keys($this->MethodesDistantes), true)) ;
			}
			public function Execute()
			{
				if(php_sapi_name() == "cli")
				{
					if($this->CheminProjetIonic != "")
					{
						$this->GenereFichiers() ;
					}
					echo $this->MessageSuccesGener. PHP_EOL ;
				}
				else
				{
					parent::Execute() ;
				}
			}
			protected function CreeScriptListeAppelRecu()
			{
				return new PvScriptTrcListeAppelRecuIonic() ;
			}
		}
		
		class PvZoneBaseIonic extends PvIHMBaseIonic
		{
			public $InclurePagesSrcMembership = 1 ;
			public $NomClasseMembership = "AkSqlMembership" ;
			public $NomPageSrcConnexion = "connexion" ;
			public $NomClassePageSrcConnexion = "PvPageSrcConnexionIonic" ;
			public $NomPageSrcInscription = "inscription" ;
			public $NomClassePageSrcInscription = "PvPageSrcInscriptionIonic" ;
			public $NomPageSrcModifPrefs = "modifPrefs" ;
			public $NomClassePageSrcModifPrefs = "PvPageSrcModifPrefsIonic" ;
			public $NomPageSrcRecouvreMP = "recouvreMP" ;
			public $NomClassePageSrcRecouvreMP = "" ;
			public $NomServiceSrcMembership = "membership" ;
			public $NomClasseServiceSrcMembership = "PvServiceSrcMembershipIonic" ;
			public $NomClasseTsMembership = "MembershipLocal" ;
			public $NomPageNonAutorise = "nonAutorise" ;
			public $PageSrcNonAutorise ;
			public $ServiceSrcMembership ;
			public $RedirigerVersConnexion = 1 ;
			public $InscrireMtdsAccesPageSrc = 1 ;
			public $AutoriserInscription = 1 ;
			public $AutoriserModifPrefs = 1 ;
			protected function CreeAppSrc()
			{
				return new PvAppSrcRestreintIonic() ;
			}
			protected function CreeMembership()
			{
				$nomClasse = "AkSqlMembership" ;
				if(class_exists($this->NomClasseMembership))
				{
					$nomClasse = $this->NomClasseMembership ;
				}
				return new $nomClasse($this) ; 
			}
			protected function CreePageSrcNonAutorise()
			{
				return new PvPageSrcNonAutoriseIonic() ;
			}
			protected function ChargeConfigAuto()
			{
				$this->Membership = $this->CreeMembership() ;
				parent::ChargeConfigAuto() ;
				$this->PageSrcNonAutorise = $this->CreePageSrcNonAutorise() ;
				$this->PageSrcNonAutorise->AdopteZone($this->NomPageNonAutorise, $this) ;
			}
			protected function & CreePageSrcParClasse($nomClasse)
			{
				$pageSrc = new PvPageSrcIndefIonic() ;
				if(class_exists($nomClasse))
				{
					$pageSrc = new $nomClasse() ;
				}
				return $pageSrc ;
			}
			protected function & InserePageSrcParClasse($nom, $nomClasse)
			{
				return $this->InserePageSrc($nom, $this->CreePageSrcParClasse($nomClasse)) ;
			}
			protected function & CreeServiceSrcParClasse($nomClasse)
			{
				$servSrc = new PvServiceSrcIndefIonic() ;
				if(class_exists($nomClasse))
				{
					$servSrc = new $nomClasse() ;
				}
				return $servSrc ;
			}
			protected function & InsereServiceSrcParClasse($nom, $nomClasse)
			{
				return $this->InsereServiceSrc($nom, $this->CreeServiceSrcParClasse($nomClasse)) ;
			}
			protected function ChargePagesSrcMembership()
			{
				if($this->AutoriserInscription == 1)
				{
					$this->PageSrcInscription = $this->InserePageSrcParClasse($this->NomPageSrcInscription, $this->NomClassePageSrcInscription) ;
				}
				if($this->AutoriserModifPrefs == 1)
				{
					$this->PageSrcModifPrefs = $this->InserePageSrcParClasse($this->NomPageSrcModifPrefs, $this->NomClassePageSrcModifPrefs) ;
				}
				$this->PageSrcConnexion = $this->InserePageSrcParClasse($this->NomPageSrcConnexion, $this->NomClassePageSrcConnexion) ;
				
			}
			protected function ChargePagesSrc()
			{
				$this->ChargePagesSrcMembership() ;
			}
			protected function ChargeServicesSrc()
			{
				$this->ServiceSrcMembership = $this->InsereServiceSrcParClasse($this->NomServiceSrcMembership, $this->NomClasseServiceSrcMembership) ;
			}
			public function & ToutesPagesSrc()
			{
				$res = parent::ToutesPagesSrc() ;
				$res[] = & $this->PageSrcNonAutorise ;
				return $res ;
			}
			public function RemplitMtdsAccesPageSrc(& $pageSrc)
			{
				$pageSrc->FichTs->InsereImportGlobal(array("Storage"), "@ionic/storage") ;
				$pageSrc->FichTs->InsereImportGlobal(array("Events"), "ionic-angular") ;
				$pageSrc->ClasseTs->InsereMembre("membreConnecteBrut", "''", "string") ;
				$pageSrc->ClasseTs->InsereMembre("membreConnecte", 'null', "any") ;
				$pageSrc->ClasseTs->MtdConstruct->Arguments[] = "public events: Events" ;
				$pageSrc->ClasseTs->MtdConstruct->Arguments[] = "public storage:Storage" ;
				$mtdDetermineMembreConnecte = $pageSrc->ClasseTs->InsereMethode("determineMembreConnecte", array('fonctSuiv:any')) ;
				$mtdDetermineMembreConnecte->CorpsBrut .= 'var _self:any = this ;
console.log("HAAAA") ;
_self.menuCtrl.close() ;
_self.menuCtrl.enable(true, "non_connecte") ;
_self.menuCtrl.enable(false, "connecte") ;
_self.membreConnecteBrut = "" ;
_self.membreConnecte = null ;
_self.storage.get("membreConnecte").then((val) => {
if(val !== null && val !== \'\') {
_self.membreConnecteBrut = val ;
_self.membreConnecte = JSON.parse(_self.membreConnecteBrut) ;
_self.menuCtrl.enable(false, "non_connecte") ;
_self.menuCtrl.enable(true, "connecte") ;
_self.events.publish("login:succes", _self.membreConnecte) ;
}
if(fonctSuiv !== undefined && fonctSuiv !== null)
{
fonctSuiv() ;
}
}, (error) => {
if(fonctSuiv !== undefined && fonctSuiv !== null)
{
fonctSuiv() ;
}	
}) ;' ;
				$mtdIdMembreConnecte = $pageSrc->ClasseTs->InsereMethode("idMembreConnecte", array()) ;
				$mtdIdMembreConnecte->CorpsBrut .= 'return (this.membreConnecte !== null) ? this.membreConnecte.Id : 0 ;' ;
				$mtdPossedeMembreConnecte = $pageSrc->ClasseTs->InsereMethode("possedeMembreConnecte", array()) ;
				$mtdPossedeMembreConnecte->CorpsBrut .= 'return this.membreConnecte !== null ;' ;
				$mtdPossedePrivileges = $pageSrc->ClasseTs->InsereMethode("possedePrivileges", array('privs:string[]')) ;
				$mtdPossedePrivileges->CorpsBrut .= 'let ok = this.possedeMembreConnecte() ;
if(! ok) {
return ok ;
}
if(privs === undefined || privs === null || privs.length == 0) {
return true ;
}
for(let i:any=0; i<privs.length; i++)
{
if(this.membreConnecte.Profile[privs[i]] !== undefined && this.membreConnecte.Profile[privs[i]].Enabled === true)
{
ok = true ;
break ;
}
}
return ok ;' ;
			}
			protected function GenereFichiersElems()
			{
				$this->PageSrcNonAutorise->GenereFichiers() ;
				parent::GenereFichiersElems() ;
			}
			protected function ChargeConfigElems()
			{
				$this->PageSrcNonAutorise->ChargeConfig() ;
				$this->PageSrcNonAutorise->ChargeComposantsIU() ;
				parent::ChargeConfigElems() ;
			}
		}
	}
	
?>