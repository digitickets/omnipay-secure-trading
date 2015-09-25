<?php

namespace Omnipay\SecureTrading\Message;

use SimpleXMLElement;

/**
 * Purchase Request
 *
 * @method Response send()
 */
class PurchaseRequest extends AbstractPurchaseRequest
{
    public function getAction()
    {
        return $this->getApplyThreeDSecure() ? 'THREEDQUERY' : 'AUTH';
    }

    /**
     * return string
     */
    public function getApplyThreeDSecure()
    {
        return $this->getParameter('applyThreeDSecure');
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setApplyThreeDSecure($value)
    {
        return $this->setParameter('applyThreeDSecure', $value);
    }

    /**
     * @return SimpleXMLElement
     */
    public function getData()
    {
        $data = parent::getData();

        if ($this->getApplyThreeDSecure()) {
            $this->validate('returnUrl');

            /** @var SimpleXMLElement $merchant */
            $merchant = $data->request->merchant;
            $merchant->addChild('termurl', $this->getReturnUrl());

            /** @var SimpleXMLElement $customer */
            $customer = $data->request->customer ?: $data->request->addChild('customer');
            $customer->addChild('accept', $this->getAccept());
            $customer->addChild('useragent', $this->getUserAgent());
        }

        return $data;
    }

    /**
     * @param string $data
     * @return Response
     */
    protected function createResponse($data)
    {
        if ($this->getApplyThreeDSecure()) {
            return $this->response = new ThreeDSecureResponse($this, $data);
        } else {
            return parent::createResponse($data);
        }
    }
}
