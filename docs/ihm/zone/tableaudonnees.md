# Le Tableau de Données - PHP-PV

## Présentation

Le tableau de données est un composant IU. Il affiche :
- Un formulaire de champs pour filtrer les résultats
- Un bloc de commandes, pour l’exportation des résultats à un format précis…
- Un tableau des résultats de la recherche
La classe de ce composant est **PvTableauDonnesHtml**.
 
![Apercu tableau données](../../images/tabldonneeshtml.png)
 
## Utilisation basique

Il utilise toujours un fournisseur de données pour le rendu.

```php
class MonScript1 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
// Déclaration
$this->Tabl1 = new PvTableauDonneesHtml() ;
// Chargement de la config
$this->Tabl1->AdopteScript("tabl1", $this) ;
$this->Tabl1->ChargeConfig() ;
// Définition des filtres de sélection
$this->Flt1 = $this->Tabl1->InsereFltSelectHttpGet("expression", "champ1 like concat(<self>, '%')") ;
$this->Flt1->Libelle = "Expression" ;
// Définition des colonnes
$this->Tabl1->InsereDefColCachee("id") ;
$this->Tabl1->InsereDefCol("champ1", "Champ 1") ;
$this->Tabl1->InsereDefCol("champ2", "Champ 2") ;
// Définition du fournisseur de données
$this->Tabl1->FournisseurDonnees = new PvFournisseurDonneesSql() ;
$this->Tabl1->FournisseurDonnees->BaseDonnees = new MaBD1() ;
$this->Tabl1->FournisseurDonnees->RequeteSelection = "matable1" ;
}
public function RenduSpecifique()
{
$ctn = '' ;
$ctn .= $this->Tabl1->RenduDispositif() ;
return $ctn ;
}
}
```

## Filtres de sélection

