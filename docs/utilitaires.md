# Utilitaires PHP-PV

## Common HttpSession

### Présentation

La classe **HttpSession** vous permet de télécharger le contenu d'une URL. Ses avantages :

- Il sauvegarde le contenu brut HTTP de la dernière requête et réponse
- Il envoie les requêtes avec n'importe quelle méthode et type de contenu
- Il gère les sessions avec les Cookie

Vous devez inclure le fichier **/Common/HttpSession.class.php** pour l'utiliser.

### Téléchargement d'URL

Pour télécharger des fichiers, utilisez la méthode **GetPage**.

```php
include "php-pv-master/Common/HttpSession.class.php" ;
$httpSession = new HttpSession() ;
$resultat = $httpSession->GetPage(
	"http://monsite/fichier.aspx", // URL
	array("id" => 2), // Paramètres
	array(
		"X-My-Header1" => 2,
		"X-My-Header2" => 2,
	), // Entêtes
) ;
echo $resultat ; // Contenu Body recu
```

Vous pouvez mettre directement la chaine de requête dans le 1er paramètre de cette méthode.

```php
$httpSession = new HttpSession() ;
$resultat = $httpSession->GetPage(
	"http://monsite/fichier.aspx?id=2", // URL
) ;
echo $resultat ; // Contenu Body recu
```

### Soumettre des données

Pour poster un formulaire, utilisez la méthode **PostData()**.

```php
$httpSession = new HttpSession() ;
$resultat = $httpSession->PostData(
	"http://monsite/fichier.aspx?id=2", // URL
	array(
		"param1" => "valeur 1",
		"param2" => "valeur 2",
		"param3" => "valeur 3",
	), // Paramètres
	array(
		"X-My-Header1" => 2,
		"X-My-Header2" => 3,
	), // Entêtes
) ;
```

### Soumettre des fichiers

Vous pouvez joindre des fichiers également avec la méthode **PostData()**.

Un fichier correspond à un tableau avec les clés :

- filename : chemin local du fichier
- type : type de contenu. facultatif
- binary : transférer en mode binaire. facultatif

```php
$httpSession = new HttpSession() ;
$resultat = $httpSession->PostData(
	"http://monsite/fichier.aspx?id=2", // URL
	array(
		"param1" => "valeur 1",
		"param2" => "valeur 2",
		"fichier1" => array(
			"filename" => "C:/Mon Dossier/Monfichier.jpg",
		),
	), // Paramètres
	array(
		"X-My-Header1" => 2,
		"X-My-Header2" => 3,
	), // Entêtes
) ;
```

### Propriétés/Méthodes principales

Vous pouvez manipuler la requête HTTP avec la classe **HttpSession**.

Propriété/Méthode | Description
------------ | -------------
$RequestHeaders | Tableau des entêtes de la requête à soumettre
$RequestMethod | Méthode HTTP à soumettre
$RequestVersion | Version HTTP de la requête.
$RequestContentType | Type de contenu de la requête.
$DownloadResponse | Valeur booléen. Confirme le téléchargement de la réponse.
$ResponseParseBodyEnabled | Valeur booléen. Télécharger le corps de la réponse également, en plus des entêtes.
$AutoSetContentType | Valeur booléen. Définit automatiquement le type de contenu de la requête.
$ConnectTimeout | Délai, en sécondes pour ouvrir une connexion à l'URL
$DownloadTimeout | Délai, en sécondes pour lire le contenu de la réponse

```php
$httpSession = new HttpSession() ;
$httpSession->ConnectTimeout = 120 ;
$httpSession->ResponseParseBodyEnabled = false ;
$resultat = $httpSession->PostData(
	"http://monsite/fichier.aspx?id=2", // URL
	array(
		"param1" => "valeur 1",
		"param2" => "valeur 2"
	)
) ;
```

Après avoir soumis votre appel HTTP, certaines propriétés vous seront utiles.

Propriété/Méthode | Description
------------ | -------------
GetRequestContents() | Retourne le contenu brut de la requête
GetResponseContents() | Retourne le contenu brut de la réponse
$ResponseHttpStatusCode | Code HTTP du statut de la réponse
$ResponseHttpStatusDesc | Description HTTP du statut de la réponse
$ResponseHeaders | Tableau des entêtes HTTP de la réponse
$ResponseData | Corps de la réponse
$ResponseHeadersData | Contenu brut des entêtes de la réponse

```php
$httpSession = new HttpSession() ;
$resultat = $httpSession->PostData(
	"http://monsite/fichier.aspx?id=2", // URL
	array(
		"param1" => "valeur 1",
		"param2" => "valeur 2"
	)
) ;
$requeteBrut = $httpSession->GetRequestContents() ;
$reponseBrut = $httpSession->GetResponseContents() ;
```

### Requêtes avec Type de contenu spécifique

Spécifiez le type de contenu de la requête avec la propriété **$RequestContentType**. Alors, soumettez votre requête avec **SubmitData()**.

```php
$httpSession = new HttpSession() ;
$httpSession->RequestContentType = "application/json" ;
$resultat = $httpSession->SubmitData(
	'http://localhost',
	'{ param1 : "valeur 1", param2 : 12}'
) ;
```

### Bibliothèques externes

La classe **HttpSession** utilise la librairie **url_to_absolute** pour les liens relatifs.

Le site internet de l'auteur n'existe plus.

