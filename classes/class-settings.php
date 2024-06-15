<?php

namespace APortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Settings
 * Handles settings page functionalities for the plugin.
 * @since 1.0.0
 */
class Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ak_add_settings_page_submenu' ) );
		add_action( 'wp_ajax_save_settings', array( $this, 'ak_save_settings' ) );
	}

	/**
	 * Add Settings Page as Submenu
	 *
	 * Adds the settings page as a submenu under the plugin's main menu.
	 *
	 * @since 1.0.0
	 */
	public function ak_add_settings_page_submenu() {
		add_submenu_page(
			'ak_custom-slug', // Parent menu slug
			esc_html__( 'Email Settings', 'akila-portfolio' ),
			esc_html__( 'Email Settings', 'akila-portfolio' ),
			'manage_options',
			'ak_settings-slug',
			array( $this, 'ak_settings_page' ),
			null // Menu position (set to null to avoid errors)
		);
	}

	/**
	 * Renders the settings page content and handles form submission for enabling/disabling email notifications.
	 *
	 * This method is responsible for rendering the settings page content and handling form submission
	 * to enable or disable email notifications for the Akila Portfolio plugin.
	 *
	 * @since 1.0.0
	 */
	public function ak_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = get_option(
			'akila_portfolio_notification_options',
			array(
				'email_notifications'    => 1,
				'notification_frequency' => 'daily',
			)
		);

		$email_notifications    = $settings['email_notifications'];
		$notification_frequency = $settings['notification_frequency'];

		include AKILA_PORTFOLIO_PLUGIN_DIR . 'templates/settings-page.php';
	}

	/**
	 * AJAX Handler to Save Settings
	 *
	 * @since 1.0.0
	 */
	public function ak_save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'akila-portfolio' ) ) );
		}

		check_ajax_referer( 'ak_my_plugin_nonce', 'security' );

		$options_array = array(
			'notification_frequency' => isset( $_POST['notification_frequency'] ) ? sanitize_text_field( $_POST['notification_frequency'] ) : 'daily',
			'email_notifications'    => $_POST['email_notifications'] ?? 0,
		);

		update_option( 'akila_portfolio_notification_options', $options_array );

		// Reschedule the cron event based on the new settings
		$cron = new Cron();
		$cron->schedule_cron_event();

		wp_send_json_success( array( 'message' => esc_html__( 'Settings saved successfully.', 'akila-portfolio' ) ) );
	}
}
