<?php
	
	if(! defined('PV_APP_SRC_IONIC'))
	{
		define('PV_APP_SRC_IONIC', 1) ;
		
		class PvAppSrcBaseIonic extends PvElemZoneIonic
		{
			public $FichComponentTs ;
			public $FichHtml ;
			public $FichModuleTs ;
			public $FichScss ;
			public $FichMainTs ;
			public $ClassePrincTs ;
			public $TagRacineHtml ;
			public $ImportsSupplModuleTs = array() ;
			public $ProvidersSupplModuleTs = array() ;
			public $ModulesAppl = array() ;
			protected function InitConfig()
			{
				// ComponentTs
				$this->FichComponentTs = new PvFichSrcTsIonic() ;
				$this->FichComponentTs->CheminRelatif = "app/app.component.ts" ;
				// AppHtml
				$this->FichHtml = new PvFichSrcHtmlIonic() ;
				$this->FichHtml->CheminRelatif = "app/app.html" ;
				// ModuleTs
				$this->FichModuleTs = new PvFichSrcTsIonic() ;
				$this->FichModuleTs->CheminRelatif = "app/app.module.ts" ;
				// AppScss
				$this->FichScss = new PvFichSrcScssIonic() ;
				$this->FichScss->CheminRelatif = "app/app.scss" ;
				// MainTs
				$this->FichMainTs = new PvFichSrcTsIonic() ;
				$this->FichMainTs->CheminRelatif = "app/main.ts" ;
			}
			public function ChargeConfig()
			{
				$this->ChargeModulesAppl() ;
				$this->ChargeConfigAuto() ;
				parent::ChargeConfig() ;
				// Specifications
				$this->ChargeFichHtml() ;
				$this->ChargeFichModuleTs() ;
				$this->ChargeFichComponentTs() ;
				$this->ChargeFichMainTs() ;
				$this->ChargeFichScss() ;
			}
			protected function ChargeModulesAppl()
			{
			}
			public function & InsereModuleAppl($nom, $module)
			{
				$this->ModulesAppl[$nom] = & $module ;
				return $module ;
			}
			public function & InsereModuleApplNatif($nom, $nomClasse, $cheminRelatif, $inscrProviders=1, $inscrImports=0)
			{
				$module = new PvModuleNatifAppSrcIonic() ;
				$module->NomClasse = $nomClasse ;
				$module->CheminRelatif = $cheminRelatif ;
				$module->InscrireDansProviders = $inscrProviders ;
				$module->InscrireDansImports = $inscrImports ;
				return $this->InsereModuleAppl($nom, $module) ;
			}
			public function & InsereModuleApplNomme($nom)
			{
				$module = new PvModuleBaseAppSrcIonic() ;
				switch(strtolower($nom))
				{
					case "camera" :
					{
						return $this->InsereModuleApplNatif($nom, "Camera", "@ionic-native/camera") ;
					}
					break ;
					case "geolocation" :
					{
						return $this->InsereModuleApplNatif($nom, "Geolocation", "@ionic-native/geolocation") ;
					}
					break ;
					case "sim" :
					{
						return $this->InsereModuleApplNatif($nom, "Sim", "@ionic-native/sim") ;
					}
					break ;
					case "datepicker" :
					case "date-picker" :
					{
						return $this->InsereModuleApplNatif("datepicker", "DatePicker", "@ionic-native/date-picker") ;
					}
					break ;
				}
				return $module ;
			}
			public function DesactiveModuleAppl($nom)
			{
				if(in_array($nom, $this->ModulesAppl))
				{
					$this->ModulesAppl[$nom]->Active = 0 ;
				}
			}
			protected function InstalleModulesAppl()
			{
				foreach($this->ModulesAppl as $nom => $module)
				{
					if($module->Active == 0)
					{
						continue ;
					}
					$module->AppliqueApp($this) ;
				}
			}
			protected function ChargeFichHtmlAuto()
			{
				// AppHtml
				$this->TagRacineHtml = $this->FichHtml->DefinitTagRacine(new PvTagRacineHtmlIonic()) ;
				$this->TagRacineHtml->InsereTagFils(new PvTagIonNav()) ;
			}
			protected function ChargeFichModuleTsAuto()
			{
				// Ajout des modules appliques
				$this->InstalleModulesAppl() ;
				//ModuleTs
				$this->FichModuleTs->InsereImportGlobal(array("BrowserModule"), "@angular/platform-browser") ;
				$this->FichModuleTs->InsereImportGlobal(array("NgModule", "ErrorHandler"), "@angular/core") ;
				$this->FichModuleTs->InsereImportGlobal(array("IonicApp", "IonicModule", "IonicErrorHandler"), "ionic-angular") ;
				$this->FichModuleTs->InsereImportGlobal(array("SplashScreen"), "@ionic-native/splash-screen") ;
				$this->FichModuleTs->InsereImportGlobal(array("StatusBar"), "@ionic-native/status-bar") ;
				$this->FichModuleTs->InsereImportGlobal(array("HttpModule"), "@angular/http") ;
				$this->FichModuleTs->InsereImportGlobal(array("IonicStorageModule"), "@ionic/storage") ;
				$this->FichModuleTs->InsereImportLocal(array("MyApp"), "./app.component") ;
				$nomClassesPage = array() ;
				$pagesSrc = $this->ZoneParent->ToutesPagesSrc() ;
				foreach($pagesSrc as $i => & $pageSrc)
				{
					$nomClassesPage[] = $pageSrc->NomClasse() ;
 					$this->FichModuleTs->InsereImportLocal(array($pageSrc->NomClasse()), "../".$pageSrc->CheminRelatif()) ;
				}
				$servsSrc = $this->ZoneParent->TousServicesSrc() ;
				$nomServsPage = array() ;
				foreach($servsSrc as $i => & $servSrc)
				{
					$nomServsPage[] = $servSrc->NomClasse() ;
 					$this->FichModuleTs->InsereImportLocal(array($servSrc->NomClasse()), "../".$servSrc->CheminRelatif()) ;
				}
				$this->FichModuleTs->CorpsBrutDecorator .= '@NgModule({'.PHP_EOL ;
				$this->FichModuleTs->CorpsBrutDecorator .= 'declarations: [MyApp, '.join(", ", $nomClassesPage).'],'.PHP_EOL ;
				$importsSuppl = join(', ', $this->ImportsSupplModuleTs) ;
				$this->FichModuleTs->CorpsBrutDecorator .= 'imports: [BrowserModule, IonicModule.forRoot(MyApp), IonicStorageModule.forRoot(), HttpModule'.(($importsSuppl != '') ? ', '.$importsSuppl : '').'],'.PHP_EOL ;
				$this->FichModuleTs->CorpsBrutDecorator .= 'bootstrap: [IonicApp],'.PHP_EOL ;
				$this->FichModuleTs->CorpsBrutDecorator .= 'entryComponents: [MyApp, '.join(", ", $nomClassesPage).'],'.PHP_EOL ;
				$listClassesServs = join(',', $nomServsPage) ;
				if(count($this->ProvidersSupplModuleTs) > 0)
				{
					$listClassesServs .= ','.join(',', $this->ProvidersSupplModuleTs) ;
				}
				$this->FichModuleTs->CorpsBrutDecorator .= 'providers: [SplashScreen, StatusBar, {provide: ErrorHandler, useClass: IonicErrorHandler}'.(($listClassesServs != '') ? ', '.$listClassesServs : '').']'.PHP_EOL ;
				$this->FichModuleTs->CorpsBrutDecorator .= '})' ;
				$this->FichModuleTs->CorpsBrutSuppl = "export class AppModule {}" ;
			}
			protected function ChargeFichComponentTsAuto()
			{
				// ComponentTs
				$this->FichComponentTs->InsereImportGlobal(array("Component"), "@angular/core") ;
				$this->FichComponentTs->InsereImportGlobal(array("Platform"), "ionic-angular") ;
				$this->FichComponentTs->InsereImportGlobal(array("StatusBar"), "@ionic-native/status-bar") ;
				$this->FichComponentTs->InsereImportGlobal(array("SplashScreen"), "@ionic-native/splash-screen") ;
				$this->FichComponentTs->InsereImportLocal(array($this->ZoneParent->PageSrcAccueil->NomClasse()), "../".$this->ZoneParent->PageSrcAccueil->CheminRelatif()) ;
				$decortrComp = $this->FichComponentTs->InsereDecorator("@Component") ;
				$decortrComp->templateUrl = "app.html" ;
				$this->ClassePrincTs = $this->FichComponentTs->InsereClasse("MyApp") ;
				$this->ClassePrincTs->InsereMembre("rootPage", $this->ZoneParent->PageSrcAccueil->NomClasse()) ;
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "platform: Platform" ;
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "statusBar: StatusBar" ;
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "splashScreen: SplashScreen" ;
				$this->ClassePrincTs->MtdConstruct->CorpsBrut = 'platform.ready().then(() => {
statusBar.styleDefault();
splashScreen.hide();
});' ;
			}
			protected function ChargeFichMainTsAuto()
			{
				// MainTs
				$this->FichMainTs->InsereImportGlobal(array("platformBrowserDynamic"), "@angular/platform-browser-dynamic") ;
				$this->FichMainTs->InsereImportLocal(array("AppModule"), "./app.module") ;
				$this->FichMainTs->CorpsBrutSuppl = "platformBrowserDynamic().bootstrapModule(AppModule) ;" ;
			}
			protected function ChargeFichScssAuto()
			{
			}
			protected function ChargeConfigAuto()
			{
				$this->ChargeFichComponentTsAuto() ;
				$this->ChargeFichMainTsAuto() ;
				$this->ChargeFichModuleTsAuto() ;
				$this->ChargeFichHtmlAuto() ;
				$this->ChargeFichScssAuto() ;
			}
			protected function ChargeFichScss()
			{
			}
			protected function ChargeFichModuleTs()
			{
			}
			protected function ChargeFichComponentTs()
			{
			}
			protected function ChargeFichMainTs()
			{
			}
			protected function ChargeFichHtml()
			{
			}
			public function GenereFichiers()
			{
				$this->GenereFichierSrc($this->FichHtml) ;
				$this->GenereFichierSrc($this->FichComponentTs) ;
				$this->GenereFichierSrc($this->FichMainTs) ;
				$this->GenereFichierSrc($this->FichScss) ;
				$this->GenereFichierSrc($this->FichModuleTs) ;
			}
		}
		
		class PvAppSrcRestreintIonic extends PvAppSrcBaseIonic
		{
			public $ClassePrincTs ;
			public $AttrsMembreConnecte = array('login', 'titreProfil') ;
			public $LibelleLienConnexion = "Connexion" ;
			public $LibelleLienDeconnexion = "D&eacute;connexion" ;
			public $LibelleLienInscription = "Inscription" ;
			public $ContenuTagMembreConnecte = '<h4 align="center">{{loginMembre}}</h4>
<div align="center"><i>{{titreProfilMembre}}</i></div>' ;
			public $ContenuTsMembreConnecte = 'this.loginMembre = membre.Login ;
this.titreProfilMembre = membre.Profile.Title ;' ;
			public $ContenuTsMembreDeconnecte = 'this.loginMembre = membre.Login ;
this.titreProfilMembre = membre.Profile.Title ;' ;
			public $ContenuTagMembreNonConnecte = '<h4 align="center">Bienvenue, Inconnu</h4>' ;
			protected function ChargeFichComponentTsAuto()
			{
				// ComponentTs
				$this->FichComponentTs->InsereImportGlobal(array("Component", "ViewChild"), "@angular/core") ;
				$this->FichComponentTs->InsereImportGlobal(array("Platform", "Nav", "Events"), "ionic-angular") ;
				$this->FichComponentTs->InsereImportGlobal(array("Storage"), "@ionic/storage") ;
				$this->FichComponentTs->InsereImportGlobal(array("StatusBar"), "@ionic-native/status-bar") ;
				$this->FichComponentTs->InsereImportGlobal(array("SplashScreen"), "@ionic-native/splash-screen") ;
				$this->FichComponentTs->InsereImportLocal(array($this->ZoneParent->PageSrcAccueil->NomClasse()), "../".$this->ZoneParent->PageSrcAccueil->CheminRelatif()) ;
				$this->FichComponentTs->InsereImportLocal(array($this->ZoneParent->PageSrcConnexion->NomClasse()), "../".$this->ZoneParent->PageSrcConnexion->CheminRelatif()) ;
				$decortrComp = $this->FichComponentTs->InsereDecorator("@Component") ;
				$decortrComp->templateUrl = "app.html" ;
				$this->ClassePrincTs = $this->FichComponentTs->InsereClasse("MyApp") ;
				$this->ClassePrincTs->InsereMembre("@ViewChild(Nav) nav", null, "Nav") ;
				$this->ClassePrincTs->InsereMembre("rootPage", $this->ZoneParent->PageSrcAccueil->NomClasse()) ;
				foreach($this->AttrsMembreConnecte as $i => $attr)
				{
					$this->ClassePrincTs->InsereMembre($attr."Membre") ;
				}
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "public events: Events" ;
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "platform: Platform" ;
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "statusBar: StatusBar" ;
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "splashScreen: SplashScreen" ;
				$this->ClassePrincTs->MtdConstruct->Arguments[] = "public storage:Storage" ;
				$this->ClassePrincTs->MtdConstruct->CorpsBrut = 'platform.ready().then(() => {
statusBar.styleDefault();
splashScreen.hide();
this.determineMembreConnecte() ;
});
events.subscribe("login:succes", (membre) => {
'.$this->ContenuTsMembreConnecte.'
}) ;
events.subscribe("logout:succes", (membre) => {
'.$this->ContenuTsMembreDeconnecte.'
}) ;' ;
				$mtdDeconnexion = $this->ClassePrincTs->InsereMethode("deconnexion") ;
				$mtdDeconnexion->CorpsBrut .= 'this.storage.set("membreConnecte", "") ;
