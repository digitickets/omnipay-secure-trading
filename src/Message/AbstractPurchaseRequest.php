<?php

namespace Omnipay\SecureTrading\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use SimpleXMLElement;

/**
 * AbstractPurchase Request
 *
 * @method Response send()
 */
abstract class AbstractPurchaseRequest extends AbstractRequest
{
    /**
     * The exact content of the HTTP accept-header field as received from the cardholder’s user agent.
     *
     * e.g. `text/xml,application/xml,text/plain;q=0.8,image/png;q=0.5*`
     *
     * @return string
     */
    public function getAccept()
    {
        return is_null($this->getParameter('accept'))
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
     * @throws InvalidCreditCardException
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('amount', 'currency');

        $data = $this->getBaseData();

        /** @var SimpleXmlElement $request */
        $request = $data->request;

        /** @var SimpleXmlElement $operation */
        $operation = $request->operation;
        $operation->addChild('accounttypedescription', 'ECOM');
        $operation->addChild('authmethod', 'FINAL');

        $billing = $request->addChild('billing');
        $amount  = $billing->addChild('amount', $this->getAmountInteger());
        $amount->addAttribute('currencycode', $this->getCurrency());

        if ($this->getCardReference()) {
            $operation->addChild('parenttransactionreference', $this->getCardReference());
        } else {
            $this->validate('card');
            $card = $this->getCard();
            $card->validate();

            $payment = $billing->addChild('payment');
            $payment->addAttribute('type', strtoupper($card->getBrand()));

            $payment->addChild('pan', $card->getNumber());
            $payment->addChild('expirydate', $card->getExpiryDate('m/Y'));
            $payment->addChild('securitycode', $card->getCvv());
        }

        if ($this->getCard()) {
            $this->setBillingCredentials($data);
            $this->setShippingCredentials($data);
        }

        return $data;
    }

    /**
     * @param SimpleXMLElement $data
     */
    protected function setBillingCredentials(SimpleXMLElement $data)
    {
        $card = $this->getCard();

        /** @var SimpleXMLElement $billing */
        $billing = $data->request->billing;

        $name = $billing->addChild('name');
        $name->addChild('first', $card->getBillingFirstName());
        $name->addChild('last', $card->getBillingLastName());

        $billing->addChild('country', $card->getBillingCountry());
        $billing->addChild('county', $card->getBillingState());
        $billing->addChild('town', $card->getBillingCity());
        $billing->addChild('postcode', $card->getBillingPostcode());

        $address = implode(
            ', ',
            array_filter(array(
                $card->getBillingAddress1(),
                $card->getBillingAddress2(),
            ))
        ) ?: null;
        $billing->addChild('street', $address);

        $billing->addChild('email', $card->getEmail());
        $billing->addChild('telephone', $card->getBillingPhone());
    }

    /**
     * @param SimpleXMLElement $data
     */
    protected function setShippingCredentials(SimpleXMLElement $data)
    {
        $card = $this->getCard();

        /** @var SimpleXMLElement $customer */
        $customer = $data->request->customer ?: $data->request->addChild('customer');

        $name = $customer->addChild('name');
        $name->addChild('first', $card->getShippingFirstName());
        $name->addChild('last', $card->getShippingLastName());

        $customer->addChild('country', $card->getShippingCountry());
        $customer->addChild('county', $card->getShippingState());
        $customer->addChild('town', $card->getShippingCity());
        $customer->addChild('postcode', $card->getShippingPostcode());

        $address = implode(
            ', ',
            array_filter(array(
                $card->getShippingAddress1(),
                $card->getShippingAddress2(),
            ))
        ) ?: null;
        $customer->addChild('street', $address);

        $customer->addChild('email', $card->getEmail());
        $customer->addChild('telephone', $card->getShippingPhone());
    }
}
