<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return; }

/**
 * Woocommerce Registration handler class.
 */
class WooCommerceRegistrationForm extends FormInterface {

	/**
	 * Woocommerce default registration form key
	 *
	 * @var $form_session_var Woocommerce default registration form key
	 */
	private $form_session_var = FormSessionVars::WC_DEFAULT_REG;
	/**
	 * Woocommerce registration popup form key
	 *
	 * @var $form_session_var2 Woocommerce registration popup form key
	 */
	private $form_session_var2 = FormSessionVars::WC_REG_POPUP;
	/**
	 * If OTP in popup is enabled or not
	 *
	 * @var $popup_enabled If OTP in popup is enabled or not
	 */
	private $popup_enabled;

	/**
	 * Handles registration form submit.
	 */
	public function handleForm() {
		$this->popup_enabled = ( smsalert_get_option( 'register_otp_popup_enabled', 'smsalert_general' ) === 'on' ) ? true : false;
		if ( isset( $_REQUEST['register'] ) ) {
			add_filter( 'woocommerce_registration_errors', array( $this, 'woocommerce_site_registration_errors' ), 10, 3 );
		}

		if ( is_plugin_active( 'dokan-lite/dokan.php' ) ) {
			add_action( 'dokan_reg_form_field', array( $this, 'smsalert_add_dokan_phone_field' ) );
			add_action( 'dokan_vendor_reg_form_start', array( $this, 'smsalert_add_dokan_phone_field' ) );
			add_action( 'dokan_vendor_reg_form_start', array( $this, 'smsalert_add_dokan_vendor_reg_field' ) );
		} else {
			add_action( 'woocommerce_register_form', array( $this, 'smsalert_add_phone_field' ) );
		}

		if ( is_plugin_active( 'dc-woocommerce-multi-vendor/dc_product_vendor.php' ) ) {
			add_action( 'wcmp_vendor_register_form', array( $this, 'smsalert_add_wcmp_phone_field' ) );
		}

		if ( $this->popup_enabled ) {
			add_action( 'woocommerce_register_form_end', array( $this, 'add_modal_html_register_otp' ) );
			add_action( 'woocommerce_register_form_end', array( $this, 'smsalert_display_registerOTP_btn' ) );
		}

		add_action( 'wp_enqueue_scripts', array( 'SmsAlertUtility', 'enqueue_script_for_intellinput' ) );
		add_action( 'woocommerce_after_save_address_validation', array( $this, 'validate_woocommerce_save_address' ), 10, 3 );

		$signup_with_mobile = smsalert_get_option( 'signup_with_mobile', 'smsalert_general', 'off' );
		if ( 'on' === $signup_with_mobile ) {
			add_action( 'woocommerce_register_form_end', array( $this, 'smsalert_display_signup_with_mobile' ), 10 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_reg_js_script' ) );
		}

		wp_enqueue_style( 'wpv_sa_common_style', SA_MOV_CSS_URL, array(), SmsAlertConstants::SA_VERSION, false );
		$this->routeData();
	}


	/** Sign up with otp starts. **/
	public function smsalert_display_signup_with_mobile() {
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
		echo wp_kses_post( '<div class="lwo-container"><div class="sa_or">OR</div><button type="button" class="button sa_myaccount_btn" name="sa_myaccount_btn_signup" value="' . __( 'Signup with Mobile', 'sms-alert' ) . '" style="width: 100%;"><span class="button__text">' . __( 'Signup with Mobile', 'sms-alert' ) . '</span></button></div>' );
	}

	/**
	 * Add smsalert phone button in ultimate form.
	 *
	 * @param int    $user_id Userid of the user.
	 * @param string $load_address Currently not in use in this function.
	 * @param string $address Currently not in use in this function.
	 */
	public function validate_woocommerce_save_address( $user_id, $load_address, $address ) {
		$db_billing_phone = get_post_meta( $user_id, '_billing_phone', true );
		$user_phone       = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
		if ( $db_billing_phone !== $user_phone ) {
			if ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) !== 'on' && ! SmsAlertUtility::isBlank( $user_phone ) ) {
				$_POST['billing_phone'] = SmsAlertcURLOTP::checkPhoneNos( $user_phone );

				$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $user_phone, array( 'exclude' => array( $user_id ) ) );
				if ( count( $getusers ) > 0 ) {
					wc_add_notice( sprintf( __( 'An account is already registered with this mobile number.', 'woocommerce' ), '<strong>Billing Phone</strong>' ), 'error' );
				}
			}
		}
	}

	/**
	 * This function is executed after a user has been registered.
	 *
	 * @param int    $user_id Userid of the user.
	 * @param string $billing_phone Phone number of the user.
	 */
	public static function smsalert_after_user_register( $user_id, $billing_phone ) {
		$user                = get_userdata( $user_id );
		$role                = ( ! empty( $user->roles[0] ) ) ? $user->roles[0] : '';
		$role_display_name   = ( ! empty( $role ) ) ? self::get_user_roles( $role ) : '';
		$smsalert_reg_notify = smsalert_get_option( 'wc_user_roles_' . $role, 'smsalert_signup_general', 'off' );
		$sms_body_new_user   = smsalert_get_option( 'signup_sms_body_' . $role, 'smsalert_signup_message', SmsAlertMessages::showMessage( 'DEFAULT_NEW_USER_REGISTER' ) );

		$smsalert_reg_admin_notify = smsalert_get_option( 'admin_registration_msg', 'smsalert_general', 'off' );
		$sms_admin_body_new_user   = smsalert_get_option( 'sms_body_registration_admin_msg', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_ADMIN_NEW_USER_REGISTER' ) );
		$admin_phone_number        = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

		$store_name = trim( get_bloginfo() );

		if ( 'on' === $smsalert_reg_notify && ! empty( $billing_phone ) ) {
			$search = array(
				'[username]',
				'[email]',
				'[billing_phone]',
			);

			$replace           = array(
				$user->user_login,
				$user->user_email,
				$billing_phone,
			);
			$sms_body_new_user = str_replace( $search, $replace, $sms_body_new_user );
			do_action( 'sa_send_sms', $billing_phone, $sms_body_new_user );
		}

		if ( 'on' === $smsalert_reg_admin_notify && ! empty( $admin_phone_number ) ) {
			$search = array(
				'[username]',
				'[store_name]',
				'[email]',
				'[billing_phone]',
				'[role]',
			);

			$replace = array(
				$user->user_login,
				$store_name,
				$user->user_email,
				$billing_phone,
				$role_display_name,
			);

			$sms_admin_body_new_user = str_replace( $search, $replace, $sms_admin_body_new_user );
			$nos                     = explode( ',', $admin_phone_number );
			$admin_phone_number      = array_diff( $nos, array( 'postauthor', 'post_author' ) );
			$admin_phone_number      = implode( ',', $admin_phone_number );
			do_action( 'sa_send_sms', $admin_phone_number, $sms_admin_body_new_user );
		}
	}

	/**
	 * This function checks whether this form is enabled or not.
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( $islogged && smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' ) ? true : false;
	}

	/**
	 * This function is used to route the request.
	 */
	public function routeData() {
		if ( ! array_key_exists( 'option', $_REQUEST ) ) {
			return;
		}
		switch ( trim( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) ) ) {
			case 'smsalert_register_otp_validate_submit':
				$this->handle_ajax_register_validate_otp( $_REQUEST );
				break;
		}
	}

	/**
	 * This function validates the OTP entered by user.
	 *
	 * @param int $data Request array.
	 */
	public function handle_ajax_register_validate_otp( $data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return;
		}

		if ( strcmp( $_SESSION['phone_number_mo'], $data['billing_phone'] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'PHONE_MISMATCH' ), 'error' ) );
		} else {
			do_action( 'smsalert_validate_otp', 'phone' );
		}
	}

	/**
	 * This function displays a OTP button on registration form.
	 */
	public static function smsalert_display_registerOTP_btn() {
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
		echo wp_kses_post( '<button type="submit" class="woocommerce-Button button smsalert_register_with_otp sa-otp-btn-init" name="register" value="' . __( 'Register', 'sms-alert' ) . '" ><span class="button__text">' . __( 'Register', 'sms-alert' ) . '</span></button>' );

		echo '<script>
		jQuery("[name=register]").not(".smsalert_register_with_otp").hide();
		</script>';
	}

	/**
	 * This function enqueues scripts on website.
	 */
	public function enqueue_reg_js_script() {
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
				'login_with_otp'          => ( 'on' === $enabled_login_with_otp ? true : false ),
				'buyer_login_otp'         => ( 'on' === $default_login_otp ? true : false ),
				'hide_default_login_form' => smsalert_get_option( 'hide_default_login_form', 'smsalert_general' ),
			)
		);
		wp_localize_script(
			'smsalert-auth',
			'smsalert_wpml',
			array(
				'lang' => apply_filters( 'wpml_current_language', null ),
			)
		);
		wp_enqueue_script( 'smsalert-auth' );
	}

	/**
	 * This function add registration OTP popup.
	 */
	public function add_modal_html_register_otp() {
		$otp_resend_timer   = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
		$otp_template_style = smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php' );
		get_smsalert_template( 'template/' . $otp_template_style, $params = array() );
		echo '<div id="register_with_otp_extra_fields"><input type="hidden" name="register" value="Register"></div>';
		$this->enqueue_reg_js_script();
	}

	/**
	 * This function is executed after a user is created.
	 *
	 * @param int   $user_id User id of the user.
	 * @param array $data User object.
	 */
	public static function wc_user_created( $user_id, $data ) {
		$billing_phone = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : null;
		$billing_phone = SmsAlertcURLOTP::checkPhoneNos( $billing_phone );
		update_user_meta( $user_id, 'billing_phone', $billing_phone );
		do_action( 'smsalert_after_update_new_user_phone', $user_id, $billing_phone );
	}

	/**
	 * This function shows error message.
	 *
	 * @param int    $error_hook Error hook.
	 * @param string $err_msg Error message.
	 * @param string $type Type.
	 */
	public function show_error_msg( $error_hook = null, $err_msg = null, $type = null ) {
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( $err_msg, $type ) );
		} else {
			return new WP_Error( $error_hook, $err_msg );
		}
	}

	/**
	 * This function shows registration error message.
	 *
	 * @param array  $errors Errors array.
	 * @param string $username Username.
	 * @param string $email Email Id.
	 *
	 * @throws Exception Validation errors.
	 */
	public function woocommerce_site_registration_errors( $errors, $username, $email ) {
		SmsAlertUtility::checkSession();
		if ( isset( $_SESSION['sa_mobile_verified'] ) ) {
			unset( $_SESSION['sa_mobile_verified'] );
			return $errors;
		}
		$password = ! empty( $_REQUEST['password'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['password'] ) ) : '';
		if ( ! SmsAlertUtility::isBlank( array_filter( $errors->errors ) ) ) {
			return $errors;
		}
		if ( isset( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) === 'smsalert_register_with_otp' ) ) {
			SmsAlertUtility::initialize_transaction( $this->form_session_var2 );
		} else {
			SmsAlertUtility::initialize_transaction( $this->form_session_var );
		}

		$user_phone = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';

		if ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) !== 'on' && ! SmsAlertUtility::isBlank( $user_phone ) ) {

			$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $user_phone );
			if ( count( $getusers ) > 0 ) {
				return new WP_Error( 'registration-error-number-exists', __( 'An account is already registered with this mobile number. Please login.', 'woocommerce' ) );
			}
		}

		if ( isset( $user_phone ) && SmsAlertUtility::isBlank( $user_phone ) ) {
			return new WP_Error( 'registration-error-invalid-phone', __( 'Please enter phone number.', 'woocommerce' ) );
		}

		do_action( 'woocommerce_register_post', $username, $email, $errors );

		if ( $errors->get_error_code() ) {
			throw new Exception( $errors->get_error_message() );
		}

		return $this->processFormFields( $username, $email, $errors, $password );
	}

	/**
	 * This function processed form fields.
	 *
	 * @param string $username User name.
	 * @param string $email Email Id.
	 * @param array  $errors Errors array.
	 * @param string $password Password.
	 */
	public function processFormFields( $username, $email, $errors, $password ) {
		global $phoneLogic;
		$phone_no  = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
		$phone_num = preg_replace( '/[^0-9]/', '', $phone_no );

		if ( ! isset( $phone_num ) || ! SmsAlertUtility::validatePhoneNumber( $phone_num ) ) {
			return new WP_Error( 'billing_phone_error', str_replace( '##phone##', $phone_num, $phoneLogic->_get_otp_invalid_format_message() ) );
		}
		smsalert_site_challenge_otp( $username, $email, $errors, $phone_num, 'phone', $password );
	}

	/**
	 * This function adds a phone field.
	 */
	public function smsalert_add_phone_field() {
		if ( is_account_page() || ! is_plugin_active( 'dc-woocommerce-multi-vendor/dc_product_vendor.php' ) ) {
			$phone_num = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
			
			global $allowedposttags;
			
			$allowedposttags['input'] = array(
					'type'      => array(),
					'name'      => array(),
					'value'     => array(),
					'class'   	=> array(),
					'id'   	    => array(),
				);
			
			echo wp_kses(
				'<p class="form-row form-row-wide">
						<label for="reg_billing_phone">' . SmsAlertMessages::showMessage( 'Phone' ) . '<span class="required">*</span><input type="tel" class="input-text phone-valid" name="billing_phone" id="reg_billing_phone" value="' . $phone_num . '" /></label>
						
				</p>',
				$allowedposttags
				
			);
		}
	}

	/**
	 * This function adds phone field to wcmp form.
	 */
	public function smsalert_add_wcmp_phone_field() {

		$phone_num = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';

		'<p class="form-row form-row-wide">
				<label for="reg_billing_phone">' . SmsAlertMessages::showMessage( 'Phone' ) . '<span class="required">*</span></label>
				<input type="tel" class="input-text phone-valid" name="billing_phone" id="reg_billing_phone" value="' . ( ! empty( $phone_num ) ? $phone_num : '' ) . '" />
		</p>';
	}

	/**
	 * This function adds phone field to Dokan form.
	 */
	public function smsalert_add_dokan_phone_field() {
		$phone_num = ! empty( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';

		global $allowedposttags;
			
		$allowedposttags['input'] = array(
				'type'      => array(),
				'name'      => array(),
				'value'     => array(),
				'class'   	=> array(),
				'id'   	    => array(),
			);
		
		echo wp_kses(
			'<p class="form-row form-row-wide">
					<label for="reg_billing_phone">' . SmsAlertMessages::showMessage( 'Phone' ) . '<span class="required">*</span></label>
					<input type="tel" class="input-text phone-valid" name="billing_phone" id="reg_billing_phone" value="' . ( ! empty( $phone_num ) ? $phone_num : '' ) . '" /></p>',
			$allowedposttags
			
		);
		?>
	<script>
		jQuery( window ).on('load', function() {
			jQuery('.user-role input[type="radio"]').change(function(e){
				if(jQuery(this).val() == "seller") {
					jQuery('#reg_billing_phone').parent().hide();
				}
				else {
					jQuery('#reg_billing_phone').parent().show();
				}
			});
			jQuery( "#shop-phone" ).change(function() {
				jQuery('#reg_billing_phone').val(this.value);
			});
		});
	</script>
		<?php
	}

	/**
	 * This function is executed on dokan vendor registration form.
	 */
	public function smsalert_add_dokan_vendor_reg_field() {
		?>
		<script>
			jQuery('#reg_billing_phone').parent().hide();
		</script>
		<?php
	}

	/**
	 * This function handles the failed verification.
	 *
	 * @param string $user_login User login.
	 * @param string $user_email Email Id.
	 * @param string $phone_number Phone number.
	 */
	public function handle_failed_verification( $user_login, $user_email, $phone_number ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return;
		}
		if ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertUtility::_get_invalid_otp_method(), 'phone', false );
		}
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
		}
	}

	/**
	 * This function is executed after verification code is executed.
	 *
	 * @param string $redirect_to Url to be redirected to.
	 * @param string $user_login User login.
	 * @param string $user_email Email Id.
	 * @param string $password Password.
	 * @param string $phone_number Phone number.
	 * @param array  $extra_data Extra fields of the form.
	 */
	public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return;
		}
		$_SESSION['sa_mobile_verified'] = true;
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
		}
	}

	/**
	 * This function removes otp session.
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->tx_session_id ] );
		unset( $_SESSION[ $this->form_session_var ] );
		unset( $_SESSION[ $this->form_session_var2 ] );
	}

	/**
	 * This function checks if the ajax form is activated or not.
	 *
	 * @param bool $is_ajax whether this is an ajax request or not.
	 */
	public function is_ajax_form_in_play( $is_ajax ) {
		SmsAlertUtility::checkSession();
		return isset( $_SESSION[ $this->form_session_var2 ] ) ? true : $is_ajax;
	}

	/**
	 * This function handles form options.
	 */
	public function handleFormOptions() {
		add_action( 'sa_addTabs', array( $this, 'addTabs' ), 10 );
		add_filter( 'sAlertDefaultSettings', array( $this, 'addDefaultSetting' ), 1 );
	}

	/**
	 * This function adds tabs.
	 *
	 * @param array $tabs Default tabs.
	 */
	public static function addTabs( $tabs = array() ) {
		$signup_param = array(
			'checkTemplateFor' => 'signup_temp',
			'templates'        => self::getSignupTemplates(),
		);

		$new_user_reg_param = array(
			'checkTemplateFor' => 'new_user_reg_temp',
			'templates'        => self::getNewUserRegisterTemplates(),
		);

		$tabs['user_registration']['nav']  = 'User Registration';
		$tabs['user_registration']['icon'] = 'dashicons-admin-users';

		$tabs['user_registration']['inner_nav']['wc_register']['title']        = __( 'Sign Up Notifications', 'sms-alert' );
		$tabs['user_registration']['inner_nav']['wc_register']['tab_section']  = 'signup_templates';
		$tabs['user_registration']['inner_nav']['wc_register']['first_active'] = true;

		$tabs['user_registration']['inner_nav']['wc_register']['tabContent'] = $signup_param;
		$tabs['user_registration']['inner_nav']['wc_register']['filePath']   = 'views/message-template.php';

		$tabs['user_registration']['inner_nav']['wc_register']['icon']   = 'dashicons-admin-users';
		$tabs['user_registration']['inner_nav']['wc_register']['params'] = $signup_param;

		$tabs['user_registration']['inner_nav']['new_user_reg']['title']       = 'Admin Notifications';
		$tabs['user_registration']['inner_nav']['new_user_reg']['tab_section'] = 'newuserregtemplates';
		$tabs['user_registration']['inner_nav']['new_user_reg']['tabContent']  = $new_user_reg_param;
		$tabs['user_registration']['inner_nav']['new_user_reg']['filePath']    = 'views/message-template.php';
		$tabs['user_registration']['inner_nav']['new_user_reg']['params']      = $new_user_reg_param;

		return $tabs;
	}

	/**
	 * This function Adds default settings in configuration.
	 *
	 * @param array $defaults Default values.
	 */
	public static function addDefaultSetting( $defaults = array() ) {
		$sms_body_registration_admin_msg = smsalert_get_option( 'sms_body_registration_admin_msg', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_ADMIN_NEW_USER_REGISTER' ) );

		$wc_user_roles = self::get_user_roles();
		foreach ( $wc_user_roles as $role_key => $role ) {
			$defaults['smsalert_signup_general'][ 'wc_user_roles_' . $role_key ]   = 'off';
			$defaults['smsalert_signup_message'][ 'signup_sms_body_' . $role_key ] = $sms_body_registration_admin_msg;
		}
		return $defaults;
	}

	/**
	 * This function gets role display name from system name.
	 *
	 * @param bool $system_name System name of the role.
	 */
	public static function get_user_roles( $system_name = null ) {
		global $wp_roles;
		$roles = $wp_roles->roles;

		if ( ! empty( $system_name ) && array_key_exists( $system_name, $roles ) ) {
			return $roles[ $system_name ]['name'];
		} else {
			return $roles;
		}
	}

	/**
	 * Gets signup template.
	 */
	public static function getSignupTemplates() {
		$wc_user_roles = self::get_user_roles();

		$variables = array(
			'[username]'      => 'Username',
			'[store_name]'    => 'Store Name',
			'[email]'         => 'Email',
			'[billing_phone]' => 'Billing Phone',
			'[shop_url]'      => 'Shop Url',
		);

		$templates = array();
		foreach ( $wc_user_roles as $role_key  => $role ) {
			$current_val = smsalert_get_option( 'wc_user_roles_' . $role_key, 'smsalert_signup_general', 'on' );

			$checkbox_name_id = 'smsalert_signup_general[wc_user_roles_' . $role_key . ']';
			$textarea_name_id = 'smsalert_signup_message[signup_sms_body_' . $role_key . ']';
			$text_body        = smsalert_get_option( 'signup_sms_body_' . $role_key, 'smsalert_signup_message', SmsAlertMessages::showMessage( 'DEFAULT_NEW_USER_REGISTER' ) );

			$templates[ $role_key ]['title']          = 'When ' . ucwords( $role['name'] ) . ' is registered';
			$templates[ $role_key ]['enabled']        = $current_val;
			$templates[ $role_key ]['status']         = $role_key;
			$templates[ $role_key ]['text-body']      = $text_body;
			$templates[ $role_key ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $role_key ]['textareaNameId'] = $textarea_name_id;
			$templates[ $role_key ]['token']          = $variables;
		}
		return $templates;
	}

	/**
	 * Gets new user registration template.
	 */
	public static function getNewUserRegisterTemplates() {
		$smsalert_notification_reg_admin_msg = smsalert_get_option( 'admin_registration_msg', 'smsalert_general', 'on' );
		$sms_body_registration_admin_msg     = smsalert_get_option( 'sms_body_registration_admin_msg', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_ADMIN_NEW_USER_REGISTER' ) );

		$templates = array();

		$new_user_variables = array(
			'[username]'      => 'Username',
			'[store_name]'    => 'Store Name',
			'[email]'         => 'Email',
			'[billing_phone]' => 'Billing Phone',
			'[role]'          => 'Role',
			'[shop_url]'      => 'Shop Url',
		);

		$templates['new-user']['title']          = 'When a new user is registered';
		$templates['new-user']['enabled']        = $smsalert_notification_reg_admin_msg;
		$templates['new-user']['status']         = 'new-user';
		$templates['new-user']['text-body']      = $sms_body_registration_admin_msg;
		$templates['new-user']['checkboxNameId'] = 'smsalert_general[admin_registration_msg]';
		$templates['new-user']['textareaNameId'] = 'smsalert_message[sms_body_registration_admin_msg]';
		$templates['new-user']['token']          = $new_user_variables;

		return $templates;
	}
}
new WooCommerceRegistrationForm();
