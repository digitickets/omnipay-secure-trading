<?php

namespace Omnipay\SecureTrading\Message;

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
     * @return \DOMDocument
     */
    public function getData()
    {
        $data = parent::getData();

        if ($this->getApplyThreeDSecure()) {
            $this->validate('returnUrl');

            /** @var \DOMDocument $request */
            $request = $data->getElementsByTagName('request')->item(0);

            /** @var \DOMDocument $merchant */
            $merchant = $request->getElementsByTagName('merchant')->item(0);
            $merchant->appendChild($data->createElement('termurl', $this->getReturnUrl()));

            /** @var \DOMDocument $customer */
            $customer = $request->getElementsByTagName('customer')->item(0) ?: $request->appendChild($data->createElement('customer'));
            $customer->appendChild($data->createElement('accept', $this->getAccept()));
            $customer->appendChild($data->createElement('useragent', $this->getUserAgent()));
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
            $response = new ThreeDSecureResponse($this, $data);

            if (!$response->isSuccessful() || $response->isRedirect()) {
                return $response;
            } else {
                $this->setApplyThreeDSecure(false);
                $this->setCard(null);
                $this->setCardReference($response->getTransactionReference());
                return $this->send();
            }
        } else {
            return parent::createResponse($data);
        }
    }
}
