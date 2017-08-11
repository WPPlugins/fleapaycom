<?php
/* SVN FILE: $Id: fleapay_core.php 27 2011-08-02 19:38:54Z zwilson $ */
/**
* @author         $Author: zwilson $
* @version        $Rev: 27 $
* @lastrevision   $Date: 2011-08-02 14:38:54 -0500 (Tue, 02 Aug 2011) $
* @filesource     $URL: file:///svnroot/wp-default-main/trunk/wp-content/plugins/fleapay/fleapay_core.php $
*/

class Fleapay
{
    const FLEAPAY_CART_BUTTON_TEXT = 'Add To Cart';

    public function __construct()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay::__construct...') : null;
    }

    public function startup()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay::startup...') : null;

        add_action('wp_print_styles', array(&$this, 'app_cart_styles'));
        add_filter('the_content', array(&$this, 'app_add_to_cart'));
    }

    /*
    * @return Array
    */
    static public function read($post_id)
    {
        FLEAPAY_DEBUG ? error_log('Fleapay::read...') : null;

        $meta_values = get_post_meta($post_id, self::app_slug() . '_product', false);

        return $meta_values;
    }

    static public function delete($post_id, $value)
    {
        FLEAPAY_DEBUG ? error_log('Fleapay::delete...') : null;

        delete_post_meta($post_id, self::app_slug() . '_product', $value);
    }

    static public function create($post_id, $data)
    {
        FLEAPAY_DEBUG ? error_log('Fleapay::create...') : null;

        add_post_meta($post_id, self::app_slug() . '_product', $data);
    }

    static public function is_enabled()
    {
        return get_option(self::app_key_name());
    }

    public function app_add_to_cart($content)
    {
        $post_ID = get_the_ID();

        if($this->is_enabled() && $post_ID &&
            ($products = $this->read($post_ID))) {
            if(is_array($products)) {
                foreach($products as $product) {
                    if($product['item']['append'] == true) {
                        $add_to_cart .= $this->app_cart_html($product['item']);
                    }
                }

                $content .= $add_to_cart;
            }
        }

        return $content;
    }

    static public function app_cart_button_name()
    {
        return self::app_slug() . '_cart_button_name';
    }

    public function app_cart_button_text()
    {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare('SELECT option_value from ' . $wpdb->options . ' WHERE option_name = "%s"', self::app_cart_button_name()));
    }

    public function app_cart_html_button($url = "#")
    {
        $_btn_text = ($_btn_text = self::app_cart_button_text()) ? $_btn_text : self::FLEAPAY_CART_BUTTON_TEXT;
        return '<div class="fleap_button"><a href="' . $url . '">' . $_btn_text . '</a></div>';
    }

    public function app_cart_html($item)
    {
        if(isset($item['id'])) {
            $widget = '<div class="fleap_outer">';
            $widget .= '<div class="fleap_view_c"><a href="' . preg_replace('/\/[\w]+$/', '', $item['url']) . '">View Cart</a></div>';
            if(isset($item['price']) &&
                $item['price'] != '$0.00' && $item['price'] != 'Gift' && $item['price'] != 'Free') {
                $widget .= '<div class="fleap_price">' . $item['price'] . '</div>';
            }
            $widget .= '<div class="fleap_desc">' . $item['value'] . '</div>';
            $widget .= $this->app_cart_html_button($item['url']);
            $widget .= '</div>';
        }

        return $widget;
    }

    public function app_cart_styles()
    {
        wp_register_style('fleapay-cart', FLEAPAY_PLUGIN_URL . 'fleapay.css');
        wp_enqueue_style('fleapay-cart');
    }

    static public function app_slug()
    {
        return strtolower(FLEAPAY_APP_NAME);
    }

    static public function app_key_name()
    {
        return self::app_slug() . '_api_key';
    }

    static public function app_key()
    {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare('SELECT option_value from ' . $wpdb->options . ' WHERE option_name = "%s"', self::app_key_name()));
    }

    static public function app_fatal_msg()
    {
        return sprintf('Sorry, we\'re having some issues with %s currently.', FLEAPAY_APP_NAME);
    }

    static public function app_no_key_msg()
    {
        return sprintf('%s is almost ready. You must <a href="%s">enter your %1$s API key</a> for it to work.', FLEAPAY_APP_NAME, FLEAPAY_SETTING_URL);
    }

    static public function _msg($msg, $type = 'error')
    {
        switch($type) {
            case 'error':
                $id = 'fleapay-warning';
                break;
            case 'success':
                $id = 'fleapay-message';
                break;
            default:
                break;
        }

        return '<div id="' . $id . '" class="updated fade"><p><strong>'.__($msg).'</strong></p></div>';
    }

    static public function http_post($path, $request = null, $host = FLEAPAY_APP_HOME, $port = 80, $ip=null)
    {
        if( !class_exists( 'WP_Http' ) ) {
            include_once ABSPATH . WPINC . '/class-http.php';;
        }
        $fleapay_ua .= FLEAPAY_APP_NAME . '/' . FLEAPAY_VERSION;

        $content_length = strlen( $request );

        // regulate and make sure we have http(s) preceeding
        $http_host = preg_match('/^http(s)?:\/\//i', $host) ? $host : 'http://' . $host;

        // regulate and make sure slash is prepended
        $path = preg_match('/^\//', $path) ? $path : '/' . $path;

        // use a specific IP if provided
        // needed by fleapay_check_server_connectivity()
        if ( $ip && long2ip( ip2long( $ip ) ) ) {
            $http_host = $ip;
        } else {
            $http_host = $host;
        }

        $fleapay_url = $host . $path;

        $request = new WP_Http;
        $result = $request->request( $fleapay_url );

        return $result;
    }
}


?>
