---
description: >-
  Django-Intram est un plugin Django qui permet d'intégrer facilement les
  paiements Intram dans votre application Django. Ce guide vous accompagnera pas
  à pas dans l'implémentation de la solution.
---

# DJANGO-INTRAM

### Prérequis

* Django 3.2 ou supérieur
* Python 3.7 ou supérieur
* Un compte Intram avec vos clés d'API
* pip installé sur votre système

### Installation

1. Installez le package via pip :

```bash
pip install django-intram
```

2. Ajoutez 'django\_intram' à vos INSTALLED\_APPS dans settings.py :

```python
INSTALLED_APPS = [
    ...
    'django_intram',
]
```

### Configuration

#### 1. Configuration des clés API

Dans votre fichier settings.py, ajoutez vos clés API Intram :

```python
# Configuration Intram
INTRAM_PUBLIC_KEY = 'votre_cle_publique'
INTRAM_PRIVATE_KEY = 'votre_cle_privee'
INTRAM_SECRET = 'votre_cle_secrete'
INTRAM_MARCHAND_ID = 'votre_id_marchand'
INTRAM_SANDBOX = True  # Mettre False en production
```

#### 2. Configuration des URLs

Dans votre urls.py principal :

```python
from django.urls import path, include

urlpatterns = [
    ...
    path('payments/', include('django_intram.urls')),
]
```

### Utilisation

#### 1. Exemple basique d'intégration

Dans votre vue (views.py) :

```python
from django.shortcuts import render
from django.conf import settings
from django_intram import Intram

def process_payment(request):
    # Créez une instance Intram
    intram = Intram(
        public_key=settings.INTRAM_PUBLIC_KEY,
        private_key=settings.INTRAM_PRIVATE_KEY,
        secret=settings.INTRAM_SECRET,
        marchand_id=settings.INTRAM_MARCHAND_ID,
        sandbox=settings.INTRAM_SANDBOX
    )
    
    # Configurez le paiement
    intram.set_currency("XOF")
    intram.set_amount(5000)  # Montant en centimes
    intram.set_description("Achat de produit")
    intram.set_name_store("Ma Boutique")
    
    # URLs de redirection
    intram.set_return_url("https://votre-site.com/success")
    intram.set_cancel_url("https://votre-site.com/cancel")
    intram.set_redirection_url("https://votre-site.com/callback")
    
    # Initialisez le paiement
    response = intram.set_request_payment()
    
    return render(request, 'payment.html', {'payment_url': response['payment_url']})
```

Dans votre template (payment.html) :

```html
<form action="{{ payment_url }}" method="POST">
    <button type="submit">Procéder au paiement</button>
</form>
```

#### 2. Gestion des callbacks

Créez une vue pour gérer les retours de paiement :

```python
from django.views.decorators.csrf import csrf_exempt

@csrf_exempt
def payment_callback(request):
    if request.method == 'POST':
        # Récupérez les données de la transaction
        transaction_data = request.POST
        
        # Vérifiez le statut de la transaction
        intram = Intram(...)
        status = intram.get_transaction_status(transaction_data['transaction_id'])
        
        if status['status'] == 'success':
            # Traitez le paiement réussi
            return JsonResponse({'status': 'success'})
        else:
            # Gérez l'échec du paiement
            return JsonResponse({'status': 'failed'})
```

#### 3. Vérification du statut d'une transaction

```python
def check_payment_status(request, transaction_id):
    intram = Intram(...)
    status = intram.get_transaction_status(transaction_id)
    return JsonResponse(status)
```

### Personnalisation

#### Template de paiement personnalisé

Créez un template dans `templates/django_intram/custom_payment.html` :

```html

<div data-gb-custom-block data-tag="extends" data-0='base.html'></div>

<div data-gb-custom-block data-tag="block">

<div class="payment-container">
    <h2>Confirmation de paiement</h2>
    <div class="payment-details">
        <p>Montant : {{ amount }} XOF</p>
        <p>Description : {{ description }}</p>
    </div>
    <form action="{{ payment_url }}" method="POST">
        <button type="submit" class="payment-button">
            Payer maintenant
        </button>
    </form>
</div>

</div>
```

### Environnements

#### Développement (Sandbox)

```python
INTRAM_SANDBOX = True
```

#### Production

```python
INTRAM_SANDBOX = False
```

### Gestion des erreurs

Exemple de gestion des erreurs :

```python
try:
    response = intram.set_request_payment()
    if response.get('status') == 'ERROR':
        # Gérer l'erreur
        logger.error(f"Erreur Intram: {response.get('message')}")
        return render(request, 'error.html', {'error': response.get('message')})
except Exception as e:
    # Gérer les exceptions
    logger.error(f"Exception: {str(e)}")
    return render(request, 'error.html', {'error': 'Une erreur est survenue'})
```

### Sécurité

* Les clés API doivent être stockées dans les variables d'environnement
* Utilisez HTTPS en production
* Validez toujours les données reçues dans les callbacks
* Vérifiez la signature des transactions

### Bonnes pratiques

1. Utilisez les logs pour suivre les transactions :

```python
import logging
logger = logging.getLogger(__name__)

logger.info(f"Transaction initiée: {transaction_id}")
```

2. Créez des modèles pour suivre les transactions :

```python
from django.db import models

class Transaction(models.Model):
    transaction_id = models.CharField(max_length=100)
    amount = models.DecimalField(max_digits=10, decimal_places=2)
    status = models.CharField(max_length=20)
    created_at = models.DateTimeField(auto_now_add=True)
```

### Dépannage

#### Problèmes courants

1. Erreur "KEY MISSING"
   * Vérifiez que toutes vos clés API sont correctement configurées
2. Erreur de callback
   * Vérifiez que votre URL de callback est accessible publiquement
   * Vérifiez que CSRF est désactivé pour l'endpoint de callback
3. Problème de connexion
   * Vérifiez votre connexion internet
   * Vérifiez que les serveurs Intram sont accessibles

### Support

Pour toute assistance :

* Documentation Intram : [https://intram.org/docs](https://intram.org/docs)
* Support email : contact@intram.org
