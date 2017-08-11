<?php
/*
Plugin Name: Fleapay
Version: 0.7.6.1
Plugin URI: http://www.fleapay.com/support/wordpress
Description: If you want to use a gateway, such as Authorize.net, PayTrace, SecureNet or TransFirst, Fleapay is the FASTEST shopping cart to integrate into your site.
This plugin provides an interface to access all your 'active' products for Fleapay e-commerce.
To get started:
1) Click the "Activate" link to the left of this description,
2) <a href="https://www.fleapay.com/plans">Sign up</a> for a Fleapay account with API key, and
3) Go to your <a href="admin.php?page=fleapay_settings">Fleapay configuration</a> page, and save your API key.

Author: Gulo Solutions, LLC
Author URI: http://www.gulosolutions.com
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit();
}

// Wordpress version
if(!defined('FP_WP_VERSION_REQ')) define('FP_WP_VERSION_REQ', 2.8);
if(!defined('WP_VERSION')) define('WP_VERSION', get_bloginfo('version'));

// PHP
if(!defined('FP_PHP_VERSION_REQ')) define('FP_PHP_VERSION_REQ', 5.0);
if(!defined('PHP_VERSION')) define('PHP_VERSION', phpversion());

// Fleapay vars
define('FLEAPAY_DEBUG', false);
define('FLEAPAY_VERSION', '0.7.6.1');
define('FLEAPAY_PLUGIN', __FILE__);
define('FLEAPAY_PLUGIN_DIR', dirname(__FILE__));
define('FLEAPAY_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('FLEAPAY_APP_NAME', 'Fleapay');
define('FLEAPAY_CACHE_EXPIRE', 1); // set cache in minutes

if(FLEAPAY_DEBUG) {
    define('FLEAPAY_APP_HOME', 'http://www.fleapay.com');
} else {
    define('FLEAPAY_APP_HOME', 'http://www.fleapay.com');
}

require_once(FLEAPAY_PLUGIN_DIR . '/fleapay_core.php');
require_once(FLEAPAY_PLUGIN_DIR . '/fleapay_admin.php');
require_once(FLEAPAY_PLUGIN_DIR . '/fleapay_products_widget.php');

define('FLEAPAY_APP_HELP', FLEAPAY_APP_HOME . '/support');
define('FLEAPAY_FAV_ICON', FLEAPAY_APP_HOME . '/favicon.ico');
define('FLEAPAY_SETTING_URL', 'admin.php?page=' . Fleapay::app_slug() . '_settings');

if(PHP_VERSION < FP_PHP_VERSION_REQ) {
    function fleapay_php_warning() {
        $fleapay_php_error = 'Sorry, ' . FLEAPAY_APP_NAME . ' plugin requires your version of php to be greater than ' . FP_PHP_VERSION_REQ . '.';
        echo Fleapay::admin_error_msg($fleapay_php_error);
    }
    add_action('admin_notices', 'fleapay_php_warning');
    return;
}

if(WP_VERSION > FP_WP_VERSION_REQ) {
    if(is_admin()) {
        $_fleapay = new Fleapay_Admin();
    } else {
        $_fleapay = new Fleapay();
    }

    register_activation_hook(FLEAPAY_PLUGIN, $_fleapay->startup());
} else {
    function fleapay_wp_warning() {
        $fleapay_wp_warning = 'Sorry, ' . FLEAPAY_APP_NAME . ' plugin requires your version of wordpress to be greater than ' . FP_WP_VERSION_REQ . '.';
        echo Fleapay::admin_error_msg($fleapay_wp_warning);
    }
    add_action('admin_notices', 'fleapay_wp_warning');
    return;
}
?>
