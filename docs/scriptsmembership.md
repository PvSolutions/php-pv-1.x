# Personnaliser Scripts Membership

## Inscription

### Activation

Vous pouvez autoriser l'inscription de nouveaux membres dans une zone web. Mettez la valeur "1" à la propriété **$AutoriserInscription**.

Changez la classe script Inscription de la zone à partir de sa propriété **$NomScriptInscription**.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
}
class MaZone1 extends PvZoneWebSimple
{
public $AutoriserInscription = 1 ;
public $NomScriptInscription = "ScriptInscriptionMonSite" ;
}
```

### Profil nouveau membre

Pendant l'inscription, le nouveau membre peut choisir son profil. Pour restreindre à certains profils, renseignez le tableau **$IdProfilsAcceptes**.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
public $IdProfilsAcceptes = array(1, 2, 4) ;
}
```

Sur la page d'inscription, vous verrez une liste déroulante pour choisir le profil. Pour masquer le profil, utilisez la propriété **$IdProfilParDefaut**.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
public $IdProfilsAcceptes = array() ;
public $IdProfilParDefaut = 2 ;
}
```

### Statut du nouveau membre

Le script d'inscription peut créer des membres inactifs, pour validation ultérieure. Utilisez la propriété bool **$ValeurActiveParDefaut**.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
// Après inscription, le membre ne peut pas se connecter
// Accédez à la page des membres pour le valider
public $ValeurActiveParDefaut = 0 ;
}
```

### Formulaire d'inscription

Si vous créez un formulaire de données, vous l'attribuez à la page d'inscription avec la propriété **NomClasseFormulaireDonnees**

```php
class FormInscriptionMonSite extends PvFormulaireInscriptionMembreMS
{
} 
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
public $NomClasseFormulaireDonnees = "FormInscriptionMonSite" ;
}
```

Pour garder les champs nécessaires, mettez la propriété **$Detaille** à 0/false. Le formulaire affichera uniquement le login, le mots de passe (à confirmer) et l'email.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
// Après inscription, le membre ne peut pas se connecter
// Accédez à la page des membres pour le valider
public $Detaille = 0 ;
}
```

Vous pouvez également ajouter des filtres au formulaire en réécrivant la méthode **ChargeConfigComposantFormulaireDonnees()**.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
protected function ChargeConfigComposantFormulaireDonnees()
{
// Charger les filtres
parent::ChargeConfigComposantFormulaireDonnees() ;
// Récupérer le composant formulaire
$form = & $this->ComposantFormulaireDonnees ;
// Ajout de filtres specifiques, etc
$this->FltMetier = $form->InsereFltEditHttpPost("metier", "metier") ;
$this->FltMetier->Libelle = "Metier" ;
$this->FltDateCreation = $form->InsereFltEditFixe("date_creation", date("Y-m-d H:i:s"), "date_creation") ;
$form->CacherFormulaireFiltresApresCmd = 1 ;
}
}
```

Voici les filtres du formulaire disponibles :

Propriété | Description
------------- | -------------
$FiltreLoginMembre | Login
$FiltreMotPasseMembre | Mot de passe
$FiltreConfirmMotPasseMembre | Confirmer le mot de passe
$FiltreEmailMembre | Email
$FiltreContactMembre | Contact
$FiltreAdresseMembre | Adresse
$FiltreNomMembre | Nom
$FiltrePrenomMembre | Prénom
$FiltreActiverMembre | Activer
$FiltreProfilMembre | Profil

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
protected function ChargeConfigComposantFormulaireDonnees()
{
// Charger les filtres
parent::ChargeConfigComposantFormulaireDonnees() ;
// Récupérer le composant formulaire
$form = & $this->ComposantFormulaireDonnees ;
// Modifier les filtres du formulaire
$form->FiltreAdresseMembre->Invisible = 1 ;
$form->FiltreContactMembre->Invisible = 1 ;
}
}
```

### Validation par mail

Après l'inscription, vous pouvez activer le membre par mail.

D'une part, créez deux colonnes **enable_confirm_mail** et **code_confirm_mail** dans votre table des membres.

```sql
alter table membership_member add (
enable_confirm_mail int(1) default 0,
code_confirm_mail varchar(6) null
) ;
```

D'autre part, mettez la valeur 1 à **$ActiverConfirmMail** de la classe du script Inscription. Vous devez définir l'adresse email qui enverra les mails avec **$EmailEnvoiConfirm**.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
public $ActiverConfirmMail = 1 ;
public $EmailEnvoiConfirm = 'nepasrepondre@monsite.com' ;
}
```

