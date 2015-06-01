<?php

namespace Nilead\OmniBaoKim\Message;

use Guzzle\Http\Message\RequestInterface;

/**
 * Noi Dia Complete Purchase Request
 */
class NoiDiaCompletePurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://onepay.vn/onecomm-pay/vpc.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/onecomm-pay/vpc.op';

    public function getData()
    {
        $this->validate('amount');

        $data = $this->getBaseData();
        $data['total_amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['currency'] = $this->getTransactionReference();

        return $data;
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint() . '?' . http_build_query($data, '', '&');
        $httpResponse = $this->httpClient->get($url)->send();

        return $this->createResponse($httpResponse->getBody());
    }

    protected function checkHash(){
        // get and remove the vpc_TxnResponseCode code from the response fields as we
        // do not want to include this field in the hash calculation
        $vpc_Txn_Secure_Hash = $this->data['vpc_SecureHash'];
        unset ($this->data['vpc_SecureHash']);

        // set a flag to indicate if hash has been validated
        $hashValidated = false;

        $SECURE_SECRET = $_SESSION['SECURE_SECRET'];
        unset($_SESSION['SECURE_SECRET']);

        if (strlen ( $SECURE_SECRET ) > 0 && $this->data['vpc_TxnResponseCode'] != "7" && $this->data['vpc_TxnResponseCode'] != "No Value Returned") {

            //$stringHashData = $SECURE_SECRET;
            //*****************************khởi tạo chuỗi mã hóa rỗng*****************************
            $stringHashData = "";

            // sort all the incoming vpc response fields and leave out any with no value
            foreach ( $this->data as $key => $value ) {
                //        if ($key != "vpc_SecureHash" or strlen($value) > 0) {
                //            $stringHashData .= $value;
                //        }
                //      *****************************chỉ lấy các tham số bắt đầu bằng "vpc_" hoặc "user_" và khác trống và không phải chuỗi hash code trả về*****************************
                if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
            //  *****************************Xóa dấu & thừa cuối chuỗi dữ liệu*****************************
            $stringHashData = rtrim($stringHashData, "&");


            //    if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper ( md5 ( $stringHashData ) )) {
            //    *****************************Thay hàm tạo chuỗi mã hóa*****************************
            if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)))) {
                // Secure Hash validation succeeded, add a data field to be displayed
                // later.
                $hashValidated = true;
            } else {
                // Secure Hash validation failed, add a data field to be displayed later.
            }
        } else {
            // Secure Hash was not validated, add a data field to be displayed later.
        }

        return $hashValidated;
    }

}
