<?php

namespace Omnipay\SecureTrading\Message;

use DOMDocument;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

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
     * @param string $value
     * @return $this
     */
    public function setSiteReference($value)
    {
        return $this->setParameter('siteReference', $value);
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
     * @return string
     */
    public function getAccountType()
    {
        return $this->getParameter('accountType');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAccountType($value)
    {
        return $this->setParameter('accountType', $value);
    }

    /**
     * @return DOMDocument
     */
    public function getBaseData()
    {
        $domTree = new DOMDocument('1.0', 'UTF-8');
        // root element
        $data = $domTree->createElement('requestblock');

        $data->setAttribute('version', '3.67');
        $data = $domTree->appendChild($data);

        $alias = $domTree->createElement('alias', $this->getUsername());
        $data->appendChild($alias);

        $request = $domTree->createElement('request');
        $request->setAttribute('type', $this->getAction());
        $data->appendChild($request);

        $merchant = $domTree->createElement('merchant');
        $merchant->appendChild($domTree->createElement('orderreference', $this->getTransactionId()));
        $request->appendChild($merchant);

        $operation = $domTree->createElement('operation');
        $operation->appendChild($domTree->createElement('sitereference', $this->getSiteReference()));
        $request->appendChild($operation);

        return $domTree;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

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
     * @param DOMDocument $data
     * @return Response
     */
    public function sendData($data)
    {
        $headers = array(
            'Content-Type: text/xml;charset=utf-8',
            'Accept: text/xml',
            'Authorization: ' . base64_encode($this->getUsername() . ':' . $this->getPassword()),
        );
        $response = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data->saveXML())->getBody()->getContents();
        return $this->createResponse($response);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
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
