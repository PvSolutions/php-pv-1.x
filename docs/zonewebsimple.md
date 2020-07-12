# Zone Web Simple

## Présentation

La zone web simple représente un ensemble de pages web. L'utilisateur pourra y accéder s'il possède les droits adéquats.

## Entêtes document

### Propriétés HTML

La zone web possède des propriétés pour le rendu HTML.

Propriété | Rôle | Contenu HTML généré
------------- | ------------- | -------------
EncodageDocument | Fixe l'encodage de la page web. Par défaut : "utf-8" | &lt;meta charset="$valeur" /&gt;
MotsCleMeta | Mots clé META | &lt;meta name="keywords" value="$valeur" /&gt;
DescriptionMeta | Description META | &lt;meta name="description" value="$valeur" /&gt;
LangueDocument | Langage du document | &lt;html lang="$valeur"&gt;
TitreDocument | Titre du document | &lt;title&gt;$valeur&lt;/title&gt;
ViewportMeta | Viewport Meta | &lt;meta name="viewport" content="$valeur" /&gt;
UrlBase | Lien de Base | &lt;base href="$valeur" /&gt;

Exemple :
```php
<?php
	include dirname(__FILE__)."/php-pv-master/Pv/Base.class.php" ;
	
	class MonApplication1 extends PvApplication
	{
		public $ZonePrinc ;
		protected function ChargeIHMs()
		{
			// Inscrire la zone web de l'Application
			$this->ZonePrinc = $this->InsereIHM("zonePrinc", new ZoneWebApplication1()) ;
		}
	}
	// Déclaration de la zone web
	class ZoneWebApplication1 extends PvZoneWebSimple
	{
		// Afficher la zone web en fonction du chemin dans le navigateur
		public $AccepterTousChemins = 1 ;
		public $ScriptAccueil ;
		public $EncodageDocument = 'utf-8' ;
		public $MotsCleMeta = 'Attributs, Zone, Web Simple' ;
		public $DescriptionMeta = 'Description d\'une Zone Web Simple' ;
		protected function ChargeScripts()
		{
			// Inscrire le script web par défaut
			$this->ScriptAccueil = $this->InsereScriptParDefaut(new ScriptAccueilApplication1()) ;
		}
	}
	// Déclaration du script web par défaut.
	class ScriptAccueilApplication1 extends PvScriptWebSimple
	{
		// Code HTML qui sera affiché dans le navigateur
		public function RenduSpecifique()
		{
			$ctn = '' ;
			$ctn = "BIENVENUE SUR MA APPLICATION 1" ;
			return $ctn ;
		}
	}
	
	$app = new MonApplication1() ;
	$app->Execute() ;

?>
```

## CSS et Javascript

### Bibliothèques CSS et Javascript

La zone web simple inclut automatiquement les scripts & styles CSS des librairies Javascript populaires.

#### JQuery

Propriété | Spécification
------------- | -------------
$InclureJQuery | Mettre à 1 pour inclure la librairie jquery
$CheminJQuery | Chemin relatif du fichier Js jQuery. Par défaut : "js/jquery.min.js" 
$InclureJQueryMigrate | Mettre à 1 pour inclure la librairie jquery-migrate 1.x
$CheminJQueryMigrate | Chemin relatif du fichier Js JQueryMigrate. Par défaut : "js/jquery-migrate.min.js"
$InclureJQueryMigrate3 | Mettre à 1 pour inclure la librairie jquery-migrate 3.x
$CheminJQueryMigrate3 | Chemin relatif du fichier Js JQueryMigrate. Par défaut : "js/jquery-migrate.min.js"

```php
class MaZone1 extends PvZoneWebSimple
{
public $InclureJQuery = 1 ;
public $CheminJQuery = "vendor/jquery/jquery.min.js" ;
}
```

#### JQuery UI

Propriété | Spécification
------------- | -------------
$InclureJQueryUi | Mettre à 1 pour inclure la librairie jqueryui
$CheminJsJQueryUi | Chemin relatif du fichier Js JQuery Ui. Par défaut : "js/jquery-ui.min.js"
$CheminCSSJQueryUi | Chemin relatif du fichier CSS jQuery Ui. Par défaut : "css/jquery-ui.css"

```php
class MaZone1 extends PvZoneWebSimple
{
public $InclureJQueryUi = 1 ;
public $CheminJsJQueryUi = "vendor/jquery-ui/jquery-ui.min.js" ;
public $CheminCSSJQueryUi = "vendor/jquery-ui/jquery-ui.css" ;
}
```

#### Bootstrap

Propriété | Spécification
------------- | -------------
$InclureBootstrap | Mettre à 1 pour inclure la librairie bootstrap
$CheminJsBootstrap | Chemin relatif du fichier Js Bootstrap. Par défaut : "js/bootstrap.min.js"
$CheminCSSBootstrap | Chemin relatif du fichier CSS Bootstrap. Par défaut : "css/bootstrap.css"
$InclureBootstrapTheme | Mettre à 1 pour inclure un thème personnalisé Bootstrap
$CheminCSSBootstrapTheme | Chemin relatif du fichier CSS Bootstrap. Par défaut : "css/bootstrap-theme.min.css"

```php
class MaZone1 extends PvZoneWebSimple
{
public $InclureBootstrap = 1 ;
public $CheminJsBootstrap = "vendor/bootstrap/bootstrap.min.js" ;
public $CheminCSSJQueryUi = "vendor/bootstrap/bootstrap.min.css" ;
}
```

#### Font Awesome

Propriété | Spécification
------------- | -------------
$InclureFontAwesome | Mettre à 1 pour inclure Font Awesome
$CheminFontAwesome | Chemin relatif du fichier CSS Font Awesome. Par défaut : "css/font-awesome.css"

```php
class MaZone1 extends PvZoneWebSimple
{
public $InclureFontAwesome = 1 ;
public $CheminFontAwesome = "vendor/fontawesome/css/all.min.css" ;
}
```

### Contenus CSS et Javascript

La zone a également des méthodes pour insérer du contenu CSS et JS.

Méthode | Description
------------- | -------------
InscritContenuCSS ($contenu) | Insère un tag &lt;style&gt; avec le $contenu
InscritLienCSS ($href) | Insère un tag &lt;link rel="stylesheet" type="text/css" href="$href" /&gt;
InscritContenuJs ($contenu) | Insère un tag &lt;script&gt; avec le $contenu
InscritContenuJsCmpIE ($contenu, $versionMin=9) | Insère un tag &lt;script&gt; avec le $contenu, avec les directives IE
InscritLienJs ($src) | Insère un tag &lt;script&gt; avec la source $src
InscritLienJsCmpIE ($src, $versionMin=9) | Insère un tag &lt;script&gt; avec la source $src, avec les directives IE

Veuillez réécrire la méthode **InclutLibrairiesExternes()**, en invoquant la méthode parente.

```php
class MaZone1 extends PvZoneWebSimple
{
Protected function InclutLibrairiesExternes()
{
Parent::InclutLibrairiesExternes() ;
// Inscrire les autres librairies JS & CSS…
$this->InscritContenuCSS("body { text-align:center ; }") ;
$this->InscritLienJs("js/main.js") ;
} 
}
```

## Service d'authentification

### Filtrage des scripts

La méthode **ChargeScripts()** s'exécute avant que la zone web charge le membre connecté. Par conséquent, vous ne pouvez pas filtrer les scripts à ce stade.

```php
public function ChargeScripts()
{
$this->InsereScriptParDefaut(new MonScript1()) ;
$this->InsereScript("presentation", new MonScript2()) ;
if($this->PossedeMembreConnecte()) // Ramènera false même s'il y a un membre connecté
{
$this->InsereScript("contact", new MonScript2()) ;
}
}
```

Pour y parvenir, déclarez les scripts dans la méthode **ChargeScriptsMembership()**.

```php
public function ChargeScriptsMembership()
{
$this->InsereScriptParDefaut(new MonScript1()) ;
$this->InsereScript("presentation", new MonScript2()) ;
if($this->PossedeMembreConnecte())
{
$this->InsereScript("chat", new MonScript2()) ; // Script créé si un membre est connecté
}
else
{
$this->InsereScript("contact", new MonScript3()) ; // Script créé si un membre n'est pas connecté
}
}
```

