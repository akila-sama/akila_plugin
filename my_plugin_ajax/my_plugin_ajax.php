<?php
/*
* Plugin Name: My plugin ajax
* Description: This is a testing plugin. This plugin is my first plugin.
* Author: akila
* Version: 1.0
*/


// Add a menu page
function custom_menu_page() {
    add_menu_page(
        'ajax Page', // Page title
        'ajax Page', // Menu title
        'manage_options', // Capability 
        'custom-page-slug', // Menu slug
        'custom_page_content', // Callback function to render the page content
        'dashicons-admin-generic', // Icon URL or Dashicons class
        25 // Menu position
    );
}
add_action('admin_menu', 'custom_menu_page');


// Function to render the page content
function custom_page_content() {

    ?>
    <div class="wrap">
        <h2>Custom Page</h2>
        <form method="post" action="">
            <label for="custom_data">Enter Custom Data:</label>
            <input type="text" id="custom_data" name="custom_data" value="<?php echo esc_attr(get_option('custom_data')); ?>" /></br>
            <input type="submit" id="submit_custom_data" name="submit_custom_data" class="button-primary" value="Save" />
        </form>
        <div id="message"></div>
             <!-- This div will display the message -->
    </div>
    
    <?php
}



//ajax  path
function enqueue_my_plugin_ajax_script() {
    wp_enqueue_script('my-plugin-ajax-script', plugin_dir_url(__FILE__) . 'js/my_plugin_ajax.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'enqueue_my_plugin_ajax_script');



// Function to save wp-option table serch (custom_data) via AJAX
function save_custom_data_ajax() {
    if (isset($_POST['custom_data'])) {
        update_option('custom_data', $_POST['custom_data']);
        wp_die();
    }
}
add_action('wp_ajax_save_custom_data_ajax', 'save_custom_data_ajax');

