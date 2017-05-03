<?php

/**
 * Espay Configuration
 */
class Espay_Config {

    /**
     * Your merchant's key
     * @static
     */
    public static $espaymerchantkey;

    /**
     * Your merchant's password, define it by your self
     * @static
     */
    public static $espaypassword;

    /**
     * Your merchant's signature key
     * @static
     */
    public static $espaysignature;

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

    const SANDBOX_BASE_URL = 'https://sandbox-kit.espay.id/espaysingle/paymentlist';
    const PRODUCTION_BASE_URL = 'https://kit.espay.id/espaysingle/paymentlist';

    /**
     * @return string Espay API URL, depends on $isProduction
     */
    public static function getBaseUrl() {
        return Espay_Config::$isProduction ?
                Espay_Config::PRODUCTION_BASE_URL : Espay_Config::SANDBOX_BASE_URL;
    }

}