### Scripts web Membership

Lorsque vous déclarez un membership dans la zone web, la zone crée automatiquement des scripts.

Nom du script | Classe script web | Pré-requis | Description
------------- | ------------- | ------------- | -------------
connexion | PvScriptConnexionWeb | Aucun | Page de connexion
deconnexion | PvScriptDeconnexionWeb | Aucun | Page de déconnexion
recouvreMP | PvScriptRecouvreMPWeb | Aucun | Page pour récupérer son mot de passe, à partir du login et du mot de passe
inscription | PvScriptInscriptionWeb | Mettre la propriété $AutoriserInscription à 1 | Page d'inscription d'un membre
modifPrefs | PvScriptModifPrefsWeb | Mettre la propriété $AutoriserModifPrefs à 1 | Page pour modifier les informations du membre (nom, prénom, …)
doitChangerMotPasse | PvScriptDoitChangerMotPasseWeb | Aucun | Page qui force le membre connecté à changer son mot de passe
changeMotPasse | PvScriptChangeMotPasseWeb | Aucun | Page pour modifier le mot de passe
ajoutMembre | PvScriptAjoutMembreMSWeb | Aucun | Ajouter un membre
importMembre | PvScriptImportMembreMSWeb | Aucun | Importe des membres à partir d'un fichier CSV
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
### Composants web Membership

Plusieurs scripts de membership utilisent un tableau de données ou un formulaire de données spécifique.

Nom du script | Classe script web | Classe composant | Propriété composant
------------- | ------------- | ------------- | -------------
recouvreMP | PvScriptRecouvreMPWeb | PvFormulaireRecouvreMPMS | $NomClasseFormulaireDonnees
inscription | PvScriptInscriptionWeb | PvFormulaireInscriptionMembreMS | $NomClasseFormulaireDonnees
modifPrefs | PvScriptModifPrefsWeb | PvFormulaireModifInfosMS | $NomClasseFormulaireDonnees
doitChangerMotPasse | PvScriptDoitChangerMotPasseWeb | PvFormulaireDoitChangerMotPasseMS | $NomClasseFormulaireDonnees
changeMotPasse | PvScriptChangeMotPasseWeb | PvFormulaireChangeMotPasseMS | $NomClasseFormulaireDonnees
ajoutMembre | PvScriptAjoutMembreMSWeb | PvFormulaireAjoutMembreMS | $NomClasseFormulaireDonnees
modifMembre | PvScriptModifMembreMSWeb | PvFormulaireModifMembreMS | $NomClasseFormulaireDonnees
supprMembre | PvScriptSupprMembreMSWeb | PvFormulaireSupprMembreMS | $NomClasseFormulaireDonnees
listeMembres | PvScriptListeMembresMSWeb | PvTableauMembresMSHtml | $NomClasseTableauDonnees
ajoutProfil | PvScriptAjoutProfilMSWeb | PvFormulaireAjoutProfilMS | $NomClasseFormulaireDonnees
modifProfil | PvScriptModifProfilMSWeb | PvFormulaireModifProfilMS | $NomClasseFormulaireDonnees
supprProfil | PvScriptSupprProfilMSWeb | PvFormulaireSupprProfilMS | $NomClasseFormulaireDonnees
listeProfils | PvScriptListeProfilsMSWeb | PvTableauProfilsMSHtml | $NomClasseTableauDonnees
ajoutRole | PvScriptAjoutRoleMSWeb | PvFormulaireAjoutRoleMS | $NomClasseFormulaireDonnees
modifRole | PvScriptModifRoleMSWeb | PvFormulaireModifRoleMS | $NomClasseFormulaireDonnees
supprRole | PvScriptSupprRoleMSWeb | PvFormulaireSupprRoleMS | $NomClasseFormulaireDonnees
listeRoles | PvScriptListeRolesMSWeb | PvTableauAjoutRoleMS | $NomClasseTableauDonnees

```php
class MaZoneWeb1 extends PvZoneWebSimple
{
public $AutoriserModifPrefs = 1 ; 
public $NomClasseScriptConnexion = "MonScriptModifPrefs" ;
// ...
}
class FormModifPrefs1 extends PvFormulaireModifInfosMS
{
}
class MonScriptModifPrefs extends PvScriptModifPrefsWeb
{
public $NomClasseFormulaireDonnees = "FormModifPrefs1" ;
}
```

### Le remplisseur de config Membership

La zone, pour remplir chaque script de membership, utilise sa propriété **$NomClasseRemplisseurConfigMembership**.
C'est un objet qui hérite de la classe **PvRemplisseurConfigMembership**.

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
RemplitFormulaireGlobalProfil(& $form) | S'applique à n'importe quel formulaire de profil
RemplitFormulaireGlobalRole(& $form) | S'applique à n'importe quel formulaire de rôle
RemplitFiltresEditionFormMembre(& $form) | Assigne les filtres d'édition à n'importe quel formulaire de membre.
InitFormulaireRole(& $form) | Initialise n'importe quel formulaire de rôle.
InitFormulaireProfil(& $form) | Initialise n'importe quel formulaire de profil.
InitFormulaireMembre(& $form) | Initialise n'importe quel formulaire de membre.
RemplitFiltresMPFormMembre(& $form) | Assigne les filtres sur n'importe quel formulaire de mot de passe
RemplitFormulaireGlobalMembre(& $form) | Définit les caractéristiques de n'importe quel formulaire de profil
RemplitFormulaireInfosMembre(& $form) | Définit les caractéristiques de n'importe quel formulaire de membre
RemplitFormulaireChangeMPMembre(& $form) | Définit les caractéristiques de n'importe quel formulaire de mot de passe
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

## Scripts Web

La zone web contient des scripts, qui renvoient un contenu spécifique en fonction d'un paramètre GET (appelleScript par défaut).

Les scripts varient le contenu d'une zone, tout en gardant les mêmes entêtes et pieds de document HTML.

### Utilisation

Vous devez déclarer chaque script, et réécrire sa méthode de rendu.

Insérez les dans la zone, à partir de la méthode **ChargeScripts**.

```php
// 
class MaZoneWeb1 extends PvZoneWebSimple
{
// Inscription des scripts
public function ChargeScripts()
{
// Inscrire la page d'accueil
$this->InsereScriptParDefaut(new MonScriptAccueil()) ;
// Inscrire la page d'a propos
$this->InsereScript("a_propos", new MonScriptAPropos()) ;
// Inscrire la page de contact
$this->InsereScript("contact", new MonScriptContact()) ;
}
// ...
}
// Déclaration du script d'accueil
class MonScriptAccueil extends PvScriptWebSimple
{
public function RenduSpecifique()
{
return "<p>Page d'accueil</p>" ;
}
}
// Déclaration du script a propos
class MonScriptAPropos extends PvScriptWebSimple
{
public function RenduSpecifique()
{
return "<p>A propos de mon site</p>" ;
}
}
// Déclaration du script de contact
class MonScriptContact extends PvScriptWebSimple
{
public function RenduSpecifique()
{
return "<p>Ma page de contact</p>" ;
}
}
```

### Propriétés et Méthodes principales

Membre | Description
------------- | -------------
$IDInstanceCalc | ID Unique du script parmi les objets créés
$ZoneParent | Accède à la zone web contenant le script
$NomElementZone | Nom du script dans la zone
$ApplicationParent | Accède à l'application contenant le script
$TitreDocument | Titre du document HTML sur le navigateur
$MotsCleMeta | Mots clés méta HTML
$DescriptionMeta | Description méta HTML
$ViewportMeta | Viewport méta HTML
$Titre | Titre du script, utilisé dans le corps du document HTML

```php
// Déclaration du script
class MonScriptAPropos extends PvScriptWebSimple
{
public $TitreDocument = "A propos de mon site" ;
public $Titre = "A propos" ;
public $MotsCleMeta = "A propos, informations, relativement, concernant" ;
public $DescriptionMeta = "Trouvez sur cette page des informations sur mon site" ;
}
```

