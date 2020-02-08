# Passerelles de paiement - PHP-PV

## Présentation

La passerelle de paiement est une IHM de l'application, pour payer des services de l'application (facture, article etc).
Elle se connecte sur un système de paiement en ligne.
Exemple :
Paypal, Skrill...
Une fois le paiement réussi, elle affecte le service demandé avec le client.

## Base de données des transactions

Les passerelles de paiement enregistrent les transactions dans une base de données.

Vous devez télécharger et importer la structure SQL à cette adresse :

(https://github.com/PvSolutions/php-pv/blob/sql/Paiement/transaction_paiement.sql)

## Processus

![Processus de paiement](../images/process-passerelle-paiement.jpg)

A partir d'une zone web, vous soumettez une transaction à la passerelle de paiement.
Elle contactera le système de paiement en ligne. Une fois le paiement réussi, la passerelle exécute le service après paiement.