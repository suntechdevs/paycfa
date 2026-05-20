---
description: >-
  Vue d'ensemble de l'API Merchant Intram v1 — solde, payouts,
  demandes de paiement, refunds, webhooks signés.
---

# Merchant API v1 — Overview

L'**API Merchant** est l'interface server-to-server pour piloter votre compte Intram depuis votre backend : lire le solde, déclencher des payouts, demander un paiement à un client, rembourser une transaction, recevoir des notifications signées.

Elle remplace progressivement l'ancienne API `/api/v1/payments/*` pour les usages backend ; les routes existantes restent en service inchangées (rétrocompatibilité totale).

## Caractéristiques

* **Asynchrone par défaut** — les mutations renvoient `202 Accepted` avec un `operation_id` que vous pouvez suivre par polling ou webhook signé.
* **Signée** — chaque requête porte un HMAC-SHA256 de son contenu (anti-tampering + anti-replay 5 min).
* **Idempotente** — chaque POST exige un `Idempotency-Key`; les rejeux dans les 24 h renvoient la réponse d'origine sans réexécuter.
* **Sandbox / Live isolés** — les clés sandbox touchent un wallet de test, les clés live touchent l'argent réel et exigent une whitelist IP.
* **Webhooks signés** — chaque event est livré avec un en-tête `X-Intram-Signature` et retenté selon un backoff exponentiel.

## Base URL

| Environnement | URL |
| :--- | :--- |
| Production / Live | `https://api.intram.org/v1` |
| Production / Sandbox | `https://api.intram.org/v1` (clé `pk_sandbox_…`) |

Le mode (sandbox vs live) est déterminé par la **clé** présentée — pas par l'URL.

## Endpoints en un coup d'œil

| Méthode | Path | Description | Type |
| :--- | :--- | :--- | :--- |
| `GET` | `/balance` | Solde wallet courant | Synchrone |
| `POST` | `/payouts` | Reversement vers Mobile Money ou compte bancaire | Async (202) |
| `POST` | `/payment-requests` | Demande de paiement client (renvoie l'URL gateway) | Async (202) |
| `POST` | `/refunds` | Rembourse une transaction | Async (202) |
| `GET` | `/transactions/:reference` | Statut d'une transaction | Synchrone |
| `GET` | `/operations/:id` | Statut d'une opération asynchrone | Synchrone |
| `GET/POST/DELETE` | `/webhooks` | Gérer les souscriptions webhook | Synchrone |

## Par où commencer

1. Lisez l'[Authentification](authentication.md) — c'est la marche la plus haute.
2. Gardez 5 minutes pour [Sandbox vs Live](sandbox-vs-live.md) — c'est ce qui décide si votre requête passe ou pas en production.
3. Suivez le [Quickstart](quickstart.md) — un appel `curl` signé à `/balance` en moins de 2 minutes.
4. Activez les [Webhooks](webhooks.md) — c'est comme ça que vous saurez quand un payout, un remboursement ou un paiement aboutit.

{% hint style="info" %}
Une collection Postman prête à l'emploi (avec script de signature automatique) est disponible : `paycfa-webservice/docs/merchant-api.postman_collection.json` + l'environnement sandbox associé.
{% endhint %}
