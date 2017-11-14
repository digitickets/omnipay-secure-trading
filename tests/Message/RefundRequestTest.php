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

        $this->assertSame('REFUND', (string)$data->getElementsByTagName('request')->item(0)->getAttributes('type'));
    }

    public function testTransactionData()
    {
        $request = $this->request->getData()->getElementsByTagName('request')->item(0);
        $merchant = $request->getElementsByTagName('merchant')->item(0);

        $this->assertSame('1200', (string)$request->getElementsByTagName('billing')->item(0)->getElementsByTagName('amount')->item(0)->textContent);
        $this->assertSame('test-transaction-reference', (string)$request->getElementsByTagName('operation')->item(0)->getElementsByTagName('parenttransactionreference')->item(0)->textContent);
        $this->assertSame('jane@test.local', (string)$merchant->getElementsByTagName('email')->item(0)->textContent);
    }
}
