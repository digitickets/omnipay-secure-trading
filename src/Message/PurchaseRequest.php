<?php

namespace Omnipay\SecureTrading\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use SimpleXMLElement;

/**
 * Purchase Request
 *
 * @method Response send()
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'AUTH';
    }

    /**
     * @return SimpleXMLElement
     * @throws InvalidCreditCardException
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('amount');

        $data = $this->getBaseData();

        /** @var SimpleXmlElement $request */
        $request = $data->request;
        /** @var SimpleXmlElement $operation */
        $operation = $request->operation;
        $operation->addChild('accounttypedescription', 'ECOM');
        $operation->addChild('authmethod', 'FINAL');

        $billing = $request->addChild('billing');
        $amount = $billing->addChild('amount', $this->getAmountInteger());
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
        $name->addChild('prefix', $card->getBillingTitle());

        $billing->addChild('country', $card->getBillingCountry());
        $billing->addChild('county', $card->getBillingState());
        $billing->addChild('town', $card->getBillingCity());
        $billing->addChild('postcode', $card->getBillingPostcode());

        $address = implode(', ',
            array_filter(array(
                $card->getBillingAddress1(),
                $card->getBillingAddress2()
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
        $name->addChild('prefix', $card->getShippingTitle());

        $customer->addChild('country', $card->getShippingCountry());
        $customer->addChild('county', $card->getShippingState());
        $customer->addChild('town', $card->getShippingCity());
        $customer->addChild('postcode', $card->getShippingPostcode());

        $address = implode(', ',
            array_filter(array(
                $card->getShippingAddress1(),
                $card->getShippingAddress2()
            ))
        ) ?: null;
        $customer->addChild('street', $address);

        $customer->addChild('email', $card->getEmail());
        $customer->addChild('telephone', $card->getShippingPhone());
    }

    /**
     * @param SimpleXMLElement $data
     */
    protected function setCardHolderCredentials(SimpleXMLElement $data)
    {
        $card = $this->getCard();

        $address = $data->addChild('Address');
        $address->addAttribute('format', 'standard');

        $line1 = $address->addChild('Line', $card->getAddress1());
        $line1->addAttribute('id', 1);

        $line2 = $address->addChild('Line', $card->getAddress2());
        $line2->addAttribute('id', 2);

        $address->addChild('City', $card->getCity());
        $address->addChild('State', $card->getState());
        $address->addChild('ZipCode', $card->getPostcode());
        $address->addChild('Country', $card->getCountry());

        $contact = $data->addChild('Contact');

        $emailAddressList = $contact->addChild('EmailAddressList');
        $emailAddress1    = $emailAddressList->addChild('EmailAddress', $card->getEmail());
        $emailAddress1->addAttribute('id', 1);
        $emailAddress1->addAttribute('type', 'other');

        $name = $contact->addChild('Name');
        $name->addChild('FirstName', $card->getFirstName());
        $name->addChild('LastName', $card->getLastName());

        $phoneNumberList = $contact->addChild('PhoneNumberList');
        $phoneNumber1    = $phoneNumberList->addChild('PhoneNumber', $card->getPhone());
        $phoneNumber1->addAttribute('id', 1);
        $phoneNumber1->addAttribute('type', 'unknown');
    }
}
