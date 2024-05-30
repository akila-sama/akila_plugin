<?php

namespace Akila\Portfolio;

class Shortcodes {

	public function __construct() {
		add_shortcode( 'recent_posts_by_category', array( $this, 'ak_recent_posts_by_category_shortcode' ) );
		add_shortcode( 'portfolio_submission_form', array( $this, 'ak_portfolio_submission_form_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ak_enqueue_portfolio_submission_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_akila_plugin_js' ) );
		add_action( 'wp_ajax_portfolio_submission', array( $this, 'ak_process_portfolio_submission' ) );
		add_action( 'wp_ajax_nopriv_portfolio_submission', array( $this, 'ak_process_portfolio_submission' ) );
	}

	/**
	* Shortcode to display recent posts by category.
	*
	* @param array $atts Shortcode attributes.
	* @return string HTML output.
	*/
	public function ak_recent_posts_by_category_shortcode( $atts ) {
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
		$posts_query = new \WP_Query( $query_args );

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

	/**
	* Shortcode for portfolio submission form.
	*
	* @param array $atts Shortcode attributes.
	* @return string HTML content for the portfolio submission form.
	*/
	public function ak_portfolio_submission_form_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'title' => 'Portfolio Submission Form', // Default title
			),
			$atts,
			'portfolio_submission_form'
		);

		ob_start();
		include plugin_dir_path( __FILE__ ) . '../templates/portfolio-form.php';
		return ob_get_clean();
	}

	/**
	* Check if the shortcode exists in the content.
	*
	* @param string $shortcode Shortcode to check for.
	* @return bool True if the shortcode exists, false otherwise.
	*/
	public function ak_has_shortcode( $shortcode ) {
		global $post;
		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $shortcode ) ) {
			return true;
		}
		return false;
	}

	/**
	* Enqueue CSS file for the portfolio submission form on the front-end only if the shortcode is present.
	*/
	public function ak_enqueue_portfolio_submission_css() {
		if ( ! is_admin() && $this->ak_has_shortcode( 'portfolio_submission_form' ) ) {
			$plugin_dir_url = plugin_dir_url( __FILE__ );
			wp_enqueue_style( 'portfolio-submission-css', $plugin_dir_url . '../css/portfolio-submission-form.css', array(), '1.0' );
		}
	}

	/**
	* Enqueue JavaScript file for the plugin on the front-end only if the shortcode is present.
	*/
	public function enqueue_akila_plugin_js() {
		if ( ! is_admin() && $this->ak_has_shortcode( 'portfolio_submission_form' ) ) {
			wp_enqueue_script( 'akila-portfolio-js-1', plugin_dir_url( __FILE__ ) . '../js/akila-portfolio.js', array( 'jquery' ), '1.0', true );
			wp_localize_script(
				'akila-portfolio-js-1',
				'ak_my_plugin',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
	}

	/**
	* Process portfolio submission form.
	*/
	public function ak_process_portfolio_submission() {
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
}
