<?php

namespace Nilead\OmniOnePay;

use Omnipay\Tests\GatewayTestCase;

class NoiDiaGatewayTest extends GatewayTestCase
{
    /**
     * @var NoiDiaGateway
     */
    protected $gateway;

    /**
     * @var array
     */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new NoiDiaGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'vpc_Merchant' => 'ONEPAY',
            'vpc_AccessCode' => 'D67342C2',
            'secureHash' => 'A3EFDFABA8653DF2342E8DAC29B51AF0',
            'testMode' => true,
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'returnUrl' => 'http://truonghoang.cool/app_dev.php/backend/process_transaction.html/1431785?client_key=94bc04c3760620d537b6717abd53ff3e&action=return',
            'amount' => 1000,
            'currency' => 'VND',
            'transactionId' => '1431785'
        );
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('NoiDiaPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\Nilead\OmniOnePay\Message\NoiDiaPurchaseResponse', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('https://mtf.onepay.vn/onecomm-pay/vpc.op?' . http_build_query($this->options, '', '&'), $response->getRedirectUrl());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('NoiDiaPurchaseFailure.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('Field AgainLink value is invalid.', $response->getMessage());
    }

    public function testFetchSuccess()
    {
        $this->setMockHttpResponse('NoiDiaFetchSuccess.txt');

        $options = [
            'vpc_Merchant' => 'ONEPAY',
            'vpc_AccessCode' => 'D67342C2',
            'secureHash' => 'A3EFDFABA8653DF2342E8DAC29B51AF0',
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'testMode' => true,
            'vpc_MerchTxnRef' => '2413'
        ];

        $request = $this->gateway->fetchCheckout($options);

        $this->assertInstanceOf('\Nilead\OmniOnePay\Message\NoiDiaFetchRequest', $request);
        $this->assertSame('2413', $request->getTransactionReference());

        $response = $request->send();
        $this->assertTrue($response->isSuccessful());

        $this->assertSame('Giao dịch thành công - Approved', $response->getMessage());
    }

    public function testFetchFailure()
    {
        $this->setMockHttpResponse('NoiDiaFetchFailure.txt');

        $options = [
            'vpc_Merchant' => 'ONEPAY',
            'vpc_AccessCode' => 'D67342C2',
            'secureHash' => 'A3EFDFABA8653DF2342E8DAC29B51AF0',
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'testMode' => true,
            'vpc_MerchTxnRef' => '2013042215193440019'
        ];

        $request = $this->gateway->fetchCheckout($options);

        $this->assertInstanceOf('\Nilead\OmniOnePay\Message\NoiDiaFetchRequest', $request);
        $this->assertSame('2013042215193440019', $request->getTransactionReference());

        $response = $request->send();
        $this->assertFalse($response->isSuccessful());

        $this->assertSame('Giao dịch thất bại - Failured', $response->getMessage());
    }

}