## Autres Fonctions

PHP-PV offre des fonctions pour faciliter votre solution.

Fonction | Description
------------ | -------------
\_GET_def($param, $defaultValue=null) | Renvoie le paramètre $_GET\[**$param**\] sinon **$defaultValue**
\_POST_def($param, $defaultValue=null) | Renvoie le paramètre $_POST\[**$param**\] sinon **$defaultValue**
send_plain_mail($to, $subject, $text, $from='', $cc='', $bcc='') | Envoie un mail au format Texte à l'adresse **$to**. Renseignez l'expéditeur avec le paramètre **$from**
send_html_mail($to, $subject, $text, $from='', $cc='', $bcc='') | Envoie un mail au format HTML à l'adresse **$to**. Renseignez l'expéditeur avec le paramètre **$from**
date_fr($date) | Retourne la date au format Français dd/mm/yyyy.
date_time_fr($date) | Retourne la date et heure au format Français dd/mm/yyyy hh:ii:ss.
format_money($number, $decimal_count=2, $max_length=5) | Retourne le nombre au format monétaire Français
svc_json_encode($param) | Encode au format JSON **$param**. Alternative à la fonction PHP native **json_encode()**
svc_json_decode($param) | Décode au format JSON **$param**. Alternative à la fonction PHP native **json_decode()**
get_current_url() | Retourne l'URL du script en cours
get_current_url_dir() | Retourne l'URL du répertoire contenant le script en cours
update_url_params($url, $params=array(), $encodeValues=1, $forceDecodeParams=0) | Retourne l'URL **$url**, avec les clé/valeurs de **$params** dans la chaine de requête (query_string). **$encodeValues** force l'encodage des valeurs, et **$forceDecodeParams** analysera la chaine de requête avant d'inclure les valeurs de **$params**.
update_current_url_params($params=array(), $encodeValues=1, $forceDecodeParams=0) | Retourne l'URL du script en cours, avec les clé/valeurs de **$params** dans la chaine de requête (query_string).
update_url_param($ParamName, $ParamValue, $URL) | Retourne l'URL **$URL**, avec la paire **$ParamName**=**$ParamValue** dans la chaine de requête (query_string).

```php
class MonScript1 extends PvScriptWebSimple
{
public function RenduSpecifique()
{
$verifier = _GET_def("verifier") ;
$ctn .= '<p>Bienvenue sur ce site</p>' ;
if($verifier == 0)
{
$ctn = '<p><a href="'.update_current_url_params(array("verifier" => 1)).'">Vérifier</a></p>' ;
}
return $ctn ;
}
}
```

## Expat Xml Parser

### Présentation

La classe **ExpatXmlParser** vous permet d'analyser les fichiers XML. Elle utilise l'extension native PHP expat xml.

Vous devez inclure le fichier **/ExpatXml/ExpatXml.class.php** pour l'utiliser.

```php
include "php-pv-master/ExpatXml/ExpatXml.class.php" ;

$xmlparser = new ExpatXmlParser() ;
$docXml = $xmlparser->ParseContent('<?xml version="1.0"?>
<main>
<node>Test</node>
</main>') ;
```

### Analyse du contenu

Deux méthodes analysent le XML :

- **ParseContent($content)** analyse le contenu en paramètre
- **ParseFile($path)** analyse le fichier en paramètre

Elles retournent un objet avec le noeud XML **RootNode()**.

```php
include "php-pv-master/ExpatXml/ExpatXml.class.php" ;

$xmlparser = new ExpatXmlParser() ;
$docXml = $xmlparser->ParseFile('monfichier.xml') ;
$rootNode = $docXml->RootNode() ;
```

### Parcours d'un noeud

Les classes Noeud XML ont ces propriétés :

Fonction | Description
------------ | -------------
$Name | Nom du tag XML
$ElementType | Type du tag XML : text, attribute, node
$ElementID | Valeur de l'attribut ID de l'élément, si elle existe.
$Attributes | Tableau des attributs du Noeud XML
GetAttribute($name) | Retourne la valeur de l'attribut **$name**, sinon null.
$ChildNodes | Tableau des noeuds contenus par le Noeud XML
ChildNodeCount() | Retourne le nombre de noeuds enfants
GetElementById($id) | Retourne le 1er noeud XML contenu, dont l'attribut "ID" est **$id**
GetChildNodesByName($name) | Retourne les noeuds XML contenus, dont l'attribut "NAME" est **$name**
GetChildNodesByTagName($name) | Retourne les noeuds XML contenus, dont le nom du tag est **$name**
GetFirstNodeByTagName($name) | Retourne le 1er noeud XML contenu, dont le nom du tag est **$name**
ChildNodeToHash() | Retourne le tableau des noeuds contenus

```php
include "php-pv-master/ExpatXml/ExpatXml.class.php" ;
$xmlparser = new ExpatXmlParser() ;
$docXml = $xmlparser->ParseContent('<?xml version="1.0"?>
<main>
<mytag>Test</mytag>
<othertag>123</othertag>
</main>') ;
$rootNode = $docXml->RootNode() ;
print "Root tagName : ".$rootNode->Name."<br>" ;
print_r($rootNode->GetChildNodesByTagName("OTHERTAG")) ;
foreach($rootNode->ChildNodes as $i => $childNode)
{
// traiter le noeud enfant...
}
```