this.nav.push('.$this->ZoneParent->PageSrcAccueil->NomClasse().') ;' ;
				$mtdAffichPageConnex = $this->ClassePrincTs->InsereMethode("affichePageConnexion") ;
				$mtdAffichPageConnex->CorpsBrut .= 'this.nav.push('.$this->ZoneParent->PageSrcConnexion->NomClasse().') ;' ;
				$mtdDetermineMembreConnecte = $this->ClassePrincTs->InsereMethode("determineMembreConnecte") ;
				$mtdDetermineMembreConnecte->CorpsBrut .= 'var _self = this ;
_self.storage.get("membreConnecte").then((val) => {
}) ;' ;
			}
			protected function ChargeFichHtmlAuto()
			{
				$this->TagRacineHtml = $this->FichHtml->DefinitTagRacine(new PvTagRacineHtmlIonic()) ;
				// AppHtml
				$this->TagMenuNonConnecte = $this->TagRacineHtml->InsereTagFils(new PvTagIonMenu()) ;
				$this->TagMenuNonConnecte->DefinitAttr("id", "non_connecte") ;
				$this->TagMenuNonConnecte->DefinitAttr("side", "left") ;
				$tagRendu = $this->TagMenuNonConnecte->InsereTagFilsList(new PvRenduHtmlIonic()) ;
				$tagRendu->Contenu = $this->ContenuTagMembreNonConnecte ;
				$tagBoutonsNonConnect = $this->TagMenuNonConnecte->InsereTagFilsList(new PvRenduHtmlIonic()) ;
				$tagBoutonsNonConnect->Contenu = '<ion-item align="center">
<button ion-button icon-left (click)="affichePageConnexion()">
<ion-icon name="log-in"></ion-icon>'.$this->LibelleLienConnexion.'
</button>
</ion-item>' ;
				$this->TagMenuConnecte = $this->TagRacineHtml->InsereTagFils(new PvTagIonMenu()) ;
				$tagRendu = $this->TagMenuConnecte->InsereTagFilsList(new PvRenduHtmlIonic()) ;
				$tagRendu->Contenu = $this->ContenuTagMembreConnecte ;
				$this->TagMenuConnecte->DefinitAttr("id", "connecte") ;
				$this->TagMenuConnecte->DefinitAttr("side", "left") ;
				$tagDeconnect = $this->TagMenuConnecte->InsereTagFilsList(new PvRenduHtmlIonic()) ;
				$tagDeconnect->Contenu = '<ion-item align="center">
<button ion-button icon-left (click)="deconnexion()">
<ion-icon name="exit"></ion-icon>
'.$this->LibelleLienDeconnexion.'
</button>
</ion-item>' ;
				$nav = $this->TagRacineHtml->InsereTagFils(new PvTagIonNav()) ;
				$nav->DefinitAttr("swipeBackEnabled", "false") ;
				$nav->DefinitAttr("#content", "#content") ;
			}
		}
		
		class PvModuleBaseAppSrcIonic
		{
			public $Active = 1 ;
			public function AppliquePage(& $pageSrc)
			{
			}
			public function AppliqueApp(& $appSrc)
			{
			}
		}
		class PvModuleNatifAppSrcIonic extends PvModuleBaseAppSrcIonic
		{
			public $NomClasse ;
			public $CheminRelatif ;
			public $NomMethodeImport = "forRoot" ;
			public $InscrireDansProviders = 1 ;
			public $InscrireMembrePage = 1 ;
			public $InscrireDansImports = 0 ;
			public function AppliquePage(& $pageSrc)
			{
				$pageSrc->FichTs->InsereImportGlobal(array($this->NomClasse), $this->CheminRelatif) ;
				if($this->InscrireMembrePage == 1)
				{
					$argumentInstr = "public ".lcfirst($this->NomClasse).":".$this->NomClasse ;
					if(! in_array($argumentInstr, $pageSrc->ClasseTs->MtdConstruct->Arguments))
					{
						$pageSrc->ClasseTs->MtdConstruct->Arguments[] = $argumentInstr ;
					}
				}
			}
			public function AppliqueApp(& $appSrc)
			{
				if($this->NomClasse != '')
				{
					$appSrc->FichModuleTs->InsereImportGlobal(array($this->NomClasse), $this->CheminRelatif) ;
				}
				if($this->InscrireDansProviders == 1)
				{
					$appSrc->ProvidersSupplModuleTs[] = $this->NomClasse ;
				}
				if($this->InscrireDansImports == 1)
				{
					$appSrc->ImportsSupplModuleTs[] = ($this->NomMethodeImport == '') ? $this->NomClasse : $this->NomClasse.'.'.$this->NomMethodeImport.'()' ;
				}
			}
		}
		
	}
	
?>