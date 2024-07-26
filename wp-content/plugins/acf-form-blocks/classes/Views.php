<?php
/**
 * Views Handler
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Views Loader
 */
class Views {

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Get the view content
	 *
	 * @param string $_view
	 * @param array  $_vars
	 *
	 * @return string
	 */
	public static function get( string $_view, array $_vars = [] ): string {
		ob_start();
		self::render( $_view, $_vars );
		return ob_get_clean() ?: '';
	}

	/**
	 * Render the view content
	 *
	 * @param string $_view
	 * @param array  $_vars
	 *
	 * @return void
	 */
	public static function render( string $_view, array $_vars = [] ): void {
		$_path = get_stylesheet_directory() . '/acf-form-blocks/' . $_view . '.php';

		if ( ! file_exists( $_path ) ) {
			$_path = ACFFB_PLUGIN_PATH . '/views/' . $_view . '.php';
		}

		if ( ! file_exists( $_path ) ) {
			return;
		}

		extract( $_vars );
		require $_path;
	}

}
