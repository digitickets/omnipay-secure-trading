<?php

namespace Omnipay\SecureTrading\Test\Message;

class PurchaseRequestWithThreeDSecureTest extends PurchaseRequestTest
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            parent::getOptions(),
            array(
                'applyThreeDSecure' => true,
                'returnUrl'         => 'http://dummy-return-url.local',
                'accept'            => '*/*',
                'userAgent'         => 'test-user-agent',
            )
        );
    }

    public function testBaseData()
    {
        $data = $this->request->getData();

        $this->assertSame('THREEDQUERY', (string)$data->request->attributes()->type);
        $this->assertSame('ECOM', (string)$data->request->operation->accounttypedescription);
    }

    public function testThreeDSecureData()
    {
        $data = $this->request->getData();

        $merchant = $data->request->merchant;
        $customer = $data->request->customer;

        $this->assertSame('http://dummy-return-url.local', (string)$merchant->termurl);
        $this->assertSame('*/*', (string)$customer->accept);
        $this->assertSame('test-user-agent', (string)$customer->useragent);
    }
}
