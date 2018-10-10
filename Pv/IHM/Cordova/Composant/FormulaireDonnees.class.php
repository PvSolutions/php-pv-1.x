<?php
	
	if(! defined('PV_FORMULAIRE_DONNEES_CORDOVA'))
	{
		define('PV_FORMULAIRE_DONNEES_CORDOVA', 1) ;
		
		class PvActCalcRenduFormDonneesCordova extends PvMethodeJsonCordova
		{
			protected function ConstruitResultat()
			{
				$form = & $this->ComposantIUParent ;
				$form->DetecteParametresLocalisation() ;
				$form->CalculeElementsRendu() ;
				$this->Resultat->messageExecution = $form->MessageExecution ;
				$this->Resultat->totalElements = $form->TotalElements ;
				$this->Resultat->elementsEnCours = $form->ElementsEnCours ;
				$this->Resultat->elementEnCours = $form->ElementEnCours ;
				$this->Resultat->elementEnCoursTrouve = $form->ElementEnCoursTrouve ;
			}
		}
		class PvActSelectCmdFormDonneesCordova extends PvMethodeJsonCordova
		{
			protected function ConstruitResultat()
			{
				$form = & $this->ComposantIUParent ;
				$form->AppliqueCommandeSelectionnee() ;
				$this->Resultat->nomCommandeSelectionnee = $form->NomCommandeSelectionnee ;
				$this->Resultat->cacherMessageExecution = $form->CacherMessageExecution ;
				$this->Resultat->cacherFormulaireFiltres = $form->CacherFormulaireFiltres ;
				$this->Resultat->statutExecution = $form->CommandeSelectionnee->StatutExecution ;
				$this->Resultat->messageExecution = $form->CommandeSelectionnee->MessageExecution ;
			}
		}
		
		class PvFormulaireDonneesCordova extends PvFormulaireDonneesHtml
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			protected static function InstrsJsDefinitRendu(& $form)
			{
				$ctn = '' ;
				$ctn .= 'pvZoneCordova.appelleUrl('.svc_json_encode($form->ActPrincCalculeRendu->ObtientUrl()).' + "&" + pvZoneCordova.encodeQueryString(args), {}, function(resultat, xhr) {
var valeur = null ;
var donnees = JSON.parse(resultat) ;
if(donnees !== undefined && donnees.elementEnCours !== undefined) {'.PHP_EOL ;
				// print $form->IDInstanceCalc.' : '.count($form->FiltresEdition)."\n" ;
				foreach($form->FiltresEdition as $i => $filtreEdit)
				{
					if($filtreEdit->RenduPossible())
					{
						$nomJsParam = svc_json_encode($filtreEdit->NomParametreLie) ;
						$comp = $filtreEdit->ObtientComposant() ;
						$ctn .= 'valeur = (donnees.elementEnCours['.$nomJsParam.'] !== undefined) ? donnees.elementEnCours['.$nomJsParam.'] : "" ;'.PHP_EOL ;
						$ctn .= $comp->RenduJsDefinitValeur().PHP_EOL ;
					}
				}
				$ctn .= '}'.PHP_EOL ;
				$ctn .= '}) ;' ;
				return $ctn ;
			}
			public static function AdopteZoneForm(& $form, $nom, & $zone)
			{
				$form->ActPrincCalculeRendu = PvFormulaireDonneesCordova::InsereActionPrincForm($form, "calculeRendu", new PvActCalcRenduFormDonneesCordova()) ;
				$form->ActPrincSelectCommande = PvFormulaireDonneesCordova::InsereActionPrincForm($form, "selectCommande", new PvActSelectCmdFormDonneesCordova()) ;
			}
			public static function & InsereActionPrincForm(& $form, $nom, & $action)
			{
				$action->ComposantIUParent = & $form ;
				$action->NomElementComposantIU = $nom ;
				return $form->ZoneParent->InsereActionPrinc($nom.'_'.$form->IDInstanceCalc, $action) ;
			}
			public static function & ChargeConfigForm(& $form)
			{
				$form->ZoneParent->InscritInstrsJsOuvrEcran($form->ScriptParent, PvFormulaireDonneesCordova::InstrsJsDefinitRendu($form)) ;
			}
			public static function RenduResultatCommandeExecuteeForm(& $form)
			{
				$ctn = '<div class="ResultatCommandeExecutee"></div>' ;
				return $ctn ;
			}
			public static function DeclarationSoumetFormulaireFiltresFrm($form, $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$filtresGets = array() ;
				$nomFiltresGets = array() ;
				$filtresGetsEdit = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					if($filtres[$nomFiltre]->TypeLiaisonParametre == "get")
					{
						$filtresGetsEdit[] = $filtres[$nomFiltre]->ObtientIDElementHtmlComposant() ;
						$nomFiltresGets[] = $filtres[$nomFiltre]->NomParametreLie ;
					}
				}
				foreach($form->ChampsGetSoumetFormulaire as $n => $v)
				{
					$filtresGetsEdit[] = $v ;
				}
				foreach($form->ParamsGetSoumetFormulaire as $n => $v)
				{
					$filtresGets[] = $v ;
				}
				$params = extract_array_without_keys($_GET, $nomFiltresGets) ;
				// print_r($nomFiltresGets) ;
				$filtresGets = array_unique($filtresGets) ;
				$indexMinUrl = 1 ;
				$urlFormulaire = $form->ActPrincSelectCommande->ObtientUrl() ;
				$instrDesactivs = '' ;
				$ctn = '<script type="text/javascript">
	function SoumetFormulaire'.$form->IDInstanceCalc.'(form)
	{
		var urlFormulaire = "'.$urlFormulaire.'" ;
		var parametresGet = '.json_encode($filtresGetsEdit).' ;
		if(parametresGet != undefined)
		{
			for(var i=0; i<parametresGet.length; i++)
			{
				urlFormulaire += "&" ;
				var nomParam = parametresGet[i] ;
				var valeurParam = "" ;
				var elementParam = document.getElementById(nomParam) ;
				if(elementParam != null)
				{
				
					nomParam = elementParam.name ;
					valeurParam = elementParam.value ;
					elementParam.disabled = "disabled" ;
				}
				urlFormulaire += encodeURIComponent(nomParam) + "=" + encodeURIComponent(valeurParam) ;
			}
		}
		var argsGet = '.json_encode($filtresGets).' ;
		if(argsGet != undefined)
		{
			for(var i in argsGet)
			{
				urlFormulaire += "&" ;
				var nomParam = argsGet[i] ;
				var valeurParam = (pvZoneCordova.argsEcran[nomParam]) ? pvZoneCordova.argsEcran[nomParam] : "" ;
				urlFormulaire += encodeURIComponent(nomParam) + "=" + encodeURIComponent(valeurParam) ;
			}
		}
		pvZoneCordova.soumetForm(urlFormulaire, jQuery(form), function(resultat, xhr) {
			var jqForm = jQuery("#'.$form->IDInstanceCalc.'") ;
			var resultExec = JSON.parse(resultat) ;
			if(resultExec !== null) {
				if(resultExec.messageExecution !== null)
				{
					var jqResultCmd = jqForm.find(".ResultatCommandeExecutee") ;
					jqResultCmd.attr("class", "ResultatCommandeExecutee") ;
					jqResultCmd.addClass((resultExec.statutExecution === 1) ? "text-success" : "text-danger") ;
					jqResultCmd.html(resultExec.messageExecution) ;
					if(resultExec.cacherFormulaireFiltres == 1) {
						jqForm.find(".FormulaireFiltres").hide() ;
					}
					if(resultExec.cacherMessageExecution == 1) {
						jqForm.find(".FormulaireFiltres").hide() ;
					}
					else {
						jqResultCmd.show() ;
					}
				}
			}
			else  {
				pvZoneCordova.afficheExceptionAppelDistant() ;
			}
		}) ;
		return false ;
	}
	function ActualiseFormulaire'.$form->IDInstanceCalc.'()
	{
'.$form->CtnJsActualiseFormulaireFiltres().' ;
	}
</script>' ;
				return $ctn ;
			}
			protected function & InsereActionPrinc($nom, $action)
			{
				return PvFormulaireDonneesCordova::InsereActionPrincForm($this, $nom, $action) ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
		}
	}
	
?>