<?php

namespace Omnipay\SecureTrading\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use SimpleXMLElement;

/**
 * CreateCard Request
 *
 * @method Response send()
 */
class CreateCardRequest extends AbstractRequest
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'STORE';
    }

    /**
     * @return SimpleXMLElement
     * @throws InvalidCreditCardException
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('card');
        $card = $this->getCard();
        $card->validate();

        $data = $this->getBaseData();

        /** @var SimpleXmlElement $request */
        $request = $data->request;
        /** @var SimpleXmlElement $operation */
        $operation = $request->operation;
        $operation->accounttypedescription = 'CARDSTORE';

        $billing = $request->addChild('billing');

        $name = $billing->addChild('name');
        $name->addChild('last', $card->getLastName());
        $name->addChild('first', $card->getFirstName());

        $payment = $billing->addChild('payment');
        $payment->addAttribute('type', strtoupper($card->getBrand()));

        $payment->addChild('pan', $card->getNumber());
        $payment->addChild('expirydate', $card->getExpiryDate('m/Y'));

        return $data;
    }

    /**
     * @param SimpleXMLElement $data
     */
    protected function setBillingCredentials(SimpleXMLElement $data)
    {
        $card = $this->getCard();

        $invoice = $data->addChild('Invoice');
        $address = $invoice->addChild('Address');
        $address->addAttribute('format', 'standard');

        $line1 = $address->addChild('Line', $card->getBillingAddress1());
        $line1->addAttribute('id', 1);

        $line2 = $address->addChild('Line', $card->getBillingAddress2());
        $line2->addAttribute('id', 2);

        $address->addChild('City', $card->getBillingCity());
        $address->addChild('State', $card->getBillingState());
        $address->addChild('ZipCode', $card->getBillingPostcode());
        $address->addChild('Country', $card->getBillingCountry());

        $contact = $invoice->addChild('Contact');
        $name    = $contact->addChild('Name');

        $name->addChild('FirstName', $card->getBillingFirstName());
        $name->addChild('LastName', $card->getBillingLastName());

        $phoneNumberList = $contact->addChild('PhoneNumberList');
        $phoneNumber1    = $phoneNumberList->addChild('PhoneNumber', $card->getBillingPhone());
        $phoneNumber1->addAttribute('id', 1);
        $phoneNumber1->addAttribute('type', 'unknown');
    }

    /**
     * @param SimpleXMLElement $data
     */
    protected function setShippingCredentials(SimpleXMLElement $data)
    {
        $card = $this->getCard();

        $invoice = $data->addChild('Delivery');
        $address = $invoice->addChild('Address');
        $address->addAttribute('format', 'standard');

        $line1 = $address->addChild('Line', $card->getShippingAddress1());
        $line1->addAttribute('id', 1);

        $line2 = $address->addChild('Line', $card->getShippingAddress2());
        $line2->addAttribute('id', 2);

        $address->addChild('City', $card->getShippingCity());
        $address->addChild('State', $card->getShippingState());
        $address->addChild('ZipCode', $card->getShippingPostcode());
        $address->addChild('Country', $card->getShippingCountry());

        $contact = $invoice->addChild('Contact');
        $name    = $contact->addChild('Name');

        $name->addChild('FirstName', $card->getShippingFirstName());
        $name->addChild('LastName', $card->getShippingLastName());

        $phoneNumberList = $contact->addChild('PhoneNumberList');
        $phoneNumber1    = $phoneNumberList->addChild('PhoneNumber', $card->getShippingPhone());
        $phoneNumber1->addAttribute('id', 1);
        $phoneNumber1->addAttribute('type', 'unknown');
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
