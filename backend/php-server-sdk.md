---
description: PHP SDK for server side integration
---

# PHP SERVER SDK

## GENERATE YOUR API KEYS

API keys are your digital references towards Intram systems. We use them to identify your account and the applications you will create. These keys are necessary for any integration of the APIs of Intram's payments APIs. Here are the steps to follow:

* First, you need to have an Intram Business account activated. [Create ](https://account.intram.org/register)one if it is not yet the case.
* [Login](https://account.intram.org/login) to your account and click on  Developers at the menu level on the left.
* Then click on API, you will see all yours API keys.
* You can switch to `SANDBOX MODE,` or`ENABLE LIVE MODE`.

### Installation via composer

```bash
composer require intram/php-sdk
```

### API configuration

[Login](https://account.intram.org/login) to your Intram account, click on Developer, then on API at this level, get the API keys and give them as arguments to the controller. Initialize Intram  by entering in order: `PUBLIC_KEY`, ⁣`PRIVATE_KEY`, `INTRAM_SECRET`, ⁣`INTRAM_MARCHAND_KEY`, `MODE` The mode: `true` for live mode and `false` for test mode.

```php
$intram = new \Intram\Intram(
            "5b06f06a0aad7d0163c414926b635ee",
            "pk_9c0410014969f276e8b3685fec7",
            "sk_08bd75f9468b484d8a9f24daddf",
            "marchand_id",
            true)
```

### Configure your department / company information

#### Setting Store name

(required)

```php
$intram->setNameStore("Suntech Store");
```

#### Setting Store Logo Url

```php
$intram->setLogoUrlStore("https://www.suntechshop/logo.png");
```

#### Setting Store Web site

```php
$intram->setWebSiteUrlStore("https://www.suntechshop");
```

#### Setting Store phone

```php
$intram->setPhoneStore("97000000");
```

#### Setting Store Postal adress

```php
$intram->setPostalAdressStore("BP 35");
```

### Create a request paiement

In order to allow the user to make a payment on your store, you must create the transaction and then send them the payment url or the qr code to scan. For that :

#### Add Invoice Items

Add the different products of the purchase (required)

```php
$intram->setItems([
            ['name'=>"T-shirt",'qte'=>"2",'price'=>"500",'totalamount'=>"1000"],
            ['name'=>"trouser",'qte'=>"1",'price'=>"12500",'totalamount'=>"12500"],
        ]);
```

#### Setting TVA Amount

TVA (optional)

```php
$intram->setTva([["name" => "VAT (18%)", "amount" => 1000],["name" => " other VAT", "amount" => 500]]);
```

#### Adding Custom Data

(optional)

```php
$intram->setCustomData([['CartID',"32393"],['PERIOD',"TABASKI"]]);
```

#### Setting Total Amount

Order total (required)

```php
$intram->setAmount(13600);
```

#### Setting Currency

Currency of paiement (required)

```php
$intram->setCurrency("XOF");
```

#### Setting Description

Description of operation (required)

```php
$intram->setDescription("Pretty and suitable for your waterfall");
```

#### Setting Template

(required)

```php
$intram->setTemplate("default");
```

#### Setting Store Redirection Url

```php
$intram->setRedirectionUrl("https://www.suntechshop/redirection-url");
```

#### Setting Store Return Url

```php
$intram->setReturnUrl("https://www.suntechshop/return-url");
```

#### Setting Store Cancel Url

```php
$intram->setCancelUrl("https://www.suntechshop/cancel-url");
```

#### Make the payment request

```php
$response = $intram->setRequestPayment();
```

#### Expected response

```php
{
 +"status": "PENDING"
 +"transaction_id": "5f2d7a96b97d9d3fea912c11"
 +"receipt_url": "localhost:3000/payment/gate/5f2d7a96b97d9d3fea912c11"
 +"total_amount": 1000
 +"currency":"xof"
 +"qr_code":"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHQAAAB0CAYAAABUmhYnAAAAAklEQVR4AewaftIAAAL9SURBVO3BQQ7jCAhFwZcvi6NyKI7KgkyWrCxZdtLdDFWv9wdrDLFGEWsUsUYRaxSxRhFrFL ▶"
}
```

#### Get data

```php
$transaction_id = $response->transaction_id;
$status = $response->status;
$receipt_url = $response->receipt_url;
$total_amount = $response->total_amount;
.......
```

### Get transaction status

Give the transaction identifier as an argument to the function (required)

```php
$intram->getTransactionStatus($transaction_id);
```

#### Expected response

```php
{
  +"status": "PENDING"
  +"amount": 1000
  +"currency": "XOF"
  +"account_number":"22996XXXXXX"
  +"source":"MOOV"
  +"fees":50
  +"net_amount":1050
}
```

## Running Tests

To run tests, just set up the API configuration environment variables. An internet connection is required for some tests to pass.

### License

MIT
