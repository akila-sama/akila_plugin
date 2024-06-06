<?php
// Ensure the file is being called by WordPress
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Akila Portfolio Settings Page Class
 *
 * This class handles the settings page for Akila Portfolio plugin.
 *
 * @package Akila_Portfolio
 * @since 1.0.0
 */
class Settings {

	/**
	 * Constructor
	 *
	 * Initializes the class and sets up necessary hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	/**
	 * Renders the settings page content and handles form submission for enabling/disabling email notifications.
	 *
	 * This method is responsible for rendering the settings page content and handling form submission
	 * to enable or disable email notifications for the Akila Portfolio plugin.
	 *
	 * @since 1.0.0
	 */
	public function akila_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['akila_submit'] ) && wp_verify_nonce( $_POST['akila_settings_nonce'], 'akila_settings_action' ) ) {
			$email_notifications = isset( $_POST['akila_email_notifications'] ) ? 1 : 0;
			update_option( 'akila_email_notifications', $email_notifications );

			$notification_frequency = isset( $_POST['akila_notification_frequency'] ) ? sanitize_text_field( $_POST['akila_notification_frequency'] ) : 'daily';
			update_option( 'akila_notification_frequency', $notification_frequency );

			if ( $email_notifications ) {
				if ( ! wp_next_scheduled( 'akila_portfolio_send_email_notifications' ) ) {
					$interval = $this->get_notification_interval( $notification_frequency );
					wp_schedule_event( time(), $interval, 'akila_portfolio_send_email_notifications' );
				}
			} else {
				wp_clear_scheduled_hook( 'akila_portfolio_send_email_notifications' );
			}

			?>
			<div class="updated"><p><?php esc_html_e( 'Settings saved successfully.', 'akila-portfolio' ); ?></p></div>
			<?php
		}

		$email_notifications    = get_option( 'akila_email_notifications', 1 );
		$notification_frequency = get_option( 'akila_notification_frequency', 'daily' );
		//Searching (akila_notification_frequency) in the database will show that daily, monthly, weekly are set.
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Email Settings', 'akila-portfolio' ); ?></h1>
			<p><?php esc_html_e( 'Please Select Email notification send notification enable Or disable', 'akila-portfolio' ); ?></p>	
			<form method="post" action="">
				<?php wp_nonce_field( 'akila_settings_action', 'akila_settings_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Email Notifications', 'akila-portfolio' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="akila_email_notifications" <?php checked( $email_notifications, 1 ); ?> />
								<?php esc_html_e( 'Enable email notifications', 'akila-portfolio' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Notification Frequency', 'akila-portfolio' ); ?></th>
						<td>
							<select name="akila_notification_frequency">
								<option value="daily" <?php selected( $notification_frequency, 'daily' ); ?>><?php esc_html_e( 'Daily', 'akila-portfolio' ); ?></option>
								<option value="weekly" <?php selected( $notification_frequency, 'weekly' ); ?>><?php esc_html_e( 'Weekly', 'akila-portfolio' ); ?></option>
								<option value="monthly" <?php selected( $notification_frequency, 'monthly' ); ?>><?php esc_html_e( 'Monthly', 'akila-portfolio' ); ?></option>
							</select>
						</td>
					</tr>
				</table>
	
				<input type="hidden" name="akila_submit" value="1" />
				<?php submit_button( esc_html__( 'Save Settings', 'akila-portfolio' ), 'primary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get notification interval based on selected frequency.
	 *
	 * @param string $frequency Notification frequency.
	 * @return string Interval for scheduling cron job.
	 */
	private function get_notification_interval( $frequency ) {
		switch ( $frequency ) {
			case 'weekly':
				return 'weekly';
			case 'monthly':
				return 'monthly';
			default:
				return 'daily';
		}
	}

	/**
	 * Add Settings Page
	 *
	 * Adds the settings page to the WordPress admin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_settings_page() {
		add_menu_page(
			esc_html__( 'Email Settings', 'akila-portfolio' ),
			esc_html__( 'Email Settings', 'akila-portfolio' ),
			'manage_options',
			'akila-settings',
			array( $this, 'akila_settings_page' ),
			'dashicons-admin-generic',
			30
		);
	}
}

new Settings();
