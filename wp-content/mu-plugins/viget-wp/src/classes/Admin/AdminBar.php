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
	 * Customize Admin Menu Bar
	 */
	public function __construct() {
		// Customize the Admin Bar
		$this->customize_admin_bar();
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
