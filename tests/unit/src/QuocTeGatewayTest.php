<?php

namespace Nilead\OmniOnePay;

use Omnipay\Tests\GatewayTestCase;

class QuocTeGatewayTest extends GatewayTestCase
{
    /**
     * @var QuocTeGateway
     */
    protected $gateway;

    /**
     * @var array
     */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new QuocTeGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'vpc_Merchant' => 'TESTONEPAY',
            'vpc_AccessCode' => '6BEB2546',
            'secureHash' => 'A3EFDFABA8653DF2342E8DAC29B51AF0',
            'testMode' => true,
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'returnUrl' => 'http://truonghoang.cool/app_dev.php/backend/process_transaction.html/1431785?client_key=94bc04c3760620d537b6717abd53ff3e&action=return',
            'amount' => 1000,
            'currency' => 'VND',
            'transactionId' => '1431786'
        );
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('QuocTePurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\Nilead\OmniOnePay\Message\QuocTePurchaseResponse', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('https://mtf.onepay.vn/vpcpay/vpcpay.op?' . http_build_query($this->options, '', '&'), $response->getRedirectUrl());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('QuocTePurchaseFailure.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('Field AgainLink value is invalid.', $response->getMessage());
    }

    public function testFetchSuccess()
    {
        $this->setMockHttpResponse('QuocTeFetchSuccess.txt');

        $options = [
            'vpc_Merchant' => 'TESTONEPAY',
            'vpc_AccessCode' => '6BEB2546',
            'vpc_Version' => '1',
            'vpc_Command' => 'queryDR',
            'testMode' => true,
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'vpc_MerchTxnRef' => 'GDEAXIEM_41382,4523317014',
        ];

        $request = $this->gateway->fetchCheckout($options);

        $this->assertInstanceOf('\Nilead\OmniOnePay\Message\QuocTeFetchRequest', $request);
        $this->assertSame('GDEAXIEM_41382,4523317014', $request->getTransactionReference());

        $response = $request->send();
        $this->assertTrue($response->isSuccessful());

        $this->assertSame('Giao dịch thành công - Approved', $response->getMessage());
    }

    public function testFetchFailure()
    {
        $this->setMockHttpResponse('NoiDiaFetchFailure.txt');

        $options = [
            'vpc_Merchant' => 'TESTONEPAY',
            'vpc_AccessCode' => 'D67342C2',
            'vpc_Version' => '1',
            'vpc_Command' => 'queryDR',
            'testMode' => true,
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'vpc_MerchTxnRef' => 'GDEAXIEM_41382,4523317014',
        ];

        $request = $this->gateway->fetchCheckout($options);

        $this->assertInstanceOf('\Nilead\OmniOnePay\Message\QuocTeFetchRequest', $request);
        $this->assertSame('GDEAXIEM_41382,4523317014', $request->getTransactionReference());

        $response = $request->send();
        $this->assertFalse($response->isSuccessful());

        $this->assertSame('Giao dịch thất bại - Failured', $response->getMessage());
    }
}
