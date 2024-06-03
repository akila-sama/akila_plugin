<?php

namespace Akila\Portfolio;

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
			'v1',
			'/custom-endpoint/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'ak_my_custom_endpoint_callback' ),
			)
		);
	}

	/**
	 * Callback function for custom endpoint.
	 *
	 * @param \WP_REST_Request $data The request data.
	 * @return \WP_REST_Response The response data.
	 */
	public function ak_my_custom_endpoint_callback( $data ) {
		$message  = esc_html__( 'This is a custom endpoint response', 'text-domain' );
		$response = array(
			'message'       => $message,
			'data_received' => $data,
		);
		return rest_ensure_response( $response );
	}
}
