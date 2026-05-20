---
description: >-
  Comment les frais Intram sont calculés selon le moyen de paiement, le pays,
  la catégorie marchand et le mode (sandbox / live).
---

# Frais

Les frais Intram s'appliquent à chaque transaction et varient selon plusieurs critères. Cette page explique **le modèle de calcul** ; les **taux actuels** sont visibles dans votre tableau de bord (Menu **Développeurs → API** ou **Paramètres → Frais**), car ils peuvent être personnalisés par marchand.

## Modèle de calcul

Pour chaque transaction, le montant facturé au client est :

```
Montant facturé = Montant transaction + Frais
```

où **Frais = Montant × Taux%**, le taux dépendant du moyen de paiement et de votre profil.

Les frais sont ensuite répartis entre :

| Composante | Description |
| :--- | :--- |
| **Frais opérateur** | Reversés au fournisseur du moyen de paiement (MTN, MOOV, banque, Stripe…) |
| **Commission Intram** | Conservés par Intram pour le service de plateforme |

**Commission Intram = Frais − Frais opérateur**

## Sources des taux (ordre de priorité)

| Priorité | Source | Quand |
| :---: | :--- | :--- |
| 1 | Taux personnalisé par marchand × moyen de paiement | Taux négocié sur-mesure |
| 2 | Taux par défaut du moyen | Taux public standard |
| 3 | `0` | Si aucun taux n'est défini (cas exceptionnel — contactez le support) |

Autrement dit : un taux négocié pour votre compte écrase toujours le taux public du moyen.

## Add-ons spécifiques

Selon le type de paiement, des frais supplémentaires peuvent s'appliquer :

| Cas | Mécanique |
| :--- | :--- |
| Paiement initié via un **Payment Link** Intram Direct | Surcharge plateforme déduite du net versé au marchand |
| Marchand de catégorie **club** | Surcharge catégorielle déduite du net versé au marchand |
| Transfert wallet-to-wallet (M2C, C2C) | Pourcentage du montant transféré (taux plateforme) |
| Refund mobile money | Frais provider éventuels débités du wallet marchand |

Ces add-ons sont définis au niveau plateforme ; consultez le support ou votre dashboard pour les valeurs à jour.

## Exemple chiffré (illustratif)

Soit une transaction MTN Bénin de **10 000 XOF** avec un taux de `2 %` et une part opérateur de `1,5 %` :

```
Montant transaction    = 10 000
Frais (2%)             =    200
Frais opérateur (1,5%) =    153
Commission Intram      =     47
Montant facturé client = 10 200
Net versé au marchand  =  9 800
```

Le marchand est crédité de `9 800` au moment du settlement. Le provider reçoit `153` ; Intram conserve `47`.

{% hint style="warning" %}
**Les chiffres ci-dessus sont un exemple — pas un barème réel.** Pour vos taux réels, ouvrez **Paramètres → Frais** dans le dashboard.
{% endhint %}

## Voir vos taux applicables

1. Connectez-vous sur [https://app.intram.org](https://app.intram.org)
2. Menu **Paramètres → Frais** (ou **Développeurs → API → Frais** selon votre version d'UI)
3. Le tableau liste chaque moyen de paiement activé sur votre compte, avec le taux **effectivement appliqué** (taux personnalisé prioritaire, sinon taux par défaut)
4. Les changements de taux décidés par l'équipe Intram sont notifiés par email et visibles ici en temps réel

## Activer un moyen de paiement pour voir ses frais

Si aucun moyen n'apparaît dans votre tableau :

1. Menu **Paramètres → Moyens de paiement**
2. Activez les moyens souhaités (MTN, MOOV, SBIN, Visa/Mastercard, virement…)
3. Une fois activés, ils apparaissent dans **Paramètres → Frais** avec leurs taux

## Frais en mode sandbox

Les frais sont calculés à l'identique en sandbox (même taux, même répartition), mais comme rien ne touche les wallets de production, c'est uniquement utile pour vérifier vos arrondis et l'affichage côté client.

## Voir aussi

* [Devises supportées](supported-devices.md)
* [Merchant API — Solde wallet](../merchant-api/reference/balance.md) — pour lire vos balances après application des frais
* [Refunds](../merchant-api/reference/refunds.md) — comment les frais se comportent lors d'un remboursement
