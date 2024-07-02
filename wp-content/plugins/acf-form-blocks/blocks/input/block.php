<?php
/**
 * Input block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Field;

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/input' !== $block['name'] ) {
			return $attrs;
		}

		$field = new Field( $block );

		$attrs['name']  = $field->get_name();
		$attrs['type']  = $field->get_input_type();
		$attrs['value'] = $field->get_value();

		if ( $field->get_placeholder() ) {
			$attrs['placeholder'] = $field->get_placeholder();
		}

		return $attrs;
	},
	10,
	2
);
