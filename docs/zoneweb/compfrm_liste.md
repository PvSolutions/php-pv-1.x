# Les composants de liste pour formulaire

## PvZoneSelectHtml

### Présentation

Affiche une liste déroulante pour sélectionner une valeur. Correspond au tag HTML \<select\>.

```php
$comp = $filtre->DeclareComposant("PvZoneSelectHtml") ;
$comp->FournisseurDonnees = new PvFournisseurDonneesDirect() ;
$comp->FournisseurDonnees->Valeurs["categories"] = array(
	array("id" => 1, "titre" => "Defaut"),
	array("id" => 2, "titre" => "Livre"),
	array("id" => 3, "titre" => "Tableau"),
	array("id" => 4, "titre" => "Calendrier"),
) ;
$comp->NomColonneValeur = "id" ;
$comp->NomColonneLibelle = "titre" ;
```

![Apercu](../images/pvzonetextehtml_apercu.png)

### Propriétés/Méthodes spécifiques





