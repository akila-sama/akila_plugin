<?php

/*
Plugin Name: My Plugin AJAX
Plugin URI: https://example.com/my-plugin-ajax
Description: This plugin allows users to submit their portfolio details through a form and inserts the data into a custom post type 'portfolio'. It also extends the functionality of a shortcode to display recent posts by category.
Version: 1.0
Author: akila
Author URI: https://example.com
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: my-plugin-ajax
Domain Path: /languages
*/


/** Add details and description about your plugin on plugin page
 * plugin name
 * shortcode name
 * functionality of shortcode
 * etc
 * Design plugin page
 */

function my_plugin_load_textdomain() {
	load_plugin_textdomain('my-plugin-ajax', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'my_plugin_load_textdomain');


// Enqueue CSS file
function enqueue_portfolio_submission_form_css() {
	wp_enqueue_style('portfolio-submission-form-style', plugin_dir_url(__FILE__) . 'css/portfolio-submission-form.css');
}
add_action('admin_enqueue_scripts', 'enqueue_portfolio_submission_form_css');

// Add a menu page
function custom_menu() {
	add_menu_page(
		__('Plugin Details', 'my-plugin-ajax'), // Page title
		__('Plugin Details', 'my-plugin-ajax'), // Menu title
		'manage_options', // Capability
		'custom-slug', // Menu slug
		'display_plugin_details', // Callback function to render the page content
		'dashicons-text-page', // Icon URL or Dashicons class
		25 // Menu position
	);
}
add_action('admin_menu', 'custom_menu');

// Function to render plugin details
function display_plugin_details() {
	include_once(plugin_dir_path(__FILE__) . 'templates/plugin-details.php');
}

// AJAX path
function enqueue_my_plugin_ajax_script() {
	wp_enqueue_script('my-plugin-ajax-script', plugin_dir_url(__FILE__) . 'js/akila_plugin.js', array('jquery'), '1.0', true);
	// Localize the script with the AJAX URL and nonce
	wp_localize_script(
		'my-plugin-ajax-script',
		'my_ajax_object',
		array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'security' => wp_create_nonce('custom_data_nonce'),
		)
	);
}
add_action('admin_enqueue_scripts', 'enqueue_my_plugin_ajax_script');

// Function to save data to wp-options table via AJAX
function save_custom_data_ajax() {
	// Verify nonce
	check_ajax_referer('custom_data_nonce', 'security');

	if (isset($_POST['custom_data'])) {
		update_option('custom_data', $_POST['custom_data']);
		echo 'success';
	} else {
		echo 'error';
	}
	wp_die();
}
add_action('wp_ajax_save_custom_data_ajax', 'save_custom_data_ajax');



/**Custom Post Type: Implement a plugin that registers a custom
post type, such as "Portfolio" or "Testimonials". Add some custom
fields to this post type, like "Client Name" and "Project URL".**/

