<?php

/**
 ***** DO NOT CALL ANY FUNCTIONS DIRECTLY FROM THIS FILE ******
 *
 * This file will be loaded even before the framework is loaded
 * so the $app is not available here, only declare functions here.
 */

if ($app->config->get('app.env') == 'dev') {

    $globalsDevFile = __DIR__ . '/../dev/globals.php';
    
    is_readable($globalsDevFile) && include $globalsDevFile;
}

if (!function_exists('wpf_float_val')) {
    /**
     * PHP float val doesn't convert an int to float 
     * so, this is just a wrapper to get a real number.
     * 
     * @param  integer $val
     * @param  integer $frac
     * @return real number/float
     */
    function wpf_float_val($val = 0, $frac = 2) {
        $val = floatval($val);
        
        if (strpos($val, '.') === false) {
            $val = sprintf("%.{$frac}f", $val);
        }

        return $val;
    }
}
