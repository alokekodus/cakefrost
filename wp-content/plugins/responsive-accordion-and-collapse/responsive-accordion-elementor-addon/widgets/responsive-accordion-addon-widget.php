<?php

class Responsive_Accordion_Addon_Widget extends \Elementor\Widget_Base {
	
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( "wpsm_accordion_bootstrap-front",wpshopmart_accordion_directory_url."responsive-accordion-elementor-addon/assets/css/accordion-bootstrap-front.css", array(), false, "all" );
		wp_enqueue_script('jquery');
		wp_register_script('wpsm_accordion_bootstrap_js',wpshopmart_accordion_directory_url ."responsive-accordion-elementor-addon/assets/js/accordion-bootstrap.js",array ('jquery'), false, false);
		wp_register_script('wpsm_accordion_front_accordion_js',wpshopmart_accordion_directory_url ."responsive-accordion-elementor-addon/assets/js/accordion-front-accordion.js",array ('jquery'), false, false);
		
		
	}
	
	public function get_style_depends() {
		return array( 'wpsm_accordion_bootstrap-front' );
	}
	
	public function get_script_depends() {
		return array( 'wpsm_accordion_front_accordion_js','wpsm_accordion_bootstrap_js' );
	}

	
	public function get_name() {
		return 'Responsive Accordion';
	}

	
	public function get_title() {
		return __( 'Responsive Accordion', 'responsive-accordion-addon' );
	}

	
	public function get_icon() {
		return 'eicon-slider-vertical';
	}
	
	
	public function get_categories() {
		return [ 'wpshopmart' ];
	}

	
	protected function _register_controls() {

		require_once( wpshopmart_accordion_directory_path .'responsive-accordion-elementor-addon/includes/responsive-accordion-addon-controls.php' );

	}

	
	protected function render() {

		$settings = $this->get_settings_for_display();
		require_once( __DIR__ . '/render-template/design-'.$settings['accordion_select_design'].'.php' );

	}
	
	protected function _content_template()
	{
		require_once( wpshopmart_accordion_directory_path .'responsive-accordion-elementor-addon/includes/responsive-accordion-addon-content.php' );
	}

}