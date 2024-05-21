<!-- templates/portfolio-form.php -->
<form id="portfolio_submission_form">
    <input type="hidden" name="action" value="portfolio_submission">
    <?php wp_nonce_field( 'portfolio_submission_nonce', 'portfolio_submission_nonce_field' ); ?>

    <label for="name"><?php _e('Name:', 'my-plugin-ajax'); ?></label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="company_name"><?php _e('Company Name:', 'my-plugin-ajax'); ?></label>
    <input type="text" id="company_name" name="company_name"><br><br>

    <label for="company_url"><?php _e('Company URL:', 'my-plugin-ajax'); ?></label>
    <input type="url" id="company_url" name="company_url"><br><br>

    <label for="email"><?php _e('Email:', 'my-plugin-ajax'); ?></label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="phone"><?php _e('Phone:', 'my-plugin-ajax'); ?></label>
    <input type="tel" id="phone" name="phone" maxlength="10" minlength="10" required><br><br>

    <label for="address"><?php _e('Address:', 'my-plugin-ajax'); ?></label>
    <textarea id="address" name="address" rows="6"></textarea><br><br>
    
    <input type="button" id="submit_btn" value="Submit">
</form>
<div id="response_msg"></div>
