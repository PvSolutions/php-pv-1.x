# Tâches Web - PHP-PV

## Définition

Une tâche web est une tâche planifiée, qui exécute des instructions.
La tâche démarre automatiquement quand vous affichez n’importe script de la zone web, une fois son délai d’attente dépassé.
Elle s’exécute dans un autre processus http que celui du script.

## Déclaration

Veuillez créer votre tâche à partir de la classe **PvTacheWebBaseSimple**. Définissez la propriété « **DelaiExecution** » (en heure) et réécrivez la méthode **ExecuteInstructions()**.

```php
class MaTacheWeb1 extends PvTacheWebBaseSimple
{
public $DelaiExecution = 0.05 ; // S’exécute après 180 secondes
protected function ExecuteInstructions()
{
Echo "OK, ma tache est executee" ;
}
}
```

Les membres utiles dans la méthode **ExecuteInstructions()** sont :
- **ApplicationParent** : Renvoie l’Application
- **ZoneParent** : Renvoie la zone contenant la tâche web

## Le gestionnaire de tâches web

La zone web possède un gestionnaire de tâches web, dont les rôles sont :
-	Contenir les tâches web
-	Définir l’emplacement de sauvegardes des états de chaque tâche (en cours, terminé, date d’exécution, …)

Pour personnaliser le gestionnaire de tâches web, veuillez réécrire la méthode **ChargeGestTachesWeb()**.

La propriété **GestTachesWeb** représente le gestionnaire de tâches. Ses membres et méthodes utiles sont :

Propriété/Méthode | Description
------------- | -------------
NomDossierTaches | Chemin du répertoire contenant l’état de chaque tâche web. Le chemin est relatif au chemin du fichier PHP de la zone web.
InsereTacheWeb($nom, $tache) | Inscrit la tâche programmée dans la zone web

Exemple :
```php
class MaZoneWeb1 extends PvZoneWebSimple
{
// ...
protected function ChargeGestTachesWeb()
{
$this->GestTachesWeb->NomDossierTaches = "taches/data" ;
$this->InsereTacheWeb('tache1', new MaTacheWeb1()) ;
}
}
```

## Le fichier Etat de la tâche web

L’état de la tâche est sauvegardé dans le fichier de ce format :
**&lt;NomDossierTaches-du-GestTachesWeb&gt;/&lt;IDInstanceCalc-tache-web&gt;.dat**
Si vous supprimez ce fichier, la tâche web sera exécutée au prochain affichage de la zone web.


