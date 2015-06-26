<?php

namespace Nilead\OmniOnePay\Message;

use Omnipay\Tests\TestCase;

class NoiDiaPurchaseRequestTest extends TestCase
{
    /**
     * @var \Nilead\OmniOnePay\Message\NoiDiaPurchaseRequest
     */
    private $request;

    public function setUp()
    {
        $client = $this->getHttpClient();
        $this->request = $this->getHttpRequest();

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

        $this->request->initialize($this->options);

        $this->request = new NoiDiaPurchaseRequest($client, $this->request);
    }

    public function testGetData()
    {
        $this->request->setVpc_MerchTxnRef('33333333333333333');

        $expected =[
            'vpc_Merchant' => 'ONEPAY',
            'vpc_AccessCode' => 'D67342C2',
            'vpc_order_id' => '1431785',
            'Title' => 'VPC 3-Party',
            'vpc_Version' => '2',
            'vpc_Command' => 'pay',
            'vpc_MerchTxnRef' => '33333333333333333',
//            'vpc_OrderInfo' => "Order_1431786_11111",
            'vpc_Amount' => 1000,
            'vpc_Locale' => 'vn',
            'vpc_ReturnURL' => 'http://truonghoang.cool/app_dev.php/backend/process_transaction.html/1431785?client_key=94bc04c3760620d537b6717abd53ff3e&action=return',
            'vpc_TicketNo' => '192.168.0.1',
            'vpc_Currency' => 'VND'

        ];

        // exclude by random property
        unset($this->request->getData()['vpc_OrderInfo']);

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testSendData()
    {
        $this->testGetData();

        $data = $this->request->generateDataWithChecksum($this->request->getData());

        $this->assertArrayHasKey('vpc_SecureHash', $data);
    }
}
