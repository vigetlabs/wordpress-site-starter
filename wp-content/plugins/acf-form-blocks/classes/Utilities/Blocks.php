<?php
/**
 * Blocks Utility
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Utilities;

/**
 * Blocks Utility Class
 */
class Blocks {

	/**
	 * Get Blocks by Type.
	 *
	 * @param array        $blocks Blocks.
	 * @param string|array $type Block Type(s).
	 * @param array        $collection Collection.
	 *
	 * @return array
	 */
	public static function get_blocks_by_type( array $blocks, string|array $type, array &$collection = [] ): array {
		if ( ! is_array( $type ) ) {
			$type = [ $type ];
		}

		foreach ( $blocks as $block ) {
			if ( in_array( $block['blockName'], $type, true ) ) {
				$collection[] = $block;
			}

			// Pattern Support.
			if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
				$pattern_blocks = self::get_pattern( $block['attrs']['ref'] );

				self::get_blocks_by_type( $pattern_blocks, $type, $collection );
			}

			// Check Inner Blocks.
			if ( ! empty( $block['innerBlocks'] ) ) {
				self::get_blocks_by_type( $block['innerBlocks'], $type, $collection );
			}
		}

		return $collection;
	}

	/**
	 * Get the block pattern by ID.
	 *
	 * @param int $block_ref_id Block ID.
	 *
	 * @return array
	 */
	public static function get_pattern( int $block_ref_id ): array {
		$block = get_post( $block_ref_id );

		if ( ! $block ) {
			return [];
		}

		return parse_blocks( $block->post_content );
	}
}
