# CSS / Javascript Zone Web - PHP-PV

## Bibliothèques CSS et Javascript

La zone web simple inclut automatiquement les scripts & styles CSS des librairies Javascript populaires.

### JQuery

Propriété | Spécification
------------- | -------------
$InclureJQuery | Mettre à 1 pour inclure la librairie jquery
$CheminJQuery | Chemin relatif du fichier Js jQuery. Par défaut : "js/jquery.min.js" 
$InclureJQueryMigrate | Mettre à 1 pour inclure la librairie jquery-migrate
$CheminJQueryMigrate | Chemin relatif du fichier Js JQueryMigrate. Par défaut : "js/jquery-migrate.min.js"

### JQuery UI

Propriété | Spécification
------------- | -------------
$InclureJQueryUi | Mettre à 1 pour inclure la librairie jqueryui
$CheminJsJQueryUi | Chemin relatif du fichier Js JQuery Ui. Par défaut : "js/jquery-ui.min.js"
$CheminCSSJQueryUi | Chemin relatif du fichier CSS jQuery Ui. Par défaut : "css/jquery-ui.css"

### Bootstrap

Propriété | Spécification
------------- | -------------
$InclureBootstrap | Mettre à 1 pour inclure la librairie bootstrap
$CheminJsBootstrap | Chemin relatif du fichier Js Bootstrap. Par défaut : "js/bootstrap.min.js"
$CheminCSSBootstrap | Chemin relatif du fichier CSS Bootstrap. Par défaut : "css/bootstrap.css"
$InclureBootstrapTheme | Mettre à 1 pour inclure un thème personnalisé Bootstrap
$CheminCSSBootstrapTheme | Chemin relatif du fichier CSS Bootstrap. Par défaut : "css/bootstrap-theme.min.css"

### Font Awesome

Propriété | Spécification
------------- | -------------
$InclureFontAwesome | Mettre à 1 pour inclure Font Awesome
$CheminFontAwesome | Chemin relatif du fichier CSS Font Awesome. Par défaut : "css/font-awesome.css"

## Contenus CSS et Javascript

La zone a également des méthodes pour insérer du contenu CSS et JS.

Méthode | Description
------------- | -------------
InscritContenuCSS ($contenu) | Insère un tag &lt;style&gt; avec le $contenu
InscritLienCSS ($href) | Insère un tag &lt;link rel="stylesheet" type="text/css" href="$href" /&gt;
InscritContenuJs ($contenu) | Insère un tag &lt;script&gt; avec le $contenu
InscritContenuJsCmpIE ($contenu, $versionMin=9) | Insère un tag &lt;script&gt; avec le $contenu, avec les directives IE
InscritLienJs ($src) | Insère un tag &lt;script&gt; avec la source $src
InscritLienJsCmpIE ($src, $versionMin=9) | Insère un tag &lt;script&gt; avec la source $src, avec les directives IE

Veuillez réécrire la méthode **InclutLibrairiesExternes()**, en invoquant la méthode parente.

```php
class MaZone1 extends PvZoneWebSimple
{
Protected function InclutLibrairiesExternes()
{
Parent::InclutLibrairiesExternes() ;
// Inscrire les autres librairies JS & CSS…
$this->InscritContenuCSS("body { text-align:center ; }") ;
} 
}
```
