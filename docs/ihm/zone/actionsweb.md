# Actions Web - PHP-PV

## Définition

Une Action Web est un ensemble d’instructions s’exécute dans la Zone Web. Elle ne se limite pas d’afficher un contenu HTML, comme les scripts web.
Elle peut également :
- déclencher le téléchargement d’un fichier
- renvoyer un fichier RSS, JS ou CSS
- renvoyer une réponse JSON
- exécuter un code précis, avant d’afficher le script web

## Déclaration

La zone web exécute une action web à partir du paramètre GET **appelleAction**.

Vous pouvez déclarer les actions dans plusieurs méthodes :

Objet | Méthode	Contexte | Description
------------- | ------------- | ------------- | -------------
Zone web | InsereActionPrinc($nom, $action) | Utiliser dans la méthode **ChargeConfig()** | Les actions principales s’exécutent avant d’exécuter le script en cours
Zone web | InsereActionAvantRendu($nom, $action) | Utiliser dans la méthode **ChargeConfig()**. | S’exécutent avant d’afficher le script en cours
Script web | InsereActionAvantRendu($nom, $action) | Utiliser dans la méthode **DetermineEnvironnement()** | Déclare l’action uniquement lorsque le script doit être affiché. Le nom de l’action sera basé sur l’ID Instance du script et le nom de l’action.

## Types d’action

Classe | Description | Utilisation
------------- | ------------- | -------------
PvActionBaseZoneWebSimple | Classe de base | Réécrire la méthode **Execute()**
PvActionNotificationWeb | Exécute des instructions et garde le résultat (succès/echec et message d’exécution) | Réécrire la méthode **Execute()**
PvActionResultatJSONZoneWeb / PvActionEnvoiJSON | Affiche un contenu JSON dans le navigateur | Réécrire la méthode **Execute()**. A l’intérieur, définissez la propriété Resultat. Cette propriété sera le retour JSON.
PvActionTelechargFichier | Démarre le téléchargement du fichier | Réécrire la méthode **Execute()**

### Utilisation PvActionNotificationWeb

Vous devez réécrire la méthode **Execute()**. A l’intérieur, utiliser ces méthodes pour définir le résultat :
- **ConfirmeSucces($msg)**
- **RenseigneErreur($msg)**
Dans le script ou la zone, utilisez la propriété **TypeErreur** et méthode **ObtientMessage()** de l’instance Action pour afficher le résultat. Pour tester si l’action a ramené un résultat, utilisez la méthode **PossedeMessage()**

### Utilisation PvActionTelechargFichier

Vous devez réécrire la méthode **Execute()**. A l’intérieur :
- Renseignez la propriété **NomFichierAttache** pour définir le nom du fichier téléchargé. Utilisez la fonction echo pour envoyer le contenu du fichier
- Si le fichier existe déjà, utilisez **CheminFichierSource** pour le charger.