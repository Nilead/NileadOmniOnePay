<?php

namespace Nilead\OmniOnePay;

use Omnipay\Common\AbstractGateway;

/**
 * OnePay Quoc Te Class
 *
 * @link https://mtf.onepay.vn/developer/resource/documents/docx/quy_trinh_tich_hop-quocte.pdf
 */
class QuocTeGateway extends AbstractGateway
{
    public function getName()
    {
        return 'OnePay Quoc Te';
    }

    public function getDefaultParameters()
    {
        return [
            'vpcAccessCode'        => '',
            'vpcMerchant'          => '',
            'secureHash'           => '',
            'vpcUser'              => '',
            'vpcPassword'          => '',
            'testMode'             => false,
        ];
    }

    public function getVpcAccessCode()
    {
        return $this->getParameter('vpcAccessCode');
    }

    public function setVpcAccessCode($vpcAccessCode)
    {
        return $this->setParameter('vpcAccessCode', $vpcAccessCode);
    }

    public function getVpcMerchant()
    {
        return $this->getParameter('vpcMerchant');
    }

    public function setVpcMerchant($vpcMerchant)
    {
        return $this->setParameter('vpcMerchant', $vpcMerchant);
    }

    public function getSecureHash()
    {
        return $this->getParameter('secureHash');
    }

    public function setSecureHash($secureHash)
    {
        return $this->setParameter('secureHash', $secureHash);
    }

    public function getVpcUser()
    {
        return $this->getParameter('vpcUser');
    }

    public function setVpcUser($vpcUser)
    {
        return $this->setParameter('vpcUser', $vpcUser);
    }

    public function getVpcPassword()
    {
        return $this->getParameter('vpcPassword');
    }

    public function setVpcPassword($vpcPassword)
    {
        return $this->setParameter('vpcPassword', $vpcPassword);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\OmniOnePay\Message\QuocTePurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Nilead\OmniOnePay\Message\QuocTeCompletePurchaseRequest', $parameters);
    }

    public function fetchCheckout(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\OmniOnePay\Message\QuocTeCompletePurchaseRequest', $parameters);
    }

}
