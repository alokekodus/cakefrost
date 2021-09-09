<?php
/**
 * This file handles login authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return; }

/**
 * WPLoginForm class.
 **/
class WPLoginForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::WP_LOGIN_REG_PHONE;

	/**
	 * Form Session Variable for login in popup.
	 *
	 * @var stirng
	 */
	private $form_session_var2 = FormSessionVars::WP_DEFAULT_LOGIN;

	/**
	 * Form Session Variable for login with otp.
	 *
	 * @var stirng
	 */
	private $form_session_var3 = FormSessionVars::WP_LOGIN_WITH_OTP;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_number_key;

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		$this->phone_number_key = 'billing_phone';
		if ( ! empty( $_REQUEST['learn-press-register-nonce'] ) ) {
			return;}
		$enabled_login_popup = smsalert_get_option( 'login_popup', 'smsalert_general' );
		$this->routeData();
		$enabled_login_with_otp = smsalert_get_option( 'login_with_otp', 'smsalert_general' );
		$default_login_otp      = smsalert_get_option( 'buyer_login_otp', 'smsalert_general' );

		if ( $enabled_login_with_otp === 'on' ) {
			add_action( 'wp_enqueue_scripts', array( 'SmsAlertUtility', 'enqueue_script_for_intellinput' ) );
		}

		if ( $default_login_otp === 'on' ) {
			if ( $enabled_login_popup === 'on' ) {
				add_action( 'woocommerce_login_form_end', array( $this, 'smsalert_display_login_button_popup' ) );
			} else {
				add_filter( 'authenticate', array( $this, 'handle_smsalert_wp_login' ), 99, 4 );
			}
		}

		if ( $enabled_login_with_otp === 'on' ) {
			add_action( 'woocommerce_login_form_end', array( $this, 'smsalert_display_login_with_otp' ) );
			add_action( 'um_after_login_fields', array( $this, 'smsalert_display_login_with_otp' ), 1002 );
		}

		if ( $enabled_login_with_otp === 'on' || $default_login_otp === 'on' ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_login_js_script' ) );
		}

	}

	/**
	 * Handle post data via ajax submit
	 *
	 * @return void
	 */
	public function routeData() {
		if ( ! array_key_exists( 'option', $_REQUEST ) ) {
			return;
		}
		switch ( trim( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) ) ) {
			case 'smsalert-ajax-otp-generate':
				$this->handle_wp_login_ajax_send_otp( $_POST );
				break;
			case 'smsalert-ajax-otp-validate':
				$this->handle_wp_login_ajax_form_validate_action( $_POST );
				break;
			case 'smsalert_ajax_form_validate':
				$this->handle_wp_login_create_user_action( $_POST );
				break;
			case 'smsalert_ajax_login_with_otp':
				$this->handle_login_with_otp();
				break;
			case 'smsalert_ajax_login_popup':
				$this->handle_login_popup();
				break;
			case 'smsalert_verify_login_with_otp':
				$this->process_login_with_otp();
				break;
		}
	}


	/**
	 * Handle login popup submit
	 *
	 * @return object
	 */
	public function handle_login_popup() {
		$username = ! empty( $_REQUEST['username'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['username'] ) ) : '';
		$password = ! empty( $_REQUEST['password'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['password'] ) ) : '';

		// check user with username and password.
		$user = $this->getUserIfUsernameIsPhoneNumber( null, $username, $password, $this->phone_number_key );

		if ( ! $user ) {
			$user = wp_authenticate( $username, $password );
		}

		if ( is_wp_error( $user ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'Invalid Username or Password', 'error' ) );
		}

		$user_meta    = get_userdata( $user->data->ID );
		$user_role    = $user_meta->roles;
		$phone_number = get_user_meta( $user->data->ID, $this->phone_number_key, true );

		if ( $this->byPassLogin( $user_role ) ) {
			return $user;
		}

		SmsAlertUtility::initialize_transaction( $this->form_session_var3 );
		smsalert_site_challenge_otp( $username, null, null, $phone_number, 'phone', $password, SmsAlertUtility::currentPageUrl(), true );
	}

	/**
	 * Display login button popup
	 *
	 * @return void
	 */
	public function smsalert_display_login_button_popup() {
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
		echo '<button type="button" class="button smsalert_login_popup sa-otp-btn-init" name="smsalert_login_popup" value="Login"><span class="button__text">Login</span></button>';
		echo '<script>
			jQuery(".smsalert_login_popup").parents("form").find("[type=\"submit\"]").css({"display":"none"});
			</script>';

		$enabled_login_with_otp = smsalert_get_option( 'login_with_otp', 'smsalert_general' );
		if ( $enabled_login_with_otp !== 'on' ) {
			$this->add_login_with_otp_popup();
		}
	}

	/**
	 * Handle login with otp
	 *
	 * @return void
	 */
	public function handle_login_with_otp() {
		if ( empty( $_REQUEST['username'] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'PHONE_NOT_FOUND' ), 'error' ) );
		} else {
			$phone_number = ! empty( $_REQUEST['username'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['username'] ) ) : '';
			if ( ! empty( $phone_number ) ) {
				$user_info  = $this->getUserFromPhoneNumber( $phone_number, $this->phone_number_key );
				$user_login = ( $user_info ) ? $user_info->data->user_login : '';
			}

			if ( ! empty( $user_login ) ) {
				SmsAlertUtility::initialize_transaction( $this->form_session_var3 );
				smsalert_site_challenge_otp( null, null, null, $phone_number, 'phone', null, SmsAlertUtility::currentPageUrl(), true );
			} else {
				wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'PHONE_NOT_FOUND' ), 'error' ) );
			}
		}
	}

	/**
	 * Display Button login with otp
	 *
	 * @return void
	 */
	public function smsalert_display_login_with_otp() {
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );

		echo '<div class="lwo-container"><div class="sa_or">OR</div><button type="button" class="button sa_myaccount_btn" name="sa_myaccount_btn_login" value="' . __( 'Login with OTP', 'sms-alert' ) . '" style="width: 100%;">' . __( 'Login with OTP', 'sms-alert' ) . '</button></div>';

		$this->add_login_with_otp_popup();
	}

	/**
	 * Add login with otp form code in login form page.
	 *
	 * @return void
	 */
	public function add_login_with_otp_popup() {
		$otp_resend_timer       = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
		$enabled_login_popup    = smsalert_get_option( 'login_popup', 'smsalert_general', 'on' );
		$enabled_login_with_otp = smsalert_get_option( 'login_with_otp', 'smsalert_general' );
		$otp_template_style     = smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php' );

		if ( $enabled_login_popup === 'on' ) {
			get_smsalert_template( 'template/' . $otp_template_style, $params = array() );
			echo '<div class="login_with_otp_extra_fields"></div>';
		}

		if ( $enabled_login_with_otp === 'on' ) {
			echo '<div class="sa-lwo-form-holder">';
			get_smsalert_template( 'template/login-with-otp-form.php', array() );
			get_smsalert_template( 'template/' . $otp_template_style, $params = array() );
			echo '<div class="login_with_otp_extra_fields"></div>';
			echo '</div>';
		}
	}

	/**
	 * Add js script to code
	 *
	 * @return void
	 */
	public function enqueue_login_js_script() {
		$enabled_login_with_otp = smsalert_get_option( 'login_with_otp', 'smsalert_general' );
		$default_login_otp      = smsalert_get_option( 'buyer_login_otp', 'smsalert_general' );

		wp_register_script( 'smsalert-auth', SA_MOV_URL . 'js/otp-sms.min.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, true );
		wp_localize_script(
			'smsalert-auth',
			'sa_otp_settings',
			array(
				'otp_time'                => smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' ),
				'show_countrycode'        => smsalert_get_option( 'checkout_show_country_code', 'smsalert_general', 'off' ),
				'site_url'                => site_url(),
				'is_checkout'             => ( ( function_exists( 'is_checkout' ) && is_checkout() ) ? true : false ),
				'login_with_otp'          => ( $enabled_login_with_otp === 'on' ? true : false ),
				'buyer_login_otp'         => ( $default_login_otp === 'on' ? true : false ),
				'hide_default_login_form' => smsalert_get_option( 'hide_default_login_form', 'smsalert_general' ),
			)
		);
		wp_localize_script(
			'smsalert-auth',
			'sa_notices',
			array(
				'waiting_txt'  => __( 'Please wait...', 'sms-alert' ),
				'enter_here'   => __( 'Enter Number Here', 'sms-alert' ),
				'enter_mob_no' => __( 'Please enter mobile number.', 'sms-alert' ),
			)
		);

		wp_localize_script(
			'smsalert-auth',
			'smsalert_wpml',
			array(
				'lang' => apply_filters( 'wpml_current_language', null ),
			)
		);

		wp_enqueue_style( 'sa-login-css', SA_MOV_URL . 'css/sms_alert_customer_validation_style.css', array(), SmsAlertConstants::SA_VERSION, 'all' );

		wp_enqueue_script( 'smsalert-auth' );
	}

	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( $islogged && ( smsalert_get_option( 'buyer_login_otp', 'smsalert_general' ) === 'on' || smsalert_get_option( 'login_with_otp', 'smsalert_general' ) === 'on' ) ) ? true : false;
	}

	/**
	 * Check wp_login_register_phon.
	 *
	 * @return bool
	 */
	public function check_wp_login_register_phone() {
		return true;
	}

	/**
	 * Check wp_login_by_phone_number.
	 *
	 * @return bool
	 */
	public function check_wp_login_by_phone_number() {
		return true;
	}

	/**
	 * By Pass Login if any role is required to escape from login authentication.
	 *
	 * @param array $user_role get all wp user roles.
	 *
	 * @return bool
	 */
	public function byPassLogin( $user_role ) {
		$current_role   = array_shift( $user_role );
		$excluded_roles = smsalert_get_option( 'admin_bypass_otp_login', 'smsalert_general', array() );
		if ( ! is_array( $excluded_roles ) && $excluded_roles === 'on' ) {
			$excluded_roles = ( $current_role === 'administrator' ) ? array( 'administrator' ) : array();
		}
		return in_array( $current_role, $excluded_roles, true ) ? true : false;
	}

	/**
	 * Check wp login restrict duplicates.
	 *
	 * @return bool
	 */
	public function check_wp_login_restrict_duplicates() {
		return ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) === 'on' ) ? true : false;
	}

	/**
	 * Handle wp login create user action.
	 *
	 * @param array $postdata posted data by user.
	 *
	 * @return void
	 */
	public function handle_wp_login_create_user_action( $postdata ) {
		$redirect_to = isset( $postdata['redirect_to'] ) ? $postdata['redirect_to'] : null;
		// added this line on 28-11-2018 due to affiliate login redirect issue.

		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] )
			|| $_SESSION[ $this->form_session_var ] !== 'validated' ) {
			return;
		}

		$user = is_email( $postdata['log'] ) ? get_user_by( 'email', $postdata['log'] ) : get_user_by( 'login', $postdata['log'] );
		if ( ! $user ) {
			$user = is_email( $postdata['username'] ) ? get_user_by( 'email', $postdata['username'] ) : get_user_by( 'login', $postdata['username'] );
		}

		update_user_meta( $user->data->ID, $this->phone_number_key, sanitize_text_field( $postdata['sa_phone_number'] ) );
		$this->login_wp_user( $user->data->user_login, $redirect_to );
	}

	/**
	 * If your user is authenticated then redirect him to page.
	 *
	 * @param object $user_log logged user details.
	 * @param string $extra_data get hidden fields.
	 *
	 * @return void
	 */
	public function login_wp_user( $user_log, $extra_data = null ) {
		$user = get_user_by( 'login', $user_log );
		wp_set_auth_cookie( $user->data->ID );
		$this->unsetOTPSessionVariables();
		do_action( 'wp_login', $user->user_login, $user );
		$redirect = SmsAlertUtility::isBlank( $extra_data ) ? site_url() : $extra_data;
		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Process login with otp.
	 *
	 * @return void
	 */
	public function process_login_with_otp() {
		SmsAlertUtility::checkSession();
		$login_with_otp_enabled = ( smsalert_get_option( 'login_with_otp', 'smsalert_general' ) === 'on' ) ? true : false;
		if ( empty( $password ) ) {
			if ( ! empty( $_REQUEST['username'] ) ) {
				$phone_number = ! empty( $_REQUEST['username'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['username'] ) ) : '';
				$user_info    = $this->getUserFromPhoneNumber( $phone_number, $this->phone_number_key );
				$user_login   = ( $user_info ) ? $user_info->data->user_login : '';
			}
		}

		if ( $login_with_otp_enabled && empty( $password ) && ! empty( $user_login ) && ! empty( $_SESSION['login_otp_success'] ) ) {
			if ( ! empty( $_POST['redirect'] ) ) {
				$redirect = wp_sanitize_redirect( wp_unslash( $_POST['redirect'] ) );
			} elseif ( wc_get_raw_referer() ) {
				$redirect = wc_get_raw_referer();
			}
			unset( $_SESSION['login_otp_success'] );

			$user = get_user_by( 'login', $user_login );
			wp_set_auth_cookie( $user->data->ID );
			$this->unsetOTPSessionVariables();
			$msg             = SmsAlertUtility::_create_json_response( 'Login successful', 'success' );
			$redirect        = apply_filters( 'woocommerce_login_redirect', $redirect, $user );
			$msg['redirect'] = $redirect;
			wp_send_json( $msg );
			exit();
		}
	}

	/**
	 * Handle smsalert login after submitted by user.
	 *
	 * @param  array  $user user data.
	 * @param  string $username wp username.
	 * @param  stirng $password wp password.
	 *
	 * @return object
	 */
	public function handle_smsalert_wp_login( $user, $username, $password ) {
		SmsAlertUtility::checkSession();
		$login_with_otp_enabled = ( smsalert_get_option( 'login_with_otp', 'smsalert_general' ) === 'on' ) ? true : false;

		if ( empty( $password ) ) {
			if ( ! empty( $_REQUEST['username'] ) ) {
				$phone_number = ! empty( $_REQUEST['username'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['username'] ) ) : '';
				$user_info    = $this->getUserFromPhoneNumber( $phone_number, $this->phone_number_key );
				$user_login   = ( $user_info ) ? $user_info->data->user_login : '';
			}
		}

		if ( $login_with_otp_enabled && empty( $password ) && ! empty( $user_login ) && ! empty( $_SESSION['login_otp_success'] ) ) {
			if ( ! empty( $_POST['redirect'] ) ) {
				$redirect = wp_sanitize_redirect( wp_unslash( $_POST['redirect'] ) );
			} elseif ( wc_get_raw_referer() ) {
				$redirect = wc_get_raw_referer();
			} else {
				$redirect = wc_get_page_permalink( 'myaccount' );
			}
			unset( $_SESSION['login_otp_success'] );
			$this->login_wp_user( $user_login, $redirect );
		}

		if ( ( array_key_exists( $this->form_session_var, $_SESSION ) && strcasecmp( $_SESSION[ $this->form_session_var ], 'validated' ) === 0 ) && ! empty( $_POST['sa_phone_number'] ) ) {
			update_user_meta( $user->data->ID, $this->phone_number_key, sanitize_text_field( wp_unslash( $_POST['sa_phone_number'] ) ) );
			$this->unsetOTPSessionVariables();
		}

		if ( isset( $_SESSION['sa_login_mobile_verified'] ) ) {
			unset( $_SESSION['sa_login_mobile_verified'] );
			return $user;
		}

		$user = $this->getUserIfUsernameIsPhoneNumber( $user, $username, $password, $this->phone_number_key );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$user_meta    = get_userdata( $user->data->ID );
		$user_role    = $user_meta->roles;
		$phone_number = get_user_meta( $user->data->ID, $this->phone_number_key, true );
		if ( $this->byPassLogin( $user_role ) ) {
			return $user;
		}

		if ( ( smsalert_get_option( 'buyer_login_otp', 'smsalert_general' ) === 'off' && smsalert_get_option( 'login_with_otp', 'smsalert_general' ) === 'on' ) ) {
			return $user;
		}

		$this->askPhoneAndStartVerification( $user, $this->phone_number_key, $username, $phone_number );
		$this->fetchPhoneAndStartVerification( $user, $this->phone_number_key, $username, $password, $phone_number );
		return $user;
	}

	/**
	 * Get User If Username Is PhoneNumber.
	 *
	 * @param  array  $user user data.
	 * @param  string $username wp username.
	 * @param  string $password wp password.
	 * @param  string $key phone field name.
	 *
	 * @return object
	 */
	public function getUserIfUsernameIsPhoneNumber( $user, $username, $password, $key ) {
		if ( ! $this->check_wp_login_by_phone_number() || ! SmsAlertUtility::validatePhoneNumber( $username ) ) {
			return $user;
		}
		$user_info = $this->getUserFromPhoneNumber( $username, $key );
		$username  = is_object( $user_info ) ? $user_info->data->user_login : $username; // added on 20-05-2019.
		return wp_authenticate_username_password( null, $username, $password );
	}

	/**
	 * Get User From PhoneNumber.
	 *
	 * @param  string $username wp username.
	 * @param  string $key phone field name.
	 *
	 * @return object
	 */
	public function getUserFromPhoneNumber( $username, $key ) {
		global $wpdb;

		$wcc_ph     = SmsAlertcURLOTP::checkPhoneNos( $username );
		$wocc_ph    = SmsAlertcURLOTP::checkPhoneNos( $username, false );
		$wth_pls_ph = '+' . $wcc_ph;

		$results = $wpdb->get_row( "SELECT `user_id` FROM {$wpdb->base_prefix}usermeta inner join {$wpdb->base_prefix}users on ({$wpdb->base_prefix}users.ID = {$wpdb->base_prefix}usermeta.user_id) WHERE `meta_key` = '$key' AND `meta_value` in('$wcc_ph','$wocc_ph','$wth_pls_ph') order by user_id desc" );
		$user_id = ( ! empty( $results ) ) ? $results->user_id : 0;
		return get_userdata( $user_id );
	}

	/**
	 * Ask Phone And Start Verification.
	 *
	 * @param  object $user wp user object.
	 * @param  string $key phone field name.
	 * @param  string $username wp username.
	 * @param  string $phone_number user phone number.
	 *
	 * @return object
	 */
	public function askPhoneAndStartVerification( $user, $key, $username, $phone_number ) {
		if ( ! SmsAlertUtility::isBlank( $phone_number ) ) {
			return;
		}
		if ( ! $this->check_wp_login_register_phone() ) {
			smsalert_site_otp_validation_form( null, null, null, SmsAlertMessages::showMessage( 'PHONE_NOT_FOUND' ), null, null );
		} else {
			SmsAlertUtility::initialize_transaction( $this->form_session_var );
			smsalert_external_phone_validation_form( SmsAlertUtility::currentPageUrl(), $user->data->user_login, __( 'A new security system has been enabled for you. Please register your phone to continue.', 'sms-alert' ), $key, array( 'user_login' => $username ) );
		}
	}

	/**
	 * Fetch Phone and start verification
	 *
	 * @param  object $user users object.
	 * @param  string $key phone key.
	 * @param  string $username username.
	 * @param  string $password password.
	 * @param  string $phone_number phone number.
	 *
	 * @return void
	 */
	public function fetchPhoneAndStartVerification( $user, $key, $username, $password, $phone_number ) {
		if ( ( array_key_exists( $this->form_session_var, $_SESSION ) && strcasecmp( $_SESSION[ $this->form_session_var ], 'validated' ) === 0 )
			|| ( array_key_exists( $this->form_session_var2, $_SESSION ) && strcasecmp( $_SESSION[ $this->form_session_var2 ], 'validated' ) === 0 ) ) {
			return;
		}
		SmsAlertUtility::initialize_transaction( $this->form_session_var2 );

		smsalert_site_challenge_otp( $username, null, null, $phone_number, 'phone', $password, SmsAlertUtility::currentPageUrl(), false );
	}

	/**
	 * Handle otp ajax send otp
	 *
	 * @param  object $data users data.
	 *
	 * @return void
	 */
	public function handle_wp_login_ajax_send_otp( $data ) {
		SmsAlertUtility::checkSession();
		if ( ! $this->check_wp_login_restrict_duplicates()
			&& ! SmsAlertUtility::isBlank( $this->getUserFromPhoneNumber( $data['billing_phone'], $this->phone_number_key ) ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( __( 'Phone Number is already in use. Please use another number.', 'sms-alert' ), SmsAlertConstants::ERROR_JSON_TYPE ) );
		} elseif ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			smsalert_site_challenge_otp( 'ajax_phone', '', null, trim( $data['billing_phone'] ), 'phone', null, $data, null );
		}
	}

	/**
	 * Handle validation otp ajax sentotp
	 *
	 * @param  object $data users data.
	 *
	 * @return void
	 */
	public function handle_wp_login_ajax_form_validate_action( $data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) && ! isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			return;
		}

		if ( strcmp( $_SESSION['phone_number_mo'], $data['billing_phone'] ) && isset( $data['billing_phone'] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'PHONE_MISMATCH' ), 'error' ) );
		} else {
			do_action( 'smsalert_validate_otp', 'phone' );
		}
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) && ! isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			return;
		}

		if ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			$_SESSION[ $this->form_session_var ] = 'verification_failed';
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
		}
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'phone', false );
		}
		if ( isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) && ! isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			return;
		}

		if ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			$_SESSION['sa_login_mobile_verified'] = true;
			$_SESSION[ $this->form_session_var ]  = 'validated';
			wp_send_json( SmsAlertUtility::_create_json_response( 'successfully validated', 'success' ) );
		} elseif ( isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			$_SESSION['login_otp_success'] = true;
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
		} else {
			$_SESSION['sa_login_mobile_verified'] = true;
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
		unset( $_SESSION[ $this->form_session_var3 ] );
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
		return ( isset( $_SESSION[ $this->form_session_var ] ) || isset( $_SESSION[ $this->form_session_var3 ] ) ) ? true : $is_ajax;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {
	}
}
	new WPLoginForm();
