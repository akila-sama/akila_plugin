=== Akila Portfolio ===
Contributors: akila
Tags: portfolio, custom post type, shortcode, ajax, REST API
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 1.0
Requires PHP: 7.2
donate link: https://qrolic.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create and manage portfolio items with custom fields, shortcodes, AJAX, and REST API support.

== Description ==

Akila Portfolio provides a complete solution for managing portfolio items on your WordPress site. Key features include:

* **Custom Post Type**: Register and manage portfolio items.
* **Custom Fields**: Add and display custom fields for each portfolio item.
* **Shortcodes**: Display recent posts by category and a portfolio submission form using shortcodes.
* **AJAX Functionality**: Submit portfolio items and manage portfolio posts using AJAX.
* **REST API Endpoints**: Custom REST API endpoints for extended functionality.
* **Admin Page Enhancements**: Custom admin pages for managing the plugin's settings and portfolio items.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/akila-portfolio` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the provided shortcodes `[recent_posts_by_category]` and `[portfolio_submission_form]` in your posts or pages.

== Frequently Asked Questions ==

= How do I display recent posts by category? =

Use the `[recent_posts_by_category]` shortcode with optional attributes `category` and `count`. For example:
`[recent_posts_by_category category="news" count="5"]`

= How do I display the portfolio submission form? =

Use the `[portfolio_submission_form]` shortcode in your posts or pages.

= Can I customize the fields in the portfolio submission form? =

The plugin includes a basic form, but you can modify the template file located at `templates/portfolio-form.php` to customize the fields and layout.

== Changelog ==

= 1.0 =
* Initial release of Akila Portfolio plugin.

== Upgrade Notice ==

= 1.0 =
* Initial release. No upgrade needed.

== Screenshots ==

1. Admin Menu - Custom menu and submenu added by the plugin.
2. Portfolio Form - Front-end portfolio submission form.
3. Portfolio Posts - Display of portfolio posts in the admin area.

== License ==

This plugin is licensed under the GPLv2 or later. For more information, see [License URI](https://www.gnu.org/licenses/gpl-2.0.html).
