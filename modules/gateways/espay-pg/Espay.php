<?php

// This snippet due to the braintree_php.
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
    throw new Exception('PHP version >= 5.2.1 required');
}

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
    throw new Exception('Espay needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Espay needs the JSON PHP extension.');
}

// Configurations
require_once('espay/Config.php');

// Utils
require_once('espay/Utils.php');