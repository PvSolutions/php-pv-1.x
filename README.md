# php-pv
Framework Objet PHP en Français, pour développer des applications entreprises.
Il possède des classes pour :
*	des interfaces web jQuery, Bootstrap avec membership (authentification)
*   des interfaces shell
*   des processus de traitement de queues
*	des serveurs socket
*	des tâches programmées
*	des clients de bases de données

## Documentation

[Guide](docs/index.md)

## Exemple d'usage
* lib/Application.class.php
```php
include dirname(__FILE__)."/../php-pv/Pv/Base.class.php" ;
class MonApplication1 extends PvApplication
{
	public $Zone1 ;
	public $TacheProg1 ;
	public $ServPersist1 ;
	public $ChemRelRegServsPersists = "services/data" ;
	protected function ChargeIHMs()
	{
		$this->Zone1 = $this->InsereIHM("zone1", new MaZone1()) ;
	}
	protected function ChargeServsPersists()
	{
		$this->ServPersist1 = $this->InsereServPersist("servPersist1", new MonServicePersist1()) ;
	}
	protected function ChargeTachesProgs()
	{
		$this->TacheProg1 = $this->InsereTacheProg("tache1", new MaTacheProg1()) ;
	}
}
class MaZone1 extends PvZoneWebSimple
{
	public $CheminFichierRelatif = "index.php" ;
	public $Script1 ;
	public $Script2 ;
	protected function ChargeScripts()
	{
		$this->Script1 = $this->InsereScriptParDefaut(new MonScript1()) ;
		$this->Script2 = $this->InsereScript("script2", new MonScript2()) ;
	}
}
class MonScript1 extends PvScriptWebSimple
{
	public $TitreDocument = "Accueil" ;
	public $Titre = "Accueil Zone 1" ;
	public function RenduSpecifique()
	{
		return "Bienvenue sur la zone 1" ;
	}
}
class MonScript2 extends PvScriptWebSimple
{
	public $TitreDocument = "Script 2" ;
	public $Titre = "Script 2 de la Zone 1" ;
	public function RenduSpecifique()
	{
		return "Script 2 appel&eacute;" ;
	}
}
class MaTacheProg1 extends PvTacheProg
{
	public $ToujoursExecuter = 1 ;
	protected $NaturePlateforme = "WEB";
	public $CheminFichierRelatif = "services/tache1.php" ;
	protected function ExecuteSession()
	{
		echo "Tache 1 appelee" ;
	}
}
class MonServicePersist1 extends PvServicePersist
{
	protected $NaturePlateforme = "WEB" ;
	public $MaxSessions = 1 ;
	public $CheminFichierRelatif = "services/service1.php" ;
	protected function ExecuteSession()
	{
		echo "Processeur Queue 1 appelee" ;
	}
}
```
* index.php
```php
include dirname(__FILE__)."/lib/Application.class.php" ;
$app = new MonApplication1() ;
$app->Execute() ;
```
* services/service1.php
```php
include dirname(__FILE__)."/../index.php" ;
```
* services/tache1.php
```php
include dirname(__FILE__)."/../index.php" ;
```
Vous pouvez tester les URLs suivantes, avec <url_racine> comme l'URL du dossier :
* http://<url_racine>/index.php (Classe MaZone1)
* http://<url_racine>/services/service1.php (Classe MonServicePersist1)
* http://<url_racine>/services/tache1.php (Classe MaTacheProg1)

## Pré-requis 
* PHP >= 5.1
* memory_usage_limit>=128MO (php.ini)