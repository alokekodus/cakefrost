<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;"><div class="accordion-section">
<?php $signup_with_mobile = smsalert_get_option( 'signup_with_mobile', 'smsalert_general', 'off' ); ?>
<input type="checkbox" name="smsalert_general[signup_with_mobile]" id="smsalert_general[signup_with_mobile]" class="notify_box" <?php echo ( ( 'on' === $signup_with_mobile ) ? "checked='checked'" : '' ); ?>> <label><?php esc_html_e( 'Signup With Mobile', 'sms-alert' ); ?></label>
	<?php
		$smsalert_defaultuserrole = get_option( 'smsalert_defaultuserrole', 'customer' );

	if ( ! get_role( $smsalert_defaultuserrole ) ) {
		$smsalert_defaultuserrole = 'subscriber';
	}
		$mob_in_uname        = get_option( 'smsalert_mobilein_uname', 0 );
		$username_generation = get_option( 'woocommerce_registration_generate_username', 'yes' );
	?>
	<table class="form-table">
		<tr>
			<th scope="row"><label><?php esc_html_e( 'Username Generation', 'sms-alert' ); ?> </label></th>
			<td>
				<select name="smsalert_mobilein_uname" class="<?php echo ( 'no' === $username_generation ) ? 'anchordisabled' : ''; ?>">
					<option value="3" 
					<?php
					if ( 3 === $mob_in_uname ) {
						echo 'selected="selected"';
					}
					?>
					><?php esc_html_e( 'From Email', 'sms-alert' ); ?></option>
					<option value="2" 
					<?php
					if ( 2 === $mob_in_uname ) {
						echo 'selected="selected"';
					}
					?>
					><?php esc_html_e( 'Random Numbers', 'sms-alert' ); ?></option>
					<option value="1" 
					<?php
					if ( 1 === $mob_in_uname ) {
						echo 'selected="selected"';
					}
					?>
					>
					<?php esc_html_e( 'From Mobile Number', 'sms-alert' ); ?></option>
					<option value="0" 
					<?php
					if ( 0 === $mob_in_uname ) {
						echo 'selected="selected"';
					}
					?>
					>
					<?php esc_html_e( 'From Name', 'sms-alert' ); ?></option>
				</select>
				<?php
				if ( 'no' === $username_generation ) {
					echo '<span class="sa_txt_warning">*To use this fetures, you will have to enable, "Automatically generate an account username" from Woocommerce>> Settings>> Account & Privacy. </span>';
				}
				?>
			</td>
		</tr>
		<tr class="top-border">
			<th scope="row" style="vertical-align:top;">
				<label for="smsalert_defaultuserrole"><?php esc_html_e( 'Default User Role', 'sms-alert' ); ?></label>
			</th>
			<td>
				<select name="smsalert_defaultuserrole" id="smsalert_defaultuserrole">
					<?php
					foreach ( wp_roles()->roles as $rkey => $rvalue ) {

						if ( $rkey === $smsalert_defaultuserrole ) {
							$sel = 'selected=selected';
						} else {
							$sel = '';
						}
						echo '<option value="' . esc_attr( $rkey ) . '" ' . esc_attr( $sel ) . '>' . esc_attr( $rvalue['name'] ) . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
</table>
	<table class="form-table top-border">

		<tbody>
		<?php
		$smsalert_default_reg_fields = array(
			'smsalert_reg_name'         => array(
				'name' => __( 'Name', 'sms-alert' ),
				'id'   => 'name',
			),
			'smsalert_reg_email'        => array(
				'name' => __( 'Email', 'sms-alert' ),
				'id'   => 'email',
			),
			'smsalert_reg_mobilenumber' => array(
				'name' => __( 'Mobile Number', 'sms-alert' ),
				'id'   => 'mobilenumber',
			),

		);
		$smsalert_reg_field_details = WCSignupWithOTp::smsalert_get_reg_fields();
		foreach ( $smsalert_default_reg_fields as $reg_field => $values ) {
			$field_value = $smsalert_reg_field_details[ $reg_field ];
			?>
			<tr>
				<th scope="row"><label><?php echo esc_html( $values['name'] ); ?></label></th>
				<td class="dg_cs_td">                   
					<select name="<?php echo esc_attr( $reg_field ); ?>">
						<option value="2" 
						<?php
						if ( 2 === $field_value ) {
							echo 'selected';
						}
						?>
						>
						<?php esc_html_e( 'Required', 'sms-alert' ); ?></option>
						<option value="1" 
						<?php
						if ( 1 === $field_value ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'Optional', 'sms-alert' ); ?></option>
						<option value="0" 
						<?php
						if ( 0 === $field_value ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'No', 'sms-alert' ); ?></option>
					</select>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
		$shortcodes = array(
			array(
				'label' => __( 'Login/Signup Form', 'smsalert' ),
				'value' => 'sf-form',
			),
			array(
				'label' => __( 'Login/Signup Modal', 'smsalert' ),
				'value' => 'sm-modal',
			),
		);

		foreach ( $shortcodes as $key => $shortcode ) {

			echo '<table class="form-table">';
			$id = 'smsalert_' . esc_attr( $shortcode['value'] ) . '_short';
			?>
			<tr class="top-border">
				<th scope="row">
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $shortcode['label'] ); ?> </label>
				</th>
				<td>
					<div>
						<input type="text" id="<?php echo esc_attr( $id ); ?>" value="[<?php echo esc_attr( $shortcode['value'] ); ?>]" readonly/>		   
						<!--optional attribute-->
						<br/><br/>
						<b><?php esc_html_e( 'Optional Attributes', 'sms-alert' ); ?></b><br />
						<ul>
						<li><b>default</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <?php esc_html_e( 'default form, default is signup form,', 'sms-alert' ); ?></li>
						<li><b>showonly</b> &nbsp;&nbsp;&nbsp;&nbsp; - <?php esc_html_e( 'set which forms you want to show. You can set multiple forms', 'sms-alert' ); ?></li>
						<?php
						if ( 'sm-modal' === $shortcode['value'] ) {
							?>
						<li><b>display</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <?php esc_html_e( 'set from where you want to display modal, default value for display is center.', 'sms-alert' ); ?></li>
							<?php
						}
						?>
						</ul>
						<b>eg</b> : <code>[<?php echo esc_attr( $shortcode['value'] ); ?> default="login" showonly="login" <?php echo ( 'sm-modal' === $shortcode['value'] ) ? 'display="from-left"' : ''; ?>]</code></span>
					<!--/-optional attribute-->
					</div>
				</td>
			</tr>
	</table>   
	<?php } ?>
	</div>
</div>
