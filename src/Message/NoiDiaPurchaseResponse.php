<?php

namespace Nilead\OmniOnePay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * NoiDia Purchase Response
 */
class NoiDiaPurchaseResponse extends AbstractResponse
{
    protected $hashValidated = false;

    protected $transactionStatus = [
        '0'  => 'Giao dịch thành công - Approved',
        '1'  => 'Ngân hàng từ chối giao dịch - Bank Declined',
        '3'  => 'Mã đơn vị không tồn tại - Merchant not exist',
        '4'  => 'Không đúng access code - Invalid access code',
        '5'  => 'Số tiền không hợp lệ - Invalid amount',
        '6'  => 'Mã tiền tệ không tồn tại - Invalid currency code',
        '7'  => 'Lỗi không xác định - Unspecified Failure ',
        '8'  => 'Số thẻ không đúng - Invalid card Number',
        '9'  => 'Tên chủ thẻ không đúng - Invalid card name',
        '10' => 'Thẻ hết hạn/Thẻ bị khóa - Expired Card',
        '11' => 'Thẻ chưa đăng ký sử dụng dịch vụ - Card Not Registed Service(internet banking)',
        '12' => 'Ngày phát hành/Hết hạn không đúng - Invalid card date',
        '13' => 'Vượt quá hạn mức thanh toán - Exist Amount',
        '21' => 'Số tiền không đủ để thanh toán - Insufficient fund',
        '99' => 'Người sủ dụng hủy giao dịch - User cancel',
        'X'  => 'Giao dịch thất bại - Failured'
    ];


    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        parse_str($data, $this->data);

        $this->setHashValidated();

    }

    public function isSuccessful()
    {
        return $this->data['vpc_TxnResponseCode'] == '0' && $this->getHashValidated() ? true : false;
    }

    public function getConfirmReference()
    {
        $dataConfirm = [];

        if($this->getHashValidated()){
            $dataConfirm['responsecode'] = 1;
            $dataConfirm['desc'] = 'confirm-success';
        }else{
            $dataConfirm['responsecode'] = 0;
            $dataConfirm['desc'] = 'confirm-fail';
        }

        return $dataConfirm;
    }

    public function getTransactionReference()
    {
        foreach (['vpc_TransactionNo'] as $key) {
            if (isset($this->data[$key])) {
                return $this->data[$key];
            }
        }
    }

    public function getVpcMerchTxnRefReference()
    {
        foreach (['vpc_MerchTxnRef'] as $key) {
            if (isset($this->data[$key])) {
                return $this->data[$key];
            }
        }
    }

    /**
     * @return string
     */
    public function getMessage()
    {
//        return $this->data['vcp_Message'];
        return $this->getResponseDescription($this->data['vpc_TxnResponseCode']);
    }

    protected function getResponseDescription($responseCode) {

        if($responseCode == '0'){
            if(!$this->getHashValidated()){
                return "Giao dịch Pending - INVALID HASH";
            }else{
                return "Giao dịch thành công - Approved";
            }
        }else{
            if (array_key_exists($responseCode, $this->transactionStatus)) {
                return $this->transactionStatus[$responseCode];
            }

            return $this->transactionStatus['X'];
        }
    }

    protected function getHashValidated(){
        return $this->hashValidated;
    }

    protected function setHashValidated(){
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

        return $this->hashValidated = $hashValidated;
    }
}
