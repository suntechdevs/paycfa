---
description: >-
  Intram Scholar — gestion complète des frais de scolarité, élèves, parents,
  classes et années académiques, avec encaissement en ligne et hors-ligne.
---

# Intram Scholar

**Intram Scholar** est la solution complète pour les **établissements scolaires** : écoles privées, instituts, centres de formation. Vous y gérez vos années académiques, classes, élèves, parents, frais et leurs échéances, paiements en ligne et hors-ligne, factures et rappels automatisés — le tout depuis un dashboard dédié.

{% hint style="info" %}
**Disponible uniquement** pour les types de compte **Entreprise** et **Association**. Les types Particulier et Auto-entrepreneur n'ont pas accès à ce module. Voir [Activation du compte marchand](../account/account-activation.md).
{% endhint %}

## Cas d'usage typiques

* **École primaire / collège / lycée privé** — facturation annuelle découpée en trimestres ou mensualités
* **Université / institut supérieur** — frais d'inscription + frais de scolarité par semestre
* **Centre de formation** — encaissement par session avec possibilité de paiement échelonné
* **École spécialisée** (musique, langue, sport…) — abonnements + frais d'inscription récurrents

## Concepts clés

### École

L'entité de base. Un marchand peut posséder **plusieurs écoles** depuis le même compte (utile pour les groupes scolaires). Chaque école a son propre nom, adresse, contact, logo et paramètres.

### Année académique

Définit le calendrier scolaire (ex : 2024-2025). Tout est organisé autour des années — les classes, frais et inscriptions sont rattachés à une année académique précise. Cela permet de **clore une année** et passer à la suivante sans perdre l'historique.

### Classe

Un regroupement d'élèves au sein d'une année académique. Chaque classe a son nom (6ème A, CM2, L1 Informatique…) et est rattachée à une année. Les frais sont configurés par classe.

### Frais (Fee)

Un montant à percevoir associé à un libellé (scolarité, inscription, cantine, transport, uniforme…). Chaque frais peut être :
* **Forfaitaire** — un montant fixe payable en une fois
* **Échéancé** — découpé en plusieurs versements (`instalments`) avec pourcentage et date butoir pour chaque échéance

Exemple : frais de scolarité 600 000 XOF découpé en 3 tranches : 40 % (octobre), 30 % (janvier), 30 % (avril).

### Élève (Student)

Une fiche par élève contenant son identité, sa classe, son statut, ses frais assignés et l'historique de ses paiements.

### Parent

Une fiche par responsable (parent, tuteur) reliée à un ou plusieurs élèves. Les rappels et notifications de paiement sont envoyés aux parents.

### Réductions (ReductionFee)

