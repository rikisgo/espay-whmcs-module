<?php

session_start();

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../../init.php';
require_once __DIR__ . '/../../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../../includes/invoicefunctions.php';

// Require Espay Library
require_once __DIR__ . '/../../espay-pg/Espay.php';

// Fetch gateway configuration parameters.
$gatewayModuleName = "espay";
$gatewayParams = getGatewayVariables($gatewayModuleName);
$espaypassword = $gatewayParams['espaypassword'];
//$espaymerchantkey = $gatewayParams['espaymerchantkey'];
$espaysignature = $gatewayParams['espaysignature'];

$signaturePostman = (!empty($_REQUEST['signature']) ? $_REQUEST['signature'] : '');
$rq_datetime = (!empty($_REQUEST['rq_datetime']) ? $_REQUEST['rq_datetime'] : '');
$member_id = (!empty($_REQUEST['member_id']) ? $_REQUEST['member_id'] : '');
$order_id = (!empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '');
$passwordServer = (!empty($_REQUEST['password']) ? $_REQUEST['password'] : '');
$debit_from = (!empty($_REQUEST['debit_from']) ? $_REQUEST['debit_from'] : '');
$credit_to = (!empty($_REQUEST['credit_to']) ? $_REQUEST['credit_to'] : '');
$product = (!empty($_REQUEST['product_code']) ? $_REQUEST['product_code'] : '');
$paidAmount = (!empty($_REQUEST['amount']) ? $_REQUEST['amount'] : '');

$config = Espay_Config::getInstance();
$fees = $config->getFeeData($gatewayParams);
$paymentFee = $fees[$product];

$payment_ref = (!empty($_REQUEST['payment_ref']) ? $_REQUEST['payment_ref'] : '');

$key = '##' . $espaysignature . '##' . $rq_datetime . '##' . $order_id . '##' . 'PAYMENTREPORT' . '##';
//$key = '##7BC074F97C3131D2E290A4707A54A623##2016-07-25 11:05:49##145000065##INQUIRY##';
$uppercase = strtoupper($key);
$signatureKeyRest = hash('sha256', $uppercase);

// validate the password
if ($espaypassword == $passwordServer) {

    if ($signatureKeyRest == $signaturePostman) {

        /**
         * Validate Callback Invoice ID.
         *
         * Checks invoice ID is a valid invoice number. Note it will count an
         * invoice in any status as valid.
         *
         * Performs a die upon encountering an invalid Invoice ID.
         *
         * Returns a normalised invoice ID.
         */
        $invoiceId = checkCbInvoiceID($order_id, $gatewayParams['name']);

        if (!$invoiceId) {
            echo '1,Invoice Id Does Not Exist,,,'; // if order id not exist show plain reponse
        } else {

            checkCbTransID($payment_ref);
            addInvoicePayment($order_id, $payment_ref, $paidAmount, $paymentFee, $gatewayModuleName);

            $reconsile_id = $member_id . " - " . $order_id . date('YmdHis');
            echo '0,Success,' . $reconsile_id . ',' . $order_id . ',' . date('Y-m-d H:i:s') . '';
        }
    } else {
        echo '1,Invalid Signature Key,,,';
    }
} else {
    // if password not true
    echo '1,Password does not match,,,';
}
?>