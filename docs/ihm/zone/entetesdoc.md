# Entêtes document Zone Web - PHP-PV

## Propriétés HTML

La zone web possède des propriétés pour le rendu HTML.

Propriété | Rôle | Contenu HTML généré
------------- | ------------- | -------------
EncodageDocument | Fixe l’encodage de la page web. Par défaut : "utf-8" | &lt;meta charset="$valeur" /&gt;
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
			// Inscrire la zone web de l’Application
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
