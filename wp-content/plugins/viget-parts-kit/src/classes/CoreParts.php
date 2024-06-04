<?php
/**
 * Add Core blocks to Parts Kit
 *
 * @package VigetPartsKit
 */

namespace VigetPartsKit;

/**
 * CoreParts Class
 */
class CoreParts {

	/**
	 * Core blocks
	 *
	 * @var array
	 */
	private array $core_blocks = [];

	/**
	 * Initialize the Core Parts
	 */
	public function __construct() {
		// Include core blocks.
		$this->register_core_blocks();

		// Render the core blocks.
		$this->render_core_blocks();
	}

	/**
	 * Register core blocks
	 *
	 * @return void
	 */
	private function register_core_blocks(): void {
		add_filter(
			'viget_parts_kit',
			function( array $parts ): array {
				if ( empty( $this->core_blocks ) ) {
					$this->core_blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
				}

				foreach ( $this->core_blocks as $block ) {
					if ( isset( $parts[ $block->name ] ) ) {
						continue;
					}

					$parts[ $block->name ] = [
						'title'    => $block->title,
						'url'      => home_url( PartsKit::URL_SLUG . '/' . urlencode( $block->name ) ),
						'children' => [],
					];
				}

				return $parts;
			}
		);
	}

	/**
	 * Render core blocks
	 *
	 * @return void
	 */
	private function render_core_blocks(): void {
		add_filter(
			'viget_parts_kit_%',
			function ( string $output, string $block_name ): string {
				if ( empty( $this->core_blocks ) ) {
					$this->core_blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
				}

				$core_block = false;

				foreach ( $this->core_blocks as $block ) {
					if ( $block->name === $block_name ) {
						$core_block = $block;
						break;
					}
				}

				if ( ! $core_block ) {
					return $output;
				}

				ob_start();
				$core_block->render();
				return ob_get_clean();
			},
			10,
			2
		);
	}
}
