<?php
	
	if(! defined('PV_SERVICE_SRC_NOYAU_IONIC'))
	{
		define('PV_SERVICE_SRC_NOYAU_IONIC', 1) ;
		
		class PvServiceSrcNoyauIonic extends PvElemZoneIonic
		{
			public $FichTs ;
			protected $_NoInstServCalc = -1 ;
			protected $_NomInstServCalc ;
			public static $TotalInstServCalc = 0 ;
			protected function CalculeInstServCalc()
			{
				PvServiceSrcNoyauIonic::$TotalInstServCalc++ ;
				$this->_NoInstServCalc = PvServiceSrcNoyauIonic::$TotalInstServCalc ;
				$this->_NomInstServCalc = get_class($this)."_".$this->_NoInstServCalc ;
			}
			public function NomInstServCalc()
			{
				return $this->_NomInstServCalc ;
			}
			public function NomClasse()
			{
				return ($this->NomElementZone != '') ? "Service".ucfirst($this->NomElementZone) : $this->_NomInstServCalc ;
			}
			public function NoInstServCalc()
			{
				return $this->_NoInstServCalc ;
			}
			public function NoInstanceServCalc()
			{
				return $this->_NoInstServCalc ;
			}
			public function CheminRelatif()
			{
				$nomServ = strtolower($this->_NomInstServCalc) ;
				return "providers/".$nomServ."/".$nomServ ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CalculeInstServCalc() ;
				$this->FichTs = new PvFichSrcTsIonic() ;
				$this->FichTs->CheminRelatif = $this->CheminRelatif().".ts" ;
			}
			public function ChargeConfig()
			{
				$this->ChargeConfigAuto() ;
				$this->ChargeFichTs() ;
				parent::ChargeConfig() ;
			}
			protected function ChargeConfigAuto()
			{
				$this->ClasseTs = $this->FichTs->InsereClasse($this->NomClasse()) ;
				// print_r($this->ClasseTs) ;
				$this->FichTs->InsereImportGlobal(array("Injectable"), "@angular/core") ;
				$this->FichTs->InsereDecorator("@Injectable") ;
			}
			public function FournitMethodesDistantes()
			{
			}
			protected function InsereMethodeDistante($nom, $mtd)
			{
				return $this->ZoneParent->InsereMethodeDistante($this->NomMethodeDistante($nom), $mtd) ;
			}
			protected function InsereMtdDist($nom, $mtd)
			{
				return $this->InsereMethodeDistante($nom, $mtd) ;
			}
			public function NomMethodeDistante($nom)
			{
				$nomMtd = "ServiceSrc_".$this->NomElementZone."_".$nom ;
				return $nomMtd ;
			}
			public function & MethodeDistante($nom)
			{
				$nomMtd = $this->NomMethodeDistante($nom) ;
				$mtdDist = new PvMtdDistNonTrouveeIonic() ;
				if(! isset($this->ZoneParent->MethodesDistantes[$nomMtd]))
				{
					return $mtdDist ;
				}
				return $this->ZoneParent->MethodesDistantes[$nomMtd] ;
			}
			public function UrlMethodeDistante($nom)
			{
				$mtd = $this->MethodeDistante($nom) ;
				return $mtd->ObtientUrl() ;
			}
			protected function ChargeFichTs()
			{
			}
			protected function PrepareFichiers()
			{
			}
			public function GenereFichiers()
			{
				$this->PrepareFichiers() ;
				$cheminRep = $this->ZoneParent->CheminProjetIonic."/src/providers" ;
				if(! is_dir($cheminRep))
				{
					mkdir($cheminRep) ;
				}
				$cheminRep2 = $this->ZoneParent->CheminProjetIonic."/src/providers/".strtolower($this->_NomInstServCalc) ;
				if(! is_dir($cheminRep2))
				{
					mkdir($cheminRep2) ;
				}
				$this->GenereFichierSrc($this->FichTs) ;
			}
			public function & PageSrc($nom)
			{
				$pageSrc = new PvPageSrcIndefIonic() ;
				if(! isset($this->ZoneParent->PagesSrc[$nom]))
				{
					return $pageSrc ;
				}
				return $this->ZoneParent->PagesSrc[$nom] ;
			}
			public function & ServiceSrc($nom)
			{
				$serviceSrc = new PvPageSrcIndefIonic() ;
				if(! isset($this->ZoneParent->ServicesSrc[$nom]))
				{
					return $serviceSrc ;
				}
				return $this->ZoneParent->ServicesSrc[$nom] ;
			}
			public function & PageSrcAccueil()
			{
				return $this->ZoneParent->PageSrcAccueil ;
			}
			public function & PageSrcNonTrouvee()
			{
				return $this->ZoneParent->PageSrcNonTrouvee ;
			}
			public function InsereImportPageSrcTs(& $pageSrc)
			{
				return $this->FichTs->InsereImportPageSrcService($pageSrc) ;
			}
			public function InsereImportPageSrcNommeeTs($nomPageSrc)
			{
				return $this->FichTs->InsereImportPageSrcService($this->PageSrc($nomPageSrc)) ;
			}
			public function InsereImportServiceSrcTs(& $serviceSrc)
			{
				return $this->FichTs->InsereImportServiceSrc($serviceSrc, $serviceSrc->NomClasse()) ;
			}
			public function InsereImportUtils()
			{
				$this->FichTs->InsereImportServiceSrcPage($this->ZoneParent->ServiceSrcUtils) ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public svcUtils:".$this->ZoneParent->ServiceSrcUtils->NomClasse() ;
			}
			public function InsereImportServiceSrcNommeTs($nomServiceSrc)
			{
				return $this->FichTs->InsereImportServiceSrcPage($this->ServiceSrc($nomServiceSrc)) ;
			}
		}
		
		class PvServiceSrcIndefIonic extends PvServiceSrcNoyauIonic
		{
		}
		
		class PvServiceSrcUtilsIonic extends PvServiceSrcNoyauIonic
		{
			protected $MtdAppelMtdDist ;
			public function AppelTsMtdDistEncode($nom, $args, $fonctSucces=null, $fonctErreur=null)
			{
				$ctn = 'this.svcUtils.appelleMethodeDistante('.svc_json_encode($nom).', '. svc_json_encode($args).', '.(($fonctSucces == null) ? 'null' : $fonctSucces).', '.(($fonctErreur == null) ? 'null' : $fonctErreur).')' ;
				return $ctn ;
			}
			public function AppelTsMtdDist($nom, $args, $fonctSucces=null, $fonctErreur=null)
			{
				$ctn = 'this.svcUtils.appelleMethodeDistante('.svc_json_encode($nom).', '.$args.', '.(($fonctSucces == null) ? 'null' : $fonctSucces).', '.(($fonctErreur == null) ? 'null' : $fonctErreur).')' ;
				return $ctn ;
			}
			public function ChargeFichTs()
			{
				$zone = & $this->ZoneParent ;
				$this->FichTs->InsereImportGlobal(array("Http", "Headers", "RequestOptions"), "@angular/http") ;
				$this->FichTs->InsereImportDirect("rxjs/add/operator/map") ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public http:Http" ;
				$this->MtdAppelMtdDist = $this->ClasseTs->InsereMethode("appelleMethodeDistante", array("nomMethode:string", "pArgs:any", "fonctSucces:any", "fonctErreur:any")) ;
				$this->MtdAppelMtdDist->CorpsBrut .= 'var headers = new Headers() ;
headers.append("Accept", "application/json") ;
headers.append("Content-Type", "application/json") ;
let options = new RequestOptions({ headers : headers}) ;
let postParams = {
method : nomMethode,
args : pArgs
} ;
return new Promise(resolve => {
this.http.post('.svc_json_encode($zone->UrlDistant).', postParams, options)
.map(res => res.json())
.subscribe(data => {
if(fonctSucces !== undefined && fonctSucces !== null)
fonctSucces(data) ;
}, error => {
if(fonctErreur !== undefined && fonctErreur !== null)
fonctErreur(error) ;
})
}) ;' ;
			}
		}
	}
	
?>