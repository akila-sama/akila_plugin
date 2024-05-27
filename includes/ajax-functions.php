<?php
namespace AkilaPlugin\Ajax;

use function AkilaPlugin\Ajax\enqueue_my_plugin_ajax_script;
use function AkilaPlugin\Ajax\save_custom_data_ajax;

// Enqueue AJAX script.
function enqueue_my_plugin_ajax_script() {
	wp_enqueue_script( 'my-plugin-ajax-script', plugin_dir_url( __FILE__ ) . '/../js/akila-plugin.js', array( 'jquery' ), '1.0', true );
	wp_localize_script(
		'my-plugin-ajax-script',
		'my_ajax_object',
		array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'custom_data_nonce' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'AkilaPlugin\Ajax\enqueue_my_plugin_ajax_script' );

// Function to save data to wp-options table via AJAX.
function save_custom_data_ajax() {
	check_ajax_referer( 'custom_data_nonce', 'security' );

	if ( isset( $_POST['custom_data'] ) ) {
		update_option( 'custom_data', $_POST['custom_data'] );
		echo 'success';
	} else {
		echo 'error';
	}
	wp_die();
}
add_action( 'wp_ajax_save_custom_data_ajax', 'AkilaPlugin\Ajax\save_custom_data_ajax' );