### Restrictions membership

Vous devez initialiser ces propriétés dans la déclaration du script.

Membre | Type | Description
------------- | ------------- | -------------
$NecessiteMembreConnecte | bool | Indique si le membre doit se connecter pour accéder au script
$Privileges | array | Indique au moins un rôle que le membre doit posséder pour accéder au script
$PrivilegesStricts | bool | Refuse l'accès au script si le membre ne posséde pas un des privilièges. Les super administrateurs n'accéderont pas s'ils n'ont pas un des rôles nécéssaires.
$AnnulDetectMemberCnx | bool | Annule la détection du membre connecté

```php
// Déclaration du script
class MonScriptModifArticle extends PvScriptWebSimple
{
public $NecessiteMembreConnecte = 1 ;
public $Privileges = array("edit_article") ;
}
```

Si vous avez besoin de faire plus de contrôle, réecrivez leur méthode **EstAccessible()**.

```php
// Déclaration du script
class MonScriptDetailArticle extends PvScriptWebSimple
{
public function EstAccessible()
{
$ok = parent::EstAccessible() ;
// S'arrêter si l'accès est déjà refusé
if(! $ok)
{
return false ;
}
// Procéder à nos contrôles :
// S'assurer que le script contient un paramètre "id"
// supérieur à 0
return (isset($_GET["id"]) && intval($_GET["id"]) > 0) ;
}
public function RenduSpecifique()
{
$ctn = '' ;
$ctn .= "<p>Article N&deg; '.intval($_GET["id"]).'</p>" ;
return $ctn ;
}
}
```

### Environnement du script

Le script possède la méthode **DetermineEnvironnement**(), pour définir les variables nécessaires au rendu.

```php
// Déclaration du script
class MonScriptDetailArticle extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
$bd = new MaBD() ;
$this->ParamId = intval($_GET["id"]) ;
$this->LgnPrinc = $bd->FetchSqlRow('select * from article where id=:id', array("id" => $this->ParamId)) ;
if(count($this->LgnPrinc) > 0)
{
// Définir les entêtes HTML à partir des variables environnement
$this->TitreDocument = "Article ".htmlentities($this->LgnPrinc["titre"]) ;
$this->Titre = "Infos article ".htmlentities($this->LgnPrinc["titre"]) ;
}
// Insérer du CSS uniquement sur ce script
$this->ZoneParent->InsereContenuCSS("h1 { color: red ; }") ;
// Insérer du Javascript uniquement sur ce script
$this->ZoneParent->InsereContenuJs("function test1() { alert("OK") ; }") ;
}
public function RenduSpecifique()
{
$ctn = '' ;
$ctn .= "<p>ID : ".$this->LgnPrinc["id"]."</p>" ;
$ctn .= "<p>Titre : ".htmlentities($this->LgnPrinc["titre"])."</p>" ;
$ctn .= "<p>PU : ".$this->LgnPrinc["PU"]." Eur.</p>" ;
return $ctn ;
}
}
```

### Gestion du rendu

Pour le rendu, voici les membres principaux de la classe script :

Membre | Type | Description
------------- | ------------- | -------------
$InclureRenduTitre | bool | Confirme ou Annule le rendu du titre pour le script en cours
$InclureRenduDescription | bool | Confirme ou Annule le rendu du titre pour le script en cours

```php
// Déclaration du script
class MonScriptDetailArticle extends PvScriptWebSimple
{
public $InclureRenduTitre = false ;
public function RenduSpecifique()
{
$ctn = '' ;
$ctn .= "Ce script sera affiché sans titre" ;
return $ctn ;
}
}
```
Vous pouvez réecrire certaines méthodes pour personnaliser le rendu :

Membre | Type | Description
------------- | ------------- | -------------
RenduChemin() | string | Contenu HTML de l'arborescence du script
RenduTitre() | string | Contenu HTML spécifique du script
RenduDescription() | string | Contenu HTML spécifique du script
RenduSpecifique() | string | Contenu HTML spécifique du script
RenduDispositifBrut() | string | Contenu HTML brut du script. Par défaut, cette méthode appelle les méthodes RenduChemin(), RenduTitre(), RenduDescription(), RenduSpecifique().

```php
// Déclaration du script
class MonScript1 extends PvScriptWebSimple
{
public function RenduTitre()
{
$ctn = '' ;
$ctn .= "<h1>Ma page web</h1>" ;
return $ctn ;
}
public function RenduSpecifique()
{
$ctn = '' ;
$ctn .= "Ce script sera affiché avec un titre très grand !" ;
return $ctn ;
}
}
```

### Impression

Pour rendre une page imprimable, déclarez sa propriété *$Imprimable* à 1.

```php
class MonScript1 extends PvScriptWebSimple
{
public $Imprimable = 1 ;
}
```

Lors du rendu, le lien d'impression est disponible dans l'action web **$ActionImprime**.

Utilisez également la méthode **ImpressionEnCours()** pour masquer les contenus à l'impression.

```php
class MonScript1 extends PvScriptWebSimple
{
public $Imprimable = 1 ;
public function RenduSpecifique()
{
$ctn = '' ;
$ctn .= '<p>Voici un script imprimable</p>' ;
// Afficher le bouton si la page n'est pas en mode impression
if(! $this->ImpressionEnCours())
{
$ctn .= '<p><a href="'.$this->ActionImprime->ObtientUrl().'">Imprimer</a></p>' ;
}
return $ctn ;
}
}
```

Personnalisez ainsi les styles d'impression dans **DetermineEnvironnement()**.

```php
class MonScript1 extends PvScriptWebSimple
{
public $Imprimable = 1 ;
public function DetermineEnvironnement()
{
// Mettre une taille de police 12px pendant l'impression
if($this->ImpressionEnCours())
{
$this->ZoneParent->InsereContenuCSS("body {
font-size:12px ;
}") ;
}
}
}
```

## Documents Web

Un document web personnalise l'affichage complet de chaque script.
Dans la zone, il est utile :
- pour les scripts à imprimer
- pour les scripts qui s'afficheront dans une boîte de dialogue

### Déclaration

Chaque document web hérite de la classe **PvDocumentWebHtml**.

Veuillez réecrire les méthodes **PrepareRendu(& $zone)**, **RenduEntete(& $zone)** et **RenduPied(& $zone)**.
Vous pouvez manipuler le script sélectionné avec **$zone->ScriptPourRendu**

```php
class MonDocumentWeb1 extends PvDocumentWebHtml
{
public function PrepareRendu(& $zone)
{
// Garder la préparation du document web html
parent::PrepareRendu($zone) ;
// Inclure des librairies CSS du document
$zone->InsereLienCSS("css/style.css") ;
// Inclure des librairies Js du document
$zone->InsereLienJs("js/main.js") ;
}
public function RenduEntete(& $zone)
{
$ctn = '' ;
// Retourne le contenu HTML jusqu'au tag body
$ctn .= parent::RenduEntete($zone) ;
return $ctn ;
} 
public function RenduPied(& $zone)
{
$ctn = '' ;
// Retourne le contenu HTML à partir de la fin du tag body
$ctn .= parent::RenduPied($zone) ;
return $ctn ;
} 
}
```

### Intégration dans la zone web

D'abord, vous devez mettre la propriété **UtiliserDocumentWeb** à 1.
Ensuite, déclarez chaque document dans la méthode **ChargeConfig()** de la zone web.

```php
class MaZoneWeb extends PvZoneWebSimple
{
public $UtiliserDocumentWeb = 1 ;
public function ChargeConfig()
{
Parent::ChargeConfig() ;
// Le 1er document web créé est utilisé pour
// tous les scripts
$this->DocumentsWeb["defaut"] = new MonDocumentWeb1() ;
$this->DocumentsWeb["impression"] = new MonDocumentWeb2() ;
}
}
```

Le 1er document web déclaré sera utilisé par défaut pour tous les scripts. Dans le cas ci-dessus, c'est le document web "defaut".

### Affectation à un script

