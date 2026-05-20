---
description: >-
  Intram Immo — gestion complète de votre parc immobilier locatif : immeubles,
  logements, locataires, baux, loyers et charges récurrentes.
---

# Intram Immo

**Intram Immo** est la solution dédiée aux **propriétaires, agences immobilières et gestionnaires de biens locatifs**. Vous y gérez vos immeubles, vos logements (chambres, studios, appartements, villas, boutiques…), vos locataires et leurs baux, et vous encaissez automatiquement loyers et charges — en ligne ou hors-ligne — avec rappels, factures et état des lieux intégrés.

{% hint style="info" %}
**Disponible uniquement** pour les types de compte **Entreprise** et **Association**. Les types Particulier et Auto-entrepreneur n'ont pas accès à ce module. Voir [Activation du compte marchand](../account/account-activation.md).
{% endhint %}

## Cas d'usage typiques

* **Propriétaire bailleur** — gestion directe de quelques biens (studios étudiants, appartements meublés)
* **Agence immobilière** — gestion locative pour le compte de propriétaires multiples
* **Résidence étudiante / coliving** — gestion d'un immeuble avec multiples chambres ou colocations
* **Gestion mixte habitation + commerce** — immeubles avec rez-de-chaussée commercial et étages résidentiels
* **Foncière / société immobilière** — pilotage centralisé d'un portefeuille d'actifs

## Concepts clés

### Immeuble (Building)

L'unité géographique de votre patrimoine. Trois types :
* **Immeuble** — bâtiment avec plusieurs logements
* **Appartement** — bien individuel
* **Résidence étudiante** — immeuble dédié à la location étudiante (chambres, studios)

Chaque immeuble a son adresse, ses documents (titre de propriété, plans), et un délai de paiement par défaut configurable.

### Logement (Room)

Une unité louable au sein d'un immeuble. Types disponibles : **chambre**, **studio**, **appartement**, **maison**, **villa**, **boutique**, **colocation**, **T2 / T3 / T4 / T5**.

Chaque logement précise s'il est **meublé**, et si **eau / électricité / ordures** sont incluses dans le loyer. Statut : `libre` ou `occupé`.

### Locataire (Tenant)

Une fiche par locataire avec identité, contact, justificatifs et historique. Un locataire peut avoir **plusieurs baux** (déménagement interne, location de plusieurs biens chez vous).

### Bail (Lease)

Le contrat qui lie un locataire à un logement. C'est le cœur du module — il contient :

* **Dates** : début, fin, renouvellement automatique optionnel
* **Loyer** : montant et fréquence (mensuel, trimestriel, semestriel, annuel)
* **Avance** : nombre de mois d'avance demandés (et statut payé / en attente)
* **Caution** : montant retenu (et statut détenue / partiellement remboursée / remboursée)
* **Garant** : optionnel, avec coordonnées et lien de parenté
* **Règles d'occupation** : habitation ou commerce, sous-location, animaux, responsabilité des charges
* **Préavis** : durée côté locataire / côté propriétaire
* **Contact local** : gardien, agent, propriétaire référent
* **Contrat PDF** signé électroniquement par OTP (email ou SMS)
* **État des lieux** d'entrée et de sortie (notes + photos)
* **Documents** : pièces jointes (max 10 par bail), partageables ou non avec le locataire

Statut du bail : `actif`, `résilié`, `renouvelé`.

### Charge récurrente (RecurringCharge)

Frais périodiques liés à un logement ou un bail (eau, électricité, gardiennage, syndic…). Vous définissez le montant et la périodicité — le système génère les factures automatiquement.

### Réduction (Discount) et Pénalité (PenaltyConfig)

* **Réduction** — remise appliquée à un locataire (geste commercial, ancienneté, fidélité)
* **Pénalité** — règle de majoration appliquée automatiquement en cas de retard de paiement, configurable par immeuble

### Configuration des rappels (ReminderConfig)

Définit quand et comment relancer un locataire avant et après l'échéance (J-7, J-3, J+1, J+15…), par email, SMS ou notification push.

## Comment ça marche

```
1. Vous créez votre immeuble (adresse, type, documents)
2. Vous ajoutez vos logements avec leurs caractéristiques (type, surface, charges incluses)
3. Vous créez la fiche locataire (identité, contact, justificatifs)
4. Vous générez le bail
   ├── Dates, loyer, fréquence
   ├── Avance + caution
   ├── Règles d'occupation
   └── Garant optionnel
5. Le contrat PDF est généré, signé par vous, puis envoyé au locataire pour signature OTP
6. Vous réalisez l'état des lieux d'entrée (notes + photos)
7. Le système génère automatiquement les échéances de loyer + charges
8. Les locataires reçoivent les notifications avec lien de paiement
9. Vous suivez les paiements en temps réel
   ├── Paiements en ligne (Mobile Money, carte) — créditent automatiquement
   └── Paiements manuels (espèces, virement) — vous les enregistrez vous-même
10. Rappels automatiques + pénalités appliquées en cas de retard
11. À la résiliation : état des lieux de sortie, déductions, remboursement caution
```

## Configurer un immeuble

1. Menu **Intram Immo → Immeubles → Nouvel immeuble**
2. Renseignez : nom, type (immeuble / appartement / résidence étudiante), adresse, description
3. Téléversez les **documents** (titre de propriété, plans, autorisations)
4. Configurez le **délai de paiement par défaut** (nombre de jours après émission d'une facture avant qu'elle soit considérée en retard)
5. Ajoutez vos **logements** un à un (ou par lot)

