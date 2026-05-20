---
description: Lire le solde live ou sandbox du wallet marchand.
---

# `GET /balance`

Renvoie le solde du wallet pour l'environnement de la clé présentée (sandbox vs live).

## Requête

```http
GET /v1/balance
X-Api-Key:    pk_live_…
X-Timestamp:  2026-05-20T10:30:00.000Z
X-Signature:  sha256=…
```

Aucun body. Aucun paramètre de query.

## Réponse `200 OK`

```json
{
  "error": false,
  "data": {
    "merchant_id": "65a1b2c3d4e5f6a7b8c9d0e1",
    "env": "live",
    "currency": {
      "code": "XOF",
      "numeric_code": "952",
      "symbol": "FCFA"
    },
    "available": 1240000,
    "pending": 35000,
    "payment_method_balances": [
      {
        "payment_method": "MTN_BENIN_229",
        "payment_method_id": "65a…",
        "available": 800000,
        "pending": 0
      }
    ],
    "as_of": "2026-05-20T10:30:00.123Z"
  }
}
```

## Champs

| Champ | Type | Description |
| :--- | :--- | :--- |
| `merchant_id` | string | ID Mongo du marchand |
| `env` | string | `sandbox` ou `live`, dérivé de la clé |
| `currency` | object \| null | Devise principale du wallet |
| `available` | number | Montant disponible (déjà settled) |
| `pending` | number | Montant en attente de settlement |
| `payment_method_balances[]` | array | Décomposition par moyen de paiement |
| `as_of` | string (ISO 8601) | Horodatage de la lecture |

## Cas limites

* **Aucun wallet provisionné** : la réponse est 200 mais `currency=null`, `available=0`, `payment_method_balances=[]`, avec un champ `message: "No wallet provisioned"`.
* **Erreur Mongo** : 500 avec `code: "internal_error"`.

## Synchrone — pas d'opération créée

Contrairement aux mutations, cet endpoint n'ajoute pas de ligne dans la collection `merchant_operations` et n'exige pas d'`Idempotency-Key`.

## Voir aussi

* [Statut d'une opération](operations.md) — pour suivre les payouts/transferts en cours qui pourraient bouger le solde
* [Sandbox vs Live](../sandbox-vs-live.md) — le solde renvoyé dépend de la clé utilisée
