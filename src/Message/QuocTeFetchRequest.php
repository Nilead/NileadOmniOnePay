<?php

namespace Nilead\OmniOnePay\Message;

/**
 * QuocTe Fetch Request
 */
class QuocTeFetchRequest extends AbstractRequest
{
    const API_VERSION = '1.0';

    protected $liveEndpoint = 'https://onepay.vn/vcppay/Vpcdps.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/vcppay/Vpcdps.op';

    public function getData()
    {
        $this->validate('vpc_MerchTxnRef');

        $data = $this->getBaseData();

        $data['vpc_Version'] = $this::API_VERSION;
        $data['vpc_Command'] = 'queryDR'; // method

        if (empty($this->getVpcPassword()) && empty($this->getVpcUser())) {
            throw new InvalidRequestException("The vpcUser or vpcPassword parameter is required");
        }

        $data['vpc_User'] = $this->getVpcUser();
        $data['vpc_Password'] = $this->getVpcPassword();
        $data['vpc_MerchTxnRef'] = $this->getVpcMerchTxnRefReference();

        return $data;
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->getEndpoint(), '', $data)->send(); // method POST

        return $this->createResponse($httpResponse->getBody());
    }

    protected function createResponse($data)
    {
        return $this->response = new FetchResponse($this, $data);
    }
}