Méthode | Description
------------- | -------------
InsereFltSelectHttpGet($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre http GET
InsereFltSelectHttpPost($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre http POST
InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='') | Ajoute un filtre http UPLOAD. Tous les fichiers téléchargés seront déposés dans le répertoire $cheminDossierDest
InsereFltSelectSession($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre contenant la valeur d’une session
InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='') | Ajoute un filtre basé sur une valeur fixe
InsereFltSelectCookie($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre contenant la valeur d’un cookie

## Définitions de colonne

Propriété / Méthode | Description
------------- | -------------
$DefinitionColonnes | Tableau des définitions de colonne
InsereDefColCachee($nomDonnees, $aliasDonnees="") | Inscrit une définition de colonne cachée. 
InsereDefColInvisible($nomDonnees, $aliasDonnees="") | 
InsereDefCol($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne, avec un libellé.
InsereDefColBool($nomDonnees, $libelle="", $aliasDonnees="", $valPositive="", $valNegative="") | Inscrit une définition de colonne qui affiche un libellé en fonction d’une valeur booléenne.
InsereDefColChoix($nomDonnees, $libelle="", $aliasDonnees="", $valsChoix=array()) | Inscrit une définition de colonne qui affiche un libellé en fonction d’une valeur.
InsereDefColMonnaie($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne au format monétaire
InsereDefColMoney($nomDonnees, $libelle="", $aliasDonnees="") | 
InsereDefColDateFr($nomDonnees, $libelle="", $inclureHeure=0) | Inscrit une définition de colonne au format Français (dd/mm/yyyy). Si $inclureHeure est égal à 1, l’heure sera affichée également.
InsereDefColDateTimeFr($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne au format Français (dd/mm/yyyy hh:mi:ss)
InsereDefColDetail($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne, qui affiche les 1ers caractères de la ligne. Si vous posez le curseur sur cette cellule, un bloc contenant le texte intégral apparaîtra.
InsereDefColHtml($modeleHtml="", $libelle="") | Inscrit une définition de colonne qui affichera un contenu HTML.
InsereDefColTimestamp($nomDonnees, $libelle="", $formatDate="d/m/Y H:i:s") | Inscrit une définition de colonne qui affichera une date à partir d’un timestamp
InsereDefColActions($libelle, $actions=array()) | Inscrit une définition de colonne affichera des liens.

## Source de valeurs supplémentaires

Vous pouvez étendre les lignes calculées dans le tableau de données. Utilisez la propriété **$SourceValeursSuppl**. Etendez la classe **PvSrcValsSupplLgnDonnees** pour réécrire sa méthode **Applique(& $composant, $ligneDonnees)**.
Vous utiliserez ces nouvelles valeurs uniquement dans une définition de colonne HTML.

```php
class SrcValsSuppl1 extends PvSrcValsSupplLgnDonnees
{
public function Applique(& $composant, $ligneDonnees)
{
$results = array('menu' => '<a href="?appelleScript=developper&id='.urlencode($ligneDonnees ["id"]).'">+</a>') ;
return array_merge($ligneDonnees, $results) ;
}
}
class MonScript1 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
$this->Tabl1 = new PvTableauDonneesHtml() ;
$this->Tabl1->AdopteScript("tabl1", $this) ;
$this->Tabl1->ChargeConfig() ;
// ...
$this->Tabl1->SourceValeursSuppl = new SrcValsSuppl1() ;
// ...
$this->Tabl1->InsereDefColCachee("id") ;
$this->Tabl1->InsereDefColHtml('${menu} ${id}', 'Actions') ;
}
}
```

## Autres propriétés

Propriété / Méthode | Description
------------- | -------------
$Largeur | Largeur du formulaire de filtres
$LargeurFormulaireFiltres | Largeur du formulaire de filtres
$AlignFormulaireFiltres | Alignement du formulaire de filtres
$MessageAucunElement | Message lorsqu’il n’y a aucun élément trouvé
$ElementsEnCours | Tableau contenant toutes les lignes trouvées
$AlerterAucunElement | Affichera le message s’il n’y a aucun élément
$TriPossible | Permettra le tri
$TotalElements | Nombre de lignes retournées
$CacherNavigateurRangees | Cacher le navigateur de rangées
$CacherFormulaireFiltres | Cacher le formulaire de filtres
$CacherBlocCommandes | Cacher le bloc de commandes
$MaxElementsPossibles = array(20) | Nombres maximum de lignes par rangée

## Liens d’action

Méthode | Description
------------- | -------------
InsereLienAction(& $col, $formatUrl='', $formatLib='') | Inscrit un lien dans la colonne Action $col.
InsereLienActionAvant(& $col, $index, $formatUrl='', $formatLib='') | Inscrit un lien dans la colonne Action $col à la position $index
InsereIconeAction(& $col, $formatUrl='', $formatCheminIcone='', $formatLib='') | Inscrit une icône dans la colonne Action $col.
InsereIconeActionAvant(& $col, $index, $formatUrl='', $formatCheminIcone='', $formatLib='') | Inscrit une icône dans la colonne Action $col à la position $index

## Commandes

Propriété / Méthode | Description
------------- | -------------
$Commandes | Tableau contenant toutes les commandes
InsereCommande($nom, $commande) | Inscrit une commande dans le tableau
InscritCmdRafraich($libelle='Actualiser', $cheminIcone='') | Inscrit une commande qui soumet le formulaire de filtres
InsereCmdRedirectUrl($nomCmd, $url, $libelle='') | Inscrit une commande qui redirige sur une URL
InsereCmdRedirectScript($nomCmd, $nomScript, $libelle='', $params=array()) | Inscrit une commande qui redirige sur un script de la zone
InsereCmdScriptSession($nomCmd, $libelle='', $urlDefaut=array()) | Inscrit une commande qui redirige sur le script session de la zone
InsereCmdExportTexte($nomCmd, $libelle='') | Inscrit une commande qui exporte les résultats au format texte (CSV)
InsereCmdExportExcel($nomCmd, $libelle='') | Inscrit une commande qui exporte les résultats au format HTML pour Excel

## Rendu du tableau de données

Vous pouvez personnaliser le rendu du tableau de données avec sa propriété **$DessinateurFiltresSelection**.
Référez-vous au rendu des filtres d’édition du formulaire de données pour l’utilisation.



