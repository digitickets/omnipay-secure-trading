<?php

namespace Omnipay\SecureTrading\Test\Message;

class PurchaseRequestWithCardReferenceTest extends PurchaseRequestTest
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            $this->getTestAuthorizationData(),
            array(
                'amount'        => '12.00',
                'currency'      => 'GBP',
                'cardReference' => 'test-card-reference',
            )
        );
    }

    public function testTransactionData()
    {
        $request = $this->request->getData();

        $this->assertObjectNotHasAttribute('payment', $request->getElementsByTagName('billing')->item(0));
        $this->assertSame('test-card-reference', (string)$request->getElementsByTagName('operation')->item(0)->getElementsByTagName('parenttransactionreference')->item(0)->textContent);
    }
}
