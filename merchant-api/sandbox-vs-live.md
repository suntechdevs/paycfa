---
description: >-
  Différences entre les modes sandbox et live, et configuration de la whitelist
  d'IPs obligatoire en mode live.
---

# Sandbox vs Live + IP allowlist

Le mode (sandbox vs live) est **déterminé côté serveur** par la clé que tu présentes — pas par un paramètre de requête. Une clé `pk_sandbox_…` tape un wallet de test ; une clé `pk_live_…` tape l'argent réel.

## Tableau comparatif

| Critère | Sandbox (`pk_sandbox_…`) | Live (`pk_live_…`) |
| :--- | :--- | :--- |
| Wallet touché | Wallet sandbox (jouet, isolé) | Wallet de production (argent réel) |
| Providers externes (MTN, MOOV, Stripe…) | Endpoints sandbox | Endpoints production |
| Signature HMAC | Requise | Requise |
| Idempotency-Key | Requise sur POST/PUT/PATCH | Requise sur POST/PUT/PATCH |
| **Whitelist IP** | **Désactivée** — appel depuis n'importe où | **Obligatoire** — sinon 403 systématique |
| Limites par opération | Permissives | Standards |
| Données stockées | Effacables sans impact | Auditées, comptables |

## La whitelist IP en mode live

C'est le filet de sécurité qui empêche qu'une clé live compromise soit utilisée depuis un serveur inconnu.

**Comment ça marche :**

1. Chaque marchand maintient une liste d'IPs ou de CIDR autorisés (max 20 entrées).
2. À chaque requête en mode live, l'API résout l'IP source (via `X-Real-IP` fourni par Nginx ou socket direct) et la compare à la liste.
3. Si l'IP correspond à une entrée exacte ou tombe dans un CIDR → la requête passe à l'étape suivante (vérification signature).
4. Sinon → 403, requête rejetée avant même de lire le body. La tentative est loggée (audit).

**Important — fail-closed** : une liste vide ne signifie pas "accepter tout". Elle signifie "rejeter tout". C'est volontaire : une clé live mal configurée ne peut jamais servir de trafic depuis l'Internet ouvert.

## Configurer la whitelist depuis le dashboard

* Menu **Développeurs → IPs autorisées**
* Récupère l'IP publique de ton serveur backend depuis ce serveur :

```bash
curl -4 ifconfig.me
```

{% hint style="warning" %}
**L'IP de ton navigateur n'est presque jamais la bonne** — c'est l'IP du serveur d'où ton backend va appeler l'API. Sauf si tu testes en local depuis la même machine que ton backend.
{% endhint %}

* Clique "Ajouter un serveur"
* Renseigne l'IP exacte (`203.0.113.4`) ou un bloc CIDR (`203.0.113.0/29` pour un cluster de 8 IPs derrière le même NAT)
* Sauvegarde — le changement est immédiat

## Formats acceptés

| Format | Exemple | Quand l'utiliser |
| :--- | :--- | :--- |
| IPv4 exacte | `203.0.113.4` | Un serveur unique |
| IPv6 exacte | `2001:db8::1` | Un serveur unique IPv6 |
| CIDR IPv4 | `203.0.113.0/29` | Cluster derrière un NAT, pool de sortie d'un load balancer |
| CIDR IPv6 | `2001:db8::/32` | Bloc IPv6 attribué par ton fournisseur |

## Cas d'usage typiques

| Topologie | Recommandation |
| :--- | :--- |
| 1 serveur backend, 1 IP fixe | Ajoute l'IP exacte |
| Plusieurs serveurs derrière un NAT unique | Ajoute l'IP du NAT (une seule entrée) |
| Cluster Kubernetes avec egress fixe | Ajoute l'IP/CIDR de l'egress gateway |
| Auto-scaling sans IP fixe | Mets en place un NAT egress avec IP statique, puis whitelist celle-ci |
| Plusieurs régions cloud | Ajoute chaque IP de sortie (max 20 entrées au total) |

{% hint style="danger" %}
Ne whitelistez pas `0.0.0.0/0` (ouvrir à tout) — l'API rejettera de toute façon une entrée aussi large, et c'est l'inverse du but recherché.
{% endhint %}

## Erreurs possibles

```json
// Liste vide sur clé live
HTTP 403
{
  "error": true,
  "code": "ip_allowlist_empty",
  "message": "Live API keys require at least one allowed IP. Configure it in your merchant dashboard before going live."
}

// IP appelante absente de la liste
HTTP 403
{
  "error": true,
  "code": "ip_not_allowed",
  "message": "This source IP is not in the merchant allowlist",
  "client_ip": "203.0.113.99"
}
```

## Tester en sandbox sans whitelist

1. Crée une clé sandbox depuis le dashboard (mode `SANDBOX`)
2. Utilise cette clé dans Postman / cURL / ton intégration locale — aucune restriction IP
3. Quand tout fonctionne, génère une clé live et configure la whitelist **avant** ton premier appel live
