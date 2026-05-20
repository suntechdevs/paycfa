---
description: >-
  Headers, calcul du HMAC-SHA256 et exemples Node.js / PHP / cURL pour signer
  une requête vers l'API Merchant.
---

# Authentification & signature

Chaque requête vers l'API Merchant porte trois headers obligatoires : ta clé publique, un timestamp, et une signature HMAC du contenu calculée avec ta clé secrète.

## Récupérer tes clés

* Connecte-toi sur [https://app.intram.org/login](https://app.intram.org/login)
* Menu **Développeurs → API**
* Choisis le mode `SANDBOX` (clés `pk_sandbox_…`) ou `LIVE` (clés `pk_live_…`)
* Tu obtiens 3 clés par environnement :

| Clé | Usage |
| :--- | :--- |
| Clé publique | Envoyée dans le header `X-Api-Key` — identifie ton compte |
| Clé secrète | **Ne sort jamais** — sert à calculer la signature HMAC |
| Clé privée | Réservée à l'ancienne API (`/api/v1/payments/request`) ; pas utilisée par le Merchant API v1 |

{% hint style="danger" %}
La clé secrète ne doit **jamais** apparaître dans une requête HTTP, dans le code front-end, ni dans un repo Git. Elle ne sert qu'à signer côté serveur.
{% endhint %}

## Headers obligatoires

```http
X-Api-Key:     pk_live_a1b2c3d4…
X-Timestamp:   2026-05-20T10:30:00.000Z
X-Signature:   sha256=<hex>
```

Pour les mutations (POST / PUT / PATCH), ajoute :

```http
Idempotency-Key: <UUID v4, 8 à 128 caractères [A-Za-z0-9_-]>
Content-Type:    application/json
```

* `X-Timestamp` doit être en ISO 8601 UTC. Toute requête de plus de **5 minutes** par rapport à l'heure serveur est rejetée (anti-replay).
* `Idempotency-Key` doit être unique par requête métier. Voir [Idempotency](idempotency.md).

## Construire la chaîne à signer

La signature couvre **5 champs séparés par `\n`** :

```
<timestamp>\n<METHOD>\n<path>\n<sorted_query>\n<raw_body>
```

| Champ | Règle |
| :--- | :--- |
| `timestamp` | La valeur exacte du header `X-Timestamp` |
| `METHOD` | En MAJUSCULES : `GET`, `POST`, `DELETE`… |
| `path` | Le chemin **après** le rewrite Nginx, donc `/api/v1/merchant/...` (voir note ci-dessous) |
| `sorted_query` | Paires `clé=valeur` triées alphabétiquement, jointes par `&`. Chaîne vide si pas de query |
| `raw_body` | Le body JSON **tel qu'envoyé** (pas de re-sérialisation). Chaîne vide si GET / HEAD / DELETE |

Puis HMAC-SHA256 de cette chaîne avec ta `secret_key`, encodé en hex, préfixé par `sha256=`.

{% hint style="warning" %}
**Important — le path signé n'est pas celui de l'URL publique.** Si tu appelles `https://api.intram.org/v1/payouts`, Nginx réécrit vers `/api/v1/merchant/payouts` avant de toucher le serveur. C'est **ce path-là** qui sert à la signature. Hardcode `/api/v1/merchant/<endpoint>` dans ton SDK.
{% endhint %}

## Exemple Node.js complet

{% code title="signed-request.js" %}
```javascript
const crypto = require('crypto');
const axios = require('axios');

const PUBLIC_KEY = process.env.INTRAM_PUBLIC_KEY;
const SECRET_KEY = process.env.INTRAM_SECRET_KEY;

async function signedPost(endpoint, body, idempotencyKey) {
  // path à signer (path interne, pas l'URL publique)
  const path = `/api/v1/merchant${endpoint}`;
  const rawBody = JSON.stringify(body);
  const timestamp = new Date().toISOString();

  const signingPayload = [
    timestamp,
    'POST',
    path,
    '',          // pas de query
    rawBody,
  ].join('\n');

  const signature = 'sha256=' + crypto
    .createHmac('sha256', SECRET_KEY)
    .update(signingPayload)
    .digest('hex');

  return axios.post(`https://api.intram.org/v1${endpoint}`, rawBody, {
    headers: {
      'Content-Type':    'application/json',
      'X-Api-Key':       PUBLIC_KEY,
      'X-Timestamp':     timestamp,
      'X-Signature':     signature,
      'Idempotency-Key': idempotencyKey,
    },
  });
}

// Utilisation
const { data } = await signedPost('/payouts', {
  amount: 25000,
  currency: 'XOF',
  destination: {
    type: 'mobile_money',
    provider_code: 'MTN_BENIN_229',
    msisdn: '22961234567',
  },
}, 'po-2026-0001-attempt-1');

console.log(data.data.operation_id);
```
{% endcode %}

## Exemple PHP

{% code title="signed-request.php" %}
```php
<?php
function signedPost($endpoint, array $body, string $idempotencyKey) {
    $publicKey = getenv('INTRAM_PUBLIC_KEY');
    $secretKey = getenv('INTRAM_SECRET_KEY');

    $path      = "/api/v1/merchant{$endpoint}";
    $rawBody   = json_encode($body, JSON_UNESCAPED_SLASHES);
    $timestamp = gmdate('Y-m-d\TH:i:s.000\Z');

    $signingPayload = implode("\n", [$timestamp, 'POST', $path, '', $rawBody]);
    $signature = 'sha256=' . hash_hmac('sha256', $signingPayload, $secretKey);

    $ch = curl_init("https://api.intram.org/v1{$endpoint}");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $rawBody,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "X-Api-Key: {$publicKey}",
            "X-Timestamp: {$timestamp}",
            "X-Signature: {$signature}",
            "Idempotency-Key: {$idempotencyKey}",
        ],
    ]);
    return json_decode(curl_exec($ch), true);
}
```
{% endcode %}

## Exemple cURL

{% code title="balance.sh" %}
```bash
PUB="pk_sandbox_…"
SEC="sk_sandbox_…"
TS=$(date -u +"%Y-%m-%dT%H:%M:%S.000Z")
PATH_="/api/v1/merchant/balance"

# Pour GET : body vide, query vide
PAYLOAD="$TS
GET
$PATH_

"

SIG="sha256=$(printf %s "$PAYLOAD" | openssl dgst -sha256 -hmac "$SEC" -binary | xxd -p -c 256)"

curl -i https://api.intram.org/v1/balance \
  -H "X-Api-Key: $PUB" \
  -H "X-Timestamp: $TS" \
  -H "X-Signature: $SIG"
```
{% endcode %}

## Codes d'erreur d'authentification

| Code HTTP | `code` JSON | Cause |
| :---: | :--- | :--- |
| 401 | `missing_api_key` | Header `X-Api-Key` absent |
| 401 | `invalid_api_key` | Clé inconnue ou révoquée |
| 401 | `signature_invalid` | HMAC mauvais, timestamp hors fenêtre, ou header manquant |
| 403 | `merchant_inactive` | Compte marchand non validé |
| 403 | `ip_allowlist_empty` | Clé live mais aucune IP whitelistée (cf. [Sandbox vs Live](sandbox-vs-live.md)) |
| 403 | `ip_not_allowed` | L'IP appelante n'est pas dans la whitelist live |

{% hint style="success" %}
Si tu utilises notre [collection Postman](https://github.com/intram/paycfa-webservice/blob/main/docs/merchant-api.postman_collection.json), tout ça est calculé automatiquement avant chaque envoi — tu n'as qu'à renseigner `apiKey` et `secretKey` dans l'environnement.
{% endhint %}
