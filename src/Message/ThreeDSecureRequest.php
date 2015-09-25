<?php

namespace Omnipay\SecureTrading\Message;

use SimpleXMLElement;

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
     * @return SimpleXMLElement
     */
    public function getData()
    {
        $data = parent::getData();

        $this->validate('returnUrl');

        /** @var SimpleXMLElement $merchant */
        $merchant = $data->request->merchant;
        $merchant->addChild('termurl', $this->getReturnUrl());

        /** @var SimpleXMLElement $customer */
        $customer = $data->request->customer ?: $data->request->addChild('customer');
        $customer->addChild('accept', $this->getAccept());
        $customer->addChild('useragent', $this->getUserAgent());

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
