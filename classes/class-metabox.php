<?php
namespace APortfolio;

/**
 * Dashboard Metabox for Akila Portfolio Plugin.
 *
 * Adds a custom dashboard metabox to display recent portfolio items.
 *
 * @package APortfolio
 * @since 1.0.0
 */
class Metabox {

	/**
	 * Constructor.
	 *
	 * Hooks into WordPress actions to add the dashboard metabox.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'ak_add_dashboard_metabox' ) );
	}

	/**
	 * Add dashboard metabox.
	 *
	 * Registers the dashboard metabox to display recent portfolio items.
	 *
	 * @since 1.0.0
	 */
	public function ak_add_dashboard_metabox() {
		wp_add_dashboard_widget(
			'akila_dashboard_metabox',
			__( 'Akila Portfolio Dashboard', 'akila-portfolio' ),
			array( $this, 'ak_render_dashboard_metabox' )
		);
	}

	/**
	 * Render dashboard metabox content.
	 *
	 * Outputs the content of the dashboard metabox, listing recent portfolio items.
	 *
	 * @since 1.0.0
	 */
	public function ak_render_dashboard_metabox() {
		echo '<h2>' . esc_html__( 'Recent Portfolio Items', 'akila-portfolio' ) . '</h2>';

		// Portfolio Query
		$portfolio_query = new \WP_Query(
			array(
				'post_type'      => 'portfolio',
				'posts_per_page' => 5,  // Adjust as needed
				'order'          => 'DESC',
			)
		);

		if ( $portfolio_query->have_posts() ) {
			echo '<ul>';
			while ( $portfolio_query->have_posts() ) {
				$portfolio_query->the_post();
				$post_id  = get_the_ID();
				$edit_url = get_edit_post_link( $post_id );

				echo '<li><a href="' . esc_url( $edit_url ) . '"><strong>' . esc_html( get_the_title() ) . '</strong></a> - ' . esc_html( get_the_date( 'F jS, g:i a' ) ) . '</li>';
			}
			echo '</ul>';
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__( 'No portfolio items found.', 'akila-portfolio' ) . '</p>';
		}
	}
}
