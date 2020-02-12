<?php

namespace Omnipay\SecureTrading\Message;

use DOMDocument;
use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;

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
     * @return string
     */
    protected function determineAccept()
    {
        return isset($_SERVER) && array_key_exists('HTTP_ACCEPT', $_SERVER)
            ? $_SERVER['HTTP_ACCEPT']
            : '';
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
     * @return string
     */
    protected function determineUserAgent()
    {
        return isset($_SERVER) && array_key_exists('HTTP_USER_AGENT', $_SERVER)
            ? $_SERVER['HTTP_USER_AGENT']
            : '';
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
     * @return DOMDocument
     * @throws InvalidCreditCardException
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('amount', 'currency');

        $data = $this->getBaseData();

        /** @var DOMDocument $request */
        $request = $data->getElementsByTagName('request')->item(0);

        /** @var DOMDocument $operation */
        $operation = $data->getElementsByTagName('operation')->item(0);
        $operation->appendChild($data->createElement('accounttypedescription', $this->getAccountType()));
        $operation->appendChild($data->createElement('authmethod', 'FINAL'));

        /** @var DOMDocument $billing */
        $billing = $request->appendChild($data->createElement('billing'));
        $amount = $data->createElement('amount', $this->getAmountInteger());
        $amount->setAttribute('currencycode', $this->getCurrency());
        $billing->appendChild($amount);

        if ($this->getCardReference()) {
            $operation->appendChild($data->createElement('parenttransactionreference', $this->getCardReference()));
        } else {
            $this->validate('card');
            $card = $this->getCard();
            $card->validate();

            $payment = $data->createElement('payment');
            $payment->setAttribute('type', strtoupper($card->getBrand()));
            $billing->appendChild($payment);

            $payment->appendChild($data->createElement('pan', $card->getNumber()));
            $payment->appendChild($data->createElement('expirydate', $card->getExpiryDate('m/Y')));
            $payment->appendChild($data->createElement('securitycode', $card->getCvv()));
        }

        /** @var DOMDocument $customer */
        $customer = $request->getElementsByTagName('customer')->item(0) ?: $request->appendChild($data->createElement('customer'));
        $customer->appendChild($data->createElement('ip', $this->getClientIp()));

        if ($this->getCard()) {
            $this->setBillingCredentials($data);
            $this->setShippingCredentials($data);
        }

        return $data;
    }

    /**
     * @param DOMDocument $data
     */
    protected function setBillingCredentials(DOMDocument $data)
    {
        $card = $this->getCard();

        /** @var DOMDocument $billing */
        $billing = $data->getElementsByTagName('billing')->item(0);

        $name = $billing->appendChild($data->createElement('name'));
        $name
            ->appendChild($data->createElement('first'))
            ->appendChild($data->createTextNode($card->getBillingFirstName()));
        $name
            ->appendChild($data->createElement('last'))
            ->appendChild($data->createTextNode($card->getBillingLastName()));

        $billing
            ->appendChild($data->createElement('country'))
            ->appendChild($data->createTextNode($card->getBillingCountry()));
        $billing
            ->appendChild($data->createElement('county'))
            ->appendChild($data->createTextNode($card->getBillingState()));
        $billing
            ->appendChild($data->createElement('town'))
            ->appendChild($data->createTextNode($card->getBillingCity()));
        $billing
            ->appendChild($data->createElement('postcode'))
            ->appendChild($data->createTextNode($card->getBillingPostcode()));

        $address = implode(
            ', ',
            array_filter(array(
                $card->getBillingAddress1(),
                $card->getBillingAddress2(),
            ))
        ) ?: null;
        $billing
            ->appendChild($data->createElement('street'))
            ->appendChild($data->createTextNode($address));

        $billing
            ->appendChild($data->createElement('email'))
            ->appendChild($data->createTextNode($card->getEmail()));
        $billing
            ->appendChild($data->createElement('telephone'))
            ->appendChild($data->createTextNode($card->getBillingPhone()));
    }

    /**
     * @param DOMDocument $data
     */
    protected function setShippingCredentials(DOMDocument $data)
    {
        $card = $this->getCard();

        /** @var DOMDocument $request */
        $request = $data->getElementsByTagName('request')->item(0);
        /** @var DOMDocument $customer */
        $customer = $data->getElementsByTagName('customer')->item(0) ?: $request->appendChild($data->createElement('customer'));

        /** @var DOMDocument $name */
        $name = $customer->appendChild($data->createElement('name'));
        $name
            ->appendChild($data->createElement('first'))
            ->appendChild($data->createTextNode($card->getShippingFirstName()));
        $name
            ->appendChild($data->createElement('last'))
            ->appendChild($data->createTextNode($card->getShippingLastName()));

        $customer
            ->appendChild($data->createElement('country'))
            ->appendChild($data->createTextNode($card->getShippingCountry()));
        $customer
            ->appendChild($data->createElement('county'))
            ->appendChild($data->createTextNode($card->getShippingState()));
        $customer
            ->appendChild($data->createElement('town'))
            ->appendChild($data->createTextNode($card->getShippingCity()));
        $customer
            ->appendChild($data->createElement('postcode'))
            ->appendChild($data->createTextNode($card->getShippingPostcode()));

        $address = implode(
            ', ',
            array_filter(array(
                $card->getShippingAddress1(),
                $card->getShippingAddress2(),
            ))
        ) ?: null;
        $customer
            ->appendChild($data->createElement('street'))
            ->appendChild($data->createTextNode($address));

        $customer
            ->appendChild($data->createElement('email'))
            ->appendChild($data->createTextNode($card->getEmail()));
        $customer
            ->appendChild($data->createElement('telephone'))
            ->appendChild($data->createTextNode($card->getShippingPhone()));
    }
}
