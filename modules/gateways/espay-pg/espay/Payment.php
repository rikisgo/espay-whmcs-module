<?php

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../../init.php';
require_once __DIR__ . '/../../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../../includes/invoicefunctions.php';

// Require Espay Library
require_once __DIR__ . '/../../espay-pg/Espay.php';

/**
 * Payment type to ensure realtime or non-realtime payment
 */
$payment_type = array(
    'ATM' => array(
        'BCAATM', 'BIIATM', 'MASPIONATM', 'MUAMALATATM', 'PERMATAATM',
    ),
    'ONLINE_PAYMENT' => array(
        'BCAKLIKPAY', 'EPAYBRI', 'DANAMONOB', 'DKIIB', 'MANDIRIIB', 'MAYAPADAIB', 'PERMATANETPAY', 'PERMATAPEB'
    ),
    'EMONEY' => array(
        'XLTUNAI', 'MANDIRIECASH', 'NOBUPAY'
    ),
    'CREDIT_CARD' => array(
        'CREDITCARD', 'BNIDBO'
    ),
    'OUTLET_PAYMENT' => array(
        'FINPAY195'
    ),
    'OTHER_NONONLINE' => array(
        'MANDIRISMS'
    )
);

// Configuration is automaticly paid or not
$payment_conf = array(
    'ATM' => false,
    'ONLINE_PAYMENT' => true,
    'EMONEY' => true,
    'CREDIT_CARD' => true,
    'OUTLET_PAYMENT' => false,
    'OTHER_NONONLINE' => false,
);

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
$paymentfee = 0;
$payment_ref = (!empty($_REQUEST['payment_ref']) ? $_REQUEST['payment_ref'] : '');

$key = '##' . $espaysignature . '##' . $rq_datetime . '##' . $order_id . '##' . 'PAYMENTREPORT' . '##';
//$key = '##7BC074F97C3131D2E290A4707A54A623##2016-07-25 11:05:49##145000065##INQUIRY##';
$uppercase = strtoupper($key);
$signatureKeyRest = hash('sha256', $uppercase);

// validate the password
if ($espaypassword == $passwordServer) {

    if ($signatureKeyRest == $signaturePostman) {

        // validate order id
        //$invoiceId = checkCbInvoiceID($order_id, $gatewayParams['name']);

        $result = select_query("tblinvoices", "COUNT(id)", array("id" => $order_id));
        $data = mysql_fetch_array($result);
        $invoiceId = $data['0'];

        if (!$invoiceId) {
            echo '1,Order Id Does Not Exist,,,'; // if order id not exist show plain reponse
        } else {

            $innerjoin = "tblclients ON tblclients.id = tblinvoices.userid";
            $field = "tblinvoices.*, tblclients.firstname, tblclients.lastname";
            $where = "tblinvoices.id='" . $order_id . "'";
            $result2 = select_query("tblinvoices", $field, $where, "", "", "", $innerjoin);
            $data2 = mysql_fetch_array($result2);

            $total = $data2['total'];
            $currency = getCurrency($data2['userid']);

            $comment = "";
            $comment .= "Transfer using Espay Payment Gateways" . "\n\n";
            $comment .= "Transfer from" . " " . $credit_to . "\n\n";
            $comment .= "Transfer to" . " " . $debit_from . "\n\n";
            $comment .= "Payments mades by." . " " . $product . "\n\n";

            $reconsile_id = $member_id . " - " . $order_id . date('YmdHis');
            echo '0,Success,' . $reconsile_id . ',' . $order_id . ',' . date('Y-m-d H:i:s') . '';

            // save the order id that already pay
            // $this->model_checkout_order->update($order_id, $this->config->get('sgopayment_order_status_id'), $comment, true);
            $espayPaymentType = Espay_Utils::getArrayKey($product, $payment_type);
            $isProcessPaidInv = $payment_conf[$espayPaymentType];

            if ($isProcessPaidInv) {
                /**
                 * Add Invoice Payment.
                 *
                 * Applies a payment transaction entry to the given invoice ID.
                 *
                 * @param int $invoiceId         Invoice ID
                 * @param string $transactionId  Transaction ID
                 * @param float $paymentAmount   Amount paid (defaults to full balance)
                 * @param float $paymentFee      Payment fee (optional)
                 * @param string $gatewayModule  Gateway module name
                 */
                addInvoicePayment(
                        $invoiceId, $payment_ref, $paidAmount, $paymentFee, $gatewayModuleName
                );
            }
        }
    } else {
        echo '1,Invalid Signature Key,,,';
    }
} else {
    // if password not true
    echo '1,Password does not match,,,';
}
?>