<?php
/**
 * Edd helper.
 *
 * @package Helper
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
	return;}

require_once WP_PLUGIN_DIR . '/easy-digital-downloads/includes/payments/class-edd-payment.php';

/**SmsAlertLearnPress class */
class SmsAlertEdd {
	/**Construct function.*/
	public function __construct() {
		add_filter( 'sAlertDefaultSettings', __CLASS__ . '::addDefaultSetting', 1 );
		add_action( 'edd_purchase_form_user_info', __CLASS__ . '::smsalert_edd_display_checkout_fields' );
		add_action( 'edd_checkout_error_checks', __CLASS__ . '::smsalert_edd_validate_checkout_fields', 10, 2 );
		add_filter( 'edd_purchase_form_required_fields', __CLASS__ . '::smsalert_edd_required_checkout_fields' );
		add_filter( 'edd_payment_meta', __CLASS__ . '::smsalert_edd_store_custom_fields' );
		add_action( 'edd_payment_personal_details_list', __CLASS__ . '::smsalert_edd_view_order_details', 10, 2 );
		add_action( 'edd_add_email_tags', __CLASS__ . '::smsalert_edd_add_phone_tag' );
		add_filter( 'edd_update_payment_status', __CLASS__ . '::trigger_after_update_edd_status' );
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 10 );
		add_action( 'edd_complete_purchase', __CLASS__ . '::trigger_after_update_edd_status' );
	}

	/**
	 * Add tabs to smsalert settings at backend.
	 * 
	 * @param array $tabs tabs.
	 * 
	 * @return array 
	 */
	public static function add_tabs( $tabs = array() ) {
		$customer_param = array(
			'checkTemplateFor' => 'edd_customer',
			'templates'        => self::get_customer_templates(),
		);

		$admin_param = array(
			'checkTemplateFor' => 'edd_admin',
			'templates'        => self::get_admin_templates(),
		);

		$tabs['edd']['nav']  = 'Easy Digital Downloads';
		$tabs['edd']['icon'] = 'dashicons-download';

		$tabs['edd']['inner_nav']['edd_customer']['title']        = 'Customer Notifications';
		$tabs['edd']['inner_nav']['edd_customer']['tab_section']  = 'eddcsttemplates';
		$tabs['edd']['inner_nav']['edd_customer']['first_active'] = true;
		$tabs['edd']['inner_nav']['edd_customer']['tabContent']   = $customer_param;
		$tabs['edd']['inner_nav']['edd_customer']['filePath']     = 'views/message-template.php';

		$tabs['edd']['inner_nav']['edd_admin']['title']       = 'Admin Notifications';
		$tabs['edd']['inner_nav']['edd_admin']['tab_section'] = 'eddadmintemplates';
		$tabs['edd']['inner_nav']['edd_admin']['tabContent']  = $admin_param;
		$tabs['edd']['inner_nav']['edd_admin']['filePath']    = 'views/message-template.php';
		return $tabs;
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 * 
	 * @param array $defaults defaults.
	 * 
	 * @return array
	 */
	public function addDefaultSetting( $defaults = array() ) {
		$edd_order_statuses = is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ? edd_get_payment_statuses() : array();

		foreach ( $edd_order_statuses as $ks => $vs ) {
			$defaults['smsalert_edd_general'][ 'edd_admin_notification_' . $vs ] = 'off';
			$defaults['smsalert_edd_general'][ 'edd_order_status_' . $vs ]       = 'off';
			$defaults['smsalert_edd_message'][ 'edd_admin_sms_body_' . $vs ]     = '';
			$defaults['smsalert_edd_message'][ 'edd_sms_body_' . $vs ]           = '';
		}
		return $defaults;
	}

	/**
	 * Get customer templates.
	 * 
	 * @return array 
	 */
	public static function get_customer_templates() {
		$edd_order_statuses = is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ? edd_get_payment_statuses() : array();

		$templates = array();
		foreach ( $edd_order_statuses as $ks  => $vs ) {
			$current_val = smsalert_get_option( 'edd_order_status_' . $vs, 'smsalert_edd_general', 'on' );

			$checkbox_name_id = 'smsalert_edd_general[edd_order_status_' . str_replace( ' ', '_', $vs ) . ']';
			$textarea_name_id = 'smsalert_edd_message[edd_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option(
				'edd_sms_body_' . $vs,
				'smsalert_edd_message',
				SmsAlertMessages::showMessage( 'DEFAULT_EDD_BUYER_SMS_STATUS_CHANGED' )
			);

			$templates[ $ks ]['title']          = 'When Order is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = str_replace( ' ', '_', $vs );
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::get_edd_variables();
		}
		return $templates;
	}

	/**
	 * Get admin templates.
	 * 
	 * @return array 
	 */
	public static function get_admin_templates() {
		$edd_order_statuses = is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ? edd_get_payment_statuses() : array();

		$templates = array();
		foreach ( $edd_order_statuses as $ks  => $vs ) {

			$current_val = smsalert_get_option( 'edd_admin_notification_' . $vs, 'smsalert_edd_general', 'on' );

			$checkbox_name_id = 'smsalert_edd_general[edd_admin_notification_' . str_replace( ' ', '_', $vs ) . ']';
			$textarea_name_id = 'smsalert_edd_message[edd_admin_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option(
				'edd_admin_sms_body_' . $vs,
				'smsalert_edd_message',
				SmsAlertMessages::showMessage( 'DEFAULT_EDD_ADMIN_SMS_STATUS_CHANGED' )
			);

			$templates[ $ks ]['title']          = 'When Order is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = str_replace( ' ', '_', $vs );
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::getEDDVariables();
		}
		return $templates;
	}


	/**
	 * Edd plugins add phone number.
	 * 
	 * @return void
	 */
	public static function smsalert_edd_display_checkout_fields() {

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
		}
		$billing_phone = is_user_logged_in() ? $current_user->billing_phone : '';
		?>
		<p id="edd-phone-wrap">
			<label class="edd-label" for="edd-phone">Phone Number</label>
			<span class="edd-description">
				Enter your phone number so we can get in touch with you.
			</span>
			<input class="edd-input" type="text" name="billing_phone" id="edd-phone" placeholder="Phone Number" value="<?php echo esc_attr( $billing_phone ); ?>" />
		</p>
		<?php
	}

	/**
	 * Make phone number required.
	 * Add more required fields here if you need to.
	 * 
	 * @param array $required_fields required_fields.
	 * 
	 * @return array
	 */
	public static function smsalert_edd_required_checkout_fields( $required_fields ) {
		$required_fields['billing_phone'] = array(
			'error_id'      => 'invalid_phone',
			'error_message' => 'Please enter a valid Phone number',
		);
		return $required_fields;
	}

	/**
	 * Set error if phone number field is empty.
	 * You can do additional error checking here if required.
	 * 
	 * @param array $valid_data valid_data.
	 * @param array $data data.
	 * 
	 * @return void
	 */
	public static function smsalert_edd_validate_checkout_fields( $valid_data, $data ) {
		if ( empty( $data['billing_phone'] ) ) {
			edd_set_error( 'invalid_phone', 'Please enter your phone number.' );
		}
	}

	/**
	 * Store the custom field data into EDD's payment meta.
	 * 
	 * @param array $payment_meta payment_meta.
	 * 
	 * @return array
	 */
	public static function smsalert_edd_store_custom_fields( $payment_meta ) {

		if ( did_action( 'edd_purchase' ) ) {
			$payment_meta['phone'] = isset( $_POST['billing_phone'] ) ? sanitize_text_field( $_POST['billing_phone'] ) : '';
		}
		return $payment_meta;
	}

	/**
	 * Add the phone number to the "View Order Details" page.
	 * 
	 * @param array $payment_meta payment_meta.
	 * @param array $user_info user_info.
	 * 
	 * @return void
	 */
	public static function smsalert_edd_view_order_details( $payment_meta, $user_info ) {
		$phone = isset( $payment_meta['phone'] ) ? $payment_meta['phone'] : 'none';
		?>
		<div class="column-container">
			<div class="column">
				<strong>Phone: </strong>
				<?php echo esc_attr( $phone ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add a {phone} tag for use in either the purchase receipt email or admin notification emails.
	 * 
	 * @return void
	 */
	public static function smsalert_edd_add_phone_tag() {
		edd_add_email_tag( 'phone', 'Customer\'s phone number', 'smsalert_edd_tag_phone' );
	}

	/**
	 * The {phone} email tag.
	 * 
	 * @return string
	 */
	public static function smsalert_edd_tag_phone( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		return $payment_data['phone'];
	}

	/**
	 * Edd plugins add phone number ends.
	 * 
	 * @return array
	 */
	public static function get_edd_variables() {
		$variables = array(
			'[order_id]'            => 'Order Id',
			'[order_status]'        => 'Order Status',
			'[edd_payment_total]'   => 'Order amount',
			'[store_name]'          => 'Store Name',
			'[edd_payment_mode]'    => 'Payment Mode',
			'[edd_payment_gateway]' => 'Payment Gateway',
			'[first_name]'          => 'Billing First Name',
			'[last_name]'           => 'Billing Last Name',
			'[item_name]'           => 'Item Name',
			'[currency]'            => 'Currency',
			'[download_url]'        => 'Download Url',
		);
		return $variables;
	}

	/**
	 * Send sms after payment actions.
	 * 
	 * @param int $payment_id payment_id.
	 * 
	 * @return string
	 */
	public static function get_edd_file_download_url( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		$file_urls    = '';
		$cart_items   = edd_get_payment_meta_cart_details( $payment_id );
		$email        = edd_get_payment_user_email( $payment_id );

		foreach ( $cart_items as $item ) {

			$price_id = edd_get_cart_item_price_id( $item );
			$files    = edd_get_download_files( $item['id'], $price_id );

			if ( $files ) {
				foreach ( $files as $filekey => $file ) {
					$file_url = edd_get_download_file_url( $payment_data['key'], $email, $filekey, $item['id'], $price_id );

					$file_urls .= $file_url . '';
				}
			} elseif ( edd_is_bundled_product( $item['id'] ) ) {

				$bundled_products = edd_get_bundled_products( $item['id'] );

				foreach ( $bundled_products as $bundle_item ) {

					$files = edd_get_download_files( $bundle_item );
					foreach ( $files as $filekey => $file ) {
						$file_url   = edd_get_download_file_url( $payment_data['key'], $email, $filekey, $bundle_item, $price_id );
						$file_urls .= $file_url . '';
					}
				}
			}
		}
		return $file_urls;
	}

	/**
	 * Trigger after update edd status.
	 * 
	 * @param int $payment_id payment_id.
	 * 
	 * @return void
	 */
	public static function trigger_after_update_edd_status( $payment_id ) {
		$payments  = new EDD_Payment( $payment_id );
		$status     = edd_get_payment_status( $payment_id, true );
		$admin_send = smsalert_get_option( 'edd_admin_notification_' . $status, 'smsalert_edd_general' );
		$cst_send   = smsalert_get_option( 'edd_order_status_' . $status, 'smsalert_edd_general' );

		if ( 'on' === $cst_send ) {
			$content = smsalert_get_option( 'edd_sms_body_' . $status, 'smsalert_edd_message' );
			$content = self::pharse_sms_body( $content, $payment_id );
			$meta    = $payments->get_meta();

			if ( array_key_exists( 'phone', $meta ) && '' !== $meta['phone'] ) {
				$edd_data             = array();
				$edd_data['number']   = $meta['phone'];
				$edd_data['sms_body'] = $content;
				SmsAlertcURLOTP::sendsms( $edd_data );
			}
		}

		if ( 'on' === $admin_send ) {
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			$nos                = explode( ',', $admin_phone_number );
			$admin_phone_number = array_diff( $nos, array( 'postauthor', 'post_author' ) );
			$admin_phone_number = implode( ',', $admin_phone_number );

			$content = smsalert_get_option( 'edd_admin_sms_body_' . $status, 'smsalert_edd_message' );
			$content = self::pharse_sms_body( $content, $payment_id );
			if ( '' !== $admin_phone_number ) {
				$edd_data             = array();
				$edd_data['number']   = $admin_phone_number;
				$edd_data['sms_body'] = $content;
				SmsAlertcURLOTP::sendsms( $edd_data );
				// do_action('sa_send_sms', $admin_phone_number, $content);
			}
		}
	}

	/**
	 * Parse sms body.
	 * 
	 * @param string $content content.
	 * @param int $payment_id payment_id.
	 * 
	 * @return string
	 */
	public static function pharse_sms_body( $content, $payment_id ) {
		$payments        = new EDD_Payment( $payment_id );
		$user_info       = $payments->get_meta();
		$order_variables = get_post_custom( $payment_id );
		$order_status    = edd_get_payment_status( $payment_id, true );

		$variables = array(
			'[order_id]'     => $payment_id,
			'[order_status]' => $order_status,
			'[first_name]'   => $user_info['user_info']['first_name'],
			'[last_name]'    => $user_info['user_info']['last_name'],
			'[download_url]' => self::get_edd_file_download_url( $payment_id ),
		);
		$content   = str_replace( array_keys( $variables ), array_values( $variables ), $content );

		foreach ( $order_variables as &$value ) {
			$value = $value[0];
		}
		unset( $value );

		$order_variables = array_combine(
			array_map(
				function( $key ) {
					return '[' . ltrim( $key, '_' ) . ']'; },
				array_keys( $order_variables )
			),
			$order_variables
		);
		$content         = str_replace( array_keys( $order_variables ), array_values( $order_variables ), $content );
		return $content;
	}
}
new SmsAlertEdd();
