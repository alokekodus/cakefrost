<?php
/**
 * Woocommerce subscriptions helper.
 *
 * @package Helper
 */

if (! defined('ABSPATH') ) {
    exit;
}
if (! is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php') ) {
    return;
}

/**
 * WCSubscription class 
 */
class WCSubscription
{
    /**
     * Construct function 
     */
    public function __construct()
    {
        add_action('sa_addTabs', array( $this, 'add_tabs' ), 100);
        add_filter('sAlertDefaultSettings', array( $this, 'add_default_setting' ), 1);
        $statuses = wcs_get_subscription_statuses();
        foreach ( $statuses as $ks => $order_status ) {
            $prefix = 'wc-';
            $vs     = $ks;
            if (substr($vs, 0, strlen($prefix)) === $prefix ) {
                $vs = substr($vs, strlen($prefix));
            }
            add_action('woocommerce_subscription_status_' . $vs, array( $this, 'smsalert_send_msg_subs_status_change' ), 10, 1);
        }

        add_action('woocommerce_subscription_renewal_payment_complete', array( $this, 'smsalert_send_msg_subs_renewal' ), 10, 2);
        add_action('woocommerce_checkout_subscription_created', array( $this, 'smsalert_send_msg_subs_created' ), 10, 3);
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
        $subscriptions_param = array(
        'checkTemplateFor' => 'wc_subscriptions',
        'templates'        => self::get_wc_subsciptions_templates(),
        );

        $tabs['woocommerce']['inner_nav']['wc_subscriptions']['title']       = __('Subscriptions', 'sms-alert');
        $tabs['woocommerce']['inner_nav']['wc_subscriptions']['tab_section'] = 'subscriptionstemplates';
        $tabs['woocommerce']['inner_nav']['wc_subscriptions']['tabContent']  = $subscriptions_param;
        $tabs['woocommerce']['inner_nav']['wc_subscriptions']['filePath']    = 'views/message-template.php';
        $tabs['woocommerce']['inner_nav']['wc_subscriptions']['icon']        = 'dashicons-products';
        return $tabs;
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
        $statuses               = wcs_get_subscription_statuses();
        $statuses['wc-create']  = 'Created';
        $statuses['wc-renewal'] = 'Renewal';
        foreach ( $statuses as $ks => $order_status ) {
            $prefix = 'wc-';
            $vs     = $ks;
            if (substr($vs, 0, strlen($prefix)) === $prefix ) {
                $vs = substr($vs, strlen($prefix));
            }
            $defaults['smsalert_wc_subscriptionl'][ 'admin_subs_' . $vs . '_msg' ]          = 'off';
            $defaults['smsalert_wc_subscriptionl'][ 'cust_subs_' . $vs . '_msg' ]           = 'off';
            $defaults['smsalert_wc_subscriptionl'][ 'sms_body_admin_subs_' . $vs . '_msg' ] = '';
            $defaults['smsalert_wc_subscriptionl'][ 'sms_body_cust_subs_' . $vs . '_msg' ]  = '';

        }

        return $defaults;
    }

