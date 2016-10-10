<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once(dirname(__FILE__) . '/espay-pg/Espay.php');

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Meta_Data_Parameters
 *
 * @return array
 */
function espay_MetaData() {
    return array(
        'DisplayName' => 'Espay Payment Gateway Module',
        'APIVersion' => '1.0',
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => true,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function espay_config() {
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Espay Payment Gateway',
        ),
        // a text field type allows for single line text input
        'espaymerchantkey' => array(
            'FriendlyName' => 'Merchant Key',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'ID that used for partner identification',
        ),
        // a text field type allows for single line text input
        'espaysignature' => array(
            'FriendlyName' => 'Signature Key',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Your Signature Key here. Get it from Espay team',
        ),
        // a text field type allows for single line text input
        'espaypassword' => array(
            'FriendlyName' => 'Password',
            'Type' => 'password',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Password that used for web service identification',
        ),
        // the dropdown field type renders a select menu of options
        'environment' => array(
            'FriendlyName' => 'Production Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to allow real transaction, untick for testing transaction in sandbox mode',
        ),
            // the yesno field type displays a single checkbox option
//        'enable3ds' => array(
//            'FriendlyName' => 'Credit Card 3DS',
//            'Type' => 'yesno',
//            'Description' => 'Tick to enable 3DS for Credit Card payment',
//        ),
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see http://docs.whmcs.com/Payment_Gateway_Module_Parameters
 *
 * @return string
 */
function espay_link($params) {

    // Gateway Configuration Parameters
    $espaymerchantkey = $params['espaymerchantkey'];
    $espaypassword = $params['espaypassword'];
    $espaysignature = $params['espaysignature'];
    $environment = $params['environment'];
//    $enable3ds = $params['enable3ds'];
    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // Set configuration
    Espay_Config::$isProduction = ($environment == 'on') ? true : false;
    Espay_Config::$espaypassword = $espaypassword;
    Espay_Config::$espaymerchantkey = $espaymerchantkey;
    Espay_Config::$espaysignature = $espaysignature;
    // error_log($enable3ds); //debugan
//    Espay_Config::$is3ds = ($enable3ds == 'on') ? true : false;

    $seedForm = array(
        'key' => $espaymerchantkey,
        'backUrl' => $returnUrl,
        'orderId' => $invoiceId,
    );

    $htmlOutput = '<form method="post" action="' . Espay_Config::getBaseUrl() . '">';
    foreach ($seedForm as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
    }
    $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '</form>';

    return $htmlOutput;
}
