<?php

namespace Nilead\OmniOnePay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * NoiDia Fetch Request
 */
class NoiDiaFetchRequest extends AbstractRequest
{
    const API_VERSION = '1';

    protected $liveEndpoint = 'https://onepay.vn/onecomm-pay/Vpcdps.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/onecomm-pay/Vpcdps.op';

    public function getData()
    {
        $data = $this->getBaseData();

        $data['vpc_Version'] = $this::API_VERSION;
        $data['vpc_Command'] = 'queryDR'; // method

        if (empty($this->getVpcPassword()) && empty($this->getVpcUser())) {
            throw new InvalidRequestException("The vpcUser or vpcPassword parameter is required");
        }

        $data['vpc_User'] = $this->getVpcUser();
        $data['vpc_Password'] = $this->getVpcPassword();

        $data['vpc_MerchTxnRef'] = $this->getVpcMerchtxnref();

        return $data;
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'post',
            $this->getEndpoint(),
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            http_build_query($data, '', '&')
        );

        return $this->response = new FetchResponse($this, $httpResponse->getBody());
    }
}
