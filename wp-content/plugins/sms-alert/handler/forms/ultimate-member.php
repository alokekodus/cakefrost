<?php
/**
 * This file handles wpmember form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'ultimate-member/ultimate-member.php' ) ) {
	return; }

/**
 * UltimateMemberRegistrationForm class.
 */
class UltimateMemberRegistrationForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::UM_DEFAULT_REG;

	/**
	 * Phone Form id.
	 *
	 * @var stirng
	 */
	private $phone_form_id = "input[name^='billing_phone']";

	/**
	 * Form Session Variable 2.
	 *
	 * @var stirng
	 */
	private $form_session_var2 = 'SA_UM_RESET_PWD';

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_number_key = 'billing_phone';

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		if ( is_plugin_active( 'ultimate-member/ultimate-member.php' ) ) {
			add_filter( 'um_add_user_frontend_submitted', array( $this, 'smsalert_um_user_registration' ), 1, 1 );
		} else // < UM version 2.0.17
		{
			add_action( 'um_before_new_user_register', array( $this, 'smsalert_um_user_registration' ), 1, 1 );
		}
		add_action( 'um_submit_form_errors_hook_registration', array( $this, 'smsalert_um_registration_validation' ), 10 );

		if ( smsalert_get_option( 'reset_password', 'smsalert_general' ) === 'on' ) {
			add_action( 'um_reset_password_process_hook', array( $this, 'smsalert_um_reset_pwd_submitted' ), 0, 1 );
		}
		add_action( 'um_registration_complete', array( $this, 'smsalert_um_registration_complete' ), 10, 2 );
		add_action( 'um_after_form', array( $this, 'um_form_add_shortcode' ), 10, 1 );

		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-um-reset-pwd-action' ) {
			$this->handle_smsalert_changed_pwd( $_POST );
			wp_enqueue_style( 'wpv_sa_common_style', SA_MOV_CSS_URL, array(), SmsAlertConstants::SA_VERSION, false );
		}

		if ( ! empty( $_REQUEST['sa_um_reset_pwd'] ) ) {
			add_filter( 'um_before_form_is_loaded', array( $this, 'my_before_form' ), 10, 1 );
		}
	}

	/**
	 * Handle submission of posted data
	 *
	 * @param  array $post_data posted by user.
	 *
	 * @return void
	 */
	public function handle_smsalert_changed_pwd( $post_data ) {
		SmsAlertUtility::checkSession();
		$error            = '';
		$new_password     = ! empty( $post_data['smsalert_user_newpwd'] ) ? $post_data['smsalert_user_newpwd'] : '';
		$confirm_password = ! empty( $post_data['smsalert_user_cnfpwd'] ) ? $post_data['smsalert_user_cnfpwd'] : '';

		if ( empty( $new_password ) ) {
			$error = SmsAlertMessages::showMessage( 'ENTER_PWD' );
		}
		if ( $new_password !== $confirm_password ) {
			$error = SmsAlertMessages::showMessage( 'PWD_MISMATCH' );
		}
		if ( ! empty( $error ) ) {
			smsalertAskForResetPassword( $_SESSION['user_login'], $_SESSION['phone_number_mo'], $error, 'phone', false );
		}

		$user = get_user_by( 'login', $_SESSION['user_login'] );
		reset_password( $user, $new_password );
		$this->unsetOTPSessionVariables();
		exit( wp_redirect( esc_url( add_query_arg( 'sa_um_reset_pwd', true, um_get_core_page( 'password-reset' ) ) ) ) );
	}

	/**
	 * Add shortcode to UM form.
	 *
	 * @param array $args form fields.
	 *
	 * @return void
	 */
	public function um_form_add_shortcode( $args ) {

		$default_login_otp   = smsalert_get_option( 'buyer_login_otp', 'smsalert_general' );
		$enabled_login_popup = smsalert_get_option( 'login_popup', 'smsalert_general' );

		if ( 'on' === $default_login_otp && 'on' === $enabled_login_popup ) {
			if ( 'login' === $args['mode'] ) {
				echo do_shortcode( '[sa_verify user_selector="#username-' . esc_attr( $args['form_id'] ) . '" pwd_selector="#user_password-' . esc_attr( $args['form_id'] ) . '" submit_selector="#um-submit-btn"]' );
			}
		}
	}

	/**
	 * Add field to um backend form section.
	 *
	 * @param array $predefined_fields form fields.
	 *
	 * @return array
	 */
	public static function my_predefined_fields( $predefined_fields ) {
		$fields            = array(
			'billing_phone' => array(
				'title'    => 'Smsalert Phone',
				'metakey'  => 'billing_phone',
				'type'     => 'text',
				'label'    => 'Mobile Number',
				'required' => 0,
				'public'   => 1,
				'editable' => 1,
				'validate' => 'billing_phone',
				'icon'     => 'um-faicon-mobile',
			),
		);
		$predefined_fields = array_merge( $predefined_fields, $fields );
		return $predefined_fields;
	}

	/**
	 * Show Success message before form.
	 *
	 * @param  object $args posted data from form.
	 *
	 * @return void
	 */
	public function my_before_form( $args ) {
		echo '<p class="um-notice success"><i class="um-icon-ios-close-empty" onclick="jQuery(this).parent().fadeOut();"></i>' . __( 'Password Changed Successfully.', 'sms-alert' ) . '</p>';
	}

	/**
	 * Send sms after um reset pwd submitted
	 *
	 * @param  object $datas posted data from registration form.
	 *
	 * @return object
	 */
	public function smsalert_um_reset_pwd_submitted( $datas ) {

		SmsAlertUtility::checkSession();
		$user_login = ! empty( $datas['username_b'] ) ? $datas['username_b'] : '';

		if ( username_exists( $user_login ) ) {
			$user = get_user_by( 'login', $user_login );
		} elseif ( email_exists( $user_login ) ) {
			$user = get_user_by( 'email', $user_login );
		}
		$phone_number = get_user_meta( $user->data->ID, $this->phone_number_key, true );
		if ( ! empty( $phone_number ) ) {
			SmsAlertUtility::initialize_transaction( $this->form_session_var2 );
			if ( ! empty( $phone_number ) ) {
				$this->startOtpTransaction( $user->data->user_login, $user->data->user_login, null, $phone_number, null, null );
			}
		}
		return $user;
	}

	/**
	 * Send sms after um registration validation
	 *
	 * @param  object $args posted data from registration form.
	 *
	 * @return void
	 */
	public function smsalert_um_registration_validation( $args ) {
		if ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) !== 'on' && ! SmsAlertUtility::isBlank( $args['billing_phone'] ) ) {
			$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $args['billing_phone'] );
			if ( count( $getusers ) > 0 ) {
				UM()->form()->add_error( 'billing_phone', __( 'An account is already registered with this mobile number. Please login.', 'sms-alert' ) );
			}
		}
	}

	/**
	 * update phone after um registration completes
	 *
	 * @param  int    $user_id user id.
	 * @param  object $args posted data from registration form.
	 *
	 * @return void
	 */
	public function smsalert_um_registration_complete( $user_id, $args ) {
		$user_phone = ( ! empty( $args['billing_phone'] ) ) ? $args['billing_phone'] : '';
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
	 * Handle smsalert um user registration.
	 *
	 * @param  object $args posted data from registration form.
	 *
	 * @return array
	 */
	public function smsalert_um_user_registration( $args ) {
		if ( ! array_key_exists( 'billing_phone', $args ) ) {
			return $args;
		}
		SmsAlertUtility::checkSession();
		$errors = new WP_Error();

		if ( isset( $_SESSION['sa_um_mobile_verified'] ) ) {
			unset( $_SESSION['sa_um_mobile_verified'] );
			return $args;
		}

		SmsAlertUtility::initialize_transaction( $this->form_session_var );

		foreach ( $args as $key => $value ) {
			if ( 'user_login' === $key ) {
				$username = $value;
			} elseif ( 'user_email' === $key ) {
				$email = $value;
			} elseif ( 'user_password' === $key ) {
				$password = $value;
			} elseif ( 'billing_phone' === $key ) {
				$phone_number = $value;
			} else {
				$extra_data[ $key ] = $value;
			}
		}

		$this->startOtpTransaction( $username, $email, $errors, $phone_number, $password, $extra_data );
		exit();
	}

	/**
	 * Start Otp process.
	 *
	 * @param  string $username username.
	 * @param  string $email user email id.
	 * @param  object $errors form error.
	 * @param  string $phone_number phone number.
	 * @param  string $password password.
	 * @param  string $extra_data get hidden fields.
	 *
	 * @return void
	 */
	public function startOtpTransaction( $username, $email, $errors, $phone_number, $password, $extra_data ) {
		smsalert_site_challenge_otp( $username, $email, $errors, $phone_number, 'phone', $password, $extra_data );
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return;
		}
		smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertUtility::_get_invalid_otp_method(), 'phone', false );
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return;
		}

		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			smsalertAskForResetPassword( $_SESSION['user_login'], $_SESSION['phone_number_mo'], SmsAlertMessages::showMessage( 'CHANGE_PWD' ), 'phone', false, 'smsalert-um-reset-pwd-action' );
		} else {
			$_SESSION['sa_um_mobile_verified'] = true;
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
		unset( $_SESSION[ $this->form_session_var2 ] );
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
		return isset( $_SESSION[ $this->form_session_var ] ) ? false : $is_ajax;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new UltimateMemberRegistrationForm();
