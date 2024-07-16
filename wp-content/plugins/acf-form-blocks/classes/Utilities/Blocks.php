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

	private static array $parsed = [];

	/**
	 * Get Blocks by Type.
	 *
	 * @param array        $blocks Blocks.
	 * @param string|array $type Block Type(s).
	 * @param array        $collection Collection.
	 * @param ?string      $parent_id Parent Block ID.
	 *
	 * @return array
	 */
	public static function get_blocks_by_type( array $blocks, string|array $type, array &$collection = [], ?string $parent_id = null ): array {
		if ( ! is_array( $type ) ) {
			$type = [ $type ];
		}

		foreach ( $blocks as $block ) {
			if ( in_array( $block['blockName'], $type, true ) ) {
				if ( $parent_id ) {
					$block['attrs']['parent_id'] = $parent_id;
				}
				$collection[] = $block;
			}

			// Pattern Support.
			if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
				$pattern_blocks = self::get_pattern( $block['attrs']['ref'] );

				self::get_blocks_by_type( $pattern_blocks, $type, $collection, $parent_id );
			}

			// Check Inner Blocks.
			if ( ! empty( $block['innerBlocks'] ) ) {
				$new_parent = $parent_id;
				if ( ! empty( $block['attrs']['block_id'] ) ) {
					$new_parent = str_replace( '/', '_', $block['blockName'] ) . '_' . $block['attrs']['block_id'];
				}
				self::get_blocks_by_type( $block['innerBlocks'], $type, $collection, $new_parent );
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
		if ( array_key_exists( $block_ref_id, self::$parsed ) ) {
			return self::$parsed[ $block_ref_id ];
		}

		$block = get_post( $block_ref_id );

		if ( ! $block ) {
			return [];
		}

		self::$parsed[ $block_ref_id ] = parse_blocks( $block->post_content );

		return self::$parsed[ $block_ref_id ];
	}
}
