<?php
/**
 * Textarea block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/textarea' !== $block['name'] ) {
			return $attrs;
		}

		$textarea = Field::factory( $block );

		$attrs['name'] = $textarea->get_name();
		$attrs['id']   = $textarea->get_id_attr();

		if ( $textarea->get_placeholder() ) {
			$attrs['placeholder'] = $textarea->get_placeholder();
		}

		if ( $textarea->is_required() ) {
			$attrs['required'] = true;
		}

		$logic = $textarea->get_conditional_logic();
		if ( $logic ) {
			$attrs['data-conditional-rules'] = wp_json_encode( $logic );
		}

		return $attrs;
	},
	10,
	2
);
