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
    protected $options;

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

        $this->options = array(
            'amount'        => '0.98',
            'currency'      => 'GBP',
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
        $response = $this->gateway->purchase($this->options)->send();

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
        $response = $this->gateway->purchase($this->options)->send();

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
        $response = $this->gateway->purchase(array_merge($this->options, array(
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
        $response = $this->gateway->purchase(array_merge($this->options, array(
            'card'          => null,
            'cardReference' => '6-9-1918130X',
        )))->send();

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

    public function testPurchaseWithThreeDSecureEnrolled()
    {
        $this->setMockHttpResponse('PurchaseWithThreeDSecureEnrolled.txt');
        $response = $this->gateway->purchase(array_merge($this->options, array(
            'returnUrl'         => 'http://dummy.return/url',
            'applyThreeDSecure' => true,
        )))->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('7-9-1794658', $response->getTransactionReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());

        $this->assertSame('Y', $response->getEnrolled());
        $this->assertTrue($response->isEnrolled());
        $this->assertSame(
            'UEZOVVBqeE5SRDQ4VFVSSVBuaHhURXBpT1M4d1ZHeEhSR052T1VobFQzSTNUMUU5UFR3dlRVUklQanho' .
            'WTNOVmNtdythSFIwY0hNNkx5OTNaV0poY0hBdWMyVmpkWEpsZEhKaFpHbHVaeTV1WlhRdllXTnpMM1pw' .
            'YzJFdVkyZHBQQzloWTNOVmNtdytQSEJoYmt4bGJtZDBhRDR4Tmp3dmNHRnVUR1Z1WjNSb1BqeHRaWE56' .
            'WVdkbFNXUStVRUZTWlhFdE1UUTBNekE0TmpBNU5EWTJNQzB0TWpBeU1qSTNOVFl3Tmp3dmJXVnpjMkZu' .
            'WlVsa1Bqd3ZUVVErUEM5VFZEND06bWRjU2NVNFlRamFIOUppUFhwNDZ3S1Z1QktQVkt1eHd6TzZsYUZv' .
            'WHZILzk2OUNnVU5GQ2tMVXJBUGZQcVh4VUls',
            $response->getMd()
        );
        $this->assertSame('RWFrVW9lSkZYYytadUVFRkFYWXg=', $response->getXid());
        $this->assertSame(
            'eJxVUk1vwjAM/SuIe0ka2o4ik4mB0DhsmmCDsVtILVpBP0hTVP79ktKO4ZOf4zzbz4bPWCHO1ygrhRze' .
            'sCzFAXtJNOl/TFd4dlzPG9JRQEMvCKjjMMoYe/IDGvQ5NBkcLqjKJM+4O6ADBqSDhkzJWGSag5Dnl+U7' .
            'd9nQ8wMgLYQU1XLO6aMBuYUhEynyTV7JGNVMKA2kiYDMq0yrKx8xQ9UBqNSJx1oXY0KKuNjn9SDCi6HC' .
            'fZI7eZolhbg6ZTOmo5WIkuxAsBZpccKB+fAspDZNT4YREEsF5N79R2W90pSuk4ivtgu12Yan9fFnt7tq' .
            'EX1tFqvjYrf9PkyA2AyIhEbOqOvTkHk9Go5db+x7QJo4iNT2zMOREeLmQmErTO/x/xhMywoz2Q3cIcC6' .
            'yDM0GUbzPx8iLCXXWGrHqm1qWgzkPsPs1e5DaiOx51prdWeuazfTPFjuxKhqZriRWwDEfiXt0kl7KsZ7' .
            'OKFfMenCUw==',
            $response->getPaReq()
        );
        $this->assertSame('https://webapp.securetrading.net/acs/visa.cgi', $response->getRedirectUrl());
        $this->assertSame(array(
            'PaReq'   => $response->getPaReq(),
            'TermUrl' => $response->getRequest()->getReturnUrl(),
            'MD'      => $response->getMd(),
        ), $response->getRedirectData());
        $this->assertSame('POST', $response->getRedirectMethod());
    }

    public function testPurchaseWithThreeDSecureNotEnrolled()
    {
        $this->setMockHttpResponse(array(
            'PurchaseWithThreeDSecureNotEnrolled.txt',
            'PurchaseSuccessWithThreeDSecureNotEnrolled.txt',
        ));
        $response = $this->gateway->purchase(array_merge($this->options, array(
            'returnUrl'         => 'http://dummy.return/url',
            'applyThreeDSecure' => true,
        )))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('7-9-1796835', $response->getTransactionReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-25', $response->getSettleDueDate());

        $this->assertSame('7-9-1796834', (string)$response->getData()->response->operation->parenttransactionreference);
    }

    public function testCompletePurchaseSuccess()
    {
        $this->setMockHttpResponse('CompletePurchaseSuccess.txt');
        $response = $this->gateway->completePurchase(array(
            'md'    => 'dummy',
            'paRes' => 'dummy',
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('4-9-2324407', $response->getTransactionReference());
        $this->assertSame('4-9-2324407', $response->getCardReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());

        $this->assertSame('Q0FWVkNBVlZDQVZWQ0FWVkNBVlY=', (string)$response->getData()->response->threedsecure->cavv);
        $this->assertSame('Y', (string)$response->getData()->response->threedsecure->status);
        $this->assertSame('dHFncHQ1WUltcGhKS1NCUmFGUUo=', (string)$response->getData()->response->threedsecure->xid);
        $this->assertSame('05', (string)$response->getData()->response->threedsecure->eci);
        $this->assertSame('Y', (string)$response->getData()->response->threedsecure->enrolled);
    }

    public function testCompletePurchaseFailure()
    {
        $this->setMockHttpResponse('CompletePurchaseFailure.txt');
        $response = $this->gateway->completePurchase(array(
            'md'    => 'dummy',
            'paRes' => 'dummy',
        ))->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Unauthenticated', $response->getMessage());
        $this->assertSame(60022, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('7-9-1794888', $response->getTransactionReference());
        $this->assertSame('7-9-1794888', $response->getCardReference());
        $this->assertSame(3, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());

        $this->assertSame('N', (string)$response->getData()->response->threedsecure->status);
        $this->assertSame('Y', (string)$response->getData()->response->threedsecure->enrolled);
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

    public function testThreeDSecureEnrolled()
    {
        $this->setMockHttpResponse('ThreeDSecureEnrolled.txt');
        $response = $this->gateway->threeDSecure(array_merge($this->options, array(
            'returnUrl' => 'http://dummy.return/url',
        )))->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('7-9-1794658', $response->getTransactionReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());

        $this->assertSame('Y', $response->getEnrolled());
        $this->assertTrue($response->isEnrolled());
        $this->assertSame(
            'UEZOVVBqeE5SRDQ4VFVSSVBuaHhURXBpT1M4d1ZHeEhSR052T1VobFQzSTNUMUU5UFR3dlRVUklQanho' .
            'WTNOVmNtdythSFIwY0hNNkx5OTNaV0poY0hBdWMyVmpkWEpsZEhKaFpHbHVaeTV1WlhRdllXTnpMM1pw' .
            'YzJFdVkyZHBQQzloWTNOVmNtdytQSEJoYmt4bGJtZDBhRDR4Tmp3dmNHRnVUR1Z1WjNSb1BqeHRaWE56' .
            'WVdkbFNXUStVRUZTWlhFdE1UUTBNekE0TmpBNU5EWTJNQzB0TWpBeU1qSTNOVFl3Tmp3dmJXVnpjMkZu' .
            'WlVsa1Bqd3ZUVVErUEM5VFZEND06bWRjU2NVNFlRamFIOUppUFhwNDZ3S1Z1QktQVkt1eHd6TzZsYUZv' .
            'WHZILzk2OUNnVU5GQ2tMVXJBUGZQcVh4VUls',
            $response->getMd()
        );
        $this->assertSame('RWFrVW9lSkZYYytadUVFRkFYWXg=', $response->getXid());
        $this->assertSame(
            'eJxVUk1vwjAM/SuIe0ka2o4ik4mB0DhsmmCDsVtILVpBP0hTVP79ktKO4ZOf4zzbz4bPWCHO1ygrhRze' .
            'sCzFAXtJNOl/TFd4dlzPG9JRQEMvCKjjMMoYe/IDGvQ5NBkcLqjKJM+4O6ADBqSDhkzJWGSag5Dnl+U7' .
            'd9nQ8wMgLYQU1XLO6aMBuYUhEynyTV7JGNVMKA2kiYDMq0yrKx8xQ9UBqNSJx1oXY0KKuNjn9SDCi6HC' .
            'fZI7eZolhbg6ZTOmo5WIkuxAsBZpccKB+fAspDZNT4YREEsF5N79R2W90pSuk4ivtgu12Yan9fFnt7tq' .
            'EX1tFqvjYrf9PkyA2AyIhEbOqOvTkHk9Go5db+x7QJo4iNT2zMOREeLmQmErTO/x/xhMywoz2Q3cIcC6' .
            'yDM0GUbzPx8iLCXXWGrHqm1qWgzkPsPs1e5DaiOx51prdWeuazfTPFjuxKhqZriRWwDEfiXt0kl7KsZ7' .
            'OKFfMenCUw==',
            $response->getPaReq()
        );
        $this->assertSame('https://webapp.securetrading.net/acs/visa.cgi', $response->getRedirectUrl());
        $this->assertSame(array(
            'PaReq'   => $response->getPaReq(),
            'TermUrl' => $response->getRequest()->getReturnUrl(),
            'MD'      => $response->getMd(),
        ), $response->getRedirectData());
        $this->assertSame('POST', $response->getRedirectMethod());
    }

    public function testThreeDSecureNotEnrolled()
    {
        $this->setMockHttpResponse('ThreeDSecureNotEnrolled.txt');
        $response = $this->gateway->threeDSecure(array_merge($this->options, array(
            'returnUrl' => 'http://dummy.return/url',
        )))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame(0, $response->getCode());
        $this->assertNull($response->getErrorData());
        $this->assertSame('6-9-1923134', $response->getTransactionReference());
        $this->assertSame(0, $response->getSettleStatus());
        $this->assertSame('2015-09-24', $response->getSettleDueDate());

        $this->assertSame('N', $response->getEnrolled());
        $this->assertFalse($response->isEnrolled());
        $this->assertNull($response->getMd());
        $this->assertSame('WmpqVzNjeFM3RnVGSUVKbm1WaFA=', $response->getXid());
        $this->assertNull($response->getPaReq());
        $this->assertNull($response->getRedirectUrl());
        $this->assertNull($response->getRedirectData());
    }
}
