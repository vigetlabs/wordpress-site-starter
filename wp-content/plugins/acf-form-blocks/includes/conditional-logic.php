<?php
/**
 * Conditional Logic
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;

add_filter(
	'acf/prepare_field/key=field_668c1b138ccd0',
	function ( array $field ): array {
		$form = acffb_get_form();
		if ( ! $form ) {
			return $field;
		}

		$block    = acffb_get_posted_acf_block();
		$block_id = acffb_get_block_id_from_acf_block_data( $block );

		$field['choices'] = [];

		foreach ( $form->get_form_object()->get_fields() as $form_field ) {
			if ( $form_field->get_name() === $block_id ) {
				continue;
			}
			$field['choices'][ $form_field->get_name() ] = $form_field->get_label();
		}

		if ( empty( $field['choices'] ) ) {
			$field['choices'] = [
				'' => 'Save changes to update options.',
			];
		}

		return $field;
	}
);

/**
 * Get the posted ACF block.
 *
 * @return array
 */
function acffb_get_posted_acf_block(): array {
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
 * // TODO: Not a great solution, but works :|
 *
 * @param array $block
 *
 * @return ?string
 */
function acffb_get_block_id_from_acf_block_data( array $block ): ?string {
	if ( empty( $block['data'] ) ) {
		return null;
	}

	$form    = acffb_get_form();
	$matches = [];

	foreach ( $form->get_form_object()->get_fields() as $form_field ) {
		if ( $block['name'] !== $form_field->get_block_name( true ) ) {
			continue;
		}

		$field_name = $form_field->get_name();

		foreach ( $block['data'] as $key => $value ) {
			if ( $form_field->get_field_data( $key ) === $value ) {
				if ( ! in_array( $field_name, $matches, true ) ) {
					$matches[] = $field_name;
				}
			} elseif ( in_array( $field_name, $matches, true ) ) {
				$matches = array_diff( $matches, [ $field_name ] );
			}
		}
	}

	if ( 1 === count( $matches ) ) {
		return $matches[0];
	}

	return null;
}
