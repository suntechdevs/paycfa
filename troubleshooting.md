---
description: >-
  Symptômes courants rencontrés lors de l'intégration des APIs Intram, leurs
  causes probables et les solutions à appliquer.
---

# Troubleshooting

Cette page regroupe les problèmes les plus fréquents — sur le compte, sur l'API publique de paiement et sur la Merchant API. Cherchez votre symptôme dans la table, puis suivez le lien vers la section détaillée.

## Index par symptôme

| Symptôme | Section |
| :--- | :--- |
| `401 invalid token` / `401 No token provided` | [Authentification dashboard](#401-token-invalide-ou-absent) |
| `401 missing_api_key` / `401 invalid_api_key` | [Clés API Merchant manquantes ou invalides](#401-clés-api-merchant-manquantes-ou-invalides) |
| `401 signature_invalid` | [Signature HMAC invalide](#401-signature-hmac-invalide) |
| `403 ip_allowlist_empty` | [Allowlist IP vide en mode live](#403-ip_allowlist_empty) |
| `403 ip_not_allowed` | [IP appelante non autorisée](#403-ip_not_allowed) |
| `403 merchant_inactive` | [Compte non validé](#403-merchant_inactive) |
| `400 missing_idempotency_key` | [Idempotency-Key manquante](#400-missing_idempotency_key) |
| `409 idempotency_conflict` | [Conflit d'idempotence](#409-idempotency_conflict) |
| `400 validation_error` | [Body invalide](#400-validation_error) |
| `404 transaction_not_found` | [Transaction introuvable](#404-transaction_not_found) |
| Webhook jamais reçu | [Aucun webhook ne parvient](#aucun-webhook-ne-parvient) |
| Signature webhook invalide chez moi | [Vérification signature webhook échoue](#vérification-signature-webhook-échoue) |
| Opération bloquée en `queued` | [Opération asynchrone ne progresse pas](#opération-asynchrone-ne-progresse-pas) |
| `insufficient_balance` au runtime | [Solde insuffisant](#solde-insuffisant) |
| `provider_rejected` au runtime | [Provider a rejeté](#provider_rejected) |
| Le client paie mais je vois `PENDING` | [Transaction reste pending après paiement](#transaction-reste-pending-après-paiement) |
| Refund refusé `not_refundable` | [Statut transaction non remboursable](#not_refundable) |
| Sandbox ne fonctionne pas | [Confusion sandbox / live](#confusion-sandbox--live) |
| `502 Bad Gateway` / `504 Gateway Timeout` | [Problème côté infrastructure](#502--504-côté-infrastructure) |

---

## 401 : token invalide ou absent

**Symptôme**
```json
{ "error": true, "message": "invalid token" }
```
ou
```json
{ "error": true, "message": "No token provided." }
```

**Quand**
Sur les endpoints **dashboard** (qui exigent un JWT utilisateur).

**Causes**
- Le header `Authorization` n'est pas envoyé.
- Le token JWT est expiré (durée de vie 24 h par défaut).
- Le token a été signé avec un autre `APP_SECRET` que celui du serveur.

**Fix**
1. Vérifiez que votre client envoie bien `Authorization: <token>` (sans préfixe `Bearer` côté admin Intram).
2. Re-loggez-vous depuis le dashboard pour obtenir un nouveau token.
3. Si vous appelez depuis votre code, refresh-loggez avant chaque appel sensible ou implémentez un mécanisme de refresh.

---

## 401 : clés API Merchant manquantes ou invalides

**Symptôme**
```json
{ "error": true, "code": "missing_api_key" }
```
ou
```json
{ "error": true, "code": "invalid_api_key" }
```

**Quand**
Sur tout endpoint [`/api/v1/merchant/*`](merchant-api/README.md).

**Causes**
- Vous n'envoyez pas le header `X-Api-Key`.
- La clé est faussée (espace ajouté en fin de chaîne, copier-coller incomplet).
- Vous utilisez une clé sandbox sur une URL live, ou inversement (vérifiez l'env de la clé : préfixe `pk_sandbox_…` vs `pk_live_…`).
- La clé a été régénérée depuis le dashboard et l'ancienne ne fonctionne plus.

**Fix**
1. Récupérez la clé depuis **Développeurs → API** dans le dashboard.
2. Vérifiez le préfixe (`pk_sandbox_` ou `pk_live_`).
3. Mettez à jour vos variables d'environnement et redéployez.

---

## 401 : signature HMAC invalide

**Symptôme**
```json
{
  "error": true,
  "code": "signature_invalid",
  "message": "Signature verification failed: <reason>"
}
```

Où `<reason>` ∈ `missing_signature`, `missing_timestamp`, `invalid_timestamp_format`, `timestamp_out_of_window`, `signature_mismatch`.

**Causes par `reason`**

| `reason` | Cause |
| :--- | :--- |
| `missing_signature` | Header `X-Signature` absent |
| `missing_timestamp` | Header `X-Timestamp` absent |
| `invalid_timestamp_format` | Timestamp n'est pas en ISO 8601 |
| `timestamp_out_of_window` | Plus de 5 minutes d'écart entre votre horloge et celle du serveur |
| `signature_mismatch` | Le HMAC calculé ne correspond pas |

**Fix pour `signature_mismatch` — checklist**
- Le path utilisé pour signer est **le path interne** `/api/v1/merchant/<endpoint>`, pas l'URL publique `https://api.intram.org/v1/<endpoint>`.
- Le body utilisé est **le raw body envoyé**, octet pour octet. Pas une re-sérialisation post-modification.
- La query string est triée alphabétiquement par clé, jointe par `&`.
- Le séparateur entre les 5 champs est un `\n` (line feed), pas `\r\n`.
- Le secret utilisé est bien votre **clé secrète** (pas la clé privée, ni la clé publique).
- Vous comparez la **même casse** : l'en-tête doit commencer par `sha256=` minuscule, suivi du hex en minuscule.

**Fix pour `timestamp_out_of_window`**
- Synchronisez l'horloge serveur via NTP : `timedatectl status` doit afficher `System clock synchronized: yes`.
- Vérifiez que vous n'envoyez pas un timestamp pré-calculé qui dort dans un queue.

Voir le [détail complet de la signature](merchant-api/authentication.md).

---

## 403 : `ip_allowlist_empty`

**Symptôme**
```json
{
  "error": true,
  "code": "ip_allowlist_empty",
  "message": "Live API keys require at least one allowed IP."
}
```

**Cause**
Vous utilisez une clé `pk_live_…` mais aucune IP n'est whitelistée pour votre compte. C'est volontaire : une liste vide rejette tout (fail-closed).

**Fix**
1. Récupérez l'IP publique de votre serveur backend : `curl -4 ifconfig.me` depuis le serveur.
2. Dashboard → **Développeurs → IPs autorisées → Ajouter un serveur**.
3. Collez l'IP, ajoutez un libellé, sauvegardez.
4. Réessayez votre appel — c'est immédiat (pas de cache).

⚠️ N'ajoutez pas l'IP de votre **navigateur** par erreur ; c'est l'IP du **serveur** qui doit être autorisée.

Voir [Sandbox vs Live + IP allowlist](merchant-api/sandbox-vs-live.md).

---

## 403 : `ip_not_allowed`

**Symptôme**
```json
{
  "error": true,
  "code": "ip_not_allowed",
  "message": "This source IP is not in the merchant allowlist",
  "client_ip": "203.0.113.99"
}
```

**Causes**
- Votre serveur a une nouvelle IP publique (changement de fournisseur, NAT modifié, scale-out vers une autre région).
- Votre allowlist contient `203.0.113.4` mais la requête vient de `203.0.113.5` (différence d'un octet).
- Vous appelez depuis une machine de dev non whitelistée (alors qu'il faudrait utiliser une clé sandbox).

**Fix**
1. Le champ `client_ip` dans la réponse vous dit **exactement** quelle IP a été vue.
2. Si c'est légitime : ajoutez cette IP à l'allowlist (ou un CIDR englobant).
3. Si vous êtes en dev local : utilisez une clé sandbox (pas de restriction).

---

## 403 : `merchant_inactive`

**Symptôme**
```json
{ "error": true, "code": "merchant_inactive", "message": "Merchant account not validated" }
```

**Cause**
Votre compte business n'est pas encore validé par l'équipe Intram.

**Fix**
1. Suivez la procédure [Activation du compte marchand](account/account-activation.md).
2. Téléversez les pièces justificatives requises.
3. Attendez la validation (1 à 3 jours ouvrés).
4. En attendant, vous pouvez intégralement développer en `SANDBOX`.

---

## 400 : `missing_idempotency_key`

**Symptôme**
```json
{ "error": true, "code": "missing_idempotency_key" }
```

**Cause**
Vous faites un POST/PUT/PATCH sans header `Idempotency-Key`.

**Fix**
Générez une clé unique par opération métier (`crypto.randomUUID()` par exemple) et passez-la en header :
```
Idempotency-Key: payout-2026-05-20-batch-1
```
Format : 8 à 128 caractères, `[A-Za-z0-9_-]`. Voir [Idempotency](merchant-api/idempotency.md).

---

## 409 : `idempotency_conflict`

**Symptôme**
```json
{ "error": true, "code": "idempotency_conflict",
  "message": "Idempotency-Key already used with a different request body" }
```

**Cause**
Vous réutilisez une clé d'idempotence avec un body différent. Le serveur refuse car il garantit qu'une clé = une opération.

**Fix**
- Si c'est un retry de la **même** opération : assurez-vous d'envoyer **exactement le même body**.
- Si c'est une **nouvelle** opération (montant différent, destinataire différent…) : utilisez une **nouvelle clé**.

Voir [Idempotency — quand changer de clé](merchant-api/idempotency.md#quand-tu-dois-changer-de-clé).

---

## 400 : `validation_error`

**Symptôme**
```json
{
  "error": true,
  "code": "validation_error",
  "message": "Invalid …",
  "details": [
    { "path": "amount", "msg": "amount must be > 0" }
  ]
}
```

**Cause**
Le body ne respecte pas le schéma de l'endpoint.

**Fix**
Lisez `details[]` pour voir précisément quel champ pose problème. Référez-vous à la doc de l'endpoint concerné dans la [Reference Merchant API](merchant-api/reference/balance.md).

---

## 404 : `transaction_not_found`

**Symptôme**
```json
{ "error": true, "code": "transaction_not_found",
  "message": "Transaction not found for this merchant" }
```

**Causes**
- La référence n'existe pas (faute de frappe).
- La transaction existe mais appartient à un autre marchand. Vous ne pouvez accéder qu'aux vôtres.
- Vous êtes en sandbox et vous cherchez une transaction live (ou inverse) — les wallets sont isolés.

**Fix**
1. Listez vos transactions via [`GET /merchant/transactions?limit=10`](merchant-api/reference/transactions.md) pour vérifier le format des références.
2. Vérifiez l'environnement de la clé que vous utilisez.

---

## Aucun webhook ne parvient

**Symptôme**
Vous avez configuré un webhook mais aucun appel n'arrive sur votre endpoint.

**Causes possibles & vérifications**

| Cause | Vérification |
| :--- | :--- |
| URL en HTTP (pas HTTPS) | Le serveur refuse de créer un webhook non-HTTPS. La création aurait dû échouer en 400 — recréez en HTTPS. |
| URL inaccessible publiquement | Votre URL doit être joignable depuis Internet (pas `localhost`, pas IP privée). Utilisez ngrok / Cloudflare Tunnel pour les tests locaux. |
| Firewall bloque le trafic Intram | Vérifiez vos règles entrantes côté serveur applicatif. Vous pouvez whitelister les IPs sortantes Intram (demandez au support la liste actuelle). |
| Souscription mal configurée (event qui ne match pas) | `GET /merchant/webhooks` pour lister vos souscriptions ; vérifiez le pattern `event`. |
| Aucun event émis (l'opération n'a peut-être pas eu lieu) | `GET /merchant/operations/:id` pour voir le statut de l'opération concernée. |

**Test rapide**
```bash
# Envoyer une livraison test à votre URL
POST /merchant/webhooks/<webhook_id>/test
```
Si ce test passe et qu'aucun event réel n'arrive ensuite : c'est que les events ne sont pas émis (cf. dernière ligne du tableau).

Voir [Webhooks signés](merchant-api/webhooks.md).

---

## Vérification signature webhook échoue

**Symptôme**
Vous recevez bien les webhooks mais votre vérification HMAC échoue.

**Causes courantes**
1. **Vous utilisez le body parsé** (`req.body` après `express.json()`) au lieu du **raw body** brut. La signature porte sur les octets bruts — re-sérialiser produit un body légèrement différent.
2. **Mauvais secret** — assurez-vous d'utiliser le secret affiché **une seule fois** à la création du webhook. Si vous l'avez perdu, supprimez et recréez la souscription.
3. **Timestamp trop vieux** — vérifiez l'écart entre `X-Intram-Timestamp` et votre horloge serveur.
4. **Encodage du body** — si vous lisez le body en UTF-8 décodé, vous pouvez perdre des octets multi-byte. Lisez en `Buffer` brut.

**Pattern correct (Node + Express)**
```javascript
app.post('/webhook',
  express.raw({ type: 'application/json' }),   // ← raw, pas json()
  (req, res) => {
    const sig = req.headers['x-intram-signature'];
    const ts  = req.headers['x-intram-timestamp'];
    const expected = 'sha256=' + crypto
      .createHmac('sha256', SECRET)
      .update(`${ts}.${req.body}`)             // req.body est un Buffer
      .digest('hex');
    // ...
  }
);
```

Voir [Recevoir les webhooks](merchant-api/webhooks.md#vérifier-la-signature).

---

## Opération asynchrone ne progresse pas

**Symptôme**
`GET /merchant/operations/:id` renvoie `status: "queued"` ou `"processing"` depuis plusieurs minutes sans bouger.

**Causes**
- Le worker (`merchant-api-worker`) est arrêté côté infrastructure. Côté ops : `pm2 status`.
- Backlog de la queue (peu probable en charge normale).
- Pour les payment requests : la transaction attend le webhook provider qui n'arrive pas.

**Fix côté intégrateur**
- Patientez quelques secondes — la grande majorité des opérations se terminent en moins de 30 s.
- Si > 5 minutes : escaladez au support en fournissant l'`operation_id`.
- Mettez en place des webhooks pour ne plus avoir à poller (préféré).

---

## `insufficient_balance`

**Symptôme** (au niveau opération, dans `op.error`)
```json
{ "code": "insufficient_balance", "message": "Insufficient merchant balance" }
```

**Causes**
- Votre wallet est effectivement vide ou en dessous du montant + frais demandés.
- Le `pending` est conséquent mais pas encore en `available`.

**Fix**
1. Vérifiez votre solde réel : `GET /merchant/balance` et regardez `available` vs `pending`.
2. Attendez le settlement du `pending` (cycle variable selon les providers).
3. Faites des payouts plus petits ou groupez-les différemment.

---

## `provider_rejected`

**Symptôme** (au niveau opération)
```json
{
  "code": "provider_rejected",
  "message": "...",
  "details": { "responsecode": "...", "responsemsg": "..." }
}
```

**Cause**
Le provider externe (MTN, Moov, SBIN, Stripe) a refusé l'opération.

**Fix**
1. Lisez `details.responsemsg` pour comprendre la raison (compte fermé, MSISDN invalide, plafond atteint…).
2. Pour Mobile Money : vérifiez le format du `msisdn` (international sans `+`, ex : `22961234567`).
3. Pour les bank wires : vérifiez le SWIFT et le numéro de compte.
4. Le wallet a été automatiquement recrédité — vous pouvez retenter avec une nouvelle clé d'idempotence après correction.

---

## Transaction reste `pending` après paiement

**Symptôme**
Le client dit avoir payé via Mobile Money mais `GET /merchant/transactions/:reference` montre `status: PENDING`.

**Causes**
- Le webhook provider n'est pas encore arrivé chez Intram (latence typique : 5–30 s, max 6 minutes).
- Le client a abandonné après l'OTP sans confirmer.
- Erreur réseau côté provider.

**Fix**
1. Patientez jusqu'à 6 minutes (timeout côté provider).
2. Si toujours `pending` après : la transaction est officiellement timeout — informez le client. Si l'argent a quand même été débité chez lui, un rapprochement manuel est nécessaire (escaladez au support).
3. Pour éviter le polling : abonnez-vous aux webhooks `payment_request.paid` / `payment_request.failed`.

---

## `not_refundable`

**Symptôme**
```json
{ "code": "not_refundable", "message": "Transaction status is ERROR" }
```

**Cause**
La transaction n'est pas dans un statut qui permet le refund. Statuts refundables : `SUCCESS` (refund complet ou partiel) et `PENDING` (annulation côté Stripe). Pas refundables : `ERROR`, `REFUNDED`.

**Fix**
- Si la transaction est `ERROR` : pas de remboursement nécessaire, l'argent n'a pas été débité.
- Si la transaction est déjà `REFUNDED` : un refund a déjà été appliqué, voir `refunds[]` dans `GET /merchant/transactions/:reference`.

---

## Confusion sandbox / live

**Symptômes possibles**
- "Mon paiement sandbox n'apparaît pas dans le dashboard live."
- "Mon payout sandbox ne crédite pas le destinataire."
- "Le client a payé mais je ne vois rien dans mon wallet."

**Cause**
Vous mélangez les deux environnements. Les wallets sandbox et live sont **complètement isolés**.

**Fix**
1. Vérifiez le préfixe de votre clé : `pk_sandbox_…` vs `pk_live_…`.
2. Dans le dashboard, le toggle SANDBOX/LIVE en haut de page filtre les transactions affichées.
3. En sandbox, aucun argent réel ne bouge — c'est normal que le destinataire ne reçoive rien sur son téléphone.

Voir [Sandbox vs Live](merchant-api/sandbox-vs-live.md).

---

## 502 / 504 côté infrastructure

**Symptômes**
- `502 Bad Gateway`
- `504 Gateway Timeout`
- Réponse vide / connexion fermée

**Causes**
- Le service Intram est temporairement indisponible (maintenance, incident).
- Votre requête dépasse le timeout du proxy (rare avec le Merchant API qui est async ; possible avec l'ancienne API `/payments/update` qui attend jusqu'à 6 min).

**Fix**
1. Vérifiez le [statut Intram](https://app.intram.org) (notifications dans le dashboard).
2. Retentez avec backoff exponentiel.
3. Pour la Merchant API : utilisez votre `Idempotency-Key` originale lors des retries — c'est sûr.
4. Si le problème persiste plus de quelques minutes, contactez le support avec l'horodatage UTC et l'`operation_id` si applicable.

---

## Vous ne trouvez pas votre symptôme ?

* Consultez le [glossaire](glossary.md) pour clarifier les termes.
* Vérifiez les pages dédiées : [Merchant API errors](merchant-api/errors.md), [Webhooks](merchant-api/webhooks.md), [Authentification](merchant-api/authentication.md).
* Contactez le support depuis le dashboard avec :
  - L'horodatage UTC précis de votre requête
  - L'`operation_id` (si Merchant API) ou la `reference` (si API publique)
  - Le code d'erreur reçu
  - Un extrait des headers que vous envoyez (en masquant les secrets)
