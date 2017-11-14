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

        $billing = $data->getElementsByTagName('billing')->item(0);

        $this->assertSame($card->getBillingFirstName(), (string)$billing->getElementsByTagName('name')->item(0)->getElementsByTagName('first')->item(0)->textContent);
        $this->assertSame($card->getBillingLastName(), (string)$billing->getElementsByTagName('name')->item(0)->getElementsByTagName('last')->item(0)->textContent);
        $this->assertSame($card->getBillingPhone(), (string)$billing->getElementsByTagName('telephone')->item(0)->textContent);
        $this->assertSame($card->getBillingCountry(), (string)$billing->getElementsByTagName('country')->item(0)->textContent);
        $this->assertSame($card->getBillingCity(), (string)$billing->getElementsByTagName('town')->item(0)->textContent);
        $this->assertSame($card->getBillingState(), (string)$billing->getElementsByTagName('county')->item(0)->textContent);
        $this->assertSame($card->getBillingPostcode(), (string)$billing->getElementsByTagName('postcode')->item(0)->textContent);
        $this->assertSame(
            $card->getBillingAddress1() . ', ' . $card->getBillingAddress2(),
            (string)$billing->getElementsByTagName('street')->item(0)->textContent
        );
        $this->assertSame($card->getEmail(), (string)$billing->getElementsByTagName('email')->item(0)->textContent);
    }

    public function testShippingDetails()
    {
        $data = $this->request->getData();
        $card = $this->request->getCard();

        $customer = $data->getElementsByTagName('customer')->item(0);

        $this->assertSame($card->getShippingFirstName(), (string)$customer->getElementsByTagName('name')->item(0)->getElementsByTagName('first')->item(0)->textContent);
        $this->assertSame($card->getShippingLastName(), (string)$customer->getElementsByTagName('name')->item(0)->getElementsByTagName('last')->item(0)->textContent);
        $this->assertSame('', (string)$customer->getElementsByTagName('telephone')->item(0)->textContent);
        $this->assertSame($card->getShippingCountry(), (string)$customer->getElementsByTagName('country')->item(0)->textContent);
        $this->assertSame($card->getShippingCity(), (string)$customer->getElementsByTagName('town')->item(0)->textContent);
        $this->assertSame($card->getShippingState(), (string)$customer->getElementsByTagName('county')->item(0)->textContent);
        $this->assertSame($card->getShippingPostcode(), (string)$customer->getElementsByTagName('postcode')->item(0)->textContent);
        $this->assertSame(
            $card->getShippingAddress1() . ', ' . $card->getShippingAddress2(),
            (string)$customer->getElementsByTagName('street')->item(0)->textContent
        );
    }
}
