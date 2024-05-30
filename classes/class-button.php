<?php

namespace Akila\Portfolio;

class Button {

	public function __construct() {
		add_filter( 'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) . '/akila-portfolio.php' ), array( $this, 'ak_add_custom_plugin_button' ) );
	}

	/**
	 * Add a custom button next to the Deactivate button on the plugins page.
	 *
	 * @param array $links Existing action links.
	 * @return array Modified action links.
	 */
	public function ak_add_custom_plugin_button( $links ) {
		$custom_plugin_page = admin_url( 'admin.php?page=custom-slug' );

		$button_label = __( 'Plugin Details', 'akila-portfolio' );

		// Add the custom button link.
		$custom_link = '<a href="' . esc_url( $custom_plugin_page ) . '" class="">' . $button_label . '</a>';
		array_unshift( $links, $custom_link );

		return $links;
	}
}
