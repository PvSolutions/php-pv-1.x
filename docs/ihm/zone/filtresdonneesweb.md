# Les filtres de données http - PHP-PV

## Présentation

Ils sont surtout utilisés dans les formulaires et les tableaux de données.
Ils vous proposent des champs de saisie, qui seront soumis après validation.

## Propriétés et Méthodes principales

Propriété / Méthode | Description
------------- | -------------
$Libelle | Libellé
$EstEtiquette | Si la valeur est 1, le filtre affichera la valeur au lieu du champ de saisie.
$ValeurVide | Valeur NULLE du filtre.
$ValeurParDefaut | Valeur par défaut
$NePasLierParametre | Renvoie toujours la valeur par défaut du filtre.
$NomParametreLie | Nom du paramètre soumis par http
$NePasLireColonne | Ne change pas la valeur de la colonne liée au filtre. Utilisée dans les formulaires de données.
$AliasParametreDonnees | Expression de la colonne de données. Ex. TO_CHAR(&lt;self&gt;)
$ExpressionDonnees | Condition SQL lorsque le filtre est utilisé dans une recherche. Ex : MON_CHAMP = &lt;self&gt;
$NomColonneLiee | Nom de la colonne dans la table, pour un filtre d’édition
$ExpressionColonneLiee | Expression de la colonne dans la table, pour un filtre d’édition. Ex. PASSWORD(&lt;self&gt;)
$LectureSeule | Passer la valeur par défaut du filtre de données, et la soumettre dans le formulaire.
$Invisible | Le filtre ne sera pas affiché sur la page. Il renvoie toujours sa valeur par défaut
$NePasIntegrerParametre | Empêche le formulaire de données d’utiliser ce filtre pour la recherche.
Lie() | Définit la valeur soumise à partir du formulaire. Elle est utilisée après clic sur une commande de formulaire donnée ou le bouton « Rechercher » du tableau de données
$DejaLie | Signale si le filtre a été lié auparavant.
$ValeurParametre | Valeur liée. Utilisez plutôt la méthode Lie().
$Role | Type du filtre de données.
$TypeLiaisonParametre | Contient la valeur "get", valeur issue de $_GET ou "post", valeur issue de $_POST

## Correcteur de valeur

C’est une propriété qui encode/décode la valeur brute d’un filtre.
Vous devez étendre la classe **PvCorrecteurValeurFiltreBase** et réécrire les méthodes clées.

```php
class MonCorrectValFiltre1 extends PvCorrecteurValeurFiltreBase
{
public function Applique($valeur, & $filtre)
{
return htmlentities($valeur) ;
}
}

class MonScript1 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
// ...
$form = new PvFormulaireDonneesHtml() ;
// ...
$flt1 = $form->InsereFltEditHttpPost("flt1", "") ;
$flt1->CorrecteurValeur = new MonCorrectValFiltre1() ;
}
}
```

Il existe des correcteurs de valeurs déjà déclarés.
Classe | Description
------------- | -------------
PvCorrecteurValeurFiltreBase | Correcteur de valeur par défaut
PvCorrecteurValeurSansAccent | Enlève tous les caractères spéciaux.

## Composant de filtre

### Présentation

Le composant de filtre de données est le champ de saisie. Vous le définissez ainsi :
Méthode | Description
------------- | -------------
DeclareComposant($nomClasseComposant) | Définit le composant à partir du nom de la classe
RemplaceComposant($composant) | Définit le composant à partir de l’instance

Exemple :
```php
$flt1 = $form->InsereFltEditHttpPost("monchamp") ;
// Le composant est dans la variable $comp1
$comp1 = $flt1->DeclareComposant("PvZoneMultiligneHtml") ;
```

### Composants Eléments HTML

Classe | Description
------------- | -------------
PvZoneTexteHtml | Composant par défaut affectée au filtre. Affiche un champ INPUT
PvZoneMultiligneHtml | Affiche un champ TEXTAREA
PvZoneMotPasseHtml | Affiche un champ PASSWORD
PvZoneEtiquetteHtml | Affiche un champ en lecture seule.

### Composants de liste

Les composants de liste utilisent un fournisseur de données pour leur rendu.