// Register Custom Post Type
function custom_portfolio_post_type() {

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
add_action( 'init', 'custom_portfolio_post_type', 0 );

// Add custom fields to the Portfolio post type
function add_custom_fields() {
	add_meta_box(
		'portfolio_fields',
		__('Portfolio Item Details', 'my-plugin-ajax'),
		'render_portfolio_fields',
		'portfolio',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'add_custom_fields');

// Render custom fields
function render_portfolio_fields($post) {
		// Include the template file
		include(plugin_dir_path(__FILE__) . 'templates/portfolio-fields.php');
}

// Save custom fields data
function save_custom_fields($post_id) {
	// Verify nonce
	if (!isset($_POST['portfolio_fields_nonce']) || !wp_verify_nonce($_POST['portfolio_fields_nonce'], 'save_portfolio_fields')) {
		return;
	}

	if (array_key_exists('client_name', $_POST)) {
		update_post_meta(
			$post_id,
			'client_name',
			sanitize_text_field($_POST['client_name'])
		);
	}

	if (array_key_exists('project_url', $_POST)) {
		update_post_meta(
			$post_id,
			'project_url',
			sanitize_text_field($_POST['project_url'])
		);
	}
}
add_action('save_post', 'save_custom_fields');

/**Shortcode Extension: Extend the functionality of a shortcode.
For example, create a shortcode that displays a list of recent
posts with a specific category.  **/

function recent_posts_by_category_shortcode( $atts ) {
	// Extract shortcode attributes
	$atts = shortcode_atts(
		array(
			'category' => '', // default category
			'count'    => 5,     // default number of posts to display
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
			$output .= '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
		}
	} else {
			$output .= '<li>' . esc_html__('No posts found' , 'my-plugin-ajax'). '</li>';
	}
	$output .= '</ul>';

	// Restore global post data
	wp_reset_postdata();

	return $output;
}
add_shortcode( 'recent_posts_by_category', 'recent_posts_by_category_shortcode' );


/**Create shortcode
Add form
get following data
name
company name
email
phone
address
insert these data in the custom post type 'portfolio' that you've created.
 **/


// Enqueue CSS file
function enqueue_portfolio_submission_css() {
	// Get the plugin directory URL
	$plugin_dir_url = plugin_dir_url( __FILE__ );

	// Enqueue the CSS file
	wp_enqueue_style( 'portfolio-submission-css', $plugin_dir_url . 'css/portfolio-submission-form.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_portfolio_submission_css' );


// Enqueue JavaScript file
function enqueue_akila_plugin_js() {
    wp_enqueue_script( 'akila-plugin-js-1', plugin_dir_url( __FILE__ ) . 'js/akila_plugin.js', array( 'jquery' ), null, true );

	wp_localize_script( 'akila-plugin-js-1', 'my_plugin', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'enqueue_akila_plugin_js' );

// Enqueue jQuery in WordPress
function enqueue_jquery() {
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_jquery' );

function portfolio_submission_form_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => 'Portfolio Submission Form', // Default title
		),
		$atts,
		'portfolio_submission_form'
	);

	ob_start();
	?>
	<h2><?php echo esc_html( $atts['title'] ); ?></h2> <!-- Title added here -->
	
	<!-- html form separate and include it   -->
	<?php include( plugin_dir_path( __FILE__ ) . 'templates/portfolio-form.php' ); ?>
	
	
	<?php
	return ob_get_clean();
}
add_shortcode( 'portfolio_submission_form', 'portfolio_submission_form_shortcode' );

// Process form submission
function process_portfolio_submission() {
	if ( isset( $_POST['portfolio_submission_nonce_field'] ) && wp_verify_nonce( $_POST['portfolio_submission_nonce_field'], 'portfolio_submission_nonce' ) ) {
		if ( isset( $_POST['name'] ) && isset( $_POST['email'] ) && isset( $_POST['phone'] ) ) {
			$name         = sanitize_text_field( $_POST['name'] );
			$company_name = sanitize_text_field( $_POST['company_name'] );
			$company_url  = isset( $_POST['company_url'] ) ? esc_url( $_POST['company_url'] ) : '';
			$email        = sanitize_email( $_POST['email'] );
			$phone        = sanitize_text_field( $_POST['phone'] );
			$address      = sanitize_textarea_field( $_POST['address'] );

			// Validate email and phone again to prevent any possible tampering
			$email_pattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
			$phone_pattern = '/^\d{10}$/';
			if ( ! preg_match( $email_pattern, $email ) || ! preg_match( $phone_pattern, $phone ) ) {
				echo 'Error: Invalid email or phone number format.';
				die();
			}

			// Create post object
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

			// Insert the post into the database
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
add_action( 'wp_ajax_portfolio_submission', 'process_portfolio_submission' );
add_action( 'wp_ajax_nopriv_portfolio_submission', 'process_portfolio_submission' );



/**Custom Columns: Add custom columns to the post list
screen for your custom post type. **/
// Add custom columns

function custom_portfolio_columns( $columns ) {
	$columns = array(
		'cb'           => '<input type="checkbox" />',
		'title'        => __( 'Title', 'my-plugin-ajax' ),
		'client_name'  => __( 'Client Name', 'my-plugin-ajax' ),
		'company_name' => __( 'Company Name', 'my-plugin-ajax' ),
		'email'        => __( 'Email', 'my-plugin-ajax' ),
		'phone'        => __( 'Phone', 'my-plugin-ajax' ),
		'address'      => __( 'Address', 'my-plugin-ajax' ),
		'date'         => __( 'Date', 'my-plugin-ajax' ),
	);
	return $columns;
}
add_filter( 'manage_portfolio_posts_columns', 'custom_portfolio_columns' );
// Populate custom columns with data
function custom_portfolio_columns_data( $column, $post_id ) {
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
add_action( 'manage_portfolio_posts_custom_column', 'custom_portfolio_columns_data', 10, 2 );

// Make custom columns sortable
function custom_portfolio_sortable_columns( $columns ) {
	$columns['client_name']  = 'client_name';
	$columns['company_name'] = 'company_name';
	$columns['email']        = 'email';
	$columns['phone']        = 'phone';
	$columns['address']      = 'address';
	return $columns;
}
add_filter( 'manage_edit-portfolio_sortable_columns', 'custom_portfolio_sortable_columns' );


/** Create a new plugin sub menu:
 * Retrieve posts using REST API.
 * Add button to delete a post using REST API */



// Function to render submenu details

// Enqueue CSS file for portfolio submission form
function enqueue_submenu_css() {
	wp_enqueue_style( 'portfolio-submission-form', plugin_dir_url( __FILE__ ) . 'css/portfolio-submission-form.css' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_submenu_css' );

// Enqueue JavaScript file for portfolio functionality
function enqueue_submenu_js() {
	wp_enqueue_script( 'akila-plugin-js', plugin_dir_url( __FILE__ ) . 'js/akila_plugin.js', array( 'jquery' ), null, true );
}
add_action( 'admin_enqueue_scripts', 'enqueue_submenu_js' );

// Update the submenu page callback function to display portfolio posts
function display_submenu_details() {
	?>
	<div class="wrap">
		<h2><?php _e('Portfolio Posts', 'my-plugin-ajax'); ?></h2>
		<div id="portfolio-posts-container"></div> <!-- Container to display portfolio posts -->
		<div id="portfolio-posts-message"></div> <!-- Container for success/error messages -->
	</div>
	<?php
}

// AJAX function to retrieve portfolio posts
function get_portfolio_posts_callback() {
	$args = array(
		'post_type'      => 'portfolio',
		'posts_per_page' => -1,
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		echo '<table><tr><th>' . __('Title', 'my-plugin-ajax') . '</th><th>' . __('Client Name', 'my-plugin-ajax') . '</th><th>' . __('Company Name', 'my-plugin-ajax') . '</th><th>' . __('Email', 'my-plugin-ajax') . '</th><th>' . __('Phone', 'my-plugin-ajax') . '</th><th>' . __('Address', 'my-plugin-ajax') . '</th><th>' . __('Date', 'my-plugin-ajax') . '</th><th>' . __('Action', 'my-plugin-ajax') . '</th></tr>';
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
			echo '<td><button class="delete-portfolio-post" data-post-id="' . esc_attr( get_the_ID() ) . '">' . __('Delete', 'my-plugin-ajax') . '</button></td>';

			echo '</tr>';
		}

		echo '</table>';
		wp_reset_postdata();
	} else {
		echo '<p>' . __('No portfolio posts found.', 'my-plugin-ajax') . '</p>';
	}

	die();
}
add_action( 'wp_ajax_get_portfolio_posts', 'get_portfolio_posts_callback' );

// AJAX function to delete portfolio post
function delete_portfolio_post_callback() {
	// Check nonce
	check_ajax_referer( 'delete_portfolio_post_nonce', 'nonce' );

	if ( isset( $_POST['post_id'] ) ) {
		// $post_id = intval( $_POST['post_id'] );
		$post_id = absint( $_POST['post_id'] );
		wp_delete_post( $post_id );
		echo 'success';
	} else {
		echo 'error';
	}
	die();
}
add_action( 'wp_ajax_delete_portfolio_post', 'delete_portfolio_post_callback' );

// Add a submenu page
function custom_submenu() {
	add_submenu_page(
		'custom-slug', // Parent menu slug
		__('REST API', 'my-plugin-ajax'), // Page title
		__('REST API', 'my-plugin-ajax'), // Menu title
		'manage_options', // Capability
		'custom-submenu-slug', // Menu slug
		'display_submenu_details' // Callback function to render the page content
	);
}
add_action( 'admin_menu', 'custom_submenu' );

// Enqueue JavaScript file for AJAX request
function enqueue_submenu_ajax_script() {
	wp_enqueue_script( 'submenu-ajax-script', plugin_dir_url( __FILE__ ) . 'js/submenu-ajax.js', array( 'jquery' ), '1.0', true );
	// Localize the script with the REST API endpoint
	wp_localize_script(
		'submenu-ajax-script',
		'submenu_ajax_object',
		array(
			'rest_url' => esc_url_raw( rest_url() ),
			'nonce'    => wp_create_nonce( 'delete_portfolio_post_nonce' ), // Create nonce here
		)
	);
}
add_action( 'admin_enqueue_scripts', 'enqueue_submenu_ajax_script' );




/**
 *  Extend the WordPress REST API by adding custom endpoints to
 *  expose additional functionality from your plugin, allowing
 *  developers to interact with your plugin programmatically.
 */

// Define callback function for custom endpoint
function my_custom_endpoint_callback( $data ) {
	// Your custom logic here
	$response = array(
		'message'       => 'This is a custom endpoint response',
		'data_received' => $data,
	);
	return rest_ensure_response( $response );
}

// Register custom endpoint
function register_custom_endpoints() {
	register_rest_route(
		'v1',
		'/custom-endpoint/',
		array(
			'methods'  => 'GET',
			'callback' => 'my_custom_endpoint_callback',
		)
	);
	//url http://localhost/wp_plugin_dev/wp-json/v1/custom-endpoint
}
add_action( 'rest_api_init', 'register_custom_endpoints' );




