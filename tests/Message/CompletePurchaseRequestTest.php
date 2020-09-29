<?php

namespace Omnipay\IPay88\Message;


use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    /**
     * @var CompletePurchaseRequest
     */
    private $request;

    public function setUp(): void
    {
        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->getHttpRequest()->request->replace([
            'MerchantCode' => 'M00003',
            'PaymentId' => 2,
            'RefNo' => '12345',
            'Amount' => '2,741.16',
            'Currency' => 'MYR',
            'Remark' => '100',
            'TransId' => '54321',
            'AuthCode' => '',
            'Status' => 1,
            'ErrDesc' => '',
            'Signature' => '0X0Yq8zEmJ3ddMtn8e/1suethT8='
        ]);

        $this->request->initialize([
            'card' => [
                'firstName' => 'Xu',
                'lastName' => 'Ding',
                'email' => 'xuding@spacebib.com',
                'number' => '93804194'
            ],
            'amount' => '1.00',
            'currency' => 'MYR',
            'description' => 'Marina Run 2016',
            'transactionId' => '12345',
            'returnUrl' => 'https://www.example.com/return',
        ]);

        $this->request->setMerchantKey('apple');

        $this->request->setMerchantCode('M00003');

        $this->request->setBackendUrl('https://www.example.com/backend');

    }

    public function testGetDataReturnCorrectComputedSignature()
    {
        $data = $this->request->getData();

        $this->assertSame('0X0Yq8zEmJ3ddMtn8e/1suethT8=', $data['ComputedSignature']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CompletePurchaseRequestReQuerySuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('54321', $response->getTransactionReference());
    }

    public function testSendFail()
    {
        $this->setMockHttpResponse('CompletePurchaseRequestReQuerySuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('54321', $response->getTransactionReference());
    }

}
