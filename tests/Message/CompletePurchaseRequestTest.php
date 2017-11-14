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
        $request = $this->request->getData()->getElementsByTagName('request')->item(0);

        $this->assertObjectNotHasAttribute('billing', $request);
        $this->assertSame('test-md', (string)$request->getElementsByTagName('operation')->item(0)->getElementsByTagName('md')->item(0)->textContent);
        $this->assertSame('test-pa-res', (string)$request->getElementsByTagName('operation')->item(0)->getElementsByTagName('pares')->item(0)->textContent);
    }
}
