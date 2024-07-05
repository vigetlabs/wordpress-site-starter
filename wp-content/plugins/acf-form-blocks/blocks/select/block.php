<?php
/**
 * Input block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Select;

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/select' !== $block['name'] ) {
			return $attrs;
		}

		/** @var Select $field */
		$field = Field::factory( $block );

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
