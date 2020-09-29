<?php

namespace Omnipay\IPay88\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

    public function getBackendUrl()
    {
        return $this->getParameter('backendUrl');
    }

    public function setBackendUrl($backendUrl)
    {
        return $this->setParameter('backendUrl', $backendUrl);
    }

    public function getMerchantKey()
    {
        return $this->getParameter('merchantKey');
    }

    public function setMerchantKey($merchantKey)
    {
        return $this->setParameter('merchantKey', $merchantKey);
    }

    public function getMerchantCode()
    {
        return $this->getParameter('merchantCode');
    }

    public function setMerchantCode($merchantCode)
    {
        return $this->setParameter('merchantCode', $merchantCode);
    }

    protected function guardParameters()
    {
        $this->validate(
            'card',
            'amount',
            'currency',
            'description',
            'transactionId',
            'returnUrl'
        );
    }

    /**
     * Check whether the signature is valid.
     *
     * @return bool True if the signature is valid; false otherwise.
     */
    public function isValid()
    {
        return $this->getSignature() === $this->buildSignature();
    }

    protected function createSignatureFromString($fullStringToHash)
    {
        return  hash('sha256', $fullStringToHash);
    }

}