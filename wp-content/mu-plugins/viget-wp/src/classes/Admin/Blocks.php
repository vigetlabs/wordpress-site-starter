<?php
/**
 * Blocks Class
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * Blocks Class
 */
class Blocks {

	/**
	 * Disable Core Block Features.
	 */
	public function __construct() {
		// Remove core block patterns.
		$this->remove_core_block_patterns();

		// Remove the downloadable blocks directory feature.
		$this->remove_downloadable_blocks();
	}

	/**
	 * Remove core block patterns.
	 *
	 * @return void
	 */
	private function remove_core_block_patterns(): void {
		add_filter( 'should_load_remote_block_patterns', '__return_false' );

		add_action(
			'after_setup_theme',
			function () {
				remove_theme_support( 'core-block-patterns' );
			}
		);
	}

	/**
	 * Remove the downloadable blocks directory feature.
	 *
	 * @return void
	 */
	private function remove_downloadable_blocks(): void {
		remove_action(
			'enqueue_block_editor_assets',
			'wp_enqueue_editor_block_directory_assets'
		);
	}
}
