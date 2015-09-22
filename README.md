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

- authorize($options) - authorize an amount on the customer's card
- capture($options) - capture an amount you have previously authorized
- purchase($options) - authorize and immediately capture an amount on the customer's card
- refund($options) - refund an already processed transaction
- void($options) - generally can only be called up to 24 hours after submitting a transaction

Gateway instantiation:

    $gateway = Omnipay::create('Secure_Trading');
    $gateway->setTerminalId('1234567');
    $gateway->setTransactionKey('5CbEvA8hDCe6ASd6');
    $gateway->setTestMode(true);

Driver also supports paying with `cardReference` instead of `card`, 
but gateway requires also additional parameter `cardHash`. It can be used in authorize and purchase requests like that:

    $gateway->purchase([
        'amount'        => '10.00',
        'cardReference' => 'abc',
        'cardHash'      => 'def123',
    ]);

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
