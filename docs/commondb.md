# Les classes base de données - PHP-PV

## Présentation

PHP-PV inclut les classes de base de données **CommonDB**.
Ces bases de données offrent les avantages suivants :
-	Elles ferment automatiquement les connexions à la fin du script PHP, ou après chaque exécution d’une requête.
-	Elles possèdent des méthodes pour sélectionner, insérer, modifier et supprimer des lignes à partir de tableau
-	Elles possèdent des méthodes pour invoquer les fonctions SQL Natives (fonction pour obtenir la date du jour, …)

## Paramètres de connexion

Vous pouvez déclarer les paramètres de connexions dans le tableau **$ConnectionParams**. Les clés sont :
- server : Hote du serveur de base de données
- schema : Nom de la base de données
- user : Login de l’utilisateur
- password : Mot de passe de l’utilisateur.
Ces informations sont interprétées différemment du type de base de données.

```php
$bd = new MysqliDB() ;
$bd->ConnectionParams['server'] = "localhost" ;
$bd->ConnectionParams['schema'] = "gestion_produits" ;
$bd->ConnectionParams['user'] = "root" ;
$bd->ConnectionParams['password'] = "mysql" ;
```
Vous pouvez déclarer ces paramètres dans une classe spécialisée. Réécrivez la méthode **InitConnectionParams()**. C'est idéal car vous ne définissez pas les paramètres pour chaque usage.

```php
class MaBD extends MysqliDB
{
protected function InitConnectionParams()
{
$this->ConnectionParams['server'] = "localhost" ;
$this->ConnectionParams['schema'] = "gestion_produits" ;
$this->ConnectionParams['user'] = "root" ;
$this->ConnectionParams['password'] = "mysql" ;
}
}

$bd = new MaBD() ;
```

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
OpenQuery($sql, $params=array()) | Ouvre un curseur sur la base de données, pour une lecture progressive
ReadQuery($query) | Retourne l'enregistrement disponible sur le curseur ouvert avec **OpenQuery**. Renvoie NULL s'il n'y a aucun. L'enregistrement est un tableau dont les champs sont les clés.
CloseQuery($query) | Ferme le curseur ouvert avec **OpenQuery**

```php
$bd = new MaBD() ;
// Ajout d'une ligne
$ok = $bd->InsertRow("ma_table", array("champ1" => $val1, "champ2" => "val2")) ;
if(! $ok)
{
die("Exception SQL : ".$bd->ConnectionException) ;
}
// Mise à jour de ligne
$ok = $bd->UpdateRow(
	"ma_table",
	array("champ1" => $val1, "champ2" => "val2"),
	"id = :id",
	array("id" => 2),
) ;
if(! $ok)
{
die("Exception SQL : ".$bd->ConnectionException) ;
}
// Suppression de ligne
$ok = $bd->DeleteRow(
	"ma_table",
	"id = :id",
	array("id" => 2),
) ;
if(! $ok)
{
die("Exception SQL : ".$bd->ConnectionException) ;
}
// Exécution d'une requete SQL
$ok = $bd->RunSql(
	"update ma_table set mon_champ1 = :val1 where id = :id",
	array("val1" => "ma valeur N.1", "id" => 2),
) ;
if(! $ok)
{
die("Exception SQL : ".$bd->ConnectionException) ;
}
// Sélection de la 1ere ligne uniquement
$lgn = $bd->FetchSqlRow(
	"select * from ma_table where id = :id",
	array("id" => 2),
) ;
if(! is_array($lgn))
{
die("Exception SQL : ".$bd->ConnectionException) ;
}
echo $lgn["id"] ;
// Sélection de toutes les lignes
$lgns = $bd->FetchSqlRows(
	"select * from ma_table where id = :id",
	array("id" => 2),
) ;
if(! is_array($lgns))
{
die("Exception SQL : ".$bd->ConnectionException) ;
}
foreach($lgns as $i => $lgn)
{
echo $lgn["id"]."<br>" ;
}
// Parcours des lignes, 1 par 1
$query = $bd->OpenQuery(
	"select * from ma_table where id = :id",
	array("id" => 2),
) ;
if($query !== null)
{
while(($lgn = $bd->ReadQuery($query)) !== null)
{
echo "ID : ".$lgn["id"]."<br>" ;
}
$bd->CloseQuery($query) ;
}
```

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

```php
$ok = $bd->RunSql(
	"update ma_table set mon_champ1 = ".$bd->SqlToInt(":val1").", date_modif=".$bd->SqlNow()." where id = :id",
	array("val1" => "ma valeur N.1", "id" => 2),
) ;
if(! $ok)
{
die("Exception SQL : ".$bd->ConnectionException) ;
}
```

## MySQL

### MysqlPdoDB

La classe est **MysqlPdoDB** utilise les classes PDO pour accéder aux bases de données MySQL.

```php
class MaBD extends MysqlPdoDB
{
protected function InitConnectionParams()
{
$this->ConnectionParams['server'] = "localhost" ;
$this->ConnectionParams['schema'] = "gestion_produits" ;
$this->ConnectionParams['user'] = "root" ;
$this->ConnectionParams['password'] = "mysql" ;
}
}

$bd = new MaBD() ;
// 
```

### MysqliDB

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

