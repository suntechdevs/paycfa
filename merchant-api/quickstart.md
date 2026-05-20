---
description: >-
  Faire ton premier appel signé à l'API Merchant en moins de 5 minutes.
---

# Quickstart

L'objectif : lire le solde de ton wallet sandbox avec un appel `curl` signé. Si ce premier ping marche, ton intégration est sur de bons rails.

## Prérequis

* Compte Intram avec accès au dashboard
* Clés sandbox (Menu **Développeurs → API → mode SANDBOX**)
* `curl` + `openssl` installés (ou utilise notre [collection Postman](https://github.com/intram/paycfa-webservice/blob/main/docs/merchant-api.postman_collection.json))

## 1. Récupère tes clés sandbox

```
X-Api-Key      = pk_sandbox_a1b2c3d4...
secret_key     = sk_sandbox_e5f6g7h8...   (ne sort jamais en clair)
```

## 2. Signe et appelle `/balance`

{% code title="quickstart.sh" %}
```bash
#!/usr/bin/env bash
set -euo pipefail

PUB="pk_sandbox_a1b2c3d4..."
SEC="sk_sandbox_e5f6g7h8..."
HOST="https://api.intram.org/v1"

# Path interne (après rewrite Nginx) — c'est lui qui sert à la signature
SIGN_PATH="/api/v1/merchant/balance"

# Timestamp ISO 8601 UTC
TS=$(date -u +"%Y-%m-%dT%H:%M:%S.000Z")

# Payload : timestamp\nMETHOD\nPATH\nQUERY\nBODY
PAYLOAD="$TS
GET
$SIGN_PATH

"

# HMAC-SHA256 hex
SIG="sha256=$(printf %s "$PAYLOAD" | openssl dgst -sha256 -hmac "$SEC" -binary | xxd -p -c 256)"

# Appel
curl -i "$HOST/balance" \
  -H "X-Api-Key: $PUB" \
  -H "X-Timestamp: $TS" \
  -H "X-Signature: $SIG"
```
{% endcode %}

Lance-le :

```bash
chmod +x quickstart.sh && ./quickstart.sh
```

Réponse attendue :

```json
HTTP/2 200
{
  "error": false,
  "data": {
    "merchant_id": "65a...",
    "env": "sandbox",
    "currency": { "code": "XOF", "symbol": "FCFA" },
    "available": 10000,
    "pending": 0,
    "payment_method_balances": [],
    "as_of": "2026-05-20T10:30:00.123Z"
  }
}
```

## 3. Déclenche un payout sandbox

```bash
PAYLOAD_BODY='{
  "amount": 5000,
  "currency": "XOF",
  "destination": {
    "type": "mobile_money",
    "country_code": "BJ",
    "provider_code": "MTN_BENIN_229",
    "msisdn": "22961234567"
  },
  "reference": "TEST-PO-1"
}'

SIGN_PATH="/api/v1/merchant/payouts"
TS=$(date -u +"%Y-%m-%dT%H:%M:%S.000Z")
PAYLOAD="$TS
POST
$SIGN_PATH

$PAYLOAD_BODY"
SIG="sha256=$(printf %s "$PAYLOAD" | openssl dgst -sha256 -hmac "$SEC" -binary | xxd -p -c 256)"

curl -i "$HOST/payouts" \
  -H "X-Api-Key: $PUB" \
  -H "X-Timestamp: $TS" \
  -H "X-Signature: $SIG" \
  -H "Idempotency-Key: test-po-1-attempt-1" \
  -H "Content-Type: application/json" \
  -d "$PAYLOAD_BODY"
```

Réponse :

```json
HTTP/2 202
{
  "error": false,
  "data": {
    "operation_id": "op_2f4a...",
    "type": "payout",
    "status": "queued",
    "env": "sandbox",
    "_links": { "self": "/api/v1/merchant/operations/op_2f4a..." }
  }
}
```

## 4. Suivre l'opération

```bash
SIGN_PATH="/api/v1/merchant/operations/op_2f4a..."
TS=$(date -u +"%Y-%m-%dT%H:%M:%S.000Z")
PAYLOAD="$TS
GET
$SIGN_PATH

"
SIG="sha256=$(printf %s "$PAYLOAD" | openssl dgst -sha256 -hmac "$SEC" -binary | xxd -p -c 256)"

curl -s "$HOST/operations/op_2f4a..." \
  -H "X-Api-Key: $PUB" -H "X-Timestamp: $TS" -H "X-Signature: $SIG" | jq
```

## 5. Étapes suivantes

| Si tu veux… | Direction |
| :--- | :--- |
| Apprendre la signature en détail | [Authentification & signature](authentication.md) |
| Comprendre sandbox vs live | [Sandbox vs Live](sandbox-vs-live.md) |
| Recevoir les updates en push | [Webhooks signés](webhooks.md) |
| Voir tous les endpoints | [Référence](reference/balance.md) |
| Comprendre les rejeux sûrs | [Idempotency](idempotency.md) |

{% hint style="success" %}
**Passer en live** : récupère tes clés `pk_live_…`, configure ta whitelist IP dans le dashboard (Menu Développeurs → IPs autorisées), puis remplace les clés. Aucun autre changement de code.
{% endhint %}
