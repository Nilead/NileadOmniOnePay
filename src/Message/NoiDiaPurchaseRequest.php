<?php

namespace Nilead\OmniOnePay\Message;

/**
 * NoiDia Purchase Request
 */
class NoiDiaPurchaseRequest extends AbstractRequest
{
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
            'vpc_TicketNo' =>  $this->httpRequest->getClientIp(),
            'vpc_Currency' => $this->getCurrency()
        ];

        return array_merge($data, $this->getBaseData());
    }

    public function sendData($data)
    {
        $data = http_build_query($this->generateDataWithChecksum($data), '', '&');

        return $this->response = new NoiDiaPurchaseResponse($this, $data);
    }

}
