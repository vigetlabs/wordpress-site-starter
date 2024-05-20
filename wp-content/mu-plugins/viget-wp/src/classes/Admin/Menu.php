<?php
/**
 * @package VigetWP
 */

namespace VigetWP\Admin;

class Menu {

	/**
	 * Modify Admin Menu Items
	 */
	public function __construct() {
		// Customize the Admin Menu
		$this->customize_menus();
	}

	/**
	 * Customize the Admin Menus
	 *
	 * @return void
	 */
	private function customize_menus(): void {
		add_action(
			'admin_menu',
			function () {
				global $menu, $submenu;

				/**
				 * Filter the admin menu items to modify
				 *
				 * @param array $mods The items to modify
				 */
				$mods = apply_filters( 'vigetwp_admin_menu', [] );

				if ( empty( $mods ) || ! is_array( $mods ) ) {
					return;
				}

				foreach ( $mods as $mod ) {
					if ( empty( $mod['menu'] ) ) { // Menu is required.
						continue;
					}

					if ( ! empty( $mod['remove'] ) && true === $mod['remove'] ) {
						if ( ! empty( $mod['submenu'] ) ) {
							remove_submenu_page( $mod['menu'], $mod['submenu'] );
						} else {
							remove_menu_page( $mod['menu'] );
						}
						continue;
					}

					if ( ! empty( $mod['submenu'] ) ) {
						if ( empty( $submenu[ $mod['menu'] ] ) ) {
							continue;
						}

						$parent = $submenu[ $mod['menu'] ];

						foreach ( $parent as $priority => $child_menu ) {
							if ( $child_menu[2] !== $mod['submenu'] ) {
								continue;
							}

							if ( ! empty( $mod['name'] ) ) {
								$submenu[ $mod['menu'] ][ $priority ][0] = $mod['name'];
							} elseif ( ! empty( $mod['url'] ) ) {
								$submenu[ $mod['menu'] ][ $priority ][2] = $mod['url'];
							}
						}
					} else {
						foreach ( $menu as $priority => $menu_item ) {
							if ( $menu_item[2] !== $mod['menu'] ) {
								continue;
							}

							if ( ! empty( $mod['name'] ) ) {
								$menu[ $priority ][0] = $mod['name'];
							} elseif ( ! empty( $mod['url'] ) ) {
								$menu[ $priority ][2] = $mod['url'];
							}
						}
					}
				}
			},
			999
		);
	}

	/**
	 * Get Customizer URL
	 *
	 * @return string
	 */
	public function get_customizer_url(): string {
		$referrer = urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		return add_query_arg( 'return', $referrer, 'customize.php' );
	}
}
