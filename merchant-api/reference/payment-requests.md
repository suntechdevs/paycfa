---
description: >-
  Demander un paiement à un client. Renvoie une URL de checkout (gateway_url)
  à partager au client.
---

# `POST /payment-requests`

Crée une demande de paiement adressée à un client. Le worker initialise une Transaction + une Invoice, puis renvoie l'URL du gateway de paiement Intram à présenter au client.

## Flow complet

```
1. POST /payment-requests         → 202 + operation_id
2. Worker crée Transaction PENDING
3. Webhook payment_request.created → result.gateway_url disponible
4. Tu redirigeas le client vers gateway_url (ou tu lui envoies par email/SMS)
5. Le client paie via le gateway
6. Webhook payment_request.paid|pending|failed selon le résultat
```

## Requête

```http
POST /v1/payment-requests
X-Api-Key: …
X-Timestamp: …
X-Signature: …
Idempotency-Key: pr-order-4521-attempt-1
Content-Type: application/json
```

### Body

```json
{
  "invoice": {
    "amount": 12000,
    "currency": "XOF",
    "description": "Commande #4521",
    "customer": {
      "email": "client@example.com",
      "first_name": "Ada",
      "last_name": "Lovelace",
      "phone": "22961234567"
    }
  },
  "payment_method": {
    "code": "MTN_BENIN_229",
    "country_code": "BJ",
    "msisdn": "22961234567"
  },
  "return_urls": {
    "success": "https://shop.example.com/ok",
    "cancel":  "https://shop.example.com/no"
  },
  "webhook_data": { "order_id": "4521" }
}
```

### Validation

| Champ | Obligatoire | Règles |
| :--- | :---: | :--- |
| `invoice.amount` | ✓ | Number > 0, dans les limites min/max de la devise |
| `invoice.currency` | ✓ | Code ISO 3 lettres |
| `invoice.description` | optionnel | Max 500 chars |
| `invoice.customer` | ✓ | Object |
| `invoice.customer.email` | optionnel | Email valide |
| `invoice.customer.phone` | optionnel | String |
| `payment_method` | optionnel | Suggère un moyen de paiement par défaut au client |
| `return_urls.success` | optionnel | URL où rediriger après succès |
| `return_urls.cancel` | optionnel | URL où rediriger après annulation |
| `webhook_data` | optionnel | Object libre transmis dans les webhooks |

## Réponse `202 Accepted`

```json
{
  "error": false,
  "data": {
    "operation_id": "op_4d2a…",
    "type": "payment_request",
    "status": "queued",
    "env": "live"
  }
}
```

## Résultat final (op.result après `payment_request.created`)

```json
{
  "transaction_reference": "AB12CD34EF",
  "transaction_id": "67e9…",
  "status": "pending",
  "amount": 12000,
  "currency": "XOF",
  "gateway_url":  "https://gateway.intram.org/67e9…",
  "payment_url":  "https://gateway.intram.org/67e9…",
  "receipt_url":  "https://gateway.intram.org/67e9…",
  "qr_code": "data:image/png;base64,iVBOR…"
}
```

| Champ | Description |
| :--- | :--- |
| `gateway_url` | URL canonique du checkout — à présenter au client |
| `payment_url` | Alias de `gateway_url` (compat) |
| `receipt_url` | URL de la facture imprimable une fois settled |
| `qr_code` | Data-URL PNG du QR pointant vers `gateway_url` |

## Événements webhook

| Événement | Quand |
| :--- | :--- |
| `payment_request.created` | Worker a créé la transaction → `gateway_url` disponible |
| `payment_request.paid` | Le client a payé avec succès |
| `payment_request.pending` | Le client a initié mais le provider n'a pas encore confirmé |
| `payment_request.failed` | Échec côté client / provider / timeout |

## Cas d'usage typiques

### E-commerce — checkout immédiat

```javascript
// 1. Ton client clique "Payer"
const { data: op } = await intram.paymentRequests.create({...});

// 2. Tu attends le webhook payment_request.created (ou polles ~1s)
const final = await waitForOperation(op.data.operation_id);

// 3. Tu rediriges
res.redirect(final.result.gateway_url);
```

### Facture par email

```javascript
const final = await waitForOperation(op.data.operation_id);
await sendEmail(customerEmail, {
  subject: 'Votre facture',
  body: `Payer ici : ${final.result.gateway_url}`,
});
```

### QR à scanner en boutique

```javascript
const final = await waitForOperation(op.data.operation_id);
display(final.result.qr_code);   // affichage sur écran caisse / impression
```

## Erreurs

| Code | Cas |
| :--- | :--- |
| `validation_error` | Body invalide |
| `unknown_currency` | Devise non supportée |
| `amount_too_small` / `amount_too_large` | Hors des limites min/max |
| `missing_invoice` | Le body n'a pas de bloc `invoice` |

## Voir aussi

* [Statut transaction](transactions.md) — lire le statut d'une transaction créée par cet endpoint
* [Webhooks](../webhooks.md) — recevoir le résultat final en push
