<?php

namespace Omnipay\SecureTrading;

use Omnipay\Common\AbstractGateway;

/**
 * SecureTrading Gateway
 */
class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'SecureTrading';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'siteReference' => '',
            'username'      => '',
            'password'      => '',
            'applyThreeDSecure' => false,
        );
    }

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
     * @param array $parameters
     * @return Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\SecureTrading\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\PurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\SecureTrading\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\SecureTrading\Message\RefundRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\ThreeDSecureRequest
     */
    public function threeDSecure(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\SecureTrading\Message\ThreeDSecureRequest', $parameters);
    }
}
