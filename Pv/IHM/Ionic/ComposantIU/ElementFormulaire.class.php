<?php
	
	if(! defined('PV_ELEMENT_FORMULAIRE_IU_IONIC'))
	{
		define('PV_ELEMENT_FORMULAIRE_IU_IONIC', 1) ;
		
		class PvElementFormulaireBaseIonic extends PvObjet
		{
			public $EspaceReserve ;
			public $AccepterLibelle = 0 ;
			public $FmtLbl ;
			public $PageSrcParent ;
			public $NomElementPageSrc ;
			public $FiltreParent ;
			public $NomClasseMethodeDistante = "" ;
			public $EncodeHtmlEtiquette = 1 ;
			public $AttrsSupplHtml = array() ;
			public function PeutCalculerElemsRendu()
			{
				return 0 ;
			}
			public function AppliqueMethode(& $methode)
			{
			}
			public function AdoptePageSrc($nom, & $pageSrc)
			{
				$this->PageSrcParent = & $pageSrc ;
				$this->NomElementPageSrc = $nom ;
			}
			public function & ZoneParent()
			{
				return $this->PageSrcParent->ZoneParent ;
			}
			public function FournitMethodesDistantes()
			{
				if($this->NomClasseMethodeDistante != '')
				{
					$nomClasse = $this->NomClasseMethodeDistante ;
					$this->MethodeDistante = $this->RattacheMethodeDistante(new $nomClasse()) ;
				}
			}
			public function & RattacheMethodeDistante($methode)
			{
				$methode->ElemFormParent = & $this ;
				return $this->ZoneParent()->InsereMethodeDistante($this->NomMethodeDistante("princ"), $methode) ;
			}
			public function NomMethodeDistante($nom)
			{
				return $this->PageSrcParent->NomElementZone."_".$this->FiltreParent->NomParametreLie."_comp_".$nom ;
			}
			public function RenduDispositif()
			{
				return "" ;
			}
			public function DeploieDispositif(& $pageSrc)
			{
			}
			protected function CreeFmtLbl()
			{
				return new PvFmtLblWeb() ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->FmtLbl = $this->CreeFmtLbl() ;
			}
			public function EncodeEtiquette($valeur)
			{
				return $this->FmtLbl->Rendu($valeur, $composant) ;
			}
			public function RenduEtiquette()
			{
				return '<ion-input type="text" name="'.$this->FiltreParent->IDInstanceCalc.'" [(ngModel)]="'.$this->FiltreParent->IDInstanceCalc.'" readonly value="'.htmlspecialchars(html_entity_decode($this->EncodeEtiquette($this->Valeur))).'"></ion-input>' ;
			}
			protected function RenduAttrsSupplIonic()
			{
				
			}
		}
		
		class PvTagIonInput extends PvElementFormulaireBaseIonic
		{
			public $AccepterLibelle = 1 ;
			protected $TypeInput = "text" ;
			public function RenduDispositif()
			{
				$ctn = '' ;
				$ctn .= '<ion-input name="'.$this->FiltreParent->IDInstanceCalc.'" type="'.$this->TypeInput.'"' ;
				$ctn .= ' [(ngModel)]="'.$this->FiltreParent->IDInstanceCalc.'"' ;
				if($this->EspaceReserve != '')
				{
					$ctn .= ' placeholder="'.htmlspecialchars(html_entity_decode($this->EspaceReserve)).'"' ;
				}
				$ctn .= ' value="'.htmlspecialchars($this->Valeur).'"' ;
				$ctn .= $this->RenduAttrsSupplIonic() ;
				$ctn .= '></ion-input>' ;
				return $ctn ;
			}
		}
		class PvTagIonInputText extends PvTagIonInput
		{
		}
		class PvTagIonInputEmail extends PvTagIonInput
		{
			protected $TypeInput = "email" ;
		}
		class PvTagIonInputPassword extends PvTagIonInput
		{
			protected $TypeInput = "password" ;
		}
		class PvTagIonInputUrl extends PvTagIonInput
		{
			protected $TypeInput = "url" ;
		}
		class PvTagIonInputTel extends PvTagIonInput
		{
			protected $TypeInput = "tel" ;
		}
		class PvTagIonInputNumber extends PvTagIonInput
		{
			protected $TypeInput = "number" ;
		}
		class PvTagIonInputSearch extends PvTagIonInput
		{
			protected $TypeInput = "search" ;
		}
		
		class PvTagIonDateTime extends PvElementFormulaireBaseIonic
		{
			public $DisplayFormat = "DD/MMMM/YYYY HH:mm:ss" ;
			public $PickerFormat = "YYYY-MM-DD HH:mm:ss" ;
			public $AccepterLibelle = 1 ;
			protected $TypeInput = "text" ;
			public function RenduDispositif()
			{
				$ctn = '' ;
				$ctn .= '<ion-datetime name="'.$this->FiltreParent->IDInstanceCalc.'"' ;
				$ctn .= ' [(ngModel)]="'.$this->FiltreParent->IDInstanceCalc.'"' ;
				$ctn .= ' displayFormat="'.$this->DisplayFormat.'"' ;
				$ctn .= ' pickerFormat="'.$this->PickerFormat.'"' ;
				if($this->EspaceReserve != '')
				{
					$ctn .= ' placeholder="'.htmlspecialchars(html_entity_decode($this->EspaceReserve)).'"' ;
				}
				$ctn .= ' value="'.htmlspecialchars($this->Valeur).'"' ;
				$ctn .= $this->RenduAttrsSupplIonic() ;
				$ctn .= '></ion-datetime>' ;
				return $ctn ;
			}
		}
		class PvTagIonDate extends PvTagIonDateTime
		{
			public $DisplayFormat = "DD/MMMM/YYYY" ;
			public $PickerFormat = "YYYY-MM-DD" ;
		}
		class PvTagIonTime extends PvTagIonDateTime
		{
			public $DisplayFormat = "HH:mm:ss" ;
			public $PickerFormat = "HH:mm:ss" ;
		}
		
		class PvElemFormDonneesIonic extends PvElementFormulaireBaseIonic
		{
			public $FournisseurDonnees ;
			public $NomColonneLibelle ;
			public $NomColonneValeur ;
			public $FiltresSelection = array() ;
			public $NomColonneValeurParDefaut ;
			public $NomClasseMethodeDistante = "PvMtdDistElemFormIonic" ;
			public $MtdCalcResult ;
			public function PeutCalculerElemsRendu()
			{
				return 1 ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreFixeIonic() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdoptePageSrc($nom, $this->PageSrcParent) ;
				return $filtre ;
			}
			public function & CreeFiltreTs($nom, $corpsBrut='')
			{
				$filtre = new PvFiltreTsIonic() ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CorpsBrutTs = $corpsBrut ;
				$filtre->AdoptePageSrc($nom, $this->PageSrcParent) ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreHttpRequestIonic() ;
				$filtre->AdoptePageSrc($nom, $this->PageSrcParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFltTs($nom, $corpsBrut)
			{
				return $this->CreeFiltreTs($nom, $corpsBrut) ;
			}
			public function & CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreFixe($nom, $valeur) ;
			}
			public function & CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
			public function & InsereFltSelectFixe($nom, $valeur, $colLiee='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->DefinitColLiee($colLiee) ;
				$flt->NomGroupeFiltre = "filtresSelect" ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectTs($nom, $corpsBrut, $colLiee='')
			{
				$flt = $this->CreeFiltreTs($nom, $corpsBrut) ;
				$flt->DefinitColLiee($colLiee) ;
				$flt->NomGroupeFiltre = "filtresSelect" ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $colLiee='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->DefinitColLiee($colLiee) ;
				$flt->NomGroupeFiltre = "filtresSelect" ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function ArgsTsFiltres(& $filtres)
			{
				$args = array() ;
				foreach($filtres as $nom => $flt)
				{
					if($flt->Invisible == 1 || $flt->NomParametreLie == '' || $flt->NePasLierParametre == 1)
					{
						continue ;
					}
					$args[] = $flt->NomParametreLie.' : '.$flt->ValeurTs() ;
				}
				return '{'.join(", ", $args).'}' ;
			}
			protected function LieFiltresSelection(& $methode)
			{
				$param = $methode->Param() ;
				if(isset($param->filtresSelect))
				{
					foreach($this->FiltresSelection as $i => $filtre)
					{
						$this->FiltresSelection[$i]->Lie() ;
					}
				}
			}
			public function DeploieDispositif(& $pageSrc)
			{
				$pageSrc->ClasseTs->InsereMembre("public resultat".$this->IDInstanceCalc, "null") ;
				$this->MtdCalcResult = $pageSrc->ClasseTs->InsereMethode("calculeResultat".$this->IDInstanceCalc) ;
				$this->MtdCalcResult->CorpsBrut .= 'let _self = this ;'.PHP_EOL
.$this->ZoneParent()->ServiceSrcUtils->AppelTsMtdDist(
					$this->NomMethodeDistante("princ"),
					"{
filtresSelect : ".$this->ArgsTsFiltres($this->FiltresSelection)."
}",
					"function(resultat) {
if(resultat.erreur.code == 0) {
_self.resultat".$this->IDInstanceCalc." = resultat.valeur ;
}
else {
_self.afficheMsg('Erreur sur le champ ".$this->FiltreParent->Libelle."', resultat.erreur.message) ;
}
}") ;
			}
			public function AppliqueMethode(& $methode)
			{
				$this->LieFiltresSelection($methode) ;
				$requete = $this->FournisseurDonnees->OuvreRequeteSelectElements($this->FiltresSelection) ;
				if($requete != null && $requete->RessourceSupport !== false)
				{
					$valeurs = array() ;
					while(($ligne = $this->FournisseurDonnees->LitRequete($requete)) != false)
					{
						$valeurs[] = $ligne ;
					}
					$this->FournisseurDonnees->FermeRequete($requete) ;
					$methode->ConfirmeSucces($valeurs) ;
				}
				elseif($requete->RessourceSupport === false)
				{
					$methode->RenseigneErreur(2, ($this->FournisseurDonnees->MessageException() != '') ? $this->FournisseurDonnees->MessageException() : "Erreur rencontree lors de la recuperation des donnees") ;
				}
				else
				{
					$methode->RenseigneErreur(4, "Fournisseur de donnees mal configure") ;
				}
			}
		}
		class PvMtdDistElemFormIonic extends PvMethodeDistanteNoyauIonic
		{
			protected function ExecuteInstructions()
			{
				$this->ElemFormParent->AppliqueMethode($this) ;
			}
		}
		
		class PvZoneSelectIonic extends PvElemFormDonneesIonic
		{
			public $TexteOk = "OK" ;
			public $TexteAnnuler = "Annuler" ;
			public $MsgAucunElement = "(Vide)" ;
			public function RenduDispositif()
			{
				$ctn = '' ;
				$ctn .= '<ion-select name="'.$this->FiltreParent->IDInstanceCalc.'"' ;
				$ctn .= ' [(ngModel)]="'.$this->FiltreParent->IDInstanceCalc.'"' ;
				if($this->EspaceReserve != '')
				{
					$ctn .= ' placeholder="'.htmlspecialchars(html_entity_decode($this->EspaceReserve)).'"' ;
				}
				$ctn .= ' okText="'.htmlspecialchars(html_entity_decode($this->TexteOk)).'" cancelText="'.htmlspecialchars(html_entity_decode($this->TexteAnnuler)).'"' ;
				$ctn .= $this->RenduAttrsSupplIonic() ;
				$ctn .= '>'.PHP_EOL ;
				$ctn .= '<ion-option *ngFor="let element of resultat'.$this->IDInstanceCalc.'" [value]="element.'.$this->NomColonneValeur.'">{{element.'.$this->NomColonneLibelle.'}}</ion-option>'.PHP_EOL ;
				$ctn .= '</ion-select>'.PHP_EOL ;
				$ctn .= '<span *ngIf="resultat'.$this->IDInstanceCalc.' !== null &amp;&amp; resultat'.$this->IDInstanceCalc.'.length == 0"><ion-input type="text" hidden [(ngModel)]="'.$this->FiltreParent->IDInstanceCalc.'"></ion-input>'.$this->MsgAucunElement.'</span>' ;
				return $ctn ;
			}
		}
		
		class PvDatePickerIonic extends PvElementFormulaireBaseIonic
		{
			public function RenduDispositif()
			{
				$ctn = '' ;
				$ctn .= '<input type="text" [(ngModel)]="'.$this->FiltreParent->IDInstanceCalc.'" (click)="popup'.$this->IDInstanceCalc.'()" >' ;
				return $ctn ;
			}
			public function DeploieDispositif(& $pageSrc)
			{
				$mtdPopup = $pageSrc->ClasseTs->InsereMethode("popup".$this->IDInstanceCalc) ;
				$variableLiee = $this->FiltreParent->IDInstanceCalc ;
				$mtdPopup->CorpsBrut .= 'var _self = this ;
this.datepicker.show({
date: '.$variableLiee.',
mode: '.svc_json_encode($this->Mode).',
}).then(
date => {
_self.'.$variableLiee.' = date ;
},
err => { _self.afficheMsg("Erreur affichage datepicker", err) ; }
);' ;
			}
		}
		
		class PvInputAdresseLocalisIonic extends PvElementFormulaireBaseIonic
		{
			public $NomIconeLocalis = "locate" ;
			public function RenduDispositif()
			{
				$ctn = '' ;
				$ctn .= '<ion-input name="'.$this->FiltreParent->IDInstanceCalc.'" type="text"  [(ngModel)]="'.$this->FiltreParent->IDInstanceCalc.'"' ;
				if($this->EspaceReserve != '')
				{
					$ctn .= ' placeholder="'.htmlspecialchars(html_entity_decode($this->EspaceReserve)).'"' ;
				}
				$ctn .= ' value="'.htmlspecialchars($this->Valeur).'"' ;
				$ctn .= $this->RenduAttrsSupplIonic() ;
				$ctn .= '></ion-input>' ;
				$ctn .= '<ion-icon name="'.$this->NomIconeLocalis.'" item-end (click)="localise'.$this->FiltreParent->IDInstanceCalc.'()"></ion-icon>' ;
				return $ctn ;
			}
			public function DeploieDispositif(& $pageSrc)
			{
				$pageSrc->FichTs->InsereImportGlobal(array("Http", "Headers", "RequestOptions"), "@angular/http") ;
				$pageSrc->FichTs->InsereImportGlobal(array("Geolocation"), "@ionic-native/geolocation") ;
				$pageSrc->FichTs->InsereImportDirect("rxjs/add/operator/map") ;
				$pageSrc->ClasseTs->MtdConstruct->Arguments[] = "public http".$this->IDInstanceCalc." : Http" ;
				$pageSrc->ClasseTs->MtdConstruct->Arguments[] = "public geolocation".$this->IDInstanceCalc." : Geolocation" ;
				$mtdPopup = $pageSrc->ClasseTs->InsereMethode("localise".$this->IDInstanceCalc) ;
				$variableLiee = $this->FiltreParent->IDInstanceCalc ;
				$mtdPopup->CorpsBrut .= 'var _self = this ;
_self.geolocation'.$this->IDInstanceCalc.'.getCurrentPosition().then((resp) => {
var headers = new Headers() ;
headers.append("Accept", "application/json") ;
headers.append("Content-Type", "application/json") ;
let options = new RequestOptions({ headers : headers}) ;
new Promise(resolve => {
this.http.post("http://maps.googleapis.com/maps/api/geocode/json?latlng=" + encodeURIComponent(resp.latitute) + "," + encodeURIComponent(resp.longitude) + "&sensor=false", {}, options)
.map(res => res.json())
.subscribe(data => {
}, error => {
})
}) ;
}).catch((error) => {
_self.afficheMsg("Erreur Localisation", error) ;
});' ;
			}
		}
	}
	
?>