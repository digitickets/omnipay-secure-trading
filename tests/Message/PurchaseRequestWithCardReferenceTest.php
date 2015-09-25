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
        $request = $this->request->getData()->request;

        $this->assertObjectNotHasAttribute('payment', $request->billing);
        $this->assertSame('test-card-reference', (string)$request->operation->parenttransactionreference);
    }
}
