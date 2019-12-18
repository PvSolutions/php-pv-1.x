# Service d'authentification d'une Zone Web - PHP-PV

## Scripts web Membership

Lorsque vous déclarez un membership dans la zone web, la zone crée automatiquement des scripts.

Nom du script | Classe script web | Pré-requis
------------- | ------------- | -------------
connexion | PvScriptConnexionWeb | Aucun | Page de connexion
deconnexion | PvScriptDeconnexionWeb | Aucun | Page de déconnexion
recouvreMP | PvScriptRecouvreMPWeb | Aucun | Page pour récupérer son mot de passe, à partir du login et du mot de passe
inscription | PvScriptInscriptionWeb | Mettre la propriété $AutoriserInscription à 1 | Page d’inscription d’un membre
modifPrefs | PvScriptModifPrefsWeb | Mettre la propriété $AutoriserModifPrefs à 1 | Page pour modifier les informations du membre (nom, prénom, …)
doitChangerMotPasse | PvScriptDoitChangerMotPasseWeb | Aucun | Page qui force le membre connecté à changer son mot de passe
changeMotPasse | PvScriptChangeMotPasseWeb | Aucun | Page pour modifier le mot de passe
ajoutMembre | PvScriptAjoutMembreMSWeb | Aucun | Ajouter un membre
importMembre | PvScriptImportMembreMSWeb | Aucun | Importe des membres à partir d’un fichier CSV
modifMembre | PvScriptModifMembreMSWeb | Aucun | Modifie un membre
supprMembre | PvScriptSupprMembreMSWeb | Aucun | Désactive le membre
listeMembres | PvScriptListeMembresMSWeb | Aucun | Liste les membres
ajoutProfil | PvScriptAjoutProfilMSWeb | Aucun | Ajoute un profil
modifProfil | PvScriptModifProfilMSWeb | Aucun | Modifie un profil
supprProfil | PvScriptSupprProfilMSWeb | Aucun | Désactive le profil
listeProfils | PvScriptListeProfilsMSWeb | Aucun | Liste les profils
ajoutRole | PvScriptAjoutRoleMSWeb | Aucun | Ajoute un rôle
modifRole | PvScriptModifRoleMSWeb | Aucun | Modifie un rôle
supprRole | PvScriptSupprRoleMSWeb | Aucun | Désactive un rôle
listeRoles | PvScriptListeRolesMSWeb | Aucun | Liste les rôles
ajoutServeurAD | PvScriptAjoutServeurADWeb | Aucun | Ajoute une connexion LDAP
modifServeurAD | PvScriptModifServeurADWeb | Aucun | Modifie une connexion LDAP
supprServeurAD | PvScriptSupprServeurADWeb | Aucun | Supprime une connexion LDAP
listeServeursAD | PvScriptListeServeursADWeb | Aucun | Liste les connexions LDAP

Vous pouvez personnaliser chacun de ces scripts quand vous déclarez la zone web.

```php
class MaZoneWeb1 extends PvZoneWebSimple
{
// Cas du script connexion
public $NomScriptConnexion = "connecter" ; 
public $NomClasseScriptConnexion = "MonScriptConnexion" ;
// ...
}

class MonScriptConnexion extends PvScriptConnexionWeb
{
}
```

## Le remplisseur de config Membership

La zone, pour remplir chaque script de membership, utilise sa propriété **$NomClasseRemplisseurConfigMembership**.
C’est un objet qui hérite de la classe **PvRemplisseurConfigMembership**.

```php
class MaZoneWeb1 extends PvZoneWebSimple
{ 
public $NomClasseRemplisseurConfigMembership = "MonRemplCfgMembership" ;
// ...
}

class MonRemplCfgMembership extends PvRemplisseurConfigMembership
{
}
```

Pour le personnaliser, veuillez créer une classe héritant de celle-ci et réécrivez les méthodes suivantes.

Propriété / Méthode | Description
------------- | -------------
RemplitFormulaireGlobalProfil(& $form) | S’applique à n’importe quel formulaire de profil
RemplitFormulaireGlobalRole(& $form) | S’applique à n’importe quel formulaire de rôle
RemplitFiltresEditionFormMembre(& $form) | Assigne les filtres d’édition à n’importe quel formulaire de membre.
InitFormulaireRole(& $form) | Initialise n’importe quel formulaire de rôle.
InitFormulaireProfil(& $form) | Initialise n’importe quel formulaire de profil.
InitFormulaireMembre(& $form) | Initialise n’importe quel formulaire de membre.
RemplitFiltresMPFormMembre(& $form) | Assigne les filtres sur n’importe quel formulaire de mot de passe
RemplitFormulaireGlobalMembre(& $form) | Définit les caractéristiques de n’importe quel formulaire de profil
RemplitFormulaireInfosMembre(& $form) | Définit les caractéristiques de n’importe quel formulaire de membre
RemplitFormulaireChangeMPMembre(& $form) | Définit les caractéristiques de n’importe quel formulaire de mot de passe
InitTableauMembre(& $table) | Initialise le tableau de données des membres
InitTableauProfil(& $table) | Initialise le tableau de données des profils
InitTableauRole(& $table) | Initialise le tableau de données des rôles
RemplitFiltresTableauMembre(& $table) | Assigne les filtres du tableau de données des membres
RemplitDefinitionsColonneTableauMembre(& $table) | Assigne les colonnes de tableau de données des membres
RemplitDefinitionColActionsTableauMembre(& $table) | Assigne les actions du tableau de données des membres
RemplitFiltresTableauRole(& $table) | Assigne les filtres du tableau de données des rôles
RemplitDefinitionsColonneTableauRole(& $table) | Assigne les colonnes de tableau de données des rôles
RemplitDefinitionColActionsTableauRole(& $table) | Assigne les actions du tableau de données des rôles
RemplitFiltresTableauProfil(& $table) | Assigne les filtres du tableau de données des profils
RemplitDefinitionsColonneTableauProfil(& $table) | Assigne les colonnes de tableau de données des profils
RemplitDefinitionColActionsTableauProfil(& $table) | Assigne les actions du tableau de données des profils


