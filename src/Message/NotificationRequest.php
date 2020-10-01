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
  https://github.com/thephpleague/omnipay-sagepay/blob/master/src/Traits/ServerNotifyTrait.php

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

$gateway = Omnipay::create('AuthorizeNetApi_Api');

$gateway->setAuthName($authName);
$gateway->setTransactionKey($authKey);
$gateway->setSignatureKey($signatureKey); // HMAC-256
$gateway->setTestMode(true); // for false

$notification = $gateway->acceptNotification();


$gateway = Omnipay::create('IPay88');
$gateway->initialize(config('omnipay.gateways.IPay88'));

$params = [
    'card' => [
        'firstName' => $order->name,
        'email' => $order->email ?: 'noemail@whatawaste.my',
        'phone' => $order->phone,
    ],
    'amount' => 1.00, // temporary hardcode
    'currency' => 'MYR',
    'description' => Str::limit($product_desc->implode(','), 90),
    'transactionId' => $order->order_number,
    'returnUrl' => url('api/customer/checkout/processing/'.$payment_method.'/'.$order_id.'/'.$token),
];
$request = $gateway->acceptNotification($params) // return NotificationRequest Class object
$response = $request->send(); // getData() then sendData()
*/

namespace Omnipay\IPay88\Message;

use Omnipay\Common\Currency;
use Omnipay\Common\Message\NotificationInterface;

class NotificationRequest extends AbstractRequest 
{

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

    protected function signature($merchantKey, $merchantCode, $paymentId, $refNo, $amount, $currency, $status)
    {
        $amount = str_replace([',', '.'], '', $amount);

        $paramsInArray = [$merchantKey, $merchantCode, $paymentId, $refNo, $amount, $currency, $status];

        return $this->createSignatureFromString(implode('', $paramsInArray));
    }
}
