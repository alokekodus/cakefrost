<?php
/**
 * Woocommerce booking helper.
 *
 * @package Helper
 */

if (! defined('ABSPATH') ) {
    exit;
}
if (! is_plugin_active('woocommerce-bookings/woocommerce-bookings.php') ) {
    return;
}
/**
 * SmsAlertWcBooking class 
 */
class SmsAlertWcBooking
{
    /**
     * Construct function 
     */
    public function __construct()
    {
        include_once WP_PLUGIN_DIR . '/woocommerce-bookings/includes/wc-bookings-functions.php';
        add_filter('sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1);
        self::add_action_for_booking_status();
        add_action('sa_addTabs', array( $this, 'add_tabs' ), 10);
    }

    /**
     * Add tabs to smsalert settings at backend.
     *
     * @param array $tabs tabs.
     * 
     * @return array
     */
    public static function add_tabs( $tabs = array() )
    {
        $customer_param = array(
        'checkTemplateFor' => 'wc_booking_customer',
        'templates'        => self::get_customer_templates(),
        );

        $admin_param = array(
        'checkTemplateFor' => 'wc_booking_admin',
        'templates'        => self::get_admin_templates(),
        );

        $tabs['woocommerce_booking']['nav']  = 'Woocommerce Booking';
        $tabs['woocommerce_booking']['icon'] = 'dashicons-admin-users';

        $tabs['woocommerce_booking']['inner_nav']['wcbk_customer']['title']        = 'Customer Notifications';
        $tabs['woocommerce_booking']['inner_nav']['wcbk_customer']['tab_section']  = 'wcbkcsttemplates';
        $tabs['woocommerce_booking']['inner_nav']['wcbk_customer']['first_active'] = true;
        $tabs['woocommerce_booking']['inner_nav']['wcbk_customer']['tabContent']   = $customer_param;
        $tabs['woocommerce_booking']['inner_nav']['wcbk_customer']['filePath']     = 'views/message-template.php';

        $tabs['woocommerce_booking']['inner_nav']['wcbk_admin']['title']       = 'Admin Notifications';
        $tabs['woocommerce_booking']['inner_nav']['wcbk_admin']['tab_section'] = 'wcbkadmintemplates';
        $tabs['woocommerce_booking']['inner_nav']['wcbk_admin']['tabContent']  = $admin_param;
        $tabs['woocommerce_booking']['inner_nav']['wcbk_admin']['filePath']    = 'views/message-template.php';
        return $tabs;
    }

    /**
     * Get customer templates function.
     *
     * @return array
     */
    public static function get_customer_templates()
    {
        $wcbk_order_statuses = self::get_booking_statuses();
        $templates           = array();

        foreach ( $wcbk_order_statuses as $ks  => $vs ) {

            $current_val = smsalert_get_option('wcbk_order_status_' . $vs, 'smsalert_wcbk_general', 'on');

            $check_box_name_id = 'smsalert_wcbk_general[wcbk_order_status_' . $vs . ']';
            $text_area_name_id = 'smsalert_wcbk_message[wcbk_sms_body_' . $vs . ']';

            $text_body = smsalert_get_option('wcbk_sms_body_' . $vs, 'smsalert_wcbk_message', sprintf(/* translators: %1$s: First name */esc_html_e('Hello %1$s, status of your booking %2$s with %3$s has been changed to %4$s.', 'sms-alert'), '[first_name]', '[booking_id]', '[store_name]', '[booking_status]'));

            $templates[ $ks ]['title']          = 'When Order is ' . ucwords($vs);
            $templates[ $ks ]['enabled']        = $current_val;
            $templates[ $ks ]['status']         = $ks;
            $templates[ $ks ]['text-body']      = $text_body;
            $templates[ $ks ]['checkboxNameId'] = $check_box_name_id;
            $templates[ $ks ]['textareaNameId'] = $text_area_name_id;
            $templates[ $ks ]['token']          = self::get_wc_bookingvariables();
        }
        return $templates;
    }

    /**
     * Get admin templates function.
     * 
     * @return array
     */
    public static function get_admin_templates()
    {
        $wcbk_order_statuses = self::get_booking_statuses();
        $templates           = array();

        foreach ( $wcbk_order_statuses as $ks  => $vs ) {

            $current_val = smsalert_get_option('wcbk_admin_notification_' . $vs, 'smsalert_wcbk_general', 'on');

            $check_box_name_id = 'smsalert_wcbk_general[wcbk_admin_notification_' . $vs . ']';
            $text_area_name_id = 'smsalert_wcbk_message[wcbk_admin_sms_body_' . $vs . ']';

            $text_body = smsalert_get_option('wcbk_admin_sms_body_' . $vs, 'smsalert_wcbk_message', sprintf(/* translators: %1$s: Store name */esc_html_e('%1$s status of order %2$s has been changed to %3$s.', 'sms-alert'), '[store_name]:', '#[booking_id]', '[booking_status]'));

            $templates[ $ks ]['title']          = 'When Order is ' . ucwords($vs);
            $templates[ $ks ]['enabled']        = $current_val;
            $templates[ $ks ]['status']         = $ks;
            $templates[ $ks ]['text-body']      = $text_body;
            $templates[ $ks ]['checkboxNameId'] = $check_box_name_id;
            $templates[ $ks ]['textareaNameId'] = $text_area_name_id;
            $templates[ $ks ]['token']          = self::get_wc_bookingvariables();
        }
        return $templates;
    }

    /**
     * Add action for booking statuses.
     */
    public static function add_action_for_booking_status()
    {
        $wcbk_order_statuses = self::get_booking_statuses();
        foreach ( $wcbk_order_statuses as $wkey => $booking_status ) {
            add_action('woocommerce_booking_' . $booking_status, __CLASS__ . '::wcbk_status_changed');
        }
    }

    /**
     * Trigger sms on status change of booking.
     *
     * @param int $booking_id booking id.
     */
    public static function wcbk_status_changed( $booking_id )
    {
        $output = self::trigger_sms($booking_id);
    }

    /**
     * Add default settings to savesetting in setting-options.
     *
     * @param array $defaults defaults.
     * 
     * @return array
     */
    public function add_default_setting( $defaults = array() )
    {
        $wcbk_order_statuses = self::get_booking_statuses();

        foreach ( $wcbk_order_statuses as $ks => $vs ) {
            $defaults['smsalert_wcbk_general'][ 'wcbk_admin_notification_' . $vs ] = 'off';
            $defaults['smsalert_wcbk_general'][ 'wcbk_order_status_' . $vs ]       = 'off';
            $defaults['smsalert_wcbk_message'][ 'wcbk_admin_sms_body_' . $vs ]     = '';
            $defaults['smsalert_wcbk_message'][ 'wcbk_sms_body_' . $vs ]           = '';
        }
        return $defaults;
    }

    /**
     * Display woocommerce booking variable at smsalert setting page.
     * 
     * @return array
     */
    public static function get_wc_bookingvariables()
    {
        $variables = array(
        '[order_id]'        => 'Order Id',
        '[store_name]'      => 'Store Name',
        '[booking_id]'      => 'Booking Id',
        '[booking_status]'  => 'Booking status',
        '[product_name]'    => 'Product Name',
        '[booking_cost]'    => 'Booking Amt',
        '[booking_start]'   => 'Booking Start',
        '[booking_end]'     => 'Booking End',
        '[first_name]'      => 'Billing First Name',
        '[last_name]'       => 'Billing Last Name',
        '[booking_persons]' => 'Person Counts',
        '[resource_name]'   => 'Resource Name',
        );
        return $variables;
    }

    /**
     * Get woocommerce booking status.
     * 
     * @return array
     */
    public static function get_booking_statuses()
    {
        $status = get_wc_booking_statuses('user', true);
        return array_keys($status);
    }

    /**
     * Trigger sms when woocommerce booking status is changed.
     *
     * @param int $booking_id booking id.
     */
    public static function trigger_sms( $booking_id )
    {
        if ($booking_id ) {
            if ('wc_booking' !== get_post_type($booking_id) ) {
                return;
            }

            $object = get_wc_booking($booking_id);
            if (! is_object($object) ) {
                return;
            }

            $booking_status     = $object->status;
            $admin_message      = smsalert_get_option('wcbk_admin_sms_body_' . $booking_status, 'smsalert_wcbk_message', '');
            $is_enabled         = smsalert_get_option('wcbk_order_status_' . $booking_status, 'smsalert_wcbk_general');
            $admin_phone_number = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
            $buyer_mob          = '';
            $product_name       = '';
            $order_id           = '';
            $booking_amt        = '';
            $first_name         = '';
            $last_name          = '';
            $bookings           = get_post_custom($booking_id);
            $booking_start      = date('M d,Y H:i', strtotime(array_shift($bookings['_booking_start'])));
            $booking_end        = date('M d,Y H:i', strtotime(array_shift($bookings['_booking_end'])));
            $person_counts      = $object->get_persons_total();
            $resource_name      = ( $object->get_resource() ) ? $object->get_resource()->post_title : '';
            $booking_amt        = array_shift($bookings['_booking_cost']);
            $order              = $object->get_order()->get_data();
            $buyer_mob          = $order['billing']['phone'];
            $first_name         = $order['billing']['first_name'];
            $last_name          = $order['billing']['last_name'];

            if ($object->get_product() ) {
                $product_name = $object->get_product()->get_title();
            }

            if ($object->get_order() ) {
                $order_id = $object->get_order()->get_order_number();
            }

            $variables = array(
            '[order_id]'        => $order_id,
            '[booking_id]'      => $booking_id,
            '[booking_status]'  => $booking_status,
            '[product_name]'    => $product_name,
            '[booking_cost]'    => $booking_amt,
            '[booking_start]'   => $booking_start,
            '[booking_end]'     => $booking_end,
            '[first_name]'      => $first_name,
            '[last_name]'       => $last_name,
            '[booking_persons]' => $person_counts,
            '[resource_name]'   => $resource_name,
            );

            if ('' !== $buyer_mob && 'on' === $is_enabled ) {
                $buyer_message = smsalert_get_option('wcbk_sms_body_' . $booking_status, 'smsalert_wcbk_message', '');
                $content       = str_replace(array_keys($variables), array_values($variables), $buyer_message);
                do_action('sa_send_sms', $buyer_mob, $content);
            }

            if ('on' === smsalert_get_option('wcbk_admin_notification_' . $booking_status, 'smsalert_wcbk_general') && '' !== $admin_phone_number ) {
                $admin_message = smsalert_get_option('wcbk_admin_sms_body_' . $booking_status, 'smsalert_wcbk_message', '');
                $content       = str_replace(array_keys($variables), array_values($variables), $admin_message);
                do_action('sa_send_sms', $admin_phone_number, $content);
            }
        }
    }
}
new SmsAlertWcBooking();
