<?php
/**
 * Submit button
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/submit' !== $block['name'] ) {
			return $attrs;
		}

		$submit = Field::factory( $block );

		$logic = $submit->get_conditional_logic();
		if ( $logic ) {
			$attrs['data-conditional-rules'] = wp_json_encode( $logic );
		}

		return $attrs;
	},
	10,
	2
);

add_filter(
	'render_block',
	function ( string $block_content, array $block ) {
		if ( 'acf/submit' !== $block['blockName'] ) {
			return $block_content;
		}

		// convert link tag to a button tag and add type submit
		$block_content = str_replace( '<a', '<button type="submit"', $block_content );

		return str_replace( '</a>', '</button>', $block_content );
	},
	10,
	2
);
