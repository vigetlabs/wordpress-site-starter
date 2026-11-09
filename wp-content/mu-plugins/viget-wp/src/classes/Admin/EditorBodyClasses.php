<?php
/**
 * Editor Body Classes
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * Adds front-end template body classes to the block editor canvas, so
 * page-scoped styles apply while editing.
 */
class EditorBodyClasses {

	/**
	 * EditorBodyClasses constructor.
	 */
	public function __construct() {
		$this->load_assets();
	}

	/**
	 * Enqueue the editor script.
	 *
	 * @return void
	 */
	private function load_assets(): void {
		add_action(
			'enqueue_block_editor_assets',
			function () {
				$version = vigetwp()->get_version();

				if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
					$version = filemtime( VIGETWP_PLUGIN_PATH . 'src/assets/js/editor-body-classes.js' );
				}

				wp_enqueue_script(
					'vigetwp-editor-body-classes',
					VIGETWP_PLUGIN_URL . 'src/assets/js/editor-body-classes.js',
					[ 'wp-data' ],
					$version,
					[
						'in_footer' => true,
					]
				);
			}
		);
	}
}
