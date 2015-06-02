<?php
namespace Nilead\OmniOnePay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * QuocTe Purchase Response
 */
class QuocTePurchaseResponse extends Response implements RedirectResponseInterface
{
    protected $liveEndpoint = 'https://onepay.vn/vpcpay/vpcpay.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/vpcpay/vpcpay.op';

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->getCheckoutEndpoint() . '?' . http_build_query($this->data, '', '&');
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return null;
    }

    protected function getCheckoutEndpoint()
    {
        return $this->getRequest()->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