```php
$comp1 = $flt1->DeclareComposant("PvZoneSelectHtml") ;
// Définition du fournisseur de données
$comp1->FournisseurDonnees = new PvFournisseurDonneesSql() ;
$comp1->FournisseurDonnees->BaseDonnees = new MaBD1() ;
$comp1->FournisseurDonnees->RequeteSelection = "matable1" ;
// Définition des valeur
$comp1->NomColonneValeur = "id" ;
$comp1->NomColonneLibelle = "monchamp1" ;
// Afficher une valeur par defaut s’il n’y a aucune valeur
$comp1->InclureElementHorsLigne = 1 ;
$comp1->ValeurElementHorsLigne = -1 ; 
$comp1->LibelleElementHorsLigne = " – Aucun --" ;
```

Classe | Description
------------- | -------------
PvZoneBoiteSelectHtml | Affiche une zone SELECT
PvZoneBoiteOptionsRadioHtml | Affiche une zone de plusieurs options RADIO à cocher.
PvZoneBoiteOptionsCocherHtml | Affiche une zone de plusieurs options CHECKBOX à cocher. Pour récupérer toutes les valeurs cochées, utilisez la propriété $ValeurBrute du filtre.
PvZoneCadreOptionsRadioHtml | Affiche une zone de plusieurs options RADIO à cocher, qui sont dans un IFRAME HTML

## Formatage de libellé
Si le filtre de données est en étiquette, son champ de saisie ne sera pas éditable.
Pour personnaliser ce rendu, utilisez la méthode **DefinitFmtLbl**. Etendez la classe **PvFmtLblBase** et réécrivez sa méthode **Rendu($valeur, & $composant)**.

```php
class MonFmtLbl1 extends PvFmtLblBase
{
public function Rendu($valeur, & $composant)
{
return base64_decode($valeur) ;
}
}
```

Ensuite, affectez ce format au composant avec la méthode **DefinitFmtLbl()** du filtre. Vous devez déclarer le composant avant d’utiliser cette méthode.

```php
$comp = $flt1->DeclareComposant("PvZoneTexteHtml") ;
// …
$flt1->DefinitFmtLbl(new MonFmtLbl1()) ;
```

Voici des formats déjà définis :

Classe | Description
------------- | -------------
PvFmtLblBase | Classe de base.
PvFmtLblWeb | Classe affectée par défaut
PvFmtLblDateFr | Affiche au format date français
PvFmtLblDateTimeFr | Affiche au format date et heure français
PvFmtMonnaie | Affiche au format monétaire

## Le filtre de données Upload

Le filtre de données Upload télécharge un fichier.

### Propriétés / Méthodes principales

Propriété / Méthodes | Description
------------- | -------------
$NettoyerCaractsFichier | Enlève les caractères spéciaux du nom fichier téléchargé.
$ExtensionsAcceptees | Tableau contenant les extensions uniquement acceptées. Si le fichier soumis n’a pas une extension, il ne sera pas copié dans le répertoire 
$ExtensionsRejetees | Tableau contenant les extensions à rejeter systématiquement.
$FormatFichierTelech | Format du nom de fichier téléchargé. 
$SourceTelechargement | Contient les valeurs "post" si aucun fichier n’est soumis ou "files" si un fichier a été soumis.
$InfosTelechargement | Contient les détails du fichier téléchargé.
$ToujoursRenseignerFichier | Renvoie une erreur dans le formulaire de données, si aucun fichier n’est soumis.

### Variables Format de fichier téléchargé

Les variables disponibles sont :
- **Cle** : Identifiant Unique
- **NombreAleatoire** : Nombre compris entre 1 & 10000
- **NomFichier** : Nom d’origine du fichier
- **Timestamp** : Timestamp actuel
- **Date** : Date au format YmdHis

Ex : "Bon-Commande-${Cle}"

### Caractéristiques du Composant

Le composant par défaut de ce filtre est le composant **PvZoneUploadHtml**.
Ses propriétés principales sont :

Propriété | Description
------------- | -------------
$InclureErreurTelecharg | Afficher l’erreur survenue lors du téléchargement
$InclureCheminCoteServeur | Afficher le chemin relatif du fichier téléchargé
$InclureZoneSelectFichier | Afficher les informations sur le fichier téléchargé
$CheminCoteServeurEditable | Autoriser la modification du chemin relatif sur le serveur
$InclureApercu | Définit l'affichage de l'aperçu.
$LargeurCadreApercu | Largeur HTML du cadre d’aperçu
$HauteurCadreApercu | Hauteur HTML du cadre d’aperçu.

### Valeurs Inclure Aperçu

Valeurs possibles :
- 0 : Ne pas autoriser d’aperçu
- 1 : Affiche un lien pour afficher dans le navigateur
- 2 : Afficher le fichier dans un cadre, si c’est possible


