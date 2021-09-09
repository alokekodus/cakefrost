<?php
/**
 * Booking calendar helper.
 *
 * @package Helper
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'booking/wpdev-booking.php' ) ) {
	return;
}
/**BookingCalendar class */
class BookingCalendar {
	/**Construct function.*/
	public function __construct() {
		add_filter( 'sAlertDefaultSettings', __CLASS__ . '::addDefaultSetting', 1 );
		add_action( 'wpbc_booking_approved', array( $this, 'sendsms_approved_pending' ), 99, 2 );
		add_action( 'wpdev_new_booking', array( $this, 'sendsms_new_booking' ), 100, 5 );
		add_action( 'wpbc_booking_trash', array( $this, 'sendsms_trash' ), 100, 2 );
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 10 );
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 * 
	 * @param array $defaults defaults.
	 * 
	 * @return array
	 */
	public function addDefaultSetting( $defaults = array() ) {
		$booking_statuses = array( 'new', 'pending', 'approved', 'trash' );

		foreach ( $booking_statuses as $ks => $vs ) {
			$defaults['smsalert_bc_general'][ 'customer_bc_notify_' . $vs ]   = 'off';
			$defaults['smsalert_bc_message'][ 'customer_sms_bc_body_' . $vs ] = '';
			$defaults['smsalert_bc_general'][ 'admin_bc_notify_' . $vs ]      = 'off';
			$defaults['smsalert_bc_message'][ 'admin_sms_bc_body_' . $vs ]    = '';
		}
		return $defaults;
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
			'checkTemplateFor' => 'bc_customer',
			'templates'        => self::get_customer_templates(),
		);

		$admin_param = array(
			'checkTemplateFor' => 'bc_admin',
			'templates'        => self::get_admin_templates(),
		);

