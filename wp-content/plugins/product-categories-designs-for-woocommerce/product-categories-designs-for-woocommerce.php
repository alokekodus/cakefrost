<?php
/**
 * Plugin Name: Product Categories Designs for WooCommerce 
 * Plugin URI: https://www.wponlinesupport.com/plugins/
 * Description: Display WooCommerce product categories designs with grid and silder view. Also work with Gutenberg shortcode block.
 * Author: WP OnlineSupport 
 * Text Domain: product-categories-designs-for-woocommerce
 * Domain Path: /languages/
 * Version: 1.2.3
 * WC tested up to: 5.2.2
 * Author URI: http://www.wponlinesupport.com/
 *
 * @package WordPress
 * @author WP OnlineSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !defined( 'PCDFWOO_VERSION' ) ) {
	define( 'PCDFWOO_VERSION', '1.2.3' ); // Version of plugin
}
if( !defined( 'PCDFWOO_VERSION_DIR' ) ) {
	define( 'PCDFWOO_VERSION_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if( !defined( 'PCDFWOO_VERSION_URL' ) ) {
	define( 'PCDFWOO_VERSION_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if( !defined( 'PCDFWOO_PRODUCT_POST_TYPE' ) ) {
	define( 'PCDFWOO_PRODUCT_POST_TYPE', 'product' ); // Plugin category name
}
if( ! defined( 'PCDFWOO_PLUGIN_LINK' ) ) {
	define( 'PCDFWOO_PLUGIN_LINK', 'https://www.wponlinesupport.com/wp-plugin/product-categories-designs-woocommerce?utm_source=WP&utm_medium=Product_Category&utm_campaign=Features-PRO#fndtn-lifetime' ); // Plugin Category
}

/**
 * Check WooCommerce is active
 *
 * @package Product Categories Designs for WooCommerce
 * @since 1.0
 */
function pcdfwoo_check_activation() {

	if ( ! class_exists('WooCommerce') ) {
		// is this plugin active?
		if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			// deactivate the plugin
			deactivate_plugins( plugin_basename( __FILE__ ) );
			// unset activation notice
			unset( $_GET[ 'activate' ] );
			// display notice
			add_action( 'admin_notices', 'pcdfwoo_admin_notices' );
		}
	}
}

// Check required plugin is activated or not
add_action( 'admin_init', 'pcdfwoo_check_activation' );

/**
 * Admin notices
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.0
 */
function pcdfwoo_admin_notices() {
	
	if ( ! class_exists('WooCommerce') ) {
		echo '<div class="error notice is-dismissible">';
		echo sprintf( __('<p><strong>%s</strong> recommends the following plugin to use.</p>', 'product-categories-designs-for-woocommerce'), 'Woo Product Slider and Carousel with Category' );
		echo sprintf( __('<p><strong><a href="%s" target="_blank">%s</a> </strong></p>', 'product-categories-designs-for-woocommerce'), 'https://wordpress.org/plugins/woocommerce/', 'WooCommerce' );
		echo '</div>';
	}
}

/**
 * Load the plugin after the main plugin is loaded.
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.0.0
 */
function pcdfwoo_load_plugin() {

	// Check main plugin is active or not
	if( class_exists('WooCommerce') ) {

		/**
		 * Load Text Domain
		 * This gets the plugin ready for translation
		 * 
		 * @package Product Categories Designs for WooCommerce
		 * @since 1.0.0
		 */
		function pcdfwoo_load_textdomain() {

			global $wp_version;

			// Set filter for plugin's languages directory
			$wp_pcdfwoo_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$wp_pcdfwoo_lang_dir = apply_filters( 'wp_pcdfwoo_languages_directory', $wp_pcdfwoo_lang_dir );

			// Traditional WordPress plugin locale filter.
			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale',  $get_locale, 'product-categories-designs-for-woocommerce' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'product-categories-designs-for-woocommerce', $locale );

			// Setup paths to current locale file
			$mofile_global  = WP_LANG_DIR . '/plugins/' . basename( PCDFWOO_VERSION_DIR ) . '/' . $mofile;

			if ( file_exists( $mofile_global ) ) { // Look in global /wp-content/languages/plugin-name folder
				load_textdomain( 'product-categories-designs-for-woocommerce', $mofile_global );
			} else { // Load the default language files
				load_plugin_textdomain( 'product-categories-designs-for-woocommerce', false, $wp_pcdfwoo_lang_dir );
			}
		}

		// Action to load plugin text domain
		add_action('plugins_loaded', 'pcdfwoo_load_textdomain');

		// Script Class
		require_once( PCDFWOO_VERSION_DIR . '/includes/pcdfwoo-functions.php' );

		// Script Class
		require_once( PCDFWOO_VERSION_DIR . '/includes/class-pcdfwoo-script.php' );

		// Including some files
		require_once( PCDFWOO_VERSION_DIR . '/includes/shortcode/class-shortcode.php' );	
		require_once( PCDFWOO_VERSION_DIR . '/includes/shortcode/class-slider-shortcode.php' );

		//Gutenberg Block Initializer
		if ( function_exists( 'register_block_type' ) ) {
			require_once( PCDFWOO_VERSION_DIR . '/includes/admin/supports/gutenberg-block.php' );
		}

		// How it work file, Load admin files
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once( PCDFWOO_VERSION_DIR . '/includes/admin/pcdfwoo-how-it-work.php' );
		}
	}
}

// Action to load plugin after the main plugin is loaded
add_action('plugins_loaded', 'pcdfwoo_load_plugin', 15);