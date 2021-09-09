<?php
namespace WooLentorBlocks;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load general WP action hook
 */
class Scripts {

	/**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Filter]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * The Constructor.
	 */
	public function __construct() {
		add_action( 'enqueue_block_assets', [ $this, 'block_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_assets' ] );
	}

	/**
	 * Block assets.
	 */
	public function block_assets() {
		$dependencies = require_once( WOOLENTOR_BLOCK_PATH . '/build/index.asset.php' );

		wp_enqueue_script(
		    'woolentor-block',
		    WOOLENTOR_BLOCK_URL . '/build/index.js',
		    $dependencies['dependencies'],
		    $dependencies['version'],
		    true
		);

		wp_enqueue_script(
		    'woolentor-block-slic-activate',
		    WOOLENTOR_BLOCK_URL . '/src/assets/js/script.js',
		    $dependencies['dependencies'],
		    $dependencies['version'],
		    true
		);

		wp_enqueue_style(
		    'woolentor-block-style',
		    WOOLENTOR_BLOCK_URL . '/src/assets/css/style-index.css',
		    false,
		    time(),
		    'all'
		);

	}
	/**
	 * Block editor assets.
	 */
	public function block_editor_assets() {

		$styles  =  class_exists('\WooLentor\Assets_Management') ? \WooLentor\Assets_Management::instance()->get_styles() : array();
		$scripts  = class_exists('\WooLentor\Assets_Management') ? \WooLentor\Assets_Management::instance()->get_scripts() : array();

		// Register Styles
        foreach ( $styles as $handle => $style ) {
            $deps = ( isset( $style['deps'] ) ? $style['deps'] : false );
            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }

		// Register Scripts
        foreach ( $scripts as $handle => $script ) {
            $deps = ( isset( $script['deps'] ) ? $script['deps'] : false );
            wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        }

		wp_enqueue_style( 'woolentor-block-editor-style', WOOLENTOR_BLOCK_URL . '/src/assets/css/editor-style.css', false, time(), 'all' );

	}

	
}
