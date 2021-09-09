<?php
/**
 * Otp popup 2 template.
 *
 * @package Template
 */

$otp_length = esc_attr(SmsAlertUtility::get_otp_length());
echo '<style>.modal{display:none;position:fixed!important;z-index:999999999999;padding-top:100px!important;left:0;top:0;width:100%!important;height:100%!important;overflow:auto;background-color:rgb(0,0,0);background-color:rgba(0,0,0,0.4)!important;}.wpforms-form .smsalertModal .modal-content{width:40%!important}.smsalertModal .modal-content{position:relative;background-color:#fefefe!important;margin:auto!important;padding:0!important;border:1px solid #888;width:40%;}.modal-body{padding:1px!important}.wpforms-form .woocommerce-error, .wpforms-form .woocommerce-info,.wpforms-form .woocommerce-message {background-color:#f7f6f7!important;padding:1em 2em 1em 3.5em!important;}.wpforms-form .woocommerce-error {border-top: 3px solid #b81c23!important;}[name=smsalert_otp_validate_submit]{width:100%!important;margin-top:15px!important;box-sizing: border-box;cursor:pointer;}@media  only screen and (max-width: 767px){.wpforms-form .smsalertModal .modal-content{width:100%!important}.smsalertModal .modal-content{width:100%}}.modal-header{background-color:#5cb85c;color:white;}.modal-footer{background-color:#5cb85c;color:white;}.otp_input{margin-bottom:12px;}.otp_input[type="number"]::-webkit-outer-spin-button, .otp_input[type="number"]::-webkit-inner-spin-button {-webkit-appearance: none;margin: 0;}
.otp_input[type="number"] {-moz-appearance: textfield;}.otp_input{width:100%}
form.sa_popup {overflow:hidden}
form.sa_popup .modal{padding-top:0;}
form.sa_popup .modal-content{width:100%;height:100%;}
form.sa_popup + .sa-lwo-form .smsalertModal{padding-top:0 !important}
form.sa_popup + .sa-lwo-form .modal-content{width:100% !important}
.digit-group .otp_input, .digit-group input[type=number]{display:none!important;}
.wpforms-form .smsalert_otp_validate_submit{background: #f7f6f7!important;}
</style>';
$modal_style = esc_attr(smsalert_get_option('modal_style', 'smsalert_general', 'center'));
echo ' <div class="modal smsalertModal ' . esc_attr($modal_style) . '" data-modal-close="' . esc_attr(substr($modal_style, 0, -2)) . '">
			<div class="modal-content">
			<div class="close"><span></span></div>
			<div class="modal-body" style="padding:1em">
			<div style="margin:1.7em 1.5em;position:relative" class="sa-message">EMPTY</div>
			<div class="smsalert_validate_field digit-group" style="margin:1.5em">
			
<input type="text" class="otp-number" id="digit-1" name="digit-1" onkeyup="return digitGroup(this,event);" data-next="digit-2" style="margin-right: 5px!important;"/>';

$j = $otp_length - 1;
for ( $i = 1; $i < $otp_length; $i++ ) {
    ?>
<input type="text" class="otp-number" id="digit-<?php echo esc_attr($i + 1); ?>" name="digit-<?php echo esc_attr($i + 1); ?>" data-next="digit-<?php echo esc_attr($i + 2); ?>" onkeyup="return digitGroup(this,event);" data-previous="digit-<?php echo esc_attr($otp_length - $j--); ?>" />

    <?php
}
$otp_input = ( ! empty($otp_input_field_nm) ) ? $otp_input_field_nm : 'smsalert_customer_validation_otp_token';

echo '
<input type="number" name="' . esc_attr($otp_input) . '" autofocus="true" placeholder="" id="' . esc_attr($otp_input) . '" class="input-text otp_input" pattern="[0-9]{' . esc_attr($otp_length) . '}" title="' . esc_attr(SmsAlertMessages::showMessage('OTP_RANGE')) . '"/>
';

echo '<br /><button type="button" name="smsalert_otp_validate_submit" style="color:grey; pointer-events:none;" class="button smsalert_otp_validate_submit" value="' . esc_attr(SmsAlertMessages::showMessage('VALIDATE_OTP')) . '">' . esc_attr(SmsAlertMessages::showMessage('VALIDATE_OTP')) . '</button></br><a style="float:right" class="sa_resend_btn" onclick="saResendOTP(this)">' . esc_html__('Resend', 'sms-alert') . '</a><span class="sa_timer" style="min-width:80px; float:right">00:00 sec</span><span class="sa_forgot" style="float:right">Didn\'t receive the code?</span><br /></div></div></div></div>';


echo '<script>
jQuery("form .smsalertModal").on("focus", "input[type=number]", function (e) {
jQuery(this).on("wheel.disableScroll", function (e) {
e.preventDefault();
});
});
jQuery("form .smsalertModal").on("blur", "input[type=number]", function (e) {
jQuery(this).off("wheel.disableScroll");
});
jQuery(".otp_input").attr("minlength", 4);
jQuery(".otp_input").removeAttr("maxlength");
</script>
<style>
form .digit-group{margin:0 0em;text-align: center;}
.digit-group input[type=text] { width: 40px!important;height: 50px;border: 1px solid currentColor!important;line-height: 50px;text-align: center;font-size: 24px;margin: 0 1px;display:inline-block!important;padding:0px;}
input:hover{border:none}
</style>';
?>
