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
	if ( $block ) {
		if ( Cache::get( get_block_id( $block, true ) ) ) {
			return Cache::get( get_block_id( $block, true ) );
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
	$forms = Blocks::get_blocks_by_type( $blocks, 'acf/form' );

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
