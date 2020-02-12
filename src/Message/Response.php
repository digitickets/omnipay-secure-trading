<?php

namespace Omnipay\SecureTrading\Message;

use DOMDocument;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Response
 */
class Response extends AbstractResponse
{
    protected $xml;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
        $this->xml = DOMDocument::loadXML($this->data);
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getCode() === 0 && $this->getSettleStatus() !== 3;
    }

    /**
     * @return null|int
     */
    public function getCode()
    {
        return isset($this->xml->getElementsByTagName('code')[0]) ? (int)$this->xml->getElementsByTagName('code')[0]->nodeValue : null;
    }

    /**
     * @return null|int
     */
    public function getSettleStatus()
    {
        return isset($this->xml->getElementsByTagName('settlestatus')[0]) ? (int)$this->xml->getElementsByTagName('settlestatus')[0]->nodeValue : null;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return isset($this->xml->getElementsByTagName('message')[0]) ? (string)$this->xml->getElementsByTagName('message')[0]->nodeValue : null;
    }

    /**
     * @return null|int
     */
    public function getErrorData()
    {
        if (isset($this->xml->getElementsByTagName('data')[0]) && $this->xml->getElementsByTagName('message')[0]->nodeValue != 'OK')
            return (string)$this->xml->getElementsByTagName('data')[0]->nodeValue;

        return null;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return false;
    }

    /**
     * @return null|string
     */
    public function getCardReference()
    {
        return $this->getTransactionReference();
    }

    /**
     * @return null|string
     */
    public function getTransactionReference()
    {
        return isset($this->xml->getElementsByTagName('transactionreference')[0]) ? (string)$this->xml->getElementsByTagName('transactionreference')[0]->nodeValue : null;
    }

    /**
     * @return null|string date format: "Y-m-d"
     */
    public function getSettleDueDate()
    {
        return isset($this->xml->getElementsByTagName('settleduedate')[0]) ? (string)$this->xml->getElementsByTagName('settleduedate')[0]->nodeValue : null;
    }

    public function getParentTransactionReference()
    {
        return isset($this->xml->getElementsByTagName('parenttransactionreference')[0]) ? (string)$this->xml->getElementsByTagName('parenttransactionreference')[0]->nodeValue : null;
    }

    public function getThreedStatus()
    {
        return isset($this->xml->getElementsByTagName('status')[0]) ? (string)$this->xml->getElementsByTagName('status')[0]->nodeValue : null;
    }

    public function getThreedEci()
    {
        return isset($this->xml->getElementsByTagName('eci')[0]) ? (string)$this->xml->getElementsByTagName('eci')[0]->nodeValue : null;
    }

    public function getThreedEnrolledStatus()
    {
        return isset($this->xml->getElementsByTagName('enrolled')[0]) ? (string)$this->xml->getElementsByTagName('enrolled')[0]->nodeValue : null;
    }

    public function getXid()
    {
        return isset($this->xml->getElementsByTagName('xid')[0]) ? (string)$this->xml->getElementsByTagName('xid')[0]->nodeValue : null;
    }

    public function getCAVV()
    {
        return isset($this->xml->getElementsByTagName('cavv')[0]) ? (string)$this->xml->getElementsByTagName('cavv')[0]->nodeValue : null;
    }
}
