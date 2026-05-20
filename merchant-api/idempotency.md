---
description: >-
  Pourquoi et comment utiliser l'Idempotency-Key pour rejouer une requête en
  toute sécurité.
---

# Idempotency

Toute requête mutante (POST / PUT / PATCH) vers le Merchant API doit porter un header `Idempotency-Key`. C'est ce qui vous permet de **retenter** une requête sans risquer un double payout ou un double refund — même si la première tentative a coupé en cours de route.

## Format de la clé

```http
Idempotency-Key: po-2026-0001-attempt-1
```

* Longueur : **8 à 128 caractères**
* Charset : `[A-Za-z0-9_-]` (alphanum + underscore + tiret)
* Unique pour une opération métier — typiquement un UUID v4 ou ton ID interne suffixé

```javascript
// Exemple Node.js
const idem = crypto.randomUUID();   // 36 caractères, conforme
```

## Comment ça marche côté serveur

1. À réception, l'API calcule une empreinte SHA-256 de `(method + path + body)` et l'associe à la clé d'idempotence dans Redis avec un TTL de **24 h**.
2. Si la clé n'existe pas → la requête est traitée normalement. La réponse (status + body) est mémorisée.
3. Si la clé existe **avec la même empreinte** → la réponse mémorisée est renvoyée immédiatement, avec un header `Idempotent-Replay: true`. **Le traitement ne se rejoue pas.**
4. Si la clé existe **avec une empreinte différente** → 409 Conflict (`idempotency_conflict`). Tu as réutilisé une clé pour une opération différente, c'est interdit.

## Quand tu DOIS rejouer

| Cas | Action |
| :--- | :--- |
| Timeout côté client (pas de réponse) | Rejoue avec **la même** `Idempotency-Key` |
| 5xx serveur | Rejoue avec la même clé, après un petit délai |
| Connexion réseau coupée | Rejoue avec la même clé |
| 4xx (sauf 409 conflict) | **Ne rejoue pas** — corrige la requête, puis utilise une **nouvelle** clé |

## Quand tu DOIS changer de clé

* Quand le contenu métier change (montant différent, destinataire différent…)
* Quand tu rejoues volontairement la même opération une seconde fois (par exemple un payout récurrent hebdomadaire)

## Erreurs

```json
// Clé manquante sur une mutation
HTTP 400
{
  "error": true,
  "code": "missing_idempotency_key",
  "message": "Idempotency-Key header is required for mutating requests"
}

// Format invalide
HTTP 400
{
  "error": true,
  "code": "invalid_idempotency_key",
  "message": "Idempotency-Key must be 8-128 alphanumeric / - / _ characters"
}

// Réutilisation avec un autre body
HTTP 409
{
  "error": true,
  "code": "idempotency_conflict",
  "message": "Idempotency-Key already used with a different request body"
}
```

## Bonne pratique : préfixer par l'opération métier

| Mauvais | Bon |
| :--- | :--- |
| `abc123` | `payout-2026-05-20-batch-1` |
| `42` | `refund-tx-AB12CD34EF-full` |
| `uuid-v4-anonyme` | `payment-req-order-4521` |

Préfixer par l'opération métier facilite le debug : si tu vois une 409, tu sais immédiatement quel flow a généré le doublon.

## Cas d'usage : rejeu sûr après timeout

```javascript
async function safePayout(payload) {
  // 1. Génère la clé UNE SEULE FOIS
  const idem = `payout-${payload.reference}-${Date.now()}`;

  // 2. Boucle de retry avec la MÊME clé
  for (let attempt = 1; attempt <= 3; attempt++) {
    try {
      return await signedPost('/payouts', payload, idem);
    } catch (err) {
      // Si c'est un 5xx ou réseau, rejoue. Sinon, propage.
      if (err.response && err.response.status >= 500) {
        await sleep(2 ** attempt * 1000);
        continue;
      }
      throw err;
    }
  }
}
```

Si la première tentative a en fait abouti (mais que la connexion a coupé avant que tu ne reçoives la réponse), la seconde verra `Idempotent-Replay: true` et te rendra le résultat d'origine — pas de double débit.
