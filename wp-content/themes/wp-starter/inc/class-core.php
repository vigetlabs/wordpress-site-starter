<?php
/**
 * Core theme class
 *
 * @package WPStarter
 */

namespace WPStarter;

/**
 * Core theme bootstrap.
 */
class Core {

	/**
	 * Instance of this class.
	 *
	 * @var Core|null
	 */
	private static ?Core $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Core
	 */
	public static function get_instance(): Core {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load dependencies into properties.
	 */
	private function __construct() {
		// Child theme support.
		$inc = get_stylesheet_directory() . '/inc';

		// Load theme post types and taxonomies.
		// require_once $inc . '/class-example.php';

		// Initialize theme post types and taxonomies.
		// $this->example = Example::get_instance();
	}
}
