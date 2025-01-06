<?php
/**
 * Block registration
 *
 * @package VigetFormBlocks
 */

add_filter(
	'vgtbt_block_locations',
	function ( array $locations ): array {
		$locations[] = VGTFB_PLUGIN_PATH . 'blocks';
		return $locations;
	}
);

add_filter(
	'block_categories_all',
	function ( array $categories ): array {
		$categories[] = [
			'slug'  => 'forms',
			'title' => __( 'Forms', 'viget-form-blocks' ),
		];
		$categories[] = [
			'slug'  => 'form-data',
			'title' => __( 'Form Data', 'viget-form-blocks' ),
		];
		return $categories;
	}
);

add_action(
	'acf/init',
	function() {
		acf_add_options_page(
			[
				'page_title'      => __( 'Form Blocks Admin', 'viget-form-blocks' ),
				'menu_slug'       => 'acffb-forms',
				'menu_title'      => __( 'Form Blocks', 'viget-form-blocks' ),
				'position'        => 20,
				'redirect'        => false,
				'description'     => __( 'Admin for Form Blocks', 'viget-form-blocks' ),
				'menu_icon'       => [
					'type'  => 'dashicons',
					'value' => 'dashicons-feedback',
				],
				'updated_message' => __( 'Settings Updated', 'viget-form-blocks' ),
				'capability'      => 'manage_options',
				'autoload'        => true,
				'icon_url'       => 'dashicons-feedback',
			]
		);
	}
);

