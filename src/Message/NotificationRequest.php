<?php

/*
  
  https://github.com/thephpleague/omnipay-common/blob/v3.0.4/src/Common/Message/NotificationInterface.php
  
  https://github.com/bileto/omnipay-gopay/blob/master/src/Gateway.php
  https://github.com/bileto/omnipay-gopay/blob/master/src/Message/Notification.php

  https://github.com/academe/omnipay-authorizenetapi/blob/master/src/HostedPageGateway.php
  https://github.com/academe/omnipay-authorizenetapi/blob/master/src/Message/AcceptNotification.php
  https://github.com/academe/omnipay-authorizenetapi/blob/master/README.md

  https://github.com/ampeco/omnipay-bankart/blob/master/src/Gateway.php
  https://github.com/ampeco/omnipay-bankart/blob/master/src/Message/AcceptNotification.php

  https://github.com/academe/omnipay-adyen/blob/master/src/ApiGateway.php
  https://github.com/academe/omnipay-adyen/blob/master/src/Message/Api/NotificationRequest.php

  https://github.com/academe/OmniPay-SagePay-Demo/blob/master/sagepay-confirm.php
  https://academe.co.uk/2015/06/omnipayauthorize-net-dpm-sequence-chart/

  https://github.com/thephpleague/omnipay-sagepay/blob/master/src/Message/ServerNotifyRequest.php

$merchantcode = $_REQUEST["MerchantCode"];
$paymentid = $_REQUEST["PaymentId"];
$refno = $_REQUEST["RefNo"];
$amount = $_REQUEST["Amount"];
$ecurrency = $_REQUEST["Currency"];
$remark = $_REQUEST["Remark"];
$transid = $_REQUEST["TransId"];
$authcode = $_REQUEST["AuthCode"];
$estatus = $_REQUEST["Status"];
$errdesc = $_REQUEST["ErrDesc"];
$signature = $_REQUEST["Signature"];
$ccname = $_REQUEST["CCName"];
$ccno = $_REQUEST["CCNo"];
$s_bankname = $_REQUEST["S_bankname"];
$s_country = $_REQUEST["S_country"];
  
IF ($estatus=1) {
    // COMPARE Return Signature with Generated Response Signature
    // update order to PAID
    echo "RECEIVEOK";
}
ELSE {
    // update order to FAIL
}

*/

namespace Omnipay\IPay88\Message;

use Omnipay\Common\Currency;
use Omnipay\Common\Message\NotificationInterface;

class NotificationRequest extends AbstractRequest implements NotificationInterface
{
    const RESPONSE_STATUS_OK        = 'RECEIVEOK';
    const RESPONSE_STATUS_ERROR     = 'ERROR';
    const RESPONSE_STATUS_INVALID   = 'INVALID';

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
        return $this->response = new NotificationResponse($this, $data);
    }


    /**
     * Confirm
     *
     * Notify IPay88 you received the payment details and wish to confirm the payment.
     *
     * @param string URL to forward the customer to.
     * @param string Optional human readable reasons for accepting the transaction.
     */
    public function confirm($nextUrl, $detail = null)
    {
        // If the signature is invalid, then do not allow the confirm.
        if (! $this->isValid()) {
            throw new InvalidResponseException('Cannot confirm an invalid notification');
        }

        $this->sendResponse(static::RESPONSE_STATUS_OK, $nextUrl, $detail);
    }


    /**
     * Respond to IPay88 confirming or rejecting the notification.
     *
     * @param string The status to send to IPay88, one of static::RESPONSE_STATUS_*
     * @param string URL to forward the customer to.
     * @param string Optional human readable reason for this response.
     */
    public function sendResponse($status, $nextUrl, $detail = null)
    {
        $message = $this->getResponseBody($status, $nextUrl, $detail);

        echo $message;

        if ((bool)$this->getExitOnResponse()) {
            exit;
        }
    }
}
