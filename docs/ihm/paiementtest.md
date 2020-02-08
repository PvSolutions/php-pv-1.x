# Passerelle de paiement Test (Assistance)

## Préparation



## Exemple

1. Déclarez le service après paiement, pour une transaction réussie

```php
class ServicePaye1 extends PvSvcAprPaiementBase
{
public function ConfirmeSucces(& $transaction) // Si la transaction aboutit
{
echo "BRAVO ! Paiement reussi !!!" ;
}
public function ConfirmeEchec(& $transaction) // Si la transaction echoue
{
}
public function Annule(& $transaction) // Si la transaction est annulee
{
}
}
```
2. Déclarez la passerelle de paiement et assignez le service après paiement.

```php
class MaPasserellePaie1 extends PvPasserelleAssistance
{
public function ChargeConfig()
{
$this->InsereSvcAprPaiement("service1", new ServicePaye1()) ;
}
}
```

3. Insérez cette passerelle dans l'application

```php
class Application1 extends PvApplication
{
public function ChargeIHMs()
{
$this->PasserellePaie1 = $this->InsereIHM("passerelle1", new MaPasserellePaie1()) ;
// ...
}
}
```

4. Dans une zone, demandez le paiement
```php
class MaZone1 extends PvZoneWebSimple
{
protected function ChargeScripts()
{
$this->InsereScriptParDefaut(new MonScript1()) ;
}
}
class MonScript1 extends PvScriptWebSimple
{
// ....
$passerellePaie = & $this->ApplicationParent->PasserellePaie1 ;
$transaction = $passerellePaie->Transaction() ;
$transaction->Montant = 20 ;
$transaction->Monnaie = 'EUR' ; // EURO
$transaction->Designation = "Paiement du produit AAAA" ;
$transaction->Cfg->NomSvcAprPaiement = "monService1" ;
$transaction->Cfg->Arg01 = $this->ZoneParent->IdMembreConnecte() ;
$passerellePaie->DemarreProcessus() ;
// ...
}
```

