<?php
/**
 * Review helper.
 *
 * @package Helper
 */

if (! defined('ABSPATH') ) {
    exit;
}
if (! is_plugin_active('woocommerce/woocommerce.php') ) {
    return;
}
class WCReview
{
    /**
     * Construct function.
     */
    public function __construct()
    {
        add_filter('sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1);
        add_action('sa_addTabs', array( $this, 'add_tabs' ), 100);
        add_action('woocommerce_order_status_changed', array( $this, 'schedule_sms' ), 100, 4);
    }

    /**
     * Schedule sms function.
     *
     * @param int    $order_id   order_id.
     * @param string $old_status old_status.
     * @param string $new_status new_status.
     * @param $instance   instance. 
     */
    public function schedule_sms( $order_id, $old_status, $new_status, $instance )
    {

        $order       = new WC_Order($order_id);
        $order_items = $order->get_items();
        $first_item  = current($order_items);
        $post_id     = $first_item['order_id'];
        $buyer_no    = get_post_meta($post_id, '_billing_phone', true);

        $customer_notify = smsalert_get_option('customer_notify', 'smsalert_or_general', 'on');
        $review_message  = smsalert_get_option('customer_notify', 'smsalert_or_message', '');
        $message_status  = smsalert_get_option('review_status', 'smsalert_review');
        $days            = smsalert_get_option('schedule_day', 'smsalert_review');

        if ($new_status === $message_status && 'on' === $customer_notify && '' !== $review_message && 0 === $order->get_parent_id() ) {

            $time_enabled = smsalert_get_option('send_at', 'smsalert_review');

            if ('on' === $time_enabled ) {
                $schedule_time = smsalert_get_option('schedule_time', 'smsalert_review');

               $date_modified = SmsAlertUtility::sa_date_time($order->get_date_modified(),'Y-m-d');
               $default_time  = $date_modified. ' ' . $schedule_time;
			   $schedule = SmsAlertUtility::sa_date_time($default_time,'Y-m-d H:i:s',$days.' days');
			   $ist = SmsAlertUtility::date_time_ist($schedule);
			} else {
                $order_time = SmsAlertUtility::date_time_ist();
                $schedule   = SmsAlertUtility::sa_date_time($order_time,'Y-m-d H:i:s',$days.' days');
			}
			
			
            $buyer_sms_data['number']   = $buyer_no;
            $buyer_sms_data['sms_body'] = $review_message;
            $buyer_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($buyer_sms_data, $order_id);
            $review_message             = ( ! empty($buyer_sms_data['sms_body']) ) ? $buyer_sms_data['sms_body'] : '';
			do_action('sa_send_sms', $buyer_no, $review_message, $schedule);
        }
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
        $review_param = array(
        'checkTemplateFor' => 'review',
        'templates'        => self::get_review_templates(),
        );

        $tabs['woocommerce']['inner_nav']['review']['title']       = 'Review Request';
        $tabs['woocommerce']['inner_nav']['review']['tab_section'] = 'reviewtemplates';
        $tabs['woocommerce']['inner_nav']['review']['tabContent']  = $review_param;
        $tabs['woocommerce']['inner_nav']['review']['filePath']    = 'views/review-template.php';
        return $tabs;
    }

    /**
     * Add default settings to savesetting in setting-options.
     * 
     * @param array $defaults defaults.
     * 
     * @return array
     */
    public static function add_default_setting( $defaults = array() )
    {
        $defaults['smsalert_review']['schedule_day']        = '1';
        $defaults['smsalert_review']['review_status']       = 'completed';
        $defaults['smsalert_review']['schedule_time']       = '10:00';
        $defaults['smsalert_review']['send_at']             = 'off';
        $defaults['smsalert_or_general']['customer_notify'] = 'off';
        $defaults['smsalert_or_message']['customer_notify'] = '';
        return $defaults;
    }

    /**
     * Get review template function.
     * 
     * @return array
     */
    public static function get_review_templates()
    {
        $current_val       = smsalert_get_option('customer_notify', 'smsalert_or_general', 'on');
        $checkbox_name_id  = 'smsalert_or_general[customer_notify]';
        $text_area_name_id = 'smsalert_or_message[customer_notify]';
        $text_body         = smsalert_get_option('customer_notify', 'smsalert_or_message', SmsAlertMessages::showMessage('DEFAULT_CUSTOMER_REVIEW_MESSAGE'));

        $templates = array();

        $templates['title']          = 'Request for Review';
        $templates['enabled']        = $current_val;
        $templates['text-body']      = $text_body;
        $templates['checkboxNameId'] = $checkbox_name_id;
        $templates['textareaNameId'] = $text_area_name_id;
        $templates['moreoption']     = 1;
        $templates['token']          = WooCommerceCheckOutForm::getvariables();
        return $templates;
    }
}
new WCReview();
