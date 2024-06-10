<?php

namespace APortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Submenu
 * Handles submenu functionalities for the plugin.
 */
class Submenu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ak_custom_submenu' ) );
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

		include AKILA_PORTFOLIO_PLUGIN_DIR . 'templates/tmpl-portfolio-post.php';
	}
}