    /**
     * Get customer templates.
     * 
     * @return array 
     */
    public static function get_wc_subsciptions_templates()
    {
        $templates              = array();
        $statuses               = wcs_get_subscription_statuses();
        $statuses['wc-create']  = 'Created';
        $statuses['wc-renewal'] = 'Renewal';
        foreach ( $statuses as $ks => $order_status ) {

            $prefix = 'wc-';
            $vs     = $ks;
            if (substr($vs, 0, strlen($prefix)) === $prefix ) {
                $vs = substr($vs, strlen($prefix));
            }

            $current_val                                   = smsalert_get_option('admin_subs_' . $vs . '_msg', 'smsalert_wc_subscriptionl', 'on');
            $checkbox_name_id                              = 'smsalert_wc_subscriptionl[admin_subs_' . $vs . '_msg]';
            $text_area_name_id                             = 'smsalert_message[sms_body_admin_subs_' . $vs . '_msg]';
            $default_template                              = ( 'Created' === $order_status ) ? SmsAlertMessages::showMessage('DEFAULT_ADMIN_SUBS_CREATE_MSG') : SmsAlertMessages::showMessage('DEFAULT_ADMIN_SUBS_STATUS_MSG');
            $text_body                                     = smsalert_get_option('sms_body_admin_subs_' . $vs . '_msg', 'smsalert_message', ( ( '' !== $default_template ) ? $default_template : '' ));
            $templates[ 'admin_subs_' . $ks ]['title']     = 'Admin Notification When Subscription is ' . ucwords($order_status);
            $templates[ 'admin_subs_' . $ks ]['enabled']   = $current_val;
            $templates[ 'admin_subs_' . $ks ]['status']    = 'admin_' . $vs;
            $templates[ 'admin_subs_' . $ks ]['text-body'] = $text_body;
            $templates[ 'admin_subs_' . $ks ]['checkboxNameId'] = $checkbox_name_id;
            $templates[ 'admin_subs_' . $ks ]['textareaNameId'] = $text_area_name_id;
            $templates[ 'admin_subs_' . $ks ]['moreoption']     = 1;
            $templates[ 'admin_subs_' . $ks ]['token']          = array_merge(
                WooCommerceCheckOutForm::getvariables(),
                array(
                '[subscription_id]'     => 'Subscription Id',
                '[subscription_status]' => 'Subscription Status',
                )
            );
        }

        foreach ( $statuses as $ks => $order_status ) {

            $prefix = 'wc-';
            $vs     = $ks;
            if (substr($vs, 0, strlen($prefix)) === $prefix ) {
                $vs = substr($vs, strlen($prefix));
            }

            $current_val       = smsalert_get_option('cust_subs_' . $vs . '_msg', 'smsalert_wc_subscriptionl', 'on');
            $checkbox_name_id  = 'smsalert_wc_subscriptionl[cust_subs_' . $vs . '_msg]';
            $text_area_name_id = 'smsalert_message[sms_body_cust_subs_' . $vs . '_msg]';
            $default_template  = ( 'Created' === $order_status ) ? SmsAlertMessages::showMessage('DEFAULT_CUST_SUBS_CREATE_MSG') : SmsAlertMessages::showMessage('DEFAULT_CUST_SUBS_STATUS_MSG');
            $text_body         = smsalert_get_option('sms_body_cust_subs_' . $vs . '_msg', 'smsalert_message', ( ( '' !== $default_template ) ? $default_template : '' ));

            $templates[ 'cust_subs_' . $ks ]['title']          = 'Customer Notification When Subscription is ' . ucwords($order_status);
            $templates[ 'cust_subs_' . $ks ]['enabled']        = $current_val;
            $templates[ 'cust_subs_' . $ks ]['status']         = 'cust_' . $vs;
            $templates[ 'cust_subs_' . $ks ]['text-body']      = $text_body;
            $templates[ 'cust_subs_' . $ks ]['checkboxNameId'] = $checkbox_name_id;
            $templates[ 'cust_subs_' . $ks ]['textareaNameId'] = $text_area_name_id;
            $templates[ 'cust_subs_' . $ks ]['moreoption']     = 1;
            $templates[ 'cust_subs_' . $ks ]['token']          = array_merge(
                WooCommerceCheckOutForm::getvariables(),
                array(
                '[subscription_id]'     => 'Subscription Id',
                '[subscription_status]' => 'Subscription Status',
                )
            );
        }

        return $templates;
    }


    /**
     * Smsalert send message.
     *
     * @param object $subscription subscription.
     */
    public function smsalert_send_msg_subs_status_change( $subscription )
    {
        $order_id = $subscription->get_parent_id();
        global $wpdb;
        $sms_admin_phone = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        $cust_no         = get_post_meta($order_id, '_billing_phone', true);
        $subs_status     = $subscription->get_status();

        $admin_msg                  = smsalert_get_option('sms_body_admin_subs_' . $subs_status . '_msg', 'smsalert_message', '');
        $admin_msg                  = $this->parse_sms_body($subscription, $admin_msg);
        $admin_sms_data['number']   = $sms_admin_phone;
        $admin_sms_data['sms_body'] = $admin_msg;
        $admin_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($admin_sms_data, $order_id);
        $admin_message              = ( ! empty($admin_sms_data['sms_body']) ) ? $admin_sms_data['sms_body'] : '';

        $smsalert_notification_subs_status_change_admin_msg = smsalert_get_option('admin_subs_' . $subs_status . '_msg', 'smsalert_wc_subscriptionl', 'on');

        if ('on' === $smsalert_notification_subs_status_change_admin_msg && '' !== $admin_message ) {
            do_action('sa_send_sms', $sms_admin_phone, $admin_message);
        }

        $customer_msg              = smsalert_get_option('sms_body_cust_subs_' . $subs_status . '_msg', 'smsalert_message', '');
        $customer_msg              = $this->parse_sms_body($subscription, $customer_msg);
        $cust_sms_data['number']   = $cust_no;
        $cust_sms_data['sms_body'] = $customer_msg;
        $cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($cust_sms_data, $order_id);
        $customer_msg              = ( ! empty($cust_sms_data['sms_body']) ) ? $cust_sms_data['sms_body'] : '';

        $smsalert_notification_subs_status_change_cust_msg = smsalert_get_option('cust_subs_' . $subs_status . '_msg', 'smsalert_wc_subscriptionl', 'on');
        if ('on' === $smsalert_notification_subs_status_change_cust_msg && '' !== $customer_msg ) {
            do_action('sa_send_sms', $cust_no, $customer_msg);
        }
    }

