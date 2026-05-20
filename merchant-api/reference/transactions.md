---
description: Lire le statut d'une transaction du marchand.
---

# Statut de transactions

## `GET /transactions/:reference`

Renvoie les détails d'une transaction du marchand authentifié.

### Requête

```http
GET /v1/transactions/AB12CD34EF
X-Api-Key: …
X-Timestamp: …
X-Signature: …
```

`:reference` accepte :

* Le `_reference` public (10 chars alphanumériques)
* Le Mongo `_id` (24 hex)

### Réponse `200 OK`

```json
{
  "error": false,
  "data": {
    "reference": "AB12CD34EF",
    "service_reference": "MTN987654321",
    "id": "67e9…",
    "status": "SUCCESS",
    "type": "CREDIT",
    "env": "live",
    "amount": 12000,
    "fees": 240,
    "net_amount": 11760,
    "currency": "XOF",
    "payment_method": {
      "code": "MTN_BENIN_229",
      "type": "Mobile",
      "name": "MTN Bénin"
    },
    "customer": {
      "id": "65a…",
      "email": "client@example.com",
      "first_name": "Ada",
      "last_name": "Lovelace"
    },
    "source_msisdn": "22961234567",
    "qr_code": "data:image/png;base64,…",
    "declined_reason": null,
    "refunded_at": null,
    "webhook_data": { "order_id": "4521" },
    "created_at": "2026-05-20T10:30:00.000Z",
    "refunds": [
      {
        "reference": "9DEFGH1234",
        "service_reference": "MTN98...REFUND",
        "status": "SUCCESS",
        "reason": "Demande client",
        "created_at": "2026-05-20T11:45:00.000Z"
      }
    ]
  }
}
```

### Statuts possibles

| `status` | Sens |
| :--- | :--- |
| `PENDING` | Créée, en attente du paiement client / provider |
| `SUCCESS` | Settled |
| `ERROR` | Échouée |
| `REFUNDED` | Remboursée (totalement) |

### Erreurs

| Code | HTTP | Cas |
| :--- | :---: | :--- |
| `transaction_not_found` | 404 | Référence inconnue ou pas à ce marchand |

---

## `GET /transactions`

Liste paginée des transactions du marchand, triée par création décroissante.

### Query params

| Param | Type | Description |
| :--- | :--- | :--- |
| `limit` | int | 1-100, défaut 20 |
| `status` | string | Filtre exact : `PENDING`, `SUCCESS`, `ERROR`, `REFUNDED` |
| `type` | string | Filtre exact : `CREDIT`, `DEBIT`, `REFUND` |
| `before` | ISO 8601 | Curseur — renvoie les éléments créés strictement avant cette date |

### Réponse

```json
{
  "error": false,
  "data": [ /* tableau de transactions, format identique au get unitaire */ ],
  "paging": {
    "limit": 20,
    "next_before": "2026-05-19T12:34:56.000Z"
  }
}
```

`next_before` est `null` quand il n'y a plus de page suivante. Sinon, passe-le tel quel en `before` au prochain appel.

### Pagination type

```javascript
let cursor = null;
const all = [];
do {
  const url = `/transactions?limit=100${cursor ? `&before=${encodeURIComponent(cursor)}` : ''}`;
  const r = await signedGet(url);
  all.push(...r.data);
  cursor = r.paging.next_before;
} while (cursor);
```

## Voir aussi

* [Refunds](refunds.md) pour rembourser une transaction
* [Payment requests](payment-requests.md) pour créer une nouvelle transaction
