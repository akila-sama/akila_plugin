<?php

namespace Akila\Portfolio;

class Portfolio {

	public function __construct() {
		add_action( 'init', array( $this, 'ak_custom_portfolio_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_custom_fields' ) );
		add_action( 'save_post', array( $this, 'save_custom_fields' ) );
		add_filter( 'manage_portfolio_posts_columns', array( $this, 'custom_portfolio_columns' ) );
		add_action( 'manage_portfolio_posts_custom_column', array( $this, 'ak_custom_portfolio_columns_data' ), 10, 2 );
		add_filter( 'manage_edit-portfolio_sortable_columns', array( $this, 'ak_custom_portfolio_sortable_columns' ) );
	}

	/**
	* Register Custom Post Type.
	*
	* @return void
	*/
	public function ak_custom_portfolio_post_type() {
		$labels = array(
			'name'                  => _x( 'Portfolio', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Portfolio Item', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Portfolio', 'text_domain' ),
			'all_items'             => __( 'All Items', 'text_domain' ),
			'add_new_item'          => __( 'Add New Item', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'edit_item'             => __( 'Edit Item', 'text_domain' ),
			'update_item'           => __( 'Update Item', 'text_domain' ),
			'view_item'             => __( 'View Item', 'text_domain' ),
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

	/**
	* Add custom fields to the Portfolio post type.
	*
	* @return void
	*/
	public function add_custom_fields() {
		add_meta_box(
			'portfolio_fields',
			__( 'Portfolio Item Details', 'akila-portfolio' ),
			array( $this, 'render_portfolio_fields' ),
			'portfolio',
			'normal',
			'default'
		);
	}

	/**
	* Render custom fields.
	*
	* @return void
	 */
	public function render_portfolio_fields() {
		include plugin_dir_path( __FILE__ ) . '../templates/portfolio-fields.php';
	}

	/**
	* Save custom fields data.
	*
	* @param int $post_id The ID of the post being saved.
	* @return void
	*/
	public function save_custom_fields( $post_id ) {
		if ( ! isset( $_POST['portfolio_fields_nonce'] ) || ! wp_verify_nonce( $_POST['portfolio_fields_nonce'], 'save_portfolio_fields' ) ) {
			return;
		}

		if ( array_key_exists( 'client_name', $_POST ) ) {
			update_post_meta( $post_id, 'client_name', sanitize_text_field( $_POST['client_name'] ) );
		}

		if ( array_key_exists( 'project_url', $_POST ) ) {
			update_post_meta( $post_id, 'project_url', sanitize_text_field( $_POST['project_url'] ) );
		}
	}

	/**
	* Customize portfolio columns.
	*
	* @param array $columns Existing columns.
	* @return array Modified columns.
	*/
	public function custom_portfolio_columns( $columns ) {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'title'        => __( 'Title', 'akila-portfolio' ),
			'client_name'  => __( 'Client Name', 'akila-portfolio' ),
			'company_name' => __( 'Company Name', 'akila-portfolio' ),
			'email'        => __( 'Email', 'akila-portfolio' ),
			'phone'        => __( 'Phone', 'akila-portfolio' ),
			'address'      => __( 'Address', 'akila-portfolio' ),
			'date'         => __( 'Date', 'akila-portfolio' ),
			//add column in portfolio
			'mail'         => __( 'Sent_mail', 'akila-portfolio' ),
		);
		return $columns;
	}

	/**
	* Populate custom columns with data.
	*
	* @param string $column The name of the column.
	* @param int $post_id The ID of the post.
	*/
	public function ak_custom_portfolio_columns_data( $column, $post_id ) {
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
			//mail date and time display from the database
			case 'mail':
				echo esc_html( get_post_meta( $post_id, 'mail', true ) );
				break;
			default:
				break;
		}
	}

	/**
	* Make custom columns sortable.
	*
	* @param array $columns The columns.
	* @return array The modified columns.
	*/
	public function ak_custom_portfolio_sortable_columns( $columns ) {
		$columns['client_name']  = 'client_name';
		$columns['company_name'] = 'company_name';
		$columns['email']        = 'email';
		$columns['phone']        = 'phone';
		$columns['address']      = 'address';
		return $columns;
	}
}
