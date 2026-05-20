---
description: >-
  Reverser des fonds depuis le wallet marchand vers un compte Mobile Money ou
  un compte bancaire.
---

# `POST /payouts`

Initie un payout (reversement). Renvoie immédiatement `202` avec un `operation_id` ; le résultat final arrive par [webhook](../webhooks.md) ou peut être pollé via `GET /operations/:id`.

## Comportement

| Destination | Flow |
| :--- | :--- |
| `mobile_money` | Le wallet est débité, le provider est appelé. Si succès → webhook `payout.completed`. Si rejet → wallet recrédité, webhook `payout.failed`. |
| `bank_account` | Le wallet est débité, un Reversement est créé en `queued_for_settlement`, traité ensuite par le back-office. Webhook `payout.queued`. |

## Requête

```http
POST /v1/payouts
X-Api-Key: …
X-Timestamp: …
X-Signature: …
Idempotency-Key: po-2026-0001-attempt-1
Content-Type: application/json
```

### Body — Mobile Money

```json
{
  "amount": 25000,
  "currency": "XOF",
  "destination": {
    "type": "mobile_money",
    "country_code": "BJ",
    "provider_code": "MTN_BENIN_229",
    "msisdn": "22961234567",
    "account_name": "Ada",
    "account_surname": "Lovelace"
  },
  "reference": "PO-2026-0001",
  "note": "Settlement hebdomadaire",
  "metadata": { "batch_id": "B-42" }
}
```

### Body — Bank wire

```json
{
  "amount": 500000,
  "currency": "XOF",
  "destination": {
    "type": "bank_account",
    "swift": "ECOCBJBJ",
    "account_number": "BJ06...",
    "account_name": "Ada Lovelace"
  },
  "reference": "PO-2026-WIRE-001"
}
```

## Validation des champs

| Champ | Obligatoire | Règles |
| :--- | :---: | :--- |
| `amount` | ✓ | Number > 0 |
| `currency` | optionnel | Code ISO 3 lettres (`XOF`, `EUR`…) |
| `destination.type` | ✓ | `mobile_money` ou `bank_account` |
| `destination.country_code` | mobile_money | 2 lettres ISO, défaut `BJ` |
| `destination.provider_code` | mobile_money | Ex : `MTN_BENIN_229`, `MOOV_AFRICA_BENIN_229`, `SBIN_BENIN_229` |
| `destination.msisdn` | mobile_money | Numéro international sans `+` |
| `destination.account_number` | bank_account | Format bancaire local ou IBAN |
| `destination.swift` | bank_account | Code SWIFT/BIC |
| `destination.account_name` | optionnel | Prénom du bénéficiaire |
| `destination.account_surname` | optionnel | Nom du bénéficiaire |
| `reference` | optionnel | Référence interne, max 100 chars |
| `note` | optionnel | Texte libre, max 500 chars |
| `metadata` | optionnel | Object libre transmis dans le webhook |

## Réponse `202 Accepted`

```json
{
  "error": false,
  "data": {
    "operation_id": "op_2f4a8b1c…",
    "type": "payout",
    "status": "queued",
    "env": "live",
    "payload": { /* le body que tu as envoyé */ },
    "created_at": "2026-05-20T10:30:00.000Z",
    "_links": { "self": "/api/v1/merchant/operations/op_2f4a8b1c…" }
  }
}
```

## Résultat final (via webhook ou polling)

Quand l'opération est terminée, `op.result` ressemble à :

```json
{
  "reference": "PO-2026-0001",
  "amount": 25000,
  "currency": "XOF",
  "destination": { "type": "mobile_money", "msisdn": "22961234567" },
  "reversement_id": "65a…",
  "status": "completed",
  "provider_response": {
    "responsecode": "00",
    "responsemsg": "Success",
    "serviceref": "MTN987654321"
  }
}
```

## Événements webhook émis

| Événement | Quand |
| :--- | :--- |
| `payout.completed` | Mobile money settled avec succès |
| `payout.queued` | Bank wire accepté pour traitement back-office |
| `payout.failed` | Provider a rejeté, solde insuffisant, ou erreur réseau |

## Erreurs

| Code | HTTP | Cas |
| :--- | :---: | :--- |
| `validation_error` | 400 | `amount`, `destination.type`, etc. invalides |
| `insufficient_balance` | 200 (worker) | Solde marchand < montant demandé |
| `provider_rejected` | 200 (worker) | Le provider MoMo a refusé — wallet recrédité |
| `provider_unavailable` | 200 (worker) | Aucun handler pour ce `country_code`/`provider_code` |
| `missing_provider_code` | 200 (worker) | `destination.provider_code` absent pour mobile_money |

## Voir aussi

* [Statut d'une opération](operations.md)
* [Webhooks](../webhooks.md) pour la livraison push du résultat
* [Idempotency](../idempotency.md) — comment retenter sûrement un payout
