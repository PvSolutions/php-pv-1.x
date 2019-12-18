# Le service d'authentification - PHP-PV

## Fonctionnement

Le Membership est le service d’authentification dans la zone.
Il se base sur les modèles relationnels suivants :

- Sans Support LDAP :

![Structure sans LDAP](../images/membership_struct1.png)

- Avec Support LDAP :

![Structure avec LDAP](../images/membership_struct2.png)

Pour l’installer, vous devez créer une base de données et les tables nécessaires.
Vous trouverez les scripts SQL dans le code source :

Fichier | Description
------------- | -------------
install-membership-pv-mysql.sql | Tables de membership pour MySQL
install-membership-pv-mysql-ad.sql | Tables de membership pour MySQL, avec authentification Active Directory
install-membership-pv-orcl.sql | Tables de membership pour Oracle
install-membership-pv-orcl-ad.sql | Tables de membership pour Oracle, avec authentification Active Directory

## Déclaration

Tout service d’authentification doit hériter de la classe **AkSqlMembership**.

```php
// 1. Déclarer la base de données
class MaBD extends MysqlDB
{
}

// 2. Déclarer le Membership
class MonMembership extends AkSqlMembership
{
protected function InitConfig(& $parent)
{
parent::InitConfig($parent) ;
// Affecter la base de données du Membership
$this->Database = new MaBD() ;
}
}

class MaZone1 extends PvZoneWebSimple
{
// ...
// 3. Affecter le membership à la Zone
Public $NomClasseMembership = "MonMembership" ;
}
```

## Propriétés et méthodes principales

Propriété/Méthode | Description
------------- | -------------
$Database | Base de données qui contient les tables de membership. Type accepté : CommonDB
$RootMemberId | ID Membre du super administrateur
$GuestMemberId | ID Membre de l’invité
$MemberTable | Nom de la table des membres dans la base de données
$ProfileTable | Nom de la table des profils dans la base de données
$RoleTable | Nom de la table des rôles dans la base de données
$PrivilegeTable | Nom de la table des privilèges dans la base de données
$SessionSource | Source de la session.
$SessionMemberKey | Clé de la session PHP ($_SESSION) qui contient l’ID du membre connecté
LogonMember($memberId) | Connecte l’ID du Membre dans la session
LogoutMember($memberId) | Déconnecte l’ID du Membre dans la session
ValidateConnection($login, $password) | Vérifie si les accès du membre sont corrects 

## Sources de la session

Les valeurs possibles de la propriété **$SessionSource** sont :
- "SESSION" : variable $_SESSION
- "COOKIE" : variable $_COOKIES

