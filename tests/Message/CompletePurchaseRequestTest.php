<?php

namespace Omnipay\SecureTrading\Test\Message;

use Omnipay\SecureTrading\Message\CompletePurchaseRequest;

class CompletePurchaseRequestTest extends PurchaseRequestTest
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            $this->getTestAuthorizationData(),
            array(
                'md'    => 'test-md',
                'paRes' => 'test-pa-res',
            )
        );
    }

    public function setUp()
    {
        parent::setUp();

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getOptions());
    }

    public function testTransactionData()
    {
        $request = $this->request->getData()->request;

        $this->assertObjectNotHasAttribute('billing', $request);
        $this->assertSame('test-md', (string)$request->operation->md);
        $this->assertSame('test-pa-res', (string)$request->operation->pares);
    }
}
