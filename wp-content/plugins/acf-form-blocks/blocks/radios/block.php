<?php
/**
 * Radio field
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


		return $attrs;
	},
	10,
	2
);
