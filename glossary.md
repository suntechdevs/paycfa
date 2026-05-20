---
description: >-
  Définition des termes métier et techniques utilisés dans la documentation
  Intram (API publique, Merchant API, dashboard).
---

# Glossaire

Termes classés par ordre alphabétique. Quand un terme renvoie à une page dédiée, le lien est indiqué.

## A

**`amount`**
Montant brut d'une transaction, hors frais. Toujours en unité entière de la devise (ex : `1000` pour 1 000 XOF).

**`amount_total`**
Ce que paie effectivement le client : `amount + fees`. Calculé par Intram à partir des [taux applicables](payment/fees.md).

**API key**
Identifiant numérique de votre compte marchand. Trois clés par environnement :
- `public_key` — identifiant non sensible (équivalent d'un username)
- `private_key` — utilisée par les anciennes intégrations SDK
- `secret_key` — **secret partagé**, sert à signer les requêtes [Merchant API](merchant-api/authentication.md) et à vérifier les [webhooks signés](merchant-api/webhooks.md)

## B

**Backend**
Vos serveurs (production / staging / dev) qui appellent l'API Intram pour piloter votre compte. Distinct du frontend (navigateur du client).

**Balance / Solde**
Montant disponible sur votre wallet. Exposé via [`GET /merchant/balance`](merchant-api/reference/balance.md). Composé de :
- `available` — disponible pour payouts/transferts immédiats
- `pending` — en attente de settlement

## C

**Callback URL**
URL fournie au moment d'initier un paiement, vers laquelle Intram redirige le client après le checkout. Différent d'un webhook : c'est une redirection navigateur, pas un appel server-to-server signé.

**CIDR**
Notation pour désigner un bloc d'adresses IP. Exemple : `203.0.113.0/24` couvre les 256 IPs de `203.0.113.0` à `203.0.113.255`. Utilisé dans [l'allowlist IP](merchant-api/sandbox-vs-live.md#la-whitelist-ip-en-mode-live).

**Compte business / Compte marchand**
Entité légale enregistrée chez Intram, distincte de votre compte utilisateur. Un utilisateur peut posséder plusieurs comptes business. Voir [Activation du compte marchand](account/account-activation.md).

**Customer / Client**
Le payeur final dans une transaction — la personne dont le wallet ou le compte Mobile Money est débité.

## D

**Dashboard**
L'interface web sur [https://app.intram.org](https://app.intram.org) permettant de gérer votre compte : clés API, IPs autorisées, webhooks, transactions, balances, paramètres.

**Devise**
Code monétaire ISO 4217. Actuellement supporté : `XOF`. Voir [Devises supportées](payment/supported-devices.md).

## F

**Fees / Frais**
Commissions prélevées sur chaque transaction. Réparties entre **Frais opérateur** (reversés au provider) et **Commission Intram** (conservés par Intram). Voir [Frais](payment/fees.md).

## G

**Gateway / Gateway URL**
Page web servie par Intram (`gateway.intram.org/<transaction_id>`) où le client effectue son paiement. Renvoyée dans `result.gateway_url` après une [demande de paiement](merchant-api/reference/payment-requests.md).

## H

**HMAC / HMAC-SHA256**
Algorithme de signature cryptographique. Vous prouvez l'authenticité d'un message en calculant `HMAC-SHA256(secret, message)` ; le receveur recalcule la même chose et compare. Utilisé pour [signer les requêtes Merchant API](merchant-api/authentication.md) et [les webhooks sortants](merchant-api/webhooks.md).

## I

**Idempotency-Key**
Header HTTP unique par opération métier, qui permet de retenter une requête sans risque de double exécution. Obligatoire sur toutes les mutations Merchant API, TTL 24 h. Voir [Idempotency](merchant-api/idempotency.md).

**IP allowlist**
Liste d'IPs autorisées à appeler l'API en mode live, par marchand. Configurable depuis le dashboard (**Développeurs → IPs autorisées**). Voir [Sandbox vs Live](merchant-api/sandbox-vs-live.md).

**ISO 4217**
Standard de codification des devises sur 3 lettres (`XOF`, `EUR`, `USD`…).

**ISO 8601**
Standard de format des dates : `2026-05-20T10:30:00.000Z`. Utilisé pour le header `X-Timestamp`.

## L

**Live / Mode live**
Environnement de production où les transactions touchent l'argent réel. Identifié par les clés `pk_live_…` / `sk_live_…`. Exige une [allowlist IP](merchant-api/sandbox-vs-live.md) configurée.

## M

**Mobile Money**
Moyen de paiement par téléphone, opéré par des opérateurs télécoms ou bancaires. Codes utilisés :
- `MTN_BENIN_229` — MTN Bénin
- `MOOV_AFRICA_BENIN_229` — Moov Africa Bénin
- `SBIN_BENIN_229` — Sirius Bank Bénin

Voir [Devises supportées](payment/supported-devices.md) pour la liste à jour.

**`msisdn`**
Numéro de téléphone international au format E.164 sans le `+`. Exemple : `22961234567` pour le Bénin (indicatif 229).

## O

**Operation**
Trace d'une opération asynchrone Merchant API (`payout`, `transfer`, `payment_request`, `refund`). Identifiée par un `operation_id` (`op_…`). Statuts : `queued`, `processing`, `succeeded`, `failed`. Voir [Opérations](merchant-api/reference/operations.md).

## P

**Payment Link / Intram Direct**
Lien de paiement personnalisé que vous générez pour un client, sans qu'il ait à passer par un checkout. Peut être envoyé par SMS, email, ou intégré à un site.

**Payment request**
Demande de paiement asynchrone créée via [`POST /merchant/payment-requests`](merchant-api/reference/payment-requests.md). Génère un `gateway_url` et un QR code à partager au client.

**Payout / Reversement**
Sortie d'argent depuis votre wallet vers un compte externe (Mobile Money ou compte bancaire). Voir [Payouts](merchant-api/reference/payouts.md).

**`pending`**
- Sur une transaction : en attente de confirmation provider/client.
- Sur un solde : reçu mais pas encore disponible pour payout.

**Provider**
Fournisseur externe de moyen de paiement (MTN, Moov, SBIN, Stripe…). Intram fait la passerelle entre vous et eux.

## Q

**QR code**
Code-barres 2D encodant l'URL du gateway. Permet à un client de scanner et payer depuis son téléphone. Renvoyé dans `result.qr_code` au format data-URL PNG.

## R

**`reference`**
Identifiant public d'une transaction ou d'un transfert, lisible par l'humain (ex : `AB12CD34EF`). Distinct du Mongo `_id`.

**Refund / Remboursement**
Inverser une transaction (totalement ou partiellement). Voir [Refunds](merchant-api/reference/refunds.md).

## S

**Sandbox**
Environnement de test où aucune somme réelle n'est manipulée. Identifié par les clés `pk_sandbox_…` / `sk_sandbox_…`. Pas de restriction IP. Voir [Sandbox vs Live](merchant-api/sandbox-vs-live.md).

**`secret_key`**
Clé secrète de votre compte. Sert à calculer la signature HMAC des requêtes Merchant API. **Ne sort jamais** d'un environnement sécurisé.

**Frais opérateur**
Part des frais reversée au provider du moyen de paiement (MTN, MOOV, SBIN, Stripe…). Voir [Frais](payment/fees.md).

**Commission Intram**
Part des frais conservée par Intram pour le service de plateforme.

**`service_reference`**
Identifiant de la transaction côté provider (ex : référence MTN, ID Stripe). Différent du `reference` Intram. Exposé dans les réponses des endpoints transaction.

**Settlement**
Le moment où une transaction passe de `PENDING` à `SUCCESS` côté provider, et où l'argent crédite réellement votre wallet.

**Signature**
Header `X-Signature: sha256=<hex>` calculé à partir de `(timestamp + method + path + query + body)` et de votre `secret_key`. Anti-tampering + anti-replay. Voir [Authentification](merchant-api/authentication.md).

## T

**Timestamp**
Header `X-Timestamp` en format [ISO 8601](#i) UTC. Rejeté si > 5 minutes d'écart avec l'heure serveur (anti-replay).

**Transaction**
Trace d'un paiement reçu par le marchand. Distincte d'un transfert (interne) et d'un payout (sortant).

**Transfer / Transfert**
Mouvement wallet-to-wallet à l'intérieur de l'écosystème Intram (M2C, C2C, M2M, C2M).

**Trusted proxy**
Quand l'application est derrière Nginx, on fait confiance au header `X-Real-IP` qu'il injecte pour résoudre la vraie IP appelante. Contrôlé par la variable d'environnement `MERCHANT_API_TRUSTED_PROXY`.

## U

**Upstream path**
Le path que le serveur Node voit après le rewrite Nginx. Pour la [Merchant API](merchant-api/authentication.md), l'URL publique `https://api.intram.org/v1/payouts` est réécrite en `/api/v1/merchant/payouts` — c'est ce dernier qui est utilisé pour calculer la signature.

## W

**Wallet**
Le porte-monnaie virtuel de votre compte marchand chez Intram. Possède un solde par devise et par moyen de paiement. Les soldes sont strictement isolés entre les environnements `sandbox` et `live`.

**Webhook**
Notification HTTP signée envoyée par Intram à votre URL configurée quand un événement se produit (ex : payout réussi, refund accepté). Politique de retry exponentiel jusqu'à 5 tentatives. Voir [Recevoir les webhooks](merchant-api/webhooks.md).

## Voir aussi

* [Troubleshooting](troubleshooting.md) — symptômes courants → causes → solutions
* [Merchant API — Overview](merchant-api/README.md)
* [API publique de paiement](api-index.md)
