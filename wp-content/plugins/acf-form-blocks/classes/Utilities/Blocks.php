<?php
/**
 * Blocks Utility
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Utilities;

use WP_Post;

/**
 * Blocks Utility Class
 */
class Blocks {

	/**
	 * Array of patterns
	 *
	 * @var WP_Post[]
	 */
	private static array $patterns = [];

	/**
	 * Get Blocks by Type.
	 *
	 * @param array        $blocks Blocks.
	 * @param string|array $type Block Type(s).
	 * @param array        $collection Collection.
	 * @param ?string      $parent_id Parent Block ID.
	 * @param ?int         $pattern_id The pattern ID.
	 *
	 * @return array
	 */
	public static function get_blocks_by_type( array $blocks, string|array $type, array &$collection = [], ?string $parent_id = null, ?int $pattern_id = null ): array {
		if ( ! is_array( $type ) ) {
			$type = [ $type ];
		}

		foreach ( $blocks as $block ) {
			if ( in_array( $block['blockName'], $type, true ) ) {
				if ( $parent_id ) {
					$block['attrs']['parent_id'] = $parent_id;
				}
				if ( $pattern_id ) {
					$block['attrs']['pattern_id'] = $pattern_id;
				}
				$collection[] = $block;
			}

			// Pattern Support.
			if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
				$pattern_blocks = self::get_pattern( intval( $block['attrs']['ref'] ) );

				self::get_blocks_by_type( $pattern_blocks, $type, $collection, $parent_id, intval( $block['attrs']['ref'] ) );
			}

			// Check Inner Blocks.
			if ( ! empty( $block['innerBlocks'] ) ) {
				$new_parent = $parent_id;
				if ( ! empty( $block['attrs']['block_id'] ) ) {
					$new_parent = str_replace( '/', '_', $block['blockName'] ) . '_' . $block['attrs']['block_id'];
				}
				self::get_blocks_by_type( $block['innerBlocks'], $type, $collection, $new_parent, $pattern_id );
			}
		}

		return $collection;
	}

	/**
	 * Get the block pattern by ID.
	 *
	 * @param int  $pattern_id Block ID.
	 * @param bool $parse      Parse the blocks.
	 *
	 * @return array|string
	 */
	public static function get_pattern( int $pattern_id, bool $parse = true ): array|string {
		if ( ! array_key_exists( $pattern_id, self::$patterns ) ) {
			$block = get_post( $pattern_id );

			if ( ! $block ) {
				return $parse ? [] : '';
			}

			self::$patterns[ $pattern_id ] = $block;
		}

		if ( ! $parse ) {
			return self::$patterns[ $pattern_id ]->post_content;
		}

		return parse_blocks( self::$patterns[ $pattern_id ]->post_content );
	}

	/**
	 * Prepare ACF block from WP Block Array
	 *
	 * @param array $block
	 * @param array $context
	 *
	 * @return array
	 */
	public static function prepare_acf_block( array $block, array $context = [] ): array {
		$attrs       = $block['attrs'] ?? [];
		$attrs['id'] = acf_get_block_id( $attrs, $context );
		$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );
		return acf_prepare_block( $attrs );
	}
}
