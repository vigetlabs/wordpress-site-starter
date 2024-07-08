<?php
/**
 * Submit button
 *
 * @package ACFFormBlocks
 */

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
