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

        $this->assertSame('AUTH', (string)$data->request->attributes()->type);
        $this->assertSame('ECOM', (string)$data->request->operation->accounttypedescription);
    }

    public function testTransactionData()
    {
        $request = $this->request->getData()->request;

        $this->assertSame('1200', (string)$request->billing->amount);
        $this->assertSame('GBP', (string)$request->billing->amount->attributes()->currencycode);
        $this->assertSame('VISA', (string)$request->billing->payment->attributes()->type);
        $this->assertSame('4111111111111111', (string)$request->billing->payment->pan);
        $this->assertSame('12/2020', (string)$request->billing->payment->expirydate);
        $this->assertSame('123', (string)$request->billing->payment->securitycode);
    }
}
