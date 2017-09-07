<?php
	
	if(! defined('PV_SERVICE_SRC_MEMBERSHIP_IONIC'))
	{
		if(! defined('PV_SERVICE_SRC_NOYAU_IONIC'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_SERVICE_SRC_MEMBERSHIP_IONIC', 1) ;
		
		class PvServiceSrcMembershipIonic extends PvServiceSrcNoyauIonic
		{
			public $ClasseTs ;
			public $MtdDistAuthentifie ;
			public function & PageSrcNonAutorise()
			{
				return $this->ZoneParent->PageSrcNonAutorise ;
			}
			public function & Membership()
			{
				return $this->ZoneParent->Membership ;
			}
			public function FournitMethodesDistantes()
			{
				$this->MtdDistAuthentifie = $this->InsereMtdDist("authentifie", new PvMtdDistAuthentifieIonic()) ;
			}
			protected function ChargeFichTs()
			{
				$this->FichTs->InsereImportGlobal(array("Storage"), "@ionic/storage") ;
				$this->FichTs->InsereImportGlobal(array("NavController"), 'ionic-angular') ;
				if($this->ZoneParent->RedirigerVersConnexion == 1)
				{
					$this->InsereImportPageSrcTs($this->ZoneParent->PageSrcConnexion) ;
				}
				else
				{
					$this->InsereImportPageSrcTs($this->PageSrcNonAutorise()) ;
				}
				$this->InsereImportUtils() ;
				$this->ClasseTs->MtdConstruct->Arguments[] = "private storage: Storage" ;
				$this->ClasseTs->MtdConstruct->CorpsBrut = "this.storage.set('membreConnecte', '') ;" ;
				$this->MtdValideAcces = $this->ClasseTs->InsereMethode("valideAcces", array("estConnecte:boolean", "privileges:string[]", "navCtrl:NavController", "fonctSucces:any")) ;
				$this->MtdValideAcces->CorpsBrut = 'if(estConnecte == false) {
if(fonctSucces !== undefined && fonctSucces !== null) {
fonctSucces() ;
}
return ;
}
this.storage.get("membreConnecte").then((result) => {
var ok:any = false ;
if(result !== "") {
var membre:any = JSON.parse(result) ;
if(membre !== null) {
if(privileges.length == 0) {
if(fonctSucces !== undefined && fonctSucces !== null) {
fonctSucces() ;
}
return ;
}
if(membre.Profile !== undefined) {
for(var i:any=0; i<privileges.length; i++) {
if(membre.Profile.Privileges[privileges[i]] !== undefined) {
if(membre.Profile.Privileges[privileges[i]].Enabled === true) {
ok = true ;
break ;
}
}
}
}
}
}
else
{
ok = false ;
}
if(ok === false) {
navCtrl.push('.(($this->ZoneParent->RedirigerVersConnexion == 0) ? $this->PageSrcNonAutorise()->NomClasse() : $this->ZoneParent->PageSrcConnexion->NomClasse()).') ;
}
else {
if(fonctSucces !== undefined && fonctSucces !== null) {
fonctSucces() ;
}
}
}) ;' ;
			}
		}
	}
	
?>