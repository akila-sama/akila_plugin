<?php
namespace APortfolio;

/**
 * Class Cron
 *
 * Handles scheduling and sending of email notifications for portfolio posts.
 */
class Cron {

	/**
	 * Cron constructor.
	 *
	 * Registers actions on initialization.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'schedule_cron_event' ) );

		add_action( 'akila_portfolio_send_email_notifications', array( $this, 'send_email_notifications' ) );
	}

	/**
	 * Schedule cron event.
	 *
	 * Schedules the event to send email notifications daily if not already scheduled.
	 */
	public function schedule_cron_event() {
		// Check if email notifications are enabled
		$options = get_option( 'akila_portfolio_notification_options', array() );
		// Schedule cron event only if email notifications are enabled
		if ( $options['email_notifications'] && ! wp_next_scheduled( 'akila_portfolio_send_email_notifications' ) ) {
			wp_schedule_event( time(), 'daily', 'akila_portfolio_send_email_notifications' );
		}
	}


	/**
	 * Send email notifications.
	 *
	 * Retrieves all published portfolio posts and sends an email notification
	 * to the email addresses associated with each post.
	 */
	public function send_email_notifications() {
		$portfolio_posts = get_posts(
			array(
				'post_type'      => 'portfolio',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
			)
		);

		foreach ( $portfolio_posts as $post_id ) {
			$email = get_post_meta( $post_id, 'email', true );

			if ( ! empty( $email ) ) {
				$subject = esc_html__( 'Portfolio Notification', 'your-text-domain' );
				$message = esc_html__( 'This is a daily notification for your portfolio post.', 'your-text-domain' );

				wp_mail( $email, $subject, $message );

			}
		}
	}
}

new Cron();