    /**
     * Smsalert send message renewal.
     *
     * @param object $subscription subscription.
     * @param object $order        order.
     */
    public function smsalert_send_msg_subs_renewal( $subscription, $order )
    {
        $order_id                   = $subscription->get_parent_id();
        $sms_admin_phone            = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        $cust_no                    = get_post_meta($order_id, '_billing_phone', true);
        $admin_msg                  = smsalert_get_option('sms_body_admin_subs_renewal_msg', 'smsalert_message', '');
        $admin_msg                  = $this->parse_sms_body($subscription, $admin_msg);
        $admin_sms_data['number']   = $sms_admin_phone;
        $admin_sms_data['sms_body'] = $admin_msg;
        $admin_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($admin_sms_data, $order_id);
        $admin_message              = ( ! empty($admin_sms_data['sms_body']) ) ? $admin_sms_data['sms_body'] : '';

        $smsalert_notification_subs_renewal_admin_msg = smsalert_get_option('admin_subs_renewal_msg', 'smsalert_wc_subscriptionl', 'on');

        if ('on' === $smsalert_notification_subs_renewal_admin_msg && '' !== $admin_message ) {
            do_action('sa_send_sms', $sms_admin_phone, $admin_message);
        }

        $customer_msg              = smsalert_get_option('sms_body_cust_subs_renewal_msg', 'smsalert_message', '');
        $customer_msg              = $this->parse_sms_body($subscription, $customer_msg);
        $cust_sms_data['number']   = $cust_no;
        $cust_sms_data['sms_body'] = $customer_msg;
        $cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($cust_sms_data, $order_id);
        $customer_msg              = ( ! empty($cust_sms_data['sms_body']) ) ? $cust_sms_data['sms_body'] : '';

        $smsalert_notification_subs_renewal_cust_msg = smsalert_get_option('cust_subs_renewal_msg', 'smsalert_wc_subscriptionl', 'on');
        if ('on' === $smsalert_notification_subs_renewal_cust_msg && '' !== $customer_msg ) {
            do_action('sa_send_sms', $cust_no, $customer_msg);
        }
    }

    /**
     * Smsalert send sms subscription created.
     *
     * @param object $subscription   subscription.
     * @param object $order          order.
     * @param object $recurring_cart recurring_cart.
     */
    public function smsalert_send_msg_subs_created( $subscription, $order, $recurring_cart )
    {
        $this->set_renewal_reminder($subscription);
        $order_id                   = $subscription->get_parent_id();
        $sms_admin_phone            = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        $cust_no                    = get_post_meta($order_id, '_billing_phone', true);
        $admin_msg                  = smsalert_get_option('sms_body_admin_subs_create_msg', 'smsalert_message', '');
        $admin_msg                  = $this->parse_sms_body($subscription, $admin_msg);
        $admin_sms_data['number']   = $sms_admin_phone;
        $admin_sms_data['sms_body'] = $admin_msg;
        $admin_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($admin_sms_data, $order_id);
        $admin_message              = ( ! empty($admin_sms_data['sms_body']) ) ? $admin_sms_data['sms_body'] : '';

        $smsalert_notification_subs_create_admin_msg = smsalert_get_option('admin_subs_create_msg', 'smsalert_wc_subscriptionl', 'on');

        if ('on' === $smsalert_notification_subs_create_admin_msg && '' !== $admin_message ) {
            do_action('sa_send_sms', $sms_admin_phone, $admin_message);
        }

        $customer_msg              = smsalert_get_option('sms_body_cust_subs_create_msg', 'smsalert_message', '');
        $customer_msg              = $this->parse_sms_body($subscription, $customer_msg);
        $cust_sms_data['number']   = $cust_no;
        $cust_sms_data['sms_body'] = $customer_msg;
        $cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($cust_sms_data, $order_id);
        $customer_msg              = ( ! empty($cust_sms_data['sms_body']) ) ? $cust_sms_data['sms_body'] : '';

        $smsalert_notification_subs_create_cust_msg = smsalert_get_option('cust_subs_create_msg', 'smsalert_wc_subscriptionl', 'on');
        if ('on' === $smsalert_notification_subs_create_cust_msg && '' !== $customer_msg ) {
            do_action('sa_send_sms', $cust_no, $customer_msg);
        }
    }

