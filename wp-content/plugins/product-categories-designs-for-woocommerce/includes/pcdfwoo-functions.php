<?php
/**
 * Plugin generic functions file
 *
 * @package Product Categories Designs for WooCommerce
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to unique number value
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.2.5
 */
function pcdfwoo_get_unique() {
	static $unique = 0;
	$unique++;

	// For Elementor & Beaver Builder
	if( ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_POST['action'] ) && $_POST['action'] == 'elementor_ajax' )
	|| ( class_exists('FLBuilderModel') && ! empty( $_POST['fl_builder_data']['action'] ) )
	|| ( function_exists('vc_is_inline') && vc_is_inline() ) ) {
		$unique = current_time('timestamp') . '-' . rand();
	}

	return $unique;
}

/**
 * Function to get featured content column
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.0.0
 */
function pcdfwoo_column( $row = '' ) {
	if($row == 2) {
		$per_row = 6;
	} else if($row == 3) {
		$per_row = 4;	
	} else if($row == 4) {
		$per_row = 3;
	} else if($row == 1) {
		$per_row = 12;
	} else{
		$per_row = 12;
	}

	return $per_row;
}
/**
 * Sanitize number value and return fallback value if it is blank
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.1
 */
function pcdfwoo_clean_number( $var, $fallback = null, $type = 'int' ) {

	if ( $type == 'number' ) {
		$data = intval( $var );
	} else {
		$data = absint( $var );
	}

	return ( empty($data) && isset($fallback) ) ? $fallback : $data;
}

/**
 * Sanitize Multiple HTML class
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.2.6
 */
function pcdfwoo_sanitize_html_classes($classes, $sep = " ") {
	$return = "";

	if( !is_array($classes) ) {
		$classes = explode($sep, $classes);
	}

	if( ! empty( $classes ) ) {
		foreach($classes as $class){
			$return .= sanitize_html_class($class) . " ";
		}
		$return = trim( $return );
	}

	return $return;
}