Pour définir le document web du script, renseignez la propriété **NomDocumentWeb** du script.

```php
class MonScriptWeb3 extends PvScriptWebSimple
{
// …
public $NomDocumentWeb = "impression" ;
// …
}
```

## Actions Web

### Définition

Une Action Web est un ensemble d'instructions s'exécute dans la Zone Web. Elle ne se limite pas d'afficher un contenu HTML, comme les scripts web.
Elle peut également :
- déclencher le téléchargement d'un fichier
- renvoyer un fichier RSS, JS ou CSS
- renvoyer une réponse JSON
- exécuter un code précis, avant d'afficher le script web

### Déclaration

La zone web exécute une action web à partir du paramètre GET **appelleAction**.

Vous pouvez déclarer les actions dans plusieurs méthodes :

Objet | Méthode	Contexte | Description
------------- | ------------- | -------------
Zone web | InsereActionPrinc($nom, $action) | Utiliser dans la méthode **ChargeConfig()** | Les actions principales s'exécutent avant d'exécuter le script en cours
Zone web | InsereActionAvantRendu($nom, $action) | Utiliser dans la méthode **ChargeConfig()**. | S'exécutent avant d'afficher le script en cours
Script web | InsereActionAvantRendu($nom, $action) | Utiliser dans la méthode **DetermineEnvironnement()** | Déclare l'action uniquement lorsque le script doit être affiché. Le nom de l'action sera basé sur l'ID Instance du script et le nom de l'action.

### Types d'action

Classe | Description | Utilisation
------------- | ------------- | -------------
PvActionBaseZoneWebSimple | Classe de base | Réécrire la méthode **Execute()**
PvActionNotificationWeb | Exécute des instructions et garde le résultat (succès/echec et message d'exécution) | Réécrire la méthode **Execute()**
PvActionResultatJSONZoneWeb / PvActionEnvoiJSON | Affiche un contenu JSON dans le navigateur | Réécrire la méthode **Execute()**. A l'intérieur, définissez la propriété Resultat. Cette propriété sera le retour JSON.
PvActionTelechargFichier | Démarre le téléchargement du fichier | Réécrire la méthode **Execute()**

#### Utilisation PvActionNotificationWeb

Vous devez réécrire la méthode **Execute()**. A l'intérieur, utiliser ces méthodes pour définir le résultat :
- **ConfirmeSucces($msg)**
- **RenseigneErreur($msg)**
Dans le script ou la zone, utilisez la propriété **TypeErreur** et méthode **ObtientMessage()** de l'instance Action pour afficher le résultat. Pour tester si l'action a ramené un résultat, utilisez la méthode **PossedeMessage()**

#### Utilisation PvActionTelechargFichier

- Renseignez la propriété **NomFichierAttache** dans la fonction **DetermineFichierAttache** pour définir le nom du fichier téléchargé.
- Réécrivez la méthode **AfficheContenu** pour envoyez le contenu du fichier. A l'intérieur, utilisez les fonctions PHP **echo**.
- Si le fichier existe déjà, utilisez **CheminFichierSource** pour le charger.
- Si vous voulez renseigner des entêtes spécifiques, réécrivez la méthode **AfficheEntetes**

## Les filtres de données http

### Présentation

Ils sont surtout utilisés dans les formulaires et les tableaux de données.
Ils vous proposent des champs de saisie, qui seront soumis après validation.

### Propriétés et Méthodes principales

Propriété / Méthode | Description
------------- | -------------
$Libelle | Libellé
$EstEtiquette | Si la valeur est 1, le filtre affichera la valeur au lieu du champ de saisie.
$ValeurVide | Valeur NULLE du filtre.
$ValeurParDefaut | Valeur par défaut
$NePasLierParametre | Renvoie toujours la valeur par défaut du filtre.
$NomParametreLie | Nom du paramètre soumis par http
$NePasLireColonne | Ne change pas la valeur de la colonne liée au filtre. Utilisée dans les formulaires de données.
$AliasParametreDonnees | Expression de la colonne de données. Ex. TO_CHAR(&lt;self&gt;)
$ExpressionDonnees | Condition SQL lorsque le filtre est utilisé dans une recherche. Ex : MON_CHAMP = &lt;self&gt;
$NomColonneLiee | Nom de la colonne dans la table, pour un filtre d'édition
$ExpressionColonneLiee | Expression de la colonne dans la table, pour un filtre d'édition. Ex. PASSWORD(&lt;self&gt;)
$LectureSeule | Passer la valeur par défaut du filtre de données, et la soumettre dans le formulaire.
$Invisible | Le filtre ne sera pas affiché sur la page. Il renvoie toujours sa valeur par défaut
$NePasIntegrerParametre | Empêche le formulaire de données d'utiliser ce filtre pour la recherche.
Lie() | Définit la valeur soumise à partir du formulaire. Elle est utilisée après clic sur une commande de formulaire donnée ou le bouton « Rechercher » du tableau de données
$DejaLie | Signale si le filtre a été lié auparavant.
$ValeurParametre | Valeur liée. Utilisez plutôt la méthode Lie().
$Role | Type du filtre de données.
$TypeLiaisonParametre | Contient la valeur "get", valeur issue de $_GET ou "post", valeur issue de $_POST

### Correcteur de valeur

C'est une propriété qui encode/décode la valeur brute d'un filtre.
Vous devez étendre la classe **PvCorrecteurValeurFiltreBase** et réécrire les méthodes clées.

```php
class MonCorrectValFiltre1 extends PvCorrecteurValeurFiltreBase
{
public function Applique($valeur, & $filtre)
{
return htmlentities($valeur) ;
}
}

class MonScript1 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
// ...
$form = new PvFormulaireDonneesHtml() ;
// ...
$flt1 = $form->InsereFltEditHttpPost("flt1", "") ;
$flt1->CorrecteurValeur = new MonCorrectValFiltre1() ;
}
}
```

Il existe des correcteurs de valeurs déjà déclarés.

Classe | Description
------------- | -------------
PvCorrecteurValeurFiltreBase | Correcteur de valeur par défaut
PvCorrecteurValeurSansAccent | Enlève tous les caractères spéciaux.

### Composant de filtre

#### Présentation

Le composant de filtre de données est le champ de saisie. Vous le définissez ainsi :

Méthode | Description
------------- | -------------
DeclareComposant($nomClasseComposant) | Définit le composant à partir du nom de la classe
RemplaceComposant($composant) | Définit le composant à partir de l'instance

Exemple :
```php
$flt1 = $form->InsereFltEditHttpPost("monchamp") ;
// Le composant est dans la variable $comp1
$comp1 = $flt1->DeclareComposant("PvZoneMultiligneHtml") ;
```

#### Composants Eléments HTML

Classe | Description
------------- | -------------
PvZoneTexteHtml | Composant par défaut affectée au filtre. Affiche un champ INPUT
PvZoneMultiligneHtml | Affiche un champ TEXTAREA
PvZoneMotPasseHtml | Affiche un champ PASSWORD
PvZoneEtiquetteHtml | Affiche un champ en lecture seule.

#### Composants de liste

Les composants de liste utilisent un fournisseur de données pour leur rendu.

```php
$comp1 = $flt1->DeclareComposant("PvZoneSelectHtml") ;
// Définition du fournisseur de données
$comp1->FournisseurDonnees = new PvFournisseurDonneesSql() ;
$comp1->FournisseurDonnees->BaseDonnees = new MaBD1() ;
$comp1->FournisseurDonnees->RequeteSelection = "matable1" ;
// Définition des valeur
$comp1->NomColonneValeur = "id" ;
$comp1->NomColonneLibelle = "monchamp1" ;
// Afficher une valeur par defaut s'il n'y a aucune valeur
$comp1->InclureElementHorsLigne = 1 ;
$comp1->ValeurElementHorsLigne = -1 ; 
$comp1->LibelleElementHorsLigne = " – Aucun --" ;
```

Classe | Description
------------- | -------------
PvZoneBoiteSelectHtml | Affiche une zone SELECT
PvZoneBoiteOptionsRadioHtml | Affiche une zone de plusieurs options RADIO à cocher.
PvZoneBoiteOptionsCocherHtml | Affiche une zone de plusieurs options CHECKBOX à cocher. Pour récupérer toutes les valeurs cochées, utilisez la propriété $ValeurBrute du filtre.
PvZoneCadreOptionsRadioHtml | Affiche une zone de plusieurs options RADIO à cocher, qui sont dans un IFRAME HTML

### Formatage de libellé
Si le filtre de données est en étiquette, son champ de saisie ne sera pas éditable.
Pour personnaliser ce rendu, utilisez la méthode **DefinitFmtLbl**. Etendez la classe **PvFmtLblBase** et réécrivez sa méthode **Rendu($valeur, & $composant)**.

```php
class MonFmtLbl1 extends PvFmtLblBase
{
public function Rendu($valeur, & $composant)
{
return base64_decode($valeur) ;
}
}
```

Ensuite, affectez ce format au composant avec la méthode **DefinitFmtLbl()** du filtre. Vous devez déclarer le composant avant d'utiliser cette méthode.

```php
$comp = $flt1->DeclareComposant("PvZoneTexteHtml") ;
// …
$flt1->DefinitFmtLbl(new MonFmtLbl1()) ;
```

Voici des formats déjà définis :

Classe | Description
------------- | -------------
PvFmtLblBase | Classe de base.
PvFmtLblWeb | Classe affectée par défaut
PvFmtLblDateFr | Affiche au format date français
PvFmtLblDateTimeFr | Affiche au format date et heure français
PvFmtMonnaie | Affiche au format monétaire

### Le filtre de données Upload

Le filtre de données Upload télécharge un fichier.

#### Propriétés / Méthodes principales

Propriété / Méthodes | Description
------------- | -------------
$NettoyerCaractsFichier | Enlève les caractères spéciaux du nom fichier téléchargé.
$ExtensionsAcceptees | Tableau contenant les extensions uniquement acceptées. Si le fichier soumis n'a pas une extension, il ne sera pas copié dans le répertoire 
$ExtensionsRejetees | Tableau contenant les extensions à rejeter systématiquement.
$FormatFichierTelech | Format du nom de fichier téléchargé. 
$SourceTelechargement | Contient les valeurs "post" si aucun fichier n'est soumis ou "files" si un fichier a été soumis.
$InfosTelechargement | Contient les détails du fichier téléchargé.
$ToujoursRenseignerFichier | Renvoie une erreur dans le formulaire de données, si aucun fichier n'est soumis.

#### Variables Format de fichier téléchargé

Les variables disponibles sont :
- **Cle** : Identifiant Unique
- **NombreAleatoire** : Nombre compris entre 1 et 10000
- **NomFichier** : Nom d'origine du fichier
- **Timestamp** : Timestamp actuel
- **Date** : Date au format YmdHis

Ex : "Bon-Commande-${Cle}"

#### Caractéristiques du Composant

Le composant par défaut de ce filtre est le composant **PvZoneUploadHtml**.
Ses propriétés principales sont :

Propriété | Description
------------- | -------------
$InclureErreurTelecharg | Afficher l'erreur survenue lors du téléchargement
$InclureCheminCoteServeur | Afficher le chemin relatif du fichier téléchargé
$InclureZoneSelectFichier | Afficher les informations sur le fichier téléchargé
$CheminCoteServeurEditable | Autoriser la modification du chemin relatif sur le serveur
$InclureApercu | Définit l'affichage de l'aperçu.
$LargeurCadreApercu | Largeur HTML du cadre d'aperçu
$HauteurCadreApercu | Hauteur HTML du cadre d'aperçu.

#### Valeurs Inclure Aperçu

Valeurs possibles :
- 0 : Ne pas autoriser d'aperçu
- 1 : Affiche un lien pour afficher dans le navigateur
- 2 : Afficher le fichier dans un cadre, si c'est possible

## Tâches Web - PHP-PV

### Définition

Une tâche web est une tâche planifiée, qui exécute des instructions.
La tâche démarre automatiquement quand vous affichez n'importe script de la zone web, une fois son délai d'attente dépassé.
Elle s'exécute dans un autre processus http que celui du script.

### Déclaration

Veuillez créer votre tâche à partir de la classe **PvTacheWebBaseSimple**. Définissez la propriété « **DelaiExecution** » (en heure) et réécrivez la méthode **ExecuteInstructions()**.

```php
class MaTacheWeb1 extends PvTacheWebBaseSimple
{
public $DelaiExecution = 0.05 ; // S'exécute après 180 secondes
protected function ExecuteInstructions()
{
Echo "OK, ma tache est executee" ;
}
}
```

Les membres utiles dans la méthode **ExecuteInstructions()** sont :
- **ApplicationParent** : Renvoie l'Application
- **ZoneParent** : Renvoie la zone contenant la tâche web

### Le gestionnaire de tâches web

La zone web possède un gestionnaire de tâches web, dont les rôles sont :
-	Contenir les tâches web
-	Définir l'emplacement de sauvegardes des états de chaque tâche (en cours, terminé, date d'exécution, …)

