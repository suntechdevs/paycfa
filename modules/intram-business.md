---
description: >-
  Intram Business — catalogue d'offres, gestion clients, abonnements et
  installments pour piloter votre activité commerciale depuis Intram.
---

# Intram Business

**Intram Business** transforme Intram en mini-CRM commercial. Vous gérez votre **catalogue d'offres** (produits + plans d'abonnement), un **fichier client** dédié, et l'**historique des paiements** liés à chaque client. C'est l'outil pour aller au-delà du paiement ponctuel et structurer votre activité récurrente.

Disponible pour **tous les types de comptes** (Particulier, Auto-entrepreneur, Entreprise, Association).

## Cas d'usage typiques

* **Coach / formateur** — vendre des prestations à l'unité (`product`) ou des packages d'accompagnement à paiements échelonnés (`installments`)
* **École privée hors module Scholar** — facturer des frais de scolarité mensuels (`monthly`)
* **Salle de sport / club** — abonnements mensuels ou annuels avec renouvellement automatique
* **Box / abonnement à la box** — livraisons récurrentes (`weekly`, `monthly`)
* **Logiciel / SaaS** — facturation d'abonnements à votre service
* **Prestations sur devis** — encaissement en plusieurs versements négociés avec le client

## Concepts clés

### Offre (Offering)

Une entrée de votre **catalogue**. Deux types :

| Type | Description | Exemple |
| :--- | :--- | :--- |
| **`product`** | Vente à l'unité, paiement immédiat | "Pack 10 séances de coaching" |
| **`plan`** | Vente récurrente avec calendrier de paiements | "Abonnement mensuel salle de sport" |

### Calendrier (pour les plans)

| `schedule_type` | Comportement |
| :--- | :--- |
| `immediate` | Paiement unique immédiat |
| `daily` | Prélèvement quotidien |
| `weekly` | Prélèvement hebdomadaire |
| `monthly` | Prélèvement mensuel |
| `yearly` | Prélèvement annuel |
| `installments` | Découpage en N échéances (négocié au cas par cas) |

Pour `installments`, vous précisez le nombre d'échéances (minimum 2) — utile pour les ventes en 3, 4 ou 6 fois sans frais.

### Client (MarchandClient)

Une fiche dans votre **carnet client** local. Distincte du compte utilisateur Intram du payeur : un même client peut avoir une fiche chez plusieurs marchands.

Chaque fiche regroupe :
* Coordonnées (nom, email, téléphone)
* Catégorie client (configurable depuis le dashboard)
* Historique de toutes les transactions le concernant
* Solde dû éventuel (ventes non soldées)

### Transaction client (MarchandClientTransaction)

Chaque vente / paiement enregistré est rattaché à un client. Le tableau de bord Business vous montre par client :
* Total dépensé
* Dernière transaction
* Solde restant dû
* Échéances à venir

## Comment ça marche

```
1. Vous créez votre catalogue d'offres depuis le dashboard
   ├── Produits (vente unique)
   └── Plans (abonnement / installments)

2. Vous ajoutez vos clients (fichier local)
   └── Ou ils sont créés automatiquement lors de la première vente

3. Vous facturez :
   ├── via un lien de paiement adossé à l'offre (encaissement online)
   └── via un paiement manuel enregistré (espèces, virement, autre)

4. Le système suit :
   ├── Calendrier des prochaines échéances
   ├── Statuts (à venir / payé / en retard)
   ├── Notifications client (email + SMS)
   └── Solde par client + global
```

## Créer une offre depuis le dashboard

1. Menu **Intram Business → Catalogue → Nouvelle offre**
2. Choisissez le type : **Produit** ou **Plan**
3. Remplissez :
   * **Nom** + description
   * **Prix** (par unité ou par échéance pour les plans)
   * **Image** optionnelle
   * **Catégorie** (organisez votre catalogue)
   * Pour les plans : **type de calendrier** + nombre d'échéances si `installments`
4. Validez — l'offre devient `active` et peut être vendue immédiatement

Les offres peuvent être **archivées** (gardent l'historique des ventes passées sans pouvoir être revendues).

## Gérer vos clients

Menu **Intram Business → Clients** :
* **Liste** des clients avec recherche par nom / email / téléphone
* **Fiche client** : coordonnées, historique des transactions, solde
* **Catégories** : créez vos propres tags (Premium, Particulier, Pro…) pour segmenter
* **Import / export CSV** pour synchroniser avec votre outil habituel

## Suivre vos transactions Business

Menu **Intram Business → Transactions** :
* Liste paginée des paiements (online + enregistrés manuellement)
* Filtre par client, par offre, par statut, par plage de dates
* Détail par transaction : montant, frais, méthode, client, offre liée
* Échéances à venir pour les plans actifs

## Paiements en ligne vs manuels

Intram Business distingue deux origines de paiement :

| Origine | Comment | Frais |
| :--- | :--- | :---: |
| **Paiement en ligne** | Le client paie via un lien généré (Mobile Money, carte) | ✅ Frais standards |
| **Paiement manuel** | Vous enregistrez vous-même un règlement reçu hors plateforme (espèces, virement, chèque) | ❌ Aucun frais Intram |

Les paiements manuels servent à **garder une vue unifiée** de votre compta, sans surcharge.

## Frais

Pour les paiements **en ligne** transitant par Intram Business, les frais suivent le [modèle standard](../payment/fees.md). Les paiements manuels enregistrés ne génèrent aucun frais.

## Intégration API (optionnelle)

Si vous voulez piloter votre catalogue ou recevoir des notifications, deux briques :

### Recevoir les notifications de paiement

Configurez un [webhook](../merchant-api/webhooks.md) abonné à `payment_request.paid` — chaque encaissement Business déclenchera un appel signé vers votre URL.

### Lister vos transactions Business depuis votre backend

[`GET /merchant/transactions?limit=100`](../merchant-api/reference/transactions.md) renvoie l'ensemble des transactions de votre compte. Les transactions issues d'Intram Business sont identifiables par le champ `webhook_data` que vous aurez transmis à la création.

### Rembourser une transaction Business

Utilisez [`POST /merchant/refunds`](../merchant-api/reference/refunds.md) avec la `transaction_reference`. Le refund met à jour automatiquement l'historique du client dans Intram Business.

## Limites

* Soumises aux [limites hebdomadaires de votre type de compte](../account/account-activation.md#comprendre-les-limites-hebdomadaires).
* Nombre d'offres dans le catalogue : illimité.
* Nombre de clients dans le fichier : illimité.
* Plans à `installments` : minimum 2 échéances, pas de maximum théorique.

## Voir aussi

* [Intram Direct](intram-direct.md) — pour les encaissements ponctuels sans gestion client
* [Activation du compte marchand](../account/account-activation.md) — disponibilité par type de compte
* [Frais](../payment/fees.md)
* [Merchant API — Payment requests](../merchant-api/reference/payment-requests.md) — pour créer un lien de paiement programmatiquement
* [Webhooks signés](../merchant-api/webhooks.md) — pour suivre les paiements en temps réel
