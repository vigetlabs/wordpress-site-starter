<?php
/**
 * Theme helper functions
 *
 * @package WPStarter
 */

use WPStarter\Core;

require_once get_stylesheet_directory() . '/inc/class-core.php';

if ( ! function_exists( 'WPStarter' ) ) {
	/**
	 * Return the Core theme instance.
	 *
	 * @return Core
	 */
	function WPStarter(): Core {
		return Core::get_instance();
	}
}
