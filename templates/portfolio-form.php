<?php
/**
 * Template for the portfolio submission form.
 *
 * This template is included in the shortcode for displaying the portfolio submission form.
 *
 * @package APortfolio
 */
?>

<h2><?php echo esc_html( $atts['title'] ); ?></h2> <!-- Title added here -->
<form id="portfolio_submission_form">
	<input type="hidden" name="action" value="portfolio_submission">
	<?php wp_nonce_field( 'portfolio_submission_nonce', 'portfolio_submission_nonce_field' ); ?>
 
	<label for="name"><?php esc_html_e( 'Name:', 'akila-portfolio' ); ?></label>
	<input type="text" id="name" name="name" required><br><br>

	<label for="company_name"><?php esc_html_e( 'Company Name:', 'akila-portfolio' ); ?></label>
	<input type="text" id="company_name" name="company_name"><br><br>

	<label for="company_url"><?php esc_html_e( 'Company URL:', 'akila-portfolio' ); ?></label>
	<input type="url" id="company_url" name="company_url"><br><br>

	<label for="email"><?php esc_html_e( 'Email:', 'akila-portfolio' ); ?></label>
	<input type="email" id="email" name="email" required><br><br>

	<label for="phone"><?php esc_html_e( 'Phone:', 'akila-portfolio' ); ?></label>
	<input type="tel" id="phone" name="phone" maxlength="10" minlength="10" required><br><br>

	<label for="address"><?php esc_html_e( 'Address:', 'akila-portfolio' ); ?></label>
	<textarea id="address" name="address" rows="6"></textarea><br><br>

	<input type="button" id="submit_btn" value="Submit">
</form>
<div id="response_msg"></div>
