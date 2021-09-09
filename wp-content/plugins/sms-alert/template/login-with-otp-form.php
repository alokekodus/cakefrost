<?php
/**
 * Login with otp form template.
 *
 * @package Template
 */

?>
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label for="username"><?php esc_html_e('Mobile Number', 'sms-alert'); ?><span class="required">*</span></label>
    <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text sa_mobileno phone-valid" name="username"  value="">
    <input type="hidden" class="woocommerce-Input woocommerce-Input--text input-text" name="redirect" value="<?php echo isset($_SERVER['REQUEST_URI']) ? esc_url_raw($_SERVER['REQUEST_URI']) : ''; ?>">
</p>

<p class="form-row">
    <button type="button" class="button smsalert_login_with_otp sa-otp-btn-init" name="smsalert_login_with_otp" value="<?php esc_html_e('Login with OTP', 'sms-alert'); ?>" ><span class="button__text"><?php esc_html_e('Login with OTP', 'sms-alert'); ?></span></button>    
    <a href="javascript:void(0)" class="sa_default_login_form"><?php esc_html_e('Back', 'sms-alert'); ?></a>
</p>
