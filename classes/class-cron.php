<?php
namespace APortfolio;

/**
 * Class Cron
 *
 * Handles scheduling and sending of email notifications for portfolio posts.
 *
 * @since 1.0.0
 */
class Cron {

	/**
	 * Cron constructor.
	 *
	 * Registers actions on initialization.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'schedule_cron_event' ) );
		add_action( 'akila_portfolio_send_email_notifications', array( $this, 'send_email_notifications' ) );

		// Add custom cron schedule
		add_filter( 'cron_schedules', array( $this, 'add_custom_cron_schedules' ) );
	}

	/**
	 * Add custom cron schedules.
	 *
	 * @since 1.0.0
	 * @param array $schedules The existing schedules.
	 * @return array The modified schedules.
	 */
	public function add_custom_cron_schedules( $schedules ) {
		$schedules['monthly'] = array(
			'interval' => 30 * DAY_IN_SECONDS, // Approximate monthly interval
			'display'  => __( 'Once a Month', 'akila-portfolio' ),
		);
		return $schedules;
	}

	/**
	 * Unschedule existing cron event.
	 *
	 * Unschedules the existing cron event if it is scheduled.
	 *
	 * @since 1.0.0
	 */
	public function unschedule_cron_event() {
		$timestamp = wp_next_scheduled( 'akila_portfolio_send_email_notifications' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'akila_portfolio_send_email_notifications' );
		}
	}

	/**
	 * Schedule cron event.
	 *
	 * Schedules the event to send email notifications based on the selected frequency.
	 *
	 * @since 1.0.0
	 */
	public function schedule_cron_event() {
		$this->unschedule_cron_event();

		$options   = get_option( 'akila_portfolio_notification_options', array() );
		$frequency = isset( $options['notification_frequency'] ) ? $options['notification_frequency'] : 'daily';

		if ( isset( $options['email_notifications'] ) && $options['email_notifications'] ) {
			if ( ! wp_next_scheduled( 'akila_portfolio_send_email_notifications' ) ) {
				wp_schedule_event( time(), $frequency, 'akila_portfolio_send_email_notifications' );
			}
		}
	}

	/**
	 * Send email notifications.
	 *
	 * Retrieves all published portfolio posts and sends an email notification
	 * to the email addresses associated with each post.
	 *
	 * @since 1.0.0
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
				$subject = esc_html__( 'Portfolio Notification', 'akila-portfolio' );
				$message = esc_html__( 'This is a notification for your portfolio post.', 'akila-portfolio' );

				wp_mail( $email, $subject, $message );
			}
		}
	}
}

new Cron();
