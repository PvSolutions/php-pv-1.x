# Documents Web - PHP-PV

Un document web personnalise l’affichage complet de chaque script.
Dans la zone, il est utile :
- pour les scripts à imprimer
- pour les scripts qui s’afficheront dans une boîte de dialogue

## Déclaration

Chaque document web hérite de la classe **PvDocumentWebHtml**.

Veuillez réecrire les méthodes **PrepareRendu(& $zone)**, **RenduEntete(& $zone)** et **RenduPied(& $zone)**.
Vous pouvez manipuler le script sélectionné avec **$zone->ScriptPourRendu**

```php
class MonDocumentWeb1 extends PvDocumentWebHtml
{
public function PrepareRendu(& $zone)
{
// Inclure des libraires Javascript & CSS spécifiques au document
}
public function RenduEntete(& $zone)
{
return parent::RenduEntete($zone) ;
} 
public function RenduPied(& $zone)
{
return parent::RenduPied($zone) ;
} 
}
```

## Intégration dans la zone web

D’abord, vous devez mettre la propriété **UtiliserDocumentWeb** à 1.
Ensuite, déclarez chaque document dans la méthode **ChargeConfig()** de la zone web.

```php
class MaZoneWeb extends PvZoneWebSimple
{
public $UtiliserDocumentWeb = 1 ;
public function ChargeConfig()
{
Parent::ChargeConfig() ;
$this->DocumentsWeb["defaut"] = new MonDocumentWeb1() ;
$this->DocumentsWeb["impression"] = new MonDocumentWeb2() ;
}
}
```

Le 1er document web déclaré sera utilisé par défaut pour tous les scripts. Dans le cas ci-dessus, c’est le document web "defaut".

## Affectation à un script

Pour définir le document web du script, renseignez la propriété **NomDocumentWeb** du script.

```php
class MonScriptWeb3 extends PvScriptWebSimple
{
// …
public $NomDocumentWeb = "impression" ;
// …
}
```