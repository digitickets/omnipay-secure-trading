<?php

namespace Omnipay\SecureTrading\Message;

use DOMDocument;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * ThreeDSecure Response
 *
 * @method ThreeDSecureRequest getRequest()
 */
class ThreeDSecureResponse extends Response implements RedirectResponseInterface
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return parent::isSuccessful() && !$this->isRedirect();
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return parent::isSuccessful() && $this->isEnrolled();
    }

    /**
     * @return bool
     */
    public function isEnrolled()
    {
        return $this->getEnrolled() === 'Y';
    }

    /**
     * @return null|string
     */
    public function getEnrolled()
    {

        return isset($this->xml->getElementsByTagName('enrolled')[0]) ? (string)$this->xml->getElementsByTagName('enrolled')[0]->nodeValue : null;
    }

    /**
     * @return null|string
     */
    public function getXid()
    {
        return isset($this->xml->getElementsByTagName('xid')[0]) ? (string)$this->xml->getElementsByTagName('xid')[0]->nodeValue : null;
    }

    /**
     * @return null|string
     */
    public function getRedirectUrl()
    {
        return isset($this->xml->getElementsByTagName('acsurl')[0]) ? (string)$this->xml->getElementsByTagName('acsurl')[0]->nodeValue : null;
    }

    /**
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * @return null|array
     */
    public function getRedirectData()
    {
        if (!$this->isRedirect()) {
            return null;
        }

        return array(
            'PaReq' => $this->getPaReq(),
            'TermUrl' => $this->getRequest()->getReturnUrl(),
            'MD' => $this->getMd(),
        );
    }

    /**
     * @return null|string
     */
    public function getPaReq()
    {
        return isset($this->xml->getElementsByTagName('pareq')[0]) ? (string)$this->xml->getElementsByTagName('pareq')[0]->nodeValue : null;
    }

    /**
     * @return null|string
     */
    public function getMd()
    {
        return isset($this->xml->getElementsByTagName('md')[0]) ? (string)$this->xml->getElementsByTagName('md')[0]->nodeValue : null;
    }
}
