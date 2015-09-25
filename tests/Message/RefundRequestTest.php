<?php

namespace Omnipay\SecureTrading\Test\Message;

use Omnipay\SecureTrading\Message\RefundRequest;

class RefundRequestTest extends AbstractRequestTest
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            $this->getTestAuthorizationData(),
            array(
                'amount'               => '12.00',
                'transactionReference' => 'test-transaction-reference',
                'card'                 => array(
                    'email' => 'jane@test.local',
                ),
            )
        );
    }

    public function setUp()
    {
        parent::setUp();

        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getOptions());
    }

    public function testBaseData()
    {
        $data = $this->request->getData();

        $this->assertSame('REFUND', (string)$data->request->attributes()->type);
    }

    public function testTransactionData()
    {
        $request = $this->request->getData()->request;

        $this->assertSame('1200', (string)$request->billing->amount);
        $this->assertSame('test-transaction-reference', (string)$request->operation->parenttransactionreference);
        $this->assertSame('jane@test.local', (string)$request->merchant->email);
    }
}
