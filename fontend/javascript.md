---
description: >-
  The JavaScript SDK is designed to generate the payment solution within your
  web application without reloading the page.
---

# JAVASCRIPT

{% hint style="info" %}
Le SDK Javascript ne permet pas le traitement des données de facturation. Vous devez utilisé un SDK BACKEND pour générer un lien de facturation qui vous servira ensuite après une requête HTTP.
{% endhint %}

Toute utilisation du SDK JavaScript requiert un compte marchand valide et des données dans votre application de gestion Intram. Si vous n'en disposez pas encore, [créez-en un](https://account.intram.org/register) dès maintenant. L'intégration du SDK JavaScript se fait en important le script qui suit avant la fermeture de la balise fermante`</body>`&#x20;

```markup
<script src="https://cdn.intram.org/sdk-javascript.js"></script>
```

Une fois le SDK importé dans votre application web vous pouvez l'utiliser de cette manière :&#x20;

```javascript
intramOpenWidget.init({
            public_key:'9974dacefe24494f68332ae23d01ff4798550392245747ac0393866fc90320bd', //your public api key
            amount:'10',
            sandbox:false,
            currency:'xof',
            company:{ 
                name:'oualid',
                template:'default+',
                color:'green',
                logo_url:'https://sungrocery.sunmarket.biz/images/boutiques/logos/1635548675-logo.jpg'
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
            public_key:'9974dacefe24494f68332ae23d01ff4798550392245747ac0393866fc90320bd', //your public api key
            amount:'10', //replace your product price 
            sandbox:false, // choose the right public key : sandbox=test or live (see your intram account)
            currency:'xof', //choose the currency
            company:{ 
                name:'oualid', //your company namr
                template:'default+', // payment gate template
                color:'green', // payment gate template color
                logo_url:'https://sungrocery.sunmarket.biz/images/boutiques/logos/1635548675-logo.jpg' // your company logo
            },
        }).then((data)=>{
            console.log(data,'****** responses')
        })
          }
});
```