## Configurer un logement

Pour chaque logement :
* **Type** — chambre, studio, T2…
* **Surface** et **étage** (optionnel)
* **Meublé** ou non
* **Charges incluses** — cochez si eau, électricité, ordures sont comprises dans le loyer
* **Loyer indicatif** (peut être ajusté à la signature du bail)
* **Photos** (optionnel)

## Créer un bail

Menu **Intram Immo → Baux → Nouveau bail** :

1. **Locataire** — sélectionner un locataire existant ou créer une fiche
2. **Logement** — choisir parmi vos logements libres
3. **Dates** — début, fin, renouvellement automatique ?
4. **Loyer** — montant et fréquence (mensuel par défaut)
5. **Avance** — nombre de mois et statut initial (payé / en attente)
6. **Caution** — nombre de mois retenus
7. **Règles** — usage (habitation / commerce), sous-location, animaux, charges
8. **Préavis** — délais en jours
9. **Garant** — si exigé, coordonnées complètes
10. **Génération du contrat PDF** — vous le signez, le locataire reçoit un lien OTP pour signer

## État des lieux

* **À l'entrée** : ajoutez des photos pièce par pièce + une note descriptive
* **À la sortie** : refaites le tour, identifiez les dégradations éventuelles, chiffrez les déductions sur la caution

Les états des lieux sont **horodatés** et **signés numériquement** par les deux parties.

## Suivre les paiements

Menu **Intram Immo → Paiements** ou **Statistiques** :

* **Vue d'ensemble par immeuble** : taux d'occupation, encaissé sur la période, impayés
* **Vue par logement** : locataire en place, prochaine échéance, retards
* **Vue par locataire** : historique des règlements, baux en cours, solde
* **Export comptable** : CSV par immeuble, par période

## Rappels et notifications

Le module envoie automatiquement :
* **Avis d'échéance** quelques jours avant la date butoir (J-7, J-3 — configurables)
* **Rappels de retard** à intervalle paramétrable (J+1, J+7, J+15…)
* **Application de pénalités** en cas de retard prolongé (selon la configuration de l'immeuble)
* **Confirmations de paiement** à chaque encaissement
* **Notifications de fin de bail** approchant pour les baux non auto-renouvelables

Canaux : **email**, **SMS**, **notification push**. Configurables par immeuble dans **Intram Immo → Paramètres → Notifications**.

## Paiements en ligne vs manuels

| Origine | Comment | Frais |
| :--- | :--- | :---: |
| **Paiement en ligne** | Le locataire paie via le lien reçu (Mobile Money, carte) | ✅ Frais standards |
| **Paiement manuel** | Vous enregistrez un règlement reçu hors plateforme (espèces, virement, chèque) | ❌ Aucun frais Intram |

Les paiements manuels conservent une **trace identique** dans l'historique du bail.

## Résiliation d'un bail

Depuis la fiche bail → **Résilier** :
1. Indiquez la **raison** : demande du locataire, non-paiement, manquement, accord amiable, fin de contrat
2. Renseignez la **date de préavis** et la **date de départ**
3. Réalisez l'**état des lieux de sortie** (photos + notes)
4. Saisissez les **déductions éventuelles** (dégradations avec libellé et montant)
5. Le système calcule automatiquement le **remboursement caution** (caution − déductions)
6. Vous validez — le statut bail passe à `résilié`, le logement repasse `libre`

## Factures

Chaque échéance payée (loyer ou charge) génère une **facture PDF** téléchargeable depuis la fiche locataire ou la fiche bail. Modèle personnalisable avec votre logo et vos mentions légales depuis **Intram Immo → Paramètres → Modèle de facture**.

## Frais

Les paiements **en ligne** suivent le [modèle standard](../payment/fees.md). Les paiements manuels enregistrés ne génèrent aucun frais Intram.

## Intégration API (optionnelle)

Si vous voulez synchroniser Intram Immo avec un SI existant (logiciel de gestion locative, ERP, comptabilité) :

### Recevoir les notifications de paiement

Configurez un [webhook](../merchant-api/webhooks.md) sur `payment_request.paid` — chaque encaissement loyer ou charge déclenchera un appel signé vers votre URL. Vous pouvez transmettre la référence du bail et celle du logement via le champ `webhook_data`.

### Récupérer les transactions Immo depuis votre backend

[`GET /merchant/transactions`](../merchant-api/reference/transactions.md) renvoie toutes vos transactions. Les transactions issues d'Immo sont identifiables via le champ `webhook_data` que vous avez configuré.

## Limites

* Disponibilité : **Entreprise** et **Association** uniquement (cf. [Types de comptes](../account/account-activation.md)).
* Soumises aux limites hebdomadaires de votre type de compte (les comptes Entreprise/Association ont des limites **illimitées** par défaut).
* Nombre d'immeubles, logements, locataires, baux : **illimité**.
* **Documents par bail** : **10 maximum** (titre de propriété, contrat d'assurance, états des lieux, etc.).
* **Photos par état des lieux** : pas de limite stricte, mais privilégiez des images compressées (< 2 Mo chacune).

## Voir aussi

* [Intram Scholar](intram-scholar.md) — pour la gestion des frais de scolarité
* [Intram Business](intram-business.md) — alternative légère pour de la facturation simple
* [Activation du compte marchand](../account/account-activation.md) — disponibilité par type de compte
* [Frais](../payment/fees.md)
* [Merchant API — Webhooks](../merchant-api/webhooks.md) — synchroniser avec un SI externe
