<?php
/**
 * Add Registered Blocks to Parts Kit
 *
 * @package VigetPartsKit
 */

namespace VigetPartsKit;

use Viget\ACFBlocksToolkit\Block_Registration;

/**
 * CoreParts Class
 */
class BlockParts {

	/**
	 * Core blocks
	 *
	 * @var array
	 */
	private array $blocks = [];

	/**
	 * Initialize the Core Parts
	 */
	public function __construct() {
		// Include core blocks.
		$this->register_blocks();
	}

	/**
	 * Register blocks with Parts Kit
	 *
	 * @return void
	 */
	private function register_blocks(): void {
		add_filter(
			'viget_parts_kit',
			function( array $parts ): array {
				if ( empty( $this->blocks ) ) {
					$this->blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
				}

				foreach ( $this->blocks as $block ) {
					if ( isset( $parts[ $block->name ] ) || empty( $block->title ) ) {
						continue;
					}

					list( $namespace, $block_name ) = explode( '/', $block->name );

					if ( str_contains( $block_name, 'legacy' ) || str_contains( $block_name, 'comment' ) ) {
						continue;
					}

					if ( ! isset( $parts[ $namespace ] ) ) {
						$parts[ $namespace ] = [
							'title'    => strtoupper( $namespace ),
							'children' => [],
							'url'      => '',
						];
					}

					$the_block = [
						'title'    => $block->title,
						'url'      => home_url( PartsKit::URL_SLUG . '/' . urlencode( $block->name ) ),
						'children' => [],
					];

					if ( 'acf' === $namespace ) {
						$acf_block = Block_Registration::get_block( $block->name );

						if ( ! empty( $acf_block['parent'] ) ) {
							foreach ( $acf_block['parent'] as $parent ) {
								if ( isset( $parts[ $namespace ]['children'][ $parent ] ) ) {
									$parts[ $namespace ]['children'][ $parent ]['children'][] = $the_block;
									continue 2;
								}
							}
						}
					}

					$parts[ $namespace ]['children'][ $block->name ] = $the_block;
				}

				// Cleanup and sort data.
				if ( ! empty( $parts ) ) {
					foreach ( $parts as &$group ) {
						if ( empty( $group['children'] ) ) {
							continue;
						}

						$group['children'] = array_values( $group['children'] );

						usort( $group['children'], fn( $a, $b ) => $a['title'] <=> $b['title'] );
					}
				}

				return array_values( $parts );
			}
		);
	}
}
