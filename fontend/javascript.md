---
description: >-
  The JavaScript SDK is designed to generate the payment solution within your
  web application without reloading the page.
---

# JAVASCRIPT

{% hint style="info" %}
Le SDK Javascript ne permet pas le traitement des données de facturation. Vous devez utilisé un SDK BACKEND pour générer un lien de facturation qui vous servira ensuite après une requête HTTP.
{% endhint %}

Toute utilisation du SDK JavaScript requiert un compte marchand valide et des données dans votre application de gestion Intram. Si vous n'en disposez pas encore, [créez-en un](https://app.intram.org/register) dès maintenant. L'intégration du SDK JavaScript se fait en important le script qui suit avant la fermeture de la balise fermante`</body>`

```markup
<script src="https://cdn.intram.org/sdk-javascript.js"></script>
```

Une fois le SDK importé dans votre application web vous pouvez l'utiliser de cette manière :

```javascript
intramOpenWidget.init({
            public_key:'9974dacefe24494f68332ae23d0', //your public api key
            amount:1000,
            sandbox:false,
            currency:'xof',
            callback_url:'https://my-shop.com/check-paiement-status/',
            company:{ 
                name:'my-shop',
                template:'default+',
                color:'green',
                logo_url:'https://my-site.com/logo.jpg'
            },
        }).then((data)=>{
            console.log(data,'****** responses')
        })
```

{% hint style="success" %}
EXEMPLE AVEC AJAX
{% endhint %}

```javascript
 $.ajax(
 {
       url: 'url',
       method: "POST",
       data: data,
       success: function (data) 
         {
             intramOpenWidget.init({
            public_key:'9974dacefe24494f68332ae23d01', //your public api key
            amount:data.company.amount, //replace your product price 
            sandbox:false, // choose the right public key : sandbox=test or live (see your intram account)
            callback_url:data.callback_url,
            currency:'xof', //choose the currency
            company:{ 
                name:'data.company.name, //your company namr
                template:'default+', // payment gate template
                color:data.company.color, // payment gate template color
                logo_url:data.company.logo // your company logo
            },
        }).then((data)=>{
            console.log(data,'****** responses')
        })
          }
});
```

{% hint style="success" %}
Numero de test

&#x20;**MTN** : 67222918

&#x20;**MOOV** : 99914337
{% endhint %}
