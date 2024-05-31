<?php
namespace AkilaPlugin\Admin;

use function AkilaPlugin\Admin\load_textdomain;
use function AkilaPlugin\Admin\enqueue_portfolio_submission_form_css;
use function AkilaPlugin\Admin\custom_menu;
use function AkilaPlugin\Admin\display_plugin_details;

// Load plugin text domain for translation.
function load_textdomain() {
	load_plugin_textdomain( 'akila-portfolio', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages' );
}
add_action( 'plugins_loaded', 'AkilaPlugin\Admin\load_textdomain' );

// Enqueue CSS file for portfolio submission form.
function enqueue_portfolio_submission_form_css() {
	if ( ! is_admin() ) {
		wp_enqueue_style( 'portfolio-submission-form-style', plugin_dir_url( __FILE__ ) . '/../css/portfolio-submission-form.css', array(), '1.0.0' );
	}
}
add_action( 'admin_enqueue_scripts', 'AkilaPlugin\Admin\enqueue_portfolio_submission_form_css' );

// Add a menu page.
function custom_menu() {
	add_menu_page(
		__( 'Plugin Details', 'akila-portfolio' ), // Page title.
		__( 'Plugin Details', 'akila-portfolio' ), // Menu title.
		'manage_options', // Capability.
		'custom-slug', // Menu slug.
		'AkilaPlugin\Admin\display_plugin_details', // Callback function to render the page content.
		'dashicons-text-page', // Icon URL or Dashicons class.
		25 // Menu position.
	);
}
add_action( 'admin_menu', 'AkilaPlugin\Admin\custom_menu' );

// Function to render plugin details.
function display_plugin_details() {
	include_once plugin_dir_path( __FILE__ ) . '/../templates/plugin-details.php';
}