    /**
     * Template parse sms body.
     *
     * @param object $subscription subscription.
     * @param string $message      message.
     * 
     * @return string
     */
    public function parse_sms_body( $subscription, $message )
    {
        $subs_id     = $subscription->get_id();
        $subs_status = $subscription->get_status();

        $find = array(
        '[subscription_id]',
        '[subscription_status]',
        );

        $replace = array(
        $subs_id,
        $subs_status,
        );

        $message = str_replace($find, $replace, $message);
        return $message;
    }

    /**
     * Set renewal reminder.
     *
     * @param object $subscription subscription.
     */
    public function set_renewal_reminder( $subscription )
    {
        $order_id     = $subscription->get_parent_id();
        $renewal_date = $subscription->get_date('next_payment');
        global $wpdb;
        if (! $renewal_date ) {
            return;
        }

        $renewal_timestamp = get_date_from_gmt($renewal_date, 'U');

        if (time() > $renewal_timestamp ) {
            return;
        }
        $dupes = $this->get_data(
            array(
                        'is_sent' => 0,
                        'subs_id' => $subscription->get_id(),
                        'user_id' => $subscription->get_user_id(),
            )
        );

        if (! ( $dupes > 0 ) ) {

            $interval = 3;
            $add      = $interval * 86400;
            $send_on  = $renewal_timestamp - $add;

            $insert = array(
            'user_id'    => $subscription->get_user_id(),
            'send_on'    => $send_on,
            'user_phone' => get_post_meta($order_id, '_billing_phone', true),
            'subs_id'    => $subscription->get_id(),
            );
            $wpdb->insert($wpdb->prefix . 'smsalert_sms_orders', $insert);
        }

    }

    /**
     * Get data
     *
     * @param array $data data.
     */
    public function get_data( $data = array() )
    {
        global $wpdb;
        $sql = 'SELECT COUNT(*)
                  FROM ' . $wpdb->prefix . 'smsalert_sms_orders
                  WHERE user_id = ' . $data['user_id'] . ' AND order_id = ' . $data['order_id'] . ' AND is_sent = ' . $data['is_sent'];
        return $wpdb->get_var($sql);
    }
}
new WCSubscription();
?>
<?php
/**
 * WCRenewal class 
 */
class WCRenewal
{
    /**
     * Construct function 
     */
    public function __construct()
    {
        add_action('sa_addTabs', array( $this, 'add_tabs' ), 100);
        add_action('smsalert_followup_sms', array( $this, 'smsalert_send_sms' ));
        add_filter('sAlertDefaultSettings', array( $this, 'add_default_setting' ), 1);
    }

    /**
     * Send sms function 
     */
    public function smsalert_send_sms()
    {
        global $wpdb;
        $customer_notify = smsalert_get_option('customer_notify', 'smsalert_wc_renewal', 'on');
        if ('on' === $customer_notify ) {
            $scheduler_data = get_option('smsalert_wc_renewal_scheduler');
            foreach ( $scheduler_data['cron'] as $sdata ) {
                $cron_frequency = 10;
                $datetime       = current_time('mysql');

                $fromdate = date('Y-m-d H:i:s', strtotime('+' . $sdata['frequency'] . ' days', strtotime($datetime)));

                $todate = date('Y-m-d H:i:s', strtotime('+' . $cron_frequency . ' minutes', strtotime($fromdate)));

                $renewal_date = get_post_meta($result['subs_id'], '_schedule_next_payment', true);

                $results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'smsalert_sms_orders as s inner join ' . $wpdb->prefix . 'posts as p on s.subs_id=p.id inner join ' . $wpdb->prefix . "postmeta as pm on s.subs_id=pm.post_id where pm.meta_key='_schedule_next_payment' and pm.meta_value >= '" . $fromdate . "' and pm.meta_value <= '" . $todate . "'", ARRAY_A);

                if (! empty($results) ) {
                    foreach ( $results as $result ) {
                        $subs_status               = $result['post_status'];
                        $customer_msg              = $sdata['message'];
                        $customer_msg              = $this->parse_sms_body($result['subs_id'], substr($subs_status, strlen('wc-')), $customer_msg);
                        $cust_sms_data['number']   = $result['user_phone'];
                        $cust_sms_data['sms_body'] = $customer_msg;
                        $cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($cust_sms_data, $result['post_parent']);
                        $customer_msg              = ( ! empty($cust_sms_data['sms_body']) ) ? $cust_sms_data['sms_body'] : '';
                        if (( 'wc-active' !== $subs_status ) || '' === $customer_msg ) {
                            continue;
                        }
                        $last_msg_count = $result['is_sent'];
                        $total_msg_sent = $last_msg_count + 1;
                        do_action('sa_send_sms', $result['user_phone'], $customer_msg);
                        $wpdb->update($wpdb->prefix . 'smsalert_sms_orders', array( 'is_sent' => $total_msg_sent ), array( 'id' => $result['id'] ));
                    }
                }
            }
        }
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
        $defaults['smsalert_wc_renewal']['customer_notify']                = 'off';
        $defaults['smsalert_wc_renewal_scheduler']['cron'][0]['frequency'] = '1';
        $defaults['smsalert_wc_renewal_scheduler']['cron'][0]['message']   = '';
        $defaults['smsalert_wc_renewal_scheduler']['cron'][1]['frequency'] = '2';
        $defaults['smsalert_wc_renewal_scheduler']['cron'][1]['message']   = '';

