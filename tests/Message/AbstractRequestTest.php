<?php

namespace Omnipay\SecureTrading\Test\Message;

use Omnipay\SecureTrading\Message\AbstractRequest;
use Omnipay\Tests\TestCase;

abstract class AbstractRequestTest extends TestCase
{
    /**
     * @var AbstractRequest
     */
    protected $request;

    protected function getTestAuthorizationData()
    {
        return array(
            'siteReference' => 'test-site-reference',
            'username'      => 'test-username',
            'password'      => 'test-password',
            'transactionId' => 'test-1234',
        );
    }

    public function testRequestAuthorizationData()
    {
        $data = $this->request->getData();

        $this->assertSame('test-username', (string)$data->alias);
        $this->assertSame('test-site-reference', (string)$data->request->operation->sitereference);
        $this->assertSame('test-1234', (string)$data->request->merchant->orderreference);
        $this->assertSame('test-password', $this->request->getPassword());
    }
}
