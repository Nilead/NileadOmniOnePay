<?php

namespace Nilead\OmniOnePay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        parse_str($data, $this->data);
    }

    public function isSuccessful()
    {
        return $this->request->getParameters("vpc_DRExists") == 'Y' && $this->request->getParameters('vpc_TxnResponseCode') == 0 ? true : false;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if($this->request->getParameters("vpc_DRExists") == 'N'){
            return  "Không tồn tại giao dịch";
        }else{
            return $this->getResponseDescription($this->request->getParameters('vpc_TxnResponseCode'));
        }
    }

    /**
     * @return string
     */
    protected function getResponseDescription($responseCode) {
        switch ($responseCode) {
            case "0" :
                $result = "Giao dịch thành công - Approved";
                break;
            case "300" :
                $result = "Giao dịch đang chờ - Pending";
                break;
            default :
                $result = " Giao dịch không thanh toán thành công - Failured";
        }
        return $result;
    }

}
