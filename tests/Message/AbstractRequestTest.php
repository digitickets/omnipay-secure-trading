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
            'accountType'   => 'ECOM',
        );
    }

    public function testRequestAuthorizationData()
    {
        $data = $this->request->getData();

        $this->assertSame('test-username', (string)$data->getElementsByTagName('alias')->item(0)->textContent);
        $this->assertSame('test-site-reference', (string)$data->getElementsByTagName('request')->item(0)->getElementsByTagName('operation')->item(0)->getElementsByTagName('sitereference')->item(0)->textContent);
        $this->assertSame('test-1234', (string)$data->getElementsByTagName('request')->item(0)->getElementsByTagName('merchant')->item(0)->getElementsByTagName('orderreference')->item(0)->textContent);
        $this->assertSame('test-password', $this->request->getPassword());
        $this->assertSame('ECOM', $this->request->getAccountType());
    }
}
