# La classe Application - PHP-PV

## Présentation

L'Application dans PHP-PV contient toutes les interfaces exécutables (zone web, tache programmée...).

![Fonctionnement](images/diagramme-application.png)

## Méthodes principales

Nom | Contexte | Description
------------ | ------------- | -------------
InitConfig() | A réécrire | Définit les membres à l’instanciation de l’application
Execute() | A partir d’une instance | Exécute l’application
ChargeConfig() | A réécrire. Invoquée par Execute(). | Définit les membres pour l’exécution de l’application.
ChargeIHMs() | A réécrire. Invoquée par ChargeConfig() | Définit les Interfaces (Web, Console, SOAP, …) de l’application
ChargeTachesProgs() | A réécrire. Invoquée par ChargeConfig() | Invoquée par la méthode ChargeConfig().Définit les tâches programmées de l’application.
ChargeServsPersists() | A réécrire. Invoquée par ChargeConfig() | Invoquée par la méthode ChargeConfig()
InsereIHM(string $nom, & PvIHM $ihm) | Utiliser dans ChargeIHMs() | Inscrit une IHM (Interface web, console ou SOAP) dans l’application
InsereTacheProg(string $nom, & PvTacheProg $tacheProg) | Utiliser dans ChargeTachesProgs() | Inscrit une tâche programmée dans l’application
InsereServPersist(string $nom, & PvServicePersist $servPersist) | Utiliser dans ChargeServsPersists() | Inscrit un service persistant dans l’application

## Les élements d’application

L’élément d’application est la classe **PvElementApplication**. Elle est le noyau des interfaces web, console, SOAP, services et tâche programmée.
Quand l’application s’exécute, elle parcourt tous ses éléments d’application. Si l’un d’entre eux est actif, l’application démarre l’exécution de cet élément et arrête le parcours.
Pour savoir si un élément d’application est actif, l’application a deux possibilités :
- Vérifier si le chemin relatif de l’élément est celui du script PHP. La propriété est **CheminFichierRelatif**.
- La propriété **AccepterTousChemins** de cet élément a pour valeur **1**.

## Voir aussi

- [Premier projet](premierprojet.md)
- [La zone bootstrap 4](ihm/zonebootstrap4.md)
- [La zone web simple](ihm/zonewebsimple.md)
