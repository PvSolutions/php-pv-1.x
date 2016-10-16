<?php
	
	if(! defined('PV_COMPOSANT_PLATF_SVC_WEB'))
	{
		define('PV_COMPOSANT_PLATF_SVC_WEB', 1) ;
		
		class PvCtnCalcElemsTablDonneesPlatfSvcWeb
		{
			public $maxElements = 0 ;
			public $totalElements = 0 ;
			public $indiceDebut = 0 ;
			public $indiceFin = 0 ;
			public $totalRangees = 0 ;
			public $valeursCellules = array() ;
		}
		class PvActCalcElemsTablDonneesPlatfSvcWeb extends PvActBasePlatfSvcWeb
		{
			protected function ConstruitResultSpec()
			{
				$tabl = & $this->TableauDonneesParent ;
				$tabl->PrepareRenduVraiment() ;
				if($tabl->FournisseurDonnees->ExceptionTrouvee())
				{
					$this->Resultat->ConfirmeErreur(1, $tabl->FournisseurDonnees->DerniereException->Message, "exception_fournisseur_donnees") ;
				}
				else
				{
					$ctn = new PvCtnCalcElemsTablDonneesPlatfSvcWeb() ;
					$ctn->maxElements = $tabl->MaxElements ;
					$ctn->totalElements = $tabl->TotalElements ;
					$ctn->indiceDebut = $tabl->IndiceDebut ;
					$ctn->indiceFin = $tabl->IndiceFin ;
					$ctn->totalRangees = $tabl->TotalRangees ;
					$ctn->valeursCellules = array() ;
					$defCols = $tabl->ObtientDefColsVisible() ;
					foreach($tabl->ElementsEnCours as $i => $ligne)
					{
						$ctn->valeursCellules[$i] = array() ;
						foreach($defCols as $j => $defCol)
						{
							$ctn->valeursCellules[$i][$j] = $defCol->FormatteValeur($tabl, $ligne) ;
						}
					}
					$this->Resultat->ConfirmeSucces($ctn) ;
				}
			}
		}
		class PvTableauDonneesPlatfSvcWeb extends PvTableauDonneesHtml
		{
			protected $ActCalcElems ;
			public $NomActCalcElems = "CalculeElems" ;
			public function & ObtientDefColsVisible()
			{
				$defCols = array() ;
				foreach($this->DefinitionsColonnes as $i => $colonne)
				{
					if(! $colonne->EstVisible($this->ZoneParent))
						continue ;
					$defCols[$i] = $colonne ;
				}
				return $defCols ;
			}
			public function AdopteZone($nomComposantIU, & $zone)
			{
				parent::AdopteZone($nomComposantIU, $zone) ;
				$this->ActCalcElems = $zone->InsereActionAvantRendu($this->IDInstanceCalc."_".$this->NomActCalcElems, new PvActCalcElemsTablDonneesPlatfSvcWeb()) ;
				$this->ActCalcElems->TableauDonneesParent = & $this ;
			}
			public function AdopteScript($nomComposantIU, & $script)
			{
				parent::AdopteScript($nomComposantIU, $script) ;
				$this->ActCalcElems = $script->InsereActionAvantRendu($this->IDInstanceCalc."_".$this->NomActCalcElems, new PvActCalcElemsTablDonneesPlatfSvcWeb()) ;
				$this->ActCalcElems->TableauDonneesParent = & $this ;
			}
			public function PrepareRenduVraiment()
			{
				parent::PrepareRendu() ;
			}
			public function PrepareRendu()
			{
				$this->DetecteParametresLocalisation() ;
			}
			protected function RenduRangeeDonnees()
			{
				$ctn = '' ;
				$libelleTriAsc = $this->LibelleTriAsc ;
				$libelleTriDesc = $this->LibelleTriDesc ;
				$libelleTriAscSelectionne = $this->LibelleTriAscSelectionne ;
				$libelleTriDescSelectionne = $this->LibelleTriDescSelectionne ;
				$parametresRendu = array() ;
				if($this->UtiliserIconesTri)
				{
					$libelleTriAsc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAsc.'" />' ;
					$libelleTriDesc = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDesc.'" />' ;
					$libelleTriAscSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriAscSelectionne.'" />' ;
					$libelleTriDescSelectionne = '<img border="0" src="'.$this->CheminRelativeIconesTri."/".$this->NomIconeTriDescSelectionne.'" />' ;
				}
				$ctn .= '<div id="'.$this->IDInstanceCalc.'_BlocMsg" style="diplay:none"></div>'.PHP_EOL ;
				$ctn .= '<table' ;
				$ctn .= ' class="RangeeDonnees"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'_RangeeDonnees" style="diplay:none;"' ;
				if($this->Largeur != "")
				{
					$ctn .= ' width="'.$this->Largeur.'"' ;
				}
				if($this->Hauteur != "")
				{
					$ctn .= ' height="'.$this->Hauteur.'"' ;
				}
				if($this->EspacementCell != "")
				{
					$ctn .= ' cellpadding="'.$this->EspacementCell.'"' ;
				}
				if($this->MargesCell != "")
				{
					$ctn .= ' cellspacing="'.$this->MargesCell.'"' ;
				}
				if($this->LargeurBordure != "")
				{
					$ctn .= ' border="'.$this->LargeurBordure.'"' ;
					if($this->CouleurBordure != "")
					{
						$ctn .= ' bordercolor="'.$this->CouleurBordure.'"' ;
					}
				}
				$ctn .= '>'.PHP_EOL ;
				$ctn .= '<tr class="Entete">'.PHP_EOL ;
				foreach($this->DefinitionsColonnes as $i => $colonne)
				{
					if(! $colonne->EstVisible($this->ZoneParent))
						continue ;
					$triPossible = ($this->TriPossible && $colonne->TriPossible) ;
					$ctn .= ($triPossible) ? '<td' : '<th' ;
					if($colonne->Largeur != "")
					{
						$ctn .= ' width="'.$colonne->Largeur.'"' ;
					}
					if($colonne->AlignEntete != "")
					{
						$ctn .= ' align="'.$colonne->AlignEntete.'"' ;
					}
					$ctn .= '>' ;
					if($triPossible)
					{
						$ctn .= '<table width="100%" cellspacing="0" cellpadding="2">' ;
						$ctn .= '<tr>' ;
						$ctn .= '<th width="*" rowspan="2">' ;
					}
					$ctn .= $colonne->ObtientLibelle() ;
					if($triPossible)
					{
						$ctn .= '</th>' ;
						$selectionne = ($this->IndiceColonneTri == $i && $this->SensColonneTri == "asc") ;
						$paramColAsc = array_merge($parametresRendu, array($this->NomParamSensColonneTri() => "asc", $this->NomParamIndiceColonneTri() => $i, $this->NomParamIndiceDebut() => 0)) ;
						$ctn .= '<td'.(($selectionne) ? ' class="ColonneTriee"' : '').'>' ;
						$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramColAsc).'">'.(($selectionne && $libelleTriAscSelectionne != "") ? $libelleTriAscSelectionne : $libelleTriAsc).'</a>' ;
						$ctn .= '</td>' ;
						$ctn .= '</tr>' ;
						$ctn .= '<tr>' ;
						$selectionne = ($this->IndiceColonneTri == $i && $this->SensColonneTri == "desc") ;
						$paramColAsc = array_merge($parametresRendu, array($this->NomParamSensColonneTri() => "desc", $this->NomParamIndiceColonneTri() => $i, $this->NomParamIndiceDebut() => 0)) ;
						$ctn .= '<td'.(($selectionne) ? ' class="ColonneTriee"' : '').'>' ;
						$ctn .= '<a href="javascript:'.$this->AppelJsEnvoiFiltres($paramColAsc).'">'.(($selectionne && $libelleTriDescSelectionne != "") ? $libelleTriDescSelectionne : $libelleTriDesc).'</a>' ;
						$ctn .= '</td>' ;
						$ctn .= '</tr>' ;
						$ctn .= '</table>' ;
					}
					$ctn .= (($triPossible) ? '</td>' : '</th>').PHP_EOL ;
				}
				$ctn .= '</tr>'.PHP_EOL ;
				for($j=0; $j<$this->MaxElements; $j++)
				{
					$ligne = array() ;
					$ctn .= '<tr' ;
					$ctn .= ' class="'.htmlentities($this->ObtientNomClsCSSElem($j, $ligne)) .'"' ;
					$ctn .= ' id="'.$this->IDInstanceCalc.'_Ligne_'.$j.'"' ;
					if($this->SurvolerLigneFocus)
					{
						$ctn .= ' onMouseOver="this.className = this.className + &quot; Survole&quot;;" onMouseOut="this.className = this.className.split(&quot; Survole&quot;).join(&quot; &quot;) ;"' ;
					}
					$ctn .= '>'.PHP_EOL ;
					$defCols = $this->ObtientDefColsVisible() ;
					foreach($defCols as $i => $colonne)
					{
						// print_r($ligne) ;
						if(! $colonne->EstVisible($this->ZoneParent))
							continue ;
						$ctn .= '<td id="'.$this->IDInstanceCalc.'_Cell_'.$j.'_'.$i.'" ' ;
						if($colonne->AlignElement != "")
						{
							$ctn .= ' align="'.$colonne->AlignElement.'"' ;
						}
						if($colonne->StyleCSS != '')
						{
							$ctn .= ' style="'.htmlentities($colonne->StyleCSS).'"' ;
						}
						if($colonne->NomClasseCSS != '')
						{
							$ctn .= ' class="'.htmlentities($colonne->NomClasseCSS).'"' ;
						}

						$ctn .= '>' ;
						$ctn .= '&nbsp;' ;
						$ctn .= '</td>'.PHP_EOL ;
					}
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$ctn .= '</table>'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduComposants()
			{
				$ctn = parent::RenduComposants() ;
				if($this->ToujoursAfficher == 1)
				{
					$ctn .= '<script type="text/javascript">
	jQuery(function() {
		SoumetFormulaire'.$this->IDInstanceCalc.'(document.getElementById("FormulaireEnvoiFiltres'.$this->IDInstanceCalc.'")) ;
	}) ;
</script>' ;
				}
				return $ctn ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$filtresGets = array() ;
				$filtresPosts = array() ;
				$nomFiltresGets = array() ;
				$nomFiltresPosts = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					if($filtres[$nomFiltre]->TypeLiaisonParametre == "get")
					{
						$filtresGets[] = $filtres[$nomFiltre]->ObtientIDElementHtmlComposant() ;
						$nomFiltresGets[] = $filtres[$nomFiltre]->NomParametreLie ;
					}
					elseif($filtres[$nomFiltre]->TypeLiaisonParametre == "post")
					{
						$filtresPosts[] = $filtres[$nomFiltre]->ObtientIDElementHtmlComposant() ;
						$nomFiltresPosts[] = $filtres[$nomFiltre]->NomParametreLie ;
					}
				}
				$params = extract_array_without_keys(array(), $nomFiltresGets) ;
				// print_r($nomFiltresGets) ;
				$urlFormulaire = $this->ActCalcElems->ObtientUrl() ;
				$instrDesactivs = '' ;
				if($this->ForcerDesactCache)
				{
					$urlFormulaire .= '&'.urlencode($this->NomParamIdAleat()).'='.htmlspecialchars(rand(0, 999999)) ;
				}
				$ctn = '<script type="text/javascript">
	function SoumetFormulaire'.$this->IDInstanceCalc.'(form)
	{
		var urlFormulaire = "'.$urlFormulaire.'" ;
		var chaineRequete = "" ;
		var parametresGet = '.json_encode($filtresGets).' ;
		var parametresPost = '.json_encode($filtresPosts).' ;
		if(parametresGet != undefined)
		{
			for(var i=0; i<parametresGet.length; i++)
			{
				var nomParam = parametresGet[i] ;
				var valeurParam = PvPlatfSvc.adressePage.paramGet(nomParam) ;
				var elementParam = document.getElementById(nomParam) ;
				if(elementParam != null)
				{
				
					nomParam = elementParam.name ;
					valeurParam = elementParam.value ;
					elementParam.disabled = "disabled" ;
				}
				urlFormulaire += "&" + encodeURIComponent(nomParam) + "=" + encodeURIComponent(valeurParam) ;
			}
		}'.PHP_EOL ;
		$ctn .= "\t\t".'if(parametresPost != undefined)
		{
			for(var i=0; i<parametresPost.length; i++)
			{
				if(i >= 0) {
					chaineRequete += "&" ;
				}
				var nomParam = parametresPost[i] ;
				var valeurParam = "" ;
				var elementParam = document.getElementById(nomParam) ;
				if(elementParam != null) {
				
					nomParam = elementParam.name ;
					valeurParam = elementParam.value ;
				}
				chaineRequete += encodeURIComponent(nomParam) + "=" + encodeURIComponent(valeurParam) ;
			}
		}'.PHP_EOL ;
		$ctn .= 'var paramsGetPage = PvPlatfSvc.adressePage.paramsGet('.svc_json_encode($this->ParamsGetSoumetFormulaire).')
		for(var nomParam in paramsGetPage) { urlFormulaire += "&" + encodeURIComponent(nomParam) + "=" + encodeURIComponent(paramsGetPage[nomParam]) ; }'.PHP_EOL ;
		$ctn .= (($this->DesactBtnsApresSoumiss) ? PHP_EOL ."\t\t".'ChangeStatutBtns'.$this->IDInstanceCalc.'(form, false)' : '').'
		if(VerifFormulaire'.$this->IDInstanceCalc.'(form)) {
			AppelAjaxSoumetForm'.$this->IDInstanceCalc.'(form, urlFormulaire, chaineRequete, function() {
				ActiveChampsParamsGet'.$this->IDInstanceCalc.'(parametresGet) ;
			'.(($this->DesactBtnsApresSoumiss) ? PHP_EOL ."\t\t".'ChangeStatutBtns'.$this->IDInstanceCalc.'(form, true)' : '').' ; 
			}) ;
		}
		else {
			'.(($this->DesactBtnsApresSoumiss) ? PHP_EOL ."\t\t".'ChangeStatutBtns'.$this->IDInstanceCalc.'(form, true)' : '').'
		}
		return false ;
	}
	function VerifFormulaire'.$this->IDInstanceCalc.'(form) {
		var nomCommande = "" ;
		if(document.getElementsByName("'.$this->IDInstanceCalc.'_Commande").length > 0)
			nomCommande = document.getElementsByName("'.$this->IDInstanceCalc.'_Commande")[0].value ;
		var OK = true ;'.(($this->InstrsJSAvantSoumetForm != '') ? PHP_EOL  .$this->InstrsJSAvantSoumetForm : '').'
		return OK ;
	}
	function VerifFormulaire'.$this->IDInstanceCalc.'(form) {
		var nomCommande = "" ;
		if(document.getElementsByName("'.$this->IDInstanceCalc.'_Commande").length > 0)
			nomCommande = document.getElementsByName("'.$this->IDInstanceCalc.'_Commande")[0].value ;
		var OK = true ;'.(($this->InstrsJSAvantSoumetForm != '') ? PHP_EOL  .$this->InstrsJSAvantSoumetForm : '').'
		return OK ;
	}
	function ActiveChampsParamsGet'.$this->IDInstanceCalc.'(parametresGet) {
		if(parametresGet != undefined) {
			for(var i=0; i<parametresGet.length; i++) {
				var nomParam = parametresGet[i] ;
				var valeurParam = PvPlatfSvc.adressePage.paramGet(nomParam) ;
				var elementParam = document.getElementById(nomParam) ;
				if(elementParam != null) {
				
					nomParam = elementParam.name ;
					valeurParam = elementParam.value ;
					elementParam.removeAttribute("disabled") ;
				}
			}
		}
	}
	function ChangeStatutBtns'.$this->IDInstanceCalc.'(form, statut) {
		for(var i=0; i<form.elements.length; i++)
		{
			var elem = form.elements[i] ;
			if(elem.type == "submit")
			{
				if(statut == false)
				{
					if(elem.disabled != undefined)
						elem.disabled = "disabled" ;
					else
						elem.setAttribute("disabled", "disabled") ;
				}
				else
				{
					elem.removeAttribute("disabled") ;
				}
			}
		}
	}
	function ActualiseDispo'.$this->IDInstanceCalc.'(result) {
'.$this->CtnJSActualiseDispo().'
	}
	function AppelAjaxSoumetForm'.$this->IDInstanceCalc.'(form, urlFormulaire, chaineRequete, quandChargmtFini) {
		// alert(urlFormulaire) ;
		jQuery.ajax(urlFormulaire, {
			type: "POST",
			data : chaineRequete,
			dataType : "json",
			success : function(result) {
				ActualiseDispo'.$this->IDInstanceCalc.'(result) ;
				if(quandChargmtFini !== undefined) {
					quandChargmtFini() ;
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				PvPlatfSvc.boiteDlg.afficheExceptionAjax(jqXHR, textStatus, errorThrown) ;
				if(quandChargmtFini !== undefined) {
					quandChargmtFini() ;
				}
			}
		}) ;
	}
</script>' ;
				return $ctn ;
			}
			protected function CtnJSActualiseDispo()
			{
				$ctn = '' ;
				$defCols = $this->ObtientDefColsVisible() ;
				$ctn .= 'var indiceCols = '.svc_json_encode(array_keys($defCols)).' ;
var maxElems = '.intval($this->MaxElements).' ;
if(result.erreur !== undefined) {
	if(result.erreur.code === 0) {
		if(result.contenu.totalElements > 0) {
			jQuery("#'.$this->IDInstanceCalc.'_BlocMsg").hide() ;
			jQuery("#'.$this->IDInstanceCalc.'_RangeeDonnees").show() ;
			for(var i=0; i<maxElems; i++) {
				var lgn = jQuery("#'.$this->IDInstanceCalc.'_Ligne_" + i.toString()) ;
				if(i < result.contenu.totalElements) {
					lgn.show() ;
					for(var j=0; j<indiceCols.length; j++) {
						var cell = jQuery("#'.$this->IDInstanceCalc.'_Cell_" + i.toString() + "_" + indiceCols[j].toString()) ;
						// alert(i.toString() + "_" + indiceCols[j].toString() + " : " + result.contenu.valeursCellules[i][indiceCols[j]]) ;
						cell.html(result.contenu.valeursCellules[i][indiceCols[j]]) ;
					}
				}
				else {
					lgn.hide() ;
				}
			}
		}
		else {
			jQuery("#'.$this->IDInstanceCalc.'_BlocMsg").show() ;
			jQuery("#'.$this->IDInstanceCalc.'_RangeeDonnees").hide() ;
			jQuery("#'.$this->IDInstanceCalc.'_BlocMsg").html('.svc_json_encode($this->MessageAucunElement).') ;
		}
	}
	else {
		jQuery("#'.$this->IDInstanceCalc.'_BlocMsg").show() ;
		jQuery("#'.$this->IDInstanceCalc.'_RangeeDonnees").hide() ;
		jQuery("#'.$this->IDInstanceCalc.'_BlocMsg").html(result.erreur.message) ;
	}
}
else
{
	jQuery("#'.$this->IDInstanceCalc.'_BlocMsg").show() ;
	jQuery("#'.$this->IDInstanceCalc.'_RangeeDonnees").hide() ;
	jQuery("#'.$this->IDInstanceCalc.'_BlocMsg").html('.svc_json_encode($this->MessageAucunElement).') ;
}' ;
				return $ctn ;
			}
			protected function CtnJSEnvoiFiltres(& $parametresRendu)
			{
				$ctn = '' ;
				$ctn .= 'function SoumetEnvoiFiltres'.$this->IDInstanceCalc.'(parametres)
{
	var parametresGet = '.svc_json_encode($parametresRendu).' ;
	var idFormulaire = '.svc_json_encode('FormulaireEnvoiFiltres'.$this->IDInstanceCalc).' ;
	for(var nom in parametres)
	{
		if(parametresGet[nom] != undefined)
		{
			parametresGet[nom] = parametres[nom] ;
		}
		else
		{
			var tableauNoeuds = document.getElementsByName(nom) ;
			if(tableauNoeuds.length > 0)
			{
				for(var j=0; j<tableauNoeuds.length; j++)
				{
					if(tableauNoeuds[j].form != null && tableauNoeuds[j].form.id != idFormulaire)
					{
						tableauNoeuds[j].value = parametres[nom] ;
					}
				}
			}
		}
		var formulaire = document.getElementById(idFormulaire) ;
		if(formulaire != null)
		{
			var url = "?'.urlencode($this->ZoneParent->NomParamScriptAppele).'='.urlencode($this->ScriptParent->NomElementZone).'" ;
			for(var nom in parametresGet)
			{
				if(url != "")
					url += "&" ;
				url += encodeURIComponent(nom) + "=" + encodeURIComponent(parametresGet[nom]) ;
			}
		}
		AppelAjaxSoumetForm(formulaire, url, "") ;
	}
}' ;
				return $ctn ;
			}

		}
	}
	
?>