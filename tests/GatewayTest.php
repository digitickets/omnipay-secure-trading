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
            'amount'        => '10.00',
            'transactionId' => 'testing123',
            'card'          => $this->getValidCard(),
        );
        $this->captureOptions  = array(
            'amount'               => '10.00',
            'transactionReference' => '4-9-2321060',
        );
    }

    public function testGatewaySettersGetters()
    {
        $this->assertSame('dummy_site_reference', $this->gateway->getSiteReference());
        $this->assertSame('username@dummy.local', $this->gateway->getUsername());
        $this->assertSame('pass123', $this->gateway->getPassword());
    }

    public function testCreateCardRequestSuccess()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');
        $response = $this->gateway->createCard($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame('33-52-9', $response->getTransactionReference());
    }

//    public function testAuthorizeSuccess()
//    {
//        $this->setMockHttpResponse('AuthorizeSuccess.txt');
//        $response = $this->gateway->authorize($this->purchaseOptions)->send();
//
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertNull($response->getMessage());
//        $this->assertSame('6f3b812a-dafa-e311-983c-00505692354f', $response->getTransactionReference());
//        $this->assertSame('a4f483ca-55fc-e311-8ca6-001422187e37', $response->getCardReference());
//        $this->assertSame('qo3tCvArxWUxsCONcIWGyHUhXKs=', $response->getCardHash());
//    }
//
//    public function testAuthorizeFailure()
//    {
//        $this->setMockHttpResponse('AuthorizeFailure.txt');
//        $response = $this->gateway->authorize($this->purchaseOptions)->send();
//
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('CvvNotMatched', $response->getMessage());
//    }
//
//    public function testPurchaseSuccess()
//    {
//        $this->setMockHttpResponse('AuthorizeSuccess.txt');
//        $response = $this->gateway->purchase($this->purchaseOptions)->send();
//
//        $requestData = $response->getRequest()->getData();
//        $this->assertSame('true', (string)$requestData->TransactionDetails->MessageType->attributes()->autoconfirm);
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertNull($response->getMessage());
//        $this->assertSame('6f3b812a-dafa-e311-983c-00505692354f', $response->getTransactionReference());
//        $this->assertSame('a4f483ca-55fc-e311-8ca6-001422187e37', $response->getCardReference());
//        $this->assertSame('qo3tCvArxWUxsCONcIWGyHUhXKs=', $response->getCardHash());
//    }
//
//    public function testPurchaseFailure()
//    {
//        $this->setMockHttpResponse('AuthorizeFailure.txt');
//        $response = $this->gateway->purchase($this->purchaseOptions)->send();
//
//        $requestData = $response->getRequest()->getData();
//        $this->assertSame('true', (string)$requestData->TransactionDetails->MessageType->attributes()->autoconfirm);
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('CvvNotMatched', $response->getMessage());
//    }
//
//    public function testCaptureSuccess()
//    {
//        $this->setMockHttpResponse('CaptureSuccess.txt');
//        $response = $this->gateway->capture($this->captureOptions)->send();
//
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertNull($response->getMessage());
//        $this->assertNotNull($response->getTransactionReference());
//    }
//
//    public function testCaptureFailure()
//    {
//        $this->setMockHttpResponse('CaptureFailure.txt');
//        $response = $this->gateway->capture($this->captureOptions)->send();
//
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('CardEaseReferenceInvalid', $response->getMessage());
//    }
//
//    public function testRefundSuccess()
//    {
//        $this->setMockHttpResponse('RefundSuccess.txt');
//        $response = $this->gateway->refund($this->captureOptions)->send();
//
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertNull($response->getMessage());
//        $this->assertNotNull($response->getTransactionReference());
//    }
//
//    public function testRefundFailure()
//    {
//        $this->setMockHttpResponse('RefundFailure.txt');
//        $response = $this->gateway->refund($this->captureOptions)->send();
//
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('TransactionAlreadyVoided', $response->getMessage());
//    }
//
//    public function testVoidSuccess()
//    {
//        $this->setMockHttpResponse('VoidSuccess.txt');
//        $response = $this->gateway->void($this->captureOptions)->send();
//
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertNull($response->getMessage());
//        $this->assertNotNull($response->getTransactionReference());
//    }
//
//    public function testVoidFailure()
//    {
//        $this->setMockHttpResponse('VoidFailure.txt');
//        $response = $this->gateway->void($this->captureOptions)->send();
//
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('TransactionAlreadyVoided', $response->getMessage());
//    }
}
