<?php
/**
 * Plugin Details Template
 *
 * This template displays detailed information about the plugin,
 * description, author, version, and shortcode details. It also includes forms for
 * submitting portfolio details and custom data.
 *
 * @package APortfolio
 */

?>
<div class="wrap">
	<h2><?php esc_html_e( 'My Plugin Details', 'akila-portfolio' ); ?></h2>
	<div class="plugin-info">
		<p><strong><?php esc_html_e( 'Plugin Name:', 'akila-portfolio' ); ?></strong> <?php esc_html_e( 'My plugin ajax', 'akila-portfolio' ); ?></p>
		<p><strong><?php esc_html_e( 'Description:', 'akila-portfolio' ); ?></strong> <?php esc_html_e( 'This is a testing plugin. This plugin is my first plugin.', 'akila-portfolio' ); ?></p>
		<p><strong><?php esc_html_e( 'Author:', 'akila-portfolio' ); ?></strong><?php esc_html_e( 'akila', 'akila-portfolio' ); ?></p>
		<p><strong><?php esc_html_e( 'Version:', 'akila-portfolio' ); ?></strong><?php esc_html_e( '1.0', 'akila-portfolio' ); ?></p>
	</div>

	<h3><?php esc_html_e( 'Shortcode Details', 'akila-portfolio' ); ?></h3>
	<div class="shortcode-info">
		<p><strong><?php esc_html_e( 'Shortcode Name:', 'akila-portfolio' ); ?></strong> <?php esc_html_e( 'portfolio_submission_form' ); ?></p>
		<p><strong><?php esc_html_e( 'Functionality:', 'akila-portfolio' ); ?></strong> <?php esc_html_e( 'This shortcode allows users to submit their portfolio details through a form, including name, company name, email, phone, and address. Upon submission, the data is inserted into the custom post type \'portfolio\'.', 'akila-portfolio' ); ?></p>
		<form>
			<input type="hidden" name="action" value="portfolio_submission">
			<?php wp_nonce_field( 'portfolio_submission_nonce', 'portfolio_submission_nonce_field' ); ?>

			<label for="name"><?php esc_html_e( 'Name:', 'akila-portfolio' ); ?></label>
			<input type="text" id="name" name="name" required><br><br>

			<label for="company_name"><?php esc_html_e( 'Company Name:', 'akila-portfolio' ); ?></label>
			<input type="text" id="company_name" name="company_name"><br><br>

			<label for="email"><?php esc_html_e( 'Email:', 'akila-portfolio' ); ?></label>
			<input type="email" id="email" name="email" required><br><br>

			<label for="phone"><?php esc_html_e( 'Phone:', 'akila-portfolio' ); ?></label>
			<input type="tel" id="phone" name="phone"><br><br>

			<label for="address"><?php esc_html_e( 'Address:', 'akila-portfolio' ); ?></label>
			<textarea id="address" name="address" rows="6"></textarea><br><br>
		</form>
	</div>
</div>
<div class="wrap">
	<h2><?php esc_html_e( 'Custom Page', 'akila-portfolio' ); ?></h2>
	<form id="custom_data_form" method="post">
		<!-- Add nonce field to the form -->
		<?php wp_nonce_field( 'custom_data_nonce', 'custom_data_nonce' ); ?>
		<label for="custom_data"><?php esc_html_e( 'Enter Custom Data:', 'akila-portfolio' ); ?></label>
		<input type="text" id="custom_data" name="custom_data" value="<?php echo esc_attr( get_option( 'custom_data' ) ); ?>" /><br>
		<input type="submit" id="submit_custom_data" name="submit_custom_data" class="button-primary" value="<?php esc_html_e( 'Save', 'akila-portfolio' ); ?>" />
	</form>
	<div id="message"></div>
</div>