Pour personnaliser le gestionnaire de tâches web, veuillez réécrire la méthode **ChargeGestTachesWeb()**.

La propriété **GestTachesWeb** représente le gestionnaire de tâches. Ses membres et méthodes utiles sont :

Propriété/Méthode | Description
------------- | -------------
NomDossierTaches | Chemin du répertoire contenant l'état de chaque tâche web. Le chemin est relatif au chemin du fichier PHP de la zone web.
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

### Le fichier Etat de la tâche web

L'état de la tâche est sauvegardé dans le fichier de ce format :
**&lt;NomDossierTaches-du-GestTachesWeb&gt;/&lt;IDInstanceCalc-tache-web&gt;.dat**
Si vous supprimez ce fichier, la tâche web sera exécutée au prochain affichage de la zone web.

## Composants IU

Les composants IU permettent d'interagir avec les utilisateurs.

### Utilisation

Vous devez suivre ce procédé :

1. Initier le composant
```php
$comp = new PvFormulaireDonnesHtml() ;
```
2. Renseigner ses propriétés d'initiation, s'il en possède
```php
$comp->InscrireCommandeExecuter = 1 ;
```
3. Adoptez le script ou la zone contexte par les méthodes AdopteScript($nom, & $script) ou AdopteZone($nom, $zone).
```php
$comp->AdopteScript("monComposant", $this) ;
```
4. Charger la configuration du composant par la méthode ChargeConfig()
```php
$comp->ChargeConfig() ;
```
5. Renseigner ses autres propriétés
```php
$comp->CommandeExecuter->Libelle = "VALIDER" ;
$comp->SuccesMessageExecution = "La page a été modifiée" ;
```
6. Invoquer le Rendu du composant par la méthode RenduDispositif()
```php
$ctn = $comp->RenduDispositif() ;
```
### Définition

