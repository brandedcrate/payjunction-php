# payjunction-php [![TravisCI][travis-img-url]][travis-ci-url]
[travis-img-url]: https://travis-ci.org/brandedcrate/payjunction-php.svg?branch=master
[travis-ci-url]: http://travis-ci.org/brandedcrate/payjunction-php

A [PayJunction](https://www.payjunction.com/) API client for [php](http://http://php.net/)


Installation
------------

The module can be installed using Composer:

```bash
php composer.phar require brandedcrate/payjunction:*
```

BrandedCrate\PayJunction fully supports PayJunction's REST API for
transactions, customers and receipts. Support for other resources is on the
way.

This library has no third-party dependencies.

Usage
------------

Instantiate an instance of \BrandedCrate\PayJunction\Client which provides
access to all the available resources.

```php
use BrandedCrate\PayJunction;

$pj = new PayJunction\Client(array(
    'username' => 'YOUR-USERNAME',
    'password' => 'YOUR-PASSWORD',
    'appkey'   => 'YOUR-APP-KEY',
    'endpoint' => 'test' // or 'live'
));
```

Error Handling
--------------

Any errors, including not found errors will be thrown as exceptions of type
`BrandedCrate\PayJunction\Exception`. Generally, you should wrap each
PayJunction call in a try/catch block because you might have a bad request and
PayJunction might throw an error.

```php
use BrandedCrate\PayJunction;

try {
    $pj->customer()->read('doesntexist');
} catch (PayJunction\Exception $e) {
    $e->getCode();             // 404
    $e->getResponse()->errors; // array of errors
}
```

Examples
------------
Consult the [PayJunction API
Documentation](https://developer.payjunction.com/documentation/) for
information about all the available API resources and actions including what
parameters can be set and example responses.

### Transactions
Create a transaction for a keyed credit card:

```php
$response = $pj->transaction()->create(array(
    'cardNumber' => '4444333322221111',
    'cardExpMonth' => '01',
    'cardExpYear' => '18',
    'cardCvv' => '999',
    'amountBase' => '10.00'
));
```

Create a transaction for a swiped credit card:

```php
$response = $pj->transaction()->create(array(
    'cardTrack' => '%B4444333322221111^First/Last^1712980100000?;4444333322221111=1712980100000?',
    'amountBase' => '10.00'
));
```

Create a transaction for an ACH transfer:

```php
$response = $pj->transaction()->create(array(
    'achRoutingNumber' => '104000016',
    'achAccountNumber' => '123456789',
    'achAccountType' => 'CHECKING',
    'achType' => 'PPD',
    'amountBase' => '10.00',
));
```

Create a new transaction from a previous transaction:

```php
$response = $pj->transaction()->create(array(
    'transactionId' => '74600',
));
```

Void a transaction by id:

```php
$response = $pj->transaction()->update('74600', array(
    'status' => 'VOID'
));
```

Read a transaction by id:

```php
$response = $pj->transaction()->read('74600');
```

Add signature to transaction:

```php
$response = $pj->transaction()->addSignature('74600', array(
    'signature' => '}SCRIPTEL A ST1501-PYJ 01.00.08 #_5,#<5\'#<5z#"5\'#"5=#"6t#"7m#<7/#>8,#:9\'#+a\'#|b\'$Mcz$Mdv$Meo$Me\'$Mfm$Mfq$Mfr$Mfq$Me[$New$Od-$Pc[$Qco$Sbr$Uat$W9t$Z8v$>7z$+6=%M6w%Q6q%T6n%V6o%Y6s%"6\'%_7o%?7,%+8s%{8[%|9z^Maq^Na-^Nbp^Obv^Obx^Oby^Obv^Pbq^Qa/^Rau^Sam^U9-^V9x^Y9v^"9u^>9v^+9x&M9,&Q9=&Uao&Zat&>ay&{a-*Pa/*Ta/*Xa-*<az*?as*|9[(O9w(R8=(U8r(X7,(Z7q("6/(<6z(<6w("6u("6t(Z6t(Y6t(X6v(W6\'(U7m(R7\'(P8u(M9q*|9[*{ay*|a\(Mbt(Pby(Sbz(Wbx("bs(?a[({axAO9\AS9xAV9nAY8-A"8xA_8wA>8yA?8/A?9rA?9/A?awA>boA<b/AZcyAWdsARemAMe/(?fx(Zgo(Tgx(Pg-*|g-*?gz*_gr*"f;*Yfr*Ye\'*Zd\*<dv*?c[*|cz(Pct(Vcr(<cs({cuAQcxAXcyA>czBMcxBTcvBYcrB>cmB|b.CQbvCUbmCXa\'C<aoC?9\'C+9nC|8yDM7\DM7wDM6[DM6xDM6qC|5[C{5/C+5.C:5=C>6qC_6,C<7qC<7.C_8rC_8.C>9sC?9;C+awC{bmDMbzDPcmDScvDVc-DYc[D<doD>dsD:dxD{d-D{d[D|etD{e.D:frD>f-D"gqDWgyDSg;DMhmC>hnCYg\CUg-CQgtCNf;CMfqCMevCNd-CQdoCTc\'CXctC<cpC+cmDPb[DVb[D<b\D|coERcrEXcuE>cyFMc-FSc[FXdqF_dtF+dvGOdvGRdrGVdoGYc;G<cxG?cpG+b.G{btG|a/G|asG|9.G{9rG:8;G>8xGZ8sGW8qGS8qGO8sF:8yF"8[FV9xFRaoFOa/FNbxFMcpFMc.FNdtFQd/FTerFXe\'F<e\F+fqGOfuGTfvGYftG>fpG|e[HPe\'HSeuHWenHZd.H_dwH:dqH|c\IOc;IRc.IVc-IYc.I_c;I:c[I|doJOdrJRdvJUdzJXd/J"enJ_euJ:e\'J|e[KOfrKRfwKUfzKWf,KXf-KWf-KVf,KSf\'KOfzJ:fyJYfxJRfxI+fyIYfzIQfzH+fzHZfxHVfvHSfsHQfqHPfoHPfmHPe[HQe/HRe\'HTewHVerHZenH>d=IMd-ISdzIYdxI?dxJNdxJSdzJWd-J"d=J>eoJ:etJ{ezJ|e/KNe\KOfqKPftKQfvKRfxKSfyKTfxKVfvKWftKWfrKXfq ]');
));
```

### receipts
Read receipt data by transaction id:

```php
$response = $pj->receipt()->read('123456');
```

Sent an email receipt:

```php
$response = $pj->receipt()->email('123456', array(
    'to' => 'stephen+automation@brandedcreate.com',
    'replyTo' => 'foobar@whatever.com',
    'requestSignature' => 'true'
));
```

### customers

Create a customer:

```php
$pj->customer()->create(array(
    'companyName' => 'ACME, inc.',
    'email' => 'customer@acme.com',
    'identifier' => 'your-custom-id',
    'firstName' => 'Joe',
    'jobTitle' => 'Wage Slave',
    'lastName' => 'Schmoe',
    'middleName' => 'Ignatius',
    'phone' => '5555551212',
    'phone2' => '1234567890',
    'website' => 'acme.com'
));
```

Delete a customer:

```php
$pj->customer()->delete('345678');
```

Read a customer:

```php
$customer = $pj->customer()->read('345678');
$customer->customerId; // int(7902)
$customer->uri;        // "https://api.payjunctionlabs.com/customers/7902"
```

## Running Tests

This package includes standalone unit tests and integration tests. Run them
with PHPUnit or use the wrapper script to do it for you.

```bash
./bin/test
```

## License

This package is released under the MIT License.
