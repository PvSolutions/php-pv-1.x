<?php
	
	if(! defined('PV_TABLEAU_DONNEES_CORDOVA'))
	{
		define('PV_TABLEAU_DONNEES_CORDOVA', 1) ;;
		
		class PvActCalcRenduTablDonneesCordova extends PvMethodeJsonCordova
		{
			protected function ConstruitResultat()
			{
				$tabl = & $this->ComposantIUParent ;
				$tabl->DetecteParametresLocalisation() ;
				$tabl->CalculeElementsRendu() ;
				$this->Resultat->messageExecution = $tabl->MessageExecution ;
				$this->Resultat->totalElements = $tabl->TotalElements ;
				$this->Resultat->totalRangees = $tabl->TotalRangees ;
				$this->Resultat->maxElements = $tabl->MaxElements ;
				$this->Resultat->indiceDebut = $tabl->IndiceDebut ;
				$this->Resultat->elementsEnCours = $tabl->ElementsEnCours ;
			}
		}
		
		class PvTableauDonneesCordova extends PvTableauDonneesHtml
		{
			public $ActPrincCalculeRendu ;
			public static function & InsereActionPrincForm(& $tabl, $nom, & $action)
			{
				$action->ComposantIUParent = & $tabl ;
				$action->NomElementComposantIU = $nom ;
				return $tabl->ZoneParent->InsereActionPrinc($nom.'_'.$tabl->IDInstanceCalc, $action) ;
			}
			public static function AdopteZoneTabl(& $tabl, $nom, $zone)
			{
				$tabl->ActPrincCalculeRendu = PvFormulaireDonneesCordova::InsereActionPrincForm($tabl, "calculeRendu", new PvActCalcRenduTablDonneesCordova()) ;
			}
			public static function RenduRangeeDonneesTabl(& $tabl)
			{
				$ctn = '' ;
				$libelleTriAsc = '<span class="text-muted fa fa-angle-up" title="'.htmlspecialchars($tabl->LibelleTriAsc).'"></span>' ;
				$libelleTriDesc = '<span class="text-muted fa fa-angle-down" title="'.htmlspecialchars($tabl->LibelleTriDesc).'"></span>' ;
				$libelleTriAscSelectionne = '<span class="fa fa-angle-up" title="'.htmlspecialchars($tabl->LibelleTriAsc).'"></span>' ;
				$libelleTriDescSelectionne = '<span class="fa fa-angle-down" title="'.htmlspecialchars($tabl->LibelleTriDesc).'"></span>' ;
				$parametresRendu = $tabl->ParametresCommandeSelectionnee() ;
				$ctn .= '<div class="panel panel-default"><div class="panel-body ConteneurRangeeDonnees">'.PHP_EOL ;
				$ctn .= '<div class="MessageErreur"></div>'.PHP_EOL ;
				$ctn .= '<div class="AucunElement"></div>'.PHP_EOL ;
				$ctn .= '<table' ;
				$ctn .= ' class="RangeeDonnees table table-striped"' ;
				$ctn .= '>'.PHP_EOL ;
				$ctn .= '<thead>'.PHP_EOL ;
				$ctn .= '<tr class="Entete">'.PHP_EOL ;
				foreach($tabl->DefinitionsColonnes as $i => $colonne)
				{
					if(! $colonne->EstVisible($tabl->ZoneParent))
						continue ;
					$triPossible = ($tabl->TriPossible && $colonne->TriPossible) ;
					$ctn .= '<th scope="col"' ;
					if($colonne->Largeur != "")
					{
						$ctn .= ' width="'.$colonne->Largeur.'"' ;
					}
					if($colonne->AlignEntete != "")
					{
						$ctn .= ' align="'.$colonne->AlignEntete.'"' ;
					}
					$ctn .= '>' ;
					$ctn .= $colonne->ObtientLibelle() ;
					$ctn .= '</th>'.PHP_EOL ;
				}
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</thead>'.PHP_EOL ;
				$ctn .= '<tbody>'.PHP_EOL ;
				$maxElements = $tabl->MaxElementsPossibles[0] ;
				for($j = 0; $j < $maxElements; $j++)
				{
					$ctn .= '<tr class="Ligne">'.PHP_EOL ;
					foreach($tabl->DefinitionsColonnes as $i => $colonne)
					{
						if(! $colonne->EstVisible($tabl->ZoneParent))
							continue ;
						$ctn .= '<td' ;
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
							$ctn .= ' class="Cellule '.htmlentities($colonne->NomClasseCSS).'"' ;
						}
						$ctn .= ' id="Cellule_'.$j.'_'.$i.'"' ;
						$ctn .= '>' ;
						$ctn .= '</td>'.PHP_EOL ;
					}
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$ctn .= '</tbody>'.PHP_EOL ;
				$ctn .= '</table>'.PHP_EOL ;
				$ctn .= '</div></div>'.PHP_EOL ;
				return $ctn ;
			}
			public static function CtnJsEnvoiFiltresTabl(& $parametresRendu)
			{
				$ctn = '' ;
				$ctn .= 'function SoumetEnvoiFiltres'.$tabl->IDInstanceCalc.'(parametres)
{
var parametresGet = '.svc_json_encode($parametresRendu).' ;
var idFormulaire = '.svc_json_encode('FormulaireEnvoiFiltres'.$tabl->IDInstanceCalc).' ;
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
}
var formulaire = document.getElementById(idFormulaire) ;
if(formulaire != null)
{
var url = "'.$tabl->ActPrincCalculeRendu->ObtientUrl().'" ;
for(var nom in parametresGet)
{
if(url != "")
url += "&" ;
url += encodeURIComponent(nom) + "=" + encodeURIComponent(parametresGet[nom]) ;
}
}
var jqRangeeDonnees = jQuery("#'.$tabl->IDInstanceCalc.'").find(".ConteneurRangeeDonnees") ;
pvZoneCordova.soumetForm(url, jQuery(tabl), function(resultat, xhr) {
RecoitSuccesDistant'.$tabl->IDInstanceCalc.'(jqRangeeDonnees, resultat, xhr, parametresGet) ;
}, function (erreur, statut, xhr) {
RecoitEchecDistant'.$tabl->IDInstanceCalc.'(jqRangeeDonnees, erreur, statut, xhr, parametresGet) ;
}) ;
}' ;
				return $ctn ;
			}
			public static function DeclarationSoumetFormulaireFiltresTabl($tabl, $filtres)
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
				foreach($tabl->ChampsGetSoumetFormulaire as $n => $v)
				{
					$filtresGetsEdit[] = $v ;
				}
				foreach($tabl->ParamsGetSoumetFormulaire as $n => $v)
				{
					$filtresGets[] = $v ;
				}
				$params = extract_array_without_keys($_GET, $nomFiltresGets) ;
				$filtresGets = array_unique($filtresGets) ;
				$indexMinUrl = 1 ;
				$urlFormulaire = $tabl->ActPrincCalculeRendu->ObtientUrl() ;
				$ctn = '<script type="text/javascript">'.PHP_EOL ;
				foreach($tabl->DefinitionsColonnes as $i => $colonne)
				{
					if(! $colonne->EstVisible($tabl->ZoneParent))
						continue ;
					$ctnJs = $colonne->InstrsJsPrepareRendu($tabl) ;
					if($ctnJs != '')
					{
						$ctn .= $ctnJs.PHP_EOL ;
					}
				}
				$ctn .= 'function DefinitCellule'.$tabl->IDInstanceCalc.'(noeudCellule, donnees, index) {
switch(index) {'.PHP_EOL ;
				$j= 0 ;
				foreach($tabl->DefinitionsColonnes as $i => $colonne)
				{
					if(! $colonne->EstVisible($tabl->ZoneParent))
						continue ;
					$instrJs = $colonne->InstrsJsFormatteValeur($tabl) ;
					if($instrJs == '')
					{
						continue ;
					}
					$ctn .= 'case '.$j.' : {'.PHP_EOL ;
					$ctn .= $instrJs.PHP_EOL ;
					$ctn .= '}'.PHP_EOL ;
					$ctn .= 'break ;'.PHP_EOL ;
					$j++ ;
				}
				$ctn .= '}
}'.PHP_EOL ;
				$ctn .= 'function RecoitSuccesDistant'.$tabl->IDInstanceCalc.'(jqRangeeDonnees, resultat, xhr, parametresGet) {
if(parametresGet != undefined) {
for(var i=0; i<parametresGet.length; i++) {
var nomParam = parametresGet[i] ;
var elementParam = document.getElementById(nomParam) ;
if(elementParam != null) {
elementParam.removeAttribute("disabled") ;
}
}
}
var jqForm = jQuery("#'.$tabl->IDInstanceCalc.'") ;
var resultExec = JSON.parse(resultat) ;
if(resultExec !== null) {
if(resultExec.messageExecution !== null) {
jqRangeeDonnees.find(".MessageErreur").html(resultExec.messageExecution) ;
jqRangeeDonnees.find(".MessageErreur").show() ;
}
else {
if(resultExec.elementsEnCours.length === 0) {
jqRangeeDonnees.find(".AucunElement").show() ;
}
else {
jqRangee = jqRangeeDonnees.find(".RangeeDonnees") ;
jqRangee.find(".Ligne").hide() ;
jqRangee.children("tbody").children("tr").each(function(indexLigne) {
if(indexLigne >= resultExec.elementsEnCours.length) {
return ;
}
var jqLigne = jQuery(this) ;
jqLigne.children("td").each(function(indexCellule) {
var noeudCellule = jQuery(this).get(0) ;
DefinitCellule'.$tabl->IDInstanceCalc.'(noeudCellule, resultExec.elementsEnCours[indexLigne], indexCellule) ;
}) ;
jqLigne.show() ;
}) ;
jqRangee.show() ;
}
}
}
else {
pvZoneCordova.afficheExceptionAppelDistant() ;
}
}
function RecoitEchecDistant'.$tabl->IDInstanceCalc.'(jqRangeeDonnees, erreur, statut, xhr, parametresGet) {
if(parametresGet != undefined) {
for(var i=0; i<parametresGet.length; i++) {
var nomParam = parametresGet[i] ;
var elementParam = document.getElementById(nomParam) ;
if(elementParam != null) {
elementParam.removeAttribute("disabled") ;
}
}
}
}
function SoumetFormulaire'.$tabl->IDInstanceCalc.'(tabl)
{
var jqRangeeDonnees = jQuery("#'.$tabl->IDInstanceCalc.'").find(".ConteneurRangeeDonnees") ;
jqRangeeDonnees.children().hide() ;
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
if(elementParam != null) {
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
pvZoneCordova.soumetForm(urlFormulaire, jQuery(tabl), function(resultat, xhr) {
RecoitSuccesDistant'.$tabl->IDInstanceCalc.'(jqRangeeDonnees, resultat, xhr, parametresGet) ;
}, function (erreur, statut, xhr) {
RecoitEchecDistant'.$tabl->IDInstanceCalc.'(jqRangeeDonnees, erreur, statut, xhr, parametresGet) ;
}) ;
return false ;
}
function ActualiseFormulaire'.$tabl->IDInstanceCalc.'()
{
'.$tabl->CtnJsActualiseFormulaireFiltres().' ;
}
</script>' ;
				return $ctn ;
			}
			public static function & ChargeConfigTabl(& $tabl)
			{
				if($tabl->ToujoursAfficher == 1)
				{
					$tabl->ZoneParent->InscritInstrsJsOuvrEcran($tabl->ScriptParent, PvTableauDonneesCordova::InstrsJsDefinitRendu($tabl)) ;
				}
			}
			protected static function InstrsJsDefinitRendu(& $tabl)
			{
				$ctn = '' ;
				$ctn .= 'SoumetEnvoiFiltres'.$tabl->IDInstanceCalc.'({}) ;' ;
				return $ctn ;
			}
		}
		
		class PvGrilleDonneesCordova extends PvGrilleDonneesHtml
		{
		}
	}
	
?>