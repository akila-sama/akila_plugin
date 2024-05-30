<?php

namespace Akila\Portfolio;

class Endpoints {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_custom_endpoints' ) );
	}

	/**
	 * Register custom REST API endpoints.
	 *
	 * @return void
	 */
	public function register_custom_endpoints() {
		register_rest_route(
			'v1',
			'/custom-endpoint/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'my_custom_endpoint_callback' ),
			)
		);
	}

	/**
	 * Callback function for custom endpoint.
	 *
	 * @param \WP_REST_Request $data The request data.
	 * @return \WP_REST_Response The response data.
	 */
	public function my_custom_endpoint_callback( $data ) {
		$response = array(
			'message'       => 'This is a custom endpoint response',
			'data_received' => $data,
		);
		return rest_ensure_response( $response );
	}
}
