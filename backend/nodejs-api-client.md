---
description: >-
  The Node.JS library for INTRAM (intram.org). Built on the INTRAM HTTP API
  (beta).
---

# NODEJS API CLIENT

## GENERATE YOUR API KEYS

Les clés API sont vos références numériques vers les systèmes Intram. Nous les utilisons pour identifier votre compte et les applications que vous allez créer. Ces clés sont nécessaires à toute intégration des API de paiement d'Intram. Voici les étapes à suivre:

* Vous devez d'abord avoir un compte Intram Business activé. [Créez-en](https://app.intram.org/register) un si ce n'est pas encore le cas.
* [Connectez-vous ](https://app.intram.org/login)à votre compte et cliquez sur Développeurs au niveau du menu à gauche.
* Cliquez ensuite sur API, vous verrez toutes vos clés API.
* Vous pouvez naviguer du mode `SANDBOX, au mode LIVE.`

## INSTALLATION

### INSTALLATION INTRAM VIA LA COMMANDE NPM

```
npm i @intram-sdk/nodejs
```

## CONFIGURATION BASIC

[Connectez-vous](https://app.intram.org/login) à votre compte puis cliquez sur Développeurs et cliquez sur API au niveau du menu à gauche. Les clés API de votre compte marchand sont directement disponibles selon le mode ('sandbox' ou 'live') choisi. Récupérez les clés API et donnez-les comme arguments aux méthodes suivantes

{% code title="Setup" %}
```javascript
var intram = require('intram');

var setup = new intram.Setup({
  mode: 'sandbox', // optional. use in sandbox mode.
  marchandKey: 'tpk_5e9469e65341de91988b352eba11f9f0c5f671384e1d6bfb09ce30103',
  privateKey: 'tpk_5e9469e65341de91988b352eba11f9f0c5f671384e1d6bfb09ce30103b',
  publicKey: "5e59e0c34bb8737cedf4c0ec92d9ae94007e33e5c30280596456990d9fc2f60",
  secret: 'tsk_243a7b89fd82a2b4e049c0c8ff39c3012ee6ec70bda3288ad2bf6a1270439c'          
});
```
{% endcode %}

{% hint style="info" %}
**INFOS**

Si vous êtes en test, utilisez les clés sandbox, sinon utilisez les clés de production et spécifiez le mode en remplaçant `«sandbox»` par `«live»` dans le code ci-dessus.
{% endhint %}

{% hint style="success" %}
**BEST PRATICE**

Il peut généralement être approprié de placer votre configuration d'API dans des variables d'environnement. Dans ce cas, vous pouvez initialiser `intram.Setup` sans passer les paramètres de configuration. La bibliothèque détectera automatiquement les variables d'environnement et les utilisera. Variables d'environnement détectées automatiquement : **INTRAM\_MARCHAND\_KEY**, **INTRAM\_PRIVATE\_KEY**, **INTRAM\_PUBLIC\_KEY**, **INTRAM\_SECRET**
{% endhint %}

### 1- CONFIGURATION DE VOTRE SERVICE / ACTIVITÉ / INFORMATIONS SUR LA SOCIÉTÉ

Vous pouvez configurer vos informations de service / activité / entreprise comme indiqué ci-dessous. Intram utilise ces paramètres pour configurer les informations qui apparaîtront sur la page de paiement, les factures PDF et les reçus imprimés.

Intram vous propose plusieurs designs pour le portail de paiement. Vous pouvez donc choisir un modèle spécifique et ajouter la référence à votre boutique.

Vous pouvez également définir une couleur pour le portail de paiement. Seule la couleur du code est nécessaire

```javascript
var store = new intram.Store({
  name: 'LandryShop', // only name is required
  tagline: "Votre satisfaction, notre priorité.",
  phoneNumber: '229670000000',
  postalAddress: 'Benin-Cotonou Akpakpa',
  websiteURL: 'Benin - Cotonou - Akpakpa',
  logoURL: 'http://www.landryShop/logo.png',
  color:"#a845f4", //for custom paiement portal color,
  template:"default", // Choose the payment portal template that your customers will see
});
```

### 2 - CONFIGURATION DE LA NOTIFICATION DE PAIEMENT INSTANTANÉ

La NOTIFICATION DE PAIEMENT INSTANTANÉ est l'URL du fichier sur lequel vous souhaitez recevoir les informations de transaction de paiement pour un éventuel traitement en backoffice. Intram utilise cette URL pour vous envoyer instantanément, sur requête `POST`, les informations relatives à l'opération de paiement.

{% hint style="success" %}
Il existe deux façons de configurer l'URL de notification instantanée de paiement: soit en accédant à votre compte Intram au niveau des informations de configuration de votre application, soit directement dans votre code.
{% endhint %}

L'utilisation de la deuxième option vous offre les deux possibilités ci-dessous.

#### **2-1 -** CONFIGURATION GLOBALE DE L'URL DE NOTIFICATION DE PAIEMENT INSTANTANÉ

Cette instruction doit être incluse dans la configuration de votre service / activité.

```javascript
var store = new intram.Store({
    name: 'Store at Sandra',
    callbackURL: 'http://www.my-shop.com/fichier_de_reception_des_données_de_facturation'
});
```

#### **2-2 -** CONFIGURATION DE L'URL DE NOTIFICATION DE PAIEMENT INSTANTANÉ OU D'UNE INSTANCE DE FACTURE

{% hint style="info" %}
Cette configuration écrasera les paramètres de redirection globale s'ils ont déjà été définis
{% endhint %}

```javascript
invoice.callbackURL = 'http://www.my-shop.com/fichier_de_reception_des_données_de_facturation'
```

{% hint style="info" %}
La validation réussie de la transaction de paiement renvoie la structure ci-dessous contenant les informations client, l'URL de sa facture Intram au format PDF ainsi qu'un hachage pour vérifier que les données reçues proviennent de nos serveurs.
{% endhint %}

#### **EXPECTED JSON ANSWER**

```javascript
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

**SI VOUS UTILISEZ EXPRESS**

```bash
npm add body-parser
```

```javascript
var bodyParser = require('body-parser')
  app.use( bodyParser.json() );       // to support JSON-encoded bodies
  app.use(bodyParser.urlencoded({     // to support URL-encoded bodies
    extended: true
}));
```

### 3 - RECUPERATION DU STATUT DE PAIEMENT

```javascript
app.post('/payment-url', function(req, res) {
    var status = req.body.status;
});
```

### 4 - RECOUVREMENT DU MONTANT TOTAL DU PAIEMENT

```javascript
app.post('/payment-url', function(req, res) {
    var amount = req.body.transaction.invoice.amount;
});
```

## APIs

INITIALISATION D'UN PAIEMENT

Ce service Api vous permet de créer une facture et de rediriger votre client vers notre plateforme afin qu'il puisse terminer le processus de paiement. Nous vous recommandons d'utiliser cette API car elle est la plus adaptée dans 99% des cas. Le principal avantage de cette option est que les clients peuvent choisir de payer à partir d'une variété d'options de paiement disponibles sur notre plateforme. De plus, si une nouvelle option est ajoutée ultérieurement, elle apparaîtra directement sur la page de paiement sans que vous ayez à modifier quoi que ce soit dans votre code source.

```javascript
// Do this if you want to redirect your customers to our website so that they can complete the payment process
// It is important to note that the constructor requires respectively as parameters
// an instance of the intram.Setup and intram.Store classes
var invoice = new intram.CheckoutInvoice(setup, store);
```

### 1 - AJOUTER DES INFORMATIONS DE PAIEMENT

#### **1-1 -** AJOUT D'ARTICLES ET DESCRIPTION DE LA FACTURE:

Il est important de savoir que les éléments de facturation sont principalement utilisés à des fins de présentation sur la page de paiement. Intram n'utilisera aucun des montants déclarés pour facturer le client. Pour ce faire, vous devez explicitement utiliser la méthode SetTotalAmount de l'API pour spécifier le montant exact à facturer au client.

```javascript
// Adding items to your bill is very basic.
// The expected parameters are product name, quantity, unit price,
// the total price and an optional description.
invoice.addItem('Croco shoes', 1, 10000, 30000, 'Shoes made of genuine crocodile skin that hunts poverty');
invoice.addItem('Ice Shirt', 1, 5000, 5000);
```

{% hint style="info" %}
Vous pouvez éventuellement définir une description générale de la facture qui sera utilisée dans les cas où vous devez inclure des informations supplémentaires dans votre facture.
{% endhint %}

```javascript
invoice.description = "Optional Description";
```

#### **1-2 -** CONFIGURATION DU MONTANT TOTAL DE LA FACTURE\*\*:\*\*

Intram attend de vous que vous spécifiiez le montant total de la facture du client. Ce sera le montant qui sera facturé à votre client. Nous considérons que vous avez déjà effectué tous les calculs sur votre serveur avant de définir ce montant.

{% hint style="info" %}
Intram n'effectuera pas de calculs sur ses serveurs. Le montant total de la facture définie depuis votre serveur sera celui qu'Intram utilisera pour facturer votre client.
{% endhint %}

```javascript
invoice.totalAmount = 42300;
```

#### **1-3 -** CONFIGURATION DE LA MONNAIE DE LA FACTURE:

Intram attend de vous que vous spécifiiez la devise de la facture du client. Ce sera la devise qui sera attachée à votre client facturé.

{% hint style="success" %}
Actuellement, les devises disponibles sont:

* XOF
{% endhint %}

```javascript
invoice.currency = 'XOF';
```

### 2 - REDIRECTION VERS LA PAGE DE PAIEMENT INTRAM

Après avoir ajouté des articles à votre facture et configuré le montant total de la facture, vous pouvez rediriger votre client vers la page de paiement en appelant la méthode de création à partir de votre objet `invoice`. Veuillez noter que la méthode `invoice.create()` renvoie un booléen (vrai ou faux) selon que la facture a été créée avec succès ou non. Cela vous permet de mettre une instruction `if - else` et de gérer le résultat comme bon vous semble.

```javascript
// The following code describes how to create a payment invoice at our servers,
// then redirect the customer to the payment page
// and then post his payment receipt if successful.
invoice.create()
  .then(function (){
    console.log(transaction.invoice.status);
    console.log(transaction.invoice.transaction); // Bill Token
    console.log(transaction.invoice.message);
    console.log('https://account.intram.cf/payment/gate/'+invoice.transaction); // Intram invoice payment redirection URL
  })
  .catch(function (e) {
    console.log(e);
  });
```

### MÉTHODES D'API SUPPLÉMENTAIRES

#### 1 - AJOUT DE TAXES (OPTIONNEL)

Vous pouvez ajouter des informations fiscales sur la page de paiement. Ces informations seront ensuite affichées sur la page de paiement, les factures PDF et les reçus imprimés, les reçus électroniques.

```javascript
// The parameters are the name of the tax and the amount of the tax.
invoice.addTax('TVA (18%)', 6300);
invoice.addTax("Livraison", 1000);
```

#### 2 - AJOUT DE DONNÉES SUPPLÉMENTAIRES (FACULTATIF)

Si vous avez besoin d'ajouter des données supplémentaires à vos informations de demande de paiement pour une utilisation future, nous vous offrons la possibilité de sauvegarder des données personnalisées sur nos serveurs et de les récupérer une fois le paiement effectué.

{% hint style="info" %}
Les données personnalisées ne sont affichées nulle part sur la page de paiement, les factures / reçus, les téléchargements et les impressions. Ils sont uniquement récupérés à l'aide de notre action de rappel de `confirm` au niveau de l'API
{% endhint %}

```javascript
// Custom data allows you to add additional data to your billing information
// that can be retrieved later using our callback Confirm action
invoice.addCustomData("category", "contest");
invoice.addCustomData("period", "Christmas 2015");
invoice.addCustomData("numero_gagnant", 5);
invoice.addCustomData("price", "50% discount coupon");
```

#### 3 - RESTRICTION DES MOYENS DE PAIEMENT À AFFICHER (OPTIONNEL)

Par défaut, les modes de paiement activés dans votre configuration d'intégration seront tous affichés sur la page de paiement pour toutes vos factures.

Cependant, si vous souhaitez conserver la liste des modes de paiement à afficher sur la page de paiement d'une facture donnée, nous vous offrons la possibilité de le faire en utilisant la méthode `addChannels`.

{% hint style="success" %}
Actuellement, les moyens de paiement disponibles sont:

`MOOV`, `MTN-BENIN`, `VISA, MASTER-CARD`.
{% endhint %}

```javascript
// Addition of several payment methods at a time
invoice.addChannels(['moov-benin', 'mtn-benin', 'visa'])
```

### CONFIGURER UNE URL REDIRECT APRÈS ANNULATION DE PAIEMENT

#### **1-1 -** CONFIGURER UNE URL REDIRECT APRÈS ANNULATION DE PAIEMENT

Vous pouvez éventuellement définir une URL vers laquelle vos clients seront redirigés après une annulation de commande.

{% hint style="info" %}
Il existe deux façons de configurer l'URL d'annulation de commande: soit globalement au niveau des informations de configuration de votre application, soit par commande.
{% endhint %}

{% hint style="danger" %}
L'utilisation de la deuxième option écrase les paramètres globaux s'ils ont déjà été définis.
{% endhint %}

```javascript
var store = new intram.Store({
name: 'Alexandra',
cancelURL: 'http://alexandra.com/cancel_url'
});
```

#### **1-2 -** CONFIGURATION DE L'URL DE REDIRECTION APRÈS ANNULATION DE PAIEMENT SUR UNE INSTANCE DE FACTURE

{% hint style="danger" %}
Cette configuration écrasera les paramètres de redirection globale s'ils ont déjà été définis.
{% endhint %}

```javascript
invoice.cancelURL = 'http://magasin-le-choco.com/cancel_url';
```

#### **1-3 -** CONFIGURER UNE URL REDIRECT APRÈS LA CONFIRMATION DE PAIEMENT

Intram fait un excellent travail de gestion des téléchargements et de l'impression des reçus des paiements une fois que votre client a payé avec succès sa commande. Cependant, il peut arriver que vous souhaitiez rediriger vos clients vers une autre URL après avoir payé leur commande avec succès. La configuration ci-dessous répond à ce besoin.

{% hint style="info" %}
Intram ajoutera `?token=invoice_token` à votre URL. Nous expliquerons comment utiliser ce jeton dans le chapitre suivant.
{% endhint %}

#### **1-4 -** CONFIGURATION GLOBALE DE L'URL REDIRECT APRÈS LA CONFIRMATION DE PAIEMENT.

Cette instruction doit être incluse dans la configuration de votre service / activité

```javascript
var store = new intram.Store({
  name: 'Alexandra',
  returnURL: 'http://alexandra.com/return_url'
});
```

#### **4-5 -** CONFIGURATION DE L'URL REDIRECT APRÈS CONFIRMER LE PAIEMENT SUR UNE INSTANCE DE FACTURE

{% hint style="danger" %}
Cette configuration écrasera les paramètres de redirection globale s'ils ont déjà été définis.
{% endhint %}

```javascript
invoice.returnURL = 'http://magasin-le-choco.com/return_url';
```

## VÉRIFICATION DU STATUT DE PAIEMENT

Notre API vous permet de vérifier l'état de toutes les transactions de paiement à l'aide du jeton de facture. Vous pouvez donc conserver votre jeton de facture et l'utiliser pour vérifier le statut de paiement de la facture. Le statut d'une facture peut être soit `PENDING (PENDING)` , `CANCELED (CANCELED)` , ou `COMPLETED (COMPLETED)` selon si oui ou non le client a payé la facture.

{% hint style="info" %}
Cependant, cette option convient aux paiements PAR car elle vous permettrait par exemple de connaître l'état de paiement de votre facture même si le client est toujours sur notre page de paiement.
{% endhint %}

```javascript
// Intram will automatically add the invoice token as a QUERYSTRING "token"
// if you have configured a "return_url" or "cancel_url".
var token = 'test_VPGPZNnHOC';

var invoice = new intram.CheckoutInvoice(setup, store);
invoice.confirm(token)
.then(function (){
  // Retrieve payment status
  // The payment status can be either completed, pending, cancelled
  console.log(invoice.status);

  console.log(invoice.responseText);  // Server response

  // The following fields will be available if and
  // only if the payment status is equal to "completed".

  // You can retrieve the name, the email address and the
  // customer's phone number using the following object
  console.log(invoice.customer); // {name: 'Alioune', phone: '773830274', email: 'aliounebadara@gmail.com'}

  // URL of the electronic PDF receipt for download
  console.log(invoice.receiptURL); // 'https://app.intram.org/sandbox-checkout/receipt/pdf/test_VPGPZNnHOC.pdf'
})
.catch(function (e) {
  console.log(e);
});
```

{% hint style="success" %}
Numero de test

&#x20;**MTN** : 61000000

&#x20;**MOOV** : 94000000
{% endhint %}
