<?php

namespace Nilead\OmniOnePay\Message;

use Guzzle\Http\Message\RequestInterface;

/**
 * Quoc Te Complete Purchase Request
 */
class QuocTeCompletePurchaseRequest extends NoiDiaCompletePurchaseRequest
{
    protected $liveEndpoint = 'https://onepay.vn/vcppay/Vpcdps.op';
    protected $testEndpoint = 'https://mtf.onepay.vn/vcppay/Vpcdps.op';

}
