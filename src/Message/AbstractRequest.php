<?php
/**
 * OnePay Abstract Request
 */

namespace Nilead\OmniOnePay\Message;

use \Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

abstract class AbstractRequest extends BaseAbstractRequest
{
    const API_VERSION = '2.0';

    protected $liveEndpoint = 'https://onepay.vn/onecomm-pay/vpc.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/onecomm-pay/vpc.op';

    public function getVpcAccessCode()
    {
        return $this->getParameter('vpc_AccessCode');
    }

    public function setVpcAccessCode($vpc_AccessCode)
    {
        return $this->setParameter('vpc_AccessCode', $vpc_AccessCode);
    }

    public function getVpcMerchant()
    {
        return $this->getParameter('vpc_Merchant');
    }

    public function setVpcMerchant($vpc_Merchant)
    {
        return $this->setParameter('vpc_Merchant', $vpc_Merchant);
    }

    public function getSecureHash()
    {
        return $this->getParameter('secure_hash');
    }

    public function setSecureHash($secure_hash)
    {
        return $this->setParameter('secure_hash', $secure_hash);
    }

    public function getVpcSecureHash()
    {
        return $this->getParameter('vpc_SecureHash');
    }

    public function setVpcSecureHash($vpc_SecureHash)
    {
        return $this->setParameter('vpc_SecureHash', $vpc_SecureHash);
    }

    public function getVpcUser()
    {
        return $this->getParameter('vpc_User');
    }

    public function setVpcUser($vpc_User)
    {
        return $this->setParameter('vpc_User', $vpc_User);
    }

    public function getVpcPassword()
    {
        return $this->getParameter('vpc_Password');
    }

    public function setVpcPassword($vpc_Password)
    {
        return $this->setParameter('vpc_Password', $vpc_Password);
    }

    public function getTransactionReference()
    {
        return $this->getParameter('vpc_TransactionNo');
    }

    public function setTransactionReference($value)
    {
        return $this->setParameter('vpc_TransactionNo', $value);
    }

    public function getVpcMerchTxnRefReference()
    {
        return $this->getParameter('vpc_MerchTxnRef');
    }

    public function setVpcMerchTxnRefReference($value)
    {
        return $this->setParameter('vpc_MerchTxnRef', $value);
    }

    protected function getBaseData()
    {
        return [
            'vpc_Merchant' => $this->getVpcMerchant(),
            'vpc_AccessCode' => $this->getVpcAccessCode(),
        ];
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint() . '?' . http_build_query($data, '', '&');
        $httpResponse = $this->httpClient->get($url)->send();

        return $this->createResponse($httpResponse->getBody());
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    public function generateDataWithChecksum($data)
    {
        // sắp xếp dữ liệu theo thứ tự a-z trước khi nối lại
        // arrange array data a-z before make a hash
        ksort($data);
        // Remove the Virtual Payment Client URL from the parameter hash as we
        // do not want to send these fields to the Virtual Payment Client.
        // bÃ¡Â»Â giÃƒÂ¡ trÃ¡Â»â€¹ url vÃƒÂ  nÃƒÂºt submit ra khÃ¡Â»Âi mÃ¡ÂºÂ£ng dÃ¡Â»Â¯ liÃ¡Â»â€¡u
        unset($data["virtualPaymentClientURL"]);
        unset($data["SubButL"]);
        unset($data["vpc_order_id"]);

        //$stringHashData = $SECURE_SECRET; *****************************Khởi tạo chuỗi dữ liệu mã hóa trống*****************************
        $stringHashData = "";

        foreach ($data as $key => $value) {
            // create the md5 input and URL leaving out any fields that have no value
            // tạo chuỗi đầu dữ liệu những tham số có dữ liệu
            if (strlen($value) > 0) {
                //$stringHashData .= $value; *****************************sử dụng cả tên và giá trị tham số để mã hóa*****************************
                if ((strlen($value) > 0) && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
        }
        //*****************************xóa ký tự & ở thừa ở cuối chuỗi dữ liệu mã hóa*****************************
        $stringHashData = rtrim($stringHashData, "&");
        // Create the secure hash and append it to the Virtual Payment Client Data if
        // the merchant secret has been provided.

        // thêm giá trị chuỗi mã hóa dữ liệu được tạo ra ở trên vào cuối url
        //$vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($stringHashData));
        // *****************************Thay hàm mã hóa dữ liệu*****************************
        $data['vpc_SecureHash'] = strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $this->getSecureHash())));

        return $data;
    }

}
