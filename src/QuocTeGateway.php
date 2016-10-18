<?php

namespace Nilead\OmniOnePay;

use Nilead\PaymentComponent\Model\TransactionContextInterface;

/**
 * OnePay Quoc Te Class
 *
 * @link https://mtf.onepay.vn/developer/resource/documents/docx/quy_trinh_tich_hop-quocte.pdf
 */
class QuocTeGateway extends NoiDiaGateway
{
    public function getName()
    {
        return 'OnePay Quoc Te';
    }

    public function purchase(array $parameters = array(), TransactionContextInterface $transactionContext = null)
    {
        if ($transactionContext) {
            $request = $transactionContext->getHttpRequest();

            $parameters['locale'] = $request->getLocale();
            $parameters['clientIp'] = $request->getClientIp();
        }

        return $this->createRequest('\Nilead\OmniOnePay\Message\QuocTePurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Nilead\OmniOnePay\Message\QuocTeFetchRequest', $parameters);
    }

    public function fetchCheckout(array $parameters = array())
    {
        return $this->createRequest('\Nilead\OmniOnePay\Message\QuocTeFetchRequest', $parameters);
    }

    public function getResponse(array $parameters = array(), $type = 'purchase')
    {
        return $this->createResponse('\Nilead\OmniOnePay\Message\QuocTePurchaseResponse', $parameters, $type);
    }

}
