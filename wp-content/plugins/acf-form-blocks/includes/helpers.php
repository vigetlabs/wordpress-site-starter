<?php
/**
 * Helper Functions
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Cache;
use ACFFormBlocks\Form;

/**
 * Get the Form Block.
 *
 * @param array $block Block data.
 *
 * @return ?Form
 */
function acffb_get_form( array $block = [] ): ?Form {
	if ( $block ) {
		if ( Cache::get( get_block_id( $block ) ) ) {
			return Cache::get( get_block_id( $block ) );
		}

		$form = new Form( $block );
		$form->update_cache();
		return $form;
	}

	$blocks = parse_blocks( get_the_content() );

	if ( empty( $blocks ) ) {
		return null;
	}

	foreach ( $blocks as $block ) {
		if ( 'acf/form' !== $block['blockName'] ) {
			continue;
		}

		$context     = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
		$attrs       = $block['attrs'] ?? [];
		$attrs['id'] = acf_get_block_id( $attrs, $context );
		$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

		return new Form( acf_prepare_block( $attrs ), true );
	}

	return null;
}
