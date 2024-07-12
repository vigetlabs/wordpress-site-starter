<?php
/**
 * Block registration
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Form;

add_filter(
	'acfbt_block_locations',
	function ( array $locations ): array {
		$locations[] = ACFFB_PLUGIN_PATH . 'blocks';
		return $locations;
	}
);

add_filter(
	'block_categories_all',
	function ( array $categories ): array {
		$categories[] = [
			'slug'  => 'forms',
			'title' => __( 'Forms', 'acf-form-blocks' ),
		];
		return $categories;
	}
);