Vous devez déclarer les composants IU dans la zone web, le document web ou le script web.
Pour le définir (étape 1. à 5 de l'utilisation), utilisez ces méthodes :

Classe | Méthode | Directives
------------- | ------------- | -------------
Document Web | PrepareRendu(& $zone) | Aucun
Zone Web | DetermineEnvironnement(& $script) | Invoquer parent::DetermineEnvironnement($script) après avoir défini le composant
Script Web | DetermineEnvironnement() | Aucun

Vous invoquez le rendu séparément :

Classe | Méthode | Directives
------------- | ------------- | -------------
Document Web | RenduEntete(& $zone) | Invoquer parent::RenduEntete($zone) avant le rendu du composant
Document Web | RenduPied(& $zone) | Invoquer parent::RenduPied($zone) après le rendu du composant
Zone Web | RenduContenuCorpsDocument () | Aucun
Script Web | protected RenduDispositifBrut() | Aucun
Script Web | RenduSpecifique() | Aucun

### Types de composant

#### Données

Nom | Classe | Rôle
------------- | ------------- | -------------
Tableau de données Html | PvTableauDonneesHtml | Affiche sous forme de tableau des données
Grille de données Html | PvGrilleDonneesHtml | Affiche sous forme de grille des données
Formulaire de données Html | PvFormulaireDonneesHtml | Affiche sous forme de formulaire de données

#### Graphiques et statistiques

Nom | Classe | Rôle
------------- | ------------- | -------------
Chart pChart | PvPChart | Chart réalisée avec la librairie PHP pChart 2.0

#### Sliders

Nom | Classe | Rôle
------------- | ------------- | -------------
Slider JQuery Camera | PvJQueryCamera | Slider réalisé à partir de la librairie Javascript jQuery Camera

## Le Tableau de Données - PHP-PV

### Présentation

Le tableau de données est un composant IU. Il affiche :
- Un formulaire de champs pour filtrer les résultats
- Un bloc de commandes, pour l'exportation des résultats à un format précis…
- Un tableau des résultats de la recherche
La classe de ce composant est **PvTableauDonnesHtml**.
 
![Apercu tableau données](images/tabldonneeshtml.png)
 
### Utilisation basique

Il utilise toujours un fournisseur de données pour le rendu.

```php
class MonScript1 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
// Déclaration
$this->Tabl1 = new PvTableauDonneesHtml() ;
// Chargement de la config
$this->Tabl1->AdopteScript("tabl1", $this) ;
$this->Tabl1->ChargeConfig() ;
// Définition des filtres de sélection
$this->Flt1 = $this->Tabl1->InsereFltSelectHttpGet("expression", "champ1 like concat(<self>, '%')") ;
$this->Flt1->Libelle = "Expression" ;
// Définition des colonnes
$this->Tabl1->InsereDefColCachee("id") ;
$this->Tabl1->InsereDefCol("champ1", "Champ 1") ;
$this->Tabl1->InsereDefCol("champ2", "Champ 2") ;
// Définition du fournisseur de données
$this->Tabl1->FournisseurDonnees = new PvFournisseurDonneesSql() ;
$this->Tabl1->FournisseurDonnees->BaseDonnees = new MaBD1() ;
$this->Tabl1->FournisseurDonnees->RequeteSelection = "matable1" ;
}
public function RenduSpecifique()
{
$ctn = '' ;
$ctn .= $this->Tabl1->RenduDispositif() ;
return $ctn ;
}
}
```

### Filtres de sélection

Méthode | Description
------------- | -------------
InsereFltSelectHttpGet($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre http GET
InsereFltSelectHttpPost($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre http POST
InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='') | Ajoute un filtre http UPLOAD. Tous les fichiers téléchargés seront déposés dans le répertoire $cheminDossierDest
InsereFltSelectSession($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre contenant la valeur d'une session
InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='') | Ajoute un filtre basé sur une valeur fixe
InsereFltSelectCookie($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre contenant la valeur d'un cookie

### Définitions de colonne

Propriété / Méthode | Description
------------- | -------------
$DefinitionColonnes | Tableau des définitions de colonne
InsereDefColCachee($nomDonnees, $aliasDonnees="") | Inscrit une définition de colonne cachée. 
InsereDefColInvisible($nomDonnees, $aliasDonnees="") | 
InsereDefCol($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne, avec un libellé.
InsereDefColBool($nomDonnees, $libelle="", $aliasDonnees="", $valPositive="", $valNegative="") | Inscrit une définition de colonne qui affiche un libellé en fonction d'une valeur booléenne.
InsereDefColChoix($nomDonnees, $libelle="", $aliasDonnees="", $valsChoix=array()) | Inscrit une définition de colonne qui affiche un libellé en fonction d'une valeur.
InsereDefColMonnaie($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne au format monétaire
InsereDefColMoney($nomDonnees, $libelle="", $aliasDonnees="") | 
InsereDefColDateFr($nomDonnees, $libelle="", $inclureHeure=0) | Inscrit une définition de colonne au format Français (dd/mm/yyyy). Si $inclureHeure est égal à 1, l'heure sera affichée également.
InsereDefColDateTimeFr($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne au format Français (dd/mm/yyyy hh:mi:ss)
InsereDefColDetail($nomDonnees, $libelle="", $aliasDonnees="") | Inscrit une définition de colonne, qui affiche les 1ers caractères de la ligne. Si vous posez le curseur sur cette cellule, un bloc contenant le texte intégral apparaîtra.
InsereDefColHtml($modeleHtml="", $libelle="") | Inscrit une définition de colonne qui affichera un contenu HTML.
InsereDefColTimestamp($nomDonnees, $libelle="", $formatDate="d/m/Y H:i:s") | Inscrit une définition de colonne qui affichera une date à partir d'un timestamp
InsereDefColActions($libelle, $actions=array()) | Inscrit une définition de colonne affichera des liens.

### Source de valeurs supplémentaires

Vous pouvez étendre les lignes calculées dans le tableau de données. Utilisez la propriété **$SourceValeursSuppl**. Etendez la classe **PvSrcValsSupplLgnDonnees** pour réécrire sa méthode **Applique(& $composant, $ligneDonnees)**.
Vous utiliserez ces nouvelles valeurs uniquement dans une définition de colonne HTML.

```php
class SrcValsSuppl1 extends PvSrcValsSupplLgnDonnees
{
public function Applique(& $composant, $ligneDonnees)
{
$results = array('menu' => '<a href="?appelleScript=developper&id='.urlencode($ligneDonnees ["id"]).'">+</a>') ;
return array_merge($ligneDonnees, $results) ;
}
}
class MonScript1 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
$this->Tabl1 = new PvTableauDonneesHtml() ;
$this->Tabl1->AdopteScript("tabl1", $this) ;
$this->Tabl1->ChargeConfig() ;
// ...
$this->Tabl1->SourceValeursSuppl = new SrcValsSuppl1() ;
// ...
$this->Tabl1->InsereDefColCachee("id") ;
$this->Tabl1->InsereDefColHtml('${menu} ${id}', 'Actions') ;
}
}
```

### Autres propriétés

Propriété / Méthode | Description
------------- | -------------
$Largeur | Largeur du formulaire de filtres
$LargeurFormulaireFiltres | Largeur du formulaire de filtres
$AlignFormulaireFiltres | Alignement du formulaire de filtres
$AlignBoutonSoumettreFormulaireFiltres | Alignement du bouton Soumettre du formulaire de filtres
$TitreBoutonSoumettreFormulaireFiltres | Libellé du bouton Soumettre du formulaire de filtres
$MessageAucunElement | Message lorsqu'il n'y a aucun élément trouvé
$ElementsEnCours | Tableau contenant toutes les lignes trouvées
$AlerterAucunElement | Affichera le message s'il n'y a aucun élément
$TriPossible | Permettra le tri
$TotalElements | Nombre de lignes retournées
$CacherNavigateurRangees | Cacher le navigateur de rangées
$CacherFormulaireFiltres | Cacher le formulaire de filtres
$CacherBlocCommandes | Cacher le bloc de commandes
$MaxElementsPossibles = array(20) | Nombres maximum de lignes par rangée

### Liens d'action

Méthode | Description
------------- | -------------
InsereLienAction(& $col, $formatUrl='', $formatLib='') | Inscrit un lien dans la colonne Action $col.
InsereLienActionAvant(& $col, $index, $formatUrl='', $formatLib='') | Inscrit un lien dans la colonne Action $col à la position $index
InsereIconeAction(& $col, $formatUrl='', $formatCheminIcone='', $formatLib='') | Inscrit une icône dans la colonne Action $col.
InsereIconeActionAvant(& $col, $index, $formatUrl='', $formatCheminIcone='', $formatLib='') | Inscrit une icône dans la colonne Action $col à la position $index

### Commandes

Propriété / Méthode | Description
------------- | -------------
$Commandes | Tableau contenant toutes les commandes
InsereCommande($nom, $commande) | Inscrit une commande dans le tableau
InscritCmdRafraich($libelle='Actualiser', $cheminIcone='') | Inscrit une commande qui soumet le formulaire de filtres
InsereCmdRedirectUrl($nomCmd, $url, $libelle='') | Inscrit une commande qui redirige sur une URL
InsereCmdRedirectScript($nomCmd, $nomScript, $libelle='', $params=array()) | Inscrit une commande qui redirige sur un script de la zone
InsereCmdScriptSession($nomCmd, $libelle='', $urlDefaut=array()) | Inscrit une commande qui redirige sur le script session de la zone
InsereCmdExportTexte($nomCmd, $libelle='') | Inscrit une commande qui exporte les résultats au format texte (CSV)
InsereCmdExportExcel($nomCmd, $libelle='') | Inscrit une commande qui exporte les résultats au format HTML pour Excel

### Rendu du tableau de données

Vous pouvez personnaliser le rendu du tableau de données avec sa propriété **$DessinateurFiltresSelection**.
Référez-vous au rendu des filtres d'édition du formulaire de données pour l'utilisation.

## Le Formulaire de Données

### Présentation

Le formulaire de données est un composant IU.
Il affiche :
- Un message d'exécution : 
- Un formulaire de filtres : Il contiendra des champs qui seront soumis par la méthode « POST ».
- Un bloc de commandes : Il contiendra des boutons, qui recevront les valeurs du formulaire

![Apercu formulaire données](images/formulairedonneeshtml.png)

La classe du formulaire de données est **PvFormulaireDonneesHtml**.

### Utilisation basique

Voici un exemple d'utilisation.

```php
class MonScript2 extends PvScriptWebSimple
{
public $Form1 ;
public $Flt1 ;
public $Flt2 ;
public function DetermineEnvironnement()
{
// Initiation
$this->Form1 = new PvFormulaireDonneesHtml() ;
// Toujours afficher le formulaire
$this->Form1->InclureElementEnCours = 0 ;
$this->Form1->InclureTotalElements = 0 ;
// Définir la classe commande "Executer"
$this->Form1->NomClasseCommandeExecuter = "MaCmdExecScript2" ;
// Liaison avec le script en cours
$this->Form1->AdopteScript("form1", $this) ;
// Chargement de la config
$this->Form1->ChargeConfig() ;
// Définition des autres propriétés
$this->Flt1 = $this->Form1->InsereFltEditHttpPost("champ1") ;
$this->Flt1->Libelle = "Champ 1" ;
$this->Flt2 = $this->Form1->InsereFltEditHttpPost("champ2") ;
$this->Flt2->Libelle = "Champ 2" ;
}
public function RenduSpecifique()
{
$ctn = '' ;
// Rendu du formulaire de donnees
$ctn .= $this->Form1->RenduDispositif() ;
return $ctn ;
}
}
class MaCmdExecScript2 extends PvCommandeExecuterBase
{
protected function ExecuteInstructions()
{
$this->ConfirmeSucces("Commande exécutée avec succès") ;
}
}
```

### Interaction avec base de données

Vous pouvez manipuler les bases de données avec son fournisseur de données.

#### Ajout d'enregistrement

```php
class MonScript2 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
// Initiation
$this->Form1 = new PvFormulaireDonneesHtml() ;
// Toujours afficher le formulaire
$this->Form1->InclureElementEnCours = 0 ;
$this->Form1->InclureTotalElements = 0 ;
// Définir la classe commande "Executer"
$this->Form1->NomClasseCommandeExecuter = "PvCommandeAjoutElement" ;
// Liaison avec le script en cours
$this->Form1->AdopteScript("form1", $this) ;
// Chargement de la config
$this->Form1->ChargeConfig() ;
// Définition des autres propriétés
$this->Flt1 = $this->Form1->InsereFltEditHttpPost("colonne1", "colonne1") ;
$this->Flt1->Libelle = "Colonne 1" ;
$this->Flt2 = $this->Form1->InsereFltEditHttpPost("colonne2", "colonne2") ;
$this->Flt2->Libelle = "Colonne 2" ;
// Définition du fournisseur de données
$this->FournisseurDonnees = new PvFournisseurDonneesSql() ;
$this->FournisseurDonnees->BaseDonnees = new MaBD() ;
$this->FournisseurDonnees->RequeteSelection = "matable1" ;
$this->FournisseurDonnees->TableEdition = "matable1" ;
}
```

#### Modification d'enregistrement

```php
class MonScript2 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
// Initiation
$this->Form1 = new PvFormulaireDonneesHtml() ;
// Afficher le formulaire s'il y a un enregistrement
$this->Form1->InclureElementEnCours = 1 ;
$this->Form1->InclureTotalElements = 1 ;
// Définir la classe commande "Executer"
$this->Form1->NomClasseCommandeExecuter = "PvCommandeModifElement" ;
// Liaison avec le script en cours
$this->Form1->AdopteScript("form1", $this) ;
// Chargement de la config
$this->Form1->ChargeConfig() ;
// Définition des filtres de sélection
$this->Cle1 = $this->Form1->InsereFltSelectHttpGet("macle1", "cle1 = <self>") ;
// Définition des autres propriétés
$this->Flt1 = $this->Form1->InsereFltEditHttpPost("colonne1", "colonne1") ;
$this->Flt1->Libelle = "Colonne 1" ;
$this->Flt2 = $this->Form1->InsereFltEditHttpPost("colonne2", "colonne2") ;
$this->Flt2->Libelle = "Colonne 2" ;
// Définition du fournisseur de données
$this->FournisseurDonnees = new PvFournisseurDonneesSql() ;
$this->FournisseurDonnees->BaseDonnees = new MaBD() ;
$this->FournisseurDonnees->RequeteSelection = "matable1" ;
$this->FournisseurDonnees->TableEdition = "matable1" ;
}
```

#### Suppression d'enregistrement

```php
class MonScript2 extends PvScriptWebSimple
{
public function DetermineEnvironnement()
{
// Initiation
$this->Form1 = new PvFormulaireDonneesHtml() ;
// Afficher le formulaire s'il y a un enregistrement
$this->Form1->InclureElementEnCours = 1 ;
$this->Form1->InclureTotalElements = 1 ;
// Empêcher l'édition des filtres
$this->Form1->Editable = 0 ;
// Définir la classe commande "Executer"
$this->Form1->NomClasseCommandeExecuter = "PvCommandeSupprElement" ;
// Liaison avec le script en cours
$this->Form1->AdopteScript("form1", $this) ;
// Chargement de la config
$this->Form1->ChargeConfig() ;
// Définition des filtres de sélection
$this->Cle1 = $this->Form1->InsereFltSelectHttpGet("macle1", "cle1 = <self>") ;
// Définition des autres propriétés
$this->Flt1 = $this->Form1->InsereFltEditHttpPost("colonne1", "colonne1") ;
$this->Flt1->Libelle = "Colonne 1" ;
$this->Flt2 = $this->Form1->InsereFltEditHttpPost("colonne2", "colonne2") ;
$this->Flt2->Libelle = "Colonne 2" ;
// Définition du fournisseur de données
$this->FournisseurDonnees = new PvFournisseurDonneesSql() ;
$this->FournisseurDonnees->BaseDonnees = new MaBD() ;
$this->FournisseurDonnees->RequeteSelection = "matable1" ;
$this->FournisseurDonnees->TableEdition = "matable1" ;
}
```

### Propriétés d'initiation

Propriété | Description
------------- | -------------
$InclureElementEnCours | Le formulaire sera disponible si le fournisseur de données contient au moins un enregistrement
$InclureTotalElement | Comptera le nombre d'enregistrement du fournisseur de données.
$InscrireCommandeExecuter | Crée une commande « Exécuter » au chargement de  config du composant
$LibelleCommandeExecuter | Libellé de la commande « Exécuter »
$NomClasseCommandeExecuter | Nom de la classe commande « Exécuter »
$InscrireCommandeAnnuler | Crée une commande « Annuler » au chargement de  config du composant
$LibelleCommandeAnnuler | Libellé de la commande « Annuler »
$NomClasseCommandeAnnuler | Nom de la classe commande « Annuler »

### Filtres de sélection

Méthode | Description
------------- | -------------
InsereFltLgSelectHttpGet($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre http GET
InsereFltLgSelectHttpPost($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre http POST
InsereFltLgSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='') | Ajoute un filtre http UPLOAD. Tous les fichiers téléchargés seront déposés dans le dossier $cheminDossierDest.
InsereFltLgSelectSession($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre contenant la valeur d'une session
InsereFltLgSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='') | Ajoute un filtre basé sur une valeur fixe
InsereFltLgSelectCookie($nom, $exprDonnees='', $nomClsComp='') | Ajoute un filtre contenant la valeur d'un cookie

### Filtres d'édition

Méthode | Description
------------- | -------------
InsereFltEditHttpGet($nom, $colLiee='', $nomClsComp='') | Ajoute un filtre http GET
InsereFltEditHttpPost($nom, $colLiee='', $nomClsComp='') | Ajoute un filtre http POST
InsereFltEditHttpUpload($nom, $cheminDossierDest="", $colLiee='', $nomClsComp='') | Ajoute un filtre http UPLOAD. Tous les fichiers téléchargés seront déposés dans le dossier $cheminDossierDest
InsereFltEditSession($nom, $colLiee='', $nomClsComp='') | Ajoute un filtre contenant la valeur d'une session
InsereFltEditFixe($nom, $valeur, $colLiee='', $nomClsComp='') | Ajoute un filtre basé sur une valeur fixe
InsereFltEditCookie($nom, $colLiee='', $nomClsComp='') | Ajoute un filtre contenant la valeur d'un cookie

### Autres propriétés

Propriété / Méthode | Description
------------- | -------------
$CacherBlocCommandes | N'affiche pas le bloc de commandes
$CacherFormulaireFiltres | N'affiche pas le formulaire des filtres
$MessageAucunElement | Message à afficher si le formulaire ne trouve pas d'élément
$CacherFormulaireFiltresApresCmd | Cache le formulaire de filtres si une commande est exécutée
$Largeur | Largeur du formulaire
$ElementsEnCours | Lignes retournées après le rendu
$ElementEnCours | 1ère Ligne retournée après le rendu
RedirigeCmdAnnulerVersUrl($url) | Redirige la page vers l'URL lorsque vous cliquerez sur le bouton « Annuler » du formulaire
RedirigeCmdExecuterVersUrl($url) | Redirige la page vers l'URL lorsque vous cliquerez sur le bouton « Executer » du formulaire
FigeFiltresEdition() | Fixe tous les filtres édition en lecture seule
CacheFiltresEdition() | Cache tous les filtres édition
DoitInclureElement() | Confirme si les propriétés $InclureElementEnCours & $InclureTotalElements sont vraies.
AnnuleLiaisonParametres() | Interdit les filtres d'édition de récupérer les valeurs de leurs paramètres.

### Commandes

Classe | Prérequis | Description
------------- | ------------- | -------------
PvCommandeAnnulerBase | Aucun | Commande pour annuler l'édition du formulaire de données
PvCommandeExecuterBase | Aucun | Commande pour exécuter le formulaire de données. Veuillez étendre cette classe.
PvCommandeAjoutElement | Les propriétés InclureElementEnCours & InclureTotalElements doivent avoir la valeur 0 | Commande pour insérer un enregistrement dans le fournisseur de données du formulaire
PvCommandeModifElement | Les propriétés InclureElementEnCours & InclureTotalElements doivent avoir la valeur 1 | Commande pour modifier un enregistrement dans le fournisseur de données du formulaire
PvCommandeSupprElement | Les propriétés InclureElementEnCours & InclureTotalElements doivent avoir la valeur 1 | Commande pour supprimer un enregistrement dans le fournisseur de données du formulaire

### Rendu du formulaire de filtres

Vous pouvez personnaliser le rendu du formulaire de filtres avec sa propriété **$DessinateurFiltresEdition**.
Veuillez créer une classe héritant de **PvDessinFiltresDonneesHtml** et réécrire sa méthode publique **Execute(& $script, & $composant, $parametres)**.
Vous avez 2 méthodes dans la nouvelle classe, pour chaque filtre de données :
- **RenduLibelleFiltre(& $filtre)** pour le libellé du filtre de données
- **RenduFiltre(& $filtre, & $composant)** pour le composant du filtre de données

```php
class MonScript1 extends PvScriptWebSimple
{
public $Form1 ;
public $Flt1 ;
public $Flt2 ;
public function DetermineEnvironnement()
{
// Initiation
$this->Form1 = new PvFormulaireDonneesHtml() ;
// Toujours afficher le formulaire
$this->Form1->InclureElementEnCours = 0 ;
$this->Form1->InclureTotalElements = 0 ;
// Definir le dessinateur de filtres edition
$this->Form1->DessinateurFiltresEdition = new MonDessinFiltresDonnees() ;
// Liaison avec le script en cours
$this->Form1->AdopteScript("form1", $this) ;
// Chargement de la config
$this->Form1->ChargeConfig() ;
// Définition des autres propriétés
$this->Flt1 = $this->Form1->InsereFltEditHttpPost("champ1") ;
$this->Flt1->Libelle = "Champ 1" ;
$this->Flt2 = $this->Form1->InsereFltEditHttpPost("champ2") ;
$this->Flt2->Libelle = "Champ 2" ;
}
public function RenduSpecifique()
{
$ctn = '' ;
// Rendu du formulaire de donnees
$ctn .= $this->Form1->RenduDispositif() ;
return $ctn ;
}
}
class MonDessinFiltresDonnees extends PvDessinFiltresDonneesHtml
{
public function Execute(& $script, & $composant, $parametres)
{
$ctn = '' ;
$ctn .= '<p><b>'.$this->RenduLibelleFiltre($script->Flt1).'<b><br>' ;
$ctn .= $this->RenduFiltre($script->Flt1, $composant).'</p>' ;
$ctn .= '<hr>' ;
$ctn .= '<p><b>'.$this->RenduLibelleFiltre($script->Flt2).'<b><br>' ;
$ctn .= $this->RenduFiltre($script->Flt2, $composant).'</p>' ;
return $ctn ;
}
}
```

### Rendu du bloc de commandes

Pour personnaliser le rendu du bloc des commandes, utilisez la propriété **$DessinateurBlocCommandes**.
Veuillez créer une classe héritant de PvDessinCommandesHtml et réécrire sa méthode publique **Execute(& $script, & $composant, $parametres)**.
Cette nouvelle classe donne le rendu d'une commande avec la méthode **RenduCommande(& $commande)**.

```php
class MonScript1 extends PvScriptWebSimple
{
public $Form1 ;
public $Flt1 ;
public $Flt2 ;
public function DetermineEnvironnement()
{
// Initiation
$this->Form1 = new PvFormulaireDonneesHtml() ;
// Toujours afficher le formulaire
$this->Form1->InclureElementEnCours = 0 ;
$this->Form1->InclureTotalElements = 0 ;
// Definir le dessinateur de commandes
$this->Form1->DessinateurBlocCommandes = new MonDessinCommandes() ;
// Liaison avec le script en cours
$this->Form1->AdopteScript("form1", $this) ;
// Chargement de la config
$this->Form1->ChargeConfig() ;
// Définition des autres propriétés
$this->Flt1 = $this->Form1->InsereFltEditHttpPost("champ1") ;
$this->Flt1->Libelle = "Champ 1" ;
$this->Flt2 = $this->Form1->InsereFltEditHttpPost("champ2") ;
$this->Flt2->Libelle = "Champ 2" ;
// Commandes
// …
}
public function RenduSpecifique()
{
$ctn = '' ;
// Rendu du formulaire de donnees
$ctn .= $this->Form1->RenduDispositif() ;
return $ctn ;
}
}
class MonDessinCommandes extends PvDessinCommandesHtml
{
public function Execute(& $script, & $composant, $parametres)
{
$ctn = '' ;
$ctn .= '<p>' ;
// Le formulaire est dans la variable $composant
$ctn .= $this->RenduCommande($composant->CommandeAnnuler) ;
$ctn .= '<hr />' ;
$ctn .= $this->RenduCommande($composant->CommandeExecuter) ;
$ctn .= '</p>' ;
return $ctn ;
}
}
```
