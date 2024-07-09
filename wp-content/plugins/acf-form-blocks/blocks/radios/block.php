<?php
/**
 * Radio buttons
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/radios' !== $block['name'] ) {
			return $attrs;
		}

		$radios = Field::factory( $block );

		$logic = $radios->get_conditional_logic();
		if ( $logic ) {
			$attrs['data-conditional-rules'] = wp_json_encode( $logic );
		}

		return $attrs;
	},
	10,
	2
);
