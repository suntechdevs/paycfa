---
description: Historique des changements de l'API Merchant.
---

# Changelog

## v1.0 — 2026-05-20 (initial)

Première version publique du Merchant API.

### Ajouts

* `GET /balance` — solde wallet sync
* `POST /payouts` — reversements mobile money + bank wire (async)
* `POST /payment-requests` — demandes de paiement avec `gateway_url` (async)
* `POST /refunds` — refunds mobile money + Stripe (async)
* `GET /transactions/:reference` + `GET /transactions` — statut + historique
* `GET /operations/:id` + `GET /operations` — suivi des opérations asynchrones
* `GET/POST/DELETE /webhooks` + `POST /webhooks/:subscription_id/test` — gestion des souscriptions
* Webhooks sortants signés HMAC-SHA256 avec retry exponentiel (1m / 5m / 30m / 2h / 12h)
* Whitelist IP obligatoire en mode live (gérée depuis le dashboard)
* Idempotency-Key obligatoire sur toutes les mutations (TTL 24h)

### Compatibilité

Aucune modification des routes existantes `/api/v1/payments/*`, `/refund/*`, `/recharges/*`, `/widget/*`, `/webhook/*`. Le Merchant API v1 vit sous `/api/v1/merchant/*` (URL publique `api.intram.org/v1/*` après rewrite Nginx).

## Politique de versioning

* **Patch / additions non-breaking** : nouveaux endpoints, nouveaux champs en réponse, nouveaux events webhook → pas de bump de version, annoncé ici.
* **Breaking changes** : changement de signature, suppression de champ, changement de comportement → nouveau path `/v2/`, l'ancien reste maintenu **au minimum 12 mois** après l'annonce, période pendant laquelle les deux coexistent.
* Les `code` JSON d'erreur sont **stables** à l'intérieur d'une version majeure. Les `message` peuvent évoluer (traductions, reformulations).
