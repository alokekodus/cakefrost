<?php

// Custom CSS
function astra__child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_stylesheet_uri());
}

// Function to allow log out without confirmation
function logout_confirmation() {
    global $wp;
    if ( isset( $wp->query_vars['customer-logout'] ) ) {
        wp_redirect( str_replace( '&amp;', '&', wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ) );
        exit;
    }

}

// Function to change My acoount to Login when user is not logged in
function bbloomer_dynamic_menu_item_label( $items, $args ) { 
    if ( ! is_user_logged_in() ) { 
        $items = str_replace( "My account", "Login/Register", $items ); 
    } 
    return $items; 
} 

// Function to change Login label
function wppb_change_text_login( $translated_text, $text, $domain ) {
    // Only on my account registering form
    if ( ! is_user_logged_in() && is_account_page() ) {
        $original_text = 'Username or email address';

        if ( $text === $original_text )
            $translated_text = esc_html__('Your registered email address', $domain );
    }
    return $translated_text;
}


// Custom validation for Billing Phone checkout field
add_action('woocommerce_checkout_process', 'custom_validate_billing_phone');
function custom_validate_billing_phone() {
	global $woocommerce;
// 	if(!(preg_match('/^[+]?[1-9]{2}[0-9]{10}$/D', $_POST['billing_phone']))){
// 		wc_add_notice("Incorrect phone number! Please enter valid 10 digits phone number", 'error');
//     }

	if(!(preg_match('/^[0-9]{10}$/D', $_POST['billing_phone']))){
		wc_add_notice("Incorrect phone number! Please enter valid 10 digits phone number", 'error');
    }
}

// Remove Email required field in woocommerce checkout
function custom_override_checkout_fields( $fields ) {
   unset($fields['billing']['billing_email']);    
   return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields', 1000, 1 );


// Actions
add_action( 'wp_enqueue_scripts', 'astra__child_theme_enqueue_styles' ); // Child theme CSS
add_action( 'template_redirect', 'logout_confirmation' ); // Logout confirmation disabled
add_filter( 'wp_nav_menu_items', 'bbloomer_dynamic_menu_item_label', 9999, 2 ); // Change My account to Login
add_filter( 'gettext', 'wppb_change_text_login', 10, 3 ); // Change login label

?>