<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author Riki
 */
class Espay_Utils {

    public static function getArrayKey($neddle, $haystack) {
        foreach ($haystack as $key => $val) {
            if (in_array($neddle, $val)) {
                return $key;
            }
        }
    }

}
