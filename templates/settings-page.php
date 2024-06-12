<?php
/**
 * Settings Page Template
 *
 * This file handles the HTML content for the settings page.
 *
 * @package Akila_Portfolio
 * @since 1.0.0
 */

// Ensure the file is being called by WordPress
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Akila Portfolio Settings', 'akila-portfolio' ); ?></h1>
	<form id="akila-settings-form">
		<?php wp_nonce_field( 'akila_settings_action', 'akila_settings_nonce' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Enable Email Notifications', 'akila-portfolio' ); ?></th>
				<td>
					<input type="checkbox" id="akila_email_notifications" name="akila_email_notifications" <?php checked( $email_notifications, '1' ); ?>>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Notification Frequency', 'akila-portfolio' ); ?></th>
				<td>
					<select id="akila_notification_frequency" name="akila_notification_frequency">
						<option value="daily" <?php selected( $notification_frequency, 'daily' ); ?>><?php esc_html_e( 'Daily', 'akila-portfolio' ); ?></option>
						<option value="weekly" <?php selected( $notification_frequency, 'weekly' ); ?>><?php esc_html_e( 'Weekly', 'akila-portfolio' ); ?></option>
						<option value="monthly" <?php selected( $notification_frequency, 'monthly' ); ?>><?php esc_html_e( 'Monthly', 'akila-portfolio' ); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<input type="submit" id="akila-submit-settings" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'akila-portfolio' ); ?>">
		<div id="akila-message"></div>
	</form>
</div>
