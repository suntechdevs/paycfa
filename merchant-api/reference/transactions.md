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
* L'identifiant interne Mongo (24 hex)

### Réponse `200 OK`

```json
{
  "error": false,
  "http_status": 200,
  "data": {
    "reference": "AB12CD34EF",
    "status": "SUCCESS",
    "type": "CREDIT",
    "amount": 12000,
    "fees": 240,
    "net_amount": 11760,
    "currency": "XOF",
    "payment_method": "MTN Bénin",
    "customer": {
      "email": "client@example.com",
      "name": "Ada Lovelace"
    },
    "source": {
      "number": "22961234567"
    },
    "date": "2026-05-20T10:30:00.000Z",
    "refunds": [
      {
        "reference": "9DEFGH1234",
        "status": "SUCCESS",
        "reason": "Demande client",
        "date": "2026-05-20T11:45:00.000Z"
      }
    ]
  }
}
```

### Champs

| Champ | Type | Description |
| :--- | :--- | :--- |
| `http_status` | number | Écho du code HTTP de la réponse |
| `reference` | string | Référence publique de la transaction |
| `status` | string | Statut courant (voir tableau ci-dessous) |
| `type` | string | `CREDIT`, `DEBIT` ou `REFUND` |
| `amount` | number | Montant brut |
| `fees` | number | Frais appliqués |
| `net_amount` | number | Montant net après frais |
| `currency` | string \| null | Code alphabétique de la devise (`XOF`, `EUR`, …) |
| `payment_method` | string \| null | Nom du moyen de paiement (`MTN Bénin`, `Moov Bénin`, `Carte Visa`, …) |
| `customer` | object | Coordonnées du client (`email`, `name`) — objet vide si non renseigné |
| `source.number` | string \| null | Numéro source de l'opération (mobile money), s'il est connu |
| `date` | string (ISO 8601) | Date de création de la transaction |
| `refunds[]` | array | Liste des remboursements liés (vide si aucun) |
| `refunds[].reference` | string | Référence publique du remboursement |
| `refunds[].status` | string | Statut du remboursement |
| `refunds[].reason` | string \| null | Motif du remboursement |
| `refunds[].date` | string (ISO 8601) | Date du remboursement |

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
  "http_status": 200,
  "data": [ /* tableau de transactions, format identique au get unitaire */ ],
  "paging": {
    "limit": 20,
    "next_before": "2026-05-19T12:34:56.000Z"
  }
}
```

`next_before` est `null` quand il n'y a plus de page suivante. Sinon, passez-le tel quel en `before` au prochain appel.

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
