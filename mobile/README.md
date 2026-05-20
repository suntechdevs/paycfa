---
description: SDK et intégrations mobiles pour accepter les paiements Intram dans vos applications iOS et Android.
---

# MOBILE

Intégrez Intram dans vos applications mobiles pour proposer un parcours de paiement fluide à vos utilisateurs.

## SDK disponibles

| Plateforme | Statut | Documentation |
| :--- | :---: | :--- |
| **Flutter** (iOS + Android) | ✅ | [Flutter](flutter.md) |
| iOS natif (Swift) | 🔜 | À venir — utilisez le [widget WebView](#alternative-webview-pour-les-stacks-natives) en attendant |
| Android natif (Kotlin / Java) | 🔜 | À venir — utilisez le [widget WebView](#alternative-webview-pour-les-stacks-natives) en attendant |
| React Native | 🔜 | À venir — utilisez le [widget WebView](#alternative-webview-pour-les-stacks-natives) en attendant |

## Alternative WebView pour les stacks natives

En attendant un SDK natif, vous pouvez intégrer le paiement Intram dans n'importe quelle app mobile en chargeant le widget JavaScript dans une **WebView** :

1. Créez la transaction depuis votre backend via l'[API publique](../backend/http.md) ou la [Merchant API](../merchant-api/reference/payment-requests.md).
2. Récupérez l'URL du gateway (`gateway_url` retourné par `/payment-requests`, ou `receipt_url` retourné par `/payments/request`).
3. Chargez cette URL dans une `WebView` (iOS), `WebView` (Android) ou composant équivalent.
4. Écoutez les redirections vers vos `success_url` / `cancel_url` pour détecter la fin du parcours.
5. Vérifiez le statut final en interrogeant l'[API depuis votre backend](../merchant-api/reference/transactions.md) — ne vous fiez pas uniquement à la redirection côté client.

## Voir aussi

* [Flutter SDK](flutter.md) — le client mobile recommandé
* [JavaScript widget](../fontend/javascript.md) — pour les stacks hybrides ou WebView
* [Merchant API — Payment requests](../merchant-api/reference/payment-requests.md) — créer la demande de paiement côté backend
* [Webhooks signés](../merchant-api/webhooks.md) — recevoir le résultat final côté serveur en push
