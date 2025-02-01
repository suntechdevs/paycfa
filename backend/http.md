---
description: >-
  L'API Intram permet d'intégrer facilement des solutions de paiement dans vos
  applications. Cette documentation fournit les informations nécessaires pour
  utiliser l'API via des requêtes HTTP directes.
---

# HTTP

### Prérequis

Pour utiliser l'API, vous aurez besoin de :

* Vos clés d'API (public\_key, private\_key, secret\_key)
* Votre identifiant marchand (marchand\_id)
* cURL installé sur votre machine pour les tests en ligne de commande

### Authentification

Toutes les requêtes doivent inclure les headers d'authentification suivants :

```
X-API-KEY: votre_public_key
X-PRIVATE-KEY: votre_private_key
X-SECRET-KEY: votre_secret_key
X-MARCHAND-KEY: votre_marchand_id
```

### Endpoints

#### Base URLs

* endpoint : `https://webservices.intram.org:4002/api/v1/`

#### 1. Initier un paiement

* **URL** : `/payments/request`
* **Méthode** : `POST`
* **Exemple de commande** :

```bash
curl -X POST \
  'https://webservices.intram.org:4002/api/v1/payments/request' \
  -H 'X-API-KEY: votre_public_key' \
  -H 'X-PRIVATE-KEY: votre_private_key' \
  -H 'X-SECRET-KEY: votre_secret_key' \
  -H 'X-MARCHAND-KEY: votre_marchand_id' \
  -H 'Content-Type: application/json' \
  -d '{
    "invoice": {
      "keys": {
        "public": "votre_public_key",
        "private": "votre_private_key",
        "secret": "votre_secret_key"
      },
      "currency": "XOF",
      "items": [],
      "taxes": [],
      "amount": 1000,
      "description": "Paiement test",
      "custom_datas": {}
    },
    "store": {
      "name": "Mon Magasin",
      "postal_adress": "Adresse du magasin",
      "logo_url": "https://monmagasin.com/logo.png",
      "web_site_url": "https://monmagasin.com",
      "phone": "+22500000000",
      "template": "default"
    },
    "actions": {
      "cancel_url": "https://monmagasin.com/cancel",
      "return_url": "https://monmagasin.com/return",
      "callback_url": "https://monmagasin.com/callback"
    }
  }'
```

#### 2. Vérifier le statut d'une transaction

* **URL** : `/transactions/confirm/{transaction_id}`
* **Méthode** : `GET`
* **Exemple de commande** :

```bash
curl -X GET \
  'https://webservices.intram.org:4002/api/v1/transactions/confirm/TRANSACTION_ID' \
  -H 'X-API-KEY: votre_public_key' \
  -H 'X-PRIVATE-KEY: votre_private_key' \
  -H 'X-SECRET-KEY: votre_secret_key' \
  -H 'X-MARCHAND-KEY: votre_marchand_id' \
  -H 'Content-Type: application/json'
```

### Exemples d'utilisation

#### Exemple minimal d'initiation de paiement

```bash
curl -X POST https://webservices.intram.org:4002/api/v1/payments/request \
-H "X-API-KEY: votre_public_key" \
-H "X-PRIVATE-KEY: votre_private_key" \
-H "X-SECRET-KEY: votre_secret_key" \
-H "X-MARCHAND-KEY: votre_marchand_id" \
-H "Content-Type: application/json" \
-d '{
  "invoice": {
    "keys": {
      "public": "votre_public_key",
      "private": "votre_private_key",
      "secret": "votre_secret_key"
    },
    "currency": "XOF",
    "amount": 1000
  },
  "store": {
    "name": "Test Store"
  },
  "actions": {
    "cancel_url": "http://example.com/cancel",
    "return_url": "http://example.com/return",
    "callback_url": "http://example.com/callback"
  }
}'
```

### Gestion des erreurs

L'API retourne des codes HTTP standards :

* 200 : Succès
* 400 : Erreur dans la requête
* 401 : Erreur d'authentification
* 500 : Erreur serveur

En cas d'erreur, un message JSON est retourné avec le statut de l'erreur.

### Notes importantes

1. **Sécurité**
   * Ne partagez jamais vos clés d'API
   * Utilisez HTTPS pour toutes les requêtes
   * Validez toujours les callbacks côté serveur
2. **Environnement de test**
   * Utilisez l'environnement sandbox pour vos tests
   * Les transactions en sandbox ne sont pas réelles
