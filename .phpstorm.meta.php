<?php

namespace PHPSTORM_META {

    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    /** @noinspection PhpUnusedLocalVariableInspection */
    $STATIC_METHOD_TYPES = [
      \Omnipay\Omnipay::create('') => [
        'SecureTrading' instanceof \Omnipay\SecureTrading\Gateway,
      ],
      \Omnipay\Common\GatewayFactory::create('') => [
        'SecureTrading' instanceof \Omnipay\SecureTrading\Gateway,
      ],
    ];
}
