<?php
	
	if(! defined('PV_PAGE_SRC_NOYAU_IONIC'))
	{
		if(! defined('PV_COMPOSANT_IU_BASE_IONIC'))
		{
			include dirname(__FILE__)."/../ComposantIU.class.php" ;
		}
		define('PV_PAGE_SRC_NOYAU_IONIC', 1) ;
		
		class PvPageSrcNoyauIonic extends PvElemZoneIonic
		{
			public $Titre ;
			public $Icone = "paper" ;
			public $Description ;
			public $FichTs ;
			public $FichScss ;
			public $FichHtml ;
			public $CacherBoutonPrec = 1 ;
			protected $_NoInstPageCalc = -1 ;
			protected $_NomInstPageCalc ;
			public static $TotalInstPageCalc = 0 ;
			public $TagHeaderTitle ;
			public $TagHeaderNavbar ;
			public $TagHeaderToolbar ;
			public $InclureHeaderToolbar = 0 ;
			public $TagHeader ;
			public $TagToolbar ;
			public $TagContent ;
			public $ClasseTs ;
			public $CompDecoratorTs ;
			public $MtdViewDidEnter ;
			public $MtdViewDidLeave ;
			public $MtdAfficheMsg ;
			public $ContenuTsAccesAutorise = '' ;
			public $LibelleOKDlg = 'OK' ;
			public $LibelleMsgChargement = 'Veuillez patienter...' ;
			public $ComposantsIU = array() ;
			protected function CalculeInstPageCalc()
			{
				PvPageSrcNoyauIonic::$TotalInstPageCalc++ ;
				$this->_NoInstPageCalc = PvPageSrcNoyauIonic::$TotalInstPageCalc ;
				$this->_NomInstPageCalc = get_class($this)."_".$this->_NoInstPageCalc ;
			}
			public function NomInstPageCalc()
			{
				return $this->_NomInstPageCalc ;
			}
			public function NomClasse()
			{
				return ($this->NomElementZone != '') ? "Page".ucfirst($this->NomElementZone) : $this->_NomInstPageCalc ;
			}
			public function NomMembreExport()
			{
				return lcfirst($this->NomClasse()) ;
			}
			public function NoInstPageCalc()
			{
				return $this->_NoInstPageCalc ;
			}
			public function NoInstancePageCalc()
			{
				return $this->_NoInstPageCalc ;
			}
			public function CheminRelatif()
			{
				return "pages/".strtolower($this->_NomInstPageCalc)."/".strtolower($this->_NomInstPageCalc) ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CalculeInstPageCalc() ;
				$nomPage = strtolower($this->_NomInstPageCalc) ;
				$this->FichTs = new PvFichSrcTsIonic() ;
				$this->FichTs->CheminRelatif = "pages/".$nomPage."/".$nomPage.".ts" ;
				$this->FichScss = new PvFichSrcScssIonic() ;
				$this->FichScss->CheminRelatif = "pages/".$nomPage."/".$nomPage.".scss" ;
				$this->FichHtml = new PvFichSrcHtmlIonic() ;
				$this->FichHtml->CheminRelatif = "pages/".$nomPage."/".$nomPage.".html" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigAuto() ;
				$this->ChargeFichs() ;
			}
			protected function ChargeFichs()
			{
			}
			protected function ChargeConfigAuto()
			{
				$this->ChargeFichHtmlAuto() ;
				$this->ChargeFichTsAuto() ;
				$this->ChargeFichScssAuto() ;
			}
			protected function ChargeFichTsAuto()
			{
				// Ts
				$this->FichTs->InsereImportGlobal(array("Component"), '@angular/core') ;
				// $this->FichTs->InsereImportGlobal(array("Page"), 'ionic/ionic') ;
				$this->FichTs->InsereImportGlobal(array("NavController", "AlertController", "ActionSheetController", "LoadingController", "NavParams"), 'ionic-angular') ;
				$this->CompDecoratorTs = $this->FichTs->InsereDecorator("@Component") ;
				$this->CompDecoratorTs->selector = "page-".strtolower($this->NomClasse()) ;
				$this->CompDecoratorTs->templateUrl = strtolower($this->_NomInstPageCalc).".html" ;
				// $this->PageDecoratorTs = $this->FichTs->InsereDecorator("@Page") ;
				$this->ClasseTs = $this->FichTs->InsereClasse($this->NomClasse()) ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public navParams: NavParams" ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public navCtrl: NavController" ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public alertCtrl: AlertController" ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public loadingCtrl: LoadingController" ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public actionSheetCtrl: ActionSheetController" ;
				$this->MtdViewDidEnter = $this->ClasseTs->InsereMethode("ionViewDidEnter") ;
				$this->MtdViewDidLeave = $this->ClasseTs->InsereMethode("ionViewDidLeave") ;
				$this->MembrBarreChargmt = $this->ClasseTs->InsereMembre("barreChargement:any") ;
				// $this->TagHeaderTitle = $this->TagHeader->InsereTagFils(new TagHeaderTitle()) ;
				$this->MtdAfficheChargmt = $this->ClasseTs->InsereMethode("afficheChargement", array()) ;
				$this->MtdAfficheChargmt->CorpsBrut .= 'if(this.barreChargement !== undefined && this.barreChargement !== null) {
return ;
}
this.barreChargement = this.loadingCtrl.create({
content: '.svc_json_encode($this->LibelleMsgChargement).'
});
this.barreChargement.present();' ;
				$this->MtdCacheChargmt = $this->ClasseTs->InsereMethode("cacheChargement", array()) ;
				$this->MtdCacheChargmt->CorpsBrut .= 'if(this.barreChargement !== null) {
this.barreChargement.dismiss() ;
this.barreChargement = null ;
}' ;
				$this->MtdAfficheMsg = $this->ClasseTs->InsereMethode("afficheMsg", array("titreDlg:string", "messageDlg:string", "fonctOK:any=undefined")) ;
				$this->MtdAfficheMsg->CorpsBrut = 'let _self = this ;
let dlg = this.alertCtrl.create({
title : titreDlg,
message : messageDlg,
buttons : [
{
text : '.svc_json_encode($this->LibelleOKDlg).',
handler: data => {
if(fonctOK !== undefined && _self !== undefined) {
fonctOK(data) ;
}
}
}
]
}) ;
dlg.present() ;' ;
				$this->InsereImportUtils() ;
			}
			protected function ChargeFichHtmlAuto()
			{
				// Html
				$this->TagHeader = $this->FichHtml->TagRacine()->InsereTagFils(new PvTagIonHeader()) ;
				$this->TagContent = $this->FichHtml->TagRacine()->InsereTagFils(new PvTagIonContent()) ;
				$this->TagContent->DefinitAttr("padding", "padding") ;
				$this->TagHeaderNavbar = $this->TagHeader->InsereTagFils(new PvTagIonNavbar()) ;
				if($this->CacherBoutonPrec == 1)
				{
					$this->TagHeaderNavbar->DefinitAttr("hideBackButton", "hideBackButton") ;
				}
				$this->TagHeaderTitle = $this->TagHeaderNavbar->InsereTagFils(new PvTagIonTitle()) ;
				$this->TagHeaderTitle->InsereContent($this->Titre) ;
				if($this->InclureHeaderToolbar == 1)
				{
					$this->TagHeaderToolbar = $this->TagHeader->InsereTagFils(new PvTagIonToolbar()) ;
				}
			}
			protected function ChargeFichScssAuto()
			{
			}
			public function InsereImportUtils()
			{
				$this->InsereImportServiceSrcTs($this->ZoneParent->ServiceSrcUtils) ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "public svcUtils:".$this->ZoneParent->ServiceSrcUtils->NomClasse() ;
			}
			protected function InsereImportServiceSrcTs(& $serviceSrc)
			{
				$this->FichTs->InsereImportServiceSrcPage($serviceSrc) ;
				$this->CompDecoratorTs->providers[] = $serviceSrc->NomClasse() ;
			}
			protected function InsereImportPageSrcTs(& $pageSrc)
			{
				$this->FichTs->InsereImportPageSrcService($pageSrc) ;
			}
			public function & InsereComposantIU($nom, $composant)
			{
				$this->ComposantsIU[$nom] = & $composant ;
				$composant->AdoptePageSrc($nom, $this) ;
				return $composant ;
			}
			public function ChargeComposantsIU()
			{
			}
			protected function AppliqueComposantsIU()
			{
				$nomComps = array_keys($this->ComposantsIU) ;
				foreach($nomComps as $i => $nomComp)
				{
					$comp = & $this->ComposantsIU[$nomComp] ;
					$comp->Deploie() ;
				}
			}
			protected function CalculeCorpsBrutViewDidEnter()
			{
				if($this->ContenuTsAccesAutorise != '')
				{
					$this->MtdViewDidEnter->CorpsBrut = 'let _self:any = this ;
'.$this->ContenuTsAccesAutorise ;
				}
			}
			protected function PrepareFichiers()
			{
				$this->CalculeCorpsBrutViewDidEnter() ;
			}
			public function GenereFichiers()
			{
				$this->AppliqueComposantsIU() ;
				$this->PrepareFichiers() ;
				$cheminRep = $this->ZoneParent->CheminProjetIonic."/src/pages/".strtolower($this->_NomInstPageCalc) ;
				if(! is_dir($cheminRep))
				{
					mkdir($cheminRep) ;
				}
				$this->GenereFichierSrc($this->FichTs) ;
				$this->GenereFichierSrc($this->FichHtml) ;
				$this->GenereFichierSrc($this->FichScss) ;
			}
			public function InsereTagRendu($contenu)
			{
				return $this->TagContent->InsereRendu($contenu) ;
			}
			public function & InsereMembreTs($contenu, $valeur="", $type="")
			{
				return $this->ClasseTs->InsereMembre($contenu, $valeur, $type) ;
			}
			public function & InsereMethodeTs($nomMethode, $args=array(), $corpsBrut="")
			{
				return $this->ClasseTs->InsereMethode($nomMethode, $args, $corpsBrut) ;
			}
		}
		
		class PvPageSrcIndefIonic extends PvPageSrcNoyauIonic
		{
		}
		
		class PvPageSrcAccueilIonic extends PvPageSrcNoyauIonic
		{
			public $Titre = "Bienvenue" ;
			public $Icone = "home" ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
			}
		}
	}
	
?>