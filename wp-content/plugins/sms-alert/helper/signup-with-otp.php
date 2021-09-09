<?php
/**
 * Signup with otp helper.
 *
 * @package Helper
 */

if (! defined('ABSPATH') ) {
    exit;
}
if (! is_plugin_active('woocommerce/woocommerce.php') ) {
    return;
}
/**
 * WCSignupWithOTp class 
 */
class WCSignupWithOTp
{
    /**
     * Construct function 
     */
    public function __construct()
    {
        $user_authorize = new smsalert_Setting_Options();
        $islogged       = $user_authorize->is_user_authorised();
        if (! $islogged ) {
            return;
        }
        $this->plugin_name = SMSALERT_PLUGIN_NAME_SLUG;
        add_action('sa_addTabs', array( $this, 'add_tabs' ), 100);

        $signup_with_mobile = smsalert_get_option('signup_with_mobile', 'smsalert_general', 'off');

        if ('on' === $signup_with_mobile ) {
            add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100);
            add_shortcode('sf-form', array( $this, 'smsalert_form_render' ), 100);
            add_shortcode('sm-modal', array( $this, 'smsalert_modal_login' ), 100);

            add_action('wp_loaded', array( $this, 'process_registration' ), 10);

            add_action('woocommerce_after_edit_address_form_billing', array( $this, 'update_billing_phone' ), 100);
            add_action('woocommerce_register_form_start', array( $this, 'wooc_extra_register_fields_smsalert' ), 100);
            add_action('smsalert_user_created', array( $this, 'smsalert_wc_update_new_details' ), 100);
            add_action('woocommerce_register_form_end', array( $this, 'smsalert_display_signup_btn' ), 100);
            add_action('woocommerce_login_form_end', array( $this, 'smsalert_display_login_back_btn' ), 100);
        }

