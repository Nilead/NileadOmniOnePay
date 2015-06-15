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
            'vpc_MerchTxnRef' => date('YmdHis') . rand(),
            'vpc_OrderInfo' => "Order_" . $this->getTransactionId() . "_" . time(),
            'vpc_Amount' => $this->getAmount(),
            'vpc_Locale' => $this->httpRequest->getLocale(),
            'vpc_ReturnURL' => $this->getReturnUrl(),
            'AgainLink' => urlencode($_SERVER['HTTP_REFERER']), //$this->getCancelUrl(),
            'vpc_TicketNo' => $_SERVER["REMOTE_ADDR"],
        ];

        return array_merge($data, $this->getBaseData());
    }

    public function sendData($data)
    {
        $data = http_build_query($this->generateDataWithChecksum($data), '', '&');

        return $this->response = new QuocTePurchaseResponse($this, $data);
    }

}
