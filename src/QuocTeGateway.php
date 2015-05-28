<?php

namespace Nilead\OmniOnePay;


/**
 * OnePay Quoc Te Class
 *
 * @link https://mtf.onepay.vn/developer/resource/documents/docx/quy_trinh_tich_hop-quocte.pdf
 */
class QuocTeGateway extends AbstractGateway
{
    protected $liveEndpoint = 'https://onepay.vn/vpcpay/vpcpay.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/vpcpay/vpcpay.op';

    public function getName()
    {
        return 'OnePay Quoc Te';
    }

    public function getDefaultParameters()
    {
        $settings = parent::getDefaultParameters();

        return $settings;
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\OmniOnePay\Message\QuocTePurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Nilead\OmniBaoKim\Message\QuocTeCompletePurchaseRequest', $parameters);
    }

    public function fetchCheckout(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayPal\Message\QuocTeCompletePurchaseRequest', $parameters);
    }

}
