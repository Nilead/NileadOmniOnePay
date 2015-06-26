<?php

namespace Nilead\OmniOnePay\Message;

use Omnipay\Tests\TestCase;

class QuocTePurchaseRequestTest extends TestCase
{
    /**
     * @var \Nilead\OmniOnePay\Message\QuocTePurchaseRequest
     */
    private $request;

    public function setUp()
    {
        $client = $this->getHttpClient();
        $this->request = $this->getHttpRequest();

        $this->options = array(
            'vpc_Merchant' => 'TESTONEPAY',
            'vpc_AccessCode' => '6BEB2546',
            'secureHash' => 'A3EFDFABA8653DF2342E8DAC29B51AF0',
            'testMode' => true,
            'vpcUser' => 'op01',
            'vpcPassword' => 'op123456',
            'returnUrl' => 'http://truonghoang.cool/app_dev.php/backend/process_transaction.html/1431786?client_key=94bc04c3760620d537b6717abd53ff3e&action=return',
            'amount' => 1000,
            'currency' => 'VND',
            'transactionId' => '1431786'
        );

        $this->request->initialize($this->options);

        $this->request = new QuocTePurchaseRequest($client, $this->request);
    }

    public function testGetData()
    {
        $this->request->setVpc_MerchTxnRef('3333333333333333344444');

        $expected =[
            'vpc_Merchant' => 'TESTONEPAY',
            'vpc_AccessCode' => '6BEB2546',
            'vpc_order_id' => '1431786',
            'Title' => 'VPC 3-Party',
            'vpc_Version' => '2',
            'vpc_Command' => 'pay',
            'vpc_MerchTxnRef' => '3333333333333333344444',
//            'vpc_OrderInfo' => "Order_1431786_22222",
            'vpc_Amount' => 1000,
            'vpc_Locale' => 'vn',
            'vpc_ReturnURL' => 'http://truonghoang.cool/app_dev.php/backend/process_transaction.html/1431786?client_key=94bc04c3760620d537b6717abd53ff3e&action=return',
            'vpc_TicketNo' => '192.168.0.2',
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
