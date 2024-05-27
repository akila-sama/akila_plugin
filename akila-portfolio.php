<?php
/**
 * Plugin Name: Akila Portfolio
 * Description: Description of your plugin.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: akila-portfolio
 */

// Include necessary files.
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax-functions.php';

/**
 * Register Custom Post Type.
 *
 * @return void
 */
function ak_custom_portfolio_post_type() {

	$labels = array(
		'name'                  => _x( 'Portfolio', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Portfolio Item', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Portfolio', 'text_domain' ),
		'name_admin_bar'        => __( 'Portfolio', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args   = array(
		'label'               => __( 'Portfolio Item', 'text_domain' ),
		'description'         => __( 'Portfolio items', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-portfolio',
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'portfolio', $args );
}
add_action( 'init', 'ak_custom_portfolio_post_type', 0 );

/**
 * Add custom fields to the Portfolio post type.
 *
 * @return void
 */
function ak_add_custom_fields() {
	add_meta_box(
		'portfolio_fields',
		__( 'Portfolio Item Details', 'akila-portfolio' ),
		'ak_render_portfolio_fields',
		'portfolio',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'ak_add_custom_fields' );

/**
 * Render custom fields.
 *
 * @return void
 */
function ak_render_portfolio_fields() {
	include plugin_dir_path( __FILE__ ) . 'templates/portfolio-fields.php';
}

/**
 * Save custom fields data.
 *
 * @param int $post_id The ID of the post being saved.
 * @return void
 */
function ak_save_custom_fields( $post_id ) {
	if ( ! isset( $_POST['portfolio_fields_nonce'] ) || ! wp_verify_nonce( $_POST['portfolio_fields_nonce'], 'save_portfolio_fields' ) ) {
		return;
	}

	if ( array_key_exists( 'client_name', $_POST ) ) {
		update_post_meta(
			$post_id,
			'client_name',
			sanitize_text_field( $_POST['client_name'] )
		);
	}

	if ( array_key_exists( 'project_url', $_POST ) ) {
		update_post_meta(
			$post_id,
			'project_url',
			sanitize_text_field( $_POST['project_url'] )
		);
	}
}
add_action( 'save_post', 'ak_save_custom_fields' );

/**
 * Shortcode to display recent posts by category.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function ak_recent_posts_by_category_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'category' => '', // default category
			'count'    => 5,   // default number of posts to display
		),
		$atts,
		'recent_posts_by_category'
	);

	// Query recent posts with the specified category
	$query_args  = array(
		'posts_per_page' => $atts['count'],
		'category_name'  => $atts['category'],
	);
	$posts_query = new WP_Query( $query_args );

	// Output the list of recent posts
	$output = '<ul>';
	if ( $posts_query->have_posts() ) {
		while ( $posts_query->have_posts() ) {
			$posts_query->the_post();
			$output .= '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
		}
	} else {
		$output .= '<li>' . esc_html__( 'No posts found', 'akila-portfolio' ) . '</li>';
	}
	$output .= '</ul>';

	// Restore global post data
	wp_reset_postdata();

	return $output;
}
add_shortcode( 'recent_posts_by_category', 'ak_recent_posts_by_category_shortcode' );

/**
 * Enqueue CSS file for portfolio submission form.
 */
function ak_enqueue_portfolio_submission_css() {
	$plugin_dir_url = plugin_dir_url( __FILE__ );

	// Enqueue CSS only on the page where the shortcode is used
	if ( is_page( 'your_portfolio_submission_page_slug' ) ) {
		wp_enqueue_style( 'portfolio-submission-css', $plugin_dir_url . 'css/portfolio-submission-form.css', array(), '1.0' );
	}
}
add_action( 'wp_enqueue_scripts', 'ak_enqueue_portfolio_submission_css' );
/**
 * Enqueue JavaScript file for the plugin.
 */
function enqueue_akila_plugin_js() {
	wp_enqueue_script( 'akila-portfolio-js-1', plugin_dir_url( __FILE__ ) . 'js/akila-portfolio.js', array( 'jquery' ), '1.0', true );

	wp_localize_script(
		'akila-portfolio-js-1',
		'my_plugin',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'enqueue_akila_plugin_js' );

/**
 * Enqueue jQuery in WordPress.
 */
function ak_enqueue_jquery() {
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'ak_enqueue_jquery' );

/**
 * Shortcode for portfolio submission form.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML content for the portfolio submission form.
 */
function ak_portfolio_submission_form_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => 'Portfolio Submission Form', // Default title
		),
		$atts,
		'portfolio_submission_form'
	);

	ob_start();
	?>
	<?php include plugin_dir_path( __FILE__ ) . 'templates/portfolio-form.php'; ?>
	<?php
	return ob_get_clean();
}
add_shortcode( 'portfolio_submission_form', 'ak_portfolio_submission_form_shortcode' );

/**
 * Process portfolio submission form.
 */
function ak_process_portfolio_submission() {
	if ( isset( $_POST['portfolio_submission_nonce_field'] ) && wp_verify_nonce( $_POST['portfolio_submission_nonce_field'], 'portfolio_submission_nonce' ) ) {
		if ( isset( $_POST['name'] ) && isset( $_POST['email'] ) && isset( $_POST['phone'] ) ) {
			$name         = sanitize_text_field( $_POST['name'] );
			$company_name = sanitize_text_field( $_POST['company_name'] );
			$company_url  = isset( $_POST['company_url'] ) ? esc_url( $_POST['company_url'] ) : '';
			$email        = sanitize_email( $_POST['email'] );
			$phone        = sanitize_text_field( $_POST['phone'] );
			$address      = sanitize_textarea_field( $_POST['address'] );

			// Validate email and phone
			$email_pattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
			$phone_pattern = '/^\d{10}$/';
			if ( ! preg_match( $email_pattern, $email ) || ! preg_match( $phone_pattern, $phone ) ) {
				echo 'Error: Invalid email or phone number format.';
				die();
			}

			// Create portfolio post
			$portfolio_data = array(
				'post_title'  => $name,
				'post_type'   => 'portfolio',
				'post_status' => 'publish',
				'meta_input'  => array(
					'client_name'  => $name,
					'company_name' => $company_name,
					'company_url'  => $company_url,
					'email'        => $email,
					'phone'        => $phone,
					'address'      => $address,
				),
			);

			// Insert post into the database
			$post_id = wp_insert_post( $portfolio_data );
			if ( is_wp_error( $post_id ) ) {
				echo 'Error: ' . esc_html( $post_id->get_error_message() );
			} else {
				echo 'Success! Your portfolio has been submitted.';
			}
		}
	}
	die();
}
add_action( 'wp_ajax_portfolio_submission', 'ak_process_portfolio_submission' );
add_action( 'wp_ajax_nopriv_portfolio_submission', 'ak_process_portfolio_submission' );

/**
 * Customize portfolio columns.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function custom_portfolio_columns( $columns ) {
	$columns = array(
		'cb'           => '<input type="checkbox" />',
		'title'        => __( 'Title', 'akila-portfolio' ),
		'client_name'  => __( 'Client Name', 'akila-portfolio' ),
		'company_name' => __( 'Company Name', 'akila-portfolio' ),
		'email'        => __( 'Email', 'akila-portfolio' ),
		'phone'        => __( 'Phone', 'akila-portfolio' ),
		'address'      => __( 'Address', 'akila-portfolio' ),
		'date'         => __( 'Date', 'akila-portfolio' ),
	);
	return $columns;
}
add_filter( 'manage_portfolio_posts_columns', 'custom_portfolio_columns' );

/**
 * Populate custom columns with data.
 *
 * @param string $column The name of the column.
 * @param int $post_id The ID of the post.
 */
function ak_custom_portfolio_columns_data( $column, $post_id ) {
	switch ( $column ) {
		case 'client_name':
			echo esc_html( get_post_meta( $post_id, 'client_name', true ) );
			break;
		case 'company_name':
			echo esc_html( get_post_meta( $post_id, 'company_name', true ) );
			break;
		case 'email':
			echo esc_html( get_post_meta( $post_id, 'email', true ) );
			break;
		case 'phone':
			echo esc_html( get_post_meta( $post_id, 'phone', true ) );
			break;
		case 'address':
			echo esc_html( get_post_meta( $post_id, 'address', true ) );
			break;
		default:
			break;
	}
}
add_action( 'manage_portfolio_posts_custom_column', 'ak_custom_portfolio_columns_data', 10, 2 );

/**
 * Make custom columns sortable.
 *
 * @param array $columns The columns.
 * @return array The modified columns.
 */
function ak_custom_portfolio_sortable_columns( $columns ) {
	$columns['client_name']  = 'client_name';
	$columns['company_name'] = 'company_name';
	$columns['email']        = 'email';
	$columns['phone']        = 'phone';
	$columns['address']      = 'address';
	return $columns;
}
add_filter( 'manage_edit-portfolio_sortable_columns', 'ak_custom_portfolio_sortable_columns' );

/**
 * Enqueue CSS file for portfolio submission form.
 */
function ak_enqueue_submenu_css() {
	wp_enqueue_style( 'portfolio-submission-form', plugin_dir_url( __FILE__ ) . 'css/portfolio-submission-form.css', array(), '1.0' );
}
add_action( 'admin_enqueue_scripts', 'ak_enqueue_submenu_css' );

/**
 * Enqueue JavaScript file for portfolio functionality.
 */
function ak_enqueue_submenu_js() {
	wp_enqueue_script( 'akila-portfolio-js', plugin_dir_url( __FILE__ ) . 'js/akila-portfolio.js', array( 'jquery' ), '1.0', true );

	wp_localize_script(
		'akila-portfolio-js',
		'my_plugin',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'ak_enqueue_submenu_js' );


/**
 * Update the submenu page callback function to display portfolio posts.
 */
function ak_display_submenu_details() {
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
function ak_get_portfolio_posts_callback() {
	$args = array(
		'post_type'      => 'portfolio',
		'posts_per_page' => -1,
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		include_once plugin_dir_path( __FILE__ ) . 'templates/retrieve-portfolio-posts.php';

		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<tr>';
			echo '<td>' . esc_html( get_the_title() ) . '</td>';
			echo '<td>' . esc_html( get_post_meta( get_the_ID(), 'client_name', true ) ) . '</td>';
			echo '<td>' . esc_html( get_post_meta( get_the_ID(), 'company_name', true ) ) . '</td>';
			echo '<td>' . esc_html( get_post_meta( get_the_ID(), 'email', true ) ) . '</td>';
			echo '<td>' . esc_html( get_post_meta( get_the_ID(), 'phone', true ) ) . '</td>';
			echo '<td>' . esc_html( get_post_meta( get_the_ID(), 'address', true ) ) . '</td>';
			echo '<td>' . esc_html( get_the_date() ) . '</td>';
			echo '<td><button class="delete-portfolio-post" data-post-id="' . esc_attr( get_the_ID() ) . '">' . esc_html__( 'Delete', 'akila-portfolio' ) . '</button></td>';

			echo '</tr>';
		}

		echo '</table>';
		wp_reset_postdata();
	} else {
		echo '<p>' . esc_html__( 'No portfolio posts found.', 'akila-portfolio' ) . '</p>';
	}

	die();
}
add_action( 'wp_ajax_get_portfolio_posts', 'ak_get_portfolio_posts_callback' );

/**
 * AJAX function to delete portfolio post.
 *
 * @return void
 */
function ak_delete_portfolio_post_callback() {
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
add_action( 'wp_ajax_delete_portfolio_post', 'ak_delete_portfolio_post_callback' );

/**
 * Add a submenu page.
 *
 * @return void
 */
function ak_custom_submenu() {
	add_submenu_page(
		'custom-slug', // Parent menu slug
		__( 'REST API', 'akila-portfolio' ), // Page title
		__( 'REST API', 'akila-portfolio' ), // Menu title
		'manage_options', // Capability
		'custom-submenu-slug', // Menu slug
		'ak_display_submenu_details' // Callback function to render the page content
	);
}
add_action( 'admin_menu', 'ak_custom_submenu' );

/**
 * Enqueue JavaScript file for AJAX request.
 *
 * @return void
 */
function ak_enqueue_submenu_ajax_script() {
	wp_enqueue_script( 'submenu-ajax-script', plugin_dir_url( __FILE__ ) . 'js/submenu-ajax.js', array( 'jquery' ), '1.0', true );
	wp_localize_script(
		'submenu-ajax-script',
		'submenu_ajax_object',
		array(
			'rest_url' => esc_url_raw( rest_url() ),
			'nonce'    => wp_create_nonce( 'delete_portfolio_post_nonce' ), // Create nonce here
		)
	);
}
add_action( 'admin_enqueue_scripts', 'ak_enqueue_submenu_ajax_script' );
/**
 * Define callback function for custom endpoint.
 *
 * @param WP_REST_Request $data The request data.
 * @return WP_REST_Response The response data.
 */
function ak_my_custom_endpoint_callback( $data ) {
	$response = array(
		'message'       => 'This is a custom endpoint response',
		'data_received' => $data,
	);
	return rest_ensure_response( $response );
}

/**
 * Register custom REST API endpoint.
 *
 * @return void
 */
function ak_register_custom_endpoints() {
	register_rest_route(
		'v1',
		'/custom-endpoint/',
		array(
			'methods'  => 'GET',
			'callback' => 'ak_my_custom_endpoint_callback',
		)
	);
}
add_action( 'rest_api_init', 'ak_register_custom_endpoints' );
