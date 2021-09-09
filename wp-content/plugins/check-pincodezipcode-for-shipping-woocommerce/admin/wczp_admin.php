<?php

if (!defined('ABSPATH'))
    exit;

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if (!class_exists('WCZP_menu')) {
    class WCZP_menu {
        protected static $instance;
        function WCZP_admin_menu() {
            add_menu_page(
                __( 'Add Post codes', 'wczp' ),
                __( 'Add Post codes', 'wczp' ),
                'manage_options',
                'post-code',
                array($this,'WCZP_add_postcode'),
                'dashicons-schedule',
                10
            );
            
            add_submenu_page( 
                'post-code', 
                __( 'Import Post codes', 'wczp' ), 
                __( 'Import Post codes', 'wczp' ),
                'manage_options', 
                'post-code-import',
                array($this,'WCZP_import_postcode')
            );

            add_submenu_page( 
                'post-code', 
                __( 'List Post codes', 'wczp' ), 
                __( 'List Post codes', 'wczp' ),
                'manage_options', 
                'post-code-list',
                array($this,'WCZP_list_postcode')
            );

            add_submenu_page( 
                'post-code', 
                __( 'Settings', 'wczp' ),  
                __( 'Settings', 'wczp' ),
                'manage_options', 
                'post-code-setting',
                array($this,'WCZP_setting')
            );
        }


        function WCZP_add_postcode() {
            global $wpdb;
            $tablename=$wpdb->prefix.'wczp_postcode';

            if(isset($_REQUEST['action']) && $_REQUEST['action'] == "oc_edit") {
                $pincode = sanitize_text_field($_REQUEST['id']);
                $cntSQL = "SELECT * FROM {$tablename} where id='".$pincode."'";
                $record = $wpdb->get_results($cntSQL, OBJECT);
                ?>
                    <div class="wrap">
                        <div class="wczp_container">
                            <h2>Update Post Code</h2>

                            <?php
                            if(isset($_GET['update']) && $_GET['update'] == 'exists') {
                                echo "<div class='wczp_notice_error'><p>Sorry, pincode already exists in records.</p></div>";
                            }

                            if(isset($_GET['update']) && $_GET['update'] == 'success') {
                                echo "<div class='wczp_notice_success'><p>Pincode updated successfully.</p></div>";
                            }

                            ?>

                            <form method="post">
                                <?php wp_nonce_field( 'WCZP_update_postcode_action', 'WCZP_update_postcode_field' ); ?>
                                <table class="wczp_table">
                                    <tr>
                                        <td>Pincode</td>
                                        <td>
                                            <input type="text" name="txtpincode" value="<?php echo $record[0]->wczp_pincode; ?>" required="">
                                            <input type="hidden" name="txtid" value="<?php echo $record[0]->id; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td><input type="text" name="txtcity" value="<?php echo $record[0]->wczp_city; ?>" required=""></td>
                                    </tr>
                                    <tr>
                                        <td>State</td>
                                        <td><input type="text" name="txtstate" value="<?php echo $record[0]->wczp_state; ?>" required=""></td>
                                    </tr>
                                    <tr>
                                        <td>Delivery within days</td>
                                        <td>
                                            <input type="number" name="txtdelivery" min=1 value="<?php echo $record[0]->wczp_ddate; ?>" required="">
                                            <td><strong>Note : If you add zero so Delivery Day count is same day</strong><td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Shipping Amount</td>
                                        <td>
                                            <input type="number" name="txtshippingamount" min=0 value="<?php echo $record[0]->wczp_ship_amount; ?>" >
                                            <td><strong>Note : If Enable shipping cost in setting so that count shipping amount</strong><td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Cash on Delivery</td>
                                        <td>
                                        	<input type="checkbox" name="txtcod" value="1" <?php if($record[0]->wczp_cod == '1') { echo 'checked'; } ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="action" value="wczp_update_postcode">
                                            <input type="submit" name="wczp_update_postcode" value="Update">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                <?php
            } else {
                ?>
                    <div class="wrap">
                        <div class="wczp_container">
                            <h2>Add Post Code</h2>
                            
                            <?php
                            if(isset($_GET['add']) && $_GET['add'] == 'exists') {
                                echo "<div class='wczp_notice_error'><p>Sorry, pincode already exists in records.</p></div>";
                            }

                            if(isset($_GET['add']) && $_GET['add'] == 'success') {
                                echo "<div class='wczp_notice_success'><p>Pincode added successfully.</p></div>";
                            }

                            ?>

                            <form method="post">
                                <?php wp_nonce_field( 'WCZP_add_postcode_action', 'WCZP_add_postcode_field' ); ?>

                                <table class="wczp_table">
                                    <tr>
                                        <td>Pincode</td>
                                        <td><input type="text" name="txtpincode" <?php if(isset($_GET['txtpincode']) && $_GET['txtpincode'] != '') { echo 'value='.sanitize_text_field( $_GET['txtpincode'] ); } ?> required=""></td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td><input type="text" name="txtcity" <?php if(isset($_GET['txtcity']) && $_GET['txtcity'] != '') { echo 'value='.sanitize_text_field( $_GET['txtcity'] ); } ?> required=""></td>
                                    </tr>
                                    <tr>
                                        <td>State</td>
                                        <td><input type="text" name="txtstate" <?php if(isset($_GET['txtstate']) && $_GET['txtstate'] != '') { echo 'value='.sanitize_text_field( $_GET['txtstate'] ); } ?> required=""></td>
                                    </tr>
                                    <tr>
                                        <td>Shipping Amount</td>
                                        <td>
                                           <input type="text" name="txtshippingamount" <?php if(isset($_GET['txtshippingamount']) && $_GET['txtshippingamount'] != '') { echo 'value='.sanitize_text_field( $_GET['txtshippingamount'] ); } ?> >     
                                           <td><strong>Note : If Enable shipping cost in setting so that count shipping amount</strong><td>                              
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Delivery within days</td>
                                        <td><input type="number" name="txtdelivery" min='1' <?php if(isset($_GET['txtdelivery']) && $_GET['txtdelivery'] != '') { echo 'value='.sanitize_text_field( $_GET['txtdelivery'] ); } ?> required=""></td>
                                        <td><strong>Note : If you add zero so Delivery Day count is same day</strong><td>
                                    </tr>
                                    <tr>
                                        <td>Cash on Delivery</td>
                                        <td>
                                        	<input type="checkbox" name="txtcod" value="1" <?php if(isset($_GET['txtcod']) && $_GET['txtcod'] == '1' ) { echo 'checked'; } ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                          <input type="hidden" name="action" value="wczp_add_postcode">
                                          <input type="submit" name="wczp_add_postcode" value="Add">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                <?php
            }
        }


        function WCZP_import_postcode() {
            ?>
            <div class="wrap">
                <div class="wczp_container">
                    <h2>Bulk Import Post Codes</h2>

                    <?php
                    if(isset($_GET['import']) && $_GET['import'] == 'error') {
                        echo "<div class='wczp_notice_error'><p>Import failed, invalid file extension or something bad happened.</p></div>";
                    }

                    if(isset($_GET['import']) && $_GET['import'] == 'success') {
                        $records = '';
                        if(isset($_GET['records']) && $_GET['records'] != '') {
                            $records = sanitize_text_field($_GET['records']);
                        }
                        echo "<div class='wczp_notice_success'><p>Total Records inserted: ".$records."</p></div>";
                    }

                    ?>

                    <form method='post' enctype='multipart/form-data' class="wczp_import">
                        <?php wp_nonce_field( 'WCZP_import_postcodes_action', 'WCZP_import_postcodes_field' ); ?>
                        <div class="wczp_importbox">
                            <h3>Bulk import postcodes via csv</h3>
                            <input type="file" name="import_file" required="" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            <input type="hidden" name="action" value="wczp_import_postcodes">
                            <input type="submit" name="butimport" value="Import">
                        </div>
                        <a href="<?php echo WCZP_PLUGIN_DIR.'/pincode.csv'; ?>" download='sample_pincode.csv' class="wczp_demo_file">Download sample file</a>
                        <p class="description">This is the sample file of pincodes for csv import.</p>
                    </form>
                </div>
            </div>
            <?php
        }


        function WCZP_list_postcode() {
            $exampleListTable = new WCZP_List_Table();
            $exampleListTable->prepare_items();
            ?>
            <div class="wrap">
                <div class="wczp_container">
                    <h2>List Post Code</h2>

                    <?php
                    if(isset($_GET['delete']) && $_GET['delete'] == 'success') {
                        echo "<div class='wczp_notice_success'><p>Record deleted successfully.</p></div>";
                    }                    
                    ?>

                    <form  method="post" class="wczp_list_postcode">
                        <input type="submit" name="all_record_delete" class="button wczp_all_delete" onclick="return confirm('Are you sure you want to delete this item?');" value="All Record Delete">
                        <?php
                            $page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
                            $paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );

                            printf( '<input type="hidden" name="page" value="%s" />', $page );
                            printf( '<input type="hidden" name="paged" value="%d" />', $paged ); 
                        ?>
                        <?php $exampleListTable->display(); ?>
                    </form>
                </div>
            </div>
            <?php
        }


        function WCZP_setting() {
            ?>
            <div class="wrap">
                <div class="wczp_container">
                    <form method="post" class="oc_wczp">
                        <?php wp_nonce_field( 'wczp_nonce_action', 'wczp_nonce_field' ); ?>
                        <table class="wczp_table">
                            <h2>General Settings</h2>
                            <tr>
                                <td>Enable Pincode Availability Check</td>
                                <td>
                                    <input type="checkbox" name="wczp_enable_checkpcode" <?php if( get_option('wczp_enable_checkpcode', 'on') == 'on' ) { echo 'checked'; } ?>>
                                </td>
                            </tr>
                            <tr>
                                <td>Pincode Availability Check Position</td>
                                <td>
                                    <select name="wczp_checkpcode_pos">
                                        <option value="after_atc" <?php if(get_option('wczp_checkpcode_pos', 'after_atc') == 'after_atc') { echo 'selected'; } ?>>After Add to Cart Button</option>
                                        <option value="before_atc" <?php if(get_option('wczp_checkpcode_pos', 'after_atc') == 'before_atc') { echo 'selected'; } ?>>Before Add to Cart Button</option>
                                        <option value="use_shortcode" <?php if(get_option('wczp_checkpcode_pos', 'after_atc') == 'use_shortcode') { echo 'selected'; } ?>>Use Shortcode</option>
                                    </select>
                                    <p class="wczp_scode_info">You can use shortcode <strong>[wczp_check_pincode]</strong> to place it anywhere you like to use in website and select "Use Shortcode" in above select option.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>Enable Shipping Cost</td>
                                <td>
                                    <input type="checkbox" name="wczp_enable_shipping_cost" <?php if( get_option('wczp_enable_shipping_cost', 'on') == 'on' ) { echo 'checked'; } ?>>
                                </td>
                            </tr>
                        </table>
                        <table class="wczp_table">
                            <h2>Design Settings</h2>
                            <tr class="wczp_color_tr">
                                <td>Box Background Color</td>
                                <td>
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_box_bg_clr', '#e0e0e0' ); ?>" name="wczp_box_bg_clr" value="<?php echo get_option( 'wczp_box_bg_clr', '#e0e0e0' ); ?>"/>
                                </td>
                            </tr>
                            <tr class="wczp_color_tr">
                                <td>Check Input Box Background Color</td>
                                <td>
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_check_ibox_bg_clr', '#101f2b' ); ?>" name="wczp_check_ibox_bg_clr" value="<?php echo get_option( 'wczp_check_ibox_bg_clr', '#101f2b' ); ?>"/>
                                </td>
                            </tr>
                            <tr class="wczp_color_tr">
                                <td>Button Background Color</td>
                                <td>
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_bg_clr', '#205f7d' ); ?>" name="wczp_btn_bg_clr" value="<?php echo get_option( 'wczp_bg_clr', '#205f7d' ); ?>"/>
                                </td>
                            </tr>
                            <tr class="wczp_color_tr">
                                <td>Button Text Color</td>
                                <td>
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_txt_clr', '#ffffff' ); ?>" name="wczp_btn_txt_clr" value="<?php echo get_option( 'wczp_txt_clr', '#ffffff' ); ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Check Button Text</td>
                                <td>
                                    <input type="text" name="wczp_btn_txt" value="Check" disabled="">
                                    <label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr>
                                <td>Change Button Text</td>
                                <td>
                                    <input type="text" name="wczp_chnge_btn_txt" value="Change" disabled="">
                                    <label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr class="wczp_nosrvtxt">
                                <td>Not Serviceable Text</td>
                                <td>
                                    <input type="text" name="wczp_not_serviceable_txt" value="Oops! We are not currently servicing your area." disabled="">
                                    <label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr class="wczp_nosrvtxt">
                                <td>Delivery Date Text</td>
                                <td>
                                    <input type="text" name="wczp_delivery_date_txt" value="<?php echo get_option('wczp_delivery_date_txt', 'Delivery Date'); ?>">
                                </td>
                            </tr>
                            <tr class="wczp_nosrvtxt">
                                <td>Cash On Delivery label text</td>
                                <td>
                                    <input type="text" name="wczp_cash_on_delivery_txt" value="<?php echo get_option('wczp_cash_on_delivery_txt', 'Cash On Delivery'); ?>">
                                </td>
                            </tr>
                            <tr class="wczp_nosrvtxt">
                                <td>Check Availability At label header Text</td>
                                <td>
                                    <input type="text" name="wczp_availability_label_txt" value="<?php echo get_option('wczp_availability_label_txt', 'Check Availability At'); ?>" disabled="">
                                    <label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Show Delivery Date</td>
                                <td>
                                    <input type="checkbox" name="wczp_del_shw" <?php if( get_option('wczp_del_shw', 'on') == 'on' ) { echo 'checked'; } ?>>

                                </td>
                            </tr>
                            <tr>
                                <td>Show Cash On Delivery Option</td>
                                <td>
                                    <input type="checkbox" name="wczp_cash_dilivery_shw" <?php if( get_option('wczp_cash_dilivery_shw', 'on') == 'on' ) { echo 'checked'; } ?>>
                                </td>
                            </tr>
                        </table>
                        <table class="wczp_table">
                            <h2>Pincode Check Popup</h2>
	                            <tr>
	                                <td>Auto Load Popup in All Pages</td>
	                                <td>
	                                   <input type="checkbox" name="wczp_all_pages_pop_shw" <?php if( get_option('wczp_all_pages_pop_shw', 'on') == 'on' ) { echo 'checked'; } ?>>Enable
	                            	</td>
	                            </tr>
	                            <tr>
	                                <td>Exclude Popup from Pages</td>
	                                <td>
	                                   <input type="text" name="wczp_excld_pages_pop_shw" value="" disabled="">
                                       <label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
	                            	</td>
	                            </tr>
	                            <tr>
	                                <td>Force Popup for Pincode</td>
	                                <td>
	                                   	<input type="checkbox" name="wczp_force_pp_for_pcode" disabled="">Enable
	                            		<label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
	                            	</td>
	                            </tr>
                                <tr>
                                    <td>Popup Background Color</td>
                                    <td>
                                        <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_cpin_pp_bg_clr', '#8BC34A' ); ?>" name="wczp_cpin_pp_bg_clr" value="<?php echo get_option( 'wczp_cpin_pp_bg_clr', '#8BC34A' ); ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Popup Text Color</td>
                                    <td>
                                       <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_cpin_pp_txt_clr', '#ffffff' ); ?>" name="wczp_cpin_pp_txt_clr" value="<?php echo get_option( 'wczp_cpin_pp_txt_clr', '#ffffff' ); ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Popup Submit Button Background Color</td>
                                    <td>
                                       <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_cpin_pp_sbtn_bg_clr', '#3f51b5' ); ?>" name="wczp_cpin_pp_sbtn_bg_clr" value="<?php echo get_option( 'wczp_cpin_pp_sbtn_bg_clr', '#3f51b5' ); ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Popup Submit Button Text Color</td>
                                    <td>
                                       <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo get_option( 'wczp_cpin_pp_sbtn_txt_clr', '#ffffff' ); ?>" name="wczp_cpin_pp_sbtn_txt_clr" value="<?php echo get_option( 'wczp_cpin_pp_sbtn_txt_clr', '#ffffff' ); ?>"/>
                                    </td>
                                </tr>
                        </table>
                        <table class="wczp_table">
                            <h2>Checkout Settings</h2>
                            <tr>
                                <td>Hide Place Order Button if Pincode is not Available in List</td>
                                <td>
                                   <input type="checkbox" name="wczp_checkpincode"  disabled="">Enable
                                   <label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr>
                                <td>Hide Place Order Button and notice text </td>
                                <td>
                                  <input type="text" name="wczp_place_order_button_txt" value="<?php echo get_option('wczp_place_order_button_txt', 'Choose valid zipcode in Billing Form then place order'); ?>" disabled="">
                                  <label class="wczp_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/check-pincode-zipcode-for-shipping-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>     
                        </table>
                        <input type="hidden" name="action" value="WCZP_settings_save">
                        <input type="submit" name="wczp_txtadd_design" value="Save">
                    </form>
                </div>
            </div>
            <?php
        }

        function WCZP_save_options() {
            if( current_user_can('administrator') ) { 
                global $wpdb;
                $tablename=$wpdb->prefix.'wczp_postcode';

                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'WCZP_settings_save') {
                    if(!isset( $_POST['wczp_nonce_field'] ) || !wp_verify_nonce( $_POST['wczp_nonce_field'], 'wczp_nonce_action' )) {
                        
                        echo 'Sorry, your nonce did not verify.';
                        exit;

                    } else {

                        if(isset($_REQUEST['wczp_enable_checkpcode']) && !empty($_REQUEST['wczp_enable_checkpcode'])) {
                            $wczp_enable_checkpcode = sanitize_text_field( $_REQUEST['wczp_enable_checkpcode'] );
                        } else {
                            $wczp_enable_checkpcode = 'off';
                        }
                        if(isset($_REQUEST['wczp_enable_shipping_cost']) && !empty($_REQUEST['wczp_enable_shipping_cost'])) {
                            $wczp_enable_shipping_cost = sanitize_text_field( $_REQUEST['wczp_enable_shipping_cost'] );
                        } else {
                            $wczp_enable_shipping_cost = 'off';
                        }


                        if(isset($_REQUEST['wczp_del_shw']) && !empty($_REQUEST['wczp_del_shw'])) {
                            $wczp_del_shw = sanitize_text_field( $_REQUEST['wczp_del_shw'] );
                        } else {
                            $wczp_del_shw = 'off';
                        }

                          if(isset($_REQUEST['wczp_cash_dilivery_shw']) && !empty($_REQUEST['wczp_cash_dilivery_shw'])) {
                            $wczp_cash_dilivery_shw = sanitize_text_field( $_REQUEST['wczp_cash_dilivery_shw'] );
                        } else {
                            $wczp_cash_dilivery_shw = 'off';
                        }


                        if(isset($_REQUEST['wczp_all_pages_pop_shw']) && !empty($_REQUEST['wczp_all_pages_pop_shw'])) {
                            $wczp_all_pages_pop_shw = sanitize_text_field( $_REQUEST['wczp_all_pages_pop_shw'] );
                        } else {
                            $wczp_all_pages_pop_shw = 'off';
                        }

                        update_option( 'wczp_delivery_date_txt', sanitize_text_field( $_REQUEST['wczp_delivery_date_txt']));
                        update_option( 'wczp_enable_shipping_cost', sanitize_text_field( $wczp_enable_shipping_cost ));
                        update_option( 'wczp_cash_on_delivery_txt', sanitize_text_field( $_REQUEST['wczp_cash_on_delivery_txt']));
                         // update_option( 'wczp_availability_label_txt', sanitize_text_field( $_REQUEST['wczp_availability_label_txt']));
                        // if(isset($_REQUEST['wczp_checkpincode']) && !empty($_REQUEST['wczp_checkpincode'])) {
                        //     $wczp_checkpincode = sanitize_text_field( $_REQUEST['wczp_checkpincode'] );
                        // } else {
                        //     $wczp_checkpincode = 'off';
                        // }
                        
                        update_option( 'wczp_enable_checkpcode', sanitize_text_field( $wczp_enable_checkpcode ));
                        update_option( 'wczp_checkpcode_pos', sanitize_text_field( $_REQUEST['wczp_checkpcode_pos']));
                        update_option( 'wczp_box_bg_clr', sanitize_text_field( $_REQUEST['wczp_box_bg_clr']));
                        update_option( 'wczp_check_ibox_bg_clr', sanitize_text_field( $_REQUEST['wczp_check_ibox_bg_clr']));
                        update_option( 'wczp_bg_clr', sanitize_text_field( $_REQUEST['wczp_btn_bg_clr']) );
                        update_option( 'wczp_txt_clr', sanitize_text_field( $_REQUEST['wczp_btn_txt_clr']));
                        update_option( 'wczp_cash_dilivery_shw', sanitize_text_field( $wczp_cash_dilivery_shw ));
                        update_option( 'wczp_del_shw', sanitize_text_field( $wczp_del_shw ));
                        update_option( 'wczp_all_pages_pop_shw', sanitize_text_field( $wczp_all_pages_pop_shw ));
                        update_option( 'wczp_cpin_pp_bg_clr', sanitize_text_field( $_REQUEST['wczp_cpin_pp_bg_clr']) );
                        update_option( 'wczp_cpin_pp_txt_clr', sanitize_text_field( $_REQUEST['wczp_cpin_pp_txt_clr']) );
                        update_option( 'wczp_cpin_pp_sbtn_bg_clr', sanitize_text_field( $_REQUEST['wczp_cpin_pp_sbtn_bg_clr']) );
                        update_option( 'wczp_cpin_pp_sbtn_txt_clr', sanitize_text_field( $_REQUEST['wczp_cpin_pp_sbtn_txt_clr']) );
                        //update_option( 'wczp_checkpincode', sanitize_text_field( $wczp_checkpincode ));
                    }
                }


                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'wczp_add_postcode') {
                    if(!isset( $_POST['WCZP_add_postcode_field'] ) || !wp_verify_nonce( $_POST['WCZP_add_postcode_field'], 'WCZP_add_postcode_action' )) {
                        
                        echo 'Sorry, your nonce did not verify.';
                        exit;

                    } else {

                        $pincode = sanitize_text_field( $_REQUEST['txtpincode']);
                        $city = sanitize_text_field( $_REQUEST['txtcity']);
                        $state = sanitize_text_field( $_REQUEST['txtstate']);
                        $ddate = sanitize_text_field( $_REQUEST['txtdelivery']);
                        $ship_amount = sanitize_text_field( $_REQUEST['txtshippingamount']);

                        if(isset($_POST['txtcod']) && $_POST['txtcod'] != '') {
                        	$cod = sanitize_text_field($_POST['txtcod']);
                        } else {
                        	$cod = '0';
                        }

                        $cntSQL = "SELECT count(*) as count FROM {$tablename} where wczp_pincode='".$pincode."'";
                        $record = $wpdb->get_results($cntSQL, OBJECT);
                        
                        if($record[0]->count == 0) {
                            if(!empty($pincode) && !empty($city) && !empty($state)  ) {
                                $data=array(
                                    'wczp_pincode' 	=> $pincode,
                                    'wczp_city' 	=> $city, 
                                    'wczp_state' 	=> $state,
                                    'wczp_ddate' 	=> $ddate,
                                    'wczp_ship_amount' => $ship_amount,
                                    'wczp_cod'		=> $cod

                                );
                                $wpdb->insert( $tablename, $data);
                                wp_redirect(admin_url('admin.php?page=post-code&add=success'));
                                exit;
                            }
                        } else {
                            wp_redirect(admin_url('admin.php?page=post-code&add=exists&txtpincode='.$pincode.'&txtcity='.$city.'&txtstate='.$state.'&txtdelivery='.$ddate.'&txtcod='.$cod));
                            exit;
                        }
                    }
                }


                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'wczp_update_postcode') {
                    if(!isset( $_POST['WCZP_update_postcode_field'] ) || !wp_verify_nonce( $_POST['WCZP_update_postcode_field'], 'WCZP_update_postcode_action' )) {
                        
                        echo 'Sorry, your nonce did not verify.';
                        exit;

                    } else {

                        $pincode_exists = 'false';
                        $id = sanitize_text_field( $_REQUEST['txtid']);
                        $pincode = sanitize_text_field( $_REQUEST['txtpincode']);
                        $city = sanitize_text_field( $_REQUEST['txtcity']);
                        $state = sanitize_text_field( $_REQUEST['txtstate']);
                        $ddate = sanitize_text_field( $_REQUEST['txtdelivery']);
                        $ship_amount = sanitize_text_field( $_REQUEST['txtshippingamount']);

                        if(isset($_POST['txtcod']) && $_POST['txtcod'] != '') {
                        	$cod = sanitize_text_field($_POST['txtcod']);
                        } else {
                        	$cod = '0';
                        }

                        $cntSQL = "SELECT * FROM {$tablename} where id='".$id."'";
                        $record = $wpdb->get_results($cntSQL, OBJECT);

                        $cntSQL_new = "SELECT count(*) as count FROM {$tablename} where wczp_pincode='".$pincode."'";
                        $record_new = $wpdb->get_results($cntSQL_new, OBJECT);

                        $current_pincode = $record[0]->wczp_pincode;
                        $count_new = $record_new[0]->count;
                        
                        if($pincode != $current_pincode) {
                            if($count_new > 0 ) {
                                $pincode_exists = 'true';
                            }
                        }


                        if($pincode_exists == 'false') {
                            if(!empty($pincode) && !empty($city) && !empty($state)  ) {
                                $data=array(
                                    'wczp_pincode' 	=> $pincode,
                                    'wczp_city' 	=> $city, 
                                    'wczp_state' 	=> $state,
                                    'wczp_ddate' 	=> $ddate,
                                    'wczp_ship_amount' => $ship_amount,
                                    'wczp_cod' 		=> $cod,

                                );
                                $condition=array(
                                    'id'=>$id
                                );

                                $wpdb->update($tablename, $data, $condition);
                                wp_redirect(admin_url('admin.php?page=post-code&action=oc_edit&id='.$id.'&update=success'));
                                exit;
                            }
                        } else {
                            wp_redirect(admin_url('admin.php?page=post-code&action=oc_edit&id='.$id.'&update=exists'));
                            exit;
                        }
                    }
                }


                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'wczp_import_postcodes') {
                    if(!isset( $_POST['WCZP_import_postcodes_field'] ) || !wp_verify_nonce( $_POST['WCZP_import_postcodes_field'], 'WCZP_import_postcodes_action' )) {
                        
                        echo 'Sorry, your nonce did not verify.';
                        exit;

                    } else {

                        if(isset($_POST['butimport'])) {

                            // File extension
                            $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

                            // If file extension is 'csv'
                            if(!empty($_FILES['import_file']['name']) && $extension == 'csv') {

                                $totalInserted = 0;
                         
                                // Open file in read mode
                                $csvFile = fopen($_FILES['import_file']['tmp_name'], 'r');
                                fgetcsv($csvFile); // Skipping header row

                                // Read file
                                while(($csvData = fgetcsv($csvFile)) !== FALSE) {
                                    $csvData = array_map("utf8_encode", $csvData);

                                    // Assign value to variables
                                    $pincode 	= trim($csvData[0]);
                                    $city 		= trim($csvData[1]);
                                    $state 		= trim($csvData[2]);
                                    $ddate 		= trim($csvData[3]);
                                    $ship_amount      = trim($csvData[4]);
                                    $cod 		= trim($csvData[5]);
                              
                                    $cntSQL = "SELECT count(*) as count FROM {$tablename} where wczp_pincode='".$pincode."'";
                                    $record = $wpdb->get_results($cntSQL, OBJECT);

                                    if($record[0]->count == 0) {

                                        // Check if variable is empty or not
                                        if($pincode!="" && $city!="" && $state!="" ) {
                                            // Insert Record
                                            $wpdb->insert($tablename, array(
                                               'wczp_pincode' 	=> $pincode,
                                               'wczp_city' 		=> $city,
                                               'wczp_state' 	=> $state,
                                               'wczp_ddate' 	=> $ddate,
                                               'wczp_ship_amount' => $ship_amount,
                                               'wczp_cod' 		=> $cod
                                            ));
                                            if($wpdb->insert_id > 0) {
                                               $totalInserted++;
                                            }
                                        }
                                    }
                                }
                                wp_redirect(admin_url('admin.php?page=post-code-import&import=success&records='.$totalInserted));
                                exit;
                            } else {
                                wp_redirect(admin_url('admin.php?page=post-code-import&import=error'));
                                exit;
                            }
                        }
                    }
                }


                if (isset($_REQUEST['action']) && $_REQUEST['action'] == "wczp_delete") {
                    if( wp_verify_nonce( $_GET['_wpnonce'], 'my_nonce' ) ) {
                        $pincode = sanitize_text_field($_REQUEST['id']);
                        $sql = "DELETE FROM $tablename WHERE id='".$pincode."'";
                        $wpdb->query($sql);
                        wp_redirect(admin_url('admin.php?page=post-code-list&delete=success'));
                        exit;
                    } else {
                        echo 'Sorry, your nonce did not verify.';
                        exit;
                    }
                }

                if(isset($_REQUEST['all_record_delete']) ){
                        $sql = "DELETE FROM $tablename";
                        $wpdb->query($sql);
                        wp_redirect(admin_url('admin.php?page=post-code-list&delete=success'));
                        exit;
                }
            }
        }

        function WCZP_support_and_rating_notice() {
            $screen = get_current_screen();
             //print_r($screen);
            if( 'post-code' == $screen->parent_base) {
                ?>
                <div class="wczp_mainnn_rantiong">
                    <div class="wczp_rateus_notice">
                        <div class="wczp_rtusnoti_left">
                            <h3>Rate Us</h3>
                            <label>If you like our plugin, </label>
                            <a target="_blank" href="https://wordpress.org/support/plugin/check-pincodezipcode-for-shipping-woocommerce/reviews/?filter=5#new-post">
                                <label>Please vote us</label>
                            </a>
                            <label>, so we can contribute more features for you.</label>
                        </div>
                        <div class="wczp_rtusnoti_right">
                            <img src="<?php echo WCZP_PLUGIN_DIR;?>/includes/images/review.png" class="wczp_review_icon">
                        </div>
                    </div>
                    <div class="wczp_support_notice">
                        <div class="wczp_rtusnoti_left">
                            <h3>Having Issues?</h3>
                            <label>You can contact us at</label>
                            <a target="_blank" href="https://www.xeeshop.com/support-us/?utm_source=aj_plugin&utm_medium=plugin_support&utm_campaign=aj_support&utm_content=aj_wordpress">
                                <label>Our Support Forum</label>
                            </a>
                        </div>
                        <div class="wczp_rtusnoti_right">
                            <img src="<?php echo WCZP_PLUGIN_DIR;?>/includes/images/support.png" class="wczp_review_icon">
                        </div>
                    </div>
                </div>
                <div class="wczp_donate_main">
                   <img src="<?php echo WCZP_PLUGIN_DIR;?>/includes/images/coffee.svg">
                   <h3>Buy me a Coffee !</h3>
                   <p>If you like this plugin, buy me a coffee and help support this plugin !</p>
                   <div class="wczp_donate_form">
                      <a class="button button-primary ocwg_donate_btn" href="https://www.paypal.com/paypalme/shayona163/" data-link="https://www.paypal.com/paypalme/shayona163/" target="_blank">Buy me a coffee !</a>
                   </div>
                </div>
                <?php
            }
        }

        function init() {
            add_action( 'admin_menu', array($this, 'WCZP_admin_menu') );
            add_action( 'init', array($this, 'WCZP_save_options') );
            add_action( 'admin_notices', array($this, 'WCZP_support_and_rating_notice' ));
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    WCZP_menu::instance();
}


