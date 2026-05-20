---
description: Introduction à l'intégration de l'API Intram.
---

# INTRODUCTION

Cette documentation vous accompagne dans l'intégration des solutions Intram dans vos applications web, mobiles et serveurs.

## Vue d'ensemble des APIs

Intram propose **deux interfaces** complémentaires que vous pouvez utiliser selon votre besoin :

| Interface | À utiliser pour | Documentation |
| :--- | :--- | :--- |
| **API publique de paiement** (legacy) | Construire un checkout client : pages widget, SDK JavaScript, plugins WordPress/Django/PHP | [HTTP](backend/http.md), [SDKs](api-index.md) |
| **Merchant API v1** (nouveau, recommandée pour le backend) | Piloter votre compte marchand depuis votre backend : solde, payouts, transferts, demandes de paiement, refunds, webhooks signés | [Merchant API](merchant-api/README.md) |

Les deux coexistent sans interférer. Choisissez la première si vous voulez encaisser un paiement client ; la seconde si vous voulez automatiser la gestion de votre compte depuis vos propres serveurs.

## Pré-requis

Avant toute intégration vous avez besoin :

1. **D'un compte Intram validé** — [inscrivez-vous](https://app.intram.org/register) puis suivez la procédure d'[activation du compte marchand](account/account-activation.md).
2. **D'au moins un compte business** validé (Paramètres → Compte business).
3. **De vos clés API** — générées automatiquement pour chaque environnement (sandbox + live).

## Récupérer vos clés API

1. **Connectez-vous** à [https://app.intram.org/login](https://app.intram.org/login) avec vos identifiants marchands.
2. Dans le menu de gauche, cliquez sur **Développeurs**.
3. Cliquez ensuite sur **API**.
4. Le tableau de bord affiche trois clés, par environnement (`SANDBOX` ou `LIVE`) :
   * **Clé API publique** (`public_key`) — identifiant non secret de votre compte
   * **Clé API privée** (`private_key`) — utilisée par les anciennes intégrations SDK
   * **Clé API secrète** (`secret_key`) — **ne sort jamais** ; utilisée pour signer les requêtes Merchant API et vérifier les webhooks

{% hint style="info" %}
Commencez toujours par développer en `SANDBOX`. Les transactions sandbox ne touchent pas l'argent réel et permettent de tester librement vos flows. Passez en `LIVE` quand votre intégration est stable.
{% endhint %}

{% hint style="danger" %}
Ne partagez jamais vos clés `private_key` et `secret_key` — pas dans un dépôt Git public, pas dans le code front-end, pas dans les logs. Si une clé est compromise, régénérez-la immédiatement depuis le dashboard.
{% endhint %}

## Par où commencer

| Vous voulez… | Direction |
| :--- | :--- |
| Créer un compte | [Inscription](account/register.md) → [Activation](account/account-activation.md) |
| Encaisser des paiements depuis votre site (page checkout) | [SDKs frontend / backend](api-index.md) ou [HTTP direct](backend/http.md) |
| Piloter votre compte (payouts, balance, transferts) depuis votre backend | [Merchant API v1 — Quickstart](merchant-api/quickstart.md) |
| Encaisser des paiements depuis votre app mobile | [Flutter](mobile/flutter.md) |
| Comprendre les frais appliqués | [Frais](payment/fees.md) |
| Connaître les devises et moyens de paiement supportés | [Devises supportées](payment/supported-devices.md) |
