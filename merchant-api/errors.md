---
description: >-
  Format standard des réponses, catalogue des codes d'erreur, gestion des
  opérations asynchrones.
---

# Enveloppe de réponse & codes d'erreur

Toutes les réponses du Merchant API suivent une enveloppe JSON standard.

## Succès synchrone

```json
HTTP 200
{
  "error": false,
  "data": { /* contenu spécifique à l'endpoint */ }
}
```

## Succès asynchrone (POST mutants)

```json
HTTP 202 Accepted
{
  "error": false,
  "data": {
    "operation_id": "op_2f4a…",
    "type": "payout",
    "status": "queued",
    "env": "live",
    "_links": { "self": "/api/v1/merchant/operations/op_2f4a…" }
  }
}
```

L'opération est en file d'attente. Suis son avancement via webhook (recommandé) ou polling sur `GET /operations/:id`.

## Erreur

```json
HTTP 4xx | 5xx
{
  "error": true,
  "code":  "validation_error",
  "message": "Invalid payout request",
  "details": [ /* optionnel — détails par champ */ ]
}
```

| Champ | Description |
| :--- | :--- |
| `error` | Toujours `true` en cas d'erreur, `false` en cas de succès |
| `code` | Identifiant machine-readable stable de l'erreur (à utiliser dans le code) |
| `message` | Message human-readable, peut évoluer entre versions |
| `details` | Optionnel — tableau de violations de validation par champ, ou objet provider-spécifique |

{% hint style="warning" %}
Toujours **dispatcher sur `code`**, jamais sur `message`. Le message peut être traduit ou reformulé sans préavis ; le code est garanti stable dans une version majeure.
{% endhint %}

## Catalogue des codes

### Authentification (401 / 403)

| Code | HTTP | Action |
| :--- | :---: | :--- |
| `missing_api_key` | 401 | Ajoute le header `X-Api-Key` |
| `invalid_api_key` | 401 | Vérifie la clé — peut-être révoquée ou mauvais env |
| `signature_invalid` | 401 | Recalcule la signature — voir [Authentification](authentication.md) |
| `merchant_inactive` | 403 | Le compte n'est pas validé — contacte le support |
| `ip_allowlist_empty` | 403 | Configure au moins une IP autorisée dans le dashboard |
| `ip_not_allowed` | 403 | L'IP appelante n'est pas dans la whitelist live |

### Idempotency (400 / 409)

| Code | HTTP | Action |
| :--- | :---: | :--- |
| `missing_idempotency_key` | 400 | Ajoute le header `Idempotency-Key` |
| `invalid_idempotency_key` | 400 | Reformule la clé (8-128 chars `[A-Za-z0-9_-]`) |
| `idempotency_conflict` | 409 | Tu réutilises une clé avec un body différent — utilise une nouvelle clé |

### Validation (400)

| Code | HTTP | Action |
| :--- | :---: | :--- |
| `validation_error` | 400 | Voir `details[]` pour la liste des champs invalides |
| `invalid_body` | 400 | Le body n'est pas du JSON valide |
| `unknown_currency` | 400 | La devise demandée n'est pas supportée |
| `amount_too_small` | 400 | Sous le minimum autorisé pour la devise |
| `amount_too_large` | 400 | Au-dessus du maximum autorisé pour la devise |

### Ressources (404 / 409)

| Code | HTTP | Action |
| :--- | :---: | :--- |
| `transaction_not_found` | 404 | Le `transaction_reference` n'existe pas ou n'appartient pas à ce marchand |
| `transfer_not_found` | 404 | Idem pour les transferts |
| `operation_not_found` | 404 | L'`operation_id` n'existe pas ou n'appartient pas à ce marchand |
| `not_refundable` | 400 | La transaction n'est pas dans un statut remboursable (déjà refunded, échouée, etc.) |

### Métier (réponses 200 avec opération `failed`)

Quand le worker traite une opération asynchrone et qu'elle échoue, la réponse `GET /operations/:id` contient un champ `error` avec un de ces codes :

| Code | Quand |
| :--- | :--- |
| `insufficient_balance` | Solde marchand insuffisant pour le payout / transfert |
| `merchant_wallet_missing` | Aucun wallet provisionné pour ce marchand (cas rare) |
| `recipient_not_found` | Destinataire d'un transfert M2C inconnu |
| `provider_rejected` | Le provider MoMo / banque a refusé l'opération |
| `provider_unavailable` | Aucun handler pour le couple country/operator demandé |
| `provider_error` | Erreur réseau ou exception inattendue du provider |
| `missing_provider_code` | `destination.provider_code` absent pour un payout mobile_money |
| `amount_exceeds_transaction` | Tentative de refund supérieure au montant original |

### Infrastructure (5xx)

| Code | HTTP | Action |
| :--- | :---: | :--- |
| `internal_error` | 500 | Réessaie avec la **même** Idempotency-Key après un délai |
| `auth_error` | 500 | Erreur côté serveur sur la vérif de la clé — contacte le support |
| `idempotency_error` | 500 | Redis indisponible — réessaie après quelques secondes |

## Différencier erreur synchrone vs erreur asynchrone

```javascript
const { data } = await signedPost('/payouts', payload, idem);

// Synchrone — la requête elle-même a-t-elle été acceptée ?
if (data.error) {
  console.error('Rejet immédiat:', data.code, data.message);
  return;
}

// Acceptée — surveiller l'opération via webhook ou polling
const opId = data.data.operation_id;

// Polling alternatif (déconseillé — préfère les webhooks)
const op = await signedGet(`/operations/${opId}`);
if (op.data.status === 'failed') {
  console.error('Échec métier:', op.data.error.code, op.data.error.message);
}
```

## Statuts d'opération

| `status` | Sens |
| :--- | :--- |
| `queued` | En file d'attente, pas encore traitée |
| `processing` | En cours de traitement par un worker |
| `succeeded` | Terminée avec succès — voir `result` |
| `failed` | Échouée — voir `error.code` et `error.message` |
| `expired` | Pas encore utilisé (réservé pour les opérations à TTL) |
