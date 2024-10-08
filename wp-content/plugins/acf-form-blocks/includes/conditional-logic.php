<?php
/**
 * Conditional Logic
 *
 * @package ACFFormBlocks
 */

add_filter(
	'acf/prepare_field/key=field_668c1b138ccd0',
	function ( array $field ): array {
		$form = ACFFormBlocks\Form::get_instance();

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
