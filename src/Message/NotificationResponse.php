<?php

namespace Omnipay\IPay88\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\IPay88\Message\CompletePurchaseResponse;

class NotificationResponse extends CompletePurchaseResponse implements NotificationInterface
{
    const STATUS_COMPLETED = 'success';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';

    protected $acknowledgementMsg = 'RECEIVEOK';

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@link NotificationInterface::STATUS_COMPLETED},
     * {@link NotificationInterface::STATUS_PENDING}, or {@link NotificationInterface::STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        return $this->isSuccessful();
    }

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getTransactionId()
    {
        return $this->data['RefNo'];
    }
    
    /**
     * Confirm
     *
     * Notify IPay88 you received the payment details and wish to confirm the payment.
     *
     * @param string URL to forward the customer to.
     * @param string Optional human readable reasons for accepting the transaction.
     */
    public function confirm()
    { 
        echo $this->acknowledgementMsg;
        exit;
    }

}