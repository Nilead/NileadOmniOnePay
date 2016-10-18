<?php

namespace Nilead\OmniOnePay\Message;

/**
 * QuocTe Fetch Request
 */
class QuocTeFetchRequest extends NoiDiaFetchRequest
{
    protected $liveEndpoint = 'https://onepay.vn/vpcpay/Vpcdps.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/vpcpay/Vpcdps.op';

    public function getData()
    {
        $data = parent::getData();

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

        return $this->response = new FetchQuocTeResponse($this, $httpResponse->getBody());
    }

    /**
     * Encode absurd name value pair format
     */
    public function encodeData(array $data)
    {
        $output = array();
        foreach ($data as $key => $value) {
            if (strlen($value) > 0 && $key != 'Title') {
                $output[] = urlencode($key) .'='. urlencode($value);
            }
        }

        return implode('&', $output);
    }
}