class WCZP_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct(
            array(
                'singular' => 'singular_form',
                'plural'   => 'plural_form',
                'ajax'     => false
            )
        );
    }


    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
        $this->process_bulk_action();
    }
   

    public function get_columns() {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'pincode'     => 'Pincode',
            'city'        => 'City',
            'state'       => 'State',
            'date'        => 'Delivery Day',
            'shipping_amount' =>'Shipping Amount',
            'cod'        => 'Cash on Delivery',
        );
        return $columns;
    }
   

    public function get_hidden_columns() {
        return array();
    }
  

    public function get_sortable_columns() {
        return array('pincode' => array('pincode', false));
    }


    private function table_data() {
        $data = array();
        global $wpdb;
        $tablename = $wpdb->prefix.'wczp_postcode';
        $wczp_records = $wpdb->get_results( "SELECT * FROM $tablename" );
        foreach ($wczp_records as $wczp_record) {

        	if($wczp_record->wczp_cod == '1') {
        		$cod = 'Yes';
        	} else {
        		$cod = 'No';
        	}

            $data[] = array(
                'id'          => $wczp_record->id,
                'pincode'     => $wczp_record->wczp_pincode,
                'city'        => $wczp_record->wczp_city,
                'state'       => $wczp_record->wczp_state,
                'date'        => $wczp_record->wczp_ddate,
                'shipping_amount' =>$wczp_record->wczp_ship_amount,
                'cod'         => $cod,
            );
        }
        return $data;
    }
   

    public function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
                return $item['id'];
            case 'pincode':
                return $item['pincode'];
            case 'city':
                return $item['city'];
            case 'state':
                return $item['state'];
            case 'date':
                return $item['date'];
            case 'shipping_amount':
                return $item['shipping_amount'];
            case 'cod':
                return $item['cod'];
            default:
                return print_r( $item, true ) ;
        }
    }


    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'pincode';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc') {
            return $result;
        }
        return -$result;
    }


    public function get_bulk_actions() {
        return array(
            'delete' => __( 'Delete', 'wczp' ),
        );
    }


    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />', $item['id']
        );    
    }

    function WCZP_recursive_sanitize_text_field($array) {
         
        foreach ( $array as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = $this->WCZP_recursive_sanitize_text_field($value);
            }else{
                $value = sanitize_text_field( $value );
            }
        }
        return $array;
    }



    public function process_bulk_action() {
        global $wpdb;
        $tablename = $wpdb->prefix.'wczp_postcode';
        // security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );
        }

        $action = $this->current_action();
        switch ( $action ) {

            case 'delete':
                $ids = isset($_REQUEST['id']) ? $this->WCZP_recursive_sanitize_text_field($_REQUEST['id']) : array();
                if (is_array($ids)) $ids = implode(',', $ids);

                    if (!empty($ids)) {
                        $wpdb->query("DELETE FROM $tablename WHERE id IN($ids)");
                    }

                wp_redirect( $_SERVER['HTTP_REFERER'] );

                break;

            default:
                // do nothing or something else
                return;
                break;
        }
        return;
    }


    function column_pincode($item) {

        $delete_url = wp_nonce_url( admin_url().'?page=post-code&action=wczp_delete&id='.$item['id'], 'my_nonce' );
        
        $actions = array(
            'edit'      => sprintf('<a href="?page=post-code&action=%s&id=%s">Edit</a>','oc_edit',$item['id']),
            'delete'    => '<a href="'.$delete_url.'">Delete</a>',
        );

        return sprintf('%1$s %2$s', $item['pincode'], $this->row_actions($actions) );
    }
}