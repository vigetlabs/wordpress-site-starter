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

add_filter(
	'acf/pre_save_block',
	function( array $attributes ): array {
		if ( ! in_array( $attributes['name'], Form::ALL_BLOCK_TYPES, true ) ) {
			return $attributes;
		}

		if ( 'acf/form' === $attributes['name'] ) {
			if ( empty( $attributes['form_id'] ) ) {
				$attributes['form_id'] = uniqid();
			}
		} elseif ( empty( $attributes['field_id'] ) ) {
			$attributes['field_id'] = uniqid();
		}

		return $attributes;
	}
);
