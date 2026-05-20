---
description: >-
  Intégration HTTP directe avec l'ancienne API Intram (mode public). Pour les
  nouvelles intégrations backend, voir la section MERCHANT API V1.
---

# HTTP

{% hint style="warning" %}
**Nouvelle intégration ?** Cette page documente l'API historique conçue pour les pages de paiement publiques (widget, gateway).
Pour piloter votre compte marchand depuis votre **backend** (lire le solde, faire des payouts, transférer, demander un paiement, rembourser, recevoir des webhooks signés), utilisez la **[Merchant API v1](../merchant-api/README.md)** — elle est asynchrone, signée HMAC, idempotente et mieux outillée.
{% endhint %}

### Prérequis

Pour utiliser cette API, vous aurez besoin de :

* Vos clés d'API : **public\_key**, **private\_key**, **secret\_key** (Menu Développeurs → API du dashboard)
* `curl` (ou un client HTTP équivalent) pour les tests en ligne de commande

### Authentification

Toutes les requêtes doivent inclure les trois headers d'authentification suivants :

```
X-API-KEY: votre_public_key
X-PRIVATE-KEY: votre_private_key
X-SECRET-KEY: votre_secret_key
```

Les clés sont validées sur la table `keys` et liées à votre compte marchand. Aucun autre identifiant n'est nécessaire dans les headers (l'identifiant marchand est dérivé automatiquement à partir de la combinaison des 3 clés).

### Endpoints

#### Base URL

* `https://webservices.intram.org:4002/api/v1/`

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
  -H 'Content-Type: application/json' \
  -d '{
    "invoice": {
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

{% hint style="info" %}
Les clés d'API se passent **uniquement en headers**. Ne les mettez jamais dans le body — c'est du code mort qui sera ignoré et qui expose vos secrets dans les logs.
{% endhint %}

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
  -H 'Content-Type: application/json'
```

### Exemples d'utilisation

#### Exemple minimal d'initiation de paiement

```bash
curl -X POST https://webservices.intram.org:4002/api/v1/payments/request \
-H "X-API-KEY: votre_public_key" \
-H "X-PRIVATE-KEY: votre_private_key" \
-H "X-SECRET-KEY: votre_secret_key" \
-H "Content-Type: application/json" \
-d '{
  "invoice": {
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

* `200` : Succès
* `400` : Erreur dans la requête
* `401` : Erreur d'authentification
* `500` : Erreur serveur

En cas d'erreur, un message JSON est retourné avec le détail de l'erreur :

```json
{
  "error": true,
  "status": "ERROR",
  "message": "Invalid API keys"
}
```

### Notes importantes

1. **Sécurité**
   * Ne partagez jamais vos clés d'API — surtout pas dans le body d'une requête, pas dans un repo Git, pas dans le code front-end.
   * Utilisez HTTPS pour toutes les requêtes.
   * Validez toujours les callbacks côté serveur en re-vérifiant l'état de la transaction avec `/transactions/confirm/{id}`.
2. **Environnement de test**
   * Utilisez des clés sandbox (Menu Développeurs → API → mode `SANDBOX`) pour vos tests.
   * Les transactions sandbox ne touchent pas les wallets de production.

### Voir aussi

* **[Merchant API v1](../merchant-api/README.md)** — la nouvelle API backend recommandée pour les nouvelles intégrations
* [Devises supportées](../payment/supported-devices.md)
* [Frais](../payment/fees.md)
