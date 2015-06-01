<?php
namespace Nilead\OmniBaoKim\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Bao Kim Express Authorize Response
 */
class ExpressCompletePurchaseResponse extends Response implements RedirectResponseInterface
{
    public function isRedirect()
    {
        return false;
    }

    public function getRedirectUrl()
    {
        return null;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }
}