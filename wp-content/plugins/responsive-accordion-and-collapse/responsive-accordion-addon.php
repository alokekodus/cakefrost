<?php

if (!defined('ABSPATH'))
{
    exit; // Exit if accessed directly.
    
}

final class Responsive_Accordion_Addon
{
    
    const RESPONSIVE_ACCORDION_ADDON_VERSION = '1.0.0';

    
    const RESPONSIVE_ACCORDION_ADDON_MINIMUM_ELEMENTOR_VERSION = '2.0.0';

   
    const RESPONSIVE_ACCORDION_ADDON_MINIMUM_PHP_VERSION = '7.0';

    
    private static $instance = null;

    
    public static function instance()
    {

        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;

    }

    
    public function __construct()
    {

        add_action('plugins_loaded', [$this, 'wpsm_accordion_on_plugins_loaded']);

    }

   
    public function elementor_textdomain()
    {

        load_plugin_textdomain('wpshopmart_accordion_text_domain');

    }

   
    public function wpsm_accordion_on_plugins_loaded()
    {       
        add_action('elementor/init', [$this, 'wpsm_accordion_init']);
		add_action( 'elementor/elements/categories_registered',array($this, 'add_elementor_widget_categories' ));
		add_action('elementor/editor/before_enqueue_scripts', array($this, 'editor_enqueue_scripts'));

    } 	
	
	public function editor_enqueue_scripts()
    {
        
        // editor style
        wp_enqueue_style(
            'responsive-accordion-editor',
            wpshopmart_accordion_directory_url . 'responsive-accordion-elementor-addon/assets/css/editor.css',
            false
        );
    }
	
	public function add_elementor_widget_categories( $elements_manager ) {

	$elements_manager->add_category(
		'wpshopmart',
		[
			'title' => __( 'WPShopmart', 'Responsive Accordion'),
			'icon' => 'font',
		]
	);	

	}

    
    public function wpsm_accordion_init()
    {

        $this->elementor_textdomain();

        // Add Plugin actions
        add_action('elementor/widgets/widgets_registered', [$this, 'wpsm_accordion_init_widgets']);

    }

    
    public function wpsm_accordion_init_widgets()
    {

        // Include Widget files
        require_once (__DIR__ . '/responsive-accordion-elementor-addon/widgets/responsive-accordion-addon-widget.php');

        // Register widget
        \Elementor\Plugin::instance()
            ->widgets_manager
            ->register_widget_type(new \Responsive_Accordion_Addon_Widget());

    }    

}

Responsive_Accordion_Addon::instance();

