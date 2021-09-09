<?php
/**
 * This file handles user registration sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'user-registration/user-registration.php' ) ) {
	return; }

/**
 * WpMemberForm class.
 */
class UserRegistrationForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::UR_DEFAULT_REG;

	/**
	 * Phone Form id.
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
		add_action( 'user_registration_before_register_user_action', array( $this, 'smsalert_ur_registration_validation' ), 9, 3 );

		add_action( 'user_registration_after_register_user_action', array( $this, 'smsalert_ur_registration_complete' ), 9, 3 );

		add_action( 'user_registration_after_form_fields', array( $this, 'my_predefined_fields' ), 9, 3 );

		add_action( 'user_registration_after_form_buttons', array( $this, 'sa_ur_handle_js_script' ) );
		$this->routeData();
	}

	/**
	 * Add additional js script.
	 *
	 * @return void
	 */
	public function sa_ur_handle_js_script() {
		if ( smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' ) {
			$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );

			echo '<style>.sa-ur-hide{display:none !important}</style><script>
			jQuery(".ur-submit-button").addClass("sa-ur-hide");
			jQuery(".user-registration .sa-otp-btn-init").unbind("click").bind("click", function() {
				jQuery(this).parents(".smsalertModal").hide();
				var e = jQuery(this).parents("form").find("#billing_phone").val();
				var data = {user_phone:e};
				var action_url = "' . site_url() . '/?option=smsalert-ur-ajax-verify";
				saInitOTPProcess(this,action_url, data,' . $otp_resend_timer . ');
			});

			jQuery(".user-registration .smsalert_otp_validate_submit").unbind("click").bind("click", function() {
				var current_form = jQuery(this).parents("form");
				var action_url = "' . site_url() . '/?option=smsalert-validate-otp-form";
				var data = current_form.serialize()+"&otp_type=phone&from_both=";
				sa_validateOTP(this,action_url,data,function(){
				 current_form.find(".sa-ur-hide						").trigger("click")
				});
				return false;
			});
			</script>';
			$this->enqueue_otp_js_script();

		}
	}

	/**
	 * Add Js code to script.
	 *
	 * @return void
	 */
	public function enqueue_otp_js_script() {
		wp_register_script( 'smsalert-auth', SA_MOV_URL . 'js/otp-sms.min.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, true );
		wp_localize_script(
			'smsalert-auth',
			'sa_notices',
			array(
				'waiting_txt' => __( 'Please wait...', 'sms-alert' ),
			)
		);
		wp_enqueue_script( 'smsalert-auth' );
	}

	/**
	 * Handle post data via ajax submit
	 *
	 * @return void
	 */
	public function routeData() {
		if ( ! array_key_exists( 'option', $_GET ) ) {
			return;
		}
		switch ( trim( sanitize_text_field( wp_unslash( $_GET['option'] ) ) ) ) {
			case 'smsalert-ur-ajax-verify':
				$this->send_otp_ur_ajax_verify( smsalert_sanitize_array( $_POST ) );
				exit();
			break;
		}
	}

	/**
	 * Initialise otp process.
	 *
	 * @param array $getdata posted data by user.
	 *
	 * @return void
	 */
	public function send_otp_ur_ajax_verify( $getdata ) {
		SmsAlertUtility::checkSession();
		SmsAlertUtility::initialize_transaction( $this->form_session_var );

		if ( array_key_exists( 'user_phone', $getdata ) && ! SmsAlertUtility::isBlank( $getdata['user_phone'] ) ) {
			$_SESSION[ $this->form_session_var ] = trim( $getdata['user_phone'] );
			$message                             = str_replace( '##phone##', $getdata['user_phone'], SmsAlertMessages::showMessage( 'OTP_SENT_PHONE' ) );
			smsalert_site_challenge_otp( 'test', null, null, trim( $getdata['user_phone'] ), 'phone', null, null, true );
		} else {
			wp_send_json( SmsAlertUtility::_create_json_response( 'Enter a number in the following format : 9xxxxxxxxx', SmsAlertConstants::ERROR_JSON_TYPE ) );
		}
	}

	/**
	 * Add OTP modal and verify button to user registration form
	 *
	 * @param array $args arguments.
	 * @param int   $form_id form id.
	 *
	 * @return void
	 */
	public static function my_predefined_fields( $args, $form_id ) {
		if ( smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' ) {
			$otp_template_style = smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php' );
			echo '<input class="sa-otp-btn-init ninja-forms-field nf-element smsalert_otp_btn_submit" value="Verify & Submit" type="button">';
			get_smsalert_template( 'template/' . $otp_template_style, $params = array() );
		}
	}

	/**
	 * Check user mobile number is duplicate or not
	 *
	 * @param array $args arguments.
	 * @param int   $form_id form id.
	 *
	 * @return void
	 */
	public function smsalert_ur_registration_validation( $args, $form_id ) {
		if ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) !== 'on' && ! SmsAlertUtility::isBlank( $args['billing_phone']->value ) ) {
			$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $args['billing_phone']->value );
			if ( count( $getusers ) > 0 ) {
				wp_send_json_error(
					array(
						'message' => __( 'An account is already registered with this mobile number. Please login.', 'sms-alert' ),
					)
				);
			}
		}
	}

	/**
	 * Update user phone number after registration.
	 *
	 * @param array $args arguments.
	 * @param int   $form_id form id.
	 * @param int   $user_id user id.
	 *
	 * @return void
	 */
	public function smsalert_ur_registration_complete( $args, $form_id, $user_id ) {
		$user_phone = ( array_key_exists( 'billing_phone', $args ) ) ? $args['billing_phone']->value : '';
		do_action( 'smsalert_after_update_new_user_phone', $user_id, $user_phone );
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
new UserRegistrationForm();
