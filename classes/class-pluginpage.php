<?php

namespace Akila\Portfolio;

class PluginPage {

	public function __construct() {
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
		if ( $screen && ( 'toplevel_page_custom-slug' === $screen->id || 'plugin-details_page_custom-submenu-slug' === $screen->id ) ) {
			wp_enqueue_style( 'portfolio-submission-form', plugin_dir_url( __FILE__ ) . '../css/portfolio-submission-form.css', array(), '1.0' );
		}
	}

	/**
	 * Enqueue JavaScript file for plugin details and REST API page in the admin area.
	 */
	public function ak_enqueue_submenu_js() {
		$screen = get_current_screen();
		if ( $screen && ( 'toplevel_page_custom-slug' === $screen->id || 'plugin-details_page_custom-submenu-slug' === $screen->id ) ) {
			wp_enqueue_script( 'akila-portfolio-js', plugin_dir_url( __FILE__ ) . '../js/akila-portfolio.js', array( 'jquery' ), '1.0.0', true );
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
			wp_enqueue_script( 'submenu-ajax-script', plugin_dir_url( __FILE__ ) . '../js/submenu-ajax.js', array( 'jquery' ), '1.0', true );
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
	 * Add a submenu page.
	 *
	 * @return void
	 */
	public function ak_custom_submenu() {
		add_submenu_page(
			'custom-slug', // Parent menu slug
			__( 'REST API', 'akila-portfolio' ), // Page title
			__( 'REST API', 'akila-portfolio' ), // Menu title
			'manage_options', // Capability
			'custom-submenu-slug', // Menu slug
			array( $this, 'ak_display_submenu_details' ) // Callback function to render the page content
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
			include plugin_dir_path( __FILE__ ) . '../templates/retrieve-portfolio-posts.php';

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

