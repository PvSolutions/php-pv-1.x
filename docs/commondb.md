# Les classes base de données - PHP-PV

## Présentation

PHP-PV inclut les classes de base de données **CommonDB**.
Ces bases de données offrent les avantages suivants :
-	Elles ferment automatiquement les connexions à la fin du script PHP, ou après chaque exécution d’une requête.
-	Elles possèdent des méthodes pour sélectionner, insérer, modifier et supprimer des lignes à partir de tableau
-	Elles possèdent des méthodes pour invoquer les fonctions SQL Natives (fonction pour obtenir la date du jour, …)

## Propriétés et Méthodes principales

Propriété/Méthode | Rôle
------------ | -------------
$ConnectionParams = array() | Contient les paramètres de connexion à la base de données.
InitConnectionParams() | Définit les paramètres de connexion.
InitConnection() | Ouvre la connexion sur la base de données
FinalConnection() | Ferme la connexion à la base de données
$ParamPrefix | Préfixe natif des paramètres de la base de données
$AutoCloseConnection | Ferme automatiquement les connexions après l’exécution d’une requête SQL. Valeur par défaut : true 
RunSql($sql, $params=array()) | Exécute le requête $sql sur la base de données, en appliquant les paramètres $params. Renvoie un résultat Booléen.
FetchSqlRows($sql, $params=array()) | Exécute la requête $sql sur la base de données, en appliquant les paramètres $params. Renvoie un tableau contenant les résultats. Chaque ligne trouvée est un tableau associatif dont les clés sont les colonnes de la requête.
FetchSqlRow ($sql, $params=array()) | Exécute la requête $sql sur la base de données, en appliquant les paramètres $params. Renvoie la 1ère ligne. Cette ligne est un tableau associatif dont les clés sont les colonnes de la requête. Elle ramène false s’il y a une exception.
InsertRow($tableName, $row=array()) | Insère la ligne $row dans la table $tableName. Les clés de la ligne $row doivent être celles des colonnes de $tableName. L’insertion s’appliquera uniquement sur les colonnes renseignées.
UpdateRow($tableName, $row=array(), $where, $params=array()) | Mets à jour la ligne $row dans la $tableName, quand la condition $where est respectée.
DeleteRow($tableName, $where, $params=array()) | Supprime les lignes dans la $tableName, quand la condition $where est respectée.
RunStoredProc($procName, $params=array()) | Exécute la procédure stockée $procName avec les paramètres $params.
FetchStoredProcRows($procName, $params=array()) | Exécute et renvoie les résultats de la procédure $procName avec les paramètres $params.
FetchStoredProcRow($procName, $params=array()) | Exécute et renvoie la 1ère ligne résultat de la procédure $procName avec les paramètres $params.


## Paramètres de connexion

Les clés du tableau **$ConnectionParams** sont :
- server : Hote du serveur de base de données
- schema : Nom de la base de données
- user : Login de l’utilisateur
- password : Mot de passe de l’utilisateur.
Ces informations sont interprétées différemment du type de base de données.

## Méthodes Natives SQL

Ces méthodes ramènent la fonction SQL adéquate.

Méthode | Paramètres | Description
------------ | ------------- | -------------
SqlConcat | $list | Concatène les éléments du tableau $list
SqlNow |  | Ramène la date et heure actuelle
SqlToDateTime | $expr | Convertit la valeur $expr en datetime.
SqlToTimestamp | $expr | Convertit la valeur $expr en timestamp
SqlAddSeconds | $expr, $val | Ajoute la valeur $val secondes à la valeur $expr
SqlAddMinutes | $expr, $val | Ajoute la valeur $val minutes à la valeur $expr
SqlAddHours | $expr, $val | Ajoute la valeur $val heures à la valeur $expr
SqlAddDays | $expr, $val | Ajoute la valeur $val jours à la valeur $expr
SqlAddMonths | $expr, $val | Ajoute la valeur $val mois à la valeur $expr
SqlAddYears | $expr, $val | Ajoute la valeur $val années à la valeur $expr
SqlDateDiff | $expr1, $expr2 | Calcule le nombre de secondes entre $expr1 et $expr2
SqlLength | $expr | Retourne le nombre de caractères dans la chaîne $expr
SqlSubstr | $expr, $start, $length=0 | Extrait dans $expr la chaine commençant par $start, de taille $length.
SqlIndexOf | $expr, $search, $start=0 | Renvoie l’indice de l’occurrence de $search à partir de $start. valeur minimale 0 dans $expr.
SqlIsNull | $expr | Vérifie si $expr est la valeur Nulle de la base de données
SqlStrToDateTime | $dateName | Convertit la chaine $dateName au format datetime de la base de données
SqlDateToStrFr | $dateName, $includeHour=0 | Convertit la date $dateName au type chaine de caractère de la base de données. Si $includeHour est 1, l’heure sera convertie également. Le format supporté est dd/mm/yyyy.
SqlToInt | $expression | Convertit l’expression $expression au type INTEGER de la base de données
SqlToDouble | $expression | Convertit l’expression $expression au type DOUBLE de la base de données
SqlToString | $expression | Convertit l’expression $expression au type Chaine de Caractères de la base de données


## Les fournisseurs de base de données

### MySQL

La classe est **MysqliDB**. Elle utilise l’extension PHP Mysqli.
Pour recevoir les données encodés en iso-8859-1, modifiez la classe ainsi :

```php
class MysqlDBIso extends MysqliDB // Changer le nom de la classe
{
public $AutoSetCharacterEncoding = 1 ;
public $MustSetCharacterEncoding = 1 ;
public $SetCharacterEncodingOnFetch = 1 ;
public $CharacterEncoding = 'utf8' ;
public function DecodeRowValue($value)
{
if(! is_string($value))
{
return parent::DecodeRowValue($value) ;
}
return html_entity_decode(htmlentities($value, ENT_COMPAT, 'ISO-8859-1')) ;
}
public function EncodeParamValue($value)
{
if(! is_string($value))
{
return parent::EncodeParamValue($value) ;
}
return html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'ISO-8859-1') ;
}
}
```

## Oracle

La classe **OciDB** permet de manipuler une base de données Oracle de 8g à 12c.
Elle utilise l’extension PHP **oci8-11g**.

## Sql Server

La classe **SqlSrvDB** manipule une base de données SQL Server. Elle utilise l’extension PHP **sqlsrv**.

