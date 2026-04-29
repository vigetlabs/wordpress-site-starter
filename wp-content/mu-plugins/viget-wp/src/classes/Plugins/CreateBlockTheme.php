<?php
/**
 * Create Block Theme Class
 *
 * @package VigetWP
 */

namespace VigetWP\Plugins;

/**
 * Create Block Theme Class
 */
class CreateBlockTheme {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter(
			'rest_post_dispatch',
			function ( $result, $server, $request ) {
				$route = $request->get_route();

				if ( '/create-block-theme/v1/save' !== $route ) {
					return $result;
				}

				$this->clean_theme_files();

				return $result;
			},
			10,
			3
		);
	}

	/**
	 * Clean theme files
	 */
	private function clean_theme_files(): void {
		$templates = glob( get_stylesheet_directory() . '/templates/*.{php,html}', GLOB_BRACE );

		// Replace the home_url() with relative URLs in the template files.
		foreach ( $templates as $template ) {
			$content  = file_get_contents( $template );
			$home_url = trailingslashit( home_url() );
			$content  = str_replace( $home_url, '/', $content );
			file_put_contents( $template, $content );
		}
	}
}
