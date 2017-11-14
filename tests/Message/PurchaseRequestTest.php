<?php

namespace Omnipay\SecureTrading\Test\Message;

use Omnipay\SecureTrading\Message\PurchaseRequest;

class PurchaseRequestTest extends AbstractRequestTest
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            $this->getTestAuthorizationData(),
            array(
                'amount'   => '12.00',
                'currency' => 'GBP',
                'card'     => array(
                    'number'      => '4111111111111111',
                    'expiryMonth' => '12',
                    'expiryYear'  => '2020',
                    'cvv'         => '123',
                ),
            )
        );
    }

    public function setUp()
    {
        parent::setUp();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getOptions());
    }

    public function testBaseData()
    {
        $data = $this->request->getData();

        $this->assertSame('AUTH', (string)$data->getElementsByTagName('request')->item(0)->getAttribute('type'));
        $this->assertSame('ECOM', (string)$data->getElementsByTagName('request')->item(0)->getElementsByTagName('operation')->item(0)->getElementsByTagName('accounttypedescription')->item(0)->textContent);
    }

    public function testTransactionData()
    {
        $request = $this->request->getData()->getElementsByTagName('request')->item(0);

        $this->assertSame('1200', (string)$request->getElementsByTagName('billing')->item(0)->getElementsByTagName('amount')->item(0)->textContent);
        $this->assertSame('GBP', (string)$request->getElementsByTagName('billing')->item(0)->getElementsByTagName('amount')->item(0)->getAttribute('currencycode'));
        $this->assertSame('VISA', (string)$request->getElementsByTagName('billing')->item(0)->getElementsByTagName('payment')->item(0)->getAtrtibute('type'));
        $this->assertSame('4111111111111111', (string)$request->getElementsByTagName('billing')->item(0)->getElementsByTagName('payment')->item(0)->getElementsByTagName('pan')->item(0)->textContent);
        $this->assertSame('12/2020', (string)$request->getElementsByTagName('billing')->item(0)->getElementsByTagName('payment')->item(0)->getElementsByTagName('expirydate')->item(0)->textContent);
        $this->assertSame('123', (string)$request->getElementsByTagName('billing')->item(0)->getElementsByTagName('payment')->item(0)->getElementsByTagName('securitycode')->item(0)->textContent);
    }
}
