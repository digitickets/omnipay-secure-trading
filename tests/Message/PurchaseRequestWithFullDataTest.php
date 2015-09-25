<?php

namespace Omnipay\SecureTrading\Test\Message;

use Omnipay\SecureTrading\Message\PurchaseRequest;

class PurchaseRequestWithFullDataTest extends PurchaseRequestTest
{
    /**
     * @var PurchaseRequest
     */
    protected $request;

    /**
     * @return array
     */
    protected function getOptions()
    {
        $data = parent::getOptions();

        $data['card'] = array_merge(
            $data['card'],
            array(
                'billingFirstName'  => 'Jane',
                'billingLastName'   => 'Smith',
                'billingPhone'      => '123456789',
                'shippingFirstName' => 'John',
                'shippingLastName'  => 'Doe',
                'country'           => 'United Kingdom',
                'city'              => 'London',
                'state'             => 'County of London',
                'postcode'          => 'NW1 ABC',
                'address1'          => 'Battersea',
                'address2'          => '492 Lavender Gardens',
                'email'             => 'jane@test.local',
            )
        );

        return $data;
    }

    public function testBillingDetails()
    {
        $data = $this->request->getData();
        $card = $this->request->getCard();

        $billing = $data->request->billing;

        $this->assertSame($card->getBillingFirstName(), (string)$billing->name->first);
        $this->assertSame($card->getBillingLastName(), (string)$billing->name->last);
        $this->assertSame($card->getBillingPhone(), (string)$billing->telephone);
        $this->assertSame($card->getBillingCountry(), (string)$billing->country);
        $this->assertSame($card->getBillingCity(), (string)$billing->town);
        $this->assertSame($card->getBillingState(), (string)$billing->county);
        $this->assertSame($card->getBillingPostcode(), (string)$billing->postcode);
        $this->assertSame(
            $card->getBillingAddress1() . ', ' . $card->getBillingAddress2(),
            (string)$billing->street
        );
        $this->assertSame($card->getEmail(), (string)$billing->email);
    }

    public function testShippingDetails()
    {
        $data = $this->request->getData();
        $card = $this->request->getCard();

        $customer = $data->request->customer;

        $this->assertSame($card->getShippingFirstName(), (string)$customer->name->first);
        $this->assertSame($card->getShippingLastName(), (string)$customer->name->last);
        $this->assertSame('', (string)$customer->telephone);
        $this->assertSame($card->getShippingCountry(), (string)$customer->country);
        $this->assertSame($card->getShippingCity(), (string)$customer->town);
        $this->assertSame($card->getShippingState(), (string)$customer->county);
        $this->assertSame($card->getShippingPostcode(), (string)$customer->postcode);
        $this->assertSame(
            $card->getShippingAddress1() . ', ' . $card->getShippingAddress2(),
            (string)$customer->street
        );
    }
}
