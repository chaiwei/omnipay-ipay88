<?php

namespace Omnipay\IPay88\Message;


use Omnipay\Common\Currency;

class CompletePurchaseRequest extends AbstractRequest
{
    protected $endpoint = 'https://payment.ipay88.com.my/epayment/enquiry.asp';

    public function getData()
    {
        $this->guardParameters();

        $data = $this->httpRequest->request->all();

        $data['ComputedSignature'] = $this->signature(
            $this->getMerchantKey(),
            $this->getMerchantCode(),
            $data['PaymentId'],
            $data['RefNo'],
            $data['Amount'],
            $data['Currency'],
            $data['Status']
        );

        return $data;
    }

    public function sendData($data)
    {
        /**
         * Requery is to double check the transaction on Ipay88 server
         * to make sure the record is exist and valid.
         * Any discrepancy will be updated on CompletePurchaseResponse __construct
         */
        $data['ReQueryStatus'] = $this->httpClient
            ->request('post', $this->endpoint.'?'.http_build_query([
                'MerchantCode' => $this->getMerchantCode(),
                'RefNo' => $data['RefNo'],
                'Amount' => $data['Amount']
                ]), [], json_encode([]))
            ->getBody()
            ->getContents();

        return $this->response = new CompletePurchaseResponse($this, $data);
    }

    
    protected function signature($merchantKey, $merchantCode, $paymentId, $refNo, $amount, $currency, $status)
    {
        $amount = str_replace([',', '.'], '', $amount);

        $paramsInArray = [$merchantKey, $merchantCode, $paymentId, $refNo, $amount, $currency, $status];

        return $this->createSignatureFromString(implode('', $paramsInArray));
    }
}
