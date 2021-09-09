<?php
/**
 * This file handles wpmember form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce-product-vendors/woocommerce-product-vendors.php' ) ) {
	return;}

/**
 * VendorRegistrationForm class.
 */
class VendorRegistrationForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::PV_DEFAULT_REG;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_form_id = "input[name^='billing_phone']";

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_action( 'wcpv_shortcode_registration_form_process', array( $this, 'smsalert_pv_registration_complete' ), 10, 2 );
		add_action( 'wcpv_registration_form', array( $this, 'vendors_reg_custom_fields' ) );
	}

	/**
	 * Add Phone field to vendor registration form.
	 *
	 * @return void
	 */
	public static function vendors_reg_custom_fields() {
		echo '<p class="form-row form-row-wide">
			  <label for="wcpv-vendor-billing-phone">Phone <span class="required">*</span></label>
			  <input class="input-text" type="text" name="billing_phone" id="wcpv-billing-phone" value="" tabindex="6">
			  </p>';
		echo do_shortcode( '[sa_verify id="form1" phone_selector="#wcpv-billing-phone" submit_selector= "register" ]' );
	}

	/**
	 * Update vendor phone number after registration.
	 *
	 * @param array $args arguments.
	 * @param array $items users details.
	 *
	 * @return void
	 */
	public function smsalert_pv_registration_complete( $args, $items ) {
		$data = get_user_by( 'login', $items['username'] );
		if ( isset( $items['billing_phone'] ) ) {
			add_user_meta( $data->ID, 'billing_phone', $items['billing_phone'], true );
		}
		do_action( 'smsalert_after_update_new_user_phone', $data->ID, $items['billing_phone'] );
	}

	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		return ( smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' ) ? true : false;
	}

	/**
	 * Handle after failed verification
	 *
	 * @param  object $user_login users object.
	 * @param  string $user_email user email.
	 * @param  string $phone_number phone number.
	 *
	 * @return void
	 */
	public function handle_failed_verification( $user_login, $user_email, $phone_number ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return;
		}
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'verification_failed';
		}
	}

	/**
	 * Handle after post verification
	 *
	 * @param  string $redirect_to redirect url.
	 * @param  object $user_login user object.
	 * @param  string $user_email user email.
	 * @param  string $password user password.
	 * @param  string $phone_number phone number.
	 * @param  string $extra_data extra hidden fields.
	 *
	 * @return void
	 */
	public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return;
		}
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'validated';
		}
	}

	/**
	 * Clear otp session variable
	 *
	 * @return void
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->tx_session_id ] );
		unset( $_SESSION[ $this->form_session_var ] );
	}

	/**
	 * Check current form submission is ajax or not
	 *
	 * @param bool $is_ajax bool value for form type.
	 *
	 * @return bool
	 */
	public function is_ajax_form_in_play( $is_ajax ) {
		SmsAlertUtility::checkSession();
		return isset( $_SESSION[ $this->form_session_var ] ) ? true : $is_ajax;
	}

	/**
	 * Get Phone Number Selector.
	 *
	 * @param string $selector phone field name.
	 *
	 * @return array
	 */
	public function getPhoneNumberSelector( $selector ) {
		SmsAlertUtility::checkSession();
		if ( self::isFormEnabled() ) {
			array_push( $selector, $this->phone_form_id );
		}
		return $selector;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new VendorRegistrationForm();

