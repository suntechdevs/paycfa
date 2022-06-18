---
description: PHP SDK for server side integration
---

# PHP SERVER SDK

## GENEREZ VOS CLES API

Les clés API sont vos références numériques vers les systèmes Intram. Nous les utilisons pour identifier votre compte et les applications que vous allez créer. Ces clés sont nécessaires pour toute intégration des API de paiements d'Intram. Voici les étapes à suivre :

* Tout d'abord, vous devez avoir un compte Intram Business activé. Créez-en un si ce n'est pas encore le cas.
* [Connectez-vous](https://app.intram.org/login) à votre compte et cliquez sur Développeurs au niveau du menu à gauche.
* Cliquez ensuite sur API, vous verrez toutes vos clés API.
* Vous pouvez passer au mode `SANDBOX` ou activer le mode `LIVE`.

### Installation via composer

```bash
composer require intram/php-sdk
```

### Api configuration

[Login](https://app.intram.org/login) à votre compte Intram, cliquez sur `Developer`, puis sur `API` à ce niveau, récupérez les clés API et donnez-les comme arguments au contrôleur. Initialiser Intram en entrant dans l'ordre : `PUBLIC_KEY`, `PRIVATE_KEY`, `INTRAM_SECRET`, `INTRAM_MARCHAND_KEY`, `MODE` Le mode : `true` pour le mode live et `false` pour le mode test.

```php
$intram = new \Intram\Intram(
            "5b06f06a0aad7d0163c414926b635ee",
            "pk_9c0410014969f276e8b3685fec7",
            "sk_08bd75f9468b484d8a9f24daddf",
            "marchand_id",
            true)
```

### Configurez les informations relatives à votre département / entreprise

#### Nom de votre entreprise (requis)

```php
$intram->setNameStore("Suntech Store");
```

#### URL du  Logo de votre entreprise (optionnel)

```php
$intram->setLogoUrlStore("https://www.suntechshop/logo.png");
```

#### Le site web de votre entreprise (optionnel)

```php
$intram->setWebSiteUrlStore("https://www.suntechshop");
```

#### Le numéro de téléphone de votre entreprise

```php
$intram->setPhoneStore("97000000");
```

#### L'adresse postale de votr eentreprise

```php
$intram->setPostalAdressStore("BP 35");
```

### Créer une requête de paiement

Afin de permettre à l'utilisateur d'effectuer un paiement sur votre boutique, vous devez créer la transaction puis lui envoyer l'URL de paiement ou le QR code à scanner. Pour cela :

Ajouter des éléments de facture

Ajouter les différents produits de l'achat&#x20;

```php
$intram->setItems([
            ['name'=>"T-shirt",'qte'=>"2",'price'=>"500",'totalamount'=>"1000"],
            ['name'=>"trouser",'qte'=>"1",'price'=>"12500",'totalamount'=>"12500"],
        ]);
```

#### La TVA&#x20;

```php
$intram->setTva([["name" => "VAT (18%)", "amount" => 1000],["name" => " other VAT", "amount" => 500]]);
```

#### Données personnelles

```php
$intram->setCustomData([['CartID',"32393"],['PERIOD',"TABASKI"]]);
```

#### Prix total de la transaction (requis)

```php
$intram->setAmount(13600);
```

#### Devise de la transaction (requis)

```php
$intram->setCurrency("XOF");
```

#### Description de la transaction (optionnelle)

```php
$intram->setDescription("Pretty and suitable for your waterfall");
```

#### Modèle de page de paiement (requis)

```php
$intram->setTemplate("default");
```

#### URL de Redirection

```php
$intram->setRedirectionUrl("https://www.suntechshop/redirection-url");
```

#### URL Retour après transaction&#x20;

```php
$intram->setReturnUrl("https://www.suntechshop/return-url");
```

#### URL retour après annulation de la transaction

```php
$intram->setCancelUrl("https://www.suntechshop/cancel-url");
```

#### Effectuer la demande de paiement

```php
$response = $intram->setRequestPayment();
```

Réponse attendue

```php
{
 +"status": "PENDING"
 +"transaction_id": "5f2d7a96b97d9d3fea912c11"
 +"receipt_url": "localhost:3000/payment/gate/5f2d7a96b97d9d3fea912c11"
 +"total_amount": 1000
 +"currency":"xof"
 +"qr_code":"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHQAAAB0CAYAAABUmhYnAAAAAklEQVR4AewaftIAAAL9SURBVO3BQQ7jCAhFwZcvi6NyKI7KgkyWrCxZdtLdDFWv9wdrDLFGEWsUsUYRaxSxRhFrFL ▶"
}
```

#### Récupérer des données

```php
$transaction_id = $response->transaction_id;
$status = $response->status;
$receipt_url = $response->receipt_url;
$total_amount = $response->total_amount;
.......
```

### Obtenir le statut de la transaction

Donnez l'identifiant de la transaction comme argument à la fonction

```php
$intram->getTransactionStatus($transaction_id);
```

#### Réponse attendue

```php
{
  +"status": "PENDING"
  +"amount": 1000
  +"currency": "XOF"
  +"account_number":"22996XXXXXX"
  +"source":"MOOV"
  +"fees":50
  +"net_amount":1050
}
```

## Tests

Pour exécuter les tests, il suffit de définir les variables d'environnement de la configuration de l'API. Une connexion Internet est nécessaire pour que certains tests soient réussis.

### Licence

MIT
