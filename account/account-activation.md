---
description: >-
  Activer votre compte marchand Intram selon votre type (particulier,
  auto-entrepreneur, entreprise, association) — documents requis et limites
  applicables.
---

# Activation du compte marchand

L'activation transforme votre compte utilisateur en **compte marchand validé**, condition indispensable pour utiliser les clés `LIVE` et encaisser de vrais paiements. Vos clés `SANDBOX` fonctionnent dès l'inscription, sans validation.

## Choisir votre type de compte

Intram propose **4 types de comptes** correspondant à votre statut légal. Chaque type donne accès à des modules différents et applique des limites de transactions distinctes.

| Type | Pour qui | Limites hebdomadaires | Modules accessibles |
| :--- | :--- | :--- | :--- |
| **Particulier** | Personne physique sans activité déclarée | 10 transactions / 500 000 XOF par semaine | [Intram Direct](../modules/intram-direct.md), [Intram Business](../modules/intram-business.md) |
| **Auto-entrepreneur** | Activité individuelle déclarée | 50 transactions / 5 000 000 XOF par semaine | [Intram Direct](../modules/intram-direct.md), [Intram Business](../modules/intram-business.md) |
| **Entreprise** | Société immatriculée (RCCM, SARL, SA…) | Illimité | Tous les modules ([Direct](../modules/intram-direct.md), [Business](../modules/intram-business.md), [Scholar](../modules/intram-scholar.md), [Immo](../modules/intram-immo.md)) |
| **Association** | Association loi 1901 ou équivalent local | Illimité | Tous les modules ([Direct](../modules/intram-direct.md), [Business](../modules/intram-business.md), [Scholar](../modules/intram-scholar.md), [Immo](../modules/intram-immo.md)) |

{% hint style="info" %}
Si vous démarrez petit, le type **Particulier** vous permet de tester l'encaissement réel rapidement sans paperasse lourde. Vous pourrez monter en gamme dès que votre activité grossit — voir [Évolution du type de compte](#évolution-du-type-de-compte).
{% endhint %}

## Documents requis par type

| Type | Pièce d'identité | Selfie | Justificatif d'activité |
| :--- | :---: | :---: | :--- |
| Particulier | ✅ CNI, passeport ou CIP | ✅ | — |
| Auto-entrepreneur | ✅ CNI, passeport ou CIP | ✅ | — |
| Entreprise | ✅ CNI, passeport ou CIP | ✅ | ✅ Registre du commerce + numéro |
| Association | ✅ CNI, passeport ou CIP | ✅ | ✅ Récépissé / statuts d'association |

Pour les entreprises et associations, un **numéro fiscal** peut également être demandé selon la juridiction.

## Étapes d'activation

### 1. Choisir le type de compte
Dans **Paramètres → Compte business**, sélectionnez le type qui correspond à votre statut légal.

### 2. Renseigner les informations
* Particulier / Auto-entrepreneur : nom complet, date de naissance, téléphone, adresse
* Entreprise / Association : raison sociale, numéro de registre, numéro fiscal, adresse du siège, contact principal

