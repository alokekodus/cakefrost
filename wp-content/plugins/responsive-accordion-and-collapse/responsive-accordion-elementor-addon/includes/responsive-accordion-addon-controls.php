<?php

$this->start_controls_section(
	'accordion_select_design_content',
	[
		'label' => __( 'Design', 'responsive-accordion-addon' ),
		'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
	]
);

$this->add_control(
	'accordion_select_design',
	[
		'label' => __( 'Select Design', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SELECT,
		'default' => '1',
		'options' => [
			'1'  => __( 'Design-1', 'wpshopmart_accordion_text_domain' ),			
			
		],
	]
);	

$this->end_controls_section();

$this->start_controls_section(
	'accordion_content_section',
	[
		'label' => __( 'Content', 'responsive-accordion-addon' ),
		'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
	]
);

$repeater = new \Elementor\Repeater();

$repeater->add_control(
	'accordion_title', [
		'label' => __( 'Accordion Title', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::TEXT,
		'default' => __( 'Accordion Sample Title' , 'wpshopmart_accordion_text_domain' ),
		'label_block' => true,
	]
);

$repeater->add_control(
	'accordion_description',
	[
		'label' => __( 'Accordion Description', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::WYSIWYG,
		'default' => __( 'Accordion Sample Description', 'wpshopmart_accordion_text_domain' ),
		'placeholder' => __( 'Type your description here', 'wpshopmart_accordion_text_domain' ),
	]
);

$repeater->add_control(
	'accordion_icon',
	[
		'label' => __( 'Accordion Icon', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::ICONS,
		'default' => [
			'value' => 'fa fa-laptop',
			'library' => 'solid',
		],
	]
);

$repeater->add_control(
	'accordion_display_icon',
	[
		'label' => __( 'Display Above Icon', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		'label_off' => __( 'No', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'yes',
	]
);

$this->add_control(
	'accordion_list',
	[
		'label' => __( 'All Accordion', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::REPEATER,
		'fields' => $repeater->get_controls(),
		'default' => [
			[
				'accordion_title' => __( 'Accordion Sample Title', 'wpshopmart_accordion_text_domain' ),
				'accordion_description' => __( 'Accordion Sample Description', 'wpshopmart_accordion_text_domain' ),
				'accordion_icon' => __( ' fa fa-laptop ', 'wpshopmart_accordion_text_domain' ),
				'accordion_display_icon' => __( 'yes', 'wpshopmart_accordion_text_domain' ),
				
			],
			[
				'accordion_title' => __( 'Accordion Sample Title', 'wpshopmart_accordion_text_domain' ),
				'accordion_description' => __( 'Accordion Sample Description', 'wpshopmart_accordion_text_domain' ),
				'accordion_icon' => __( ' fa fa-laptop ', 'wpshopmart_accordion_text_domain' ),
				'accordion_display_icon' => __( 'yes', 'wpshopmart_accordion_text_domain' ),
			],
			[
				'accordion_title' => __( 'Accordion Sample Title', 'wpshopmart_accordion_text_domain' ),
				'accordion_description' => __( 'Accordion Sample Description', 'wpshopmart_accordion_text_domain' ),
				'accordion_icon' => __( ' fa fa-laptop ', 'wpshopmart_accordion_text_domain' ),
				'accordion_display_icon' => __( 'yes', 'wpshopmart_accordion_text_domain' ),
			],
			
		],
		'title_field' => '{{{ accordion_title }}}',
	]
);

$this->end_controls_section();

$this->start_controls_section(
	'accordion_custom_css_section',
	[
		'label' => __( 'Code', 'responsive-accordion-addon' ),
		'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
	]
);

$this->add_control(
	'accordion_custom_css',
	[
		'label' => __( 'Custom Css', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::CODE,
		'description' =>'Enter Css without using style tag',
		'language' => 'css',
		'rows' => 20,
	]
);	

$this->end_controls_section();

$this->start_controls_section(
	'accordion_style_section',
	[
		'label' => __( 'Style', 'responsive-accordion-addon' ),
		'tab' => \Elementor\Controls_Manager::TAB_STYLE,
	]
);

// $this->add_control(
	// 'accordion_section_title',
	// [
		// 'label' => __( 'Display Accordion Section Title', 'wpshopmart_accordion_text_domain' ),
		// 'type' => \Elementor\Controls_Manager::SWITCHER,
		// 'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		// 'label_off' => __( 'No', 'responsive-accordion-addon' ),
		// 'return_value' => 'yes',
		// 'default' => 'yes',
	// ]
// );

$this->add_control(
	'accordion_open_close_icon',
	[
		'label' => __( 'Accordion Open/Close Icon Alignment', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Left', 'responsive-accordion-addon' ),
		'label_off' => __( 'Right', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'no',
	]
);

$this->add_control(
	'accordion_open_close_icon_show',
	[
		'label' => __( 'Display Open Close Icon', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		'label_off' => __( 'No', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'yes',
	]
);

$this->add_control(
	'accordion_tile_font_icon_show',
	[
		'label' => __( 'Display Accordion Title Font Icon', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		'label_off' => __( 'No', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'yes',
	]
);

$this->add_control(
	'accordion_enable_radius',
	[
		'label' => __( 'Enable Accordion Radius', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		'label_off' => __( 'No', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'yes',
	]
);

$this->add_control(
	'accordion_margin_space',
	[
		'label' => __( 'Enable Accordion Margin/Space', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		'label_off' => __( 'No', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'yes',
	]
);

$this->add_control(
	'accordion_toggle_collapse',
	[
		'label' => __( 'Enable Toggle/Collapse', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		'label_off' => __( 'No', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'no',
	]
);

$this->add_control(
	'accordion_border_show',
	[
		'label' => __( 'Display Accordion Border', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __( 'Yes', 'responsive-accordion-addon' ),
		'label_off' => __( 'No', 'responsive-accordion-addon' ),
		'return_value' => 'yes',
		'default' => 'yes',
	]
);

$this->add_control(
	'accordion_divider_1',
	[
		'type' => \Elementor\Controls_Manager::DIVIDER,
	]
);

$this->add_control(
	'accordion_expand_collapse',
	[
		'label' => __( 'Expand/Collapse Accordion Option On Page Load', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::CHOOSE,
		'options' => [
			'1' => [
				'title' => __( 'First Accordion Open', 'wpshopmart_accordion_text_domain' ),
				
			],
			'2' => [
				'title' => __( 'Open All Accordion', 'wpshopmart_accordion_text_domain' ),
				
			],
			'3' => [
				'title' => __( 'Hide/close All Accordion', 'wpshopmart_accordion_text_domain' ),
				
			],
		],
		'default' => '1',
		// 'toggle' => true,
	]
);

$this->add_control(
	'accordion_style',
	[
		'label' => __( 'Accordion Styles', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::CHOOSE,
		'options' => [
			'1' => [
				'title' => __( 'Simple', 'wpshopmart_accordion_text_domain' ),
				
			],
			'2' => [
				'title' => __( 'Soft', 'wpshopmart_accordion_text_domain' ),
				
			],
			'3' => [
				'title' => __( 'Noise', 'wpshopmart_accordion_text_domain' ),
				
			],
		],
		'default' => '1',
		// 'toggle' => true,
	]
);

$this->add_control(
	'accordion_divider_2',
	[
		'type' => \Elementor\Controls_Manager::DIVIDER,
	]
);

$this->add_control(
	'accordion_title_back_color',
	[
		'label' => __( 'Accordion Title Background Color', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'scheme' => [
			'type' => \Elementor\Scheme_Color::get_type(),
			'value' => \Elementor\Scheme_Color::COLOR_1,
		],
		'default' => '#e8e8e8',
		'selectors' => [
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .acc-a' => 'background-color: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-default > .wpsm_panel-heading' => 'background-color: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-default > .wpsm-heading-class' => 'border-color: {{VALUE}}',			
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-body' => 'border: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .ac_open_cl_icon' => 'background-color: {{VALUE}}',
			
		],
	]
);

$this->add_control(
	'accordion_title_icon_color',
	[
		'label' => __( 'Accordion Title/Icon Font Color', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'scheme' => [
			'type' => \Elementor\Scheme_Color::get_type(),
			'value' => \Elementor\Scheme_Color::COLOR_1,
		],
		'default' => '#000000',
		'selectors' => [
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-title a' => 'color: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-title a:hover' => 'color: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-title a:focus' => 'color: {{VALUE}}',			
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-default > .wpsm_panel-heading' => 'color: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .ac_open_cl_icon' => 'color: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .acc-a' => 'color: {{VALUE}}',
			
		],
	]
);


$this->add_control(
	'accordion_desc_back_color',
	[
		'label' => __( 'Accordion Description Background Color', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'scheme' => [
			'type' => \Elementor\Scheme_Color::get_type(),
			'value' => \Elementor\Scheme_Color::COLOR_1,
		],
		'default' => '#ffffff',
		'selectors' => [
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-body' => 'background-color: {{VALUE}}',
			
		],
	]
);

$this->add_control(
	'accordion_desc_font_color',
	[
		'label' => __( 'Accordion Description Font Color', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'scheme' => [
			'type' => \Elementor\Scheme_Color::get_type(),
			'value' => \Elementor\Scheme_Color::COLOR_1,
		],
		'default' => '#000000',
		'selectors' => [
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-body' => 'color: {{VALUE}}',
			
		],
	]
);

$this->add_control(
	'accordion_divider_3',
	[
		'type' => \Elementor\Controls_Manager::DIVIDER,
	]
);

$this->add_control(
	'accordion_title_icon_font_size',
	[
		'label' => __( 'Title/Icon Font Size', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SLIDER,
		'size_units' => [ 'px'],
		'range' => [
			'px' => [
				'min' => 5,
				'max' => 30,
				'step' => 1,
			],
			
		],
		'default' => [
			'unit' => 'px',
			'size' => 18,
		],
		'selectors' => [
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-title a' => 'font-size: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().'  .wpsm_panel-body' => 'font-size: {{SIZE}}{{UNIT}};',			
								
		],
	]
);


$this->add_control(
	'accordion_desc_font_size',
	[
		'label' => __( 'Description Font Size', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::SLIDER,
		'size_units' => [ 'px'],
		'range' => [
			'px' => [
				'min' => 5,
				'max' => 30,
				'step' => 1,
			],
			
		],
		'default' => [
			'unit' => 'px',
			'size' => 16,
		],
		'selectors' => [
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().'  .wpsm_panel-body' => 'font-size: {{SIZE}}{{UNIT}};',				
								
		],
	]
);

$this->add_control(
	'accordion_divider_4',
	[
		'type' => \Elementor\Controls_Manager::DIVIDER,
	]
);

$this->add_control(
	'accordion_font_family',
	[
		'label' => __( 'Font Style/Family', 'wpshopmart_accordion_text_domain' ),
		'type' => \Elementor\Controls_Manager::FONT,
		'default' => "Open Sans",
		'selectors' => [
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().' .wpsm_panel-title a' => 'font-family: {{VALUE}}',
			'{{WRAPPER}} #wpsm_accordion_'.get_the_ID().'  .wpsm_panel-body' => 'font-family: {{VALUE}}',
			
			
		],
	]
);


$this->end_controls_section();


?>