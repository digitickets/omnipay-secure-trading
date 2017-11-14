<?php

namespace Omnipay\SecureTrading\Message;

use DOMDocument;

/**
 * ThreeDSecure Request
 *
 * @method ThreeDSecureResponse send()
 */
class ThreeDSecureRequest extends AbstractPurchaseRequest
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'THREEDQUERY';
    }

    /**
     * @return DOMDocument
     */
    public function getData()
    {
        $data = parent::getData();

        $this->validate('returnUrl');

        /** @var DOMDocument $request */
        $request = $data->getElementsByTagName('request');
        /** @var DOMDocument $merchant */
        $merchant = $data->getElementsByTagName('merchant')->item(0);
        $merchant->appendChild($data->createElement('termurl', $this->getReturnUrl()));

        /** @var DOMDocument $customer */
        $customer = $request->getElementsByTagName('customer')->item(0) ?: $request->appendChild($data->createElement('customer'));
        $customer->appendChild($data->createElement('accept', $this->getAccept()));
        $customer->appendChild($data->createElement('useragent', $this->getUserAgent()));

        return $data;
    }

    /**
     * @param string $data
     * @return Response
     */
    protected function createResponse($data)
    {
        return $this->response = new ThreeDSecureResponse($this, $data);
    }
}
