---
description: Lire le solde du wallet marchand par moyen de paiement.
---

# `GET /balance`

Renvoie le solde du wallet, ventilé par moyen de paiement, pour l'environnement de la clé présentée (sandbox vs live).

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
  "http_status": 200,
  "data": {
    "currency": "XOF",
    "payment_method_balances": [
      {
        "payment_method": "MTN Bénin",
        "available": 800000,
        "pending": 0
      },
      {
        "payment_method": "Moov Bénin",
        "available": 440000,
        "pending": 35000
      }
    ]
  }
}
```

## Champs

| Champ | Type | Description |
| :--- | :--- | :--- |
| `http_status` | number | Écho du code HTTP de la réponse |
| `currency` | string \| null | Code alphabétique de la devise principale du wallet (`XOF`, `EUR`, …) |
| `payment_method_balances[]` | array | Décomposition par moyen de paiement |
| `payment_method_balances[].payment_method` | string | Nom du moyen de paiement (`MTN Bénin`, `Moov Bénin`, `Carte Visa`, …) |
| `payment_method_balances[].available` | number | Montant disponible pour ce moyen de paiement |
| `payment_method_balances[].pending` | number | Montant en attente de settlement pour ce moyen de paiement |

## Cas limites

* **Aucun wallet provisionné** : la réponse est `200` avec `currency: null` et `payment_method_balances: []`.
* **Erreur interne** : `500` avec `code: "internal_error"`.

## Synchrone — pas d'opération créée

Contrairement aux mutations, cet endpoint n'ajoute pas de ligne dans la file des opérations et n'exige pas d'`Idempotency-Key`.

## Voir aussi

* [Statut d'une opération](operations.md) — pour suivre les payouts / refunds en cours
* [Sandbox vs Live](../sandbox-vs-live.md) — le solde renvoyé dépend de la clé utilisée
