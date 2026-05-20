---
description: >-
  Recevoir, vérifier et traiter les webhooks signés émis par Intram pour les
  opérations asynchrones.
---

# Recevoir les webhooks signés

Quand une opération asynchrone du Merchant API change d'état (payout terminé, transfert échoué, paiement reçu…), Intram fait un `POST` HTTP signé vers l'URL que tu as configurée. C'est le moyen recommandé de suivre l'état des opérations — bien plus efficace que du polling.

## Configurer un endpoint webhook

Via l'API (recommandé pour automatiser) :

```bash
curl -X POST https://api.intram.org/v1/webhooks \
  -H "X-Api-Key: $PUB" -H "X-Timestamp: $TS" -H "X-Signature: $SIG" \
  -H "Idempotency-Key: webhook-setup-1" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://my-app.example.com/intram/webhook",
    "event": "*"
  }'
```

Le champ `event` accepte :

| Pattern | Comportement |
| :--- | :--- |
| `payout.completed` | Match exact d'un seul event |
| `payout.*` | Tous les events `payout.completed`, `payout.failed`, `payout.queued` |
| `*` ou `all` | Tous les events |

{% hint style="warning" %}
La réponse de création contient `secret` — c'est ta **clé HMAC pour vérifier les webhooks entrants**. Elle n'est **affichée qu'une seule fois**, stocke-la dans un coffre-fort (Vault, AWS Secrets Manager, etc.). Si tu la perds, recrée le webhook.
{% endhint %}

## Headers d'un webhook entrant

Chaque livraison porte :

```http
Content-Type:        application/json
X-Intram-Signature:  sha256=<hex>
X-Intram-Timestamp:  2026-05-20T10:31:14.000Z
X-Intram-Event:      payout.completed
X-Intram-Delivery:   whd_550e8400e29b41d4a716446655440000
X-Intram-Attempt:    1
User-Agent:          Intram-Webhooks/1.0
```

* `X-Intram-Signature` — HMAC-SHA256 de `timestamp.raw_body` avec le secret du webhook
* `X-Intram-Timestamp` — ISO 8601. Rejette si > 5 minutes
* `X-Intram-Delivery` — identifiant unique de la tentative. Utilise-le pour dédupliquer côté toi en cas de double livraison
* `X-Intram-Attempt` — numéro de tentative (1 à 5)

## Body d'un webhook

```json
{
  "event": "payout.completed",
  "operation_id": "op_2f4a…",
  "occurred_at": "2026-05-20T10:31:14.000Z",
  "data": {
    "reference": "PO-2026-0001",
    "amount": 25000,
    "currency": "XOF",
    "status": "completed",
    "destination": { "type": "mobile_money", "msisdn": "22961234567" }
  }
}
```

## Vérifier la signature

{% code title="webhook-verifier.js (Node.js / Express)" %}
```javascript
const crypto = require('crypto');
const express = require('express');

const WEBHOOK_SECRET = process.env.INTRAM_WEBHOOK_SECRET;

const app = express();

// IMPORTANT : récupérer le RAW body, pas le JSON parsé
app.post('/intram/webhook',
  express.raw({ type: 'application/json' }),
  (req, res) => {
    const signature = req.headers['x-intram-signature'];
    const timestamp = req.headers['x-intram-timestamp'];
    const delivery  = req.headers['x-intram-delivery'];

    // 1. Anti-replay : refuse si > 5 minutes
    if (Math.abs(Date.now() - Date.parse(timestamp)) > 5 * 60_000) {
      return res.status(401).send('Timestamp out of window');
    }

    // 2. Recalcule la signature attendue
    const expected = 'sha256=' + crypto
      .createHmac('sha256', WEBHOOK_SECRET)
      .update(`${timestamp}.${req.body}`)
      .digest('hex');

    // 3. Comparaison timing-safe
    const a = Buffer.from(signature || '');
    const b = Buffer.from(expected);
    if (a.length !== b.length || !crypto.timingSafeEqual(a, b)) {
      return res.status(401).send('Invalid signature');
    }

    // 4. Dédup par X-Intram-Delivery (au cas où un retry passe en double)
    if (alreadySeen(delivery)) return res.status(200).send('ok');
    markSeen(delivery);

    // 5. Parse et traite
    const event = JSON.parse(req.body.toString('utf8'));
    handleEvent(event);

    // 6. Toujours répondre 2xx pour acquitter
    res.status(200).send('ok');
  }
);
```
{% endcode %}

{% hint style="danger" %}
**Si tu utilises `express.json()` globalement**, ton middleware aura déjà mangé le raw body — la signature ne matchera jamais. Il faut soit appliquer `express.raw()` spécifiquement sur la route webhook, soit conserver le raw body via une option de bodyParser.
{% endhint %}

## Politique de retry

| Tentative | Délai après la précédente |
| :---: | :--- |
| 1 | immédiat |
| 2 | 1 min |
| 3 | 5 min |
| 4 | 30 min |
| 5 | 2 h |
| (puis) | 12 h |
| Au-delà de 5 | livraison marquée `exhausted`, abandon |

* Une réponse **2xx** acquitte la livraison — pas de retry.
* Toute autre réponse (4xx, 5xx, timeout > 15 s, connexion refusée) déclenche un retry suivant le calendrier ci-dessus.
* Les retries portent un `X-Intram-Attempt` incrémenté mais le **même** `X-Intram-Delivery` — c'est ce qui te permet de dédupliquer.

## Catalogue des events

| Event | Émis quand |
| :--- | :--- |
| `payout.completed` | Le payout mobile money a été confirmé par le provider |
| `payout.queued` | Le payout bancaire a été accepté pour traitement back-office |
| `payout.failed` | Le payout a été rejeté (solde, provider, validation) |
| `transfer.completed` | Le transfert M2C wallet-to-wallet est settled |
| `transfer.failed` | Le transfert a échoué |
| `payment_request.created` | La demande de paiement est prête, `gateway_url` disponible |
| `payment_request.paid` | Le client a payé avec succès |
| `payment_request.pending` | Le client a initié mais le provider n'a pas confirmé |
| `payment_request.failed` | Le flow paiement a échoué côté client ou provider |
| `refund.completed` | Le refund a été settled (mobile money succeeded ou Stripe refund created) |
| `refund.pending` | Le refund est accepté par le provider mais pas encore settled |
| `refund.failed` | Le refund a été rejeté |
| `test.ping` | Envoi de test via `POST /webhooks/:id/test` |

## Bonnes pratiques

1. **Réponds rapidement** (sous 5 secondes). Si ton handler doit faire du travail lourd, queue-le puis renvoie 200 tout de suite.
2. **Idempotence** côté toi : un event peut arriver plusieurs fois (rare mais possible). Dédup sur `X-Intram-Delivery`.
3. **Endpoint HTTPS uniquement** — la création d'un webhook refuse les URLs `http://`.
4. **Logge les `X-Intram-Attempt > 1`** — un retry signifie que ton premier renvoi a échoué, c'est un signal de monitoring.
5. **Endpoint de test** : utilise `POST /webhooks/:id/test` pour vérifier que ton handler fonctionne sans déclencher une vraie opération.
