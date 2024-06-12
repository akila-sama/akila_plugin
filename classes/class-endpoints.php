<?php


namespace APortfolio;

/**
 * Class Endpoints
 * Registers and handles custom REST API endpoints.
 */
class Endpoints {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'ak_register_custom_endpoints' ) );
	}

	/**
	 * Register custom REST API endpoints.
	 *
	 * @return void
	 */
	public function ak_register_custom_endpoints() {
		register_rest_route(
			'akila-portfolio/v1',
			'/portfolio-posts',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'ak_get_portfolio_posts_callback' ),
			)
		);
	}

	/**
	 * Callback function for retrieving portfolio posts.
	 *
	 * @param \WP_REST_Request $request The request data.
	 * @return \WP_REST_Response The response data.
	 */
	public function ak_get_portfolio_posts_callback( $request ) {
		$args = array(
			'post_type'      => 'portfolio', // Adjust post type as needed
			'posts_per_page' => -1,
		);

		$query = new \WP_Query( $args );

		$posts = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_data = array(
					'title'        => get_the_title(),
					'client_name'  => get_post_meta( get_the_ID(), 'client_name', true ),
					'company_name' => get_post_meta( get_the_ID(), 'company_name', true ),
					'email'        => get_post_meta( get_the_ID(), 'email', true ),
					'phone'        => get_post_meta( get_the_ID(), 'phone', true ),
					'address'      => get_post_meta( get_the_ID(), 'address', true ),
					'date'         => get_the_date( 'Y-m-d H:i:s' ),
					'post_id'      => get_the_ID(),
				);
				$posts[]   = $post_data;
			}
			wp_reset_postdata();
		}

		// Return portfolio posts as JSON response
		return rest_ensure_response( $posts );
	}
}
