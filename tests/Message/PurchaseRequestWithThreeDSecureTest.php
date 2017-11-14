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

        $this->assertSame('THREEDQUERY', (string)$data->getElementsByTagName('request')->item(0)->getAttribute('type'));
        $this->assertSame('ECOM', (string)$data->getElementsByTagName('request')->item(0)->getElementsByTagName('operation')->item(0)->getElementsByTagName('accounttypedescription')->item(0)->textContent);
    }

    public function testThreeDSecureData()
    {
        $data = $this->request->getData();

        $merchant = $data->getElementsByTagName('merchant')->item(0);
        $customer = $data->getElementsByTagName('customer')->item(0);

        $this->assertSame('http://dummy-return-url.local', (string)$merchant->getElementsByTagName('termurl')->item(0)->textContent);
        $this->assertSame('*/*', (string)$customer->getElementsByTagName('accept')->item(0)->textContent);
        $this->assertSame('test-user-agent', (string)$customer->getElementsByTagName('useragent')->item(0)->textContent);
    }
}
