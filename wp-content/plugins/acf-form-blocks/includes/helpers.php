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
use ACFFormBlocks\Utilities\Blocks;
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
	if ( ! empty( $block['block_id'] ) ) {
		$cache = Cache::get( $block['block_id'] );
		if ( $cache ) {
			return $cache;
		}

		$form = new Form( new FormElement( $block ) );
		Cache::set( $form->get_form_object()->get_id(), $form, true );
		return $form;
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

	$form = acffb_get_form_block( $blocks, $context );

	if ( ! $form ) {
		return null;
	}

	return acffb_get_form( $form );
}

/**
 * Get the Form Block Recursively.
 *
 * @param array $blocks Blocks.
 * @param array $context Context.
 *
 * @return ?array
 */
function acffb_get_form_block( array $blocks, array $context = [] ): ?array {
	$forms = Blocks::get_blocks_by_type( $blocks, 'acf/form' );

	if ( ! $forms ) {
		return null;
	}

	// Return first form block.
	$block = $forms[0];

	$attrs       = $block['attrs'] ?? [];
	$attrs['id'] = acf_get_block_id( $attrs, $context );
	$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

	return acf_prepare_block( $attrs );
}
