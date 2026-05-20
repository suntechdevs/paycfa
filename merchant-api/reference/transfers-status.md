---
description: Lire le statut d'un transfert où le marchand est sender ou receiver.
---

# Statut de transferts

## `GET /transfers/:reference`

### Requête

```http
GET /v1/transfers/5VKQ81AOO2
X-Api-Key: …
X-Timestamp: …
X-Signature: …
```

`:reference` accepte le `_reference` public (10 chars) ou le Mongo `_id`.

### Réponse `200 OK`

```json
{
  "error": false,
  "data": {
    "reference": "5VKQ81AOO2",
    "id": "67e9…",
    "type": "MARCHAND_CUSTOMER",
    "amount": 5000,
    "fees": 100,
    "received_amount": 5000,
    "conversion_rate": 1,
    "wallet_currency": "XOF",
    "source_currency": "XOF",
    "sender": {
      "marchand_id": "65a…",
      "user_id": null
    },
    "receiver": {
      "marchand_id": null,
      "user_id": "65b…",
      "user_email": "client@example.com"
    },
    "reason": "Remboursement commande #4521",
    "created_at": "2026-05-20T10:31:00.000Z"
  }
}
```

Le marchand authentifié doit être soit sender soit receiver — sinon `404 transfer_not_found`.

---

## `GET /transfers`

Liste paginée des transferts du marchand.

### Query params

| Param | Type | Description |
| :--- | :--- | :--- |
| `limit` | int | 1-100, défaut 20 |
| `direction` | string | `sent` (initiés par moi), `received` (crédités à moi). Omis → les deux |
| `before` | ISO 8601 | Curseur de pagination |

### Réponse

```json
{
  "error": false,
  "data": [ /* tableau de transferts */ ],
  "paging": { "limit": 20, "next_before": "2026-05-19T12:34:56.000Z" }
}
```

## Types

| `type` | Sens |
| :--- | :--- |
| `MARCHAND_CUSTOMER` | M2C — marchand vers wallet client (cas typique des `POST /transfers`) |
| `CUSTOMER_CUSTOMER` | C2C — entre clients |
| `CUSTOMER_MARCHAND` | C2M — d'un client vers un marchand |
| `MARCHAND_MARCHAND` | M2M — entre marchands |

## Voir aussi

* [Créer un transfert](transfers.md)
