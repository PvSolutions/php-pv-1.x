# Composants IU - PHP-PV

Les composants IU permettent d’interagir avec les utilisateurs.

## Utilisation

Vous devez suivre ce procédé :

1. Initier le composant
```php
$comp = new PvFormulaireDonnesHtml() ;
```
2. Renseigner ses propriétés d’initiation, s’il en possède
```php
$comp->InscrireCommandeExecuter = 1 ;
```
3. Adoptez le script ou la zone contexte par les méthodes AdopteScript($nom, & $script) ou AdopteZone($nom, $zone).
```php
$comp->AdopteScript("monComposant", $this) ;
```
4. Charger la configuration du composant par la méthode ChargeConfig()
```php
$comp->ChargeConfig() ;
```
5. Renseigner ses autres propriétés
```php
$comp->CommandeExecuter->Libelle = "VALIDER" ;
$comp->SuccesMessageExecution = "La page a été modifiée" ;
```
6. Invoquer le Rendu du composant par la méthode RenduDispositif()
```php
$ctn = $comp->RenduDispositif() ;
```
## Définition

Vous devez déclarer les composants IU dans la zone web, le document web ou le script web.
Pour le définir (étape 1. à 5 de l’utilisation), utilisez ces méthodes :

Classe | Méthode | Directives
------------- | ------------- | -------------
Document Web | PrepareRendu(& $zone) | Aucun
Zone Web | DetermineEnvironnement(& $script) | Invoquer parent::DetermineEnvironnement($script) après avoir défini le composant
Script Web | DetermineEnvironnement() | Aucun

Vous invoquez le rendu séparément :

Classe | Méthode | Directives
------------- | ------------- | -------------
Document Web | RenduEntete(& $zone) | Invoquer parent::RenduEntete($zone) avant le rendu du composant
Document Web | RenduPied(& $zone) | Invoquer parent::RenduPied($zone) après le rendu du composant
Zone Web | RenduContenuCorpsDocument () | Aucun
Script Web | protected RenduDispositifBrut() | Aucun
Script Web | RenduSpecifique() | Aucun

## Types de composant

### Données

Nom | Classe | Rôle
------------- | ------------- | -------------
Tableau de données Html | PvTableauDonneesHtml | Affiche sous forme de tableau des données
Grille de données Html | PvGrilleDonneesHtml | Affiche sous forme de grille des données
Formulaire de données Html | PvFormulaireDonneesHtml | Affiche sous forme de formulaire de données

### Graphiques et statistiques

Nom | Classe | Rôle
------------- | ------------- | -------------
Chart pChart | PvPChart | Chart réalisée avec la librairie PHP pChart 2.0

### Sliders

Nom | Classe | Rôle
------------- | ------------- | -------------
Slider JQuery Camera | PvJQueryCamera | Slider réalisé à partir de la librairie Javascript jQuery Camera


