---
description: Gérer les souscriptions webhook depuis l'API.
---

# Souscriptions webhook

Les souscriptions définissent où et pour quels events tu reçois les notifications signées. Voir [Recevoir les webhooks](../webhooks.md) pour le format des livraisons.

## `GET /webhooks`

Liste les souscriptions du marchand.

```http
GET /v1/webhooks
X-Api-Key: …
X-Timestamp: …
X-Signature: …
```

```json
{
  "error": false,
  "data": [
    {
      "id": "65a…",
      "url": "https://my-app.example.com/intram/webhook",
      "event": "*",
      "secret_hint": "…ab12cd",
      "created_at": "2026-05-20T08:00:00.000Z"
    }
  ]
}
```

`secret_hint` est les 6 derniers caractères du secret — utile pour identifier visuellement quelle souscription a quel secret, sans révéler le secret en entier.

---

## `POST /webhooks`

Crée une nouvelle souscription. **La réponse contient le `secret` complet — affiché une seule fois.**

```http
POST /v1/webhooks
X-Api-Key: …
Idempotency-Key: webhook-create-1
Content-Type: application/json
```

### Body

```json
{
  "url": "https://my-app.example.com/intram/webhook",
  "event": "payout.*"
}
```

| Champ | Obligatoire | Règles |
| :--- | :---: | :--- |
| `url` | ✓ | Doit être en `https://` |
| `event` | ✓ | Pattern : exact (`payout.completed`), prefix wildcard (`payout.*`), catch-all (`*` / `all`) |

### Réponse `201 Created`

```json
{
  "error": false,
  "data": {
    "id": "65a…",
    "url": "https://my-app.example.com/intram/webhook",
    "event": "payout.*",
    "secret": "whsec_b4c3a2…",
    "secret_hint": "…ab12cd",
    "created_at": "2026-05-20T10:30:00.000Z"
  },
  "message": "Webhook created. Store the secret now — it will not be shown again."
}
```

{% hint style="danger" %}
Le `secret` n'est retourné qu'à la création. Stocke-le immédiatement dans un coffre-fort. Si tu le perds, supprime la souscription et recrée-la.
{% endhint %}

---

## `DELETE /webhooks/:id`

Supprime une souscription. Les livraisons en cours ne sont pas annulées (elles iront jusqu'à `exhausted` si elles échouent), mais aucune nouvelle livraison ne sera créée pour cette URL.

```http
DELETE /v1/webhooks/65a…
X-Api-Key: …
X-Timestamp: …
X-Signature: …
```

```json
{
  "error": false,
  "message": "Webhook deleted"
}
```

---

## `POST /webhooks/:id/test`

Enqueue une livraison de test (`event: test.ping`) vers l'URL configurée. Utile pour vérifier que ton handler est joignable et que ta vérification de signature fonctionne sans déclencher de vraie opération.

```http
POST /v1/webhooks/65a…/test
X-Api-Key: …
Idempotency-Key: webhook-test-1
Content-Type: application/json
```

Body : `{}` (vide).

Réponse :

```json
HTTP 202
{
  "error": false,
  "message": "Test delivery enqueued"
}
```

Payload livré (chez toi) :

```json
{
  "event": "test.ping",
  "operation_id": "op_test",
  "occurred_at": "2026-05-20T10:30:00.000Z",
  "data": { "test": true, "sent_at": "2026-05-20T10:30:00.000Z" }
}
```

## Patterns d'événements

| Pattern | Match |
| :--- | :--- |
| `payout.completed` | Exactement `payout.completed` |
| `payout.*` | `payout.completed`, `payout.failed`, `payout.queued` |
| `*` ou `all` | Tous les events |

Tu peux créer plusieurs souscriptions sur la même URL avec des patterns différents — chaque souscription est livrée indépendamment.

## Voir aussi

* [Recevoir les webhooks signés](../webhooks.md)
* [Catalogue des événements](../webhooks.md#catalogue-des-events)