Remise appliquée à un élève sur un frais donné (ex : -50% sur la scolarité pour les boursiers, -10% pour le second enfant d'une fratrie).

## Comment ça marche

```
1. Vous créez votre école et configurez l'année académique en cours
2. Vous définissez vos classes et les frais associés (forfaitaires ou échéancés)
3. Vous inscrivez vos élèves dans leurs classes
   ├── Import depuis Excel / CSV pour les inscriptions en masse
   └── Ou saisie individuelle
4. Vous associez les frais à chaque élève (et appliquez des réductions si besoin)
5. Le système génère automatiquement les échéances et factures
6. Les parents reçoivent les notifications (email + SMS) avec un lien pour payer
7. Vous suivez les paiements en temps réel
   ├── Paiements en ligne (Mobile Money, carte) — créditent automatiquement
   └── Paiements manuels (espèces, virement) — vous les enregistrez vous-même
8. Rappels automatiques pour les échéances en retard
```

## Configurer une école

1. Menu **Intram Scholar → Écoles → Nouvelle école**
2. Renseignez : nom, sigle, adresse, contact, logo, description
3. Configurez les paramètres : devise, modes de paiement autorisés, modèle de facture
4. Créez l'**année académique** en cours (date début + date fin)
5. Créez vos **classes** et configurez les **frais** par classe

## Gérer les élèves

Menu **Intram Scholar → Élèves** :
* **Liste paginée** avec recherche par nom, classe, statut de paiement
* **Inscription individuelle** ou **import en masse** depuis un fichier Excel/CSV (le template d'import est téléchargeable)
* **Fiche élève** : informations, parents liés, frais affectés, historique des paiements, échéances à venir, solde restant dû
* **Mutation** : déplacer un élève d'une classe à une autre en conservant son historique

## Suivre les paiements

Menu **Intram Scholar → Paiements** ou **Statistiques** :

* **Vue d'ensemble** par école : encaissé sur la période, taux de recouvrement, top classes
* **Vue par classe** : qui a payé, qui doit, montant collecté vs attendu
* **Vue par élève** : timeline des règlements, prochaines échéances, retards
* **Export comptable** : CSV par école, par classe, par période

## Rappels et notifications automatiques

Le module envoie automatiquement :
* **Notifications de nouvelle facture** dès qu'une échéance est due
* **Rappels J-7, J-3, J-0** avant chaque échéance
* **Rappels de retard** à intervalle configurable après la date butoir
* **Confirmations de paiement** lors de chaque encaissement

Les canaux disponibles : **email**, **SMS**, **notification push** (si l'app mobile est utilisée par le parent). Configurables par école dans **Intram Scholar → Paramètres → Notifications**.

## Paiements en ligne vs manuels

| Origine | Comment | Frais |
| :--- | :--- | :---: |
| **Paiement en ligne** | Le parent paie via le lien reçu (Mobile Money, carte) | ✅ Frais standards |
| **Paiement manuel** | Vous enregistrez un règlement reçu hors plateforme (espèces, virement, chèque) | ❌ Aucun frais Intram |

Les paiements manuels conservent une **trace identique** dans l'historique de l'élève — vous avez une vue unifiée pour votre comptabilité.

## Factures

Chaque échéance payée génère une **facture PDF** téléchargeable depuis la fiche élève. Le modèle peut être personnalisé avec votre logo et vos mentions légales depuis **Intram Scholar → Paramètres → Modèle de facture**.

## Frais

Les paiements **en ligne** suivent le [modèle standard](../payment/fees.md). Les paiements manuels enregistrés ne génèrent aucun frais Intram.

## Intégration API (optionnelle)

Si vous voulez synchroniser Intram Scholar avec un SI existant (logiciel de gestion scolaire, ERP), deux briques :

### Recevoir les notifications de paiement

Configurez un [webhook](../merchant-api/webhooks.md) sur `payment_request.paid` — chaque encaissement scolarité déclenchera un appel signé vers votre URL. Vous pouvez ensuite mettre à jour votre système avec la référence de la transaction et celle de l'élève (transmise dans `webhook_data`).

### Récupérer les transactions Scholar depuis votre backend

[`GET /merchant/transactions`](../merchant-api/reference/transactions.md) renvoie toutes vos transactions. Les transactions issues de Scholar sont identifiables via le champ `webhook_data` que vous avez configuré.

## Limites

* Disponibilité : **Entreprise** et **Association** uniquement (cf. [Types de comptes](../account/account-activation.md)).
* Soumises aux limites hebdomadaires de votre type de compte (les comptes Entreprise/Association ont des limites **illimitées** par défaut).
* Nombre d'écoles, années, classes, élèves, frais : **illimité**.
* Import en masse : pas de limite stricte, mais privilégiez des lots de < 500 lignes pour de meilleures performances.

## Voir aussi

* [Intram Immo](intram-immo.md) — pour la gestion immobilière (loyers, charges, baux)
* [Intram Business](intram-business.md) — alternative légère sans gestion académique structurée
* [Activation du compte marchand](../account/account-activation.md) — disponibilité par type de compte
* [Frais](../payment/fees.md)
* [Merchant API — Webhooks](../merchant-api/webhooks.md) — synchroniser avec un SI externe
