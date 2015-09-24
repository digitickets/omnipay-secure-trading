<?php

namespace Omnipay\SecureTrading\Message;

use SimpleXMLElement;

/**
 * ThreeDSecure Request
 *
 * @method ThreeDSecureResponse send()
 */
class ThreeDSecureRequest extends PurchaseRequest
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'THREEDQUERY';
    }

    /**
     * The exact content of the HTTP accept-header field as received from the cardholder’s user agent.
     *
     * e.g. `text/xml,application/xml,text/plain;q=0.8,image/png;q=0.5*`
     *
     * return string
     */
    public function getAccept()
    {
        is_null($this->getParameter('accept'))
            ? $this->determineAccept() : $this->getParameter('accept');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAccept($value)
    {
        return $this->setParameter('accept', $value);
    }

    /**
     * @return string
     */
    protected function determineAccept()
    {
        return isset($_SERVER) && array_key_exists('HTTP_ACCEPT', $_SERVER)
            ? $_SERVER['HTTP_ACCEPT']
            : '';
    }

    /**
     * The exact content of the HTTP user-agent header field as received from the cardholder’s user agent.
     *
     * e.g. `Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.11) Gecko/20071204 Ubuntu/7.10 (gutsy) Firefox/2.0.0.11`
     *
     * return string
     */
    public function getUserAgent()
    {
        return is_null($this->getParameter('userAgent'))
            ? $this->determineUserAgent() : $this->getParameter('userAgent');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUserAgent($value)
    {
        return $this->setParameter('userAgent', $value);
    }

    /**
     * @return string
     */
    protected function determineUserAgent()
    {
        return isset($_SERVER) && array_key_exists('HTTP_USER_AGENT', $_SERVER)
            ? $_SERVER['HTTP_USER_AGENT']
            : '';
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
