<?php
/**
 * This file handles wp forms via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_plugin_active( 'wpforms-lite/wpforms.php' ) && ! is_plugin_active( 'wpforms/wpforms.php' ) ) {
	return; }

/**
 * WpForm class.
 */
class WpForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::WPFORM;

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_action( 'wpforms_process_complete', array( $this, 'wpf_dev_process_complete' ), 10, 4 );
		add_filter( 'wpforms_display_field_after', array( $this, 'wpf_dev_process_filter' ), 10, 2 );
		add_action( 'wpforms_form_settings_panel_content', array( $this, 'custom_wpforms_form_settings_panel_content' ), 10, 1 );
		add_filter( 'wpforms_builder_settings_sections', array( $this, 'custom_wpforms_builder_settings_sections' ), 10, 2 );
	}

	/**
	 * Display form phone field after form
	 *
	 * @param array $field form fields.
	 * @param array $form_data form datas.
	 *
	 * @return void
	 */
	public function wpf_dev_process_filter( $field, $form_data ) {
		$msg_enable = $form_data['smsalert']['message_enable'];
		if ( $msg_enable ) {
			$phone_field    = $form_data['settings']['smsalert']['visitor_phone'];
			$phone_field_id = preg_replace( '/[^0-9]/', '', $phone_field );
			if ( $field['id'] === $phone_field_id ) {
				echo do_shortcode( '[sa_verify id="form1" phone_selector="#wpforms-' . esc_attr( $form_data['id'] ) . '-field_' . esc_attr( $field['id'] ) . '" submit_selector= ".wpforms-submit" ]' );
			}
		}
	}

	/**
	 * Add Tab smsalert setting in wpform builder section
	 *
	 * @param array $sections form section.
	 * @param array $form_data form datas.
	 *
	 * @return array
	 */
	public function custom_wpforms_builder_settings_sections( $sections, $form_data ) {
		$sections['smsalert'] = 'SMS Alert';
		return $sections;
	}

	/**
	 * Add Tab panel smsalert setting in wpform builder section
	 *
	 * @param object $instance tab panel object.
	 *
	 * @return void
	 */
	public function custom_wpforms_form_settings_panel_content( $instance ) {
		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-smsalert">';

		echo '<div class="wpforms-panel-content-section-title"><span id="wpforms-builder-settings-notifications-title">SMS Alert Message Configuration</span></div>';
		wpforms_panel_field(
			'select',
			'smsalert',
			'message_enable',
			$instance->form_data,
			__( 'Message', 'sms-alert' ),
			array(
				'default' => '1',
				'options' => array(
					'1' => __( 'On', 'sms-alert' ),
					'0' => __( 'Off', 'sms-alert' ),
				),
			)
		);
		wpforms_panel_field(
			'text',
			'smsalert',
			'admin_number',
			$instance->form_data,
			__( 'Send Admin SMS To', 'sms-alert' ),
			array(
				'default' => '',
				'parent'  => 'settings',
				'after'   => '<p class="note">' .
								__( 'Admin order sms notifications will be send in this number. Enter multiple numbers by comma separated', 'sms-alert' ) . '</p>',
			)
		);
			wpforms_panel_field(
				'textarea',
				'smsalert',
				'admin_message',
				$instance->form_data,
				__( 'Admin Message', 'sms-alert' ),
				array(
					'rows'      => 6,
					'default'   => '[store_name]: Hello admin, a new user has submitted the form.',
					'smarttags' => array(
						'type' => 'all',
					),
					'parent'    => 'settings',
					'class'     => 'email-msg',

				)
			);
			wpforms_panel_field(
				'text',
				'smsalert',
				'visitor_phone',
				$instance->form_data,
				__( 'Select Phone Field', 'sms-alert' ),
				array(
					'default'   => '',
					'smarttags' => array(
						'type' => 'all',
					),
					'parent'    => 'settings',
				)
			);
			wpforms_panel_field(
				'textarea',
				'smsalert',
				'visitor_message',
				$instance->form_data,
				__( 'Visitor Message', 'sms-alert' ),
				array(
					'rows'      => 6,
					'default'   => 'Thank you for contacting us.',
					'smarttags' => array(
						'type' => 'all',
					),
					'parent'    => 'settings',
					'class'     => 'email-msg',
				)
			);
		echo '</div>';
	}


	/**
	 * Process wp form submission and send sms
	 *
	 * @param array $fields form fields.
	 * @param array $entry form entries.
	 * @param array $form_data form data.
	 * @param int   $entry_id entity id.
	 *
	 * @return void
	 */
	public function wpf_dev_process_complete( $fields, $entry, $form_data, $entry_id ) {
		$msg_enable = $form_data['smsalert']['message_enable'];
		if ( $msg_enable ) {
			$phone_field     = $form_data['settings']['smsalert']['visitor_phone'];
			$admin_number    = $form_data['settings']['smsalert']['admin_number'];
			$visitor_message = $form_data['settings']['smsalert']['visitor_message'];
			$admin_message   = $form_data['settings']['smsalert']['admin_message'];
			$phone_field_id  = preg_replace( '/[^0-9]/', '', $phone_field );
			if ( ! empty( $phone_field_id ) ) {
				$phone = '';
				$datas = array();
				foreach ( $fields as $key => $field ) {
					$datas[ '{field_id="' . $key . '"}' ] = $field['value'];
					if ( $phone_field_id === $key ) {
						$phone = $field['value'];
					}
				}
				do_action( 'sa_send_sms', $phone, self::parse_sms_content( $visitor_message, $datas ) );
				if ( ! empty( $admin_number ) ) {
					do_action( 'sa_send_sms', $admin_number, self::parse_sms_content( $admin_message, $datas ) );
				}
			}
		}
	}

	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		return ( is_plugin_active( 'wpforms-lite/wpforms.php' ) || is_plugin_active( 'wpforms/wpforms.php' ) ) ? true : false;
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
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new WpForm();
