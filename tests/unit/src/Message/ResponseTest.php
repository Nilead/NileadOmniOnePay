<?php

namespace Nilead\OmniOnePay\Message;

use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testConstruct()
    {
        // response should decode URL format data
        $response = new Response($this->getMockRequest(), 'example=value&foo=bar');
        $this->assertEquals(array('example' => 'value', 'foo' => 'bar'), $response->getData());
    }

    public function testProPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('NoiDiaPurchaseSuccess.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('96U93778BD657313D', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testProPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('NoiDiaPurchaseFailure.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('This transaction cannot be processed. Please enter a valid credit card expiration year.', $response->getMessage());
    }
}
