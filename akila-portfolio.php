<?php
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

new Akila\Portfolio\Portfolio(); // Initializes the Portfolio class for handling portfolio items
new Akila\Portfolio\PluginPage(); // Initializes the PluginPage class for admin page enhancements
new Akila\Portfolio\Shortcodes(); // Initializes the Shortcodes class for managing shortcodes
new Akila\Portfolio\Button(); // Initializes the Button class for adding a custom button in the plugins page
new Akila\Portfolio\Endpoints(); // Initializes the Endpoints class for custom REST API endpoints

// Activation hook
function cpp_activate_plugin() {
	// Change permalink structure to Post name
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules(); // To make sure the changes take effect immediately
}
register_activation_hook( __FILE__, 'cpp_activate_plugin' );

// Deactivation hook
function cpp_deactivate_plugin() {
	// Change permalink structure to Plain
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '' );
	$wp_rewrite->flush_rules(); // To make sure the changes take effect immediately
}
register_deactivation_hook( __FILE__, 'cpp_deactivate_plugin' );
