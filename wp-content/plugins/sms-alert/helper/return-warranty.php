<?php
/**
 * Return warranty helper.
 *
 * @package Helper
 */

if (! defined('ABSPATH') ) {
    exit;
}
if (! is_plugin_active('woocommerce-warranty/woocommerce-warranty.php') ) {
    return;
}
/**
 * sa_Return_Warranty class 
 */    
class sa_Return_Warranty
{
    /**
     * Construct function.
     */
    public function __construct()
    {
        add_filter('sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1);
        add_action('sa_addTabs', array( $this, 'add_tabs' ), 10);
        add_action('wc_warranty_settings_tabs', __CLASS__ . '::smsalert_warranty_tab');
        add_action('wc_warranty_settings_panels', __CLASS__ . '::smsalert_warranty_settings_panels');
        add_action('admin_post_wc_warranty_settings_update', array( $this, 'update_wc_warranty_settings' ), 5);
        add_action('wp_ajax_warranty_update_request_fragment', array( $this, 'on_rma_status_update' ), 0);
        add_action('wc_warranty_created', array( $this, 'on_new_rma_request' ), 5);
    }

    /**
     * Get warranty status function.
     */
    public static function get_warranty_status()
    {
        if (! class_exists('WooCommerce_Warranty') ) {
            return array();
        }

        $wc_warranty = new WooCommerce_Warranty();
        return $wc_warranty->get_default_statuses();
    }

    /**
     * Update wc warranty settings function.
     * 
     * @param array $data data.
     */
    public function update_wc_warranty_settings( $data )
    {
        $options = $_POST;
        if ('smsalert_warranty' === $options['tab'] ) {
            foreach ( $options as $name => $value ) {
                if (is_array($value) ) {
                    foreach ( $value as $k => $v ) {
                        if (! is_array($v) ) {
                            $value[ $k ] = sanitize_text_field(wp_unslash($v));
                        }
                    }
                }
                update_option($name, $value);
            }
        }
    }
    
    /**
     * Send rma status sms function.
     *
     * @param int    $request_id request_id.
     * @param string $status     status.
     */
    public function send_rma_status_sms( $request_id, $status )
    {
        $wc_warranty_checkbox = smsalert_get_option('warranty_status_' . $status, 'smsalert_warranty', '');
        $is_sms_enabled       = ( 'on' === $wc_warranty_checkbox ) ? true : false;
        if ($is_sms_enabled ) {
            $sms_content = smsalert_get_option('sms_text_' . $status, 'smsalert_warranty', '');
            $order_id    = get_post_meta($request_id, '_order_id', true);
            $rma_id      = get_post_meta($request_id, '_code', true);
            $order       = wc_get_order($order_id);
            global $wpdb;
            $products = $items = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT *
				FROM {$wpdb->prefix}wc_warranty_products
				WHERE request_id                                        = %d",
                    $request_id
                ),
                ARRAY_A
            );

            $item_name = '';
            foreach ( $products as $product ) {

                if (empty($product['product_id']) && empty($item['product_name']) ) {
                    continue;
                }

                if (0 === $product['product_id'] ) {
                    $item_name .= $item['product_name'] . ', ';
                } else {
                    $item_name .= warranty_get_product_title($product['product_id']) . ', ';
                }
            }

            $item_name                  = rtrim($item_name, ', ');
            $sms_content                = str_replace('[item_name]', $item_name, $sms_content);
            $buyer_sms_data             = array();
            $buyer_mob                  = get_post_meta($order_id, '_billing_phone', true);
            $buyer_sms_data['number']   = $buyer_mob;
            $buyer_sms_data['sms_body'] = $sms_content;
            $buyer_sms_data['rma_id']   = $rma_id;
            $buyer_sms_data             = WooCommerceCheckOutForm::pharse_sms_body($buyer_sms_data, $order_id);
            $message                    = ( ! empty($buyer_sms_data['sms_body']) ) ? $buyer_sms_data['sms_body'] : '';

            do_action('sa_send_sms', $buyer_mob, $message);
        }
    }

    /**
     * On new rma request function.
     */
    public function on_new_rma_request( $warranty_id )
    {
        $this->send_rma_status_sms($warranty_id, 'new');
    }

    /**
     * On rma status update function.
     */
    public function on_rma_status_update()
    {
        $request_id = isset($_POST['request_id']) ? sanitize_text_field(wp_unslash($_POST['request_id'])) : '';
        $status     = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';

        $this->send_rma_status_sms($request_id, $status);
    }

    /**
     * Smsalert warranty tab function.
     */
    public static function smsalert_warranty_tab()
    {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : '';
        ?>
        <a href="admin.php?page=warranties-settings&tab=smsalert_warranty" class="nav-tab <?php echo ( 'smsalert_warranty' === $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('SMS Alert', 'wc_warranty'); ?></a>
        <?php
    }
    
    /**
     * Smsalert warranty settings panels.
     */
    public static function smsalert_warranty_settings_panels()
    {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : '';

        if ('smsalert_warranty' === $active_tab ) {
            $return_warranty_param = array(
            'checkTemplateFor' => 'return_warranty',
            'templates'        => self::getReturnWarrantyTemplates(),
            );
            get_smsalert_template('views/message-template.php', $return_warranty_param);
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
        $wc_warrant_status = self::get_warranty_status();

        foreach ( $wc_warrant_status as $ks                           => $vs ) {
            $vs = str_replace(' ', '-', strtolower($vs));
            $defaults['smsalert_warranty'][ 'warranty_status_' . $vs ] = 'off';
            $defaults['smsalert_warranty']['sms_text_'][ $vs ]         = '';
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
    public static function add_tabs( $tabs = array() )
    {
        $return_warranty_param = array(
        'checkTemplateFor' => 'return_warranty',
        'templates'        => self::getReturnWarrantyTemplates(),
        );

        $tabs['return_warranty']['title']       = __('Return & Warranty', 'sms-alert');
        $tabs['return_warranty']['tab_section'] = 'return_warranty';
        $tabs['return_warranty']['tabContent']  = $return_warranty_param;
        $tabs['return_warranty']['filePath']    = 'views/message-template.php';

        $tabs['return_warranty']['icon'] = 'dashicons-products';
        return $tabs;
    }

    /**
     * Get Return Warranty Templates.
     * 
     * @return array
     */
    public static function getReturnWarrantyTemplates()
    {
        $wc_warrant_status = self::get_warranty_status();
        $variables         = array(
        '[order_id]'           => 'Order Id',
        '[rma_number]'         => 'RMA Number',
        '[rma_status]'         => 'RMA Status',
        '[order_amount]'       => 'Order Total',
        '[billing_first_name]' => 'First Name',
        '[item_name]'          => 'Product Name',
        '[store_name]'         => 'Store Name',
        );
        $templates         = array();

        foreach ( $wc_warrant_status as $ks                           => $vs ) {

            $vs               = str_replace(' ', '-', strtolower($vs));
            $wc_warranty_text = smsalert_get_option('sms_text_' . $vs, 'smsalert_warranty', '');
            $current_val      = smsalert_get_option('warranty_status_' . $vs, 'smsalert_warranty', 'on');

            $checkbox_name_id  = 'smsalert_warranty[warranty_status_' . $vs . ']';
            $text_area_name_id = 'smsalert_warranty[sms_text_' . $vs . ']';

            $text_body = smsalert_get_option('sms_text_' . $vs, 'smsalert_warranty', '') ? smsalert_get_option('sms_text_' . $vs, 'smsalert_warranty', '') : SmsAlertMessages::showMessage('DEFAULT_WARRANTY_STATUS_CHANGED');

            $templates[ $ks ]['title']          = 'When RMA is ' . ucwords($vs);
            $templates[ $ks ]['enabled']        = $current_val;
            $templates[ $ks ]['status']         = $ks;
            $templates[ $ks ]['text-body']      = $text_body;
            $templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
            $templates[ $ks ]['textareaNameId'] = $text_area_name_id;
            $templates[ $ks ]['token']          = $variables;
        }
        return $templates;
    }
}
    new sa_Return_Warranty();
?>
