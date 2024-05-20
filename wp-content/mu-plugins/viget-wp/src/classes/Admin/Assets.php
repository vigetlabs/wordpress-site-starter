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
				wp_enqueue_style(
					'vigetwp-admin-styles',
					VIGETWP_PLUGIN_URL . 'src/assets/css/admin.css',
					[], // dependencies.
					vigetwp()->get_version()
				);
			}
		);
	}
}