		$tabs['booking_calendar']['nav']  = 'Booking Calendar';
		$tabs['booking_calendar']['icon'] = 'dashicons-calendar-alt';

		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['title']        = 'Customer Notifications';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['tab_section']  = 'bookingcalendarcusttemplates';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['first_active'] = true;
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['tabContent']   = $customer_param;
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['filePath']     = 'views/message-template.php';

		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['title']       = 'Admin Notifications';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['tab_section'] = 'bookingcalendaradmintemplates';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['tabContent']  = $admin_param;
		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['filePath']    = 'views/message-template.php';
		return $tabs;
	}

	/**
	 * Get customer templates.
	 * 
	 * @return array 
	 */
	public static function get_customer_templates() {
		$booking_statuses = array(
			// '[new]'       => 'New',
			'[pending]'  => 'Pending',
			'[approved]' => 'Approved',
			'[trash]'    => 'Trash',
		);

		$templates = array();
		foreach ( $booking_statuses as $ks  => $vs ) {
			$current_val = smsalert_get_option( 'customer_bc_notify_' . strtolower( $vs ), 'smsalert_bc_general', 'on' );

			$checkbox_name_id = 'smsalert_bc_general[customer_bc_notify_' . strtolower( $vs ) . ']';
			$textarea_name_id = 'smsalert_bc_message[customer_sms_bc_body_' . strtolower( $vs ) . ']';

			$default_template = SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_CUSTOMER_' . strtoupper( $vs ) );

			$text_body = smsalert_get_option( 'customer_sms_bc_body_' . strtolower( $vs ), 'smsalert_bc_message', ( ( '' !== $default_template ) ? $default_template : SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_CUSTOMER' ) ) );

			$templates[ $ks ]['title']          = 'When customer booking is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $vs;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::get_booking_calendarvariables();
		}
		return $templates;
	}

	/**
	 * Get admin templates.
	 * 
	 * @return array 
	 */
	public static function get_admin_templates() {
		$booking_statuses = array(
			// '[new]'       => 'New',
			'[pending]'  => 'Pending',
			'[approved]' => 'Approved',
			'[trash]'    => 'Trash',
		);

		$templates = array();
		foreach ( $booking_statuses as $ks  => $vs ) {

			$current_val    = smsalert_get_option( 'admin_bc_notify_' . strtolower( $vs ), 'smsalert_bc_general', 'on' );
			$checkboxNameId = 'smsalert_bc_general[admin_bc_notify_' . strtolower( $vs ) . ']';
			$textareaNameId = 'smsalert_bc_message[admin_sms_bc_body_' . strtolower( $vs ) . ']';

			/* translators: %1$s: Store name tag, %2$s: Booking status */
			$text_body = smsalert_get_option( 'admin_sms_bc_body_' . strtolower( $vs ), 'smsalert_bc_message', sprintf( __( '%1$s status of booking has been changed to %2$s.', 'sms-alert' ), '[store_name]:', $vs ) );

			$default_template = SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_ADMIN_' . strtoupper( $vs ) );

			$text_body = smsalert_get_option( 'admin_sms_bc_body_' . strtolower( $vs ), 'smsalert_bc_message', ( ( '' !== $default_template ) ? $default_template : SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_ADMIN' ) ) );

			$templates[ $ks ]['title']          = 'When admin change status to ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $vs;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkboxNameId;
			$templates[ $ks ]['textareaNameId'] = $textareaNameId;
			$templates[ $ks ]['token']          = self::get_booking_calendarvariables();
		}
		return $templates;
	}

	/**
	 * Send sms new booking.
	 * 
	 * @param int $booking_id booking_id 
	 * 
	 * @return void 
	 */
	public function sendsms_new_booking( $booking_id ) {
		if ( function_exists( 'wpbc_api_get_booking_by_id' ) ) {
			$buyer_sms_data = array();
			$booking        = wpbc_api_get_booking_by_id( $booking_id );
			$buyer_number   = $booking['formdata']['phone1'];

			$customer_message   = smsalert_get_option( 'customer_sms_bc_body_pending', 'smsalert_bc_message', '' );
			$customer_bc_notify = smsalert_get_option( 'customer_bc_notify_pending', 'smsalert_bc_general', 'on' );

			if ( 'on' === $customer_bc_notify && '' !== $customer_message ) {
				$buyer_message = $this->parse_sms_body( $booking, $customer_message );
				do_action( 'sa_send_sms', $buyer_number, $buyer_message );
			}

			// send msg to admin
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			$nos                = explode( ',', $admin_phone_number );
			$admin_phone_number = array_diff( $nos, array( 'postauthor', 'post_author' ) );
			$admin_phone_number = implode( ',', $admin_phone_number );

			if ( ! empty( $admin_phone_number ) ) {

				$admin_bc_notify = smsalert_get_option( 'admin_bc_notify_pending', 'smsalert_bc_general', 'on' );
				$admin_message   = smsalert_get_option( 'admin_sms_bc_body_pending', 'smsalert_bc_message', '' );

				if ( 'on' === $admin_bc_notify && '' !== $admin_message ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}
			}
		}
	}

	/**
	 * Send sms approved pending.
	 * 
	 * @param int $booking_id booking_id 
	 * @param int $is_approve_or_pending is_approve_or_pending.
	 * @return void 
	 */
	public function sendsms_approved_pending( $booking_id, $is_approve_or_pending ) {
		if ( function_exists( 'wpbc_api_get_booking_by_id' ) ) {
			$buyer_sms_data = array();
			$booking        = wpbc_api_get_booking_by_id( $booking_id );
			$buyer_number   = $booking['formdata']['phone1'];

			if ( 1 === $booking['is_new'] ) {
				exit();
			}

			if ( 1 === $is_approve_or_pending ) {
				$customer_message = smsalert_get_option( 'customer_sms_bc_body_approved', 'smsalert_bc_message', '' );
			} else {
				$customer_message = smsalert_get_option( 'customer_sms_bc_body_pending', 'smsalert_bc_message', '' );
			}

			$customer_bc_pending_notify  = smsalert_get_option( 'customer_bc_notify_pending', 'smsalert_bc_general', 'on' );
			$customer_bc_approved_notify = smsalert_get_option( 'customer_bc_notify_approved', 'smsalert_bc_general', 'on' );

			if ( ( 'on' === $customer_bc_approved_notify && '' !== $customer_message ) || ( 'on' === $customer_bc_pending_notify && '' !== $customer_message ) ) {
				$buyer_message = $this->parse_sms_body( $booking, $customer_message );
				do_action( 'sa_send_sms', $buyer_number, $buyer_message );
			}

			// send msg to admin
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			if ( ! empty( $admin_phone_number ) ) {

				$smsalert_bc_admin_pending_notify = smsalert_get_option( 'admin_bc_notify_pending', 'smsalert_bc_general', 'on' );
				$smsalert_bc_admin_approve_notify = smsalert_get_option( 'admin_bc_notify_approved', 'smsalert_bc_general', 'on' );

				if ( 1 === $is_approve_or_pending ) {
					$admin_message = smsalert_get_option( 'admin_sms_bc_body_approved', 'smsalert_bc_message', '' );
				} else {
					$admin_message = smsalert_get_option( 'admin_sms_bc_body_pending', 'smsalert_bc_message', '' );
				}

				$nos                = explode( ',', $admin_phone_number );
				$admin_phone_number = array_diff( $nos, array( 'postauthor', 'post_author' ) );
				$admin_phone_number = implode( ',', $admin_phone_number );

				if ( 'on' === $smsalert_bc_admin_pending_notify && '' !== $admin_message && 0 === $is_approve_or_pending ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}

				if ( 'on' === $smsalert_bc_admin_approve_notify && '' !== $admin_message && 1 === $is_approve_or_pending ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}
			}
		}
	}

	/**
	 * Send sms trash.
	 * 
	 * @param int $booking_id booking_id 
	 * @param string $is_trash is_trash.
	 * 
	 * @return void 
	 */
	public function sendsms_trash( $booking_id, $is_trash ) {

		if ( function_exists( 'wpbc_api_get_booking_by_id' ) ) {
			$buyer_sms_data           = array();
			$booking                  = wpbc_api_get_booking_by_id( $booking_id );
			$buyer_sms_data['number'] = $booking['formdata']['phone1'];

			$customer_message   = smsalert_get_option( 'customer_sms_bc_body_trash', 'smsalert_bc_message', '' );
			$customer_bc_notify = smsalert_get_option( 'customer_bc_notify_trash', 'smsalert_bc_general', 'on' );

			if ( 'on' === $customer_bc_notify && '' !== $customer_message ) {
				$buyer_sms_data['sms_body'] = $this->parse_sms_body( $booking, $customer_message );
				SmsAlertcURLOTP::sendsms( $buyer_sms_data );
			}

			// send msg to admin
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			$nos                = explode( ',', $admin_phone_number );
			$admin_phone_number = array_diff( $nos, array( 'postauthor', 'post_author' ) );
			$admin_phone_number = implode( ',', $admin_phone_number );

			if ( ! empty( $admin_phone_number ) ) {

				$admin_bc_notify = smsalert_get_option( 'admin_bc_notify_trash', 'smsalert_bc_general', 'on' );
				$admin_message   = smsalert_get_option( 'admin_sms_bc_body_trash', 'smsalert_bc_message', '' );

				if ( 'on' === $admin_bc_notify && '' !== $admin_message ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}
			}
		}
	}

	/**
	 * Parse sms body.
	 * 
	 * @param array $data data.
	 * @param string $content content.
	 * 
	 * @return string 
	 */
	public function parse_sms_body( $data = array(), $content = null ) {
		$name         = $data['formdata']['name1'];
		$secondname   = $data['formdata']['secondname1'];
		$email        = $data['formdata']['email1'];
		$visitor      = $data['formdata']['visitors1'];
		$phone        = $data['formdata']['phone1'];
		$details      = $data['formdata']['details1'];
		$booking_date = $data['booking_date'];

		$find = array(
			'[name]',
			'[secondname]',
			'[email]',
			'[visitors]',
			'[phone]',
			'[details]',
			'[date]',
		);

		$replace = array(
			$name,
			$secondname,
			$email,
			$visitor,
			$phone,
			$details,
			$booking_date,
		);

		$content = str_replace( $find, $replace, $content );
		return $content;
	}

	/**
	 * Get booking calendar variables.
	 * 
	 * @return array 
	 */
	public static function get_booking_calendarvariables() {
		$variables = wpbc_get_form_fields_free();

		$variable = array();
		foreach ( $variables as $vk => $vv ) {
			$variable[ '[' . $vk . ']' ] = $vv;
		}
		$variable['[date]'] = 'Booking Date';
		return $variable;
	}
}
new BookingCalendar();
