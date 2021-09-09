<?php
add_thickbox();
$url = add_query_arg(
	array(
		'action'    => 'foo_modal_box',
		'TB_iframe' => 'true',
		'width'     => '800',
		'height'    => '500',
	),
	admin_url( 'admin.php?page=all-order-variable' )
);
?>
<div class="cvt-accordion">
	<div class="accordion-section">
		<a class="cvt-accordion-body-title" href="javascript:void(0)">
			<input type="checkbox" name="smsalert_or_general[customer_notify]" id="smsalert_or_general[customer_notify]" class="notify_box" <?php echo ( ( 'on' === $templates['enabled'] ) ? "checked='checked'" : '' ); ?>/><label><?php echo esc_attr( $templates['title'] ); ?></label>
		</a>
		<div class="">
			<table class="form-table">
				<tr valign="top">
				<td>
					<div class="smsalert_tokens">
					<?php
					foreach ( $templates['token'] as $vk => $vv ) {
						echo  "<a href='#' data-val='".esc_attr($vk)."'>".esc_attr($vv)."</a> | ";
					}
					?>
					<?php if ( ! empty( $templates['moreoption'] ) ) { ?>
						<a href="<?php echo esc_url( $url ); ?>" class="thickbox search-token-btn">[...More]</a>
					<?php } ?>
					</div>
					<textarea data-parent_id="smsalert_or_general[customer_notify]" name="smsalert_or_message[customer_notify]" id="smsalert_or_message[customer_notify]" <?php echo( ( 'on' === $templates['enabled'] ) ? '' : "readonly='readonly'" ); ?>><?php echo esc_textarea( $templates['text-body'] ); ?></textarea>
				</td>
				</tr>
			</table>
		</div>
		<div class="" style="padding: 5px 10px 10px 10px;">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"> <?php esc_html_e( 'Send Review SMS after', 'sms-alert' ); ?> <span class="tooltip" data-title="Enter SMSAlert Password"><span class="dashicons dashicons-info"></span></span>
						</th>
						<td>
							<input type="number" data-parent_id="smsalert_or_general[customer_notify]" name="smsalert_review[schedule_day]" id="smsalert_review[schedule_day]" min="1" max="30" value="<?php echo esc_attr( smsalert_get_option( 'schedule_day', 'smsalert_review', '1' ) ); ?>"  style="width: 36%;"><span class="tooltip" data-title="Max day 30"><span class="dashicons dashicons-info"></span></span>
						</td>
						<th scope="row"><?php esc_html_e( 'Days when order is marked as', 'sms-alert' ); ?><span class="tooltip" data-title="Select Order Status"><span class="dashicons dashicons-info"></span></span>
						</th>
						<td>
							<select name="smsalert_review[review_status]" id="smsalert_review[review_status]" data-parent_id="smsalert_or_general[customer_notify]">
								<option value="completed" selected>
								<?php
								echo esc_html( smsalert_get_option( 'review_status', 'smsalert_review', __( 'Completed', 'sms-alert' ) ) );
								?>
								</option>
								<?php
								$order_statuses = is_plugin_active( 'woocommerce/woocommerce.php' ) ? wc_get_order_statuses() : array();
								foreach ( $order_statuses as $status ) {
									?>
								<option value="<?php echo esc_attr( strtr( strtolower( $status ), ' ', '-' ) ); ?>"><?php echo esc_attr( $status ); ?></option>
								<?php } ?>
							</select>
							<span class="tooltip" data-title="Select Order Status"><span class="dashicons dashicons-info"></span></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
						<input type="checkbox" data-parent_id="smsalert_or_general[customer_notify]" name="smsalert_review[send_at]" id="smsalert_review[send_at]" class="notify_box" <?php echo ( ( smsalert_get_option( 'send_at', 'smsalert_review', 'off' ) === 'on' ) ? "checked='checked'" : '' ); ?>/><?php esc_html_e( 'Send At', 'sms-alert' ); ?> <span class="tooltip" data-title="Send At"><span class="dashicons dashicons-info"></span></span>
						</th>
						<td>
							<input type="time" data-parent_id="smsalert_review[send_at]" name="smsalert_review[schedule_time]" id="smsalert_review[schedule_time]" value="<?php echo esc_attr( smsalert_get_option( 'schedule_time', 'smsalert_review', '10:00' ) ); ?>" ><span class="tooltip" data-title="Schedule time"><span class="dashicons dashicons-info"></span></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
