
<!-- akila_plugin.php code html separate here =
function render_portfolio_fields($post)
-->

<label for="client_name"><?php esc_html_e( 'Client Name:', 'my-plugin-ajax' ); ?></label>
<input type="text" id="client_name" name="client_name" value="<?php echo esc_attr( $client_name ); ?>"><br><br>

<label for="project_url"><?php esc_html_e( 'Project URL:', 'my-plugin-ajax' ); ?></label>
<input type="text" id="project_url" name="project_url" value="<?php echo esc_attr( $project_url ); ?>"><br><br>

<?php
// Generate nonce field
wp_nonce_field( 'save_portfolio_fields', 'portfolio_fields_nonce' );
?>
