<?php

namespace APortfolio;

require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-restapi.php';
require_once AKILA_PORTFOLIO_PLUGIN_DIR . 'classes/class-settings.php';

if ( ! defined( 'AKILA_PORTFOLIO_PLUGIN_URL' ) ) {
	define( 'AKILA_PORTFOLIO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'AKILA_PORTFOLIO_PLUGIN_DIR' ) ) {
	define( 'AKILA_PORTFOLIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
/**
 * Class PluginPage
 * Handles the administration interface and AJAX functionality for the plugin.
 * @since 1.0.0
 */
class PluginPage {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ak_custom_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ak_admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_save_custom_data_ajax', array( $this, 'save_custom_data_ajax' ) );
		add_action( 'wp_ajax_get_portfolio_posts', array( $this, 'ak_get_portfolio_posts_callback' ) );
		add_action( 'wp_ajax_delete_portfolio_post', array( $this, 'ak_delete_portfolio_post_callback' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) . '/akila-portfolio.php' ), array( $this, 'ak_add_custom_plugin_button' ) );

		new Restapi();
		new Settings();
	}

	/**
	 * Add a custom menu page.
	 * Registers a custom menu page for displaying plugin details.
	 * @since 1.0.0
	 */
	public function ak_custom_menu() {
		add_menu_page(
			__( 'Plugin Details', 'akila-portfolio' ),
			__( 'Plugin Details', 'akila-portfolio' ),
			'manage_options',
			'ak_custom-slug', // Menu slug.
			array( $this, 'ak_display_plugin_details' ),
			'dashicons-text-page', // Icon URL or Dashicons class.
			25
		);
	}

	/**
	 * Add a custom button next to the Deactivate button on the plugins page.
	 *
	 * @since 1.0.0
	 * @param array $links Existing action links.
	 * @return array Modified action links.
	 */
	public function ak_add_custom_plugin_button( $links ) {
		$custom_plugin_page = admin_url( 'admin.php?page=ak_custom-slug' );
		$button_label = esc_html__( 'Plugin Details', 'akila-portfolio' );
		$custom_link = '<a href="' . esc_url( $custom_plugin_page ) . '" class="">' . $button_label . '</a>';
		array_unshift( $links, $custom_link );

		return $links;
	}

	/**
	 * Render plugin details.
	 * Callback function to display plugin details in the custom menu page.
	 * @since 1.0.0
	 */
	public function ak_display_plugin_details() {
		include_once AKILA_PORTFOLIO_PLUGIN_DIR . 'templates/plugin-details.php';
	}

	/**
	 * Enqueue scripts and styles for the admin area.
	 *
	 * This function enqueues necessary scripts and styles for the plugin's admin area.
	 *
	 * @since 1.0.0
	 */
	public function ak_admin_enqueue_scripts() {
		$screen = get_current_screen();

		$screen_array = array(
			'plugin-details_page_ak_custom-submenu-slug',
			'toplevel_page_ak_custom-slug',
			'plugin-details_page_ak_settings-slug',

		);
		if ( in_array( $screen->id, $screen_array, true ) ) {
			wp_enqueue_script( 'akila-portfolio-js', AKILA_PORTFOLIO_PLUGIN_URL . '../js/akila-portfolio.js', array( 'jquery', 'wp-util' ), '1.0.0', true );
			wp_enqueue_style( 'portfolio-submission-form', AKILA_PORTFOLIO_PLUGIN_URL . '../css/portfolio-submission-form.css', array(), '1.0' );

		}

		wp_localize_script(
			'akila-portfolio-js',
			'ak_my_plugin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ak_my_plugin_nonce' ),
				'rest_url' => rest_url(),
			)
		);
	}

	/**
	 * Function to save data to wp-options table via AJAX.
	 * Handles AJAX request to save custom data to the WordPress options table.
	 * @since 1.0.0
	 */
	public function save_custom_data_ajax() {
		check_ajax_referer( 'ak_my_plugin_nonce', 'security' );

		if ( isset( $_POST['custom_data'] ) ) {
			update_option( 'custom_data', $_POST['custom_data'] );
			echo esc_html( 'success' );
		} else {
			echo esc_html( 'error' );
		}
		wp_die();
	}

	/**
	 * AJAX function to delete portfolio post.
	 * Handles AJAX request to delete a portfolio post.
	 * @since 1.0.0
	 */
	public function ak_delete_portfolio_post_callback() {
		check_ajax_referer( 'ak_my_plugin_nonce', 'nonce' );

		if ( isset( $_POST['post_id'] ) ) {
			$post_id = absint( $_POST['post_id'] );
			wp_delete_post( $post_id );
			echo esc_html( 'success' );
		} else {
			echo esc_html( 'error' );
		}
		die();
	}
}
