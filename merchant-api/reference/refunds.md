---
description: Rembourser tout ou partie d'une transaction.
---

# `POST /refunds`

Lance un refund sur une transaction existante du marchand. Le worker route automatiquement vers le provider d'origine (MTN/MOOV/SBIN pour mobile money, Stripe pour bank/card).

## Comportement

| Type d'origine | Action |
| :--- | :--- |
| Mobile money | Appel `requestRefund` du provider correspondant. Si succès → débit du wallet marchand + transaction marquée `REFUNDED`. |
| Bank / Card (Stripe) | Si transaction `PENDING` → `paymentIntents.cancel`. Si `SUCCESS` → `refunds.create`. |

## Requête

```http
POST /v1/refunds
X-Api-Key: …
X-Timestamp: …
X-Signature: …
Idempotency-Key: refund-AB12CD34EF-full
Content-Type: application/json
```

### Body

```json
{
  "transaction_reference": "AB12CD34EF",
  "amount": 12000,
  "reason": "Demande client",
  "metadata": { "ticket_id": "T-9001" }
}
```

| Champ | Obligatoire | Règles |
| :--- | :---: | :--- |
| `transaction_reference` | ✓ | Référence publique de la transaction |
| `amount` | optionnel | Défaut = montant total. Doit être ≤ montant original. Refund partiel selon le support du provider |
| `reason` | optionnel | Max 500 chars |
| `metadata` | optionnel | Object libre |

## Pré-validation synchrone

Avant d'enregistrer l'opération, l'endpoint vérifie que la transaction existe et appartient au marchand. Si non : **404 immédiat**, pas d'opération créée.

```json
HTTP 404
{
  "error": true,
  "http_status": 404,
  "code": "transaction_not_found",
  "message": "Transaction not found for this merchant"
}
```

## Réponse `202 Accepted`

```json
{
  "error": false,
  "http_status": 202,
  "data": {
    "operation_id": "op_6f7c…",
    "type": "refund",
    "status": "queued",
    "env": "live",
    "date": "2026-05-20T10:30:00.000Z"
  }
}
```

## Résultat final

```json
{
  "status": "completed",
  "refund_reference": "9DEFGH1234",
  "amount": 12000,
  "transaction_reference": "AB12CD34EF",
  "provider_response": { "responsecode": "00", "responsemsg": "Success" }
}
```

`status` ∈ `completed` (settled), `pending` (mobile money attend la confirmation), ou implicite `failed` via le champ `error` au niveau opération.

## Événements webhook

| Événement | Quand |
| :--- | :--- |
| `refund.completed` | Settled (provider success ou Stripe refund créé) |
| `refund.pending` | Mobile money en attente de confirmation provider |
| `refund.failed` | Rejeté, non-refundable, ou erreur réseau |

## Effets de bord

* La transaction d'origine passe en `_status: REFUNDED` (et `_refunded_at` est rempli).
* Le wallet marchand est débité du montant du refund (en plus des frais éventuels du provider).
* Le refund apparaît dans `GET /transactions/:reference` sous le champ `refunds[]`.

## Erreurs

| Code | Cas |
| :--- | :--- |
| `validation_error` | Body invalide |
| `transaction_not_found` | Référence inconnue ou pas à ce marchand (404 sync) |
| `transaction_not_settled` | La transaction n'a pas encore de moyen de paiement assigné |
| `not_refundable` | Statut hors `SUCCESS`/`PENDING` (déjà refunded, échouée, etc.) |
| `amount_exceeds_transaction` | `amount` > montant original |
| `insufficient_balance` | Wallet marchand insuffisant pour couvrir le refund |
| `stripe_unavailable` | SDK Stripe non configuré côté serveur (cas bank) |
| `unsupported_payment_method` | Le moyen de paiement n'est ni Mobile ni Banque |
| `provider_rejected` | Provider a refusé le refund |

## Voir aussi

* [Statut transaction](transactions.md) pour voir l'historique des refunds appliqués
