---
description: >-
  Devises (ISO 4217) et moyens de paiement actuellement pris en charge par
  Intram pour vos transactions.
---

# Devises supportées

{% hint style="info" %}
Cette section évolue continuellement à mesure que nous ajoutons de nouveaux pays et opérateurs.
{% endhint %}

## Devises

| Code ISO 4217 | Devise | Zone | Statut |
| :---: | :--- | :--- | :---: |
| `XOF` | Franc CFA (BCEAO) | UEMOA (Bénin, Burkina, Côte d'Ivoire, Mali, Niger, Sénégal, Togo, Guinée-Bissau) | ✅ |

D'autres devises arriveront prochainement. Pour anticiper, structurez votre intégration pour passer la devise en paramètre (`currency: "XOF"`) plutôt que de la coder en dur.

## Moyens de paiement par pays

| Pays | Mobile Money | Code interne |
| :--- | :--- | :--- |
| Bénin (BJ) | MTN Mobile Money | `MTN_BENIN_229` |
| Bénin (BJ) | Moov Africa Money | `MOOV_AFRICA_BENIN_229` |
| Bénin (BJ) | Sirius Bank | `SBIN_BENIN_229` |

Les cartes bancaires (Visa / Mastercard) sont également supportées via le gateway Stripe pour les transactions en `XOF` converties.

## Activer un moyen de paiement sur votre compte

1. Connectez-vous à [https://app.intram.org](https://app.intram.org)
2. Menu **Paramètres → Moyens de paiement**
3. Activez les moyens souhaités — ils deviendront proposables aux clients dans vos transactions
4. Les taux appliqués apparaissent dans **Paramètres → Frais** ([documentation](fees.md))

## Voir aussi

* [Frais](fees.md) — calcul des commissions par moyen de paiement
* [Merchant API — Payouts](../merchant-api/reference/payouts.md) — pour reverser vers ces moyens depuis votre backend
* [Merchant API — Payment requests](../merchant-api/reference/payment-requests.md) — pour demander un paiement client via ces moyens
