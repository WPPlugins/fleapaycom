<?php
/* SVN FILE: $Id: fleapay_products_widget.php 27 2011-08-02 19:38:54Z zwilson $ */
/**
* @author         $Author: zwilson $
* @version        $Rev: 27 $
* @lastrevision   $Date: 2011-08-02 14:38:54 -0500 (Tue, 02 Aug 2011) $
* @filesource     $URL: file:///svnroot/wp-default-main/trunk/wp-content/plugins/fleapay/fleapay_products_widget.php $
*/

class Fleapay_Products_Widget extends WP_Widget
{
    public function __construct()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::__construct...') : null;

        parent::__construct($class_name, $name, $widget_ops);

        if(Fleapay::is_enabled()) {
            $post_ID = (int) (isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : 0));
            $this->post = get_post($post_ID);

            $class_name = get_class($this);
            $this->name = str_replace('_', ' ', $class_name);
            $widget_ops = array('classname' => $class_name, 'description' => __( 'Plugin to access ' . FLEAPAY_APP_NAME . ' e-commerce products.') );

            add_action('init', array( &$this, 'admin_widget_init'));
        }
    }

    public function admin_widget_init()
    {
        add_action('add_meta_boxes', array( &$this, 'admin_setup' ));

        // this has to be in init
        add_action('wp_ajax_admin_prod_add', array(&$this, 'admin_prod_add_callback'));
        add_action('wp_ajax_admin_prod_del', array(&$this, 'admin_prod_del_callback'));

        $this->title = __( FLEAPAY_APP_NAME . ' Product(s)' );
    }

    public function admin_setup()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::admin_setup...') : null;

        add_meta_box( $this->id_base, $this->title, array( &$this, 'form' ), $this->post->post_type, 'normal', 'high' );

        add_action('admin_print_scripts', array(&$this, 'admin_jquery'));
        add_action('admin_print_styles', array(&$this, 'admin_styles'));
        add_action('admin_print_footer_scripts', array(&$this, 'admin_ajax'));
    }

    public function admin_styles()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::admin_styles...') : null;

        wp_deregister_style('jquery-ui');
        wp_register_style('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css');
        wp_enqueue_style('jquery-ui');

        ?>
        <style>
            .ui-autocomplete {
                max-height: 100px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                /* add padding to account for vertical scrollbar */
                padding-right: 20px;
            }
            /* IE 6 doesn't support max-height
             * we use height instead, but this forces the menu to always be this tall
             */
            * html .ui-autocomplete {
                height: 100px;
            }
        </style>
        <?php
    }

    public function admin_jquery()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::admin_jquery...') : null;

        wp_deregister_script('jquery-ui-core-google');
        wp_register_script('jquery-ui-core-google', ('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js'), false, '1.10.3');
        wp_enqueue_script('jquery-ui-core-google');

        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        wp_localize_script( Fleapay::app_slug() . '-ajax-request', Fleapay::app_slug() . 'Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }

    public function admin_prod_add_callback()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::admin_prod_add_callback...') : null;

        // generate the response
        if(!empty($_POST['prod'])) {
            $post_id = $this->post->ID;
            $response = json_encode( array( 'success' => true, 'id' => $post_id) );

            Fleapay::create($post_id, $_POST['prod']);
        } else {
            $response = json_encode( array( 'success' => false ) );
        }

        header( "Content-Type: application/json" );
        echo $response;

        exit;
    }

    public function admin_prod_del_callback()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::admin_prod_del_callback...') : null;

        // generate the response
        if(!empty($_POST['prod'])) {
            $post_id = $this->post->ID ? $this->post->ID : $_POST['post_ID'];
            $response = json_encode( array( 'success' => true, 'id' => $post_id) );

            Fleapay::delete($post_id, $_POST['prod']);
        } else {
            $response = json_encode( array( 'success' => false ) );
        }

        header( "Content-Type: application/json" );
        echo $response;

        exit;
    }

    public function admin_ajax()
    {
    ?>
    <script type="text/javascript" charset="utf-8">
        jQuery(document).ready(function($){
            var selected_products = <?=$this->selected_products;?>;
            var products = <?=$this->products?>;
            var post_ID = $('#post_ID').val();
            var selectedProduct;
            var availableProducts = $( products ).map(
                                        function() {
                                            return {
                                                value: $.trim( this.title ) || "(unknown product)",
                                                price: this.price,
                                                url: this.url,
                                                id: this.url.replace(/.*\//, ''),
                                                append: 1
                                            };
                                        }).get();

            var displayProduct = function(item, index) {
                                    input = '<div id="'+index+'" class="<?=Fleapay::app_slug()?>_product">';
                                    input += '<span id="'+item.id+'" class="<?=Fleapay::app_slug()?>_product_remove">&nbsp;X&nbsp;</span> '+item.value;
                                    // radio's will be used in a later release
                                    input += '</div>';
                                    jQuery(input).appendTo('#<?=Fleapay::app_slug()?>_list');
                                    // item.append == true ? $('#append_'+item.id).attr('checked', 'checked') : $('#shortcode_'+item.id).attr('checked', 'checked');
                                    $('#'+item.id).bind('click', function() {
                                        var div = $(this).parent().attr('id');
                                        var data = {
                                                action: 'admin_prod_del',
                                                prod: selected_products[div],
                                                post_ID: post_ID ? post_ID : '<?=$this->post->ID?>',
                                            };

                                        jQuery.post(ajaxurl, data, function(response) {
                                            if(response.success == true) {
                                                selected_products[div] = '';
                                                $('#'+div).remove();
                                            }
                                        });
                                    })
                                };

            var prodObj = function(obj) {
                return data = {
                        action: 'admin_prod_add',
                        prod: obj,
                        post_ID: post_ID ? post_ID : '<?=$this->post->ID?>'
                    };
            };

            var postObj = function(obj) {
                if(!$('#'+obj.prod.item.id).length) {
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post(ajaxurl, obj, function(response) {
                        var item = obj.prod;
                        if(response.success == true && (indx = (selected_products.push(item) - 1)) !== false) {
                            displayProduct(item.item, indx);
                        }
                    });
                }
            };

            if(selected_products.length) {
                jQuery.each(selected_products, function(key, val) {
                    displayProduct(val.item, key);
                });
            }

            $("#<?php echo $this->get_field_id('add'); ?>").click(function() {
                // alert($("#<?php echo $this->get_field_id('search');?>").val());
                postObj(selectedProduct);
                return false;
            });

            $("#<?php echo $this->get_field_id('search');?>").autocomplete({
                source: availableProducts,
                disabled: false,
                autoFocus: false,
                minLength: 2,
                search: function ( event, ui ) {
                },
                select: function( event, prod ) {
                    var data = prodObj(prod);
                    postObj(data);
                },
                focus: function(event, prod) {
                    var data = prodObj(prod);
                    selectedProduct = data;
                },
                change: function(event, prod) {
                    $(this).attr('value', '');
                }
            });
        });
    </script>
    <?php
    }

    public function form($instance)
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::form...') : null;

        $this->selected_products = json_encode(($_sel_prod = Fleapay::read($this->post->ID)) ? $_sel_prod : array());

        $this->products = array();

        $this->added = Fleapay::read(get_the_ID());

        if($products = $this->_fetchProducts()) {
            if(isset($products->error)) {
                $this->products = $products;
                echo __('<p>' . $this->products->error->msg . '</p>');
            } else if(count($products->products) > 0) {
                $this->products = json_encode($products->products);
                ?>
                <label>Product Search:</label>
                <input class="fat autocomplete ac_input" id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>" type="text" autocomplete="off" />
                <input type="submit" id="<?php echo $this->get_field_id('add'); ?>" name="<?php echo $this->get_field_name('add'); ?>" value="Add" class="button button-highlighted" />
                <div id="<?=Fleapay::app_slug()?>_list" style="margin-bottom: 18px;"></div>
                <?php
            } else {
                echo __('<p>' . Fleapay::app_fatal_msg() . '</p>');
            }
        } else {
            echo __('<p>' . Fleapay::app_no_key_msg() . '</p>');
        }
    }

    private function _fetchProducts()
    {
        FLEAPAY_DEBUG ? error_log('Fleapay_Products_Widget::_fetchProducts...') : null;

        $expire = 60*FLEAPAY_CACHE_EXPIRE;
        $key = Fleapay::app_key();
        if (false === ($data = get_transient($key))) {
            $prod_url = '/api/products/' . $key;
            $response = Fleapay::http_post($prod_url);

            if(is_wp_error($response)) {
                $data = new stdClass();
                $_e = $response->get_error_messages();
                $data->error->msg = array_pop($_e);
            } else if(isset($response['body'])) {
                if($data = json_decode($response['body'])) {
                    set_transient($key, $data, $expire);
                }
            }

            // if debugging is on, delete cache everytime
            if(FLEAPAY_DEBUG) {
                delete_transient($key);
            }
        }

        return $data;
    }
}
?>
