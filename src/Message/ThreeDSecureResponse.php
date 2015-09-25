<?php

namespace Omnipay\SecureTrading\Message;

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
     * @return null|string
     */
    public function getEnrolled()
    {
        return isset($this->data->response->threedsecure->enrolled)
            ? (string)$this->data->response->threedsecure->enrolled : null;
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
    public function getMd()
    {
        return isset($this->data->response->threedsecure->md)
            ? (string)$this->data->response->threedsecure->md : null;
    }

    /**
     * @return null|string
     */
    public function getXid()
    {
        return isset($this->data->response->threedsecure->xid)
            ? (string)$this->data->response->threedsecure->xid : null;
    }

    /**
     * @return null|string
     */
    public function getPaReq()
    {
        return isset($this->data->response->threedsecure->pareq)
            ? (string)$this->data->response->threedsecure->pareq : null;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return parent::isSuccessful() && $this->isEnrolled();
    }

    /**
     * @return null|string
     */
    public function getRedirectUrl()
    {
        return isset($this->data->response->threedsecure->acsurl)
            ? (string)$this->data->response->threedsecure->acsurl : null;
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
            'PaReq'   => $this->getPaReq(),
            'TermUrl' => $this->getRequest()->getReturnUrl(),
            'MD'      => $this->getMd(),
        );
    }
}
