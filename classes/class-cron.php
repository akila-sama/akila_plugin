<?php
namespace Akila\Portfolio;

class Cron {
	public function __construct() {
		// Activation hook
		register_activation_hook( AKILA_PORTFOLIO_PLUGIN_DIR . '../akila-portfolio.php', array( $this, 'activate_plugin' ) );

		// Deactivation hook
		register_deactivation_hook( AKILA_PORTFOLIO_PLUGIN_DIR . '../akila-portfolio.php', array( $this, 'deactivate_plugin' ) );

		// Schedule cron event
		add_action( 'init', array( $this, 'schedule_cron_event' ) );

		// Hook for sending email notifications
		add_action( 'akila_portfolio_send_email_notifications', array( $this, 'send_email_notifications' ) );
	}

	/**
	 * Activation hook callback
	 */
	public function activate_plugin() {
		// Schedule the cron event to send email notifications daily
		if ( ! wp_next_scheduled( 'akila_portfolio_send_email_notifications' ) ) {
			wp_schedule_event( time(), 'daily', 'akila_portfolio_send_email_notifications' );
		}
	}

	/**
	 * Deactivation hook callback
	 */
	public function deactivate_plugin() {
		// Clear the scheduled cron event when plugin is deactivated
		wp_clear_scheduled_hook( 'akila_portfolio_send_email_notifications' );
	}

	/**
	 * Schedule cron event
	 */
	public function schedule_cron_event() {
		if ( ! wp_next_scheduled( 'akila_portfolio_send_email_notifications' ) ) {
			wp_schedule_event( time(), 'daily', 'akila_portfolio_send_email_notifications' );
		}
	}

	/**
	 * Send email notifications
	 */
	public function send_email_notifications() {
		// Get all portfolio posts
		$portfolio_posts = get_posts(
			array(
				'post_type'      => 'portfolio',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
			)
		);

		// Loop through portfolio posts
		foreach ( $portfolio_posts as $post_id ) {
			// Get email address from post meta
			$email = get_post_meta( $post_id, 'email', true );

			// Check if email exists and send email
			if ( ! empty( $email ) ) {
				$subject = 'Portfolio Notification';
				$message = 'This is a daily notification for your portfolio post.';
				wp_mail( $email, $subject, $message );
			}
		}
	}
}

new Cron();
