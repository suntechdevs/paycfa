---
description: >-
  Transférer des fonds du wallet marchand vers le wallet d'un client Intram
  (M2C, wallet-to-wallet interne).
---

# `POST /transfers`

Crée un transfert wallet marchand → wallet client (M2C). Reste à l'intérieur de l'écosystème Intram — pas d'appel provider externe, donc instantané.

## Comportement

1. Validation des limites min/max pour la devise.
2. Débit du wallet marchand de `amount + fees`.
3. Crédit du wallet client de `amount` (création du wallet client si absent).
4. Création d'un document `Transfer` + écriture des frais dans le wallet Intram.
5. Émission de l'événement `transfer.completed` (ou `transfer.failed`).

## Requête

```http
POST /v1/transfers
X-Api-Key: …
X-Timestamp: …
X-Signature: …
Idempotency-Key: tr-2026-0001-attempt-1
Content-Type: application/json
```

### Body

```json
{
  "amount": 5000,
  "recipient": { "type": "customer_email", "value": "client@example.com" },
  "reason": "Remboursement commande #4521",
  "payment_method_id": "65a…",
  "metadata": { "order_id": "4521" }
}
```

### Recipient

| `recipient.type` | `recipient.value` | Notes |
| :--- | :--- | :--- |
| `customer_email` | Email du client | Le compte client doit déjà exister sur Intram |
| `customer_id` | Mongo `_id` du User | Plus rapide si tu as déjà l'ID |
| `merchant_id` | Mongo `_id` du Marchand | (M2M, support partiel — voir équipe) |

### Validation

| Champ | Obligatoire | Règles |
| :--- | :---: | :--- |
| `amount` | ✓ | Number > 0, dans `[min_transfert, max_transfert]` pour la devise |
| `recipient.type` | ✓ | Voir tableau ci-dessus |
| `recipient.value` | ✓ | String non-vide |
| `reason` | optionnel | Max 500 chars |
| `payment_method_id` | optionnel | ID Mongo d'un MeansPaiement |
| `metadata` | optionnel | Object libre |

## Réponse `202 Accepted`

```json
{
  "error": false,
  "data": {
    "operation_id": "op_3e5b…",
    "type": "transfer",
    "status": "queued",
    "env": "live",
    "_links": { "self": "/api/v1/merchant/operations/op_3e5b…" }
  }
}
```

## Résultat final

```json
{
  "reference": "5VKQ81AOO2",
  "amount": 5000,
  "fees": 100,
  "currency": "XOF",
  "received_amount": 5000,
  "transfer_id": "65a…"
}
```

## Événements webhook

| Événement | Quand |
| :--- | :--- |
| `transfer.completed` | Settled avec succès |
| `transfer.failed` | Validation, solde insuffisant ou recipient inconnu |

## Erreurs

| Code | Cas |
| :--- | :--- |
| `validation_error` | Body invalide |
| `recipient_not_found` | Email/ID du recipient inconnu |
| `amount_too_small` | < minimum pour la devise |
| `amount_too_large` | > maximum pour la devise |
| `insufficient_balance` | Solde marchand insuffisant pour `amount + fees` |

## Voir aussi

* [Statut d'un transfert](transfers-status.md) — lire un transfert existant
* [Payouts](payouts.md) — pour reverser vers l'extérieur (MoMo, banque) plutôt qu'entre wallets internes
