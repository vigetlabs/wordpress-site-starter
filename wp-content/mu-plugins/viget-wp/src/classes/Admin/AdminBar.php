<?php
/**
 * AdminBar Class
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * AdminBar Class
 */
class AdminBar {

	/**
	 * Initialize the Admin Menu Bar Customizations.
	 */
	public function __construct() {
		// Remove the WordPress logo from the admin bar.
		$this->remove_wp_logo();

		// Customize the Admin Bar
		$this->customize_admin_bar();
	}

	/**
	 * Remove the WordPress logo from the admin bar.
	 *
	 * @return void
	 */
	private function remove_wp_logo(): void {
		add_action( 'wp_before_admin_bar_render', function() {
			global $wp_admin_bar;
			$wp_admin_bar->remove_node( 'wp-logo' );
		} );
	}

	/**
	 * Customize the Admin Bar
	 *
	 * @return void
	 */
	private function customize_admin_bar(): void {
		add_action(
			'wp_before_admin_bar_render',
			function () {
				global $wp_admin_bar;

				/**
				 * Filter the admin bar items to remove
				 *
				 * @param array $remove The items to remove
				 */
				$remove = apply_filters( 'vigetwp_admin_bar', [] );

				if ( ! is_array( $remove ) ) {
					return;
				}

				foreach ( $remove as $value ) {
					$wp_admin_bar->remove_menu( $value );
				}
			}
		);
	}
}
