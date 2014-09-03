# payjunction-php [![TravisCI][travis-img-url]][travis-ci-url]
[travis-img-url]: https://travis-ci.org/andrewjwolf/payjunction-php.svg?branch=master
[travis-ci-url]: http://travis-ci.org/andrewjwolf/payjunction-php

A [PayJunction](https://www.payjunction.com/) API client for [php](http://http://php.net/)


Installation
------------

The module can be installed using Composer by adding the following lines to your composer.json file:

    "require": {
        "brandedcreate/payjunction-php": "dev-master"
    }

Within shell navigate to the directory composer.json file resides in and run the following:

```bash
$ composer update
```


The API client has child classes for each of the resources it supports (transactions, receipts and customers) and each class has a method for each of the supported actions.
Each of the methods return a php standard object for easier readability and handling.

The library has no dependencies.


Usage
------------

```php

$options = array(
'username' => 'YOUR-USERNAME',
'password' => 'YOUR-PASSWORD',
'appkey' => 'YOUR-APP-KEY'
);

$customerClient = new CustomerClient($options); //create an instance of the Customer Client
$transactionClient = new TransactionClient($options); //create an instance of the Transaction Client
$receiptClient = new ReceiptClient($options); //create an instance of the ReceiptClient

//This is another way to get specific clients that may be more convenient

$payjunction = new PayjunctionClient($options);
$customerClient = $payjunction->customer();//get a single instance of the Customer Client in relation to the Payjunction Client
$transactionClient = $payjunction->transaction();//get a single instance of the Transaction Client in relation to the Payjunction Client
$receiptClient = $payjunction->receipt();//get a single instance of the Receipt Client in relation to the Payjunction Client
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
$data = array(
            'cardNumber' => '4444333322221111',
            'cardExpMonth' => '01',
            'cardExpYear' => '18',
            'cardCvv' => '999',
            'amountBase' => '10.00'
        );

$response = $transactionClient->create($data);

```

Create a transaction for a swiped credit card:
```php
$data = array(
            'cardTrack' => '%B4444333322221111^First/Last^1712980100000?;4444333322221111=1712980100000?',
            'amountBase' => '10.00'
        );

$response = $transactionClient->create($data);
```

Create a transaction for an ACH transfer:
```php
$data = array(
            'achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => '10.00',
        );

$response = $transactionClient->create($data);
```

Create a new transaction from a previous transaction:
```php

$data = array(
            'transactionId' => '74600',
        );
$response = $transactionClient->create($data);
```

Void a transaction:
```php

$transaction_id = '74600';

$update_data = array(
            'status' => 'VOID'
        );
$response = $transactionClient->update($transaction_id,$update_data);

```

Read a transaction:
```php
$transaction_id = '74600';

$response = $transactionClient->read($transaction_id);
```

Add signature to transaction:
```php
$transaction_id = '74600';

$signature_data = array(
    'signature' => '}SCRIPTEL A ST1501-PYJ 01.00.08 #_5,#<5\'#<5z#"5\'#"5=#"6t#"7m#<7/#>8,#:9\'#+a\'#|b\'$Mcz$Mdv$Meo$Me\'$Mfm$Mfq$Mfr$Mfq$Me[$New$Od-$Pc[$Qco$Sbr$Uat$W9t$Z8v$>7z$+6=%M6w%Q6q%T6n%V6o%Y6s%"6\'%_7o%?7,%+8s%{8[%|9z^Maq^Na-^Nbp^Obv^Obx^Oby^Obv^Pbq^Qa/^Rau^Sam^U9-^V9x^Y9v^"9u^>9v^+9x&M9,&Q9=&Uao&Zat&>ay&{a-*Pa/*Ta/*Xa-*<az*?as*|9[(O9w(R8=(U8r(X7,(Z7q("6/(<6z(<6w("6u("6t(Z6t(Y6t(X6v(W6\'(U7m(R7\'(P8u(M9q*|9[*{ay*|a\(Mbt(Pby(Sbz(Wbx("bs(?a[({axAO9\AS9xAV9nAY8-A"8xA_8wA>8yA?8/A?9rA?9/A?awA>boA<b/AZcyAWdsARemAMe/(?fx(Zgo(Tgx(Pg-*|g-*?gz*_gr*"f;*Yfr*Ye\'*Zd\*<dv*?c[*|cz(Pct(Vcr(<cs({cuAQcxAXcyA>czBMcxBTcvBYcrB>cmB|b.CQbvCUbmCXa\'C<aoC?9\'C+9nC|8yDM7\DM7wDM6[DM6xDM6qC|5[C{5/C+5.C:5=C>6qC_6,C<7qC<7.C_8rC_8.C>9sC?9;C+awC{bmDMbzDPcmDScvDVc-DYc[D<doD>dsD:dxD{d-D{d[D|etD{e.D:frD>f-D"gqDWgyDSg;DMhmC>hnCYg\CUg-CQgtCNf;CMfqCMevCNd-CQdoCTc\'CXctC<cpC+cmDPb[DVb[D<b\D|coERcrEXcuE>cyFMc-FSc[FXdqF_dtF+dvGOdvGRdrGVdoGYc;G<cxG?cpG+b.G{btG|a/G|asG|9.G{9rG:8;G>8xGZ8sGW8qGS8qGO8sF:8yF"8[FV9xFRaoFOa/FNbxFMcpFMc.FNdtFQd/FTerFXe\'F<e\F+fqGOfuGTfvGYftG>fpG|e[HPe\'HSeuHWenHZd.H_dwH:dqH|c\IOc;IRc.IVc-IYc.I_c;I:c[I|doJOdrJRdvJUdzJXd/J"enJ_euJ:e\'J|e[KOfrKRfwKUfzKWf,KXf-KWf-KVf,KSf\'KOfzJ:fyJYfxJRfxI+fyIYfzIQfzH+fzHZfxHVfvHSfsHQfqHPfoHPfmHPe[HQe/HRe\'HTewHVerHZenH>d=IMd-ISdzIYdxI?dxJNdxJSdzJWd-J"d=J>eoJ:etJ{ezJ|e/KNe\KOfqKPftKQfvKRfxKSfyKTfxKVfvKWftKWfrKXfq ]');
$response = $transactionClient->addSignature($transaction_id,$signature_data);
```

### receipts
Read receipt data by transaction id:
```php
$receipt_id = '123456';

$response = $receiptClient->read($receipt_id);
```

Sent an email receipt:
```php
$transaction_id = '123456';
$data = array(
            'to' => 'stephen+automation@brandedcreate.com',
            'replyTo' => 'foobar@whatever.com',
            'requestSignature' => 'true'
        );
$response = $receiptClient->email($transaction_id,$data);
```

### customers
Create a customer:
```php

        $data = array(
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
        );

$response = $customerClient->create($data);
```

Delete a customer:
```php
$customer_id = '345678';
$customerClient->delete($customer_id);
```

## Running Tests
Included with the package is a bash script to execute the included PHPUnit tests.
You must have a running instance of a php server endpoint with access to the test/echo directory for the unit tests to pass.
Execution of the test runner and creation of the server endpoint are done automatically through the execution of the following command.

```bash
./bin/test
```

Alternatively you may also use the phpunit.xml configuration file to run the test suites from within your IDE or some other means. Remember you must have the correct endpoint configured so that the unit tests can reach the test/echo directory via curl.