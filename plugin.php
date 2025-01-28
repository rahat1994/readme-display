<?php defined('ABSPATH') or die(__FILE__);

/*
Plugin Name: Readme Display
Description: Readme Display WordPress Plugin
Version: 1.0.0
Author: 
Author URI: 
Plugin URI: 
License: GPLv2 or later
Text Domain: readme-display
Domain Path: /language
*/

define('RD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RD_PLUGIN_VERSION', '1.0.0');
define('RD_PLUGIN_DEVELOPMENT', true);
define('RD_DB_VERSION', '1.0.0');



/*************** Code IS Poetry **************/
return (function ($_) {

    return $_(__FILE__);
})(
    require __DIR__ . '/boot/app.php',

    require __DIR__ . '/vendor/autoload.php'
);
/************ Built With WPFluent *************/


