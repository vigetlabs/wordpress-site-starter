<?php
/**
 * Helper Functions
 *
 * TODO: Make more robust (Class w/static Methods).
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Form as FormElement;
use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\Cache;

/**
 * Get the Form Block.
 *
 * @param array  $block Block data.
 * @param string $content Block content.
 * @param array  $context Context.
 *
 * @return ?Form
 */
function acffb_get_form( array $block = [], string $content = '', array $context = [] ): ?Form {
	if ( $block ) {
		if ( Cache::get( get_block_id( $block ) ) ) {
			return Cache::get( get_block_id( $block ) );
		}

		return new Form( new FormElement( $block ) );
	}

	if ( ! $content ) {
		$content = get_the_content();
	}

	$blocks = parse_blocks( $content );

	if ( empty( $blocks ) ) {
		return null;
	}

	if ( ! $context ) {
		$context = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
	}

	return acffb_get_form_block( $blocks, $context );
}

/**
 * Get the Form Block Recursively.
 *
 * @param array $blocks Blocks.
 * @param array $context Context.
 *
 * @return ?Form
 */
function acffb_get_form_block( array $blocks, array $context = [] ): ?Form {
	$forms = acffb_get_blocks_by_type( $blocks, 'acf/form' );

	if ( ! $forms ) {
		return null;
	}

	// Return first form block.
	$block = $forms[0];

	$attrs       = $block['attrs'] ?? [];
	$attrs['id'] = acf_get_block_id( $attrs, $context );
	$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

	$form_el = new FormElement( acf_prepare_block( $attrs ) );

	return new Form( $form_el, true );
}

/**
 * Get Blocks by Type.
 *
 * @param array        $blocks Blocks.
 * @param string|array $type Block Type(s).
 *
 * @return array
 */
function acffb_get_blocks_by_type( array $blocks, string|array $type ): array {
	$found = [];

	if ( ! is_array( $type ) ) {
		$type = [ $type ];
	}

	foreach ( $blocks as $block ) {
		if ( in_array( $block['blockName'], $type, true ) ) {
			$found[] = $block;
		}

		// Pattern Support.
		if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
			$pattern_blocks = acffb_get_pattern( $block['attrs']['ref'] );

			$found = array_merge( $found, acffb_get_blocks_by_type( $pattern_blocks, $type ) );
		}

		// Check Inner Blocks.
		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = array_merge( $found, acffb_get_blocks_by_type( $block['innerBlocks'], $type ) );
		}
	}

	return $found;
}

/**
 * Get the block pattern by ID.
 *
 * @param int $block_ref_id Block ID.
 *
 * @return array
 */
function acffb_get_pattern( int $block_ref_id ): array {
	$block = get_post( $block_ref_id );

	if ( ! $block ) {
		return [];
	}

	return parse_blocks( $block->post_content );
}
