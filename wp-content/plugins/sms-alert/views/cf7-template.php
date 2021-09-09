<?php
$wpcf7 = WPCF7_ContactForm::get_current();
if (empty($wpcf7->id()) ) {
    echo '<h3>';
    esc_html_e('Please save your contact form 7 once.','sms-alert');
    echo '</h3>';
} else {
    ?>
<div id="cf7si-sms-sortables" class="meta-box-sortables ui-sortable">
    <h3><?php esc_html_e('Admin SMS Notifications','sms-alert'); ?></h3>
    <fieldset>
        <legend><?php esc_html_e('In the following fields, you can use these tags:','sms-alert'); ?>
            <br />
    <?php $data['form']->suggest_mail_tags(); ?>
        </legend>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wpcf7-sms-recipient"><?php esc_html_e('To:','sms-alert'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="wpcf7-sms-recipient" name="wpcf7smsalert-settings[phoneno]" class="wide" size="70" value="<?php echo esc_attr($data['phoneno']); ?>">
                        <br/> <?php echo wp_kses_post(__('<small>Enter Numbers By <code>,</code> for multiple</small>','sms-alert')); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wpcf7-mail-body"><?php esc_html_e('Message body:','sms-alert'); ?></label>
                    </th>
                    <td>
                        <textarea id="wpcf7-mail-body" name="wpcf7smsalert-settings[text]" cols="100" rows="6" class="large-text code"><?php echo esc_textarea(trim($data['text'])); ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <hr/>
    <h3><?php esc_html_e('Visitor SMS Notifications','sms-alert'); ?></h3>
    <fieldset>
        <legend><?php esc_html_e('In the following fields, you can use these tags:','sms-alert'); ?>
            <br />
    <?php $data['form']->suggest_mail_tags(); ?>
        </legend>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wpcf7-mail-body"><?php esc_html_e('Visitor Mobile::','sms-alert'); ?></label>
                    </th>
                    <td>
                        <select name="wpcf7smsalert-settings[visitorNumber]" id="visitorNumber">
                        <?php
                        $contact_form = WPCF7_ContactForm::get_instance($wpcf7->id());
                        $form_fields  = $contact_form->scan_form_tags();
                        if (! empty($form_fields) ) {
                            foreach ( $form_fields as $form_field ) {
                                $field = json_decode(wp_json_encode($form_field), true);
                                if ('' !== $field['name'] ) {
								$visitor_no = ( ! empty( $data['visitorNumber'] ) ) ? $data['visitorNumber'] : "";
                                    ?>
                            <option value="<?php echo '[' . esc_attr($field['name']) . ']'; ?>" <?php echo ( '[' . $field['name'] . ']' === $visitor_no ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr($field['name']); ?></option>
                                                   <?php
                                }
                            }
                        }
						
						$visitor_no = ( ! empty( $data['visitorNumber'] ) ) ? $data['visitorNumber'] : "";
						$visitor_msg = ( ! empty( $data['visitorMessage'] ) ) ? $data['visitorMessage'] : "";
						
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wpcf7-mail-body"><?php esc_html_e('Message body:','sms-alert'); ?></label>
                    </th>
                    <td>
                        <textarea id="wpcf7-mail-body" name="wpcf7smsalert-settings[visitorMessage]" cols="100" rows="6" class="large-text code"><?php echo esc_textarea($visitor_msg); ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <!--Group Sync-->
        <hr/>
    <h3><?php esc_html_e('Create Contacts in SMS Alert Group','sms-alert'); ?></h3>
    <fieldset>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wpcf7-mail-body"><?php esc_html_e('Add To Group:','sms-alert'); ?></label>
                    </th>
                    <td>
                        <select name="wpcf7smsalert-settings[smsalert_group]" id="smsalert_group">
                        <?php
                        $groups = json_decode(SmsAlertcURLOTP::group_list(), true);
                        if (! is_array($groups['description']) || array_key_exists('desc', $groups['description']) ) {
                            ?>
                            <option value=""><?php esc_html_e('SELECT', 'sms-alert'); ?></option>
                            <?php
                        } else {
                            foreach ( $groups['description'] as $group ) {
							$smsalert_grp = ( ! empty( $data['smsalert_group'] ) ) ? $data['smsalert_group'] : "";
								
                            ?>
                            <option value="<?php echo esc_attr($group['Group']['name']); ?>" <?php echo ( $smsalert_grp === $group['Group']['name'] ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr($group['Group']['name']); ?></option>
                                <?php
                            }
                        }
                        ?>
                        </select>
                        <?php
                        if (! empty($groups) && ( ! is_array($groups['description']) || array_key_exists('desc', $groups['description']) ) ) {
                            ?>
                            <a href="javascript:void(0)" onclick="create_group(this);" id="create_group" style="text-decoration: none;"><?php esc_html_e('Create Group', 'sms-alert'); ?></a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wpcf7-mail-body"><?php esc_html_e('Name Field:','sms-alert'); ?></label>
                    </th>
                    <td>
                        <select name="wpcf7smsalert-settings[smsalert_name]" id="smsalert_name">
                        <?php
                        $username = smsalert_get_option('smsalert_name', 'smsalert_gateway');
                        $password = smsalert_get_option('smsalert_password', 'smsalert_gateway');

                        $wpcf7        = WPCF7_ContactForm::get_current();
                        $contact_form = WPCF7_ContactForm::get_instance($wpcf7->id());
                        $form_fields  = $contact_form->scan_form_tags();
                        if (! empty($form_fields) ) {
                            foreach ( $form_fields as $form_field ) {
                                $field = json_decode(wp_json_encode($form_field), true);
                                if ('' !== $field['name'] ) {
								
								$smsalert_name = ( ! empty( $data['smsalert_name'] ) ) ? $data['smsalert_name'] : "";
                                    ?>
                            <option value="<?php echo '[' . esc_attr($field['name']) . ']'; ?>" <?php echo ( '[' . $field['name'] . ']' === $smsalert_name ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr($field['name']); ?></option>
                                                   <?php
                                }
                            }
                        }
                        ?>
                        <input type="hidden" name="smsalert_gateway[smsalert_name]" id="smsalert_gateway[smsalert_name]" value="<?php echo esc_attr($username); ?>" data-id="smsalert_name" class="hidden">
                        <input type="hidden" name="smsalert_gateway[smsalert_password]" id="smsalert_gateway[smsalert_password]" value="<?php echo esc_attr($password); ?>" data-id="smsalert_password" class="hidden">
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <!--/-Group Sync-->
</div>
<?php } ?>
