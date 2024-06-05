<?php
/**
 * Template for rendering custom fields in the Portfolio Item meta box.
 *
 * This template includes fields for 'Client Name' and 'Project URL' with appropriate labels.
 * It also generates a nonce field for security purposes.
 *
 * @package Akila_Portfolio
 */
?>

<label for="client_name"><?php esc_html_e( 'Client Name:', 'akila-portfolio' ); ?></label>
<input type="text" id="client_name" name="client_name" value="<?php echo esc_attr( $client_name ); ?>"><br><br>

<label for="project_url"><?php esc_html_e( 'Project URL:', 'akila-portfolio' ); ?></label>
<input type="text" id="project_url" name="project_url" value="<?php echo esc_attr( $project_url ); ?>"><br><br>

<?php
// Generate nonce field
wp_nonce_field( 'save_portfolio_fields', 'portfolio_fields_nonce' );
?>
