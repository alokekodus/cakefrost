<?php
/**
 * This file handles pie registration form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'pie-register/pie-register.php' ) ) {
	return; }

/**
 * PieRegistrationForm class.
 */
class PieRegistrationForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::PIE_REG;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_field_key;

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		$this->phone_field_key = 'billing_phone';
		add_action( 'pie_register_after_register_validate', array( $this, 'smsalert_pie_user_registration' ), 99, 0 );
		add_filter( 'piereg_edit_above_form_data', array( $this, 'add_short_code_user_verification' ), 99, 1 );
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
	 * Add shortcode for OTP features to pie form.
	 *
	 * @param int $form_id form id.
	 *
	 * @return string
	 */
	public function add_short_code_user_verification( $form_id ) {
		return do_shortcode( "[sa_verify phone_selector='.phone-valid' submit_selector='.pie_register_reg_form .pie_submit']" );
	}

	/**
	 * Handle after validation of registration form.
	 *
	 * @return void
	 */
	public function smsalert_pie_user_registration() {     }

	/**
	 * Handle after failed verification.
	 *
	 * @param  object $user_login users object.
	 * @param  string $user_email user email.
	 * @param  string $phone_number phone number.
	 *
	 * @return void
	 */
	public function handle_failed_verification( $user_login, $user_email, $phone_number ) {
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
		return true;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new PieRegistrationForm();
