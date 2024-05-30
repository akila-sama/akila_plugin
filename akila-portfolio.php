<?php
/**
 * Plugin Name: Akila Portfolio
 * Description: Akila Portfolio is a comprehensive portfolio management plugin for WordPress. It allows users to create and manage portfolio items with custom fields, display recent posts by category, and submit portfolio items via a front-end form. It includes AJAX-based functionalities, custom REST API endpoints, and admin page enhancements.
 * Version: 1.0
 * Author: Akila
 * Text Domain: akila-portfolio
 */

require_once plugin_dir_path( __FILE__ ) . 'includes/admin-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/class-portfolio.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/class-pluginpage.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/class-shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/class-button.php'; // Include the new class
require_once plugin_dir_path( __FILE__ ) . 'classes/class-endpoints.php';


// Initialize classes.
new Akila\Portfolio\Portfolio();
new Akila\Portfolio\PluginPage();
new Akila\Portfolio\Shortcodes();
new Akila\Portfolio\Button();
new Akila\Portfolio\Endpoints();
