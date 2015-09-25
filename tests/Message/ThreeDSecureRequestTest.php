<?php

namespace Omnipay\SecureTrading\Test\Message;

use Omnipay\SecureTrading\Message\ThreeDSecureRequest;

class ThreeDSecureRequestTest extends PurchaseRequestWithThreeDSecureTest
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        $data = array_merge(
            parent::getOptions(),
            array(
                'returnUrl' => 'http://dummy-return-url.local',
                'accept'    => '*/*',
                'userAgent' => 'test-user-agent',
            )
        );

        unset($data['applyThreeDSecure']);

        return $data;
    }

    public function setUp()
    {
        parent::setUp();

        $this->request = new ThreeDSecureRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getOptions());
    }
}
