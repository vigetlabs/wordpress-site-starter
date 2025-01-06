<?php
/**
 * Helper Functions
 *
 * @package VigetFormBlocks
 */

/**
 * Get the posted ACF block.
 *
 * @return array
 */
function vgtfb_get_posted_acf_block(): array {
	if ( empty( $_REQUEST['block'] ) ) {
		return [];
	}

	$block     = json_decode( wp_unslash( $_REQUEST['block'] ), true );
	$post_id   = ! empty( $_REQUEST['post_id'] ) ? intval( $_REQUEST['post_id'] ) : 0;
	$client_id = ! empty( $_REQUEST['clientId'] ) ? $_REQUEST['clientId'] : false;

	if ( empty( $block['id'] ) && ! empty( $client_id ) ) {
		$block['id'] = $client_id;
	}

	// Prepare block ensuring all settings and attributes exist.
	$block = acf_prepare_block( $block );
	return acf_add_block_meta_values( $block, $post_id );
}

/**
 * Get the block ID from ACF Field block data.
 *
 * @param array $block
 *
 * @return ?string
 */
function vgtfb_get_block_id_from_acf_block_data( array $block ): ?string {
	if ( empty( $block['name'] ) || empty( $block['blockId'] ) ) {
		return null;
	}

	$block_name = str_replace( '/', '_', $block['name'] );
	$prefix     = 'acf_form' === $block_name ? $block_name : 'acf_field';

	return $prefix . '_' . $block['blockId'];
}
