<?php
/**
 * Helper Functions
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Form as FormElement;
use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\Cache;

/**
 * Get the Form Block.
 *
 * TODO: Make more robust.
 *
 * @param array $block Block data.
 * @param string $content Block content.
 * @param ?array $context Context.
 *
 * @return ?Form
 */
function acffb_get_form( array $block = [], string $content = '', ?array $context = null ): ?Form {
	if ( $block ) {
		if ( Cache::get( get_block_id( $block ) ) ) {
			return Cache::get( get_block_id( $block ) );
		}

		$form = new Form( new FormElement( $block ) );
		$form->update_cache();
		return $form;
	}

	if ( ! $content ) {
		$content = get_the_content();
	}

	$blocks = parse_blocks( $content );

	if ( empty( $blocks ) ) {
		return null;
	}

	foreach ( $blocks as $block ) {
		// Support for only 1 form per page.
		if ( 'acf/form' !== $block['blockName'] ) {
			continue;
		}

		if ( ! $context ) {
			$context = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
		}

		$attrs       = $block['attrs'] ?? [];
		$attrs['id'] = acf_get_block_id( $attrs, $context );
		$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

		$form = new FormElement( acf_prepare_block( $attrs ) );

		return new Form( $form, true );
	}

	return null;
}
