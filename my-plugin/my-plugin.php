<?php
/*
* Plugin Name: My plugin
* Description: This is a testing plugin. This plugin is my first plugin.
* Author: akila
* Version: 1.0
*/


// Add a menu page
function custom_menu_page() {
    add_menu_page(
        'Custom Page', // Page title
        'Custom Page', // Menu title
        'manage_options', // Capability 
        'custom-page-slug', // Menu slug
        'custom_page_content', // Callback function to render the page content
        'dashicons-admin-generic', // Icon URL or Dashicons class
        25 // Menu position
    );
}
add_action('admin_menu', 'custom_menu_page');


// Function to save wp-option table serch (custom_data) 
function save_custom_data() {
    if (isset($_POST['submit_custom_data'])) {
         update_option('custom_data', $_POST['custom_data']);
    }
}

add_action('admin_init', 'save_custom_data');

// Function to render the page content
function custom_page_content() {

    ?>
    <div class="wrap">
        <h2>Custom Page</h2>
        <form method="post" action="">
            <label for="custom_data">Enter Custom Data:</label>
            <input type="text" id="custom_data" name="custom_data" value="<?php echo esc_attr(get_option('custom_data')); ?>" /></br>
            <input type="submit" name="submit_custom_data" class="button-primary" value="Save" />
        </form>
    </div>
    <?php
}



// submenu create in settings
add_action('admin_menu','my_setting_submenu');

function my_setting_submenu(){
    add_options_page(    //settings ni andar menu lavva aa lakhvu ane slug(my-settings)
        'my menu',          //page title
        'my menu',          //menu title
        'manage_options',   //capability
        'my-settings',      //slug
        'my_setting'        // callback func
    );
}


//save data
add_action('admin_init','save_data');

function save_data(){
    if (isset($_POST['submit_data'])) {
         update_option('custom_info', $_POST['custom_info']);
    }                       //id                //name
}

function my_setting(){
    ?>
    <h1>sub menu</h1>
    <form action ="" method="post">
    <label for="custom_info">Enter Custom Data:</label>
    <input type="text" id="custom_info" name="custom_info" 
            value="<?php echo esc_attr(get_option('custom_info')); ?>" /></br>
    <input type="submit" name="submit_data" value="save">
    </form>
    <?php
}


?>
 
