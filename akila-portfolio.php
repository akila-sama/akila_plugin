<?php
// Ensure the file is being called by WordPress
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name: Akila Portfolio
 * Description: Akila Portfolio is a comprehensive portfolio management plugin for WordPress. It allows users to create and manage portfolio items with custom fields, display recent posts by category, and submit portfolio items via a front-end form. It includes AJAX-based functionalities, custom REST API endpoints, and admin page enhancements.
 * Version: 1.0
 * Author: Akila
 * Text Domain: akila-portfolio
 */

/**
 * Define Akila Portfolio plugin directory constant if not already defined.
 */
if ( ! defined( 'AKILA_PORTFOLIO_PLUGIN_DIR' ) ) {
	define( 'AKILA_PORTFOLIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-portfolio.php';
require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-pluginpage.php';
require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-shortcodes.php';
require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-button.php';
require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-endpoints.php';
require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-cron.php';

new APortfolio\Portfolio(); // Initializes the Portfolio class for handling portfolio items
new APortfolio\PluginPage(); // Initializes the PluginPage class for admin page enhancements
new APortfolio\Shortcodes(); // Initializes the Shortcodes class for managing shortcodes
new APortfolio\Button(); // Initializes the Button class for adding a custom button in the plugins page
new APortfolio\Endpoints(); // Initializes the Endpoints class for custom REST API endpoints

/**
 * Function to run on plugin activation.
 * This function sets the permalink structure to '/%postname%/' and flushes the rewrite rules.
 * It also schedules a daily cron event for sending email notifications if not already scheduled.
 *
 * @return void
 */
function ak_activate_plugin() {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules();

	// Schedule the cron event if not already scheduled
	if ( ! wp_next_scheduled( 'akila_portfolio_send_email_notifications' ) ) {
		wp_schedule_event( time(), 'daily', 'akila_portfolio_send_email_notifications' );
	}
}
register_activation_hook( __FILE__, 'ak_activate_plugin' );

/**
 * Function to run on plugin deactivation.
 * This function resets the permalink structure to the default and flushes the rewrite rules.
 * It also clears the scheduled cron event for sending email notifications.
 *
 * @return void
 */
function ak_deactivate_plugin() {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '' );
	$wp_rewrite->flush_rules();

	// Clear the scheduled cron event
	wp_clear_scheduled_hook( 'akila_portfolio_send_email_notifications' );
}
register_deactivation_hook( __FILE__, 'ak_deactivate_plugin' );
