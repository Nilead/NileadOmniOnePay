<?php

namespace Nilead\OmniOnePay\Message;

/**
 * QuocTe Purchase Request
 */
class QuocTePurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://onepay.vn/vpcpay/vpcpay.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/vpcpay/vpcpay.op';

    public function getData()
    {
        $this->validate('amount');

        $data = [
            'vpc_order_id' => $this->getTransactionId(),
            'Title' => 'VPC 3-Party',
            'virtualPaymentClientURL' => $this->getEndpoint(),
            'vpc_Version' => $this::API_VERSION,
            'vpc_Command' => 'pay',
            //'vpc_MerchTxnRef' =>'201204091225015472',
            'vpc_MerchTxnRef' => date('YmdHis') . rand(),
            'vpc_OrderInfo' => "Order_" . $this->getTransactionId() . "_" . time(),
            //'vpc_OrderInfo' => 'JSECURETEST01',
            'vpc_Amount' => $this->getAmount(),
            //'vpc_Amount' => '1000',
            'vpc_Locale' => 'vn',
            //'vpc_ReturnURL'=>$url_return,
            //'vpc_ReturnURL'=>'http://localhost',
            'vpc_ReturnURL' => $this->getReturnUrl(),
            'AgainLink' => urlencode($_SERVER['HTTP_REFERER']), //$this->getCancelUrl(),
            //'vpc_TicketNo' =>'192.168.0.1',
            'vpc_TicketNo' => $_SERVER["REMOTE_ADDR"],
            //'pay_method' => isset($_SESSION['pay_method']) ? $_SESSION['pay_method'] : 'CC',
            'vpc_Currency' => $this->getCurrency(),
        ];

        $_SESSION['SECURE_SECRET'] = $this->getSecureHash();

        return array_merge($data, $this->getBaseData());
    }

    public function sendData($data)
    {
        $data = http_build_query($this->generateDataWithChecksum($data), '', '&');

        return $this->createResponse($data);
    }

    protected function createResponse($data)
    {
        return $this->response = new QuocTePurchaseResponse($this, $data);
    }
}