        add_filter('sAlertDefaultSettings', array( $this, 'add_default_setting' ), 1);

    }

    /**
     * Smsalert display login back button function. 
     */
    public function smsalert_display_login_back_btn()
    {
        $users_can_register = get_option('woocommerce_enable_myaccount_registration', 'yes');
        if ('yes' === $users_can_register ) {
            ?>
            <div class="signdesc">Don't have an account? <a href="javascript:void(0)" class="signupbutton">Signup</a></div>
            <?php
        }
    }

    /**
     * Display signup button function.
     */
    public function smsalert_display_signup_btn()
    {
        ?>
        <div class="backtoLoginContainer"><a href="javascript:void(0)" class="backtoLogin">Back to login</a></div>
        <?php
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
        $tabs['signupwithotp']['icon']        = 'dashicons-admin-users';
        $tabs['signupwithotp']['title']       = 'Shortcodes';
        $tabs['signupwithotp']['tab_section'] = 'signup_with_phone';
        $tabs['signupwithotp']['tabContent']  = array();
        $tabs['signupwithotp']['filePath']    = 'views/signup-with-otp-template.php';
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
        $defaults['smsalert_mobilein_uname']   = '2';
        $defaults['smsalert_defaultuserrole']  = 'customer';
        $defaults['smsalert_reg_name']         = '0';
        $defaults['smsalert_reg_email']        = '0';
        $defaults['smsalert_reg_mobilenumber'] = '2';
        $defaults['smsalert_otp_user_update']  = 'off';
        return $defaults;
    }

    /**
     * Enqueue scripts function. 
     */
    public function enqueue_scripts()
    {
        $register_otp = smsalert_get_option('buyer_signup_otp', 'smsalert_general', 'on');
        if ('on' === $register_otp ) {
            $smsalert_reg_details = $this->smsalert_get_reg_fields();
            $enable_otp           = get_option('smsalert_otp_user_update', 'on');
            $jsData               = array(
            'mail_accept'       => $smsalert_reg_details['smsalert_reg_email'],
            'signupwithotp'     => esc_html_e('SIGN UP WITH OTP', 'sms-alert'),
            'update_otp_enable' => $enable_otp,
            );
            wp_enqueue_script($this->plugin_name . 'signup_with_otp', SA_MOV_URL . 'js/signup.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, false);
            wp_localize_script($this->plugin_name . 'signup_with_otp', 'smsalert_mdet', $jsData);
        }
    }

    /**
     * Update new details function.
     *
     * @param int $user_id user_id.
     */
    public function smsalert_wc_update_new_details( $user_id )
    {
        $user = get_user_by('ID', $user_id);
        if (! $user ) {
            return false;
        }
        $billing_first_name = get_user_meta($user->ID, 'billing_first_name', true);
        if (! empty($billing_first_name) ) {
            return false;
        }
        if (! empty($user->first_name) ) {
            update_user_meta($user_id, 'billing_first_name', $user->first_name);
        }

        if (! empty($user->user_email) ) {
            update_user_meta($user_id, 'billing_email', $user->user_email);
        }
    }

    /**
     * Register extra fields function. 
     */
    public function wooc_extra_register_fields_smsalert()
    {
        $smsalert_reg_details = self::smsalert_get_reg_fields();
        if (empty($smsalert_reg_details) ) {
            return;
        }
        ?>
    <input type="hidden" name="smsalert_name" id="smsalert_name"/>
        <?php
    }

    /**
     * Update billing phone function. 
     */
    public function update_billing_phone()
    {
        $enable_otp = get_option('smsalert_otp_user_update', 'on');
        if ('on' === $enable_otp ) {
            echo '<div style="clear:both">';
            echo '<input type="hidden" id="old_billing_phone" value="' . esc_attr(get_user_meta(get_current_user_id(), 'billing_phone', true)) . '">';

            echo do_shortcode('[sa_verify id="form1" phone_selector="#billing_phone" submit_selector="save_address"]');
            echo '</div>';
            echo "<script>setTimeout(function(){ jQuery('[name=billing_phone]').trigger('change'); }, 1000);</script>";
        }
    }

    /**
     * Smsalert forms function.
     */
    public function smsalert_forms( $values = '' )
    {
        echo do_shortcode('[woocommerce_my_account]');
        $default  = '';
        $showonly = '';
        if (isset($values['default']) && '' !== $values['default'] ) {
            $default = $values['default'];
        }
        if (isset($values['showonly']) && '' !== $values['showonly'] ) {
            $showonly = $values['showonly'];
        }
        if (( 'register' === $default && '' === $showonly ) || 'register' === $showonly ) {
            echo '<style>.signdesc,.u-column1{display:none;}.u-column2{display:block;}</style>';
        } elseif (( 'login' === $default && '' === $showonly ) || 'login' === $showonly ) {
            echo '<style>.u-column1{display:block;}.u-column2,.signdesc{display:none;}</style>';
        } elseif ('login,register' === $showonly || 'register,login' === $showonly ) {
            if ('login' === $default ) {
                echo '<style>.signdesc,.u-column1{display:block;}.u-column2{display:none;}</style>';
            } else {
                echo '<style>.signdesc,.u-column1{display:none;}.backtoLoginContainer,.u-column2{display:block;}</style>';
            }
        }
    }

    /**
     * Form render function.
     *
     * @param string $values values. 
     */
    public function smsalert_form_render( $values )
    {
        if (is_user_logged_in() ) {
            return '';
        }
        $values = ! empty($values) ? $values : array( 'default' => 'register' );
        ?>
        <div class="smsalert-modal">
        <?php
        $this->smsalert_forms($values);
        ?>
        </div>
        <?php
    }

    /**
     * Modal login function
     *
     * @param array $attrs attrs. 
     */
    public function smsalert_modal_login( $attrs = array() )
    {
        $default       = isset($attrs['default']) ? $attrs['default'] : 'register';
        $showonly      = isset($attrs['showonly']) ? $attrs['showonly'] : '';
        $display_style = isset($attrs['display']) ? $attrs['display'] : 'center';
        $element       = 'onclick="jQuery(\'this\').smsalert_login_modal(jQuery(this));return false;" attr-disclick="1" class="smsalert-login-modal"';

        if (! is_user_logged_in() ) {
            $text = ( '' !== $default ) ? ucfirst($default) : 'Register';
            $url  = '?default=' . $default . '&showonly=' . $showonly;

            if ('' !== $showonly ) {
                $text = $showonly;
            }
            ?>
        <div  class="modal smsalert-modal smsalertModal">
            <div class="modal-content">
                <div class="close"><span></span></div>
                <div class="modal-body">
                    <div class="smsalert_validate_field">
                        <div id="slide_form">
            <?php // echo $this->smsalert_forms(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php
            return '<span href="' . esc_url($url) . '" ' . $element . '" data-display="' . esc_attr($display_style) . '"><a href="javascript:void(0)">' . esc_attr($text) . '</a></span>';
        } else {
            return '';
        }
    }

    /**
     * Get registere fields function.
     * 
     * @param boolean $default default.
     */
    public static function smsalert_get_reg_fields( $default = false )
    {
        $register_otp = smsalert_get_option('buyer_signup_otp', 'smsalert_general', 'on');
        if ('on' !== $register_otp ) {
            return;
        }
        return array(
        'smsalert_reg_name'         => 0,
        'smsalert_reg_email'        => 0,
        'smsalert_reg_mobilenumber' => 2,
        );
    }

    /**
     * Process registration function. 
     */
    public static function process_registration()
    {

        $tname = '';
        $phone = '';
        if (isset($_POST['smsalert_name']) ) {
            $smsalert_reg_details = self::smsalert_get_reg_fields();
            $nameaccep            = $smsalert_reg_details['smsalert_reg_name'];
            $emailaccep           = $smsalert_reg_details['smsalert_reg_email'];
            $mobileaccp           = $smsalert_reg_details['smsalert_reg_mobilenumber'];

            $validation_error = new WP_Error();

            if (isset($_POST['billing_first_name']) ) {
                $name = sanitize_text_field(wp_unslash($_POST['billing_first_name']));
            } else {
                $name = '';
            }

            $mail = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

            $generate_pwd = get_option('woocommerce_registration_generate_password');
            if ('yes' === $generate_pwd ) {
                $password = wp_generate_password();
            } elseif (isset($_POST['password']) ) {
                $password = sanitize_text_field(wp_unslash($_POST['password']));
                if (empty($password) ) {
                    $validation_error->add('Password', esc_html_e('Please enter a valid Password!', 'sms-alert'));
                }
            }

            $error = '';
            $page  = 2;
            if (empty($name) && 2 === $nameaccep ) {
                $validation_error->add('invalidname', esc_html_e('Invalid Name!', 'sms-alert'));
            }

            $m  = isset($_REQUEST['billing_phone']) ? sanitize_email(wp_unslash($_REQUEST['billing_phone'])) : '';
            $m2 = isset($_REQUEST['email']) ? sanitize_text_field(wp_unslash($_REQUEST['email'])) : '';
            if (2 === $emailaccep ) {
                if (empty($mail) || ! is_email($mail) ) {
                    $validation_error->add('Mail', esc_html_e('Please enter a valid Email!', 'sms-alert'));
                }
            } elseif (1 === $emailaccep && ! empty($mail) ) {
                if (! is_email($mail) ) {
                    $validation_error->add('Mail', esc_html_e('Please enter a valid Email!', 'sms-alert'));
                }
            }

            if (! empty($mail) && email_exists($mail) ) {
                $validation_error->add('MailinUse', esc_html_e('Email already in use!', 'sms-alert'));
            }
            $useMobAsUname    = '';
            $auto_user_create = get_option('woocommerce_registration_generate_username', 'yes');
            if ('yes' === $auto_user_create ) {
                $useMobAsUname = get_option('smsalert_mobilein_uname', 0);
            } elseif (isset($_POST['username']) ) {
                $username = sanitize_text_field(wp_unslash($_POST['username']));
                if (empty($username) ) {
                    $validation_error->add('Mail', esc_html_e('Please enter a valid Username!', 'sms-alert'));
                }
            }

            if (3 === $useMobAsUname && empty($username) ) {
                $username = $mail;
            }

            if (! empty($username) ) {
                $ulogin = sanitize_text_field($username);
                $check  = username_exists($ulogin);
                if (! empty($check) ) {
                    $validation_error->add('UsernameInUse', esc_html_e('Username is already in use!', 'sms-alert'));
                }
            } else {
                $auto = 0;
                if (1 === $useMobAsUname && ! empty($m) ) {
                    $tname = $m;
                } elseif (( ! empty($name) || ! empty($mail) ) && 0 === $useMobAsUname ) {
                    $auto = 1;

                    if (! empty($name) ) {
                        $tname = strtolower(preg_replace('/\s*/', '', $name));
                    } elseif (! empty($mail) ) {
                        $tname = strstr($mail, '@', true);
                    }
                } elseif (2 === $useMobAsUname ) {
                    $tname = self::generate_random_number();
                    $check = username_exists($tname);
                    if (! empty($check) ) {
                        while ( ! empty($check) ) {
                               $alt_ulogin = self::generate_random_number();
                               $check      = username_exists($alt_ulogin);

                        }
                        $ulogin = $alt_ulogin;
                    } else {
                        $ulogin = $tname;
                    }
                }

                if (empty($tname) || 1 === $auto ) {
                    if (empty($tname) ) {
                        if (! empty($mail) ) {
                               $tname = strstr($mail, '@', true);
                        } elseif (! empty($m) ) {
                            $tname = $m;
                        }
                    }
                    if (! empty($tname) && username_exists($tname) ) {
                        $check = username_exists($tname);
                        if ($m === $tname && $check ) {
                            $validation_error->add('MobinUse', esc_html_e('Mobile number already in use!', 'sms-alert'));
                        }

                        if (! empty($check) ) {
                            $suffix = 2;
                            while ( ! empty($check) ) {
                                $alt_ulogin = $tname . $suffix;
                                $check      = username_exists($alt_ulogin);
                                $suffix++;
                            }
                            $ulogin = $alt_ulogin;
                        } else {
                            $ulogin = $tname;
                        }
                    } else {
                        $ulogin = $tname;
                    }
                } else {
                    $check = username_exists($tname);
                    if (! empty($check) ) {
                        $suffix = 2;
                        while ( ! empty($check) ) {
                               $alt_ulogin = $tname . $suffix;
                               $check      = username_exists($alt_ulogin);
                               $suffix++;
                        }
                        $ulogin = $alt_ulogin;
                    } else {
                        $ulogin = $tname;
                    }
                }
            }

            $validation_error = apply_filters('woocommerce_process_registration_errors', $validation_error, $username, $password, null);
            if ($mobileaccp > 0 ) {

                $m = isset($_REQUEST['billing_phone']) ? sanitize_text_field(wp_unslash($_REQUEST['billing_phone'])) : '';
                if (is_numeric($m) ) {
                    $m     = sanitize_text_field($m);
                    $phone = $m;

                }

                if (empty($ulogin) ) {
                    $check = username_exists($phone);
                    if (! empty($check) ) {
                        $validation_error->add('MobinUse', esc_html_e('Mobile number already in use!', 'sms-alert'));
                    } else {
                        $ulogin = $phone;
                    }
                }

                $validation_error = apply_filters('woocommerce_registration_errors', $validation_error, $ulogin, $mail);

                if (! $validation_error->get_error_code() ) {

                    if (empty($password) ) {
                        $password = wp_generate_password();
                    }

                    $ulogin       = sanitize_user($ulogin, true);
                    $new_customer = wp_create_user($ulogin, $password, $mail);
                } else {

                }
            } else {
                if (empty($password) && $password === 2 ) {
                    $validation_error->add('invalidpassword', esc_html_e('Invalid password', 'sms-alert'));
                } elseif (empty($password) ) {
                    $password = wp_generate_password();
                }
                if (empty($ulogin) ) {
                    $ulogin = strstr($mail, '@', true);
                    if (username_exists($ulogin) ) {
                        $validation_error->add('MailinUse', esc_html_e('Email is already in use!', 'sms-alert'));
                    }
                }

                if (! $validation_error->get_error_code() ) {
                    $ulogin        = sanitize_user($ulogin, true);
                    $new_customer  = wp_create_user($ulogin, $password, $mail);
                    $login_message = "<span class='msggreen'>User registered successfully.</span>";

                    $page = 1;
                } else {

                }
            }

            if ($validation_error->get_error_code() ) {
                $e = implode('<br />', $validation_error->get_error_messages());

                wc_add_notice('<strong>' . esc_html_e('Error:', 'woocommerce') . '</strong> ' . $e, 'error');

            } else {

                if (! is_wp_error($new_customer) ) {
                    $smsalert_defaultuserrole = get_option('smsalert_defaultuserrole', 'customer');

                    $userdata = array(
                    'ID'         => $new_customer,
                    'user_login' => $ulogin,
                    'user_email' => $mail,
                    'role'       => $smsalert_defaultuserrole,
                    );
                    if (! empty($name) ) {
                        $userdata['first_name']   = $name;
                        $userdata['display_name'] = $name;

                    }

                    $role = array(
                    'ID'   => $new_customer,
                    'role' => $smsalert_defaultuserrole,
                    );
                    if (! empty($name) ) {
                        $role['first_name']   = $name;
                        $role['display_name'] = $name;

                    }

                    wp_update_user($role);

                    $new_customer_data = apply_filters('woocommerce_new_customer_data', $userdata);
                    wp_update_user($new_customer_data);

                    apply_filters('woocommerce_registration_auth_new_customer', true, $new_customer);

                    $new_customer_data['user_pass'] = $password;
                    do_action('woocommerce_created_customer', $new_customer, $new_customer_data, $password);
                    do_action('smsalert_user_created', $new_customer);

                    wc_set_customer_auth_cookie($new_customer);
                    $redirect = wc_get_page_permalink('myaccount');
                    wp_safe_redirect(
                        wp_validate_redirect(
                            apply_filters('woocommerce_registration_redirect', $redirect),
                            wc_get_page_permalink('myaccount')
                        )
                    );
                    exit;
                } else {
                    $validation_error->add('Error', esc_html_e('Please try again', 'sms-alert'));
                }
            }
            unset($_POST);
        }

    }

    /**
     * Generate random number function.
     * 
     * @return string
     */
    public static function generate_random_number()
    {
        $length       = 12;
        $returnString = wp_rand(1, 9);
        $srtlength    = strlen($returnString);
        while ( $srtlength < $length ) {
            $returnString .= wp_rand(0, 9);
        }
        return $returnString;
    }
}
new WCSignupWithOTp();
?>
