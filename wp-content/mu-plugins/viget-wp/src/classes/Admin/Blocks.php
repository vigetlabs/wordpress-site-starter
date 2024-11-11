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

		// Remove template part wrapper div
		$this->remove_template_part_wrapper();
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

	/**
	 * Remove the template part wrapper div
	 *
	 * @return void
	 */
	private function remove_template_part_wrapper(): void {
		add_filter(
			'render_block_core/template-part',
			function( $block_content, $block ) {
				$use_wrapper = $block['attrs']['useWrapper'] ?? false;

				if ( true === $use_wrapper ) {
					return $block_content;
				}

				$proc = new \WP_HTML_Tag_Processor( $block_content );

				if ( true === $proc->next_tag() ) {
					$block_content = trim( $block_content );

					// Remove opening tag.
					$block_content = substr(
						$block_content,
						strpos( $block_content, '>' ) + 1
					);

					// Remove closing tag.
					$block_content = substr(
						$block_content,
						0,
						strlen( $block_content ) - strlen( "</{$proc->get_tag()}>" ),
					);

					$block_content = trim( $block_content );
				}

				return $block_content;
			},
			10,
			2
		);
	}
}
