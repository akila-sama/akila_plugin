<?php

namespace Akila\Portfolio;

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
	 */
	public function ak_display_plugin_details() {
		include_once AKILA_PORTFOLIO_PLUGIN_DIR . 'templates/plugin-details.php';
	}

	/**
	* Enqueue AJAX script.
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
	 *
	 * @return void
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
	 *
	 * @return void
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
}
