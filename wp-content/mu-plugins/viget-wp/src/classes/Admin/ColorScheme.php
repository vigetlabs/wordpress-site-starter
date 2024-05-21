<?php
/**
 * Admin Color Scheme Customization
 *
 * @since 1.0.0
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * ColorScheme Class
 */
class ColorScheme {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Set the Admin color scheme
		$this->set_color_scheme();

		// Default to the custom color scheme
		$this->default_user_color_scheme();

		// Set the TinyMCE color scheme
		$this->set_tinymce_color_scheme();
	}

	/**
	 * Set the Admin Color Scheme
	 *
	 * @return void
	 */
	private function set_color_scheme(): void {
		add_action(
			'admin_init',
			function () {
				wp_admin_css_color(
					get_template(),
					get_bloginfo( 'name' ),
					VIGETWP_PLUGIN_URL . 'src/assets/css/admin-style.css',

					// Update this to match the four main color of the admin style.
					[
						'#1a4155',
						'#fff',
						'#f26628' ,
						'#f26628',
					]
				);
			}
		);
	}

	/**
	 * Default to the custom color scheme
	 *
	 * @return void
	 */
	private function default_user_color_scheme(): void {
		add_filter(
			'get_user_option_admin_color',
			fn() => get_template(),
			5
		);
	}

	/**
	 * Set the TinyMCE color scheme
	 *
	 * @return void
	 */
	private function set_tinymce_color_scheme(): void {
		add_action(
			'admin_init',
			function () {
				add_editor_style( VIGETWP_PLUGIN_URL . 'src/assets/css/admin-editor.css' );
			}
		);
	}
}
