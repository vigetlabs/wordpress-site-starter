<?php
/**
 * Textarea block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Field;

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/textarea' !== $block['name'] ) {
			return $attrs;
		}

		$field = new Field( $block );

		$attrs['name'] = $field->get_name();

		if ( $field->get_placeholder() ) {
			$attrs['placeholder'] = $field->get_placeholder();
		}

		if ( $field->is_required() ) {
			$attrs['required'] = true;
		}

		return $attrs;
	},
	10,
	2
);
