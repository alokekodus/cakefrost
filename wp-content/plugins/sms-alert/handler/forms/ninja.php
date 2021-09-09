<?php
/**
 * This file handles ninja form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
	return; }

/**
 * SmsAlertNinjaForms class.
 */
class SmsAlertNinjaForms extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::NF_FORMS;

	/**
	 * Form session Phone Variable.
	 *
	 * @var stirng
	 */
	private $form_phone_ver = FormSessionVars::NF_PHONE_VER;

	/**
	 * Phone Form id.
	 *
	 * @var stirng
	 */
	private $phone_form_id;

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_action( 'ninja_forms_after_form_display', array( $this, 'enqueue_nj_form_script' ), 100 );

		add_action( 'ninja_forms_localize_field_settings_submit', array( $this, 'add_custom_button' ), 99, 2 );

		add_action( 'ninja_forms_localize_field_settings_phone', array( $this, 'add_class_phone_field' ), 99, 2 );

		add_action( 'ninja_forms_after_submission', __CLASS__ . '::smsalert_send_sms_form_submit', 10, 1 );

		$this->routeData();
	}

	/**
	 * Add additional js code to your script
	 *
	 * @return void
	 */
	public function sa_ninja_handle_js_script() {
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );

		echo '<script>

		jQuery(document).on("click", ".nf-form-cont .sa-otp-btn-init",function(event){
			event.stopImmediatePropagation();
			jQuery(this).parents(".smsalertModal").hide();
			var e = jQuery(this).parents("form").find(".sa-phone-field").val();
			var data = {user_phone:e};
			var action_url = "' . site_url() . '/?option=smsalert-nj-ajax-verify";
			saInitOTPProcess(this,action_url, data,' . $otp_resend_timer . ');
		});

		jQuery(document).on("click", ".nf-form-cont .smsalert_otp_validate_submit",function(event){
			event.stopImmediatePropagation();
			var current_form = jQuery(this).parents("form");
			var action_url = "' . site_url() . '/?option=smsalert-validate-otp-form";
			var data = current_form.serialize()+"&otp_type=phone&from_both=";
			sa_validateOTP(this,action_url,data,function(){
				current_form.find(".sa-hide").trigger("click")
			});
			return false;
		});
		</script>';
	}

	/**
	 * Add Class to Phone field in ninja form for frontend.
	 *
	 * @param array $settings ninja current form field settings.
	 * @param array $form ninja form.
	 *
	 * @return array
	 */
	public function add_class_phone_field( $settings, $form ) {
		$form_id            = $form->get_id();
		$form_enable        = smsalert_get_option( 'ninja_order_status_' . $form_id, 'smsalert_ninja_general', 'on' );
		$otp_enable         = smsalert_get_option( 'ninja_otp_' . $form_id, 'smsalert_ninja_general', 'on' );
		$phone_field        = smsalert_get_option( 'ninja_sms_phone_' . $form_id, 'smsalert_ninja_general', '' );
		$otp_template_style = smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php' );

		if ( $settings['key'] === $phone_field ) {
			$settings['element_class'] = 'sa-phone-field phone-valid';
		}
		return $settings;
	}

	/**
	 * Add custom button for starting the otp.
	 *
	 * @param array $settings ninja current form field settings.
	 * @param array $form ninja form.
	 *
	 * @return array
	 */
	public function add_custom_button( $settings, $form ) {
		$form_id            = $form->get_id();
		$form_enable        = smsalert_get_option( 'ninja_order_status_' . $form_id, 'smsalert_ninja_general', 'on' );
		$otp_enable         = smsalert_get_option( 'ninja_otp_' . $form_id, 'smsalert_ninja_general', 'on' );
		$phone_field        = smsalert_get_option( 'ninja_sms_phone_' . $form_id, 'smsalert_ninja_general', '' );
		$otp_template_style = smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php' );
		if ( 'on' === $form_enable && 'on' === $otp_enable ) {
			$settings['element_class'] = 'sa-hide';

			$settings['afterField'] = '
				<div id="nf-field-4-container" class="nf-field-container submit-container  label-above ">
					<div class="nf-before-field">
						<nf-section></nf-section>
					</div>
					<div class="nf-field">
						<div class="field-wrap submit-wrap">
							<div class="nf-field-label"></div>
							<div class="nf-field-element">
								<input class="sa-otp-btn-init ninja-forms-field nf-element smsalert_otp_btn_submit" value="' . __( 'Verify & Submit', 'sms-alert' ) . '" type="button">
							</div>
						</div>
					</div>
				<style>.sa-hide{display:none !important}</style>
				';

			$settings['afterField'] .= get_smsalert_template( 'template/' . $otp_template_style, $params = array(), true );
			$settings['afterField'] .= $this->sa_ninja_handle_js_script();
		}
		return $settings;
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
			case 'smsalert-nj-ajax-verify':
				$this->send_otp_nj_ajax_verify( $_POST );
				exit();
			break;
		}
	}

	/**
	 * Verify Otp submitted through ajax form submission.
	 *
	 * @param array $getdata posted data by user.
	 *
	 * @return void
	 */
	public function send_otp_nj_ajax_verify( $getdata ) {
		SmsAlertUtility::checkSession();
		SmsAlertUtility::initialize_transaction( $this->form_session_var );

		if ( array_key_exists( 'user_phone', $getdata ) && ! SmsAlertUtility::isBlank( $getdata['user_phone'] ) ) {
			$_SESSION[ $this->form_phone_ver ] = trim( $getdata['user_phone'] );
			$message                           = str_replace( '##phone##', $getdata['user_phone'], SmsAlertMessages::showMessage( 'OTP_SENT_PHONE' ) );
			smsalert_site_challenge_otp( 'test', null, null, trim( $getdata['user_phone'] ), 'phone', null, null, true );
		} else {
			wp_send_json( SmsAlertUtility::_create_json_response( __( 'Enter a number in the following format : 9xxxxxxxxx', 'sms-alert' ), SmsAlertConstants::ERROR_JSON_TYPE ) );
		}
	}

	/**
	 * Add Js code to your script
	 *
	 * @return void
	 */
	public function enqueue_nj_form_script() {
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
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public function isFormEnabled() {
		return is_plugin_active( 'ninja-forms/ninja-forms.php' ) ? true : false;
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
		unset( $_SESSION[ $this->form_session_var ] );
		unset( $_SESSION[ $this->form_phone_ver ] );
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
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {
		if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
			add_filter( 'sAlertDefaultSettings', __CLASS__ . '::addDefaultSetting', 1, 2 );
			add_action( 'sa_addTabs', array( $this, 'addTabs' ), 10 );
		}
	}

	/**
	 * Add tabs to smsalert settings at backend
	 *
	 * @param array $tabs list of tabs data.
	 * @return array
	 */
	public static function addTabs( $tabs = array() ) {
		$tabs['ninja']['nav']  = 'Ninja Form';
		$tabs['ninja']['icon'] = 'dashicons-list-view';

		$tabs['ninja']['inner_nav']['ninja_admin']['title']        = 'Admin Notifications';
		$tabs['ninja']['inner_nav']['ninja_admin']['tab_section']  = 'ninjaadmintemplates';
		$tabs['ninja']['inner_nav']['ninja_admin']['first_active'] = true;
		$tabs['ninja']['inner_nav']['ninja_admin']['tabContent']   = array();
		$tabs['ninja']['inner_nav']['ninja_admin']['filePath']     = 'views/ninja_admin_template.php';

		$tabs['ninja']['inner_nav']['ninja_admin']['icon']       = 'dashicons-list-view';
		$tabs['ninja']['inner_nav']['ninja_cust']['title']       = 'Customer Notifications';
		$tabs['ninja']['inner_nav']['ninja_cust']['tab_section'] = 'ninjacsttemplates';
		$tabs['ninja']['inner_nav']['ninja_cust']['tabContent']  = array();
		$tabs['ninja']['inner_nav']['ninja_cust']['filePath']    = 'views/ninja_customer_template.php';

		$tabs['ninja']['inner_nav']['ninja_cust']['icon'] = 'dashicons-admin-users';
		return $tabs;
	}

	/**
	 * Get variables to show variables above sms content template at backend settings.
	 *
	 * @param int $form_id form id.
	 * @return array
	 */
	public static function getNinjavariables( $form_id = null ) {
		$variables = array();
		$form      = Ninja_Forms()->form( $form_id )->get();
		$form_name = $form->get_settings();
		return $form_name['formContentData'];
	}

	/**
	 * Get default settings for the smsalert ninja forms.
	 *
	 * @param array $defaults smsalert backend settings default values.
	 * @return array
	 */
	public static function addDefaultSetting( $defaults = array() ) {
		$wpam_statuses = self::get_ninja_forms();
		foreach ( $wpam_statuses as $ks => $vs ) {
			$defaults['smsalert_ninja_general'][ 'ninja_admin_notification_' . $ks ] = 'off';
			$defaults['smsalert_ninja_general'][ 'ninja_order_status_' . $ks ]       = 'off';
			$defaults['smsalert_ninja_general'][ 'ninja_message_' . $ks ]            = 'off';
			$defaults['smsalert_ninja_message'][ 'ninja_admin_sms_body_' . $ks ]     = '';
			$defaults['smsalert_ninja_message'][ 'ninja_sms_body_' . $ks ]           = '';
			$defaults['smsalert_ninja_general'][ 'ninja_sms_phone_' . $ks ]          = '';
			$defaults['smsalert_ninja_general'][ 'ninja_sms_otp_' . $ks ]            = '';
			$defaults['smsalert_ninja_general'][ 'ninja_otp_' . $ks ]                = '';
			$defaults['smsalert_ninja_message'][ 'ninja_otp_sms_' . $ks ]            = '';
		}
		return $defaults;
	}

	/**
	 * Get ninja forms.
	 *
	 * @return array
	 */
	public static function get_ninja_forms() {
		$ninja_forms = array();
		$forms       = Ninja_Forms()->form()->get_forms();
		foreach ( $forms as $form ) {
			$form_id                 = $form->get_id();
			$ninja_forms[ $form_id ] = $form->get_setting( 'title' );
		}
		return $ninja_forms;
	}

	/**
	 * Replace variables for sms contennt
	 *
	 * @param string $content sms content to be sent.
	 * @param array  $datas values of varibles.
	 *
	 * @return string
	 */
	public static function parse_sms_content( $content = null, $datas = array() ) {
		$find    = array_keys( $datas );
		$replace = array_values( $datas );
		$content = str_replace( $find, $replace, $content );
		return $content;
	}

	/**
	 * Send sms after ninja form submission.
	 *
	 * @param array $form_data posted data from ninja form by user.
	 *
	 * @return void
	 */
	public static function smsalert_send_sms_form_submit( $form_data ) {
		$datas = array();
		if ( ! empty( $form_data ) ) {
			$billing_phone = '';
			$phone_field   = smsalert_get_option( 'ninja_sms_phone_' . $form_data['form_id'], 'smsalert_ninja_general', '' );
			foreach ( $form_data['fields'] as $field ) {
				$datas[ '[' . $field['key'] . ']' ] = $field['value'];
				if ( $field['key'] === $phone_field ) {
					$billing_phone = $field['value'];
				}
			}
			$form_enable      = smsalert_get_option( 'ninja_message_' . $form_data['form_id'], 'smsalert_ninja_general', 'on' );
			$buyer_sms_notify = smsalert_get_option( 'ninja_order_status_' . $form_data['form_id'], 'smsalert_ninja_general', 'on' );
			$admin_sms_notify = smsalert_get_option( 'ninja_admin_notification_' . $form_data['form_id'], 'smsalert_ninja_general', 'on' );

			if ( 'on' === $form_enable && 'on' === $buyer_sms_notify ) {
				if ( ! empty( $billing_phone ) ) {
					$buyer_sms_content = smsalert_get_option( 'ninja_sms_body_' . $form_data['form_id'], 'smsalert_ninja_message', '' );
					do_action( 'sa_send_sms', $billing_phone, self::parse_sms_content( $buyer_sms_content, $datas ) );
				}
			}

			if ( 'on' === $admin_sms_notify ) {

				$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
				$admin_phone_number = str_replace( 'post_author', '', $admin_phone_number );

				if ( ! empty( $admin_phone_number ) ) {
					$admin_sms_content = smsalert_get_option( 'ninja_admin_sms_body_' . $form_data['form_id'], 'smsalert_ninja_message', '' );
					do_action( 'sa_send_sms', $admin_phone_number, self::parse_sms_content( $admin_sms_content, $datas ) );
				}
			}
		}
	}
}
new SmsAlertNinjaForms();
