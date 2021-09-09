<?php
/**
* Plugin Name: Check Pincode/Zipcode for Shipping Woocommerce
* Description: Check shipping is avaible or not in woocommerce
* Version: 1.0
* Copyright: 2019
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
  die('-1');
}

if (!defined('WCZP_PLUGIN_NAME')) {
  define('WCZP_PLUGIN_NAME', 'Check Pincode/Zipcode for Shipping Woocommerce');
}
if (!defined('WCZP_PLUGIN_VERSION')) {
  define('WCZP_PLUGIN_VERSION', '1.0');
}
if (!defined('WCZP_PLUGIN_FILE')) {
  define('WCZP_PLUGIN_FILE', __FILE__);
}
if (!defined('WCZP_PLUGIN_DIR')) {
  define('WCZP_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('WCZP_BASE_NAME')) {
    define('WCZP_BASE_NAME', plugin_basename(WCZP_PLUGIN_FILE));
}
if (!defined('WCZP_DOMAIN')) {
  define('WCZP_DOMAIN', 'wczp');
}


if (!class_exists('WCZP')) {

    class WCZP {

        protected static $WCZP_instance;
        function __construct() {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            add_action('admin_init', array($this, 'WCZP_check_plugin_state'));
        }


        function WCZP_check_plugin_state(){
            if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
                set_transient( get_current_user_id() . 'wqrerror', 'message' );
            }
        }


        function init() {
            add_action( 'admin_notices', array($this, 'WCZP_show_notice'));
            add_action( 'admin_enqueue_scripts', array($this, 'WCZP_load_admin'));
            add_action( 'wp_enqueue_scripts',  array($this, 'WCZP_load_front'));
            add_filter( 'plugin_row_meta', array( $this, 'WCZP_plugin_row_meta' ), 10, 2 );
        }


        function WCZP_show_notice() {
            if ( get_transient( get_current_user_id() . 'wqrerror' ) ) {

                deactivate_plugins( plugin_basename( __FILE__ ) );
                delete_transient( get_current_user_id() . 'wqrerror' );
                echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=woocommerce">WooCommerce</a> plugin installed and activated.</p></div>';
            }
        }


       	
        function WCZP_plugin_row_meta( $links, $file ) {
            if ( WCZP_BASE_NAME === $file ) {
                $row_meta = array(
                    'rating'    =>  '<a href="https://www.xeeshop.com/support-us/?utm_source=aj_plugin&utm_medium=plugin_support&utm_campaign=aj_support&utm_content=aj_wordpress" target="_blank">Support</a> |<a href="https://wordpress.org/support/plugin/check-pincodezipcode-for-shipping-woocommerce/reviews/?filter=5#new-post" target="_blank"><img src="'.WCZP_PLUGIN_DIR.'/includes/images/star.png" class="wczp_rating_div"></a>',
                );
                return array_merge( $links, $row_meta );
            }
            return (array) $links;
        }


        function WCZP_load_admin() {
            wp_enqueue_style( 'WCZP_admin_style', WCZP_PLUGIN_DIR . '/includes/css/wczp_back_style.css', false, '1.0.0' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker-alpha', WCZP_PLUGIN_DIR . '/includes/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), '1.0.0', true );
        }


        function WCZP_load_front() {
            wp_enqueue_style( 'WCZP_front_style', WCZP_PLUGIN_DIR . '/includes/css/wczp_front_style.css', false, '1.0.0' );
            wp_enqueue_script( 'WCZP_front_script', WCZP_PLUGIN_DIR . '/includes/js/wczp_front_script.js', false, '1.0.0' );
            wp_localize_script( 'WCZP_front_script', 'wczp_ajax_postajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
            $translation_array = WCZP_PLUGIN_DIR;
            wp_localize_script( 'WCZP_front_script', 'wczp_plugin_url', $translation_array );
            $not_serviceable_text = get_option('wczp_not_serviceable_txt', 'Oops! We are not currently servicing your area.');
            wp_localize_script( 'WCZP_front_script', 'wczp_not_srvcbl_txt', $not_serviceable_text );
        }


        function WCZP_create_table() {
            global $table_prefix, $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $tablename = $table_prefix.'wczp_postcode';

            $sql = "CREATE TABLE $tablename (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                wczp_pincode TEXT NOT NULL,
                wczp_city TEXT NOT NULL,
                wczp_state TEXT NOT NULL,
                wczp_ddate TEXT NOT NULL,
                wczp_ship_amount TEXT NOT NULL,
                wczp_cod TEXT NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

        
        function includes() {
            include_once('admin/wczp_admin.php');
            include_once('front/wczp_front.php');
        }


        public static function WCZP_instance() {
            if (!isset(self::$WCZP_instance)) {
                self::$WCZP_instance = new self();
                self::$WCZP_instance->init();
                self::$WCZP_instance->includes();
            }
            return self::$WCZP_instance;
        }

    }

    register_activation_hook( WCZP_PLUGIN_FILE, array('WCZP', 'WCZP_create_table' ));
    add_action('plugins_loaded', array('WCZP', 'WCZP_instance'));
}