<?php

namespace Omnipay\SecureTrading\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use SimpleXMLElement;

/**
 * Abstract Request
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    /**
     * @var string
     */
    protected $endpoint = 'https://webservices.securetrading.net:443/xml/';

    /**
     * @return string
     */
    abstract public function getAction();

    /**
     * @return string
     */
    public function getSiteReference()
    {
        return $this->getParameter('siteReference');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSiteReference($value)
    {
        return $this->setParameter('siteReference', $value);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return boolean
     */
    public function getApplyThreeDSecure()
    {
        return $this->getParameter('applyThreeDSecure');
    }

    /**
     * @param boolean $value
     * @return $this
     */
    public function setApplyThreeDSecure($value)
    {
        return $this->setParameter('applyThreeDSecure', $value);
    }

    /**
     * @return SimpleXMLElement
     */
    public function getBaseData()
    {
        $data = new SimpleXMLElement('<?xml version="1.0"?><requestblock/>');
        $data->addAttribute('version', '3.67');

        $alias = $data->addChild('alias', $this->getUsername());

        $request = $data->addChild('request');
        $request->addAttribute('type', $this->getAction());

        $merchant = $request->addChild('merchant');
        $merchant->addChild('orderreference', $this->getTransactionId());

        $operation = $request->addChild('operation');
        $operation->addChild('sitereference', $this->getSiteReference());

        return $data;
    }

    /**
     * @param SimpleXMLElement $data
     * @return Response
     */
    public function sendData($data)
    {
        $headers     = array(
            'Content-Type: text/xml;charset=utf-8',
            'Accept: text/xml',
        );
        $httpRequest = $this->httpClient->post($this->getEndpoint(), $headers, $data->asXML());
        $httpRequest->setAuth($this->getUsername(), $this->getPassword());
        $httpResponse = $httpRequest->send();

        return $this->createResponse($httpResponse->xml());
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $data
     * @return Response
     */
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