### 3. Téléverser les pièces justificatives
Selon votre type, fournissez les documents listés [ci-dessus](#documents-requis-par-type).

{% hint style="warning" %}
Vérifiez que vos documents sont **lisibles, non rognés, au format accepté** (PDF, JPG, PNG) et que la taille respecte la limite affichée dans le formulaire. Un document non conforme entraîne un refus et oblige à recommencer cette étape.
{% endhint %}

### 4. Soumettre pour vérification
Une fois tous les champs remplis et les documents téléversés, cliquez sur **Soumettre**. Le statut KYC passe à `pending`.

### 5. Attendre la validation
Notre équipe vérifie votre dossier sous **1 à 3 jours ouvrés** :

* ✅ **Validé** — vous recevez un email de confirmation, votre compte passe en `verified`, vos clés `LIVE` deviennent utilisables, les limites correspondant à votre type sont appliquées.
* ❌ **Refusé** — vous recevez un email avec la raison précise. Le statut KYC passe à `rejected`. Vous pouvez resoumettre directement depuis le dashboard après correction.

## Pendant la validation

Vous pouvez intégralement développer votre intégration en attendant :
* Utilisez les clés `SANDBOX` (Menu **Développeurs → API → mode SANDBOX**)
* Toutes les fonctionnalités sont disponibles en sandbox sans restriction de type ni de plafond
* Aucune somme réelle n'est manipulée — les wallets sandbox sont isolés

## Comprendre les limites hebdomadaires

Les limites s'appliquent **uniquement en mode live** et se calculent sur une **fenêtre glissante de 7 jours**.

* **`weekly_count`** — nombre maximum de transactions encaissées sur 7 jours
* **`weekly_amount`** — somme maximum des montants encaissés sur 7 jours, exprimée dans la devise principale (XOF par défaut)

Quand l'une des deux limites est atteinte, les nouvelles transactions sont rejetées jusqu'à ce que la fenêtre glissante se libère.

| Type | Compteur transactions | Plafond montant |
| :--- | :---: | :---: |
| Particulier | 10 / semaine | 500 000 XOF / semaine |
| Auto-entrepreneur | 50 / semaine | 5 000 000 XOF / semaine |
| Entreprise | Illimité | Illimité |
| Association | Illimité | Illimité |

{% hint style="info" %}
Si vos limites sont trop basses pour votre activité, demandez un **upgrade de type** (voir ci-dessous) ou contactez le support pour un **dépassement ponctuel** négocié.
{% endhint %}

## Évolution du type de compte

Vous pouvez faire évoluer votre compte vers un type supérieur sans recréer un compte. Les transitions autorisées :

| Depuis | Vers | Documents supplémentaires à fournir |
| :--- | :--- | :--- |
| Particulier | Auto-entrepreneur | (aucun supplémentaire) |
| Particulier | Entreprise | Registre du commerce + numéro fiscal |
| Particulier | Association | Récépissé / statuts d'association |
| Auto-entrepreneur | Entreprise | Registre du commerce + numéro fiscal |
| Auto-entrepreneur | Association | Récépissé / statuts d'association |
| Entreprise | Association | Récépissé / statuts d'association |
| Association | Entreprise | Registre du commerce + numéro fiscal |

La **rétrogradation** (par exemple Entreprise → Particulier) n'est **pas proposée** depuis l'interface. Si vous avez besoin de changer dans ce sens, contactez le support.

### Comment demander un upgrade

1. **Paramètres → Compte business → Évoluer mon compte**
2. Sélectionnez le type cible
3. Remplissez les nouveaux champs requis (nouvelle raison sociale, nouveau numéro, etc.)
4. Téléversez les nouveaux justificatifs
5. Soumettez — le statut passe à `pending_upgrade` ; vos limites actuelles restent appliquées en attendant la validation
6. Une fois validé : votre type bascule, les nouvelles limites s'appliquent, et un historique de changement est enregistré

## Statuts KYC

Le champ `kyc_status` visible dans votre dashboard suit ce cycle :

| Statut | Sens | Action attendue |
| :--- | :--- | :--- |
| (non renseigné) | Compte business créé, KYC pas encore soumis | Compléter le formulaire d'activation |
| `pending` | Dossier soumis, en cours d'examen | Patienter (1 à 3 jours ouvrés) |
| `verified` | Dossier accepté — vous pouvez utiliser les clés LIVE | — |
| `rejected` | Dossier refusé — voir la raison dans l'email reçu | Corriger et resoumettre |

## Voir aussi

* [Inscription](register.md) — créer le compte utilisateur initial
* [Récupérer vos clés API](../README.md#récupérer-vos-clés-api)
* [Devises et moyens de paiement supportés](../payment/supported-devices.md)
* [Frais](../payment/fees.md)
