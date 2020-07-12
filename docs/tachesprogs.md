# Taches programmées PHP-PV

## Présentation

La tâche programmée est un script PHP qui s'exécute.

Elle est un élément d'application. Elle hérite de la classe **PvTacheProg**, et doit réécrire sa méthode **ExecuteSession()**.

```php
class MaTacheProg1 extends PvTacheProg
{
public $AccepterTousChemins = 1 ;
protected function ExecuteSession()
{
echo "Traitement effectue !\n" ;
}
}
```

Inscrivez la tâche programmée dans la méthode **ChargeTachesProgs()** de l'application. Utilisez la méthode **InsereTacheProg($nom, $tacheProg)**. 

```php
class MonApplication1 extends PvApplication
{
public function ChargeTachesProgs()
{
$this->InsereTacheProg("maTache1", new MaTacheProg1()) ;
}
}
```

Inscrivez le script PHP dans le gestionnaire de tâches de votre système d'exploitation, pour planifier l'exécution.

## Exécution sur serveur web

La tâche programmée s'exécute en ligne de commande par défaut. Modifiez la propriété **$NaturePlateforme** à **"web"** si vous essayez sur un serveur web.

```php
class MaTacheProg1 extends PvTacheProg
{
public $AccepterTousChemins = 1 ;
public $NaturePlateforme = "web" ;
protected function ExecuteSession()
{
echo "Traitement effectue !\n" ;
}
}
```