<?php

namespace APortfolio;

/**
 * Class Shortcodes
 *
 * Handles the registration and processing of shortcodes related to the portfolio.
 */
class Shortcodes {

	public function __construct() {
		add_shortcode( 'recent_posts_by_category', array( $this, 'ak_recent_posts_by_category_shortcode' ) );
		add_shortcode( 'portfolio_submission_form', array( $this, 'ak_portfolio_submission_form_shortcode' ) );
		add_shortcode( 'sent_emails_details', array( $this, 'ak_sent_emails_details_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ak_enqueue_portfolio_submission_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ak_enqueue_akila_plugin_js' ) );
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
				'category' => '',
				'count'    => 5,
			),
			$atts,
			'recent_posts_by_category'
		);

		$query_args  = array(
			'posts_per_page' => $atts['count'],
			'category_name'  => $atts['category'],
		);
		$posts_query = new \WP_Query( $query_args );

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
		include AKILA_PORTFOLIO_PLUGIN_DIR . 'templates/portfolio-form.php';
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
			wp_enqueue_style( 'portfolio-submission-css', AKILA_PORTFOLIO_PLUGIN_URL . '../css/portfolio-submission-form.css', array(), '1.0' );
		}
	}

	/**
	* Enqueue JavaScript file for the plugin on the front-end only if the shortcode is present.
	*/
	public function ak_enqueue_akila_plugin_js() {
		if ( ! is_admin() && $this->ak_has_shortcode( 'portfolio_submission_form' ) ) {
			wp_enqueue_script( 'akila-portfolio-js-1', AKILA_PORTFOLIO_PLUGIN_URL . '../js/akila-portfolio.js', array( 'jquery' ), '1.0', true );
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
	 *
	 * This function handles the submission of portfolio form data.
	 * It validates the submitted data, creates a portfolio post, and sends an email notification.
	 *
	 * @return void
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
						//email sent faild but date ,time store database
						'mail'         => gmdate( 'Y-m-d H:i:s' ),
					),
				);

				// Insert post into the database
				$post_id = wp_insert_post( $portfolio_data );
				if ( is_wp_error( $post_id ) ) {
					echo esc_html__( 'Error: ', 'akila-portfolio' ) . esc_html( $post_id->get_error_message() );
				} else {
					$to      = $email;
					$subject = 'Portfolio Submission Confirmation';
					$message = 'Thank you for submitting your portfolio. We have received your details and will review them shortly.';
					$headers = array( 'Content-Type: text/html; charset=UTF-8' );

					$sent = wp_mail( $to, $subject, $message, $headers );

					if ( $sent ) {
						echo esc_html__( 'Success! Your portfolio has been submitted. An email confirmation has been sent.', 'akila-portfolio' );
					} else {
						echo esc_html__( 'Success! Your portfolio has been submitted, but there was an error sending the email confirmation.', 'akila-portfolio' );
					}
				}
			}
		}
		die();
	}
	/**
	 * Shortcode to display sent emails details.
	 *
	 * Retrieves the details of sent emails and generates HTML content to display them.
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML content for displaying sent emails details.
	 */
	public function ak_sent_emails_details_shortcode() {
		$sent_emails = get_posts(
			array(
				'post_type'      => 'portfolio',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => 'mail',
						'compare' => 'EXISTS', // Check if the 'mail' meta key exists
					),
				),
			)
		);

		$output  = '<div class="sent-emails-details">';
		$output .= '<h2>Sent Emails Details</h2>';
		$output .= '<ul>';

		foreach ( $sent_emails as $email ) {
			$recipient_email = get_post_meta( $email->ID, 'email', true );
			$sent_time       = get_post_meta( $email->ID, 'mail', true );

			$sent_time = gmdate( 'F j, Y g:i a', strtotime( $sent_time ) );

			$output .= '<li>';
			$output .= 'Recipient Email: ' . $recipient_email . '<br>';
			$output .= 'Sent Time: ' . $sent_time;
			$output .= '</li>';
		}

		$output .= '</ul>';
		$output .= '</div>';

		return $output;
	}
}
