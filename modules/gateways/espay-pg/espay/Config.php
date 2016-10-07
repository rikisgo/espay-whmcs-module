<?php

/**
 * Espay Configuration
 */
class Espay_Config {

    /**
     * Your merchant's server key
     * @static
     */
    public static $espaymerchantkey;

    /**
     * Your merchant's client key
     * @static
     */
    public static $espaypassword;

    /**
     * true for production
     * false for sandbox mode
     * @static
     */
    public static $isProduction = false;

    /**
     * Set it true to enable 3D Secure by default
     * @static

      public static $is3ds = false;
     */

    /**
     * Default options for every request
     * @static
     */
    public static $curlOptions = array();

    const SANDBOX_BASE_URL = 'http://secure-dev.sgo.co.id/espaysingle/paymentlist';
    const PRODUCTION_BASE_URL = 'http://espay.id';

    /**
     * @return string Espay API URL, depends on $isProduction
     */
    public static function getBaseUrl() {
        return Espay_Config::$isProduction ?
                Espay_Config::PRODUCTION_BASE_URL : Espay_Config::SANDBOX_BASE_URL;
    }

}
