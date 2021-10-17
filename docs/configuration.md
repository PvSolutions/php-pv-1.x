# Configuration PHP-PV

**PHP-PV** fonctionne en mode CLI et WEB.

## Téléchargement

Veuillez télécharger la dernière version sur https://github.com/PvSolutions/php-pv/archive/master.zip.

## Installation sur serveur web

Décompressez le contenu à la racine du dossier www du serveur web.

## Installation en mode CLI

Décompressez le contenu dans n'importe quel répertoire.

## Hello world

Créez un fichier **hellopv.php** au même niveau que le dossier **php-pv-master**.

```php
// Inclure la librairie
include dirname(__FILE__)."/php-pv-master/Pv/Base.class.php" ;
// Définir la classe de l'application
class ApplicationHelloPV extends PvApplication
{
	protected function ChargeIHMs()
	{
		$this->ZonePrinc = $this->InsereIHM("zonePrinc", new ZonePrincHelloPV()) ;
	}
}
class ZonePrincHelloPV extends PvZoneWebSimple
{
	public $AccepterTousChemins = 1 ;
	protected function ChargeScripts()
	{
		$this->ScriptAccueil = $this->InsereScriptParDefaut(new ScriptAccueilHelloPV) ;
	}
}
class ScriptAccueilHelloPV extends PvScriptWebSimple
{
	protected function RenduDispositifBrut()
	{
		return '<h1>HELLO WORLD</h1>' ;
	}
}
// Exécuter l'application
$app = new ApplicationHelloPV() ;
$app->Execute() ;
```
Résultat :

![Aperçu Hello World avec PHP-PV](images/hellopv.png)

