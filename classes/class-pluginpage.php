<?php

namespace APortfolio;

if ( ! defined( 'AKILA_PORTFOLIO_PLUGIN_URL' ) ) {
	define( 'AKILA_PORTFOLIO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'AKILA_PORTFOLIO_PLUGIN_DIR' ) ) {
	define( 'AKILA_PORTFOLIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
/**
 * Class PluginPage
 * Handles the administration interface and AJAX functionality for the plugin.
 */
class PluginPage {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ak_custom_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ak_enqueue_my_plugin_ajax_script' ) );
		add_action( 'wp_ajax_save_custom_data_ajax', array( $this, 'save_custom_data_ajax' ) );
		add_action( 'admin_menu', array( $this, 'ak_custom_submenu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ak_enqueue_submenu_css' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ak_enqueue_submenu_js' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ak_enqueue_submenu_ajax_script' ) );
		add_action( 'wp_ajax_get_portfolio_posts', array( $this, 'ak_get_portfolio_posts_callback' ) );
		add_action( 'wp_ajax_delete_portfolio_post', array( $this, 'ak_delete_portfolio_post_callback' ) );

		add_action( 'admin_menu', array( $this, 'ak_add_settings_page_submenu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ak_enqueue_scripts' ) );
		add_action( 'wp_ajax_save_settings', array( $this, 'ak_save_settings' ) );
	}

	/**
	 * Enqueue CSS file for plugin details and REST API page in the admin area.
	 */
	public function ak_enqueue_submenu_css() {
		$screen = get_current_screen();
		if ( $screen && ( 'toplevel_page_ak_custom-slug' === $screen->id || 'plugin-details_page_ak_custom-submenu-slug' === $screen->id ) ) {
			wp_enqueue_style( 'portfolio-submission-form', AKILA_PORTFOLIO_PLUGIN_URL . '../css/portfolio-submission-form.css', array(), '1.0' );
		}
	}

	/**
	 * Enqueue JavaScript file for plugin details and REST API page in the admin area.
	 */
	public function ak_enqueue_submenu_js() {
		$screen = get_current_screen();
		if ( $screen && ( 'toplevel_page_custom-slug' === $screen->id || 'toplevel_page_ak_custom-slug' === $screen->id || 'plugin-details_page_ak_custom-submenu-slug' === $screen->id ) ) {
			wp_enqueue_script( 'akila-portfolio-js', AKILA_PORTFOLIO_PLUGIN_URL . '../js/akila-portfolio.js', array( 'jquery' ), '1.0.0', true );
			wp_localize_script(
				'akila-portfolio-js',
				'ak_my_plugin',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
	}

	/**
	 * Enqueue JavaScript file for AJAX request in the admin area only.
	 */
	public function ak_enqueue_submenu_ajax_script() {
		if ( is_admin() ) {
			wp_enqueue_script( 'submenu-ajax-script', AKILA_PORTFOLIO_PLUGIN_URL . '../js/akila-portfolio.js', array( 'jquery' ), '1.0', true );
			wp_localize_script(
				'submenu-ajax-script',
				'submenu_ajax_object',
				array(
					'rest_url' => esc_url_raw( rest_url() ),
					'nonce'    => wp_create_nonce( 'delete_portfolio_post_nonce' ),
				)
			);
		}
	}


	/**
	 * Add a custom menu page.
	 * Registers a custom menu page for displaying plugin details.
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
	 * Render plugin details.
	 * Callback function to display plugin details in the custom menu page.
	 */
	public function ak_display_plugin_details() {
		include_once AKILA_PORTFOLIO_PLUGIN_DIR . 'templates/plugin-details.php';
	}

	/**
	 * Enqueue AJAX script.
	 * Enqueues JavaScript file for handling AJAX requests in the admin area.
	 */
	public function ak_enqueue_my_plugin_ajax_script() {
		wp_enqueue_script( 'my-plugin-ajax-script', AKILA_PORTFOLIO_PLUGIN_URL . '../js/akila-portfolio.js', array( 'jquery' ), '1.0', true );
		wp_localize_script(
			'my-plugin-ajax-script',
			'my_ajax_object',
			array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'custom_data_nonce' ),
			)
		);
	}

	/**
	 * Function to save data to wp-options table via AJAX.
	 * Handles AJAX request to save custom data to the WordPress options table.
	 */
	public function save_custom_data_ajax() {
		check_ajax_referer( 'custom_data_nonce', 'security' );

		if ( isset( $_POST['custom_data'] ) ) {
			update_option( 'custom_data', $_POST['custom_data'] );
			echo 'success';
		} else {
			echo 'error';
		}
		wp_die();
	}

	/**
	* Add a submenu page.
	* Registers a submenu page for managing REST API functionalities.
	*/
	public function ak_custom_submenu() {
		add_submenu_page(
			'ak_custom-slug', // Parent menu slug
			__( 'REST API', 'akila-portfolio' ),
			__( 'REST API', 'akila-portfolio' ),
			'manage_options',
			'ak_custom-submenu-slug', // Menu slug
			array( $this, 'ak_display_submenu_details' )
		);
	}

	/**
	* Update the submenu page callback function to display portfolio posts.
	* Callback function to display portfolio posts in the submenu page.
	*/
	public function ak_display_submenu_details() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Portfolio Posts', 'akila-portfolio' ); ?></h2>
			<div id="portfolio-posts-container"></div> <!-- Container to display portfolio posts -->
			<div id="portfolio-posts-message"></div> <!-- Container for success/error messages -->
		</div>
		<?php
	}

	/**
	 * AJAX function to retrieve portfolio posts.
	 * Handles AJAX request to retrieve portfolio posts.
	 */
	public function ak_get_portfolio_posts_callback() {
		$args = array(
			'post_type'      => 'portfolio',
			'posts_per_page' => -1,
		);

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			include AKILA_PORTFOLIO_PLUGIN_DIR . 'templates/retrieve-portfolio-posts.php';

		} else {
			echo '<p>' . esc_html__( 'No portfolio posts found.', 'akila-portfolio' ) . '</p>';
		}

		die();
	}

	/**
	 * AJAX function to delete portfolio post.
	 * Handles AJAX request to delete a portfolio post.
	 */
	public function ak_delete_portfolio_post_callback() {
		check_ajax_referer( 'delete_portfolio_post_nonce', 'nonce' );

		if ( isset( $_POST['post_id'] ) ) {
			$post_id = absint( $_POST['post_id'] );
			wp_delete_post( $post_id );
			echo 'success';
		} else {
			echo 'error';
		}
		die();
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
	 * Enqueue Scripts
	 *
	 * Enqueue the JavaScript file for AJAX handling.
	 *
	 * @since 1.0.0
	 */
	public function ak_enqueue_scripts() {
		wp_enqueue_script( 'akila-settings-js', AKILA_PORTFOLIO_PLUGIN_URL . '../js/akila-portfolio.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script(
			'akila-settings-js',
			'ak_my_plugin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'akila_settings_nonce' ),
			)
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

		$settings               = get_option(
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

		check_ajax_referer( 'akila_settings_nonce', 'security' );

		$options_array = array(
			'notification_frequency' => isset( $_POST['notification_frequency'] ) ? sanitize_text_field( $_POST['notification_frequency'] ) : 'daily',
			'email_notifications'    => $_POST['email_notifications'] ?? 0,
		);

		update_option( 'akila_portfolio_notification_options', $options_array );

		wp_send_json_success( array( 'message' => esc_html__( 'Settings saved successfully.', 'akila-portfolio' ) ) );
	}

	/**
	 * Get notification interval based on selected frequency.
	 *
	 * @param string $frequency Notification frequency.
	 * @return string Interval for scheduling cron job.
	 */
	private function ak_get_notification_interval( $frequency ) {
		switch ( $frequency ) {
			case 'weekly':
				return 'weekly';
			case 'monthly':
				return 'monthly';
			default:
				return 'daily';
		}
	}
}
