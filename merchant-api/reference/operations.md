---
description: Suivre l'avancement et le résultat d'une opération asynchrone.
---

# Opérations

Toutes les mutations asynchrones (`/payouts`, `/payment-requests`, `/refunds`) renvoient un `operation_id` que vous pouvez suivre via ces endpoints.

{% hint style="info" %}
**Préfère les webhooks** au polling. Le polling est fait pour le fallback (en dev local, en debug, ou quand ton endpoint webhook est temporairement indisponible).
{% endhint %}

## `GET /operations/:id`

### Requête

```http
GET /v1/operations/op_2f4a8b1c...
X-Api-Key: …
X-Timestamp: …
X-Signature: …
```

### Réponse `200 OK`

```json
{
  "error": false,
  "http_status": 200,
  "data": {
    "operation_id": "op_2f4a8b1c...",
    "type": "payout",
    "status": "succeeded",
    "env": "live",
    "payload": { /* body envoyé à l'origine */ },
    "result": {
      "reference": "PO-2026-0001",
      "amount": 25000,
      "currency": "XOF",
      "status": "completed"
    },
    "error": null,
    "attempts": 1,
    "date": "2026-05-20T10:30:00.000Z",
    "updated_date": "2026-05-20T10:30:14.000Z",
    "completed_date": "2026-05-20T10:30:14.000Z",
    "_links": { "self": "/api/v1/merchant/operations/op_2f4a8b1c..." }
  }
}
```

### Statuts

| `status` | Sens |
| :--- | :--- |
| `queued` | En file d'attente, pas encore traitée |
| `processing` | Worker actif |
| `succeeded` | Terminée — voir `result` |
| `failed` | Échouée — voir `error.code` et `error.message` |
| `expired` | (réservé pour TTL) |

### Quand `status === "failed"`

```json
{
  "status": "failed",
  "result": null,
  "error": {
    "code": "insufficient_balance",
    "message": "Insufficient merchant balance",
    "details": null
  }
}
```

Les codes d'erreur métier sont listés dans le [catalogue d'erreurs](../errors.md#métier-réponses-200-avec-opération-failed).

---

## `GET /operations`

Liste paginée des opérations du marchand.

### Query params

| Param | Description |
| :--- | :--- |
| `limit` | 1-100, défaut 20 |
| `type` | `payout`, `payment_request`, `refund` |
| `status` | `queued`, `processing`, `succeeded`, `failed`, `expired` |
| `before` | ISO 8601 — curseur de pagination |

### Exemple : tous les payouts échoués des 7 derniers jours

```bash
# Le filtrage par date passe par `before` (curseur). Pour une fenêtre,
# itère depuis maintenant et coupe quand date < now - 7j.
GET /v1/operations?type=payout&status=failed&limit=100
```

### Réponse

```json
{
  "error": false,
  "http_status": 200,
  "data": [ /* tableau d'opérations */ ],
  "paging": { "limit": 20, "next_before": "2026-05-19T12:34:56.000Z" }
}
```

## Pattern de polling

Si tu ne peux pas mettre en place de webhook (ex: dev local), poll avec un backoff :

```javascript
async function waitForOperation(opId, { maxMs = 60000, intervalMs = 1000 } = {}) {
  const deadline = Date.now() + maxMs;
  while (Date.now() < deadline) {
    const op = await signedGet(`/operations/${opId}`);
    if (op.data.status === 'succeeded' || op.data.status === 'failed') return op.data;
    await new Promise(r => setTimeout(r, intervalMs));
    intervalMs = Math.min(intervalMs * 1.5, 10000);
  }
  throw new Error(`Timeout waiting for ${opId}`);
}
```

## Voir aussi

* [Webhooks](../webhooks.md) — l'alternative push, recommandée
* [Catalogue d'erreurs](../errors.md)
