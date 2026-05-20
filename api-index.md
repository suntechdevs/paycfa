---
description: >-
  Catalogue des SDK et clients pour l'API publique de paiement Intram.
  Permettent de présenter un checkout client dans votre application.
---

# API publique de paiement

Cette section regroupe les clients officiels pour intégrer l'**API publique de paiement** — celle qui sert à présenter un checkout à votre client, en l'envoyant sur une page de paiement ou via un widget embarqué.

{% hint style="info" %}
Si votre besoin est de **piloter votre compte marchand depuis votre backend** (lire le solde, déclencher un payout, transférer vers un client, demander un paiement, rembourser…), utilisez plutôt la [**Merchant API v1**](merchant-api/README.md) — elle est asynchrone, signée HMAC, idempotente et mieux outillée pour le backend.
{% endhint %}

## Pré-requis

Avant d'utiliser un de ces clients, vous devez avoir suivi les étapes de la section [**Introduction**](README.md) :

1. Avoir un compte marchand Intram validé
2. Avoir récupéré vos clés API (`public_key`, `private_key`, `secret_key`) depuis **Développeurs → API**
3. Choisir l'environnement (`sandbox` pour le développement, `live` pour la production)

## Clients disponibles

Choisissez le SDK adapté à votre stack technique :

| Stack | Statut | Documentation |
| :--- | :---: | :--- |
| **PHP** | ✅ | [PHP Server SDK](backend/php-server-sdk.md) |
| **WordPress / WooCommerce** | ✅ | [WooCommerce Plugin](backend/woocommerce-plugin.md) |
| **Django (Python)** | ✅ | [Django-Intram](backend/django-intram.md) |
| **Node.js** | ✅ | [Node.js API Client](backend/nodejs-api-client.md) |
| **Flutter (mobile)** | ✅ | [Flutter](mobile/flutter.md) |
| **HTTP / JSON direct** | ✅ | [HTTP](backend/http.md) |
| **JavaScript (widget navigateur)** | ✅ | [JavaScript](fontend/javascript.md) |

Tous ces clients consomment le même endpoint public `https://webservices.intram.org:4002/api/v1/`.

## Si votre stack n'est pas listée

Utilisez directement [l'API HTTP/JSON](backend/http.md) — tous les SDK ci-dessus en sont des wrappers. Vous pouvez vous appuyer sur n'importe quel client HTTP standard (`requests` Python, `Guzzle` PHP, `axios` Node…) pour faire vos requêtes.

## Voir aussi

* [Frais](payment/fees.md) — comprendre le calcul des commissions appliquées
* [Devises supportées](payment/supported-devices.md)
* [Merchant API v1](merchant-api/README.md) — l'alternative backend recommandée pour les nouvelles intégrations
