<?php
/**
 * Select block
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

		/** @var Select $select */
		$select = Field::factory( $block );

		$attrs['name'] = $select->get_name();
		$attrs['id']   = $select->get_id_attr();

		if ( $select->get_placeholder() ) {
			$attrs['placeholder'] = $select->get_placeholder();
		}

		if ( $select->is_required() ) {
			$attrs['required'] = true;
		}

		$logic = $select->get_conditional_logic();
		if ( $logic ) {
			$attrs['data-conditional-rules'] = wp_json_encode( $logic );
		}

		return $attrs;
	},
	10,
	2
);
