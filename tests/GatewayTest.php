<?php

namespace Omnipay\SecureTrading\Test;

use Omnipay\SecureTrading\Gateway;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var array
     */
    protected $purchaseOptions;

    /**
     * @var array
     */
    protected $captureOptions;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->setSiteReference('dummy_site_reference');
        $this->gateway->setUsername('username@dummy.local');
        $this->gateway->setPassword('pass123');

        $this->purchaseOptions = array(
            'amount'        => '0.98',
            'transactionId' => 'test-1234',
            'card'          => $this->getValidCard(),
        );
    }

    public function testGatewaySettersGetters()
    {
        $this->assertSame('dummy_site_reference', $this->gateway->getSiteReference());
        $this->assertSame('username@dummy.local', $this->gateway->getUsername());
        $this->assertSame('pass123', $this->gateway->getPassword());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('6-9-1922866', $response->getTransactionReference());
        $this->assertSame('6-9-1922866', $response->getCardReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');
        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Invalid field', $response->getMessage());
        $this->assertSame(30000, $response->getCode());
        $this->assertSame('securitycode', $response->getErrorData());
        $this->assertSame('4-9-2324005', $response->getTransactionReference());
        $this->assertSame('4-9-2324005', $response->getCardReference());
        $this->assertNull($response->getSettleStatus());
        $this->assertNull($response->getSettleDueDate());
    }

    public function testPurchaseCardReferenceSuccess()
    {
        $this->setMockHttpResponse('PurchaseCardReferenceSuccess.txt');
        $response = $this->gateway->purchase(array_merge($this->purchaseOptions, array(
            'card'          => null,
            'cardReference' => '6-9-1918130',
        )))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('3-9-2205017', $response->getTransactionReference());
        $this->assertSame('3-9-2205017', $response->getCardReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());
    }

    public function testPurchaseCardReferenceFailure()
    {
        $this->setMockHttpResponse('PurchaseCardReferenceFailure.txt');
        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Missing parent', $response->getMessage());
        $this->assertSame(20004, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getSettleStatus());
        $this->assertNull($response->getSettleDueDate());
    }

    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $response = $this->gateway->refund(array(
            'amount'               => '0.66',
            'transactionReference' => '6-9-1922866',
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('6-9-1922867', $response->getTransactionReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());
    }

    public function testRefundFailure()
    {
        $this->setMockHttpResponse('RefundFailure.txt');
        $response = $this->gateway->refund(array(
            'amount'               => '1.00',
            'transactionReference' => '3-9-2205014',
        ))->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Refund amount too great', $response->getMessage());
        $this->assertSame(20007, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('3-9-2205015', $response->getTransactionReference());
        $this->assertNull($response->getSettleStatus());
        $this->assertNull($response->getSettleDueDate());
    }

    public function testCreateCardRequestSuccess()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');
        $response = $this->gateway->createCard($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame('33-52-9', $response->getTransactionReference());
    }
}