En plus, vous pouvez personnaliser chaque message :

Propriété | Description
------------- | -------------
$SujetMailConfirm | Sujet du mail de confirmation
$CorpsMailConfirm | Corps du mail de confirmation. Mettez toujours le lien de confirmation **${url}** dans cette valeur.
$SujetMailSuccesConfirm | Sujet du mail de validation d'inscription
$CorpsMailSuccesConfirm | Corps du mail de validation d'inscription
$MsgSuccesCmdExecuter | Message succès exécution du bouton d'inscription
$MsgSuccesEnvoiMailConfirm | Message d'alerte d'envoi du mail de confirmation. Elle apparait sur la page d'inscription
$MsgSuccesConfirmMail | Message d'alerte d'inscription validée. Elle apparait sur la page d'inscription, après vérification du bouton.

Pour chaque propriété, vous avez un tableau contenant toutes les colonnes de **"membership_member"** pour personnaliser. Ce tableau possède aussi ces clés.

Nom | Description
------------- | -------------
MEMBER_LOGIN | Login du nouveau membre
login_member | Alias de la clé MEMBER_LOGIN
password_member | Mot de passe du nouveau membre

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
public $ActiverConfirmMail = 1 ;
public $EmailEnvoiConfirm = 'nepasrepondre@monsite.com' ;
public $SujetMailConfirm = 'Confirmez votre inscription, ${login_member}' ;
public $CorpsMailConfirm = '<p>Bonjour ${login_member},</p>
<p>Nous vous prions de confirmer votre inscription en cliquant sur ce lien :</p>
<p><a href="${url}">${url}</a></p>
<p>Cordialement, <a href="http://www.monsite.com/">MonSite</a></p>' ;
public $SujetMailSuccesConfirm = 'Inscription validée, ${login_member}' ;
public $CorpsMailSuccesConfirm = '<p>Bonjour ${MEMBER_LOGIN},</p>
<p>Votre compte a ete bien confirmé. Bienvenue sur notre site <b>MonSite</b>.</p>
<p><b>Login :</b> ${MEMBER_LOGIN}</p>
<p><b>Mot de passe :</b> ${password_member}</p>
<p><b>Email :</b> ${email}</p>
<p><a href="http://www.monsite.com/">MonSite</a></p>
<p>Cordialement</p>' ;
}
```

### Redirection

Après validation d'un nouveau membre sur cette page, vous pouvez le connecter automatiquement avec **$ConnecterNouveauMembre**. En plus, vous avez la propriété **$UrlAutoConnexionMembre** pour la redirection.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
public $ConnecterNouveauMembre = 1 ;
public $UrlAutoConnexionMembre = "?appelleScript=tableau_bord" ;
}
```

Envoyez un mail avec la propriété **$EnvoiMailSucces**, si vous n'avez pas activé de confirmation par mail. Renseignez aussi l'expéditeur **$EmailEnvoiConfirm**, le sujet **$SujetMailSuccesConfirm** et le corps du mail **$CorpsMailSuccesConfirm**.

```php
class ScriptInscriptionMonSite extends PvScriptInscriptionWeb
{
public $EnvoiMailSucces = 1 ;
public $EmailEnvoiConfirm = 'nepasrepondre@monsite.com' ;
public $SujetMailConfirm = 'Bienvenue sur MonSite, ${login_member}' ;
public $SujetMailSuccesConfirm = 'Bienvenue, ${login_member}' ;
public $CorpsMailSuccesConfirm = '<p>Bonjour ${MEMBER_LOGIN},</p>
<p>Bienvenue sur notre site <b>MonSite</b>.</p>
<p><b>Login :</b> ${MEMBER_LOGIN}</p>
<p><b>Mot de passe :</b> ${password_member}</p>
<p><b>Email :</b> ${email}</p>
<p><a href="http://www.monsite.com/">MonSite</a></p>
<p>Cordialement</p>' ;
}
```

## Connexion

### Activation

Changez la classe script Connexion de la zone à partir de sa propriété **$NomScriptConnexion**.

```php
class ScriptConnexionMonSite extends PvScriptConnexionWeb
{
}
class MaZone1 extends PvZoneWebSimple
{
public $NomScriptConnexion = "ScriptConnexionMonSite" ;
}
```

### Propriétés de formulaire

Le script de connexion contient ces propriétés de formulaire :

