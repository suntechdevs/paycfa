---
description: >-
  Intram Direct — créez et partagez des liens de paiement personnalisés pour
  encaisser sans site web ni intégration complexe.
---

# Intram Direct

**Intram Direct** est la solution la plus simple pour commencer à encaisser : vous créez un **lien de paiement** depuis le dashboard, vous le partagez à vos clients (SMS, email, WhatsApp, réseaux sociaux, QR code), et l'argent crédite votre wallet dès qu'ils paient.

Aucun site web, aucun développement requis. Disponible pour **tous les types de comptes** (Particulier, Auto-entrepreneur, Entreprise, Association).

## Cas d'usage typiques

* **Indépendant / freelance** — facturer un client ponctuel sans avoir à installer un système de paiement complet
* **Petit commerce** — partager un lien fixe pour un produit unique (formation, abonnement annuel, ticket d'événement)
* **Collecte de fonds** — récolter des dons pour une association ou un projet
* **Vente sur les réseaux sociaux** — coller le lien dans une bio Instagram, WhatsApp Business ou une story
* **Facturation manuelle** — envoyer un lien personnalisé à un client après accord verbal

## Comment ça marche

1. **Vous créez un lien** depuis le dashboard avec : libellé, prix, description (optionnelle), image (optionnelle), type de paiement
2. **Vous obtenez** une URL courte (`https://gateway.intram.org/<référence>`) et un **QR code** prêts à partager
3. **Votre client ouvre le lien**, voit le détail, choisit son moyen de paiement et règle
4. **L'argent crédite votre wallet** dès la confirmation du provider
5. **Vous êtes notifié** par email + dashboard, et — si vous avez configuré un [webhook](../merchant-api/webhooks.md) — par notification signée

## Créer un lien depuis le dashboard

1. Connectez-vous sur [https://app.intram.org](https://app.intram.org)
2. Menu **Intram Direct → Liens de paiement → Nouveau lien**
3. Remplissez :
   * **Libellé** — nom interne pour vous y retrouver
   * **Prix** — montant fixe à encaisser
   * **Utilité / Description** — ce que voit le client sur la page de paiement
   * **Image** — optionnelle, affichée sur la page (logo produit, photo, etc.)
   * **URL de redirection** — page de votre site où renvoyer le client après paiement (succès)
   * **Type de paiement** — moyens autorisés (Mobile Money, Cartes…)
4. Validez — le lien est généré, accompagné d'un QR code téléchargeable

## Gérer vos liens

Depuis **Intram Direct → Liens de paiement** vous pouvez :
* **Activer / désactiver** un lien sans le supprimer (utile pour les ventes flash)
* **Archiver** un lien obsolète sans perdre l'historique des transactions
* **Voir les statistiques** par lien : nombre de paiements, montant collecté, taux de succès
* **Exporter** la liste des paiements en CSV pour votre comptabilité

## Suivre vos transactions

Menu **Intram Direct → Statistiques** :

* **Revenu total** sur la période choisie
* **Taux de succès** (transactions réussies / tentatives)
* **Liens actifs** vs archivés
* **Filtrage** par lien, par statut, par plage de dates
* **Détail transaction par transaction** avec recherche

Vous pouvez aussi consulter chaque transaction individuelle via [`GET /merchant/transactions/:reference`](../merchant-api/reference/transactions.md) depuis votre backend.

## Frais

Une **surcharge plateforme** s'applique aux transactions encaissées via Intram Direct (en plus des frais standards du moyen de paiement choisi par le client). Elle est déduite du montant net versé au marchand.

Voir [Frais](../payment/fees.md) pour le détail du calcul et vos taux applicables.

## Intégration API (optionnelle)

Si vous voulez **automatiser la création de liens** ou les exposer dans votre propre interface, deux options :

### Option 1 — Créer une transaction one-shot depuis votre backend

Plutôt qu'un lien réutilisable, créez une [demande de paiement à usage unique](../merchant-api/reference/payment-requests.md) via la Merchant API :

```bash
POST /v1/payment-requests
{
  "invoice": {
    "amount": 12000,
    "currency": "XOF",
    "description": "Commande #4521",
    "customer": { "email": "client@example.com" }
  },
  "return_urls": {
    "success": "https://shop.example.com/ok",
    "cancel":  "https://shop.example.com/no"
  }
}
```

Vous obtenez un `gateway_url` à présenter / envoyer au client. Idéal pour les paniers de boutique en ligne.

### Option 2 — Recevoir les notifications de paiement de vos liens existants

Configurez un [webhook](../merchant-api/webhooks.md) avec l'event `payment_request.paid` (et `payment_request.failed`) pour recevoir en temps réel le résultat de chaque paiement réalisé via vos liens Intram Direct.

## Limites

* Soumises aux [limites hebdomadaires de votre type de compte](../account/account-activation.md#comprendre-les-limites-hebdomadaires).
* Un même lien peut servir un nombre **illimité** de paiements (sauf si vous le désactivez).
* La devise par défaut est `XOF` ; voir [Devises supportées](../payment/supported-devices.md) pour les autres.

## Voir aussi

* [Intram Business](intram-business.md) — pour gérer un vrai catalogue d'offres et des clients récurrents
* [Frais](../payment/fees.md) — comprendre le coût d'une transaction
* [Merchant API — Payment requests](../merchant-api/reference/payment-requests.md) — l'équivalent API d'un lien à usage unique
* [Webhooks signés](../merchant-api/webhooks.md) — recevoir les notifications de paiement
