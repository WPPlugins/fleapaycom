<?php
/* SVN FILE: $Id: fleapay_admin.php 27 2011-08-02 19:38:54Z zwilson $ */
/**
* @author         $Author: zwilson $
* @version        $Rev: 27 $
* @lastrevision   $Date: 2011-08-02 14:38:54 -0500 (Tue, 02 Aug 2011) $
* @filesource     $URL: file:///svnroot/wp-default-main/trunk/wp-content/plugins/fleapay/fleapay_admin.php $
*/

class Fleapay_Admin extends Fleapay
{
    public function __construct()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Admin::__construct...') : null;
        parent::__construct();
    }

    public function startup()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Admin::startup...') : null;

        add_action('admin_menu', array(&$this, 'create_admin_menus'));
        $this->admin_warnings();

        add_action('admin_print_styles', array(&$this, 'admin_styles'));
        add_action('widgets_init', create_function('', 'return register_widget("Fleapay_Products_Widget");'));

        if(!$this->app_cart_button_text()) {
            global $wpdb;
            $wpdb->query($wpdb->prepare("INSERT INTO $wpdb->options (option_name, option_value) VALUES (%s, %s)", $this->app_cart_button_name(), parent::FLEAPAY_CART_BUTTON_TEXT));
        }
    }

    static public function create_admin_menus()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Admin::create_admin_menus...') : null;

        $menu_slug = self::app_slug();
        $settings_slug = strtolower($menu_slug . '_settings');
        $help_slug = strtolower($menu_slug . '_help');
        $capability_access = 'manage_options';

        if ( function_exists('add_menu_page') ) {
            // we made capability access null so we didn't have a duplicate submenu item named FLEAPAY_APP_NAME
            add_menu_page(__(FLEAPAY_APP_NAME), __(FLEAPAY_APP_NAME), null, $menu_slug, array('Fleapay_Admin', 'admin_settings'), FLEAPAY_FAV_ICON);

            if ( function_exists('add_menu_page') ) {
                add_submenu_page($menu_slug, __(FLEAPAY_APP_NAME . ' Settings'), __('Settings'), $capability_access, $settings_slug, array('Fleapay_Admin', 'admin_settings'));
                add_submenu_page($menu_slug, __(FLEAPAY_APP_NAME . ' Help'), __('Help'), $capability_access, $help_slug, array('Fleapay_Admin', 'admin_help'));
            }
        }
    }

    static public function admin_warnings()
    {
        if ( !Fleapay::is_enabled() ) {
            function fleapay_warning() {
                // echo '<div id="'.$menu_slug.'-warning" class="updated fade"><p><strong>'.__(FLEAPAY_APP_NAME . ' is almost ready.').'</strong> '.sprintf(__('You must <a href="%1$s">enter your ' . FLEAPAY_APP_NAME . ' API key</a> for it to work.'), FLEAPAY_SETTING_URL).'</p></div>';
                echo Fleapay_Admin::admin_error_msg(Fleapay::app_no_key_msg());
            }
            add_action('admin_notices', self::app_slug() . '_warning');
            return;
        }
    }

    static public function admin_main()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Admin::admin_main...') : null;
    }

    static public function admin_settings()
    {
        global $wpdb;

        FLEAPAY_DEBUG ? error_log('Fleapay_Admin::admin_settings...') : null;

        $key_name = self::app_key_name();
        $btn_name = self::app_cart_button_name();

        if($_POST) {
            if($_fp_key = $_POST[$key_name]) {
                $api_response = self::http_post('/api/' . $_fp_key);

                if($api_response['body']) {
                    $_rsp = json_decode($api_response['body']);

                    if(isset($_rsp->valid) && $_rsp->valid == 'true') {
                        if(!$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) from $wpdb->options WHERE option_name = %s", $key_name))) {
                            $_res = $wpdb->query($wpdb->prepare("INSERT INTO $wpdb->options (option_value, option_name) VALUES (%s, %s)", $_fp_key, $key_name));
                        } else {
                            $_res = $wpdb->query($wpdb->prepare("UPDATE $wpdb->options SET option_value = %s WHERE option_name = %s", $_fp_key, $key_name));
                        }

                        echo self::admin_success_msg('API key saved!');
                    } else if(isset($_rsp->error)) {
                        echo self::admin_error_msg($_rsp->error->msg);
                    } else {
                        echo self::admin_error_msg('Sorry, we could not verify your api key at this time.');
                    }
                } else {
                    echo self::admin_error_msg('Sorry, we could not verify your api key at this time.');
                }
            }

            if($_btn_name = $_POST[$btn_name]) {
                $_res = $wpdb->query($wpdb->prepare("UPDATE $wpdb->options SET option_value = %s WHERE option_name = %s", $_btn_name, $btn_name));
            }
        }

        $fleapay_key = self::app_key();
        $fleapay_cart_button_text = self::app_cart_button_text();

        require_once FLEAPAY_PLUGIN_DIR . '/settings.php';
    }

    static public function admin_help()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Admin::admin_help...') : null;

        require_once FLEAPAY_PLUGIN_DIR . '/help.php';
    }

    static public function admin_error_msg($msg)
    {
        return parent::_msg($msg, 'error');
    }

    static public function admin_success_msg($msg)
    {
        return parent::_msg($msg, 'success');
    }

    static public function admin_styles()
    {
        wp_register_style('fleapay_admin', FLEAPAY_PLUGIN_URL . 'fleapay_admin.css');
        wp_enqueue_style('fleapay_admin');
        wp_register_style('fleapay_css', FLEAPAY_PLUGIN_URL . 'fleapay.css');
        wp_enqueue_style('fleapay_css');
    }
}
?>