Nom | Description
------------- | -------------
$LibellePseudo | Libellé du champ Login
$LibelleMotPasse | Libellé du champ Mot de passe
$NomParamPseudo | Nom du champ Login
$NomParamMotPasse | Nom du champ Mot de passe
$ValeurParamPseudo | Valeur du champ Login
$ValeurParamMotPasse | Valeur du champ Mot de passe
$AlignBoutonSoumettre | Alignement du bouton Connexion. Par défaut "center".
$LibelleBoutonSoumettre | Libellé du bouton Connexion

```php
class ScriptConnexionMonSite extends PvScriptConnexionWeb
{
public $LibellePseudo = "Compte Marchand" ;
public $LibelleMotPasse = "Clé" ;
}
```

### Rendu

Pour changer le rendu des champs de connexion, réecrivez la méthode **RenduTableauParametres()**. Vous devez inscrire un champ caché de nom **$NomParamSoumetTentative**. Sa valeur doit être **$ValeurParamSoumetTentative**.

```php
class ScriptConnexionMonSite extends PvScriptConnexionWeb
{
public function RenduTableauParametres()
{
$ctn = '' ;
$ctn .= '<div>
<label for="'.$this->NomParamPseudo.'">'.$this->LibellePseudo.'</label>
<input type="text" name="'.$this->NomParamPseudo.'" id="'.$this->NomParamPseudo.'" value="'.htmlentities($this->ValeurParamPseudo).'" />
</div>
<div>
<label for="'.$this->NomParamMotPasse.'">'.$this->LibelleMotPasse.'</label>
<input type="password" name="'.$this->NomParamMotPasse.'" id="'.$this->NomParamMotPasse.'" value="" />
</div>
<!-- Paramètre pour valider la tentative de connexion -->
<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
return $ctn ;
}
}
```

### Critères de connexion

En utilisant la méthode **ValideTentativeConnexion()**, vous pouvez ajouter des conditions avant la confrontation login/mot de passe.

```php
class ScriptConnexionMonSite extends PvScriptConnexionWeb
{
protected function ValideTentativeConnexion()
{
$bd = new MaBD1() ;
// On accepte si le login possede une carte
$lgn = $bd->FetchSqlRow(
'select * from carte_membre where login=:login',
array('login' => $this->ValeurParamPseudo)
) ;
if(count($lgn) > 0)
{
return 1 ;
}
return 0 ;
}
}
```

### Redirection

Pour activer la redirection après connexion, mettez la propriété **$AutoriserUrlsRetour**.

Le paramètre GET est **urlRetour**.

```php
// Exemple de URL :
// ?appelleScript=connexion&urlRetour=%3FappelleScript%3Dcontact
class ScriptConnexionMonSite extends PvScriptConnexionWeb
{
public $AutoriserUrlsRetour = 1 ;
public $NomParamUrlRetour = "urlRetour" ; // Paramètre GET
// Message d'erreur affiché si l'URL retour est renseignée
public $MessageAccesUrlRetour = "Vous devez être connecté pour accéder à cette page" ;
}
```

Vous pouvez éventuellement définir un lien (**$UrlConnexionReussie**) ou le nom du script (**$NomScriptConnexionReussie**) pour après connexion réussie.

```php
class ScriptConnexionMonSite extends PvScriptConnexionWeb
{
public $NomScriptConnexionReussie = "tableau_bord" ; // Meme valeur
}
```

Avec la méthode **SauveSessionMembre()**, vous pouvez manipuler le membre identifié par la paire login/mot de passe. Vous avez l'ID du membre avec la propriété **$IdMembre**.

```php
class ScriptConnexionMonSite extends PvScriptConnexionWeb
{
protected function SauveSessionMembre()
{
// Sauvegarde l'ID du membre dans la session courante
// Toujours l'invoquer
parent::SauveSessionMembre() ;
// On sauvegarde la connexion réussi dans une autre table...
$bd = new MaBD1() ;
$bd->RunSql(
	'insert into connexion (id_membre, id_session)
values (:id_membre, :id_session)', array('id_membre' => $this->IdMembre, 'id_session' => session_id())
) ;
}
}
```

## Déconnexion

Propriété | Description
------------- | -------------
$UrlDeconnexionReussie | URL après avoir déconnecté le membre
$NomScriptDeconnexionReussie | Nom du script après déconnexion réussie
$MessageDeconnexionReussie | Message affiché si aucune URL ou script n'est défini
$FiltreProfilMembre | Profil
$MessageRetourAccueil | Libellé du lien pour rediriger vers l'accueil

```php
class ScriptDeconnexionMonSite extends PvScriptDeconnexionWeb
{
public $UrlDeconnexionReussie = "?appelleScript=accueil" ;
}
```