        return $defaults;
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
        $renewal_param = array(
        'checkTemplateFor' => 'wc_renewal',
        'templates'        => self::get_wc_renewal_templates(),
        );

        $tabs['woocommerce']['inner_nav']['wc_renewal']['title']       = __('Renewal', 'sms-alert');
        $tabs['woocommerce']['inner_nav']['wc_renewal']['tab_section'] = 'renewaltemplates';
        $tabs['woocommerce']['inner_nav']['wc_renewal']['tabContent']  = $renewal_param;
        $tabs['woocommerce']['inner_nav']['wc_renewal']['filePath']    = 'views/renewal-template.php';
        $tabs['woocommerce']['inner_nav']['wc_renewal']['icon']        = 'dashicons-products';
        return $tabs;
    }

    /**
     * Get wc renewal templates function. 
     * 
     * @return array
     * */
    public static function get_wc_renewal_templates()
    {
        $current_val      = smsalert_get_option('customer_notify', 'smsalert_wc_renewal', 'on');
        $checkbox_name_id = 'smsalert_wc_renewal[customer_notify]';

        $scheduler_data = get_option('smsalert_wc_renewal_scheduler');
        $templates      = array();
        $count          = 0;

        if (empty($scheduler_data) ) {
            $scheduler_data['cron'][] = array(
            'frequency' => '1',
            'message'   => SmsAlertMessages::showMessage('DEFAULT_WC_RENEWAL_CUSTOMER_MESSAGE'),
            );
            $scheduler_data['cron'][] = array(
            'frequency' => '2',
            'message'   => SmsAlertMessages::showMessage('DEFAULT_WC_RENEWAL_CUSTOMER_MESSAGE'),
            );
        }

        foreach ( $scheduler_data['cron'] as $key => $data ) {

            $text_area_name_id = 'smsalert_wc_renewal_scheduler[cron][' . $count . '][message]';
            $select_name_id    = 'smsalert_wc_renewal_scheduler[cron][' . $count . '][frequency]';
            $text_body         = $data['message'];

            $templates[ $key ]['frequency']      = $data['frequency'];
            $templates[ $key ]['enabled']        = $current_val;
            $templates[ $key ]['title']          = 'Send renewal reminder message to customer';
            $templates[ $key ]['checkboxNameId'] = $checkbox_name_id;
            $templates[ $key ]['text-body']      = $text_body;
            $templates[ $key ]['textareaNameId'] = $text_area_name_id;
            $templates[ $key ]['selectNameId']   = $select_name_id;
            $templates[ $key ]['token']          = array_merge(
                WooCommerceCheckOutForm::getvariables(),
                array(
                '[subscription_id]'     => 'Subscription Id',
                '[subscription_status]' => 'Subscription Status',
                )
            );

            $count++;
        }

        return $templates;
    }

    /**
     * Parse sms body function.
     *
     * @param int    $subs_id     subs id.
     * @param string $subs_status subs status.
     * @param string $message     message.
     * 
     * @return string
     */
    public function parse_sms_body( $subs_id, $subs_status, $message )
    {
        $find = array(
        '[subscription_id]',
        '[subscription_status]',
        );

        $replace = array(
        $subs_id,
        $subs_status,
        );

        $message = str_replace($find, $replace, $message);
        return $message;
    }
}
new WCRenewal();

