# Omnipay: Secure Trading

**Secure Trading gateway for the Omnipay PHP payment processing library**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/meebio/omnipay-secure-trading.svg?style=flat-square)](https://packagist.org/packages/meebio/omnipay-secure-trading)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/meebio/omnipay-secure-trading/master.svg?style=flat-square)](https://travis-ci.org/meebio/omnipay-secure-trading)
[![Total Downloads](https://img.shields.io/packagist/dt/meebio/omnipay-secure-trading.svg?style=flat-square)](https://packagist.org/packages/meebio/omnipay-secure-trading)


[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Secure Trading support for Omnipay.

## Install

Via Composer

``` bash
$ composer require meebio/omnipay-secure-trading
```

## Usage

The following gateways are provided by this package:

 * Secure Trading

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

This driver supports following transaction types:

- `purchase($options)` - authorize and immediately capture an amount on the customer's card
- `completePurchase($options)` - handle return from off-site gateways after purchase
- `refund($options)` - refund an already processed transaction
- `threeDSecure($options)` - authorize customer's card through 3D Secure process, same as `purchase($options)`
 with option `applyThreeDSecure` set to true

Gateway instantiation:

```php
$gateway = Omnipay::create('SecureTrading');
$gateway->setSiteReference('siteReference123');
$gateway->setUsername('username123');
$gateway->setPassword('password123');
```

Driver also supports paying with `cardReference` instead of `card`.
 It can be used in authorize and purchase requests like that:

```php
$gateway->purchase([
    'amount'        => '10.00',
    'cardReference' => 'abc',
]);
```
    
### 3D Secure
To enable 3D Secure credit card authorization through `purchase` request, `applyThreeDSecure` parameter needs to be set to true. Then whole purchase flow is like below:

```php
$gateway = Omnipay::create('SecureTrading');
$gateway->setSiteReference('siteReference123');
$gateway->setUsername('username123');
$gateway->setPassword('password123');

$request = $gatewat->purchase([
    'transactionId'     => 'test-1234',
    'applyThreeDSecure' => true,
    'returnUrl'         => 'http://test-website.test/return-url',
    'amount'            => '2.99',
    'currency'          => 'GBP',
    'card'              => [
        'number'      => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear'  => '2020',
        'cvv'         => '123',
        'firstName'   => 'Forename',
        'lastName'    => 'Surname',
    ],
]);

$response = $request->send();
if ($response->isSuccessful()) {
    // card not enrolled or unknown enrollment
    // and payment is successful, no redirection needed
} elseif ($response->isRedirect()) {
    // redirect to offsite payment gateway
    $response->redirect();
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
```
    
In case of redirection, following code is needed to process payment after customer returns from remote server:

```php    
$gateway = Omnipay::create('SecureTrading');
$gateway->setSiteReference('siteReference123');
$gateway->setUsername('username123');
$gateway->setPassword('password123');
    
$request = $gateway->completePurchase([
    'md'    => $_POST['MD'],
    'paRes' => $_POST['PaRes'],
]);

$response = $request->send();
if ($response->isSuccessful()) {
    // payment is successful
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/meebio/omnipay-secure-trading/issues),
or better yet, fork the library and submit a pull request.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email jablonski.kce@gmail.com instead of using the issue tracker.

## Credits

- [John Jablonski](https://github.com/jan-j)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
