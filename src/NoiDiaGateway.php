<?php

namespace Nilead\OmniOnePay;

use Omnipay\Common\AbstractGateway;

/**
 * OnePay Noi Dia Class
 *
 * @link https://mtf.onepay.vn/developer/resource/documents/docx/quy_trinh_tich_hop-noidia.pdf
 */
class NoiDiaGateway extends AbstractGateway
{
    public function getName()
    {
        return 'OnePay Noi Dia';
    }

    public function getDefaultParameters()
    {
        $settings = parent::getDefaultParameters();

        return $settings;
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\OmniOnePay\Message\NoiDiaPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Nilead\OmniBaoKim\Message\NoiDiaCompletePurchaseRequest', $parameters);
    }

    public function fetchCheckout(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayPal\Message\NoiDiaCompletePurchaseRequest', $parameters);
    }
}
