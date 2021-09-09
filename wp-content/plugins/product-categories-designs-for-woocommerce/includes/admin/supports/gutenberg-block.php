<?php
/**
 * Blocks Initializer
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function pcdfwoo_register_guten_block() {

	wp_register_script( 'pcdfwoo-block-js', PCDFWOO_VERSION_URL.'assets/js/blocks.build.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components' ), PCDFWOO_VERSION, true );
	wp_localize_script( 'pcdfwoo-block-js', 'PcdfWoo_Block', array(
															'pro_demo_link' 	=> 'https://demo.wponlinesupport.com/prodemo/product-categories-designs-for-woo-pro/',
															'free_demo_link' 	=> 'https://demo.wponlinesupport.com/product-categories-designs-for-woocommerce-demo/',
															'pro_link' 			=> PCDFWOO_PLUGIN_LINK,
														));

	// Register block and explicit attributes for grid
	register_block_type( 'pcdfwoo/product-grid', array(
		'attributes' => array(
			'design' => array(
							'type'		=> 'string',
							'default'	=> 'design-1',
						),
			'columns' => array(
							'type'		=> 'number',
							'default'	=> 3,
						),
			'height' => array(
							'type'		=> 'number',
							'default'	=> 300,
						),
			'number' => array(
							'type'		=> 'number',
							'default'	=> 0,
						),
			'orderby' => array(
							'type'		=> 'string',
							'default'	=> 'name',
						),
			'order' => array(
							'type'		=> 'string',
							'default'	=> 'asc',
						),
			'ids' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'parent' => array(
							'type'		=> 'string',
							'default'	=> 0,
						),
			'hide_empty' => array(
							'type'		=> 'string',
							'default'	=> '1',
						),
			'align' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'className' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
		),
		'render_callback' => 'pcdfwoo_product_categories',
	));


	// Register block, and explicitly define the attributes for slider
	register_block_type( 'pcdfwoo/product-slider', array(
		'attributes' => array(
			'design' => array(
							'type'		=> 'string',
							'default'	=> 'design-1',
						),
			'height' => array(
							'type'		=> 'number',
							'default'	=> 300,
						),
			'slidestoshow' => array(
							'type'		=> 'number',
							'default'	=> 3,
						),
			'slidestoscroll' => array(
							'type'		=> 'number',
							'default'	=> 1,
						),
			'dots' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'arrows' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'autoplay' => array(
							'type'		=> 'string',
							'default'	=> 'false',
						),
			'autoplay_interval' => array(
							'type'		=> 'number',
							'default'	=> 3000,
						),
			'speed' => array(
							'type'		=> 'number',
							'default'	=> 300,
						),
			'loop' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'number' => array(
							'type'		=> 'number',
							'default'	=> 0,
						),
			'orderby' => array(
							'type'		=> 'string',
							'default'	=> 'name',
						),
			'order' => array(
							'type'		=> 'string',
							'default'	=> 'asc',
						),
			'ids' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'parent' => array(
							'type'		=> 'string',
							'default'	=> 0,
						),
			'hide_empty' => array(
							'type'		=> 'string',
							'default'	=> '1',
						),
			'align' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'className' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
		),
		'render_callback' => 'pcdfwoo_product_categories_slider',
	));

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'pcdfwoo-block-js', 'product-categories-designs-for-woocommerce', PCDFWOO_VERSION_DIR . '/languages' );
	}

}
add_action( 'init', 'pcdfwoo_register_guten_block' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.0
 */
function pcdfwoo_editor_assets() {	

	// Block Editor CSS
	if( ! wp_style_is( 'wpos-guten-block-css', 'registered' ) ) {
		wp_register_style( 'wpos-guten-block-css', PCDFWOO_VERSION_URL.'assets/css/blocks.editor.build.css', array( 'wp-edit-blocks' ), PCDFWOO_VERSION );
	}

	// Block Editor Script
	wp_enqueue_style( 'wpos-guten-block-css' );
	wp_enqueue_script( 'pcdfwoo-block-js' );
}
add_action( 'enqueue_block_editor_assets', 'pcdfwoo_editor_assets' );

/**
 * Adds an extra category to the block inserter
 *
 * @package Product Categories Designs for WooCommerce
 * @since 1.0
 */
function pcdfwoo_add_block_category( $categories ) {

	$guten_cats = wp_list_pluck( $categories, 'slug' );

	if( ! in_array( 'wpos_guten_block', $guten_cats ) ) {
		$categories[] = array(
							'slug'	=> 'wpos_guten_block',
							'title'	=> __('WPOS Blocks', 'product-categories-designs-for-woocommerce'),
							'icon'	=> null,
						);
	}

	return $categories;
}
add_filter( 'block_categories', 'pcdfwoo_add_block_category' );