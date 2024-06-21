<?php
/**
 * Admin Assets
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * Assets
 */
class Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
		$this->load_assets();
	}

	/**
	 * Load admin assets
	 *
	 * @return void
	 */
	private function load_assets(): void {
		add_action(
			'admin_enqueue_scripts',
			function () {
				$version = vigetwp()->get_version();

				if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
					$js_path = VIGETWP_PLUGIN_PATH . 'src/assets/js/skip-cropping.js';
					$version = filemtime( $js_path );
				}

				wp_enqueue_script(
					'vigetwp-admin-skip-cropping',
					VIGETWP_PLUGIN_URL . 'src/assets/js/skip-cropping.js',
					[ 'jquery', 'media-views' ],
					$version,
					[
						'in_footer' => true,
					]
				);

				if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
					$css_path = VIGETWP_PLUGIN_PATH . 'src/assets/css/admin.css';
					$version  = filemtime( $css_path );
				}

				wp_enqueue_style(
					'vigetwp-admin-styles',
					VIGETWP_PLUGIN_URL . 'src/assets/css/admin.css',
					[], // dependencies.
					$version
				);
			}
		);
	}
}
