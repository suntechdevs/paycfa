---
description: >-
  Un package Flutter qui simplifie l'implémentation des paiements mobiles avec
  Intram.
---

# Flutter

* Support de Flutter 3.0+
* Amélioration de la stabilité
* Corrections de bugs mineurs
* Version initiale
* Support des paiements simples et complets
* Intégration avec l'API Intram

### Installation

Ajoutez cette ligne à votre fichier `pubspec.yaml` :

```yaml
dependencies:
  intram_sdk_flutter: ^1.0.1
```

Puis exécutez :

```bash
flutter pub get
```



### Utilisation

#### Import

```dart
import 'package:intram_sdk_flutter/intram_sdk_flutter.dart';
```

#### Configuration

**Génération des clés API**

Les clés API sont vos références numériques aux systèmes Intram. Pour les obtenir :

1. Créez un compte Intram Business sur [https://app.intram.org/register](https://app.intram.org/register)
2. Connectez-vous à [votre compte](https://app.intram.org/login)
3. Accédez à "Developers" puis "API"
4. Récupérez vos clés API

**Configuration des clés**

```dart
String public_key = "6a695a2def2ba8e68c773a260f95c0a081e05e79fa6f55a815ef815244e8083a";
String private_key = "tpk_6296f02e367ddee79462671b54dbe568e12f4f41b0bf6b8f758de56cd1ccc68d";
String secret_key = "tsk_e29b25acdef149504854a5dc8c63ff472598c865b38e0829ebb984f3c93b8d16";
String merchant_key = "5ef22f1df84ed86d57b17519";
```

#### Implémentation

**Paiement Complet**

```dart
bool required_user_information = true;
dynamic custom_user_information = {};
```

**Paiement Simple**

```dart
bool required_user_information = false;
dynamic custom_user_information = {
  "message": "Bienvenue sur Hangbé Store, veuillez éffectuer votre paiement pour faire cette commande",
  "firstname": "Client",
  "lastname": "Hangbé",
  "email": "contact@hangbe.com"
};
```

**Initialisation**

```dart
IntramSdkPayment intramSdkPayment = IntramSdkPayment(
  public_key, 
  private_key, 
  secret_key, 
  merchant_key,
  required_user_information, 
  custom_user_information
);
```

#### Paiement

**Effectuer un paiement**

```dart
int amount = 5000;
bool sandbox = true;
String store_name = "Hangbé Store";
String color = "#08a0cf";
String logo = "https://intram.org/images/logo-1.png";

dynamic init_payment_result = await intramSdkPayment.makePayment(
  context, 
  amount, 
  sandbox, 
  store_name, 
  color, 
  logo
);
```

**Réponse de paiement**

```yaml
{success: true, transaction: PJ2flG2YVc}
```

**Vérifier une transaction**

```dart
dynamic transaction = await intramSdkPayment.getTransaction(init_payment_result["transaction"]);
```

### Paramètres

#### Configuration principale

| Paramètre     | Type   | Description                     |
| ------------- | ------ | ------------------------------- |
| public\_key   | String | Clé publique du compte marchand |
| private\_key  | String | Clé privée du compte marchand   |
| secret\_key   | String | Clé secrète du compte marchand  |
| merchant\_key | String | Identifiant du marchand         |

#### Paiement

| Paramètre   | Type   | Description                              |
| ----------- | ------ | ---------------------------------------- |
| amount      | int    | Montant du paiement                      |
| sandbox     | bool   | Mode test (true) ou production (false)   |
| store\_name | String | Nom de la boutique                       |
| color       | String | Couleur du template (format hexadécimal) |
| logo        | String | URL du logo                              |

### Support

Pour toute assistance :

* Email : contact@intram.org
